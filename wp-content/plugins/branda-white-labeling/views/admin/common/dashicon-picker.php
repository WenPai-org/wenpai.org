<?php
$list = empty( $list ) ? array() : $list;
?>
<script type="text/html" id="tmpl-dashicon-picker">
	<div class="branda-dashicon-picker">
		<div class="branda-dashicon-picker-search">
			<i class="sui-icon-magnifying-glass-search" aria-hidden="true"></i>

			<div class="sui-form-field">
				<input type="text"
					   placeholder="<?php esc_attr_e( 'Search icon', 'ub' ); ?>"
					   class="sui-form-control">
			</div>
		</div>

		<div class="branda-dashicon-picker-icons">
			<?php foreach ( $list as $group_id => $group ) : ?>
				<div class="branda-dashicon-picker-group">
					<label class="sui-label">
						<?php echo esc_html( $group['title'] ); ?>
					</label>

					<div class="branda-dashicon-picker-group-inner">
						<?php foreach ( $group['icons'] as $code => $class ) : ?>
							<span data-code="<?php echo esc_attr( $class ); ?>"
								  class="dashicons dashicons-<?php echo esc_attr( $class ); ?>">
							</span>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</script>
