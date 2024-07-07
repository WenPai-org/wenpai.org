<?php
/**
 * Plugin Name:       Gutena Ecosys Onboard
 * Description:       On board for gutena ecosystem.
 * Requires at least: 6.0
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            ExpressTech
 * Author URI:        https://expresstech.io
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       gutena-ecosys-onboard
 *
 * @package           create-block
 */

defined( 'ABSPATH' ) || exit;

/**
 * Abort if the class is already exists.
 */
if ( ! class_exists( 'Gutena_Ecosys_Onboard' ) && ! class_exists('Gutena_Kit') ) {

	class Gutena_Ecosys_Onboard {

		// The instance of this class
		private static $instance = null;

		/**
		 * Plugin Url.
		 *
		 * @access   private
		 * @var      array    $plugin_url  .
		 */
		private $plugin_url = '';

		/**
		 * Gutena install plugin slug.
		 *
		 * @access   private
		 * @var      array    $gutena_plugins  .
		 */
		private $install_plugin_slug = 'gutena-kit';

		/**
		 * Gutena block plugin blockname list.
		 *
		 * @access   private
		 * @var      array    $gutena_plugins_blockname  .
		 */
		private $gutena_plugins_blockname;

		// Returns the instance of this class.
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function __construct() {
			//plugin url : Retrieves a URL within the plugins
			$this->plugin_url = esc_url( trailingslashit( plugins_url( '', __FILE__ ) ) );

			//Gutena blockNames
			$this->gutena_plugins_blockname = array( 
				'gutena/accordion', 
				'gutena/forms', 
				'gutena/newsletter',
				'gutena/instagram-gallery',
				'gutena/lightbox',
				'gutena/tabs',
				'gutena/post-featured-tag',
				'gutena/star-ratings',
				'gutena/testimonials',
				'gutena/team',
				'gutena/slider'
			);
			
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_styles_and_script' ) );
			//Install gutena kit ajax
			add_action( 'wp_ajax_install_gutena_kit_plugin', array( $this, 'install_gutena_kit_plugin' ) );

			//Dismiss CTA
			add_action( 'wp_ajax_dismiss_gutena_kit_cta', array( $this, 'dismiss_cta' ) );
		}

		public function enqueue_block_editor_styles_and_script() {
			//return if Gutena_Kit already activated or require files not exists
			if ( ! file_exists( __DIR__ . '/build/index.asset.php' ) || ! function_exists( 'admin_url' ) || class_exists('Gutena_Kit') ) {
				return;
			}

			$asset_file = include(  __DIR__ . '/build/index.asset.php' );

			wp_enqueue_script( 'gutena-ecosys-onboard', $this->plugin_url . 'build/index.js', $asset_file['dependencies'], $asset_file['version'], true );
			wp_enqueue_style( 'gutena-ecosys-onboard-style', $this->plugin_url . 'build/index.css', array(), $asset_file['version'] );
			//Provide data for form submission script
			wp_localize_script(
				'gutena-ecosys-onboard',
				'gutenaEcosysOnboardData',
				array(
					'gutena_plugins_blockname' => $this->gutena_plugins_blockname,
					'gutena_kit_require' 	=> empty( get_transient( 'dismiss_gutena_kit_install_cta' ) ),
					'install_action'       	=> 'install_gutena_kit_plugin',
					'dismiss_action'       	=> 'dismiss_gutena_kit_cta',
					'ajax_url'            	=> admin_url( 'admin-ajax.php' ),
					'nonce'               	=> wp_create_nonce( 'updates' ),
					'gk_dashboard_url' => esc_url( admin_url( 'themes.php?page=gutenakit_admin_dashboard&tab=blocksettings' ) ),
					'gutena_weblink'        => esc_url( 'https://gutena.io/' ),
					'icons'					=> array(
						'logo_img'			=> $this->plugin_url.'assets/cta/logo.svg',
						'bg_img'			=> $this->plugin_url.'assets/cta/bg.png',
						'close_img'			=> $this->plugin_url.'assets/cta/close-icon.svg',
						'install_img'		=> $this->plugin_url.'assets/cta/download-icon.svg',
						'right_arrow'		=> $this->plugin_url.'assets/cta/right-arrow.svg',
						'right_arrow_dark' 	=> $this->plugin_url.'assets/cta/right-arrow-dark.svg',
					)
				)
			);
		}

		public function install_gutena_kit_plugin() {
			check_ajax_referer( 'updates' );
			//check if gutena kit already installed and activated
			if ( class_exists('Gutena_Kit') ) {
				wp_send_json_success( array(
					'message'         => __( 'Gutena kit already activated' ),
					'success_code'	  => 'already_activated'
				) );
			}

			if ( function_exists( 'wp_ajax_install_plugin' ) && isset( $_POST['slug'] ) && $this->install_plugin_slug === sanitize_key( wp_unslash( $_POST['slug'] ) ) ) {
				wp_ajax_install_plugin();
			}

			wp_send_json_error(
				array(
					'slug'         => '',
					'errorCode'    => 'function_not_exists',
					'errorMessage' => __( 'Something went wrong. Please try later' ),
				)
			);
		}

		public function dismiss_cta() {
			check_ajax_referer( 'updates' );
			//Set transient for 30days i.e dismiss notice for  30days
			set_transient( 'dismiss_gutena_kit_install_cta', '1', 2592000 );

			wp_send_json_success( array(
				'message'         => __( 'gutena kit install CTA dismissed successfully.' ),
				'success_code'	  => 'dismissed_successfully'
			) );
		}

	}

	Gutena_Ecosys_Onboard::get_instance();
}
