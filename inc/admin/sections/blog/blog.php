<?php defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Blog Post Single', 'redux-framework-demo' ),
		'id' => 'blog_single',
		'subsection' => true,
		'fields' => array(
			array(
				'id' => 'blog_single_page_width',
				'type' => 'button_set',
				'title' => __( 'Page Width', 'redux-framework-demo' ),
				'options' => mcd_page_widths(),
				'default' => isset($mcd_settings['blog_single_page_width']) ? $mcd_settings['blog_single_page_width'] : 'default',
			),
			array(
				'id' => 'blog_single_page_title',
				'type' => 'button_set',
				'title' => __( 'Page Title', 'redux-framework-demo' ),
				'options' => array(
					'default' => 'Post Title',
					'custom' => 'Custom',
				),
				'default' => isset($mcd_settings['blog_single_page_title']) ? $mcd_settings['blog_single_page_title'] : 'default',
			),
			array(
				'id' => 'blog_single_page_custom_title',
				'type' => 'text',
				'title' => __( 'Custom Page Title', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['blog_single_page_custom_title']) ? $mcd_settings['blog_single_page_custom_title'] : 'Job',
				'required' => array('careers_single_page_title', 'equals', 'custom'),
			),
			array(
				'id' => 'blog_single_page_template',
				'type' => 'select',
				'title' => __( 'Choose Page Template', 'redux-framework-demo' ),
				'options' => mcd_pages_list(),
				'validate' => 'not_empty',
			),
			array(
				'id' => 'blog_single_page_slug',
				'type' => 'text',
				'title' => __( 'Page Slug', 'redux-framework-demo' ),
				'desc' => __( 'Single Post page URL will be:<br>'.get_site_url().'/<strong>'.$mcd_settings['blog_single_page_slug'].'</strong>/post-slug/', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['blog_single_page_slug']) ? $mcd_settings['blog_single_page_slug'] : 'post',
			),
			array(
				'id' => 'blog_single_show_author',
				'type' => 'switch',
				'title' => __( 'Show Author', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['blog_single_show_author']) ? $mcd_settings['blog_single_show_author'] : false,
			),
			array(
				'id' => 'blog_single_show_categories',
				'type' => 'switch',
				'title' => __( 'Show Post Category', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['blog_single_show_categories']) ? $mcd_settings['blog_single_show_categories'] : false,
			),
			array(
				'id' => 'blog_single_social_share',
				'type' => 'switch',
				'title' => __( 'Social Sharing', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['blog_single_social_share']) ? $mcd_settings['blog_single_social_share'] : true,
			),
		)
	)
);
