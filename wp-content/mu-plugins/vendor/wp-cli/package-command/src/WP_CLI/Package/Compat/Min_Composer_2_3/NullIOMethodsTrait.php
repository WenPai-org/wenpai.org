<?php

namespace WP_CLI\Package\Compat\Min_Composer_2_3;

use WP_CLI;

trait NullIOMethodsTrait {
	/**
	 * {@inheritDoc}
	 */
	public function isVerbose(): bool {
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function write( $messages, bool $newline = true, int $verbosity = self::NORMAL ): void {
		self::output_clean_message( $messages );
	}

	/**
	 * {@inheritDoc}
	 */
	public function writeError( $messages, bool $newline = true, int $verbosity = self::NORMAL ): void {
		self::output_clean_message( $messages );
	}

	private static function output_clean_message( $messages ) {
		$messages = (array) preg_replace( '#<(https?)([^>]+)>#', '$1$2', $messages );
		foreach ( $messages as $message ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags
			WP_CLI::log( strip_tags( trim( $message ) ) );
		}
	}
}
