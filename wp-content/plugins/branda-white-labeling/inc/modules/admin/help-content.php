<?php
/**
 * Branda class.
 *
 * Class that handles admin tips module.
 *
 * @package Branda
 * @subpackage AdminArea
 */
if ( ! class_exists( 'Branda_Admin_Help_Content' ) ) {

	/**
	 * Class Branda_Admin_Help_Content.
	 */
	class Branda_Admin_Help_Content extends Branda_Helper {

		/**
		 * Contextual help class.
		 *
		 * @var Branda_Contextual_Help
		 */
		private $_help;

		/**
		 * Module option name.
		 *
		 * @var string
		 */
		protected $option_name = 'ub_admin_help';

		/**
		 * Branda_Admin_Help_Content constructor.
		 */
		public function __construct() {
			parent::__construct();
			$this->module = 'admin-help-content';
			// Create class object of Branda_Contextual_Help.
			if ( ! class_exists( 'Branda_Contextual_Help' ) ) {
				require_once dirname( __FILE__ ) . '/class-wpmudev-contextual-help.php';
			}
			// Common module hooks.
			add_filter( 'ultimatebranding_settings_admin_help_content', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_admin_help_content_process', array( $this, 'update' ) );
			add_filter( 'ultimatebranding_settings_admin_help_content_process', array( $this, 'update_order' ), 11, 1 );
			add_filter( 'ultimatebranding_settings_admin_help_content_preserve', array( $this, 'add_preserve_fields' ) );
			/**
			 * Add dialog.
			 *
			 * @since 3.0,0
			 */
			add_filter( 'branda_get_module_content', array( $this, 'add_dialog' ), 10, 2 );
			/**
			 * Save item.
			 *
			 * @since 3.0.0
			 */
			add_action( 'wp_ajax_branda_admin_help_save', array( $this, 'ajax_save_item' ) );
			/**
			 * Delete item.
			 *
			 * @since 3.0.0
			 */
			add_action( 'wp_ajax_branda_admin_help_delete', array( $this, 'ajax_delete_item' ) );
			/**
			 * Get Item
			 *
			 * @since 3.1.0
			 */
			add_action( 'wp_ajax_branda_admin_help_content_get', array( $this, 'ajax_get_item' ) );
			/**
			 * Upgrade options.
			 *
			 * @since 3.0.0
			 */
			add_action( 'init', array( $this, 'upgrade_options' ) );
			/**
			 * Run helps
			 */
			$this->_help = new Branda_Contextual_Help();
			$this->initialize_help_content();
		}

		/**
		 * Save help tabs order.
		 *
		 * @param bool $status Status.
		 *
		 * @since 3.0.0
		 *
		 * @return bool
		 */
		public function update_order( $status ) {
			if ( $status ) {
				$data          = $this->get_value();
				$data['order'] = isset( $_POST['branda-help-content-order'] ) ? $_POST['branda-help-content-order'] : array();
				$this->update_value( $data );
			}
			return true && $status;
		}

		/**
		 * Upgrade options to new.
		 *
		 * @since 3.0.0
		 */
		public function upgrade_options() {
			// Migrate previous data.
			$old = branda_get_option( 'admin_help_content' );
			if ( ! empty( $old ) ) {
				$data   = array(
					'settings' => array(
						'merge_panels'    => 'off',
						'prevent_network' => 'off',
					),
					'sidebar'  => array(
						'content'      => '',
						'content_meta' => '',
					),
				);
				$update = false;
				if ( isset( $old['prevent_network'] ) ) {
					$data['settings']['prevent_network'] = $old['prevent_network'] ? 'on' : 'off';
					unset( $old['prevent_network'] );
					$update = true;
				}
				if ( isset( $old['merge_panels'] ) ) {
					$data['settings']['merge_panels'] = $old['merge_panels'] ? 'on' : 'off';
					unset( $old['merge_panels'] );
					$update = true;
				}
				if ( isset( $old['sidebar'] ) ) {
					$data['sidebar']['content'] = $old['sidebar'];
					unset( $old['sidebar'] );
					$update = true;
				}
				if ( $update ) {
					$this->update_value( $data );
				}
				if ( isset( $old['tabs'] ) ) {
					branda_update_option( 'ub_admin_help_items', $old['tabs'] );
				}
				branda_delete_option( 'admin_help_content' );
			}
			/**
			 * Migrate items
			 */
			$data = branda_get_option_filtered( 'ub_admin_help_items' );
			if ( ! empty( $data ) && is_array( $data ) ) {
				$value = $this->get_value();
				if (
					! isset( $value['items'] )
					|| ! is_array( $value['items'] )
				) {
					$value['items'] = array();
				}
				foreach ( $data as $one ) {
					$id                    = $this->generate_id( $one );
					$one['id']             = $id;
					$value['items'][ $id ] = $one;
				}
				$this->update_value( $value );
				branda_delete_option( 'ub_admin_help_items' );
			}
		}

		/**
		 * Set options
		 *
		 * @since 3.0.0
		 */
		protected function set_options() {
			$options = array(
				'items'    => array(
					'title'       => __( 'Help Items', 'ub' ),
					'description' => __( 'Start editing the default help item or add new help items as per your need.', 'ub' ),
					'fields'      => array(
						'list' => array(
							'type'        => 'callback',
							'callback'    => array( $this, 'get_list' ),
							'description' => array(
								'content'  => __( 'Reorder the help items by dragging and dropping.', 'ub' ),
								'position' => 'bottom',
							),
						),
					),
				),
				'sidebar'  => array(
					'title'       => __( 'Help Sidebar', 'ub' ),
					'description' => __( 'Add a sidebar within the help content area. You can leave this empty if you don’t want a help sidebar.', 'ub' ),
					'fields'      => array(
						'content' => array(
							'type'        => 'wp_editor',
							'placeholder' => esc_html__( 'Add your help sidebar content here…', 'ub' ),
						),
					),
				),
				'settings' => array(
					'title'       => __( 'Settings', 'ub' ),
					'description' => __( 'Choose the appropriate settings to have extra control over the help content.', 'ub' ),
					'fields'      => array(
						'prevent_network' => array(
							'checkbox_label' => __( 'Hide new help panels in Network Admin area', 'ub' ),
							'description'    => array(
								'content'  => __( 'Hide the new help panels added in the Network Admin area and show a generic guide on Branda Dashboard instead.', 'ub' ),
								'position' => 'bottom',
							),
							'type'           => 'checkbox',
							'classes'        => array( 'switch-button' ),
						),
						'merge_panels'    => array(
							'checkbox_label' => __( 'Keep the default help items', 'ub' ),
							'description'    => array(
								'content'  => __( 'Merge the new help items with the default items instead of deleting them.', 'ub' ),
								'position' => 'bottom',
							),
							'type'           => 'checkbox',
							'classes'        => array( 'switch-button' ),
							'default'        => 'on',
						),
					),
				),
			);
			if ( ! $this->is_network || ! is_network_admin() ) {
				unset( $options['settings']['fields']['prevent_network'] );
			}
			$this->options = $options;
		}

		/**
		 * Main handling method.
		 *
		 * Pick up stored settings, convert them to proper format
		 * and feed them to abstract help handler.
		 */
		private function initialize_help_content() {
			$opts = $this->get_value();
			if (
				defined( 'WP_NETWORK_ADMIN' )
				&& WP_NETWORK_ADMIN
				&& isset( $opts['settings'] )
				&& isset( $opts['settings']['prevent_network'] )
				&& 'on' === $opts['settings']['prevent_network']
			) {
				return false;
			}
			$tabs = $this->get_value( 'items' );
			if ( empty( $tabs ) ) {
				return;
			}
			foreach ( $tabs as $idx => $tab ) {
				$tabs[ $idx ]['content'] = isset( $tab['content_meta'] ) ? $tab['content_meta'] : wpautop( $tab['content'] );
			}
			$merge_panels = false;
			if (
				isset( $opts['settings'] )
				&& isset( $opts['settings']['merge_panels'] )
				&& 'on' === $opts['settings']['merge_panels']
			) {
				$merge_panels = true;
			}
			$sidebar = $this->get_value( 'sidebar', 'content_meta' );
			if ( empty( $sidebar ) ) {
				$sidebar = $this->get_value( 'sidebar', 'content', '' );
			}
			// Re-order.
			if ( isset( $opts['order'] ) ) {
				$order = array();
				foreach ( $opts['order'] as $id ) {
					if ( ! isset( $tabs[ $id ] ) ) {
						continue;
					}
					$order[] = $tabs[ $id ];
					unset( $tabs[ $id ] );
				}
				$order += $tabs;
				$tabs   = $order;
			}
			$this->_help->add_page( '_global_', $tabs, $sidebar, ! $merge_panels );
			$this->_help->initialize();
		}

		/**
		 * List of existing elements.
		 *
		 * @since 3.0.0
		 */
		public function get_list() {
			$template = $this->get_template_name( 'list' );
			$tabs     = $this->get_value( 'items' );
			$items    = array();
			if ( is_array( $tabs ) ) {
				$order = $this->get_value( 'order' );
				if ( is_array( $order ) ) {
					foreach ( $order as $id ) {
						if ( ! isset( $tabs[ $id ] ) ) {
							continue;
						}
						$tab          = $tabs[ $id ];
						$args         = array(
							'id'    => $id,
							'title' => $tab['title'],
						);
						$items[ $id ] = $tab;
					}
				}
				// Get lines which has not order.
				foreach ( $tabs as $id => $tab ) {
					$args             = array(
						'id'    => $id,
						'title' => $tab['title'],
					);
						$items[ $id ] = $tab;
				}
				foreach ( $items as $key => $data ) {
					$items[ $key ]['nonce'] = $this->get_nonce_value( $key );
				}
			}
			$nonce = $this->get_nonce_value( 'new' );
			$args  = array(
				'button'      => $this->button(
					array(
						'data'  => array(
							'nonce' => $nonce,
						),
						'icon'  => 'plus',
						'text'  => __( 'Add Help Item', 'ub' ),
						'sui'   => 'magenta',
						'class' => 'branda-admin-help-content-item-edit',
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
						'text'  => __( 'Add Help Item', 'ub' ),
						'sui'   => 'dashed',
						'class' => 'branda-admin-help-content-item-edit',
					)
				),
			);
			return $this->render( $template, $args, true );
		}

		/**
		 * Add SUI dialog.
		 *
		 * @param string $content Current module content.
		 * @param array  $module Current module.
		 *
		 * @since 3.0.0
		 *
		 * @return string $content
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
			 * Dialog delete
			 */
			$content .= $this->get_dialog_delete(
				null,
				array(
					'title'       => __( 'Delete Custom Help Item', 'ub' ),
					'description' => __( 'Are you sure you wish to permanently delete this custom help item?', 'ub' ),
				)
			);
			/**
			 * Dialog settings
			 */
			$args     = array(
				'dialog_id'     => $dialog_id,
				'nonce_edit'    => $this->get_nonce_value( 'edit' ),
				'nonce_restore' => $this->get_nonce_value( 'restore' ),
			);
			$template = $this->get_template_name( 'dialogs/edit' );
			$content .= $this->render( $template, $args, true );
			return $content;
		}

		/**
		 * SUI: get dialog content.
		 *
		 * @param int    $id ID.
		 * @param array  $data Data array.
		 * @param string $type Type.
		 *
		 * @since 3.0.0
		 *
		 * @return string
		 */
		private function get_dialog( $id = 0, $data = array(), $type = 'add' ) {
			$options        = array(
				'item' => array(
					'show-as' => 'flat',
					'fields'  => array(
						'title'   => array(
							'label' => __( 'Title', 'ub' ),
						),
						'content' => array(
							'label'       => __( 'Content', 'ub' ),
							'type'        => 'wp_editor',
							'id'          => 'branda_admin_help_content_save_' . $id,
							'placeholder' => esc_html__( 'Add your help sidebar content here…', 'ub' ),
						),
					),
				),
			);
			$simple_options = new Simple_Options();
			$dialog         = $simple_options->build_options( $options, $data, $this->module );
			/**
			 * Footer.
			 */
			$footer  = '';
			$args    = array(
				'icon'    => 'undo',
				'text'    => __( 'Reset', 'ub' ),
				'sui'     => 'ghost',
				'classes' => array(
					'branda-dialog-reset',
					$this->get_name( 'reset' ),
				),
				'data'    => array(
					'id' => $id,
				),
			);
			$footer .= $this->button( $args );
			$args    = array(
				'data'  => array(
					'nonce' => $this->get_nonce_value( $id ),
					'id'    => $id,
				),
				'icon'  => 'check',
				'text'  => __( 'Apply', 'ub' ),
				'sui'   => '',
				'class' => 'branda-admin-help-content-add',
			);
			$footer .= $this->button( $args );
			/**
			 * Dialog
			 */
			$id   = 'add' === $type ? $this->get_name( 'add' ) : $this->get_nonce_action( 'edit', $id );
			$args = array(
				'id'      => $id,
				'content' => $dialog,
				'title'   => 'add' === $type ? __( 'Add Help Item', 'ub' ) : __( 'Edit Help Item', 'ub' ),
				'footer'  => array(
					'content' => $footer,
					'classes' => array(
						'sui-space-between',
					),
				),
				'classes' => array(
					$this->get_name( 'dialog' ),
				),
			);
			return $this->sui_dialog( $args );
		}

		/**
		 * Save data using ajax.
		 *
		 * @since 3.0.0
		 */
		public function ajax_save_item() {
			$id           = ! empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
			$nonce_action = $this->get_nonce_action( $id );
			$this->check_input_data( $nonce_action, array( 'id', 'title', 'content' ) );
			$message = __( 'Item was updated.', 'ub' );
			$tabs    = $this->get_value( 'items' );
			if ( empty( $tabs ) || ! is_array( $tabs ) ) {
				$tabs = array();
			}
			$content              = isset( $_POST['content_meta'] ) ? $_POST['content_meta'] : stripslashes( $_POST['content'] );
			$item                 = array(
				'title'   => sanitize_text_field( $_POST['title'] ),
				'content' => wp_kses_post( $content ),
				'updated' => time(),
			);
			$item['content_meta'] = do_shortcode( $item['content'] );
			if ( 'new' === $id ) {
				$message         = sprintf( 'Item was added.', 'ub' );
				$item['created'] = time();
				$id              = $this->generate_id( $item );
				$item['nonce']   = $this->get_nonce_value( $id );
			}
			$item['id']  = $id;
			$tabs[ $id ] = $item;
			// Update option.
			$value = $this->get_value();

			if ( ! is_array( $value ) ) {
				$value = array();
			}

			$value['items'] = $tabs;
			$this->update_value( $value );
			$item['message'] = $message;
			wp_send_json_success( $item );
		}

		/**
		 * Delete data using ajax.
		 *
		 * @since 3.0.0
		 */
		public function ajax_delete_item() {
			$id           = ! empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
			$nonce_action = $this->get_nonce_action( $id );
			$this->check_input_data( $nonce_action, array( 'id' ) );
			$value = $this->get_value();
			if (
				isset( $value['items'] )
				&& is_array( $value['items'] )
				&& isset( $value['items'][ $id ] )
			) {
				$item = $value['items'][ $id ];
				unset( $value['items'][ $id ] );
				$this->update_value( $value );
				wp_send_json_success(
					array(
						'id'      => $id,
						'message' => __( 'Item was deleted.', 'ub' ),
					)
				);
			}
			$this->json_error();
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
			return array( 'items' => null );
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
			$items = $this->get_value( 'items' );
			if ( isset( $items[ $id ] ) ) {
				$item = $items[ $id ];
				wp_send_json_success( $item );
			}
			wp_send_json_error( array( 'message' => __( 'Selected item does not exists!', 'ub' ) ) );
		}
	}
}
new Branda_Admin_Help_Content();
