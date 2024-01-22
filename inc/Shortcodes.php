<?php

if( !class_exists('MCDShortcodes') ) {
	class MCDShortcodes {

		private $mcd_settings;
		private $page_title = MCD_PLUGIN_TITLE;
		private $template = '';
		private $search = array();
		private $links_page_template = 'templates/links/index.php';

		function __construct() {
			$this->mcd_settings = get_option(MCD_REDUX_OPT_NAME);
		}

		function register() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue') );

      add_shortcode('mcp_site_name', function() { return get_bloginfo('name'); });
      add_shortcode('mcp_site_url', function() { return site_url(); });
      add_shortcode('mcp_site_domain', function() { return $_SERVER['HTTP_HOST']; });
			
			add_shortcode( 'mycenterdeals', array( $this, 'deals_shortcode' ) );
			add_shortcode( 'mycenterstores', array( $this, 'stores_shortcode' ) );
			add_shortcode( 'mycenterevents', array( $this, 'events_shortcode' ) );
			add_shortcode( 'mycentercareers', array( $this, 'careers_shortcode' ) );
			add_shortcode( 'mycenterblog', array( $this, 'blog_posts_shortcode' ) );
			add_shortcode( 'mycentermap2', array( $this, 'mapit2_shortcode' ) );
			add_shortcode( 'mcd_search_form', array( $this, 'mcd_search_form' ) );
			add_shortcode( 'mcd_slider', array( $this, 'mcd_slider' ) );
			add_shortcode( 'mcd_opening_hours_week', array( $this, 'mcd_opening_hours_week' ) );
			add_shortcode( 'mcd_opening_hours_today', array( $this, 'mcd_opening_hours_today' ) );

			add_filter( 'query_vars', array( $this, 'add_rewrite_vars' ) );
			add_action( 'template_redirect', array( $this, 'single_page_rewrite_catch' ) );
			add_action( 'redux/options/'.MCD_REDUX_OPT_NAME.'/settings/change', array( $this, 'redux_options_saved') );

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
			
			$shortcode = $this->is_shortcode_present();
			if( (is_a( $post, 'WP_Post') && $shortcode ) || $this->is_querystring_present() ) {
				$classes[] = MCD_PLUGIN_NAME.'-plugin code-'.$shortcode;
				if( $this->is_querystring_present() ) {
					$classes[] = MCD_PLUGIN_NAME.'-single';
				}
			}
			return $classes;
		}

		function return_include_output($file) {
			ob_start();
			include( $file );
			return ob_get_clean(); 
		}

		function deals_shortcode() {
			return $this->return_include_output( MCD_PLUGIN_PATH . 'templates/deals/index.php' );
		}

		function stores_shortcode($shortcode_atts) {
			global $mcd_settings;
			$atts = shortcode_atts(
				array(
					'cat' => -1,
					'map' => 'yes',
					'search' => 'yes',
					'tags' => '',
				),
			$shortcode_atts );
			// $atts['cat'] = $atts['cat']>=0?$atts['cat']:'';
			$this->mcd_settings['stores_shortcode_atts'] = $atts;

			$this->mcd_settings['stores_api_url'] = MCD_API_STORES.'?limit=100&page=1';

			$api_call_url = MCD_API_MAP_CONFIG.'?center='.$mcd_settings['center_id'];
			$api_response = mcd_api_data($api_call_url);
			$this->mcd_settings['map_config'] = $api_response;

			return $this->return_include_output( MCD_PLUGIN_PATH . 'templates/stores/index.php' );
		}

		function events_shortcode($shortcode_atts) {
			$this->mcd_settings['events_shortcode_atts'] = $shortcode_atts;
			$this->mcd_settings['slider_items'] = array();

			return $this->return_include_output( MCD_PLUGIN_PATH . 'templates/events/index.php' );
		}

		function careers_shortcode() {
			return $this->return_include_output( MCD_PLUGIN_PATH . 'templates/careers/index.php' );
		}

		function blog_posts_shortcode($shortcode_atts) {
			global $mcd_settings;
			$atts = shortcode_atts(
				array(
					'filters' => 'no',
				),
			$shortcode_atts );
			$this->mcd_settings['blog_posts_shortcode_atts'] = $atts;

			return $this->return_include_output( MCD_PLUGIN_PATH . 'templates/blog/index.php' );
		}

		function mapit2_shortcode() {
			if ( !is_admin() ) {
				return $this->return_include_output( MCD_PLUGIN_PATH . 'templates/map/index.php' );
			}
		}

		function mcd_search_form($shortcode_atts) {
			$this->mcd_settings['search_shortcode_atts'] = $shortcode_atts;
			return $this->return_include_output( MCD_PLUGIN_PATH . 'templates/search/form.php' );
		}

		function mcd_slider($shortcode_atts) {
			$atts = shortcode_atts(
				array(
					'type' => 'event',
					'show-dots' => 'false',
					'count' => 8,
					'show' => 4,
					'auto-slide' => 'true',
					'auto-slide-speed' => 4000,
					'metadata' => 'true',
					'items-on-mobile' => 1,
					'holiday' => '',
					'kids' => '',
					'cat' => '',
					'curbside_pickup' => '',
					'tags' => '',
				),
			$shortcode_atts );

			$this->mcd_settings['slider_shortcode_atts'] = $atts;

			$api_call_url = '';
			if( $atts['type'] == 'events' ) {
				// $api_call_url = MCD_API_EVENTS.'api/events/upcoming?center='.$this->mcd_settings['center_id'].(!empty($atts['holiday'])?'&holiday='.($atts['holiday']=='true'?1:0):'').(!empty($atts['kids'])?'&kids='.($atts['kids']=='true'?1:0):'');
				$api_call_url = MCD_API_EVENTS.'?limit='.$atts['count'].'&page=1';
			} elseif( $atts['type'] == 'deals' ) {
				$api_call_url = MCD_API_DEALS.'?limit='.$atts['count'].'&page=1';
			} elseif( $atts['type'] == 'careers' ) {
				$api_call_url = MCD_API_CAREERS.'?center='.$this->mcd_settings['center_id'].'&limit='.$atts['count'];
			} elseif( $atts['type'] == 'stores' ) {
				// $api_call_url = MCD_API_STORES.'/listing?center='.$this->mcd_settings['center_id'].'&limit='.$atts['count'].(!empty($atts['cat'])?'&category='.$atts['cat']:'').(!empty($atts['curbside_pickup'])?'&curbside_pickup='.($atts['curbside_pickup']=='yes'?1:0):'').(!empty($atts['tags'])?'&tags='.$atts['tags']:'');
				$api_call_url = MCD_API_STORES.'?limit='.$atts['count'].'&page=1';
			}

			$api_response = mcd_api_data($api_call_url);
			$this->mcd_settings['slider_items'] = array();

			if( $atts['type'] == 'events' ) {
				$this->mcd_settings['slider_items'] = $api_response['items'];
			} elseif( $atts['type'] == 'deals' && isset($api_response['items']) ) {
				$this->mcd_settings['slider_items'] = $api_response['items'];
			} elseif( $atts['type'] == 'careers' && isset($api_response['expiry']['careers']) ) {
				$this->mcd_settings['slider_items'] = $api_response['items'];
			} elseif( $atts['type'] == 'stores' && isset($api_response['items']) ) {
				$this->mcd_settings['slider_items'] = $api_response['items'];
			}
			return $this->return_include_output( MCD_PLUGIN_PATH . 'templates/slider/index.php' );
		}

		function mcd_opening_hours_week($shortcode_atts) {
			$atts = shortcode_atts(
				array(
					'group_days' => "no",
					'include_holidays' => true,
					'include_io' => true,
				),
			$shortcode_atts );

			$this->mcd_settings['opening_hours_week_shortcode_atts'] = $atts;

			$api_call_url = MCD_OPENING_HOURS_WEEK.'?center='.$this->mcd_settings['center_id'];
			if( $atts['group_days'] == "yes" ) {
				$api_call_url .= '&group_days=1';
			}
			$api_response = mcd_api_data($api_call_url);
			if( is_array($api_response) && count($api_response) > 0 ) {
				$this->mcd_settings['opening_hours_week'] = $api_response;
				return $this->return_include_output( MCD_PLUGIN_PATH . 'templates/opening-hours/week.php' );
			}
		}

		function mcd_opening_hours_today($shortcode_atts) {
			$atts = shortcode_atts(
				array(
					'open_text' => 'OPEN TODAY',
					'closed_text' => "We're Closed",
					// 'show_today' => true,
				),
			$shortcode_atts );

			$this->mcd_settings['opening_hours_today_shortcode_atts'] = $atts;

			$api_call_url = MCD_OPENING_HOURS_TODAY.'?center='.$this->mcd_settings['center_id'];
			$api_response = mcd_api_data($api_call_url);
			if( is_array($api_response) && count($api_response) > 0 ) {
				$this->mcd_settings['opening_hours_today'] = $api_response;
				return $this->return_include_output( MCD_PLUGIN_PATH . 'templates/opening-hours/today.php' );
			}
		}

		function add_rewrite_vars( $vars ) {
			$vars[] = 'mycenterdeal';
			$vars[] = 'mycenterstore';
			$vars[] = 'mycenterevent';
			$vars[] = 'mycentercareer';
			$vars[] = 'mycenterblogpost';
			$vars[] = 'mycentersearch';
			$vars[] = 'mcdmapretailer';
			return $vars;
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

			add_rewrite_tag( '%mycentersearch%', '([^&]+)' );
			add_rewrite_rule(
				'^'.$saved_options['search_page_slug'].'/([^/]*)/?',
				'index.php?page_id='.$this->mcd_settings['search_page_template'].'&mycentersearch=$matches[1]',
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
			if( isset($_GET['mycentersearch']) ) {
				wp_redirect( home_url( "/".$this->mcd_settings['search_page_slug']."/" ) . urlencode( get_query_var( 'mycentersearch' ) ) ); exit;
			}
			if( isset($_GET['mcdmapretailer']) ) {
				wp_redirect( home_url( "/".$this->mcd_settings['map_page']."/" ) . urlencode( get_query_var( 'mcdmapretailer' ) ) ); exit;
			}

			if ( array_key_exists( 'mycenterdeal', $wp_query->query_vars ) ) {
				$this->template = 'templates/deals/single.php';
				$req_url = MCD_API_DEALS.'/'.get_query_var('mycenterdeal', 0);
        $dealData = mcd_api_data($req_url);
        $dealData['start_date'] = date("M d, Y", strtotime($dealData['start_date']));
        $dealData['end_date'] = date("M d, Y", strtotime($dealData['end_date']));
				$this->mcd_settings['mycenterdeal'] = $dealData;
				if( $this->mcd_settings['deals_single_page_title'] == 'custom' ) {
					$this->page_title = $this->mcd_settings['deals_single_page_custom_title'];
				} else {
					$this->page_title = $this->mcd_settings['mycenterdeal']['title'];
				}
			} elseif ( array_key_exists( 'mycenterstore', $wp_query->query_vars ) ) {
				$this->template = 'templates/stores/single.php';
				$req_url = MCD_API_STORES.'/'.get_query_var('mycenterstore', 0);
				$this->mcd_settings['mycenterstore'] = mcd_api_data($req_url);
				if( $this->mcd_settings['stores_single_page_title'] == 'custom' ) {
					$this->page_title = $this->mcd_settings['stores_single_page_custom_title'];
				} else {
					$this->page_title = $this->mcd_settings['mycenterstore']['name'];
				}

				$req_url = MCD_API_MAP_CONFIG.'?center='.$this->mcd_settings['center_id'];
				$map_config = mcd_api_data($req_url);
				$this->mcd_settings['map_config'] = $map_config;
			} elseif ( array_key_exists( 'mycenterevent', $wp_query->query_vars ) ) {
				$this->template = 'templates/events/single.php';
				mcd_include_js('add-to-calendar', 'assets/plugins/add-to-calendar.min.js', true);
				$req_url = MCD_API_EVENTS.'/'.get_query_var('mycenterevent', 0);
        $eventData = mcd_api_data($req_url);
				$this->mcd_settings['mycenterevent'] = $eventData;
				if( $this->mcd_settings['events_single_page_title'] == 'custom' ) {
					$this->page_title = $this->mcd_settings['events_single_page_custom_title'];
				} else {
					$this->page_title = $this->mcd_settings['mycenterevent']['title'];
				}
			} elseif ( array_key_exists( 'mycentercareer', $wp_query->query_vars ) ) {
				$this->template = 'templates/careers/single.php';
				$req_url = MCD_API_CAREERS.'/'.get_query_var('mycentercareer', 0);
        $careerData = mcd_api_data($req_url);
        $careerData['start_date'] = date("M d, Y", strtotime($careerData['start_date']));
        $careerData['end_date'] = date("M d, Y", strtotime($careerData['end_date']));
				$this->mcd_settings['mycentercareer'] = $careerData;
				if( $this->mcd_settings['careers_single_page_title'] == 'custom' ) {
					$this->page_title = $this->mcd_settings['careers_single_page_custom_title'];
				} else {
					$this->page_title = $this->mcd_settings['mycentercareer']['title'];
				}
			} elseif ( array_key_exists( 'mycenterblogpost', $wp_query->query_vars ) ) {
				$this->template = 'templates/news/single.php';
				$req_url = MCD_API_NEWS.'/'.get_query_var('mycenterblogpost', 0);
				$blogpost = mcd_api_data($req_url);
        $this->mcd_settings['mycenterblogpost'] = $blogpost;
				if( $this->mcd_settings['blog_single_page_title'] == 'custom' ) {
					$this->page_title = $this->mcd_settings['blog_single_page_custom_title'];
				} else {
					$this->page_title = $this->mcd_settings['mycenterblogpost']['title'];
				}

				$req_url = MCD_API_NEWS.'?center='.$this->mcd_settings['center_id'].'&limit=4';
				$mcd_latest_posts = mcd_api_data($req_url);
				$this->mcd_settings['mcd_latest_posts'] = $mcd_latest_posts['posts'];
			} elseif( $this->is_search_page() ) {
				$this->template = 'templates/search/index.php';
				$search_keywords = urldecode(get_query_var('mycentersearch'));
				$this->page_title = 'Search Results for "'.$search_keywords.'"';

				$req_url = MCD_API_SEARCH.'?center='.$this->mcd_settings['center_id'].'&q='.$search_keywords;
				$this->search['mcd_results'] = mcd_api_data($req_url);

				$this->search['titles'] = mcd_search_result_types();
				$this->search['types'] = $this->mcd_settings['search_result_types'];
				foreach ($this->search['types'] as $key => $value) {
					if( !$value ) unset($this->search['types'][$key]);
				}

				$this->search['post_results'] = array();
				foreach ($this->search['types'] as $key => $value) {
					if( substr($key, 0, 3) != 'wp_' ) continue;

					$post_type = substr($key, 3);
					$args = array(
						'post_type' => $post_type,
						's' => $search_keywords,
						'posts_per_page' => -1,
					);
					$this->search['post_results'][$post_type] = query_posts( $args );
					wp_reset_query();
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
			
			if( $this->is_search_page()	|| (is_a($post, 'WP_Post') && $this->is_shortcode_present()) || $this->is_querystring_present() ) {
				if( is_object($post) && $this->is_shortcode_present() ) {

					if( $this->is_shortcode_present('mycentermap2') ) {
						mcd_include_js('angular-functions', 'assets/angular/common/functions.js', true);
					} else {
						mcd_include_js('angular', 'assets/angular/angular.min.js');
						mcd_include_js('angular-sanitize', 'assets/angular/angular-sanitize.min.js');
					}

					if( has_shortcode( $post->post_content, 'mycenterdeals') ) {
						mcd_include_js('angular-deals', 'assets/angular/controllers/app-deals.js', true);
            mcd_include_js('moment', 'assets/plugins/calendar/moment.min.js', true);
					}
					if( has_shortcode( $post->post_content, 'mycenterstores') ) {
						mcd_include_js('angular-stores', 'assets/angular/controllers/app-stores.js', true);
					}
					if( has_shortcode( $post->post_content, 'mycenterevents') ) {
						mcd_include_css('fullcalendar', 'assets/plugins/calendar/fullcalendar/core.min.css');
						mcd_include_css('fullcalendar-daygrid', 'assets/plugins/calendar/fullcalendar/daygrid.min.css');
						mcd_include_js('rrule', 'assets/plugins/calendar/rrule.min.js', true);
						mcd_include_js('fullcalendar', 'assets/plugins/calendar/fullcalendar/core.min.js', true);
						mcd_include_js('fullcalendar-daygrid', 'assets/plugins/calendar/fullcalendar/daygrid.min.js', true);
						mcd_include_js('fullcalendar-rrule', 'assets/plugins/calendar/fullcalendar/rrule.min.js', true);
            mcd_include_js('moment', 'assets/plugins/calendar/moment.min.js', true);
						mcd_include_js('angular-events', 'assets/angular/controllers/app-events.js', true);
					}
					if( has_shortcode( $post->post_content, 'mycentercareers') ) {
						mcd_include_js('angular-careers', 'assets/angular/controllers/app-careers.js', true);
            mcd_include_js('moment', 'assets/plugins/calendar/moment.min.js', true);
					}
					if( has_shortcode( $post->post_content, 'mycenterblog') ) {
						mcd_include_js('isotope', 'assets/plugins/isotope.pkgd.min.js', true);
						mcd_include_js('angular-blogposts', 'assets/angular/controllers/app-blog.js', true);
            mcd_include_js('moment', 'assets/plugins/calendar/moment.min.js', true);
					}
					if( has_shortcode( $post->post_content, 'mcd_slider') ) {
						mcd_include_js('owl-carousel', 'assets/plugins/owl/owl.carousel.min.js', true);
						mcd_include_css('owl-carousel', 'assets/plugins/owl/assets/owl.carousel.min.css');
						mcd_include_css('owl-carousel-theme', 'assets/plugins/owl/assets/owl.theme.default.min.css');
					}
					
					mcd_include_js('angular-functions', 'assets/angular/common/functions.js', true);
				}
				
				mcd_include_js('main', 'assets/js/main.js');
				mcd_include_css('style', 'assets/css/style.min.css');
        wp_enqueue_style( 'eyeon-elementor-style' );
			}
		}

		function is_shortcode_present($code1='', $code2='', $code3='', $code4='', $code5='') {
			global $post, $wp_query;
			$shortcodes = array(
				'mycenterdeals',
				'mycenterstores',
				'mycenterevents',
				'mycentercareers',
				'mycenterblog',
				'mycentermap2',
				'mcd_slider',
				'mcd_opening_hours_week',
				'mycentershopping',
				'mcp_shopping_guide',
			);

			if( !empty($code1) ) {
				$shortcodes = array_filter(array($code1, $code2, $code3, $code4, $code5));
			}

			$response = false;
			foreach ($shortcodes as $shortcode) {
				if( has_shortcode(@$post->post_content, $shortcode) ) {
					$response = $shortcode;
					break;
				}
			}
			return $response;
		}

		function is_querystring_present() {
			global $post, $wp_query;
			$query_strings = array(
				'mycenterdeal',
				'mycenterstore',
				'mycenterevent',
				'mycentercareer',
				'mycenterblogpost',
				'mycentersearch',
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

		function is_search_page() {
			global $post, $wp_query;
			if( is_object($post) && array_key_exists('mycentersearch', $wp_query->query_vars) && $this->mcd_settings['search_page_template'] == $post->ID ) {
				return true;
			}
			return false;
		}

		function dynamic_styles_scripts() {
			global $post, $wp_query;
			$mcd_settings = $this->mcd_settings;

			//include ( MCD_PLUGIN_PATH . 'helpers/analytics.php');
			
			if( is_object($post) && ($this->is_shortcode_present() || $this->is_querystring_present()) ) {
				include ( MCD_PLUGIN_PATH . 'assets/dynamic.php');
			}
		}

	}

	$mcd_shortcodes = new MCDShortcodes();
	$mcd_shortcodes->register();
}

