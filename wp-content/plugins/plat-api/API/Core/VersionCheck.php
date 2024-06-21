<?php

namespace Platform\API\API\Core;

use Platform\API\API\Base;
use Platform\API\Service\Core;
use Platform\API\Service\Translation;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class VersionCheck extends Base {

	public function __construct() {
		register_rest_route( 'core/version-check', '1.7', array(
			'methods'  => WP_REST_Server::ALLMETHODS,
			'callback' => array( $this, 'version_check' ),
		) );
	}

	public function version_check( WP_REST_Request $request ): WP_REST_Response {
		// 首先对输入数据消毒
		$params = $this->prepare_params( $request->get_params() );

		// 初始化核心服务
		$core_service = new Core();
		$updated_core = $core_service->update_check( $request->get_params() );

		/**
		 * 处理版本号
		 * WordPress 的版本机制比较混乱，除了标准的 6.4.3 这种格式外，还有 6.4 6.4.3-RC1 6.4.3-beta1 等等
		 * 而在翻译平台上主线版本号一律是 dev，旧版本则是 6.4.x 这种格式，需要进行一些特殊处理
		 */
		// 首先通过正则检查是否为正式版本号（6.4.3 6.4 这种格式），如果不是则默认为 dev
		$version = $params['version'] ?? 'dev';
		// 通过 - 分割版本号，只取前面的部分
		$version = explode( '-', $version )[0];
		if ( ! preg_match( '/^\d+\.\d+(\.\d+)?$/', $version ) ) {
			$version = 'dev';
		}
		// 如果是正式版本号，则将其转换为 6.4.x 这种格式
		if ( $version != 'dev' ) {
			$version = preg_replace( '/^(\d+\.\d+).*?$/', '$1', $params['version'] );
			// 平台只收录到 6.4 之后的版本，对于之前的版本统一按 dev 处理
			global $wp_version;
			$main_wp_version = preg_replace( '/^(\d+\.\d+).*?$/', '$1', $wp_version );
			if ( version_compare( $version, '6.4', '>=' ) && version_compare( $version, $main_wp_version, '<' ) ) {
				$version = $version . '.x';
			} else {
				$version = 'dev';
			}
		}

		// 检查核心的翻译更新
		$translations = json_decode( $params['translations'] ?? '[]', true );
		if ( JSON_ERROR_NONE !== json_last_error() ) {
			$args = array(
				'message' => 'translations 字段格式错误，无法解析为 Json',
			);
			$this->error( $args );
		}

		$translations_service = new Translation();
		$updated_translations = $translations_service->update_check(
			[
				$version,
			],
			$translations,
			'core',
			'core'
		);

		$args = array(
			'offers'       => $updated_core,
			'translations' => $updated_translations,
		);

		return new WP_REST_Response( $args );
	}

	private function prepare_params( array $params ): array {
		$allowed = array(
			'version',
			'php',
			'locale',
			'mysql',
			'local_package',
			'blogs',
			'users',
			'multisite_enabled',
			'initial_db_version',
			'translations',
			'channel' // 取值为 rc beta development
		);

		return array_filter( $params, function ( string $param ) use ( $allowed ) {
			return in_array( $param, $allowed );
		}, ARRAY_FILTER_USE_KEY );
	}

}
