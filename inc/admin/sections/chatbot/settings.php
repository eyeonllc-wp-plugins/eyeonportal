<?php defined( 'ABSPATH' ) || exit;

$chatbot_defaults = array(
	'chatbot_position'           => 'bottom-right',
	'chatbot_bot_name'           => 'Center Assistant',
	'chatbot_welcome_message'    => 'Hi! Ask me anything about our center — stores, deals, hours, events, and more.',
	'chatbot_offline_message'    => 'Sorry, the assistant is temporarily unavailable. Please try again later or contact the center directly.',
	'chatbot_header_bg'          => '#3d80b9',
	'chatbot_header_text'        => '#ffffff',
	'chatbot_chat_bg'            => '#f8f9fb',
	'chatbot_user_bg'            => '#3d80b9',
	'chatbot_user_text'          => '#ffffff',
	'chatbot_assistant_bg'       => '#ffffff',
	'chatbot_assistant_text'     => '#222222',
	'chatbot_send_bg'            => '#3d80b9',
	'chatbot_send_text'          => '#ffffff',
	'chatbot_launcher_bg'        => '#3d80b9',
	'chatbot_launcher_icon_color' => '#ffffff',
);

function eyeon_chatbot_setting_default( $key, $fallback = '' ) {
	global $mcd_settings, $chatbot_defaults;

	if ( 'chatbot_position' === $key ) {
		$position = isset( $mcd_settings[ $key ] ) ? $mcd_settings[ $key ] : '';
		return 'bottom-left' === $position ? 'bottom-left' : 'bottom-right';
	}

	if ( isset( $mcd_settings[ $key ] ) && $mcd_settings[ $key ] !== '' ) {
		return $mcd_settings[ $key ];
	}

	return isset( $chatbot_defaults[ $key ] ) ? $chatbot_defaults[ $key ] : $fallback;
}

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'AI Chatbot', EYEON_NAMESPACE ),
		'id'    => 'chatbot_settings',
		'icon'  => 'el el-comment',
		'desc'  => __( 'Customize the floating AI chatbot appearance. Enable/disable the chatbot from Admin Portal → Center → Developer tab.', EYEON_NAMESPACE ),
		'fields' => array(
			array(
				'id'      => 'chatbot_position',
				'type'    => 'button_set',
				'title'   => __( 'Floating Icon Position', EYEON_NAMESPACE ),
				'options' => array(
					'bottom-right' => __( 'Bottom Right', EYEON_NAMESPACE ),
					'bottom-left'  => __( 'Bottom Left', EYEON_NAMESPACE ),
				),
				'default' => eyeon_chatbot_setting_default( 'chatbot_position', 'bottom-right' ),
			),
			array(
				'id'      => 'chatbot_bot_name',
				'type'    => 'text',
				'title'   => __( 'Bot Name', EYEON_NAMESPACE ),
				'default' => eyeon_chatbot_setting_default( 'chatbot_bot_name' ),
			),
			array(
				'id'      => 'chatbot_welcome_message',
				'type'    => 'textarea',
				'title'   => __( 'Welcome Message', EYEON_NAMESPACE ),
				'default' => eyeon_chatbot_setting_default( 'chatbot_welcome_message' ),
			),
			array(
				'id'      => 'chatbot_offline_message',
				'type'    => 'textarea',
				'title'   => __( 'Offline / Error Message', EYEON_NAMESPACE ),
				'default' => eyeon_chatbot_setting_default( 'chatbot_offline_message' ),
			),
			array(
				'id'   => 'chatbot_style_launcher_divide',
				'type' => 'divide',
				'desc' => __( 'Floating Icon', EYEON_NAMESPACE ),
			),
			array(
				'id'       => 'chatbot_launcher_icon',
				'type'     => 'media',
				'title'    => __( 'Floating Icon Image', EYEON_NAMESPACE ),
				'subtitle' => __( 'Optional custom image for the floating chat button. Leave empty to use the default chat icon.', EYEON_NAMESPACE ),
				'url'      => true,
				'preview'  => true,
				'default'  => array(
					'url' => '',
				),
			),
			array(
				'id'       => 'chatbot_launcher_bg',
				'type'     => 'color',
				'title'    => __( 'Floating Icon Background', EYEON_NAMESPACE ),
				'validate' => 'color',
				'default'  => eyeon_chatbot_setting_default( 'chatbot_launcher_bg' ),
			),
			array(
				'id'       => 'chatbot_launcher_icon_color',
				'type'     => 'color',
				'title'    => __( 'Floating Icon Color', EYEON_NAMESPACE ),
				'subtitle' => __( 'Color of the default chat icon when no custom image is uploaded.', EYEON_NAMESPACE ),
				'validate' => 'color',
				'default'  => eyeon_chatbot_setting_default( 'chatbot_launcher_icon_color' ),
			),
			array(
				'id'   => 'chatbot_style_header_divide',
				'type' => 'divide',
				'desc' => __( 'Chat Header', EYEON_NAMESPACE ),
			),
			array(
				'id'       => 'chatbot_header_bg',
				'type'     => 'color',
				'title'    => __( 'Header Background Color', EYEON_NAMESPACE ),
				'validate' => 'color',
				'default'  => eyeon_chatbot_setting_default( 'chatbot_header_bg' ),
			),
			array(
				'id'       => 'chatbot_header_text',
				'type'     => 'color',
				'title'    => __( 'Header Text Color', EYEON_NAMESPACE ),
				'validate' => 'color',
				'default'  => eyeon_chatbot_setting_default( 'chatbot_header_text' ),
			),
			array(
				'id'   => 'chatbot_style_chat_divide',
				'type' => 'divide',
				'desc' => __( 'Chat Window', EYEON_NAMESPACE ),
			),
			array(
				'id'       => 'chatbot_chat_bg',
				'type'     => 'color',
				'title'    => __( 'Chat Background Color', EYEON_NAMESPACE ),
				'validate' => 'color',
				'default'  => eyeon_chatbot_setting_default( 'chatbot_chat_bg' ),
			),
			array(
				'id'   => 'chatbot_style_user_divide',
				'type' => 'divide',
				'desc' => __( 'User Messages', EYEON_NAMESPACE ),
			),
			array(
				'id'       => 'chatbot_user_bg',
				'type'     => 'color',
				'title'    => __( 'User Question Background Color', EYEON_NAMESPACE ),
				'validate' => 'color',
				'default'  => eyeon_chatbot_setting_default( 'chatbot_user_bg' ),
			),
			array(
				'id'       => 'chatbot_user_text',
				'type'     => 'color',
				'title'    => __( 'User Question Text Color', EYEON_NAMESPACE ),
				'validate' => 'color',
				'default'  => eyeon_chatbot_setting_default( 'chatbot_user_text' ),
			),
			array(
				'id'   => 'chatbot_style_assistant_divide',
				'type' => 'divide',
				'desc' => __( 'AI Answers', EYEON_NAMESPACE ),
			),
			array(
				'id'       => 'chatbot_assistant_bg',
				'type'     => 'color',
				'title'    => __( 'AI Answer Background Color', EYEON_NAMESPACE ),
				'validate' => 'color',
				'default'  => eyeon_chatbot_setting_default( 'chatbot_assistant_bg' ),
			),
			array(
				'id'       => 'chatbot_assistant_text',
				'type'     => 'color',
				'title'    => __( 'AI Answer Text Color', EYEON_NAMESPACE ),
				'validate' => 'color',
				'default'  => eyeon_chatbot_setting_default( 'chatbot_assistant_text' ),
			),
			array(
				'id'   => 'chatbot_style_send_divide',
				'type' => 'divide',
				'desc' => __( 'Send Button', EYEON_NAMESPACE ),
			),
			array(
				'id'       => 'chatbot_send_bg',
				'type'     => 'color',
				'title'    => __( 'Send Button Background Color', EYEON_NAMESPACE ),
				'validate' => 'color',
				'default'  => eyeon_chatbot_setting_default( 'chatbot_send_bg' ),
			),
			array(
				'id'       => 'chatbot_send_text',
				'type'     => 'color',
				'title'    => __( 'Send Button Text Color', EYEON_NAMESPACE ),
				'validate' => 'color',
				'default'  => eyeon_chatbot_setting_default( 'chatbot_send_text' ),
			),
		),
	)
);
