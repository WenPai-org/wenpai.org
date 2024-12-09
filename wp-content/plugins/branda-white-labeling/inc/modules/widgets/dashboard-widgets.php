<?php
/**
 * Branda Dashboard Widgets class.
 *
 * @package Branda
 * @subpackage Widgets
 */
if ( ! class_exists( 'Branda_Dashboard_Widgets' ) ) {

	require_once dirname( __FILE__ ) . '/dashboard-widgets-widget.php';

	class Branda_Dashboard_Widgets extends Branda_Helper {
		protected $option_name     = 'ub_dashboard_widgets';
		protected $items_name      = 'ub_dashboard_widgets_items';
		private $available_widgets = 'ub_rwp_all_active_dashboard_widgets';

		private $priorities       = array( 'core', 'low', 'high' );
		private $types            = array( 'dashboard', 'dashboard-network' );
		private $widget_positions = array( 'normal', 'advanced', 'side' );

		/**
		 * Single item defaults
		 *
		 * @since 3.1.0
		 */
		private $item_defaults = array(
			'id'           => 'new',
			'title'        => '',
			'content'      => '',
			'content_meta' => '',
			'site'         => 'on',
			'network'      => 'on',
		);

		public function __construct() {
			parent::__construct();
			$this->module = 'dashboard-widgets';
			/**
			 * hooks
			 */
			add_filter( 'ultimatebranding_settings_dashboard_widgets', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_dashboard_widgets_process', array( $this, 'update' ) );
			add_filter( 'ultimatebranding_settings_dashboard_widgets_process', array( $this, 'update_order' ), 11 );
			/**
			 * remove widgets
			 */
			add_action( 'wp_dashboard_setup', array( $this, 'remove_wp_dashboard_widgets' ), PHP_INT_MAX );
			add_action( 'wp_network_dashboard_setup', array( $this, 'remove_wp_dashboard_widgets' ), PHP_INT_MAX );
			/**
			 * save available boxes
			 *
			 * @since 2.1.0
			 */
			add_action( 'wp_dashboard_setup', array( $this, 'save_available_widgets' ), 99999 );
			add_action( 'wp_network_dashboard_setup', array( $this, 'save_available_widgets' ), 99999 );
			/**
			 * Dashboard Welcome
			 */
			$message = $this->get_value( 'welcome', 'text' );
			if ( ! empty( $message ) && is_string( $message ) ) {
				add_action( 'welcome_panel', array( $this, 'render_custom_welcome_message' ) );
				add_filter( 'get_user_metadata', array( $this, 'remove_dashboard_welcome' ), 10, 4 );
			}
			/**
			 * upgrade options
			 *
			 * @since 3.0.0
			 */
			add_action( 'init', array( $this, 'upgrade_options' ) );
			/**
			 * add options names
			 *
			 * @since 2.1.0
			 */
			add_filter( 'ultimate_branding_options_names', array( $this, 'add_options_names' ) );
			/**
			 * Add dialog
			 *
			 * @since 3.0,0
			 */
			add_filter( 'branda_get_module_content', array( $this, 'add_dialog' ), 10, 2 );
			/**
			 * Handle AJAX actions
			 *
			 * @since 3.0.0
			 */
			add_action( 'wp_ajax_branda_dashboard_widget_save', array( $this, 'ajax_save_item' ) );
			add_action( 'wp_ajax_branda_dashboard_widget_delete', array( $this, 'ajax_delete_item' ) );
			/**
			 * AJAX get single item
			 *
			 * @since 3.1.0
			 */
			add_action( 'wp_ajax_branda_dashboard_widgets_get', array( $this, 'ajax_get_item' ) );
			/**
			 * AJAX reset visibility
			 *
			 * @since 3.1.0
			 */
			add_action( 'wp_ajax_branda_dashboard_widget_visibility_reset', array( $this, 'ajax_visibility_reset' ) );
			/**
			 * text widgets
			 */
			$has_items = $this->has_items();
			if ( $has_items ) {
				add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ), 467 );
				add_action( 'wp_network_dashboard_setup', array( $this, 'add_dashboard_widgets' ), 467 );
				add_action( 'wp_user_dashboard_setup', array( $this, 'add_dashboard_widgets' ), 467 );
				add_action( 'admin_print_styles', array( $this, 'admin_print_styles' ) );
			}
			/**
			 * delete widget
			 *
			 * @since 3.1.0
			 */
			add_action( 'branda_delete_available_widget', array( $this, 'delete_available_widget' ), 10, 1 );
		}

		/**
		 * Upgrade option
		 *
		 * @since 3.0.0
		 */
		public function upgrade_options() {
			$update = false;
			$data   = $this->get_value();
			/**
			 * Remove Dashboard Widgets
			 */
			$option = branda_get_option_filtered( 'ub_remove_wp_dashboard_widgets' );
			if (
				isset( $option['remove_dashboard'] )
				&& isset( $option['remove_dashboard']['wp_widgets'] )
			) {
				if ( ! isset( $data['visibility'] ) ) {
					$data['visibility'] = array(
						'wp_widgets' => array(),
					);
					$update             = true;
				}
				foreach ( $option['remove_dashboard']['wp_widgets'] as $key => $value ) {
					if ( empty( $value ) || ! $value ) {
						continue;
					}
					$data['visibility']['wp_widgets'][ $key ] = $value;
					$update                                   = true;
				}
				branda_delete_option( 'ub_remove_wp_dashboard_widgets' );
			}
			/**
			 * Custom Welcome Message
			 */
			$option = branda_get_option( 'ub_custom_welcome_message' );
			if ( ! empty( $option ) ) {
				if ( isset( $option['dashboard_widget'] ) ) {
					if ( isset( $option['welcome'] ) ) {
						$data['welcome'] = array();
						$update          = true;
					}
					foreach ( $option['dashboard_widget'] as $key => $value ) {
						$data['welcome'][ $key ] = $option['dashboard_widget'][ $key ];
						$update                  = true;
					}
				}
				branda_delete_option( 'ub_custom_welcome_message' );
			}
			/**
			 * Dashboard Text Widget
			 */
			$options = branda_get_option_filtered( 'wpmudev_dashboard_text_widgets_options' );
			if ( ! empty( $options ) ) {
				$data = array();
				foreach ( $options as $one ) {
					$id          = $this->generate_id( $one );
					$data[ $id ] = array(
						'id'           => $id,
						'title'        => isset( $one['title'] ) ? $one['title'] : '',
						'content'      => isset( $one['content'] ) ? $one['content'] : '',
						'content_meta' => isset( $one['content_parse'] ) ? $one['content_parse'] : '',
						'site'         => isset( $one['show-on'] ) && isset( $one['show-on']['site'] ) ? $one['show-on']['site'] : 'on',
						'network'      => isset( $one['show-on'] ) && isset( $one['show-on']['network'] ) ? $one['show-on']['network'] : 'on',
					);
				}
				if ( ! empty( $data ) ) {
					branda_update_option( $this->items_name, $data );
					branda_delete_option( 'wpmudev_dashboard_text_widgets_options' );
				}
			}
			/**
			 * save
			 */
			if ( $update ) {
				$this->update_value( $data );
			}
		}

		/**
		 * Set options
		 *
		 * @since 2.0.0
		 */
		protected function set_options() {
			if ( ! is_admin() ) {
				return;
			}
			$available_widgets = branda_get_option_filtered( $this->available_widgets );
			$options           = array(
				'visibility' => array(
					'title'       => __( 'Widget Visibility', 'ub' ),
					'description' => $this->is_network ? __( 'Choose the widgets which you want to remove from all the WP dashboards on your network.', 'ub' )
							: __( 'Choose the widgets which you want to remove from the WP dashboard.', 'ub' ),
					'fields'      => array(
						'wp_widgets' => array(
							'type'    => 'checkboxes',
							'options' => $available_widgets,
						),
					),
				),
				'welcome'    => array(
					'title'       => __( 'Dashboard Welcome', 'ub' ),
					'description' => __( 'Customize the default welcome message displayed in the dashboard welcome wizard.', 'ub' ),
					'fields'      => array(
						'shortocode' => array(
							'type'        => 'sui-tab',
							'label'       => __( 'Shortcodes', 'ub' ),
							'description' => __( 'Choose whether you want to allow shortcode parsing within the welcome message or not. Be careful it can break compatibility with themes with UI builders.', 'ub' ),
							'options'     => array(
								'on'  => __( 'Parse Shortcodes', 'ub' ),
								'off' => __( 'Stop Parsing', 'ub' ),
							),
							'default'     => 'off',
						),
						'text'       => array(
							'type'        => 'wp_editor',
							'label'       => __( 'Message', 'ub' ),
							'description' => __( 'Choose the welcome message for the widget.', 'ub' ),
							'default'     => '',
							'placeholder' => esc_html__( 'Add your custom welcome message here…', 'ub' ),
						),
					),
				),
				'text'       => array(
					'title'       => __( 'Text Widgets', 'ub' ),
					'description' => __( 'Add text widgets to the WordPress dashboard with your custom content.', 'ub' ),
					'fields'      => array(
						'list' => array(
							'type'     => 'callback',
							'callback' => array( $this, 'get_list' ),
						),
					),
				),
			);

			if ( empty( $available_widgets ) ) {
				$options['visibility']['fields'] = array(
					'info' => array(
						'type'  => 'description',
						'value' => Branda_Helper::sui_notice( sprintf( __( 'If you do not see any widget here, please visit <a href="%s">Dashboard page</a> and come back on this page.', 'ub' ), admin_url() ), 'info' ),
					),
				);
			} else {
				$options['visibility']['fields']['reset'] = array(
					'type'  => 'button',
					'value' => __( 'Reset list', 'ub' ),
					'sui'   => array(
						'ghost',
					),
					'data'  => array(
						'modal-open' => $this->get_name( 'visibility-reset' ),
						'modal-mask' => 'true',
					),
				);
			}
			$this->options = $options;
		}

		/**
		 * save available boxes
		 *
		 * @since 2.1.0
		 */
		public function save_available_widgets() {
			global $wp_meta_boxes;
			$available_widgets = branda_get_option_filtered( $this->available_widgets );
			if ( ! is_array( $available_widgets ) ) {
				$available_widgets = array();
			}
			foreach ( $this->types as $type ) {
				if ( ! isset( $wp_meta_boxes[ $type ] ) ) {
					continue;
				}
				foreach ( $this->widget_positions as $position ) {
					if ( ! isset( $wp_meta_boxes[ $type ][ $position ] ) ) {
						continue;
					}
					foreach ( $this->priorities as $priority ) {
						if ( ! isset( $wp_meta_boxes[ $type ][ $position ][ $priority ] ) ) {
							continue;
						}
						foreach ( $wp_meta_boxes[ $type ][ $position ][ $priority ] as $key => $box ) {
							$title = strip_tags( (string) branda_get_array_value( $box, 'title' ) );
							if ( empty( $title ) ) {
								continue;
							}
							$available_widgets[ $key ] = $title;
						}
					}
				}
			}
			asort( $available_widgets );
			branda_update_option( $this->available_widgets, $available_widgets );
		}

		/**
		 * Add option names
		 *
		 * @since 2.1.0
		 */
		public function add_options_names( $options ) {
			$options[] = 'rwp_active_dashboard_widgets';
			$options[] = $this->available_widgets;
			$options[] = $this->items_name;
			return $options;
		}

		/**
		 * Remove selected widgets
		 */
		public function remove_wp_dashboard_widgets() {
			global $wp_meta_boxes;
			$active = $this->get_value( 'visibility', 'wp_widgets', array() );
			foreach ( $active as $key => $value ) {
				foreach ( $this->types as $type ) {
					foreach ( $this->widget_positions as $context ) {
						remove_meta_box( $key, $type, $context );
					}
				}
			}
		}

		/**
		 * Removes default welcome message from dashboard
		 *
		 * @param $value
		 * @param $object_id
		 * @param $meta_key
		 * @param $single
		 *
		 * @since 1.0
		 *
		 * @return bool
		 */
		public function remove_dashboard_welcome( $value, $object_id, $meta_key, $single ) {
			global $wp_version;
			if ( version_compare( $wp_version, '3.5', '>=' ) ) {
				remove_action( 'welcome_panel', 'wp_welcome_panel' );
				return $value;
			} else {
				if ( 'show_welcome_panel' === $meta_key ) {
					return false;
				}
			}
			return $value;
		}

		/**
		 * Renders custom content
		 *
		 * @since 1.2
		 */
		public function render_custom_welcome_message() {
			$value   = $this->get_value( 'welcome', 'shortocode', 'off' );
			$content = $this->get_value( 'welcome', 'text', '' );
			if ( 'on' === $value ) {
				// $value = $this->get_value( 'welcome', 'text_meta', null );
				// if ( ! empty( $value ) ) {
				// $content = $value;
				// }
				$content = do_shortcode( $content );
			}
			echo wpautop( $content );
		}

		/**
		 * List of existing elements.
		 *
		 * @since 3.0.0
		 */
		public function get_list() {
			$template = $this->get_template_name( 'list' );
			$nonce    = $this->get_nonce_value( 'new' );
			$items    = branda_get_option_filtered( $this->items_name );
			if ( is_array( $items ) ) {
				foreach ( $items as $key => $data ) {
					if ( ! is_array( $data ) ) {
						continue;
					}
					if ( ! isset( $data['id'] ) ) {
						continue;
					}
					$items[ $key ]['nonce'] = $this->get_nonce_value( $key );
				}
			}
			$args = array(
				'button'      => $this->button(
					array(
						'data'  => array(
							'nonce' => $nonce,
						),
						'icon'  => 'plus',
						'text'  => __( 'Add Text Widget', 'ub' ),
						'sui'   => 'magenta',
						'class' => 'branda-dashboard-widgets-item-edit',
					)
				),
				'order'       => $this->get_value( 'order' ),
				'template'    => $this->get_template_name( 'row' ),
				'items'       => $items,
				'button_plus' => $this->button(
					array(
						'data'  => array(
							'nonce' => $nonce,
						),
						'icon'  => 'plus',
						'text'  => __( 'Add Text Widget', 'ub' ),
						'sui'   => 'dashed',
						'class' => 'branda-dashboard-widgets-item-edit',
					)
				),
			);
			return $this->render( $template, $args, true );
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
			/**
			 * Dialog Reset List
			 */
			$template = $this->get_template_name( 'dialogs/visibility-reset' );
			$args     = array(
				'dialog_id' => $this->get_name( 'visibility-reset' ),
				'nonce'     => $this->get_nonce_value( 'visibility-reset' ),
			);
			$content .= $this->render( $template, $args, true );
			/**
			 * Dialog delete
			 */
			$content .= $this->get_dialog_delete(
				null,
				array(
					'title'       => __( 'Delete Text Widget', 'ub' ),
					'description' => __( 'Are you sure you wish to permanently delete this text widget?', 'ub' ),
				)
			);
			/**
			 * Dialog settings
			 */
			$args     = array(
				'dialog_id'     => $dialog_id,
				'nonce_edit'    => $this->get_nonce_value( 'edit' ),
				'nonce_restore' => $this->get_nonce_value( 'restore' ),
				'is_network'    => $this->is_network,
			);
			$template = $this->get_template_name( 'dialogs/edit' );
			$content .= $this->render( $template, $args, true );
			$panes    = array( 'visibility' );
			foreach ( $panes as $pane ) {
				$template = $this->get_template_name( 'tmpl/panes/' . $pane );
				$content .= $this->render( $template, $args, true );
			}
			return $content;
		}

		/**
		 * Save custom widgets order.
		 *
		 * @param bool $status Status.
		 *
		 * @since 3.4.0
		 *
		 * @return bool
		 */
		public function update_order( $status ) {
			if ( ! $status ) {
				return $status;
			}

			$items = branda_get_option_filtered( $this->items_name );
			$order = filter_input( INPUT_POST, 'branda-help-content-order', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			if ( ! empty( $items ) && is_array( $items ) && ! empty( $order ) && is_array( $order ) ) {
				$new_items = array();
				// reorder items
				foreach ( $order as $key ) {
					if ( ! empty( $items[ $key ] ) ) {
						$new_items[ $key ] = $items[ $key ];
						unset( $items[ $key ] );
					}
				}

				// Add rest items
				if ( $items ) {
					$new_items += $items;
				}

				branda_update_option( $this->items_name, $new_items );
			}

			return $status;
		}

		/**
		 * AJAX: save feed data
		 *
		 * @since 3.0.0
		 */
		public function ajax_save_item() {
			$id           = ! empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
			$nonce_action = $this->get_nonce_action( $id );
			$message      = __( 'Dashboard Widget was created.', 'ub' );
			$this->check_input_data( $nonce_action, array( 'id', 'title', 'content' ) );
			$items = branda_get_option_filtered( $this->items_name );
			if ( 'new' === $id ) {
				$id = $this->generate_id( $_POST );
			}
			if ( isset( $items[ $id ] ) ) {
				$message = __( 'Dashboard Widget was updated.', 'ub' );
			}
			$content      = wp_kses_post( filter_input( INPUT_POST, 'content' ) );
			$item         = wp_parse_args(
				array(
					'id'           => $id,
					'title'        => ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '',
					'content'      => $content,
					'content_meta' => apply_filters( 'the_content', $content ),
					'site'         => ! empty( $_POST['site'] ) ? sanitize_text_field( $_POST['site'] ) : '',
					'network'      => ! empty( $_POST['network'] ) ? sanitize_text_field( $_POST['network'] ) : '',
				),
				$this->item_defaults
			);
			$items[ $id ] = $item;
			branda_update_option( $this->items_name, $items );
			$item['nonce']   = $this->get_nonce_value( $id );
			$item['message'] = $message;
			/**
			 * add/update $available_widgets
			 */
			$available_widgets = branda_get_option_filtered( $this->available_widgets );
			if ( ! is_array( $available_widgets ) ) {
				$available_widgets = array();
			}
			$branda_id                       = $this->get_name( $id );
			$available_widgets[ $branda_id ] = $items[ $id ]['title'];
			asort( $available_widgets );
			branda_update_option( $this->available_widgets, $available_widgets );
			/**
			 * Send it back
			 */
			wp_send_json_success( $item );
		}

		/**
		 * Helper to get single row of items list
		 *
		 * @since 3.0.0
		 */
		private function get_list_one_row( $id, $tab ) {
			$content  = '<div class="sui-builder-field">';
			$content .= '<div class="sui-builder-field-label">';
			$content .= $tab['title'];
			$content .= '</div>';
			/**
			 * Button: delete
			 */
			$args     = array(
				'only-icon' => true,
				'icon'      => 'trash',
				'data'      => array(
					'modal-open' => $this->get_name( 'edit' ),
					'nonce'      => $nonce,
				),
				'classes'   => array(
					'sui-hover-show',
				),
				'sui'       => array(
					'red',
				),
			);
			$content .= $this->button( $args );
			/**
			 * Button: edit
			 */
			$args     = array(
				'only-icon' => true,
				'icon'      => 'widget-settings-config',
				'data'      => array(
					'modal-open' => $this->get_nonce_action( $id, 'delete' ),
				),
			);
			$content .= $this->button( $args );
			$content .= '</div>'; // Builder Field
			return $content;
		}

		private function has_items() {
			$items = branda_get_option_filtered( $this->items_name );
			if ( empty( $items ) || ! is_array( $items ) ) {
				return false;
			}
			return true;
		}

		public function add_dashboard_widgets() {
			global $wp_version;
			$version_compare = version_compare( $wp_version, '3.7.1' );
			$widget_items    = array();
			$items           = branda_get_option_filtered( $this->items_name );
			if ( empty( $items ) || ! is_array( $items ) ) {
				return;
			}
			foreach ( $items as $widget_id => $widget_options ) {
				// IF we still have them, ignore.
				if ( 0 >= $version_compare ) {
					if (
						'df-dashboard_primary' === $widget_id
						|| 'df-dashboard_secondary' === $widget_id
					) {
						continue;
					}
				}
				$widget_options['branda_id'] = $this->get_name( $widget_id );
				if ( is_multisite() && is_network_admin() ) {
					if ( isset( $widget_options['network'] ) && 'on' === $widget_options['network'] ) {
						$widget_items[ $widget_id ] = new Branda_Dashboard_Widgets_Widget();
						$widget_items[ $widget_id ]->init( $widget_id, $widget_options );
					}
				} else {
					if ( isset( $widget_options['site'] ) && 'on' === $widget_options['site'] ) {
						$widget_items[ $widget_id ] = new Branda_Dashboard_Widgets_Widget();
						$widget_items[ $widget_id ]->init( $widget_id, $widget_options );
					}
				}
			}
		}

		/**
		 * AJAX: delete feed data
		 *
		 * @since 3.0.0
		 */
		public function ajax_delete_item() {
			$id           = ! empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
			$nonce_action = $this->get_nonce_action( $id );
			$this->check_input_data( $nonce_action, array( 'id' ) );
			$items = branda_get_option_filtered( $this->items_name );
			if ( isset( $items[ $id ] ) ) {
				/**
				 * remove widget from DB
				 */
				unset( $items[ $id ] );
				branda_update_option( $this->items_name, $items );
				/**
				 * remove widget from available widgets list
				 */
				$available_widgets = branda_get_option_filtered( $this->available_widgets );
				$widget_id         = $this->get_name( $id );
				if (
					is_array( $available_widgets )
					&& isset( $available_widgets[ $widget_id ] )
				) {
					unset( $available_widgets[ $widget_id ] );
					branda_update_option( $this->available_widgets, $available_widgets );
				}
				/**
				 * Send Message
				 */
				wp_send_json_success(
					array(
						'id'      => $id,
						'message' => __( 'Dashboard Widget was deleted.', 'ub' ),
					)
				);
			}
			wp_send_json_error( array( 'message' => __( 'Selected widget does not exists!', 'ub' ) ) );
		}

		/**
		 * Get SUI configuration for modal window.
		 *
		 * @since 3.0.0
		 *
		 * @return array $config Configuration of modal window.
		 */
		public function get_sui_tabs_config( $item = array() ) {
			$config = array(
				array(
					'tab'      => __( 'General', 'ub' ),
					'tab_name' => 'general',
					'fields'   => array(
						'title'   => array(
							'label' => __( 'Widget Title', 'ub' ),
							'value' => isset( $item['title'] ) ? $item['title'] : '',
						),
						'content' => array(
							'label'       => __( 'Widget Content', 'ub' ),
							'type'        => 'wp_editor',
							'value'       => isset( $item['content'] ) ? $item['content'] : '',
							'placeholder' => esc_html__( 'Add your help sidebar content here…', 'ub' ),
						),
					),
				),
				array(
					'tab'      => __( 'Visibility', 'ub' ),
					'tab_name' => 'visibility',
					'fields'   => array(
						'site'    => array(
							'type'        => 'sui-tab',
							'label'       => __( 'Site Dashboard', 'ub' ),
							'description' => __( 'Choose whether to show this text widget on site dashboard or not.', 'ub' ),
							'options'     => array(
								'on'  => __( 'Show', 'ub' ),
								'off' => __( 'Hide', 'ub' ),
							),
							'default'     => 'on',
							'value'       => isset( $item['site'] ) ? $item['site'] : 'on',
						),
						'network' => array(
							'type'        => 'sui-tab',
							'label'       => __( 'Network Dashboard', 'ub' ),
							'description' => __( 'Choose whether to show this text widget on the network dashboard or not.', 'ub' ),
							'options'     => array(
								'on'  => __( 'Show', 'ub' ),
								'off' => __( 'Hide', 'ub' ),
							),
							'default'     => 'on',
							'value'       => isset( $item['network'] ) ? $item['network'] : 'on',
							'divider'     => array(
								'position' => 'before',
							),
						),
					),
				),
			);
			/**
			 * remove multisite show
			 */
			if ( ! $this->is_network ) {
				unset( $config[1]['fields']['network'] );
			}
			return $config;
		}

		public function admin_print_styles() {
			$screen = get_current_screen();
			if ( ! is_a( $screen, 'WP_Screen' ) ) {
				return;
			}
			if ( ! preg_match( '/^dashboard(\-network)?$/', $screen->base ) ) {
				return;
			}
			$template = sprintf( '/admin/modules/%s/css', $this->module );
			$args     = array(
				'id' => $this->get_name(),
			);
			$this->render( $template, $args );
		}

		/**
		 * AJAX get single item
		 *
		 * @since 3.1.0
		 */
		public function ajax_get_item() {
			$id           = ! empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
			$nonce_action = $this->get_nonce_action( $id );
			$this->check_input_data( $nonce_action, array( 'id' ) );
			$items = branda_get_option_filtered( $this->items_name );
			if ( isset( $items[ $id ] ) ) {
				/**
				 * add defaults
				 */
				$item               = wp_parse_args( $items[ $id ], $this->item_defaults );
				$item['is_network'] = $this->is_network;
				$item['message']    = __( 'Selected item was successfully restored!', 'ub' );
				wp_send_json_success( $item );
			}
			wp_send_json_error( array( 'message' => __( 'Selected item does not exists!', 'ub' ) ) );
		}

		/**
		 * Delete available widget by ID
		 *
		 * @since 3.1.0
		 */
		public function delete_available_widget( $id ) {
			$value = branda_get_option_filtered( $this->available_widgets );
			if ( isset( $value[ $id ] ) ) {
				unset( $value[ $id ] );
				branda_update_option( $this->available_widgets, $value );
			}
		}

		/**
		 * AJAX reset visibility
		 *
		 * @since 3.1.0
		 */
		public function ajax_visibility_reset() {
			$nonce_action = $this->get_nonce_action( 'visibility-reset' );
			$this->check_input_data( $nonce_action );
			$result = branda_delete_option( $this->available_widgets );
			if ( $result ) {
				wp_send_json_success();
			}
			wp_send_json_error( array( 'message' => __( 'Whoops! Something went wrong.', 'ub' ) ) );
		}
	}
}
new Branda_Dashboard_Widgets();
