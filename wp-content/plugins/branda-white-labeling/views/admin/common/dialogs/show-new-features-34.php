<div class="sui-modal sui-modal-md">

	<div class="sui-modal-content sui-content-fade-in branda-new-features" id="branda-show-new-feature" aria-labelledby="branda-show-new-feature-title" aria-describedby="branda-show-new-feature-description" role="dialog" aria-modal="true">

		<div class="sui-box">

				<div class="sui-box-header sui-content-center sui-flatten sui-spacing-top--60">
					<figure class="sui-box-banner" aria-hidden="true">
						<img src="<?php echo branda_url( 'assets/images/branda/smtp-log-feature.png' ); // WPCS: XSS ok. ?>"
							srcset="<?php echo branda_url( 'assets/images/branda/smtp-log-feature@2x.png' ); // WPCS: XSS ok. ?> 2x"
							alt="<?php esc_html_e( 'New features', 'ub' ); ?>" />
					</figure>

					<button class="sui-button-icon sui-button-float--right" data-modal-close>
						<i class="sui-icon-close sui-md" aria-hidden="true"></i>
						<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this modal', 'ub' ); ?></span>
					</button>

					<h3 class="sui-box-title sui-lg" id="branda-show-new-feature-title"><?php esc_html_e( 'New SMTP Log Feature and Role Permission Changes', 'ub' ); ?></h3>

					<p class="sui-description" id="branda-show-new-feature-description"><?php esc_html_e( 'A new SMTP log feature has been added to Branda 3.4 Pro, and the options for customizing admin menu role permissions have been improved.', 'ub' ); ?></p>

				</div>

				<div class="sui-box-body">

					<div class="sui-form-field">
						<ul class="branda-small-list">
							<li><span class="sui-settings-label sui-dark"><?php esc_html_e( 'SMTP Log', 'ub' ); ?></span></li>
						</ul>
						<p class="sui-description">
						<?php
							printf(
								esc_html__( 'With the %1$sSMTP log%2$s feature you will be able to gather detailed information about your emails. You can track recipients\' email histories and export the log history.', 'ub' ),
								'<a target="_blank" href="' . add_query_arg(
									array(
										'page'   => 'branding_group_emails',
										'module' => 'email-logs',
									),
									network_admin_url( 'admin.php' )
								) . '">',
								'</a>'
							);
							?>
							</p>
					</div>

					<div class="sui-form-field">
						<ul class="branda-small-list">
							<li><span class="sui-settings-label sui-dark"><?php esc_html_e( 'Role Permission Changes', 'ub' ); ?></span></li>
						</ul>
						<p class="sui-description"><?php esc_html_e( 'The logic for customizing the admin menu has been updated. Now, users can only change permissions for roles lower than their own. For example, users with the Administrator role can change permissions for all roles, while Editors can change the permissions for the Author, Contributor and Subscriber roles, but cannot change the permissions for Administrators.', 'ub' ); ?></p>
					</div>

				</div>

				<div class="sui-box-footer sui-flatten sui-content-center sui-spacing-bottom--50 branda-new-feature-got-it" data-dismiss-id="new-feature" data-nonce="<?php echo esc_attr( wp_create_nonce( 'new-feature' ) ); ?>">
					<button type="button" class="sui-button"><?php esc_html_e( 'Got it', 'ub' ); ?></button>
				</div>

		</div>

	</div>

</div>
