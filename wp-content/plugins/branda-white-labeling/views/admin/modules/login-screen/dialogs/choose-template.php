<p class="sui-description"><?php esc_html_e( 'Customize one of our pre-designed login templates, or start styling login page from scratch.', 'ub' ); ?></p>
<?php
if ( $show_warning ) {
	echo Branda_Helper::sui_notice( esc_html__( 'Be careful, changing a template would override the customization you\'ve done.', 'ub' ), 'warning' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
?>
<div class="sui-box-selectors">
	<ul>
<?php
foreach ( $elements as $k => $value ) {
	$classes = array(
		'branda-login-screen-li',
		sprintf( 'branda-login-screen-template-%s', $k ),
	);
	if ( $current === $value['id'] ) {
		$classes[] = 'branda-selected';
	}
	$classes = implode( ' ', $classes );
	printf( '<li class="%s">', esc_attr( $classes ) );
	printf( '<label for="%s" class="sui-box-selector">', esc_attr( $value['branda_id'] ) );
	printf(
		'<input type="radio" name="branda-login-screen-template" value="%s" id="%s" %s />',
		esc_attr( $value['id'] ),
		esc_attr( $value['branda_id'] ),
		checked( $current, $value['id'], false )
	);
	echo '<span class="branda-template-container"';
	if ( isset( $value['screenshot'] ) ) {
		printf(
			' style="background-image:url(%s);"',
			esc_attr( $value['screenshot'] )
		);
	}
	echo '>';
	printf(
		'<span class="login-screen-icon"><i class="sui-icon-%s" aria-hidden="true"></i></span>',
		'scratch' === $k ? 'pencil' : 'clipboard-notes'
	);
	printf(
		'<span class="login-screen-title">%s</span>',
		esc_html( $value['Name'] )
	);
	echo '</span>';
	echo '</label></li>';
}
?>
	</ul>
</div>

