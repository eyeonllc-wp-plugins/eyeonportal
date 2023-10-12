<?php defined( 'ABSPATH' ) || exit;

function mcp_stores_categories() {
	$categories = '
		<table>
			<thead>
				<tr>
					<th colspan="2" align="center">Retailer Categories</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Category</th>
				</tr>
			</thead>
			<tbody>
	';
	foreach( mcp_retailers_categories() as $category ) {
		$categories .= '
			<tr>
				<td>'.$category['id'].'</td>
				<td>'.$category['name'].'</td>
			</tr>
		';
	}
	$categories .= '</tbody></table>';
	return $categories;
}

function mcd_common_html() {
	return '
		<p><code><strong>[mycenterdeals]</strong></code> - Display Deals of selected Center.</p>
		<p><code><strong>[mycentercareers]</strong></code> - Display Careers of selected Center.</p>
		<br>
		<p><code><strong>[mycentermap2]</strong></code> - MapIT2 (ThreeJS).</p>
		<br>
		<p><code><strong>[mcd_search_form style="light"]</strong></code> - Display Search Form. <strong><code>style</code></strong> can be "light" or "dark".</p>
	';
}

function mcd_stores_html() {
	return '
		<p><code><strong>[<span id="mycenterstores_shortcode">mycenterstores</span>]</strong></code> - Display Stores.</p>
		<div style="padding-top:6px;margin-left:30px;">
			<strong>Optional Parameters:</strong><br>
			<p><code><strong>cat</strong></code>: {category_id}.</p>
			<p><code><strong>map</strong></code>: "yes" or "no". <em>Default: "yes"</em></p>
			<p><code><strong>search</strong></code>: "yes" or "no". <em>Default: "yes"</em></p>
			<p><code><strong>tags</strong></code>: tags by comma separated.</p>
		</div>
		<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery(document).on("click", "#categories_dropdown", function(event) {
				event.preventDefault();
				var shortcode = "mycenterstores";
				var selected_category = jQuery(this).val();
				if( selected_category >= 0 ) {
					shortcode += " cat=\""+selected_category+"\"";
				}
				jQuery("#mycenterstores_shortcode").html(shortcode);
				return false;
			});
		});
		</script>
	';
}

function mcd_events_html() {
	return '
		<p><code><strong>[mycenterevents]</strong></code> - Display Events Grid & Calendar of selected Center.</p>
		<div style="padding-top:6px;margin-left:30px;">
			<strong>Optional Parameters:</strong><br>
			<p><code><strong>type</strong></code>: "calendar" or "grid".</p>
			<p><code><strong>holiday</strong></code>: "true" or "false".</p>
		</div>
	';
}

function mcd_blogs_html() {
	return '
		<p><code><strong>[mycenterblog]</strong></code> - Display Blog Posts of selected Center.</p>
		<div style="padding-top:6px;margin-left:30px;">
			<strong>Optional Parameters:</strong><br>
			<p><code><strong>filters</strong></code>: "yes" or "no". <em>Default: "no"</em></p>
		</div>
	';
}

function mcd_slider_html() {
	return '
		<p><strong>Slider Shortcodes:</strong></p>
		<p><code><strong>[mcd_slider type="events"]</strong></code></p>
		<div style="padding-top:6px;margin-left:30px;margin-bottom:10px;">
			<strong>Required Parameters:</strong><br>
			<p><code><strong>type</strong></code>: "stores", "events", "deals", "careers".</p>
		</div>
		<div style="padding-top:6px;margin-left:30px;">
			<strong>Optional Parameters:</strong><br>
			<p><code><strong>count</strong></code>: No of items to fetched from portal. <em>Default: 8</em></p>
			<p><code><strong>show</strong></code>: No of items to show in slider. <em>Default: 4</em></p>
			<p><code><strong>auto-slide</strong></code>: "true" or "false". <em>Default: true</em></p>
			<p><code><strong>auto-slide-speed</strong></code>: <em>Default: 4000</em></p>
			<p><code><strong>metadata</strong></code>: Display additional infomation about item like Title, Dates & Time. <em>Default: true</em></p>
			<p><code><strong>items-on-mobile</strong></code>: No of items show in mobile. <em>Default: 1</em></p>
			<p><code><strong>show-dots</strong></code>: Display dots navigation just below the slider. <em>Default: false</em></p>
			<br>
			<p><code><strong>holiday</strong></code>: "true" or "false". This parameter is only for type="events".</p>
			<p><code><strong>kids</strong></code>: "true" or "false". This parameter is only for type="events".</p>
			<br>
			<p><code><strong>cat</strong></code>: {category_id}. This parameter is only for type="stores".</p>
			<p><code><strong>curbside_pickup</strong></code>: "yes" or "no". <em>Default: "no"</em>. This parameter is only for type="stores".</p>
			<p><code><strong>tags</strong></code>: tags by comma separated. This parameter is only for type="stores".</p>
		</div>
	';
}

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

Redux::set_section(
	$opt_name,
	array(
		'title' => __( 'Shortcodes', 'redux-framework-demo' ),
		'id' => 'shortcodes',
		'icon' => 'el el-shortcode',
		'fields' => array(
			array(
				'id' => 'mcp_stores_categories',
				'type' => 'raw',
				'content' => __( mcp_stores_categories(), 'redux-framework-demo' ),
			),
			array(
				'id' => 'mcd_common_html',
				'type' => 'raw',
				'content' => __( mcd_common_html(), 'redux-framework-demo' ),
			),
			array(
				'id' => 'mcd_stores_html',
				'type' => 'raw',
				'content' => __( mcd_stores_html(), 'redux-framework-demo' ),
			),
			array(
				'id' => 'mcd_events_html',
				'type' => 'raw',
				'content' => __( mcd_events_html(), 'redux-framework-demo' ),
			),
			array(
				'id' => 'mcd_blogs_html',
				'type' => 'raw',
				'content' => __( mcd_blogs_html(), 'redux-framework-demo' ),
			),
			array(
				'id' => 'mcd_slider_html',
				'type' => 'raw',
				'content' => __( mcd_slider_html(), 'redux-framework-demo' ),
			),
			array(
				'id' => 'mcd_opening_hours',
				'type' => 'raw',
				'content' => __( mcd_opening_hours(), 'redux-framework-demo' ),
			),
		)
	)
);
