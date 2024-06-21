<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package p2020
 */

namespace P2020;

require_once get_template_directory() . '/inc/class-ellipsis-menu.php';
require_once get_template_directory() . '/inc/menu/menu.php';

use function \P2020\html_output;

$customizer_menu_url = admin_url( 'nav-menus.php' );
$pages_url           = admin_url( 'edit.php?post_type=page' );
$editor_page_url     = admin_url( 'post-new.php?post_type=page' );
?>

<div id="sidebar" class="p2020-sidebar 
<?php
if ( is_page() ) {
	echo 'is-dark';
}
?>
">
	<div class="screen-reader-text skip-link">
		<a href="#content" title="<?php esc_attr_e( 'Skip to content', 'p2020' ); ?>">
			<?php esc_html_e( 'Skip to content', 'p2020' ); ?>
		</a>
	</div>

	<header class="p2020-sidebar__header">
		<div class="p2020-sidebar-padded-container">
			<?php get_template_part( 'partials/sidebar-header' ); ?>
		</div>
	</header>

	<div class="p2020-sidebar__main">

		<div data-sidebar-primary 
		<?php
		if ( is_page() ) {
			echo esc_attr( 'hidden' );}
		?>
		>
			<?php get_template_part( 'partials/sidebar-info' ); ?>

			<?php do_action( 'before_sidebar' ); ?>

			<?php
			if ( is_page() && is_active_sidebar( 'sidebar-pages' ) ) {
				dynamic_sidebar( 'sidebar-pages' );
			} else {
				dynamic_sidebar( 'sidebar-1' );
			}
			?>
		</div><!-- [data-sidebar-primary] -->

		<div data-sidebar-secondary 
		<?php
		if ( ! is_page() ) {
			echo esc_attr( 'hidden' );}
		?>
		>

			<div class="p2020-sidebar__menu">
				<div class="p2020-sidebar-padded-container">
					<div class="p2020-sidebar__menu-header">
						<h2><?php esc_html_e( 'Documents', 'p2020' ); ?></h2>
						<?php if ( current_user_can( 'editor' ) ) : ?>
							<div class="p2020-sidebar__menu-header-ellipsis">
								<?php
									$pages_menu = new \P2020\EllipsisMenu();
									$pages_menu->add_item( __( 'New document', 'p2020' ), $editor_page_url );
									$pages_menu->add_item( __( 'Manage documents', 'p2020' ), $pages_url );
									echo html_output( $pages_menu->generate() );
								?>
							</div>
						<?php endif; ?>
					</div>
					<?php \P2020\Menu\render_page_menu(); ?>
				</div>
			</div>

			<div class="p2020-sidebar__menu">
				<div class="p2020-sidebar-padded-container">
					<div class="p2020-sidebar__menu-header">
						<h2><?php esc_html_e( 'Links', 'p2020' ); ?></h2>
						<?php if ( current_user_can( 'customize' ) ) : ?>
							<div class="p2020-sidebar__menu-header-ellipsis">
								<?php
									$nav_menu = new \P2020\EllipsisMenu();
									$nav_menu->add_item( __( 'Manage links', 'p2020' ), $customizer_menu_url );
									echo html_output( $nav_menu->generate() );
								?>
							</div>
						<?php endif; ?>
					</div>
					<?php \P2020\Menu\render_nav_menu(); ?>
				</div>
			</div>

		</div><!-- [data-sidebar-secondary] -->

		<footer class="p2020-sidebar__footer">
			<div class="p2020-sidebar-padded-container">
				<ul class="p2020-sidebar__footer-links">
					<li>
						<?php
							// CAUTION: This class is a fragile hack to get O2 to attach its
							// cheatsheet toggling event listener to this element.
							// See https://opengrok.a8c.com/source/xref/trunk/wp-content/plugins/p2tenberg/p2/js/keyboard.js#35
						?>
						<button class="o2-toggle-keyboard-help">
							<?php esc_html_e( 'Keyboard shortcuts', 'p2020' ); ?>
						</button>
					</li>
				</ul>

				<p class="p2020-sidebar__footer-credit">
					<?php esc_html_e( 'Powered by WordPress', 'p2020' ); ?>
				</p>
			</div>
		</footer>

	</div><!-- .p2020-sidebar__main -->
<style>
html {
    -webkit-font-smoothing: antialiased;
}
body, button, input, select, textarea, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code, del, dfn, em, figure, font, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, dl, dt, dd, ol, ul, li, fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td {
    font-family: -apple-system, "Noto Sans", "Helvetica Neue", Helvetica, "Nimbus Sans L", Arial, "Liberation Sans", "PingFang SC", "Hiragino Sans GB", "Noto Sans CJK SC", "Source Han Sans SC", "Source Han Sans CN", "Microsoft YaHei", "Wenquanyi Micro Hei", "WenQuanYi Zen Hei", "ST Heiti", SimHei, "WenQuanYi Zen Hei Sharp", sans-serif;
}
.o2-editor .o2-editor-footer .o2-editor-tabs li a {
    background: #fafafa;
}
.o2-editor .o2-editor-toolbar-button {
    margin: 0.5em 0 0.2em 0px;
}
.o2-editor-content-wrapper {
    margin-top: 0px;
}	
.o2-editor-toolbar-wrapper {
    margin-bottom: 0px;
}	
.o2-editor .o2-editor-footer .o2-editor-tabs {
    margin: -5px 0-5px 0;
}	
.o2-editor .o2-editor-preview {
    padding: 0.4em 0.8em;
}	
.o2-editor .o2-editor-signin input {
    padding: 10px;
    border: 1px solid #ddd;
}	
.p2tenberg-editor .block-editor-writing-flow .tag, .comment-content .tag, .entry-content:not(.is-editing) .tag, .o2-editor-preview .tag, .p2tenberg-editor .block-editor-writing-flow .p2-xpost, .comment-content .p2-xpost, .entry-content:not(.is-editing) .p2-xpost, .o2-editor-preview .p2-xpost {
    background: #f3f3f3;
}	
.p2tenberg-editor .block-editor-writing-flow .editor-post-title__input, .p2tenberg-editor .block-editor-writing-flow h1, .comment-content h1, .entry-content:not(.is-editing) h1, .o2-editor-preview h1, .entry-title, .p2tenberg-editor .block-editor-writing-flow .p2tenberg-auto-title {
    font-weight: 600;
    font-size: 1.5rem;
}	
.p2020-sidebar {
   background: #000000;
}	
a#p2020-custom-header-partial {
   display: none;
}	
.p2020-sidebar {
   background-image: linear-gradient(to right, var(--color-sidebar-background) 50%, #000000 50%);
}
.p2tenberg-editor .block-editor-writing-flow p, .comment-content p, .entry-content:not(.is-editing) p, .o2-editor-preview p {
   color: #5b5b5b;
}	
</style>
</div>
