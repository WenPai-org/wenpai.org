<?php
/**
 * Displays element to paginate modules in Email Logs
 *
 * @package Branda
 */

if ( $total <= $limit ) {
	return;
}
// don't use filter_input() here.
$paged = ! empty( $_GET['paged'] ) ? (int) $_GET['paged'] : 1; // phpcs:ignore
$args             = array( 'module' => $module );
$base_url         = add_query_arg( $args, remove_query_arg( 'paged' ) );
$max              = (int) ceil( $total / $limit );
$short_pagination = 5 > $max;
?>
<ul class="sui-pagination">
<?php
/**
 * Conditions:
 * 1. Show this button if there are 5 pages or more.
 * 2. Hide this button if first page is current page.
 */
if ( ! $short_pagination ) {
	/**
	 * ELEMENT: Skip to first page
	 */
	?>
	<li class="sui-pagination--start">
		<a href="<?php echo esc_url( $base_url ); ?>" <?php disabled( $paged <= 1 ); ?>>
			<span class="sui-icon-arrow-skip-start" aria-hidden="true"></span>
			<span class="sui-screen-reader-text"><?php esc_html_e( 'Go to first page', 'ub' ); ?></span>
		</a>
	</li>

	<?php
	/**
	 * ELEMENT: Go to previous page
	 */
	$u = add_query_arg( 'paged', $paged - 1, $base_url );
	?>
	<li class="sui-pagination--next">
		<a href="<?php echo esc_url( $u ); ?>" <?php disabled( $paged <= 1 ); ?>>
			<span class="sui-icon-chevron-left" aria-hidden="true"></span>
			<span class="sui-screen-reader-text"><?php esc_html_e( 'Go to previous page', 'ub' ); ?></span>
		</a>
	</li>
	<?php
}

/**
 * ELEMENT: List of pages
 *
 * 1. Use "sui-active" class to determine current page.
 */
$i = 1;
do {
	if ( 1 < $i ) {
		$u = add_query_arg( 'paged', $i, $base_url );
	} else {
		$u = $base_url;
	}
	if ( ! $short_pagination && ( $paged - 2 === $i && 1 !== $i || $paged + 2 === $i && $i !== $max ) ) {
		printf(
			'<li><a href="%s"> ... </a></li>',
			esc_url( $u )
		);
	} elseif ( $short_pagination || in_array( $i, range( $paged - 2, $paged + 2 ), true ) ) {
		printf(
			'<li><a class="%s" href="%s">%d</a></li>',
			esc_attr( $paged === $i ? 'sui-active' : '' ),
			esc_url( $u ),
			esc_html( $i )
		);
	}
	$i++;
} while ( $i <= $max );

/**
 * Conditions:
 * 1. Show this button if there are 5 pages or more.
 * 2. Hide this button if last page is current page.
 */
if ( ! $short_pagination ) {
	/**
	 * ELEMENT: Go to next page
	 */
	$u = add_query_arg( 'paged', $paged + 1, $base_url );
	?>
	<li class="sui-pagination--next">
		<a href="<?php echo esc_url( $u ); ?>" <?php disabled( $paged >= $max ); ?>>
			<span class="sui-icon-chevron-right" aria-hidden="true"></span>
			<span class="sui-screen-reader-text"><?php esc_html_e( 'Go to next page', 'ub' ); ?></span>
		</a>
	</li>
	<?php

	/**
	 * ELEMENT: Skip to last page
	 */
	$u = add_query_arg( 'paged', $max, $base_url );
	?>
	<li class="sui-pagination--end">
		<a href="<?php echo esc_url( $u ); ?>" <?php disabled( $paged >= $max ); ?>>
			<span class="sui-icon-arrow-skip-end" aria-hidden="true"></span>
			<span class="sui-screen-reader-text"><?php esc_html_e( 'Go to last page', 'ub' ); ?></span>
		</a>
	</li>
	<?php
}
?>
</ul>
