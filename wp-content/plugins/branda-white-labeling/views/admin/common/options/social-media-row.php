<div class="sui-accordion-item">
	<div class="sui-accordion-item-header">
		<div for="simple_options_content_social_media_<?php echo esc_attr( $id ); ?>" class="sui-label sui-accordion-item-title">
			<i class="sui-icon-drag" aria-hidden="true"></i>
			<span class="social-logo social-logo-<?php echo esc_attr( $id ); ?>"></span> <?php echo esc_attr( $label ); ?>
		</div>
		<div class="sui-accordion-col-auto branda-social-logo-remove-container">
			<button type="button" class="sui-button-icon sui-button-red branda-social-logo-remove" data-id="<?php echo esc_attr( $id ); ?>"><i class="sui-icon-trash" aria-hidden="true"></i></button>
		</div>
		<div class="sui-accordion-col-auto">
			<button type="button" class="sui-button-icon sui-accordion-open-indicator" aria-label="<?php echo esc_attr( $buttom_open_label ); ?>"><i class="sui-icon-chevron-down" aria-hidden="true"></i></button>
		</div>
	</div>
	<div class="sui-accordion-item-body">
		<label class="sui-label"><?php echo esc_html( $field_label ); ?></label>
		<input type="text" id="simple_options_content_social_media_<?php echo esc_attr( $id ); ?>" name="simple_options[content][social_media_<?php echo esc_attr( $id ); ?>]" value="<?php echo esc_attr( $value ); ?>" class="sui-form-control" placeholder="<?php esc_attr_e( 'Type your profile URL here', 'ub' ); ?>"/>
	</div>
</div>

