<?php defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Event', 'redux-framework-demo' ),
		'id' => 'events_single',
		'subsection' => true,
		'fields' => array(
			array(
				'id' => 'events_single_page_width',
				'type' => 'button_set',
				'title' => __( 'Page Width', 'redux-framework-demo' ),
				'options' => mcd_page_widths(),
				'default' => isset($mcd_settings['events_single_page_width']) ? $mcd_settings['events_single_page_width'] : 'default',
			),
			array(
				'id' => 'events_single_page_title',
				'type' => 'button_set',
				'title' => __( 'Page Title', 'redux-framework-demo' ),
				'options' => array(
					'event' => 'Event Title',
					'custom' => 'Custom',
				),
				'default' => isset($mcd_settings['events_single_page_title']) ? $mcd_settings['events_single_page_title'] : 'event',
			),
			array(
				'id' => 'events_single_page_custom_title',
				'type' => 'text',
				'title' => __( 'Custom Page Title', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['events_single_page_custom_title']) ? $mcd_settings['events_single_page_custom_title'] : 'Event',
				'required' => array('events_single_page_title', 'equals', 'custom'),
			),
			array(
				'id' => 'events_single_page_template',
				'type' => 'select',
				'title' => __( 'Choose Page Template', 'redux-framework-demo' ),
				'options' => mcd_pages_list(),
				'validate' => 'not_empty',
			),
			array(
				'id' => 'events_single_page_slug',
				'type' => 'text',
				'title' => __( 'Page Slug', 'redux-framework-demo' ),
				'desc' => __( 'Single Store page URL will be:<br>'.get_site_url().'/<strong>'.$mcd_settings['events_single_page_slug'].'</strong>/a-culinary-competition/', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['events_single_page_slug']) ? $mcd_settings['events_single_page_slug'] : 'event',
			),
			array(
				'id' => 'events_single_qrcode',
				'type' => 'switch',
				'title' => __( 'QR Code', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['events_single_qrcode']) ? $mcd_settings['events_single_qrcode'] : true,
			),
			array(
				'id' => 'events_single_add_to_calendar',
				'type' => 'switch',
				'title' => __( 'Add to Calendar Button', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['events_single_add_to_calendar']) ? $mcd_settings['events_single_add_to_calendar'] : false,
			),
			array(
				'id' => 'events_single_social_share',
				'type' => 'switch',
				'title' => __( 'Social Sharing', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['events_single_social_share']) ? $mcd_settings['events_single_social_share'] : true,
			),
			array(
				'id' => 'events_single_upcoming_events',
				'type' => 'switch',
				'title' => __( 'Upcoming Events', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['events_single_upcoming_events']) ? $mcd_settings['events_single_upcoming_events'] : true,
			),
			array(
				'id' => 'events_single_upcoming_events_limit',
				'type' => 'text',
				'title' => __( 'Upcoming Events Limit', 'redux-framework-demo' ),
				'subtitle' => __( 'No of upcoming events to show', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['events_single_upcoming_events_limit']) ? $mcd_settings['events_single_upcoming_events_limit'] : 10,
				'required' => array('events_single_upcoming_events', 'equals', 1),
			),
		)
	)
);