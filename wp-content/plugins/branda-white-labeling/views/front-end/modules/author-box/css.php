<!-- Branda: Author Box -->
<style type="text/css" id="<?php echo esc_attr( $id ); ?>">
<?php
/*************************************************
 * BOX
 */
?>
.branda-author-box {
<?php
/**
 * Border with
 */
if ( isset( $container_border_width ) ) {
	?>
	border-width: <?php echo (int) $container_border_width; ?>px;
<?php } ?>
<?php
/**
 * Border Style
 */
if ( ! empty( $container_border_style ) ) {
	?>
	border-style: <?php echo esc_attr( $container_border_style ); ?>;
<?php } ?>
<?php
/**
 * Border Radius
 */
if ( 0 < $container_border_radius ) {
	?>
	-webkit-border-radius: <?php echo esc_attr( $container_border_radius ); ?>px;
	-moz-border-radius: <?php echo esc_attr( $container_border_radius ); ?>px;
	border-radius: <?php echo esc_attr( $container_border_radius ); ?>px;
<?php } ?>
<?php
/**
 * Border color
 */
if ( ! empty( $container_border_color ) ) {
	?>
	border-color: <?php echo esc_attr( $container_border_color ); ?>;
<?php } ?>
<?php
/**
 * background color
 */
if ( ! empty( $container_background_color ) ) {
	?>
	background-color: <?php echo esc_attr( $container_background_color ); ?>;
<?php } ?>
}
<?php
/*************************************************
 * Avatar
 */
?>
.branda-author-box .branda-author-box-content img.avatar {
	width: <?php echo esc_attr( $avatar_size ); ?>px;
	height: <?php echo esc_attr( $avatar_size ); ?>px;
<?php
/**
 * Border with
 */
if ( 0 < $avatar_border_width ) {
	?>
	border-width: <?php echo esc_attr( $avatar_border_width ); ?>px;
<?php } ?>
<?php
/**
 * Border Style
 */
if ( ! empty( $avatar_border_style ) ) {
	?>
	border-style: <?php echo esc_attr( $avatar_border_style ); ?>;
<?php } ?>
<?php
/**
 * Border Radius
 */
if ( 0 < $avatar_border_radius ) {
	?>
	border-radius: <?php echo esc_attr( $avatar_border_radius ); ?>px;
<?php } else { ?>
	border-radius: 0;
<?php } ?>
<?php
/**
 * Border color
 */
if ( ! empty( $avatar_border_color ) ) {
	?>
	border-color: <?php echo esc_attr( $avatar_border_color ); ?>;
<?php } ?>
}
<?php
/*************************************************
 * Latest Entries
 */
?>
.branda-author-box .branda-author-box-content .branda-author-box-more ul a {
<?php
/**
 * Entries Color
 */
if ( ! empty( $latest_entries_entry_color ) ) {
	?>
	color: <?php echo esc_attr( $latest_entries_entry_color ); ?>;
<?php } ?>
}
.branda-author-box .branda-author-box-content .branda-author-box-more h4,
.branda-author-box .branda-author-box-content .branda-author-box-more h4 a {
<?php
/**
 * Title Color
 */
if ( ! empty( $latest_entries_title_color ) ) {
	?>
	color: <?php echo esc_attr( $latest_entries_title_color ); ?>;
<?php } ?>
}
<?php
/*************************************************
 * Social Media
 */
?>
.branda-author-box .social-media {
<?php
/**
 * background color
 */
if ( ! empty( $social_background_color ) ) {
	?>
	background-color: <?php echo esc_attr( $social_background_color ); ?>;
<?php } ?>
<?php
/**
 * Border Radius
 */
if ( ! empty( $social_media_radius ) ) {
	?>
	-webkit-border-radius: <?php echo esc_attr( $social_media_radius ); ?>;
	-moz-border-radius: <?php echo esc_attr( $social_media_radius ); ?>;
	border-radius: <?php echo esc_attr( $social_media_radius ); ?>;
<?php } ?>
}
.branda-author-box .social-media a {
	text-decoration: none;
<?php
/**
 * Color
 */
if ( ! empty( $social_media_color ) ) {
	?>
	color: <?php echo esc_attr( $social_media_color ); ?>;
<?php } ?>
}
<?php
/*************************************************
 * Colors: author title
 */
if ( ! empty( $name_and_bio_name_color ) ) {
	?>
.branda-author-box .branda-author-box-desc h4,
.branda-author-box .branda-author-box-desc h4 a {
	color: <?php echo esc_attr( $name_and_bio_name_color ); ?>;
}
<?php } ?>
<?php
/**
 * Colors: author description
 */
if ( ! empty( $name_and_bio_bio_color ) ) {
	?>
.branda-author-box .branda-author-box-desc .description,
.branda-author-box .branda-author-box-desc .description p {
	color: <?php echo esc_attr( $name_and_bio_bio_color ); ?>;
}
<?php } ?>
<?php
/*************************************************
 * Custom CSS
 */
?>
<?php echo $custom; ?>
</style>

