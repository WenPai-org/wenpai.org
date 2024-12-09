<div class="sui-modal branda-container-all-modules">
	<div
		role="dialog"
		aria-modal="true"
		id="branda-manage-all-modules"
		class="sui-modal-content"
		aria-labelledby="branda-manage-all-modules-title"
		aria-describedby="branda-manage-all-modules-description"
	>
		<div class="sui-box branda-manage-all-modules-box" role="document">
			<div class="sui-box-header">
				<h1 class="sui-header-title" id="branda-manage-all-modules-title"><?php esc_html_e( 'Manage All Modules', 'ub' ); ?></h1>
				<p id="branda-manage-all-modules-description"><?php esc_html_e( 'Select the modules which should be active. The checked modules are already active. You can use this section to activate/deactivate modules in bulk instead of doing so one by one.', 'ub' ); ?></p>
			</div>
			<div class="sui-box-body">
				<section id="sui-branda-content" class="sui-container branda-avoid-flag">
					<div class="sui-row">
<?php
foreach ( $groups as $group_key => $group ) {
	if ( 'data' === $group_key ) {
		continue;
	}
	$actived = 0;
	foreach ( $modules[ $group_key ]['modules'] as $key => $module ) {
		if ( isset( $module['status'] ) && 'active' === $module['status'] ) {
			$actived++;
		}
	}
	$checked = $actived === count( $modules[ $group_key ]['modules'] );
	echo '<div class="sui-col">';
	printf( '<div class="sui-label">%s</div>', $group['title'] );
	$id = sprintf( 'branda-%s-%s', $group_key, 'all' );
	printf( '<label for="%s" class="sui-checkbox sui-checkbox-stacked">', esc_attr( $id ) );
	printf(
		'<input type="checkbox" class="branda-group-checkbox" id="%s" %s />',
		esc_html( $id ),
		checked( $checked, true, false )
	);
	echo '<span aria-hidden="true"></span>';
	printf( '<span>%s</span>', esc_html__( 'All', 'ub' ) );
	echo '</label>';
	foreach ( $modules[ $group_key ]['modules'] as $key => $module ) {
		$checked = isset( $module['status'] ) && 'active' === $module['status'];
		$id      = sprintf( 'branda-%s-%s', $group_key, $key );
		printf( '<label for="%s" class="sui-checkbox sui-checkbox-stacked">', esc_attr( $id ) );
		printf(
			'<input type="checkbox" id="%s" name="%s"%s />',
			esc_attr( $id ),
			esc_attr( $module['module'] ),
			checked( $checked, true, false )
		);
		echo '<span aria-hidden="true"></span>';
		// If menu title is set, use that instead of name for consistency.
		printf( '<span>%s</span>', isset( $module['menu_title'] ) ? esc_html( $module['menu_title'] ) : esc_html( $module['name'] ) );
		echo '</label>';
	}
	echo '</div>';
}
?>
					</div>
				</section>
			</div>
			<div class="sui-box-footer">
				<button class="sui-button sui-button-ghost" data-modal-close=""><?php echo esc_html_x( 'Cancel', 'button', 'ub' ); ?></button>
				<button class="sui-button sui-button-blue branda-save-all" type="button" data-nonce="<?php echo wp_create_nonce( 'branda-manage-all-modules' ); ?>">
					<span class="sui-loading-text"><?php echo esc_html_x( 'Save Changes', 'button', 'ub' ); ?></span><i class="sui-icon-loader sui-loading" aria-hidden="true"> </i>
				</button>
			</div>
		</div>
	</div>
</div>

