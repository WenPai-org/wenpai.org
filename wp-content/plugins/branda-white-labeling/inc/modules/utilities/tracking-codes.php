<?php
use function _HumbugBox86e303171e7c\Safe\array_combine;
/**
 * Branda Tracking Codes class.
 *
 * @since 2.3.0
 *
 * @package Branda
 * @subpackage FrontEnd
 */
if ( ! class_exists( 'Branda_Tracking_Codes' ) ) {

	class Branda_Tracking_Codes extends Branda_Helper {

		protected $option_name = 'ub_tracking_codes';

		/**
		 * List of tracking ids.
		 *
		 * @var array
		 */
		protected $tracking_ids = array();

		/**
		 * List of tracking providers.
		 * @var array
		 */
		protected $_tracking_providers = array();

		protected $tracking_providers = array(
			'google_tag'     => 'Google Analytics',
			'facebook_pixel' => 'Facebook Pixel',
			'site_improve'   => 'Site Improve Analytics',
			'cbe'            => 'Capture Behavioral Engagement',
			'monsido'        => 'Monsido',
			'ms_clarity'     => 'MS Clarity',
			'cloudflare'     => 'Cloudflare Analytics',
			'piwik'          => 'PIWIK Pro',
		);

		/**
		 * Allow raw js in tracking code. User needs to have unfiltered_html cap.
		 *
		 * @var boolean
		 */
		protected $allow_raw_js = false;

		protected $messages = array();

		public function __construct() {
			parent::__construct();
			$this->module = 'tracking-codes';

			$this->_tracking_providers = array(
				'google_tag'     => array(
					'title' => esc_html__( 'Google Tag', 'ub' ),
					'description' => esc_html__( 'The Google tag (gtag.js) is a single tag you can add to your website to use a variety of Google products and services.', 'ub' ),
					'logo' => '',
					'link' => 'https://developers.google.com/analytics/devguides/collection/gtagjs',
					'info_link' => 'https://support.google.com/analytics/answer/11994839?sjid=18245109847698086166-EU',
				),
				'facebook_pixel' => array(
					'title' => esc_html__( 'Facebook Pixel', 'ub' ),
					'description' => esc_html__( 'The Meta Pixel is a snippet of JavaScript code that loads a small library of functions you can use to track Facebook ad-driven visitor activity on your website.', 'ub' ),
					'logo' => '',
					'link' => 'https://developers.facebook.com/docs/meta-pixel/get-started',
					'info_link' => 'https://developers.facebook.com/docs/meta-pixel/get-started',
					'inputs'      => array(
						'fb_pixel_id' => array(
							'label'       => esc_html__( 'Pixel ID', 'ub' ),
							'description' => esc_html__( 'Your Facebook Pixel ID.', 'ub' ),
							'placeholder' => esc_html__( 'your-pixel-id-goes-here', 'ub' ),
						),
					),
				),/*
				'site_improve' => array(
					'title' => esc_html__( 'Site Improve Analytics', 'ub' ),
					'description' => esc_html__( 'Site Improve Analytics gives you powerful insights into visitor behavior and website performance with intuitive dashboards and easy-to-use reporting.', 'ub' ),
					'logo' => '',
					'link' => 'https://help.siteimprove.com/support/solutions/articles/80000448448-adding-siteimprove-analytics-javascript-to-your-website',
					'info_link' => '',
				),*//*
				'cbe' => array(
					'title' => esc_html__( 'Capture Behavioral Engagement', 'ub' ),
					'description' => esc_html__( 'Behavioral Data provides your web traffic trends and analysis as well as a detailed profile of all your prospective students.', 'ub' ),
					'logo' => '',
					'link' => 'https://www.capturehighered.com/platform/engage-marketing-automation/behavioral-data/',
					'info_link' => 'https://www.capturehighered.com/',
				),*//*
				'monsido' => array(
					'title' => esc_html__( 'Monsido', 'ub' ),
					'description' => esc_html__( 'One tool to monitor and perfect your website’s accessibility, content quality, branding, SEO, data privacy, Core Web Vitals and more.', 'ub' ),
					'logo' => '',
					'link' => 'https://help.monsido.com/en/articles/5460155-add-the-monsido-script',
					'info_link' => 'https://monsido.com/',
				),*/
				'ms_clarity' => array(
					'title' => esc_html__( 'MS Clarity', 'ub' ),
					'description' => esc_html__( 'Clarity is a free, easy-to-use tool that captures how real people actually use your site.', 'ub' ),
					'logo' => '',
					'link' => 'https://learn.microsoft.com/en-us/clarity/setup-and-installation/clarity-setup',
					'info_link' => 'https://clarity.microsoft.com/',
					'inputs'      => array(
						'ms_clarity_token' => array(
							'label'       => esc_html__( 'Tracking ID', 'ub' ),
							'description' => esc_html__( 'Your MS Clarity tracking ID.', 'ub' ),
							'placeholder' => esc_html__( 'your-ms-clarity-id-goes-here', 'ub' ),
						),
					),
				),
				'cloudflare' => array(
					'title' => esc_html__( 'Cloudflare Analytics', 'ub' ),
					'description' => esc_html__( 'Cloudflare visualizes the metadata collected by our products in the Cloudflare dashboard.', 'ub' ),
					'logo' => '',
					'link' => 'https://developers.cloudflare.com/analytics/web-analytics/getting-started/web-analytics-spa',
					'info_link' => 'https://developers.cloudflare.com/analytics/',
					'inputs'      => array(
						'cf_token' => array(
							'label'       => esc_html__( 'Token', 'ub' ),
							'description' => esc_html__( 'Your CloudFlare Token.', 'ub' ),
							'placeholder' => esc_html__( '123abc456dfg789', 'ub' ),
						),
					),
				),
				'piwik' => array(
					'title' => esc_html__( 'PIWIK Pro', 'ub' ),
					'description' => esc_html__( 'Piwik PRO uses a container to let you manage tags and consents, and run a tracking code (a JavaScript code) on your web pages.', 'ub' ),
					'logo' => '',
					'link' => 'https://help.piwik.pro/support/getting-started/install-a-tracking-code',
					'info_link' => 'https://help.piwik.pro/',
					'inputs'      => array(
						'container_url' => array(
							'label'       => esc_html__( 'Container Url', 'ub' ),
							'description' => esc_html__( 'Your Piwik PRO account address.', 'ub' ),
							'placeholder' => esc_html__( 'https://YOURNAME.piwik.pro/', 'ub' ),
						),
						'site_id' => array(
							'label'       => esc_html__( 'Site ID', 'ub' ),
							'description' => wp_kses_post( __( 'You site ID on Piwik PRO.', 'ub' ) ),
							'placeholder' => esc_html__( '123-456-789', 'ub' ),
						),
					),
				),
			);

			$this->allow_raw_js = apply_filters( 'branda_allow_unfiltered_html_actions', current_user_can( 'unfiltered_html' ), 'tracking_scripts' );
			$this->messages     = array(
				'providers'      => $this->_tracking_providers,
				'default_fields' => array(
					'ga_tracking_id' => array(
						'label'       => esc_attr__( 'Measurement/Tracking ID', 'ub' ),
						'placeholder' => esc_attr__( 'EG: G-XXXXXXXXXX OR UA-XXXXXXXXX-X', 'ub' ),
					),
				),
			);
			/**
			 * handle
			 */
			add_filter( 'ultimatebranding_settings_tracking_codes', array( $this, 'admin_options_page' ) );
			/**
			 * AJAX
			 *
			 * @since 3.0.0
			 */
			add_action( 'wp_ajax_branda_tracking_codes_save', array( $this, 'ajax_save' ) );
			add_action( 'wp_ajax_branda_tracking_codes_delete', array( $this, 'ajax_delete' ) );
			add_action( 'wp_ajax_branda_tracking_codes_bulk_delete', array( $this, 'ajax_bulk_delete' ) );
			/**
			 * @since 3.0.1
			 */
			add_action( 'wp_ajax_branda_admin_panel_tips_reset', array( $this, 'ajax_reset' ) );
			/**
			 * frontend
			 */
			add_action( 'wp_body_open', array( $this, 'target_begin_of_body' ), 0 );
			add_action( 'wp_footer', array( $this, 'target_footer' ), PHP_INT_MAX );
			add_action( 'wp_head', array( $this, 'target_head' ), 10 );
			/**
			 * Add settings button.
			 *
			 * @since 3.0.0
			 */
			add_filter( 'branda_settings_after_box_title', array( $this, 'add_button_after_title' ), 10, 2 );
			/**
			 * Single item delete
			 */
			add_filter( 'branda_dialog_delete_attr', array( $this, 'dialog_delete_attr_filter' ), 10, 3 );
			/**
			 * Upgrade options
			 */
			add_action( 'init', array( $this, 'upgrade_options' ) );

			add_filter( 'ub_escaped_value', array( $this, 'escape_data' ), 10, 5 );

			// Preapare params and scripts for G4 tracking.
			add_action( 'init', array( $this, 'prepare_tracking_scripts' ) );

			// Print tracking head scripts for G4 and Universal Analytics (Google recommends head).
			add_action( 'wp_head', array( $this, 'head_tracking_scripts' ) );
			//add_action( 'wp_footer', array( $this, 'footer_tracking_scripts' ) );
		}

		/**
		 * Upgrade options to new.
		 *
		 * @since 3.0.0
		 */
		public function upgrade_options() {
			$value = $this->get_value();
			if ( empty( $value ) ) {
				return;
			}
			if ( isset( $value['plugin_version'] ) ) {
				return;
			}
			/**
			 * Convert old
			 */
			$data = array();
			foreach ( $value as $key => $one ) {
				$new = array();
				/**
				 * checl multisite settings
				 */
				if ( isset( $one['sites_active'] ) && 'on' === $one['sites_active'] ) {
					unset( $one['sites_active'] );
					$one['filters_active'] = 'on';
				}
				foreach ( $one as $subkey => $value ) {
					/**
					 * ignore subkey
					 */
					if ( 'tracking_ub_tc_action' === $subkey ) {
						continue;
					}
					/**
					 * raname subkey
					 */
					if ( 'filters_active' === $subkey ) {
						$subkey = 'filters_filter';
					}
					$subkey         = preg_replace( '/^(filters|tracking|sites)_/', '', $subkey );
					$new[ $subkey ] = $value;
				}
				/**
				 * sanitize place
				 */
				if ( ! isset( $new['place'] ) ) {
					$new['place'] = 'head';
				}
				/**
				 * stripslashes on code
				 */
				if ( isset( $new['code'] ) ) {
					$new['code'] = stripslashes( $new['code'] );
				}

				/**
				 * Escape code id
				 */
				if ( isset( $new['ga_tracking_id'] ) ) {
					$new['ga_tracking_id'] = esc_html( $new['ga_tracking_id'] );
				}

				$data[ $key ] = $new;
			}
			$this->update_value( $data );
		}

		/**
		 * Get data by target
		 *
		 * @since 2.3.0
		 *
		 * @param string $target Target for tracking code.
		 */
		private function get_data( $target ) {
			/**
			 * Prevent on WP Admin
			 *
			 * @since 3.1.2
			 */
			if ( is_admin() ) {
				return;
			}
			$results = array();
			$data    = $this->local_get_value();
			if ( empty( $data ) ) {
				return;
			}
			
			foreach ( $data as $one ) {
				/**
				 * ignore inactive
				 */
				if ( ! isset( $one['active'] ) || 'on' !== $one['active'] ) {
					continue;
				}
				/**
				 * Handle raw js code and make sure it appears only in specific place.
				 */
				if ( ! empty( $one['code'] ) && ( ! isset( $one['place'] ) || $target !== $one['place'] ) ) {
					continue;
				}
				/**
				 * ignore empty
				 */
				if ( empty( $one['code'] ) ) {
					continue;
				}

				/**
				 * check filters
				 */
				$show = $this->check_filters( $one );
				if ( false === $show ) {
					continue;
				}
				/**
				 * YES! Show it.
				 */
				$results[] = $one;
			}

			/**
			 * print it!
			 */
			foreach ( $results as $one ) {
				if ( ! empty( $one['code'] ) ) {
					$this->debug( $one['id'], __CLASS__ );
					echo stripslashes( html_entity_decode( $one['code'], ENT_QUOTES ) );
					$this->debug( $one['id'], __CLASS__, false );
				}
			}
			return $results;
		}

		/**
		 * Prepares tracking scripts only for G4 ids not for legacy codes.
		 * Registers head and footer scripts and lists tracking ids.
		 *
		 * @return void
		 */
		public function prepare_tracking_scripts() {
			if ( is_admin() || ! is_main_query() || ! empty( $this->tracking_ids ) ) {
				return;
			}

			$tracking_data_list = $this->local_get_value();

			if ( empty( $tracking_data_list ) ) {
				return;
			}

			foreach ( $tracking_data_list as $tracking_data ) {
				//if ( ! isset( $tracking_data['active'] ) || 'on' !== $tracking_data['active'] || empty( $tracking_data['ga_tracking_id'] ) ) {
				if ( empty( $tracking_data['provider'] ) || ! isset( $tracking_data['active'] ) || 'on' !== $tracking_data['active'] ) {
					continue;
				}

				if ( ! $this->check_filters( $tracking_data ) ) {
					continue;
				}

				if ( in_array( $tracking_data['provider'], array_keys( $this->_tracking_providers ) ) ) {
					$provider_key = $tracking_data['provider'];

					if ( ! isset( $this->tracking_ids[ $tracking_data['provider'] ] ) ) {
						$this->tracking_ids[ $tracking_data['provider'] ] = array();
					}

					if ( ! empty( $tracking_data['ga_tracking_id'] ) ) {
						$this->tracking_ids[ $tracking_data['provider'] ][] = esc_attr( $tracking_data['ga_tracking_id'] );
					}

					if ( ! empty( $provider_key ) && ! empty( $this->_tracking_providers[ $provider_key ]['inputs'] ) ) {
						$provider_inputs = array_keys( $this->_tracking_providers[ $provider_key ]['inputs'] );

						if ( ! empty( $provider_inputs ) ) {

							$provider_info = array();

							foreach ( $provider_inputs  as $provider_input_key ) {

								if ( ! empty( $tracking_data[ $provider_input_key ] ) ) {
									//$this->tracking_ids[ $tracking_data['provider'] ][ $provider_input_key ] = $tracking_data[ $provider_input_key ];
									$provider_info[ $provider_input_key ] = $tracking_data[ $provider_input_key ];;
								}
							}

							if ( ! empty( $provider_info ) ) {
								$this->tracking_ids[ $tracking_data['provider'] ][] = $provider_info;
							}
						}
					}
				} else {
					if ( ! empty( $tracking_data['ga_tracking_id'] ) ) {
						$this->append_tracking_ids( $tracking_data['ga_tracking_id'] );
					}
				}
			}
		}

		public function append_tracking_ids( $tracking_id ) {
			$tracking_id = esc_attr( $tracking_id );
			$code_types  = array(
				array(
					//'name' => esc_html__( 'Universal Analitics' ,  'ub' ),
					'slug' => 'UA',
					'prefix' => 'UA-',
				),
				array(
					//'name' => esc_html__( 'Google Analytics 4' ,  'ub' ),
					'slug' => 'G4',
					'prefix' => 'G-',
				),
				array(
					//'name' => esc_html__( 'Google AdWords' ,  'ub' ),
					'slug' => 'AW',
					'prefix' => 'AW-',
				),
			);

			foreach ( $code_types as $code_type ) {
				if ( empty( $code_type[ 'prefix' ] ) || empty( $code_type[ 'slug' ] ) ) {
					continue;
				}

				$prefix = $code_type[ 'prefix' ];
				$slug = $code_type[ 'slug' ];

				if ( substr( $tracking_id, 0, strlen( $prefix ) ) === $prefix ) {
					if ( empty( $this->tracking_ids[ $slug ] ) || ! is_array(  $this->tracking_ids[ $slug ] ) ) {
						$this->tracking_ids[ $slug ] = array();
					}

					if ( ! in_array( $tracking_id, $this->tracking_ids[ $slug ] ) ) {
						$this->tracking_ids[ $slug ][] = $tracking_id;
					}
				}
			}
		}

		public function head_tracking_scripts() {
			if ( empty( $this->tracking_ids ) ) {
				return;
			}

			$tracking_codes_ua     = ! empty( $this->tracking_ids[ 'UA' ] ) ? $this->tracking_ids[ 'UA' ] : array();
			$tracking_codes_g4     = ! empty( $this->tracking_ids[ 'G4' ] ) ? $this->tracking_ids[ 'G4' ] : array();
			$tracking_codes_aw     = ! empty( $this->tracking_ids[ 'AW' ] ) ? $this->tracking_ids[ 'AW' ] : array();
			$combined_tracking_ids = array_merge( $tracking_codes_ua, $tracking_codes_g4, $tracking_codes_aw );
			$google_tag_provider   = array_key_first( $this->_tracking_providers );

			if ( $combined_tracking_ids ) {
				$this->tracking_ids[ $google_tag_provider ] = array_merge( $combined_tracking_ids, $this->tracking_ids[ $google_tag_provider ] );
			}

			foreach ( $this->tracking_ids as $provider => $tracking_ids ) {
				if ( ! in_array( $provider, array_keys( $this->_tracking_providers ) ) ) {
					continue;
				}
				call_user_func( array( $this, $provider . '_tracking_script' ), $tracking_ids );
			}
		}

		private function get_tracking_key_config( $tracking_id, $function ) {
			$config_params     = apply_filters( 'branda_google_analytics_key_config', array(), $tracking_id );
			$config_params_str = '';
			$params_content    = '';

			if ( ! empty( $config_params ) ) {
				foreach ( $config_params as $param_name => $param_value ) {
					$config_params_str .= "'{$param_name}': $param_value" . PHP_EOL;
				}

				$params_content = ! empty( $config_params_str ) ? ", {{$config_params_str}} " : '';
			}

			return "{$function}('config', '{$tracking_id}' {$params_content})";
		}

		public function footer_tracking_scripts() {
			if ( empty( $this->tracking_ids ) ) {
				return;
			}

			$scripts = array();

			if ( ! empty( $this->tracking_ids[ 'G4' ] ) ) {
				foreach ( $this->tracking_ids[ 'G4' ] as $tracking_id ) {
					$tracking_id = esc_attr( $tracking_id );
					$scripts[]   = "
					<!-- Branda Footer -->
					<!-- Google Tag Manager (noscript) -->
					<noscript><iframe src=\"https://www.googletagmanager.com/ns.html?id={$tracking_id}\"
					height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>
					<!-- End Google Tag Manager (noscript) -->
					";
				}
			}

			echo implode( PHP_EOL, $scripts );
		}

		/**
		 * Get data for head
		 *
		 * @since 2.3.0
		 */
		public function target_head() {
			$this->get_data( 'head' );
		}

		/**
		 * Get data for body
		 *
		 * @since 2.3.0
		 */
		public function target_begin_of_body() {
			$this->get_data( 'body' );
		}

		/**
		 * Get data for footer
		 *
		 * @since 2.3.0
		 */
		public function target_footer() {
			$this->get_data( 'footer' );
		}

		/**
		 * Set options
		 *
		 * @since 2.3.0
		 */
		protected function set_options() {
			$options       = array(
				'list' => array(
					'fields' => array(
						'list' => array(
							'type'     => 'callback',
							'callback' => array( $this, 'get_list' ),
						),
					),
				),
			);
			$this->options = $options;
		}

		/**
		 * Get list of trackin codes.
		 *
		 * @since 2.3.0
		 */
		public function get_list() {
			require_once 'tracking-codes-list-table.php';
			$data = $this->local_get_value();
			if ( empty( $data ) ) {
				$data = array();
			}
			ob_start();
			$list_table = new Branda_Tracking_Codes_List_Table();

			$list_table->set_config( $this );
			
			$list_table->prepare_items( $data );

			if ( ! $this->allow_raw_js && ! empty( $list_table->items )  ) {
				foreach ( $list_table->items as $item_key => $item_value ) {
					if ( ! empty( $item_value['code'] ) ) {
						unset( $list_table->items[ $item_key ] );
					}
				}
			}

			$list_table->display();
			$content = ob_get_contents();
			ob_end_clean();
			$content .= $this->get_dialog_delete( 'bulk' );
			return $content;
		}

		/**
		 * Check visibility by filter.
		 *
		 * @since 2.3.0
		 *
		 * @param array $data Configuration data of single tracking code.
		 * @return boolean show or hide value.
		 */
		private function check_filters( $data ) {
			$show = true;
			/**
			 * Handle only Main Query and leave the admin alone!
			 */
			if ( ! is_main_query() || is_admin() ) {
				return $show;
			}
			/**
			 * Subsite limit
			 */
			if ( isset( $data['sites'] ) && is_array( $data['sites'] ) ) {
				$blog_id = get_current_blog_id();
				$show    = in_array( $blog_id, $data['sites'] );
			}
			/**
			 * Filters are off or misconfigured
			 */
			if ( ! isset( $data['filter'] ) || 'on' !== $data['filter'] ) {
				return $show;
			}
			/**
			 * filter by user
			 */
			if ( $show && isset( $data['users'] ) ) {
				$show = $this->filter_by_user( $data['users'] );
			}
			/**
			 * filter by author
			 */
			if ( $show && isset( $data['authors'] ) ) {
				$show = $this->filter_by_author( $data['authors'] );
			}
			/**
			 * filter by archive
			 */
			if ( $show && isset( $data['archives'] ) ) {
				$show = $this->filter_by_archive( $data['archives'] );
			}
			/**
			 * By default return true
			 */
			return $show;
		}

		/**
		 * Check visibility by filter.
		 *
		 * @since 2.3.0
		 *
		 * @param array $data Configuration data of single tracking code.
		 * @return boolean show or hide value.
		 */
		private function filter_by_user( $filter ) {
			if ( ! is_array( $filter ) || empty( $filter ) ) {
				return true;
			}
			$logged = is_user_logged_in();
			if ( in_array( 'anonymous', $filter ) && ! $logged ) {
				return true;
			}
			if ( in_array( 'logged', $filter ) && $logged ) {
				return true;
			}
			$roles = array();
			foreach ( $filter as $one ) {
				if ( preg_match( '/^wp:role:(.+)$/', $one, $mataches ) ) {
					$roles[] = $mataches[1];
				}
			}
			if ( ! empty( $roles ) && ! $logged ) {
				return false;
			}
			$user = wp_get_current_user();
			foreach ( $roles as $role ) {
				if ( 'super' === $role ) {
					return is_super_admin();
				}
				if ( in_array( $role, $user->roles ) ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Check visibility by filter.
		 *
		 * @since 2.3.0
		 *
		 * @param array $data Configuration data of single tracking code.
		 * @return boolean show or hide value.
		 */
		private function filter_by_author( $filter ) {
			if ( ! is_array( $filter ) || empty( $filter ) ) {
				return true;
			}
			if ( ! is_singular() ) {
				return false;
			}
			global $post;
			return in_array( $post->post_author, $filter );
		}

		/**
		 * Check visibility by filter.
		 *
		 * @since 2.3.0
		 *
		 * @param array $data Configuration data of single tracking code.
		 * @return boolean show or hide value.
		 */
		private function filter_by_archive( $filter ) {
			if ( ! is_array( $filter ) || empty( $filter ) ) {
				return true;
			}
			/**
			 * 404
			 */
			if ( in_array( '404', $filter ) && is_404() ) {
				return true;
			}
			/**
			 * author archive
			 */
			if ( in_array( 'authors', $filter ) && is_author() ) {
				return true;
			}
			/**
			 * category archive
			 */
			if ( in_array( 'category', $filter ) && is_category() ) {
				return true;
			}
			/**
			 * tag archive
			 */
			if ( in_array( 'tags', $filter ) && is_tag() ) {
				return true;
			}
			/**
			 * The Home Page
			 */
			if ( in_array( 'home', $filter ) && is_front_page() && is_home() ) {
				return true;
			}
			/**
			 * The Front Page
			 */
			if ( in_array( 'front', $filter ) && is_front_page() ) {
				return true;
			}
			/**
			 * The Blog Page
			 */
			if ( in_array( 'blog', $filter ) && is_home() ) {
				return true;
			}
			/**
			 * The Single Post Page
			 */
			if ( in_array( 'single', $filter ) && is_single() ) {
				return true;
			}
			/**
			 * The Sticky Post Page
			 */
			if ( in_array( 'sticky', $filter ) && is_single() && is_sticky() ) {
				return true;
			}
			/**
			 * The Page
			 */
			if ( in_array( 'page', $filter ) && is_page() ) {
				return true;
			}
			/**
			 * The archive
			 */
			if ( in_array( 'archive', $filter ) && is_archive() ) {
				return true;
			}
			/**
			 * The search
			 */
			if ( in_array( 'search', $filter ) && is_search() ) {
				return true;
			}
			/**
			 * The attachment
			 */
			if ( in_array( 'attachment', $filter ) && is_attachment() ) {
				return true;
			}
			/**
			 * The singular
			 */
			if ( in_array( 'singular', $filter ) && is_singular() ) {
				return true;
			}
			return false;
		}

		/**
		 * Populates the response object for the "get-location" ajax call.
		 * Location data defines where a custom sidebar is displayed, i.e. on which
		 * pages it is used and which theme-sidebars are replaced.
		 *
		 * @since  2.3.0
		 * @return array $archive_type Array of Archive types.
		 */
		private function get_location_data() {
			$archive_type = array(
				'attachment' => __( 'Any Attachment Page', 'ub' ),
				'archive'    => __( 'Any Archive Page', 'ub' ),
				'sticky'     => __( 'Sticky Post', 'ub' ),
				'singular'   => __( 'Any Entry Page', 'ub' ),
				'page'       => __( 'Single Page', 'ub' ),
				'single'     => __( 'Single Post', 'ub' ),
				'front'      => __( 'Front Page', 'ub' ),
				'home'       => __( 'Home Page', 'ub' ),
				'blog'       => __( 'Blog Page', 'ub' ),
				'search'     => __( 'Search Results', 'ub' ),
				// '404' => __( 'Not Found (404)', 'ub' ), currently we can not handle 404 page, because we use `loop_start` filter.
				'authors'    => __( 'Any Author Archive', 'ub' ),
			);
			$all          = get_taxonomies(
				array(
					'public'   => true,
					'_builtin' => true,
				),
				'object'
			);
			foreach ( $all as $taxonomy ) {
				$default_taxonomies[] = $taxonomy->labels->singular_name;
				switch ( $taxonomy->name ) {
					case 'post_format':
						break;
					case 'post_tag':
						/**
						* this a legacy and backward compatibility
						*/
						$archive_type['tags'] = sprintf( __( '%s Archives', 'ub' ), $taxonomy->labels->singular_name );
						break;
					case 'category':
						$archive_type[ $taxonomy->name ] = sprintf( __( '%s Archives', 'ub' ), $taxonomy->labels->singular_name );
						break;
					default:
						break;
				}
			}
			/**
			 * sort array by values
			 */
			asort( $archive_type );
			return $archive_type;
		}

		/**
		 * Allow to get value from provate/protection function.
		 *
		 * @since 2.3.0
		 */
		public function local_get_value() {
			$codes = $this->get_value();
			if ( empty( $codes ) ) {
				return array();
			}
			if ( isset( $codes['plugin_version'] ) ) {
				unset( $codes['plugin_version'] );
			}
			if ( isset( $codes['imported'] ) ) {
				unset( $codes['imported'] );
			}
			/**
			 * sanitize
			 */
			foreach ( $codes as $id => $code ) {
				if ( ! isset( $code['active'] ) ) {
					$codes[ $id ]['active'] = 'off';
				}
				if ( ! isset( $code['filter'] ) ) {
					$codes[ $id ]['filter'] = 'off';
				}
				if ( ! isset( $code['place'] ) ) {
					$codes[ $id ]['place'] = 'head';
				}
			}
			return $codes;
		}

		/**
		 * Allow to update value from provate/protection function.
		 *
		 * @since 2.3.0
		 */
		public function local_update_value( $value ) {
			return $this->update_value( $value );
		}

		/**
		 * Add button after title.
		 *
		 * @since 3.0.0
		 */
		public function add_button_after_title( $content, $module ) {
			if ( $this->module !== $module['module'] ) {
				return $content;
			}
			$content .= '<div class="sui-actions-left">';
			$content .= $this->button_add();
			$content .= '</div>';
			$content .= $this->add_dialog();
			return $content;
		}

		/**
		 * SUI: button add
		 *
		 * @since 3.0.0
		 *
		 * @return string Button HTML.
		 */
		public function button_add() {
			$args = array(
				'data' => array(
					'modal-open' => $this->get_name( 'new' ),
				),
				'icon' => 'plus',
				'text' => _x( 'Add Tracking Code', 'button', 'ub' ),
				'sui'  => 'blue',
			);
			return $this->button( $args );
		}

		/**
		 * Add modal windows.
		 *
		 * @since 3.0.0
		 */
		public function add_dialog( $atts = array() ) {
			global $wp_roles;
			$defaults = array(
				'module'           => $this->module,
				'is_network_admin' => $this->is_network_admin,
				'id'               => 'new',
				'active'           => 'off',
				'title'            => '',
				'code'             => '',
				'ga_tracking_id'   => '',
				'place'            => 'head',
				'filter'           => 'off',
				'users'            => array(),
				'authors'          => array(),
				'archives'         => array(),
				'sites'            => array(),
				'providers'        => wp_list_pluck( $this->_tracking_providers, 'title' ),
				'providers_data'   => $this->_tracking_providers,
			);
			
			$args                  = wp_parse_args( $atts, $defaults );
			$args['data_archives'] = $this->get_location_data();
			/**
			 * Add authors
			 */
			$authors        = array();
			$get_query_args = array(
				'fields'  => array( 'ID', 'display_name' ),
				'orderby' => 'display_name',
			);
			if ( $this->is_network ) {
				$get_query_args['blog_id'] = 0;
			} else {
				if ( version_compare( $GLOBALS['wp_version'], '5.9', '<' ) ) {
					$get_query_args['who'] = 'authors';
				} else {
					$get_query_args['capability'] = array( 'edit_posts' );
				}
			}
			$users = get_users( $get_query_args );
			foreach ( $users as $user ) {
				$authors[ $user->ID ] = $user->display_name;
			}
			/**
			 * Add superadmins
			 */
			if ( $this->is_network ) {
				$users = get_super_admins();
				foreach ( $users as $login ) {
					$user = get_user_by( 'login', $login );
					if ( is_a( $user, 'WP_User' ) ) {
						$authors[ $user->data->ID ] = $user->data->display_name;
					}
				}
			}
			natcasesort( $authors );
			$args['data_authors'] = $authors;
			/**
			 * Add users roles
			 */
			$roles = array(
				'logged'    => __( 'Only logged users', 'ub' ),
				'anonymous' => __( 'Only non logged users', 'ub' ),
			);
			foreach ( $wp_roles->roles as $slug => $data ) {
				$roles[ 'wp:role:' . $slug ] = $data['name'];
			}
			if ( $this->is_network ) {
				$roles['wp:role:super'] = __( 'Super Admin', 'ub' );
			}
			natcasesort( $roles );
			$args['data_users'] = $roles;
			/**
			 * Sites
			 */
			if ( $this->is_network && function_exists( 'get_sites' ) ) {
				$args['data_sites'] = $this->get_sites_by_args();
			}

			$args['provider']                   = 'new' === $args['id'] ? '' : $this->get_provider( $args['id'] );
			$args['allow_raw_tracking_scripts'] = $this->allow_raw_js;
			$arg                                = $defaults;
			
			/**
			 * generate
			 */
			$template = $this->get_template_name( 'dialogs/edit' );
			$content  = $this->render( $template, $args, true );

			/**
			 * Footer
			 */
			$footer      = '';
			$button_args = array(
				'sui'  => 'ghost',
				'text' => __( 'Cancel', 'ub' ),
				'data' => array(
					'modal-close' => '',
				),
			);
			$footer     .= $this->button( $button_args );
			$button_args = array(
				'data'  => array(
					'nonce' => $this->get_nonce_value(),
				),
				'text'  => 'new' === $args['id'] ? __( 'Add', 'ub' ) : __( 'Update', 'ub' ),
				'class' => $this->get_name( 'save' ),
			);
			if ( 'new' === $args['id'] ) {
				$button_args['icon'] = 'check';
			}
			$footer .= $this->button( $button_args );
			/**
			 * Dialog
			 */
			$dialog_args = array(
				'id'           => $this->get_name( $args['id'] ),
				'title'        => 'new' === $args['id'] ? __( 'Add Tracking Code', 'ub' ) : __( 'Edit Tracking Code', 'ub' ),
				'content'      => $content,
				'confirm_type' => false,
				'classes'      => array( 'sui-modal-lg' ),
				'footer'       => array(
					'content' => $footer,
					'classes' => array( 'sui-space-between' ),
				),
			);
			return $this->sui_dialog( $dialog_args );
		}

		/**
		 * Use get_sites() helper.
		 *
		 * @since 2.3.0
		 */
		private function get_sites_by_args( $args = array(), $mode = 'search' ) {
			$results = array();
			if ( ! function_exists( 'get_sites' ) ) {
				return $results;
			}
			$args['orderby'] = 'domain';
			$sites           = get_sites( $args );
			foreach ( $sites as $site ) {
				$details = get_blog_details( $site->blog_id );
				if ( 'search' === $mode ) {
					$results[] = array(
						'id'       => $site->blog_id,
						'title'    => $site->blogname,
						'subtitle' => $site->siteurl,
					);
				} else {
					$results[ $site->blog_id ] = $site->blogname;
				}
			}
			return $results;
		}

		/**
		 * Save code
		 *
		 * @since 3.0.0
		 */
		public function ajax_save() {
			$nonce_action = $this->get_nonce_action();
			$this->check_input_data( $nonce_action, array( 'branda' ) );
			$branda  = $this->sanitize_request_payload(
				$_POST['branda'],
				array(
					'code' => array( $this, 'sanitize_tracking_code' ),
				)
			);
			$id      = isset( $branda['id'] ) ? sanitize_text_field( $branda['id'] ) : 'new';
			$message = esc_html__( 'Tracking Code %s was updated.', 'ub' );
			if ( 'new' === $id ) {
				$message = esc_html__( 'Tracking Code %s was created.', 'ub' );
				$id      = $this->generate_id( $branda );
			}
			$this->uba->add_message(
				array(
					'type'    => 'success',
					'message' => sprintf( $message, $this->bold( sanitize_text_field( $branda['title'] ) ) ),
				)
			);
			$branda['id'] = $id;
			/**
			 * strip Backslashes
			 */
			if ( isset( $branda['code'] ) ) {
				$branda['code'] = stripslashes( $branda['code'] );
			}

			/**
			 * Escape code id.
			 */
			if ( isset( $branda['ga_tracking_id'] ) ) {
				$branda['ga_tracking_id'] = sanitize_text_field( $branda['ga_tracking_id'] );
			}

			if ( isset( $branda['provider'] ) && in_array( $branda['provider'], array_keys( $this->_tracking_providers ) ) ) {
				$branda['provider'] = sanitize_key( $branda['provider'] );
			} else {
				$branda['provider'] = array_key_first( $this->_tracking_providers );
			}

			$branda['place'] =  ! empty( $branda['place'] ) ? sanitize_text_field( $branda['place'] ) : '';
			$branda['active'] = sanitize_text_field( $branda['active'] );
			$branda['filter'] = sanitize_text_field( $branda['filter'] );

			/**
			 * save
			 */
			$data        = $this->local_get_value();
			$data[ $id ] = $branda;
			$this->update_value( $data );
			$this->delete_hummingbird_cache();
			wp_send_json_success();
		}

		public function escape_data( $escaped_data, $original_data, $module, $section, $name ) {
			if ( $this->module !== $module || ! is_array( $escaped_data ) || empty( $escaped_data ) ) {
				return $escaped_data;
			}

			foreach ( array_keys( $escaped_data ) as $data_key ) {
				if ( isset( $escaped_data[$data_key][ 'code' ] ) ) {
					$escaped_data[$data_key][ 'code' ] = ! empty( $original_data[$data_key][ 'code' ] ) ? $this->sanitize_tracking_code( $original_data[$data_key][ 'code' ] ) : '';
				}
			}

			return $escaped_data;
		}

		protected function sanitize_tracking_code( $code ) {
			$code  = stripslashes( $code );
			$attrs = array(
				'script'   => array(
					'async'          => array(),
					'crossorigin'    => array(),
					'defer'          => array(),
					'integrity'      => array(),
					'nomodule'       => array(),
					'nonce'          => array(),
					'referrerpolicy' => array(),
					'src'            => array(),
					'type'           => array(),
					'charset'        => array(),
					'language'       => array(),
					'data-ad-client' => array(),
				),
				'ins'      => array(
					'class'  => array(),
					'style'  => array(),
					'data-*' => true,
				),
				'noscript' => array(),
				'iframe'   => array(
					'src'    => array(),
					'style'  => true,
					'width'  => array(),
					'height' => array(),
				),
				'style'    => array(),
			);
			$attrs = apply_filters( 'branda_tracking_codes_allowed_script_attributes', $attrs );

			add_filter( 'safe_style_css', function( $styles ) {
				$styles[] = 'display';
				$styles[] = 'visibility';
				return $styles;
			} );

			return wp_kses( $code, $attrs );
		}

		/**
		 * delete single code
		 *
		 * @since 3.0.0
		 */
		public function ajax_delete() {
			$id           = ! empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
			$nonce_action = $this->get_nonce_action( $id, 'delete' );
			$this->check_input_data( $nonce_action, array( 'id' ) );
			$data = $this->local_get_value();
			if ( ! isset( $data[ $id ] ) ) {
				$this->json_error();
			}
			$message = esc_html__( 'Tracking Code %s was deleted.', 'ub' );
			$this->uba->add_message(
				array(
					'type'    => 'success',
					'message' => sprintf( $message, $this->bold( $data[ $id ]['title'] ) ),
				)
			);
			unset( $data[ $id ] );
			$this->update_value( $data );
			$this->delete_hummingbird_cache();
			wp_send_json_success();
		}

		/**
		 * delete bulk codes.
		 *
		 * @since 3.0.0
		 */
		public function ajax_bulk_delete() {
			$id           = ! empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
			$nonce_action = $this->get_nonce_action( $id, 'delete' );
			$this->check_input_data( $nonce_action, array( 'ids' ) );
			$data   = $this->local_get_value();
			$titles = array();
			$ids    = $this->sanitize_request_payload( $_POST['ids'] );
			if ( ! is_array( $ids ) || empty( $ids ) ) {
				$this->json_error();
			}
			foreach ( $ids as $id ) {
				if ( isset( $data[ $id ] ) ) {
					$titles[] = $this->bold( $data[ $id ]['title'] );
					unset( $data[ $id ] );
				}
			}
			if ( empty( $titles ) ) {
				$this->json_error();
			}
			$message = esc_html(
				_n(
					'Tracking Code %s was deleted.',
					'Tracking Codes %s was deleted.',
					count( $titles ),
					'ub'
				)
			);
			$this->uba->add_message(
				array(
					'type'    => 'success',
					'message' => sprintf( $message, implode( ', ', $titles ) ),
				)
			);
			$this->update_value( $data );
			$this->delete_hummingbird_cache();
			wp_send_json_success();
		}

		/**
		 * Delete Hummingbird cache
		 *
		 * @since 3.0.0
		 */
		private function delete_hummingbird_cache() {
			if ( class_exists( 'WP_Hummingbird' ) ) {
				$hummingbird = WP_Hummingbird::get_instance();
				if ( is_object( $hummingbird ) ) {
					foreach ( $hummingbird->core->modules as $module ) {
						if ( ! $module->is_active() ) {
							continue;
						}
						$module->clear_cache();
					}
				}
			}
		}

		/**
		 * Replace default by module related
		 */
		public function dialog_delete_attr_filter( $args, $module, $id ) {
			if ( $this->module === $module ) {
				$args['title']       = __( 'Delete Tracking Code', 'ub' );
				$args['description'] = __( 'Are you sure you wish to permanently delete this tracking code?', 'ub' );
				if ( 'bulk' === $id ) {
					$args['title']       = __( 'Delete Tracking Codes', 'ub' );
					$args['description'] = __( 'Are you sure you wish to permanently delete selected tracking codes?', 'ub' );
				}
			}
			return $args;
		}

		/**
		 * Save code
		 *
		 * @since 3.0.1
		 */
		public function ajax_reset() {
			$id           = ! empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
			$nonce_action = $this->get_nonce_action( 'reset', $id );
			$this->check_input_data( $nonce_action );
			$data = $this->local_get_value();
			if ( isset( $data[ $id ] ) ) {
				wp_send_json_success( $data[ $id ] );
			}
			wp_send_json_error();
		}

		/**
		 * GA tracking script
		 * @link https://developers.google.com/analytics/devguides/collection/gtagjs
		 *
		 * @param $tracking_ids
		 *
		 * @return void
		 */
		public function google_tag_tracking_script( $tracking_ids ) {
			$scripts               = array();
			$first_key             = null;
			$data_layer            = apply_filters( 'branda_google_analytics_data_layer', 'branda_tracking' );
			$function              = apply_filters( 'branda_google_analytics_function', 'branda_tracking_ga' );

			if ( ! empty( $tracking_ids ) ) {
				foreach ( $tracking_ids as $tracking_id ) {
					$tracking_id = esc_attr( $tracking_id );
					$first_key   = empty( $first_key ) ? $tracking_id : $first_key;
					$scripts[]   = $this->get_tracking_key_config( $tracking_id, $function );
				}
				?>

				<?php // phpcs:ignore ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $first_key ); ?>&l=<?php echo esc_attr( $data_layer ); ?>"></script>
<script>
<!-- Branda Tracking Head -->
<!-- Google Tag Manager -->
window.<?php echo esc_attr( $data_layer ); ?> = window.<?php echo esc_attr( $data_layer ); ?> || [];
<?php echo PHP_EOL; ?>
function <?php echo esc_attr( $function ); ?>() {<?php echo esc_attr( $data_layer ); ?>.push(arguments);}

<?php echo PHP_EOL; ?>
<?php echo esc_attr( $function ); ?>('js', new Date())
<?php
echo PHP_EOL;

echo implode( PHP_EOL, $scripts );
echo PHP_EOL; ?>
</script>
				<?php
			}
		}

		/**
		 * Facebook Pixel tracking script
		 * @link https://developers.facebook.com/docs/meta-pixel/get-started
		 *
		 * @param $tracking_ids
		 *
		 * @return void
		 */
		public function facebook_pixel_tracking_script( $tracking_data ) {
			$scripts = array();

			if ( ! empty( $tracking_data ) && is_array( $tracking_data ) ) {
				foreach( $tracking_data as $index => $tracking_values ) {
					$pixel_id  = ! empty( $tracking_values['fb_pixel_id'] ) ? $tracking_values['fb_pixel_id'] : '';
					$fbq       = "fbq('init', '" . esc_js( $pixel_id ) . "');";
					$fbq .= PHP_EOL . "fbq('track', 'PageView');";
					$scripts[] = $fbq;
				}

				echo PHP_EOL; ?>
<!-- Facebook Pixel Code -->
<script>
	!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
	n.callMethod.apply(n,arguments):n.queue.push(arguments)};
	if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
	n.queue=[];t=b.createElement(e);t.async=!0;
	t.src=v;s=b.getElementsByTagName(e)[0];
	s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
<?php echo implode( PHP_EOL, $scripts ) . PHP_EOL; ?>
</script>
<noscript></noscript>
<!-- End Facebook Pixel Code -->
				<?php
			}
		}

		/**
		 * Monsido tracking script
		 * @link https://help.monsido.com/en/articles/5460155-add-the-monsido-script
		 *
		 * @param $tracking_ids
		 *
		 * @return void
		 */
		public function monsido_tracking_script( $tracking_ids ) {
		echo PHP_EOL; ?>
<!-- Branda Tracking Codes -->
<script type="text/javascript">
window._monsido = window._monsido || {
	token: "<?php echo esc_js( $tracking_ids[0] ); ?>",
};
</script>
<script type="text/javascript" async src="https://app-script.monsido.com/v2/monsido-script.js"></script>
			<?php
		}

		/**
		 * MS Clarity tracking script
		 * @link https://learn.microsoft.com/en-us/clarity/setup-and-installation/clarity-setup
		 *
		 * @param $tracking_ids
		 *
		 * @return void
		 */
		public function ms_clarity_tracking_script( $tracking_data ) {
			echo PHP_EOL; ?><!-- Branda Tracking Codes --><?php
			//foreach ( $tracking_ids as $tracking_id ) {
			if ( ! empty( $tracking_data ) && is_array( $tracking_data ) ) {
				foreach( $tracking_data as $index => $tracking_values ) {
					$tracking_id = ! empty( $tracking_values['ms_clarity_token'] ) ? $tracking_values['ms_clarity_token'] : '';
				echo PHP_EOL; ?>
<script type="text/javascript">
	(function(c,l,a,r,i,t,y){
		c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
		t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
		y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
	})(window, document, "clarity", "script", "<?php echo esc_js( $tracking_id ); ?>");
</script>
				<?php
				}
			}
		}

		/**
		 * Piwik Pro tracking script
		 * @link https://help.piwik.pro/support/getting-started/install-a-tracking-code
		 *
		 * @param $tracking_ids
		 *
		 * @return void
		 */
		public function piwik_tracking_script( $tracking_data ) {

			if ( ! empty( $tracking_data ) && is_array( $tracking_data ) ) {
				foreach( $tracking_data as $index => $tracking_values ) {
					$container_url = ! empty( $tracking_values['container_url'] ) ? $tracking_values['container_url'] : '';
					$site_id       = ! empty( $tracking_values['site_id'] ) ? $tracking_values['site_id'] : '';

					echo PHP_EOL; ?><!-- Branda Tracking Codes - PIWIK PRO --><?php
				//echo PHP_EOL; ?>
<script type="text/javascript">
(function(window, document, dataLayerName, container_url, id) {
window[dataLayerName]=window[dataLayerName]||[],window[dataLayerName].push({start:(new Date).getTime(),event:"stg.start"});var scripts=document.getElementsByTagName('script')[0],tags=document.createElement('script');
function stgCreateCookie(a,b,c){var d="";if(c){var e=new Date;e.setTime(e.getTime()+24*c*60*60*1e3),d="; expires="+e.toUTCString();f="; SameSite=Strict"}document.cookie=a+"="+b+d+f+"; path=/"}
var isStgDebug=(window.location.href.match("stg_debug")||document.cookie.match("stg_debug"))&&!window.location.href.match("stg_disable_debug");stgCreateCookie("stg_debug",isStgDebug?1:"",isStgDebug?14:-1);
var qP=[];dataLayerName!=="dataLayer"&&qP.push("data_layer_name="+dataLayerName),isStgDebug&&qP.push("stg_debug");var qPString=qP.length>0?("?"+qP.join("&")):"";
tags.async=!0,tags.src=container_url+id+".js"+qPString,scripts.parentNode.insertBefore(tags,scripts);
!function(a,n,i){a[n]=a[n]||{};for(var c=0;c<i.length;c++)!function(i){a[n][i]=a[n][i]||{},a[n][i].api=a[n][i].api||function(){var a=[].slice.call(arguments,0);"string"==typeof a[0]&&window[dataLayerName].push({event:n+"."+i+":"+a[0],parameters:[].slice.call(arguments,1)})}}(i[c])}(window,"ppms",["tm","cm"]);
})(window, document, 'dataLayer', '<?php echo trailingslashit(esc_js( $container_url ) ); ?>', '<?php echo esc_js( $site_id ); ?>');
</script>
<!-- PIWIK PRO END -->
				<?php
				}
			}
			
		}

		/**
		 * SiteImprove tracking script
		 * @link https://help.siteimprove.com/support/solutions/articles/80000448448-adding-siteimprove-analytics-javascript-to-your-website
		 *
		 * @param $tracking_ids
		 *
		 * @return void
		 */
		public function site_improve_tracking_script( $tracking_ids ) {
			echo PHP_EOL; ?><!-- Branda Tracking Codes --><?php
			foreach ( $tracking_ids as $tracking_id ) {
				echo PHP_EOL; ?>
<script type="text/javascript">
(function() {
	var sz = document.createElement('script'); sz.type = 'text/javascript'; sz.async = true;
	sz.src = '//siteimproveanalytics.com/js/siteanalyze_<?php echo esc_js( $tracking_id ); ?>.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(sz, s);
})();
</script>
				<?php
			}
		}

		/**
		 * Cloudflare tracking script
		 * @link https://developers.cloudflare.com/analytics/web-analytics/getting-started/web-analytics-spa
		 *
		 * @param $tracking_ids
		 *
		 * @return void
		 */
		public function cloudflare_tracking_script( $tracking_data ) {
			if ( ! empty( $tracking_data ) && is_array( $tracking_data ) ) {
				foreach( $tracking_data as $index => $tracking_values ) {
					$token = ! empty( $tracking_values['cf_token'] ) ? $tracking_values['cf_token'] : '';

					if ( empty( $token ) ) {
						continue;
					}

					echo PHP_EOL; ?>
<!-- Cloudflare Web Analytics -->
<script defer src='https://static.cloudflareinsights.com/beacon.min.js' data-cf-beacon='{"token": "<?php echo esc_js( $token ); ?>"}'></script>
<!-- End Cloudflare Web Analytics -->
					<?php
				}
			}
		}

		/**
		 * CBE tracking script
		 * @link https://capturehighered.com
		 *
		 * @param $tracking_ids
		 *
		 * @return void
		 */
		public function cbe_tracking_script( $tracking_ids ) {
			$scripts = array();
			foreach ( $tracking_ids as $tracking_id ) {
				$_cbe = "_cbe('create', '" . esc_js( $tracking_id ) . "');";
				$_cbe .= PHP_EOL . "_cbe('log', 'pageview');";
				$scripts[] = $_cbe;
			}
			echo PHP_EOL; ?>
<!-- Branda Tracking -->
<!-- begin CBE code -->
<script>
(function(a,b,c,d,e,f,g) {
	a[e] = a[e] || function() {(a[e].q = a[e].q || []).push(arguments)};f=b.createElement(c);
	g=b.getElementsByTagName(c)[0];f.async=1;f.src=d+"/cbe/cbe.js";g.parentNode.insertBefore(f,g);
})(window,document,"script","https://cbe.capturehighered.net","_cbe");

<?php echo implode( PHP_EOL, $scripts ) . PHP_EOL; ?>
</script>
<!-- end CBE code -->
			<?php
		}

		/**
		 * Get tracking provider
		 *
		 * @param $id
		 *
		 * @return string
		 */
		public function get_provider( $id ) {
			$tracking_data = $this->local_get_value();
			if ( empty( $tracking_data ) ) {
				return '';
			}

			if ( isset( $tracking_data[ $id ] ) ) {
				if ( isset( $tracking_data[ $id ]['provider'] ) ) {
					return $tracking_data[ $id ]['provider'];
				} else {
					// GA as default
					return array_key_first( $this->_tracking_providers );
				}
			}

			return '';
		}

		/**
		 * Get tracking provider name
		 *
		 * @param $provider
		 *
		 * @return string
		 */
		public function get_provider_name( $provider ) {
			if ( ! empty( $this->_tracking_providers[ $provider ]['title'] ) ) {
				return $this->_tracking_providers[ $provider ]['title'];
				//return $this->tracking_providers[ $provider ];
			}

			// GA as default
			$provider = array_key_first( $this->_tracking_providers );
			return ! empty( $this->_tracking_providers[ $provider ]['title'] ) ? $this->_tracking_providers[ $provider ]['title'] : '';
		}
	}
}
new Branda_Tracking_Codes();
