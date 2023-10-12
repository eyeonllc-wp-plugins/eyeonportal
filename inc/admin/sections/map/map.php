<?php defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Map', 'redux-framework-demo' ),
		'id' => 'map_settings',
		'icon' => 'el el-icon-map-marker',
		'fields' => array(
			array(
				'id' => 'map_page',
				'type' => 'select',
				'title' => __( 'Choose Map Page', 'redux-framework-demo' ),
				'desc' => __( 'Choose the page where <code>[mycentermap2]</code> shortcode is added.<br>It will help plugin to use "Find IT" button on single retailer pages to open Map page and select the retailer on Floor Map.<br>If this is not set then "Find IT" button won\'t show up on Store popup.', 'redux-framework-demo' ),
				'options' => mcd_pages_list(),
			),
		)
	)
);
