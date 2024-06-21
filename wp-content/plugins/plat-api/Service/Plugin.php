<?php

namespace Platform\API\Service;

use function Platform\Helper\get_products_from_es;

class Plugin {

	public function update_check( $plugins ): array {
		switch_to_blog( SITE_ID_PLUGINS );
		$slugs = array();

		foreach ( $plugins as $plugin => $meta ) {
			list( $slug ) = explode( '/', $plugin );

			$slugs[] = $slug;
		}

		$fields     = array(
			'ID',
			'post_name',
			'meta._thumbnail_id',
			'meta.banner.value',
			'meta.version.value',
			'meta.requires_wordpress_version.value',
			'meta.tested_wordpress_version.value',
			'meta.requires_php_version.value',
			'meta.download_url.value',
		);
		$db_plugins = get_products_from_es( $slugs, $fields );
		if ( is_wp_error( $db_plugins ) ) {
			return array(
				'update'    => array(),
				'no_update' => array(),
			);
		}

		$db_plugins = $this->prepare_db_plugins( $db_plugins );

		$update_exists    = array();
		$no_update_exists = array();
		foreach ( $plugins as $key => $plugin ) {
			/**
			 * 插件slug有如下2种情况
			 * classic-editor/classic-editor.php
			 * hello.php
			 */
			preg_match( '/^([a-z0-9-]+)(\.php|\/.*)$/', $key, $matches );
			$slug = $matches[1] ?? '';

			$db_plugin       = $db_plugins[ $slug ] ?? array();
			$request_version = $plugin['Version'] ?? '';
			$db_version      = $db_plugin['new_version'] ?? '';

			$db_plugin['plugin'] = $key;

			if ( version_compare( $request_version, $db_version, '<' ) ) {
				$update_exists[ $key ] = $db_plugin;
			} elseif ( isset( $db_plugins[ $slug ] ) ) {
				$no_update_exists[ $key ] = $db_plugin;
			}
		}

		restore_current_blog();

		return array(
			'update'    => $update_exists,
			'no_update' => $no_update_exists
		);
	}

	private function prepare_db_plugins( array $db_plugins ): array {
		$data = array();

		foreach ( $db_plugins['hits']['hits'] ?? array() as $item ) {
			$slug = $item['_source']['post_name'];

			$icons   = array();
			$icon_id = $item['_source']['meta']['_thumbnail_id'][0]['long'] ?? '';
			if ( ! empty( $icon_id ) ) {
				$post = get_post( $icon_id );

				$icons = array(
					'1x' => $post->guid,
				);
			}

			$banner  = $item['_source']['meta']['banner'][0]['value'] ?? '';
			$banners = empty( $banner ) ? array() : array(
				'1x' => $item['_source']['meta']['banner'][0]['value'] ?? ''
			);

			$requires = $item['_source']['meta']['requires_wordpress_version'][0]['value'] ?? false;
			$requires = empty( $requires ) ? false : $requires;

			$tested = $item['_source']['meta']['tested_wordpress_version'][0]['value'] ?? false;
			$tested = empty( $tested ) ? false : $tested;

			$requires_php = $item['_source']['meta']['requires_php_version'][0]['value'] ?? false;
			$requires_php = empty( $requires_php ) ? false : $requires_php;

			$package = $item['_source']['meta']['download_url'][0]['value'] ?? '';

			$args          = array(
				'id'            => $slug,
				'slug'          => $slug,
				'new_version'   => $item['_source']['meta']['version'][0]['value'] ?? '',
				'url'           => network_site_url( '/plugins/' . $slug . '/' ),
				'package'       => $package,
				'icons'         => $icons,
				'banners'       => $banners,
				'banners_rtl'   => array(),
				'requires'      => $requires,
				'tested'        => $tested,
				'requires_php'  => $requires_php,
				'compatibility' => array(),
			);
			$data[ $slug ] = $args;
		}

		return $data;
	}

}
