<?php
$search_nonce = empty( $search_nonce ) ? '' : $search_nonce;

$input_too_short = esc_html__( 'Type a username in the search box.', 'ub' );
if ( is_multisite() ) {
	if ( is_main_site() ) {
		$input_too_short .= ' ' . esc_html__( 'Please note, you can only customize the admin menu for the users who are added to the main site.', 'ub' );
	} else {
		$input_too_short .= ' ' . esc_html__( 'Please note, you can only customize the admin menu for the users of this subsite.', 'ub' );
	}
}
?>

<div class="branda-custom-admin-menu-users">
	<div class="sui-box-body">
		<div>
			<label for="branda-admin-menu-user-search" class="sui-label">
				<?php esc_html_e( 'Custom users', 'ub' ); ?>
			</label>

			<select class="sui-select sui-select-ajax"
				id="branda-admin-menu-user-search"
				data-placeholder="<?php esc_attr_e( 'Search user', 'ub' ); ?>"
				data-input-too-short="<?php echo esc_attr( $input_too_short ); ?>"
				data-dropdown-parent-class="branda-custom-admin-menu-users"
				data-action="branda_admin_menu_search_user"
				data-user-id="<?php echo esc_attr( get_current_user_id() ); ?>"
				data-nonce="<?php echo esc_attr( $search_nonce ); ?>">
			</select>

			<span class="sui-description">
				<?php esc_html_e( 'Search and add users to customize the admin menu.', 'ub' ); ?>
			</span>
		</div>
	</div>

	<div class="branda-custom-admin-menu-user-tabs-container sui-box-body"></div>
</div>
