<?php
$settings = $this->get_settings_for_display();

$filtered_settings = array_intersect_key($settings, array_flip([
  'fetch_all',
  'fetch_limit',
  'deal_flag',
  'retailer_categories',
  'retailer_tags',
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
    <!-- <input type="text" id="stores-search-<?= $unique_id ?>" class="stores-search eyeon-hide" placeholder="Search..." /> -->

    <div class="<?= ($settings['view_mode']==='grid'?'content-cols':'') ?>">
      <?php if( $settings['categories_sidebar'] === 'show' ) : ?>
      <div class="stores-categories">
        <ul id="stores-categories-<?= $unique_id ?>">
          <li data-value="all" class="active">All Stores</li>
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

    const eyeonStores = $('#eyeon-stores-<?= $unique_id ?>');
    const categoryList = $('#stores-categories-<?= $unique_id ?>');
    const searchInput = $('#stores-search-<?= $unique_id ?>');
    const retailersList = $('#stores-list-<?= $unique_id ?>');

    let retailers = [];
    var page = 1;
    var defaultLimit = 100;
    let categories = new Set([]);
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
      const ajaxData = {
        limit,
        page,
        category_ids: [],
        tag_ids: [],
      };
      $.each(settings.retailer_categories, function(index, category) {
        const parseCategory = JSON.parse(category);
        ajaxData.category_ids.push(parseCategory.id);
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

    function setup_categories() {
      eyeonStores.removeClass('eyeon-loader').find('.eyeon-wrapper').removeClass('eyeon-hide');

      retailers.forEach(retailer => {
        if (retailer.categories.length === 0) {
          retailer.categories.push(otherCategory);
        }

        retailer.categories.forEach(category => {
          categories.add(category.name);
        });
      });

      const categoriesArray = Array.from(categories);
      categoriesArray.sort();
      categories = new Set(categoriesArray);

      <?php if( $settings['categories_sidebar'] === 'show' ) : ?>
      categories.forEach(category => {
        categoryList.append(`
          <li data-value="${category.toLowerCase()}">${category}</li>
        `);
      });
      <?php endif; ?>

      render('all', '');
    }

    function render(category, search) {
      retailersList.empty();

      if( retailers.length > 0 ) {
        retailers.forEach(retailer => {
          if (category === 'all' || retailer.categories.some(cat => cat.name.toLowerCase() === category)) {
            if (search === '' || retailer.name.toLowerCase().includes(search)) {
              const retailerItem = $(`
                <a href="<?= mcd_single_page_url('mycenterstore') ?>${retailer.slug}" class="store">
                  <div class="image">
                    <img src="${retailer.media.url}" alt="${retailer.name}" />
                    ${(settings.deal_flag === 'show' && retailer.deals && retailer.deals > 0) ? '<span class="deal-flag">Deal</span>' : ''}
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