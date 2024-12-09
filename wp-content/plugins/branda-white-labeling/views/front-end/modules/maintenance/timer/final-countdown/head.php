<?php wp_print_scripts( 'ub-jquery-final-countdown-script' ); ?>
<script type="text/javascript" id="<?php echo esc_attr( $id ); ?>">
jQuery( document ).ready( function( $ ) {
	$( '.countdown' ).final_countdown( {
		'end': <?php echo $distance_raw; ?>,
		'now': <?php echo time(); ?>
	}, function() {
		window.location.reload();
	});
});
</script>
