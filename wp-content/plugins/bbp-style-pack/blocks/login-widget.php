<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//where to show...
$check = true ;
if ( !empty( $attributes['bbpressOnly'] ) ) {
	//then set to false and make below turn to true
	$check = false ;
	//only show on bbpress pages or if in wdigets or page/post edits
	if (is_bbpress()) $check=true ;
	elseif (isset($_REQUEST['context'])&& $_REQUEST['context'] == 'edit') {
		$check = true ;
	}
}
//if check is false, then return ;
if (!$check) return ;

		echo '<div class="widget bsp-widget bsp-login-container">';

		do_action ('bsp_logon_widget_before_title') ;

		if ( ! empty( $attributes['title'] ) ) {
			echo '<span class="bsp-lw-title"><h3 class="widget-title bsp-widget-title">' .  $attributes['title']  . '</h3></span>' ;
		}
		
		do_action ('bsp_logon_widget_after_title') ;

		if ( ! is_user_logged_in() ) : ?>

			<form method="post" action="<?php bbp_wp_login_action( array( 'context' => 'login_post' ) ); ?>" class="bbp-login-form">
				<fieldset class="bbp-form">
					<legend><?php esc_html_e( 'Log In', 'bbpress' ); ?></legend>

					<div class="bbp-username">
						<label for="user_login"><?php esc_html_e( 'Username', 'bbpress' ); ?>: </label>
						<input type="text" name="log" value="<?php bbp_sanitize_val( 'user_login', 'text' ); ?>" size="20" maxlength="100" id="user_login" autocomplete="off" />
					</div>

					<div class="bbp-password">
						<label for="user_pass"><?php esc_html_e( 'Password', 'bbpress' ); ?>: </label>
						<input type="password" name="pwd" value="<?php bbp_sanitize_val( 'user_pass', 'password' ); ?>" size="20" id="user_pass" autocomplete="off" />
					</div>

					<div class="bbp-remember-me">
						<input type="checkbox" name="rememberme" value="forever" <?php checked( bbp_get_sanitize_val( 'rememberme', 'checkbox' ) ); ?> id="rememberme" />
						<label for="rememberme"><?php esc_html_e( 'Keep me signed in', 'bbpress' ); ?></label>
					</div>

					<?php do_action( 'login_form' ); ?>

					<div class="bbp-submit-wrapper">

						<button type="submit" name="user-submit" id="user-submit" class="button submit user-submit"><?php esc_html_e( 'Log In', 'bbpress' ); ?></button>

						<?php bbp_user_login_fields(); ?>

					</div>

					<?php if ( ! empty( $attributes['register'] ) || ! empty( $attributes['lostpass'] ) ) : ?>

						<div class="bbp-login-links">

							<?php if ( ! empty( $attributes['register'] ) ) : ?>

								<a href="<?php echo esc_url( $attributes['register'] ); ?>" title="<?php esc_attr_e( 'Register', 'bbpress' ); ?>" class="bbp-register-link"><?php esc_html_e( 'Register', 'bbpress' ); ?></a>

							<?php endif; ?>

							<?php if ( ! empty( $attributes['lostpass'] ) ) : ?>

								<a href="<?php echo esc_url( $attributes['lostpass'] ); ?>" title="<?php esc_attr_e( 'Lost Password', 'bbpress' ); ?>" class="bbp-lostpass-link"><?php esc_html_e( 'Lost Password', 'bbpress' ); ?></a>

							<?php endif; ?>

						</div>

					<?php endif; ?>

				</fieldset>
			</form>

		<?php else : ?>

			<div class="bbp-logged-in">
				<a href="<?php bbp_user_profile_url( bbp_get_current_user_id() ); ?>" class="submit user-submit"><?php echo get_avatar( bbp_get_current_user_id(), '40' ); ?></a>
				<h4><?php bbp_user_profile_link( bbp_get_current_user_id() ); ?></h4>

				<?php bbp_logout_link(); ?>
			</div>

		<?php endif;

		do_action ('bsp_logon_widget_after') ;
		?>
		</div>
		<?php
