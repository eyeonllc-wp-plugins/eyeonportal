<?php
/*
Plugin Name: EyeOn Portal
Plugin URI: https://eyeonllc.com/
Description: Show Deals, Stores & Events of a Center from mycenterportal.com portal.
Version: 0.0.71
Author: EyeOn LLC
Author URI: https://eyeonllc.com/
Licence: GPLv2 or later
*/

require 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/eyeonllc-wp-plugins/eyeonportal',
	__FILE__,
	'eyeonportal'
);
$myUpdateChecker->setBranch('master');
// $myUpdateChecker->setAuthentication('token_here');


defined('MCD_REDUX_OPT_NAME')		OR define( 'MCD_REDUX_OPT_NAME', 'mcd_settings' );

if( !defined('ABSPATH') ) die();
$mcd_settings = get_option(MCD_REDUX_OPT_NAME);

// Common Constants
defined('EYEON_NAMESPACE') OR define('EYEON_NAMESPACE', 'eyeon_elementor_widgets');
define( 'MCD_PLUGIN_NAME', 'eyeonportal' );
define( 'MCD_PLUGIN_TITLE', 'EyeOn Portal' );
define( 'MCD_PLUGIN', plugin_basename( __FILE__ ) );
define( 'MCD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'MCD_PLUGIN_URL', plugins_url( '', __FILE__ ).'/' );

$plugin_data = get_file_data(MCD_PLUGIN_PATH.'eyeonportal.php', array("version"=>"Version"));
define('MCD_PLUGIN_VERSION', $plugin_data['version']);

// API to get data from mycenterdeals portal
$api_base_url = 'https://web-backend-prod.eyeonportal.com/';
if( isset($mcd_settings['mcd_site_mode']) ) {
	if( $mcd_settings['mcd_site_mode'] == 'staging' ) {
		$api_base_url = 'https://web-backend-staging.eyeonportal.com/';
	} elseif( $mcd_settings['mcd_site_mode'] == 'development' ) {
		$api_base_url = 'https://staging.mycenterportal.test/';
	}
}
defined('API_BASE_URL')				      OR define( 'API_BASE_URL', $api_base_url );

defined('MCD_API_CENTERS')		      OR define( 'MCD_API_CENTERS', API_BASE_URL . 'v1/centers?limit=100&page=1');

defined('MCD_API_STORES')			      OR define( 'MCD_API_STORES', API_BASE_URL . 'v1/retailers' );
defined('MCD_API_DEALS')			      OR define( 'MCD_API_DEALS', API_BASE_URL . 'v1/deals' );
defined('MCD_API_EVENTS')			      OR define( 'MCD_API_EVENTS', API_BASE_URL . 'v1/events' );
defined('MCD_API_CAREERS')		      OR define( 'MCD_API_CAREERS', API_BASE_URL . 'v1/careers' );
defined('MCD_API_NEWS')	            OR define( 'MCD_API_NEWS', API_BASE_URL . 'v1/blogs' );

defined('MCD_API_CENTER_HOURS')	    OR define( 'MCD_API_CENTER_HOURS', API_BASE_URL . 'v1/opening_hours' );
defined('MCP_API_LINKS')			      OR define( 'MCP_API_LINKS', API_BASE_URL.'v1/links' );

defined('RESTAURANTS_CATEGORY_ID')	OR define( 'RESTAURANTS_CATEGORY_ID', '4' );


defined('MCD_API_MAP_CONFIG')	      OR define( 'MCD_API_MAP_CONFIG', API_BASE_URL . 'api/mapit2/config' );

defined('MCD_API_SEARCH')			      OR define( 'MCD_API_SEARCH', API_BASE_URL . 'api/search' );

defined('MCD_OPENING_HOURS_WEEK')   OR define( 'MCD_OPENING_HOURS_WEEK', API_BASE_URL . 'api/opening-hours/week' );
defined('MCD_OPENING_HOURS_TODAY')	OR define( 'MCD_OPENING_HOURS_TODAY', API_BASE_URL . 'api/opening-hours/today' );


add_theme_support( 'title-tag' );


// Common functions
require_once MCD_PLUGIN_PATH . 'inc/functions.php';

// Plugin Registration
require_once MCD_PLUGIN_PATH . 'inc/Plugin.php';

if ( is_admin() ) {
	// Backend Settings page
	require_once MCD_PLUGIN_PATH . 'inc/Admin.php';
}

// if ( !is_admin() && !wp_is_json_request() ) {
	// Frontend Shortcodes
	require_once MCD_PLUGIN_PATH . 'inc/Shortcodes.php';
	require_once MCD_PLUGIN_PATH . 'elementor/RegisterWidgets.php';
// }


// Enqueue Elementor library
function enqueue_elementor_library() {
    if (class_exists('Elementor\Plugin')) {
        wp_enqueue_script('elementor-frontend');
        wp_enqueue_style('elementor-frontend');
    }
}
add_action('wp_enqueue_scripts', 'enqueue_elementor_library');

// Register custom Elementor template
function register_custom_elementor_template() {
    \Elementor\Plugin::instance()->templates_manager->register_template_type('custom', [
        'label' => __('Custom Template 1', 'your-textdomain'),
    ]);
}
add_action('elementor/template-library/before_template', 'register_custom_elementor_template');

// Render custom Elementor template content
function render_custom_elementor_template_content($content, $template_id) {
  echo $template_id;
    if ('your-custom-template-id' === $template_id) {
        ob_start();
        ?>
        <div class="custom-elementor-template">
            <!-- Your custom template content here -->
            <h2>This is a custom Elementor template generated programmatically.</h2>
        </div>
        <?php
        $content = ob_get_clean();
    }
    return $content;
}
add_filter('elementor/frontend/template_content', 'render_custom_elementor_template_content', 10, 2);