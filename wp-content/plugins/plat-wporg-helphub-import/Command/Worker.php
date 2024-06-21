<?php

namespace Platform\Translate\WPOrgHelpHubImport\Command;

use Exception;
use Platform\Logger\Logger;
use Platform\Translate\WPOrgHelpHubImport\Service\Article;
use WP_CLI;
use WP_CLI_Command;

defined( 'ABSPATH' ) || exit;

class Worker extends WP_CLI_Command {
	public function __construct() {
		parent::__construct();

		try {
			WP_CLI::add_command( 'platform helphub_import', __NAMESPACE__ . '\Worker' );
		} catch ( Exception $e ) {
			Logger::error( Logger::STORE, '注册 WP-CLI 命令失败', [ 'error' => $e->getMessage() ] );
		}
	}

	/**
	 * 拉取并导入所有文章
	 * @return void
	 */
	public function sync_all(): void {
		$service = new Article();
		$service->job();


		WP_CLI::line( '搞定！' );
	}
}
