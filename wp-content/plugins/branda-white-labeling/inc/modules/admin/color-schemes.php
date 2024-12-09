<?php
/**
 * Branda Color Schemes class.
 *
 * Class to handle ultimate color scheme customization.
 *
 * @package Branda
 * @subpackage AdminArea
 */
if ( ! class_exists( 'Branda_Color_Schemes' ) ) {

	class Branda_Color_Schemes extends Branda_Helper {

		protected $option_name = 'ub_color_schemes';
		protected $defaults    = array();

		/**
		 * Custom Scheme Slug
		 *
		 * @since 3.1.0
		 */
		private $slug = 'wpi_custom_scheme';

		/**
		 * Ultimate_Color_Schemes constructor.
		 */
		public function __construct() {
			parent::__construct();
			$this->set_defaults();
			$this->module = 'color-schemes';
			/**
			 * Common module hooks
			 */
			add_filter( 'ultimatebranding_settings_color_schemes', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_color_schemes_process', array( $this, 'update' ), 10 );
			// Custom header actions.
			add_action( 'admin_init', array( $this, 'admin_custom_color_scheme_option' ), 11 );
			// Admin interface.
			add_filter( 'get_user_option_admin_color', array( $this, 'force_admin_scheme_color' ), 5 );
			add_action( 'user_register', array( $this, 'set_default_admin_color' ) );
			add_action( 'wpmu_new_user', array( $this, 'set_default_admin_color' ) );
			/**
			 * Add dialog
			 *
			 * @since 3.0,0
			 */
			add_filter( 'branda_get_module_content', array( $this, 'add_dialog' ), 10, 2 );
			/**
			 * Prevent to delete "order" and "nodes",
			 *
			 * @since 3.0.0
			 */
			add_filter( 'ultimatebranding_settings_color_schemes_preserve', array( $this, 'add_preserve_fields' ) );
			/**
			 * Change available schemes on user profile.
			 *
			 * @since 3.0.0
			 */
			add_action( 'admin_color_scheme_picker', array( $this, 'admin_color_scheme_picker' ), 0 );
			/**
			 * AJAX
			 */
			add_action( 'wp_ajax_branda_color_schemes_save', array( $this, 'ajax_save' ) );
			/**
			 * upgrade options
			 *
			 * @since 3.0.0
			 */
			add_action( 'init', array( $this, 'upgrade_options' ) );
			/**
			 * Change Admin Bar Logo CSS
			 */
			add_filter( 'branda_admin_bar_logo_css_args', array( $this, 'bar_logo_args' ), 10, 1 );
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
			$ucs_visible_color_schemes = branda_get_option( 'ucs_visible_color_schemes' );
			if ( is_array( $ucs_visible_color_schemes ) ) {
				$ucs_visible_color_schemes = array_filter( $ucs_visible_color_schemes );
			}
			$data = array(
				'available_color_schemes' => array(
					'scheme' => $ucs_visible_color_schemes,
				),
				'defaul_color_scheme'     => array(
					'scheme' => branda_get_option( 'ucs_default_color_scheme', 'do-not-change' ),
				),
				'force_color_scheme'      => array(
					'scheme' => branda_get_option( 'ucs_force_color_scheme', 'do-not-force' ),
				),
				'settings'                => array(
					'ultimate' => $this->convert_ultimate_scheme(),
				),
			);
			/**
			 * Delete old values
			 */
			branda_delete_option( 'ucs_default_color_scheme' );
			branda_delete_option( 'ucs_force_color_scheme' );
			branda_delete_option( 'ucs_visible_color_schemes' );
			/**
			 * Available Colors Schemes
			 */
			$this->update_value( $data );
		}

		/**
		 * Convert Ultimate Scheme!
		 *
		 * Convert Ultimate Scheme for version less than 3.0.0
		 *
		 * @since 3.0.0
		 */
		private function convert_ultimate_scheme() {
			$map      = array(
				/**
				 * Scheme Name
				 */
				'ucs_color_scheme_name'                    => 'scheme_name',
				/**
				 * General
				 */
				'ucs_background_color'                     => 'general_background',
				/**
				 * Links
				 */
				'ucs_default_link_color'                   => 'links_static_default',
				'ucs_delete_trash_spam_link_color'         => 'links_static_delete',
				'ucs_inactive_plugins_color'               => 'links_static_inactive',
				'ucs_default_link_hover_color'             => 'links_static_default_hover',
				'ucs_delete_trash_spam_link_hover_color'   => 'links_static_delete_hover',
				/**
				 * Forms
				 */
				'ucs_checkbox_radio_color'                 => 'form_checkbox',
				/**
				 * Core UI
				 */
				'ucs_primary_button_background_color'      => 'core_ui_primary_button_background',
				'ucs_primary_button_text_color'            => 'core_ui_primary_button_color',
				'ucs_primary_button_text_color_shadow'     => 'core_ui_primary_button_shadow_color',
				'ucs_disabled_button_background_color'     => 'core_ui_disabled_button_background',
				'ucs_disabled_button_text_color'           => 'core_ui_disabled_button_color',
				'ucs_primary_button_hover_background_color' => 'core_ui_primary_button_background_hover',
				'ucs_primary_button_hover_text_color'      => 'core_ui_primary_button_color_hover',
				'ucs_primary_button_text_color_shadow_hover' => 'core_ui_primary_button_shadow_color_hover',
				/**
				 * List Tables
				 */
				'ucs_table_view_switch_icon_color'         => 'list_tables_switch_icon',
				'ucs_table_post_comment_icon_color'        => 'list_tables_post_comment_count_hover',
				'ucs_table_alternate_row_color'            => 'list_tables_alternate_row',
				'ucs_table_view_switch_icon_hover_color'   => 'list_tables_switch_icon_hover',
				'ucs_table_post_comment_strong_icon_color' => 'list_tables_post_comment_count',
				'ucs_table_list_hover_color'               => 'list_tables_pagination_hover',
				/**
				 * Admin Menu
				 */
				'ucs_admin_menu_link_color'                => 'admin_menu_color',
				'ucs_admin_menu_background_color'          => 'admin_menu_background',
				'ucs_admin_menu_icons_color'               => 'admin_menu_icon_color',
				'ucs_admin_menu_submenu_link_color'        => 'admin_menu_submenu_link',
				'ucs_admin_menu_submenu_background_color'  => 'admin_menu_submenu_background',
				'ucs_admin_menu_bubble_text_color'         => 'admin_menu_bubble_color',
				'ucs_admin_menu_bubble_background_color'   => 'admin_menu_bubble_background',
				'ucs_admin_menu_link_hover_color'          => 'admin_menu_color_hover',
				'ucs_admin_menu_link_hover_background_color' => 'admin_menu_background_hover',
				'ucs_admin_menu_submenu_link_hover_color'  => 'admin_menu_submenu_link_hover',
				'ucs_admin_menu_current_link_color'        => 'admin_menu_color_current',
				'ucs_admin_menu_current_background_color'  => 'admin_menu_background_curent',
				'ucs_admin_menu_current_icons_color'       => 'admin_menu_icon_color_current',
				'ucs_admin_menu_current_link_hover_color'  => 'admin_menu_color_current_hover',
				/**
				 * Admin Menu
				 */
				'ucs_admin_bar_background_color'           => 'admin_bar_background',
				'ucs_admin_bar_text_color'                 => 'admin_bar_color',
				'ucs_admin_bar_icon_color'                 => 'admin_bar_icon_color',
				'ucs_admin_bar_submenu_icon_color'         => 'admin_bar_submenu_icon_color',
				'ucs_admin_bar_item_hover_background_color' => 'admin_bar_item_background_hover',
				'ucs_admin_bar_item_hover_text_color'      => 'admin_bar_item_color_hover',
				'ucs_admin_bar_item_hover_focus_background' => 'admin_bar_item_background_focus',
				'ucs_admin_bar_item_hover_focus_color'     => 'admin_bar_item_color_focus',
				/**
				 * Media Uploader
				 */
				'ucs_admin_media_progress_bar_color'       => 'admin_media_progress_bar_color',
				'ucs_admin_media_selected_attachment_color' => 'admin_media_selected_attachment_color',
				/**
				 * Themes
				 */
				'ucs_admin_active_theme_background_color'  => 'admin_themes_background',
				'ucs_admin_active_theme_actions_background_color' => 'admin_themes_actions_background',
				'ucs_admin_active_theme_details_background_color' => 'admin_themes_details_background',
				/**
				 * Plugins
				 */
				'ucs_admin_active_plugin_border_color'     => 'admin_plugins_border_color',
			);
			$ultimate = array();
			foreach ( $map as $old_name => $name ) {
				if ( empty( $name ) ) {
					continue;
				}
				$default = false;
				if ( isset( $this->defaults[ $name ] ) ) {
					$default = $this->defaults[ $name ];
				}
				$value = branda_get_option( $old_name, $default );
				/**
				 * delete old value
				 */
				branda_delete_option( $old_name );
				if ( $value === $default ) {
					continue;
				}
				$ultimate[ $name ] = $value;
			}
			return $ultimate;
		}

		/**
		 * Build form with options.
		 *
		 * @since 3.0.0
		 */
		protected function set_options() {
			/**
			 * Colors table
			 */
			global $_wp_admin_css_colors;
			$colors = array();
			if ( is_array( $_wp_admin_css_colors ) ) {
				foreach ( $_wp_admin_css_colors as $color => $color_info ) {
					$colors[ $color ] = $color_info->name;
				}
			}
			asort( $colors );
			/**
			 * Default Color Scheme
			 */
			$colors_default  = array(
				'do-not-change' => __( 'WordPress default', 'ub' ),
			);
			$colors_default += $colors;
			/**
			 * Force Color Scheme
			 */
			$colors_force  = array(
				'do-not-force' => __( 'Do not force color scheme', 'ub' ),
			);
			$colors_force += $colors;
			/**
			 * Options
			 */
			$options = array(
				'available_color_schemes' => array(
					'title'       => __( 'Available Color Scheme', 'ub' ),
					'description' => __( 'Choose the color schemes which should be visible within the User Profile.', 'ub' ),
					'fields'      => array(
						'schemes' => array(
							'type'     => 'callback',
							'callback' => array( $this, 'get_list' ),
						),
					),
				),
				'defaul_color_scheme'     => array(
					'title'       => __( 'Default Color Scheme', 'ub' ),
					'description' => __( 'Choose a default color scheme for newly registered users.', 'ub' ),
					'fields'      => array(
						'scheme' => array(
							'type'    => 'select2',
							'options' => $colors_default,
							'default' => 'default',
						),
					),
				),
				'force_color_scheme'      => array(
					'title'       => __( 'Force Color Scheme', 'ub' ),
					'description' => __( 'Choose a color scheme to be used for every user.', 'ub' ),
					'fields'      => array(
						'scheme' => array(
							'type'    => 'select2',
							'options' => $colors_force,
							'default' => 'do-not-force',
						),
					),
				),
			);
			if ( $this->is_network ) {
				$options['force_color_scheme']['description'] = __( 'Choose a color scheme to be used for every user across network.', 'ub' );
			}
			$this->options = $options;
		}

		/**
		 * Set default admin color scheme.
		 *
		 * @param int $user_id User ID.
		 */
		public function set_default_admin_color( $user_id ) {
			$value = $this->get_value( 'defaul_color_scheme', 'scheme', 'do-not-change' );
			if ( empty( $value ) || 'do-not-force' === $value ) {
				return;
			}
			$args = array(
				'ID'          => $user_id,
				'admin_color' => $value,
			);
			wp_update_user( $args );
		}

		/**
		 * Force admin color scheme.
		 *
		 * @param string $result Color scheme.
		 *
		 * @return mixed|void
		 */
		public function force_admin_scheme_color( $scheme ) {
			$value = $this->get_value( 'force_color_scheme', 'scheme', 'do-not-force' );
			if ( empty( $value ) || 'do-not-force' === $value ) {
				return $scheme;
			}
			return $value;
		}

		/**
		 * Admin custom color scheme option.
		 *
		 * Set admin color scheme css.
		 *
		 * @uses wp_admin_css_color()
		 */
		public function admin_custom_color_scheme_option() {
			if ( isset( $_GET['custom-color-scheme'] ) ) {
				$this->set_custom_color_scheme();
				exit;
			}
			$admin_url = is_network_admin() ? network_admin_url() : admin_url();

			// Set Branda color scheme without colors and icons for including it in $_wp_admin_css_colors for set_option method
			// It will be updated in the end of this method
			wp_admin_css_color( $this->slug, 'Branda', $admin_url );

			$url = add_query_arg( 'custom-color-scheme', $this->getv( 'last_update' ), $admin_url );
			/**
			 *  Custom scheme.
			 */
			$name   = $this->getv( 'scheme_name' );
			$colors = array(
				$this->getv( 'general_background' ),
				$this->getv( 'admin_menu_background' ),
				$this->getv( 'admin_menu_background_curent' ),
				$this->getv( 'admin_menu_bubble_background' ),
			);
			$icons  = array(
				'base'    => $this->getv( 'admin_menu_icon_color' ),
				'focus'   => $this->getv( 'admin_menu_icon_color_focus' ),
				'current' => $this->getv( 'admin_menu_icon_color_current' ),
			);
			wp_admin_css_color( $this->slug, $name, $url, $colors, $icons );
		}

		/**
		 * Custom color scheme css.
		 *
		 * Load custom scheme css from the file.
		 */
		public function set_custom_color_scheme() {
			header( 'Content-type: text/css' );
			$args     = array_merge( $this->defaults, $this->get_value( 'settings', 'ultimate' ) );
			$template = $this->get_template_name( 'css' );
			$this->render( $template, $args );
		}

		/**
		 * Color scheme list.
		 *
		 * @since 3.0.0
		 *
		 * @return string
		 */
		public function get_list() {
			global $_wp_admin_css_colors;
			$visible_colors = $this->get_value( 'available_color_schemes', 'scheme', array() );
			$content        = '';
			$css_schemes    = $_wp_admin_css_colors;
			if ( isset( $css_schemes[ $this->slug ] ) && array_keys( $css_schemes )[0] !== $this->slug ) {
				// move Branda scheme css to the top of the list
				$branda_scheme = $css_schemes[ $this->slug ];
				unset( $css_schemes[ $this->slug ] );
				$css_schemes = array_merge( array( $this->slug => $branda_scheme ), $css_schemes );
			}
			foreach ( $css_schemes as $color => $color_info ) {
				$id       = $this->get_name( $color );
				$content .= '<div class="color-option">';
				$checked  = false == $visible_colors || in_array( $color, $visible_colors );
				$content .= '<label class="sui-checkbox sui-checkbox-stacked">';
				$content .= sprintf(
					'<input type="checkbox" name="%s" id="%s" value="%s" %s />',
					esc_attr( 'simple_options[available_color_schemes][scheme][]' ),
					esc_attr( $id ),
					esc_attr( $color ),
					checked( $checked, true, false )
				);
				$content .= '<span></span>';
				$content .= sprintf(
					'<span class="sui-description">%s</span>',
					esc_html( $color_info->name )
				);
				$content .= '</label>';
				
				if ( $this->slug === $color ) {
					$args     = array(
						'only-icon' => true,
						'icon'      => 'pencil',
						'data'      => array(
							'modal-open' => $this->get_name( 'edit' ),
							'tooltip'    => esc_attr( sprintf( _x( 'Customize "%s" scheme', 'Label for link to edit custom theme', 'ub' ), $color_info->name ) ),
						),
						'sui'       => array(
							'tooltip',
						),
						'classes'   => array(
							'branda-customize-ultimate-scheme',
							'sui-tooltip',
						),
					);
					$content .= $this->button( $args );
				}
				
				$content .= sprintf(
					'<label for="%s">',
					esc_attr( $id )
				);
				$content .= '<table class="color-palette"><tr>';
				foreach ( $color_info->colors as $html_color ) {
					$content .= sprintf(
						'<td style="background-color:%s">&nbsp;</td>',
						esc_attr( $html_color )
					);
				}
				$content .= '</tr></table>';
				$content .= '</label>';
				$content .= '</div>'; // color-option
			}
			return $content;
		}

		/**
		 * Add SUI dialog
		 *
		 * @since 3.0.0
		 *
		 * @param string $content Current module content.
		 * @param array  $module Current module.
		 */
		public function add_dialog( $content, $module ) {
			if ( $this->module !== $module['module'] ) {
				return $content;
			}
			/**
			 * Dialog settings
			 */
			$args = array(
				'dialog_id'          => $this->get_name( 'edit' ),
				'button_reset_nonce' => $this->get_nonce_value( 'reset' ),
				'button_reset_class' => $this->get_name( 'reset' ),
				'button_apply_nonce' => $this->get_nonce_value(),
				'button_apply_class' => $this->get_name( 'save' ),
			);
			/**
			 * values
			 */
			$value = $this->get_value( 'settings', 'ultimate' );
			foreach ( $this->defaults as $key => $default ) {
				$args[ $key ] = isset( $value[ $key ] ) ? $value[ $key ] : $default;
			}
			$template = $this->get_template_name( 'dialogs/edit' );
			$content .= $this->render( $template, $args, true );
			return $content;
		}

		/**
		 * Get value
		 */
		private function getv( $key ) {
			$values = $this->get_value( 'settings', 'ultimate' );
			if (
				isset( $values[ $key ] )
				&& ! empty( $values[ $key ] )
			) {
				return $values[ $key ];
			}
			if ( isset( $this->defaults[ $key ] ) ) {
				return $this->defaults[ $key ];
			}
			return new WP_Error();
		}

		/**
		 * Set defaults
		 *
		 * @since 3.0.0
		 */
		private function set_defaults() {
			$defaults       = array(
				'scheme_name'                             => __( 'Branda', 'ub' ),
				/**
				 * general
				 */
				'general_background'                      => '#f1f1f1',
				/**
				 * links
				 */
				'links_static_default'                    => '#45b29d',
				'links_static_delete'                     => '#df5a49',
				'links_static_inactive'                   => '#888',
				'links_static_default_hover'              => '#e27a3f',
				'links_static_delete_hover'               => '#e27a3f',
				'links_static_inactive_hover'             => '#e27a3f',
				/**
				 * Forms
				 */
				'form_checkbox'                           => '#45b29d',
				/**
				 * Core UI
				 */
				'core_ui_primary_button_background'       => '#334d5c',
				'core_ui_primary_button_background_hover' => '#efc94c',
				'core_ui_primary_button_color'            => '#fff',
				'core_ui_primary_button_color_hover'      => '#fff',
				'core_ui_primary_button_shadow_color'     => '#334d5c',
				'core_ui_primary_button_shadow_color_hover' => '#ec4',
				'core_ui_disabled_button_background'      => '#ccc',
				'core_ui_disabled_button_color'           => '#000',
				/**
				 * List Tables
				 */
				'list_tables_switch_icon'                 => '#45b29d',
				'list_tables_switch_icon_hover'           => '#d46f15',
				'list_tables_pagination_hover'            => '#45b29d',
				'list_tables_post_comment_count'          => '#d46f15',
				'list_tables_post_comment_count_hover'    => '#45b29d',
				'list_tables_alternate_row'               => '#e5ecf0',
				/**
				 * Admin Menu
				 */
				'admin_menu_background'                   => '#45b29d',
				'admin_menu_background_hover'             => '#334d5c',
				'admin_menu_color'                        => '#fff',
				'admin_menu_color_hover'                  => '#fff',
				'admin_menu_color_current'                => '#fff',
				'admin_menu_color_current_hover'          => '#fff',
				'admin_menu_background_curent'            => '#efc94c',
				'admin_menu_icon_color'                   => '#fff',
				'admin_menu_icon_color_focus'             => '#00a0d2',
				'admin_menu_icon_color_current'           => '#fff',
				'admin_menu_submenu_background'           => '#334d5c',
				'admin_menu_submenu_link'                 => '#cbc5d3',
				'admin_menu_submenu_link_hover'           => '#fff',

				'admin_menu_bubble_color'                 => '#fff',
				'admin_menu_bubble_background'            => '#df5a49',
				/**
				 * Admin Bar
				 */
				'admin_bar_background'                    => '#45b29d',
				'admin_bar_color'                         => '#fff',
				'admin_bar_icon_color'                    => '#fff',
				'admin_bar_item_background_hover'         => '#334d5c',
				'admin_bar_item_color_hover'              => '#45b29d',
				'admin_bar_item_background_focus'         => '#45b29d',
				'admin_bar_item_color_focus'              => '#45b29d',
				'admin_bar_submenu_icon_color'            => '#ece6f6',
				'admin_bar_submenu_icon_color_hover'      => '#fff',
				'admin_bar_submenu_icon_color_focus'      => '#fff',
				/**
				 * Media Uploader
				 */
				'admin_media_progress_bar_color'          => '#334d5c',
				'admin_media_selected_attachment_color'   => '#334d5c',
				/**
				 * Themes
				 */
				'admin_themes_background'                 => '#334d5c',
				'admin_themes_actions_background'         => '#45b29d',
				'admin_themes_details_background'         => '#45b29d',
				/**
				 * Plugins
				 */
				'admin_plugins_border_color'              => '#efc94c',
				/**
				 * Last update
				 */
				'last_update'                             => 1,
			);
			$this->defaults = $defaults;
		}

		/**
		 * Add settings sections to prevent delete on save.
		 *
		 * Add settings sections (virtual options not included in
		 * "set_options()" function to avoid delete during update.
		 *
		 * @since 3.0.0
		 */
		public function add_preserve_fields( $fields ) {
			return array(
				'settings' => array(
					'ultimate',
				),
			);
		}

		/**
		 * Change available schemes on user profile.
		 *
		 * @since 3.0.0
		 */
		public function admin_color_scheme_picker( $user_id ) {
			/**
			 * Colors table
			 */
			global $_wp_admin_css_colors;
			$value = $this->get_value( 'force_color_scheme', 'scheme', 'default' );
			if ( ! empty( $value ) && 'do-not-force' !== $value ) {
				$new                  = array(
					$value => $_wp_admin_css_colors[ $value ],
				);
				$_wp_admin_css_colors = $new;
				return;
			}
			/**
			 * Cat to available color scheme.
			 */
			$value = $this->get_value( 'available_color_schemes', 'scheme', array() );
			if ( empty( $value ) || ! is_array( $value ) ) {
				return;
			}
			foreach ( $_wp_admin_css_colors as $key => $data ) {
				if ( in_array( $key, $value ) ) {
					continue;
				}
				unset( $_wp_admin_css_colors[ $key ] );
			}
		}

		/**
		 * Save Ultimate scheme data
		 */
		public function ajax_save() {
			$nonce_action = $this->get_nonce_action();
			$this->check_input_data( $nonce_action, array( 'branda' ) );
			$request = $this->sanitize_request_payload( $_POST['branda'] );
			$data    = array();
			foreach ( $this->defaults as $key => $default ) {
				if ( ! isset( $request[ $key ] ) ) {
					continue;
				}
				$value = $request [ $key ];
				if ( empty( $value ) ) {
					continue;
				}
				/**
				 * exception for name
				 */
				if ( 'scheme_name' === $key ) {
					$data[ $key ] = sanitize_text_field( $value );
				} else {
					$value = strtolower( $value );
					if (
						! preg_match( '/^#[0-9a-f]+$/', $value )
						&& ! preg_match( '/^[rgba]{3,4}\([0-9\,\. ]+\)$/', $value )
					) {
						$value = $default;
					}
					if ( $default === $value ) {
						continue;
					}
				}
				$data[ $key ]        = $value;
				$data['last_update'] = time();
			}
			$this->set_value( 'settings', 'ultimate', $data );
			$message = array(
				'type'    => 'success',
				'message' => sprintf(
					__( '%s was successfully updated.', 'ub' ),
					$this->bold( $data['scheme_name'] )
				),
			);
			$this->uba->add_message( $message );
			wp_send_json_success();
		}

		/**
		 * Change admin bar logo args
		 *
		 * @since 3.1.0
		 */
		public function bar_logo_args( $args ) {
			$colors       = array_merge( $this->defaults, $this->get_value( 'settings', 'ultimate' ) );
			$args['base'] = $colors['admin_bar_icon_color'];
			return $args;
		}
	}
}
new Branda_Color_Schemes();
