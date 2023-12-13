<?php
$settings = $this->get_settings_for_display();
$filtered_settings = array_intersect_key($settings, array_flip([
  'fetch_all',
  'fetch_limit',
]));
$unique_id = uniqid();
?>

<div id="eyeon-deals-<?= $unique_id ?>" class="eyeon-deals eyeon-loader">
  <div class="eyeon-wrapper eyeon-hide">
      <div id="deals-list-<?= $unique_id ?>" class="deals-list"></div>
    </div>
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

    function fetch_deals() {
      var limit = defaultLimit;
      if( settings.fetch_all !== 'yes' ) {
        var remainingLimit = settings.fetch_limit - (page - 1) * defaultLimit;
        limit = Math.min(remainingLimit, defaultLimit);
      }
      $.ajax({
        url: "<?= MCD_API_DEALS ?>",
        data: { limit, page },
        method: 'GET',
        dataType: 'json',
        headers: {
          center_id: '<?= $mcd_settings['center_id'] ?>'
        },
        success: function (response) {
          if (response.items) {
            deals = deals.concat(response.items);
            var fetchMore = false;
            if( settings.fetch_all !== 'yes' && page * defaultLimit < settings.fetch_limit ) {
              fetchMore = true;
            }
            if( settings.fetch_all === 'yes' && response.count > deals.length ) {
              fetchMore = true;
            }
            if( fetchMore ) {
              page++;
              fetch_deals();
            } else {
              render();
            }
          }
        }
      });
    }

    function render() {
      eyeonDeals.removeClass('eyeon-loader').find('.eyeon-wrapper').removeClass('eyeon-hide');

      dealsList.empty();

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
        eyeonDeals.find('.eyeon-wrapper').html(`
          <div class="no-items-found">More Deals Coming Soon.</div>
        `);
      }
    }
  });
</script>