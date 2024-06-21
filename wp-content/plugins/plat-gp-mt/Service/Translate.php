<?php

namespace Platform\Translate\MachineTranslate\Service;

use GP;
use GP_Route;
use Platform\Chinese_Format\Chinese_Format;
use Platform\Logger\Logger;
use StanfordNLP\POSTagger;
use Translation_Entry;
use Translations;
use WordPressdotorg\GlotPress\TranslationSuggestions\Translation_Memory_Client;
use WP_Error;
use const Platform\Translate\MachineTranslate\PLUGIN_DIR;

/**
 * AI 翻译引擎
 *
 * 该引擎只对翻译平台托管的项目生效。引擎同时提供了 WEB 端和 API 端的外部接口。
 * 对于 WEB 端的请求，会直接保存进对应的项目，而 API 端则将结果返回。
 */
class Translate extends GP_Route {

	/**
	 * 面向 WEB 场景的外部接口函数
	 */
	public function web( int $project_id, array $originals ): bool {

		/**
		 * 获取翻译集
		 */
		$translation_set = GP::$translation_set->find_one( array( 'project_id' => $project_id ) );
		if ( empty( $translation_set ) ) {
			Logger::error( Logger::TRANSLATE, '从 WEB 端为 AI 翻译引擎传入的项目 ID 无法获取到对应的翻译集', array(
				'project_id' => $project_id,
			) );

			return false;
		}

		$translations = $this->job( $project_id, $originals, false );

		wp_set_current_user( BOT_USER_ID );

		/**
		 * 翻译入库
		 */
		$translation_set->import( $translations );

		return true;
	}

	/**
	 * 面向 API 场景的外部接口函数
	 */
	public function api(): void {
		header( 'Content-Type: application/json' );

		$json_data         = file_get_contents( 'php://input' );
		$request_originals = json_decode( $json_data, true );
		if ( JSON_ERROR_NONE !== json_last_error() ) {
			echo json_encode( array( 'error' => '参数错误，不是标准的 Json 字符串' ), JSON_UNESCAPED_SLASHES );
			exit;
		}

		$request_originals = $request_originals['originals'] ?? array();

		$gp_originals_singular = GP::$original->find_many( array(
			'singular' => $request_originals,
			'status'   => '+active'
		) );
		$gp_originals_plural   = GP::$original->find_many( array(
			'plural' => $request_originals,
			'status' => '+active'
		) );
		$gp_originals          = array_merge( $gp_originals_singular, $gp_originals_plural );
		if ( empty( $gp_originals ) ) {
			echo json_encode( array( 'error' => '你请求的字符串未托管在 ' . get_bloginfo( 'name' ) . '上。碍于系统资源有限，我们暂时无法将接口完全对外开放。' ), JSON_UNESCAPED_SLASHES );
			exit;
		}

		// 取用户请求翻译的字符串与 GlotPress 数据库中积累的项目字符串的交集，也就是说，只允许翻译项目中存在的字符串
		$originals = array();
		foreach ( $gp_originals as $gp_original ) {
			if ( in_array( $gp_original->singular, $request_originals ) || in_array( $gp_original->plural, $request_originals ) ) {
				$originals[] = $gp_original;
			}
		}

		$translations = $this->job( 0, $originals, true );


		$data = array();
		foreach ( $translations->entries as $translation ) {
			$data[ $translation->singular ] = $translation->translations[0] ?? '';
		}

		echo json_encode( $data, JSON_UNESCAPED_SLASHES );
	}

