<script type="text/html" id="tmpl-<?php echo esc_attr( $dialog_id ); ?>-pane-general">
<div class="sui-row">
	<div class="sui-col">
		<div class="sui-form-field branda-general-title">
			<label for="branda-general-title-{{data.id}}" class="sui-label"><?php esc_html_e( 'Title', 'ub' ); ?></label>
			<input id="branda-general-title-{{data.id}}" type="text" name="branda[title]" value="{{data.title}}" data-default="" data-required="required" aria-describedby="input-description" class="sui-form-control">
			<span class="hidden"><?php esc_html_e( 'This field can not be empty!', 'ub' ); ?></span>
			<span class="sui-description"><?php esc_html_e( 'You can also paste the full URL of an image instead of text title. For e.g. http://example.com/img.png', 'ub' ); ?></span>
		</div>
	</div>
	<div class="sui-col">
		<div class="sui-form-field">
			<label for="branda-general-icon-{{data.id}}" class="sui-label"><?php esc_html_e( 'Icon', 'ub' ); ?></label>
<?php echo $icons; ?>
			<span class="sui-description"><?php esc_html_e( 'Choose an icon for your custom menu item.', 'ub' ); ?></span>
		</div>
	</div>
</div>
<div class="sui-form-field branda-general-url">
	<label for="branda-general-url-{{data.id}}" class="sui-label"><?php esc_html_e( 'Link to', 'ub' ); ?></label>
	<div class="sui-side-tabs sui-tabs">
		<div class="sui-tabs-menu">
			<label class="sui-tab-item<# if ( 'none' === data.url ) { #> active<# } #>"><input type="radio" name="branda[url]" value="none" data-name="url" data-tab-menu="branda-admin-bar-url-{{data.id}}-none" data-default="none"<# if ( 'none' === data.url ) { #> checked="checked"<# } #>><?php esc_html_e( 'None', 'ub' ); ?></label>
			<label class="sui-tab-item<# if ( 'main' === data.url ) { #> active<# } #>">
				<input type="radio" name="branda[url]" value="main" data-name="url" data-tab-menu="branda-admin-bar-url-{{data.id}}-main" data-default="none"<# if ( 'main' === data.url ) { #> checked="checked"<# } #>><?php esc_html_e( 'Main Site', 'ub' ); ?>
			</label>
<# if ( data.is_network ) { #>
			<label class="sui-tab-item<# if ( 'current' === data.url ) { #> active<# } #>">
				<input type="radio" name="branda[url]" value="current" data-name="url" data-tab-menu="branda-admin-bar-url-{{data.id}}-current" data-default="none"<# if ( 'current' === data.url ) { #> checked="checked"<# } #>><?php esc_html_e( 'Current Site', 'ub' ); ?>
			</label>
<# } #>
			<label class="sui-tab-item<# if ( 'wp-admin' === data.url ) { #> active<# } #>">
				<input type="radio" name="branda[url]" value="wp-admin" data-name="url" data-tab-menu="branda-admin-bar-url-{{data.id}}-wp-admin" data-default="none"<# if ( 'wp-admin' === data.url ) { #> checked="checked"<# } #>><?php esc_html_e( 'Admin Area', 'ub' ); ?>
			</label>
			<label class="sui-tab-item<# if ( 'custom' === data.url ) { #> active<# } #>">
				<input type="radio" name="branda[url]" value="custom" data-name="url" data-tab-menu="branda-admin-bar-url-{{data.id}}-custom" data-default="none"<# if ( 'custom' === data.url ) { #> checked="checked"<# } #>><?php esc_html_e( 'Custom URL', 'ub' ); ?>
			</label>
		</div>
	</div>
</div>
<div class="sui-border-frame branda-admin-bar-url-options<# if ( 'none' === data.url ) { #> hidden<# } #>">
	<div class="sui-form-field branda-general-custom<# if ( 'custom' !== data.url ) {#> hidden<# } #>">
		<label for="branda-general-custom-{{data.id}}" class="sui-label"><?php esc_html_e( 'URL', 'ub' ); ?></label>
		<input id="branda-general-custom-{{data.id}}" type="text" name="branda[custom]" value="{{data.custom}}" data-default="" data-required="no" placeholder="<?php esc_attr_e( 'E.g. http://example.com', 'ub' ); ?>" aria-describedby="input-description" class="sui-form-control">
	</div>
	<div class="sui-form-field branda-general-target">
		<label for="branda-general-target-{{data.id}}" class="sui-label"><?php esc_html_e( 'Open link in', 'ub' ); ?></label>
		<div class="sui-side-tabs sui-tabs">
			<div class="sui-tabs-menu">
				<label class="sui-tab-item<# if ( 'new' === data.target ) { #> active<# } #>">
					<input type="radio" name="branda[target]" value="new" data-name="target" data-tab-menu="branda-admin-bar-target-{{data.id}}-new" data-default="current"<# if ( 'new' === data.target ) { #> checked="checked"<# } #>><?php esc_html_e( 'New Tab', 'ub' ); ?></label>
				<label class="sui-tab-item<# if ( 'current' === data.target ) { #> active<# } #>">
					<input type="radio" name="branda[target]" value="current" data-name="target" data-tab-menu="branda-admin-bar-target-{{data.id}}-current" data-default="current"<# if ( 'current' === data.target ) { #> checked="checked"<# } #>><?php esc_html_e( 'Same Tab', 'ub' ); ?></label>
			</div>
		</div>
	</div>
</div>
</script>
