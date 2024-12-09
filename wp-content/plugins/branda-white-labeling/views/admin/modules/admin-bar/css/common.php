<style type="text/css" id="<?php echo esc_attr( $id ); ?>">
<?php echo $styles; ?>

<?php if ( ! empty( $mobile ) ) { ?>
@media screen and (max-width: 782px) {
	<?php echo $mobile; ?> {
	display: block;
}
}
<?php } ?>
</style>
