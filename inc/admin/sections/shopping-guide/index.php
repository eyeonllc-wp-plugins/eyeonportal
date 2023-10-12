<?php defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Shopping Guide', 'redux-framework-demo' ),
		'id' => 'shopping_guide_settings_main',
		'icon' => 'el el-website',
	)
);

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Articles', 'redux-framework-demo' ),
		'id' => 'shopping_guide_settings',
		'subsection' => true,
		'fields' => array(
			array(
				'id' => 'shopping_guide_page',
				'type' => 'select',
				'title' => __( 'Shopping Guide Page', 'redux-framework-demo' ),
				'options' => mcd_pages_list(),
			),
			// array(
			// 	'id' => 'shopping_guide_page_width',
			// 	'type' => 'button_set',
			// 	'title' => __( 'Page Width', 'redux-framework-demo' ),
			// 	'options' => mcd_page_widths(),
			// 	'default' => isset($mcd_settings['shopping_guide_page_width']) ? $mcd_settings['shopping_guide_page_width'] : 'default',
			// ),
			// array(
			// 	'id' => 'shopping_guide_articles_per_row',
			// 	'type' => 'button_set',
			// 	'title' => __( 'Articles in a row', 'redux-framework-demo' ),
			// 	'options' => array(
			// 		3 => 3,
			// 		4 => 4,
			// 		5 => 5,
			// 		6 => 6,
			// 	), 
			// 	'default' => isset($mcd_settings['shopping_guide_articles_per_row']) ? $mcd_settings['shopping_guide_articles_per_row'] : 4,
			// ),
		)
	)
);

