<?php defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Careers', 'redux-framework-demo' ),
		'id' => 'careers_settings',
		'subsection' => true,
		'fields' => array(
			array(
				'id' => 'careers_listing_page',
				'type' => 'select',
				'title' => __( 'Careers Page', 'redux-framework-demo' ),
				'options' => mcd_pages_list(),
			),
			array(
				'id' => 'careers_listing_page_width',
				'type' => 'button_set',
				'title' => __( 'Page Width', 'redux-framework-demo' ),
				'options' => mcd_page_widths(),
				'default' => isset($mcd_settings['careers_listing_page_width']) ? $mcd_settings['careers_listing_page_width'] : 'default',
			),
			array(
				'id' => 'careers_listing_careers_per_row',
				'type' => 'button_set',
				'title' => __( 'Jobs in a row', 'redux-framework-demo' ),
				'options' => array(
					3 => 3,
					4 => 4,
					5 => 5,
					6 => 6,
				), 
				'default' => isset($mcd_settings['careers_listing_careers_per_row']) ? $mcd_settings['careers_listing_careers_per_row'] : 4,
			),
		)
	)
);