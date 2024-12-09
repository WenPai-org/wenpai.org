<div class="sui-upload">
	<input type="file" value="" class="branda-upload" name="<?php echo esc_attr( $field_name ); ?>" />
	<button type="button" class="sui-upload-button"><i class="sui-icon-upload-cloud" aria-hidden="true"></i> <?php esc_html_e( 'Upload file', 'ub' ); ?></button>
	<div class="sui-upload-file">
		<span></span>
		<button type="button" aria-label="<?php esc_attr_e( 'Remove file', 'ub' ); ?>"> <i class="sui-icon-close" aria-hidden="true"></i></button>
	</div>
</div>
<?php
echo Branda_Helper::sui_inline_notice( 'branda-wrong-filetype' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
