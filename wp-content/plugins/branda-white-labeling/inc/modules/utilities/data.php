<?php
/**
 * Branda Data class.
 *
 * Class that handle Settings functionality.
 *
 * @since 3.0.0
 *
 * @package Branda
 * @subpackage Settings
 */
if ( ! class_exists( 'Branda_Data' ) ) {

	/**
	 * Class Branda_Data.
	 */
	class Branda_Data extends Branda_Helper {

		/**
		 * Option name
		 *
		 * @since 3.0.0
		 */
		protected $option_name = 'ub_data';

		/**
		 * Branda_Data constructor.
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			parent::__construct();
			$this->module = 'data';
			/**
			 * Branda Admin Class actions
			 *
			 * @since 3.0,0
			 */
			add_filter( 'ultimatebranding_settings_data', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_data_process', array( $this, 'update' ) );
			/**
			 * Add custom content title
			 *
			 * @since 3.0,0
			 */
			add_filter( 'branda_before_module_form', array( $this, 'add_title_before_form' ), 10, 2 );
			/**
			 * Change bottom save button params
			 *
			 * @since 3.0,0
			 */
			add_filter( 'branda_after_form_save_button_args', array( $this, 'change_bottom_save_button' ), 10, 2 );
			/**
			 * Add dialog
			 *
			 * @since 3.0,0
			 */
			add_filter( 'branda_get_module_content', array( $this, 'add_dialog' ), 10, 2 );
			/**
			 * Handla AJAX actions
			 *
			 * @since 3.0.0
			 */
			add_action( 'wp_ajax_branda_data_reset', array( $this, 'ajax_reset' ) );
			add_action( 'wp_ajax_branda_data_delete_subsites', array( $this, 'ajax_delete_subsites_data' ) );
			/**
			 * handle uninstall
			 */
			add_action( 'branda_uninstall_plugin', array( $this, 'uninstall_plugin' ) );
		}

		/**
		 * Build form with options.
		 *
		 * @since 3.0.0
		 */
		protected function set_options() {
			$options = array(
				'uninstallation' => array(
					'title'       => __( 'Uninstallation', 'ub' ),
					'description' => __( 'When you uninstall this plugin, what do you want to do with your settings and stored data?', 'ub' ),
					'fields'      => array(
						'settings' => array(
							'label'       => __( 'Settings', 'ub' ),
							'description' => __( 'Choose whether to save your settings for next time, or reset them.', 'ub' ),
							'type'        => 'sui-tab',
							'options'     => array(
								'preserve' => __( 'Preserve', 'ub' ),
								'reset'    => __( 'Reset', 'ub' ),
							),
							'default'     => 'preserve',
						),
						'data'     => array(
							'label'       => __( 'Data', 'ub' ),
							'description' => __( 'Choose whether to keep or remove log data.', 'ub' ),
							'type'        => 'sui-tab',
							'options'     => array(
								'keep'   => __( 'Keep', 'ub' ),
								'remove' => __( 'Remove', 'ub' ),
							),
							'default'     => 'keep',
							'after'       => Branda_Helper::sui_notice(
								__( 'This option only affects the main site’s data. If you want to delete data of the subsites before uninstalling the plugin, please use the setting below.', 'ub' ),
								'default'
							),
						),
					),
				),
				'subsites'       => array(
					'title'       => __( 'Subsites', 'ub' ),
					'description' => __( 'Manage the data stored in each subsite here.', 'ub' ),
					'fields'      => array(
						'button' => array(
							'label'       => __( 'Delete Data', 'ub' ),
							'type'        => 'button',
							'value'       => __( 'Delete Subsites Data', 'ub' ),
							'sui'         => array( 'red', 'ghost' ),
							'description' => __( 'Want to delete the data of all the subsites? Use this option to manually delete data from all the subsites.', 'ub' ),
							'data'        => array(
								'modal-open' => $this->get_name( 'confirm-delete-subsites' ),
								'modal-mask' => 'true',
							),
							'before'      => sprintf( '<div id="%s">', $this->get_name( 'delete-subsites-container' ) ),
							'after'       => '</div>',
						),
					),
				),
				'reset'          => array(
					'title'       => __( 'Reset Settings', 'ub' ),
					'description' => __( 'Needing to start fresh? Use this button to roll back to the default settings.', 'ub' ),
					'fields'      => array(
						'button' => array(
							'type'        => 'button',
							'value'       => __( 'Reset', 'ub' ),
							'icon'        => 'undo',
							'sui'         => 'ghost',
							'description' => array(
								'content'  => __( 'Note: This will instantly revert all settings to their default states but will leave your data intact.', 'ub' ),
								'position' => 'bottom',
							),
							'data'        => array(
								'modal-open' => $this->get_name( 'confirm-reset' ),
								'modal-mask' => 'true',
							),
						),
					),
				),
			);
			/**
			 * remove some options from single site install
			 */
			if ( $this->is_network && ! is_network_admin() ) {
				unset( $options['uninstallation'] );
			}
			if ( ! $this->is_network || ! is_network_admin() ) {
				unset( $options['subsites'] );
				unset( $options['uninstallation']['fields']['data']['after'] );
			}
			$this->options = $options;
		}

		/**
		 * Add title before form.
		 *
		 * @since 3.0.0
		 *
		 * @param string $content Current content.
		 * @param array  $module Current module.
		 */
		public function add_title_before_form( $content, $module ) {
			if ( $this->module !== $module['module'] ) {
				return $content;
			}
			$template    = $this->get_template_name( 'header' );
			$description = ! $this->is_network || $this->is_network_admin
				? esc_html__( 'Control what to do with your settings and data. Settings are considered the module configurations, Data includes the transient bits such as logs, frequently used modules, last import/export time and other pieces of information stored over time.', 'ub' )
				: esc_html__( 'Control what to do with your settings.', 'ub' );

			$content .= $this->render( $template, array( 'description' => $description ), true );
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
			 * Dialog Reset
			 */
			$args = array(
				'dialog_id' => $this->get_name( 'confirm-reset' ),
				'nonce'     => $this->get_nonce_value( 'reset' ),
				'blog_id'   => 0,
			);
			if ( $this->is_network && ! is_network_admin() ) {
				$blog_id         = get_current_blog_id();
				$args['nonce']   = $this->get_nonce_value( 'reset', $blog_id );
				$args['blog_id'] = $blog_id;
			}
			$template = $this->get_template_name( 'dialogs/confirm-reset' );
			$content .= $this->render( $template, $args, true );
			/**
			 * Dialog settings
			 */
			$args = array(
				'dialog_id'    => $this->get_name( 'confirm-delete-subsites' ),
				'button_nonce' => $this->get_nonce_value( 'delete-subsites' ),
				'button_class' => $this->get_name( 'delete-subsites' ),
			);
			/**
			 * values
			 */
			$template = $this->get_template_name( 'dialogs/confirm-delete-subsites' );
			$content .= $this->render( $template, $args, true );
			return $content;
		}

		/**
		 * AJAX: reset item
		 *
		 * @since 3.0.0
		 */
		public function ajax_reset() {
			$blog_id      = intval( filter_input( INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT ) );
			$nonce_action = $this->get_nonce_action( 'reset' );
			if ( 0 < $blog_id ) {
				$nonce_action = $this->get_nonce_action( 'reset', $blog_id );
			}
			$this->check_input_data( $nonce_action );
			if ( 0 < $blog_id ) {
				$result = $this->reset_subsite( $blog_id );
				if ( is_wp_error( $result ) ) {
					$message = array(
						'message' => $result->get_error_message(),
					);
					wp_send_json_error( $message );
				}
			} else {
				/**
				 * reset
				 */
				$result = $this->delete_all_plugin_data( true );
				if ( is_wp_error( $result ) ) {
					$message = array(
						'message' => $result->get_error_message(),
					);
					wp_send_json_error( $message );
				}
			}
			$url = add_query_arg( 'page', 'branding', network_admin_url( 'admin.php' ) );
			if ( 0 < $blog_id ) {
				$url = add_query_arg( 'page', 'branding', admin_url( 'admin.php' ) );
			}
			wp_send_json_success(
				array(
					'url' => $url,
				)
			);
		}

		/**
		 * Delete all plugin options!
		 */
		private function delete_all_plugin_data( $delete_settings_too = false, $blog_id = false, $delete_network_settings = true ) {
			$variables = $this->get_variables( $delete_settings_too );
			if ( is_wp_error( $variables ) ) {
				return $variables;
			}
			foreach ( $variables as $key ) {
				if ( 'ub_stats' === $key ) {
					continue;
				}
				if ( false === $blog_id ) {
					delete_option( $key );
				} else {
					delete_blog_option( $blog_id, $key );
				}

				if ( $delete_network_settings ) {
					delete_site_option( $key );
				}
			}
			/**
			 * delete_all_plugin_data
			 */
			if ( $delete_settings_too ) {
				$this->delete_settings( $blog_id );
			}
			return true;
		}

		private function delete_settings( $blog_id ) {
			if ( false !== $blog_id ) {
				switch_to_blog( $blog_id );
			}

			// Remove admin_panel_tip CPT.
			$posts = get_posts(
				array(
					'post_type'   => 'admin_panel_tip',
					'numberposts' => -1,
				)
			);
			foreach ( $posts as $post ) {
				wp_delete_post( $post->ID, true );
			}
			// Remove all relevant usermeta.
			delete_metadata( 'user', 0, 'show_welcome_dialog', '', true );
			delete_metadata( 'user', 0, 'tips_dismissed', '', true );
			delete_metadata( 'user', 0, 'show_tips', '', true );
			delete_metadata( 'user', 0, 'Branda_Cookie_Notice', '', true );

			if ( false !== $blog_id ) {
				restore_current_blog();
			}

			// Delete SMTP secret key.
			branda_delete_option( 'wpmudev_branda_smtp_encryption_key' );
		}

		/**
		 * handle uninstall plugin
		 *
		 * @since 3.0.0
		 */
		public function uninstall_plugin() {
			/**
			 * Get data in old way, plugin is not installed, we can not check
			 * how it is installed
			 */
			$this->data = get_site_option( $this->option_name );
			if ( empty( $this->data ) ) {
				$this->data = get_option( $this->option_name );
			}
			$this->set_data();
			$value = $this->get_value( 'uninstallation', 'settings', 'preserve' );
			if ( 'reset' === $value ) {
				$value               = $this->get_value( 'uninstallation', 'data', 'keep' );
				$delete_settings_too = 'remove' === $value;
				$this->delete_all_plugin_data( $delete_settings_too );
			}
			$value = $this->get_value( 'uninstallation', 'data', 'keep' );
			if ( 'remove' === $value ) {
				delete_site_option( 'ub_stats' );
			}
		}

		/**
		 * AJAX to delete data from all subsites, site by site.
		 *
		 * @since 3.0.0
		 */
		public function ajax_delete_subsites_data() {
			if ( ! function_exists( 'get_sites' ) ) {
				$this->json_error();
			}
			$html   = '';
			$args   = array();
			$offset = intval( filter_input( INPUT_POST, 'offset', FILTER_SANITIZE_NUMBER_INT ) );
			if ( 0 === $offset ) {
				$nonce_action = $this->get_nonce_action( 'delete-subsites' );
				$this->check_input_data( $nonce_action );
				$template = $this->get_template_name( 'progress-bar' );
				$html     = $this->render( $template, array(), true );
			} else {
				$nonce_action = $this->get_nonce_action( 'delete-subsites', $offset );
				$this->check_input_data( $nonce_action );
			}
			/**
			 * Count
			 */
			$get_sites_args = array(
				'count' => true,
			);
			$count          = get_sites( $get_sites_args );
			/**
			 * get ids
			 */
			$get_sites_args = array(
				'number' => 1,
				'offset' => $offset,
				'fields' => 'ids',
			);
			$site           = get_sites( $get_sites_args );
			if ( empty( $site ) ) {
				$html  = sprintf(
					'<span class="sui-description">%s</span>',
					esc_html__( 'Want to delete the data of all the subsites? Use this option to manually delete data from all the subsites.', 'ub' )
				);
				$html .= Branda_Helper::sui_notice( __( 'Successfully deleted data from all the subsites.', 'ub' ), 'success' );
				$args  = array(
					'offset' => 'end',
					'html'   => $html,
				);
				wp_send_json_success( $args );
			}
			$site_id = array_shift( $site );
			$offset++;
			$args = array(
				'offset'   => $offset,
				'nonce'    => $this->get_nonce_value( 'delete-subsites', $offset ),
				'html'     => $html,
				'progress' => intval( ( 100 * $offset ) / $count ),
				'state'    => '',
			);
			/**
			 * Skip main site
			 */
			if ( is_main_site( $site_id ) ) {
				wp_send_json_success( $args );
			}
			/**
			 * Do Magic with subsite!
			 */
			$this->delete_all_plugin_data( true, $site_id, false );
			/**
			 * send return data
			 */
			$site          = get_blog_details( $site_id );
			$args['state'] = sprintf(
				__( 'Deleting data from %s', 'ub' ),
				$this->bold( $site->blogname )
			);
			wp_send_json_success( $args );
		}

		/**
		 * reset subsite
		 *
		 * @since 3.2.0
		 */
		private function reset_subsite( $blog_id ) {
			switch_to_blog( $blog_id );
			$variables = $this->get_variables( true );
			if ( is_wp_error( $variables ) ) {
				return $variables;
			}
			foreach ( $variables as $key ) {
				delete_option( $key );
			}
			$this->delete_settings( $blog_id );
			return true;
		}

		/**
		 * Get variables
		 *
		 * @since 3.2.0
		 */
		private function get_variables( $delete_settings_too ) {
			$variables = array();
			if ( $delete_settings_too ) {
				$variables = array(
					'branda_db_version',
					'ultimatebranding_activated_modules',
					'ultimate_branding_delete_settings',
					'ultimate_branding_messages',
				);
			}
			$contfiguration = $this->uba->get_configuration();
			if ( empty( $contfiguration ) || ! is_array( $contfiguration ) ) {
				return new WP_Error( 'error', __( 'Ups... Missing configuration...', 'ub' ) );
			}
			foreach ( $contfiguration as $module ) {
				if ( isset( $module['options'] ) ) {
					$variables = array_merge( $variables, $module['options'] );
				}
			}
			$variables = array_filter( $variables );
			return $variables;
		}
	}
}
new Branda_Data();
