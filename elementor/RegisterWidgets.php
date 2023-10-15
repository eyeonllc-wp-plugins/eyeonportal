<?php

function register_eyeon_stores_widget() {
  require_once plugin_dir_path( __FILE__).'widgets/stores/index.php';
  \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new EyeOn_Stores_Widget());
}

add_action('elementor/widgets/widgets_registered', 'register_eyeon_stores_widget');

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