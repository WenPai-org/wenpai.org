<div class="sui-modal sui-modal-sm branda-welcome-step1">
	<div class="sui-modal-content" id="<?php echo esc_attr( $dialog_id ); ?>" aria-labelledby="<?php echo esc_attr( $dialog_id ) . '-title'; ?>" aria-describedby="<?php echo esc_attr( $dialog_id ) . '-description'; ?>" role="dialog" aria-modal="true">
		<div class="sui-box" role="document">
			<div class="sui-box-header sui-content-center sui-flatten">
				<button class="sui-button-icon sui-button-float--right" data-modal-close>
					<i class="sui-icon-close sui-md" aria-hidden="true"></i>
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this modal', 'ub' ); ?></span>
				</button>
				<h3 class="sui-box-title sui-lg" id="<?php echo esc_attr( $dialog_id ) . '-title'; ?>"><?php esc_html_e( 'Welcome to Branda', 'ub' ); ?></h3>
			</div>
			<div class="sui-box-body sui-content-center sui-flatten" id="<?php echo esc_attr( $dialog_id ) . '-description'; ?>">
				<p><?php esc_html_e( 'Let’s get you started by activating the modules you want to use. You can always activate/deactivate modules later.', 'ub' ); ?></p>
			</div>
			<div class="sui-box-footer branda-welcome-footer-step1 sui-content-center sui-flatten">
				<div class="sui-form-field sui-actions-center">
					<button class="sui-button sui-button-blue branda-welcome-all-modules" type="button" data-nonce="<?php echo wp_create_nonce( 'branda-welcome-all-modules' ); ?>"><?php echo esc_html_x( 'Activate Modules', 'button', 'ub' ); ?></button>
				</div>
			</div>
			<div class="sui-box-footer branda-welcome-footer-step1 branda-welcome-footer-close sui-content-center  sui-flatten">
				<div class="sui-form-field sui-actions-center">
					<a href="#" class="sui-modal-close" data-modal-close=""><?php echo esc_html_x( 'Skip for now', 'button', 'ub' ); ?></a>
				</div>
			</div>
			<div class="sui-box-footer branda-welcome-footer-step2 sui-content-center sui-flatten">
				<div class="sui-form-field sui-actions-center">
					<button type="button" class="sui-modal-close sui-button sui-button-ghost" data-modal-close=""><?php echo esc_html_x( 'Skip', 'button', 'ub' ); ?></button>
					<button class="sui-button sui-button-blue branda-welcome-activate" type="button" data-nonce="<?php echo wp_create_nonce( 'branda-welcome-activate' ); ?>"><?php echo esc_html_x( 'Activate', 'button', 'ub' ); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>
