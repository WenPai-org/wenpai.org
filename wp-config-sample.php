<?php
/**
 * WordPress 基础配置文件
 *
 * 这个文件被安装程序用于自动生成 wp-config.php 配置文件
 * 您不必使用网站，可以将这个文件复制到「wp-config.php」并填写这些值。
 *
 * 本文件包含以下配置选项：
 *
 * * 数据库设置
 * * 密钥
 * * 数据库表名前缀
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** 数据库设置 - 您可以从您的主机获取这些信息 ** //
/** WordPress 数据库名称 */
define( 'DB_NAME', 'database_name_here' );

/** 数据库用户名 */
define( 'DB_USER', 'username_here' );

/** 数据库密码 */
define( 'DB_PASSWORD', 'password_here' );

/** 数据库主机 */
define( 'DB_HOST', 'localhost' );

/** 创建表时使用的数据库字符集。 */
define( 'DB_CHARSET', 'utf8' );

/** 数据库排序规则类型。如不确定，请勿更改。 */
define( 'DB_COLLATE', '' );

/**#@+
 * 身份验证唯一密钥与盐。
 *
 * 将这些更改为任意独一无二的字符串！您可以使用
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org 密钥服务}
 * 生成这些。
 *
 * 您可以随时更改这些内容以使所有现有 cookies 失效。
 * 这将强制所有用户必须重新登录。
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'put your unique phrase here' );
define( 'SECURE_AUTH_KEY',  'put your unique phrase here' );
define( 'LOGGED_IN_KEY',    'put your unique phrase here' );
define( 'NONCE_KEY',        'put your unique phrase here' );
define( 'AUTH_SALT',        'put your unique phrase here' );
define( 'SECURE_AUTH_SALT', 'put your unique phrase here' );
define( 'LOGGED_IN_SALT',   'put your unique phrase here' );
define( 'NONCE_SALT',       'put your unique phrase here' );

/**#@-*/

/**
 * WordPress 数据表前缀。
 *
 * 如果您为每个安装分配一个唯一前缀，您可以在一个数据库中拥有多个安装。
 * 请只使用数字、字母和下划线！
 */
$table_prefix = 'wp_';

/**
 * 开发者专用：WordPress 调试模式。
 *
 * 将此值更改为 true 以启用开发过程中的通知显示。
 * 强烈建议插件和主题开发人员在其开发环境中使用 WP_DEBUG。
 *
 * 有关可用于调试的其他常量的信息，请访问文档。
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/**
 * 简体中文专属：ICP 备案号显示
 *
 * 在设置 → 常规中设置你的 ICP 备案号。
 * 可调用简码 [cn_icp] 或函数 cn_icp() 显示。
 *
 * @since 6.5.0
 * @link https://cn.wordpress.org/support/i10n-features/
 */
define( 'CN_ICP', true );

/**
 * 简体中文专属：公安备案号显示
 *
 * 在设置 → 常规中设置你的公安备案号。
 * 可调用简码 [cn_ga] 或函数 cn_ga() 显示。
 *
 * @since 6.5.0
 * @link https://cn.wordpress.org/support/i10n-features/
 */
define( 'CN_GA', true );

/* 在这行和「停止编辑」行之间添加任何自定义值。 */

// 内存设置
const WP_MEMORY_LIMIT     = '1G';
const WP_MAX_MEMORY_LIMIT = '2G';

// 开启缓存
const WP_CACHE        = true;
const WP_REDIS_CONFIG = [
	'token'            => '',
	'host'             => '127.0.0.1',
	'port'             => 6379,
	'database'         => 0, // change for each site
	'maxttl'           => 86400 * 7,
	'timeout'          => 1.0,
	'read_timeout'     => 1.0,
	'split_alloptions' => true,
	'debug'            => false,
];

const WP_REDIS_DISABLED = false;

// 多站点相关配置
const WP_ALLOW_MULTISITE   = true;
const MULTISITE            = true;
const SUBDOMAIN_INSTALL    = true;
const DOMAIN_CURRENT_SITE  = 'wenpai.org';
const SITE_ID_CURRENT_SITE = 1;
const BLOG_ID_CURRENT_SITE = 1;
const COOKIE_DOMAIN        = '.wenpai.org';
const COOKIEPATH           = '/';
const SITECOOKIEPATH       = '/';
const ADMIN_COOKIE_PATH    = '/';

// 定义机器人的用户ID
const BOT_USER_ID = 4;

// 定义各站点 ID
const SITE_ID_FORUM     = 5;
const SITE_ID_TRANSLATE = 7;
const SITE_ID_PLUGINS   = 3;
const SITE_ID_THEMES    = 4;
const SITE_ID_DOCUMENT  = 2;

// 防止 ms-files.php 报错
const BLOGUPLOADDIR = 'wp-content/uploads';

// 翻译平台相关配置
const GP_URL_BASE = '/';

// 强制使用 HTTPS
$_SERVER['HTTPS'] = 'on';
const FORCE_SSL_LOGIN = true;
const FORCE_SSL_ADMIN = true;

// 禁用 CRON
const DISABLE_WP_CRON = true;

// 禁止文章修订
const WP_POST_REVISIONS = false;

// 禁止自动保存
const AUTOSAVE_INTERVAL = 86400;

// JWT Token
const JWT_AUTH_SECRET_KEY = '';

// 密码哈希
const WP_PASSWORD_HASH_ALGO    = PASSWORD_ARGON2ID;
const WP_PASSWORD_HASH_OPTIONS = [
	'memory_cost' => 65536,
	'time_cost'   => 4,
	'threads'     => 1
];

// DeeplX API 地址
const DEEPLX_API = 'https://api.deeplx.org/translate';

/* 就这样，停止编辑！祝您使用愉快。 */

/** WordPress 目录的绝对路径。 */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** 设置 WordPress 变量和包含的文件。 */
require_once ABSPATH . 'wp-settings.php';
