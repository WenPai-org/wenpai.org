<?php
/**
 * Scripts
 *
 * @package     UsernameChanger\Scripts
 * @since       3.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Load admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function username_changer_admin_scripts() {
	$js_dir  = USERNAME_CHANGER_URL . 'assets/js/';
	$css_dir = USERNAME_CHANGER_URL . 'assets/css/';

	// Use minified libraries if SCRIPT_DEBUG is turned off.
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	$minimum_length = username_changer()->settings->get_option( 'minimum_length', 3 );
	$screen         = get_current_screen();

	wp_enqueue_script( 'username-changer', $js_dir . 'admin.js', array( 'jquery' ), USERNAME_CHANGER_VER, true );
	wp_localize_script(
		'username-changer',
		'username_changer_vars',
		array(
			'nonce'                => wp_create_nonce( 'change_username' ),
			'ajaxurl'              => admin_url( 'admin-ajax.php' ),
			'change_button_label'  => username_changer()->settings->get_option( 'change_button_label', __( 'Change Username', 'username-changer' ) ),
			'save_button_label'    => username_changer()->settings->get_option( 'save_button_label', __( 'Save Username', 'username-changer' ) ),
			'cancel_button_label'  => username_changer()->settings->get_option( 'cancel_button_label', __( 'Cancel', 'username-changer' ) ),
			'please_wait_message'  => username_changer()->settings->get_option( 'please_wait_message', __( 'Please wait...', 'username-changer' ) ),
			'error_short_username' => username_changer_do_tags( username_changer()->settings->get_option( 'error_short_username', __( 'Username is too short, the minimum length is {minlength} characters.', 'username-changer' ) ) ),
			'current_screen'       => $screen->id,
			'can_change_username'  => username_changer_can_change_own_username(),
			'minimum_length'       => $minimum_length,
		)
	);

	wp_register_style( 'username-changer', $css_dir . 'admin.css', array(), USERNAME_CHANGER_VER );
	wp_enqueue_style( 'username-changer' );
}
add_action( 'admin_enqueue_scripts', 'username_changer_admin_scripts', 100 );
