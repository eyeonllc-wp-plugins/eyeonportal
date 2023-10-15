<?php
//eyeon_debug($settings, false);
$unique_id = uniqid();
?>

<div class="eyeon-stores">
  <input type="text" id="stores-search-<?= $unique_id ?>" class="stores-search" placeholder="Search..." />

  <select id="stores-categories-<?= $unique_id ?>" class="stores-categories">
    <option value="all">All Retailers</option>
  </select>
    
  <div id="stores-list-<?= $unique_id ?>" class="stores-list"></div>
</div>

<script type="text/javascript">
  jQuery(document).ready(function($) {
    const settings = <?= json_encode($settings) ?>;
    const categoryFilter = $('#stores-categories-<?= $unique_id ?>');
    const searchInput = $('#stores-search-<?= $unique_id ?>');
    const retailersList = $('#stores-list-<?= $unique_id ?>');

    let retailers = [];
    var page = 1;
    var defaultLimit = 100;
    const categories = new Set([]);
    const otherCategory = {
      name: 'Others'
    };

    fetch_retailers();

    function fetch_retailers() {
      var limit = defaultLimit;
      if( settings.fetch_all !== 'yes' ) {
        var remainingLimit = settings.fetch_limit - (page - 1) * defaultLimit;
        limit = Math.min(remainingLimit, defaultLimit);
      }
      $.ajax({
        url: "<?= MCD_API_STORES ?>",
        data: { limit, page },
        method: 'GET',
        dataType: 'json',
        headers: {
          center_id: '<?= $mcd_settings['center_id'] ?>'
        },
        success: function (response) {
          if (response.items) {
            retailers = retailers.concat(response.items);
            var fetchMore = false;
            if( settings.fetch_all !== 'yes' && page * defaultLimit < settings.fetch_limit ) {
              fetchMore = true;
            }
            if( settings.fetch_all === 'yes' && response.count > retailers.length ) {
              fetchMore = true;
            }
            console.log('angrej123 fetchMore', fetchMore);
            if( fetchMore ) {
              page++;
              fetch_retailers();
            } else {
              setup_categories();
            }
          }
        }
      });
    }

    function setup_categories() {
      retailers.forEach(retailer => {
        if (retailer.categories.length === 0) {
          retailer.categories.push(otherCategory);
        }

        retailer.categories.forEach(category => {
          categories.add(category.name);
        });
      });

      categories.forEach(category => {
        categoryFilter.append($('<option>', {
          value: category.toLowerCase(),
          text: category
        }));
      });

      console.log('angrej123 categoryFilter', categoryFilter);

      renderRetailers('all', '');
    }

    // Function to render the retailers list based on category and search
    function renderRetailers(category, search) {
      retailersList.empty();

      console.log('angrej123 retailers', retailers);

      retailers.forEach(retailer => {
        if (category === 'all' || retailer.categories.some(cat => cat.name.toLowerCase() === category)) {
          if (search === '' || retailer.name.toLowerCase().includes(search)) {
            const retailerItem = $(`
              <div class="eyeon-store">
                <div class="eyeon-store-image">
                  <img src="${retailer.media.url}" alt="${retailer.name}" />
                </div>
              </li>
            `);
            retailersList.append(retailerItem);
          }
        }
      });
    }

    // Event listeners for filter and search
    categoryFilter.on('change', function() {
      const selectedCategory = $(this).val();
      const search = searchInput.val().toLowerCase();
      renderRetailers(selectedCategory, search);
    });

    searchInput.on('input', function() {
      const selectedCategory = categoryFilter.val();
      const search = $(this).val().toLowerCase();
      renderRetailers(selectedCategory, search);
    });

  });
</script>