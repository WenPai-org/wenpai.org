<?php
/**
 * Admin actions
 *
 * @package     Username_Changer\Admin\Actions
 * @since       3.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Process username change requests through AJAX
 *
 * @since       3.0.0
 * @return      void
 */
function username_changer_ajax_username_change() {
	$response = array(
		'success'   => false,
		'new_nonce' => wp_create_nonce( 'change_username' ),
	);

	// Validate nonce.
	check_ajax_referer( 'change_username', 'security' );

	// Validate request.
	if ( empty( $_POST['new_username'] ) || empty( $_POST['old_username'] ) ) {
		$response['message'] = __( 'Invalid request.', 'username-changer' );
		wp_send_json( $response );
	}

	$old_username     = trim( wp_strip_all_tags( wp_unslash( $_POST['old_username'] ) ) );
	$old_username_tag = esc_attr( sanitize_text_field( wp_unslash( $_POST['old_username'] ) ) );
	$new_username     = trim( wp_strip_all_tags( wp_unslash( $_POST['new_username'] ) ) );
	$new_username_tag = esc_attr( sanitize_text_field( wp_unslash( $_POST['new_username'] ) ) );
	$current_user     = wp_get_current_user();
	$current_username = $current_user->user_login;

	// Make sure the user can change this username.
	if ( ! current_user_can( 'edit_users' ) ) {
		if ( $current_username !== $old_username || ! username_changer_can_change_own_username() ) {
			$response['message'] = username_changer_do_tags( username_changer()->settings->get_option( 'error_wrong_permissions', __( 'You do not have the correct permissions to change this username.', 'username-changer' ) ), $old_username_tag, $new_username_tag );
			wp_send_json( $response );
		}
	}

	// Validate new username.
	if ( ! validate_username( $new_username ) ) {
		$response['message'] = username_changer_do_tags( username_changer()->settings->get_option( 'error_invalid_chars', __( 'The username {new_username} contains invalid characters. Please enter a valid username.', 'username-changer' ) ), $old_username_tag, $new_username_tag );
		wp_send_json( $response );
	}

	// Make sure new username isn't on the illegal logins list.
	$illegal_user_logins = array_map( 'strtolower', (array) apply_filters( 'illegal_user_logins', array() ) );
	if ( in_array( $new_username, $illegal_user_logins, true ) ) {
		$response['message'] = __( 'Sorry, that username is not allowed.', 'username-changer' );
		wp_send_json( $response );
	}

	// Make sure the new username isn't already taken.
	if ( username_exists( $new_username ) ) {
		$response['message'] = username_changer_do_tags( username_changer()->settings->get_option( 'error_duplicate_username', __( 'The username {new_username} is already in use. Please try again.', 'username-changer' ) ), $old_username_tag, $new_username_tag );
		wp_send_json( $response );
	}

	// Change the username.
	$success = username_changer_process( $old_username, $new_username );

	if ( $success ) {
		$response['success'] = true;

		// Append re-login link if old_username == current_username.
		if ( $old_username === $current_username ) {
			$response['message'] = sprintf(
				'%s&nbsp;<a href="%s">%s</a>',
				username_changer_do_tags( username_changer()->settings->get_option( 'success_message', __( 'Username successfully changed to {new_username}.', 'username-changer' ) ), $old_username_tag, $new_username_tag ),
				wp_login_url(),
				username_changer_do_tags( username_changer()->settings->get_option( 'relogin_message', __( 'Click here to log back in.', 'username-changer' ) ), $old_username_tag, $new_username_tag )
			);
		} else {
			$response['message'] = username_changer_do_tags( username_changer()->settings->get_option( 'success_message', __( 'Username successfully changed to {new_username}.', 'username-changer' ) ), $old_username_tag, $new_username_tag );

			// Send emails as necessary.
			if ( username_changer()->settings->get_option( 'enable_notifications', false ) ) {
				$changed_user = get_user_by( 'login', $old_username );
				$mail_to      = $changed_user->user_email;
				$subject      = username_changer_do_tags( username_changer()->settings->get_option( 'email_subject', __( 'Username change notification - {sitename}', 'username-changer' ) ), $old_username_tag, $new_username_tag );
				$message      = username_changer_do_tags( username_changer()->settings->get_option( 'email_message', __( 'Howdy! We\'re just writing to let you know that your username for {siteurl} has been changed to {new_username}.', 'username-changer' ) . "\n\n" . __( 'Login now at {loginurl}', 'username-changer' ) ), $old_username_tag, $new_username_tag );

				$subject = stripslashes( $subject );
				$message = stripslashes( $message );

				$from_name  = get_bloginfo( 'name' );
				$from_email = get_bloginfo( 'admin_email' );

				$headers  = 'From: ' . stripslashes_deep( html_entity_decode( $from_name, ENT_COMPAT, 'UTF-8' ) ) . " <$from_email>\r\n";
				$headers .= 'Reply-To: ' . $from_email . "\r\n";

				wp_mail( $mail_to, $subject, $message, $headers ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_mail_wp_mail
			}
		}
	} else {
		$response['message'] = __( 'An unknown error occurred.', 'username-changer' );
	}

	wp_send_json( $response );
}
add_action( 'wp_ajax_change_username', 'username_changer_ajax_username_change' );
