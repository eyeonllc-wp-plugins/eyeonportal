<?php

if( !class_exists('MCDShortcodes') ) {
	class MCDShortcodes {

		private $mcd_settings;
		private $page_title = MCD_PLUGIN_TITLE;
		private $template = '';
		private $search = array();
		private $links_page_template = 'templates/links.php';

		function __construct() {
			$this->mcd_settings = get_option(MCD_REDUX_OPT_NAME);
		}

		function register() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue') );

      add_shortcode('mcd_search_form', function() { return ''; });
      add_shortcode('mcd_opening_hours_week', function() { return ''; });
      add_shortcode('mcd_opening_hours_today', function() { return ''; });

      add_shortcode('mcp_site_name', function() { return get_bloginfo('name'); });
      add_shortcode('mcp_site_url', function() { return site_url(); });
      add_shortcode('mcp_site_domain', function() { return $_SERVER['HTTP_HOST']; });
			
			add_filter( 'query_vars', array( $this, 'add_rewrite_vars' ) );
			add_action( 'template_redirect', array( $this, 'single_page_rewrite_catch' ) );
			add_action( 'redux/options/'.MCD_REDUX_OPT_NAME.'/settings/change', array( $this, 'redux_options_saved') );
      
      add_action( 'parse_request', array($this, 'handle_image_proxy_request') );

			add_filter( 'body_class', array( $this, 'add_plugin_body_class') );
			add_filter( 'wp_head', array( $this, 'dynamic_styles_scripts') );
			add_action( 'init', array( $this, 'mcd_flush_rewrite_rules' ) );

			add_filter( 'theme_page_templates', array( $this, 'links_page_template' ) );
			add_filter( 'template_include', array( $this, 'links_change_page_template' ) );

			add_filter( 'wp_title', array( $this, 'change_page_title' ), 999 );
			add_filter( 'wpseo_title', array( $this, 'change_page_title' ), 999 );
			add_filter( 'pre_get_document_title', array( $this, 'change_page_title' ) );
    }

		function mcd_flush_rewrite_rules() {
			global $wp_rewrite;

			$db_mcd_plugin_version = get_option('mcd_plugin_version');
			$this->add_rewrite_rules($this->mcd_settings);
			if( MCD_PLUGIN_VERSION != $db_mcd_plugin_version ) {
				$wp_rewrite->flush_rules(false);
				update_option('mcd_plugin_version', MCD_PLUGIN_VERSION);
			}
		}

		function redux_options_saved($options) {
			global $wp_rewrite;
			$this->add_rewrite_rules($options);
			$wp_rewrite->flush_rules(false);
		}

		function add_plugin_body_class($classes) {
			global $post, $wp_query;
			$classes[] = mcd_current_theme_name();
			
			if( $this->is_querystring_present() ) {
        $classes[] = MCD_PLUGIN_NAME.'-single';
			}
			return $classes;
		}

		function return_include_output($file) {
			ob_start();
			include( $file );
			return ob_get_clean(); 
		}

		function add_rewrite_vars( $vars ) {
			$vars[] = 'mycenterdeal';
			$vars[] = 'mycenterstore';
			$vars[] = 'mycenterevent';
			$vars[] = 'mycentercareer';
			$vars[] = 'mycenterblogpost';
			$vars[] = 'mcdmapretailer';
			return $vars;
		}

    function handle_image_proxy_request() {
      if (isset($_GET['eyeonmedia'])) {
        $image_url = urldecode($_GET['eyeonmedia']);

        // Security check - you might want to add more validation here
        $allowed_domains = array(
          'eyeon-media-development.eyeondev1.com',
          'eyeon-media-staging.eyeondev1.com',
          'eyeon-media-production.eyeondev1.com',
        );

        $parsed_url = parse_url($image_url);
        if (!in_array($parsed_url['host'], $allowed_domains)) {
          status_header(403);
          die('Domain not allowed');
        }
        
        // Fetch the image
        $response = wp_remote_get($image_url, array(
          'timeout' => 10,
          'sslverify' => false
        ));

        if (is_wp_error($response)) {
          status_header(404);
          die('Failed to fetch image');
        }

        $content_type = wp_remote_retrieve_header($response, 'content-type');
        $image_data = wp_remote_retrieve_body($response);

        // Verify it's an image
        if (!strpos($content_type, 'image/') === 0) {
          status_header(400);
          die('Invalid image type');
        }

        // Set headers
        nocache_headers();
        header('Content-Type: ' . $content_type);
        header('Content-Length: ' . strlen($image_data));

        // Output image
        echo $image_data;
        exit;
      }
    }

		function add_rewrite_rules($saved_options) {
			add_rewrite_tag( '%mycenterdeal%', '([^&]+)' );
			add_rewrite_rule(
				'^'.$saved_options['deals_single_page_slug'].'/([^/]*)/?',
				'index.php?page_id='.$this->mcd_settings['deals_single_page_template'].'&mycenterdeal=$matches[1]',
				'top'
			);

			add_rewrite_tag( '%mycenterstore%', '([^&]+)' );
			add_rewrite_rule(
				'^'.$saved_options['stores_single_page_slug'].'/([^/]*)/?',
				'index.php?page_id='.$this->mcd_settings['stores_single_page_template'].'&mycenterstore=$matches[1]',
				'top'
			);

			add_rewrite_tag( '%mycenterevent%', '([^&]+)' );
			add_rewrite_rule(
				'^'.$saved_options['events_single_page_slug'].'/([^/]*)/?',
				'index.php?page_id='.$this->mcd_settings['events_single_page_template'].'&mycenterevent=$matches[1]',
				'top'
			);

			add_rewrite_tag( '%mycentercareer%', '([^&]+)' );
			add_rewrite_rule(
				'^'.$saved_options['careers_single_page_slug'].'/([^/]*)/?',
				'index.php?page_id='.$this->mcd_settings['careers_single_page_template'].'&mycentercareer=$matches[1]',
				'top'
			);

			add_rewrite_tag( '%mycenterblogpost%', '([^&]+)' );
			add_rewrite_rule(
				'^'.$saved_options['blog_single_page_slug'].'/([^/]*)/?',
				'index.php?page_id='.$this->mcd_settings['blog_single_page_template'].'&mycenterblogpost=$matches[1]',
				'top'
			);

			$map_page_slug = get_post_field('post_name', $this->mcd_settings['map_page']);
			add_rewrite_tag( '%mcdmapretailer%', '([^&]+)' );
			add_rewrite_rule(
				'^'.$map_page_slug.'/([^/]*)/?',
				'index.php?page_id='.$this->mcd_settings['map_page'].'&mcdmapretailer=$matches[1]',
				'top'
			);
		}

		function single_page_rewrite_catch() {
			global $wp_query;

			// redirect query string pages to SEO frienly URLs
			if( isset($_GET['mycenterdeal']) ) {
				wp_redirect( home_url( "/".$this->mcd_settings['stores_single_page_slug']."/" ) . urlencode( get_query_var( 'mycenterdeal' ) ) ); exit;
			}
			if( isset($_GET['mycenterstore']) ) {
				wp_redirect( home_url( "/".$this->mcd_settings['stores_single_page_slug']."/" ) . urlencode( get_query_var( 'mycenterstore' ) ) ); exit;
			}
			if( isset($_GET['mycenterevent']) ) {
				wp_redirect( home_url( "/".$this->mcd_settings['stores_single_page_slug']."/" ) . urlencode( get_query_var( 'mycenterevent' ) ) ); exit;
			}
			if( isset($_GET['mycentercareer']) ) {
				wp_redirect( home_url( "/".$this->mcd_settings['careers_single_page_slug']."/" ) . urlencode( get_query_var( 'mycentercareer' ) ) ); exit;
			}
			if( isset($_GET['mycenterblogpost']) ) {
				wp_redirect( home_url( "/".$this->mcd_settings['blog_single_page_slug']."/" ) . urlencode( get_query_var( 'mycenterblogpost' ) ) ); exit;
			}
			if( isset($_GET['mcdmapretailer']) ) {
				wp_redirect( home_url( "/".$this->mcd_settings['map_page']."/" ) . urlencode( get_query_var( 'mcdmapretailer' ) ) ); exit;
			}

			if ( array_key_exists( 'mycenterdeal', $wp_query->query_vars ) ) {
				$this->template = 'templates/deal.php';
				$req_url = MCD_API_DEALS.'/'.get_query_var('mycenterdeal', 0);
        $dealData = mcd_api_data($req_url);
				$this->mcd_settings['mycenterdeal'] = $dealData;
				if( $this->mcd_settings['deals_single_page_title'] == 'custom' ) {
					$this->page_title = $this->mcd_settings['deals_single_page_custom_title'];
				} else {
					$this->page_title = @$this->mcd_settings['mycenterdeal']['title'];
				}
			} elseif ( array_key_exists( 'mycenterstore', $wp_query->query_vars ) ) {
				$this->template = 'templates/store.php';
        $multiple_location_retailer_id = (isset($_GET['r']) && !empty(['r'])) ? $_GET['r'] : null;
				$req_url = MCD_API_STORES.'/'.get_query_var('mycenterstore', 0).($multiple_location_retailer_id?'/'.$multiple_location_retailer_id:'');
        $custom_center_id = (isset($_GET['c']) && !empty(['c'])) ? $_GET['c'] : null;
				$this->mcd_settings['mycenterstore'] = mcd_api_data($req_url, $custom_center_id);
				if( $this->mcd_settings['stores_single_page_title'] == 'custom' ) {
					$this->page_title = $this->mcd_settings['stores_single_page_custom_title'];
				} else {
					$this->page_title = @$this->mcd_settings['mycenterstore']['name'];
				}

				$req_url = MCD_API_MAP_CONFIG.'?center='.$this->mcd_settings['center_id'];
				$map_config = mcd_api_data($req_url);
				$this->mcd_settings['map_config'] = $map_config;
			} elseif ( array_key_exists( 'mycenterevent', $wp_query->query_vars ) ) {
				$this->template = 'templates/event.php';

        // ==================
        $this->mcd_settings['events_single_add_to_calendar'] = false;
				// mcd_include_js('add-to-calendar', 'assets/plugins/add-to-calendar.min.js', true);
        // ==================

				$req_url = MCD_API_EVENTS.'/'.get_query_var('mycenterevent', 0);
        $eventData = mcd_api_data($req_url);
				$this->mcd_settings['mycenterevent'] = $eventData;
				if( $this->mcd_settings['events_single_page_title'] == 'custom' ) {
					$this->page_title = $this->mcd_settings['events_single_page_custom_title'];
				} else {
					$this->page_title = $this->mcd_settings['mycenterevent']['title'];
				}
			} elseif ( array_key_exists( 'mycentercareer', $wp_query->query_vars ) ) {
				$this->template = 'templates/career.php';
				$req_url = MCD_API_CAREERS.'/'.get_query_var('mycentercareer', 0);
        $careerData = mcd_api_data($req_url);
				$this->mcd_settings['mycentercareer'] = $careerData;
				if( $this->mcd_settings['careers_single_page_title'] == 'custom' ) {
					$this->page_title = $this->mcd_settings['careers_single_page_custom_title'];
				} else {
					$this->page_title = $this->mcd_settings['mycentercareer']['title'];
				}
			} elseif ( array_key_exists( 'mycenterblogpost', $wp_query->query_vars ) ) {
				$this->template = 'templates/blog.php';
        wp_enqueue_script( 'eyeon-moment' );
        wp_enqueue_script( 'eyeon-elementor-utils' );
				$req_url = MCD_API_NEWS.'/'.get_query_var('mycenterblogpost', 0);
				$blogpost = mcd_api_data($req_url);
        $this->mcd_settings['mycenterblogpost'] = $blogpost;
				if( $this->mcd_settings['blog_single_page_title'] == 'custom' ) {
					$this->page_title = $this->mcd_settings['blog_single_page_custom_title'];
				} else {
					$this->page_title = $this->mcd_settings['mycenterblogpost']['title'];
				}
			}

			add_filter( 'the_content', array( $this, 'change_single_page_content') );
		}
 
		function links_page_template( $templates ) {
			$templates[$this->links_page_template] = 'MCP Links Template';
			return $templates;
		}

		function links_change_page_template($template) {
			if (is_page()) {
				$meta = get_post_meta(get_the_ID());

				if (!empty($meta['_wp_page_template'][0]) && $meta['_wp_page_template'][0] != $template) {
					$selected_template = $meta['_wp_page_template'][0];
					if( $selected_template == $this->links_page_template ) {
						$template = MCD_PLUGIN_PATH.$meta['_wp_page_template'][0];
					}
				}
			}

			return $template;
		}

		function change_single_page_content( $content ) {
			global $post, $wp_query;

			if( !empty($this->template) ) {
				$content .= $this->return_include_output( MCD_PLUGIN_PATH . $this->template );
			}
			return $content;
		}

		function change_page_title($title) {
			if( $this->is_querystring_present() ) {
				$title = $this->page_title.' - '.get_bloginfo('name');
			}
			return $title;
		}

		function enqueue() {
			global $post, $wp_query;
			
			mcd_include_css('fontawesome', 'assets/plugins/fontawesome/css/fontawesome-all.min.css');
			
			if( $this->is_querystring_present() ) {
				mcd_include_js('main', 'assets/js/main.js');
				mcd_include_css('style', 'assets/css/style.min.css');
        wp_enqueue_style( 'eyeon-elementor-style' );
			}
		}

		function is_querystring_present() {
			global $post, $wp_query;
			$query_strings = array(
				'mycenterdeal',
				'mycenterstore',
				'mycenterevent',
				'mycentercareer',
				'mycenterblogpost',
			);
			$response = false;
			foreach ($query_strings as $query_string) {
				if( array_key_exists($query_string, $wp_query->query_vars) ) {
					$response = true;
					break;
				}
			}
			return $response;
		}

		function dynamic_styles_scripts() {
			global $post, $wp_query;
			$mcd_settings = $this->mcd_settings;

			if( $this->is_querystring_present() ) {
				include ( MCD_PLUGIN_PATH . 'assets/dynamic.php');
			}
		}
	}

	$mcd_shortcodes = new MCDShortcodes();
	$mcd_shortcodes->register();
}

