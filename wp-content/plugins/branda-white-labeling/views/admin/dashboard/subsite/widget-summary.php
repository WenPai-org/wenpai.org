<div class="sui-box sui-summary <?php echo esc_attr( $class ); ?>" id="branda-dashboard-widget-summary">
	<div class="sui-summary-image-space" aria-hidden="true" style="<?php echo esc_attr( $style ); ?>"></div>
	<div class="sui-summary-segment">
		<div class="sui-summary-details">
			<span class="sui-summary-large"><?php echo esc_html( $stats['active'] ); ?></span>
			<span class="sui-summary-sub"><?php echo esc_html( _n( 'Active Module', 'Active Modules', $stats['active'], 'ub' ) ); ?></span>
		</div>
	</div>
	<div class="sui-summary-segment">
		<div class="sui-summary-details">
			<div class="sui-control-with-icon">
				<input type="text" id="branda-dashboard-widget-summary-search" class="sui-form-control sui-input-md" placeholder="<?php esc_attr_e( 'Search module...', 'ub' ); ?>" />
				<i class="sui-icon-magnifying-glass-search" aria-hidden="true"></i>
			</div>
		</div>
	</div>
</div>
