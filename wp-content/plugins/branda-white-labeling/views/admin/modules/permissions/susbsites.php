<?php
if ( empty( $groups ) ) {
	echo Branda_Helper::sui_notice( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_html__( 'There is no module available with settings that can be overridden.', 'ub' )
	);
	return;
}
?>
<div class="sui-tabs">
	<div data-tabs>
<?php
$first   = true;
$counter = array();
foreach ( $groups as $group_key => $modules ) {
	$module = array_shift( $modules );
	if ( ! isset( $counter[ $group_key ] ) ) {
		$counter[ $group_key ] = array(
			'all'    => 0,
			'active' => 0,
		);
	}
	?>
		<div<?php echo $first ? ' class="active"' : ''; ?>><?php echo esc_html( $module['group_data']['title'] ); ?></div>
	<?php
	$first = false;
	foreach ( $modules as $id => $module ) {
		$counter[ $group_key ]['all']++;
		if ( $module['checked'] ) {
			$counter[ $group_key ]['active']++;
		}
	}
}
?>
	</div>
	<div data-panes>
<?php
$first = true;
foreach ( $groups as $group_key => $modules ) {
	?>
		<div<?php echo $first ? ' class="active"' : ''; ?>>
			<label class="sui-checkbox">
				<input type="checkbox" class="all"  <?php checked( $counter[ $group_key ]['all'], $counter[ $group_key ]['active'] ); ?> />
				<span aria-hidden="true"></span>
				<span><?php esc_html_e( 'All', 'ub' ); ?></span>
			</label>
			<div class="sui-box">
	<?php
	$first = false;
	$i     = 0;
	$cols  = 2;
	foreach ( $modules as $id => $module ) {
		if ( 0 === $i % $cols ) {
			?>
		<div class="sui-row">
			<?php
		}
		?>
		<div class="sui-col-md-6">
			<label class="sui-checkbox">
				<input type="checkbox" name="simple_options[subsites][items][]" value="<?php echo esc_attr( $module['key'] ); ?>" id="<?php echo esc_attr( $module['id'] ); ?>" <?php checked( $module['checked'] ); ?> />
				<span aria-hidden="true"></span>
				<span><?php echo esc_html( $module['title'] ); ?></span>
			</label>
		</div>
		<?php
		$i++;
		if ( 0 === $i % $cols ) {
			?>
		</div>
			<?php
		}
	}
	if ( 0 !== $i % $cols ) {
		?>
	</div>
		<?php
	}
	?>
			</div>
		</div>
<?php } ?>
	</div>
</div>