	/**
	 * 核心函数
	 *
	 * 翻译填充的流程：
	 * 1. 尝试匹配记忆库，匹配时会按优先级处理匹配关系，按需跳过已经翻译的内容
	 * 2. 对原文进行术语库替换，同时将作品的作者及名称一并替换
	 * 3. 调用谷歌翻译对处理后的原文进行翻译
	 * 4. 恢复被替换的术语
	 *
	 * 该函数批量处理 AI 翻译工作并返回最终结果
	 */
	private function job( int $project_id, array $originals, bool $jump = true ): Translations {

		/**
		 * 初始化翻译实体
		 */
		$translations = new Translations();

		/**
		 * 初始化术语表
		 */
		$glossaries = $this->get_glossaries();

		$excluded = array();

		$project_name = $this->get_project_name( $project_id );
		if ( ! empty( $project_name ) ) {
			$excluded[] = $project_name;
		}

		foreach ( $excluded as $item ) {
			if ( empty( $item ) ) {
				continue;
			}
			$glossaries[ $item ] = array(
				'translation'    => $item,
				'part_of_speech' => 'noun',
			);
		}

		/**
		 * 开始翻译流程
		 */
		foreach ( $originals as $original_key => $original ) {
			// 检查是否已经翻译
			if ( ! $jump ) {
				$translation_check = GP::$translation->find_one( array(
					'original_id' => $original->id,
					'status'      => '+active',
				) );
				if ( ! empty( $translation_check ) ) {
					unset( $originals[ $original_key ] );
					continue;
				}
			}

			// 检查术语库和记忆库
			$translation = $glossaries[ strtolower( $original->singular ) ]['translation'] ?? '';
			//$translation = $translation ?: $this->query_memory( $original->singular );

			if ( ! empty( $translation ) ) {
				$entry = new Translation_Entry( array(
					'singular'     => $original?->singular,
					'plural'       => $original?->plural,
					'translations' => array( $translation ),
					'context'      => $original?->context,
					'references'   => $original?->references,
					'flags'        => array(
						'current'
					),
				) );
				$translations->add_entry( $entry );

				// 如果已经被预翻译了，就不要再进行机翻了
				unset( $originals[ $original_key ] );
			}
		}

		// 调用 AI 翻译
		$tasks = array();
		end( $originals );
		$key_last    = key( $originals );
		$sources_len = 0;
		$format      = Chinese_Format::get_instance();

		foreach ( $originals as $original_key => $original ) {
			$original   = $original->fields();
			$source     = $original['singular'] ?? '';
			$source_esc = $source;
			if ( empty( $source ) ) {
				continue;
			}

			/**
			 * 获取词性，用于术语库替换
			 *
			 * 先尝试从 gp_postags 表中获取，如果没有再调用 StanfordNLP 获取并保存
			 */
			global $wpdb;
			$pos_map = $wpdb->get_var( $wpdb->prepare( "select tag from {$wpdb->prefix}gp_postags where source = %s", $source_esc ) );
			if ( empty( $pos_map ) ) {
				$pos_map = self::get_pos_tags( $source_esc );

				$wpdb->insert( "{$wpdb->prefix}gp_postags", array(
					'source' => $source_esc,
					'tag'    => json_encode( $pos_map ),
				) );
			} else {
				$pos_map = json_decode( $pos_map, true );
			}

			/**
			 * 替换术语库
			 */
			foreach ( $glossaries as $key => $value ) {
				// 开始替换前先检查术语是否在表中，如果不在就不替换
				if ( ! key_exists( $key, $pos_map ) ) {
					continue;
				}

				// 词性不匹配的话就不替换
				if ( $pos_map[ $key ] !== $value['part_of_speech'] ) {
					continue;
				}

				$source_esc = str_replace( $key, $value['translation'], $source_esc );
			}


			/**
			 * 文本替换前进行预处理（主要去除HTML标签，这样防止在后续处理的时候把HTML标签也处理了）
			 */
			preg_match_all( '/<.+?>/', $source_esc, $matches );
			$matches = $matches[0] ?? array();

			// 结果集去重
			$matches = array_unique( $matches );

			// 原文中被替换的关键字以及代号 id
			$id_map = array();

			foreach ( $matches as $rand_id => $match ) {
				// 排除掉结束标签（替换开始标签时会顺带替换对应的结束标签）
				if ( str_contains( $match, '</' ) ) {
					continue;
				}

				/**
				 * 替换 HTML 开始标签
				 */
				$h_id            = "<$rand_id>";
				$id_map[ $h_id ] = $match;
				// 替换 HTML 后对两端增加空格，以使标识符与周围单词划开界限
				$source_esc = str_replace( $match, " $h_id ", $source_esc );

				/**
				 * 替换 HTML 开始标签
				 */
				$h_id = "</$rand_id>";

				// 先提取一下这个标签究竟是何方牛马
				preg_match_all( '/<(\w+)[^>]*>/', $match, $tag_matches );
				$tag_name = $tag_matches[1][0] ?? '';

				if ( ! empty( $tag_name ) ) {
					$id_map[ $h_id ] = "</$tag_name>";
					// 替换 HTML 后对两端增加空格，以使标识符与周围单词划开界限
					$source_esc = str_replace( "</$tag_name>", " $h_id ", $source_esc );

					// 为了防止有的人不讲武德，写不规范的结束标签，所以多匹配一种情况
					$h_id            = "</ $rand_id>";
					$id_map[ $h_id ] = "</$tag_name>";
					// 替换 HTML 后对两端增加空格，以使标识符与周围单词划开界限
					$source_esc = str_replace( "</ $rand_id>", " $h_id ", $source_esc );
				}
			}

			/**
			 * 替换简码
			 */
			preg_match_all( '/\[.+?]/', $source_esc, $matches );

			foreach ( $matches[0] ?? array() as $match ) {
				$id            = count( $id_map ) + 1;
				$id_map[ $id ] = $match;
				$source_esc    = str_replace( $match, "#$id", $source_esc );
			}

			$tasks[ $source ] = array(
				'original_id' => $original_key,
				'source'      => $source,
				'source_esc'  => $source_esc,
				'target'      => '',
				'glossaries'  => $id_map,
			);

			$source_len = strlen( urlencode( $source_esc ) );
			// 如果当前语句单条的长度就超限的话就直接略过
			if ( $source_len > 10000 ) {
				continue;
			}

			$sources_len += $source_len;

			/**
			 * 计算当前累计的字符数
			 * 谷歌限制为 5000 字符一次提交，这里限制为 4000 进行一次批量提交，因为后续还需要往里面插入换行符
			 * DeeplX 限制大概在 15000 字符一次提交
			 */
			if ( $sources_len > 9000 || $key_last === $original_key ) {
				// 如果最后一次任务的字符数加进去大于 10000 的话就挪到下一次任务
				if ( $sources_len > 10000 ) {
					unset( $tasks[ $source ] );
				}

				$sources_esc = array();
				foreach ( $tasks as $task ) {
					$sources_esc[] = $task['source_esc'];
				}

				$data = self::machine_translate( $sources_esc );
				if ( is_wp_error( $data ) ) {
					Logger::error( Logger::TRANSLATE, 'AI 翻译失败：' . $data->get_error_message(), array(
						'project_id' => $project_id,
						'data'       => $data->get_all_error_data(),
					) );

					goto over;
				}

				foreach ( $tasks as &$task ) {
					$task['target'] = $data[ $task['source_esc'] ];

					foreach ( $task['glossaries'] as $k => $v ) {
						// 替换关键字
						//$task['target'] = preg_replace( "~(\s*)#(\s*)$k(\s*)~m", $v, $task['target'] );
						// 替换 HTML 标签等不太会被谷歌翻译导致插入空格的内容
						$task['target'] = preg_replace( "~(\s*)$k(\s*)~m", $v, $task['target'] );
					}

					// 去除2个汉字之间的空格，这些可能是在替换术语之后插入的
					// 之所以不在替换术语的时候处理是因为会导致中文和英文之间紧挨着，可能会影响翻译结果？
					$task['target'] = preg_replace( "~(\p{Han})(\s+)(\p{Han})~u", "$1$3", $task['target'] );

					if ( ! empty( $task['target'] ) ) {
						$o     = $originals[ $task['original_id'] ];
						$entry = new Translation_Entry( array(
							'singular'     => $o?->singular,
							'plural'       => $o?->plural,
							'translations' => array( $format->convert( $task['target'] ) ),
							'context'      => $o?->context,
							'references'   => $o?->references,
							'flags'        => array(
								'fuzzy',
							),
						) );
						$translations->add_entry( $entry );
					}
				}
				unset( $task );

				over:
				unset( $tasks );
				$tasks = array();
				if ( $sources_len > 10000 ) {
					$tasks[ $source ] = array(
						'original_id' => $original_key,
						'source'      => $source,
						'source_esc'  => $source_esc,
						'target'      => '',
						'glossaries'  => $id_map,
					);
				}

				$sources_len = empty( $tasks ) ? 0 : $source_len;
			}
		}


		return $translations;
	}

