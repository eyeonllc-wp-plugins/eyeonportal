<?php

if( !class_exists('MyCenterPortal') ) {
	class MyCenterPortal {

		function __construct() {
		}

		function register() {
			add_filter( "plugin_action_links_".MCD_PLUGIN, array( $this, 'settings_link' ) );
		}

		function activate() {
			flush_rewrite_rules();
		}

		function deactivate() {
			flush_rewrite_rules();
		}

		function settings_link($links) {
			$settings_link = '<a href="admin.php?page='.MCD_PLUGIN_NAME.'">Settings</a>';
			array_push( $links, $settings_link );
			return $links;
		}

	}

	$myCenterDeals = new MyCenterPortal();
	$myCenterDeals->register();

	// activation
	register_activation_hook( __FILE__, array( $myCenterDeals, 'activate' ) );

	// deactivation
	register_deactivation_hook( __FILE__, array( $myCenterDeals, 'deactivate' ) );

}

