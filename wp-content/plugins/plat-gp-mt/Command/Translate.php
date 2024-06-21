<?php
/**
 * AI 翻译指定内容
 *
 * 这是一个 Cli 命令行程序，由管理员在使用时手工调用
 */

namespace Platform\Translate\MachineTranslate\Command;

use Exception;
use GP;
use Platform\Logger\Logger;
use WP_CLI_Command;
use WP_CLI;
use Platform\Translate\MachineTranslate\Service\Translate as TranslateService;

class Translate extends WP_CLI_Command {
	public function __construct() {
		parent::__construct();
		try {
			WP_CLI::add_command( 'platform machine_translate', __NAMESPACE__ . '\Translate' );
		} catch ( Exception $e ) {
			Logger::error( Logger::TRANSLATE, '注册 WP-CLI 命令失败', [ 'error' => $e->getMessage() ] );
		}
	}

	public function translate( $args, $assoc_args ): void {
		if ( ! isset( $assoc_args['slug'] ) ) {
			WP_CLI::line( '你需要给出slug：readme or body' );
			exit;
		}

		$this->worker( $assoc_args['slug'] );

		WP_CLI::line( '恭喜你，执行成功' );
	}

	/**
	 * @param string $slug
	 */
	private function worker( string $slug ): void {
		// 取出全部项目
		$projects = GP::$project->find_many( array(
			'slug' => $slug,
		) );

		// 循环全部项目，开始翻译
		foreach ( $projects as $project ) {

			$project_id = $project->id;
			$project    = GP::$project->find_one( array( 'id' => $project_id ) )->fields();

			// 获取待翻译原文
			$site_id = SITE_ID_TRANSLATE;
			$sql     = <<<SQL
select *
from wp_{$site_id}_gp_originals
where project_id = {$project_id}
  and id not in (
    select original_id
    from wp_{$site_id}_gp_translations
    where translation_set_id = (
        select id
        from wp_{$site_id}_gp_translation_sets
        where project_id = {$project_id}
    )
)
  and status = '+active';
SQL;

			$originals = GP::$original->many( $sql );

			$translate    = new TranslateService();
			$translations = $translate->web( $project_id, $originals );
			Logger::info( Logger::TRANSLATE, 'ID为：' . $project_id . ' 的项目 ' . $slug . ' 翻译成功' );
		}

	}

}
