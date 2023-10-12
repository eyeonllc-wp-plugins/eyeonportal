<?php defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Single Article', 'redux-framework-demo' ),
		'id' => 'article_single',
		'subsection' => true,
		'fields' => array(
			array(
				'id' => 'article_single_page_slug',
				'type' => 'text',
				'title' => __( 'Page Slug', 'redux-framework-demo' ),
				'desc' => __( 'Single Article page URL will be:<br>'.get_site_url().'/<strong>'.$mcd_settings['article_single_page_slug'].'</strong>/236-high-rise-denim-skirt', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['article_single_page_slug']) ? $mcd_settings['article_single_page_slug'] : 'article',
			),
			array(
				'id' => 'article_single_page_template',
				'type' => 'select',
				'title' => __( 'Choose Page Template', 'redux-framework-demo' ),
				'options' => mcd_pages_list(),
				'validate' => 'not_empty',
			),
		)
	)
);
