<div class="sui-form-field">

	<p><?php esc_html_e( 'Find and add GET parameters from our list of popular page builders.', 'ub' ); ?></p>

	<label for="branda-popular-builders" id="branda-label-popular-builders" class="sui-label"><?php esc_html_e( 'Select GET  parameters', 'ub' ); ?></label>

	<select
		id="branda-popular-builders"
		class="sui-select sui-select-lg"
		data-placeholder="<?php esc_html_e( 'Type builder name here', 'ub' ); ?>"
		aria-labelledby="branda-label-popular-builders"
		multiple="multiple"
	>
		<?php
			$builders = apply_filters(
				'branda_admin_bar_popular_builders',
				array(
					'fl_builder'                    => 'Beaver Builder',
					'brizy-edit-iframe'             => 'Brizy',
					'elementor-preview'             => 'Elementor',
					'ct_builder'                    => 'Oxigen Builder',
					'siteorigin_panels_live_editor' => 'SiteOrigin Page Builder',
					'et_fb'                         => 'The Divi Builder',
					'tb-preview'                    => 'Themify Builder',
					'tve=true'                      => 'Thrive Architect',
					'vcv-action'                    => 'Visual Composer Website Builder',
					'vc_action=vc_inline'           => 'WPBakery Page Builder',
				)
			);

			foreach ( $builders as $key => $builder_name ) {
				echo '<option value="' . esc_attr( $key ) . '">'
						. esc_html( $builder_name . ' `' . $key . '`' )
						. '</option>';
			}
			?>
	</select>

</div>
