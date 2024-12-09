<?php
$dialog_id = sprintf( 'branda-copy-settings-%s', $module['module'] );
$data_name = '_' . Branda_Helper::hyphen_to_underscore( $dialog_id );
?>
<button type="button" class="sui-button sui-button-ghost" data-modal-open="<?php echo esc_attr( $dialog_id ); ?>" data-modal-mask="true" data-data-name="<?php echo esc_attr( $data_name ); ?>" ><?php echo esc_html_x( 'Copy Settings', 'button', 'ub' ); ?></button>
<div class="sui-modal sui-modal-sm">
	<div class="sui-modal-content" id="<?php echo esc_attr( $dialog_id ); ?>" aria-labelledby="<?php echo esc_attr( $dialog_id ) . '-title'; ?>" aria-describedby="<?php echo esc_attr( $dialog_id ) . '-description'; ?>" role="dialog" aria-modal="true">
		<div class="sui-box" role="document">
			<div class="sui-box-header sui-content-center  sui-flatten">
				<button class="sui-button-icon sui-button-float--right" data-modal-close>
					<i class="sui-icon-close sui-md" aria-hidden="true"></i>
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this modal', 'ub' ); ?></span>
				</button>
				<h3 class="sui-box-title sui-lg" id="<?php echo esc_attr( $dialog_id ) . '-title'; ?>"><?php esc_html_e( 'Copy Settings', 'ub' ); ?></h3>
			</div>
			<div class="sui-box-body sui-content-center sui-flatten">
				<p id="<?php echo esc_attr( $dialog_id ) . '-description'; ?>"><?php esc_html_e( 'Choose the module you want to copy settings from and specify the sections to copy the setting of.', 'ub' ); ?></p>
				<div class="sui-form-field">
					<label for="dialog-text-5" class="sui-label"><?php esc_html_e( 'Copy settings from', 'ub' ); ?></label>
					<select class="branda-copy-settings-select">
						<option value=""><?php esc_html_e( 'Choose module', 'ub' ); ?></option>
<?php
asort( $related );
foreach ( $related as $module_key => $data ) {
	printf(
		'<option value="%s">%s</option>',
		esc_attr( $module_key ),
		esc_html( $data['title'] )
	);
}
?>
					</select>
				</div>
<?php
foreach ( $related as $module_key => $data ) {
	printf(
		'<div class="branda-copy-settings-options branda-copy-settings-%s hidden">',
		esc_attr( $module_key )
	);
	foreach ( $data['options'] as $value => $label ) {
		?>
<label class="sui-checkbox sui-checkbox-stacked">
<input type="checkbox" class="branda-copy-settings-section" value="<?php echo esc_attr( $value ); ?>" />
	<span aria-hidden="true"></span>
	<span><?php echo esc_html( $label ); ?></span>
</label>
		<?php
	}
	echo '</div>';
}
?>
			</div>
			<div class="sui-box-footer sui-space-between sui-flatten">
				<button type="button" class="sui-button sui-button-ghost" data-modal-close=""><?php echo esc_html_x( 'Cancel', 'Dialog "Copy Settings" button', 'ub' ); ?></button>
				<button type="button" class="sui-modal-close sui-button sui-button-blue branda-copy-settings-copy-button" data-module="<?php echo esc_attr( $module['module'] ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( $dialog_id ) ); ?>" disabled="disabled"><?php echo esc_html_x( 'Copy', 'Dialog "Copy Settings" button', 'ub' ); ?></button>
			</div>
		</div>
	</div>
</div>
