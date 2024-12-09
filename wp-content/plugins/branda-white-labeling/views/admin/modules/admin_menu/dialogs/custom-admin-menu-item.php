<?php
$is_main_item = empty( $is_main_item ) ? false : $is_main_item;
$settings_tab_content = $this->menu_item_settings();
?>
<script type="text/html" id="tmpl-<?php echo $is_main_item ? 'menu' : 'submenu'; ?>-builder-field">
	<div class="sui-builder-field sui-accordion-item sui-can-move <# if(data.is_invisible) { #>branda-menu-item-invisible<# } #> <# if(data.is_hidden) { #>branda-menu-item-hidden<# } #> <# if(!data.is_native) { #>branda-menu-item-non-native<# } #>"
	     data-slug="{{ data.slug }}">

		<div class="sui-accordion-item-header sui-can-move">

			<i class="sui-icon-drag" aria-hidden="true"></i>

			<div class="sui-builder-field-label">
				<label class="sui-checkbox">
					<input type="checkbox" class="branda-custom-admin-menu-is-selected"/>
					<span aria-hidden="true"></span>
				</label>

				<span class="branda-menu-item-icon-container"></span>
				<span class="branda-menu-item-title">{{ data.title || data.title_default }}</span>
			</div>

			<div class="branda-custom-admin-menu-controls">
				<button class="sui-button-icon sui-dropdown-anchor">
					<i class="sui-icon-more" aria-hidden="true"></i>
				</button>

				<ul>
					<li>
						<a class="sui-button-icon sui-hover-show sui-tooltip branda-custom-admin-menu-remove"
						   data-tooltip="<?php esc_html_e( 'Delete', 'ub' ); ?>">

							<i class="sui-icon-trash" aria-hidden="true"></i>
							<span class="branda-custom-admin-menu-button-text">
								<?php esc_html_e( 'Delete', 'ub' ); ?>
							</span>
						</a>
					</li>

					<li>
						<a class="sui-button-icon sui-hover-show sui-tooltip branda-custom-admin-menu-undo"
						   data-tooltip="<?php esc_html_e( 'Undo item changes', 'ub' ); ?>">

							<i class="sui-icon-undo" aria-hidden="true"></i>
							<span class="branda-custom-admin-menu-button-text">
								<?php esc_html_e( 'Undo item changes', 'ub' ); ?>
							</span>
						</a>
					</li>

					<li>
						<a class="sui-button-icon sui-hover-show sui-tooltip branda-custom-admin-menu-duplicate"
						   data-tooltip="<?php esc_html_e( 'Duplicate', 'ub' ); ?>">

							<i class="sui-icon-copy" aria-hidden="true"></i>
							<span class="branda-custom-admin-menu-button-text">
								<?php esc_html_e( 'Duplicate', 'ub' ); ?>
							</span>
						</a>
					</li>

					<# if(!(data.slug || '').startsWith('branding')) { #>
					<li>
						<label class="branda-custom-admin-menu-is-invisible">
							<input type="checkbox" name="is_invisible" <# if(data.is_invisible) { #>checked<# } #> />

							<a class="sui-button-icon sui-hover-show sui-tooltip branda-custom-admin-menu-make-invisible"
							   data-tooltip="<?php esc_html_e( 'Hide but allow access', 'ub' ); ?>">

								<i class="sui-icon-eye-hide" aria-hidden="true"></i>
								<span class="branda-custom-admin-menu-button-text">
									<?php esc_html_e( 'Hide but allow access', 'ub' ); ?>
								</span>
							</a>

							<a class="sui-button-icon sui-hover-show sui-tooltip branda-custom-admin-menu-make-visible"
							   data-tooltip="<?php esc_html_e( 'Unhide', 'ub' ); ?>">

								<i class="sui-icon-eye" aria-hidden="true"></i>
								<span class="branda-custom-admin-menu-button-text">
									<?php esc_html_e( 'Unhide', 'ub' ); ?>
								</span>
							</a>
						</label>
					</li>

					<li>
						<label class="branda-custom-admin-menu-is-hidden">
							<input type="checkbox" name="is_hidden" <# if(data.is_hidden) { #>checked<# } #> />

							<a class="sui-button-icon sui-hover-show sui-tooltip branda-custom-admin-menu-hide"
							   data-tooltip="<?php esc_html_e( 'Hide and disable access', 'ub' ); ?>">

								<i class="sui-icon-unlock" aria-hidden="true"></i>
								<span class="branda-custom-admin-menu-button-text">
									<?php esc_html_e( 'Hide and disable access', 'ub' ); ?>
								</span>
							</a>

							<a class="sui-button-icon sui-hover-show sui-tooltip branda-custom-admin-menu-unhide"
							   data-tooltip="<?php esc_html_e( 'Unhide and enable access', 'ub' ); ?>">

								<i class="sui-icon-lock" aria-hidden="true"></i>
								<span class="branda-custom-admin-menu-button-text">
									<?php esc_html_e( 'Unhide and enable access', 'ub' ); ?>
								</span>
							</a>
						</label>
					</li>
					<# } #>
				</ul>
			</div>

			<span class="sui-builder-field-border" aria-hidden="true"></span>

			<button class="sui-button-icon sui-accordion-open-indicator <?php echo $is_main_item ? 'sui-tooltip' : ''; ?>"
				<?php if ( $is_main_item ) : ?>
					data-tooltip="<?php esc_attr_e( 'Manage sub-menu', 'ub' ); ?>"
				<?php endif; ?>
			>
				<i class="sui-icon-chevron-down" aria-hidden="true"></i>
				<span class="sui-screen-reader-text">
					<?php esc_html_e( 'Manage sub-menu', 'ub' ); ?>
				</span>
			</button>

		</div>

		<div class="sui-accordion-item-body sui-box-body">

			<?php if ( ! $is_main_item ) : ?>
				<div class="branda-menu-item-settings-container">
					<?php echo $settings_tab_content; ?>
				</div>
			<?php else : ?>
				<div class="sui-tabs sui-tabs-flushed">
					<div data-tabs>
						<div class="active"><?php esc_html_e( 'Settings', 'ub' ); ?></div>
						<div><?php esc_html_e( 'Submenu', 'ub' ); ?></div>
					</div>

					<div data-panes>
						<div class="active">
							<div class="branda-menu-item-settings-container">
								<?php echo $settings_tab_content; ?>
							</div>
						</div>

						<div class="branda-submenu-container"></div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</script>
