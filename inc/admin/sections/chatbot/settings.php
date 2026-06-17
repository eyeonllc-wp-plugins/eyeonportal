<?php defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'AI Chatbot', EYEON_NAMESPACE ),
		'id' => 'chatbot_settings',
		'icon' => 'el el-comment',
		'desc' => __( 'Customize the floating AI chatbot appearance. Enable/disable the chatbot from Admin Portal → Center → Developer tab.', EYEON_NAMESPACE ),
		'fields' => array(
			array(
				'id' => 'chatbot_position',
				'type' => 'select',
				'title' => __( 'Icon Position', EYEON_NAMESPACE ),
				'options' => array(
					'bottom-right' => __( 'Bottom Right', EYEON_NAMESPACE ),
					'bottom-left' => __( 'Bottom Left', EYEON_NAMESPACE ),
					'top-right' => __( 'Top Right', EYEON_NAMESPACE ),
					'top-left' => __( 'Top Left', EYEON_NAMESPACE ),
				),
				'default' => isset( $mcd_settings['chatbot_position'] ) ? $mcd_settings['chatbot_position'] : 'bottom-right',
			),
			array(
				'id' => 'chatbot_bot_name',
				'type' => 'text',
				'title' => __( 'Bot Name', EYEON_NAMESPACE ),
				'default' => isset( $mcd_settings['chatbot_bot_name'] ) ? $mcd_settings['chatbot_bot_name'] : 'Center Assistant',
			),
			array(
				'id' => 'chatbot_welcome_message',
				'type' => 'textarea',
				'title' => __( 'Welcome Message', EYEON_NAMESPACE ),
				'default' => isset( $mcd_settings['chatbot_welcome_message'] ) ? $mcd_settings['chatbot_welcome_message'] : 'Hi! Ask me anything about our center — stores, deals, hours, events, and more.',
			),
			array(
				'id' => 'chatbot_offline_message',
				'type' => 'textarea',
				'title' => __( 'Offline / Error Message', EYEON_NAMESPACE ),
				'default' => isset( $mcd_settings['chatbot_offline_message'] ) ? $mcd_settings['chatbot_offline_message'] : 'Sorry, the assistant is temporarily unavailable. Please try again later or contact the center directly.',
			),
			array(
				'id' => 'chatbot_icon_url',
				'type' => 'text',
				'title' => __( 'Custom Avatar URL', EYEON_NAMESPACE ),
				'subtitle' => __( 'Optional image URL for the chatbot avatar.', EYEON_NAMESPACE ),
				'default' => isset( $mcd_settings['chatbot_icon_url'] ) ? $mcd_settings['chatbot_icon_url'] : '',
			),
		),
	)
);
