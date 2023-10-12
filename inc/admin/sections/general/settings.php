<?php defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'General Settings', 'mycenterportal.com' ),
		'id' => 'general_settings',
		'icon' => 'el el-home',
		'fields' => array(
			array(
				'id' => 'mcd_site_mode',
				'type' => 'button_set',
				'title' => __( 'Plugin Mode', 'redux-framework-demo' ),
				'options' => array(
					'development' => 'Development',
					'staging' => 'Staging',
					'prod' => 'Production',
				),
				'ajax_save' => false,
				'default' => isset($mcd_settings['mcd_site_mode']) ? $mcd_settings['mcd_site_mode'] : 'live',
			),
			array(
				'id' => 'center_id',
				'type' => 'select',
				'title' => __( 'Choose Center', 'redux-framework-demo' ),
				'options' => mcd_centers_dropdown_values(),
				'default' => isset($mcd_settings['center_id']) ? $mcd_settings['center_id'] : 0,
				'validate' => 'not_empty',
			),
			array(
				'id' => 'default_page_width',
				'type' => 'text',
				'title' => __( 'Default Page Width', 'redux-framework-demo' ),
				'subtitle' => __( 'Max container width', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['default_page_width']) ? $mcd_settings['default_page_width'] : 1200,
			),
			array(
				'id' => 'accent_color',
				'type' => 'color',
				'title' => __( 'Accent Color', 'redux-framework-demo' ),
				'subtitle' => __( 'Max container width of Single page', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['accent_color']) ? $mcd_settings['accent_color'] : '#3d80b9',
				'validate' => 'color',
			),
		)
	)
);
