<?php defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Product Single', 'redux-framework-demo' ),
		'id' => 'product_single',
		'subsection' => true,
		'fields' => array(
			array(
				'id' => 'product_single_page_slug',
				'type' => 'text',
				'title' => __( 'Page Slug', 'redux-framework-demo' ),
				'desc' => __( 'Single Product page URL will be:<br>'.get_site_url().'/<strong>'.$mcd_settings['product_single_page_slug'].'</strong>/2414678-high-rise-denim-skirt', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['product_single_page_slug']) ? $mcd_settings['product_single_page_slug'] : 'product',
			),
			array(
				'id' => 'product_single_page_template',
				'type' => 'select',
				'title' => __( 'Choose Page Template', 'redux-framework-demo' ),
				'options' => mcd_pages_list(),
				'validate' => 'not_empty',
			),
			array(
				'id' => 'product_single_fetch_limit',
				'type' => 'text',
				'title' => __( 'Similar Products', 'redux-framework-demo' ),
				'subtitle' => __( 'No of products to show in Similar Products section.', 'redux-framework-demo' ),
				'desc' => __( 'Max Limit: 100', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['product_single_fetch_limit']) ? $mcd_settings['product_single_fetch_limit'] : 8,
			),
		)
	)
);
