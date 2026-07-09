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

		private function is_allowed_package_url( $package_url ) {
			$host = wp_parse_url( $package_url, PHP_URL_HOST );
			if ( empty( $host ) ) {
				return false;
			}

			$allowed_hosts = array(
				'api.github.com',
				'github.com',
				'codeload.github.com',
				'objects.githubusercontent.com',
			);

			return in_array( strtolower( $host ), $allowed_hosts, true );
		}

		private function resolve_package_data( $params ) {
			if ( ! empty( $params['package_url'] ) && is_string( $params['package_url'] ) ) {
				$package_url = esc_url_raw( $params['package_url'] );
				if ( empty( $package_url ) || ! $this->is_allowed_package_url( $package_url ) ) {
					return new WP_Error(
						'invalid_package_url',
						'Plugin package URL is not allowed.',
						array( 'status' => 400 )
					);
				}

				$response = wp_remote_get(
					$package_url,
					array(
						'timeout'     => 180,
						'redirection' => 5,
						'user-agent'  => 'EyeOn-Portal-ManageWp/' . MCD_PLUGIN_VERSION,
					)
				);

				if ( is_wp_error( $response ) ) {
					return $response;
				}

				$status_code = wp_remote_retrieve_response_code( $response );
				if ( $status_code < 200 || $status_code >= 300 ) {
					return new WP_Error(
						'download_failed',
						'Failed to download plugin package.',
						array( 'status' => 502 )
					);
				}

				$package_data = wp_remote_retrieve_body( $response );
				if ( empty( $package_data ) ) {
					return new WP_Error(
						'empty_package',
						'Downloaded plugin package is empty.',
						array( 'status' => 502 )
					);
				}

				return $package_data;
			}

			if ( ! empty( $params['package_base64'] ) && is_string( $params['package_base64'] ) ) {
				$package_data = base64_decode( $params['package_base64'], true );
				if ( false === $package_data || '' === $package_data ) {
					return new WP_Error(
						'invalid_package',
						'Invalid plugin package.',
						array( 'status' => 400 )
					);
				}

				return $package_data;
			}

			return new WP_Error(
				'missing_package',
				'Plugin package not provided.',
				array( 'status' => 400 )
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

			$params       = $request->get_json_params();
			$package_data = $this->resolve_package_data( $params );
			if ( is_wp_error( $package_data ) ) {
				$status = $package_data->get_error_data();
				$code   = is_array( $status ) && isset( $status['status'] ) ? (int) $status['status'] : 400;

				return new WP_REST_Response(
					array(
						'success' => false,
						'code'    => $package_data->get_error_code(),
						'message' => $package_data->get_error_message(),
					),
					$code
				);
			}

			$target_version = ! empty( $params['target_version'] ) ? (string) $params['target_version'] : null;

			return $this->install_plugin_package(
				$package_data,
				MCD_PLUGIN_VERSION,
				$target_version
			);
		}

		private function normalize_plugin_zip_file( $zip_path ) {
			$zip = new ZipArchive();
			if ( true !== $zip->open( $zip_path ) ) {
				return new WP_Error(
					'invalid_zip',
					'Downloaded package is not a valid zip archive.',
					array( 'status' => 502 )
				);
			}

			if ( false !== $zip->locateName( 'eyeonportal/eyeonportal.php' ) ) {
				$zip->close();
				return $zip_path;
			}

			$plugin_path = false;
			for ( $i = 0; $i < $zip->numFiles; $i++ ) {
				$name = $zip->getNameIndex( $i );
				if ( is_string( $name ) && preg_match( '#/eyeonportal\.php$#', $name ) ) {
					$plugin_path = $name;
					break;
				}
			}

			if ( ! $plugin_path ) {
				$zip->close();
				return new WP_Error(
					'plugin_entry_missing',
					'Downloaded package is missing eyeonportal.php.',
					array( 'status' => 502 )
				);
			}

			$prefix      = preg_replace( '/eyeonportal\.php$/', '', $plugin_path );
			$new_zip_path = $zip_path . '.normalized.zip';
			$new_zip     = new ZipArchive();

			if ( true !== $new_zip->open( $new_zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE ) ) {
				$zip->close();
				return new WP_Error(
					'zip_create_failed',
					'Could not create normalized plugin package.',
					array( 'status' => 500 )
				);
			}

			for ( $i = 0; $i < $zip->numFiles; $i++ ) {
				$name = $zip->getNameIndex( $i );
				if ( ! is_string( $name ) || strpos( $name, $prefix ) !== 0 || substr( $name, -1 ) === '/' ) {
					continue;
				}

				$relative = substr( $name, strlen( $prefix ) );
				if ( '' === $relative ) {
					continue;
				}

				$new_zip->addFromString( 'eyeonportal/' . $relative, $zip->getFromIndex( $i ) );
			}

			$zip->close();
			$new_zip->close();
			@unlink( $zip_path );

			return $new_zip_path;
		}

		private function install_plugin_package( $package_data, $previous_version, $target_version = null ) {
			if ( ! function_exists( 'request_filesystem_credentials' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			require_once ABSPATH . 'wp-admin/includes/plugin.php';

			// Capture activation state before the upgrade. WordPress silently
			// deactivates the plugin during Plugin_Upgrader::upgrade(), so we
			// only restore activation if it was active beforehand.
			$was_active = is_plugin_active( MCD_PLUGIN );

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

			$normalized_zip = $this->normalize_plugin_zip_file( $temp_zip );
			if ( is_wp_error( $normalized_zip ) ) {
				@unlink( $temp_zip );
				return new WP_REST_Response(
					array(
						'success' => false,
						'code'    => $normalized_zip->get_error_code(),
						'message' => $normalized_zip->get_error_message(),
					),
					502
				);
			}
			$temp_zip = $normalized_zip;

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

			$this->ensure_plugin_active( $was_active );

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
					'plugin_active'     => is_plugin_active( MCD_PLUGIN ),
					'message'           => 'Plugin updated successfully',
				)
			);
		}

		private function ensure_plugin_active( $was_active = true ) {
			// Respect the prior state: if the plugin was deactivated before the
			// update, leave it deactivated afterwards.
			if ( ! $was_active ) {
				return;
			}

			if ( ! function_exists( 'activate_plugin' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			if ( is_plugin_active( MCD_PLUGIN ) ) {
				return;
			}

			// Activate silently. The main plugin file is already loaded in this
			// request, so a non-silent activation would run plugin_sandbox_scrape()
			// and re-include the file, which can fatal on redeclared symbols and
			// leave the plugin deactivated.
			$result = activate_plugin( MCD_PLUGIN, '', false, true );

			// Fallback: force the option directly if activation still failed.
			if ( is_wp_error( $result ) || ! is_plugin_active( MCD_PLUGIN ) ) {
				$active = get_option( 'active_plugins', array() );
				if ( ! in_array( MCD_PLUGIN, $active, true ) ) {
					$active[] = MCD_PLUGIN;
					update_option( 'active_plugins', $active );
				}
			}
		}
	}

	$eyeon_manage_wp = new EyeOnManageWp();
	$eyeon_manage_wp->register();
}
