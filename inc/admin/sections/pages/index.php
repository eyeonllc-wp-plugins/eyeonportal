<?php defined( 'ABSPATH' ) || exit;

$page_list = mcd_pages_list();

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Pages', 'redux-framework-demo' ),
		'id' => 'pages_settings_main',
		'icon' => 'el el-list-alt',
    'fields' => array(
			array(
				'id' => 'stores_listing_page',
				'type' => 'select',
				'title' => __( 'Stores Page', 'redux-framework-demo' ),
				'options' => $page_list,
			),
      array(
				'id' => 'deals_listing_page',
				'type' => 'select',
				'title' => __( 'Deals Page', 'redux-framework-demo' ),
				'options' => $page_list,
			),
      array(
				'id' => 'events_listing_page',
				'type' => 'select',
				'title' => __( 'Events Page', 'redux-framework-demo' ),
				'options' => $page_list,
			),
      array(
				'id' => 'careers_listing_page',
				'type' => 'select',
				'title' => __( 'Careers Page', 'redux-framework-demo' ),
				'options' => $page_list,
			),
      array(
				'id' => 'blog_listing_page',
				'type' => 'select',
				'title' => __( 'News Page', 'redux-framework-demo' ),
				'options' => $page_list,
			),
      array(
				'id' => 'map_page',
				'type' => 'select',
				'title' => __( 'Map Page', 'redux-framework-demo' ),
				'desc' => __( 'Choose the page where Map widget is added.<br>It will help plugin to use "Find IT" button on single retailer pages to open Map page and select the retailer on Floor Map.<br>If this is not set then "Find IT" button won\'t show up on Store popup.', 'redux-framework-demo' ),
				'options' => $page_list,
			),
    )
	)
);

