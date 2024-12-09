<script type="text/html" id="tmpl-simple-options-media">
<div class="sui-upload image-wrapper {{{data.container_class}}}" data-id="{{{data.id}}}" data-section_key="{{{data.section_key}}}">
	<div class="sui-upload-image" aria-hidden="true">
		<div class="sui-image-mask"></div>
		<div role="button" class="sui-image-preview" style="background-image: url('{{{data.image_src}}}');"></div>
	</div>
	<button type="button" class="sui-upload-button button-select-image"><i class="sui-icon-upload-cloud" aria-hidden="true"></i> <?php echo esc_html( $button ); ?></button>
	<div class="sui-upload-file">
		<span>{{{data.file_name}}}</span>
		<button type="button" class="sui-upload-button--remove image-reset" aria-label="<?php echo esc_attr( $label ); ?>"><i class="sui-icon-close" aria-hidden="true"></i></button>
	</div>
	<input type="hidden" name="simple_options[{{{data.section_key}}}][{{{data.id}}}][]" value="{{{data.value}}}" class="attachment-id" />
</div>
<?php echo Branda_Helper::sui_inline_notice( 'branda-only-images' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</script>
