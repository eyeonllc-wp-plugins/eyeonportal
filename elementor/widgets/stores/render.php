<?php
$settings = $this->get_settings_for_display();

$fields = [
  'fetch_all',
  'fetch_limit',
  'deal_flag',
  'custom_flags',
  'retailer_categories',
  'retailer_tags',
  'featured_image',
  'no_results_found_text',
  // 'retailer_location_position',
];
$filtered_settings = array_intersect_key($settings, array_flip(array_merge($fields, get_carousel_fields())));
$unique_id = uniqid();

$custom_center_id = null;
$center_id = $mcd_settings['center_id'];
if( isset($settings['custom_center_id']) && !empty($settings['custom_center_id']) ) {
  $center_id = $settings['custom_center_id'];
  $custom_center_id = $center_id;
}

// eyeon_debug($filtered_settings, false);
?>

<div id="eyeon-stores-<?= $unique_id ?>" class="eyeon-stores eyeon-loader">
  <div class="eyeon-wrapper" style="display:none;">

    <?php if( $settings['view_mode'] === 'grid' ) : ?>
      <div class="stores-header">
        <?php if( $settings['categories_sidebar'] === 'show' ) : ?>
          <div class="categories-sidebar-placeholder"></div>
        <?php endif; ?>
        
        <div class="search-bar">
          <?php if( $settings['stores_search'] === 'show' ) : ?>
            <input type="text" id="stores-search-<?= $unique_id ?>" class="stores-search" placeholder="Search..." />
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="<?= ($settings['view_mode']==='grid'?'content-cols':'') ?>">
      <?php if( $settings['categories_sidebar'] === 'show' ) : ?>
      <div class="stores-categories">
        <select id="stores-categories-dropdown-<?= $unique_id ?>" class="show-on-mob">
          <option value="all" selected><?= in_array(RESTAURANTS_CATEGORY_ID, $settings['retailer_categories'])?'All Restaurants':'All Stores' ?></option>
        </select>
        <ul id="stores-categories-<?= $unique_id ?>" class="hide-on-mob">
          <li data-value="all" class="active"><?= in_array(RESTAURANTS_CATEGORY_ID, $settings['retailer_categories'])?'All Restaurants':'All Stores' ?></li>
        </ul>
      </div>
      <?php endif; ?>
      
      <?php
      $classes = $settings['hover_style'];
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
      <div class="stores-list">
        <div id="stores-list-<?= $unique_id ?>" class="stores <?= $classes ?>"></div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  jQuery(document).ready(function($) {
    const settings = <?= json_encode($filtered_settings) ?>;
    settings.retailer_categories = settings.retailer_categories.map(function (item) {
      return parseInt(item);
    });

    const eyeonStores = $('#eyeon-stores-<?= $unique_id ?>');
    const categoryList = $('#stores-categories-<?= $unique_id ?>');
    const categoryDropdownList = $('#stores-categories-dropdown-<?= $unique_id ?>');
    const searchInput = $('#stores-search-<?= $unique_id ?>');
    const retailersList = $('#stores-list-<?= $unique_id ?>');

    let retailers = [];
    var page = 1;
    var defaultLimit = 100;
    let categories = [];
    let retailersFetched = false;
    let categoriesFetched = false;

    fetch_retailers();
    fetch_categories();

    function fetch_retailers() {
      var limit = defaultLimit;
      if( settings.fetch_all !== 'yes' ) {
        var remainingLimit = settings.fetch_limit - (page - 1) * defaultLimit;
        limit = Math.min(remainingLimit, defaultLimit);
      }
      const ajaxData = {
        limit,
        page,
        category_ids: [],
        tag_ids: [],
      };
      $.each(settings.retailer_categories, function(index, category) {
        ajaxData.category_ids.push(category);
      });
      $.each(settings.retailer_tags, function(index, tag) {
        const parseTag = JSON.parse(tag);
        ajaxData.tag_ids.push(parseTag.id);
      });

      $.ajax({
        url: "<?= MCD_API_STORES ?>",
        data: ajaxData,
        method: 'GET',
        dataType: 'json',
        headers: {
          center_id: '<?= $center_id ?>'
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
            if( fetchMore ) {
              page++;
              fetch_retailers();
            } else {
              retailersFetched = true;
              setup_categories();
            }
          }
        }
      });
    }

    function fetch_categories() {
      $.ajax({
        url: "<?= MCD_API_STORES.'/categories' ?>",
        data: {
          limit: 100,
          page: 1,
          group: true
        },
        method: 'GET',
        dataType: 'json',
        headers: {
          center_id: '<?= $center_id ?>'
        },
        success: function (response) {
          if (response.items) {
            categoriesFetched = true;
            responseCategories = response.items;
            <?php if( in_array(RESTAURANTS_CATEGORY_ID, $settings['retailer_categories']) ) : ?>
              $.each(response.items, function(index, category) {
                if (category.id === <?= RESTAURANTS_CATEGORY_ID ?>) {
                  responseCategories = category.children;
                }
              });
            <?php endif; ?>

            $.each(responseCategories, function (index, item) {
              categories.push({
                id: item.id,
                name: item.name,
                display: false,
              });
            });
            setup_categories();
          }
        }
      });
    }

    function setup_categories() {
      if( !retailersFetched || !categoriesFetched ) return false;

      retailers.forEach(retailer => {
        retailer.categories.forEach(category => {
          updateCategoryDisplay(category.id);
        });
      });

      <?php if( $settings['categories_sidebar'] === 'show' ) : ?>
      categories.forEach(category => {
        if( category.display ) {
          categoryDropdownList.append(`
            <option value="${category.name.toLowerCase()}">${category.name}</option>
          `);
          categoryList.append(`
            <li data-value="${category.name.toLowerCase()}">${category.name}</li>
          `);
        }
      });
      <?php endif; ?>

      eyeonStores.removeClass('eyeon-loader').find('.eyeon-wrapper').removeAttr('style');
      renderRetailers();
    }

    function updateCategoryDisplay(categoryId) {
      $.each(categories, function(index, category) {
        if (category.id === categoryId) {
          category.display = true;
        }
      });
    }

    function getRepeatedGlobalRetailersIds() {
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
      const params = {
        <?= ($custom_center_id!==null?'c:'.$custom_center_id:'') ?>
      };
      if(multipleLocationRetailer) params.r = retailer.id;
      if (Object.keys(params).length > 0) {
        queryString = Object.entries(params)
          .map(([key, value]) => `${encodeURIComponent(key)}=${encodeURIComponent(value)}`)
          .join('&');

        queryString = `?${queryString}`;
      }
      return `${retailer_url}${queryString}`
    }

    function renderRetailers() {
      retailersList.empty();

      const repeatedGlobalRetailersIds = getRepeatedGlobalRetailersIds();

      if( retailers.length > 0 ) {
        retailers.forEach(retailer => {
          const multipleLocationRetailer = repeatedGlobalRetailersIds.includes(retailer.global_retailer_id);
          
          const retailerItem = $(`
            <a href="${getRetailerUrl(retailer, multipleLocationRetailer)}" class="store store-${retailer.id}">
              <div class="image ${(settings.featured_image === 'show')?'show-featured-image':''}">
                <img class="retailer-logo" src="${retailer.media.url}" alt="${retailer.name}" />
                ${(settings.deal_flag === 'show' && retailer.deals && retailer.deals > 0) ? '<span class="deal-flag">Deal</span>' : ''}
                ${(settings.custom_flags === 'show' && retailer.custom_flags && retailer.custom_flags.length > 0) ? `
                  <ul class="custom-flags">
                    ${retailer.custom_flags.map(flag => `<li>${flag.name}</li>`).join('')}
                  </ul>
                ` : ''}
                ${multipleLocationRetailer ? `<span class="retailer-location">${retailer.location}</span>` : ''}
                ${(settings.featured_image === 'show') ? `<img class="featured-image" src="${retailer.featured_image.url}" alt="${retailer.name}" />` : ''}
              </div>
            </a>
          `);
          retailersList.append(retailerItem);
        });
        filterRetailersByCategoryAndSearch('all', '');
        
        <?php include(MCD_PLUGIN_PATH.'elementor/widgets/common/carousel/setup-js.php'); ?>
      } else {
        eyeonStores.find('.eyeon-wrapper').html(`
          <div class="no-items-found">${settings.no_results_found_text}</div>
        `);
      }
      
      if( retailers.length > 0 && elementorFrontend.config.environmentMode.edit) {
        eyeonStores.find('.eyeon-wrapper').append(`
          <div class="no-items-found">${settings.no_results_found_text}</div>
        `);
      }

    }

    function filterRetailersByCategoryAndSearch(category, search) {
      retailers.forEach(retailer => {
        retailersList.find('.store.store-'+retailer.id).addClass('eyeon-hide');
        if (category === 'all' || retailer.categories.some(cat => cat.name.toLowerCase() === category)) {
          if (search === '' || retailer.name.toLowerCase().includes(search)) {
            retailersList.find('.store.store-'+retailer.id).removeClass('eyeon-hide');
          }
        }
      });
    }

    // Event listeners for filter and search
    categoryList.on('click', 'li', function() {
      categoryList.find('li.active').removeClass('active');
      $(this).addClass('active');
      const selectedCategory = $(this).attr('data-value');
      let search = '';
      if( searchInput.length > 0 ) {
        search = searchInput.val().toLowerCase();
      }

      categoryDropdownList.val(selectedCategory);
      filterRetailersByCategoryAndSearch(selectedCategory, search);
    });

    categoryDropdownList.on('change', function() {
      const selectedCategory = $(this).val();
      let search = '';
      if( searchInput.length > 0 ) {
        search = searchInput.val().toLowerCase();
      }

      // change categories list selection
      categoryList.find('li.active').removeClass('active');
      categoryList.find('li[data-value="'+selectedCategory+'"]').addClass('active');

      filterRetailersByCategoryAndSearch(selectedCategory, search);
    });

    searchInput.on('input', function() {
      const selectedCategory = categoryList.find('li.active:first').attr('data-value');
      const search = $(this).val().toLowerCase();
      filterRetailersByCategoryAndSearch(selectedCategory, search);
    });
  });
</script>