<div class="sui-builder-field" data-nonce="<?php echo esc_attr( $nonce ); ?>" data-id="<?php echo esc_attr( $id ); ?>">
	<div class="sui-builder-field-label">
	<?php
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		printf(
			'<div class="branda-field-image" aria-hidden="true"><img class="branda-circle-image" src="%s">
														</div>',
			$avatar
		);
		?>
	<span class="branda-permissions-user-title"><?php echo esc_html( $title ); ?></span>
	<span><?php echo esc_html( $email ); ?></span>
</div>
	<?php if ( get_current_user_id() === $id ) { ?>
		<span class="sui-tag"><?php esc_html_e( 'You', 'ub' ); ?></span>
	<?php } else { ?>
		<div class="sui-button-icon branda-button-hover-red sui-tooltip sui-tooltip-top branda-permissions-delete" data-tooltip="<?php esc_html_e( 'Remove access', 'ub' ); ?>">
				<span class="sui-loading-text">
					<i class="sui-icon-trash" aria-hidden="true"></i>
				</span>
				<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
			<span class="sui-screen-reader-text"><?php esc_html_e( 'Remove access', 'ub' ); ?></span>
		</div>
	<?php } ?>
</div>
