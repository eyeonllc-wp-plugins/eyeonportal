<?php defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Events', 'redux-framework-demo' ),
		'id' => 'events_settings',
		'subsection' => true,
		'fields' => array(
			array(
				'id' => 'events_listing_page',
				'type' => 'select',
				'title' => __( 'Events Page', 'redux-framework-demo' ),
				'options' => mcd_pages_list(),
			),
			array(
				'id' => 'events_listing_page_width',
				'type' => 'button_set',
				'title' => __( 'Page Width', 'redux-framework-demo' ),
				'options' => mcd_page_widths(),
				'default' => isset($mcd_settings['events_listing_page_width']) ? $mcd_settings['events_listing_page_width'] : 'default',
			),
			array(
				'id' => 'events_listing_grid_items',
				'type' => 'text',
				'title' => __( 'Grid View Total Events', 'redux-framework-demo' ),
				'subtitle' => __( 'No of events to be shown in Grid View', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['events_listing_grid_items']) ? $mcd_settings['events_listing_grid_items'] : 10,
			),
			array(
				'id' => 'events_listing_events_per_row',
				'type' => 'button_set',
				'title' => __( 'Events in a Row', 'redux-framework-demo' ),
				'subtitle' => __( 'No of events in a row', 'redux-framework-demo' ),
				'options' => array(
					2 => 2,
					3 => 3,
					4 => 4,
				), 
				'default' => isset($mcd_settings['events_listing_events_per_row']) ? $mcd_settings['events_listing_events_per_row'] : 3,
			),
		)
	)
);
