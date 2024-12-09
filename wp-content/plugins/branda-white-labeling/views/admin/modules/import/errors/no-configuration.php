<div class="sui-header branda-import-error">
	<?php $this->render( 'admin/modules/import/header' ); ?>
	<?php
	echo Branda_Helper::sui_notice( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		sprintf(
			esc_html__( 'The file %s you are trying to import doesn’t have any module configurations. Please check your file or upload another file.', 'ub' ),
			'<b>' . esc_html( $filename ) . '</b>'
		)
	);
	?>
</div>
<?php $this->render( 'admin/modules/import/errors/footer', array( 'cancel_url' => $cancel_url ) ); ?>
