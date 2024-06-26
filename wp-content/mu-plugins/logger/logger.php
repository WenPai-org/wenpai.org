<?php
/**
 * Plugin Name: 日志服务
 * Description: 统一的日志服务
 * Version: 1.0
 * Author: 如来
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Platform\Logger;

use Monolog\Logger as Logger_Lib;
use Monolog\Handler\StreamHandler;

/**
 * @method static Logger_Lib debug( string $name, string $message, array $context = array() )
 * @method static Logger_Lib info( string $name, string $message, array $context = array() )
 * @method static Logger_Lib notice( string $name, string $message, array $context = array() )
 * @method static Logger_Lib warning( string $name, string $message, array $context = array() )
 * @method static Logger_Lib error( string $name, string $message, array $context = array() )
 * @method static Logger_Lib critical( string $name, string $message, array $context = array() )
 * @method static Logger_Lib alert( string $name, string $message, array $context = array() )
 * @method static Logger_Lib emergency( string $name, string $message, array $context = array() )
 *
 * @see Logger_Lib
 */
class Logger {

    const LEVEL = Logger_Lib::DEBUG;

    // 定义全局的日志名称常量
    const FORUM = 'Forum';
    const TRANSLATE = 'Translate';
    const TRANSLATEPACK = 'TranslatePack';
    const STORE = 'Store';
	const Plugins = 'Plugins';
	const Themes = 'Themes';
    const DOCUMENT = 'Document';
    const API = 'Api';
    const GLOBAL = 'Global';
    const PHP = 'PHP';

	/**
     * @var array
     */
    private static array $instances = array();

    public static function __callStatic( $func, $args ): void {
        if ( count( $args ) < 2 ) {
            return;
        }

        $name = $args[0];
        unset( $args[0] );

        if ( ! key_exists( $name, self::$instances ) || ! ( self::$instances[ $name ] instanceof Logger_Lib ) ) {
            $log = new Logger_Lib( $name );
            $log->pushHandler( new StreamHandler( ABSPATH . "wp-content/logs/{$name}.log", self::LEVEL ) );
            self::$instances[ $name ] = $log;
        }

        call_user_func_array( array( self::$instances[ $name ], $func ), $args );
    }

}
