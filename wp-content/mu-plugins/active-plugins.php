<?php
/**
 * Plugin Name: Active Plugins
 * Description: 细化控制每个站点加载的网络插件（注意：后台启用或停用插件会导致屏蔽的插件一并被停用）
 * Version: 1.0
 * Author: 树新蜂
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// 控制站群激活的插件
add_filter( 'site_option_active_sitewide_plugins', function ( $value ) {
	global $blog_id;

	// 以前是 EP 站群激活才需要同时站群激活 Woo，现在不站群激活 EP 了，所以这里也不再需要
	/*if ( SITE_ID_STORE != $blog_id ) {
		unset( $value['woocommerce/woocommerce.php'] );
	}*/

	return $value;
} );

// 前台不加载的插件
add_filter( 'option_active_plugins', function ( $value ) {
	global $blog_id;

	// 以前是 EP 站群激活才需要同时站群激活 Woo，现在不站群激活 EP 了，所以这里也不再需要
	/*if ( SITE_ID_FORUM == $blog_id ) {
		$key = array_search( 'woocommerce/woocommerce.php', $value );

		if ( $key || 0 === $key ) {
			unset( $value[ $key ] );
		}
	}*/

	if ( PHP_SAPI !== 'cli' && ! is_admin() ) {
		$plugins = [
			// 爬虫用 7.x 版本的 GuzzleHttp 会导致下面那两卧龙凤雏报错
			'plat-wporg-product-spider/plat-wporg-product-spider.php',
		];

		return array_diff( $value, $plugins );
	}

	return $value;
} );

// CLI 下不加载的插件
add_filter( 'option_active_plugins', function ( $value ) {
	if ( PHP_SAPI === 'cli' ) {
		$plugins = [
			// 这货报错
			'license-manager-for-woocommerce/license-manager-for-woocommerce.php',
			// 这两卧龙凤雏使用 6.x 版本的 GuzzleHttp 导致市场爬虫报错
			'wenprise-alipay-checkout-for-woocommerce/wenprise-alipay-checkout-for-woocommerce.php',
			'wenprise-wechatpay-checkout-for-woocommerce/wenprise-wechatpay-checkout-for-woocommerce.php',
		];

		return array_diff( $value, $plugins );
	}

	return $value;
} );
