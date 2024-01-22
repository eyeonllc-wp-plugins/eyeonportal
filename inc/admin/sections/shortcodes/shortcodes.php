<?php defined( 'ABSPATH' ) || exit;

function mcd_opening_hours() {
	return '
		<p><strong>Opening Hours</strong></p>
		<p><code><strong>[mcd_opening_hours_week]</strong></code> - Display a table with regular opening timings, holidays & irregular openings for a week.</p>
		<div style="padding-top:6px;margin-left:30px;">
			<strong>Optional Parameters:</strong><br>
			<p><code><strong>group_days</strong></code>: "yes"</p>
		</div>
		<p><code><strong>[mcd_opening_hours_today]</strong></code> - Show Open/Closed status, open/close timings & Holiday for today.</p>
		<div style="padding-top:6px;margin-left:30px;">
			<strong>Optional Parameters:</strong><br>
			<p><code><strong>open_text</strong></code>: "OPEN TODAY"</p>
			<p><code><strong>closed_text</strong></code>: "We\'re Closed"</p>
		</div>
	';
}

function mcd_additional_shortcodes() {
	return '
		<p><strong>Additional Shortcodes</strong></p>
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
				'id' => 'mcd_opening_hours',
				'type' => 'raw',
				'content' => __( mcd_opening_hours(), 'redux-framework-demo' ),
			),
			array(
				'id' => 'mcd_additional_shortcodes',
				'type' => 'raw',
				'content' => __( mcd_additional_shortcodes(), 'redux-framework-demo' ),
			),
		)
	)
);
