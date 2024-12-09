<div class="sui-box sui-summary">
	<div class="sui-summary-image-space" aria-hidden="true"></div>
	<div>
		<?php
		echo Branda_Helper::sui_notice( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			''
			. esc_html__( 'This section has been disabled because Defender is configured to remove the generator information from your site for security reasons. However, if you still want to enable the generator information on your site and want to further customize it, change the generator information settings on Defender.', 'ub' ) .
			'<br><br><a class="sui-button sui-button-ghost" href="' . esc_url( $link ) . '">' . esc_html_x( 'Defender Settings', 'button', 'ub' ) . '</a>',
			'warning'
		);
		?>
	</div>
</div>
