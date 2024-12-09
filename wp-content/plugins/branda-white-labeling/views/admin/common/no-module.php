<?php
/**
 * Wrong module template.
 *
 * @since 3.0.7
 */
?>
<div class="sui-box" data-tab="unknown">
	<div class="sui-box-header">
		<h2 class="sui-box-title"><?php esc_html_e( 'Unknown module', 'ub' ); ?></h2>
	</div>
	<div class="sui-box-body">
		<?php
			echo Branda_Helper::sui_notice( esc_html__( 'Selected module does not exists or is not available.', 'ub' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</div>
</div>

