<?php
/**
 * License handler for Simple Settings
 *
 * @package     Widgit\SimpleSettings\License
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Simple Settings license handler class
 *
 * @since       1.0.0
 */
class Simple_Settings_License {


	/**
	 * The licensed file
	 *
	 * @access      private
	 * @since       1.0.0
	 * @var         string $file The licensed file
	 */
	private $file;


	/**
	 * The license key
	 *
	 * @access      private
	 * @since       1.0.0
	 * @var         string $license The license key
	 */
	private $license;


	/**
	 * The name of the item
	 *
	 * @access      private
	 * @since       1.0.0
	 * @var         string $item_name The name of the item
	 */
	private $item_name;


	/**
	 * The ID of the item
	 *
	 * @access      private
	 * @since       1.0.0
	 * @var         int $item_id The ID of the item
	 */
	private $item_id;


	/**
	 * The short name of the item
	 *
	 * @access      private
	 * @since       1.0.0
	 * @var         string $item_shortname The short name of the item
	 */
	private $item_shortname;


	/**
	 * The version of the item
	 *
	 * @access      private
	 * @since       1.0.0
	 * @var         string $version The version of the item
	 */
	private $version;


	/**
	 * The author of the item
	 *
	 * @access      private
	 * @since       1.0.0
	 * @var         string $author The author of the item
	 */
	private $author;


	/**
	 * The slug of the item
	 *
	 * @access      private
	 * @since       1.0.0
	 * @var         string $slug The slug of the item
	 */
	private $slug;


	/**
	 * The API URL
	 *
	 * @access      private
	 * @since       1.0.0
	 * @var         string $api_url The API URL
	 */
	private $api_url = '';


