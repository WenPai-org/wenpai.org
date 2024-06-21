<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <title><?php echo gp_title(); ?></title>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php wp_head(); ?>
    <!-- footer 置底 -->
    <style>
        .gp-content {
            min-height: 80vh;
        }
    </style>
</head>

<body <?php body_class( 'js' ); ?>>
<?php block_header_area(); ?>

<div class="gp-content">
<?php echo gp_breadcrumb(); ?>

    <div id="gp-js-message" class="gp-js-message"></div>

<?php if ( gp_notice( 'error' ) ) : ?>
    <div class="error">
		<?php echo gp_notice( 'error' ); ?>
    </div>
<?php endif; ?>

<?php if ( gp_notice() ) : ?>
    <div class="notice">
		<?php echo gp_notice(); ?>
    </div>
<?php endif; ?>

<?php
/**
 * Fires after the error and notice elements on the header.
 *
 * @since 1.0.0
 */
do_action( 'gp_after_notices' );
