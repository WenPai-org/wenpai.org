<div class="sui-modal sui-modal-alt sui-modal-sm branda-dialog-new">

	<div class="sui-modal-content" id="branda-permissions-add-user" aria-labelledby="branda-permissions-add-user-title" aria-describedby="branda-permissions-add-user-description" role="dialog" aria-modal="true">

		<div class="sui-box" role="document">

				<div class="sui-box-header  sui-content-center  sui-flatten">

					<button class="sui-button-icon sui-button-float--right" data-modal-close>
						<i class="sui-icon-close sui-md" aria-hidden="true"></i>
						<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this modal', 'ub' ); ?></span>
					</button>

					<h3 class="sui-box-title sui-lg" id="branda-permissions-add-user-title"><?php esc_html_e( 'Add User', 'ub' ); ?></h3>

				</div>

				<div class="sui-box-body sui-box-body-slim  sui-flatten">
					<?php $is_large = is_multisite() && wp_is_large_network( 'users' ); ?>
					<p id="branda-permissions-add-user-description" class="sui-description">
						<?php
						echo $is_large
							? esc_html__( 'Input the user login or email in the box to add. You can add as many users as you like.', 'ub' )
							: esc_html__( 'Type the username in the searchbox to add. You can add as many users as you like.', 'ub' );
						?>
							</p>

					<div class="sui-form-field">
						<label class="sui-label" for="searchuser"><?php echo $is_large ? esc_html__( 'User login or email', 'ub' ) : esc_html__( 'Search users', 'ub' ); ?></label>
						<div class="sui-control-with-icon">
							<?php if ( $is_large ) { ?>
								<input id="user_login" placeholder="<?php esc_html_e( 'User login or email', 'ub' ); ?>" class="sui-form-control" />
							<?php } else { ?>
								<select class="sui-select sui-form-control"
										id="searchuser"
										name="user"
										data-placeholder="<?php esc_html_e( 'Type Username', 'ub' ); ?>"
										data-hash="<?php echo esc_attr( wp_create_nonce( 'usersearch' ) ); ?>"
										data-language-searching="<?php esc_attr_e( 'Searching...', 'ub' ); ?>"
										data-language-noresults="<?php esc_attr_e( 'No results found', 'ub' ); ?>"
										data-language-error-load="<?php esc_attr_e( 'Searching...', 'ub' ); ?>"
								>
								</select>
							<?php } ?>
							<i class="sui-icon-profile-male" aria-hidden="true"></i>
						</div>
					</div>
				</div>

				<div class="sui-box-footer  sui-flatten sui-space-between">

					<a class="sui-button sui-button-ghost" data-modal-close><?php esc_html_e( 'Cancel', 'ub' ); ?></a>

					<button class="sui-button branda-permissions-add-user" data-nonce="<?php echo esc_attr( wp_create_nonce( 'add_user' ) ); ?>">
						<span class="sui-loading-text"><i class="sui-icon-check" aria-hidden="true"></i><?php esc_html_e( 'Add', 'ub' ); ?></span>
						<span class="sui-loading-text-adding"><i class="sui-icon-loader" aria-hidden="true"></i><?php esc_html_e( 'Adding', 'ub' ); ?></span>
					</button>

				</div>

		</div>

	</div>

</div>
