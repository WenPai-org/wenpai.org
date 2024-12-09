<table class="sui-table sui-table-flushed" data-action="<?php echo esc_attr( $action ); ?>">
	<thead>
		<tr>
			<th class="sui-table--name"><?php esc_html_e( 'Plugin name', 'ub' ); ?></th>
			<th class="sui-table--status"><?php esc_html_e( 'Deactivate', 'ub' ); ?></th>
			<th class="sui-table--status"><?php esc_html_e( 'Deactivate &amp; Import Configuration', 'ub' ); ?></th>
		</tr>
	</thead>
	<tbody>
<?php foreach ( $plugins as $key => $data ) { ?>
		<tr data-plugin="<?php echo esc_attr( $key ); ?>" data-nonce="<?php echo esc_attr( $data['nonce'] ); ?>">
			<td><b><?php echo esc_html( $data['name'] ); ?></b></td>
			<td><button type="button" class="sui-button sui-button-ghost" data-mode="deactivate"><?php esc_html_e( 'Deactivate', 'ub' ); ?></button></td>
			<td>
	<?php if ( isset( $data['class'] ) ) { ?>
<button type="button" class="sui-button" data-mode="import"><?php esc_html_e( 'Deactivate &amp; Import Configuration', 'ub' ); ?></button></td>
<?php } else { ?>
&nbsp;
<?php } ?>
		</tr>
<?php } ?>
	</tbody>
</table>

