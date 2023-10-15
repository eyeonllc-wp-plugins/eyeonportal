jQuery(document).ready(function () {
  jQuery('.mcd_search_tab_links li a').click(function (event) {
    jQuery('.mcd_search_tab_links li.active').removeClass('active');

    jQuery(this).parents('li').addClass('active');
    var target_block_id = jQuery(this).attr('href');

    jQuery('#mcd_search_tabs .mcd_search_result.active').removeClass('active');
    jQuery(target_block_id).addClass('active');

    return false;
  });
  jQuery('.mcd_search_tab_links li:first-child a').trigger('click');


  jQuery(document).on('click', '.mcd-open-popup', function (event) {
    jQuery('.mcd-display-popup').fadeIn().addClass('show');
  });

  jQuery(document).on('click', '.mcd-close-popup, .mcd-popup-bg', function (event) {
    jQuery('.mcd-display-popup').fadeOut().removeClass('show');
  });

  jQuery('.mcd-prev-next-nav .item:not(.disabled)').click(function (event) {
    jQuery('.mycenterdeals-wrapper').addClass('mcd_loading_div center_pos');
  });

  set_wp_map_height();

  jQuery(window).bind('load resize', function () {
    set_wp_map_height();
  });

  function set_wp_map_height() {
    if (window.MAPIT2) {
      window.MAPIT2.PAGE_LOADED = true;
    }
  }
});

