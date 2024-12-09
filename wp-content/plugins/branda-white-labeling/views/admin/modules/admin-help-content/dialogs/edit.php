<div class="sui-modal sui-modal-lg">
	<div class="sui-modal-content" id="<?php echo esc_attr( $dialog_id ); ?>" aria-labelledby="<?php echo esc_attr( $dialog_id ) . '-title'; ?>" role="dialog" aria-modal="true">
		<div class="sui-box" role="document">
			<div class="sui-box-header">
				<button class="sui-button-icon sui-button-float--right" data-modal-close>
					<i class="sui-icon-close sui-md" aria-hidden="true"></i>
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this modal', 'ub' ); ?></span>
				</button>
				<h3 class="sui-box-title" id="<?php echo esc_attr( $dialog_id ) . '-title'; ?>">
					<span class="branda-new"><?php esc_html_e( 'Add Help Item', 'ub' ); ?></span>
					<span class="branda-edit"><?php esc_html_e( 'Edit Help Item', 'ub' ); ?></span>
				</h3>
			</div>
			<div class="sui-box-body">
				<div class="sui-form-field simple-option simple-option-text" >
					<label for="<?php echo esc_attr( $dialog_id ); ?>-title" class="sui-label"><?php esc_html_e( 'Title', 'ub' ); ?></label>
					<input type="text" id="<?php echo esc_attr( $dialog_id ); ?>-title" name="branda[title]" class="sui-form-control" placeholder="<?php esc_attr_e( 'Enter help item title here…', 'ub' ); ?>" />
				</div>
				<div class="sui-form-field simple-option simple-option-wp_editor" >
				<label for="<?php echo esc_attr( $dialog_id ); ?>-content" class="sui-label"><?php esc_html_e( 'Content', 'ub' ); ?></label>
					<div class="branda-editor-placeholder hidden" aria-hidden="true"><?php esc_attr_e( 'Enter help item content here…', 'ub' ); ?></div>
<?php
$id   = $dialog_id . '-content';
$args = array(
	'textarea_name' => 'branda[content]',
	'textarea_rows' => 9,
	// 'textarea_placeholder' => esc_attr_e( 'Add your help item content here…', 'ub' ),
	'teeny'         => true,
);
wp_editor( '', $id, $args );
?>
				</div>
				<input type="hidden" name="branda[id]" value="new" />
			</div>
			<div class="sui-box-footer sui-space-between">
				<button class="sui-button sui-button-ghost" type="button" data-modal-close=""><?php esc_html_e( 'Cancel', 'ub' ); ?></button>
				<button class="sui-button branda-admin-help-content-save branda-save" type="button">
					<span class="sui-loading-text">
						<span class="branda-new"><i class="sui-icon-check"></i><?php esc_html_e( 'Add', 'ub' ); ?></span>
						<span class="branda-edit"><?php esc_html_e( 'Update', 'ub' ); ?></span>
					</span>
					<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
				</button>
			</div>
		</div>
	</div>
</div>

