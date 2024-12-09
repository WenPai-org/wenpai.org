.cb-slideshow,
.cb-slideshow li {
	line-height: 0;
	margin: 0;
	padding: 0;
}
.cb-slideshow,
.cb-slideshow:after {
	height: 100%;
	left: 0;
	position: fixed;
	top: 0;
	width: 100%;
	z-index: -1;
}
.cb-slideshow:after {
	content: '';
}
.cb-slideshow li span {
	color: transparent;
	height: 100%;
	left: 0;
	line-height: 0;
	position: absolute;
	top: 0;
	width: 100%;
	background-size: <?php
	if ( is_array( $background_size ) ) {
		echo $background_size[0];
		echo ' ';
		echo $background_size[1];
	} else {
		echo $background_size;
	}

	// spied here https://tympanus.net/codrops/2012/01/02/fullscreen-background-image-slideshow-with-css3/
	$period             = count( $images ) * $duration;
	$show_perc          = (int) round( 100 / count( $images ) );
	$start_to_show_perc = (int) round( 0.5 * $show_perc );
	$stop_to_show_perc  = (int) round( 1.5 * $show_perc );
	?>;
	background-position: <?php echo $background_position_x; ?> <?php echo $background_position_y; ?>;
	background-repeat: no-repeat;
	opacity: 0;
	z-index: -1;
	-webkit-backface-visibility: hidden;
	backface-visibility: hidden;
	-webkit-animation: imageAnimation <?php echo $period; ?>s linear infinite 0s;
	-moz-animation: imageAnimation <?php echo $period; ?>s linear infinite 0s;
	-o-animation: imageAnimation <?php echo $period; ?>s linear infinite 0s;
	-ms-animation: imageAnimation <?php echo $period; ?>s linear infinite 0s;
	animation: imageAnimation <?php echo $period; ?>s linear infinite 0s;
}
<?php
$i = 0;
foreach ( $images as $image ) {
	?>
.cb-slideshow li:nth-child(<?php echo $i + 1; ?>) span {
	background-image: url(<?php echo $image; ?>);
	<?php if ( 0 < $i ) { ?>
	animation-delay: <?php echo $duration * $i; ?>s;
<?php } ?>
}
	<?php
	$i++;
}
?>
@keyframes imageAnimation {
	0% { opacity: 0; animation-timing-function: ease-in; }
	<?php echo $start_to_show_perc; ?>% { opacity: 1; animation-timing-function: ease-out; }
	<?php echo $show_perc; ?>% { opacity: 1 }
	<?php echo $stop_to_show_perc; ?>% { opacity: 0 }
	100% { opacity: 0 }
}
.no-cssanimations .cb-slideshow li span{
	opacity: 1;
}
