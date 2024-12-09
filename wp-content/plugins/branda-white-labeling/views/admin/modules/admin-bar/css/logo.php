<style type="text/css" id="<?php echo esc_attr( $id ); ?>">
<?php global $is_IE; ?>
body #wpadminbar #wp-admin-bar-wp-logo > .ab-item {
<?php if ( $is_svg && ! $is_IE ) { ?>
	-webkit-mask-image: url(<?php echo esc_url( $src ); ?>);
	-webkit-mask-repeat: no-repeat;
	-webkit-mask-position: 50%;
	-webkit-mask-size: 80%;
	mask-image: url(<?php echo esc_url( $src ); ?>);
	mask-repeat: no-repeat;
	mask-position: 50%;
	mask-size: 80%;
	background-color: <?php echo esc_attr( $base ); ?>;
<?php } else { ?>
	background-image: url(<?php echo esc_url( $src ); ?>);
	background-repeat: no-repeat;
	background-position: 50%;
	background-size: 80%;
<?php } ?>
}
<?php if ( $is_svg && ! $is_IE ) { ?>
body #wpadminbar #wp-admin-bar-wp-logo > .ab-item:focus {
	background-color: <?php echo esc_attr( $focus ); ?>;
}
<?php } ?>
body #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
	content: " ";
}
</style>

