<p class="ultimate-branding-password">
	<label for="password"><?php _e( 'Password', 'ub' ); ?>:</label>
<?php
if ( ! empty( $error ) ) {
	echo '<p class="error">' . $error . '</p>';
}
?>
	<input name="password_1" type="password" id="password_1" value="" autocomplete="off" maxlength="20" class="input"/><br />
	<span><?php _e( 'Leave fields blank for a random password to be generated.', 'ub' ); ?></span>
</p>
<p class="ultimate-branding-password">
	<label for="password"><?php _e( 'Confirm Password', 'ub' ); ?>:</label>
	<input name="password_2" type="password" id="password_2" value="" autocomplete="off" maxlength="20" class="input" /><br />
	<span><?php _e( 'Type your new password again.', 'ub' ); ?></span>
</p>
