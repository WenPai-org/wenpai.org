<div class="sui-footer">
	<a href="<?php echo $cancel_url; ?>" class="sui-button sui-button-ghost"><?php echo esc_html_x( 'Cancel', 'button', 'ub' ); ?></a>
	<button class="sui-button sui-button-disabled sui-tooltip" type="submit" disabled="disabled" data-tooltip="<?php esc_attr_e( 'Configuration is not possible!', 'ub' ); ?>">
		<span class="sui-loading-text"><?php esc_html_e( 'Configure', 'ub' ); ?></span><i class="sui-icon-loader sui-loading" aria-hidden="true"> </i>
	</button>
</div>
