<?php

namespace Platform\Token\Service;

defined( 'ABSPATH' ) || exit;

class Base {

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	public function rest_api_init(): void {
		new Token();
	}
}
