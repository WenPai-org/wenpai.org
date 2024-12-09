<div class="sui-modal sui-modal-lg">
	<div class="sui-modal-content" id="<?php echo esc_attr( $dialog_id ); ?>" aria-labelledby="<?php echo esc_attr( $dialog_id ) . '-title'; ?>" role="dialog" aria-modal="true">
		<div class="sui-box" role="document">
			<div class="sui-box-header">
				<button class="sui-button-icon sui-button-float--right" data-modal-close>
					<i class="sui-icon-close sui-md" aria-hidden="true"></i>
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this modal', 'ub' ); ?></span>
				</button>
				<h3 class="sui-box-title" id="<?php echo esc_attr( $dialog_id ) . '-title'; ?>"><?php esc_html_e( 'Edit Color Scheme', 'ub' ); ?></h3>
			</div>
			<div class="sui-box-body">
				<div class="sui-form-field branda-border-bottom">
					<label for="branda-color-scheme-name" class="sui-settings-label"><?php esc_attr_e( 'Name', 'ub' ); ?></label>
					<span class="sui-description"><?php esc_html_e( 'Choose a name for this custom color scheme.', 'ub' ); ?></span>
					<input id="branda-color-scheme-name" type="text" name="branda[scheme_name]" class="sui-form-control" required="required" value="<?php echo esc_attr( $scheme_name ); ?>" />
					<span class="hidden"><?php esc_html_e( 'Scheme name can not be empty!', 'ub' ); ?></span>
				</div>
				<div class="sui-form-field branda-accordion-below">
					<label class="sui-settings-label"><?php esc_attr_e( 'Colors', 'ub' ); ?></label>
					<span class="sui-description"><?php esc_html_e( 'Adjust the default colour combinations as per your need.', 'ub' ); ?></span>
				</div>
				<div class="sui-accordion ">
					<div class="sui-accordion-item">
						<div class="sui-accordion-item-header">
							<div class="sui-accordion-item-title"><?php esc_html_e( 'General', 'ub' ); ?></div>
							<div class="sui-accordion-col-auto">
								<button type="button" class="sui-button-icon sui-accordion-open-indicator" aria-label="<?php esc_html_e( 'Open item', 'ub' ); ?>"><i class="sui-icon-chevron-down" aria-hidden="true"></i></button>
							</div>
						</div>
						<div class="sui-accordion-item-body">
							<div class="sui-box">
								<div class="sui-box-body">
									<div class="sui-form-field">
										<label class="sui-label"><?php esc_html_e( 'Background', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $general_background,
		'name'  => 'branda[general_background]',
	)
);
?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="sui-accordion-item">
						<div class="sui-accordion-item-header">
							<div class="sui-accordion-item-title"><?php esc_html_e( 'Links', 'ub' ); ?></div>
							<div class="sui-accordion-col-auto">
								<button type="button" class="sui-button-icon sui-accordion-open-indicator" aria-label="<?php esc_html_e( 'Open item', 'ub' ); ?>"><i class="sui-icon-chevron-down" aria-hidden="true"></i></button>
							</div>
						</div>
						<div class="sui-accordion-item-body">
							<div class="sui-box">
								<div class="sui-box-body">
									<div class="sui-tabs sui-tabs-flushed">
										<div data-tabs="">
											<div class="active"><?php esc_html_e( 'Static', 'ub' ); ?></div>
											<div class=""><?php esc_html_e( 'Hover', 'ub' ); ?></div>
										</div>
										<div data-panes="">
											<div class="active">
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Default link', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $links_static_default,
		'name'  => 'branda[links_static_default]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Delete / Trash / Spam link', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $links_static_delete,
		'name'  => 'branda[links_static_delete]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Inactive plugin link', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $links_static_inactive,
		'name'  => 'branda[links_static_inactive]',
	)
);
?>
												</div>
											</div>
											<div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Default link', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $links_static_default_hover,
		'name'  => 'branda[links_static_default_hover]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Delete / Trash / Spam link', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $links_static_delete_hover,
		'name'  => 'branda[links_static_delete_hover]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Inactive plugin link', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $links_static_inactive_hover,
		'name'  => 'branda[links_static_inactive_hover]',
	)
);
?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="sui-accordion-item">
						<div class="sui-accordion-item-header">
							<div class="sui-accordion-item-title"><?php esc_html_e( 'Forms', 'ub' ); ?></div>
							<div class="sui-accordion-col-auto">
								<button type="button" class="sui-button-icon sui-accordion-open-indicator" aria-label="<?php esc_html_e( 'Open item', 'ub' ); ?>"><i class="sui-icon-chevron-down" aria-hidden="true"></i></button>
							</div>
						</div>
						<div class="sui-accordion-item-body">
							<div class="sui-box">
								<div class="sui-box-body">
									<div class="sui-form-field">
										<label class="sui-label"><?php esc_html_e( 'Checkbox / Radio Button￼', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $form_checkbox,
		'name'  => 'branda[form_checkbox]',
	)
);
?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="sui-accordion-item">
						<div class="sui-accordion-item-header">
							<div class="sui-accordion-item-title"><?php esc_html_e( 'Core UI', 'ub' ); ?></div>
							<div class="sui-accordion-col-auto">
								<button type="button" class="sui-button-icon sui-accordion-open-indicator" aria-label="<?php esc_html_e( 'Open item', 'ub' ); ?>"><i class="sui-icon-chevron-down" aria-hidden="true"></i></button>
							</div>
						</div>
						<div class="sui-accordion-item-body">
							<div class="sui-box">
								<div class="sui-box-body">
									<div class="sui-tabs sui-tabs-flushed">
										<div data-tabs="">
											<div class="active"><?php esc_html_e( 'Static', 'ub' ); ?></div>
											<div class=""><?php esc_html_e( 'Hover', 'ub' ); ?></div>
										</div>
										<div data-panes="">
											<div class="active">
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Primary Button', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $core_ui_primary_button_background,
		'name'  => 'branda[core_ui_primary_button_background]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Primary Button Text', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $core_ui_primary_button_color,
		'name'  => 'branda[core_ui_primary_button_color]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Primary Button Text Shadow', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $core_ui_primary_button_shadow_color,
		'name'  => 'branda[core_ui_primary_button_shadow_color]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Disabled Button', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $core_ui_disabled_button_background,
		'name'  => 'branda[core_ui_disabled_button_background]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Disabled Button Text', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $core_ui_disabled_button_color,
		'name'  => 'branda[core_ui_disabled_button_color]',
	)
);
?>
												</div>
											</div>
											<div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Primary Button', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $core_ui_primary_button_background_hover,
		'name'  => 'branda[core_ui_primary_button_background_hover]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Primary Button Text', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $core_ui_primary_button_color_hover,
		'name'  => 'branda[core_ui_primary_button_color_hover]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Primary Button Text Shadow', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $core_ui_primary_button_shadow_color_hover,
		'name'  => 'branda[core_ui_primary_button_shadow_color_hover]',
	)
);
?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="sui-accordion-item">
						<div class="sui-accordion-item-header">
							<div class="sui-accordion-item-title"><?php esc_html_e( 'List Tables', 'ub' ); ?></div>
							<div class="sui-accordion-col-auto">
								<button type="button" class="sui-button-icon sui-accordion-open-indicator" aria-label="<?php esc_html_e( 'Open item', 'ub' ); ?>"><i class="sui-icon-chevron-down" aria-hidden="true"></i></button>
							</div>
						</div>
						<div class="sui-accordion-item-body">
							<div class="sui-box">
								<div class="sui-box-body">
									<div class="sui-tabs sui-tabs-flushed">
										<div data-tabs="">
											<div class="active"><?php esc_html_e( 'Static', 'ub' ); ?></div>
											<div class=""><?php esc_html_e( 'Hover', 'ub' ); ?></div>
										</div>
										<div data-panes="">
											<div class="active">
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'View Switch Icon', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $list_tables_switch_icon,
		'name'  => 'branda[list_tables_switch_icon]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Post Comment Count', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $list_tables_post_comment_count,
		'name'  => 'branda[list_tables_post_comment_count]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Alternate Row', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $list_tables_alternate_row,
		'name'  => 'branda[list_tables_alternate_row]',
	)
);
?>
												</div>
											</div>
											<div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'View Switch Icon', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $list_tables_switch_icon_hover,
		'name'  => 'branda[list_tables_switch_icon_hover]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Post Comment Count', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $list_tables_post_comment_count_hover,
		'name'  => 'branda[list_tables_post_comment_count_hover]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Pagination / Button / Icon', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $list_tables_pagination_hover,
		'name'  => 'branda[list_tables_pagination_hover]',
	)
);
?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="sui-accordion-item">
						<div class="sui-accordion-item-header">
							<div class="sui-accordion-item-title"><?php esc_html_e( 'Admin Menu', 'ub' ); ?></div>
							<div class="sui-accordion-col-auto">
								<button type="button" class="sui-button-icon sui-accordion-open-indicator" aria-label="<?php esc_html_e( 'Open item', 'ub' ); ?>"><i class="sui-icon-chevron-down" aria-hidden="true"></i></button>
							</div>
						</div>
						<div class="sui-accordion-item-body">
							<div class="sui-box">
								<div class="sui-box-body">
									<div class="sui-tabs sui-tabs-flushed">
										<div data-tabs="">
											<div class="active"><?php esc_html_e( 'Static', 'ub' ); ?></div>
											<div class=""><?php esc_html_e( 'Hover', 'ub' ); ?></div>
											<div class=""><?php esc_html_e( 'Current', 'ub' ); ?></div>
											<div class=""><?php esc_html_e( 'Current Hover', 'ub' ); ?></div>
											<div class=""><?php esc_html_e( 'Focus', 'ub' ); ?></div>
										</div>
										<div data-panes="">
											<div class="active">
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Link', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_menu_color,
		'name'  => 'branda[admin_menu_color]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Link Background', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_menu_background,
		'name'  => 'branda[admin_menu_background]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Icon', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_menu_icon_color,
		'name'  => 'branda[admin_menu_icon_color]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Submenu Link', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_menu_submenu_link,
		'name'  => 'branda[admin_menu_submenu_link]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Submenu Link Background', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_menu_submenu_background,
		'name'  => 'branda[admin_menu_submenu_background]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Bubble', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_menu_bubble_color,
		'name'  => 'branda[admin_menu_bubble_color]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Bubble Background', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_menu_bubble_background,
		'name'  => 'branda[admin_menu_bubble_background]',
	)
);
?>
												</div>
											</div>
											<div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Link', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_menu_color_hover,
		'name'  => 'branda[admin_menu_color_hover]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Link Background', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_menu_background_hover,
		'name'  => 'branda[admin_menu_background_hover]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Submenu Link', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_menu_submenu_link_hover,
		'name'  => 'branda[admin_menu_submenu_link_hover]',
	)
);
?>
												</div>
											</div>
											<div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Link', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_menu_color_current,
		'name'  => 'branda[admin_menu_color_current]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Link Background', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_menu_background_curent,
		'name'  => 'branda[admin_menu_background_curent]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Icon', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_menu_icon_color_current,
		'name'  => 'branda[admin_menu_icon_color_current]',
	)
);
?>
												</div>
											</div>
											<div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Link', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_menu_color_current_hover,
		'name'  => 'branda[admin_menu_color_current_hover]',
	)
);
?>
												</div>
											</div>
											<div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Icon', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_menu_icon_color_focus,
		'name'  => 'branda[admin_menu_icon_color_focus]',
	)
);
?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="sui-accordion-item">
						<div class="sui-accordion-item-header">
							<div class="sui-accordion-item-title"><?php esc_html_e( 'Admin Bar', 'ub' ); ?></div>
							<div class="sui-accordion-col-auto">
								<button type="button" class="sui-button-icon sui-accordion-open-indicator" aria-label="<?php esc_html_e( 'Open item', 'ub' ); ?>"><i class="sui-icon-chevron-down" aria-hidden="true"></i></button>
							</div>
						</div>
						<div class="sui-accordion-item-body">
							<div class="sui-box">
								<div class="sui-box-body">
									<div class="sui-tabs sui-tabs-flushed">
										<div data-tabs="">
											<div class="active"><?php esc_html_e( 'Static', 'ub' ); ?></div>
											<div class=""><?php esc_html_e( 'Hover', 'ub' ); ?></div>
											<div class=""><?php esc_html_e( 'Focus', 'ub' ); ?></div>
										</div>
										<div data-panes="">
											<div class="active">
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Background', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_bar_background,
		'name'  => 'branda[admin_bar_background]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Color', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_bar_color,
		'name'  => 'branda[admin_bar_color]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Icon', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_bar_icon_color,
		'name'  => 'branda[admin_bar_icon_color]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Submenu Icon and Links', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_bar_submenu_icon_color,
		'name'  => 'branda[admin_bar_submenu_icon_color]',
	)
);
?>
												</div>
											</div>
											<div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Item Background', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_bar_item_background_hover,
		'name'  => 'branda[admin_bar_item_background_hover]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Item Color', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_bar_item_color_hover,
		'name'  => 'branda[admin_bar_item_color_hover]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Submenu Icon and Links', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_bar_submenu_icon_color_hover,
		'name'  => 'branda[admin_bar_submenu_icon_color_hover]',
	)
);
?>
												</div>
											</div>
											<div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Item Background', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_bar_item_background_focus,
		'name'  => 'branda[admin_bar_item_background_focus]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Item Color', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_bar_item_color_focus,
		'name'  => 'branda[admin_bar_item_color_focus]',
	)
);
?>
												</div>
												<div class="sui-form-field">
													<label class="sui-label"><?php esc_html_e( 'Submenu Icon and Links', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_bar_submenu_icon_color_focus,
		'name'  => 'branda[admin_bar_submenu_icon_color_focus]',
	)
);
?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="sui-accordion-item">
						<div class="sui-accordion-item-header">
							<div class="sui-accordion-item-title"><?php esc_html_e( 'Media Uploader', 'ub' ); ?></div>
							<div class="sui-accordion-col-auto">
								<button type="button" class="sui-button-icon sui-accordion-open-indicator" aria-label="<?php esc_html_e( 'Open item', 'ub' ); ?>"><i class="sui-icon-chevron-down" aria-hidden="true"></i></button>
							</div>
						</div>
						<div class="sui-accordion-item-body">
							<div class="sui-box">
								<div class="sui-box-body">
									<div class="sui-form-field">
										<label class="sui-label"><?php esc_html_e( 'Progress Bar￼', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_media_progress_bar_color,
		'name'  => 'branda[admin_media_progress_bar_color]',
	)
);
?>
									</div>
									<div class="sui-form-field">
										<label class="sui-label"><?php esc_html_e( 'Selected Attachment￼', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_media_selected_attachment_color,
		'name'  => 'branda[admin_media_selected_attachment_color]',
	)
);
?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="sui-accordion-item">
						<div class="sui-accordion-item-header">
							<div class="sui-accordion-item-title"><?php esc_html_e( 'Themes', 'ub' ); ?></div>
							<div class="sui-accordion-col-auto">
								<button type="button" class="sui-button-icon sui-accordion-open-indicator" aria-label="<?php esc_html_e( 'Open item', 'ub' ); ?>"><i class="sui-icon-chevron-down" aria-hidden="true"></i></button>
							</div>
						</div>
						<div class="sui-accordion-item-body">
							<div class="sui-box">
								<div class="sui-box-body">
									<div class="sui-form-field">
										<label class="sui-label"><?php esc_html_e( 'Active Theme Background', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_themes_background,
		'name'  => 'branda[admin_themes_background]',
	)
);
?>
									</div>
									<div class="sui-form-field">
										<label class="sui-label"><?php esc_html_e( 'Active Theme Actions Background', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_themes_actions_background,
		'name'  => 'branda[admin_themes_actions_background]',
	)
);
?>
									</div>
									<div class="sui-form-field">
										<label class="sui-label"><?php esc_html_e( 'Theme Details Button Background', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_themes_details_background,
		'name'  => 'branda[admin_themes_details_background]',
	)
);
?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="sui-accordion-item">
						<div class="sui-accordion-item-header">
							<div class="sui-accordion-item-title"><?php esc_html_e( 'Plugins', 'ub' ); ?></div>
							<div class="sui-accordion-col-auto">
								<button type="button" class="sui-button-icon sui-accordion-open-indicator" aria-label="<?php esc_html_e( 'Open item', 'ub' ); ?>"><i class="sui-icon-chevron-down" aria-hidden="true"></i></button>
							</div>
						</div>
						<div class="sui-accordion-item-body">
							<div class="sui-box">
								<div class="sui-box-body">
									<div class="sui-form-field">
										<label class="sui-label"><?php esc_html_e( 'Progress Bar￼', 'ub' ); ?></label>
<?php
$this->render(
	'admin/common/options/sui-colorpicker',
	array(
		'value' => $admin_plugins_border_color,
		'name'  => 'branda[admin_plugins_border_color]',
	)
);
?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="sui-box-footer sui-space-between">
				<button class="sui-button sui-button-ghost" type="button" data-modal-close=""><?php esc_html_e( 'Cancel', 'ub' ); ?></button>
				<button class="sui-button <?php echo esc_attr( $button_apply_class ); ?>" data-nonce="<?php echo esc_attr( $button_apply_nonce ); ?>" type="button">
					<?php esc_html_e( 'Update', 'ub' ); ?>
				</button>
			</div>
		</div>
	</div>
</div>
