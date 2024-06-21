<?php

namespace Platform\API\Service;

use function Platform\Helper\get_products_from_es;
use function Platform\Helper\get_woo_download_url;

class Theme {

	public function update_check( $themes ): array {
		switch_to_blog( SITE_ID_THEMES );
		$slugs = array();

		foreach ( $themes as $meta ) {
			$slugs[] = $meta['Stylesheet'] ?? '';
		}

		$fields    = array(
			'ID',
			'post_name',
			'meta._thumbnail_id.long',
			'meta.banner.value',
			'meta.version.value',
			'meta.requires_wordpress_version.value',
			'meta.tested_wordpress_version.value',
			'meta.requires_php_version.value',
			'meta.download_url.value',
		);
		$db_themes = get_products_from_es( $slugs, $fields );
		if ( is_wp_error( $db_themes ) ) {
			return array(
				'update'    => array(),
				'no_update' => array(),
			);
		}

		$db_themes = $this->prepare_db_themes( $db_themes );

		$update_exists    = array();
		$no_update_exists = array();
		foreach ( $themes as $theme ) {
			$slug            = $theme['Stylesheet'] ?? '';
			$db_theme        = $db_themes[ $slug ] ?? array();
			$request_version = $theme['Version'] ?? '';
			$db_version      = $db_theme['new_version'] ?? '';

			if ( version_compare( $request_version, $db_version, '<' ) ) {
				$update_exists[ $slug ] = $db_theme;
			} elseif ( isset( $db_themes[ $slug ] ) ) {
				$no_update_exists[ $slug ] = $db_theme;
			}
		}

		restore_current_blog();

		return array(
			'update'    => $update_exists,
			'no_update' => $no_update_exists
		);
	}

	private function prepare_db_themes( array $db_themes ): array {
		$data = array();

		foreach ( $db_themes['hits']['hits'] ?? array() as $item ) {
			$slug = $item['_source']['post_name'];

			$requires = $item['_source']['meta']['requires_wordpress_version'][0]['value'] ?? false;
			$requires = empty( $requires ) ? false : $requires;

			$requires_php = $item['_source']['meta']['tested_wordpress_version'][0]['value'] ?? false;
			$requires_php = empty( $requires_php ) ? false : $requires_php;

			$package = $item['_source']['meta']['download_url'][0]['value'] ?? '';

			$args          = array(
				'theme'        => $slug,
				'new_version'  => $item['_source']['meta']['version'][0]['value'] ?? '',
				'url'          => network_site_url( '/themes/' . $slug . '/' ),
				'package'      => $package,
				'requires'     => $requires,
				'requires_php' => $requires_php,
			);
			$data[ $slug ] = $args;
		}

		return $data;
	}

}
