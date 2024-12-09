<?php
$classes   = empty( $classes ) ? array() : $classes;
$slug      = empty( $slug ) ? '' : $slug;
$current   = empty( $current ) ? '' : $current;
$box_title = empty( $box_title ) ? '' : $box_title;
$module    = empty( $module ) ? array() : $module;
$buttons   = empty( $buttons ) ? '' : $buttons;
?>

<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
	 data-tab="<?php echo esc_attr( sanitize_title( $slug ) ); ?>"<?php echo $current === $slug ? '' : ' style="display: none;"'; ?>>
	<div class="sui-box-header">
		<h2 class="sui-box-title"><?php echo esc_html( $box_title ); ?>
											 <?php
												if ( ! empty( $module['only_pro'] ) ) {
													echo '&nbsp;&nbsp;' . Branda_Helper::maybe_pro_tag(); }
												?>
		</h2>
		<?php echo apply_filters( 'branda_settings_after_box_title', '', $module ); ?>
	</div>

	<div class="sui-box-body">
		<p><?php echo $module['description']; ?></p>
	</div>

	<div class="sui-box-footer">
		<?php echo $buttons; ?>
	</div>
</div>
