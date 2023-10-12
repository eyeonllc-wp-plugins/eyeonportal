<?php defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Deals', 'redux-framework-demo' ),
		'id' => 'deals_settings',
		'subsection' => true,
		'fields' => array(
			array(
				'id' => 'deals_listing_page',
				'type' => 'select',
				'title' => __( 'Deals Page', 'redux-framework-demo' ),
				'options' => mcd_pages_list(),
			),
			array(
				'id' => 'deals_listing_page_width',
				'type' => 'button_set',
				'title' => __( 'Page Width', 'redux-framework-demo' ),
				'options' => mcd_page_widths(),
				'default' => isset($mcd_settings['deals_listing_page_width']) ? $mcd_settings['deals_listing_page_width'] : 'default',
			),
			array(
				'id' => 'deals_listing_deals_per_row',
				'type' => 'button_set',
				'title' => __( 'Deals in a row', 'redux-framework-demo' ),
				'options' => array(
					3 => 3,
					4 => 4,
					5 => 5,
					6 => 6,
				), 
				'default' => isset($mcd_settings['deals_listing_deals_per_row']) ? $mcd_settings['deals_listing_deals_per_row'] : 4,
			),
		)
	)
);

