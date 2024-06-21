<?php

namespace WordPressdotorg\GlotPress\Routes\Routes;

use GP_Route;

/**
 * Maintenance Route Class.
 */
class Maintenance extends GP_Route {

	public function show_maintenance_message() {
		wp_die( '我们正在进行计划中维护，请在约 30 分钟后再尝试访问。', '维护中', [ 'response' => 503 ] );
	}
}
