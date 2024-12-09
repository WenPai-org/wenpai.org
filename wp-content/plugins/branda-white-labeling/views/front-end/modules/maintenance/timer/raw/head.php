<script type='text/javascript'>
var branda_distance = <?php echo intval( $distance ); ?>;
var branda_counter = setInterval( function() {
	var days = Math.floor( branda_distance / ( 60 * 60 * 24 ) );
	var hours = Math.floor( ( branda_distance % ( 60 * 60 * 24 ) ) / ( 60 * 60 ) );
	var minutes = Math.floor( ( branda_distance % ( 60 * 60 ) ) / ( 60 ) );
	var seconds = Math.floor( ( branda_distance % ( 60 ) ) );
	var value = '';
	if ( 0 < days ) {
		value += days + '<?php _ex( 'd', 'day letter of timer', 'ub' ); ?>' + ' ';
	}
	if ( 0 < hours ) {
		value += hours + '<?php _ex( 'h', 'hour letter of timer', 'ub' ); ?>' + ' ';
	}
	if ( 0 < minutes ) {
		value += minutes + '<?php _ex( 'm', 'minute letter of timer', 'ub' ); ?>' + ' ';
	}
	if ( 0 < seconds ) {
		value += seconds + '<?php _ex( 's', 'second letter of timer', 'ub' ); ?>';
	}
	if ( '' == value ) {
		value = '<?php _e( 'We are back now!', 'ub' ); ?>';
	}
	document.getElementById( 'counter' ).innerHTML = value;
	if ( 0 > branda_distance ) {
		window.location.reload();
	}
	branda_distance--;
}, 1000 );
</script>
