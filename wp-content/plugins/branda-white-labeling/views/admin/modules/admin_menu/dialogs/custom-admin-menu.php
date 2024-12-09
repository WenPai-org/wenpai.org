<script type="text/html" id="tmpl-custom-admin-menu">
	<div class="branda-custom-admin-menu branda-custom-admin-menu-{{ data.menu_key }}">
		<div class="branda-custom-admin-menu-builder-fields">
		</div>

		<div class="sui-box-footer">
			<button class="sui-button sui-button-ghost branda-discard-admin-menu-changes"
					type="button">
				<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>

				<span class="sui-loading-text">
					<i class="sui-icon-refresh" aria-hidden="true"></i>
					<?php esc_html_e( 'Discard All changes', 'ub' ); ?>
				</span>
			</button>

			<button class="sui-button branda-apply-admin-menu-changes"
					type="button">
				<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>

				<span class="sui-loading-text">
					<i class="sui-icon-check" aria-hidden="true"></i>
					<?php esc_html_e( 'Apply', 'ub' ); ?>
				</span>
			</button>
		</div>
	</div>
</script>
