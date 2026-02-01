<?php
$settings = $this->get_settings_for_display();

$fields = [
  'search_placeholder_text',
  'search_icon',
  'search_results_retailer_logo',
  'search_results_categories',
];
$filtered_settings = array_intersect_key($settings, array_flip($fields));
$unique_id = uniqid();
?>

<div id="eyeon-search-<?= $unique_id ?>" class="eyeon-search">
  <div class="eyeon-wrapper">
    <div class="search-bar">
      <?php if( $settings['search_icon'] === 'show' ) : ?>
        <span class="icon icon-search"></span>
      <?php endif; ?>
      <input type="text" id="stores-search-<?= $unique_id ?>" class="stores-search" placeholder="<?= $settings['search_placeholder_text'] ?>" />
      <div id="search-results-dropdown-<?= $unique_id ?>" class="search-results-dropdown"></div>
    </div>
  </div>
</div>

<script type="text/javascript">
  jQuery(document).ready(function($) {
    const settings = <?= json_encode($filtered_settings) ?>;
    const searchInput = $('#stores-search-<?= $unique_id ?>');
    const searchResults = $('#search-results-dropdown-<?= $unique_id ?>');

    let retailers = [];
    let retailersFetched = false;

    fetch_retailers();

    function fetch_retailers(force_refresh = false) {
      $.ajax({
        url: EYEON.ajaxurl+'?api=<?= MCD_API_STORES ?>',
        data: {
          action: 'eyeon_api_request',
          apiUrl: "<?= MCD_API_STORES ?>",
          paginated_data: true,
          force_refresh: force_refresh
        },
        method: "POST",
        dataType: 'json',
        xhrFields: {
          withCredentials: true
        },
        success: function (response) {
          if( response.stale_data ) {
            fetch_retailers(true);
          }
          if (response.items) {
            retailers = response.items;
            retailersFetched = true;
            filterRetailersBySearch(searchInput.val().toLowerCase());
          }
        }
      });
    }

    function getMultipleLocationsGlobalRetailerIds() {
      const retailerCount = retailers.reduce((acc, retailer) => {
        const id = retailer.global_retailer_id;
        acc[id] = (acc[id] || 0) + 1;
        return acc;
      }, {});

      const multipleOccurences = Object.keys(retailerCount).filter(id => retailerCount[id] > 1).map(id => Number(id));
      return multipleOccurences;
    }

    function getRetailerUrl(retailer, multipleLocationRetailer) {
      let retailer_url = `<?= mcd_single_page_url('mycenterstore') ?>${retailer.slug}`;
      let queryString = '';
      const params = {};
      if(multipleLocationRetailer) params.r = retailer.id;
      if (Object.keys(params).length > 0) {
        queryString = Object.entries(params)
          .map(([key, value]) => `${encodeURIComponent(key)}=${encodeURIComponent(value)}`)
          .join('&');

        queryString = `?${queryString}`;
      }
      return `${retailer_url}${queryString}`
    }

    function filterRetailersBySearch(search) {
      if (!search) {
        searchResults.removeClass('show').empty();
        return;
      }

      if (!retailersFetched) {
        searchResults.html('<div class="search-loading">Loading...</div>').addClass('show');
        return;
      }

      const multipleLocationsGlobalRetailerIds = getMultipleLocationsGlobalRetailerIds();
      
      // First, get all matching retailers
      const matchingRetailers = retailers.filter(retailer => 
        retailer.name.toLowerCase().includes(search) || 
        retailer.tags.some(tag => tag.name.toLowerCase().includes(search)) ||
        retailer.categories.some(cat => cat.name.toLowerCase().includes(search)) ||
        (retailer.description && retailer.description.toLowerCase().includes(search))
      );

      // Sort retailers to prioritize name matches
      const sortedRetailers = matchingRetailers.sort((a, b) => {
        const aNameMatch = a.name.toLowerCase().includes(search);
        const bNameMatch = b.name.toLowerCase().includes(search);
        
        if (aNameMatch && !bNameMatch) return -1;
        if (!aNameMatch && bNameMatch) return 1;
        
        // If both match by name or both don't match by name, keep original order
        return 0;
      });

      searchResults.empty();
      
      if (sortedRetailers.length > 0) {
        sortedRetailers.forEach(retailer => {
          const multipleLocationRetailer = multipleLocationsGlobalRetailerIds.includes(retailer.global_retailer_id);
          const retailerUrl = getRetailerUrl(retailer, multipleLocationRetailer);
          
          const categoryNames = retailer.categories.map(cat => cat.name).join(', ');
          
          const resultItem = $(`
            <a href="${retailerUrl}" class="search-result-item">
              ${settings.search_results_retailer_logo === 'show' ? `<img src="${retailer.media.url}" alt="${retailer.name}" />` : ''}
              <div class="retailer-info">
                <div class="retailer-name">${retailer.name}</div>
                ${settings.search_results_categories === 'show' ? `<div class="retailer-category">${categoryNames}</div>` : ''}
              </div>
            </a>
          `);
          
          // Add click handler to result item
          resultItem.on('click', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            searchResults.removeClass('show');
            $('body').css('opacity', '0.5');
            setTimeout(() => {
              window.location.href = url;
            }, 300);
          });
          
          searchResults.append(resultItem);
        });
        searchResults.addClass('show');
      } else {
        searchResults.html('<div class="search-no-results">No results found</div>').addClass('show');
      }
    }

    searchInput.on('input', function() {
      const search = $(this).val().toLowerCase();
      filterRetailersBySearch(search);
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
      if (!$(e.target).closest('.search-bar').length) {
        searchResults.removeClass('show');
      }
    });

    // Handle keyboard navigation
    searchInput.on('keydown', function(e) {
      const visibleItems = searchResults.find('.search-result-item');
      const currentIndex = visibleItems.index(visibleItems.filter(':focus'));
      
      switch(e.key) {
        case 'ArrowDown':
          e.preventDefault();
          if (currentIndex < visibleItems.length - 1) {
            visibleItems.eq(currentIndex + 1).focus();
          } else {
            visibleItems.first().focus();
          }
          break;
        case 'ArrowUp':
          e.preventDefault();
          if (currentIndex > 0) {
            visibleItems.eq(currentIndex - 1).focus();
          } else {
            visibleItems.last().focus();
          }
          break;
        case 'Enter':
          if (currentIndex >= 0) {
            visibleItems.eq(currentIndex)[0].click();
          }
          break;
        case 'Escape':
          searchResults.removeClass('show');
          break;
      }
    });
  });
</script>