<?php
/**
 * Plugin Name: WordPress.Org 翻译导入
 * Description: 该插件的主要由钩子触发，自动从 WordPress.org 拉取翻译项目，若项目不存在则新建。
 * Version: 1.0
 * Author: 树新蜂
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Platform\Translate\WPOrgTranslateImport;

const PLUGIN_FILE = __FILE__;
const PLUGIN_DIR  = __DIR__;

// 加载插件
require_once( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' );

// 注册插件激活钩子
register_activation_hook( PLUGIN_FILE, [ Plugin::class, 'activate' ] );
// 注册插件删除钩子
register_uninstall_hook( PLUGIN_FILE, [ Plugin::class, 'uninstall' ] );

new Plugin();
