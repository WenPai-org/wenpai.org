<?php

namespace Platform\Translate\WPOrgHelpHubImport\Service;

use Exception;
use GP;
use GP_Project;
use PO;
use Translation_Entry;
use Platform\Logger\Logger;
use function Platform\Helper\is_chinese;
use function Platform\Translate\WPOrgHelpHubImport\html_split;

defined( 'ABSPATH' ) || exit;

class Translate {

	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		add_action( 'plat_gp_helphub_import', array( $this, 'job' ), 10, 3 );
	}

	public function job( string $name, string $slug, string $content ) {
		if ( empty( $name ) || empty( $slug ) || empty( $content ) ) {
			Logger::error( Logger::DOCUMENT, '传入了空的参数', array(
				'name'    => $name,
				'slug'    => $slug,
				'content' => $content,
			) );

			return;
		}

		try {
			$section_strings = html_split( $content );
		} catch ( Exception $e ) {
			Logger::error( Logger::DOCUMENT, '切片 HTML 文本失败', array(
				'name'  => $name,
				'slug'  => $slug,
				'error' => $e->getMessage(),
			) );

			return;
		}

		$section_strings[] = $name;

		$pot = new PO();
		$pot->set_header( 'MIME-Version', '1.0' );
		$pot->set_header( 'Content-Type', 'text/plain; charset=UTF-8' );
		$pot->set_header( 'Content-Transfer-Encoding', '8bit' );

		foreach ( $section_strings as $text ) {
			// 如果字符串是空的就跳过
			if ( empty( $text ) || ' ' === $text ) {
				continue;
			}

			$pot->add_entry( new Translation_Entry( [
				'singular' => $text,
			] ) );
		}

		$temp_file = tempnam( sys_get_temp_dir(), 'plat-helphub-pot' );
		$pot_file  = "$temp_file.pot";
		rename( $temp_file, $pot_file );

		$exported = $pot->export_to_file( $pot_file );
		if ( ! $exported ) {
			Logger::error( Logger::DOCUMENT, '从文档内容创建 POT 文件失败', array(
				'name' => $name,
				'slug' => $slug
			) );

			return;
		}

		$project   = $this->update_gp_project( $name, $slug );
		$format    = gp_get_import_file_format( 'po', '' );
		$originals = $format->read_originals_from_file( $pot_file, $project );
		// 当读取了 pot 文件后删除临时文件
		unlink( $pot_file );

		if ( empty( $originals ) ) {
			Logger::error( Logger::DOCUMENT, '无法从通过文档内容生成的 POT 文件中加载原文', array(
				'name' => $name,
				'slug' => $slug
			) );

			return;
		}

		GP::$original->import_for_project( $project, $originals );
	}

	/**
	 * 更新 GlotPress 上的项目，并返回子项目的 ID
	 *
	 * @param $name
	 * @param $slug
	 *
	 * @return GP_Project
	 */
	private function update_gp_project( $name, $slug ): GP_Project {
		// 检查项目是否已存在
		$exist = GP::$project->find_one( array( 'path' => "docs/helphub/$slug" ) );
		if ( ! empty( $exist ) ) {
			return $exist;
		}

		$parent_project = GP::$project->find_one( array(
			'path' => 'docs/helphub',
		) );

		// 创建项目
		$args    = array(
			'name'                => $name,
			'author'              => '',
			'slug'                => $slug,
			'path'                => "docs/helphub/$slug",
			'description'         => '',
			'parent_project_id'   => $parent_project->id,
			'source_url_template' => '',
			'active'              => 1
		);
		$project = GP::$project->create_and_select( $args );

		// 为子项目创建翻译集
		$args = array(
			'name'       => '简体中文',
			'slug'       => 'default',
			'project_id' => $project->id,
			'locale'     => 'zh-cn',
		);
		GP::$translation_set->create( $args );

		return $project;
	}
}