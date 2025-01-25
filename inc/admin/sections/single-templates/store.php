<?php defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Store', 'redux-framework-demo' ),
		'id' => 'stores_single',
		'subsection' => true,
		'fields' => array(
			array(
				'id' => 'stores_single_page_width',
				'type' => 'button_set',
				'title' => __( 'Page Width', 'redux-framework-demo' ),
				'options' => mcd_page_widths(),
				'default' => isset($mcd_settings['stores_single_page_width']) ? $mcd_settings['stores_single_page_width'] : 'default',
			),
			array(
				'id' => 'stores_single_page_title',
				'type' => 'button_set',
				'title' => __( 'Page Title', 'redux-framework-demo' ),
				'options' => array(
					'store' => 'Store Name',
					'custom' => 'Custom',
				),
				'default' => isset($mcd_settings['stores_single_page_title']) ? $mcd_settings['stores_single_page_title'] : 'store',
			),
			array(
				'id' => 'stores_single_page_custom_title',
				'type' => 'text',
				'title' => __( 'Custom Page Title', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['stores_single_page_custom_title']) ? $mcd_settings['stores_single_page_custom_title'] : 'Retailer',
				'required' => array('stores_single_page_title', 'equals', 'custom'),
			),
			array(
				'id' => 'stores_single_page_template',
				'type' => 'select',
				'title' => __( 'Choose Page Template', 'redux-framework-demo' ),
				'options' => mcd_pages_list(),
				'validate' => 'not_empty',
			),
			array(
				'id' => 'stores_single_page_slug',
				'type' => 'text',
				'title' => __( 'Page Slug', 'redux-framework-demo' ),
				'desc' => __( 'Single Store page URL will be:<br>'.get_site_url().'/<strong>'.$mcd_settings['stores_single_page_slug'].'</strong>/champs-sports/', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['stores_single_page_slug']) ? $mcd_settings['stores_single_page_slug'] : 'directory',
			),
			array(
				'id' => 'stores_single_social_links',
				'type' => 'switch',
				'title' => __( 'Social Links', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['stores_single_social_links']) ? $mcd_settings['stores_single_social_links'] : true,
			),
			array(
				'id' => 'stores_single_deals',
				'type' => 'switch',
				'title' => __( 'Retailer\'s Deals', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['stores_single_deals']) ? $mcd_settings['stores_single_deals'] : true,
			),
			array(
				'id' => 'stores_single_deals_fetch',
				'type' => 'text',
				'title' => __( 'Fetch Deals', 'redux-framework-demo' ),
				'subtitle' => __( 'No of deals to show on the page', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['stores_single_deals_fetch']) ? $mcd_settings['stores_single_deals_fetch'] : 4,
				'required' => array('stores_single_deals', 'equals', 1),
			),
			array(
				'id' => 'stores_single_deals_per_row',
				'type' => 'button_set',
				'title' => __( 'Display Deals', 'redux-framework-demo' ),
				'subtitle' => __( 'No of deals in a row', 'redux-framework-demo' ),
				'options' => array(
					3 => 3,
					4 => 4,
					5 => 5,
					6 => 6,
				), 
				'default' => isset($mcd_settings['stores_single_deals_per_row']) ? $mcd_settings['stores_single_deals_per_row'] : 4,
				'required' => array('stores_single_deals', 'equals', 1),
			),
		)
	)
);
