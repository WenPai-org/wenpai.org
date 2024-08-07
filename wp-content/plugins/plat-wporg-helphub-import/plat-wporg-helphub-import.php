<?php
/**
 * Plugin Name: WordPress.Org HelpHub 导入
 * Description: 该插件自动从 WordPress.org 导入 HelpHub 文章到本地
 * Version: 1.0
 * Author: 树新蜂
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Platform\Translate\WPOrgHelpHubImport;

const PLUGIN_FILE = __FILE__;
const PLUGIN_DIR  = __DIR__;

// 加载插件
require_once( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' );

// 注册插件激活钩子
register_activation_hook( PLUGIN_FILE, [ Plugin::class, 'activate' ] );
// 注册插件删除钩子
register_uninstall_hook( PLUGIN_FILE, [ Plugin::class, 'uninstall' ] );

new Plugin();
