<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Main Class
 *
 * @author Justin Greer <justin@justin-greer.com>
 * @package WP Single Sign On Client
 */
class WPSSO_Client {

	/** Version */
	public $version = "4.1.0";

	/** Server Instance */
	public static $_instance = null;

	/** Default Settings */
	protected $default_settings = array(
		'client_id'            => '',
		'client_secret'        => '',
		'server_url'           => '',
		'server_oauth_trigger' => 'oauth',
		'server_auth_endpoint' => 'authorize',
		'server_token_endpont' => 'token',
		'server_user_endpoint' => 'me'
	);

	function __construct() {
		add_action( "init", array( __CLASS__, "includes" ) );
	}

	/**
	 * populate the instance if the plugin for extendability
	 * @return object plugin instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * plugin includes called during load of plugin
	 * @return void
	 */
	public static function includes() {
		require_once( WPSSO_FILE . '/includes/actions.php' );
		require_once( WPSSO_FILE . '/includes/functions.php' );
		require_once( WPSSO_FILE . '/includes/admin-options.php' );
		require_once( WPSSO_FILE . '/includes/rewrites.php' );
		require_once( WPSSO_FILE . '/includes/filters.php' );
	}

	/**
	 * Plugin Setup
	 */
	public function setup() {

		$options = get_option( "wposso_options" );
		if ( ! isset( $options["server_url"] ) ) {
			update_option( "wposso_options", $this->default_settings );
		}

		$this->install();
	}

	/**
	 * Plugin Initializer
	 */
	public function plugin_init() {

	}

	/**
	 * Plugin Install
	 */
	public function install() {
	}

	/**
	 * Plugin Upgrade
	 */
	public function upgrade() {
	}

}

function WPSSO_Client() {
	return WPSSO_Client::instance();
}

$GLOBAL['WPSSO_Client'] = WPSSO_Client();