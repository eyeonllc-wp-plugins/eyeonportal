<?php defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Blog', 'redux-framework-demo' ),
		'id' => 'blog_settings',
		'subsection' => true,
		'fields' => array(
			array(
				'id' => 'blog_listing_page',
				'type' => 'select',
				'title' => __( 'Blog Page', 'redux-framework-demo' ),
				'options' => mcd_pages_list(),
			),
			array(
				'id' => 'blog_listing_page_width',
				'type' => 'button_set',
				'title' => __( 'Page Width', 'redux-framework-demo' ),
				'options' => mcd_page_widths(),
				'default' => isset($mcd_settings['blog_listing_page_width']) ? $mcd_settings['blog_listing_page_width'] : 'default',
			),
			array(
				'id' => 'blog_listing_posts_per_row',
				'type' => 'button_set',
				'title' => __( 'Posts in a row', 'redux-framework-demo' ),
				'options' => array(
					3 => 3,
					4 => 4,
					5 => 5,
					6 => 6,
				), 
				'default' => isset($mcd_settings['blog_listing_posts_per_row']) ? $mcd_settings['blog_listing_posts_per_row'] : 4,
			),
		)
	)
);
