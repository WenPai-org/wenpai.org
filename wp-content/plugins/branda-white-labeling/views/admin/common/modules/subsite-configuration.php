<?php
/**
 * Wrong module template.
 *
 * @since 3.2.0
 */
?>
<div class="sui-box-body">
<?php
	echo Branda_Helper::sui_notice( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		sprintf(
			__( 'This module can be overridden by subsite admins as per your <a href="%s">Permissions Settings</a>.', 'ub' ),
			esc_url( $url )
		),
		'info'
	);
	?>
</div>

