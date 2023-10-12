<?php defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Store Products', 'redux-framework-demo' ),
		'id' => 'store_products',
		'subsection' => true,
		'fields' => array(
			array(
				'id' => 'store_products_per_row',
				'type' => 'button_set',
				'title' => __( 'Products in a row', 'redux-framework-demo' ),
				'options' => array(
					3 => 3,
					4 => 4,
					5 => 5,
					6 => 6,
				), 
				'default' => isset($mcd_settings['store_products_per_row']) ? $mcd_settings['store_products_per_row'] : 4,
			),
			array(
				'id' => 'store_products_fetch_limit',
				'type' => 'text',
				'title' => __( 'Fetch Products', 'redux-framework-demo' ),
				'subtitle' => __( 'No of products to show on page load', 'redux-framework-demo' ),
				'desc' => __( 'Max Limit: 100', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['store_products_fetch_limit']) ? $mcd_settings['store_products_fetch_limit'] : 16,
			),
		)
	)
);
