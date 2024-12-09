<script type="text/html" id="tmpl-menu-tab">
	<button type="button"
	        role="tab"
	        id="branda-custom-admin-menu-tab-{{ data.key }}"
	        data-menu-key="{{ data.key }}"
	        data-content="branda-custom-admin-menu-tab-content-{{ data.key }}"
	        class="sui-tab-item <# if(data.is_active) { #>active<# } #>"
	        aria-controls="branda-custom-admin-menu-tab-content-{{ data.key }}"
	        aria-selected="false"
	        tabindex="-1">
		{{ data.label }}

		<# if(data.is_deletion_allowed) { #>
			<span><i class="sui-icon-close" aria-hidden="true"></i></span>
		<# } #>
	</button>
</script>
