<?php
$settings = $this->get_settings_for_display();
$unique_id = uniqid();
?>

<div id="eyeon-events-<?= $unique_id ?>" class="eyeon-events eyeon-loader">
  <div class="eyeon-wrapper eyeon-hide">
      <div id="events-list-<?= $unique_id ?>" class="events-list"></div>
    </div>
  </div>
</div>

<script type="text/javascript">
  jQuery(document).ready(function($) {
    const settings = <?= json_encode($settings) ?>;

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
              renderEvents();
            }
          }
        }
      });
    }

    function renderEvents() {
      eyeonEvents.removeClass('eyeon-loader').find('.eyeon-wrapper').removeClass('eyeon-hide');

      eventsList.empty();
      events.forEach(event => {
        console.log('angrej123 event:', event);
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
    }
  });
</script>