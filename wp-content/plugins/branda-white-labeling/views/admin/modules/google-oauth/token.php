<?php
/**
 * Google OAuth token section.
 *
 * @package    Branda
 * @subpackage Emails
 */

if ( isset( $auth_url ) ) : ?>

	<a href="<?php echo esc_url( $auth_url ); ?>" class="sui-button">
		<span class="sui-icon-key" aria-hidden="true"></span>
		<?php esc_html_e( 'Get New Token', 'ub' ); ?>
	</a>

<?php endif; ?>
