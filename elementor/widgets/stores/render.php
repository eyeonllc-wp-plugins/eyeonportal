<?php
$settings = $this->get_settings_for_display();

$filtered_settings = array_intersect_key($settings, array_flip([
  'fetch_all',
  'fetch_limit',
  'deal_flag',
  'retailer_categories',
  'retailer_tags',
  'featured_image',
  'view_mode',
  'carousel_items',
  'carousel_items_tablet',
  'carousel_items_mobile',
  'carousel_dots',
  'carousel_navigation',
  'carousel_autoplay',
  'carousel_autoplay_speed',
  'carousel_slideby',
  'carousel_slideby_tablet',
  'carousel_slideby_mobile',
  'carousel_margin',
  'carousel_margin_tablet',
  'carousel_margin_mobile',
  'carousel_loop'
]));
$unique_id = uniqid();
?>

<div id="eyeon-stores-<?= $unique_id ?>" class="eyeon-stores eyeon-loader">
  <div class="eyeon-wrapper eyeon-hide">
    <input type="text" id="stores-search-<?= $unique_id ?>" class="stores-search eyeon-hide" placeholder="Search..." />

    <div class="<?= ($settings['view_mode']==='grid'?'content-cols':'') ?>">
      <?php if( $settings['categories_sidebar'] === 'show' ) : ?>
      <div class="stores-categories">
        <ul id="stores-categories-<?= $unique_id ?>">
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
          center_id: '<?= $mcd_settings['center_id'] ?>'
        },
        success: function (response) {
          if (response.items) {
            retailersFetched = true;
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
          center_id: '<?= $mcd_settings['center_id'] ?>'
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

      eyeonStores.removeClass('eyeon-loader').find('.eyeon-wrapper').removeClass('eyeon-hide');

      retailers.forEach(retailer => {
        retailer.categories.forEach(category => {
          updateCategoryDisplay(category.id);
        });
      });

      <?php if( $settings['categories_sidebar'] === 'show' ) : ?>
      categories.forEach(category => {
        if( category.display ) {
          categoryList.append(`
            <li data-value="${category.name.toLowerCase()}">${category.name}</li>
          `);
        }
      });
      <?php endif; ?>

      render('all', '');
    }

    function updateCategoryDisplay(categoryId) {
      $.each(categories, function(index, category) {
        if (category.id === categoryId) {
          category.display = true;
        }
      });
    }

    function render(category, search) {
      retailersList.empty();

      if( retailers.length > 0 ) {
        retailers.forEach(retailer => {
          if (category === 'all' || retailer.categories.some(cat => cat.name.toLowerCase() === category)) {
            if (search === '' || retailer.name.toLowerCase().includes(search)) {
              const retailerItem = $(`
                <a href="<?= mcd_single_page_url('mycenterstore') ?>${retailer.slug}" class="store">
                  <div class="image ${(settings.featured_image === 'show')?'show-featured-image':''}">
                    <img class="retailer-logo" src="${retailer.media.url}" alt="${retailer.name}" />
                    ${(settings.deal_flag === 'show' && retailer.deals && retailer.deals > 0) ? '<span class="deal-flag">Deal</span>' : ''}
                    ${(settings.featured_image === 'show') ? `<img class="featured-image" src="${retailer.featured_image.url}" alt="${retailer.name}" />` : ''}
                  </div>
                  <!--<h3 class="store-name">${retailer.name}</h3>-->
                </a>
              `);
              retailersList.append(retailerItem);
            }
          }
        });
        
        <?php include(MCD_PLUGIN_PATH.'elementor/widgets/common/carousel/setup-js.php'); ?>
      } else {
        eyeonStores.find('.eyeon-wrapper').html(`
          <div class="no-items-found">No items found.</div>
        `);
      }
    }

    // Event listeners for filter and search
    categoryList.on('click', 'li', function() {
      categoryList.find('li.active').removeClass('active');
      $(this).addClass('active');
      const selectedCategory = $(this).attr('data-value');
      const search = searchInput.val().toLowerCase();
      render(selectedCategory, search);
    });

    searchInput.on('input', function() {
      const selectedCategory = categoryList.find('ul li.active:first').attr('data-value');
      const search = $(this).val().toLowerCase();
      render(selectedCategory, search);
    });
  });
</script>