<?php
/**
 * Branda Export class.
 *
 * @package Branda
 * @subpackage Settings
 */
if ( ! class_exists( 'Branda_Export' ) ) {

	class Branda_Export extends Branda_Helper {

		public function __construct() {
			parent::__construct();
			$this->module = 'export';
			/**
			 * hooks
			 */
			add_filter( 'ultimatebranding_settings_export', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_export_process', array( $this, 'export' ) );
			/**
			 * Disable button "Save Changes".
			 *
			 * @since 3.0.0
			 */
			add_filter( 'ultimatebranding_settings_panel_show_submit', array( $this, 'disable_save_changes' ), 10, 2 );
		}

		/**
		 * Disable button "Save Changes".
		 *
		 * @since 3.0.0
		 */
		public function disable_save_changes( $status, $module ) {
			if ( $this->module === $module['module'] ) {
				return false;
			}
			return $status;
		}

		/**
		 * Handle form send
		 *
		 * @since 2.8.6
		 */
		public function update( $status ) {
			if ( ! isset( $_REQUEST['simple_options'] ) ) {
				return;
			}
			/**
			 * export
			 */
			if (
				isset( $_REQUEST['simple_options']['export'] )
				&& isset( $_REQUEST['simple_options']['export']['button'] )
			) {
				$this->export();
			}
		}

		/**
		 * Prepare export file
		 *
		 * @since 2.8.6
		 */
		public function export() {
			global $ub_version;
			if ( empty( $this->messages ) ) {
				$uba            = branda_get_uba_object();
				$this->messages = $uba->messages;
			}
			if (
				! isset( $_POST['simple_options'] )
				|| ! isset( $_POST['simple_options']['export'] )
				|| ! isset( $_POST['simple_options']['export']['_wpnonce'] )
			) {
				die( $this->messages['wrong'] );
			}
			$nonce_name = $this->get_nonce_action( 'export' );
			if ( ! wp_verify_nonce( $_POST['simple_options']['export']['_wpnonce'], $nonce_name ) ) {
				die( $this->messages['security'] );
			}
			$options_names = apply_filters( 'ultimate_branding_options_names', array() );
			/**
			 * get modules
			 */
			$modules = get_branda_activated_modules( 'raw' );
			$remove  = array(
				'utilities/data.php',
				'utilities/export.php',
				'utilities/import.php',
				'utilities/accessibility.php',
				'utilities/permissions.php',
			);
			/**
			 * If it is a subsite export, then we should limit exported data.
			 *
			 * @since 3.2.0
			 */
			$is_subsite = $this->is_network && ! is_network_admin();
			if ( $is_subsite ) {
				$remove[]      = 'utilities/subsites.php';
				$uba           = branda_get_uba_object();
				$options_names = apply_filters( 'branda_subsites_options_names', array() );
				$modules       = apply_filters( 'branda_subsites_allowed_modules', $modules );
			}
			/**
			 * Remove some keys
			 */
			foreach ( $remove as $key ) {
				if ( isset( $modules[ $key ] ) ) {
					unset( $modules[ $key ] );
				}
			}
			/**
			 * Data to export
			 */
			$data = array(
				'name'            => 'Branda',
				'url'             => 'https://wpmudev.com/plugins/ultimate-branding',
				'version'         => apply_filters( 'branda_version', 0 ),
				'timestamp'       => time(),
				'date'            => date( 'c' ),
				'activate_module' => $modules,
				'modules'         => array(),
			);
			$data = apply_filters( 'ultimate_branding_export_data', $data );
			/**
			 * options to remove
			 */
			$remove = array(
				'ultimate_branding_stats',
				'ub_stats',
				'ub_data',
				'ub_accessibility',
				'ub_permissions',
			);
			foreach ( $options_names as $name ) {
				if ( in_array( $name, $remove ) ) {
					continue;
				}
				$option_value = branda_get_option( $name );
				/**
				 * there is senseless to export empty value
				 */
				if ( empty( $option_value ) ) {
					continue;
				}
				$data['modules'][ $name ] = $option_value;
			}
			/**
			 * add debug information
			 *
			 * @since 1.8.7
			 */
			if (
				isset( $_POST['simple_options'] )
				&& isset( $_POST['simple_options']['export'] )
				&& isset( $_POST['simple_options']['export']['add_debug_information'] )
			) {
				$keys = array( 'name', 'description', 'wpurl', 'url', 'admin_email', 'charset', 'version', 'html_type', 'language' );
				$site = array();
				foreach ( $keys as $key ) {
					$site[ $key ] = get_bloginfo( $key );
				}
				$site['is_rtl']       = is_rtl();
				$site['is_multisite'] = is_multisite();
				$themes_data          = wp_get_themes();
				$themes               = array();
				$headers              = array( 'Name', 'Description', 'Author', 'Version', 'ThemeURI', 'AuthorURI', 'Status', 'Tags' );
				$current_theme        = wp_get_theme();
				$current_theme        = $current_theme->get( 'Name' );
				foreach ( $themes_data as $name => $theme ) {
					$themes[ $name ] = array();
					foreach ( $headers as $header ) {
						$themes[ $name ][ $header ] = $theme->get( $header );
					}
					$themes[ $name ]['active'] = $current_theme === $themes[ $name ]['Name'] ? 'active' : 'inactive';
				}
				$data['debug'] = array(
					'site'    => $site,
					'plugins' => get_plugins(),
					'themes'  => $themes,
				);
			}
			/**
			 * site name
			 */
			$sitename = $this->get_site_name();
			/**
			 * filename
			 */
			$wp_filename = sprintf(
				'%s.branda.%s.%d.json',
				$sitename,
				date( 'Y-m-d' ),
				time()
			);
			/**
			 * send it to browser
			 */
			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $wp_filename );
			header( 'Content-Type: text/json; charset=' . get_option( 'blog_charset' ), true );
			/**
			 * Check PHP version, for PHP < 3 do not add options
			 */
			$version = phpversion();
			$compare = version_compare( $version, '5.3', '<' );
			if ( $compare ) {
				echo json_encode( $data );
				exit;
			}
			$option = defined( 'JSON_PRETTY_PRINT' ) ? JSON_PRETTY_PRINT : null;
			echo json_encode( $data, $option );
			$this->uba->set_last_write( $this->module );
			exit;
		}

		/**
		 * Build form with options.
		 *
		 * @since 2.8.6
		 */
		protected function set_options() {
			$this->options = array(
				'export' => array(
					'title'       => __( 'Export', 'ub' ),
					'description' => __( 'Export your Branda configurations into a JSON file to use it on another website.', 'ub' ),
					'fields'      => array(
						'add_debug_information' => array(
							'type'           => 'checkbox',
							'checkbox_label' => __( 'Include debug info', 'ub' ),
							'description'    => array(
								'content'  => __( 'Checking this will include debug information of the installed themes and plugins in the export file.', 'ub' ),
								'position' => 'bottom',
							),
							'group'          => array(
								'begin'   => true,
								'end'     => true,
								'classes' => 'sui-border-frame',
							),
						),
						'button'                => array(
							'type'    => 'submit',
							'value'   => __( 'Export', 'ub' ),
							'sui'     => array(
								'blue',
							),
							'classes' => array(
								'branda-module-save',
							),
							'icon'    => 'download-cloud',
						),
						'_wpnonce'              => array(
							'type'  => ' hidden',
							'value' => $this->get_nonce_value( 'export' ),
						),
					),
				),
			);
		}
	}

}
new Branda_Export();
