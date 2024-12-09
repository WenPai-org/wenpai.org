<script type="text/html" id="tmpl-<?php echo esc_attr( $dialog_id ); ?>-pane-visibility">
<div class="sui-form-field branda-visibility-roles">
	<label for="branda-visibility-roles-{{data.id}}" class="sui-label"><?php esc_html_e( 'User Roles', 'ub' ); ?></label>
	<span class="sui-description"><?php esc_html_e( 'Select the user roles which are allowed to see this menu.', 'ub' ); ?></span>
<#
var i = 0;
var columns = 6;
_.each( data.available_roles, function( value, key, obj ) {
	if ( 0 === i % ( 12 / columns ) ) {
		if ( 0 < i ) {
			#></div><#
		}
		#><div class="sui-row"><#
	}
#>
		<div class="sui-col-md-{{columns}}">
			<label class="sui-checkbox">
				<input
					type="checkbox"
					name="branda[roles][{{key}}]"
					value="{{key}}"
					<# if ( 'undefined' !== typeof data.roles[key] ) { #>checked="checked"<# } #>
				>
				<span></span>
				<span>{{value}}</span>
			</label>
		</div>
<#
	i++;
});
if ( 0 !== i % ( 12 / columns ) ) {
	#></div><#
}
#>
</div>
<div class="sui-form-field branda-visibility-mobile<# if ( '' === data.icon ) { #> hidden<# } #>">
	<label for="branda-visibility-mobile-{{data.id}}" class="sui-label"><?php esc_html_e( 'Show on Mobile', 'ub' ); ?></label>
	<span class="sui-description"><?php esc_html_e( 'Show menu element icon on mobile.', 'ub' ); ?></span>
	<div class="sui-side-tabs sui-tabs">
		<div class="sui-tabs-menu">
			<label class="sui-tab-item<# if ( 'show' === data.mobile ) { #> active<# } #>">
				<input type="radio" name="branda[mobile]" value="show" data-name="mobile" data-tab-menu="branda-admin-bar-mobile-{{data.id}}-show" data-default="current"<# if ( 'show' === data.mobile ) { #> checked="checked"<# } #>><?php esc_html_e( 'Show', 'ub' ); ?></label>
			<label class="sui-tab-item<# if ( 'hide' === data.mobile ) { #> active<# } #>">
				<input type="radio" name="branda[mobile]" value="hide" data-name="mobile" data-tab-menu="branda-admin-bar-mobile-{{data.id}}-hide" data-default="hide"<# if ( 'hide' === data.mobile ) { #> checked="checked"<# } #>><?php esc_html_e( 'Hide', 'ub' ); ?></label>
		</div>
		<div class="sui-tabs-content">
			<div class="sui-tab-boxed <# if ( 'show' === data.mobile ) { #>active<# } #>" data-tab-content="branda-admin-bar-mobile-{{{data.id}}}-show">
				<?php
				echo Branda_Helper::sui_notice( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					esc_html__( 'Make sure you\'ve set an icon for this menu item in the GENERAL tab as only the menu icon is visible on mobile devices.', 'ub' ),
					'default'
				);
				?>
			</div>
		</div>
	</div>
</div>
</script>
