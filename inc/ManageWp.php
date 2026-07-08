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

		private function get_update_checker() {
			global $eyeonportal_update_checker;
			return $eyeonportal_update_checker;
		}

		private function get_remote_update_info() {
			$installed        = MCD_PLUGIN_VERSION;
			$remote_version   = $installed;
			$update_available = false;

			$checker = $this->get_update_checker();
			if ( $checker ) {
				$update = $checker->checkForUpdates();
				if ( $update && ! empty( $update->version ) ) {
					$remote_version   = $update->version;
					$update_available = version_compare( $installed, $remote_version, '<' );
				}
			}

			return array(
				'remote_version'   => $remote_version,
				'update_available' => $update_available,
			);
		}

		public function handle_status( $request ) {
			$auth = $this->verify_token( $request );
			if ( is_wp_error( $auth ) ) {
				return $auth;
			}

			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$update_info = $this->get_remote_update_info();

			return rest_ensure_response(
				array(
					'installed_version'  => MCD_PLUGIN_VERSION,
					'wp_version'         => get_bloginfo( 'version' ),
					'plugin_active'      => is_plugin_active( MCD_PLUGIN ),
					'remote_version'     => $update_info['remote_version'],
					'update_available'   => $update_info['update_available'],
					'manage_api_version' => self::MANAGE_API_VERSION,
				)
			);
		}

		public function handle_update( $request ) {
			$auth = $this->verify_token( $request );
			if ( is_wp_error( $auth ) ) {
				return $auth;
			}

			$previous_version = MCD_PLUGIN_VERSION;
			$checker          = $this->get_update_checker();

			if ( ! $checker ) {
				return new WP_Error(
					'update_checker_unavailable',
					'Update checker not available.',
					array( 'status' => 500 )
				);
			}

			$update = $checker->checkForUpdates();

			if ( ! $update ) {
				return rest_ensure_response(
					array(
						'success'            => true,
						'previous_version'   => $previous_version,
						'installed_version'  => MCD_PLUGIN_VERSION,
						'message'            => 'Already up to date',
					)
				);
			}

			if ( ! function_exists( 'request_filesystem_credentials' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			require_once ABSPATH . 'wp-admin/includes/plugin.php';

			$transient = get_site_transient( 'update_plugins' );
			if ( ! is_object( $transient ) ) {
				$transient = new stdClass();
			}
			if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
				$transient->response = array();
			}
			$transient->response[ MCD_PLUGIN ] = $update;
			set_site_transient( 'update_plugins', $transient );

			$skin     = new Automatic_Upgrader_Skin();
			$upgrader = new Plugin_Upgrader( $skin );
			$result   = $upgrader->upgrade( MCD_PLUGIN );

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
