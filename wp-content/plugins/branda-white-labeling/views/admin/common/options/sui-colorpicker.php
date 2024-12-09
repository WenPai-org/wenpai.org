<?php
if ( ! isset( $alpha ) ) {
	$alpha = 'true';
}
if ( ! isset( $id ) ) {
	$id = '';
}
if ( ! isset( $button ) ) {
	$button = _x( 'Select', 'Default button name for color picker.', 'ub' );
}
?>
<div class="sui-colorpicker-wrap">
	<div class="sui-colorpicker" aria-hidden="true">
		<div class="sui-colorpicker-value">
			<span role="button">
				<span style="background-color: <?php echo esc_attr( $value ); ?>"></span>
			</span>
			<input type="text" value="<?php echo esc_attr( $value ); ?>" readonly="readonly" />
			<button><i class="sui-icon-close" aria-hidden="true"></i></button>
		</div>
		<button class="sui-button"><?php echo esc_html( $button ); ?></button>
	</div>
	<input type="text" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" id="<?php echo esc_attr( $id ); ?>" class="sui-colorpicker-input" data-alpha="<?php echo esc_attr( $alpha ); ?>" data-attribute="<?php echo esc_attr( $value ); ?>" />
</div>
