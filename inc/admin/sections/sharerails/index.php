<?php defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'ShareRails', 'redux-framework-demo' ),
		'id' => 'sharerails',
		'icon' => 'el el-shopping-cart',
		'fields' => array(
			array(
				'id' => 'sharerails_api_key',
				'type' => 'password',
				'class' => 'regular-text',
				'title' => __( 'Center API Token', 'redux-framework-demo' ),
				'desc' => __( '', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['sharerails_api_key']) ? $mcd_settings['sharerails_api_key'] : '',
			),
			array(
				'id' => 'sharerails_currency',
				'type' => 'text',
				'title' => __( 'Currency', 'redux-framework-demo' ),
				'desc' => __( '', 'redux-framework-demo' ),
				'default' => isset($mcd_settings['sharerails_currency']) ? $mcd_settings['sharerails_currency'] : '$',
			),
		)
	)
);
