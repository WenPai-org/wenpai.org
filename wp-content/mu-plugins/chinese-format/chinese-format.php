<?php
/**
 * Plugin Name: 中文格式化
 * Description: 为包含包含英文的中文添加正确的空格，并为一些专有名词添加正确的大小写
 * Version: 1.0
 * Author: 如来
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Platform\Chinese_Format;

require __DIR__ . '/vendor/autoload.php';

use Naux\AutoCorrect;

class Chinese_Format extends AutoCorrect{

	private static ?Chinese_Format $instance = null;

	public static function get_instance(): Chinese_Format {
		if ( ! ( self::$instance instanceof Chinese_Format ) ) {
			self::$instance = new Chinese_Format();
		}

		return self::$instance;
	}
}
