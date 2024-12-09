<?php
$dialog_id    = empty( $dialog_id ) ? '' : $dialog_id;
$search_nonce = empty( $search_nonce ) ? '' : $search_nonce;
?>

<div class="sui-modal sui-modal-lg">

	<div class="sui-modal-content"
		id="<?php echo esc_attr( $dialog_id ); ?>"
		aria-modal="true"
		aria-labelledby="<?php echo esc_attr( $dialog_id ) . '-title'; ?>"
		role="dialog">

		<div class="sui-box branda-custom-admin-menu-dialog" role="document">
			<div class="sui-box-header">
				<button class="sui-button-icon sui-button-float--right" data-modal-close>
					<i class="sui-icon-close sui-md" aria-hidden="true"></i>
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this modal', 'ub' ); ?></span>
				</button>
				<h3 class="sui-box-title" id="<?php echo esc_attr( $dialog_id ) . '-title'; ?>">
					<?php esc_html_e( 'Custom admin menu', 'ub' ); ?>
				</h3>
			</div>

			<div class="sui-box-body">
				<div class="sui-side-tabs branda-admin-menu-main-tabs">
					<div class="sui-tabs-menu">
						<label class="sui-tab-item active">
							<input type="radio" data-tab-menu="configure"><?php esc_html_e( 'Configure menu items', 'ub' ); ?></label>
						<label class="sui-tab-item">
							<input type="radio" data-tab-menu="settings"><?php esc_html_e( 'Settings', 'ub' ); ?></label>
					</div>
					<div class="sui-tabs-content">
						<div class="sui-tab-boxed active" data-tab-content="configure">
							<p><?php esc_html_e( "Customize the admin menu by user role or separately for custom users. You can change the order of menu items, hide items you don't want and add new items as needed.", 'ub' ); ?></p>

							<div class="sui-row">
								<div class="sui-col-md-12">
									<label>
										<select class="sui-select"
												id="branda-admin-menu-role-user-switch"
												data-minimum-results-for-search="-1">
											<option value="-" <?php checked( true ); ?>>
												<?php echo esc_html( 'Choose the customization option' ); ?>
											</option>
											<option value="roles">
												<?php echo esc_html( 'User Roles' ); ?>
											</option>
											<option value="users">
												<?php echo esc_html( 'Custom Users' ); ?>
											</option>
										</select>
									</label>
								</div>
							</div>

							<?php
							$this->render( 'admin/modules/admin_menu/dialogs/custom-admin-menu-roles', array() );
							$this->render(
								'admin/modules/admin_menu/dialogs/custom-admin-menu-users',
								array(
									'search_nonce' => $search_nonce,
								)
							);
							?>
						</div>
						<div class="sui-tab-boxed" data-tab-content="settings">
							<div class="sui-row">
								<div class="sui-col-md-12">
									<?php
										$message = sprintf(
											esc_html__( 'Take your custom admin menu one step further by controlling what your users can and can\'t do using the %1$sUser Role Editor%2$s plugin.', 'ub' ),
											'<a href="https://wordpress.org/plugins/user-role-editor/" target="_blank">',
											'</a>'
										);
										echo Branda_Helper::sui_notice( $message, 'info' );
										?>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
