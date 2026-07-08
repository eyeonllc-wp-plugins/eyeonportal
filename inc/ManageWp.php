<?php

if ( ! class_exists( 'EyeOnManageWp' ) ) {
	class EyeOnManageWp {

		const MANAGE_API_VERSION = '1';

		public function register() {
			add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		}

		public function register_rest_routes() {
			register_rest_route(
				'eyeon-portal/v1',
				'/manage-wp/status',
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'handle_status' ),
					'permission_callback' => '__return_true',
				)
			);

			register_rest_route(
				'eyeon-portal/v1',
				'/manage-wp/update',
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'handle_update' ),
					'permission_callback' => '__return_true',
				)
			);
		}

		private function verify_token( $request ) {
			$token      = $request->get_header( 'X-Eyeon-Api-Token' );
			$settings   = get_option( MCD_REDUX_OPT_NAME );
			$configured = isset( $settings['api_access_token'] ) ? $settings['api_access_token'] : '';

			if ( empty( $configured ) ) {
				return new WP_Error(
					'token_not_configured',
					'API token not configured on site.',
					array( 'status' => 503 )
				);
			}

			if ( empty( $token ) || ! hash_equals( $configured, $token ) ) {
				return new WP_Error(
					'invalid_token',
					'API token mismatch',
					array( 'status' => 401 )
				);
			}

			return true;
		}

		public function handle_status( $request ) {
			$auth = $this->verify_token( $request );
			if ( is_wp_error( $auth ) ) {
				return $auth;
			}

			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			return rest_ensure_response(
				array(
					'installed_version'  => MCD_PLUGIN_VERSION,
					'wp_version'         => get_bloginfo( 'version' ),
					'plugin_active'      => is_plugin_active( MCD_PLUGIN ),
					'remote_version'     => MCD_PLUGIN_VERSION,
					'update_available'   => false,
					'manage_api_version' => self::MANAGE_API_VERSION,
				)
			);
		}

		public function handle_update( $request ) {
			$auth = $this->verify_token( $request );
			if ( is_wp_error( $auth ) ) {
				return $auth;
			}

			$params = $request->get_json_params();
			if ( empty( $params['package_base64'] ) || ! is_string( $params['package_base64'] ) ) {
				return new WP_REST_Response(
					array(
						'success' => false,
						'code'    => 'missing_package',
						'message' => 'Plugin package not provided.',
					),
					400
				);
			}

			$package_data = base64_decode( $params['package_base64'], true );
			if ( false === $package_data || '' === $package_data ) {
				return new WP_REST_Response(
					array(
						'success' => false,
						'code'    => 'invalid_package',
						'message' => 'Invalid plugin package.',
					),
					400
				);
			}

			$target_version = ! empty( $params['target_version'] ) ? (string) $params['target_version'] : null;

			return $this->install_plugin_package(
				$package_data,
				MCD_PLUGIN_VERSION,
				$target_version
			);
		}

		private function install_plugin_package( $package_data, $previous_version, $target_version = null ) {
			if ( ! function_exists( 'request_filesystem_credentials' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			require_once ABSPATH . 'wp-admin/includes/plugin.php';

			$temp_file = wp_tempnam( 'eyeonportal-update' );
			if ( ! $temp_file ) {
				return new WP_REST_Response(
					array(
						'success' => false,
						'code'    => 'temp_file_failed',
						'message' => 'Could not create temporary file.',
					),
					500
				);
			}

			$temp_zip = $temp_file . '.zip';
			if ( ! @rename( $temp_file, $temp_zip ) ) {
				$temp_zip = $temp_file;
			}

			if ( false === file_put_contents( $temp_zip, $package_data ) ) {
				@unlink( $temp_zip );
				return new WP_REST_Response(
					array(
						'success' => false,
						'code'    => 'write_package_failed',
						'message' => 'Could not write package file.',
					),
					500
				);
			}

			$transient = get_site_transient( 'update_plugins' );
			if ( ! is_object( $transient ) ) {
				$transient = new stdClass();
			}
			if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
				$transient->response = array();
			}

			$transient->response[ MCD_PLUGIN ] = (object) array(
				'package'     => $temp_zip,
				'new_version' => $target_version ? $target_version : $previous_version,
				'plugin'      => MCD_PLUGIN,
			);
			set_site_transient( 'update_plugins', $transient );

			$skin     = new Automatic_Upgrader_Skin();
			$upgrader = new Plugin_Upgrader( $skin );
			$result   = $upgrader->upgrade( MCD_PLUGIN );

			@unlink( $temp_zip );

			if ( is_wp_error( $result ) ) {
				return new WP_REST_Response(
					array(
						'success' => false,
						'code'    => 'upgrader_failed',
						'message' => $result->get_error_message(),
					),
					500
				);
			}

			if ( false === $result ) {
				$error_message = 'Plugin upgrade failed.';
				if ( is_wp_error( $skin->result ) ) {
					$error_message = $skin->result->get_error_message();
				}

				return new WP_REST_Response(
					array(
						'success' => false,
						'code'    => 'upgrader_failed',
						'message' => $error_message,
					),
					500
				);
			}

			clearstatcache();
			$plugin_data = get_file_data(
				MCD_PLUGIN_PATH . 'eyeonportal.php',
				array( 'version' => 'Version' )
			);

			return rest_ensure_response(
				array(
					'success'           => true,
					'previous_version'  => $previous_version,
					'installed_version' => $plugin_data['version'],
					'message'           => 'Plugin updated successfully',
				)
			);
		}
	}

	$eyeon_manage_wp = new EyeOnManageWp();
	$eyeon_manage_wp->register();
}
