<?php
/**
 * Branda Cookie Notice class.
 *
 * Class that handle cookie notice module.
 *
 * @since      2.2.0
 *
 * @package Branda
 * @subpackage Front-end
 */
if ( ! class_exists( 'Branda_Cookie_Notice' ) ) {

	/**
	 * Class Branda_Cookie_Notice.
	 */
	class Branda_Cookie_Notice extends Branda_Helper {

		/**
		 * Module option name.
		 *
		 * @var string
		 */
		protected $option_name = 'ub_cookie_notice';

		/**
		 * Cookie name string.
		 *
		 * @var string
		 */
		private $cookie_name = __CLASS__;

		/**
		 * User meta name.
		 *
		 * @var string
		 */
		private $user_meta_name = __CLASS__;

		/**
		 * Branda_Cookie_Notice constructor.
		 */
		public function __construct() {
			parent::__construct();
			$this->module      = 'cookie-notice';
			$this->cookie_name = sprintf( '%s_%d', __CLASS__, $this->get_value( 'configuration', 'cookie_version' ) );
			// UB admin actions.
			add_filter( 'ultimatebranding_settings_cookie_notice', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_cookie_notice_process', array( $this, 'update' ) );
			// Front end actions.
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
			add_action( 'wp_footer', array( $this, 'add_cookie_notice' ), PHP_INT_MAX );
			add_action( 'wp_head', array( $this, 'add_cookie_notice_css' ), PHP_INT_MAX );
			add_action( 'wp_ajax_ub_cookie_notice', array( $this, 'save_user_meta' ) );
			add_action( 'wp_ajax_nopriv_ub_dismiss_visitor_notice', array( $this, 'dismiss_visitor_notice' ) );
			// Upgrade options.
			add_action( 'init', array( $this, 'upgrade_options' ) );
		}

		/**
		 * Upgrade options to new structure.
		 *
		 * @since 3.0.0
		 */
		public function upgrade_options() {
			$value = $this->get_value();
			if ( empty( $value ) ) {
				return;
			}
			if ( ! isset( $value['privacy_policy'] ) ) {
				return;
			}
			$data = array(
				'content'       => array(),
				'design'        => array(),
				'colors'        => array(),
				'configuration' => array(),
			);
			// Configuration.
			if ( isset( $value['configuration'] ) ) {
				$data['configuration']          = $value['configuration'];
				$data['content']['message']     = isset( $value['configuration']['message'] ) ? $value['configuration']['message'] : '';
				$data['content']['button_text'] = isset( $value['configuration']['button_text'] ) ? $value['configuration']['button_text'] : '';
			}
			// Privacy policy.
			if ( isset( $value['privacy_policy'] ) ) {
				$v                                      = $value['privacy_policy'];
				$data['content']['privacy_policy_show'] = isset( $v['show'] ) ? $v['show'] : 'on';
				$data['content']['privacy_policy_text'] = isset( $v['text'] ) ? $v['text'] : __( 'Privacy Policy', 'ub' );
				$data['content']['privacy_policy_link_in_new_tab'] = isset( $v['link_in_new_tab'] ) ? $v['link_in_new_tab'] : 'off';
			}
			// Box.
			if ( isset( $value['box'] ) ) {
				/**
				 * Design: Location
				 */
				$data['design']['location'] = $this->get_value( 'box', 'position', 'bottom' );
				/**
				 * Colors: General: Text
				 */
				$data['colors']['content_color'] = $this->get_value( 'box', 'color', '#fff' );
				/**
				 * Colors: General: Background
				 */
				$color                                = $this->get_value( 'box', 'background_color', '#0085ba' );
				$color                                = $this->convert_hex_to_rbg( $color );
				$color[]                              = intval( $this->get_value( 'box', 'background_transparency', 100 ) / 100 );
				$data['colors']['content_background'] = sprintf( 'rgba( %s )', implode( ', ', $color ) );
			}
			// Button.
			if ( isset( $value['button'] ) ) {
				$v                        = $value['button'];
				$data['design']['radius'] = isset( $v['radius'] ) ? $v['radius'] : 5;
				if ( isset( $v['color'] ) ) {
					$data['colors']['button_label'] = $v['color'];
				}
				if ( isset( $v['background_color'] ) ) {
					$data['colors']['button_background'] = $v['background_color'];
				}
				if ( isset( $v['color_hover'] ) ) {
					$data['colors']['button_label_hover'] = $v['color_hover'];
				}
				if ( isset( $v['background_color_hover'] ) ) {
					$data['colors']['button_background_hover'] = $v['background_color_hover'];
				}
			}
			$this->update_value( $data );
		}

		/**
		 * How it should look?
		 *
		 * @since 2.2.0
		 */
		public function add_cookie_notice_css() {
			$name     = sprintf( '#%s', esc_attr( $this->get_name() ) );
			$template = $this->get_template_name( 'css', 'front-end/modules' );
			$args     = array(
				'id'     => $this->get_name( 'css' ),
				'name'   => $name,
				'colors' => $this->get_value( 'colors' ),
				'design' => $this->get_value( 'design' ),
			);
			$this->render( $template, $args );
		}

		/**
		 * Set options for admin page.
		 *
		 * @since 2.2.0
		 */
		protected function set_options() {
			$options = array(
				'content'       => array(
					'title'       => __( 'Content', 'ub' ),
					'description' => __( 'Customize the content shown within the cookie notice.', 'ub' ),
					'fields'      => array(
						'message'                        => array(
							'type'        => 'wp_editor',
							'label'       => __( 'Message', 'ub' ),
							'description' => __( 'Customize the cookie message that you want to show to your visitors.', 'ub' ),
							'default'     => __( 'We use cookies to ensure that we give you the best experience on our website. If you continue to use this site we will assume that you are happy with it.', 'ub' ),
							'placeholder' => esc_html__( 'You can write cookie notice content here…', 'ub' ),
						),
						'button_text'                    => array(
							'label'       => __( 'Accept button text', 'ub' ),
							'description' => __( 'Choose the text of the accept cookies button.', 'ub' ),
							'default'     => __( 'Accept', 'ub' ),
							'classes'     => array(
								'sui-input-md',
							),
						),
						'privacy_policy_info'            => array(
							'type'         => 'description',
							'value'        => Branda_Helper::sui_notice( sprintf( __( 'The currently selected Privacy Policy page does not exist. Please %1$screate or select a new page%2$s.', 'ub' ), '<a href="' . admin_url( 'options-privacy.php' ) . '">', '</a>' ) ),
							'master'       => $this->get_name( 'privacy-policy' ),
							'master-value' => 'on',
							'display'      => 'sui-tab-content',
						),
						'privacy_policy_text'            => array(
							'label'        => __( 'Text', 'ub' ),
							'default'      => _x( 'Privacy Policy', 'Privacy Policy button text', 'ub' ),
							'master'       => $this->get_name( 'privacy-policy' ),
							'master-value' => 'on',
							'display'      => 'sui-tab-content',
							'classes'      => array(
								'sui-input-md',
							),
						),
						'privacy_policy_link_in_new_tab' => $this->get_options_link_in_new_tab(
							array(
								'master'       => $this->get_name( 'privacy-policy' ),
								'master-value' => 'on',
								'display'      => 'sui-tab-content',
							)
						),
						'privacy_policy_show'            => array(
							'type'        => 'sui-tab',
							'label'       => __( 'Privacy Policy link', 'ub' ),
							'description' => __( 'Choose whether you want to show a privacy policy link and rename it as you like.', 'ub' ),
							'options'     => array(
								'on'  => __( 'Show', 'ub' ),
								'off' => __( 'Hide', 'ub' ),
							),
							'default'     => 'on',
							'slave-class' => $this->get_name( 'privacy-policy' ),
						),
					),
				),
				'design'        => array(
					'title'       => __( 'Design', 'ub' ),
					'description' => __( 'Customize the design of cookie notice bar.', 'ub' ),
					'fields'      => array(
						'location'             => array(
							'type'        => 'sui-tab',
							'label'       => __( 'Location', 'ub' ),
							'description' => __( 'Choose the location on your site where you want to place this cookie notice.', 'ub' ),
							'options'     => array(
								'top'    => __( 'Top', 'ub' ),
								'bottom' => __( 'Bottom', 'ub' ),
							),
							'default'     => 'bottom',
						),
						/*
						'animation' => array(
							'type' => 'sui-tab',
							'label' => __( 'Animation', 'ub' ),
							'options' => array(
								'none' => __( 'None', 'ub' ),
								'fade' => __( 'Fade', 'ub' ),
								'slide' => __( 'Slide-in', 'ub' ),
							),
							'description' => __( 'Choose how do you want to animate the cookie notice.', 'ub' ),
							'default' => 'none',
						),
						*/
						'radius'               => array(
							'type'        => 'number',
							'label'       => __( 'Button corner radius', 'ub' ),
							'description' => __( 'Choose the corner radius of cookie accept button in pixels.', 'ub' ),
							'attributes'  => array( 'placeholder' => '20' ),
							'default'     => 5,
							'min'         => 0,
							'classes'     => array( 'sui-input-sm' ),
						),
						'cookie_button_border' => array(
							'type'       => 'number',
							'label'      => __( 'Cookie button border', 'ub' ),
							'attributes' => array( 'placeholder' => '20' ),
							'default'    => 1,
							'min'        => 0,
							'classes'    => array( 'sui-input-sm' ),
						),
					),
				),
				'colors'        => array(
					'title'       => __( 'Colors', 'ub' ),
					'description' => __( 'Adjust the default colors combination of cookie notice as per your theme.', 'ub' ),
					'show-as'     => 'accordion',
					'fields'      => $this->get_options_fields( 'colors', array( 'general', 'buttons', 'links', 'reset' ) ),
				),
				'configuration' => array(
					'title'       => __( 'Behaviour', 'ub' ),
					'description' => __( 'Customize various options to alter the behaviour of cookie notice as per your need.', 'ub' ),
					'fields'      => array(
						'reloading'      => array(
							'type'        => 'sui-tab',
							'label'       => __( 'Reloading', 'ub' ),
							'description' => __( 'Enable this if you want the page to reload after cookies are accepted.', 'ub' ),
							'options'     => array(
								'off' => __( 'Disable', 'ub' ),
								'on'  => __( 'Enable', 'ub' ),
							),
							'default'     => 'off',
						),
						'logged'         => array(
							'type'        => 'sui-tab',
							'label'       => __( 'Visibility for logged-in users', 'ub' ),
							'description' => __( 'Show cookie notice to the logged-in users.', 'ub' ),
							'options'     => array(
								'off' => __( 'Hide', 'ub' ),
								'on'  => __( 'Show', 'ub' ),
							),
							'default'     => 'off',
						),
						'cookie_expiry'  => array(
							'type'        => 'select',
							'label'       => __( 'Cookie expiry time', 'ub' ),
							'options'     => array(
								// HOUR_IN_SECONDS => __( '1 hour', 'ub' ),
								DAY_IN_SECONDS       => __( '1 day', 'ub' ),
								WEEK_IN_SECONDS      => __( '1 week', 'ub' ),
								MONTH_IN_SECONDS     => __( '1 month', 'ub' ),
								3 * MONTH_IN_SECONDS => __( '3 months', 'ub' ),
								6 * MONTH_IN_SECONDS => __( '6 months', 'ub' ),
								YEAR_IN_SECONDS      => __( '1 year', 'ub' ),
							),
							'default'     => MONTH_IN_SECONDS,
							'description' => __( 'Choose the amount of time for which cookie should be stored.', 'ub' ),
							'classes'     => array(
								'sui-input-md',
							),
						),
						'cookie_version' => array(
							'type'        => 'number',
							'label'       => __( 'Cookie Version', 'ub' ),
							'min'         => 1,
							'description' => __( 'Choose a version number for the cookie. Update this to invalidate the previous cookie and force all users to view the notification again.', 'ub' ),
							'default'     => 1,
							'classes'     => array( 'sui-input-md' ),
						),
					),
				),
			);
			// Unset Privacy Policy Page if WP function does not exist.
			if ( function_exists( 'get_privacy_policy_url' ) ) {
				$check = $this->check_privacy_policy_page();
				if ( $check ) {
					unset( $options['content']['fields']['privacy_policy_info'] );
				}
			} else {
				unset( $options['privacy_policy'] );
				unset( $options['content']['fields']['privacy_policy_info'] );
			}
			$this->options = $options;
		}

		/**
		 * Load scripts and styles - frontend.
		 *
		 * @since 2.2.0
		 */
		public function wp_enqueue_scripts() {
			if ( ! $this->show_cookie_notice() ) {
				return;
			}
			
			$slug = $this->get_name( 'front' );
			// Javascript.
			$file = branda_files_url( 'modules/front-end/assets/js/cookie-notice-front.js' );
			wp_enqueue_script( $slug, $file, array( 'jquery' ), $this->build, true );
			$value = intval( $this->get_value( 'configuration', 'cookie_expiry' ) );
			$data  = array(
				'id'        => sprintf( '#%s', $this->get_name() ),
				'cookie'    => array(
					'domain'   => defined( 'COOKIE_DOMAIN' ) && COOKIE_DOMAIN ? COOKIE_DOMAIN : '',
					'name'     => $this->cookie_name,
					'path'     => defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/',
					'secure'   => is_ssl() ? 'on' : 'off',
					'timezone' => HOUR_IN_SECONDS * get_option( 'gmt_offset' ),
					'value'    => $value,
				),
				'reloading' => $this->get_value( 'configuration', 'reloading' ),
				'animation' => $this->get_value( 'design', 'animation' ),
				'ajaxurl'   => admin_url( 'admin-ajax.php' ),
				'logged'    => is_user_logged_in() ? 'yes' : 'no',
				'user_id'   => get_current_user_id(),
				'nonce'     => wp_create_nonce( __CLASS__ ),
			);
			wp_localize_script( $slug, 'ub_cookie_notice', $data );
			// CSS.
			$file = branda_files_url( 'modules/front-end/assets/css/cookie-notice.css' );
			wp_enqueue_style( $slug, $file, array(), $this->build );
		}

		/**
		 * Cookie notice output.
		 *
		 * @since 2.2.0
		 */
		public function add_cookie_notice() {
			$show = $this->show_cookie_notice();
			if ( ! $show ) {
				return;
			}
			$classes  = array(
				'ub-position-' . $this->get_value( 'design', 'location', 'bottom' ),
				'ub-style-' . $this->get_value( 'design', 'style', 'none' ),
			);
			$content  = sprintf(
				'<div id="%s" role="banner" class="%s">',
				esc_attr( $this->get_name() ),
				implode( ' ', $classes )
			);
			$content .= '<div class="cookie-notice-container">';
			$content .= '<div class="branda-cn-container">';
			$content .= sprintf(
				'<span id="ub-cn-notice-text" class="branda-cn-column">%s</span>',
				$this->get_value( 'content', 'message' )
			);
			// Data.
			$content .= sprintf(
				'<span class="branda-cn-column"><a href="#" class="button ub-cn-set-cookie">%s</a></span>',
				esc_html( $this->get_value( 'content', 'button_text' ) )
			);
			// Privacy Policy.
			if ( function_exists( 'get_privacy_policy_url' ) ) {
				$show = $this->get_value( 'content', 'privacy_policy_show' );
				if ( 'on' === $show ) {
					$link_in_new_tab = $this->get_value( 'content', 'privacy_policy_link_in_new_tab', 'off' );
					$target          = ( 'on' === $link_in_new_tab ) ? ' target="_blank"' : '';
					$link            = get_privacy_policy_url();
					if ( ! empty( $link ) ) {
						$content .= sprintf(
							'<span class="branda-cn-column"><a href="%s" class="ub-cn-privacy-policy"%s>%s</a></span>',
							$link,
							$target,
							$this->get_value( 'content', 'privacy_policy_text' )
						);
					}
				}
			}
			$content .= '</div>';
			$content .= '</div>';
			$content .= '</div>';
			echo apply_filters( 'ultimate_branding_cookie_notice_output', $content, $this->get_value() );
		}

		/**
		 * Get current time.
		 *
		 * @return int|string
		 */
		private function get_now() {
			return current_time( 'timestamp' ) - HOUR_IN_SECONDS * get_option( 'gmt_offset' );
		}

		/**
		 * Show cookie notice?
		 *
		 * @since 2.2.0
		 */
		private function show_cookie_notice() {
			static $show_notice = null;

			if ( is_bool( $show_notice ) ) {
				return $show_notice;
			}

			$show_notice = true;
			$time        = filter_input( INPUT_COOKIE, $this->cookie_name, FILTER_SANITIZE_NUMBER_INT );
			if ( ! empty( $time ) ) {
				$now = $this->get_now();
				if ( $time > $now ) {
					$show_notice = false;
				}
			}
			// Check settings for logged user.
			if ( is_user_logged_in() ) {
				$show = $this->get_value( 'configuration', 'logged' );
				if ( 'off' === $show ) {
					$show_notice = false;
				}
				$user_time = 0;
				$time      = get_user_meta( get_current_user_id(), $this->user_meta_name, true );
				$key       = $this->get_meta_key_name();
				if ( isset( $time[ $key ] ) ) {
					$user_time = intval( $time[ $key ] );
				}
				if ( 0 < $user_time ) {
					$now = $this->get_now();
					if ( $user_time > $now ) {
						$show_notice = false;
					}
				}
			}
			return $show_notice;
		}

		/**
		 * Get user meta key name.
		 *
		 * @param null|int $blog_id Blog ID.
		 *
		 * @return string
		 */
		private function get_meta_key_name( $blog_id = null ) {
			if ( empty( $blog_id ) ) {
				$blog_id = get_current_blog_id();
			}
			$key = sprintf(
				'blog_%d_version_%d',
				$blog_id,
				$this->get_value( 'configuration', 'cookie_version' )
			);
			return $key;
		}

		/**
		 * Save user meta info about cookie.
		 *
		 * @since 2.2.0
		 */
		public function save_user_meta() {
			if ( ! isset( $_POST['nonce'] ) ) {
				wp_send_json_error( 'missing nonce' );
			}
			if ( ! wp_verify_nonce( $_POST['nonce'], __CLASS__ ) ) {
				wp_send_json_error( 'wrong nonce' );
			}
			if ( ! isset( $_POST['user_id'] ) ) {
				wp_send_json_error( 'missing user ID' );
			}
			$value   = $this->get_value( 'configuration', 'cookie_expiry' );
			$value   = current_time( 'timestamp' ) + intval( $value );
			$user_id = filter_input( INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT );
			if ( 0 < $user_id ) {
				$time = get_user_meta( $user_id, $this->user_meta_name, true );
				if ( ! is_array( $time ) ) {
					$time = array();
				}
				$key          = $this->get_meta_key_name();
				$time[ $key ] = $value;
				update_user_meta( $_POST['user_id'], $this->user_meta_name, $time );
				// Clear caches.
				$this->clear_cache();
			}
			wp_send_json_success();
		}

		/**
		 * Common Options: Colors -> General.
		 *
		 * @param array $defaults Default options.
		 *
		 * @since 3.0.0
		 *
		 * @return array
		 */
		protected function get_options_fields_colors_general( $defaults = array() ) {
			$data = array(
				'content_color'      => array(
					'type'      => 'color',
					'label'     => __( 'Text', 'ub' ),
					'accordion' => array(
						'begin'   => true,
						'title'   => __( 'General', 'ub' ),
						'classes' => array(
							'body' => array(
								$this->get_name( 'color-general' ),
							),
						),
					),
					'default'   => '#fff',
				),
				'content_background' => array(
					'type'      => 'color',
					'label'     => __( 'Background', 'ub' ),
					'accordion' => array(
						'end' => true,
					),
					'data'      => array(
						'alpha' => true,
					),
					'default'   => 'rgba( 0, 133, 186, 1 )',
				),
			);
			/**
			 * Allow to change fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Options data.
			 * @param array $defaults Default values from function.
			 * @param       string    Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Common Options: Colors -> Buttons.
		 *
		 * @param array $defaults Default options.
		 *
		 * @since 3.0.0
		 *
		 * @return array
		 */
		protected function get_options_fields_colors_buttons( $defaults = array() ) {
			$data = array(
				'button_label'             => array(
					'type'      => 'color',
					'label'     => __( 'Text', 'ub' ),
					'default'   => '#ffffff',
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Accept Cookies Button', 'ub' ),
					),
					'panes'     => array(
						'begin'      => true,
						'title'      => __( 'Static', 'ub' ),
						'begin_pane' => true,
					),
				),
				'button_border'            => array(
					'type'    => 'color',
					'label'   => __( 'Border', 'ub' ),
					'default' => '#006799',
				),
				'button_background'        => array(
					'type'    => 'color',
					'label'   => __( 'Background', 'ub' ),
					'default' => '#0085ba',
					'panes'   => array(
						'end_pane' => true,
					),
				),
				// Hover.
				'button_label_hover'       => array(
					'type'    => 'color',
					'label'   => __( 'Text', 'ub' ),
					'default' => '#ffffff',
					'panes'   => array(
						'title'      => __( 'Hover', 'ub' ),
						'begin_pane' => true,
					),
				),
				'button_border_hover'      => array(
					'type'    => 'color',
					'label'   => __( 'Border', 'ub' ),
					'default' => '#006799',
				),
				'button_background_hover'  => array(
					'type'    => 'color',
					'label'   => __( 'Background', 'ub' ),
					'default' => '#008ec2',
					'panes'   => array(
						'end_pane' => true,
					),
				),
				// Active.
				'button_label_active'      => array(
					'type'    => 'color',
					'label'   => __( 'Text', 'ub' ),
					'default' => '#ffffff',
					'panes'   => array(
						'title'      => __( 'Active', 'ub' ),
						'begin_pane' => true,
					),
				),
				'button_border_active'     => array(
					'type'    => 'color',
					'label'   => __( 'Border', 'ub' ),
					'default' => '#006799',
				),
				'button_background_active' => array(
					'type'    => 'color',
					'label'   => __( 'Background', 'ub' ),
					'default' => '#0073aa',
					'panes'   => array(
						'end_pane' => true,
					),
				),
				// Focus.
				'button_label_focus'       => array(
					'type'    => 'color',
					'label'   => __( 'Text', 'ub' ),
					'default' => '#ffffff',
					'panes'   => array(
						'title'      => __( 'Focus', 'ub' ),
						'begin_pane' => true,
					),
				),
				'button_border_focus'      => array(
					'type'    => 'color',
					'label'   => __( 'Border', 'ub' ),
					'default' => '#5b9dd9',
				),
				'button_background_focus'  => array(
					'type'      => 'color',
					'label'     => __( 'Background', 'ub' ),
					'default'   => '#008ec2',
					'panes'     => array(
						'end_pane' => true,
						'end'      => true,
					),
					'accordion' => array(
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
			 * @param       string    Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Common Options: Colors -> links.
		 *
		 * @param array $defaults Default options.
		 *
		 * @since 3.1.3
		 *
		 * @return array
		 */
		protected function get_options_fields_colors_links( $defaults = array() ) {
			$data = array(
				'link_color'         => array(
					'type'      => 'color',
					'label'     => __( 'Text', 'ub' ),
					'default'   => '#ffffff',
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Links', 'ub' ),
					),
					'panes'     => array(
						'begin'      => true,
						'title'      => __( 'Static', 'ub' ),
						'begin_pane' => true,
						'end_pane'   => true,
					),
				),
				/**
				 * :visited
				 */
				'link_color_visited' => array(
					'type'    => 'color',
					'label'   => __( 'Text', 'ub' ),
					'default' => '#ffffff',
					'panes'   => array(
						'title'      => __( 'Visited', 'ub' ),
						'begin_pane' => true,
						'end_pane'   => true,
					),
				),
				/**
				 * :hover
				 */
				'link_color_hover'   => array(
					'type'    => 'color',
					'label'   => __( 'Text', 'ub' ),
					'default' => '#ffffff',
					'panes'   => array(
						'title'      => __( 'Hover', 'ub' ),
						'begin_pane' => true,
						'end_pane'   => true,
					),
				),
				/**
				 * :active
				 */
				'link_color_active'  => array(
					'type'    => 'color',
					'label'   => __( 'Text', 'ub' ),
					'default' => '#ffffff',
					'panes'   => array(
						'title'      => __( 'Active', 'ub' ),
						'begin_pane' => true,
						'end_pane'   => true,
					),
				),
				/**
				 * :focus
				 */
				'link_color_focus'   => array(
					'type'      => 'color',
					'label'     => __( 'Text', 'ub' ),
					'default'   => '#ffffff',
					'panes'     => array(
						'title'      => __( 'focus', 'ub' ),
						'begin_pane' => true,
						'end_pane'   => true,
						'end'        => true,
					),
					'accordion' => array(
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
			 * @param       string    Current module name.
			 */
			return apply_filters( 'branda_' . __FUNCTION__, $data, $defaults, $this->module );
		}

		/**
		 * Dismiss the cookie notice for visitor.
		 *
		 * To dismiss cookie notice, we need to clear caches
		 * if HB is active.
		 *
		 * @since 3.0
		 */
		public function dismiss_visitor_notice() {
			// Verify nonce first.
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], __CLASS__ ) ) {
				wp_send_json_error( 'invalid nonce' );
			}
			// Clear caches.
			$this->clear_cache();
			// Send a success notice.
			wp_send_json_success();
		}

		/**
		 * Clear cache to hide cookie notice.
		 *
		 * We should clear the page cache when cookie notice is
		 * dismissed by a visitor. Otherwise it will keep on showing
		 * the notice even after dismissal.
		 *
		 * @since 3.0
		 */
		private function clear_cache() {
			// Clear HB cache.
			do_action( 'wphb_clear_page_cache' );
		}

		/**
		 * Check Privacy policy page
		 *
		 * @since 3.1.2
		 */
		private function check_privacy_policy_page() {
			$policy_page_id = (int) get_option( 'wp_page_for_privacy_policy' );
			if ( empty( $policy_page_id ) || 'publish' !== get_post_status( $policy_page_id ) ) {
				return false;
			}
			return true;
		}
	}
}
new Branda_Cookie_Notice();
