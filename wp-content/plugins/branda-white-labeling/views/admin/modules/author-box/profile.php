<h2><?php esc_html_e( 'Social Media Profiles', 'ub' ); ?></h2>
<table class="form-table">
	<tbody>
<?php foreach ( $fields as $key => $data ) { ?>
		<tr class="branda-social-media-<?php echo esc_attr( $key ); ?>">
			<th><label for="<?php echo esc_attr( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label></th>
			<td><input type="url" id="<?php echo esc_attr( $data['id'] ); ?>" class="regular-text" value="<?php echo esc_attr( $data['value'] ); ?>" name="<?php echo esc_attr( $data['option_name'] ); ?>[<?php echo esc_attr( $key ); ?>]" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" /></td>
		</tr>
<?php } ?>
	</tbody>
</table>