	/**
	 * Class constructor
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       string $_file The item file.
	 * @param       string $_slug The item slug.
	 * @param       string $_item The item name.
	 * @param       string $_version The item version.
	 * @param       string $_author The item author.
	 * @param       string $_api_url The API URL.
	 * @return      void
	 */
	public function __construct( $_file, $_slug, $_item, $_version, $_author, $_api_url = null ) {
		$this->file = $_file;

		if ( is_numeric( $_item ) ) {
			$this->item_id = absint( $_item );
		} else {
			$this->item_name = $_item;
		}

		$this->item_shortname = preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $this->item_name ) ) );
		$this->item_slug      = $_slug;

		$options = get_option( $this->item_shortname . '_settings', '' );

		$this->version = $_version;
		$this->license = ( isset( $options['license_key'] ) ? trim( $options['license_key'] ) : '' );
		$this->author  = $_author;
		$this->api_url = is_null( $_api_url ) ? $this->api_url : $_api_url;

		// Setup hooks.
		$this->includes();
		$this->hooks();
	}


	/**
	 * Include the updater class
	 *
	 * @access  private
	 * @return  void
	 */
	private function includes() {
		if ( ! class_exists( 'Simple_Settings_Plugin_Updater' ) ) {
			require_once 'Simple_Settings_Plugin_Updater.php';
		}
	}


	/**
	 * Setup hooks
	 *
	 * @access  private
	 * @return  void
	 */
	private function hooks() {
		// Register settings.
		add_filter( $this->item_shortname . '_settings_tabs', array( $this, 'tabs' ) );
		add_filter( $this->item_shortname . '_registered_settings', array( $this, 'settings' ) );

		// Activate license key on settings save.
		add_action( 'admin_init', array( $this, 'activate_license' ) );

		// Deactivate license key.
		add_action( 'admin_init', array( $this, 'deactivate_license' ) );

		// Updater.
		add_action( 'admin_init', array( $this, 'auto_updater' ), 0 );

		add_action( 'admin_notices', array( $this, 'notices' ) );
	}


	/**
	 * Auto updater
	 *
	 * @access  private
	 * @return  void
	 */
	public function auto_updater() {
		if ( 'valid' !== get_option( $this->item_shortname . '_license_active' ) ) {
			return;
		}

		$args = array(
			'version' => $this->version,
			'license' => $this->license,
			'author'  => $this->author,
		);

		if ( ! empty( $this->item_id ) ) {
			$args['item_id'] = $this->item_id;
		} else {
			$args['item_name'] = $this->item_name;
		}

		// Setup the updater.
		$edd_updater = new Simple_Settings_Plugin_Updater(
			$this->api_url,
			$this->file,
			$args
		);
	}


	/**
	 * Add license tab to settings
	 *
	 * @access      public
	 * @param       array $tabs The setting tabs.
	 * @return      array $tabs The updated setting tabs
	 */
	public function tabs( $tabs ) {
		$tabs['license'] = __( 'Licensing', 'simple-settings' );

		return $tabs;
	}


	/**
	 * Add license field to settings
	 *
	 * @access      public
	 * @param       array $settings The existing settings.
	 * @return      array The updated settings
	 */
	public function settings( $settings ) {
		$license_settings = array(
			'license' => array(
				array(
					'id'      => $this->item_shortname . '_license_key',
					// Translators: The item name for this license.
					'name'    => sprintf( __( '%1$s License Key', 'simple-settings' ), $this->item_name ),
					'desc'    => __( 'Please enter your license key to enable automatic updates and support.', 'simple-settings' ),
					'type'    => 'license_key',
					'options' => array( 'is_valid_license_option' => $this->item_shortname . '_license_active' ),
					'size'    => 'regular',
				),
			),
		);

		return array_merge( $settings, $license_settings );
	}


	/**
	 * Activate the license key
	 *
	 * @access  public
	 * @return  void
	 */
	public function activate_license() {
		check_admin_referer( $this->func . '_settings_nonce', $this->func . '_settings_nonce' );

		if ( ! isset( $_POST[ $this->item_shortname . '_settings' ] ) ) {
			return;
		}

		if ( ! isset( $_POST[ $this->item_shortname . '_settings' ][ $this->item_shortname . '_license_key' ] ) ) {
			return;
		}

		foreach ( $_POST as $key => $value ) {
			if ( false !== strpos( $key, 'license_key_deactivate' ) ) {
				// Don't activate a key when deactivating a different key.
				return;
			}
		}

		$nonce = esc_url( $this->item_shortname ) . '_license_key-nonce';

		if ( ! isset( $_REQUEST[ $nonce ] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ $nonce ] ) ), $nonce ) ) {
			wp_die( esc_attr__( 'Nonce verification failed', 'simple-settings' ), esc_attr__( 'Error', 'simple-settings' ), array( 'response' => 403 ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( 'valid' === get_option( $this->item_shortname . '_license_active' ) ) {
			return;
		}

		$license = sanitize_text_field( wp_unslash( $_POST[ $this->item_shortname . '_settings' ][ $this->item_shortname . '_license_key' ] ) );

		if ( empty( $license ) ) {
			return;
		}

		// Data to send to the API.
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => rawurlencode( $this->item_name ),
			'url'        => home_url(),
		);

		// Call the API.
		$response = wp_remote_post(
			$this->api_url,
			array(
				'timeout'   => 3,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

		// Make sure there are no errors.
		if ( is_wp_error( $response ) ) {
			return;
		}

		// Tell WordPress to look for updates.
		set_site_transient( 'update_plugins', null );

		// Decode license data.
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		update_option( $this->item_shortname . '_license_active', $license_data->license );

		if ( ! (bool) $license_data->success ) {
			set_transient( $this->item_shortname . '_license_error', $license_data, 1000 );
		} else {
			delete_transient( $this->item_shortname . '_license_error' );
		}
	}


	/**
	 * Deactivate the license key
	 *
	 * @access  public
	 * @return  void
	 */
	public function deactivate_license() {
		if ( ! isset( $_POST[ $this->item_shortname . '_settings' ] ) ) {
			return;
		}

		if ( ! isset( $_POST[ $this->item_shortname . '_settings' ][ $this->item_shortname . '_license_key' ] ) ) {
			return;
		}

		$nonce = $this->item_shortname . '_license_key-nonce';

		if ( ! isset( $_REQUEST[ $nonce ] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ $nonce ] ) ), $nonce ) ) {
			wp_die( esc_attr__( 'Nonce verification failed', 'simple-settings' ), esc_attr__( 'Error', 'simple-settings' ), array( 'response' => 403 ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Run on deactivate button press.
		if ( isset( $_POST[ $this->item_shortname . '_license_key_deactivate' ] ) ) {
			$license = sanitize_text_field( wp_unslash( $_POST[ $this->item_shortname . '_settings' ][ $this->item_shortname . '_license_key' ] ) );

			if ( empty( $license ) ) {
				return;
			}

			// Data to send to the API.
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => $license,
				'item_name'  => rawurlencode( $this->item_name ),
				'url'        => home_url(),
			);

			// Call the API.
			$response = wp_remote_post(
				$this->api_url,
				array(
					'timeout'   => 3,
					'sslverify' => false,
					'body'      => $api_params,
				)
			);

			// Make sure there are no errors.
			if ( is_wp_error( $response ) ) {
				return;
			}

			// Decode the license data.
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			delete_option( $this->item_shortname . '_license_active' );

			if ( ! (bool) $license_data->success ) {
				set_transient( $this->item_shortname . '_license_error', $license_data, 1000 );
			} else {
				delete_transient( $this->item_shortname . '_license_error' );
			}
		}
	}


	/**
	 * Admin notices for errors
	 *
	 * @access  public
	 * @return  void
	 */
	public function notices() {
		if ( isset( $_REQUEST[ $this->sanitize_key( $args['id'] ) . '-nonce' ] ) ) {
			check_admin_referer( $this->sanitize_key( $args['id'] ) . '-nonce', $this->sanitize_key( $args['id'] ) . '-nonce' );
		}

		if ( ! isset( $_GET['page'] ) || $this->item_slug . '-settings' !== $_GET['page'] ) {
			return;
		}

		if ( ! isset( $_GET['tab'] ) || 'license' !== $_GET['tab'] ) {
			return;
		}

		$license_error = get_transient( $this->item_shortname . '_license_error' );

		if ( false === $license_error ) {
			return;
		}

		if ( ! empty( $license_error->error ) ) {

			switch ( $license_error->error ) {
				case 'item_name_mismatch':
					$message = __( 'This license does not belong to the product you have entered it for.', 'simple-settings' );
					break;
				case 'no_activations_left':
					$message = __( 'This license does not have any activations left', 'simple-settings' );
					break;
				case 'expired':
					$message = __( 'This license key is expired. Please renew it.', 'simple-settings' );
					break;
				default:
					// Translators: The details of the error code.
					$message = sprintf( __( 'There was a problem activating your license key, please try again or contact support. Error code: %s', 'simple-settings' ), $license_error->error );
					break;
			}
		}

		if ( ! empty( $message ) ) {
			echo '<div class="error">';
			echo '<p>' . esc_html( $message ) . '</p>';
			echo '</div>';
		}

		delete_transient( $this->item_shortname . '_license_error' );
	}
}
