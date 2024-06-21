<?php

namespace Platform\Translate\GeneratePack\Command;

use Exception;
use Platform\Logger\Logger;
use Platform\Translate\GeneratePack\Service\Pack;
use WP_CLI_Command;
use WP_CLI;

class Worker extends WP_CLI_Command {

	public function __construct() {
		parent::__construct();
		try {
			WP_CLI::add_command( 'platform translate_pack', __NAMESPACE__ . '\Worker' );
		} catch ( Exception $e ) {
			Logger::error( Logger::STORE, '注册 WP-CLI 命令失败', [ 'error' => $e->getMessage() ] );
		}
	}

	/**
	 * 全量打包
	 * @return void
	 */
	public function all(): void {
		$pack = new Pack();
		$pack->generate_all_pack();

		WP_CLI::line( '跑完了' );
	}
}
