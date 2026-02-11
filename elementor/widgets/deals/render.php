<?php
$settings = $this->get_settings_for_display();
$fields = [
  'fetch_all',
  'fetch_limit',
  'default_sorting',
  'no_results_found_text',
];
$filtered_settings = array_intersect_key($settings, array_flip(array_merge($fields, get_carousel_fields())));
$unique_id = uniqid();
?>

<div id="eyeon-deals-<?= $unique_id ?>" class="eyeon-deals eyeon-loader">
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
    
    <?php if( $settings['sorting_options'] ) : ?>
      <div class="sorting-wrapper">
        <div class="sorting-options">
          <div class="option" data-value="recently_added">Recently Added</div>
          <div class="option" data-value="ending_soon">Ending Soon</div>
        </div>
      </div>
    <?php endif; ?>

    <div id="deals-list-<?= $unique_id ?>" class="deals-list <?= $classes ?>"></div>
  </div>
</div>

<script type="text/javascript">
  jQuery(document).ready(function($) {
    const settings = <?= json_encode($filtered_settings) ?>;

    const eyeonDeals = $('#eyeon-deals-<?= $unique_id ?>');
    const dealsList = $('#deals-list-<?= $unique_id ?>');
    
    let deals = [];
    var page = 1;
    var defaultLimit = 100;
    
    fetch_deals();
    
    function fetch_deals(force_refresh = false) {
      $.ajax({
        url: EYEON.ajaxurl+'?api=<?= MCD_API_DEALS ?>',
        data: {
          action: 'eyeon_api_request',
          nonce: EYEON.nonce,
          apiUrl: "<?= MCD_API_DEALS ?>",
          paginated_data: true,
          force_refresh: force_refresh
        },
        method: "POST",
        dataType: 'json',
        xhrFields: {
          withCredentials: true
        },
        success: function (response) {
          if (response.items) {
            let allDeals = response.items;
            if (settings.fetch_all !== 'yes' && settings.fetch_limit > 0) {
              allDeals = allDeals.slice(0, settings.fetch_limit);
            }

            deals = allDeals;
            render();
          }
          
          if (response.stale_data) {
            fetch_deals(true);
          }
        }
      });
    }
    
    function filterAndSortItems() {
      const uniqueDeals = [];
      const seenIds = new Set();

      deals.forEach(deal => {
        if (!seenIds.has(deal.global_deal_id)) {
          seenIds.add(deal.global_deal_id);
          uniqueDeals.push(deal);
        }
      });

      deals = uniqueDeals.sort(function(a, b) {
        if( settings.default_sorting === 'recently_added' ) {
          return new Date(b.created_at) - new Date(a.created_at);
        }
        return new Date(a.end_date) - new Date(b.end_date);
      });
    }
    
    function render() {
      filterAndSortItems();

      eyeonDeals.removeClass('eyeon-loader').find('.eyeon-wrapper').removeClass('eyeon-hide');
      eyeonDeals.find('.no-items-found').remove();
      dealsList.html('');
      
      if( deals.length > 0 ) {
        deals.forEach(deal => {
          const dealItem = $(`
          <a href="<?= mcd_single_page_url('mycenterdeal') ?>${deal.slug}" class="deal">
            <div class="image">
              <img src="${deal.media.url}" alt="${deal.title}" />
            </div>
            <div class="deal-content">
              <img class="retailer-logo" src="${deal.retailer.media.url}" alt="${deal.retailer.name}" />
              <div class="details">
                <h3 class="deal-title">${deal.title}</h3>
                <div class="deal-expiry">Valid until ${eyeonFormatDate(deal.end_date)}</div>
              </div>
            </div>
          </a>
          `);
          dealsList.append(dealItem);
        });
      } else {
        eyeonDeals.find('.eyeon-wrapper').addClass('eyeon-hide');
        if(eyeonDeals.find('.no-items-found').length === 0) {
          eyeonDeals.append(`
            <div class="no-items-found">${settings.no_results_found_text}</div>
          `);
        }
      }
      
      if( deals.length > 0 && elementorFrontend.config.environmentMode.edit && eyeonDeals.find('.no-items-found').length === 0) {
        eyeonDeals.append(`
          <div class="no-items-found">${settings.no_results_found_text}</div>
        `);
      }
      
      <?php include(MCD_PLUGIN_PATH.'elementor/widgets/common/carousel/setup-js.php'); ?>
    }
    
    <?php if( $settings['sorting_options'] ) : ?>
      eyeonDeals.find(`.sorting-options .option[data-value="${settings.default_sorting}"]`).addClass('active');
      eyeonDeals.find('.sorting-options .option').click(function() {
        settings.default_sorting = $(this).attr('data-value');
        eyeonDeals.find('.sorting-options .option').removeClass('active');
        $(this).addClass('active');
        render();
      });
    <?php endif; ?>
  });
</script>