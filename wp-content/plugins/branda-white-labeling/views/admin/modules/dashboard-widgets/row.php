<div class="sui-builder-field" data-nonce="<?php echo esc_attr( $nonce ); ?>" data-id="<?php echo esc_attr( $id ); ?>">
	<input type="hidden" name="branda-help-content-order[]" value="<?php echo esc_attr( $id ); ?>" />
	<i class="sui-icon-drag" aria-hidden="true"></i>
	<div class="sui-builder-field-label"><?php echo esc_html( $title ); ?></div>
	<div class="sui-button-icon sui-button-red sui-hover-show branda-dashboard-widgets-item-delete">
		<i class="sui-icon-trash" aria-hidden="true"></i>
		<span class="sui-screen-reader-text"><?php esc_html__( 'Remove help item', 'ub' ); ?></span>
	</div>
	<span class="sui-builder-field-border sui-hover-show" aria-hidden="true"></span>
	<div class="sui-button-icon branda-dashboard-widgets-item-edit">
		<i class="sui-icon-widget-settings-config" aria-hidden="true"></i>
	</div>
</div>
