<div class="updated admin-panel-tips" data-id="<?php echo esc_attr( $id ); ?>" data-user-id="<?php echo esc_attr( $user_id ); ?>">
	<p class="apt-action" data-nonce="<?php echo esc_attr( $nonce_dismiss ); ?>">[ <a href="#" ><?php esc_html_e( 'Dismiss', 'ub' ); ?></a> ]</p>
	<p class="apt-action" data-nonce="<?php echo esc_attr( $nonce_hide ); ?>">[ <a href="#" ><?php esc_html_e( 'Hide', 'ub' ); ?></a> ]</p>
<?php if ( ! empty( $title ) ) { ?>
	<h4><?php echo $title; ?></h4>
<?php } ?>
<?php if ( ! empty( $content ) ) { ?>
	<div class="apt-content"><?php echo $content; ?></div>
<?php } ?>
</div>

