<?php

namespace Platform\API\API;

use Platform\API\API\Plugins\UpdateCheck as Plugins_Update_Check;
use Platform\API\API\Plugins\Info as Plugins_Info;
use Platform\API\API\Themes\Info as Themes_Info;
use Platform\API\API\Themes\UpdateCheck as Themes_Update_Check;
use Platform\API\API\Patterns\Index as Patterns_Index;
use Platform\API\API\Translations\Index as Translations_Index;
use Platform\API\API\Core\VersionCheck as Core_Version_Check;
use Platform\API\API\Core\StableCheck as Core_Stable_Check;
use Platform\API\API\Core\Checksums as Core_Checksums;
use Platform\API\API\Core\Credits as Core_Credits;
use Platform\API\API\Core\Importers as Core_Importers;
use Platform\API\API\Core\Happy as Core_Happy;
use Platform\API\API\Core\Handbook as Core_Handbook;
use Platform\API\API\ChinaYes\VersionCheck as China_Yes_Version_Check;

class Base {

	/**
	 * 初始化 API
	 */
	public static function init(): void {
		self::load_routes();
		self::load_fields();
	}

	/**
	 * 引入所有 API 端点
	 */
	public static function load_routes(): void {
		new Plugins_Info();
		new Themes_Info();
		new Plugins_Update_Check();
		new Themes_Update_Check();
		new Patterns_Index();
		new Translations_Index();
		new Core_Version_Check();
		new Core_Stable_Check();
		new Core_Checksums();
		new Core_Credits();
		new Core_Importers();
		new Core_Happy();
		new Core_Handbook();
		new China_Yes_Version_Check();
	}

	/**
	 * 引入所有 API 字段
	 */
	public static function load_fields() {

	}

	protected function success() {

	}

	protected function error( array $data, int $status_code = 500 ): void {
		wp_send_json_error( $data, $status_code, JSON_UNESCAPED_UNICODE );
	}

}