	/**
	 * 获取术语表
	 */
	private function get_glossaries(): array {
		//$glossaries = wp_cache_get( 'translate_glossaries', 'platform' );
		$glossaries = array();

		if ( empty( $glossaries ) ) {
			$glossaries       = array();
			$glossary_entries = GP::$glossary_entry->all();

			// 为术语衍生不同时态的版本
			foreach ( $glossary_entries as $key => $value ) {
				if ( empty( $value->term ) || empty( $value->translation ) || $value->term === $value->translation ) {
					continue;
				}

				$terms     = array();
				$base_term = $value->term;
				$terms[]   = $base_term; // 直接使用基本术语

				// 只处理不包含空格，且不全为大写的术语
				if ( ! str_contains( $base_term, ' ' ) && strtoupper( $base_term ) !== $base_term ) {
					// 复数形式处理
					if ( preg_match( '/y$/', $base_term ) && ! preg_match( '/[aeiou]y$/', $base_term ) ) {
						$terms[] = substr( $base_term, 0, - 1 ) . 'ies';
					} elseif ( preg_match( '/(f|fe)$/', $base_term ) ) {
						$pluralForm = preg_replace( '/(f|fe)$/', 'ves', $base_term );
						$terms[]    = $pluralForm;
					} elseif ( preg_match( '/o$/', $base_term ) && ! preg_match( '/(piano|photo)$/', $base_term ) ) {
						$terms[] = $base_term . 'es';
					} else {
						$terms[] = $base_term . 's';
					}

					// 过去式和进行时处理
					if ( preg_match( '/[aeiou]e$/', $base_term ) ) {
						$terms[] = $base_term . 'd';
						$terms[] = $base_term . 'ing';
					} elseif ( preg_match( '/y$/', $base_term ) && ! preg_match( '/[aeiou]y$/', $base_term ) ) {
						$terms[] = substr( $base_term, 0, - 1 ) . 'ied';
						$terms[] = substr( $base_term, 0, - 1 ) . 'ying';
					} else {
						$doubleLastConsonant = preg_match( '/([^aeiou][aeiou][^aeiouwxy])$/', $base_term ) ? $base_term . substr( $base_term, - 1 ) : $base_term;
						$terms[]             = $base_term . 'ed';
						$terms[]             = $doubleLastConsonant . 'ing';
					}
				}

				foreach ( $terms as $term ) {
					$glossaries[ $term ] = array(
						'translation'    => $value->translation,
						'part_of_speech' => $value->part_of_speech,
					);
				}
			}

			/**
			 * 对术语库按键的长度降序排序，这样防止先匹配短的术语导致长术语无法匹配
			 */
			uksort( $glossaries, function ( $a, $b ) {
				return mb_strlen( $a ) < mb_strlen( $b );
			} );

			wp_cache_set( 'translate_glossaries', $glossaries, 'platform', 7200 );
		}

		return $glossaries;

	}

