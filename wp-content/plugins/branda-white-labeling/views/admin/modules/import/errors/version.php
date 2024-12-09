<div class="sui-header branda-import-error">
	<h1 class="sui-header-title"><?php esc_html_e( 'Import Error', 'ub' ); ?></h1>
	<?php
	echo Branda_Helper::sui_notice( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		sprintf(
			esc_html__( 'The file you are trying to import is for %1$s version %2$s. Please upgrade source site to Branda and export again.', 'ub' ),
			sprintf(
				'<b>%s</b>',
				esc_html( $product )
			),
			sprintf(
				'<b>%s</b>',
				esc_html( $version )
			)
		)
	);
	?>
</div>
<?php $this->render( 'admin/modules/import/errors/footer', array( 'cancel_url' => $cancel_url ) ); ?>
