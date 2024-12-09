<style id="<?php echo esc_attr( $id ); ?>" type="text/css">
.cb-slideshow,
.cb-slideshow li,
.cb-slideshow li span {
	line-height: 0;
}
<?php if ( isset( $design['form_messages_opacity'] ) ) { ?>
#login_error {
	opacity: <?php printf( '%0.2f', intval( $design['form_messages_opacity'] ) / 100 ); ?>
}
<?php } ?>
</style>

