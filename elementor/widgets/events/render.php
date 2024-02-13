<?php
$settings = $this->get_settings_for_display();
$fields = [
  'fetch_all',
  'fetch_limit',
  'external_event_new_tab',
  'event_title',
  'event_excerpt',
  'event_metadata',
  'event_ongoing_dates',
  'no_results_found_text',
];
$filtered_settings = array_intersect_key($settings, array_flip(array_merge($fields, get_carousel_fields())));
$unique_id = uniqid();
?>

<div id="eyeon-events-<?= $unique_id ?>" class="eyeon-events eyeon-loader">
  <div class="eyeon-wrapper eyeon-hide">
    <?php if( $settings['categories_filters'] === 'show' ) : ?>
    <div class="categories">
      <select id="categories-dropdown-<?= $unique_id ?>" class="show-on-mob"></select>
      <ul id="categories-<?= $unique_id ?>" class="hide-on-mob"></ul>
    </div>
    <?php endif; ?>

    <?php
    $classes = '';
    if ($settings['view_mode']==='carousel' ) {
      $classes .= ' owl-carousel owl-carousel-'.$unique_id.' owl-theme';
      if($settings['carousel_navigation']==='show') {
        $classes .= ' owl-nav-show';
      }
      if($settings['carousel_dots']==='show') {
        $classes .= ' owl-dots-show';
      }
    } else {
      $classes .= ' grid-view';
    }
    ?>
    <div id="events-list-<?= $unique_id ?>" class="events-list <?= $classes ?>"></div>
  </div>
</div>

