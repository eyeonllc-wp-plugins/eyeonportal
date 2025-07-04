<?php
$settings = $this->get_settings_for_display();

$fields = [
  'fetch_all',
  'fetch_limit',
  'deal_flag',
  'custom_flags',
  'categories_sidebar',
  'retailer_categories',
  'retailer_tags',
  'featured_image',
  'no_results_found_text',
  'header_heading_show',
];
$filtered_settings = array_intersect_key($settings, array_flip(array_merge($fields, get_carousel_fields())));
$unique_id = uniqid();

$custom_center_id = null;
$center_id = $mcd_settings['center_id'];
if( isset($settings['custom_center_id']) && !empty($settings['custom_center_id']) ) {
  $center_id = $settings['custom_center_id'];
  $custom_center_id = $center_id;
}
?>

<div id="eyeon-stores-<?= $unique_id ?>" class="eyeon-stores eyeon-loader">
  <div class="eyeon-wrapper" style="display:none;">

    <?php if( $settings['view_mode'] === 'grid' ) : ?>
      <div class="stores-header <?= $settings['categories_sidebar'] === 'dropdown'?'with-dropdown':'' ?>">
        <?php if( $settings['categories_sidebar'] === 'show' ) : ?>
          <div class="categories-sidebar-placeholder hide-on-mob"></div>
        <?php endif; ?>

        <?php if( $settings['categories_sidebar'] === 'show' || $settings['categories_sidebar'] === 'dropdown' ) : ?>
          <div class="stores-categories-select <?= $settings['categories_sidebar'] === 'show'?'show-on-mob':'' ?>">
            <div class="custom-select-wrapper">
              <div class="custom-select">
                <div class="custom-select__trigger">
                  <!-- <span><?= in_array(RESTAURANTS_CATEGORY_ID, $settings['retailer_categories'])?'All Restaurants':'All Stores' ?></span> -->
                  <span>Categories</span>
                  <svg aria-hidden="true" class="e-font-icon-svg e-fas-angle-down" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg"><path d="M143 352.3L7 216.3c-9.4-9.4-9.4-24.6 0-33.9l22.6-22.6c9.4-9.4 24.6-9.4 33.9 0l96.4 96.4 96.4-96.4c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9l-136 136c-9.2 9.4-24.4 9.4-33.8 0z"></path></svg>
                </div>
                <div class="custom-options">
                  <span class="custom-option selected" data-value="all">
                    Categories
                  </span>
                </div>
              </div>
              <select id="stores-categories-dropdown-<?= $unique_id ?>" class="hidden-select">
                <option value="all" selected>Categories</option>
              </select>
            </div>
          </div>
        <?php endif; ?>

        <?php if( $settings['categories_sidebar'] === 'dropdown' && $settings['header_heading_show'] === 'show' ) : ?>
          <span class="stores-directory-heading">
            <?php if( !empty($settings['header_heading_link']['url']) ) : ?>
              <a href="<?= $settings['header_heading_link']['url'] ?>" target="<?= $settings['header_heading_link']['is_external']?'_blank':'_self' ?>" <?= $settings['header_heading_link']['nofollow']?'rel="nofollow"':'' ?>>
                <span><?= $settings['header_heading_text'] ?></span>
              </a>
            <?php else : ?>
              <span><?= $settings['header_heading_text'] ?></span>
            <?php endif; ?>
            </span>
        <?php endif; ?>
        
        <div class="search-bar">
          <?php if( $settings['stores_search'] === 'show' ) : ?>
            <span class="icon icon-search"></span>
            <input type="text" id="stores-search-<?= $unique_id ?>" class="stores-search" placeholder="Search..." />
          <?php endif; ?>
        </div>

        <?php if( $settings['categories_sidebar'] === 'dropdown' && !$settings['header_heading_show'] ) : ?>
          <div class="heading-heading-placeholder"></div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="<?= ($settings['view_mode']==='grid'?'content-cols':'') ?>">
      <?php if( @$settings['categories_sidebar'] === 'show' ) : ?>
      <div class="stores-categories hide-on-mob">
        <ul id="stores-categories-<?= $unique_id ?>">
          <li data-value="all" class="active">Categories</li>
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

      if( categoryDropdownList.length > 0 ) {
        categories.forEach(category => {
          if( category.display ) {
            categoryDropdownList.append(`
              <option value="${category.name.toLowerCase()}">${category.name}</option>
            `);

            // Add to custom select options
            const customOptions = document.querySelector('.custom-options');
            customOptions.insertAdjacentHTML('beforeend', `
              <span class="custom-option" data-value="${category.name.toLowerCase()}">
                ${category.name}
              </span>
            `);

            categoryList.append(`
              <li data-value="${category.name.toLowerCase()}">${category.name}</li>
            `);
          }
        });
      }

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

      const multipleLocationsGlobalRetailerIds = getMultipleLocationsGlobalRetailerIds();

      if( retailers.length > 0 ) {
        retailers.forEach(retailer => {
          const multipleLocationRetailer = multipleLocationsGlobalRetailerIds.includes(retailer.global_retailer_id);
          
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
          if (search === '' || 
              retailer.name.toLowerCase().includes(search) || 
              retailer.tags.some(tag => tag.name.toLowerCase().includes(search)) ||
              retailer.categories.some(cat => cat.name.toLowerCase().includes(search)) ||
              (retailer.description && retailer.description.toLowerCase().includes(search))) {
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

    document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
      const select = wrapper.querySelector('select');
      const customSelect = wrapper.querySelector('.custom-select');
      const customTrigger = wrapper.querySelector('.custom-select__trigger');
      const customOptions = wrapper.querySelector('.custom-options');

      // Toggle custom select
      customTrigger.addEventListener('click', () => {
        customSelect.classList.toggle('open');
      });

      // Close when clicking outside
      document.addEventListener('click', (e) => {
        if (!wrapper.contains(e.target)) {
          customSelect.classList.remove('open');
        }
      });

      // Handle option selection
      customOptions.addEventListener('click', (e) => {
        const option = e.target.closest('.custom-option');
        if (!option) return;

        // Update selected option
        wrapper.querySelectorAll('.custom-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        option.classList.add('selected');

        // Update trigger text
        customTrigger.querySelector('span').textContent = option.textContent;

        // Update hidden select
        select.value = option.dataset.value;
        select.dispatchEvent(new Event('change'));

        // Close dropdown
        customSelect.classList.remove('open');
      });
    });

    searchInput.on('input', function() {
      const selectedCategory = categoryDropdownList.val();
      const search = $(this).val().toLowerCase();
      filterRetailersByCategoryAndSearch(selectedCategory, search);
    });
  });
</script>