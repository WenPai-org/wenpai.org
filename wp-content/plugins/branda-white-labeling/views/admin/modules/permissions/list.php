<div class="sui-border-frame">
	<div class="sui-description"><?php esc_html_e( 'Users who have access', 'ub' ); ?></div>
	<div class="sui-box-builder" id="branda-permissions-users">
		<div class="sui-box-builder-body">
			<div class="sui-box-builder-fields branda-admin-permissions-user-items">
	<?php
	if ( is_array( $items ) ) {
		foreach ( $items as $id => $args ) {
			$this->render( $template, $args );
		}
	}
	?>
			</div>
	<?php echo $button_plus; // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</div>
