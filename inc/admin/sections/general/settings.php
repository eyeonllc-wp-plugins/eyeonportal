<?php defined( 'ABSPATH' ) || exit;

$center = eyeon_get_center();

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'General Settings', 'mycenterportal.com' ),
		'id' => 'general_settings',
		'icon' => 'el el-home',
		'fields' => array(
			array(
				'id' => 'api_access_token',
				'type' => 'text',
				'title' => __( 'API Access Token', EYEON_NAMESPACE ),
				'default' => isset($mcd_settings['api_access_token']) ? $mcd_settings['api_access_token'] : '',
				'desc' => __( '#'.$center['id'].' - '.$center['name'], EYEON_NAMESPACE ),
        'ajax_save' => false,
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
