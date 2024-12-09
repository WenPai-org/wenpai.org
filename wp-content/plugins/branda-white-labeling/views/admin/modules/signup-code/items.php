<div class="sui-label"><?php esc_html_e( 'Signup Codes', 'ub' ); ?></div>
<div class="sui-box-builder <?php echo esc_attr( $container_class ); ?>">
	<div class="sui-box-builder-body">
		<div class="sui-box-builder-fields">
<?php
foreach ( $items as $id => $item ) {
	$item['roles'] = $roles;
	$this->render( $row, $item );
}
?>
		</div>
	</div>
	<div class="sui-box-builder-footer">
		<button type="button" class="sui-button sui-button-dashed branda-add" data-type="<?php echo esc_attr( $type ); ?>" data-template="<?php echo esc_attr( $this->get_name( 'row-' . $type ) ); ?>"><i class="sui-icon-plus" aria-hidden="true"></i><?php esc_html_e( 'Add Signup Code', 'ub' ); ?></button>
	</div>
</div>
<div class="sui-description"><?php esc_html_e( 'Users must provide one of the codes to signup successfully, and they\'ll get the user role associated with the signup code they use.', 'ub' ); ?></div>

