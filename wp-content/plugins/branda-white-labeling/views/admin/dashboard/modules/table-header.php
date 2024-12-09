<thead>
	<tr>
		<th class="sui-table--name"><?php esc_attr_e( 'Module', 'ub' ); ?></th>
<?php if ( 'subsite' === $mode ) { ?>
		<th class="sui-table--status"><?php esc_attr_e( 'Edit', 'ub' ); ?></th>
<?php } else { ?>
		<th class="sui-table--status"><?php esc_attr_e( 'Status', 'ub' ); ?></th>
<?php } ?>
	</tr>
</thead>

