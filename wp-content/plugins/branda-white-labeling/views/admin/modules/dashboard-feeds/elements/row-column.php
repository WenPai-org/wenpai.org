<div class="sui-col-md-4">
	<span class="branda-list-title"><?php echo esc_html( $data['label'] ); ?></span>
</div>
<div class="sui-col-md-8">
<?php
if ( isset( $data['type'] ) && 'sui-tab' === $data['type'] ) {
	echo isset( $item[ $id ] ) && isset( $data['options'][ $item[ $id ] ] ) ? '<span class="branda-list-detail">' . $data['options'][ $item[ $id ] ] . '</span>' : '&ndash;';
} elseif ( isset( $item[ $id ] ) ) {
	$value = $item[ $id ];
	if (
		'title' === $id
		&& isset( $item['link'] )
		&& ! empty( $item['link'] )
	) {
		$value = sprintf(
			'<a href="%s">%s</a>',
			esc_url( $item['link'] ),
			$value
		);
	}
	echo '<span class="branda-list-detail">' . $value . '</span>';
} else {
	echo '&ndash;';
}
?>
</div>

