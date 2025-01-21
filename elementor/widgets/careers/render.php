<?php
$settings = $this->get_settings_for_display();
$filtered_settings = array_intersect_key($settings, array_flip([
  'fetch_all',
  'fetch_limit',
  'career_excerpt',
  'expiry_date',
  'career_expiry_prefix',
  'career_expiry_suffix',
  'no_results_found_text',
]));
$unique_id = uniqid();
?>

<div id="eyeon-careers-<?= $unique_id ?>" class="eyeon-careers eyeon-loader">
  <div class="eyeon-wrapper eyeon-hide">
      <div id="careers-list-<?= $unique_id ?>" class="careers-list"></div>
    </div>
  </div>
</div>

<script type="text/javascript">
  jQuery(document).ready(function($) {
    const settings = <?= json_encode($filtered_settings) ?>;

    const eyeonCareers = $('#eyeon-careers-<?= $unique_id ?>');
    const careersList = $('#careers-list-<?= $unique_id ?>');

    let careers = [];
    var page = 1;
    var defaultLimit = 100;

    fetch_careers();

    function fetch_careers() {
      var limit = defaultLimit;
      if( settings.fetch_all !== 'yes' ) {
        var remainingLimit = settings.fetch_limit - (page - 1) * defaultLimit;
        limit = Math.min(remainingLimit, defaultLimit);
      }
      $.ajax({
        url: "<?= MCD_API_CAREERS ?>",
        data: { limit, page },
        method: 'GET',
        dataType: 'json',
        headers: {
          center_id: '<?= $mcd_settings['center_id'] ?>'
        },
        success: function (response) {
          if (response.items) {
            careers = careers.concat(response.items);
            var fetchMore = false;
            if( settings.fetch_all !== 'yes' && page * defaultLimit < settings.fetch_limit ) {
              fetchMore = true;
            }
            if( settings.fetch_all === 'yes' && response.count > careers.length ) {
              fetchMore = true;
            }
            if( fetchMore ) {
              page++;
              fetch_careers();
            } else {
              render();
            }
          }
        }
      });
    }

    function render() {
      eyeonCareers.removeClass('eyeon-loader').find('.eyeon-wrapper').removeClass('eyeon-hide');

      careersList.empty();

      if( careers.length > 0 ) {
        careers.forEach(career => {
          const careerItem = $(`
            <a href="<?= mcd_single_page_url('mycentercareer') ?>${career.slug}" class="career">
              <div class="retailer-logo">
                <img src="${career.retailer.media.url}" alt="${career.retailer.name}" />
              </div>
              <div class="career-content">
                <h3 class="career-title">${career.title}</h3>
                ${ settings.career_excerpt ? `<div class="career-excerpt">${career.short_description}</div>` : '' }
                ${ (settings.expiry_date && career.end_date) ? `<div class="career-expiry">${settings.career_expiry_prefix?settings.career_expiry_prefix+' ':''}${eyeonFormatDate(career.end_date)}${settings.career_expiry_suffix?' '+settings.career_expiry_suffix:''}</div>` : '' }
              </div>
            </a>
          `);
          careersList.append(careerItem);
        });
      } else {
        eyeonCareers.find('.eyeon-wrapper').html(`
          <div class="no-items-found">${settings.no_results_found_text}</div>
        `);
      }
      
      if( careers.length > 0 && elementorFrontend.config.environmentMode.edit) {
        eyeonCareers.find('.eyeon-wrapper').append(`
          <div class="no-items-found">${settings.no_results_found_text}</div>
        `);
      }
    }
  });
</script>