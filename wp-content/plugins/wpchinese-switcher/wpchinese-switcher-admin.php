<?php

class wpcs_Admin {
	var $base = '';
	var $is_submitted = false;
	var $is_success = false;
	var $is_error = false;
	var $message = '';
	var $options = false;
	var $langs = false;
	var $url = '';
	var $admin_lang = false;

	function wpcs_Admin() {
		return $this->__construct();
	}

	function __construct() {
		global $wpcs_options, $wpcs_langs, $wpcs_modules;
		$locale = str_replace( '_', '-', strtolower( get_locale() ) );
		if ( $wpcs_options === false ) {
			$wpcs_options = get_wpcs_option( 'wpcs_options' );
		}
		$this->langs   = &$wpcs_langs;
		$this->options = $wpcs_options;
		if ( empty( $this->options ) ) {
			$this->options = array(
				'wpcs_search_conversion'       => 1,
				'wpcs_used_langs'              => array( 'zh-hans', 'zh-hant', 'zh-cn', 'zh-hk', 'zh-sg', 'zh-tw' ),
				'wpcs_browser_redirect'        => 0,
				'wpcs_auto_language_recong'    => 0,
				'wpcs_flag_option'             => 1,
				'wpcs_use_cookie_variant'      => 0,
				'wpcs_use_fullpage_conversion' => 1,
				'wpcso_use_sitemap'            => 1,
				'wpcs_trackback_plugin_author' => 0,
				'wpcs_add_author_link'         => 0,
				'wpcs_use_permalink'           => 0,
				'wpcs_no_conversion_tag'       => '',
				'wpcs_no_conversion_ja'        => 0,
				'wpcs_no_conversion_qtag'      => 0,
				'wpcs_engine'                  => 'mediawiki', // alternative: opencc
				'nctip'                        => '',
			);

            update_wpcs_option('wpcs_options', $this->options);
		}

		if ( ! empty( $_GET['variant'] ) && in_array( $_GET['variant'], array_keys( $this->langs ) ) ) {
			$this->admin_lang = $_GET['variant'];
		} else if ( in_array( $locale, array_keys( $this->langs ) ) ) {
			$this->admin_lang = $locale;
		}
		$this->base = str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) );
		if ( is_network_admin() && wpcs_mobile_exist( 'network' ) ) {
			// 在网络管理员界面
			$this->url = network_admin_url( 'settings.php?page=' . $this->base . 'wpchinese-switcher.php' );
		} else {
			// 在子站点管理员界面
			$this->url = admin_url( 'options-general.php?page=' . $this->base . 'wpchinese-switcher.php' );
		}
		add_filter( 'plugin_action_links', array( &$this, 'action_links' ), 10, 2 );
		if ( is_multisite() && wpcs_mobile_exist( 'network' ) ) {
			add_submenu_page( 'settings.php', 'WP Chinese Switcher', 'Chinese Switcher', 'manage_network_options', $this->base . 'wpchinese-switcher.php', array(
				&$this,
				'display_options'
			) );
		} else {
			add_options_page( 'WP Chinese Switcher', 'Chinese Switcher', 'manage_options', $this->base . 'wpchinese-switcher.php', array(
				&$this,
				'display_options'
			) );
		}

		wp_enqueue_script( 'jquery' );
	}

	function action_links( $links, $file ) {
		if ( $file == $this->base . 'wpchinese-switcher.php' ) {
			$links[] = '<a href="options-general.php?page=' . $file . '" title="Change Settings">Settings</a>';
		}

		return $links;
	}

	function install_cache_module() {
		global $wpcs_options;

		$ret = true;

		$file = file_get_contents( dirname( __FILE__ ) . '/wpcs-wp-super-cache-plugin.php' );

		$used_langs = 'Array()';
		if ( count( $wpcs_options['wpcs_used_langs'] ) > 0 ) {
			$used_langs = "Array('" . implode( "', '", $wpcs_options['wpcs_used_langs'] ) . "')";
		}
		$file = str_replace( '##wpcs_auto_language_recong##',
			$wpcs_options['wpcs_auto_language_recong'], $file );
		$file = str_replace( '##wpcs_used_langs##',
			$used_langs, $file );

		$fp = @fopen( WP_PLUGIN_DIR . '/wp-super-cache/plugins/wpcs-wp-super-cache-plugin.php', 'w' );
		if ( $fp ) {
			fputs( $fp, $file );
			fclose( $fp );
		} else {
			$ret = false;
		}

		return true;
	}

	function uninstall_cache_module() {
		return unlink( WP_PLUGIN_DIR . '/wp-super-cache/plugins/wpcs-wp-super-cache-plugin.php' );
	}

	function get_cache_status() {
		if ( ! is_plugin_active( 'wp-super-cache/wp-cache.php' ) ) {
			return 0; // not activated
		}
		if ( ! file_exists( WP_PLUGIN_DIR . '/wp-super-cache/plugins/wpcs-wp-super-cache-plugin.php' ) ) {
			return 1;
		}

		return 2;
	}

	function display_options() {
		global $wp_rewrite;

		if ( ! empty( $_POST['wpcso_uninstall_nonce'] ) ) {
			delete_option( 'wpcs_options' );
			update_option( 'rewrite_rules', '' );
			echo '<div class="wrap"><h2>WP Chinese Switcher Setting</h2><div class="updated">Uninstall Successfully. 卸载成功, 现在您可以到<a href="plugins.php">插件菜单</a>里禁用本插件.</div></div>';

			return;
		} else if ( $this->options === false ) {
			echo '<div class="wrap"><h2>WP Chinese Switcher Setting</h2><div class="error">错误: 没有找到配置信息, 可能由于WordPress系统错误或者您已经卸载了本插件. 您可以<a href="plugins.php">尝试</a>禁用本插件后再重新激活.</div></div>';

			return;
		}

		if ( ! empty( $_POST['toggle_cache'] ) ) {
			if ( $this->get_cache_status() == 1 ) {
				$result = $this->install_cache_module();
				if ( $result ) {
					echo '<div class="updated fade" style=""><p>' . _e( '安装WP Super Cache 兼容成功.', 'wpchinese-switcher' ) . '</p></div>';
				} else {
					echo '<div class="error" style=""><p>' . _e( '错误: 安装WP Super Cache 兼容失败', 'wpchinese-switcher' ) . '.</p></div>';
				}
			} else if ( $this->get_cache_status() == 2 ) {
				$result = $this->uninstall_cache_module();
				if ( $result ) {
					echo '<div class="updated fade" style=""><p>' . _e( '卸载WP Super Cache 兼容成功', 'wpchinese-switcher' ) . '.</p></div>';
				} else {
					echo '<div class="error" style=""><p>' . _e( '错误: 卸载WP Super Cache 兼容失败', 'wpchinese-switcher' ) . '.</p></div>';
				}
			}
		}

		if ( ! empty( $_POST['wpcso_submitted'] ) ) {
			$this->is_submitted = true;
			$this->process();

			if ( $this->get_cache_status() == 2 ) {
				$this->install_cache_module();
			}
		}
		?>
        <script type="text/javascript">
            //<!--
            function toggleVisibility(id) {
                var e = document.getElementById(id);
                if (!e) return;
                if (e.style.display == "block")
                    e.style.display = "none";
                else
                    e.style.display = "block";
                return false;
            }

            //-->
        </script>
        <div class="wrap">
            <style>

                html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code, del, dfn, em, font, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, dl, dt, dd, ol, ul, li, fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td {
                    font-family: "Source Han Sans SC", "Noto Sans CJK SC", "Source Han Sans CN", "Noto Sans SC", "Source Han Sans TC", "Noto Sans CJK TC", sans-serif, -apple-system, "Noto Sans", "Helvetica Neue", Helvetica, "Nimbus Sans L", Arial, "Liberation Sans", "PingFang SC", "Hiragino Sans GB", "Noto Sans CJK SC", "Source Han Sans SC", "Source Han Sans CN", "Microsoft YaHei", "Wenquanyi Micro Hei", "WenQuanYi Zen Hei", "ST Heiti", SimHei, "WenQuanYi Zen Hei Sharp", sans-serif;
                }

                .wrap {
                    margin: 3% 2%;
                    max-width: 1128px;
                    margin-left: auto;
                    margin-right: auto;
                    width: 100%;
                }

                .form-table td {
                    margin-bottom: 9px;
                    padding: 3% 2%;
                    line-height: 2;
                    vertical-align: middle;
                }

                hr {
                    border-top: 1px solid #6a6a6a;
                    border-bottom: 0px solid #6a6a6a;
                }

                div#wpcs_block_cache {
                    padding: 3% 2%;
                }

                form#wpcso_uninstall_form {
                    padding: 3% 2%;
                }

                .tooltip {
                    position: relative;
                    display: inline-block;
                    cursor: pointer;
                }

                .tooltip .tooltiptext {
                    visibility: hidden;
                    width: 400px; /* 宽度可以根据内容调整 */
                    background-color: #555; /* 深灰色背景 */
                    color: #fff; /* 文字颜色 */
                    padding: 15px 20px; /* 上下填充5px，左右填充10px */
                    border-radius: 6px; /* 圆角 */
                    zoom: 85%;
                    font-weight: 300;
                    /* 位置 */
                    position: absolute;
                    z-index: 1;
                    bottom: 150%; /* 调整位置确保提示不会与其他元素重叠 */
                    left: 50%;
                    margin-left: -100px; /* 根据宽度调整，保持居中 */

                    /* 箭头效果 */
                    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2); /* 阴影效果 */

                    /* 淡入效果 */
                    opacity: 0;
                    transition: opacity 0.3s;
                }

                .tooltip:hover .tooltiptext {
                    visibility: visible;
                    opacity: 1;
                }

                .tooltip::after {
                    font-family: 'dashicons';
                    content: "\f223";
                    padding: 2px 5px;
                    font-size: 18px;
                }


                .tab button {
                    background-color: #f1f1f1;
                    float: left;
                    border: none;
                    outline: none;
                    cursor: pointer;
                    padding: 14px 16px;
                    transition: 0.3s;
                }

                .tab button:hover {
                    background-color: #ddd;
                }

                .tab button.active {
                    background-color: #ccc;
                }

                .tabcontent {
                    display: none;
                    padding: 5% 2%;
                    border: 1px solid #ccc;
                    border-top: none;
                    clear: both;
                    border: 1px solid #c3c4c7;
                    box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
                    background: #fff;
                    box-sizing: border-box;
                }

            </style>

            <script>
                function openTab(evt, tabName) {
                    var i, tabcontent, tablinks;

                    tabcontent = document.getElementsByClassName("tabcontent");
                    for (i = 0; i < tabcontent.length; i++) {
                        tabcontent[i].style.display = "none";
                    }

                    tablinks = document.getElementsByClassName("tablinks");
                    for (i = 0; i < tablinks.length; i++) {
                        tablinks[i].className = tablinks[i].className.replace(" active", "");
                    }

                    document.getElementById(tabName).style.display = "block";
                    if (evt) evt.currentTarget.className += " active";
                }

                // 当页面加载完成后自动激活Tab1
                document.addEventListener("DOMContentLoaded", function () {
                    // 默认打开Tab1
                    document.querySelector(".tablinks").click();
                });
            </script>


            <div style="padding: 2px 5px 0 0;"><?php _e( '选择管理后台语言：', 'wpchinese-switcher' );
				echo $this->navi(); ?></div>
            <h2>WP Chinese Switcher Settings</h2>
			<?php ob_start(); ?>
			<?php if ( $this->is_submitted && $this->is_success ) { ?>
                <div class="updated fade" style=""><p><?php echo $this->message; ?></p></div>
			<?php } else if ( $this->is_submitted && $this->is_error ) { ?>
                <div class="error" style=""><p><?php echo $this->message; ?></p></div>
			<?php } ?>
            <p><?php printf( __( '版本 %s', 'wpchinese-switcher' ), wpcs_VERSION ); ?>. <a href="https://wenpai.org/"
                                                                                           title="<?php _e( '文派开源', 'wpchinese-switcher' ); ?>"
                                                                                           target="_blank"><?php _e( '文派开源', 'wpchinese-switcher' ); ?></a>
                | <a
                        href="https://wpchinese.cn" target="_blank"
                        title="<?php _e( '插件主页', 'wpchinese-switcher' ); ?>"><?php _e( '插件主页', 'wpchinese-switcher' ); ?></a>
            </p>

            <div class="tab">
                <button class="tablinks" onclick="openTab(event, 'Tab1')">
					<?php echo __( '基础设置', 'wpchinese-switcher' ); ?>
                </button>
                <button class="tablinks"
                        onclick="openTab(event, 'Tab2')"><?php echo __( '高级设置', 'wpchinese-switcher' ); ?></button>
            </div>

            <div id="Tab1" class="tabcontent">
                <form id="wpcso_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>"><input type="hidden"
                                                                                                           name="wpcso_submitted"
                                                                                                           value="1"/>
                    <table class="form-table">
                        <tbody>

                        <tr>
                            <td valign="top"
                                width="30%"><?php _e( '自定义<code>"不转换"</code>标签名称', 'wpchinese-switcher' ); ?>
                                <span class="tooltip">
    <span class="tooltiptext"><?php _e( '注意：本插件的自带小工具中将包含当前页面原始版本链接, 您可在此自定义其显示名称。留空则使用默认的"不转换"。', 'wpchinese-switcher' ); ?></span>
  </span>
                            </td>
                            </span>
                            </td>
                            <td><!--wpcs_NC_START-->
                                <input type="text" style="width: 100px;" name="wpcso_no_conversion_tip"
                                       id="wpcso_no_conversion_tip"
                                       value="<?php echo esc_html( $this->options['nctip'] ); ?>"/><!--wpcs_NC_END-->
                            </td>
                        </tr>

                        <tr>
                            <td valign="top" width="30%"><?php _e( '选择可用的中文语系模块', 'wpchinese-switcher' ); ?>
                                <span class="tooltip">
    <span class="tooltiptext"><?php _e( "注意：此项为全局设置，请至少勾选一种中文语言，否则插件无法正常运行。支持自定义名称，留空为默认值", 'wpchinese-switcher' ); ?>。</span>
  </span>
                            </td>
                            <td><!--wpcs_NC_START-->
								<?php foreach ( $this->langs as $key => $value ) { ?>
                                <input type="checkbox" id="wpcso_variant_<?php echo $key; ?>"
                                       name="wpcso_variant_<?php echo $key; ?>"<?php echo in_array( $key, $this->options['wpcs_used_langs'] ) ? ' checked="checked"' : ''; ?> />
                                <label for="wpcso_variant_<?php echo $key; ?>"><?php $str = $value[2] . ' (' . $key . ')';
									echo str_replace( ' ', '&nbsp;', str_pad( $str, 14 + strlen( $str ) - mb_strlen( $str ) ) ); ?></label>
                                <input type="text"
                                       placeholder="<?php esc_attr_e( '请输入显示名（默认值如左）', 'wpchinese-switcher' ); ?>"
                                       style="width: 200px;margin-bottom: 5px"
                                       name="<?php echo $this->langs[ $key ][1]; ?>"
                                       value="<?php echo ! empty( $this->options[ $value[1] ] ) ? esc_html( $this->options[ $value[1] ] ) : ''; ?>"/><br/>
								<?php } ?><!--wpcs_NC_END-->
                            </td>
                        </tr>

                        <tr>
                            <td valign="top" width="30%"><?php _e( '简繁切换按钮的展示形式', 'wpchinese-switcher' ); ?>
                                <span class="tooltip">
                                    <span class="tooltiptext"><?php _e( '注意：插件内置了两种模式，您可以修改语言切换按钮的展现方式来满足个性化需求，或使用底部转换 URL 链接自行调用。', 'wpchinese-switcher' ); ?></span>
                                </span>
                            </td>
                            <td>
								<?php $wpcs_translate_type = $this->options['wpcs_translate_type'] ?? 0 ?>
                                <select id="wpcs_translate_type" value="<?php echo $wpcs_translate_type ?>"
                                        name="wpcs_translate_type" style="width: 250px;">
                                    <option value="0"<?php echo $wpcs_translate_type == 0 ? ' selected="selected"' : ''; ?>>
										<?php _e( '平铺', 'wpchinese-switcher' ); ?>
                                    </option>
                                    <option value="1"<?php echo $wpcs_translate_type == 1 ? ' selected="selected"' : ''; ?>>
										<?php _e( '下拉列表', 'wpchinese-switcher' ); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td scope="row" valign="top" width="30%">
								<?php _e( '中文搜索关键词简繁转换', 'wpchinese-switcher' ) ?>
                                <span class="tooltip">
                                    <span class="tooltiptext">
                                        <?php _e( '注意：此选项将增强 WordPress 对中文繁简关键词的搜索能力。</br><hr>
                  1、例如搜索"<!--wpcs_NC_START--><code>网络</code><!--wpcs_NC_END-->"时, 数据库里含有"
                        <!--wpcs_NC_START--><code>网络</code><!--wpcs_NC_END-->",
                  "<!--wpcs_NC_START--><code>网络</code><!--wpcs_NC_END-->" 和"<!--wpcs_NC_START--><code>网络</code>
                        <!--wpcs_NC_END-->"的文章都会放到搜索结果里返回。</br>
                  2、支持多个中文词语搜索, 如搜索"<!--wpcs_NC_START--><code>简体 繁体</code><!--wpcs_NC_END-->"时,
                  含有"<!--wpcs_NC_START--><code>简体</code><!--wpcs_NC_END-->"和"<!--wpcs_NC_START--><code>繁体</code>
                        <!--wpcs_NC_END-->"两个词的文章也会被返回。</br><hr>
                 提示：此功能将增加搜索时数据库负担，低配服务器建议关闭。', 'wpchinese-switcher' ); ?>
                                    </span>
                                </span>
                            </td>
                            <td>
                                <select id="wpcso_search_conversion" name="wpcso_search_conversion"
                                        style="width: 250px;">
                                    <option value="2"<?php echo $this->options['wpcs_search_conversion'] == 2 ? ' selected="selected"' : ''; ?>>
										<?php echo __( '开启', 'wpchinese-switcher' ); ?>
                                    </option>
                                    <option value="0"<?php echo ( $this->options['wpcs_search_conversion'] != 2 && $this->options['wpcs_search_conversion'] != 1 ) ? ' selected="selected"' : ''; ?>>
										<?php echo __( '关闭', 'wpchinese-switcher' ); ?>
                                    </option>
                                    <option value="1"<?php echo $this->options['wpcs_search_conversion'] == 1 ? ' selected="selected"' : ''; ?>>
										<?php echo __( '仅语言非"不转换"时开启（默认）', 'wpchinese-switcher' ); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td valign="top" width="30%">
								<?php _e( '识别浏览器中文语言动作', 'wpchinese-switcher' ); ?>
                                <span class="tooltip">
                                    <span class="tooltiptext">
                                        <b><?php _e( '注意：此项设置不会应用于搜索引擎。', 'wpchinese-switcher' ); ?></b> <hr>
                                        <?php _e( '1、设置为非"关闭"，将自动识别访客浏览器首选中文语言。', 'wpchinese-switcher' ); ?><br/>
                                        <?php _e( '2、设置"跳转至…"，将302重定向到访客浏览器首选语言版本。', 'wpchinese-switcher' ); ?><br/>
                                        <?php _e( '3、设置"显示为…"，将直接显示中文转换版本而不重定向。', 'wpchinese-switcher' ); ?><br/><hr>
                                        <?php _e( '提示：当您设置"显示为…"时， 必须把选项"Cookie识别用户语言偏好"关闭，或也设置为"显示为…"相同选项。否则插件只会在浏览器第一次访问时直接显示，其他情况依旧跳转。', 'wpchinese-switcher' ); ?>
                                        <br/><br/>
                                        <b><?php _e( '说明：关于"允许不同语系内通用"复选项', 'wpchinese-switcher' ); ?></b>
                                        <?php _e( '此项仅在"识别浏览器动作"选项不为"关闭"时才有效。', 'wpchinese-switcher' ); ?><br/><hr>
                                        <?php _e( '1、假如您在本页设置里禁用了部分中文，如<code>zh-hk</code>， 那么浏览器里只有"<!--wpcs_NC_START--> 港澳繁体 <!--wpcs_NC_END--> "的用户访问网站时默认不会被识别;', 'wpchinese-switcher' ); ?><br/>
                                        <?php _e( '2、如果选中了此复选框，只要您开启了"<!--wpcs_NC_START--> 繁体中文<!--wpcs_NC_END--> ", "
                        <!--wpcs_NC_START--> 台湾正体
                        <!--wpcs_NC_END--> "
                  或 "<!--wpcs_NC_START--> 港澳繁体<!--wpcs_NC_END--> " 中任一种语言， 浏览器使用这几种语言的用户都会被插件识别并根据选项做出动作。', 'wpchinese-switcher' ); ?><br/>
                                        <?php _e( '3、此时页面被转换后的语言可能并不是用户浏览器设置的那种, 而是您开启的对应繁体语言。简体语系同理。', 'wpchinese-switcher' ); ?>
                                    </span>
                                </span>
                            </td>
                            <td>
                                <select id="wpcso_browser_redirect" value="" name="wpcso_browser_redirect"
                                        style="width: 250px;">
                                    <option value="2"<?php echo $this->options['wpcs_browser_redirect'] == 2 ? ' selected="selected"' : ''; ?>>
										<?php _e( '显示为对应繁简版本', 'wpchinese-switcher' ); ?>
                                    </option>
                                    <option value="1"<?php echo $this->options['wpcs_browser_redirect'] == 1 ? ' selected="selected"' : ''; ?>>
										<?php _e( '跳转至对应繁简页面', 'wpchinese-switcher' ); ?>
                                    </option>
                                    <option value="0"<?php echo $this->options['wpcs_browser_redirect'] == 0 ? ' selected="selected"' : ''; ?>>
										<?php _e( '关闭（默认值）', 'wpchinese-switcher' ); ?>
                                    </option>
                                </select> <input type="checkbox" name="wpcso_auto_language_recong"
                                                 id="wpcso_auto_language_recong"
                                                 value=""<?php echo $this->options['wpcs_auto_language_recong'] == 1 ? ' checked="checked"' : ''; ?> /><label
                                        for="wpcso_auto_language_recong"><?php _e( '允许不同语系内通用', 'wpchinese-switcher' ); ?>
                                    。</label>
                            </td>
                        </tr>

                        <tr>
                            <td valign="top" width="30%">
								<?php _e( 'Cookie识别用户语言偏好', 'wpchinese-switcher' ); ?>
                                <span class="tooltip">
                    <span class="tooltiptext"><?php _e( '注意：<b>本项设置不会应用于搜索引擎。</b> 如果开启这项设置，本插件将自动保存访客的语言选择。</br><hr>举例而言,
                  当访客通过 "', 'wpchinese-switcher' ); ?><?php echo $this->options['wpcs_use_permalink'] ?
		                    esc_html( trailingslashit( wpcs_link_conversion( get_option( 'home' ) . '/', 'zh-tw' ) ) ) :
		                    esc_html( wpcs_link_conversion( get_option( 'home' ) . '/', 'zh-tw' ) ); ?>"
                            <?php _e( '链接访问了您网站的台湾繁体版本时，进程信息将保存到Cookie中。<br/><br/> 如果该访客重启浏览器并通过', 'wpchinese-switcher' ); ?>
                   "<?php echo get_option( 'home' ); ?>/"
                   <?php _e( '再次访问您网站时,
                  则会自动跳转至繁体地址。如果设置为"显示为对应繁简内容"，则无需跳转。
                  (参见上一项说明)<br/><br/>如果您使用了WP Super Cache/ Hyper Cache之类缓存插件,
                  请把这两项设置均设为"关闭"，否则这两功能不仅不会正常工作，还可能造成缓存异常。<b><hr>提示：本选项和上方"识别浏览器"选项均与缓存插件不兼容</b>。</span>
                </span>', 'wpchinese-switcher' ); ?>
                            </td>
                            <td>
                                <select id="wpcso_use_cookie_variant" value="" name="wpcso_use_cookie_variant"
                                        style="width: 250px;">';
                                    <option value="2"<?php echo $this->options['wpcs_use_cookie_variant'] == 2 ? ' selected="selected"' : ''; ?>>
										<?php _e( '显示为对应繁简版本', 'wpchinese-switcher' ); ?>
                                    </option>
                                    <option value="1"<?php echo $this->options['wpcs_use_cookie_variant'] == 1 ? ' selected="selected"' : ''; ?>>
										<?php _e( '跳转至对应繁简页面', 'wpchinese-switcher' ); ?>
                                    </option>
                                    <option value="0"<?php echo $this->options['wpcs_use_cookie_variant'] == 0 ? ' selected="selected"' : ''; ?>>
										<?php _e( '关闭（默认值）', 'wpchinese-switcher' ); ?>
                                    </option>
                                </select>
                                <input type="checkbox" name="wpcso_auto_language_recong"
                                       id="wpcso_auto_language_recong"
                                       value=""<?php echo $this->options['wpcs_auto_language_recong'] == 1 ? ' checked="checked"' : ''; ?> />
                                <label for="wpcso_auto_language_recong"><?php _e( '允许不同语系内通用', 'wpchinese-switcher' ); ?>
                                    。</label>
                            </td>
                        </tr>

                        <tr>
                            <td valign="top" width="30%">
								<?php _e( '排除某些HTML标签内中文', 'wpchinese-switcher' ); ?>
                                <span class="tooltip">
                                    <span class="tooltiptext">
                                        <?php _e( '注意：这里输入的HTML标签里内容将不进行中文繁简转换（仅适用文章内容）， 保持原样输出。请输入HTML标签名：', 'wpchinese-switcher' ); ?></br>
                                        <hr>
                                        <?php _e( '如', 'wpchinese-switcher' ); ?> <code>pre</code>;
                                        <?php _e( '多个HTML标签之间以', 'wpchinese-switcher' ); ?> <code>,</code> <?php _e( '分割，如', 'wpchinese-switcher' ); ?> <code>pre,code</code>.
                                        <?php _e( '支持部分基本的', 'wpchinese-switcher' ); ?><?php _e( 'CSS选择器', 'wpchinese-switcher' ); ?><?php _e( '的DOM筛选语法，如', 'wpchinese-switcher' ); ?><code>div.nocc</code>,
                                        <code>.class1,div#para1</code>, <code>table,span.nocc,div[attr="hello"]</code>.</br>
                                        <?php _e( '如遇HTML错误，请关闭此选项。', 'wpchinese-switcher' ); ?>
                                    </span>
                                </span>
                            </td>
                            <td>
                                <input type="text"
                                       value="<?php echo esc_attr( $this->options['wpcs_no_conversion_tag'] ); ?>"
                                       style="width: 250px;"
                                       name="wpcso_no_conversion_tag" id="wpcso_no_conversion_tag"/>
                                (<?php _e( '默认为空', 'wpchinese-switcher' ); ?>)
                            </td>
                        </tr>

                        <tr>
                            <td valign="top" width="30%">
								<?php _e( '排除日语<code>(lang="ja")</code>标签', 'wpchinese-switcher' ); ?>
                                <span class="tooltip">
                                    <span class="tooltiptext">
                                        <?php _e( '注意：如果选中此选项, 文章内容中用 lang="ja" 标记为日语的 HTML  标签将不进行繁简转换, 保持原样输出。</br><hr>', 'wpchinese-switcher' ); ?>
	                                    <?php _e( '例如:', 'wpchinese-switcher' ); ?> "<!--wpcs_NC_START--><code
                                                lang="ja">&lt;span lang="ja"&gt;あなたを、おつれしましょうか？ この町の願いが叶う場所に。&lt;/span&gt;</code>
                                        <!--wpcs_NC_END-->"
                                        <?php _e( '中的CJK汉字', 'wpchinese-switcher' ); ?> <!--wpcs_NC_START--><code
                                                lang="ja">連</code>
                                        <!--wpcs_NC_END--> <?php _e( '和', 'wpchinese-switcher' ); ?> <!--wpcs_NC_START--><code
                                                lang="ja">叶</code>
                                        <!--wpcs_NC_END--> <?php _e( '将不会进行繁简转换。', 'wpchinese-switcher' ); ?>
	                                    <?php _e( '如遇HTML错误，请关闭此选项。', 'wpchinese-switcher' ); ?>
                                    </span>
                                </span>
                            </td>
                            <td>
                                <input type="checkbox" name="wpcso_no_conversion_ja"
                                       id="wpcso_no_conversion_ja" <?php echo ! empty( $this->options['wpcs_no_conversion_ja'] ) ? ' checked="checked"' : ''; ?> />
                                <label for="wpcso_no_conversion_ja"><?php _e( '(默认关闭)', 'wpchinese-switcher' ); ?></label>
                            </td>
                        </tr>

                        <tr>
                            <td valign="top" width="30%"><?php _e( '排除HTML中任意内容TAG', 'wpchinese-switcher' ); ?>
                                <span class="tooltip">
                                    <span class="tooltiptext"><?php _e( '注意：您可以在页面模板或文章内容中使用以下标签。</br><hr>
                                    1、如需原样输出内容，可使用 HTML 标签<code>&lt;!--wpcs_NC_START--&gt;</code>和<code>&lt;!--wpcs_NC_END--&gt;</code> 插入内容。
                                            <br/>2、您可以在经典编辑器（HTML模式）工具栏中插入一个按钮(显示为"wpcs_NC"), 方便快速在文章中插入此标签。', 'wpchinese-switcher' ); ?></span>
                                </span>
                            </td>
                            <td>
                                <!--wpcs_NC_START--><code>&lt;!--wpcs_NC_START--&gt;文人墨客，文派墨图，文风笔笙&lt;!--wpcs_NC_END--&gt;</code>
                                <!--wpcs_NC_END--><br/>
                                <input type="checkbox" name="wpcso_no_conversion_qtag"
                                       id="wpcso_no_conversion_qtag" <?php checked( ! empty( $this->options['wpcs_no_conversion_qtag'] ) ); ?> />
                                <label for="wpcso_no_conversion_qtag"><?php _e( '为经典编辑器添加此"不转换中文"的按钮标签', 'wpchinese-switcher' ); ?></label>
                            </td>
                        </tr>

                        <tr>
                            <td valign="top" width="30%">
								<?php _e( '繁简转换页面永久链接格式', 'wpchinese-switcher' ); ?>
                                <span class="tooltip">
                                    <span class="tooltiptext">
                                        <?php _e( '注意：此选项影响插件生成的转换页面链接，请仔细阅读：</br><hr>
1、默认形式：为原始固定链接后加上<code>?variant=zh-tw</code>参数，其中<code>"zh-tw"</code>为对应语言代码。</br>
2、可选格式：原始固定链接后加上<code>/zh-tw</code>或<code>/zh-tw/</code>；或<code>/zh-tw</code>后加上原始固定链接。对于  SEO 很有用。</br>
3、有无斜杠：URL末尾是否有<code>/</code>取决于您的网站固定链接末尾是否有<code>/</code>，首页的繁简转换版本URL末尾永远有<code>/</code>。</br><hr>
提示：若未开启固定链接，则只能选第一种默认URL形式。', 'wpchinese-switcher' ); ?>
                                    </span>
                                </span>
                            </td>
                            <td>
                                <label><input type="radio" name="wpcso_use_permalink"
                                              value="0"<?php echo $this->options['wpcs_use_permalink'] == 0 ? ' checked="checked"' : ''; ?> />
                                    <code><?php echo home_url() . ( empty( $wp_rewrite->permalink_structure ) ? '/?p=123&variant=zh-tw' : $wp_rewrite->permalink_structure . '?variant=zh-tw' ); ?></code>
                                    (默认)</label><br/>
                                <label><input type="radio" name="wpcso_use_permalink"
                                              value="1"<?php echo empty( $wp_rewrite->permalink_structure ) ? ' disabled="disabled"' : ''; ?><?php echo $this->options['wpcs_use_permalink'] == 1 ? ' checked="checked"' : ''; ?> />
                                    <code><?php echo home_url() . user_trailingslashit( trailingslashit( $wp_rewrite->permalink_structure ) . 'zh-tw' ) . ( empty( $wp_rewrite->permalink_structure ) ? '/' : '' ); ?></code></label><br/>
                                <label><input type="radio" name="wpcso_use_permalink"
                                              value="2"<?php echo empty( $wp_rewrite->permalink_structure ) ? ' disabled="disabled"' : ''; ?><?php echo $this->options['wpcs_use_permalink'] == 2 ? ' checked="checked"' : ''; ?> />
                                    <code><?php echo home_url() . '/zh-tw' . $wp_rewrite->permalink_structure . ( empty( $wp_rewrite->permalink_structure ) ? '/' : '' ); ?></code></label><br/>
                            </td>
                        </tr>
						<?php
						global $wpcs_modules;
						if ( wpcs_mobile_exist( 'sitemap' ) ):
							?>
                            <tr>
                                <td valign="top" width="30%">
									<?php _e( '是否启用多语言网站地图', 'wpchinese-switcher' ); ?>
                                    <span class="tooltip">
                                    <span class="tooltiptext">
                                        <?php _e( '网站地图的访问地址为：https://域名/zh-tw/wp-sitemap.xml，其中zh-tw可替换成你想访问的语言', 'wpchinese-switcher' ); ?>
                                    </span>
                                </span>
                                </td>
                                <td>
                                    <input type="checkbox" id="wpcso_use_sitemap"
                                           name="wpcso_use_sitemap"<?php checked( $this->options['wpcso_use_sitemap'], 1 ); ?> />
                                    <label for="wpcso_use_sitemap"><?php _e( '(默认开启)', 'wpchinese-switcher' ); ?></label>
                                </td>
                            </tr>

                            <tr>
                                <td valign="top" width="30%">
									<?php _e( '网站地图支持的post_type', 'wpchinese-switcher' ); ?>
                                    <span class="tooltip">
                                    <span class="tooltiptext">
                                        <?php _e( '默认为 post 和 page 生成地图，如果你需要添加自定义 post_type 请自行添加后用逗号分隔。', 'wpchinese-switcher' ); ?>
                                    </span>
                                </span>
                                </td>
                                <td>
                                    <input type="text"
                                           value="<?php echo esc_attr( $this->options['wpcso_sitemap_post_type'] ?? 'post,page' ); ?>"
                                           style="width: 250px;"
                                           name="wpcso_sitemap_post_type" id="wpcso_sitemap_post_type"/>
                                    (<?php _e( '默认为:post,page', 'wpchinese-switcher' ); ?>)
                                </td>
                            </tr>
						<?php endif; ?>
                        <tr>
                            <td valign="top" width="30%">
								<?php _e( '启用对页面内容的整体转换', 'wpchinese-switcher' ); ?>
                                <span class="tooltip">
                                    <span class="tooltiptext">
                                        <?php _e( '注意：开启后，将极大提高页面生成速度并减少资源使用。</br><hr>
                                        1、插件将对 WordPress 全部页面内容进行中文整体转换（使用 ob_start 和 ob_flush 函数），
                                        </br>2、如果遇到异常（包括中文转换错误，HTML页面错误或PHP错误等），请关闭此选项。', 'wpchinese-switcher' ); ?>
                                    </span>
                                </span>
                            </td>
                            <td>
                                <input type="checkbox" id="wpcso_use_fullpage_conversion"
                                       name="wpcso_use_fullpage_conversion"<?php checked( $this->options['wpcs_use_fullpage_conversion'], 1 ); ?> />
                                <label for="wpcso_use_fullpage_conversion"><?php _e( '(默认开启)', 'wpchinese-switcher' ); ?></label>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <input class="button" type="submit" name="submit"
                                       value="<?php esc_attr_e( '保存选项', 'wpchinese-switcher' ); ?>"/>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>

            <div id="Tab2" class="tabcontent">
				<?php
				$cache_status = $this->get_cache_status();
				?>
                <div id="wpcs_block_cache" style="display: <?php
				echo ( $cache_status != 0 ) ? 'block' : 'none';
				?>;">
                    <div style="padding-top: 30px; padding-bottom: 20px; ">
						<?php _e( 'WP Super Cache兼容', 'wpchinese-switcher' ); ?>
                        <span class="tooltip">
                                <span class="tooltiptext">
                                    <?php _e( '注意：默认情况下， 本插件的"识别浏览器中文语言动作"和"Cookie识别用户语言偏好"这两个功能与缓存插件不兼容。如果您使用的是', 'wpchinese-switcher' ); ?><?php _e( 'WP Super Cache', 'wpchinese-switcher' ); ?><?php _e( '的"Legacy page caching"缓存模式, 您可以点击下面的按钮安装WP Super Cache兼容. ', 'wpchinese-switcher' ); ?><b><br/><br/><?php _e( '1、 如果您没有开启"识别浏览器中文语言动作"和"Cookie识别用户语言偏好"这两个功能(默认均为关闭)，则无需安装此兼容', 'wpchinese-switcher' ); ?></b>; <?php _e( '安装本兼容将增加WP Super Cache的缓存磁盘空间占用; ', 'wpchinese-switcher' ); ?><br/><br/><?php _e( '2、仅支持WP Super Cache插件的"Legacy page caching"模式, 不支持"PHP Cache"和"mod_rewrite cache"模式。(安装后默认为"PHP Cache"模式, 您必须手动切换到"Legacy"模式。)', 'wpchinese-switcher' ); ?>
                                </span>
                            </span>
                    </div>
                    <form id="wpcso_cache_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <table class="form-table">
                            <tbody>
                            <tr>
                                <td valign="top" width="30%"><?php
									if ( $cache_status == 2 ) {
										echo '<div style="font-weight: solid; width: 50px; color: green; border: 1px solid #333; margin: 2px; padding: 5px">' . __( "已安装", "wpchinese-switcher" ) . '</div>';
									} else if ( empty( $this->options['wpcs_browser_redirect'] ) && empty( $this->options['wpcs_use_cookie_variant'] ) ) {
										echo '<div style="color: green; font-weight: solid; width: 350px; border: 1px solid #333; margin: 2px; padding: 5px">' . __( "未开启\"识别浏览器中文语言动作\"和\"使用Cookie保存并识别用户语言偏好\"功能. 无需安装", "wpchinese-switcher" ) . '</div>';
									} else {
										echo '<div style="font-weight: solid; width: 50px; color: red; border: 1px solid #333; margin: 2px; padding: 5px">' . __( "未安装", "wpchinese-switcher" ) . '</div>';
									}
									?></td>
                                <td><?php
									if ( $cache_status == 2 ) {
										if ( empty( $GLOBALS['cache_enabled'] ) ) {
											echo '<div style="font-weight: solid; border: 1px solid #333; margin: 2px; padding: 5px">' . __( "WP Super Cache未开启缓存", "wpchinese-switcher" ) . '</div>';
										} else if ( ! empty( $GLOBALS['super_cache_enabled'] ) ) {
											echo '<div style="color: red; font-weight: solid; border: 1px solid #333; margin: 2px; padding: 5px">' . __( "警告: WP Super Cache未设为\"legacy page caching\"模式, 本兼容模块无法正常工作.", "wpchinese-switcher" ) . '</div>';
										}
									}
									?></td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="hidden" name="toggle_cache" value="1"/>
                                    <input class="button" type="submit" name="submit" value="<?php
									if ( $this->get_cache_status() == 0 ) {
										echo __( "未使用WP Super Cache插件", "wpchinese-switcher" );
									} else if ( $this->get_cache_status() == 1 ) {
										echo __( "安装兼容", "wpchinese-switcher" );
									} else {
										echo __( "卸载兼容", "wpchinese-switcher" );
									}
									?>" <?php
									if ( $this->get_cache_status() == 0 || ( empty( $this->options['wpcs_browser_redirect'] ) && empty( $this->options['wpcs_use_cookie_variant'] ) ) ) {
										echo 'disabled="disabled"';
									}
									?> />
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>

                <form id="wpcso_uninstall_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                    <table class="form-table">
                        <tbody>
                        <tr>
                            <td valign="top" width="30%"><?php _e( '确定卸载本插件?', 'wpchinese-switcher' ); ?>
                                <span class="tooltip">
                                    <span class="tooltiptext">
                                        <?php _e( '注意：这将清除数据库<code>wp_options</code>表中本插件的设置项（键值为 <code>wpcs_options</code>），提交后请到插件管理中禁用本插件。', 'wpchinese-switcher' ); ?>
                                    </span>
                                </span>
                            </td>
                            <td>
                                <input type="checkbox" name="wpcso_uninstall_nonce" id="wpcso_uninstall_nonce"
                                       value="1"/>
                                <label for="wpcso_uninstall_nonce"><?php _e( '确认卸载 (此操作不可逆)', 'wpchinese-switcher' ); ?></label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input class="button" type="submit" name="submit"
                                       value="<?php _e( '卸载插件', 'wpchinese-switcher' ); ?>"/>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div> <!-- close wrap div -->
		<?php
		$o = ob_get_clean();
		if ( $this->admin_lang ) {
			wpcs_load_conversion_table();
			$o = limit_zhconversion( $o, $this->langs[ $this->admin_lang ][0] );
		}
		echo $o;
	}

	function navi() {
		$variant = ! empty( $_GET['variant'] ) ? $_GET['variant'] : '';
		$str     = '<span><a title="' . __( '默认', 'wpchinese-switcher' ) . '" href="' . $this->url . '" ' . ( ! $variant ? 'style="color: #464646; text-decoration: none !important;"' : '' ) . ' >' . __( '默认', 'wpchinese-switcher' ) . '</a></span>&nbsp;';
		if ( ! $this->options['wpcs_used_langs'] ) {
			return $str;
		}
		foreach ( $this->langs as $key => $value ) {
			$str .= '<span><a href="' . $this->url . '&variant=' . $key . '" title="' . $value[2] . '" ' . ( $variant == $key ? 'style="color: #464646; text-decoration: none !important;"' : '' ) . '>' . $value[2] . '</a>&nbsp;</span>';
		}

		return $str;
	}

	function process() {
		global $wp_rewrite, $wpcs_options;
		$langs = array();
		foreach ( $this->langs as $key => $value ) {
			if ( isset( $_POST[ 'wpcso_variant_' . $key ] ) ) {
				$langs[] = $key;
			}
		}
		$options = array(
			'wpcs_used_langs'              => $langs,
			'wpcs_search_conversion'       => intval( $_POST['wpcso_search_conversion'] ),
			'wpcs_browser_redirect'        => intval( $_POST['wpcso_browser_redirect'] ),
			'wpcs_translate_type'          => ( isset( $_POST['wpcs_translate_type'] ) ? intval( $_POST['wpcs_translate_type'] ) : 0 ),
			// 0列表展示，1下拉展示
			'wpcs_use_cookie_variant'      => intval( $_POST['wpcso_use_cookie_variant'] ),
			'wpcs_use_fullpage_conversion' => ( isset( $_POST['wpcso_use_fullpage_conversion'] ) ? 1 : 0 ),
			'wpcso_use_sitemap'            => ( isset( $_POST['wpcso_use_sitemap'] ) ? 1 : 0 ),
			'wpcso_sitemap_post_type'      => ( isset( $_POST['wpcso_add_author_link'] ) ? trim( $_POST['wpcso_sitemap_post_type'] ) : 'post,page' ),
			'wpcs_trackback_plugin_author' => ( isset( $_POST['wpcso_trackback_plugin_author'] ) ? intval( $_POST['wpcso_trackback_plugin_author'] ) : 0 ),
			'wpcs_add_author_link'         => ( isset( $_POST['wpcso_add_author_link'] ) ? 1 : 0 ),
			'wpcs_use_permalink'           => intval( $_POST['wpcso_use_permalink'] ),
			'wpcs_auto_language_recong'    => ( isset( $_POST['wpcso_auto_language_recong'] ) ? 1 : 0 ),
			'wpcs_no_conversion_tag'       => trim( $_POST['wpcso_no_conversion_tag'], " \t\n\r\0\x0B,|" ),
			'wpcs_no_conversion_ja'        => ( isset( $_POST['wpcso_no_conversion_ja'] ) ? 1 : 0 ),
			'wpcs_no_conversion_qtag'      => ( isset( $_POST['wpcso_no_conversion_qtag'] ) ? 1 : 0 ),
			'nctip'                        => trim( $_POST['wpcso_no_conversion_tip'] ),
		);

		foreach ( $this->langs as $lang => $value ) {
			if ( ! empty( $_POST[ $value[1] ] ) ) {
				$options[ $value[1] ] = trim( $_POST[ $value[1] ] );
			}
		}

		if ( $this->get_cache_status() == 2 && empty( $options['wpcs_browser_redirect'] ) && empty( $options['wpcs_use_cookie_variant'] ) ) {
			$this->uninstall_cache_module();
		}

		$wpcs_options = $options; //因为可能需要刷新rewrite规则, 必须立即更新wpcs_options全局变量
		if ( $this->options['wpcs_use_permalink'] != $options['wpcs_use_permalink'] ||
		     ( $this->options['wpcs_use_permalink'] != 0 && $this->options['wpcs_used_langs'] != $options['wpcs_used_langs'] )
		) {
			if ( ! has_filter( 'rewrite_rules_array', 'wpcs_rewrite_rules' ) ) {
				add_filter( 'rewrite_rules_array', 'wpcs_rewrite_rules' );
			}
			$wp_rewrite->flush_rules();
		}

		update_wpcs_option( 'wpcs_options', $options );

		$this->options    = $options;
		$this->is_success = true;
		$this->message    .= '设置已更新。';
	}

}
