<div class="sui-row"><?php
foreach ( $groups as $group_key => $group ) {
	if ( 'data' === $group_key ) {
		continue;
	}
	echo '<div class="sui-col">';
	printf( '<div class="sui-label">%s</div>', $group['title'] );
	$id = sprintf( 'branda-%s-%s', $group_key, 'all' );
	printf( '<label for="%s" class="sui-checkbox sui-checkbox-stacked">', esc_attr( $id ) );
	printf(
		'<input type="checkbox" class="branda-group-checkbox" id="%s" />',
		esc_html( $id )
	);
	echo '<span aria-hidden="true"></span>';
	printf( '<span>%s</span>', esc_html__( 'All', 'ub' ) );
	echo '</label>';
	foreach ( $modules[ $group_key ]['modules'] as $key => $module ) {
		$id = sprintf( 'branda-%s-%s', $group_key, $key );
		printf( '<label for="%s" class="sui-checkbox sui-checkbox-stacked">', esc_attr( $id ) );
		printf(
			'<input type="checkbox" id="%s" name="%s"%s />',
			esc_attr( $id ),
			esc_attr( $module['module'] ),
			isset( $module['status'] ) && 'active' === $module['status'] ? ' checked="checked"' : ''
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

