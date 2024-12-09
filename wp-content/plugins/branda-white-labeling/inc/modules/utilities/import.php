<?php
/**
 * Branda Import class.
 *
 * Class that handle import functionality.
 *
 * @package Branda
 * @subpackage Settings
 */
if ( ! class_exists( 'Branda_Import' ) ) {

	/**
	 * Class Branda_Import.
	 */
	class Branda_Import extends Branda_Helper {

		/**
		 * Already imported
		 *
		 * @since 3.1.0
		 */
		private $imported = array();

		/**
		 * Branda_Import constructor.
		 */
		public function __construct() {
			parent::__construct();
			$this->module = 'import';
			// Register all hooks for the module.
			add_filter( 'ultimatebranding_settings_import', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_import_process', array( $this, 'update' ) );
			add_filter( 'branda_handle_group_page', array( $this, 'steps' ), 10, 2 );
			add_filter( 'branda_sui_wrap_class', array( $this, 'container_classes' ), 10, 2 );
			/**
			 *Hooks on error page
			 */
			add_filter( 'branda_change_footer', array( $this, 'hide_footer_links' ), 10, 2 );
			add_filter( 'branda_footer_text', array( $this, 'change_footer_text' ), 10, 2 );
			add_filter( 'branda_sui_wrap_class', array( $this, 'add_error_class' ), 10, 2 );
		}

		/**
		 * Helper to find variables from post or get.
		 *
		 * @param string $name Name of the variable.
		 *
		 * @since 3.0.0
		 *
		 * @return mixed
		 */
		private function get_variable( $name ) {
			$value = ! empty( $_GET[$name] ) ? sanitize_text_field( $_GET[$name] ) : null;
			if ( empty( $value ) ) {
				$value = ! empty( $_POST[$name] ) ? sanitize_text_field( $_POST[$name] ) : '';
			}
			return $value;
		}

		/**
		 * Add container class on admin page.
		 *
		 * @param array  $classes Classes.
		 * @param string $module  Module name.
		 *
		 * @since 3.0.0
		 *
		 * @return array
		 */
		public function container_classes( $classes, $module ) {
			if ( $this->module === $module ) {
				$step = $this->get_variable( 'step' );
				if ( empty( $step ) ) {
					return $classes;
				}
				$classes[] = sprintf( $this->get_name( $step ) );
			}
			return $classes;
		}

		/**
		 * Handle import steps from form.
		 *
		 * @param string $content Content.
		 * @param string $module  Module name.
		 *
		 * @since 3.0.0
		 *
		 * @return string $content
		 */
		public function steps( $content, $module ) {
			if ( $this->module !== $module ) {
				return $content;
			}
			$key = $this->get_variable( 'key' );
			if ( empty( $key ) ) {
				return $content;
			}
			$step    = $this->get_variable( 'step' );
			$name    = $this->get_name();
			$user_id = get_current_user_id();
			$data    = get_user_option( $name, $user_id );
			/**
			 * Handle import errors
			 */
			if ( 'error' === $key ) {
				$template = 'admin/modules/import/errors/unknown';
				$args     = array(
					'filename'   => isset( $data['filename'] ) && ! empty( $data['filename'] ) ? $data['filename'] : _x( 'unknown', 'File name on import error screen.', 'ub' ),
					'cancel_url' => $this->get_cancel_url(),
				);
				if ( isset( $data['error'] ) ) {
					switch ( $data['error'] ) {
						case 'invalid-file':
						case 'no-configuration':
							$template = sprintf( 'admin/modules/import/errors/%s', $data['error'] );
							break;
						default:
							break;
					}
				}
				$content = $this->render( $template, $args, true );
				return $content;
			}
			if ( empty( $data ) || ! is_array( $data ) || empty( $data[ $key ] ) ) {
				return $content;
			}
			$data = $data[ $key ];
			// Move on with step.
			switch ( $step ) {
				case 'import':
					if ( isset( $data['version'] ) && $data['version'] !== $this->build ) {
						$version_compare = version_compare( '3', $data['version'], '>' );
						if ( $version_compare ) {
							$args['cancel_url'] = $this->get_cancel_url();
							$args['version']    = $data['version'];
							$args['product']    = __( 'Ultimate Branding', 'ub' );
							$template           = 'admin/modules/import/errors/version';
							$content            = $this->render( $template, $args, true );
							return $content;
						}
					}
					return $this->import_step_2( $content, $key, $data );
				break;
				case 'confirm':
					return $this->import_step_3( $content, $key, $data );
				break;
			}
			return $content;
		}

		/**
		 * Import process step 2.
		 *
		 * @param string $content Content.
		 * @param string $key     Key.
		 * @param array  $data    Data.
		 *
		 * @since 3.0.0
		 *
		 * @return string $content
		 */
		private function import_step_2( $content, $key, $data ) {
			global $branda_network;
			$activate_modules = array();
			if ( isset( $data['activate_module'] )
				 && is_array( $data['activate_module'] )
			) {
				$activate_modules = $data['activate_module'];
			}
			$keys_to_remove = array(
				'key',
				'step',
				'_wpnonce',
			);
			$args           = array(
				'action' => remove_query_arg( $keys_to_remove ),
				'key'    => $key,
				'groups' => branda_get_groups_list(),
			);
			$configuration  = $this->uba->get_configuration();
			$modules        = array();
			foreach ( $configuration as $id => $module ) {
				if ( $branda_network && ! is_network_admin() ) {
					$subsite = apply_filters( 'branda_module_check_for_subsite', false, $id, $module );
					if ( ! $subsite ) {
						continue;
					}
				}
				if ( ! isset( $modules[ $module['group'] ] ) ) {
					$modules[ $module['group'] ] = array();
				}
				if ( array_key_exists( $id, $activate_modules ) ) {
					$modules[ $module['group'] ]['modules'][ $id ]           = $module;
					$modules[ $module['group'] ]['modules'][ $id ]['status'] = 'active';
				}
			}
			foreach ( $modules as $group => $data ) {
				if (
					! isset( $data['modules'] )
					|| empty( $data['modules'] )
				) {
					unset( $modules[ $group ] );
					continue;
				}
				$m = $data['modules'];
				uasort( $m, array( $this->uba, 'sort_modules_by_name' ) );
				$modules[ $group ]['modules'] = $m;
			}
			$args['modules'] = $modules;
			// Cancel link to import form.
			$args['cancel_url'] = $this->get_cancel_url();
			$template           = 'admin/modules/import/configure-modules';
			if ( empty( $modules ) ) {
				$template         = 'admin/modules/import/errors/no-configuration';
				$args['filename'] = '';
			}
			$content = $this->render( $template, $args, true );
			return $content;
		}

		/**
		 * Import process step 3.
		 *
		 * @param string $content Content.
		 * @param string $key     Key.
		 * @param array  $data    Data.
		 *
		 * @since 3.0.0
		 *
		 * @return string $content
		 */
		private function import_step_3( $content, $key, $data ) {
			global $branda_network;
			$nonce_name = $this->get_nonce_action( 'confirm' );
			if ( ! wp_verify_nonce( $_POST['_wpnonce'], $nonce_name ) ) {
				return $this->uba->messages['security'];
			}
			$number = 0;
			if (
				isset( $_POST['modules'] )
				&& ! empty( $_POST['modules'] )
			) {
				$configuration = $this->uba->get_configuration();
				/**
				 * KM_Download_Remote_Image
				 */
				$file = branda_files_dir( 'class-download-remote-image.php' );
				include_once $file;
				foreach ( $configuration as $id => $module ) {
					if ( array_key_exists( $module['module'], $_POST['modules'] ) ) {
						if ( $branda_network && ! is_network_admin() ) {
							$subsite = apply_filters( 'branda_module_check_for_subsite', false, $id, $module );
							if ( ! $subsite ) {
								continue;
							}
						}
						$number ++;
						$this->uba->activate_module( $id );
						if ( isset( $module['options'] ) ) {
							foreach ( $module['options'] as $option_name ) {
								if (
									isset( $data['modules'] )
									&& isset( $data['modules'][ $option_name ] )
								) {
									$value                   = $this->maybe_fetch_attachments(
										$data['modules'][ $option_name ],
										$module
									);
									$value['plugin_version'] = $this->build;
									$value['imported']       = array(
										'time'    => time(),
										'version' => isset( $data['version'] ) ? $data['version'] : 'unknown',
									);
									branda_update_option( $option_name, $value );
								}
							}
						}
					}
				}
			}
			$message = array(
				'message' => $this->uba->messages['fail'],
			);
			if ( 0 < $number ) {
				$message = array(
					'type'    => 'success',
					'message' => sprintf(
						_n(
							'%s module imported and configured successfully.',
							'%s modules imported and configured successfully.',
							$number,
							'ub'
						),
						$this->bold( $number )
					),
				);
				$this->uba->set_last_write( $this->module );
			}
			$this->uba->add_message( $message );
			// Delete user option data.
			$name    = $this->get_name();
			$user_id = get_current_user_id();
			delete_user_option( $name, $user_id );
			/**
			 * update import stats
			 */
			do_action( 'branda_admin_stats_write', 'import' );
		}

		/**
		 * Handle form submit.
		 *
		 * @param bool $status Current status.
		 *
		 * @since 2.8.6
		 *
		 * @return array
		 */
		public function update( $status ) {
			if ( empty( $this->messages ) ) {
				$this->messages = $this->uba->messages;
			}
			// Check for required values.
			if ( ! isset( $_POST['simple_options'] )
				 || ! isset( $_POST['simple_options']['import'] )
				 || ! isset( $_POST['simple_options']['import']['_wpnonce'] )
			) {
				die( $this->messages['wrong'] );
			}
			$nonce_name = $this->get_nonce_action( 'import' );
			if ( ! wp_verify_nonce( $_POST['simple_options']['import']['_wpnonce'], $nonce_name ) ) {
				die( $this->messages['security'] );
			}
			// Check if import exist.
			if ( ! isset( $_FILES['import'] ) ) {
				die( $this->messages['wrong'] );
			}
			// Get import file.
			$file = $_FILES['import'];
			if ( ! empty( $file['error'] ) ) {
				die( $this->messages['wrong'] );
			}
			// Should be a json file.
			if ( ! preg_match( '/json$/i', $file['name'] ) ) {
				die( $this->messages['wrong'] );
			}
			$import       = wp_import_handle_upload();
			$import_id    = $import['id'];
			$filename     = $import['file'];
			$file_content = file_get_contents( $filename );
			/**
			 *  Delete file.
			 */
			wp_delete_attachment( $import_id );
			/**
			 * User ID && option name
			 */
			$name    = $this->get_name();
			$user_id = get_current_user_id();
			$options = json_decode( $file_content, true );
			/**
			 * Check configurations: data integration, it should be an array
			 */
			if ( ! is_array( $options ) ) {
				$data = array(
					'key'      => 'error',
					'step'     => 'import',
					'module'   => 'import',
					'error'    => 'invalid-file',
					'filename' => isset( $_FILES['import']['name'] ) ? $_FILES['import']['name'] : $filename,
				);
				update_user_option( $user_id, $name, $data );
				return $data;
			}
			/**
			 * Check configurations: modules settings
			 */
			$modules = array();
			if ( isset( $options['modules'] ) ) {
				$modules = $options['modules'];
				$unset   = array( 'ub_stats', 'unknown', 'ub_data' );
				foreach ( $unset as $key ) {
					if ( isset( $modules[ $key ] ) ) {
						unset( $modules[ $key ] );
					}
				}
			}
			if ( empty( $modules ) ) {
				$data = array(
					'key'      => 'error',
					'step'     => 'import',
					'module'   => 'import',
					'error'    => 'no-configuration',
					'filename' => isset( $_FILES['import']['name'] ) ? $_FILES['import']['name'] : $filename,
				);
				update_user_option( $user_id, $name, $data );
				return $data;
			}
			// Add import data.
			$options['timestamp_import'] = time();
			$options['version_import']   = $this->build;
			// Put in user options.
			$id   = $this->generate_id( $options );
			$data = get_user_option( $name, $user_id );
			if ( empty( $data ) || ! is_array( $data ) ) {
				$data = array();
			}
			$data[ $id ] = $options;
			$this->safe_update_user_option( $user_id, $name, $data );
			return array(
				'key'      => $id,
				'step'     => 'import',
				'_wpnonce' => $this->get_nonce_value( 'import', 'step' ),
				'module'   => 'import',
			);
		}

		private function safe_update_user_option( $user_id, $key, $value ) {
			// The function update_user_option calls wp_unslash so call wp_slash first so that legitimate slashes don't get removed
			// For example ub_text_replacement contains regex with backslashes that we want to keep
			return update_user_option( $user_id, $key, wp_slash( $value ) );
		}

		/**
		 * Build form with options.
		 *
		 * @since 2.8.6
		 */
		protected function set_options() {
			$this->options = array(
				'import' => array(
					'title'       => __( 'Import', 'ub' ),
					'description' => __( 'Use this tool to import the Branda configurations.', 'ub' ),
					'hide-reset'  => true,
					'hide-th'     => true,
					'fields'      => array(
						'file'     => array(
							'type'        => 'file',
							'name'        => 'import',
							'description' => array(
								'content'  => __( 'Choose a JSON (.json) file to import the configurations.', 'ub' ),
								'position' => 'bottom',
							),
						),
						'button'   => array(
							'type'     => 'submit',
							'value'    => __( 'Import', 'ub' ),
							'disabled' => true,
							'sui'      => array(
								'blue',
							),
							'icon'     => 'upload-cloud',
							'classes'  => array(
								$this->get_name( 'import' ),
								'branda-module-save',
							),
						),
						'_wpnonce' => array(
							'type'  => ' hidden',
							'value' => $this->get_nonce_value( 'import' ),
						),
						'module'   => array(
							'type'  => 'hidden',
							'value' => 'import',
						),
					),
				),
			);
		}

		/**
		 * Get cancel url
		 *
		 * @since 3.0.0
		 *
		 * @return string $cancel_url Cancel URL
		 */
		private function get_cancel_url() {
			$cancel_url = add_query_arg(
				array(
					'page'   => 'branding_group_data',
					'module' => 'import',
				),
				is_network_admin() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' )
			);
			return $cancel_url;
		}

		/**
		 * Hide plugin footer links on import ERRORS pages.
		 *
		 * @since 3.0.0
		 *
		 * @param boolean $hide_links Show or hide plugin footer.
		 * @param string  $module Current module.
		 */
		public function hide_footer_links( $hide_links, $module ) {
			if ( $module !== $this->module ) {
				return $hide_links;
			}
			$key = $this->get_variable( 'key' );
			if ( 'error' === $key ) {
				return true;
			}
			return $hide_links;
		}

		/**
		 * Hide plugin footer on import ERRORS pages.
		 *
		 * @since 3.0.0
		 *
		 * @param string $footer_text Plugin footer text.
		 * @param string $module Current module.
		 */
		public function change_footer_text( $footer_text, $module ) {
			if ( $module !== $this->module ) {
				return $footer_text;
			}
			$key = $this->get_variable( 'key' );
			if ( 'error' === $key ) {
				return '';
			}
			return $footer_text;
		}

		/**
		 * Add error class to sui-wrap on import ERRORS pages.
		 *
		 * @since 3.0.0
		 *
		 * @param array  $classes Classes of sui-wrap.
		 * @param string $module Current module.
		 */
		public function add_error_class( $classes, $module ) {
			if ( $module !== $this->module ) {
				return $classes;
			}
			$key = $this->get_variable( 'key' );
			if ( 'error' === $key ) {
				$classes[] = $this->get_name( 'error' );
			}
			return $classes;
		}

		/**
		 * Try to fetch logos and backgrounds.
		 *
		 * @since 3.1.0
		 *
		 * @param array  $value
		 * @param string $module Module.
		 */
		private function maybe_fetch_attachments( $value, $module ) {
			foreach ( $value as $group => $group_data ) {
				if ( ! is_array( $group_data ) ) {
					continue;
				}
				foreach ( $group_data as $key => $field ) {
					if ( ! is_array( $field ) ) {
						continue;
					}
					switch ( $key ) {
						/**
						 * Single image
						 */
						case 'favicon_meta':
						case 'logo_image_meta':
						case 'form_background_image_meta':
						case 'logo_meta':
							$image = $this->image( $field[0] );
							if ( ! is_wp_error( $image ) ) {
								$id_key                     = preg_replace( '/_meta/', '', $key );
								$value[ $group ][ $id_key ] = $image['id'];
								$value[ $group ][ $key ][0] = $image['url'];
							}
							break;
						/**
						 * Background
						 */
						case 'content_background':
							$new = array();
							foreach ( $field as $one ) {
								if (
									isset( $one['meta'] )
									&& is_array( $one['meta'] )
								) {
									$image = $this->image( $one['meta'][0] );
									if ( ! is_wp_error( $image ) ) {
										$one['value']   = $image['id'];
										$one['meta'][0] = $image['url'];
									}
								}
								$new[] = $one;
							}
							$value[ $group ][ $key ] = $new;
							break;

						default:
					}
				}
			}
			return $value;
		}

		/**
		 * Import image to local WordPress
		 *
		 * @since 3.1.0
		 *
		 * @param string $url URL to resource.
		 *
		 * @return array/WP error result.
		 */
		private function image( $url ) {
			if ( isset( $this->imported[ $url ] ) ) {
				return $this->imported[ $url ];
			}
			$download_remote_image = new KM_Download_Remote_Image( $url );
			$attachment_id         = $download_remote_image->download();
			if ( false !== $attachment_id ) {
				$this->imported[ $url ] = array(
					'id'  => $attachment_id,
					'url' => wp_get_attachment_url( $attachment_id ),
				);
				return $this->imported[ $url ];
			}
			return new WP_Error( 'broke', __( 'Branda failed to import a image.', 'ub' ) );
		}
	}

}
new Branda_Import();
