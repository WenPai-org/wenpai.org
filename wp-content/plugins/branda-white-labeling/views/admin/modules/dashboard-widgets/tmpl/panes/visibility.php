<script type="text/html" id="tmpl-<?php echo esc_attr( $dialog_id ); ?>-pane-visibility">
<div class="sui-form-field branda-visibility-site">
	<label for="branda-visibility-site{{data.id}}" class="sui-label"><?php esc_html_e( 'Site Dashboard', 'ub' ); ?></label>
	<span class="sui-description"><php esc_html_e( 'Choose whether to show this text widget on site dashboard or not.', 'ub' ); ?></span>
	<div class="sui-side-tabs sui-tabs">
		<div class="sui-tabs-menu">
			<label class="sui-tab-item<# if ( 'on' === data.site ) { #> active<# } #>"><input type="radio" name="branda[site]" value="on" data-name="site" data-tab-menu="branda-dashboard-widgets-site{{data.id}}-on"<# if ( 'on' === data.site ) { #> checked="checked"<# } #>><?php esc_html_e( 'Show', 'ub' ); ?></label>
			<label class="sui-tab-item<# if ( 'off' === data.site ) { #> active<# } #>"><input type="radio" name="branda[site]" value="off" data-name="site" data-tab-menu="branda-dashboard-widgets-site{{data.id}}-off"<# if ( 'off' === data.site ) { #> checked="checked"<# } #>><?php esc_html_e( 'Hide', 'ub' ); ?></label>
		</div>
	</div>
</div>
<?php if ( $this->is_network && is_network_admin() ) { ?>
<div class="branda-divider"></div>
<div class="sui-form-field branda-visibility-network">
	<label for="branda-visibility-network{{data.id}}" class="sui-label"><?php esc_html_e( 'Network Dashboard', 'ub' ); ?></label>
	<span class="sui-description"><?php esc_html_e( 'Choose whether to show this text widget on the network dashboard or not.', 'ub' ); ?></span>
	<div class="sui-side-tabs sui-tabs">
		<div class="sui-tabs-menu">
			<label class="sui-tab-item<# if ( 'on' === data.network ) { #> active<# } #>"><input type="radio" name="branda[network]" value="on" data-name="network" data-tab-menu="branda-dashboard-widgets-network{{data.id}}-on"<# if ( 'on' === data.network ) { #> checked="checked"<# } #>><?php esc_html_e( 'Show', 'ub' ); ?></label>
			<label class="sui-tab-item<# if ( 'off' === data.network ) { #> active<# } #>"><input type="radio" name="branda[network]" value="off" data-name="network" data-tab-menu="branda-dashboard-widgets-network{{data.id}}-off"<# if ( 'off' === data.network ) { #> checked="checked"<# } #>><?php esc_html_e( 'Hide', 'ub' ); ?></label>
		</div>
	</div>
</div>
<?php } ?>
</script>
