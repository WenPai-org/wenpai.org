<canvas
	class="branda-focal"
	id="<?php echo esc_attr( $html_id ); ?>"
	width="300"
	height="200"
	data-background-image="<?php echo esc_attr( $background_image ); ?>"
><?php esc_html_e( 'This text is displayed if your browser does not support HTML5 Canvas.', 'ub' ); ?></canvas>
<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>[x]" value="<?php echo esc_attr( $value_x ); ?>" class="branda-focal-x" />
<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>[y]" value="<?php echo esc_attr( $value_y ); ?>" class="branda-focal-y" />
<span class="sui-description">
	<?php esc_html_e( 'Image position:', 'ub' ); ?>
	<span class="branda-focal-x"><?php echo esc_attr( $value_x ); ?></span>%
	<span class="branda-focal-y"><?php echo esc_attr( $value_y ); ?></span>%
</span>
