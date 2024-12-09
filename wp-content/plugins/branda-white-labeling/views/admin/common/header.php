<div class="sui-header">
	<h1 class="sui-header-title"><?php echo esc_html( $title ); ?></h1>
	<div class="sui-actions-right">
<?php if ( $show_manage_all_modules_button ) { ?>
		<button class="sui-button" type="button" data-modal-open="branda-manage-all-modules" data-modal-mask="true"><?php echo esc_html_x( 'Manage All Modules', 'button', 'ub' ); ?></button>
<?php } ?>
<?php if ( $documentation_chapter && ! empty( $helps ) ) : ?>
		<a target="_blank" class="sui-button sui-button-ghost"
		   href="https://wpmudev.com/docs/wpmu-dev-plugins/branda/?utm_source=branda&utm_medium=plugin&utm_campaign=branda_<?php echo esc_attr( $documentation_chapter ); ?>_docs#<?php echo esc_attr( $documentation_chapter ); ?>">
			<i class="sui-icon-academy"></i>
			<?php esc_html_e( 'View Documentation', 'ub' ); ?>
		</a>
<?php endif; ?>
	</div>
</div>
