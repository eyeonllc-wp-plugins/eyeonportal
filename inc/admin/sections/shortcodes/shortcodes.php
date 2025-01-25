<?php defined( 'ABSPATH' ) || exit;

function mcd_additional_shortcodes() {
	return '
		<p><code><strong>[mcp_site_name]</strong></code> - Display Wordpress Site Title.</p>
		<p><code><strong>[mcp_site_url]</strong></code> - Display Wordpress Site URL i.e. https://example.com/</p>
		<p><code><strong>[mcp_site_domain]</strong></code> - Display Wordpress Site Domain i.e. example.com</p>
	';
}

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Shortcodes', 'redux-framework-demo' ),
		'id' => 'shortcodes',
		'icon' => 'el el-shortcode',
		'fields' => array(
			array(
				'id' => 'mcd_additional_shortcodes',
				'type' => 'raw',
				'content' => __( mcd_additional_shortcodes(), 'redux-framework-demo' ),
			),
		)
	)
);
