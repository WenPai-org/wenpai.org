<div class="sui-box-builder">
	<div class="sui-box-builder-header"><?php echo $button; ?></div>
	<div class="sui-box-builder-body">
		<div class="sui-box-builder-fields branda-admin-help-content-items">
<?php
if ( is_array( $items ) ) {
	foreach ( $items as $id => $args ) {
		$this->render( $template, $args );
	}
}
?>
		</div>
<?php echo $button_plus; ?>
	</div>
</div>
