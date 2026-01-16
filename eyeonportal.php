<?php
/*
Plugin Name: EyeOn Portal
Plugin URI: https://eyeonllc.com/
Description: Show Deals, Stores & Events of a Center from EyeOn Portal.
Version: 0.1.60
Author: EyeOn LLC
Author URI: https://eyeonllc.com/
Licence: GPLv2 or later
*/

define('THREEJS_MAP_VERSION', '1.1.9');
define('THREEJS_MAP_API_RESPONSE_KEY', 'eyeon_map_api_response');

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
$api_base_url = 'https://web-backend-prod.eyeonportal.com';
if( isset($mcd_settings['api_access_token']) && !empty($mcd_settings['api_access_token']) ) {
	if( strpos($mcd_settings['api_access_token'], 'stage_') === 0 ) {
		$api_base_url = 'https://web-backend-staging.eyeonportal.com';
	} elseif( strpos($mcd_settings['api_access_token'], 'dev_') === 0 ) {
		$api_base_url = 'http://localhost:3002';
	}
}
defined('API_BASE_URL')				      OR define( 'API_BASE_URL', $api_base_url );

defined('MCD_API_CENTER')		        OR define( 'MCD_API_CENTER', '/v1/center');

defined('MCD_API_STORES')			      OR define( 'MCD_API_STORES', '/v1/retailers' );
defined('MCD_API_DEALS')			      OR define( 'MCD_API_DEALS', '/v1/deals' );
defined('MCD_API_EVENTS')			      OR define( 'MCD_API_EVENTS', '/v1/events' );
defined('MCD_API_CAREERS')		      OR define( 'MCD_API_CAREERS', '/v1/careers' );
defined('MCD_API_NEWS')	            OR define( 'MCD_API_NEWS', '/v1/blogs' );
defined('MCD_API_BANNERS')	        OR define( 'MCD_API_BANNERS', '/v1/web_slider' );

defined('MCD_API_CENTER_HOURS')	    OR define( 'MCD_API_CENTER_HOURS', '/v1/opening_hours' );
defined('MCP_API_LINKS')			      OR define( 'MCP_API_LINKS', '/v1/links' );

defined('RESTAURANTS_CATEGORY_ID')	OR define( 'RESTAURANTS_CATEGORY_ID', '4' );

defined('EYEON_API_SESSION_TOKEN') OR define('EYEON_API_SESSION_TOKEN', 'eyeon_api_session_token');
defined('EYEON_API_SESSION_TOKEN_EXPIRE') OR define('EYEON_API_SESSION_TOKEN_EXPIRE', 600);


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


require_once MCD_PLUGIN_PATH . 'inc/delete-db-rows/DeleteDBRows.php';