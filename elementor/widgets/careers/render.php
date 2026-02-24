<?php
$settings = $this->get_settings_for_display();
$filtered_settings = array_intersect_key($settings, array_flip([
  'fetch_all',
  'fetch_limit',
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

    function fetch_careers(force_refresh = false) {
      $.ajax({
        url: EYEON.ajaxurl+'?api=<?= MCD_API_CAREERS ?>',
        data: {
          action: 'eyeon_api_request',
          nonce: EYEON.nonce,
          apiUrl: "<?= MCD_API_CAREERS ?>",
          paginated_data: true,
          force_refresh: force_refresh
        },
        method: "POST",
        dataType: 'json',
        xhrFields: {
          withCredentials: true
        },
        success: function (response) {
          parse_careers(response);
        }
      });
    }

    function parse_careers(response) {
      if (response.items) {
        let allCareers = response.items;
        
        // Apply fetch_limit after fetching (if not fetching all)
        if (settings.fetch_all !== 'yes' && settings.fetch_limit > 0) {
          allCareers = allCareers.slice(0, settings.fetch_limit);
        }
        
        careers = allCareers;
        render_careers();
      }
    }

    function render_careers() {
      eyeonCareers.removeClass('eyeon-loader').find('.eyeon-wrapper').removeClass('eyeon-hide');
      eyeonCareers.find('.no-items-found').remove();
      careersList.html('');

      if( careers.length > 0 ) {
        careers.forEach(career => {
          const careerItem = $(`
            <a href="<?= mcd_single_page_url('mycentercareer') ?>${career.slug}" class="career">
              <div class="retailer-logo">
                <img src="${career.retailer.media.url}" alt="${career.retailer.name}" />
              </div>
              <div class="career-content">
                <h3 class="career-title">${career.title}</h3>
                ${ (settings.expiry_date && career.end_date) ? `<div class="career-expiry">${settings.career_expiry_prefix?settings.career_expiry_prefix+' ':''}${eyeonFormatDate(career.end_date)}${settings.career_expiry_suffix?' '+settings.career_expiry_suffix:''}</div>` : '' }
              </div>
            </a>
          `);
          careersList.append(careerItem);
        });
      } else {
        eyeonCareers.find('.eyeon-wrapper').addClass('eyeon-hide');
        if(eyeonCareers.find('.no-items-found').length === 0) {
          eyeonCareers.append(`
            <div class="no-items-found">${settings.no_results_found_text}</div>
          `);
        }
      }
      
      if( careers.length > 0 && elementorFrontend.config.environmentMode.edit && eyeonCareers.find('.eyeon-wrapper .no-items-found').length === 0) {
        eyeonCareers.append(`
          <div class="no-items-found">${settings.no_results_found_text}</div>
        `);
      }
    }
    
    const cachedCareers = <?= json_encode(json_decode(get_option(get_eyeon_api_cache_key(MCD_API_CAREERS)))) ?>;
    if (cachedCareers) {
      parse_careers(cachedCareers);
    }
    fetch_careers(true);
  });
</script>