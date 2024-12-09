<div class="sui-box-body sui-message sui-message-lg">
	<img
		src="<?php echo esc_html( branda_url( 'assets/images/branda/upgrade.png' ) ); ?>"
		srcset="<?php echo esc_html( branda_url( 'assets/images/branda/upgrade.png' ) ); ?>"
		class="sui-image"
		aria-hidden="true"
	/>
	<p><?php echo esc_html( $description ); ?></p>
	<a href="https://wpmudev.com/project/ultimate-branding/?utm_source=branda&utm_medium=plugin&utm_campaign=<?php echo esc_attr( $utm_campaign ); ?>" class="sui-button sui-button-purple" target="_blank">
		<?php esc_html_e( 'Upgrade', 'ub' ); ?>
	</a>
</div>
