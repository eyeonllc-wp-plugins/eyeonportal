<?php defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Stores', 'redux-framework-demo' ),
		'id' => 'stores_settings',
		'subsection' => true,
		'fields' => array(
			array(
				'id' => 'stores_listing_page',
				'type' => 'select',
				'title' => __( 'Stores Page', 'redux-framework-demo' ),
				'options' => mcd_pages_list(),
			),
			array(
				'id' => 'stores_listing_page_width',
				'type' => 'button_set',
				'title' => __( 'Page Width', 'redux-framework-demo' ),
				'options' => mcd_page_widths(),
				'default' => isset($mcd_settings['stores_listing_page_width']) ? $mcd_settings['stores_listing_page_width'] : 'default',
			),
			array(
				'id' => 'stores_listing_alphabetical_stores_per_row',
				'type' => 'button_set',
				'title' => __( 'Stores in a Row', 'redux-framework-demo' ),
				'subtitle' => __( 'How many stores to show in a row', 'redux-framework-demo' ),
				'options' => array(
					2 => 2,
					3 => 3,
					4 => 4,
					5 => 5,
					6 => 6,
					7 => 7,
					8 => 8,
					9 => 9,
				), 
				'default' => isset($mcd_settings['stores_listing_alphabetical_stores_per_row']) ? $mcd_settings['stores_listing_alphabetical_stores_per_row'] : 9,
			),
			array(
				'id' => 'stores_listing_show_store_name',
				'type' => 'switch',
				'title' => __( 'Show Store Name', 'redux-framework-demo' ),
				'subtitle' => __( 'Check to show Retailer name below logos.', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['stores_listing_show_store_name']) ? $mcd_settings['stores_listing_show_store_name'] : false,
			),
			array(
				'id' => 'stores_listing_grayscale_effect',
				'type' => 'switch',
				'title' => __( 'Grayscale Effect', 'redux-framework-demo' ),
				'subtitle' => __( 'Add grayscale effect on hover on logos.', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['stores_listing_grayscale_effect']) ? $mcd_settings['stores_listing_grayscale_effect'] : true,
			),
			array(
				'id' => 'stores_listing_show_categories',
				'type' => 'switch',
				'title' => __( 'Show Categories', 'redux-framework-demo' ),
				'subtitle' => __( 'Check to show Categories dropdown for filtering stores', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['stores_listing_show_categories']) ? $mcd_settings['stores_listing_show_categories'] : true,
			),
			array(
				'id' => 'stores_listing_store_background',
				'type' => 'color',
				'title' => __( 'Retailer Logo Background Color', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['stores_listing_store_background']) ? $mcd_settings['stores_listing_store_background'] : '#F5F5F5',
				'validate' => 'color',
			),
		)
	)
);