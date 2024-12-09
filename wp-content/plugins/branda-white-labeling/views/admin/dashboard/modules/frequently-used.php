<div id="branda-dashboard-widget-frequently-used" class="sui-box sui-box-close">
	<div class="sui-box-header">
		<h3 class="sui-box-title"><i class="sui-icon-clock" aria-hidden="true"></i><?php esc_html_e( 'Frequently Used', 'ub' ); ?></h3>
	</div>
<?php
$is_empty = true;
if ( ! empty( $modules ) ) {
	foreach ( $modules as $id => $module ) {
		if ( ! isset( $module['name'] ) ) {
			continue;
		}
		$is_empty = false;
		break; // Break foreach
	}
}
if ( $is_empty ) {
	?>
	<div class="sui-box-body">
		<?php
			echo Branda_Helper::sui_notice( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				esc_html__( 'We don\'t have enough data at the moment. As you begin interacting with the plugin, we\'ll start collecting data and show your frequently used modules here.', 'ub' ),
				'default'
			);
		?>
	</div>
	<?php
} else {
	?>
	<div class="sui-box-body">
		<p><?php esc_attr_e( 'You can find your top 5 frequently used modules here.', 'ub' ); ?></p>
	</div>
	<table class="sui-table sui-table-flushed">
	<?php $this->render( 'admin/dashboard/modules/table-header', array( 'mode' => $mode ) ); ?>
		<tbody>
	<?php
	foreach ( $modules as $id => $module ) {
		if ( ! isset( $module['name'] ) ) {
			continue;
		}
		$args = array(
			'id'     => $id,
			'module' => $module,
			'mode'   => $mode,
		);
		$this->render( 'admin/dashboard/modules/one-row', $args );
	}
	?>
		</tbody>
	</table>
<?php } ?>
</div>
