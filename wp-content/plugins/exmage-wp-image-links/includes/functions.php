<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! function_exists( 'exmage_remove_filter' ) ) {
	/**
	 * Remove an anonymous object filter.
	 *
	 * @param string $tag Hook name.
	 * @param string $class Class name
	 * @param string $method Method name
	 *
	 * @return void
	 */
	function exmage_remove_filter( $tag, $class, $method ) {
		$filters = $GLOBALS['wp_filter'][ $tag ];

		if ( empty ( $filters ) ) {
			return;
		}

		foreach ( $filters as $priority => $filter ) {
			foreach ( $filter as $identifier => $function ) {
				if ( is_array( $function ) && is_array( $function['function'] ) && is_a( $function['function'][0], $class ) && $method === $function['function'][1] ) {
					remove_filter(
						$tag,
						array( $function['function'][0], $method ),
						$priority
					);
				}
			}
		}
	}
}