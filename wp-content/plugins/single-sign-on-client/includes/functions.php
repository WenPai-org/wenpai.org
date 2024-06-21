<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Auto SSO for users that are not logged in.
 */
add_filter( 'template_redirect', 'auto_sso_init', 11 );
function auto_sso_init( $template ) {

	// If the user is not logged in, load in the SSO client in a child IFrame instead of redirect
	if ( ! is_user_logged_in() ) {
		$options = get_option( "wposso_options" );
		if ( isset( $options["auto_sso"] ) && $options["auto_sso"] == 1 ) {

			/*
			 * @todo Build options instead of just a last query. This will be able to handle the pass of the iframe
			 */
			global $wp;
			$last_page = home_url( $wp->request );

			$params   = array(
				'oauth'         => 'authorize',
				'response_type' => 'code',
				'client_id'     => $options['client_id'],
				'client_secret' => $options['client_secret'],
				'redirect_uri'  => site_url( '?auth=sso' ),
				'state'         => urlencode( $last_page ),
			);
			$redirect = add_query_arg( $params, $options['server_url'] );

			wp_redirect( $redirect );
			exit;
		}
	}
}

function wp_sso_get_option( $option_name ) {
	$options = get_option( 'wposso_options' );
	if ( ! $options ) {
		return false;
	}
	if ( ! empty( $v = $options[ $option_name ] ) ) {
        return $v;
	}
}

/**
 * Main Functions
 *
 * @author Justin Greer <justin@justin-greer.com>
 * @package WP Single Sign On Client
 */

/**
 * Function wp_sso_login_form_button
 *
 * Add login button for SSO on the login form.
 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/login_form
 */
function wp_sso_login_form_button() {
	?>
    <div id="sso">
        <a style="width:100%; text-align:center;"
           class="button button-primary button-large"
           href="<?php echo site_url( '?auth=sso' ); ?>">使用 Weixiaoduo.com 账号登录</a>
        <br/>
        <span class="text-in-middle">或</span>
    </div>
    <script>
        document.getElementById('loginform').insertAdjacentElement('afterbegin', document.querySelector('#sso'));
    </script>
    <style>
        #sso {
            margin-bottom: 1em;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            height: 80px;
        }

        .text-in-middle {
            background-color: white;
            padding: 10px;
        }
    </style>
	<?php
}

add_action( 'login_form', 'wp_sso_login_form_button' );

/**
 * Login Button Shortcode
 *
 * @param  [type] $atts [description]
 *
 * @return [type]       [description]
 */
function single_sign_on_login_button_shortcode( $atts ) {
	$a = shortcode_atts( array(
		'type'   => 'primary',
		'title'  => 'Login using Single Sign On',
		'class'  => 'sso-button',
		'target' => '_blank',
		'text'   => 'Single Sign On'
	), $atts );

	return '<a class="' . $a['class'] . '" href="' . site_url( '?auth=sso' ) . '" title="' . $a['title'] . '" target="' . $a['target'] . '">' . $a['text'] . '</a>';
}

add_shortcode( 'sso_button', 'single_sign_on_login_button_shortcode' );

/**
 * Get user login redirect. Just in case the user wants to redirect the user to a new url.
 */
function wpssoc_get_user_redirect_url() {
	$options           = get_option( 'wposso_options' );
    if ( ! $options ) {
        return site_url();
    }
	$user_redirect_set = $options['redirect_to_dashboard'] == '1' ? get_dashboard_url() : site_url();
	$user_redirect     = apply_filters( 'wpssoc_user_redirect_url', $user_redirect_set );

	return $user_redirect;
}

/**
 * Add message to the login/out page with login only
 */
function wpo_login_only_message( $message ) {
	if ( empty( $message ) && isset( $_GET['wpo_login_only'] ) ) {
		return '<p><strong>' . apply_filters( 'wpo_login_only_msg', 'You need to have an account to use Single Sign On' ) . '</strong></p>';
	} else {
		return $message;
	}
}

add_filter( 'login_message', 'wpo_login_only_message' );
