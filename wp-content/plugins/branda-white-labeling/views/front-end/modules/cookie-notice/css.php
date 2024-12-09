<style type="text/css" id="<?php echo esc_attr( $id ); ?>">
<?php
/**
 * common
 */
?>
<?php echo $name; ?> {
<?php if ( isset( $colors['content_color'] ) ) { ?>
	color: <?php echo esc_attr( $colors['content_color'] ); ?>;
<?php } ?>
<?php if ( isset( $colors['content_background'] ) ) { ?>
	background-color: <?php echo esc_attr( $colors['content_background'] ); ?>;
<?php } ?>
}
<?php
/**
 * a tag
 */
?>
<?php echo $name; ?> a,
<?php echo $name; ?> a:link {
<?php if ( isset( $colors['link_color'] ) ) { ?>
	color: <?php echo esc_attr( $colors['link_color'] ); ?>;
<?php } ?>
}
<?php echo $name; ?> a:visited {
<?php if ( isset( $colors['link_color_visited'] ) ) { ?>
	color: <?php echo esc_attr( $colors['link_color_visited'] ); ?>;
<?php } ?>
}
<?php echo $name; ?> a:hover {
<?php if ( isset( $colors['link_color_hover'] ) ) { ?>
	color: <?php echo esc_attr( $colors['link_color_hover'] ); ?>;
<?php } ?>
}
<?php echo $name; ?> a:active {
<?php if ( isset( $colors['link_color_active'] ) ) { ?>
	color: <?php echo esc_attr( $colors['link_color_active'] ); ?>;
<?php } ?>
}
<?php echo $name; ?> a:focus {
<?php if ( isset( $colors['link_color_focus'] ) ) { ?>
	color: <?php echo esc_attr( $colors['link_color_focus'] ); ?>;
<?php } ?>
}
<?php
/**
 * .button
 */
?>
<?php echo $name; ?> .button,
<?php echo $name; ?> .button:link {
<?php if ( isset( $colors['button_label'] ) ) { ?>
	color: <?php echo esc_attr( $colors['button_label'] ); ?>;
<?php } ?>
<?php if ( isset( $colors['button_border'] ) ) { ?>
	border-color: <?php echo esc_attr( $colors['button_border'] ); ?>;
<?php } ?>
<?php if ( isset( $colors['button_background'] ) ) { ?>
	background-color: <?php echo esc_attr( $colors['button_background'] ); ?>;
<?php } ?>
<?php if ( isset( $design['cookie_button_border'] ) ) { ?>
	border-style: solid;
	border-width: <?php echo esc_attr( $design['cookie_button_border'] ); ?>px;
<?php } ?>
<?php
if ( isset( $design['radius'] ) ) {
	$radius = intval( $design['radius'] );
	?>
	-webkit-border-radius: <?php echo esc_attr( $radius ); ?>px;
	-moz-border-radius: <?php echo esc_attr( $radius ); ?>px;
	border-radius: <?php echo esc_attr( $radius ); ?>px;
<?php } ?>
}
<?php echo $name; ?> .button:visited {
<?php if ( isset( $colors['button_label_visited'] ) ) { ?>
	color: <?php echo esc_attr( $colors['button_label_visited'] ); ?>;
<?php } ?>
<?php if ( isset( $colors['button_border_visited'] ) ) { ?>
	border-color: <?php echo esc_attr( $colors['button_border_visited'] ); ?>;
<?php } ?>
<?php if ( isset( $colors['button_background_visited'] ) ) { ?>
	background-color: <?php echo esc_attr( $colors['button_background_visited'] ); ?>;
<?php } ?>
}
<?php echo $name; ?> .button:hover {
<?php if ( isset( $colors['button_label_hover'] ) ) { ?>
	color: <?php echo esc_attr( $colors['button_label_hover'] ); ?>;
<?php } ?>
<?php if ( isset( $colors['button_border_hover'] ) ) { ?>
	border-color: <?php echo esc_attr( $colors['button_border_hover'] ); ?>;
<?php } ?>
<?php if ( isset( $colors['button_background_hover'] ) ) { ?>
	background-color: <?php echo esc_attr( $colors['button_background_hover'] ); ?>;
<?php } ?>
}
<?php echo $name; ?> .button:active {
<?php if ( isset( $colors['button_label_active'] ) ) { ?>
	color: <?php echo esc_attr( $colors['button_label_active'] ); ?>;
<?php } ?>
<?php if ( isset( $colors['button_border_active'] ) ) { ?>
	border-color: <?php echo esc_attr( $colors['button_border_active'] ); ?>;
<?php } ?>
<?php if ( isset( $colors['button_background_active'] ) ) { ?>
	background-color: <?php echo esc_attr( $colors['button_background_active'] ); ?>;
<?php } ?>
}
<?php echo $name; ?> .button:focus {
<?php if ( isset( $colors['button_label_focus'] ) ) { ?>
	color: <?php echo esc_attr( $colors['button_label_focus'] ); ?>;
<?php } ?>
<?php if ( isset( $colors['button_border_focus'] ) ) { ?>
	border-color: <?php echo esc_attr( $colors['button_border_focus'] ); ?>;
<?php } ?>
<?php if ( isset( $colors['button_background_focus'] ) ) { ?>
	background-color: <?php echo esc_attr( $colors['button_background_focus'] ); ?>;
<?php } ?>
}
</style>

