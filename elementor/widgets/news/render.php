<?php
$settings = $this->get_settings_for_display();

$filtered_settings = array_intersect_key($settings, array_flip(array_merge([
  'fetch_all',
  'fetch_limit',
  'show_post_date',
  'show_excerpt',
], get_carousel_fields())));
$unique_id = uniqid();
?>

<div id="eyeon-news-<?= $unique_id ?>" class="eyeon-news eyeon-loader">
  <div class="eyeon-wrapper" style="display:none;">
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
    var page = 1;
    var defaultLimit = 100;
    let categories = [];

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

      <?php if( $settings['categories_filters'] === 'show' ) : ?>
      categories.forEach(category => {
        categoryList.append(`
          <li data-value="${category.id}" class="${category.id===0?'active':''}">${category.name}</li>
        `);
        categoryDropdownList.append(`
          <option value="${category.id}">${category.name}</option>
        `);
      });
      <?php endif; ?>

      eyeonNews.removeClass('eyeon-loader').find('.eyeon-wrapper').removeAttr('style');
      renderNews();
    }

    function renderNews() {
      newsList.empty();

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
                  ${ settings.show_excerpt === 'show' ? `
                    <div class="news-excerpt">${item.short_description}</div>
                  `: '' }
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
        eyeonNews.find('.eyeon-wrapper').html(`
          <div class="no-items-found">No news articles found.</div>
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