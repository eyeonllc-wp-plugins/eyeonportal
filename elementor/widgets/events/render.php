<?php
$settings = $this->get_settings_for_display();
$fields = [
  'fetch_all',
  'fetch_limit',
  'external_event_new_tab',
  'event_title',
  'event_excerpt',
  'event_metadata',
  'no_results_found_text',
];
$filtered_settings = array_intersect_key($settings, array_flip(array_merge($fields, get_carousel_fields())));
$unique_id = uniqid();
?>

<div id="eyeon-events-<?= $unique_id ?>" class="eyeon-events eyeon-loader">
  <div class="eyeon-wrapper eyeon-hide">
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
    const eventsList = $('#events-list-<?= $unique_id ?>');

    let events = [];
    var page = 1;
    var defaultLimit = 100;

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
              render();
            }
          }
        }
      });
    }

    function render() {
      eyeonEvents.removeClass('eyeon-loader').find('.eyeon-wrapper').removeClass('eyeon-hide');

      eventsList.empty();

      if( events.length > 0 ) {
        events.forEach(event => {
          const eventItem = $(`
            <a href="${event.event_url?event.event_url:`<?= mcd_single_page_url('mycenterevent') ?>${event.slug}`}" class="event" ${(event.event_url && settings.external_event_new_tab)?'target="_blank"':''}>
              <div class="image">
                <img src="${event.media.url}" alt="${event.title}" />
              </div>
              ${ settings.event_title ? `<h3 class="event-title">${event.title}</h3>` : '' }
              ${ settings.event_excerpt? `<p class="event-excerpt">${event.short_description}</p>` : '' }
              ${ settings.event_metadata ? `
                <div class="metadata">
                  <div class="date">
                    <i class="far fa-calendar"></i>
                    <span>${event.start_date!==event.end_date ? eyeonFormatDate(event.start_date)+' - '+eyeonFormatDate(event.end_date) : eyeonFormatDate(event.start_date)}</span>
                  </div>
                  <div class="time">
                    <i class="far fa-clock"></i>
                    <span>${eyeonConvertTo12HourFormat(event.start_time)} - ${eyeonConvertTo12HourFormat(event.end_time)}</span>
                  </div>
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
  });
</script>