	/**
	 * 获取给定语句的所有单词的词性
	 */
	private static function get_pos_tags( string $text ): array {
		$pos = new POSTagger(
			PLUGIN_DIR . '/stanford-postagger/models/english-left3words-distsim.tagger',
			PLUGIN_DIR . '/stanford-postagger/stanford-postagger-4.2.0.jar',
		);

		$result = $pos->tag( explode( ' ', $text ) );

		$tag_map = array(
			'NN'  => 'noun',
			'NNS' => 'noun',
			'VB'  => 'verb',
			'VBD' => 'verb',
			'VBG' => 'verb',
			'VBN' => 'verb',
			'VBP' => 'verb',
			'VBZ' => 'verb',
			'JJ'  => 'adjective',
			'JJR' => 'adjective',
			'JJS' => 'adjective',
			'RB'  => 'adverb',
			'RBR' => 'adverb',
			'RBS' => 'adverb',
			'UH'  => 'interjection',
			'CC'  => 'conjunction',
			'IN'  => 'preposition',
			'PRP' => 'pronoun',
		);

		$pos_map = array();
		foreach ( $result[0] ?? array() as $item ) {
			$tag  = $item[1] ?? '';
			$word = $item[0] ?? '';

			if ( ! key_exists( $tag, $tag_map ) ) {
				continue;
			}

			$pos_map[ $word ] = $tag_map[ $tag ];
		}

		return $pos_map;
	}

