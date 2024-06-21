<?php

namespace Platform\Translate\WPOrgTranslateImport\Web;

use Platform\Translate\WPOrgTranslateImport\Service\Project;

class Import {
	function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

	public function add_admin_menu(): void {
		add_menu_page( '导入 WordPress.org 项目', '导入项目', 'manage_options', 'wporg-import', array(
			$this,
			'settings_page_html'
		), null, 99 );
	}

	public function settings_page_html(): void {
		// 确保用户有权限
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// 处理表单提交
		$this->handle_form_submit();
		if ( ! empty( get_transient( 'wporg_import_message' ) ) ) {
			$message = get_transient( 'wporg_import_message' );
		}
		?>
        <div class="wrap">
            <h1><?= esc_html( get_admin_page_title() ); ?></h1>
            <h2>导入 translate.wordpress.org 中存在的插件或主题到平台</h2>
			<?php if ( ! empty( $message ) ): ?>
                <div id="message" class="updated notice is-dismissible"><p><?php echo $message; ?></p></div>
			<?php endif; ?>
            <form method="post">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">选择类型</th>
                        <td>
                            <select name="type">
                                <option value="plugins">插件</option>
                                <option value="themes">主题</option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">别名（slug）</th>
                        <td>
                            <input type="text" name="slug" value=""/>
                        </td>
                    </tr>
                </table>
				<?php
				submit_button( '提交' );
				?>
            </form>
        </div>
		<?php
	}

	private function handle_form_submit(): void {
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['type'], $_POST['slug'] ) ) {
			$type = sanitize_text_field( $_POST['type'] );
			$slug = sanitize_text_field( $_POST['slug'] );
			if ( empty( $slug ) ) {
				set_transient( 'wporg_import_message', '别名不能为空', 5 );

				return;
			}

			$this->do_import( $type, $slug );
			set_transient( 'wporg_import_message', '触发导入成功，请前往 <a target="_blank" href="https://translate.wenpai.org/projects/' . $type . '/' . $slug . '">translate.wenpai.org/projects/' . $type . '/' . $slug . '</a> 查看是否成功。', 5 );
		}
	}

	private function do_import( $type, $slug ): void {
		$project = new Project();
		$project->import( $slug, $type );
	}
}
