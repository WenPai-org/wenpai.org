<?php
/**
 * File callback.php
 *
 * @author Justin Greer <justin@justin-greer.com
 * @package WP Single Sign On Client
 *
 * This file is called when the OAuth param is found in the URL.
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Redirect the user back to the home page if logged in.
if ( is_user_logged_in() ) {
	wp_redirect( home_url() );
	exit;
}

// Grab a copy of the options and set the redirect location.
$user_redirect = wpssoc_get_user_redirect_url();

// Check for custom redirect
if ( ! empty( $_GET['redirect_uri'] ) ) {
	$user_redirect = esc_url( $_GET['redirect_uri'] );
}

// Authenticate Check and Redirect
if ( ! isset( $_GET['code'] ) ) {
	$params = array(
		'oauth'         => 'authorize',
		'response_type' => 'code',
		'client_id'     => wp_sso_get_option( 'client_id' ),
		'client_secret' => wp_sso_get_option( 'client_secret' ),
		'redirect_uri'  => site_url( '?auth=sso' ),
		'state'         => $user_redirect
	);
	$params = http_build_query( $params );

	wp_redirect( wp_sso_get_option( 'server_url' ) . '?' . $params );
	exit;
}

// Handle the callback from the server is there is one.
if ( ! empty( $_GET['code'] ) ) {

	// If the state is present, let's redirect to that link.
	if ( ! empty( $_GET['state'] ) ) {
		$user_redirect = sanitize_text_field( $_GET['state'] );
	}

	$code       = sanitize_text_field( $_GET['code'] );
	$server_url = wp_sso_get_option( 'server_url' ) . '?oauth=token';
	$response   = wp_remote_post( $server_url, array(
		'method'      => 'POST',
		'timeout'     => 45,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking'    => true,
		'headers'     => array(),
		'body'        => array(
			'grant_type'    => 'authorization_code',
			'code'          => $code,
			'client_id'     => wp_sso_get_option( 'client_id' ),
			'client_secret' => wp_sso_get_option( 'client_secret' ),
			'redirect_uri'  => site_url( '?auth=sso' )
		),
		'cookies'     => array(),
		'sslverify'   => false
	) );

	if ( is_wp_error( $response ) ) {
		$error_message = $response->get_error_message();
		exit( "Something went wrong: $error_message" );
	}

	$tokens = json_decode( wp_remote_retrieve_body( $response ) );

	if ( isset( $tokens->error ) ) {
		wp_die( $tokens->error_description );
	}

	$token_server_url = wp_sso_get_option( 'server_url' ) . '?oauth=me&access_token=' . $tokens->access_token;
	$token_response   = wp_remote_get( $token_server_url, array(
		'timeout'     => 45,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking'    => true,
		'headers'     => array(),
		'sslverify'   => false
	) );
	
	if ( is_wp_error( $token_response ) ) {
		$error_message = $token_response->get_error_message();
		echo "Something went wrong: $error_message";
	}

	$user_info = json_decode( $token_response['body'] );

	$user_id = username_exists( $user_info->user_login );

	if ( ! $user_id && email_exists( $user_info->user_email ) == false ) {

		if ( $options['login_only'] == 1 ) {
			wp_safe_redirect( wp_login_url() . '?wpo_login_only' );
			exit;
		}

		// Does not have an account... Register and then log the user in
		$random_password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
		$user_id         = wp_create_user( $user_info->user_login, $random_password, $user_info->user_email );

		if ( isset( $user_info->first_name ) ) {
			update_user_meta( $user_id, 'first_name', $user_info->first_name );
		}

		if ( isset( $user_info->last_name ) ) {
			update_user_meta( $user_id, 'last_name', $user_info->last_name );
		}

		// Trigger new user created action so that there can be modifications to what happens after the user is created.
		// This can be used to collect other information about the user.
		do_action( 'wpoc_user_created', $user_info, 1 );

		wp_clear_auth_cookie();
		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id );

		if ( is_user_logged_in() ) {
			wp_safe_redirect( $user_redirect );
			exit;
		}

	} else {

		// Already Registered... Log the User In using ID or Email
		$random_password = __( 'User already exists.  Password inherited.' );
		$user            = get_user_by( 'login', $user_info->user_login );

		/*
		 * Added just in case the user is not used but the email may be. If the user returns false from the user ID,
		 * we should check the user by email. This may be the case when the users are preregistered outside of OAuth
		 */
		if ( ! $user ) {
			$user = get_user_by( 'email', $user_info->user_email );
		}

		if ( isset( $user_info->first_name ) ) {
			update_user_meta( $user->ID, 'first_name', $user_info->first_name );
		}

		if ( isset( $user_info->last_name ) ) {
			update_user_meta( $user->ID, 'last_name', $user_info->last_name );
		}

		// Trigger action when a user is logged in.
		// This will help allow extensions to be used without modifying the core plugin.
		do_action( 'wpoc_user_login', $user_info, 1 );

		// User ID 1 is not allowed
		if ( '1' === $user->ID ) {
			wp_die( 'For security reasons, this user can not use Single Sign On' );
		}

		wp_clear_auth_cookie();
		wp_set_current_user( $user->ID );
		wp_set_auth_cookie( $user->ID );

		if ( is_user_logged_in() ) {
			wp_safe_redirect( $user_redirect );
			exit;
		}

	}

	exit( 'Single Sign On Failed. User mismatch or clash with existing data and SSO can not complete.' );
}