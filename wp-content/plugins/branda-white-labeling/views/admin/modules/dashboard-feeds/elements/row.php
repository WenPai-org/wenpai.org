<div class="sui-row">
	<h3 class="sui-col-md-12"><?php echo esc_html( $one['tab'] ); ?></h3>
<?php
/**
 * content
 */
$template = '/admin/modules/dashboard-feeds/elements/row-column';
foreach ( $one['fields'] as $id => $data ) {
	$args = array(
		'id'   => $id,
		'data' => $data,
		'item' => $item,
	);
	$this->render( $template, $args );
}
?>
	<div class="sui-col branda-divider"></div>
</div>

