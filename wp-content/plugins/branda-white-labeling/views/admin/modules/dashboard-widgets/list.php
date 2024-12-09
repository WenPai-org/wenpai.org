<div class="sui-box-builder">
	<div class="sui-box-builder-header"><?php echo $button; ?></div>
	<div class="sui-box-builder-body">
		<div class="sui-box-builder-fields branda-dashboard-widgets-items">
<?php
if ( is_array( $items ) ) {
	foreach ( $items as $id => $args ) {
		$this->render( $template, $args );
	}
}
?>
		</div>
<?php echo $button_plus; ?>
		<span class="sui-box-builder-message<?php echo esc_attr( empty( $items ) ? '' : ' hidden' ); ?>"><?php esc_html_e( 'No text widget has been added yet. Click on “+ Add Text Widget” to add your first text widget using a simple wizard.', 'ub' ); ?></span>
	</div>
</div>
