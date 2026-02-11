<?php
$settings = $this->get_settings_for_display();
$fields = [
  'fetch_all',
  'fetch_limit',
  'show_post_date',
  'news_category',
  'no_results_found_text',
];
$filtered_settings = array_intersect_key($settings, array_flip(array_merge($fields, get_carousel_fields())));
$unique_id = uniqid();
?>

<div id="eyeon-news-<?= $unique_id ?>" class="eyeon-news eyeon-loader">
  <div class="eyeon-wrapper eyeon-hide">
    <?php if( $settings['categories_filters'] === 'show' ) : ?>
    <div class="news-categories">
      <select id="news-categories-dropdown-<?= $unique_id ?>" class="show-on-mob"></select>
      <ul id="news-categories-<?= $unique_id ?>" class="hide-on-mob"></ul>
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

<script type="text/javascript">
  jQuery(document).ready(function($) {
    const settings = <?= json_encode($filtered_settings) ?>;

    const eyeonNews = $('#eyeon-news-<?= $unique_id ?>');
    const categoryList = $('#news-categories-<?= $unique_id ?>');
    const categoryDropdownList = $('#news-categories-dropdown-<?= $unique_id ?>');
    const newsList = $('#news-list-<?= $unique_id ?>');

    let news = [];
    let categories = [];

    fetch_news();

    function fetch_news(force_refresh = false) {
      const news_category = parseInt(settings.news_category);

      $.ajax({
        url: EYEON.ajaxurl+'?api=<?= MCD_API_NEWS ?>',
        data: {
          action: 'eyeon_api_request',
          nonce: EYEON.nonce,
          apiUrl: "<?= MCD_API_NEWS ?>",
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
            let allNews = response.items;
            
            // Filter by category (if specific category selected)
            if (news_category > 0) {
              allNews = allNews.filter(function(item) {
                if (!item.categories || item.categories.length === 0) return false;
                return item.categories.some(function(cat) {
                  return cat.id === news_category;
                });
              });
            }
            
            // Apply fetch_limit after filtering (if not fetching all)
            if (settings.fetch_all !== 'yes' && settings.fetch_limit > 0) {
              allNews = allNews.slice(0, settings.fetch_limit);
            }
            
            news = allNews;
            
            <?php if( $settings['categories_filters'] === 'show' ) : ?>
              setup_categories();
            <?php else : ?>
              renderNews();
            <?php endif; ?>
          }
          
          if (response.stale_data) {
            fetch_news(true);
          }            
        }
      });
    }

    function setup_categories() {
      let fetchedCategories = [];
      news.forEach(item => {
        item.categories.forEach(category => {
          if( !(fetchedCategories.some(cat => cat.id === category.id)) ) {
            fetchedCategories.push({
              id: category.id,
              name: category.name,
            });
          }
        });
      });

      fetchedCategories = fetchedCategories.sort(function (a, b) {
        var nameA = a.name.toUpperCase();
        var nameB = b.name.toUpperCase();
        if (nameA < nameB) return -1;
        if (nameA > nameB) return 1;
        return 0;
      });

      categories = [{id: 0, name: 'All'}].concat(fetchedCategories);

      categoryList.html('');
      categoryDropdownList.html('');

      categories.forEach(category => {
        categoryList.append(`
          <li data-value="${category.id}" class="${category.id===0?'active':''}">${category.name}</li>
        `);
        categoryDropdownList.append(`
          <option value="${category.id}">${category.name}</option>
        `);
      });

      renderNews();
    }
    
    function renderNews() {
      eyeonNews.removeClass('eyeon-loader').find('.eyeon-wrapper').removeClass('eyeon-hide');
      eyeonNews.find('.no-items-found').remove();
      newsList.html('');

      if( news.length > 0 ) {
        news.forEach(item => {
          const newsItem = $(`
            <a href="<?= mcd_single_page_url('mycenterblogpost') ?>${item.slug}" class="news-item news-item-${item.id}">
              <div class="image">
                <img src="${item.media.url}" alt="${item.title}" />
              </div>
              <div class="news-details">
                <div class="news-content">
                  ${ settings.show_post_date === 'show' ? `
                    <div class="news-post-date">
                      <i class="far fa-calendar"></i>
                      <span>${eyeonFormatDate(item.post_date)}</span>
                    </div>
                  `: '' }
                  <div class="news-title">${item.title}</div>
                </div>
                <div class="readmore">
                  <span>Read More</span>
                  <i class="fas fa-arrow-right"></i>
                </div>
              </div>
            </a>
          `);
          newsList.append(newsItem);
        });
        filterNewsByCategory();
        
        <?php include(MCD_PLUGIN_PATH.'elementor/widgets/common/carousel/setup-js.php'); ?>
      } else {
        eyeonNews.find('.eyeon-wrapper').addClass('eyeon-hide');
        if(eyeonNews.find('.no-items-found').length === 0) {
          eyeonNews.append(`
            <div class="no-items-found">${settings.no_results_found_text}</div>
          `);
        }
      }
      
      if( news.length > 0 && elementorFrontend.config.environmentMode.edit && eyeonNews.find('.no-items-found').length === 0) {
        eyeonNews.append(`
          <div class="no-items-found">${settings.no_results_found_text}</div>
        `);
      }
    }

    function filterNewsByCategory(categoryId = 0) {
      newsList.find('.news-item').addClass('eyeon-hide');
      news.forEach(item => {
        if (categoryId == 0 || item.categories.some(cat => cat.id == categoryId)) {
          newsList.find('.news-item.news-item-'+item.id).removeClass('eyeon-hide');
        }
      });
    }

    // Event listeners for filter
    categoryList.on('click', 'li', function() {
      categoryList.find('li.active').removeClass('active');
      $(this).addClass('active');
      const selectedCategoryId = parseInt($(this).attr('data-value'));

      categoryDropdownList.val(selectedCategoryId);
      filterNewsByCategory(selectedCategoryId);
    });

    categoryDropdownList.on('change', function() {
      const selectedCategoryId = parseInt($(this).val());

      // change categories list selection
      categoryList.find('li.active').removeClass('active');
      categoryList.find('li[data-value="'+selectedCategoryId+'"]').addClass('active');

      filterNewsByCategory(selectedCategoryId);
    });
  });
</script>