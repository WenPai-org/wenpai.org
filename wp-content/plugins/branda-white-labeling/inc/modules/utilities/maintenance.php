<?php
/**
 * Branda Website Mode class.
 *
 * @package Branda
 * @subpackage Utilites
 */
if ( ! class_exists( 'Brenda_Maintenance' ) ) {

	class Brenda_Maintenance extends Branda_Helper {
		protected $option_name = 'ub_maintenance';
		private $current_sites = array();
		protected $file        = __FILE__;

		public function __construct() {
			parent::__construct();
			$this->module = 'maintenance';
			add_filter( 'ultimatebranding_settings_maintenance', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_maintenance_process', array( $this, 'update' ), 10, 1 );
			/**
			 * AJAX
			 */
			add_action( 'wp_ajax_branda_maintenance_search_sites', array( $this, 'ajax_search_subsite' ) );
			add_action( 'wp_ajax_branda_maintenance_delete_subsite', array( $this, 'ajax_delete_subsite' ) );
			/**
			 * use, depend on status
			 */
			if ( ! is_admin() ) {
				$status = $this->get_value( 'mode', 'mode' );
				if ( 'off' !== $status ) {
					add_action( 'template_redirect', array( $this, 'output' ), 0 );
					add_filter( 'rest_authentication_errors', array( $this, 'only_allow_logged_in_rest_access' ) );
				}
			}
			/**
			 * add related config
			 *
			 * @since 2.3.0
			 */
			add_filter( 'ultimate_branding_related_modules', array( $this, 'add_related_background' ) );
			add_filter( 'ultimate_branding_related_modules', array( $this, 'add_related_logo' ) );
			add_filter( 'ultimate_branding_related_modules', array( $this, 'add_related_social_media_settings' ) );
			add_filter( 'ultimate_branding_related_modules', array( $this, 'add_related_social_media' ) );

			add_filter( 'ub_get_option-' . $this->option_name, array( $this, 'subsite_override' ), 10, 3 );

			/**
			 * upgrade options
			 *
			 * @since 3.0.0
			 */
			add_action( 'init', array( $this, 'upgrade_options' ) );
			/**
			 * Add template
			 *
			 * @since 3.0,0
			 */
			add_filter( 'branda_get_module_content', array( $this, 'add_template' ), 10, 2 );
			/**
			 * Remove some color options
			 *
			 * @since 3.1.0
			 */
			add_filter( 'branda_get_options_fields_colors_error_messages', array( $this, 'remove_colors' ), 10, 3 );
		}

		/**
		 * Upgrade option
		 *
		 * @since 3.0.0
		 */
		public function upgrade_options() {
			$value = $this->get_value();
			/**
			 * Check we have plugin_version in saved data
			 */
			if ( isset( $value['plugin_version'] ) ) {
				/**
				 * do not run again big upgrade if config was saved by Branda
				 */
				$version_compare = version_compare( $value['plugin_version'], '3.0.0' );
				if ( -1 < $version_compare ) {
					return;
				}
				return;
			}
			/**
			 * redefine
			 */
			$data = array(
				'mode'    => array(),
				'content' => array(),
				'design'  => array(),
				'colors'  => array(),
			);
			/**
			 * Common migration
			 */
			$data = $this->common_upgrade_options( $data, $value );
			/**
			 * Fix defaults
			 */
			if (
				isset( $value['background'] )
				&& isset( $value['background']['color'] )
			) {
				$data['colors']['document_background']       = $value['background']['color'];
				$data['colors']['error_messages_background'] = $value['background']['color'];
			}
			/**
			 * mode
			 */
			if ( isset( $value['mode'] ) ) {
				$v = $value['mode'];
				if ( isset( $v['mode'] ) ) {
					$data['mode']['mode'] = $v['mode'];
				}
				if ( isset( $v['sites'] ) ) {
					$data['mode']['subsites'] = 'selected' === $v['sites'] ? 'custom' : 'all';
				}
			}
			/**
			 * document
			 */
			if ( isset( $value['document'] ) ) {
				$v = $value['document'];
				if ( isset( $v['title'] ) ) {
					$data['content']['title'] = $v['title'];
				}
				if ( isset( $v['content'] ) ) {
					$data['content']['content'] = $v['content'];
				}
				if ( isset( $v['content_meta'] ) ) {
					$data['content']['content_meta'] = $v['content_meta'];
				}
				/**
				 * Fix defaults
				 */
				if ( isset( $v['background'] ) ) {
					$data['colors']['content_background'] = $v['background'];
				}
			}
			/**
			 * timer
			 */
			if ( isset( $value['timer'] ) ) {
				$v = $value['timer'];
				if ( isset( $v['use'] ) ) {
					$data['content']['countdown_show'] = $v['use'];
				}
				if ( isset( $v['till_date'] ) ) {
					$data['content']['countdown_till_date'] = $v['till_date'];
				}
				if ( isset( $v['till_time'] ) ) {
					$data['content']['countdown_till_time'] = $v['till_time'];
				}
				if ( isset( $v['template'] ) ) {
					$data['design']['countdown_template'] = $v['template'];
				}
			}
			/**
			 * sites
			 */
			if (
				isset( $value['sites'] )
				&& isset( $value['sites']['list'] )
			) {
				$data['subsites']['id'] = $value['sites']['list'];
			}
			$this->update_value( $data );
		}

		/**
		 * Get network value only if the current subsite is set in `Apply on` option
		 *
		 * @param array  $value
		 * @param string $option
		 * @param mixed  $default
		 * @return mixed
		 */
		public function subsite_override( $value, $option, $default ) {
			if ( $this->is_network && ! is_network_admin() && ! wp_doing_ajax() && isset( $value['subsites']['id'] ) ) {
				$current_site = get_current_blog_id();
				if ( empty( $value['subsites']['id'] ) || ! in_array( (string) $current_site, $value['subsites']['id'], true ) ) {
					unset( $value['mode'] );
				}
			}

			return $value;
		}


		/**
		 * modify option name
		 *
		 * @since 1.9.2
		 */
		public function get_module_option_name( $option_name, $module ) {
			if ( is_string( $module ) && $this->module == $module ) {
				return $this->option_name;
			}
			return $option_name;
		}

		protected function set_options() {
			$description = __( 'Choose the website mode for your website.', 'ub' );
			if ( $this->is_network ) {
				$description = __( 'Choose the website mode as per your needs and then choose websites on your network to apply this mode.', 'ub' );
			}
			$button_add_args = array(
				'text'    => __( 'Add', 'ub' ),
				'classes' => array(
					$this->get_name( 'subsite-add' ),
				),
			);
			$options         = array(
				'mode'    => array(
					'title'       => __( 'Configuration', 'ub' ),
					'description' => $description,
					'fields'      => array(
						'mode'     => array(
							'type'        => 'sui-tab',
							'label'       => __( 'Mode', 'ub' ),
							'options'     => array(
								'off'         => __( 'Off', 'ub' ),
								'coming-soon' => __( 'Coming Soon', 'ub' ),
								'maintenance' => __( 'Maintenance', 'ub' ),
							),
							'default'     => 'off',
							'description' => __( 'Choose a mode for your website.', 'ub' ),
						),
						'search'   => array(
							'id'           => $this->get_name( 'search' ),
							'type'         => 'select2-ajax',
							'small-select' => true,
							'master'       => $this->get_name( 'subsites' ),
							'master-value' => 'custom',
							'display'      => 'sui-tab-content',
							'before'       => '<div class="sui-row"><div class="sui-col-sm-9">',
							'after'        => '</div><div class="sui-col-sm-3">' . $this->button( $button_add_args ) . '</div></div>',
							'placeholder'  => esc_attr__( 'Search the subsite', 'ub' ),
							'data'         => array(
								'user-id' => get_current_user_id(),
								'nonce'   => wp_create_nonce( $this->get_nonce_action_name( 'search' ) ),
								'action'  => 'branda_maintenance_search_sites',
								'extra'   => 'branda_maintenance_add_already_used_sites',
							),
							'network-only' => true,
						),
						'list'     => array(
							'type'         => 'callback',
							'callback'     => array( $this, 'get_list' ),
							'master'       => $this->get_name( 'subsites' ),
							'master-value' => 'custom',
							'display'      => 'sui-tab-content',
							'network-only' => true,
						),
						'subsites' => array(
							'type'         => 'sui-tab',
							'label'        => __( 'Apply on', 'ub' ),
							'description'  => __( 'Choose the sites on your network to apply this mode.', 'ub' ),
							'options'      => array(
								'all'    => __( 'All sites', 'ub' ),
								'custom' => __( 'Selected sites', 'ub' ),
							),
							'default'      => 'all',
							'slave-class'  => $this->get_name( 'subsites' ),
							'network-only' => true,
						),
					),
				),
				/**
				 * Common: Document
				 */
				'content' => array(
					'title'       => __( 'Content', 'ub' ),
					'description' => __( 'Adjust the default content of your info page.', 'ub' ),
					'show-as'     => 'accordion',
					'fields'      => $this->get_options_fields( 'content', array( 'logo', 'error_messages', 'countdown', 'social', 'reset' ) ),
				),
				'design'  => array(
					'title'       => __( 'Design', 'ub' ),
					'description' => __( 'Adjust the default design of the info page.', 'ub' ),
					'show-as'     => 'accordion',
					'fields'      => $this->get_options_fields( 'design', array( 'logo', 'background', 'countdown', 'social', 'document', 'reset' ) ),
				),
				'colors'  => array(
					'title'       => __( 'Colors', 'ub' ),
					'description' => __( 'Adjust the default colour combinations as per your liking.', 'ub' ),
					'show-as'     => 'accordion',
					'fields'      => $this->get_options_fields( 'colors', array( 'logo', 'error_messages', 'document', 'reset' ) ),
				),
				/**
				 * Custom CSS
				 *
				 * @since 3.0.0
				 */
				'css'     => $this->get_custom_css_array(
					array(
						'ace_selectors' => $this->get_ace_selectors(),
					)
				),
			);
			/**
			 * Change accordion name
			 *
			 * @since 3.1.3
			 */
			$options['colors']['fields']['error_messages_background']['accordion']['title'] = __( 'Display Message', 'ub' );
			$this->options = $options;
		}

		/**
		 * get current sites html
		 */
		public function get_list() {
			$content = '';
			$items   = $this->get_current_sites();
			if ( is_array( $items ) && ! empty( $items ) ) {
				foreach ( $items as $blog_id ) {
					$details = get_blog_details( $blog_id );
					if ( $details ) {
						$item     = array(
							'blog_id'    => $blog_id,
							'blog_url'   => $details->siteurl,
							'blog_title' => $details->blogname,
						);
						$content .= $this->get_list_one_row( $item, true );
						$content .= $this->get_dialog_delete( $item['blog_id'] );
					}
				}
			}
			return $content;
		}
		/**
		 * get and set current sites
		 */
		private function get_current_sites() {
			if ( empty( $this->current_sites ) ) {
				$sites = $this->get_value( 'subsites', 'id' );
				if ( ! empty( $sites ) ) {
					$this->current_sites = array_filter( $sites );
				}
			}
			return $this->current_sites;
		}

		/**
		 * Check if website mode is enabled for the current site/subsite
		 *
		 * @return boolean
		 */
		private function is_website_mode() {
			/**
			 * do not render for logged users
			 */
			$logged = is_user_logged_in();
			if ( $logged ) {
				return false;
			}
			/**
			 * check status
			 */
			$status = $this->get_value( 'mode', 'mode' );
			if ( 'off' === $status ) {
				return false;
			}
			/**
			 * check sites options
			 */
			$sites = $this->get_value( 'mode', 'subsites' );
			if ( 'custom' === $sites ) {
				$sites = $this->get_current_sites();
				if ( empty( $sites ) ) {
					return false;
				}
				$blog_id = get_current_blog_id();
				if ( ! in_array( $blog_id, $sites ) ) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Display the coming soon page
		 */
		public function output() {
			/**
			 * Compability with Domain Mapping
			 * Cross-domain autologin (asynchronously)
			 *
			 * @since 3.1.1
			 */
			if ( class_exists( 'Domainmap_Module_Cdsso' ) ) {
				global $wp_query;
				if ( isset( $wp_query->query_vars[ Domainmap_Module_Cdsso::SSO_ENDPOINT ] ) ) {
					return;
				}
			}

			if ( ! $this->is_website_mode() ) {
				return;
			}

			/**
			 * set data
			 */
			$head = '';
			if ( function_exists( 'wp_site_icon' ) ) {
				ob_start();
				wp_site_icon();
				$head = ob_get_contents();
				ob_end_clean();
			}
			$distance     = $use_timer = false;
			$body_classes = array(
				'ultimate-branding-maintenance',
			);
			/**
			 * javascript, check time;
			 */
			$v = $this->get_value( 'content' );
			if (
				isset( $v['countdown_show'] )
				&& 'on' == $v['countdown_show']
				&& isset( $v['countdown_till_date'] )
				&& isset( $v['countdown_till_date']['alt'] )
			) {
				$distance = $this->get_distance();
				if ( 1 > $distance ) {
					return;
				}
				$use_timer      = true;
				$body_classes[] = 'has-counter';
			}
			/**
			 *  set headers
			 */
			$status = $this->get_value( 'mode', 'mode' );
			if ( 'maintenance' === $status ) {
				header( 'HTTP/1.1 503 Service Temporarily Unavailable' );
				header( 'Status: 503 Service Temporarily Unavailable' );
				header( 'Retry-After: 86400' ); // retry in a day
				$maintenance_file = WP_CONTENT_DIR . '/maintenance.php';
				if ( ! empty( $enable_maintenance_php ) and file_exists( $maintenance_file ) ) {
					include_once $maintenance_file;
					exit();
				}
			}
			$this->set_data();
			// Prevent Plugins from caching
			// Disable caching plugins. This should take care of:
			// - W3 Total Cache
			// - WP Super Cache
			// - ZenCache (Previously QuickCache)
			if ( ! defined( 'DONOTCACHEPAGE' ) ) {
				define( 'DONOTCACHEPAGE', true );
			}
			if ( ! defined( 'DONOTCDN' ) ) {
				define( 'DONOTCDN', true );
			}
			if ( ! defined( 'DONOTCACHEDB' ) ) {
				define( 'DONOTCACHEDB', true );
			}
			if ( ! defined( 'DONOTMINIFY' ) ) {
				define( 'DONOTMINIFY', true );
			}
			if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
				define( 'DONOTCACHEOBJECT', true );
			}
			header( 'Cache-Control: max-age=0; private' );
			$template = $this->get_template_name( 'output', 'front-end/modules' );
			$css      = '';
			$args     = array(
				'mode'                 => $this->get_value( 'mode', 'mode' ),
				'logo'                 => '',
				'countdown'            => '',
				'content_content_meta' => '',
				'social_media'         => '',
				'body_classes'         => array(
					$this->get_name(),
				),
				'after_body_tag'       => $this->html_background_common( false ),
				'language'             => get_bloginfo( 'language' ),
				'title'                => get_bloginfo( 'name' ),
				'head'                 => '',
			);
			/**
			 * Add defaults.
			 */
			if ( empty( $this->data['content']['title'] ) && empty( $this->data['content']['content'] ) ) {
				$this->data['content']['title']   = __( 'We&rsquo;ll be back soon!', 'ub' );
				$this->data['content']['content'] = __( 'Sorry for the inconvenience but we&rsquo;re performing some maintenance at the moment. We&rsquo;ll be back online shortly!', 'ub' );
				if ( 'coming-soon' == $status ) {
					$this->data['content']['title']   = __( 'Coming Soon', 'ub' );
					$this->data['content']['content'] = __( 'Stay tuned!', 'ub' );
				}
				$this->data['content']['meta'] = $this->data['content']['content'];
			}
			/**
			 * Fill $args table
			 */
			foreach ( $this->data as $section => $data ) {
				if ( ! is_array( $data ) ) {
					continue;
				}
				foreach ( $data as $name => $value ) {
					if ( empty( $value ) ) {
						$value = '';
					} elseif ( ! is_string( $value ) ) {
						$value = '';
					}
					if ( ! empty( $value ) ) {
						switch ( $section ) {
							case 'content':
								switch ( $name ) {
									case 'title':
										if ( ! empty( $value ) ) {
											$value = sprintf( '<h1>%s</h1>', esc_html( $value ) );
										}
										break;
									case 'content':
									case 'content_meta':
										if ( ! empty( $value ) ) {
											$value = sprintf( '<div class="content">%s</div>', wp_kses_post( stripcslashes( $value ) ) );
										}
										break;
									default:
										break;
								}
								break;
							default:
								break;
						}
					}
					$re          = sprintf( '%s_%s', $section, $name );
					$args[ $re ] = $value;
				}
			}
			/**
			 * Common: Social Media
			 */
			$result               = $this->common_options_social_media();
			$args['social_media'] = $result['social_media'];
			$args['body_classes'] = array_merge( $args['body_classes'], $result['body_classes'] );
			$css_dependencies     = empty( $result['stylesheet'] ) ? array() : array( $result['stylesheet'] );
			$this->enqueue( 'ub-front-end-maintenance-styling', 'css/front-end/maintenance.css', $this->build, $css_dependencies );
			/**
			 * Common: body
			 */
			$css .= $this->common_css_body( 'body.branda-maintenance' );
			/**
			 * Common: Document
			 */
			$css .= $this->common_css_document();
			/**
			 * Common Background
			 *
			 * @since 2.3.0
			 */
			$background_css = $this->css_background_common( 'html', false, false );
			/**
			 * Logo
			 */
			$logo_css = $this->css_logo_common( '#logo', false );
			if ( ! empty( $logo_css ) ) {
				$css          .= $logo_css;
				$args['logo']  = '<div id="logo">';
				$url           = $this->get_value( 'content', 'logo_url', '#' );
				$alt           = $this->get_value( 'content', 'logo_alt', '' );
				$args['logo'] .= sprintf(
					'<a href="%s" title="%s">%s</a>',
					esc_url( $url ),
					esc_attr( $alt ),
					esc_html( $alt )
				);
				$args['logo'] .= '</div>';
			}
			/**
			 * css error Messages
			 */
			$css  .= 'h1,.content{';
			$css  .= 'margin:0;padding: 10px 20px;';
			$value = $this->get_value( 'colors', 'error_messages_background', false );
			$css  .= $this->css_background_color( $value );
			$value = $this->get_value( 'colors', 'error_messages_text', false );
			$css  .= $this->css_color( $value );
			$value = $this->get_value( 'colors', 'error_messages_border', false );
			if ( $value ) {
				$css .= sprintf( 'border-color:%s;', $value );
			}
			$css  .= '}';
			$css  .= PHP_EOL;
			$css  .= 'h1 a,.content a{';
			$value = $this->get_value( 'colors', 'error_messages_link', false );
			$css  .= $this->css_color( $value );
			$css  .= '}';
			$css  .= PHP_EOL;
			$css  .= 'h1 a:hover,.content a:hover{';
			$value = $this->get_value( 'colors', 'error_messages_link_hover', false );
			$css  .= $this->css_color( $value );
			$css  .= '}';
			$css  .= PHP_EOL;
			$css  .= 'h1 a:active,.content a:active{';
			$value = $this->get_value( 'colors', 'error_messages_link_active', false );
			$css  .= $this->css_color( $value );
			$css  .= '}';
			$css  .= PHP_EOL;
			$css  .= 'h1 a:focus,.content a:focus{';
			$value = $this->get_value( 'colors', 'error_messages_link_focus', false );
			$css  .= $this->css_color( $value );
			$css  .= '}';
			$css  .= PHP_EOL;
			/**
			 * Custom CSS
			 *
			 * @since 3.0.0
			 */
			$value = $this->get_value( 'css', 'css', null );
			if ( ! empty( $value ) ) {
				$css .= $value;
			}
			/**
			 * timer template
			 */
			$timer_template = '';
			if ( $use_timer ) {
				$args['body_classes'][] = 'has-counter';
				$timer                  = $this->get_value( 'design', 'countdown_template' );
				if ( empty( $timer ) ) {
					$timer = 'raw';
				}
				$args['body_classes'][] = $this->get_name( 'timer-' . $timer );
				$timer_template         = $this->get_template_name( 'timer/' . $timer . '/body', 'front-end/modules' );
				$timer_args             = array(
					'id'            => $this->get_name( $timer ),
					'distance'      => $this->get_distance(),
					'distance_raw'  => $this->get_distance( 'raw' ),
					'language_code' => strtolower( substr( get_bloginfo( 'language' ), 0, 2 ) ),
				);
				$args['countdown']      = $this->render( $timer_template, $timer_args, true );
				$timer_template         = $this->get_template_name( 'timer/' . $timer . '/head', 'front-end/modules' );

				$this->enqueue(
					'ub-flipclock-script',
					'vendor/flipclock/flipclock.min.js',
					'2018-04-12',
					array( 'jquery' )
				);
				$this->enqueue(
					'ub-flipclock-styling',
					'vendor/flipclock/flipclock.css',
					'2018-04-12'
				);

				$this->enqueue(
					'ub-jquery-final-countdown-kinetic-script',
					'vendor/jquery-final-countdown/js/kinetic.js',
					'5.1.0',
					array( 'jquery' )
				);
				$this->enqueue(
					'ub-jquery-final-countdown-script',
					'vendor/jquery-final-countdown/js/jquery.final-countdown.min.js',
					$this->build,
					array( 'ub-jquery-final-countdown-kinetic-script', 'jquery' )
				);

				$args['head'] .= $this->render( $timer_template, $timer_args, true );
			}

			wp_add_inline_style( 'ub-front-end-maintenance-styling', $background_css );
			wp_add_inline_style( 'ub-front-end-maintenance-styling', $css );

			/**
			 * Render
			 */
			$this->render( $template, $args );
			exit;
		}

		public function only_allow_logged_in_rest_access( $access ) {
			/**
			 * check website mode
			 */
			if ( ! $this->is_website_mode() ) {
				return $access;
			}
			$current_wp_version = get_bloginfo( 'version' );
			if ( version_compare( $current_wp_version, '4.7', '>=' ) ) {
				return new WP_Error( 'rest_cannot_access', __( 'Only authenticated users can access the REST API.', 'ub' ), array( 'status' => rest_authorization_required_code() ) );
			}
			return $access;
		}

		/**
		 * calculate distance to open site
		 *
		 * @since 1.9.9
		 */
		private function get_distance( $mode = 'difference' ) {
			$v = $this->get_value( 'content' );
			if (
				isset( $v['countdown_show'] )
				&& 'on' == $v['countdown_show']
				&& isset( $v['countdown_till_date'] )
				&& isset( $v['countdown_till_date']['alt'] )
			) {
				$date        = $v['countdown_till_date']['alt'] . ' ' . ( isset( $v['countdown_till_time'] ) ? $v['countdown_till_time'] : '00:00' );
				$timestamp   = strtotime( $date );
				$gmt_offsset = get_option( 'gmt_offset' );
				if ( ! empty( $gmt_offsset ) ) {
					$timestamp -= HOUR_IN_SECONDS * intval( $gmt_offsset );
				}
				if ( 'raw' === $mode ) {
					return $timestamp;
				}
				$distance = $timestamp - time();
				if ( 0 > $distance ) {
					$value                 = $this->get_value();
					$value['mode']['mode'] = 'off';
					$this->update_value( $value );
					return 0;
				}
				return $distance;
			}
			return 0;
		}

		/**
		 * Options: Content -> Error Messages
		 *
		 * @since 3.0.0
		 */
		public function get_options_fields_content_error_messages( $defaults = array() ) {
			$data = array(
				'title'   => array(
					'label'     => __( 'Title (optional)', 'ub' ),
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Display Message', 'ub' ),
					),
				),
				'content' => array(
					'type'        => 'wp_editor',
					'label'       => __( 'Content (optional)', 'ub' ),
					'accordion'   => array(
						'end' => true,
					),
					'placeholder' => esc_html__( 'Enter your content here…', 'ub' ),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data logo options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Content Countdown Timer options fields
		 *
		 * @since 3.0.0
		 */
		public function get_options_fields_content_countdown( $defaults = array() ) {
			$data = array(
				'countdown_till_date' => array(
					'type'         => 'date',
					'label'        => __( 'Till Date', 'ub' ),
					'description'  => array(
						'content'  => __( 'Choose the date until the selected website mode will be applied.', 'ub' ),
						'position' => 'bottom',
					),
					'placeholder'  => esc_attr__( 'Pick a date', 'ub' ),
					'master'       => $this->get_name( 'timer' ),
					'master-value' => 'on',
					'display'      => 'sui-tab-content',
					'group'        => array(
						'begin' => true,
					),
					'accordion'    => array(
						'begin' => true,
						'title' => __( 'Countdown Timer', 'ub' ),
					),
				),
				'countdown_till_time' => array(
					'type'         => 'time',
					'label'        => __( 'Till Time', 'ub' ),
					'description'  => array(
						'content'  => sprintf( __( 'The timezone set in your WordPress settings shows the current time as %s.', 'ub' ), current_time( 'h:ia' ) ),
						'position' => 'bottom',
					),
					'master'       => $this->get_name( 'timer' ),
					'master-value' => 'on',
					'display'      => 'sui-tab-content',
					'default'      => '00:00',
				),
				'countdown_show'      => array(
					'type'        => 'sui-tab',
					'label'       => __( 'Visibility', 'ub' ),
					'options'     => array(
						'off' => __( 'Hide', 'ub' ),
						'on'  => __( 'Show', 'ub' ),
					),
					'default'     => 'off',
					'slave-class' => $this->get_name( 'timer' ),
					'accordion'   => array(
						'end' => true,
					),
					'group'       => array(
						'end' => true,
					),
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Design: Countdown Timer options fields
		 *
		 * @since 3.0.0
		 */
		public function get_options_fields_design_countdown( $defaults = array() ) {
			$data = array(
				'flipclock'          => array(
					'type'         => 'raw',
					'master'       => $this->get_name( 'template' ),
					'master-value' => 'flipclock',
					'display'      => 'sui-tab-content',
					'content'      => '<div class="branda-countdown branda-countdown-flipclock"></div>',
					'accordion'    => array(
						'begin' => true,
						'title' => __( 'Countdown Timer', 'ub' ),
					),
				),
				'final-countdown'    => array(
					'type'         => 'raw',
					'master'       => $this->get_name( 'template' ),
					'master-value' => 'final-countdown',
					'display'      => 'sui-tab-content',
					'content'      => '<div class="branda-countdown branda-countdown-final-countdown"></div>',
				),
				'raw'                => array(
					'type'         => 'raw',
					'master'       => $this->get_name( 'template' ),
					'master-value' => 'raw',
					'display'      => 'sui-tab-content',
					'content'      => 'raw',
					'content'      => '<div class="branda-countdown branda-countdown-raw"></div>',
				),
				'countdown_template' => array(
					'type'        => 'sui-tab',
					'label'       => __( 'Template', 'ub' ),
					'options'     => array(
						'final-countdown' => __( 'Final Countdown', 'ub' ),
						'flipclock'       => __( 'FlipClock', 'ub' ),
						'raw'             => __( 'Raw', 'ub' ),
					),
					'default'     => 'final-countdown',
					'slave-class' => $this->get_name( 'template' ),
					'accordion'   => array(
						'end' => true,
					),
				),
			);

			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param string Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Add WP Template
		 *
		 * @since 3.0.0
		 *
		 * @param string $content Current module content.
		 * @param array  $module Current module.
		 */
		public function add_template( $content, $module ) {
			if ( $this->module !== $module['module'] ) {
				return $content;
			}
			$args     = array(
				'blog_id'    => '{{{data.id}}}',
				'blog_url'   => '{{{data.subtitle}}}',
				'blog_title' => '{{{data.title}}}',
			);
			$content .= sprintf(
				'<script type="text/html" id="tmpl-%s">%s</script>',
				$this->get_name( 'subsite' ),
				$this->get_list_one_row( $args )
			);
			return $content;
		}

		/**
		 * helper to get one row
		 *
		 * @since 3.0.0
		 */
		public function get_list_one_row( $data ) {
			$id   = $this->get_name( 'subsite' );
			$args = array(
				'only-icon' => true,
				'sui'       => array( 'icon', 'red' ),
				'icon'      => 'trash',
			);
			if ( '{{{data.id}}}' !== $data['blog_id'] ) {
				$args['data'] = array(
					'modal-open' => $this->get_nonce_action( $data['blog_id'], 'delete' ),
				);
			} else {
				$args['class'] = $this->get_name( 'delete' );
			}
			$content  = sprintf(
				'<div class="sui-row simple-option simple-option-media" data-blog-id="%s" id="%s-container-%s" >',
				esc_attr( $data['blog_id'] ),
				esc_attr( $id ),
				esc_attr( $data['blog_id'] )
			);
			$content .= sprintf(
				'<input type="hidden" name="simple_options[subsites][id][]" value="%s" />',
				esc_attr( $data['blog_id'] )
			);
			$content .= '<div class="sui-col">';
			$content .= sprintf(
				'<span class="sui-label">%s</span>',
				esc_html( $data['blog_title'] )
			);
			$content .= sprintf(
				'<span class="sui-description">%s</span>',
				esc_attr( $data['blog_url'] )
			);
			$content .= '</div>';
			$content .= '<div class="sui-col">';
			$content .= sprintf(
				'<div class="sui-actons-right">%s</div>',
				$this->button( $args )
			);
			$content .= '</div>';
			$content .= '</div>';
			return $content;
		}

		/**
		 * AJAX delete subsite
		 *
		 * @since 3.0.0
		 */
		public function ajax_delete_subsite() {
			$id           = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT );
			$nonce_action = $this->get_nonce_action( $id, 'delete' );
			$this->check_input_data( $nonce_action, array( 'id' ) );
			$value = $this->get_value( 'subsites', 'id', array() );
			if ( false !== ( $key = array_search( $id, $value ) ) ) {
				unset( $value[ $key ] );
				$this->set_value( 'subsites', 'id', $value );
			}
			wp_send_json_success();
		}

		/**
		 * Get ACE editor buttons
		 *
		 * @since 3.0.0
		 */
		private function get_ace_selectors() {
			$selectors = array(
				'general' => array(
					'selectors' => array(
						'.overall' => __( 'Overall', 'ub' ),
						'.page'    => __( 'Page', 'ub' ),
						'.clock'   => __( 'Clock', 'ub' ),
						'.content' => __( 'Content', 'ub' ),
						'#social'  => __( 'Social Media', 'ub' ),
						'#logo'    => __( 'Logo', 'ub' ),
					),
				),
			);
			return $selectors;
		}

		/**
		 * Remove some color options
		 *
		 * @since 3.1.0
		 */
		public function remove_colors( $data, $defaults, $module ) {
			if ( $module !== $this->module ) {
				return $data;
			}
			if ( isset( $data['error_messages_border'] ) ) {
				unset( $data['error_messages_border'] );
			}
			return $data;
		}
	}
}
new Brenda_Maintenance();