<script type="text/javascript">
  jQuery(document).ready(function($) {
    const settings = <?= json_encode($filtered_settings) ?>;

    const eyeonEvents = $('#eyeon-events-<?= $unique_id ?>');
    const categoryList = $('#categories-<?= $unique_id ?>');
    const categoryDropdownList = $('#categories-dropdown-<?= $unique_id ?>');
    const eventsList = $('#events-list-<?= $unique_id ?>');

    let events = [];
    var page = 1;
    var defaultLimit = 100;
    let categories = [];

    fetch_events();

    function fetch_events() {
      var limit = defaultLimit;
      if( settings.fetch_all !== 'yes' ) {
        var remainingLimit = settings.fetch_limit - (page - 1) * defaultLimit;
        limit = Math.min(remainingLimit, defaultLimit);
      }
      $.ajax({
        url: "<?= MCD_API_EVENTS ?>",
        data: { limit, page },
        method: 'GET',
        dataType: 'json',
        headers: {
          center_id: '<?= $mcd_settings['center_id'] ?>'
        },
        success: function (response) {
          if (response.items) {
            events = events.concat(response.items);
            var fetchMore = false;
            if( settings.fetch_all !== 'yes' && page * defaultLimit < settings.fetch_limit ) {
              fetchMore = true;
            }
            if( settings.fetch_all === 'yes' && response.count > events.length ) {
              fetchMore = true;
            }
            if( fetchMore ) {
              page++;
              fetch_events();
            } else {
              setup_events();
            }
          }
        }
      });
    }

    function setup_events() {
      <?php if( $settings['categories_filters'] === 'show' ) : ?>
        setup_categories();
      <?php endif; ?>

      events = events.map(parseAndFindUpcoming);

      events.sort(function (a, b) {
        // Sort by ongoing events
        if (a.ongoing_event && !b.ongoing_event) return 1;
        if (!a.ongoing_event && b.ongoing_event) return -1;

        // Sort by upcoming_date if available
        if (a.upcoming_date && b.upcoming_date) {
          return a.upcoming_date.localeCompare(b.upcoming_date);
        } else if (a.upcoming_date) {
          // Handle the case where b.upcoming_date is undefined
          return -1;
        } else if (b.upcoming_date) {
          // Handle the case where a.upcoming_date is undefined
          return 1;
        }

        // Sort by start_date and start_time
        var startDateA = new Date(a.start_date + ' ' + (a.is_all_day_event ? '00:00:00' : a.start_time));
        var startDateB = new Date(b.start_date + ' ' + (b.is_all_day_event ? '00:00:00' : b.start_time));

        if (startDateA > startDateB) return 1;
        if (startDateA < startDateB) return -1;

        return 0;
      });

      render_events();
    }

    function setup_categories() {
      let fetchedCategories = [];
      events.forEach(item => {
        item.categories = [];
        if(item.category) item.categories.push(item.category);

        if( item.ongoing_event ) {
          item.categories.push({
            id: 9999,
            title: 'On-Going'
          });
        }

        item.categories.forEach(category => {
          if( !(fetchedCategories.some(cat => cat.id === category.id)) ) {
            fetchedCategories.push({
              id: category.id,
              name: category.title,
            });
          }
        });
      });

      fetchedCategories = fetchedCategories.sort(function (a, b) {
        var nameA = a.name.toUpperCase();
        var nameB = b.name.toUpperCase();
        if (nameA < nameB) return -1;
        if (nameA > nameB) return 1;
        return 0;
      });

      categories = [{id: 0, name: 'All'}].concat(fetchedCategories);

      categories.forEach(category => {
        categoryList.append(`
          <li data-value="${category.id}" class="${category.id===0?'active':''}">${category.name}</li>
        `);
        categoryDropdownList.append(`
          <option value="${category.id}">${category.name}</option>
        `);
      });
    }
    
    function parseAndFindUpcoming(event) {
      if (event.repeat_rrule && event.repeat_rrule !== '') {
        var rule = rrule.RRule.fromString(event.repeat_rrule);

        var startDate = new Date();
        startDate.setDate(startDate.getDate() - 1);

        // Get occurrences within a certain time range (adjust as needed)
        var occurrences = rule.between(startDate, new Date(new Date().getTime() + 365 * 24 * 60 * 60 * 1000));

        // Find the next occurrence after the current date
        var upcomingOccurrence = occurrences.find(function (occurrence) {
          return occurrence >= startDate;
        });

        if (upcomingOccurrence) {
          // Update event with the upcoming occurrence date
          event.upcoming_date = upcomingOccurrence.toISOString().split('T')[0];
        }
      }

      event.datesStr = '';
      if( event.upcoming_date ) {
        event.datesStr = eyeonFormatDate(event.upcoming_date);
      } else {
        event.datesStr = event.start_date!==event.end_date ? eyeonFormatDate(event.start_date)+' - '+eyeonFormatDate(event.end_date) : eyeonFormatDate(event.start_date);
      }

      return event;
    }

    function render_events() {
      eyeonEvents.removeClass('eyeon-loader').find('.eyeon-wrapper').removeClass('eyeon-hide');

      eventsList.empty();

      if( events.length > 0 ) {
        console.log('settings', settings.event_ongoing_dates);
        events.forEach(event => {
          console.log('event', event.ongoing_event);
          const eventItem = $(`
            <a href="${event.event_url?event.event_url:`<?= mcd_single_page_url('mycenterevent') ?>${event.slug}`}" class="event event-${event.id}" ${(event.event_url && settings.external_event_new_tab)?'target="_blank"':''}>
              <div class="image">
                <img src="${event.media.url}" alt="${event.title}" />
              </div>
              ${ settings.event_title ? `<h3 class="event-title">${event.title}</h3>` : '' }
              ${ settings.event_excerpt? `<p class="event-excerpt">${event.short_description}</p>` : '' }
              ${ settings.event_metadata && (!event.ongoing_event || (event.ongoing_event && settings.event_ongoing_dates)) ? `
                <div class="metadata">
                  <div class="date">
                    <i class="far fa-calendar"></i>
                    <span>${event.datesStr}</span>
                  </div>
                  ${!event.is_all_day_event ? `
                    <div class="time">
                      <i class="far fa-clock"></i>
                      <span>${eyeonConvertTo12HourFormat(event.start_time)} - ${eyeonConvertTo12HourFormat(event.end_time)}</span>
                    </div>
                  ` : '' }
                </div>
              `: '' }
            </a>
          `);
          eventsList.append(eventItem);
        });
      } else {
        eyeonEvents.find('.eyeon-wrapper').html(`
          <div class="no-items-found">${settings.no_results_found_text}</div>
        `);
      }
      
      if( events.length > 0 && elementorFrontend.config.environmentMode.edit) {
        eyeonEvents.find('.eyeon-wrapper').append(`
          <div class="no-items-found">${settings.no_results_found_text}</div>
        `);
      }

      <?php include(MCD_PLUGIN_PATH.'elementor/widgets/common/carousel/setup-js.php'); ?>
    }
    function filterByCategory(categoryId = 0) {
      eventsList.find('.event').addClass('eyeon-hide');
      events.forEach(item => {
        if (categoryId == 0 || item.categories.some(cat => cat.id == categoryId)) {
          eventsList.find('.event.event-'+item.id).removeClass('eyeon-hide');
        }
      });
    }

    // Event listeners for filter
    categoryList.on('click', 'li', function() {
      categoryList.find('li.active').removeClass('active');
      $(this).addClass('active');
      const selectedCategoryId = parseInt($(this).attr('data-value'));

      categoryDropdownList.val(selectedCategoryId);
      filterByCategory(selectedCategoryId);
    });

    categoryDropdownList.on('change', function() {
      const selectedCategoryId = parseInt($(this).val());

      // change categories list selection
      categoryList.find('li.active').removeClass('active');
      categoryList.find('li[data-value="'+selectedCategoryId+'"]').addClass('active');

      filterByCategory(selectedCategoryId);
    });
  });
</script>