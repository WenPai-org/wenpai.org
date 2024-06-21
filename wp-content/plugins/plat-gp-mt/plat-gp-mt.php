<?php
/**
 * Plugin Name: GlotPress AI 翻译
 * Description: 为 GlotPress 提供 AI 翻译功能，支持缓存，优先匹配记忆库
 * Version: 1.0
 * Author: 树新蜂
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Platform\Translate\MachineTranslate;

const PLUGIN_FILE = __FILE__;
const PLUGIN_DIR  = __DIR__;

// 加载插件
require_once( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' );

// 注册插件激活钩子
register_activation_hook( PLUGIN_FILE, [ Plugin::class, 'activate' ] );
// 注册插件删除钩子
register_uninstall_hook( PLUGIN_FILE, [ Plugin::class, 'uninstall' ] );

new Plugin();
