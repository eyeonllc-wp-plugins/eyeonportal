<?php defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Deal', 'redux-framework-demo' ),
		'id' => 'deals_single',
		'subsection' => true,
		'fields' => array(
			array(
				'id' => 'deals_single_page_width',
				'type' => 'button_set',
				'title' => __( 'Page Width', 'redux-framework-demo' ),
				'options' => mcd_page_widths(),
				'default' => isset($mcd_settings['deals_single_page_width']) ? $mcd_settings['deals_single_page_width'] : 'default',
			),
			array(
				'id' => 'deals_single_page_title',
				'type' => 'button_set',
				'title' => __( 'Page Title', 'redux-framework-demo' ),
				'options' => array(
					'deal' => 'Deal Title',
					'custom' => 'Custom',
				),
				'default' => isset($mcd_settings['deals_single_page_title']) ? $mcd_settings['deals_single_page_title'] : 'deal',
			),
			array(
				'id' => 'deals_single_page_custom_title',
				'type' => 'text',
				'title' => __( 'Custom Page Title', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['deals_single_page_custom_title']) ? $mcd_settings['deals_single_page_custom_title'] : 'Deal',
				'required' => array('deals_single_page_title', 'equals', 'custom'),
			),
			array(
				'id' => 'deals_single_page_template',
				'type' => 'select',
				'title' => __( 'Choose Page Template', 'redux-framework-demo' ),
				'options' => mcd_pages_list(),
				'validate' => 'not_empty',
			),
			array(
				'id' => 'deals_single_page_slug',
				'type' => 'text',
				'title' => __( 'Page Slug', 'redux-framework-demo' ),
				'desc' => __( 'Single Store page URL will be:<br>'.get_site_url().'/<strong>'.$mcd_settings['deals_single_page_slug'].'</strong>/bed-room-sale/', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['deals_single_page_slug']) ? $mcd_settings['deals_single_page_slug'] : 'deal',
			),
			array(
				'id' => 'deals_single_social_share',
				'type' => 'switch',
				'title' => __( 'Social Sharing', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['deals_single_social_share']) ? $mcd_settings['deals_single_social_share'] : true,
			),
		)
	)
);