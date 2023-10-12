<?php defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Search Page', 'redux-framework-demo' ),
		'id' => 'search_page',
		'icon' => 'el el-search',
		'fields' => array(
			array(
				'id' => 'search_page_template',
				'type' => 'select',
				'title' => __( 'Choose Page Template', 'redux-framework-demo' ),
				'options' => mcd_pages_list(),
				'validate' => 'not_empty',
			),
			array(
				'id' => 'search_page_slug',
				'type' => 'text',
				'title' => __( 'Page Slug', 'redux-framework-demo' ),
				'desc' => __( 'Search page URL will be:<br>'.get_site_url().'/<strong>'.$mcd_settings['search_page_slug'].'</strong>/mens/', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['search_page_slug']) ? $mcd_settings['search_page_slug'] : 'search',
			),
			array(
				'id' => 'search_result_types',
				'type' => 'sortable',
				'title' => __('Search Result types', 'redux-framework-demo'),
				'subtitle' => __('Choose which tab you want to show for search results.', 'redux-framework-demo'),
				'mode' => 'checkbox',
				'options' => mcd_search_result_types(),
				'default' => mcd_search_result_types(true),
			),
		)
	)
);
