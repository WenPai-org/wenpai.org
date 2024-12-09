<div class="<?php echo esc_attr( implode( ' ', $sui['summary']['classes'] ) ); ?>" id="branda-dashboard-widget-summary">
<div class="sui-summary-image-space" aria-hidden="true" style="<?php echo esc_attr( $sui['summary']['style'] ); ?>"></div>
	<div class="sui-summary-segment">
		<div class="sui-summary-details">
			<span class="sui-summary-large"><?php echo esc_html( $stats['active'] ); ?></span>
			<span class="sui-summary-sub"><?php echo esc_html( _n( 'Active Module', 'Active Modules', $stats['active'], 'ub' ) ); ?></span>
			<div class="sui-control-with-icon">
				<input type="text" id="branda-dashboard-widget-summary-search" class="sui-form-control sui-input-md" placeholder="<?php esc_attr_e( 'Search module...', 'ub' ); ?>" />
				<i class="sui-icon-magnifying-glass-search" aria-hidden="true"></i>
			</div>
		</div>
	</div>
	<div class="sui-summary-segment">
		<ul class="sui-list">
			<li>
				<span class="sui-list-label"><?php esc_html_e( 'Total Modules', 'ub' ); ?></span>
				<span class="sui-list-detail"><?php echo esc_html( $stats['total'] ); ?></span>
			</li>
			<li>
				<span class="sui-list-label"><?php esc_html_e( 'Recently Activated', 'ub' ); ?></span>
				<span class="sui-list-detail"><?php echo $stats['recently_activated']; ?></span>
			</li>
			<li>
				<span class="sui-list-label"><?php esc_html_e( 'Recently Deactivated', 'ub' ); ?></span>
				<span class="sui-list-detail"><?php echo $stats['recently_deactivated']; ?></span>
			</li>
		</ul>
	</div>
</div>
