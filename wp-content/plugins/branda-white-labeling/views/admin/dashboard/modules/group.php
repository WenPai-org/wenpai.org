<div id="branda-dashboard-widget-<?php echo esc_attr( $group ); ?>" class="sui-box sui-box-close">
	<div class="sui-box-header">
		<h3 class="sui-box-title">
		<?php
		$mask = '<i class="sui-icon-%s" aria-hidden="true"></i>';
		if ( preg_match( '/^dashicons/', $info['icon'] ) ) {
			$mask = '<span class="dashicons %s"></span>';
		}
		printf( $mask, esc_attr( $info['icon'] ) );
		echo esc_html( $info['title'] );
		?>
		</h3>
	</div>
	<div class="sui-box-body">
		<p><?php echo esc_html( $info['description'] ); ?></p>
	</div>
	<table class="sui-table sui-table-flushed">
<?php $this->render( 'admin/dashboard/modules/table-header', array( 'mode' => $mode ) ); ?>
		<tbody>
<?php
foreach ( $data['modules'] as $id => $module ) {
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
</div>
