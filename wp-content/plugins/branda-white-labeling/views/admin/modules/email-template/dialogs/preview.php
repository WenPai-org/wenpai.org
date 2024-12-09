<div class="sui-modal">
	<div class="sui-modal-content" id="<?php echo esc_attr( $id ); ?>" aria-labelledby="<?php echo esc_attr( $id ) . '-title'; ?>" role="dialog" aria-modal="true">
		<div class="sui-box" role="document">
			<div class="sui-box-header">
				<button class="sui-button-icon sui-button-float--right" data-modal-close>
					<i class="sui-icon-close sui-md" aria-hidden="true"></i>
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this modal', 'ub' ); ?></span>
				</button>
				<h3 class="sui-box-title" id="<?php echo esc_attr( $id ) . '-title'; ?>"><?php esc_html_e( 'Preview', 'ub' ); ?></h3>
			</div>
			<div class="sui-box-body"></div>
			<div class="sui-box-footer">
				<div class="sui-actions-right">
					<button class="sui-button" data-modal-close><?php esc_html_e( 'Close', 'ub' ); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>
