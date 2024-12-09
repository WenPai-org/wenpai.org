<?php
/**
 * Upsell meta box on dashboard page.
 *
 * @since 3.3.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $uba;
?>
<div id="branda-dashboard-widget-upsell" class="sui-box sui-box-close">
	<div class="sui-box-header">
		<h3 class="sui-box-title"><span class="dashicons branda_u_logo" style="background-image: url('<?php echo esc_url( branda_url( 'assets/images/branda/branda-logo.svg' ) ); ?>');"></span><?php esc_html_e( 'Branda Pro', 'ub' ); ?>
		<?php echo Branda_Helper::maybe_pro_tag(); ?></h3>
	</div>

	<div class="sui-box-body">
		<p><?php esc_html_e( 'Get our WordPress full white labeling with Branda Pro and additional benefits of WPMU DEV membership.', 'ub' ); ?></p>

		<ul>
			<li><i class="sui-icon-check sui-lg" aria-hidden="true"></i> <?php esc_html_e( 'Branda Pro white labeling for unlimited sites', 'ub' ); ?></li>
			<li><i class="sui-icon-check sui-lg" aria-hidden="true"></i> <?php esc_html_e( 'SMTP email logging', 'ub' ); ?></li>
			<li><i class="sui-icon-check sui-lg" aria-hidden="true"></i> <?php esc_html_e( 'Early access to new white label branding features', 'ub' ); ?></li>
			<li><i class="sui-icon-check sui-lg" aria-hidden="true"></i> <?php esc_html_e( 'Smush and Hummingbird Pro performance upgrade', 'ub' ); ?></li>
			<li><i class="sui-icon-check sui-lg" aria-hidden="true"></i> <?php esc_html_e( 'Marketing suite – Forms, pop-ups, email and more', 'ub' ); ?></li>
			<li><i class="sui-icon-check sui-lg" aria-hidden="true"></i> <?php esc_html_e( 'Customizable Google Analytics dashboards', 'ub' ); ?></li>
			<li><i class="sui-icon-check sui-lg" aria-hidden="true"></i> <?php esc_html_e( 'Manage unlimited WordPress sites from the Hub', 'ub' ); ?></li>
			<li><i class="sui-icon-check sui-lg" aria-hidden="true"></i> <?php esc_html_e( '24/7 live WordPress support', 'ub' ); ?></li>
			<li><i class="sui-icon-check sui-lg" aria-hidden="true"></i> <?php esc_html_e( 'The WPMU DEV Guarantee', 'ub' ); ?></li>
		</ul>

		<a href="https://wpmudev.com/project/ultimate-branding/?utm_source=branda&utm_medium=plugin&utm_campaign=branda_dashboard_upsellwidget_button" class="sui-button sui-button-purple" target="_blank">
			<?php esc_html_e( 'Upgrade to Pro', 'ub' ); ?>
		</a>
	</div>

</div>
