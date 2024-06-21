<?php namespace Naux;

class AutoCorrect {
	private $dicts = null;

	public function __construct() {
		$this->dicts = include __DIR__ . '/dicts.php';
	}

	public function withDict( $dicts ) {
		$this->dicts = array_merge( $this->dicts, $dicts );

		return $this;
	}

	public function convert( $content ) {
		// 针对 DeeplX 的修正
		// 原文：请查看 "新 "徽章并查看搜索外观设置。
		// 替换后：请查看「新」徽章并查看搜索外观设置。
		// 原文：您的网站激活了 %1$s，自动将产品的页面类型设置为 "项目页面"。因此，页面类型选择被禁用。
		// 替换后：您的网站激活了 %1$s，自动将产品的页面类型设置为「项目页面」。因此，页面类型选择被禁用。
		// 原文：出现类似 "Fatal error：允许的内存大小 8388608 字节已用完 "这样的信息表明
		// 替换后：出现类似「Fatal error：允许的内存大小 8388608 字节已用完」这样的信息表明
		// 原文：在 "索引 MySQL "页面上（从仪表板上的 "工具 "菜单），您可以找到 "监控数据库操作 "选项卡。使用该选项卡可要求监控您选择的分钟数。
		// 替换后：在「索引 MySQL」页面上（从仪表板上的「工具」菜单），您可以找到「监控数据库操作」选项卡。使用该选项卡可要求监控您选择的分钟数。

		// 先匹配下是否包含 html 标签，如果包含则不进行处理
		if ( ! preg_match( '/<[^>]+>/u', $content ) ) {
			$content = preg_replace_callback(
				'/(?<!=)\s?"(.*?)\s?"/u',
				function ( $matches ) {
					return '「' . $matches[1] . '」';
				},
				$content
			);
		}

		$content = $this->auto_space( $content );

		return $this->auto_correct( $content );
	}

	public function auto_space( $content ) {
		// 替换中文引号为方引号
		if ( ! preg_match( '/<[^>]+>/u', $content ) ) {
			$content = str_replace( '“', '「', $content );
			$content = str_replace( ' "', '「', $content );
			$content = str_replace( '‘', '『', $content );
			$content = str_replace( ' \'', '『', $content );
			$content = str_replace( '”', '」', $content );
			$content = str_replace( '" ', '」', $content );
			$content = str_replace( '’', '』', $content );
			$content = str_replace( '\' ', '』', $content );
		}

		// HTML 标签内的空格处理
		// https://regex101.com/r/hU3wD2/25
		$content = preg_replace( '~(\p{Han})(<(?!ruby)[a-zA-Z]+?[^>]*?>)([a-zA-Z0-9\p{Ps}\p{Pi}@$#])~u', '\1 \2\3', $content );
		$content = preg_replace( '~(\p{Han})(<\/(?!ruby)[a-zA-Z]+>)([a-zA-Z0-9])~u', '\1\2 \3', $content );
		$content = preg_replace( '~([a-zA-Z0-9\p{Pe}\p{Pf}!?‽:;,.%])(<(?!ruby)[a-zA-Z]+?[^>]*?>)(\p{Han})~u', '\1 \2\3', $content );
		$content = preg_replace( '~([a-zA-Z0-9\p{Ps}\p{Pi}!?‽:;,.%])(<\/(?!ruby)[a-zA-Z]+>)(\p{Han})~u', '\1\2 \3', $content );

		$content = preg_replace( '~((?![#年月日号午时分秒，；。！？：|()《》〈〉（）「」『』【】{}<>\[\]])\p{Han})([a-zA-Z0-9+$@#\[\(\/‘“]|%\d\$s|%s)~u', '\1 \2', $content );
		$content = preg_replace( '~([a-zA-Z0-9+$’”\]\)@#!\/]|[\d[年月日时分秒]]{2,}|%\d\$s|%s)((?![#年月日号午时分秒，；。！？：|()《》〈〉（）「」『』【】{}\[\]<>])\p{Han})~u', '\1 \2', $content );
		# Fix () [] near the English and number
		// 防止误伤函数名 字母+( 形式
		if ( ! preg_match( '/[a-zA-Z0-9]+\(/', $content ) ) {
			$content = preg_replace( '~([a-zA-Z0-9]+)([\[\(‘“])~u', '\1 \2', $content );
			$content = preg_replace( '~([\)\]’”])([a-zA-Z0-9]+)~u', '\1 \2', $content );
		}

		return $content;
	}

	public function auto_correct( $content ) {
		// 不对不包含中文的内容进行处理，这有可能是代码、邮件地址等，防止误伤
		if ( ! preg_match( '/\p{Han}/u', $content ) ) {
			return $content;
		}
		// 同时也不对正则匹配包含 URL 的内容进行处理
		if ( preg_match( '/https?:\/\/\S+/', $content ) ) {
			return $content;
		}

		foreach ( $this->dicts as $from => $to ) {
			$content = preg_replace( "/(?<!\.|[a-z]|\/|\\\|-|_){$from}(?!\.|[a-z]|\/|\\\|-|_)/i", $to, $content );
		}

		return $content;
	}
}
