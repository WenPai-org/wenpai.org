<tr data-id="<?php echo esc_attr( $module['module'] ); ?>">
	<td class="sui-table--name sui-table-item-title"><?php echo esc_attr( $module['name'] ); ?></td>
	<td>
	<?php
	if ( isset( $stats['last_write_human'] ) ) {
		echo $stats['last_write_human'];
	} else {
		_e( 'Never', 'ub' );
	}
	?>
	</td>
	<td class="sui-table--configure">
		<a href="
		<?php
		echo add_query_arg(
			array(
				'page'   => sprintf( 'branding_group_%s', $module['group'] ),
				'module' => $module['module'],
			),
			is_network_admin() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' )
		);
		?>
		" class="sui-button-icon sui-tooltip sui-tooltip-top-right-mobile" data-tooltip="<?php esc_attr_e( 'Edit Module', 'ub' ); ?>">
			<i class="sui-icon-pencil" aria-hidden="true"></i>
		</a>
	</td>
</tr>
