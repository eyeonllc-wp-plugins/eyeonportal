<?php
/*
Plugin Name: EyeOn Portal
Plugin URI: https://eyeonllc.com/
Description: Show Deals, Stores & Events of a Center from mycenterportal.com portal. (new)
Version: 0.0.11
Author: EyeOn LLC
Author URI: https://eyeonllc.com/
Licence: GPLv2 or later
*/

require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
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
$api_base_url = 'https://web-backend-prod.eyeonportal.com/v1/';
if( isset($mcd_settings['mcd_site_mode']) ) {
	if( $mcd_settings['mcd_site_mode'] == 'staging' ) {
		$api_base_url = 'https://web-backend-staging.eyeonportal.com/v1/';
	} elseif( $mcd_settings['mcd_site_mode'] == 'development' ) {
		$api_base_url = 'https://staging.mycenterportal.test/';
	}
}
defined('API_BASE_URL')				  OR define( 'API_BASE_URL', $api_base_url );

defined('MCD_API_CENTERS')		  OR define( 'MCD_API_CENTERS', API_BASE_URL . 'centers?limit=100&page=1');

defined('MCD_API_STORES')			  OR define( 'MCD_API_STORES', API_BASE_URL . 'retailers' );
defined('MCD_API_DEALS')			  OR define( 'MCD_API_DEALS', API_BASE_URL . 'deals' );
defined('MCD_API_EVENTS')			  OR define( 'MCD_API_EVENTS', API_BASE_URL . 'events' );
defined('MCD_API_CAREERS')		  OR define( 'MCD_API_CAREERS', API_BASE_URL . 'careers' );
defined('MCD_API_BLOG_POSTS')	  OR define( 'MCD_API_BLOG_POSTS', API_BASE_URL . 'blogs' );

defined('MCD_API_MAP_CONFIG')	  OR define( 'MCD_API_MAP_CONFIG', API_BASE_URL . 'api/mapit2/config' );

defined('MCD_API_SEARCH')			  OR define( 'MCD_API_SEARCH', API_BASE_URL . 'api/search' );

defined('MCD_OPENING_HOURS_WEEK')	OR define( 'MCD_OPENING_HOURS_WEEK', API_BASE_URL . 'api/opening-hours/week' );
defined('MCD_OPENING_HOURS_TODAY')	OR define( 'MCD_OPENING_HOURS_TODAY', API_BASE_URL . 'api/opening-hours/today' );

defined('MCP_API_LINKS')			OR define( 'MCP_API_LINKS', API_BASE_URL.'api/links' );

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
