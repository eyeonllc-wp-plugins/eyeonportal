<?php

/*
Elementor Categories Group
*/
function register_eyeon_elementor_categories( $elements_manager ) {
  $elements_manager->add_category(
    'eyeon',
    [
      'title' => esc_html__( 'EyeOn Portal', EYEON_NAMESPACE ),
      'icon' => 'fa fa-plug',
    ]
  );
}
add_action( 'elementor/elements/categories_registered', 'register_eyeon_elementor_categories' );

/*
Include Scripts & Styles
*/
function eyeon_elementor_scripts() {
  // mcd_include_css('fontawesome', 'assets/plugins/fontawesome/css/fontawesome-all.min.css');
  mcd_include_css('eyeon-elementor-style', 'assets/css/elementor.min.css');

  mcd_include_js('moment', 'assets/plugins/calendar/moment.min.js', true);
  mcd_include_js('eyeon-elementor-utils', 'elementor/js/utils.js');
}
add_action( 'wp_enqueue_scripts', 'eyeon_elementor_scripts' );

/*
Scripts & Styles for Elementor widget editor
*/
function enqueue_custom_script() {
  global $mcd_settings;

  // Categories Select2
  wp_register_script( 'eyeon-retailers-categories-script', mcd_version_url( 'elementor/controls/retailer-categories.js' ) );
  $categoriesCustomData = array(
    'center_id' => $mcd_settings['center_id'],
    'api_endpoint' => MCD_API_STORES.'/categories'
  );
  wp_localize_script('eyeon-retailers-categories-script', 'categoriesCustomData', $categoriesCustomData);
  wp_enqueue_script( 'eyeon-retailers-categories-script' );

  // Tags Select2
  wp_register_script( 'eyeon-retailers-tags-script', mcd_version_url( 'elementor/controls/retailer-tags.js' ) );
  $tagsCustomData = array(
    'center_id' => $mcd_settings['center_id'],
    'api_endpoint' => MCD_API_STORES.'/tags',
  );
  wp_localize_script('eyeon-retailers-tags-script', 'tagsCustomData', $tagsCustomData);
  wp_enqueue_script( 'eyeon-retailers-tags-script' );
}
add_action('elementor/editor/after_enqueue_scripts', 'enqueue_custom_script');

/*
Register Elementor Widgets
*/
function register_eyeon_widgets( $widgets_manager ) {
  require_once plugin_dir_path( __FILE__).'widgets/stores/index.php';
  $widgets_manager->register( new \EyeOn_Stores_Widget() );

  require_once plugin_dir_path( __FILE__).'widgets/events/index.php';
  $widgets_manager->register( new \EyeOn_Events_Widget() );

  require_once plugin_dir_path( __FILE__).'widgets/deals/index.php';
  $widgets_manager->register( new \EyeOn_Deals_Widget() );
}  
add_action('elementor/widgets/register', 'register_eyeon_widgets');

