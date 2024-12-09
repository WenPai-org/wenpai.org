<div class="sui-modal sui-modal-sm branda-dialog-reset-module">
	<div class="sui-modal-content" id="branda-dialog-reset-module-<?php echo esc_attr( $module ); ?>" aria-labelledby="branda-dialog-reset-module-<?php echo esc_attr( $module ) . '-title'; ?>" aria-describedby="<?php echo esc_attr( $module ) . '-description'; ?>" role="dialog" aria-modal="true">
		<div class="sui-box" role="document">
			<div class="sui-box-header sui-flatten sui-content-center">
				<button class="sui-button-icon sui-button-float--right" data-modal-close>
					<i class="sui-icon-close sui-md" aria-hidden="true"></i>
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this modal', 'ub' ); ?></span>
				</button>
				<h3 id="<?php echo esc_attr( $module ) . '-title'; ?>" class="sui-box-title sui-lg"><?php esc_html_e( 'Reset Module', 'ub' ); ?></h3>
			</div>
			<div class="sui-box-body sui-flatten sui-content-center">
				<p id="branda-dialog-reset-module-<?php echo esc_attr( $module ) . '-title'; ?>">
															 <?php
																printf(
																	esc_html__( 'Are you sure you wish to reset %s module to default state?', 'ub' ),
																	sprintf( '<b>%s</b>', esc_html( $title ) )
																);

																?>
				</p>
			</div>
			<div class="sui-box-footer sui-content-center sui-flatten">
				<div class="sui-form-field sui-actions-center">
					<button type="button" class="sui-modal-close sui-button sui-button-ghost" data-modal-close><?php echo esc_html_x( 'Cancel', 'button', 'ub' ); ?></button>
					<button
						type="button"
						class="sui-button sui-button-ghost sui-button-red sui-button-icon branda-reset"
						data-module="<?php echo esc_attr( $module ); ?>"
						data-nonce="<?php echo esc_attr( $nonce ); ?>"
					>
						<i class="sui-icon-undo" aria-hidden="true"></i><?php echo esc_html_x( 'Reset', 'button', 'ub' ); ?>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
