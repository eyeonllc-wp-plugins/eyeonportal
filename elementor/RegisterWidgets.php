<?php

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

function eyeon_elementor_scripts() {
  // mcd_include_css('fontawesome', 'assets/plugins/fontawesome/css/fontawesome-all.min.css');
  mcd_include_css('eyeon-elementor-style', 'assets/css/elementor.min.css');

  mcd_include_js('moment', 'assets/plugins/calendar/moment.min.js', true);
  mcd_include_js('eyeon-elementor-utils', 'elementor/js/utils.js');
}
add_action( 'wp_enqueue_scripts', 'eyeon_elementor_scripts' );

function register_eyeon_widgets() {
  require_once plugin_dir_path( __FILE__).'widgets/stores/index.php';
  \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new EyeOn_Stores_Widget());

  require_once plugin_dir_path( __FILE__).'widgets/events/index.php';
  \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new EyeOn_Events_Widget());

  require_once plugin_dir_path( __FILE__).'widgets/deals/index.php';
  \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new EyeOn_Deals_Widget());
}  
add_action('elementor/widgets/widgets_registered', 'register_eyeon_widgets');

