<?php
/**
 * Title: Cover section with text, buttons
 * Slug: powder/hero-cover-text-buttons-dark
 * Categories: powder-hero
 */
?>
<!-- wp:cover {"overlayColor":"contrast","isUserOverlayColor":true,"minHeight":600,"align":"full","style":{"spacing":{"blockGap":"10px","margin":{"top":"0"}}},"textColor":"base","className":"is-style-default","layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-cover alignfull is-style-default has-base-color has-text-color" style="margin-top:0;min-height:600px"><span aria-hidden="true" class="wp-block-cover__background has-contrast-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container">
	<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|x-small"}},"layout":{"type":"constrained","wideSize":"880px","justifyContent":"left"}} -->
	<div class="wp-block-group">
		<!-- wp:heading {"level":1,"style":{"typography":{"fontStyle":"normal","fontWeight":"300"}},"fontSize":"max-60"} -->
		<h1 class="wp-block-heading has-max-60-font-size" style="font-style:normal;font-weight:300"><?php echo esc_html__( 'World-class interior design studio based in Laguna Beach.', 'powder' ); ?></h1>
		<!-- /wp:heading -->
		<!-- wp:group {"style":{"spacing":{"blockGap":"10px"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group">
			<!-- wp:separator -->
			<hr class="wp-block-separator has-alpha-channel-opacity"/>
			<!-- /wp:separator -->
			<!-- wp:paragraph {"style":{"typography":{"lineHeight":"1"}}} -->
			<p style="line-height:1"><?php echo esc_html__( 'Transform your space with our stylish design solutions.', 'powder' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
		<!-- wp:buttons {"layout":{"type":"flex"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|medium"}}}} -->
		<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--medium)">
			<!-- wp:button {"backgroundColor":"base","textColor":"contrast","style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast"}}}}} -->
			<div class="wp-block-button"><a class="wp-block-button__link has-contrast-color has-base-background-color has-text-color has-background has-link-color wp-element-button" href="#"><?php echo esc_html__( 'Start Project →', 'powder' ); ?></a></div>
			<!-- /wp:button -->
			<!-- wp:button {"className":"is-style-outline"} -->
			<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html__( 'Learn More', 'powder' ); ?></a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div></div>
<!-- /wp:cover -->
