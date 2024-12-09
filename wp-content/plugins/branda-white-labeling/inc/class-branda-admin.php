<?php
if ( ! class_exists( 'Branda_Admin' ) ) {
	require_once dirname( __FILE__ ) . '/class-branda-base.php';
	require_once dirname( __FILE__ ) . '/class-branda-admin-stats.php';
	class Branda_Admin extends Branda_Base {

		var $modules    = array();
		var $plugin_msg = array();

		/**
		 * Default messages.
		 *
		 * @since 1.8.5
		 */
		var $messages = array();

		/**
		 * Stats
		 *
		 * @since 2.3.0
		 */
		private $stats = null;

		/**
		 * module
		 *
		 * @since 3.0.0
		 */
		private $module = '';

		/**
		 * Show Welcome Dialog
		 *
		 * @since 3.0.0
		 */
		private $show_welcome_dialog = false;

		/**
		 * Top page slug
		 */
		private $top_page_slug;

		/**
		 * Messages storing
		 *
		 * @since 3.1.0
		 */
		private $messages_option_name = 'branda_messages';

		/**
		 * Is Branda admin menu shown or not
		 *
		 * @var bool
		 */
		private static $is_show_admin_menu = false;

		public function __construct() {
			parent::__construct();
			/**
			 * set and sanitize variables
			 */
			add_action( 'plugins_loaded', array( $this, 'set_and_sanitize_variables' ), 2 );
			/**
			 * run stats
			 */
			$this->stats = new Branda_Admin_Stats();
			foreach ( $this->configuration as $key => $data ) {
				$is_avaialble = $this->can_load_module( $data );
				if ( ! $is_avaialble ) {
					continue;
				}
				if ( isset( $data['disabled'] ) && $data['disabled'] ) {
					continue;
				}
				$this->modules[ $key ] = $data['module'];
			}
			/**
			 * Filter allow to turn off available modules.
			 *
			 * @since 1.9.4
			 *
			 * @param array $modules available modules array.
			 */
			$this->modules = apply_filters( 'ultimatebranding_available_modules', $this->modules );
			add_action( 'plugins_loaded', array( $this, 'load_modules' ), 11 );
			add_action( 'plugins_loaded', array( $this, 'setup_translation' ) );
			add_action( 'network_admin_menu', array( $this, 'network_admin_page' ) );
			add_action( 'admin_menu', array( $this, 'admin_page' ) );
			add_filter( 'admin_title', array( $this, 'admin_title' ), 10, 2 );
			/**
			 * AJAX
			 */
			add_action( 'wp_ajax_ultimate_branding_toggle_module', array( $this, 'toggle_module' ) );
			add_action( 'wp_ajax_branda_reset_module', array( $this, 'ajax_reset_module' ) );
			add_action( 'wp_ajax_branda_manage_all_modules', array( $this, 'ajax_bulk_modules' ) );
			add_action( 'wp_ajax_branda_module_copy_settings', array( $this, 'ajax_copy_settings' ) );
			add_action( 'wp_ajax_branda_welcome_get_modules', array( $this, 'ajax_welcome' ) );
			add_action( 'wp_ajax_branda_dismiss_black_friday_notice', array( $this, 'dismiss_black_friday_2021' ) );
			add_filter( 'branda_admin_messages_array', array( $this, 'add_admin_notices' ) );
			add_action( 'wp_ajax_ultimate_branding_new_feature_dismiss', array( $this, 'new_feature_dismiss' ) );
			/**
			 * default messages
			 */
			$this->messages = array(
				'success'               => __( 'Success! Your changes were successfully saved!', 'ub' ),
				'fail'                  => __( 'There was an error, please try again.', 'ub' ),
				'reset-section-success' => __( 'Section was reset to defaults.', 'ub' ),
				'wrong'                 => __( 'Something went wrong!', 'ub' ),
				'security'              => __( 'Nope! Security check failed!', 'ub' ),
				'missing'               => __( 'Missing required data!', 'ub' ),
				'wrong_userlogin'       => __( 'This user login doesn\'t exist!', 'ub' ),
			);
			/**
			 * remove default footer
			 */
			add_filter( 'admin_footer_text', array( $this, 'remove_default_footer' ), PHP_INT_MAX );
			/**
			 * upgrade
			 *
			 * @since 3.0.0
			 */
			add_action( 'init', array( $this, 'upgrade' ) );
			/**
			 * Add branda class to admin body
			 */
			add_filter( 'admin_body_class', array( $this, 'add_branda_admin_body_class' ), PHP_INT_MAX );
			/**
			 * Add import/export modules instantly on
			 */
			add_filter( 'ub_get_option-ultimatebranding_activated_modules', array( $this, 'add_instant_modules' ), 10, 3 );
			/**
			 * Add sui-wrap classes
			 *
			 * @since 3.0.6
			 */
			add_filter( 'branda_sui_wrap_class', array( $this, 'add_sui_wrap_classes' ) );
			/**
			 * Delete image from modules, when it is deleted from WordPress
			 *
			 * @since 3.1.0
			 */
			add_action( 'delete_attachment', array( $this, 'delete_attachment_from_configs' ), 10, 1 );

			// Add additional links on Plugins page
			global $branda_plugin_file;
			add_filter( "plugin_action_links_{$branda_plugin_file}", array( $this, 'add_plugin_action_links' ), 10, 4 );
			add_filter( "network_admin_plugin_action_links_{$branda_plugin_file}", array( $this, 'add_plugin_action_links' ), 10, 4 );
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );
		}

		public function dismiss_black_friday_2021() {
			update_site_option( 'branda_black_friday_2021_dismissed', 1 );
		}

		/**
		 * Load Permissions for checking access to modules
		 */
		private function load_permissions() {
			if ( ! class_exists( 'Branda_Permissions' ) ) {
				branda_load_single_module( 'utilities/permissions.php' );
			}
			Branda_Permissions::get_instance();
		}

		/**
		 * Faked instant on modules as active.
		 *
		 * @since 3.0.0
		 */
		public function add_instant_modules( $value, $option, $default ) {
			if ( ! is_array( $value ) ) {
				$value = array();
			}
			foreach ( $this->configuration as $key => $module ) {
				if ( isset( $module['instant'] ) && 'on' === $module['instant'] ) {
					$value[ $key ] = 'yes';
				}
			}
			return $value;
		}

		/**
		 * Add "Branda" to admin title.
		 *
		 * @since 1.9.8
		 */
		public function admin_title( $admin_title, $title ) {
			$screen = get_current_screen();
			if ( is_a( $screen, 'WP_Screen' ) && preg_match( '/_page_branding/', $screen->id ) ) {
				$admin_title = sprintf(
					'%s%s%s',
					_x( 'Branda', 'admin title', 'ub' ),
					_x( ' &lsaquo; ', 'admin title separator', 'ub' ),
					$admin_title
				);
				if ( ! empty( $this->module ) ) {
					$module_data = $this->get_module_by_module( $this->module );
					if ( ! empty( $module_data ) && isset( $module_data['group'] ) ) {
						$groups = branda_get_groups_list();
						if ( isset( $groups[ $module_data['group'] ] ) ) {
							$admin_title = sprintf(
								'%s%s%s',
								$groups[ $module_data['group'] ]['title'],
								_x( ' &lsaquo; ', 'admin title separator', 'ub' ),
								$admin_title
							);
						}
					}
				}
			}
			return $admin_title;
		}

		/**
		 * Plugin action links
		 *
		 * @param type   $actions
		 * @param string $plugin_file
		 * @return array
		 */
		public function add_plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
			if ( $this->is_network && ( is_main_site() || is_plugin_active_for_network( $plugin_file ) ) ) {
				$admin_url = network_admin_url( 'admin.php' );
			} elseif ( ! $this->is_network || self::$is_show_admin_menu ) {
				$admin_url = admin_url( 'admin.php' );
			}
			if ( ! empty( $admin_url ) ) {
				$dash_url           = add_query_arg( 'page', 'branding', $admin_url );
				$links['dashboard'] = '<a href="' . $dash_url . '">' . __( 'Dashboard', 'ub' ) . '</a>';
			}
			$links['docs'] = '<a href="https://wpmudev.com/docs/wpmu-dev-plugins/branda/?utm_source=branda&utm_medium=plugin&utm_campaign=branda_pluginlist_docs" target="_blank">' . __( 'Docs', 'ub' ) . '</a>';
			if ( Branda_Helper::is_pro() ) {
				if ( ! Branda_Helper::is_member() ) {
					$links['renew'] = '<a href="https://wpmudev.com/project/ultimate-branding/?utm_source=branda&utm_medium=plugin&utm_campaign=branda_pluginlist_renew" target="_blank" style="color: #8D00B1;">' . __( 'Renew Membership', 'ub' ) . '</a>';
				}
			} else {
				if ( is_network_admin() || ! is_multisite() ) {
					$url              = 'https://wpmudev.com/project/ultimate-branding/?utm_source=branda&utm_medium=plugin&utm_campaign=branda_pluginlist_upgrade';
					$links['upgrade'] = '<a href="' . esc_url( $url ) . '" aria-label="' . esc_attr( __( 'Upgrade For 80% Off!', 'ub' ) ) . '" target="_blank" style="color: #8D00B1;">' . esc_html__( 'Upgrade For 80% Off!', 'ub' ) . '</a>';
				}
			}
			$actions = array_merge( $links, $actions );

			return $actions;
		}

		/**
		 * Links next to version number
		 *
		 * @global string $branda_plugin_file
		 * @param array  $plugin_meta
		 * @param string $plugin_file
		 * @return array
		 */
		public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
			global $branda_plugin_file;
			if ( $branda_plugin_file === $plugin_file ) {
				if ( Branda_Helper::is_pro() ) {
					$plugin_meta[2] = '<a href="https://wpmudev.com/project/ultimate-branding/" target="_blank">' . esc_html__( 'View Details', 'ub' ) . '</a>';
					$row_meta       = array(
						'support' => '<a href="https://wpmudev.com/hub/support/#wpmud-chat-pre-survey-modal" target="_blank">' . esc_html__( 'Premium Support', 'ub' ) . '</a>',
					);
				} else {
					$plugin_meta[1] = esc_html__( 'By', 'ub' ) . ' <a href="https://profiles.wordpress.org/wpmudev/" target="_blank">WPMU DEV</a>';
					$row_meta       = array(
						'rate'    => '<a href="https://wordpress.org/support/plugin/branda-white-labeling/reviews/#new-post" target="_blank">' . esc_html__( 'Rate Branda', 'ub' ) . '</a>',
						'support' => '<a href="https://wordpress.org/support/plugin/branda-white-labeling/" target="_blank">' . esc_html__( 'Support', 'ub' ) . '</a>',
					);
				}

				$row_meta['roadmap'] = '<a href="https://wpmudev.com/roadmap/" target="_blank">' . esc_html__( 'Roadmap', 'ub' ) . '</a>';

				$plugin_meta = array_merge( $plugin_meta, $row_meta );
			}

			return $plugin_meta;
		}

		/**
		 * Add message to show
		 */
		public function add_message( $message ) {
			$messages = get_user_option( $this->messages_option_name );

			if ( empty( $messages ) ) {
				$messages = array();
			}

			if ( ! is_array( $messages ) ) {
				// Here let's check if it's serialized, so we can convert to array.
				$unserialized_messages = maybe_unserialize( $messages );

				$messages = is_array( $unserialized_messages ) ? $unserialized_messages : array();
			}

			if ( ! in_array( $message, $messages ) ) {
				$user_id    = get_current_user_id();
				$messages[] = $message;
				update_user_option( $user_id, $this->messages_option_name, $messages, false );
			}
		}

		/**
		 * Add admin notice from option.
		 *
		 * @since 3.4
		 */
		public function add_admin_notices( $texts ) {
			$screen = get_current_screen();
			if ( ! preg_match( '/_page_branding/', $screen->id ) ) {
				return $texts;
			}
			$messages = get_user_option( $this->messages_option_name );
			if ( empty( $messages ) ) {
				return $texts;
			}
			$fire_delete = false;
			foreach ( $messages as $message ) {
				if ( ! isset( $message['message'] ) || empty( $message['message'] ) ) {
					continue;
				}
				$fire_delete              = true;
				$texts['admin_notices'][] = $message;
			}
			if ( $fire_delete ) {
				add_action( 'shutdown', array( $this, 'delete_messages' ) );
			}

			return $texts;
		}

		/**
		 * delete messages
		 *
		 * @since 3.1.0
		 */
		public function delete_messages() {
			$user_id = get_current_user_id();
			delete_user_option( $user_id, $this->messages_option_name, false );
		}

		public function setup_translation() {
			// Load up the localization file if we're using WordPress in a different language
			// Place it in this plugin's "languages" folder and name it "mp-[value in wp-config].mo"
			$dir = sprintf( '/%s/languages', basename( branda_dir( '' ) ) );
			load_plugin_textdomain( 'ub', false, $dir );
		}

		/**
		 * Check user permissions
		 *
		 * @return boolean
		 */
		private function check_user_access() {
			return Branda_Permissions::get_instance()->current_user_has_access();
		}

		public function add_admin_header_core() {
			/**
			 * Filter allow to avoid run wp_enqueue* functions.
			 *
			 * @since 3.0.0
			 * @param boolean $add Load assets or not load - it is a question.
			 */
			$add = apply_filters( 'branda_add_admin_header_core', true );
			if ( ! $add ) {
				return;
			}

			global $wp_version;
			if ( version_compare( $wp_version, '5.2', '<' ) && ! wp_script_is( 'clipboard' ) ) {
				wp_register_script(
					'clipboard',
					branda_url( 'external/clipboard/clipboard.js' ),
					array(),
					$this->build,
					true
				);
			}
			wp_register_script(
				'branda-sui-ace',
				branda_url( 'external/ace/ace.js' ),
				array(),
				$this->build,
				true
			);
			wp_register_script(
				'branda-sui-a11y-dialog',
				branda_url( 'external/a11y-dialog/a11y-dialog.js' ),
				array(),
				$this->build,
				true
			);
			wp_register_script(
				'branda-sui-select2',
				branda_url( 'external/select2/select2.full.js' ),
				array(),
				$this->build,
				true
			);

			/**
			 * Shared UI
			 *
			 * @since 3.0.0
			 */
			if ( defined( 'BRANDA_SUI_VERSION' ) ) {
				$sanitize_version = str_replace( '.', '-', BRANDA_SUI_VERSION );
				$sui_body_class   = "sui-$sanitize_version";
				wp_register_script(
					'sui-scripts',
					branda_url( 'assets/js/shared-ui.min.js' ),
					array( 'jquery', 'clipboard', 'branda-sui-ace', 'branda-sui-a11y-dialog', 'branda-sui-select2' ),
					$sui_body_class,
					true
				);
				wp_enqueue_style(
					'sui-styles',
					branda_url( 'assets/css/shared-ui-style.min.css' ),
					array(),
					$sui_body_class
				);
			}
			// Add in the core CSS file
			$file = branda_url( 'assets/css/ultimate-branding-admin-style.min.css' );
			wp_enqueue_style( 'branda-admin', $file, array(), $this->build );
			wp_enqueue_script(
				array(
					'jquery-ui-sortable',
				)
			);
			$file = sprintf( 'assets/js/ultimate-branding-admin%s.js', defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min' );
			wp_enqueue_script(
				'ub_admin',
				branda_url( $file ),
				array(
					'jquery',
					'jquery-effects-highlight',
					'sui-scripts',
					'underscore',
					'wp-util',
				),
				$this->build,
				true
			);
			wp_enqueue_style( 'wp-color-picker' );
			$file = branda_url( 'external/wp-color-picker-alpha/wp-color-picker-alpha.min.js' );
			wp_enqueue_script( 'wp-color-picker-alpha', $file, array( 'wp-color-picker' ), '2.1.3', true );
			$color_picker_strings = array(
				'clear'            => __( 'Clear', 'ub' ),
				'clearAriaLabel'   => __( 'Clear color', 'ub' ),
				'defaultString'    => __( 'Default', 'ub' ),
				'defaultAriaLabel' => __( 'Select default color', 'ub' ),
				'pick'             => __( 'Select Color', 'ub' ),
				'defaultLabel'     => __( 'Color value', 'ub' ),
			);
			wp_localize_script( 'wp-color-picker-alpha', 'wpColorPickerL10n', $color_picker_strings );

			/**
			 * Messages
			 */
			$messages = array(
				'messages' => array(
					'copy'    => array(
						'confirm'      => __( 'Are you sure to replace all section data?', 'ub' ),
						'select_first' => __( 'Please select a source module first.', 'ub' ),
					),
					'reset'   => array(
						'module' => __( 'Are you sure? This will replace all entered data by defaults.', 'ub' ),
					),
					'welcome' => array(
						'empty' => __( 'Please select some modules first or skip this step.', 'ub' ),
					),
					'form'    => array(
						'number' => array(
							'max' => __( 'Entered value is above field limit!', 'ub' ),
							'min' => __( 'Entered value is below field limit!', 'ub' ),
						),
					),
					'unsaved' => __( 'Changes are not saved, are you sure you want to navigate away?', 'ub' ),
					'feeds'   => array(
						'fetch' => esc_html__( 'Try to fetch feeds data, please wait...', 'ub' ),
						'no'    => esc_html__( 'No feed found, try to another site or enter data manually.', 'ub' ),
					),
					'export'  => array(
						'not_json' => esc_html__( 'Whoops, only .json filetypes are allowed.', 'ub' ),
					),
					'common'  => array(
						'only_image' => esc_html__( 'Whoops, only images are allowed.', 'ub' ),
					),
				),
				'buttons'  => array(
					'save_changes' => __( 'Save Changes', 'ub' ),
				),
			);
			foreach ( $this->messages as $key => $value ) {
				$messages['messages'][ $key ] = $value;
			}
			/**
			 * Filter messages array
			 *
			 * @since 3.0.0
			 */
			$messages = apply_filters( 'branda_admin_messages_array', $messages );
			wp_localize_script( 'ub_admin', 'ub_admin', $messages );
		}

		public function add_admin_header_branding() {
			$this->add_admin_header_core();
			do_action( 'ultimatebranding_admin_header_global' );
			$update = apply_filters( 'ultimatebranding_update_branding_page', true );
			if ( $update ) {
				$this->update_branding_page();
			}
		}

		/**
		 * Set module status from "Manage All Modules" page.
		 *
		 * @since 3.0.0
		 */
		public function ajax_bulk_modules() {
			$fields = array( 'branda', 'nonce' );
			foreach ( $fields as $field ) {
				if ( ! isset( $_POST[ $field ] ) ) {
					$args = array(
						'message' => $this->messages['missing'],
					);
					wp_send_json_error( $args );
				}
			}
			if (
				! wp_verify_nonce( $_POST['nonce'], 'branda-manage-all-modules' )
				&& ! wp_verify_nonce( $_POST['nonce'], 'branda-welcome-activate' )
			) {
				$args = array(
					'message' => $this->messages['security'],
				);
				wp_send_json_error( $args );
			}
			$modules = $_POST['branda'];
			if ( ! is_array( $modules ) ) {
				$modules = array();
			}
			$activated = $deactivated = 0;
			foreach ( $this->configuration as $key => $module ) {
				if ( isset( $module['instant'] ) && $module['instant'] ) {
					continue;
				}
				$is_active = branda_is_active_module( $key );
				if ( in_array( $module['module'], $modules ) ) {
					if ( ! $is_active ) {
						$this->activate_module( $key );
						$activated++;
					}
				} else {
					if ( $is_active ) {
						$this->deactivate_module( $key );
						$deactivated++;
					}
				}
			}
			$message = '';
			if ( 0 < $activated ) {
				$message .= sprintf(
					_n(
						'%d new module was activated successfully.',
						'%d new modules was activated successfully.',
						$activated,
						'ub'
					),
					number_format_i18n( $activated )
				);
				if ( 0 < $deactivated ) {
					$message .= ' ';
				}
			}
			if ( 0 < $deactivated ) {
				$message .= sprintf(
					_n(
						'%d module was deactivated successfully.',
						'%d modules was deactivated successfully.',
						$deactivated,
						'ub'
					),
					number_format_i18n( $deactivated )
				);
			}
			/**
			 * Speciall message, when was nothing to do!
			 */
			if ( 0 === $activated && 0 === $deactivated ) {
				$args             = array(
					'type'        => 'info',
					'can_dismiss' => true,
					'message'     => sprintf(
						'<q>%s</q> &mdash; <i>%s</i>',
						esc_html__( '42: The answer to life, the universe and everything.', 'ub' ),
						esc_html__( 'Douglas Adams', 'ub' )
					),
				);
				$args['message'] .= '<br >';
				$args['message'] .= __( 'Nothing was changed, nothing to activate or deactivate.', 'ub' );
				wp_send_json_error( $args );
			}
			if ( empty( $message ) ) {
				$args = array(
					'message' => $this->messages['wrong'],
				);
				wp_send_json_error( $args );
			}
			$message = array(
				'type'    => 'success',
				'message' => $message,
			);
			$this->add_message( $message );
			wp_send_json_success();
		}

		/**
		 * Check plugins those will be used if they are active or not
		 */
		public function load_modules() {
			// Configuration has already been set in `Branda_Loader` class.
			//$this->set_configuration();
			$repeat_config = false;

			// Load our remaining modules here
			foreach ( $this->modules as $module => $plugin ) {
				if ( branda_is_active_module( $module ) ) {
					if ( ! isset( $this->configuration[ $module ] ) ) {
						continue;
					}
					if ( $this->should_be_module_off( $this->configuration[ $module ] ) ) {
						continue;
					}
					branda_load_single_module( $module );
					
					if ( 'utilities/text-replacement.php' === $module ) {
						$repeat_config = true;
					}
				}
			}

			if ( $repeat_config ) {
				// We can set config again so the plugin's strings get replaced by Text Replacement module.
				$this->set_configuration();
			}

			/**
			 * set related
			 *
			 * @since 2.3.0
			 */
			$this->related = apply_filters( 'ultimate_branding_related_modules', $this->related );
		}

		/**
		 * add bold
		 *
		 * @since 2.1.0
		 */
		private function bold( $a ) {
			return sprintf( '"<b>%s</b>"', $a );
		}

		/**
		 * Separate logo
		 *
		 * @since 3.0.0
		 */
		public function get_u_logo() {
			$svg = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M17.23 8.06L11.17 2H2V11.1L8.85999 17.2L17.23 8.06ZM0 0H12L20 8L9 20L0 12V0ZM3.66664 3.25281C3.91331 3.08799 4.20334 3 4.50001 3C4.89784 3 5.27938 3.15803 5.56068 3.43933C5.84199 3.72064 6.00001 4.10218 6.00001 4.5C6.00001 4.79667 5.91203 5.0867 5.7472 5.33337C5.58238 5.58005 5.34814 5.77227 5.07405 5.8858C4.79996 5.99933 4.49832 6.02907 4.20735 5.97119C3.91638 5.91331 3.64912 5.77045 3.43934 5.56067C3.22956 5.35089 3.0867 5.08364 3.02882 4.79266C2.97094 4.50169 3.00068 4.20005 3.11421 3.92596C3.22774 3.65188 3.41997 3.41763 3.66664 3.25281Z" fill="#F0F6FC"/></svg>';
			$icon = 'data:image/svg+xml;base64,' . base64_encode( $svg );
			return $icon; // phpcs:ignore -- base64_encode is harmless here
		}

		/**
		 * Add main menu
		 *
		 * @since 2.0.0
		 *
		 * @param string $capability Capability.
		 */
		private function menu( $capability ) {
			$parent_menu_title = Branda_Helper::is_pro() ? __( 'Branda Pro', 'ub' ) : __( 'Branda', 'ub' );

			// Add in our menu page
			$this->top_page_slug = add_menu_page(
				$parent_menu_title,
				$parent_menu_title,
				$capability,
				'branding',
				array( $this, 'handle_main_page' ),
				$this->get_u_logo()
			);
			add_action( 'admin_init', array( $this, 'add_action_hooks' ) );
			add_action( 'load-' . $this->top_page_slug, array( $this, 'add_admin_header_branding' ) );
			$menu = add_submenu_page(
				'branding',
				__( 'Dashboard', 'ub' ),
				__( 'Dashboard', 'ub' ),
				$capability,
				'branding',
				array( $this, 'handle_main_page' )
			);
			add_action( 'load-' . $menu, array( $this, 'load_dashboard' ) );
			/**
			 * Sort sub menu items.
			 */
			uasort( $this->submenu, array( $this, 'sort_sub_menus' ) );
			/**
			 * Add groups submenus
			 */
			foreach ( $this->submenu as $key => $data ) {
				$show = true;
				if ( $this->is_network && ! is_network_admin() ) {
					$modules = $this->get_modules_by_group( $key );
					$show    = apply_filters( 'branda_group_check_for_subsite', false, $key, $modules );
				}
				if ( ! $show ) {
					continue;
				}
				$menu = add_submenu_page(
					'branding',
					$data['title'],
					$data['title'],
					$capability,
					sprintf( 'branding_group_%s', esc_attr( $key ) ),
					array( $this, 'handle_group' )
				);
				add_action( 'load-' . $menu, array( $this, 'add_admin_header_branding' ) );
			}

			if ( ! Branda_Helper::is_member() ) {
				$menu = add_submenu_page(
					'branding',
					__( 'Branda Pro', 'ub' ),
					__( 'Branda Pro', 'ub' ),
					$capability,
					'branda_pro',
					array( $this, 'handle_branda_pro' )
				);
				add_action( 'load-' . $menu, array( $this, 'add_admin_header_branding' ) );
			}

			do_action( 'ultimate_branding_add_menu_pages' );
		}

		/**
		 * Add pages
		 */
		public function admin_page() {
			/**
			 * Check show?
			 */
			$show = true;
			if ( $this->is_network ) {
				$show = false;
				foreach ( $this->submenu as $key => $data ) {
					if ( $show ) {
						continue;
					}
					$modules = $this->get_modules_by_group( $key );
					$show    = apply_filters( 'branda_group_check_for_subsite', false, $key, $modules );
				}
			}
			if ( $show ) {
				// Check user permissions
				if ( ! $this->check_user_access() ) {
					return;
				}
				$this->menu( 'read' );
			}
			self::$is_show_admin_menu = (bool) $show;
		}

		/**
		 * Add pages
		 */
		public function network_admin_page() {
			if ( $this->is_network && $this->check_user_access() ) {
				$this->menu( 'read' );
			}
		}

		/**
		 * Sort admin sub menus.
		 *
		 * We need to make sure the main dashboard menu
		 * gets the first priority.
		 *
		 * @param mixed $a
		 * @param mixed $b
		 *
		 * @return int
		 */
		private function sort_sub_menus( $a, $b ) {
			if ( isset( $b['menu-position'] ) && 'bottom' === $b['menu-position'] ) {
				return -1;
			}
			if ( isset( $a['menu-position'] ) && 'bottom' === $a['menu-position'] ) {
				return 1;
			}
			return strcasecmp( $a['title'], $b['title'] );
		}

		public function activate_module( $module ) {
			$update  = true;
			$modules = get_branda_activated_modules();
			if (
				isset( $modules[ $module ] )
			) {
				if ( 'yes' !== $modules[ $module ] ) {
					$update             = true;
					$modules[ $module ] = 'yes';
				}
			} else {
				$update             = true;
				$modules[ $module ] = 'yes';
			}
			if ( $update ) {
				$modules[ $module ] = 'yes';
				update_branda_activated_modules( $modules );
				branda_load_single_module( $module );
				do_action( 'branda_module_activated', $module );
				return true;
			}
			return false;
		}

		public function deactivate_module( $module ) {
			$modules = get_branda_activated_modules();
			if ( isset( $modules[ $module ] ) ) {
				unset( $modules[ $module ] );
				update_branda_activated_modules( $modules );
				do_action( 'branda_module_deactivated', $module );
				return true;
			}
			return false;
		}

		public function update_branding_page() {
			global $action, $page;
			wp_reset_vars( array( 'action', 'page' ) );
			if ( isset( $_REQUEST['action'] ) && ! empty( $_REQUEST['action'] ) ) {
				$t = Branda_Helper::hyphen_to_underscore( $this->module );
				/**
				 * check
				 */
				check_admin_referer( 'ultimatebranding_settings_' . $t );
				$result = apply_filters( 'ultimatebranding_settings_' . $t . '_process', true );
				$url    = wp_validate_redirect( wp_get_raw_referer() );
				if ( is_array( $result ) ) {
					$url = add_query_arg( $result, $url );
				}
				wp_safe_redirect( $url );
				do_action( 'ultimatebranding_settings_update_' . $t );
			}
		}

		/**
		 * Helper to build link
		 *
		 * @since 3.0.0
		 */
		private function get_module_link( $module ) {
			$url  = add_query_arg(
				array(
					'page'   => sprintf( 'branding_group_%s', $module['group'] ),
					'module' => $module['module'],
				),
				is_network_admin() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' )
			);
			$link = sprintf(
				'<a href="%s" class="branda-module branda-module-%s" data-group="%s">%s</a>',
				esc_url( $url ),
				esc_attr( $module['module'] ),
				esc_attr( $module['group'] ),
				esc_html( $module['name'] )
			);
			return $link;
		}

		/**
		 * Helper to get array of modules state.
		 *
		 * @since 3.0.0
		 * @since 3.2.0 Added $subsite param.
		 *
		 * @param boolean $subsite Subsite mode.
		 *
		 * @return array $modules Array of modules, grupped.
		 */
		public function get_modules_stats( $subsite = false ) {
			$modules = array();
			foreach ( $this->configuration as $key => $module ) {
				if ( ! array_key_exists( $key, $this->modules ) ) {
					continue;
				}
				/**
				 * check for subsites
				 */
				if ( $subsite ) {
					$show = apply_filters( 'branda_module_check_for_subsite', false, $key, $module );
					if ( false === $show ) {
						continue;
					}
				}
				if ( ! isset( $modules[ $module['group'] ] ) ) {
					$modules[ $module['group'] ] = array();
				}
				$modules[ $module['group'] ]['modules'][ $key ]           = $module;
				$modules[ $module['group'] ]['modules'][ $key ]['status'] = 'inactive';
				if ( branda_is_active_module( $key ) ) {
					$modules[ $module['group'] ]['modules'][ $key ]['status'] = 'active';
				}
			}
			foreach ( $modules as $group => $data ) {
				$modules[ $group ]['modules'] = $data['modules'];
			}
			return $modules;
		}

		public function handle_branda_pro() {
			add_filter( 'branda_show_manage_all_modules_button', '__return_false' );
			$classes  = apply_filters( 'branda_sui_wrap_class', array(), $this->module );
			$template = 'admin/branda-pro';
			printf(
				'<main class="%s">',
				esc_attr( implode( ' ', $classes ) )
			);
			$this->render( $template );

			$this->footer();

			echo '</main>';
		}

		public function handle_main_page() {
			if ( $this->is_network && ! is_network_admin() ) {
				$this->handle_main_page_subsite();
				return;
			}
			$this->handle_main_page_global();
		}

		private function handle_main_page_global() {
			$stats              = $this->stats->get_stats();
			$recently_activated = $recently_deactivated = __( 'none', 'ub' );
			if ( isset( $stats['activites'] ) ) {
				if (
					isset( $stats['activites']['activate'] )
					&& isset( $this->configuration[ $stats['activites']['activate'] ] )
				) {
					$recently_activated = $this->get_module_link(
						$this->configuration[ $stats['activites']['activate'] ]
					);
				}
				if (
					isset( $stats['activites']['deactivate'] )
					&& isset( $this->configuration[ $stats['activites']['deactivate'] ] )
				) {
					$recently_deactivated = $this->get_module_link(
						$this->configuration[ $stats['activites']['deactivate'] ]
					);
				}
			}
			$args = array(
				'stats'                          => array(
					'active'               => 0,
					'total'                => 0,
					'recently_activated'   => $recently_activated,
					'recently_deactivated' => $recently_deactivated,
					'frequently_used'      => array(),
					'modules'              => $this->stats->get_frequently_used_modules(),
					'raw'                  => $this->stats->get_modules_raw_data(),
				),
				'show_manage_all_modules_button' => $this->show_manage_all_modules_button(),
				'helps'                          => $this->get_helps_list(),
			);
			if ( $args['stats']['modules'] ) {
				foreach ( $args['stats']['modules'] as $key => $value ) {
					if ( ! array_key_exists( $key, $this->modules ) ) {
						continue;
					}
					if ( isset( $this->configuration[ $key ] ) ) {
						$args['stats']['modules'][ $key ]           = $this->configuration[ $key ];
						$args['stats']['modules'][ $key ]['status'] = 'inactive';
						if ( branda_is_active_module( $key ) ) {
							$args['stats']['modules'][ $key ]['status'] = 'active';
						}
					} else {
						unset( $args['stats']['modules'][ $key ] );
					}
				}
			}
			/**
			 * Count
			 */
			foreach ( $this->configuration as $key => $module ) {
				if ( ! array_key_exists( $key, $this->modules ) ) {
					continue;
				}
				if ( branda_is_active_module( $key ) ) {
					if ( isset( $module['instant'] ) && $module['instant'] ) {
						continue;
					}
					$args['stats']['active']++;
				}
				$args['stats']['total']++;
			}
			/**
			 * Modules Status
			 */
			$args['modules'] = $this->get_modules_stats();
			/**
			 * groups
			 */
			$args['groups'] = branda_get_groups_list();
			/**
			 * SUI
			 */
			$args['sui']                         = array(
				'summary' => array(
					'style'   => $this->get_box_summary_image_style(),
					'classes' => array(
						'sui-box',
						'sui-summary',
					),
				),
			);
			$args['sui']['summary']['classes'][] = $this->get_hide_branding_class();
			/**
			 * render
			 */
			$classes  = apply_filters( 'branda_sui_wrap_class', array(), $this->module );
			$template = 'admin/dashboard';
			printf(
				'<main class="%s">',
				implode( ' ', $classes )
			);
			$this->render( $template, $args );
			if ( $this->show_welcome_dialog ) {
				$args     = array(
					'dialog_id' => 'branda-welcome',
					'modules'   => $args['modules'],
					'groups'    => branda_get_groups_list(),
				);
				$template = 'admin/dashboard/welcome';
				$this->render( $template, $args );
			}
			$this->footer();
			echo '</main>';
		}

		/**
		 * Dash integration
		 *
		 * @since 3.2.0
		 * @return string Class name
		 */
		public function get_hide_branding_class() {
			$class          = '';
			$hide_branding  = apply_filters( 'wpmudev_branding_hide_branding', $this->hide_branding );
			$branding_image = apply_filters( 'wpmudev_branding_hero_image', null );
			if ( $hide_branding && ! empty( $branding_image ) ) {
				$class = 'sui-rebranded';
			} elseif ( $hide_branding && empty( $branding_image ) ) {
				$class = 'sui-unbranded';
			}

			return $class;
		}

		/**
		 * Handle Dashboard for subsites.
		 *
		 * @since 3.2.0
		 */
		private function handle_main_page_subsite() {
			$modules = $this->get_modules_stats( true );
			$count   = 0;
			foreach ( $modules as $group ) {
				if ( isset( $group['modules'] ) && is_array( $group['modules'] ) ) {
					$count += count( $group['modules'] );
				}
			}
			$args = array(
				'stats'                          => array(
					'active'               => $count,
					'total'                => 0,
					'recently_activated'   => '',
					'recently_deactivated' => '',
					'frequently_used'      => array(),
					'modules'              => $this->stats->get_frequently_used_modules( 'subsite' ),
					'raw'                  => $this->stats->get_modules_raw_data(),
				),
				'show_manage_all_modules_button' => $this->show_manage_all_modules_button(),
				'helps'                          => $this->get_helps_list(),
				'groups'                         => branda_get_groups_list(),
				'modules'                        => $modules,
				'message'                        => apply_filters(
					'branda_subsites_dashboard_message',
					array(
						'url'  => $this->get_network_permissions_url(),
						'show' => true,
					)
				),
			);
			if ( $args['stats']['modules'] ) {
				foreach ( $args['stats']['modules'] as $key => $value ) {
					if ( ! array_key_exists( $key, $this->modules ) ) {
						continue;
					}
					if ( isset( $this->configuration[ $key ] ) ) {
						$args['stats']['modules'][ $key ]           = $this->configuration[ $key ];
						$args['stats']['modules'][ $key ]['status'] = 'inactive';
						if ( branda_is_active_module( $key ) ) {
							$args['stats']['modules'][ $key ]['status'] = 'active';
						}
					} else {
						unset( $args['stats']['modules'][ $key ] );
					}
				}
			}
			/**
			 * render
			 */
			$classes  = apply_filters( 'branda_sui_wrap_class', array(), $this->module );
			$template = 'admin/dashboard/subsite';
			printf(
				'<main class="%s">',
				implode( ' ', $classes )
			);
			$this->render( $template, $args );
			$this->footer();
			echo '</main>';
		}

		/**
		 * Show group page
		 *
		 * @since 3.0.0
		 */
		public function handle_group() {
			$classes = array(
				sprintf( 'sui-wrap-branda-module-%s', $this->module ),
			);
			$classes = apply_filters( 'branda_sui_wrap_class', $classes, $this->module );
			printf( '<main class="%s">', implode( ' ', $classes ) );
			$content = apply_filters( 'branda_handle_group_page', '', $this->module );
			if ( ! empty( $content ) ) {
				echo $content;
			} else {
				/**
				 * Common header
				 */
				$args = array(
					'title'                          => $this->get_current_group_title(),
					'show_manage_all_modules_button' => $this->show_manage_all_modules_button(),
					'documentation_chapter'          => $this->get_current_group_documentation_chapter(),
					'helps'                          => $this->get_helps_list(),
				);
				$this->render( 'admin/common/header', $args );
				/**
				 * Content
				 */
				echo '<div class="sui-row-with-sidenav">';
				echo '<div class="sui-sidenav">';
				$this->group_tabs( 'menu' );
				echo '</div>'; // sui-sidenav
				$this->group_tabs( 'content' );
				echo '</div>'; // sui-row-with-sidenav
			}
			$this->footer();
			echo '</main>';
		}

		/**
		 * Helper to show group
		 *
		 * @since 3.0.0
		 */
		private function group_tabs( $type ) {
			$modules = $this->get_modules_by_group( null, true );
			if ( is_wp_error( $modules ) ) {
				if ( 'content' === $type ) {
					$error_string = $modules->get_error_message();
					echo '<div class="error"><p>' . $error_string . '</p></div>';
				}
				return;
			}
			/**
			 * Get current module or set first
			 */
			$current = $modules[ key( $modules ) ]['module'];
			if ( ! empty( $this->module ) ) {
				$current = $this->module;
			}
			$content = '';
			switch ( $type ) {
				case 'menu':
					$content = $this->group_tabs_menu( $modules, $current );
					break;
				case 'content':
					$content = $this->group_tabs_content( $modules, $current );
					break;
				default:
					break;
			}
			echo $content;
		}

		private function group_tabs_menu( $modules, $current ) {
			$tabs   = '';
			$select = '';
			foreach ( $modules as $id => $module ) {
				$slug  = $module['module'];
				$title = $module['name'];
				if ( isset( $module['title'] ) ) {
					$title = $module['title'];
				}
				if ( isset( $module['menu_title'] ) ) {
					$title = $module['menu_title'];
				}
				if ( ! empty( $module['only_pro'] ) ) {
					$icon = Branda_Helper::maybe_pro_tag();
				} else {
					unset( $icon );
				}
				/**
				 * Active?
				 */
				if ( empty( $icon ) ) {
					$icon = branda_is_active_module( $id ) ? '<i class="sui-icon-check-tick"></i>' : '';
				}

				if ( isset( $module['instant'] ) && 'on' === $module['instant'] ) {
					$icon = '';
				}
				$tabs   .= sprintf(
					'<li class="sui-vertical-tab %s"><a href="#" data-tab="%s">%s%s</a></li>',
					esc_attr( $current === $slug ? 'current' : '' ),
					sanitize_title( $slug ),
					esc_html( $title ),
					$icon
				);
				$select .= sprintf(
					'<option %s value="%s">%s</option>',
					esc_attr( $current === $slug ? 'selected="selected' : '' ),
					sanitize_title( $slug ),
					esc_html( $title )
				);
			}
			$content  = '<ul class="sui-vertical-tabs sui-sidenav-hide-md">';
			$content .= $tabs;
			$content .= '</ul>';
			$content .= '<div class="sui-sidenav-hide-lg">';
			$content .= '<select class="sui-mobile-nav" id="branda-mobile-nav" style="display: none;">';
			$content .= $select;
			$content .= '</select>';
			$content .= '</div>';
			return $content;
		}

		private function group_tabs_content( $modules, $current ) {
			$content               = '';
			$some_module_is_active = false;
			$show_deactivate       = true;
			if ( $this->is_network && ! $this->is_network_admin ) {
				$show_deactivate = false;
			};
			foreach ( $modules as $id => $module ) {
				$slug      = $module['module'];
				$is_active = branda_is_active_module( $module['key'] );
				/**
				 * Hide options if subsites configuration
				 */
				$has_susbsite_configuration = false;
				if ( $this->is_network && $this->is_network_admin ) {
					$subsite = apply_filters( 'branda_module_check_for_subsite', false, $id, $module );
					if ( $subsite ) {
						$has_susbsite_configuration = true;
					}
				}
				$module_name = Branda_Helper::hyphen_to_underscore( $module['module'] );
				$action      = 'ultimatebranding_settings_' . $module_name;
				/**
				 * Module header
				 *
				 * hide for instant active modules
				 */
				$show_module_header = true;
				if ( isset( $module['instant'] ) && 'on' === $module['instant'] ) {
					$show_module_header = false;
				}
				if ( $show_module_header ) {
					$classes = array(
						'sui-box',
						'branda-settings-tab',
						sprintf( 'branda-settings-tab-%s', sanitize_title( $slug ) ),
						sprintf( 'branda-settings-tab-title-%s', sanitize_title( $slug ) ),
						'branda-settings-tab-title',
					);
					$buttons = '';
					if ( $is_active ) {
						$template  = 'admin/common/modules/header';
						$classes[] = 'sui-box-sticky';
						/**
						 * deactivate button
						 */
						if (
							$show_deactivate
							&& (
								! isset( $module['instant'] ) || 'on' !== $module['instant']
							)
						) {
							$args     = array(
								'data'  => array(
									'nonce' => wp_create_nonce( $slug ),
									'slug'  => $slug,
								),
								'class' => 'ub-deactivate-module',
								'text'  => __( 'Deactivate', 'ub' ),
								'sui'   => 'ghost',
							);
							$buttons .= $this->button( $args );
						}
						/**
						 * submit button
						 */
						$filter = $action . '_process';
						if (
							has_filter( $filter )
							&& apply_filters( 'ultimatebranding_settings_panel_show_submit', true, $module )
						) {
							$args     = array(
								'text'  => __( 'Save Changes', 'ub' ),
								'sui'   => 'blue',
								'icon'  => 'save',
								'class' => 'branda-module-save',
							);
							$buttons .= $this->button( $args );
						}
					} else {
						$template = '/admin/modules/' . $module['module'] . '/module-inactive';
						if ( ! self::get_template_file_name( $template ) ) {
							// If module custom template doesn't exist - use common one.
							$template = 'admin/common/module-inactive';
						}
						/**
						 * activate button
						 */
						$args    = array(
							'data'  => array(
								'nonce' => wp_create_nonce( $slug ),
								'slug'  => $slug,
							),
							'class' => 'ub-activate-module',
							'sui'   => 'blue',
							'text'  => __( 'Activate', 'ub' ),
						);
						$buttons = $this->button( $args );
					}
					$status_indicator = isset( $module['status-indicator'] ) ? $module['status-indicator'] : 'show';
					$args             = array(
						'box_title'                  => isset( $module['name_alt'] ) ? $module['name_alt'] : $module['name'],
						'classes'                    => $classes,
						'module'                     => $module,
						'copy_button'                => $this->get_copy_button( $module ),
						'buttons'                    => $buttons,
						'slug'                       => $slug,
						'current'                    => $current,
						'status_indicator'           => $status_indicator,
						'has_susbsite_configuration' => $has_susbsite_configuration,
					);
					$content         .= $this->render( $template, $args, true );
				}
				/**
				 * body
				 */
				if ( $is_active ) {
					$classes  = array(
						'sui-box',
						'branda-settings-tab',
						sprintf( 'branda-settings-tab-%s', sanitize_title( $slug ) ),
						'branda-settings-tab-content',
						sprintf( 'branda-settings-tab-content-%s', sanitize_title( $slug ) ),
					);
					$classes  = apply_filters( 'branda_settings_tab_content_classes', $classes, $module );
					$content .= sprintf(
						'<div class="%s" data-tab="%s"%s>',
						esc_attr( implode( ' ', $classes ) ),
						esc_attr( sanitize_title( $slug ) ),
						$current === $slug ? '' : ' style="display: none;"'
					);
					/**
					 * Show module content
					 */
					$show_module_content = true;
					if ( $has_susbsite_configuration ) {
						$show_module_content = false;
					}
					if ( ! $show_module_content ) {
						$show_message = true;
						if (
							isset( $module['allow-override-message'] )
							&& 'hide' === $module['allow-override-message']
						) {
							$show_message = false;
						}
						if ( $show_message ) {
							$template = 'admin/common/modules/subsite-configuration';
							$args     = array(
								'url' => $this->get_network_permissions_url(),
							);
							$content .= $this->render( $template, $args, true );
						}
					}
					$module_content = $this->get_module_content( $module );
					if ( is_wp_error( $module_content ) ) {
						$content .= '<div class="sui-box-body">';
						$content .= Branda_Helper::sui_notice( $module_content->get_error_message() );
						$content .= '</div>'; // sui-box-body
					} else {
						$content .= $module_content;
					}
					$content .= '</div>'; // sui-box
				}
				if ( $current === $slug ) {
					$some_module_is_active = true;
				}
			}
			if ( ! $some_module_is_active ) {
				$template = 'admin/common/no-module';
				$content .= $this->render( $template, array(), true );
			}
			return "<div>{$content}</div>";
		}

		/**
		 * Show New feature dialog if it's available to show
		 *
		 * @return null
		 */
		private function maybe_show_new_feature_dialog() {
			if ( ! Branda_Helper::is_full_pro() ) {
				return;
			}

			$major_minor_version = $this->get_major_minor_version();
			if ( $this->to_major_minor( $this->get_first_installed_version() ) === $major_minor_version ) {
				// Only need to show after an upgrade, not fresh installation
				return;
			}

			$meta_key = 'branda_hide_new_features';

			$dismissed_dialog_version = get_user_meta( get_current_user_id(), $meta_key, true );

			if ( version_compare( $major_minor_version, $dismissed_dialog_version, '<=' ) ) {
				return;
			}

			$template_suffix = str_replace( '.', '', $major_minor_version );
			$template        = 'admin/common/dialogs/show-new-features-' . $template_suffix;
			$this->render( $template, array() );
		}

		/**
		 * Add notice template and footer "In love by WPMU DEV".
		 *
		 * @since 3.0.0
		 */
		private function footer() {
			$show = $this->show_manage_all_modules_button();
			if ( $show ) {
				/**
				 * Modules Status & Manage All Modules
				 */
				$args     = array(
					'modules' => $this->get_modules_stats(),
					'groups'  => branda_get_groups_list(),
				);
				$template = 'admin/common/dialogs/manage-all-modules';
				$this->render( $template, $args );
			}

			$this->maybe_show_new_feature_dialog();

			$hide_footer = false;
			$footer_text = sprintf( __( 'Made with %s by WPMU DEV', 'ub' ), ' <i class="sui-icon-heart"></i>' );
			if ( Branda_Helper::is_member() ) {
				$hide_footer = apply_filters( 'wpmudev_branding_change_footer', $hide_footer );
				$footer_text = apply_filters( 'wpmudev_branding_footer_text', $footer_text );
				$hide_footer = apply_filters( 'branda_change_footer', $hide_footer, $this->module );
				$footer_text = apply_filters( 'branda_footer_text', $footer_text, $this->module );
			}
			$args     = array(
				'hide_footer' => $hide_footer,
				'footer_text' => $footer_text,
			);
			$template = 'admin/common/footer';
			$this->render( $template, $args );
			do_action( 'branda_ubadmin_footer', $this->module );
		}

		/**
		 * Print button save.
		 *
		 * @since 1.8.4
		 * @since 3.0.0 returns value instead of print.
		 */
		public function button_save() {
			$content = sprintf(
				'<p class="submit"><input type="submit" name="submit" class="button-primary" value="%s" /></p>',
				esc_attr__( 'Save Changes', 'ub' )
			);
			return $content;
		}

		/**
		 * Should I show menu in admin subsites?
		 *
		 * @since 1.8.6
		 */
		private function check_show_in_subsites() {
			if ( is_multisite() && is_network_admin() ) {
				return true;
			}
			$modules = get_branda_activated_modules();
			if ( empty( $modules ) ) {
				return false;
			}
			foreach ( $modules as $module => $state ) {
				if ( 'yes' != $state ) {
					continue;
				}
				if ( isset( $this->configuration[ $module ] ) ) {
					$state = apply_filters( 'branda_module_check_for_subsite', false, $module, $this->configuration[ $module ] );
					if ( $state ) {
						return $state;
					}
				}
			}
			return false;
		}

		/**
		 * Get module by group.
		 *
		 * @since 3.0.0
		 * @since 3.1.0
		 *
		 * @param string $group group.
		 */
		public function get_modules_by_group( $group = null, $filter = false ) {
			global $branda_network;
			if ( null === $group ) {
				$group = $this->group;
			}
			$modules = array();
			foreach ( $this->configuration as $key => $module ) {
				if ( ! array_key_exists( $key, $this->modules ) ) {
					continue;
				}
				if ( ! isset( $module['group'] ) ) {
					continue;
				}
				if ( $group == $module['group'] ) {
					$modules[ $key ] = $module;
				}
			}
			/**
			 * Filter
			 */
			if ( $branda_network && $filter ) {
				$is_network_admin = is_network_admin();
				if ( ! $is_network_admin ) {
					$m = array();
					foreach ( $modules as $key => $module ) {
						$show = apply_filters( 'branda_module_check_for_subsite', false, $key, $module );
						if ( $show ) {
							$m[ $key ] = $module;
						}
					}
					$modules = $m;
				}
			}
			if ( empty( $modules ) ) {
				return new WP_Error( 'error', __( 'There is no modules in selected group!', 'ub' ) );
			}

			return $modules;
		}

		/**
		 * get nonced url
		 *
		 * @since 1.8.8
		 */
		private function get_nonce_url( $module ) {
			$page         = $this->get_current_page();
			$is_active    = branda_is_active_module( $module );
			$url          = add_query_arg(
				array(
					'page'   => $page,
					'action' => $is_active ? 'disable' : 'enable',
					'module' => $module,
				),
				is_network_admin() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' )
			);
			$nonce_action = sprintf( '%s-module-%s', $is_active ? 'disable' : 'enable', $module );
			$url          = wp_nonce_url( $url, $nonce_action );
			return $url;
		}

		/**
		 * Get base url
		 *
		 * @since 1.8.8
		 */
		private function get_base_url() {
			if ( empty( $this->base_url ) ) {
				$page           = $this->get_current_page();
				$this->base_url = add_query_arg(
					array(
						'page' => $page,
					),
					is_network_admin() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' )
				);
			}
			return $this->base_url;
		}

		/**
		 * sanitize variables
		 *
		 * @since 3.0.0
		 */
		public function set_and_sanitize_variables() {
			$this->load_permissions();

			$this->module = '';
			if (
				isset( $_REQUEST['page'] )
				&& preg_match( '/branding_group_(.+)$/', $_REQUEST['page'], $matches )
			) {
				if ( array_key_exists( $matches[1], $this->submenu ) ) {
					$this->group = $matches[1];
				}
			}
			if ( 'dashboard' === $this->group ) {
				return;
			}
			/**
			 * module
			 */
			//$input_module = filter_input( INPUT_POST, 'module', FILTER_SANITIZE_STRING );
			$input_module  = ! empty( $_POST['module'] ) ? sanitize_text_field( $_POST['module'] ) : null;

			if ( empty( $input_module ) ) {
				//$input_module = filter_input( INPUT_GET, 'module', FILTER_SANITIZE_STRING );
				$input_module = ! empty( $_GET[ 'module' ] ) ? sanitize_text_field( $_GET[ 'module' ] ) : null;
			}

			$is_empty = empty( $input_module );
			if ( ! $is_empty ) {
				if ( 'dashboard' !== $input_module ) {
					foreach ( $this->configuration as $module ) {
						if ( isset( $module['module'] ) && $input_module === $module['module'] ) {
							$this->module = $module['module'];
							return;
						}
					}
				}
			}
			/**
			 * module is not requested!
			 */
			$modules = $this->get_modules_by_group( null, true );
			if ( is_wp_error( $modules ) ) {
				return;
			}
			/**
			 * try to find active one first
			 */
			$mods = $modules;
			while ( empty( $this->module ) && $module = array_shift( $mods ) ) {
				$is_active = branda_is_active_module( $module['key'] );
				if ( $is_active ) {
					$this->module = $module['module'];
				}
			}
			/**
			 * Set first module as current module.
			 */
			if ( empty( $this->module ) && is_array( $modules ) && ! empty( $modules ) ) {
				$module_data  = array_shift( $modules );
				$this->module = $module_data['module'];
			}
		}

		/**
		 * get group
		 *
		 * @since x.x.x
		 */
		public function get_current_group() {
			return $this->group;
		}

		/**
		 * Dismiss New Feature dialogs.
		 */
		public function new_feature_dismiss() {
			$dialog_id = filter_input( INPUT_POST, 'id' );
			$nonce     = filter_input( INPUT_POST, '_ajax_nonce' );
			if ( ! ( $nonce && $dialog_id ) ) {
				wp_send_json_error( array( 'message' => $this->messages['wrong'] ) );
			}

			check_ajax_referer( 'new-feature' );
			$user_id  = get_current_user_id();
			$meta_key = 'branda_hide_new_features';

			update_user_meta( $user_id, $meta_key, $this->get_major_minor_version() );
		}

		private function get_major_minor_version() {
			return $this->to_major_minor( $this->build );
		}

		private function to_major_minor( $version ) {
			if ( substr_count( $version, '.' ) > 1 ) {
				list( $major, $minor, $patch ) = explode( '.', $version );
				return "{$major}.{$minor}";
			}

			return $version;
		}

		/**
		 * Activate/deactivate single module AJAX action.
		 *
		 * @since 1.9.6
		 */
		public function toggle_module() {
			if (
				isset( $_POST['nonce'] )
				&& isset( $_POST['state'] )
				&& isset( $_POST['module'] )
			) {
				/**
				 * get module
				 */
				$module_data = $this->get_module_by_module( sanitize_key( $_POST['module'] ) );
				if ( is_wp_error( $module_data ) ) {
					$message = array(
						'message' => $module_data->get_error_message(),
					);
					wp_send_json_error( $message );
				}
				if ( ! wp_verify_nonce( $_POST['nonce'], $module_data['module'] ) ) {
					wp_send_json_error( array( 'message' => __( 'Nope! Security check failed!', 'ub' ) ) );
				}
				$result  = false;
				$message = array(
					'message' => $this->messages['fail'],
				);
				/**
				 * try to activate or deactivate
				 */
				if ( 'on' == $_POST['state'] ) {
					$result = $this->activate_module( $module_data['key'] );
					if ( $result ) {
						$message = array(
							'type'    => 'success',
							'message' => sprintf(
								__( '%s module is active now.', 'ub' ),
								$this->bold( $module_data['name'] )
							),
						);
					}
				} else {
					$result = $this->deactivate_module( $module_data['key'] );
					if ( $result ) {
						$message = array(
							'type'    => 'success',
							'message' => sprintf(
								__( 'Module %s was deactivated without errors.', 'ub' ),
								$this->bold( $module_data['name'] )
							),
						);
					}
				}
				$this->add_message( $message );
				$data = array(
					'state'  => $result,
					'module' => sanitize_key( $_POST['module'] ),
				);
				wp_send_json_success( $data );
			}
			wp_send_json_error( array( 'message' => $this->messages['wrong'] ) );
		}

		/**
		* Sort module by menu_title or page_title.
		*
		* @since 2.0.0
		*/
		public function sort_modules_by_name( $a, $b ) {
			$an = $a['name'];
			$bn = $b['name'];
			if ( isset( $a['menu_title'] ) ) {
				$an = $a['menu_title'];
			}
			if ( isset( $b['menu_title'] ) ) {
				$bn = $b['menu_title'];
			}
			return strcmp( $an, $bn );
		}

		private function get_module_content( $module ) {
			$is_active = branda_is_active_module( $module['key'] );
			if ( ! $is_active ) {
				return new WP_Error( 'error', __( 'This module is not active!', 'ub' ) );
			}
			/**
			 * Turn off Smush scripts
			 *
			 * @since 3.0.0
			 */
			add_filter( 'wp_smush_enqueue', '__return_false' );
			$content = '';
			/**
			 * Form encoding type
			 */
			$enctype = apply_filters( 'ultimatebranding_settings_form_enctype', 'multipart/form-data' );
			if ( ! empty( $enctype ) ) {
				$enctype = sprintf(
					' enctype="%s"',
					esc_attr( $enctype )
				);
			}
			/**
			 * Fields with form
			 */
			$action   = Branda_Helper::hyphen_to_underscore( 'ultimatebranding_settings_' . $module['module'] );
			$messages = apply_filters( $action . '_messages', $this->messages );
			if ( has_filter( $action ) ) {
				$content .= apply_filters( 'branda_before_module_form', '', $module );
				/**
				 * Filter Branda form classes.
				 *
				 * @since 3.0.0
				 *
				 * @param $classes array Array of Branda form classes.
				 * @param $module array Current module data,
				 */
				$classes  = apply_filters(
					'branda_form_classes',
					array(
						'branda-form',
						sprintf( 'module-%s', sanitize_title( $module['key'] ) ),
						$this->is_network ? 'branda-network' : 'branda-single',
					),
					$module
				);
				$content .= sprintf(
					'<form action="%s" method="%s" class="module-%s"%s>',
					esc_url( remove_query_arg( array( 'module' ) ) ),
					apply_filters( 'ultimatebranding_settings_form_method', 'post' ),
					esc_attr( implode( ' ', $classes ) ),
					$enctype
				);
				$content .= $this->hidden( 'module', $module['module'] );
				$content .= $this->hidden( 'page', $this->get_current_page() );
				if ( apply_filters( 'ultimatebranding_settings_form_add_fields', true ) ) {
					$content .= $this->hidden( 'action', 'process' );
					/**
					 * nonce
					 */
					$content .= wp_nonce_field( $action, '_wpnonce', false, false );
				}
				$content .= apply_filters( $action, '' );
				/**
				 * footer
				 */
				if ( isset( $module['add-bottom-save-button'] ) && $module['add-bottom-save-button'] ) {
					$filter = $action . '_process';
					if (
						has_filter( $filter )
						&& apply_filters( 'ultimatebranding_settings_panel_show_submit', true, $module )
					) {
						$content .= '<div class="sui-box-footer">';
						$content .= '<div class="sui-actions-right">';
						$args     = array(
							'text'  => __( 'Save Changes', 'ub' ),
							'sui'   => 'blue',
							'icon'  => 'save',
							'class' => 'branda-module-save',
						);
						$args     = apply_filters( 'branda_after_form_save_button_args', $args, $module );
						$content .= $this->button( $args );
						$content .= '</div>'; // sui-actions-right
						$content .= '</div>'; // sui-box-header
					}
				}
				$content .= '</form>';
				do_action( 'branda_after_module_form', $module );
			} else {
				$content .= Branda_Helper::sui_notice( $this->messages['wrong'] );
				if ( Branda_Helper::is_debug() ) {
					error_log( 'Missing action: ' . $action );
				}
			}
			/**
			 * filter module content.
			 *
			 * @since 3.0.0
			 *
			 * @param string $content Current module content.
			 * @param array $module Current module.
			 */
			return apply_filters( 'branda_get_module_content', $content, $module );
		}

		/**
		 * SUI button
		 */
		public function button( $args ) {
			$content        = $data = '';
			$add_sui_loader = true;
			/**
			 * add data attributes
			 */
			if ( isset( $args['data'] ) ) {
				foreach ( $args['data'] as $key => $value ) {
					$data .= sprintf(
						' data-%s="%s"',
						sanitize_title( $key ),
						esc_attr( $value )
					);
					if ( 'modal-open' === $key ) {
						$data .= ' data-modal-mask="true"';
					}
				}
			}
			/**
			 * add disabled attribute
			 */
			if ( isset( $args['disabled'] ) && $args['disabled'] ) {
				$data .= ' disabled="disabled"';
			}
			/**
			 * add ID attribute
			 */
			if ( isset( $args['id'] ) ) {
				$data .= sprintf( ' id="%s"', esc_attr( $args['id'] ) );
			}
			/**
			 * add style attribute
			 */
			if ( isset( $args['style'] ) ) {
				$data .= sprintf( ' style="%s"', esc_attr( $args['style'] ) );
			}
			/**
			 * Build classes
			 */
			$classes = array(
				'sui-button',
			);
			if ( isset( $args['only-icon'] ) && true === $args['only-icon'] ) {
				$classes        = array();
				$add_sui_loader = false;
			}
			if ( isset( $args['sui'] ) ) {
				if ( ! empty( $args['sui'] ) ) {
					if ( ! is_array( $args['sui'] ) ) {
						$args['sui'] = array( $args['sui'] );
					}
					foreach ( $args['sui'] as $sui ) {
						$classes[] = sprintf( 'sui-button-%s', $sui );
					}
				} elseif ( false !== $args['sui'] ) {
					$classes[] = 'sui-button-blue';
				}
			}
			if ( ! isset( $args['text'] ) ) {
				$classes[] = 'sui-button-icon';
			}
			if ( isset( $args['class'] ) ) {
				$classes[] = $args['class'];
			}
			if ( isset( $args['classes'] ) && is_array( $args['classes'] ) ) {
				$classes = array_merge( $classes, $args['classes'] );
			}
			/**
			 * Start
			 */
			$content .= sprintf(
				'<button class="%s" %s type="%s">',
				esc_attr( implode( ' ', $classes ) ),
				$data,
				isset( $args['type'] ) ? esc_attr( $args['type'] ) : 'button'
			);
			if ( $add_sui_loader ) {
				$content .= '<span class="sui-loading-text">';
			}
			/**
			 * Icon
			 */
			if ( isset( $args['icon'] ) ) {
				$content .= sprintf(
					'<i class="sui-icon-%s"></i>',
					sanitize_title( $args['icon'] )
				);
			}
			if ( isset( $args['text'] ) ) {
				$content .= esc_attr( $args['text'] );
			} elseif ( isset( $args['value'] ) ) {
				$content .= esc_attr( $args['value'] );
			}
			if ( $add_sui_loader ) {
				$content .= '</span>';
				$content .= '<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>';
			}
			$content .= '</button>';
			/**
			 * Wrap
			 */
			if ( isset( $args['wrap'] ) && is_string( $args['wrap'] ) ) {
				$content = sprintf(
					'<div class="%s">%s</div>',
					esc_attr( $args['wrap'] ),
					$content
				);
			}
			return $content;
		}

		/**
		 * Helper for hidden field.
		 *
		 * @since 3.0.0
		 *
		 * @input string $name HTML form field name.
		 * @input string $value HTML form field value.
		 *
		 * @return string HTML hidden syntax.
		 */
		private function hidden( $name, $value ) {
			return sprintf(
				'<input type="hidden" name="%s" value="%s" />',
				esc_attr( $name ),
				esc_attr( $value )
			);
		}

		/**
		 * Remove default footer on Branda screens.
		 *
		 * @since 3.0.0
		 */
		public function remove_default_footer( $content ) {
			$screen = get_current_screen();
			if (
				is_a( $screen, 'WP_Screen' )
				&& preg_match( '/_page_branding/', $screen->id )
			) {
				remove_filter( 'update_footer', 'core_update_footer' );
				return '';
			}
			return $content;
		}

		/**
		 * Get current module
		 *
		 * @since 3.0.0
		 *
		 * @return string Current module.
		 */
		public function get_current_module() {
			return $this->module;
		}

		/**
		 * Check is current module?
		 *
		 * @since 3.0.0
		 */
		public function is_current_module( $module ) {
			return $this->module === $module;
		}

		/**
		 * reset whole module
		 *
		 * @since 3.0.0
		 */
		public function ajax_reset_module() {
			if (
				isset( $_POST['_wpnonce'] )
				&& isset( $_POST['module'] )
			) {
				/**
				 * get module
				 */
				$module_data = $this->get_module_by_module( sanitize_key( $_POST['module'] ) );
				if ( is_wp_error( $module_data ) ) {
					$message = array(
						'message' => $module_data->get_error_message(),
					);
					wp_send_json_error( $message );
				}
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'reset-module-' . $module_data['module'] ) ) {
					wp_send_json_error( array( 'message' => $this->messages['security'] ) );
				}
				$filter = sprintf( 'ultimatebranding_settings_%s_reset', $module_data['module'] );
				$status = apply_filters( $filter, false );
				if ( $status ) {
					$message = array(
						'type'    => 'success',
						'message' => sprintf(
							__( '%s module was reset.', 'ub' ),
							$this->bold( $module_data['name'] )
						),
					);
					$this->add_message( $message );
					wp_send_json_success();
				}
			}
			wp_send_json_error( array( 'message' => $this->messages['wrong'] ) );
		}

		/**
		 * Return messages
		 *
		 * @since 3.0.0
		 */
		public function get_messages() {
			return $this->messages;
		}

		/**
		 * Map of old -> new modules.
		 *
		 * @since 3.0.0
		 */
		private function get_modules_map() {
			$map = array(
				/**
				 * Dashboard Widgets
				 */
				'dashboard-text-widgets/dashboard-text-widgets.php' => 'widgets/dashboard-widgets.php',
				'custom-dashboard-welcome.php'            => 'widgets/dashboard-widgets.php',
				'remove-wp-dashboard-widgets.php'         => 'widgets/dashboard-widgets.php',
				'remove-wp-dashboard-widgets/remove-wp-dashboard-widgets.php' => 'widgets/dashboard-widgets.php',
				'dashboard-widgets/dashboard-widgets.php' => 'widgets/dashboard-widgets.php',
				'dashboard-feeds/dashboard-feeds.php'     => 'widgets/dashboard-feeds.php',
				/**
				 * Turn on Content Header
				 */
				'global-header-content.php'               => 'content/header.php',
				/**
				 * Turn on Content Footer
				 */
				'global-footer-content.php'               => 'content/footer.php',
				/**
				 * Turn on Email Header
				 */
				'custom-email-from.php'                   => 'emails/headers.php',
				/**
				 * Turn on Registration Emails
				 */
				'custom-ms-register-emails.php'           => 'emails/registration.php',
				/**
				 * Text Replacement ( Text Change )
				 */
				'site-wide-text-change.php'               => 'utilities/text-replacement.php',
				'text-replacement/text-replacement.php'   => 'utilities/text-replacement.php',
				/**
				 * Images: Favicons
				 * Images: Image upload size
				 */
				'favicons.php'                            => 'utilities/images.php',
				'image-upload-size.php'                   => 'utilities/images.php',
				/**
				 * Admin Bar
				 * Admin Bar Logo
				 */
				'custom-admin-bar.php'                    => 'admin/bar.php',
				'admin-bar-logo.php'                      => 'admin/bar.php',
				/**
				 * Login Screen
				 */
				'custom-login-screen.php'                 => 'login-screen/login-screen.php',
				/**
				 * Site Generator
				 */
				'site-generator-replacement.php'          => 'utilities/site-generator.php',
				/**
				 * Email Temlate
				 */
				'htmlemail.php'                           => 'emails/template.php',
				/**
				 * Blog creation: signup code
				 */
				'signup-code.php'                         => 'login-screen/signup-code.php',
				/**
				 * Color Schemes
				 */
				'ultimate-color-schemes.php'              => 'admin/color-schemes.php',
				/**
				 * Admin Footer Text
				 */
				'admin-footer-text.php'                   => 'admin/footer.php',
				/**
				 * Meta Widget
				 */
				'rebranded-meta-widget.php'               => 'widgets/meta-widget.php',
				/**
				 * Admin Custom CSS
				 */
				'custom-admin-css.php'                    => 'admin/custom-css.php',
				/**
				 * Admin Message
				 */
				'admin-message.php'                       => 'admin/message.php',
				/**
				 * Comments Control
				 */
				'comments-control.php'                    => 'utilities/comments-control.php',
				/**
				 * Blog Description on Blog Creation
				 */
				'signup-blog-description.php'             => 'front-end/signup-blog-description.php',
				/**
				 * Document
				 */
				'document.php'                            => 'front-end/document.php',
				/**
				 * Admin Help Content
				 */
				'admin-help-content.php'                  => 'admin/help-content.php',
				/**
				 * ms-site-check
				 */
				'ms-site-check/ms-site-check.php'         => 'front-end/site-status-page.php',
				/**
				 * Cookie Notice
				 */
				'cookie-notice/cookie-notice.php'         => 'front-end/cookie-notice.php',
				/**
				 * DB Error Page
				 */
				'db-error-page/db-error-page.php'         => 'front-end/db-error-page.php',
				/**
				 * Author Box
				 */
				'author-box/author-box.php'               => 'front-end/author-box.php',
				/**
				 * SMTP
				 */
				'smtp/smtp.php'                           => 'emails/smtp.php',
				/**
				 * Tracking Codes
				 */
				'tracking-codes/tracking-codes.php'       => 'utilities/tracking-codes.php',
				/**
				 * Website Mode
				 */
				'maintenance/maintenance.php'             => 'utilities/maintenance.php',
			);
			return $map;
		}

		private function get_first_installed_version() {
			return branda_get_option( 'branda_first_installed_version', '0' );
		}

		private function set_first_installed_version() {
			branda_update_option( 'branda_first_installed_version', $this->build );
		}

		/**
		 * Upgrade
		 *
		 * @since 3.0.0
		 */
		public function upgrade() {
			$key        = 'branda_db_version';
			$db_version = intval( branda_get_option( $key, 0 ) );

			if ( empty( $db_version ) ) {
				$this->set_first_installed_version();
			}

			/**
			 * Branda 3.0.0
			 */
			$value = 20190205;
			if ( $value > $db_version ) {
				$modules = get_branda_activated_modules();
				$map     = $this->get_modules_map();
				foreach ( $map as $old => $new ) {
					if (
						isset( $modules[ $old ] )
						&& 'yes' === $modules[ $old ]
					) {
						$this->deactivate_module( $old );
						$this->activate_module( $new );
					}
				}
				/**
				 * Turn on Registration Emails
				 */
				$module = 'export-import.php';
				if (
					isset( $modules[ $module ] )
					&& 'yes' === $modules[ $module ]
				) {
					$this->activate_module( 'utilities/import.php' );
					$this->activate_module( 'utilities/export.php' );
					$this->deactivate_module( $module );
				}
				/**
				 * Turn on Admin Menu
				 *
				 * Urgent: do not turn off previous modules!
				 */
				$m = array(
					'admin-panel-tips/admin-panel-tips.php',
					'link-manager.php',
					'remove-dashboard-link-for-users-without-site.php',
					'remove-permalinks-menu-item.php',
				);
				foreach ( $m as $module ) {
					if (
						isset( $modules[ $module ] )
						&& 'yes' === $modules[ $module ]
					) {
						$this->activate_module( 'admin/menu.php' );
					}
				}
				/**
				 * update
				 */
				branda_update_option( $key, $value );
			}
		}

		/**
		 * Add admin body classes
		 *
		 * @since 3.0.0
		 */
		public function add_branda_admin_body_class( $classes ) {
			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
				if (
					preg_match( '/page_branda/', $screen->id )
					|| preg_match( '/page_branding/', $screen->id )
				) {
					if ( ! is_string( $classes ) ) {
						$classes = '';
					}
					$classes .= ' branda-admin-page';
					/**
					 * Shared UI
					 * Include library version as class on body.
					 *
					 * @since 3.0.0
					 */
					if ( defined( 'BRANDA_SUI_VERSION' ) ) {
						$sanitize_version = str_replace( '.', '-', BRANDA_SUI_VERSION );
						$classes         .= sprintf( ' sui-%s', $sanitize_version );
					}
					/**
					 * add import class
					 */
					if ( 'import' === $this->module ) {
						if (
							isset( $_REQUEST['key'] )
							&& 'error' === $_REQUEST['key']
						) {
							$classes .= ' branda-import';
						}
						if (
							isset( $_REQUEST['step'] )
							&& 'import' === $_REQUEST['step']
						) {
							$classes .= ' branda-import';
						}
					}
				}
			}
			return $classes;
		}

		/**
		 * Get configuration
		 *
		 * @since 3.0.0
		 */
		public function get_configuration() {
			return $this->configuration;
		}

		/**
		 * Get modules
		 *
		 * @since 3.0.0
		 */
		public function get_modules() {
			return $this->modules;
		}

		/**
		 * Set last "write" module usage
		 *
		 * @since 3.0.0
		 */
		public function set_last_write( $module ) {
			$module = $this->get_module_by_module( $module );
			$this->stats->set_last_write( $module['key'] );
		}

		/**
		 * Copy settings from another modules.
		 *
		 * @since 3.0.0
		 */
		private function get_copy_button( $module ) {
			$content = '';
			if ( empty( $this->related ) || ! is_array( $this->related ) ) {
				return $content;
			}
			$module_module = $module['module'];
			$related       = array();
			foreach ( $this->related as $section => $data ) {
				if ( array_key_exists( $module_module, $data ) ) {
					unset( $data[ $module_module ] );
					if ( empty( $data ) ) {
						continue;
					}
					$related[ $section ] = $data;
				}
			}
			if ( empty( $related ) ) {
				return $content;
			}
			$trans = array(
				'background'            => __( 'Background', 'ub' ),
				'logo'                  => __( 'Logo', 'ub' ),
				'social_media_settings' => __( 'Social Media Settings', 'ub' ),
				'social_media'          => __( 'Social Media', 'ub' ),
			);
			$c     = array();
			foreach ( $related as $section => $section_data ) {
				foreach ( $section_data as $module_key => $module_key_data ) {
					if ( ! isset( $c[ $module_key ] ) ) {
						$c[ $module_key ]            = $module_key_data;
						$c[ $module_key ]['options'] = array();
					}
					$c[ $module_key ]['options'][ $section ] = $trans[ $section ];
				}
			}
			$args     = array(
				'related' => $c,
				'module'  => $module,
			);
			$template = 'admin/common/copy';
			$content .= $this->render( $template, $args, true );
			return $content;
		}

		/**
		 * Copy settings from source to target module.
		 *
		 * @since 3.0.0
		 */
		public function ajax_copy_settings() {
			//$target = filter_input( INPUT_POST, 'target_module', FILTER_SANITIZE_STRING );
			//$source = filter_input( INPUT_POST, 'source_module', FILTER_SANITIZE_STRING );
			//$nonce  = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );

			$target = ! empty( $_POST['target_module'] ) ? sanitize_text_field( $_POST['target_module'] ) : null;
			$source = ! empty( $_POST['source_module'] ) ? sanitize_text_field( $_POST['source_module'] ) : null;
			$nonce  = ! empty( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : null;

			if (
				empty( $target )
				|| empty( $source )
				|| empty( $nonce )
				|| ! isset( $_POST['sections'] )
			) {
				wp_send_json_error( array( 'message' => $this->messages['missing'] ) );
			}
			$nonce_action = sprintf( 'branda-copy-settings-%s', $target );
			if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
				wp_send_json_error( array( 'message' => $this->messages['security'] ) );
			}
			$source_module_data          = $this->get_module_by_module( $source );
			$target_module_data          = $this->get_module_by_module( $target );
			$source_module_configuration = branda_get_option( $source_module_data['options'][0] );
			$target_module_configuration = branda_get_option( $target_module_data['options'][0], array() );
			if ( empty( $source_module_configuration ) ) {
				wp_send_json_error( array( 'message' => __( 'Please configure source module first!', 'ub' ) ) );
			}
			/**
			 *
			 */
			if ( ! is_array( $target_module_configuration ) ) {
				$target_module_configuration = array();
			}
			$copy = array(
				'content' => array(),
				'design'  => array(),
				'colors'  => array(),
			);
			foreach ( $_POST['sections'] as $section ) {
				switch ( $section ) {
					case 'background':
						$copy['content'][] = 'content_background';
						$copy['design'][]  = 'background_mode';
						$copy['design'][]  = 'background_duration';
						$copy['design'][]  = 'background_size';
						$copy['design'][]  = 'background_size_width';
						$copy['design'][]  = 'background_size_height';
						$copy['design'][]  = 'background_focal';
						$copy['design'][]  = 'background_crop';
						$copy['design'][]  = 'background_crop_width';
						$copy['design'][]  = 'background_crop_height';
						$copy['design'][]  = 'background_crop_width_p';
						$copy['design'][]  = 'background_crop_height_p';
						$copy['design'][]  = 'background_attachment';
						$copy['design'][]  = 'background_size';
						$copy['design'][]  = 'background_size_width';
						$copy['design'][]  = 'background_size_height';
						$copy['design'][]  = 'background_position_x';
						$copy['design'][]  = 'background_position_x_custom';
						$copy['design'][]  = 'background_position_x_units';
						$copy['design'][]  = 'background_position_y';
						$copy['design'][]  = 'background_position_y_custom';
						$copy['design'][]  = 'background_position_y_units';
						$copy['colors'][]  = 'background_color';
						$copy['colors'][]  = 'document_color';
						$copy['colors'][]  = 'document_background';
						break;
					case 'logo':
						$copy['content'][] = 'logo_show';
						$copy['content'][] = 'logo_image';
						$copy['content'][] = 'logo_url';
						$copy['content'][] = 'logo_alt';
						$copy['content'][] = 'logo_image_meta';
						$copy['design'][]  = 'logo_width';
						$copy['design'][]  = 'logo_opacity';
						$copy['design'][]  = 'logo_position';
						$copy['design'][]  = 'logo_margin_top';
						$copy['design'][]  = 'logo_margin_right';
						$copy['design'][]  = 'logo_margin_bottom';
						$copy['design'][]  = 'logo_margin_left';
						$copy['design'][]  = 'logo_rounded';
						$copy['colors'][]  = 'document_color';
						$copy['colors'][]  = 'document_background';
						break;
					case 'social_media':
						if (
						isset( $source_module_configuration['content'] )
						&& is_array( $source_module_configuration['content'] )
						) {
							foreach ( $source_module_configuration['content'] as $key => $value ) {
								if ( ! preg_match( '/^social_media_/', $key ) ) {
									continue;
								}
								$target_module_configuration['content'][ $key ] = $value;
							}
						}
						break;
					case 'social_media_settings':
						$copy['design'][] = 'social_media_show';
						$copy['design'][] = 'social_media_target';
						$copy['design'][] = 'social_media_colors';
						break;
					default:
						wp_send_json_error( array( 'message' => $this->messages['wrong'] ) );
				}
			}
			foreach ( $copy as $group => $data ) {
				if (
					! isset( $target_module_configuration[ $group ] )
					|| ! is_array( $target_module_configuration[ $group ] )
				) {
					$target_module_configuration[ $group ] = array();
				}
				foreach ( $data as $option ) {
					if ( isset( $source_module_configuration[ $group ][ $option ] ) ) {
						$target_module_configuration[ $group ][ $option ] = $source_module_configuration[ $group ][ $option ];
					}
				}
			}
			$message = array(
				'type'    => 'success',
				'message' => sprintf(
					__( 'Module %s was updated.', 'ub' ),
					$this->bold( $target_module_data['name'] )
				),
			);
			$this->add_message( $message );
			branda_update_option( $target_module_data['options'][0], $target_module_configuration );
			wp_send_json_success();
		}

		/**
		 * Load dashboard
		 *
		 * @since 3.0.0
		 */
		public function load_dashboard() {
			$modules = get_branda_activated_modules( 'raw' );
			if ( empty( $modules ) ) {
				$user_id = get_current_user_id();
				$show    = get_user_meta( $user_id, 'show_welcome_dialog', true );
				$show    = empty( $show );
				if ( $show ) {
					$this->show_welcome_dialog = true;
					update_user_meta( $user_id, 'show_welcome_dialog', 'hide' );
				}
			}
		}

		/**
		 * Branda Welcome!
		 *
		 * @since 3.0.0
		 */
		public function ajax_welcome() {
			//$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
			$nonce = ! empty( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : null;

			if ( ! wp_verify_nonce( $nonce, 'branda-welcome-all-modules' ) ) {
				$args = array(
					'message' => $this->messages['security'],
				);
				wp_send_json_error( $args );
			}
			$args     = array(
				'dialog_id' => 'branda-welcome',
				'modules'   => $this->get_modules_stats(),
				'groups'    => branda_get_groups_list(),
			);
			$template = 'admin/dashboard/welcome-modules';
			$args     = array(
				'content'     => $this->render( $template, $args, true ),
				'title'       => esc_html__( 'Activate Modules', 'ub' ),
				'description' => esc_html__( 'Choose the modules you want to activate. Each module helps you white label a specific part of your website. If you’re not sure or forget to activate any module now, you can always do that later.', 'ub' ),
			);
			wp_send_json_success( $args );
		}

		/**
		 * Check if high contrast mode is enabled.
		 *
		 * For the accessibility support, enable/disable
		 * high contrast support on admin area.
		 *
		 * @since 3.0.0
		 *
		 * @return bool
		 */
		private function high_contrast_mode() {
			// Get accessibility settings.
			$accessibility_options = branda_get_option( 'ub_accessibility', array() );
			if ( isset( $accessibility_options['accessibility']['high_contrast'] )
				&& 'on' === $accessibility_options['accessibility']['high_contrast'] ) {
				return true;
			}
			return false;
		}

		/**
		 * Common hooks for all screens
		 *
		 * @since 3.0.1
		 */
		public function add_action_hooks() {
			// Filter built-in wpmudev branding script.
			//add_filter( 'wpmudev_whitelabel_plugin_pages', array( $this, 'builtin_wpmudev_branding' ) );
		}

		/**
		 * Add more pages to builtin wpmudev branding.
		 *
		 * @since 3.0.1
		 *
		 * @param array $plugin_pages Nextgen pages is not introduced in built in wpmudev branding.
		 *
		 * @return array
		 */
		public function builtin_wpmudev_branding( $plugin_pages ) {
			global $hook_suffix;
			if ( strpos( $hook_suffix, '_page_branding' ) ) {
				$plugin_pages[ $hook_suffix ] = array(
					'wpmudev_whitelabel_sui_plugins_branding',
					'wpmudev_whitelabel_sui_plugins_footer',
					'wpmudev_whitelabel_sui_plugins_doc_links',
				);
			}
			return $plugin_pages;
		}

		/**
		 * Handle Branda SUI wrapper container classes.
		 *
		 * @since 3.0.6
		 */
		public function add_sui_wrap_classes( $classes ) {
			if ( is_string( $classes ) ) {
				$classes = array( $classes );
			}
			if ( ! is_array( $classes ) ) {
				$classes = array();
			}
			$classes[] = 'sui-wrap';
			$classes[] = 'sui-wrap-branda';
			/**
			 * Add high contrast mode.
			 */
			$is_high_contrast_mode = $this->high_contrast_mode();
			if ( $is_high_contrast_mode ) {
				$classes[] = 'sui-color-accessible';
			}
			/**
			 * Set hide branding
			 *
			 * @since 3.0.6
			 */
			$hide_branding = apply_filters( 'wpmudev_branding_hide_branding', $this->hide_branding );
			if ( $hide_branding ) {
				$classes[] = 'no-branda';
			}
			return $classes;
		}

		/**
		 * Delete image from modules, when it is deleted from WordPress
		 *
		 * @since 3.1.0
		 */
		public function delete_attachment_from_configs( $attachemnt_id ) {
			$affected_modules = array(
				'admin-bar',
				'db-error-page',
				'login-screen',
				'ms-site-check',
				'images',
				'maintenance',
			);
			foreach ( $this->configuration as $module ) {
				if ( ! in_array( $module['module'], $affected_modules ) ) {
					continue;
				}
				if ( ! isset( $module['options'] ) ) {
					continue;
				}
				foreach ( $module['options'] as $option_name ) {
					$value = branda_get_option( $option_name );
					if ( empty( $value ) ) {
						continue;
					}
					$update = false;
					foreach ( $value as $group => $group_data ) {
						if ( ! is_array( $group_data ) ) {
							continue;
						}
						foreach ( $group_data as $key => $field ) {
							switch ( $key ) {
								/**
								 * Single image
								 */
								case 'favicon':
								case 'logo_image':
								case 'logo':
									$field = intval( $field );
									if ( $attachemnt_id === $field ) {
										$update = true;
										unset( $value[ $group ][ $key ] );
										$key .= '_meta';
										if ( isset( $value[ $group ][ $key ] ) ) {
											unset( $value[ $group ][ $key ] );
										}
									}
									break;
								/**
								 * Background
								 */
								case 'content_background':
									if ( is_array( $field ) ) {
										foreach ( $field as $index => $one ) {
											$id = isset( $one['value'] ) ? intval( $one['value'] ) : 0;
											if ( $attachemnt_id === $id ) {
												if ( isset( $value[ $group ][ $key ] ) ) {
													$update = true;
													unset( $value[ $group ][ $key ][ $index ] );
												}
											}
										}
									}
									break;
								default:
							}
						}
					}
					if ( $update ) {
						branda_update_option( $option_name, $value );
					}
				}
			}
		}

		/**
		 * Should be shown "Manage All Modules" button?
		 * It's depends.
		 *
		 * @since 3.2.0
		 *
		 * @return boolean $show To show, or not to show, that is the * question.
		 */
		private function show_manage_all_modules_button() {
			$show = true;
			if ( $this->is_network && ! $this->is_network_admin ) {
				$show = false;
			}
			return apply_filters( 'branda_show_manage_all_modules_button', $show );
		}

		/**
		 * Get inline style for box summary-image div
		 *
		 * @since 3.2.0
		 * @return string
		 */
		public function get_box_summary_image_style() {
			$image_url = apply_filters( 'wpmudev_branding_hero_image', null );
			if ( ! empty( $image_url ) ) {
				return 'background-image:url(' . esc_url( $image_url ) . ')';
			}
			return '';
		}

		/**
		 * Get modules with inline help
		 *
		 * @since 3.2.0
		 */
		private function get_helps_list() {
			$helps = array();
			$show  = true;
			if ( $this->is_network && ! $this->is_network_admin ) {
				$show = false;
			}
			if ( $show ) {
				$helps[] = 'dashboard';
			}
			foreach ( $this->configuration as $id => $module ) {
				if ( isset( $module['has-help'] ) && $module['has-help'] ) {
					$show = true;
					if ( $this->is_network && ! $this->is_network_admin ) {
						$show = apply_filters( 'branda_module_check_for_subsite', false, $id, $module );
					}
					if ( $show ) {
						$helps[] = sprintf( 'modules/%s', sanitize_title( $module['module'] ) );
					}
				}
			}
			return $helps;
		}

		/**
		 * Helper to get network admin permissions settings page.
		 *
		 * @since 3.2.0
		 */
		private function get_network_permissions_url() {
			$url = add_query_arg(
				array(
					'page'   => 'branding_group_data',
					'module' => 'permissions',
				),
				network_admin_url( 'admin.php' )
			);
			return $url;
		}
	}
}
