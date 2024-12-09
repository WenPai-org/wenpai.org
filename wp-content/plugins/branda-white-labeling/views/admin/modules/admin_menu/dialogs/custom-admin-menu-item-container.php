<script type="text/html" id="tmpl-menu-item-container">
	<div class="sui-box-builder">
		<?php if ( ! empty( $no_menu_items ) ) { ?>
			<div class="sui-box-builder-body">
			<?php
			echo Branda_Helper::sui_notice( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				sprintf(
					esc_html__( 'If you don\'t see any admin menu item here, please visit your main site\'s %1$sdashboard page%2$s and come back on this page to customize the admin menu items.', 'ub' ),
					'<a href="' . esc_url( admin_url() ) . '" target="_blank">',
					'</a>'
				),
				'info'
			);
			?>
			</div>
		<?php } else { ?>
		<div class="sui-box-builder-header">
			<div class="sui-builder-options sui-options-inline">
				<label class="sui-checkbox sui-checkbox-sm">
					<input type="checkbox" class="branda-menu-select-all"/>
					<span aria-hidden="true"></span>
					<span><?php esc_html_e( 'Select All', 'ub' ); ?></span>
				</label>

				<span class="branda-custom-menu-bulk-controls">
					<button class="sui-button-icon sui-button-outlined sui-tooltip branda-custom-admin-menu-duplicate"
							data-tooltip="<?php esc_html_e( 'Duplicate', 'ub' ); ?>">

						<i class="sui-icon-copy" aria-hidden="true"></i>
						<span class="sui-screen-reader-text">
							<?php esc_html_e( 'Duplicate', 'ub' ); ?>
						</span>
					</button>

					<button class="sui-button-icon sui-button-outlined sui-tooltip branda-custom-admin-menu-make-invisible"
							data-tooltip="<?php esc_html_e( 'Hide but allow access', 'ub' ); ?>">

						<i class="sui-icon-eye-hide" aria-hidden="true"></i>
						<span class="sui-screen-reader-text">
							<?php esc_html_e( 'Hide but allow access', 'ub' ); ?>
						</span>
					</button>

					<button class="sui-button-icon sui-button-outlined sui-tooltip branda-custom-admin-menu-make-visible"
							data-tooltip="<?php esc_html_e( 'Unhide', 'ub' ); ?>">

						<i class="sui-icon-eye" aria-hidden="true"></i>
						<span class="sui-screen-reader-text">
							<?php esc_html_e( 'Unhide', 'ub' ); ?>
						</span>
					</button>

					<button class="sui-button-icon sui-button-outlined sui-tooltip branda-custom-admin-menu-hide"
							data-tooltip="<?php esc_html_e( 'Hide and disable access', 'ub' ); ?>">

						<i class="sui-icon-unlock" aria-hidden="true"></i>
						<span class="sui-screen-reader-text">
							<?php esc_html_e( 'Hide and disable access', 'ub' ); ?>
						</span>
					</button>

					<button class="sui-button-icon sui-button-outlined sui-tooltip branda-custom-admin-menu-unhide"
							data-tooltip="<?php esc_html_e( 'Unhide and enable access', 'ub' ); ?>">

						<i class="sui-icon-lock" aria-hidden="true"></i>
						<span class="sui-screen-reader-text">
							<?php esc_html_e( 'Unhide and enable access', 'ub' ); ?>
						</span>
					</button>
				</span>
			</div>
		</div>

		<div class="sui-box-builder-body">
			<div class="sui-builder-fields sui-accordion"></div>

			<button class="sui-button sui-button-dashed">
				<i class="sui-icon-plus" aria-hidden="true"></i>
				<?php esc_html_e( 'Add Item', 'ub' ); ?>
			</button>
		</div>
		<?php } ?>
	</div>
</script>