	/**
	 * 获取项目名
	 *
	 * 这不是简单的读取项目属性，因为项目属性中的项目名通常包含了长尾副词，所以这个函数尝试用项目原文中分析项目名称
	 */
	private function get_project_name( int $project_id ): string {
		$allowed = array(
			'Theme Name of the theme',
			'Plugin Name of the plugin',
			'Name of the plugin',
			'Name of the theme',
		);

		$original = GP::$original->find_one( array(
			'project_id' => $project_id,
			'comment'    => $allowed,
		) );

		if ( $original ) {
			return $original->singular;
		} else {
			return '';
		}
	}

	public static function query_memory( string $source ): string {
		$source = strtolower( $source );

		$memory = Translation_Memory_Client::query( $source );
		if ( is_wp_error( $memory ) || empty( $memory ) || ! isset( $memory[0] ) ) {
			return '';
		}

		// 第一个必定是得分最高的，所以没必要判断后面的
		if ( $memory[0]['similarity_score'] <= 90 ) {
			return '';
		}

		return $memory[0]['translation'];
	}

	/**
	 * 谷歌翻译接口封装函数
	 */
	private function machine_translate( array $originals ): string|array|WP_Error {
		// 原文中不允许出现换行符，因为换行符将用来分割多条原文
		$originalsSanitized = array_map( function ( $source ) {
			return str_replace( array( "\n", "\n\r", "\r\n" ), '', $source );
		}, $originals );

		// 原文中的 HTML 实体需要转换成对应的字符，否则可能会导致谷歌翻译403
		$original = html_entity_decode( join( "\n", $originalsSanitized ) );

		/*$tr = new GoogleTranslate();
		// 设置代理 Url
		//$tr->setUrl( 'https://translate.googleapis.com/translate_a/single' );
		$tr->setTarget( 'zh-CN' );
		try {
			$translation = $tr->translate( $original );
		} catch ( \ErrorException $e ) {
			Logger::error( Logger::TRANSLATE, '谷歌翻译接口调用失败', [
				'error' => $e->getMessage(),
			] );
		}*/
		$request         = [
			'text'        => $original,
			'source_lang' => 'auto',
			'target_lang' => 'ZH',
		];
		$response        = wp_remote_post( DEEPLX_API,
			array(
				'timeout' => 120,
				'headers' => array(
					'Content-Type' => 'application/json',
				),
				'body'    => wp_json_encode( $request ),
			)
		);
		$response_status = wp_remote_retrieve_response_code( $response );
		$output          = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $response_status || is_wp_error( $response ) ) {
			return new WP_Error( 500, '未响应正确的状态码' );
		}
		if ( ! isset( $output['code'] ) || 200 !== $output['code'] ) {
			return new WP_Error( 500, '返回错误' );
		}

		$translation = $output['data'];

		$translationList = explode( "\n", $translation );

		// 如果翻译的数量和原文的数量对不上的话就记录错误日志同时返回空数组
		if ( count( $translationList ) !== count( $originals ) ) {
			Logger::error( Logger::TRANSLATE, '翻译接口返回的译文数量与传入的原文数量不符', [
				'originals'    => $originals,
				'translations' => $translationList,
			] );

			return [];
		}

		return array_combine( $originals, $translationList );
	}
}
