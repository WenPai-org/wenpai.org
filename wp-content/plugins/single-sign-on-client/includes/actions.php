<?php
/**
 * Plugin Actions
 *
 * Contains
 * - Login Hook: Catches the login and pass and does a user credential check for the login instead of the current WordPress user database.
 */
//add_action( 'wp_authenticate', 'onelogin_client_login_hook', 1 );
function onelogin_client_login_hook( $user ) {

	// Hook into the login past generically
	if ( ! empty( $_POST['log'] ) && ! empty( $_POST['pwd'] ) && isset( $_POST['wp-submit'] ) && $_POST['wp-submit'] == 'Log In' ) {

		/*
		 * Check if the user is a current site user or not
		 * @todo This may be an issue and we need to figure out how we are going to handle this properly
		 */
		//if ( username_exists( sanitize_text_field( $_POST['log'] ) ) ) {

		//}

		/*
		 * Get the One Client Configuration
		 */
		$options = get_option( "wposso_options" );

		// Start the One Login Flow here using the credentials of the user.
		$curl_post_data = array(
			'grant_type' => 'password',
			'username'   => sanitize_text_field( $_POST['log'] ),
			'password'   => $_POST['pwd'],
			//'client_id'     => $client_id,
			//'client_secret' => $client_secret
		);

		/*
		 * @todo Add the option to do header or URL request. Some servers have issues with allowing header authentication
		 * @todo Add option to allow insecure connections. This is be discouraged in production. Maybe say Production/Development in the settings instead of secure/insecure?
		 * @todo Add option to change or add a custom user agent.
		 */
		$curl = curl_init( $options['server_url'] . '?oauth=token' );
		//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt( $curl, CURLOPT_USERPWD, $options['client_id'] . ':' . $options['client_secret'] );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $curl_post_data );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5' );
		curl_setopt( $curl, CURLOPT_REFERER, site_url() );

		$curl_response = curl_exec( $curl );
		curl_close( $curl );

		$response = json_decode( $curl_response );

		//print_r( $response );

		// If the access token is not empty, the login was valid
		if ( ! empty( $response->access_token ) ) {
			$user_info_request = wp_remote_get( $options['server_url'] . '?oauth=me&access_token=' . $response->access_token, array(
				'timeout'     => 120,
				'httpversion' => '1.1',
				'sslverify'   => false
			) );

			$user_info = json_decode( wp_remote_retrieve_body( $user_info_request ) );

			$user_id = username_exists( $user_info->user_login );

			if ( ! $user_id && email_exists( $user_info->user_email ) == false ) {

				// Does not have an account... Register and then log the user in
				$random_password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
				$user_id         = wp_create_user( $user_info->user_login, $random_password, $user_info->user_email );

				if ( isset( $user_info->first_name ) ) {
					update_user_meta( $user_id, 'first_name', $user_info->first_name );
				}

				if ( isset( $user_info->last_name ) ) {
					update_user_meta( $user_id, 'last_name', $user_info->last_name );
				}

				do_action( 'onelogin_user_created', $user_info, 1 );

				wp_clear_auth_cookie();
				wp_set_current_user( $user_id );
				wp_set_auth_cookie( $user_id );

				if ( is_user_logged_in() ) {
					wp_safe_redirect( site_url() );
					exit;
				}

			} else {

				// Already Registered... Log the User In
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

				// User ID 1 is not allowed
				if ( '1' === $user->ID ) {
					wp_die( 'For security reasons, this user can not use Single Sign On' );
				}

				do_action( 'onelogin_user_login', $user_info, 1 );

				wp_clear_auth_cookie();
				wp_set_current_user( $user->ID );
				wp_set_auth_cookie( $user->ID );

				if ( is_user_logged_in() ) {
					wp_safe_redirect( site_url() );
					exit;
				}

			}
		}

		exit();
	}
}