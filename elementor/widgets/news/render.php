<?php
$settings = $this->get_settings_for_display();

$filtered_settings = array_intersect_key($settings, array_flip(array_merge([
  'fetch_all',
  'fetch_limit',
], get_carousel_fields())));
$unique_id = uniqid();
?>

<div id="eyeon-news-<?= $unique_id ?>" class="eyeon-news eyeon-loader">
  <div class="eyeon-wrapper" style="display:none;">
    <div class="<?= ($settings['view_mode']==='grid'?'content-cols':'') ?>">
      <?php if( $settings['categories_sidebar'] === 'show' ) : ?>
      <div class="news-categories">
        <select id="news-categories-dropdown-<?= $unique_id ?>" class="show-on-mob">
          <option value="all" selected>All</option>
        </select>
        <ul id="news-categories-<?= $unique_id ?>" class="hide-on-mob">
          <li data-value="all" class="active">All</li>
        </ul>
      </div>
      <?php endif; ?>
      
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
      <div class="news-list">
        <div id="news-list-<?= $unique_id ?>" class="news <?= $classes ?>"></div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  jQuery(document).ready(function($) {
    const settings = <?= json_encode($filtered_settings) ?>;

    const eyeonNews = $('#eyeon-news-<?= $unique_id ?>');
    const categoryList = $('#news-categories-<?= $unique_id ?>');
    const categoryDropdownList = $('#news-categories-dropdown-<?= $unique_id ?>');
    const searchInput = $('#news-search-<?= $unique_id ?>');
    const newsList = $('#news-list-<?= $unique_id ?>');

    let news = [];
    var page = 1;
    var defaultLimit = 100;
    let categories = [];
    let retailersFetched = false;
    let categoriesFetched = false;

    fetch_news();

    function fetch_news() {
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
        url: "<?= MCD_API_NEWS ?>",
        data: ajaxData,
        method: 'GET',
        dataType: 'json',
        headers: {
          center_id: '<?= $mcd_settings['center_id'] ?>'
        },
        success: function (response) {
          if (response.items) {
            news = news.concat(response.items);
            var fetchMore = false;
            if( settings.fetch_all !== 'yes' && page * defaultLimit < settings.fetch_limit ) {
              fetchMore = true;
            }
            if( settings.fetch_all === 'yes' && response.count > news.length ) {
              fetchMore = true;
            }
            if( fetchMore ) {
              page++;
              fetch_news();
            } else {
              setup_categories();
            }
          }
        }
      });
    }

    function setup_categories() {
      console.log('news', news);
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

    function renderRetailers() {
      retailersList.empty();

      if( retailers.length > 0 ) {
        retailers.forEach(retailer => {
          const retailerItem = $(`
            <a href="<?= mcd_single_page_url('mycenterstore') ?>${retailer.slug}" class="store store-${retailer.id}">
              <div class="image ${(settings.featured_image === 'show')?'show-featured-image':''}">
                <img class="retailer-logo" src="${retailer.media.url}" alt="${retailer.name}" />
                ${(settings.deal_flag === 'show' && retailer.deals && retailer.deals > 0) ? '<span class="deal-flag">Deal</span>' : ''}
                ${(settings.custom_flags === 'show' && retailer.custom_flags && retailer.custom_flags.length > 0) ? `
                  <ul class="custom-flags">
                    ${retailer.custom_flags.map(flag => `<li>${flag}</li>`).join('')}
                  </ul>
                ` : ''}
                ${(settings.featured_image === 'show') ? `<img class="featured-image" src="${retailer.featured_image.url}" alt="${retailer.name}" />` : ''}
              </div>
              <!--<h3 class="store-name">${retailer.name}</h3>-->
            </a>
          `);
          retailersList.append(retailerItem);
        });
        filterRetailersByCategoryAndSearch('all', '');
        
        <?php include(MCD_PLUGIN_PATH.'elementor/widgets/common/carousel/setup-js.php'); ?>
      } else {
        eyeonStores.find('.eyeon-wrapper').html(`
          <div class="no-items-found">No retailers found.</div>
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
      const search = searchInput.val().toLowerCase();

      categoryDropdownList.val(selectedCategory);

      filterRetailersByCategoryAndSearch(selectedCategory, search);
    });

    categoryDropdownList.on('change', function() {
      const selectedCategory = $(this).val();
      const search = searchInput.val().toLowerCase();

      // change categories list selection
      categoryList.find('li.active').removeClass('active');
      categoryList.find('li[data-value="'+selectedCategory+'"]').addClass('active');

      filterRetailersByCategoryAndSearch(selectedCategory, search);
    });

    searchInput.on('input', function() {
      const selectedCategory = categoryList.find('ul li.active:first').attr('data-value');
      const search = $(this).val().toLowerCase();
      filterRetailersByCategoryAndSearch(selectedCategory, search);
    });
  });
</script>