<?php
$template_id = empty( $template_id ) ? '' : $template_id;
$input_name  = empty( $input_name ) ? '' : $input_name;
?>
<script type="text/html" id="tmpl-<?php echo esc_attr( $template_id ); ?>">
	<div class="sui-upload <# if(data.filename) { #>sui-has_file<# } #>">

		<div class="sui-upload-image"
			aria-hidden="true">
			<div class="sui-image-mask"></div>
			<div role="button" class="sui-image-preview" style="background-image: url('{{ data.url }}');"></div>
		</div>

		<button type="button" class="sui-upload-button">
			<i class="sui-icon-upload-cloud" aria-hidden="true"></i> <?php esc_html_e( 'Upload image', 'ub' ); ?>
		</button>

		<div class="sui-upload-file">
			<span>{{ data.filename }}</span>
			<button type="button" class="sui-upload-button--remove"
				aria-label="<?php esc_attr_e( 'Remove file', 'ub' ); ?>">
				<i class="sui-icon-close" aria-hidden="true"></i>
			</button>
		</div>

		<input type="hidden" class="attachment-id" value="{{ data.id }}" name="<?php echo esc_attr( $input_name ); ?>"/>
	</div>

	<?php echo Branda_Helper::sui_inline_notice( 'branda-only-images' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</script>
