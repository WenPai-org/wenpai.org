<?php
// Notice is SMTP module is disabled.
$notice = static::maybe_add_smtp_notice();
$hide_branding = apply_filters( 'wpmudev_branding_hide_branding', false );
$branding_image = apply_filters( 'wpmudev_branding_hero_image', null );
?>
<div class="sui-box-body sui-message sui-message-lg">
	<?php if ( ! empty( $branding_image ) ): ?>
		<img src="<?php echo esc_html( $branding_image ); ?>"
		     class="sui-image"
		     aria-hidden="true"
		/>
	<?php elseif ( ! $hide_branding ): ?>
		<img src="<?php echo esc_html( branda_url( 'assets/images/branda/empty-office-tray.png' ) ); ?>"
		     srcset="<?php echo esc_html( branda_url( 'assets/images/branda/empty-office-tray.png' ) ); ?>"
		     class="sui-image"
		     aria-hidden="true"
		/>
	<?php endif; ?>
	<h2><?php esc_html_e( 'No log history yet!', 'ub' ); ?></h2>
	<?php if ( $notice ) { ?>
		<div style="text-align: left;">
			<?php echo $notice; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	<?php } else { ?>
		<p><?php esc_html_e( 'You don’t have any logs yet. When you do, you’ll be able to view all the logs here.', 'ub' ); ?></p>
	<?php } ?>
</div>
