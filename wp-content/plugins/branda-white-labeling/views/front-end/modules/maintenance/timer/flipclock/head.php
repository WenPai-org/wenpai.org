<?php
wp_print_scripts( 'ub-flipclock-script' );
wp_print_styles( 'ub-flipclock-styling' );
?>
<script type="text/javascript">
var clock;
jQuery(document).ready(function($) {
	// Instantiate a coutdown FlipClock
	clock = $('.clock').FlipClock( <?php echo $distance; ?>, {
	clockFace: 'DailyCounter',
		countdown: true,
		language: '<?php echo $language_code; ?>',
		callbacks: {
		stop: function() {
			window.location.reload();
			}
		}
	});
});
</script>
