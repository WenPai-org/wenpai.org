<?php
/**
 * Branda Admin Bar class.
 *
 * Class that handle admin bar functionality.
 *
 * @package Branda
 * @subpackage AdminArea
 */
if ( ! class_exists( 'Branda_Admin_Bar' ) ) {

	/**
	 * Class Branda_Admin_Bar.
	 */
	class Branda_Admin_Bar extends Branda_Helper {

		/**
		 * Module option name.
		 *
		 * @var string
		 */
		protected $option_name = 'ub_admin_bar';

		/**
		 * Roles enabled by default
		 *
		 * @var array
		 */
		private $default_roles;

		/**
		 * Single item defaults
		 *
		 * @since 3.1.0
		 */
		private $item_defaults = array(
			'id'     => 'new',
			'title'  => '',
			'icon'   => '',
			'url'    => 'none',
			'custom' => '',
			'target' => '_self',
			'roles'  => array(),
			'mobile' => 'show',
		);

		/**
		 * Escape callback function to be called in parent class's `esc_deep()` method.
		 * `esc_html` or `wp_kses_post` will break css
		 *
		 * @since 3.4.9.1
		 */
		protected $esc_callback = array( __CLASS__, 'esc_data' );

		/**
		 * Branda_Admin_Bar constructor.
		 */
		public function __construct() {
			parent::__construct();
			$this->module = 'admin-bar';
			/**
			 * User roles
			 */
			$this->set_roles();
			$this->default_roles  = $this->roles;
			$this->roles['guest'] = __( 'Guest', 'ub' );
			// Is network
			$this->item_defaults['is_network'] = $this->is_network;
			// Available roles
			$this->item_defaults['available_roles'] = $this->roles;
			// Common module hooks.
			add_filter( 'ultimatebranding_settings_admin_bar', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_admin_bar_process', array( $this, 'update' ), 10 );
			add_filter( 'ultimatebranding_settings_admin_bar_process', array( $this, 'validate_builders' ), 11 );
			add_filter( 'ultimatebranding_settings_admin_bar_preserve', array( $this, 'add_preserve_fields' ) );
			// Grab admin menu elements.
			add_action( 'admin_bar_menu', array( $this, 'get_admin_bar_menu_nodes' ), PHP_INT_MAX );
			add_action( 'activate_plugin', array( $this, 'delete_admin_bar_menu_nodes' ) );
			// Render admin bar.
			add_action( 'wp_after_admin_bar_render', array( $this, 'after_admin_bar_render' ) );
			// Remove from admin bar.
			add_action( 'admin_bar_menu', array( $this, 'remove_menus_from_admin_bar' ), PHP_INT_MAX - 10 );
			// Reorder admin bar.
			add_action( 'admin_bar_menu', array( $this, 'reorder_menus' ), PHP_INT_MAX - 1 );
			// Add custom to admin menu.
			add_action( 'admin_bar_menu', array( $this, 'add_custom_menus' ), 1 );
			// Save ordering via ajax.
			add_action( 'wp_ajax_branda_admin_bar_order_save', array( $this, 'ajax_order_save' ) );
			add_action( 'wp_ajax_branda_admin_bar_order_reset', array( $this, 'ajax_order_reset' ) );
			/**
			 * Admin bar items
			 */
			add_action( 'wp_ajax_branda_admin_bar_menu_save', array( $this, 'ajax_save_item' ) );
			add_action( 'wp_ajax_branda_admin_bar_delete', array( $this, 'ajax_delete_item' ) );
			add_action( 'wp_ajax_branda_admin_bar_submenu_restore', array( $this, 'ajax_submenu_restore' ) );
			/**
			 * Since 3.0.7
			 */
			add_action( 'wp_ajax_branda_admin_bar_get', array( $this, 'ajax_get_item' ) );
			// Admin bar custom CSS.
			add_action( 'wp_head', array( $this, 'print_style_tag' ) );
			add_action( 'admin_head', array( $this, 'print_style_tag' ) );
			// Logo.
			add_action( 'admin_print_styles', array( $this, 'logo_output' ) );
			add_action( 'wp_head', array( $this, 'logo_output' ) );
			// Upgrade options to new.
			add_action( 'init', array( $this, 'upgrade_options' ) );
			/**
			 * Add dialog
			 *
			 * @since 3.1.0
			 */
			add_filter( 'branda_get_module_content', array( $this, 'add_dialog' ), 10, 2 );
			/**
			 * Branda admin enqueue scripts
			 *
			 * @since 3.1.0
			 */
			add_action( 'branda_admin_enqueue_module_admin_assets', array( $this, 'enqueue_scripts' ) );
			/**
			 * Hide Toolbar based on admin roles
			 *
			 * @since 3.1.2
			 */
			add_filter( 'show_admin_bar', array( $this, 'hide_toolbar_based_on_user_role' ), PHP_INT_MAX );
			/**
			 * Reset cache custom css after updating
			 *
			 * @since 3.2
			 */
			add_action( 'branda_helper_update', array( $this, 'reset_cache' ), 10, 3 );
		}

		public function reset_cache( $module, $value, $current_value ) {
			if ( 'admin-bar' === $module && isset( $value['css']['css'] ) && isset( $current_value['css']['css'] )
					&& $value['css']['css'] !== $current_value['css']['css'] ) {
				$styles = stripslashes( $current_value['css']['css'] );
				if ( ! empty( $styles ) ) {
					$cache_id = md5( $styles );
					wp_cache_delete( $cache_id );
				}
			}
		}


		/**
		 * Upgrade options to new structure.
		 *
		 * @since 2.1.0
		 */
		public function upgrade_options() {
			$value = $this->get_value();

			if ( empty( $value ) ) {
				$value = array();
			}

			if ( isset( $value['plugin_version'] ) ) {
				return;
			}
			if ( ! isset( $value['settings'] ) ) {
				$value['settings'] = array();
			}
			$update = false;
			// Admin Bar Logo.
			$data = branda_get_option( 'admin_bar_logo' );
			if ( ! empty( $data ) ) {
				if ( isset( $data['admin_bar_logo'] ) ) {
					$data = $data['admin_bar_logo'];
					if ( isset( $data['logo_upload'] ) ) {
						$value['logo']['logo'] = $data['logo_upload'];
						$update                = true;
					}
					if ( isset( $data['logo_upload_meta'] ) ) {
						$value['logo']['logo_meta'] = $data['logo_upload_meta'];
						$update                     = true;
					}
				}
				branda_delete_option( 'admin_bar_logo' );
			}
			// Custom Admin Bar: custom menus.
			$data = branda_get_option_filtered( 'ub_admin_bar_menus' );
			if ( ! empty( $data ) && is_array( $data ) ) {
				if ( ! isset( $value['settings']['items'] ) ) {
					$value['settings']['items'] = array();
				}
				foreach ( $data as $menu_id ) {
					$update      = true;
					$option_name = sprintf( 'ub_admin_bar_menu_%d', $menu_id );
					$menu        = maybe_unserialize( branda_get_option( $option_name ) );
					if ( empty( $menu ) ) {
						branda_delete_option( $option_name );
						continue;
					}
					$id        = $this->generate_id( $menu );
					$link_type = isset( $menu['url'] ) ? $menu['url'] : '';
					switch ( $link_type ) {
						case '#':
							$link_type = 'none';
							break;
						case 'site_url':
							$link_type = 'current';
							break;
						case 'network_site_url':
							$link_type = 'main';
							break;
						case 'admin_url':
							$link_type = 'wp-admin';
							break;
						default:
							$link_type = 'custom';
					}
					$item = array(
						'id'      => $id,
						'title'   => isset( $menu['title'] ) ? $menu['title'] : '',
						'icon'    => isset( $menu['dashicons'] ) ? $menu['dashicons'] : '',
						'url'     => $link_type,
						'target'  => isset( $menu['target'] ) ? $this->get_target( $menu['target'] ) : false,
						'roles'   => isset( $menu['menu_roles'] ) ? $menu['menu_roles'] : '',
						'submenu' => array(),
						'custom'  => 'custom' === $link_type ? $menu['url'] : '',
					);
					if ( isset( $menu['links'] ) && is_array( $menu['links'] ) ) {
						foreach ( $menu['links'] as $link ) {
							$link_id = $this->generate_id( $link );
							$type    = isset( $link['url_type'] ) ? $link['url_type'] : 'custom';
							if ( 'external' === $type ) {
								$type = 'custom';
							}
							$item['submenu'][ $link_id ] = array(
								'id'           => $link_id,
								'title'        => $link['title'],
								'url'          => $type,
								'target'       => isset( $link['target'] ) ? $this->get_target( $link['target'] ) : false,
								'url_' . $type => $link['url'],
							);
						}
					}
					$value['settings']['items'][ $id ] = $item;
					branda_delete_option( $option_name );
				}
				branda_delete_option( 'ub_admin_bar_menus' );
			}
			// Custom Admin Bar: custom order.
			$data = branda_get_option_filtered( 'ub_admin_bar_order' );
			if ( ! empty( $data ) ) {
				$value['settings']['order'] = $data;
				$update                     = true;
				branda_delete_option( 'ub_admin_bar_order' );
			}
			// Custom Admin Bar: custom css.
			$data = branda_get_option( 'ub_admin_bar_style' );
			if ( ! empty( $data ) ) {
				$value['css']['css'] = $data;
				$update              = true;
				branda_delete_option( 'ub_admin_bar_style' );
			}
			// Disable menu items.
			$data = branda_get_option( 'wdcab' );
			if ( ! empty( $data ) ) {
				$value['items']['custom-entries'] = empty( $data['enabled'] ) ? 'hide' : 'show';
				// Move disabled menus.
				if ( ! empty( $data['disabled_menus'] ) && is_array( $data['disabled_menus'] ) ) {
					foreach ( $data['disabled_menus'] as $disabled_menu => $menu_item ) {
						$value['items']['disabled_menus'][ $menu_item ] = 1;
					}
					// Set visibility to specific items.
					$value['items']['visibility'] = 'hide';
				}
				// Move menu roles.
				if ( ! empty( $data['wp_menu_roles'] ) && is_array( $data['wp_menu_roles'] ) ) {
					foreach ( $data['wp_menu_roles'] as $role_name => $role_key ) {
						$value['items']['wp_menu_roles'][ $role_key ] = $role_name;
					}
				}
				// Toolbar visibility.
				$value['visibility']               = array();
				$value['visibility']['visibility'] = empty( $data['show_toolbar_for_non_logged'] ) ? 'hidden' : 'visible';
				$update                            = true;
				branda_delete_option( 'wdcab' );
			}
			// Update module settins if it is needed.
			if ( $update ) {
				$this->update_value( $value );
			}
		}

		/**
		 * Add settings sections to prevent delete on save.
		 *
		 * Add settings sections (virtual options not included in
		 * "set_options()" function to avoid delete during update.
		 *
		 * @since 3.0.0
		 *
		 * @return array
		 */
		public function add_preserve_fields() {
			return array(
				'settings' => array(
					'order',
					'nodes',
					'items',
				),
			);
		}

		/**
		 * Saves new menu order into database
		 *
		 * @since  1.6
		 * @access public
		 *
		 * @return void
		 */
		public function ajax_order_save() {
			$nonce_action = $this->get_nonce_action();
			$this->check_input_data( $nonce_action, array( 'order' ) );
			$order = $this->sanitize_request_payload( $_POST['order'] );
			if ( is_array( $order ) && count( $order ) > 0 ) {
				$this->set_value( 'settings', 'order', $order );
				wp_send_json_success( array( 'message' => __( 'Admin Bar order was saved successfully!', 'ub' ) ) );
			}
			$this->json_error();
		}

		/**
		 * Reset order to default.
		 *
		 * Reset menu items to default order
		 */
		public function ajax_order_reset() {
			$nonce_action = $this->get_nonce_action( 'order', 'reset' );
			$this->check_input_data( $nonce_action );
			$this->set_value( 'settings', 'order', null );
			$message = array(
				'type'    => 'success',
				'message' => sprintf( 'Admin bar order was reset.', 'ub' ),
			);
			$this->uba->add_message( $message );
			wp_send_json_success();
		}

		/**
		 * Restore submenu
		 *
		 * Reset menu items to default order
		 */
		public function ajax_submenu_restore() {
			$id           = ! empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
			$nonce_action = $this->get_nonce_action( $id );
			$this->check_input_data( $nonce_action );
			$items = $this->get_value( 'settings', 'items', array() );
			$item  = array();
			if (
				is_array( $items )
				&& isset( $items[ $id ] )
			) {
				$item = $items[ $id ];
			}
			if ( ! isset( $item['roles'] ) ) {
				$item['roles'] = array();
			}
			wp_send_json_success( array( $item ) );
		}

		/**
		 * Keeps the menus in order based on saved orderings
		 *
		 * @hook   admin_bar_menu
		 * @since  1.6
		 * @access public
		 * @global $wp_admin_bar WP_Admin_Bar
		 *
		 * @return void
		 */
		public function reorder_menus() {
			global $wp_admin_bar;
			$order = $this->get_value( 'settings', 'order' );
			if ( ! $order || ! is_array( $order ) ) {
				return;
			}
			$nodes = $wp_admin_bar->get_nodes();
			// Remove all nodes.
			foreach ( $nodes as $node_id => $node ) {
				$wp_admin_bar->remove_node( $node_id );
			}
			// Add ordered nodes.
			foreach ( $order as $o ) {
				if ( isset( $nodes[ $o ] ) ) {
					$wp_admin_bar->add_node( $nodes[ $o ] );
					unset( $nodes[ $o ] );
				}
			}
			// Add rest of the nodes.
			if ( count( $nodes ) > 0 ) {
				foreach ( $nodes as $node ) {
					$wp_admin_bar->add_node( $node );
				}
			}
		}

		/**
		 * Enqueue scripts for the module.
		 *
		 * @since 2.0.0
		 *
		 * @uses  wp_enqueue_script()
		 * @uses  wp_register_script()
		 */
		public function enqueue_scripts( $module ) {
			if ( $this->module !== $module ) {
				return;
			}
			// Module scripts.
			$file = branda_files_url( 'modules/admin/assets/js/jquery.classywiggle.min.js' );
			wp_enqueue_script( 'jquery-effects-wingle', $file, array( 'jquery' ), '1.2.0', true );

			if ( ! wp_script_is( 'branda-dashicon-select' ) ) {
				$dashicon_select = branda_files_url( 'modules/admin/assets/js/admin/dashicon-select.js' );
				wp_enqueue_script( 'branda-dashicon-select', $dashicon_select, array( 'jquery' ), $this->build, true );
			}

			if ( ! wp_style_is( 'branda-dashicon-select' ) ) {
				wp_enqueue_style(
					'branda-dashicon-select',
					branda_files_url( 'modules/admin/assets/css/admin/dashicon-select.css' ),
					array(),
					$this->build
				);
			}
		}

		/**
		 * Delete admin bar menu orders.
		 */
		public function delete_admin_bar_menu_nodes() {
			$this->set_value( 'settings', 'nodes', null );
		}

		/**
		 * Get admin bar menu items.
		 *
		 * @param WP_Admin_Bar $wp_admin_bar Passed by reference.
		 */
		public function get_admin_bar_menu_nodes( $wp_admin_bar ) {
			$value = $this->get_value( 'settings', 'nodes' );
			if ( empty( $value ) ) {
				$value = array();
				$nodes = $wp_admin_bar->get_nodes();
				foreach ( $nodes as $node ) {
					$title = strip_tags( $node->title );
					if ( empty( $title ) ) {
						continue;
					}
					if ( empty( $node->parent ) || 'top-secondary' === $node->parent ) {
						$value[ $node->id ] = $title;
					}
				}
				$value['wp-logo']     = __( 'WordPress menu', 'ub' );
				$value['site-name']   = __( 'Site menu', 'ub' );
				$value['my-sites']    = __( 'My Sites', 'ub' );
				$value['new-content'] = __( 'Add New', 'ub' );
				$value['comments']    = __( 'Comments', 'ub' );
				$value['updates']     = __( 'Updates', 'ub' );
				// Add or replace names.
				asort( $value );
				$this->set_value( 'settings', 'nodes', $value );
			}
		}

		/**
		 * Build form with options.
		 *
		 * @since 2.8.6
		 */
		protected function set_options() {
			/**
			 * Disabled menus
			 */
			$disabled_menus = $this->get_value( 'settings', 'nodes', array() );
			if ( is_array( $disabled_menus ) ) {
				$disabled_menus = array_map( 'trim', $disabled_menus );
				asort( $disabled_menus );
			}
			// Module options array.
			$options = array(
				'logo'       => array(
					'title'       => __( 'Logo', 'ub' ),
					'description' => __( 'Replace the default WordPress logo in the admin bar with your own. ', 'ub' ),
					'fields'      => array(
						'logo' => array(
							'type' => 'media',
						),
					),
				),
				'visibility' => array(
					'title'       => __( 'Toolbar Visibility', 'ub' ),
					'description' => __( 'By default, the toolbar is visible only to the logged in users, however you can change its visibility for logged out users too.', 'ub' ),
					'fields'      => array(
						'visibility'              => array(
							'type'        => 'checkboxes',
							'label'       => __( 'Front-end', 'ub' ),
							'description' => __( 'Choose the user roles to show toolbar on front-end.', 'ub' ),
							'columns'     => 2,
							'options'     => $this->roles,
							'default'     => $this->default_roles,
						),
						'visibility][hidden_role' => array(
							'type'  => 'hidden',
							'value' => 1,

						),
					),
				),
				'items'      => array(
					'title'       => __( 'Menu Items', 'ub' ),
					'description' => __( 'Customize the menu by hiding menu items based on user roles, by reordering them or by adding your own custom menu items.', 'ub' ),
					'fields'      => array(
						'disabled_menus' => array(
							'type'         => 'checkboxes',
							'label'        => __( 'Menu Items', 'ub' ),
							'description'  => __( 'Choose the menu items which needs to be hidden.', 'ub' ),
							'columns'      => 2,
							'options'      => $disabled_menus,
							'master'       => $this->get_name( 'items-visibility' ),
							'master-value' => 'hide',
							'display'      => 'sui-tab-content',
						),
						'wp_menu_roles'  => array(
							'type'         => 'checkboxes',
							'label'        => __( 'User Roles', 'ub' ),
							'description'  => __( 'Choose the user roles to hide the chosen menu items.', 'ub' ),
							'columns'      => 2,
							'options'      => $this->roles,
							'master'       => $this->get_name( 'items-visibility' ),
							'master-value' => 'hide',
							'display'      => 'sui-tab-content',
						),
						'visibility'     => array(
							'type'        => 'sui-tab',
							'label'       => __( 'Visibility', 'ub' ),
							'description' => __( 'Choose whether you want to show all the menu items or hide specific menu items from particular user roles.', 'ub' ),
							'options'     => array(
								'all'  => __( 'Show All', 'ub' ),
								'hide' => __( 'Hide Specific Items', 'ub' ),
							),
							'default'     => 'all',
							'slave-class' => $this->get_name( 'items-visibility' ),
						),
						'list'           => array(
							'type'         => 'callback',
							'callback'     => array( $this, 'get_list' ),
							'master'       => $this->get_name( 'items-custom-entries' ),
							'master-value' => 'show',
							'display'      => 'sui-tab-content',
						),
						'custom-entries' => array(
							'type'        => 'sui-tab',
							'label'       => __( 'Custom entries', 'ub' ),
							'description' => __( 'Choose whether you want to show custom entries to the menu or not.', 'ub' ),
							'options'     => array(
								'hide' => __( 'Hide', 'ub' ),
								'show' => __( 'Show', 'ub' ),
							),
							'default'     => 'show',
							'slave-class' => $this->get_name( 'items-custom-entries' ),
						),
						'reorder'        => array(
							'label'       => __( 'Reorder menu items', 'ub' ),
							'description' => __( 'Click \'Reorder Menus\' then drag and drop menu items to reorder. \'Restore Default Order\' reverts them back to their original order.', 'ub' ),
							'type'        => 'callback',
							'callback'    => array( $this, 'get_reorder' ),
						),
					),
				),
				'css'        => array(
					'title'       => __( 'Custom CSS', 'ub' ),
					'description' => __( 'Add custom CSS styles to the admin bar. No other part of WordPress will be affected.', 'ub' ),
					'fields'      => array(
						'css' => array(
							'type'          => 'css_editor',
							'placeholder'   => esc_html__( 'Enter custom CSS here…', 'ub' ),
							'ace_selectors' => array(
								array(
									'title'     => '',
									'selectors' => array(
										'#wpadminbar' => __( 'Admin Bar', 'ub' ),
										'#wpadminbar .ab-item:before, #wpadminbar .ab-item .ab-icon:before' => __( 'Admin Bar Icon', 'ub' ),
										'#wpadminbar a.ab-item, #wpadminbar span.ab-label' => __( 'Menu Item', 'ub' ),
									),
								),
							),
						),
					),
				),
				'builders'   => array(
					'title'       => __( 'Improve page builder compatibility', 'ub' ),
					'description' => __( 'If you are using a page builder and experiencing compatibility issues with the Admin Bar feature, add the GET parameters for your page builder, and Branda will not overwrite the settings on those pages.', 'ub' ),
					'fields'      => array(
						'get_params' => array(
							'type'        => 'textarea',
							'label'       => __( 'GET parameters', 'ub' ),
							'placeholder' => __( 'Add GET parameters here', 'ub' ),
							'description' => sprintf( __( 'Add parameters one per line. If you are having trouble locating the GET parameters for your page builder, you can find parameters for popular builders %1$shere%2$s.', 'ub' ), '<a href="#" id="branda_popular_builders" data-a11y-dialog-show="branda-dialog-add-popular-builders">', '</a>' ),
						),
					),
				),
			);
			$this->options = $options;
		}

		/**
		 * Output admin bar content.
		 *
		 * @since 1.8.8
		 */
		public function logo_output() {
			$value = $this->get_value( 'logo' );
			if ( empty( $value ) ) {
				return;
			}
			// Logo.
			$src = '';
			if ( isset( $value['logo_meta'] ) ) {
				$src = $value['logo_meta'][0];
			} else {
				$img = wp_get_attachment_image_src( $value['logo'] );
				if ( is_array( $img ) ) {
					$src = $img[0];
				}
			}

			$has_svg_ext = self::has_svg_ext( $src );

			// Spitting conditions for better readability and maintainability.
			if ( empty( $src ) ) {
				return;
			} elseif ( $has_svg_ext && ! self::is_valid_svg( $src ) ) {
				return;
			} elseif ( ! $has_svg_ext && false === wp_getimagesize( $src ) ) {
				return;
			}

			/**
			 * CSS template
			 */
			$template = $this->get_template_name( 'css/logo' );
			$args     = array(
				'id'      => $this->get_name( 'logo' ),
				'src'     => esc_url( $src ),
				'is_svg'  => preg_match( '/svg$/i', $src ),
				'base'    => '#a0a5aa',
				'focus'   => '#00a0d2',
				'current' => '#fff',
			);
			global $_wp_admin_css_colors;
			$color_scheme = get_user_option( 'admin_color' );
			if ( ! empty( $_wp_admin_css_colors[ $color_scheme ]->icon_colors ) ) {
				$icon_colors = $_wp_admin_css_colors[ $color_scheme ]->icon_colors;
				$args        = wp_parse_args( $icon_colors, $args );
			} elseif ( ! empty( $_wp_admin_css_colors['fresh']->icon_colors ) ) {
				$icon_colors = $_wp_admin_css_colors['fresh']->icon_colors;
				$args        = wp_parse_args( $icon_colors, $args );
			}
			/**
			 * Allow to change logo css args
			 *
			 * @since 3.1.0
			 */
			$args = apply_filters( 'branda_admin_bar_logo_css_args', $args );
			/**
			 * Allow to change logo css
			 *
			 * @since 3.1.0
			 */
			echo apply_filters(
				'branda_admin_bar_logo_css',
				$this->render( $template, $args, true ),
				$args
			);
		}

		/**
		 * Reorder menu items settings.
		 *
		 * @return string $content
		 */
		public function get_reorder() {
			$content  = '<div class="sui-row">';
			$args     = array(
				'id'   => 'ub_admin_bar_start_ordering',
				'text' => __( 'Reorder Menus', 'ub' ),
			);
			$content .= $this->button( $args );
			$args     = array(
				'data' => array(
					'modal-open' => $this->get_name( 'reset' ),
				),
				'text' => __( 'Reset Default Order', 'ub' ),
				'sui'  => 'ghost',
			);
			$content .= $this->button( $args );
			$content .= '</div>';
			/**
			 * Footer.
			 */
			$footer   = '';
			$args     = array(
				'data' => array(
					'modal-close' => '',
				),
				'text' => __( 'Cancel', 'ub' ),
				'sui'  => 'ghost',
			);
			$footer  .= $this->button( $args );
			$args     = array(
				'data'  => array(
					'nonce' => $this->get_nonce_value( 'order', 'reset' ),
				),
				'text'  => __( 'Yes, reset!', 'ub' ),
				'sui'   => array(
					'ghost',
					'red',
				),
				'class' => $this->get_name( 'reset' ),
			);
			$footer  .= $this->button( $args );
			$args     = array(
				'id'      => $this->get_name( 'reset' ),
				'title'   => __( 'Are you sure?', 'ub' ),
				'content' => __( 'Are you sure to reset custom order?', 'ub' ),
				'footer'  => array(
					'content' => $footer,
					'classes' => array(
						'sui-actions-center',
					),
				),
				'classes' => array( 'sui-modal-sm' ),
			);
			$content .= $this->sui_dialog( $args );
			return $content;
		}

		/**
		 * List of existing elements.
		 *
		 * @since 3.0.0
		 *
		 * @return string $content
		 */
		public function get_list() {
			$content = '';
			$nonce   = $this->get_nonce_value( 'new' );
			// Top button.
			$args     = array(
				'data'  => array(
					'nonce' => $nonce,
				),
				'icon'  => 'plus',
				'text'  => __( 'Add Custom Item', 'ub' ),
				'sui'   => 'magenta',
				'class' => 'branda-admin-bar-item-edit',
			);
			$content .= '<div class="sui-box-builder">';
			$content .= '<div class="sui-box-builder-header">';
			$content .= $this->button( $args );
			$content .= '</div>'; // Box Builder Header
			// List.
			$items    = $this->get_value( 'settings', 'items' );
			$content .= sprintf(
				'<div class="sui-box-builder-body%s">',
				empty( $items ) ? '' : ' branda-has-items'
			);
			$content .= '<div class="sui-box-builder-fields">';
			$dialogs  = '';
			if ( is_array( $items ) ) {
				$template = $this->get_template_name( 'row' );
				foreach ( $items as $id => $item ) {
					$args     = array(
						'id'    => $id,
						'title' => $item['title'],
						'icon'  => $item['icon'],
						'nonce' => $this->get_nonce_value( $id ),
					);
					$content .= $this->render( $template, $args, true );
				}
			}
			$content .= '</div>'; // Box Builder Fields.
			$args     = array(
				'data'  => array(
					'nonce' => $nonce,
				),
				'icon'  => 'plus',
				'text'  => __( 'Add Custom Item', 'ub' ),
				'sui'   => 'dashed',
				'class' => 'branda-admin-bar-item-edit',
			);
			$content .= $this->button( $args );
			$content .= sprintf(
				'<div class="sui-description %s">%s</div>',
				is_array( $items ) && 0 < count( $items ) ? 'sui-hidden' : '',
				esc_html__( 'No custom menu item added yet. Click on “+ Add custom Item” to add your first custom menu item using a simple wizard.', 'ub' )
			);
			$content .= '</div>'; // Box Builder Body.
			$content .= '</div>'; // Box Builder.
			return $content;
		}

		/**
		 * Get SUI configuration for modal window.
		 *
		 * @param array $item Modal item array.
		 *
		 * @since 3.0.0
		 *
		 * @return array $config Configuration of modal window.
		 */
		private function get_sui_tabs_config( $item = array() ) {
			$config = array(
				array(
					'tab'      => __( 'General', 'ub' ),
					'tab_name' => 'general',
					'fields'   => array(
						'title'  => array(
							'label'       => __( 'Menu title', 'ub' ),
							'value'       => isset( $item['title'] ) ? $item['title'] : '',
							'description' => array(
								'content'  => __( 'You can also paste the full URL of an image instead of text title. For e.g. http://example.com/img.png', 'ub' ),
								'position' => 'bottom',
							),
							'sui-row'     => 'begin',
							'required'    => 'required',
						),
						'icon'   => array(
							'type'        => 'callback',
							'label'       => __( 'Icon', 'ub' ),
							'callback'    => array( $this, 'dashicons' ),
							'sui-row'     => 'end',
							'description' => array(
								'content'  => __( 'Choose an icon for your custom menu item.', 'ub' ),
								'position' => 'bottom',
							),
							'value'       => isset( $item['icon'] ) ? $item['icon'] : '',
						),
						'url'    => array(
							'type'    => 'sui-tab',
							'label'   => __( 'Redirect users to', 'ub' ),
							'options' => array(
								'none'     => __( 'None', 'ub' ),
								'main'     => __( 'Main Site', 'ub' ),
								'current'  => __( 'Current Site', 'ub' ),
								'wp-admin' => __( 'WP Admin Area', 'ub' ),
								'custom'   => __( 'Custom URL', 'ub' ),
							),
							'default' => 'none',
							'value'   => isset( $item['url'] ) ? $item['url'] : 'none',
						),
						'custom' => array(
							'label'       => __( 'URL', 'ub' ),
							'placeholder' => esc_attr__( 'E.g. http://example.com', 'ub' ),
							'value'       => isset( $item['custom'] ) ? $item['custom'] : '',
							'group'       => array(
								'begin'   => true,
								'classes' => array(
									'sui-border-frame',
									$this->get_name( 'url-options' ),
								),
							),
						),
						'target' => array(
							'type'    => 'sui-tab',
							'label'   => __( 'Open link in', 'ub' ),
							'options' => array(
								'_blank' => __( 'New Tab', 'ub' ),
								'_self'  => __( 'Same Tab', 'ub' ),
							),
							'default' => 'current',
							'value'   => isset( $item['target'] ) ? $this->get_target( $item['target'] ) : '_self',
							'group'   => array(
								'end' => true,
							),
						),
					),
				),
				array(
					'tab'      => __( 'Submenu', 'ub' ),
					'tab_name' => 'submenu',
					'fields'   => array(
						'items' => array(
							'description' => __( 'Reorder the submenu items by dragging and dropping as per your need.', 'ub' ),
							'type'        => 'callback',
							'callback'    => array( $this, 'submenu_items_list' ),
						),
					),
				),
				array(
					'tab'      => __( 'Visibility', 'ub' ),
					'tab_name' => 'visibility',
					'fields'   => array(
						'roles' => array(
							'type'        => 'checkboxes',
							'columns'     => 3,
							'label'       => __( 'User Roles', 'ub' ),
							'description' => __( 'Select the user roles which are allowed to see this menu.', 'ub' ),
							'options'     => $this->roles,
							'value'       => isset( $item['roles'] ) ? $item['roles'] : array(),
						),
					),
				),
			);
			// Remove multisite show.
			if ( ! $this->is_network ) {
				unset( $config[0]['fields']['redirect']['options']['main'] );
			}
			return $config;
		}

		/**
		 * Select dashicon from WP.
		 *
		 * @param integer $id ID.
		 * @param string  $value Value.
		 *
		 * @since 3.0.0
		 *
		 * @return string
		 */
		public function dashicons( $id = '{{data.id}}', $value = '' ) {
			$template = $this->get_template_name( 'dashicons' );
			$args     = array(
				'id'        => $id,
				'value'     => $value,
				'indicator' => $this->sui_accordion_indicator(),
				'list'      => include branda_dir( '/etc/dashicons.php' ),
			);
			$content  = $this->render( $template, $args, true );
			return $content;
		}

		/**
		 * Get SUI configuration for new submenu.
		 *
		 * @param array  $item Submenu item.
		 * @param string $id   ID.
		 *
		 * @since 3.0.0
		 *
		 * @return array $config Configuration of modal window.
		 */
		private function get_sui_submenu_config( $item = array(), $id = '{{{data.id}}}' ) {
			$config = array(
				'submenu][' . $id . '][title'  => array(
					'label'    => __( 'Title', 'ub' ),
					'value'    => isset( $item['title'] ) ? $item['title'] : '',
					'sui-row'  => 'begin',
					'required' => 'required',
					'classes'  => array(
						$this->get_name( 'submenu-title' ),
					),
				),
				'submenu][' . $id . '][target' => array(
					'type'    => 'sui-tab',
					'label'   => __( 'Open link in', 'ub' ),
					'options' => array(
						'_blank' => __( 'New Tab', 'ub' ),
						'_self'  => __( 'Same Tab', 'ub' ),
					),
					'default' => 'current',
					'value'   => isset( $item['target'] ) ? $this->get_target( $item['target'] ) : '_self',
					'sui-row' => 'end',
				),
				'submenu][' . $id . '][url'    => array(
					'type'    => 'sui-tab',
					'label'   => '',
					'options' => array(
						'admin'  => __( 'Admin Page', 'ub' ),
						'site'   => __( 'Site Page', 'ub' ),
						'custom' => __( 'External', 'ub' ),
					),
					'default' => 'none',
					'value'   => isset( $item['url'] ) ? $item['url'] : 'none',
					'content' => array(
						'admin'  => sprintf(
							'<label class="sui-label">%s</label><input type="text" aria-describedby="input-description" class="sui-form-control" placeholder="%s" name="branda[submenu][%s][url_admin]" value="%s" data-default="" /><p class="sui-description">%s</p>',
							esc_html__( 'URL', 'ub' ),
							esc_attr__( 'E.g. media.php', 'ub' ),
							esc_attr( $id ),
							isset( $item['url_admin'] ) ? $item['url_admin'] : '',
							sprintf(
								__( 'URL is relative to %s', 'ub' ),
								$this->bold(
									admin_url()
								)
							)
						),
						'site'   => sprintf(
							'<label class="sui-label">%s</label><input type="text" aria-describedby="input-description" class="sui-form-control" placeholder="%s" name="branda[submenu][%s][url_site]" value="%s" data-default="" />',
							esc_html__( 'URL', 'ub' ),
							esc_attr__( 'E.g. http://example.com', 'ub' ),
							esc_attr( $id ),
							isset( $item['url_site'] ) ? $item['url_site'] : ''
						),
						'custom' => sprintf(
							'<label class="sui-label">%s</label><input type="text" aria-describedby="input-description" class="sui-form-control" placeholder="%s" name="branda[submenu][%s][url_custom]" value="%s" data-default="" />',
							esc_html__( 'URL', 'ub' ),
							esc_attr__( 'E.g. http://example.com', 'ub' ),
							esc_attr( $id ),
							isset( $item['url_custom'] ) ? $item['url_custom'] : ''
						),
					),
				),
			);
			return $config;
		}

		/**
		 * Submenu items list.
		 *
		 * @param int $id ID.
		 *
		 * @return string
		 */
		public function submenu_items_list( $id ) {
			$content = '';
			$data    = array();
			$items   = $this->get_value( 'settings', 'items' );
			if ( isset( $items[ $id ] ) ) {
				$data = $items[ $id ];
			}
			$hide     = false;
			$content .= '<div class="sui-box-builder">';
			$content .= '<div class="sui-box-builder-body">';
			$content .= '<div class="sui-accordion branda-sui-accordion-sortable">';
			if ( isset( $data['submenu'] ) && is_array( $data['submenu'] ) ) {
				foreach ( $data['submenu'] as $id => $item ) {
					$hide     = true;
					$content .= '<div class="sui-accordion-item">';
					// Header.
					$content .= '<div class="sui-accordion-item-header">';
					$content .= '<div class="sui-accordion-item-title sui-accordion-item-action">';
					$content .= '<i class="sui-icon-drag" aria-hidden="true"></i>';
					$content .= $item['title'];
					$content .= '</div>';
					$args     = array(
						'only-icon' => true,
						'icon'      => 'trash',
						'sui'       => array(
							'red',
						),
						'classes'   => array(
							'sui-accordion-item-action',
							$this->get_name( 'submenu-delete' ),
						),
					);
					$content .= $this->button( $args );
					$content .= '<span class="branda-action-divider"></span>';
					$content .= $this->sui_accordion_indicator();
					$content .= '</div>';
					// Body.
					$content .= '<div class="sui-accordion-item-body">';
					$config   = $this->get_sui_submenu_config( $item, $id );
					$content .= $this->proceed_sui_config( $config );
					$content .= '</div>';
					$content .= '</div>';
				}
			}
			$content .= '</div>';
			/**
			 * extra add
			 */
			$content .= sprintf(
				'<div class="%s%s">',
				$this->get_name( 'no-submenu' ),
				$hide ? ' hidden' : ''
			);
			$args     = array(
				'icon'  => 'plus',
				'text'  => __( 'Add Item', 'ub' ),
				'sui'   => 'dashed',
				'class' => $this->get_name( 'submenu-add' ),
				'data'  => array(
					'template' => $this->get_name( 'submenu-add-template' ),
				),
			);
			$content .= sprintf(
				'%s<span class="sui-box-builder-message">%s</span>',
				$this->button( $args ),
				esc_html__( 'No submenu item added yet. Click on “+ Add Item” to add a submenu item', 'ub' )
			);
			$content .= '</div>';
			$content .= '</div>';
			$content .= '</div>';
			/**
			 * /extra add
			 */
			// $content .= '</div>';
			// Button.
			$content .= '<div class="sui-row">';
			$args     = array(
				'data'  => array(
					'template' => $this->get_name( 'submenu-add-template' ),
				),
				'class' => $this->get_name( 'submenu-add' ),
				'icon'  => 'plus',
				'text'  => __( 'Add Item', 'ub' ),
				'sui'   => 'blue',
			);
			$content .= '<div class="sui-actions-left">';
			$content .= $this->button( $args );
			$content .= '</div>';
			$content .= '</div>';
			$content .= sprintf(
				'<script type="text/html" id="tmpl-%s">',
				$this->get_name( 'submenu-add-template' )
			);
			$content .= $this->render( 'admin/modules/admin-bar/dialogs/submenu', array(), true );
			$content .= '</script>';
			return $content;
		}

		/**
		 * Renders after admin bad renderer.
		 *
		 * @hook   wp_after_admin_bar_render
		 *
		 * @since  1.6
		 * @access public
		 */
		public function after_admin_bar_render() {
			/**
			 * On front-end we do not have $uba object
			 */
			if ( ! is_object( $this->uba ) ) {
				return;
			}
			/**
			 * Check current module
			 */
			$module = $this->uba->get_current_module();
			if ( $this->module !== $module ) {
				return;
			}
			wp_nonce_field( $this->get_nonce_action(), $this->get_name( 'reorder-nonce' ), false );
		}

		/**
		 * Removes selected default menus from admin bar
		 *
		 * @since  1.0
		 * @access public
		 *
		 * @return void
		 */
		public function remove_menus_from_admin_bar() {
			global $current_user, $wp_admin_bar;
			$visibility = $this->get_value( 'items', 'visibility' );
			if ( 'hide' !== $visibility ) {
				return;
			}
			/**
			 * Roles to hide.
			 */
			$roles = array_keys( (array) $this->get_value( 'items', 'wp_menu_roles' ) );
			/**
			 * elements to hide
			 */
			$menus = array_keys( (array) $this->get_value( 'items', 'disabled_menus' ) );
			/**
			 * We are clear! nothing to hide!
			 */
			if ( empty( $menus ) ) {
				return;
			}
			/**
			 * proceed
			 */
			$hide = false;
			/**
			 * Always hide for non-logged users
			 */
			if ( is_user_logged_in() ) {
				$user       = wp_get_current_user();
				$user_roles = (array) $user->roles;
				foreach ( $roles as $role ) {
					if ( in_array( $role, $user_roles ) ) {
						/**
						 * exception for super
						 */
						if (
							$this->is_network
							&& 'administrator' === $role
							&& is_super_admin()
						) {
							break;
						}
						$hide = true;
						break;
					}
				}
				/**
				 * check super!
				 */
				if ( $this->is_network && ! $hide && in_array( 'super', $roles )
				) {
					$hide = current_user_can( 'manage_network' );
				}
			} else {
				$hide = true;
			}
			/**
			 * Not hide, we should go now!
			 */
			if ( ! $hide ) {
				return;
			}
			/**
			 * remove selected nodes
			 */
			foreach ( $menus as $id ) {
				$wp_admin_bar->remove_node( $id );
			}
		}

		/**
		 * Save item using ajax.
		 */
		public function ajax_save_item() {
			$nonce_action = $this->get_nonce_action( 'edit' );
			$this->check_input_data( $nonce_action, array( 'branda' ) );
			$data = $this->sanitize_request_payload( $_POST['branda'] );
			/**
			 * remove nonce
			 */
			if ( isset( $data['nonce'] ) ) {
				unset( $data['nonce'] );
			}
			$id = isset( $data['id'] ) ? $data['id'] : $this->generate_id( $data );
			if ( 'new' === $id ) {
				$id = $this->generate_id( $data );
			}
			$data['id'] = $id;
			if ( isset( $data['submenu'] ) ) {
				foreach ( $data['submenu'] as $submenu_id => $submenu ) {
					$data['submenu'][ $submenu_id ]['id'] = $submenu_id;
				}
			}
			$items = $this->get_value( 'settings', 'items', array() );
			// If not found, get from existing values.
			$before = array();
			if ( isset( $items[ $id ] ) ) {
				$before = $items[ $id ];
			}
			$items[ $id ] = wp_parse_args( $data, $before );
			/**
			 * Check deleted keys
			 */
			foreach ( $items[ $id ] as $key => $value ) {
				if ( isset( $data[ $key ] ) ) {
					continue;
				}
				unset( $items[ $id ][ $key ] );
			}
			$this->set_value( 'settings', 'items', $items );
			/**
			 * Send response in json.
			 */
			$title  = $items[ $id ]['title'];
			$is_url = filter_var( $title, FILTER_VALIDATE_URL );
			if ( $is_url ) {
				$title = sprintf(
					'<img src="%s" class="branda-image" title="%s" />',
					esc_url( $title ),
					esc_attr( $title )
				);
			} elseif ( ! empty( $items[ $id ]['icon'] ) ) {
				$title = sprintf(
					'<span class="dashicons dashicons-%s"></span>%s',
					esc_attr( $items[ $id ]['icon'] ),
					$title
				);
			}
			/**
			 * Compose return data
			 */
			$data = array(
				'id'            => $id,
				'title'         => $items[ $id ]['title'],
				'title_to_show' => $title,
				'nonce'         => $this->get_nonce_value( $id ),
			);
			wp_send_json_success( $data );
		}

		/**
		 * Delete item using ajax.
		 *
		 * @since 3.0.0
		 */
		public function ajax_delete_item() {
			$id           = ! empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
			$nonce_action = $this->get_nonce_action( $id );
			$this->check_input_data( $nonce_action, array( 'id' ) );
			$items = $this->get_value( 'settings', 'items', array() );
			if ( isset( $items[ $id ] ) ) {
				unset( $items[ $id ] );
				$result = $this->set_value( 'settings', 'items', $items );
				if ( $result ) {
					$message = array(
						'type'    => 'success',
						'message' => sprintf( 'Item was deleted.', 'ub' ),
					);
					$this->uba->add_message( $message );
					wp_send_json_success(
						array(
							'id'      => $id,
							'message' => __( 'Selected item was successfully deleted', 'ub' ),
						)
					);
				}
			}
			// Send ajax response.
			wp_send_json_error( array( 'message' => __( 'Selected item does not exists!', 'ub' ) ) );
		}

		/**
		 * Checks to see if user has access to the custom menu based on his roles
		 *
		 * @param array $roles Roles.
		 * @param bool  $keys  Keys.
		 *
		 * @return bool
		 */
		private function user_has_access( $roles, $keys = false ) {
			$user = wp_get_current_user();
			if ( empty( $user ) || ! is_array( $roles ) ) {
				return false;
			}
			if ( ! $keys && ( ! current_user_can( 'manage_network' ) && array_intersect( $roles, $user->roles ) )
				|| current_user_can( 'manage_network' ) && in_array( 'super', $roles )
			) {
				return true;
			} elseif ( $keys ) {
				foreach ( $roles as $key => $val ) {
					$val           = $key;
					$roles[ $key ] = $val;
				}
				if ( ( ! current_user_can( 'manage_network' ) && array_intersect( $roles, $user->roles ) )
					|| current_user_can( 'manage_network' ) && in_array( 'super', $roles )
				) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Adds custom menus to the admin bar.
		 *
		 * @since  1.0
		 * @access public
		 *
		 * @hook   admin_bar_menu
		 * @global $wp_admin_bar
		 * @global $current_user
		 *
		 * @return void
		 */
		public function add_custom_menus() {
			global $wp_admin_bar, $current_user;
			$enabled = $this->get_value( 'items', 'custom-entries', 'show' );
			if ( 'show' !== $enabled ) {
				return;
			}
			/**
			 * Admin bar menu objects.
			 *
			 * @var $menu UB_Admin_Bar_Menu
			 * @var $sub  UB_Admin_Bar_Menu
			 */
			$menus = $this->get_value( 'settings', 'items' );
			if ( is_array( $menus ) && ! empty( $menus ) ) {
				foreach ( $menus as $menu ) {
					$menu_roles = isset( $menu['roles'] ) ? $menu['roles'] : array();
					if ( ! is_array( $menu_roles ) ) {
						$menu_roles = array();
					}
					if (
						( is_user_logged_in() && $this->user_has_access( $menu_roles, true ) )
						||
						( ! is_user_logged_in() && isset( $menu_roles['guest'] ) )
					) {
						$args = array(
							'id'    => $this->get_name( $menu['id'] ),
							'title' => $this->get_title_image( $menu ),
							'href'  => $this->get_menu_link( $menu ),
							'meta'  => array(
								'target' => isset( $menu['target'] ) ? $this->get_target( $menu['target'] ) : false,
							),
						);
						$wp_admin_bar->add_menu( $args );
						if ( isset( $menu['submenu'] ) && is_array( $menu['submenu'] ) ) {
							foreach ( $menu['submenu'] as $sub ) {
								$args = array(
									'parent' => $this->get_name( $menu['id'] ),
									'id'     => $sub['id'],
									'title'  => $this->get_title_image( $sub ),
									'href'   => $this->get_menu_link( $sub ),
									'meta'   => array(
										'target' => isset( $sub['target'] ) ? $this->get_target( $sub['target'] ) : false,
									),
								);
								$wp_admin_bar->add_menu( $args );
							}
						}
					}
				}
			}
		}

		/**
		 * Retrieves image used as title if it's and image, returns url if it's not an image.
		 *
		 * @param array $item Menu item.
		 *
		 * @since  1.5
		 * @access public
		 *
		 * @return string $title
		 */
		private function get_title_image( $item ) {
			$title = isset( $item['title'] ) ? esc_html( $item['title'] ) : '';
			$icon  = '';
			if ( isset( $item['icon'] ) && ! empty( $item['icon'] ) ) {
				$icon = sprintf(
					'<span class="ab-icon ub-menu-item dashicons dashicons-%s"></span>',
					$item['icon']
				);
			} else {
				$is_valid_url = filter_var( $title, FILTER_VALIDATE_URL );
				if ( $is_valid_url ) {
					$title = sprintf(
						'<img src="%s" class="ub_admin_bar_image" alt="" />',
						$title
					);
				}
			}
			if ( empty( $icon ) ) {
				return $title;
			}
			$title = sprintf(
				'%s<span class="ab-label">%s</span>',
				$icon,
				$title
			);
			return $title;
		}

		/**
		 * Retrieves type of link/menu
		 * Values are admin_url, site_url, # or url
		 *
		 * @param array $item Menu item.
		 *
		 * @since 1.5
		 *
		 * @return string
		 */
		private function get_menu_link( $item ) {
			if ( ! isset( $item['url'] ) ) {
				return false;
			}
			switch ( $item['url'] ) {
				case 'admin':
					return isset( $item['url_admin'] ) ? admin_url( $item['url_admin'] ) : false;
				case 'current':
					return site_url();
				case 'custom':
					if ( isset( $item['url_custom'] ) ) {
						return $item['url_custom'];
					}
					return isset( $item['custom'] ) ? $item['custom'] : '';
				case 'main':
					return network_site_url();
				case 'none':
					return false;
				case 'site':
					return isset( $item['url_site'] ) ? site_url( $item['url_site'] ) : false;
				case 'wp-admin':
					return admin_url();
			}
			return false;
		}

		/**
		 * Add custom styles to html head section
		 *
		 * @hook   wp_head
		 * @hook   admin_head
		 *
		 * @since  1.8.5
		 * @access public
		 */
		public function print_style_tag() {
			$show = get_user_option( 'show_admin_bar_front' );
			$show = $this->hide_toolbar_based_on_user_role( $show );
			if ( ! is_admin() && ( ! $show || 'false' === $show ) ) {
				return;
			}
			/**
			 * CSS
			 */
			$mobile = array();
			$items  = $this->get_value( 'settings', 'items', array() );
			foreach ( $items as $id => $data ) {
				$item = wp_parse_args( $data, $this->item_defaults );
				if ( empty( $item['icon'] ) ) {
					continue;
				}
				if ( 'show' === $item['mobile'] ) {
					$mobile[] = sprintf( '#wpadminbar ul#wp-admin-bar-root-default>li#wp-admin-bar-%s', $this->get_name( $id ) );
					// $mobile[] = sprintf( '#%s', $this->get_name( $id ) );
				}
			}

			$template = $this->get_template_name( 'css/common' );
			$args     = array(
				'id'     => $this->get_name(),
				'styles' => $this->styles(),
				'mobile' => implode( ',' . PHP_EOL, $mobile ),
			);
			$this->render( $template, $args );
		}

		/**
		 * Returns menus style.
		 *
		 * @param bool $editor If true, it's in editor mode.
		 *
		 * @since 1.6
		 *
		 * @return array|mixed|string|void
		 */
		public function styles( $editor = false ) {
			$style      = <<<UBSTYLE
			.ub_admin_bar_image{
				max-width: 100%;
				max-height: 28px;
				padding: 2px 0;
			}
			#wpadminbar .ub-menu-item.dashicons {
				font-family: dashicons;
				top: 2px;
			}
UBSTYLE;
			$save_style = stripslashes( $this->get_value( 'css', 'css', false ) );
			if ( $editor ) {
				return '<style>' . $save_style . '</style>';
			}
			$styles = empty( $save_style ) ? $style : $save_style;
			$styles = $this->prefix_styles( $styles );
			return $styles;
		}

		/**
		 * Adds prefix to the define styles
		 *
		 * @param string $styles Styles.
		 *
		 * @since  1.6
		 * @access private
		 *
		 * @return array|string
		 */
		private function prefix_styles( $styles ) {
			$cache_id = md5( $styles );
			$style    = wp_cache_get( $cache_id );
			if ( ! $style ) {
				$style = $this->style_normilize( $styles );
				wp_cache_set( $cache_id, $style );
			}
			return $style;
		}

		/**
		 * Add prefix to CSS rules. Supports media queries.
		 *
		 * @param string $css Style.
		 *
		 * @since  1.8.3.2
		 * @access public
		 *
		 * @return string style
		 */
		private function style_normilize( $css ) {
			if ( empty( $css ) ) {
				return $css;
			}
			$pattern = '~@media\b[^{]*({((?:[^{}]+|(?1))*)})~';
			preg_match_all( $pattern, $css, $matches, PREG_PATTERN_ORDER );
			$style_normilized = '';
			$media_wraps      = $matches[0];
			$media_chunks     = $matches[2];
			foreach ( $media_chunks as $key => $media_chunk ) {
				$whole_chunk = $media_wraps[ $key ];
				$css         = str_replace( $whole_chunk, '', $css );
				$wrap        = explode( '{', $whole_chunk );
				$wrap        = $wrap[0];
				if ( ! empty( $media_chunk ) ) {
					$styles = array_filter( explode( '}', $media_chunk ) );
					$output = array();
					foreach ( $styles as $style ) {
						if ( trim( $style ) !== '' ) {
							if ( ! preg_match( '/#wpadminbar/', $style ) ) {
								$output[] = '#wpadminbar ' . $style . '}';
							} else {
								$output[] = $style . '}';
							}
						}
					}
					$media_chunk = implode( '', $output );
				}
				$style_normilized .= $wrap . '{' . $media_chunk . '}';
			}
			if ( ! empty( $css ) ) {
				$styles = array_filter( explode( '}', $css ) );
				$output = array();
				foreach ( $styles as $style ) {
					if ( trim( $style ) !== '' ) {
						if ( ! preg_match( '/\#wpadminbar/', $style ) ) {
							$output[] = '#wpadminbar ' . $style . '}';
						} else {
							$output[] = $style . '}';
						}
					}
				}
				$css              = implode( '', $output );
				$style_normilized = $css . $style_normilized;
			}
			return $style_normilized;
		}

		/**
		 * Add SUI dialog
		 *
		 * @since 3.1.0
		 *
		 * @param string $content Current module content.
		 * @param array  $module Current module.
		 */
		public function add_dialog( $content, $module ) {
			if ( $this->module !== $module['module'] ) {
				return $content;
			}
			/**
			 * Dialog ID
			 */
			$dialog_id = $this->get_name( 'edit' );
			/**
			 * Custom Item Row
			 */
			$template = $this->get_template_name( 'tmpl/row' );
			$args     = array(
				'template'  => $this->get_template_name( 'row' ),
				'dialog_id' => $dialog_id,
			);
			$content .= $this->render( $template, $args, true );

			$content .= $this->add_popular_builders_dialog();

			/**
			 * Dialog delete
			 */
			$content .= $this->get_dialog_delete(
				null,
				array(
					'title'       => __( 'Delete Custom Menu Item', 'ub' ),
					'description' => __( 'Are you sure you wish to permanently delete this custom menu item?', 'ub' ),
				)
			);
			/**
			 * Dialog settings
			 */
			$args     = array(
				'dialog_id'     => $dialog_id,
				'nonce_edit'    => $this->get_nonce_value( 'edit' ),
				'nonce_restore' => $this->get_nonce_value( 'restore' ),
				'icons'         => $this->dashicons(),
			);
			$template = $this->get_template_name( 'dialogs/edit' );
			$content .= $this->render( $template, $args, true );
			$panes    = array( 'general', 'submenu', 'visibility' );
			foreach ( $panes as $pane ) {
				$template = $this->get_template_name( 'tmpl/panes/' . $pane );
				$content .= $this->render( $template, $args, true );
			}
			return $content;
		}

		/**
		 * Add popular builders dialog.
		 *
		 * @return string
		 */
		private function add_popular_builders_dialog() {
			$template = $this->get_template_name( 'dialogs/popular-builders' );
			$dialog   = $this->render( $template, array(), true );

			// Footer.
			$cancel_args = array(
				'data' => array(
					'modal-close' => '',
				),
				'text' => __( 'Cancel', 'ub' ),
				'sui'  => 'ghost',
			);
			$footer      = $this->button( $cancel_args );
			$add_args    = array(
				'text'     => __( 'Add', 'ub' ),
				'class'    => 'branda-admin-bar-add-popular-builders',
				'icon'     => 'check',
				'disabled' => true,
			);
			$footer     .= $this->button( $add_args );
			$dialog_args = array(
				'id'      => $this->get_name( 'add-popular-builders' ),
				'content' => $dialog,
				'footer'  => array(
					'content' => $footer,
					'classes' => array(
						'sui-space-between',
					),
				),
				'title'   => __( 'Builder GET parameters', 'ub' ),
				'classes' => array(
					'sui-modal-sm',
				),
			);

			return $this->sui_dialog( $dialog_args );
		}

		/**
		 * Get menu item
		 *
		 * @since 3.1.0
		 */
		public function ajax_get_item() {
			$id           = ! empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
			$nonce_action = $this->get_nonce_action( $id );
			$this->check_input_data( $nonce_action, array( 'id' ) );
			$items = $this->map_deep( 
				$this->get_value( 'settings', 'items', array() ),
				array( __CLASS__, 'esc_html' )
			);
			
			/**
			 * new
			 */
			if ( 'new' === $id ) {
				$item          = $this->item_defaults;
				$item['roles'] = array_combine( array_keys( $this->roles ), array_keys( $this->roles ) );
				wp_send_json_success( $item );
			} elseif ( isset( $items[ $id ] ) ) {
				$item = wp_parse_args( $items[ $id ], $this->item_defaults );
				wp_send_json_success( $item );
			}
			wp_send_json_error( array( 'message' => __( 'Selected item does not exists!', 'ub' ) ) );
		}

		/**
		 * Hide Toolbar based on admin roles (only frontend);
		 *
		 * @since 3.1.2
		 */
		public function hide_toolbar_based_on_user_role( $show ) {
			$value = $this->get_value( 'visibility', 'visibility', array() );
			if ( empty( $value ) ) {
				return $show;
			}
			if ( $this->is_builders() ) {
				return $show;
			}
			if ( ! is_array( $value ) ) {
				if ( 'visible' === $value ) {
					if ( ! is_user_logged_in() ) {
						return true;
					} else {
						return false;
					}
				}
				if ( 'hidden' === $value ) {
					return $show;
				}
				return $show;
			} else {
				if (
					! is_user_logged_in()
					&& isset( $value['guest'] )
					&& 'on' === $value['guest']
				) {
					return true;
				}
			}
			$allowed_roles = array_keys( $value );
			$show          = Branda_Helper::is_allowed_role( $allowed_roles );

			return $show;
		}

		/**
		 * Validate builder's GET params (uniqute and remove empty one)
		 */
		public function validate_builders() {
			$value = $this->get_value( 'builders', 'get_params', '' );
			if ( ! $value || ! is_string( $value ) ) {
				return;
			}

			$get_params       = preg_split( '/[, \r\n]/', $value );
			$validated_values = array();
			foreach ( $get_params as $param ) {
				if ( empty( $param ) ) {
					continue;
				}
				$validated_values[] = $param;
			}

			$validated_values = array_unique( $validated_values );

			$this->set_value( 'builders', 'get_params', implode( "\n", $validated_values ) );
		}

		/**
		 * Check is it builders Edit page or not
		 *
		 * @return boolean
		 */
		private function is_builders() {
			$value = $this->get_value( 'builders', 'get_params', '' );
			if ( ! $value || ! is_string( $value ) ) {
				return false;
			}

			$get_params = preg_split( '/[, \r\n]/', $value );
			foreach ( $get_params as $param ) {
				if ( empty( $param ) ) {
					continue;
				}
				$value = explode( '=', $param );
				if ( ! is_array( $value ) || empty( $value[0] ) ) {
					continue;
				}
				$get        = filter_input( INPUT_GET, $value[0] );
				$is_builder = ! is_null( $get );
				if ( ! empty( $value[1] ) ) {
					$is_builder = $is_builder && trim( $value[1] ) === $get;
				}
				if ( $is_builder ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Validate & Sanitize target attribute.
		 *
		 * @since 3.1.2
		 */
		private function get_target( $target ) {
			switch ( $target ) {
				case 'new':
				case '_blank':
					return '_blank';
				case 'current':
				case '_self':
				default:
					return false;
			}
			return false;
		}

		/**
		 * Sanitize module fields.
		 *
		 * @param mixed|array|string $data
		 * @return string
		 */
		public static function esc_data( $data ) {
			if ( empty( $data['key'] ) || empty( $data['value'] ) ) {
				return $data;
			}

			return sanitize_text_field( $data['value'] );
		}

		/**
		 * Escape module fields.
		 *
		 * @param mixed|array|string $data
		 * @return string
		 */
		public static function esc_html( $data ) {
			if ( empty( $data['key'] ) || empty( $data['value'] ) ) {
				return $data;
			}

			return esc_html( stripslashes( $data['value'] ) );
		}

	}
}
new Branda_Admin_Bar();
