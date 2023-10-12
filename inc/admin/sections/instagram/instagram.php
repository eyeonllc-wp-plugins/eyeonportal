<?php defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Instagram', 'redux-framework-demo' ),
		'id' => 'instagram_settings',
		'icon' => 'el el-instagram',
		'fields' => array(
			array(
				'id' => 'instagram_page_width',
				'type' => 'button_set',
				'title' => __( 'Page Width', 'redux-framework-demo' ),
				'options' => mcd_page_widths(),
				'default' => isset($mcd_settings['instagram_page_width']) ? $mcd_settings['instagram_page_width'] : 'default',
			),
			array(
				'id' => 'instagram-responsiveness-section-start',
				'type' => 'section',
				'title' => __('Responsiveness Options', 'redux-framework-demo'),
				'subtitle' => __('Choose items to show in a row for different devices.', 'redux-framework-demo'),
				'indent' => true 
			),
				array(
					'id' => 'instagram_items_desktop',
					'type' => 'button_set',
					'title' => __( 'Desktop', 'redux-framework-demo' ),
					'options' => array(
						3 => 3,
						4 => 4,
						5 => 5,
						6 => 6,
					),
					'default' => isset($mcd_settings['instagram_items_desktop']) ? $mcd_settings['instagram_items_desktop'] : 5,
				),
				array(
					'id' => 'instagram_items_tablet',
					'type' => 'button_set',
					'title' => __( 'Tablet', 'redux-framework-demo' ),
					'options' => array(
						2 => 2,
						3 => 3,
						4 => 4,
						5 => 5,
					),
					'default' => isset($mcd_settings['instagram_items_tablet']) ? $mcd_settings['instagram_items_tablet'] : 3,
				),
				array(
					'id' => 'instagram_items_mobile',
					'type' => 'button_set',
					'title' => __( 'Mobile', 'redux-framework-demo' ),
					'options' => array(
						1 => 1,
						2 => 2,
						3 => 3,
					),
					'default' => isset($mcd_settings['instagram_items_mobile']) ? $mcd_settings['instagram_items_mobile'] : 1,
				),
			array(
				'id' => 'instagram-responsiveness-section-end',
				'type' => 'section',
				'indent' => false,
			),
		)
	)
);
