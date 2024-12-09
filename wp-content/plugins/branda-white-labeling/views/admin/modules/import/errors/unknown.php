<div class="sui-header branda-import-error">
	<?php $this->render( 'admin/modules/import/header' ); ?>
	<?php
	echo Branda_Helper::sui_notice( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		sprintf(
			esc_html__( 'During uploading %s an unknown error occurred. Please try again.', 'ub' ),
			sprintf(
				'<b>%s</b>',
				esc_html( $filename )
			)
		)
	);
	?>
</div>
<?php
$this->render( 'admin/modules/import/errors/footer', array( 'cancel_url' => $cancel_url ) );
