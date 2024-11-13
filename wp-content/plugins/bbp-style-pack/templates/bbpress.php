<?php
/**
 * bbpress file to emulate page.html to display forums
 *
 * 
 */
global $bsp_style_settings_theme_support ;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/***************  the version after 5.6.9
*/

if (empty ($bsp_style_settings_theme_support['fse_template_version'])) {

/*
Added blocks and PHP bits to create a PHP template for usage in a FSE theme
as described in https://fullsiteediting.com/lessons/how-to-use-php-templates-in-block-themes/
Please note that weirdly not 100% all CSS is loaded correctly, in my case some missing lines to do with
 .wp-container-core-group-layout-1.wp-container-core-group-layout-1 were added to bbpsp's Custom CSS
*/

?>

<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php wp_head(); ?>
</head>


<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="wp-site-blocks">

<header class="wp-block-template-part site-header">
<?php block_header_area(); ?>
</header>

<?php 
}
?>

<?php 

/************* pre 5.7.0 version */

if (!empty ($bsp_style_settings_theme_support['fse_template_version'])) {

block_template_part('header');

wp_head();	
	
}

// start bbpsp code
//*************if just the forums

if ($bsp_style_settings_theme_support['fse'] == 1) {

while ( have_posts() ) : the_post();
	?>

	<div class="bbpress-container bsp-fse-container">
		<?php the_title( '<h1 style="margin-bottom: 6rem; font-size: clamp(2.75rem, 6vw, 3.25rem);" class="alignwide wp-block-query-title">', '</h1>' ); ?>
		<div class="bbpress-content">
			<?php the_content(); ?>
		</div>
	</div>

<?php
endwhile;
}



if ($bsp_style_settings_theme_support['fse'] == 2) {
		echo '<div class="bbpress-container bsp-fse-container">' ;
		the_title( '<h1 style="margin-bottom: 6rem; font-size: clamp(2.75rem, 6vw, 3.25rem);" class="alignwide wp-block-query-title">', '</h1>' ); 
		//no template page set					
		if (empty ($bsp_style_settings_theme_support['fse_template_page'])) {
			if (current_user_can( 'manage_options' ) ) {
				esc_html_e('No template page selected for forums to display', 'bbp-style-pack');
				echo '<br/>' ;
				esc_html_e('See - ', 'bbp-style-pack');
				echo '<a href="' . site_url() . '/wp-admin/options-general.php?page=bbp-style-pack&tab=bsp_block_theme">' ;
				esc_html_e('Style Pack theme support settings', 'bbp-style-pack');
				echo '</a>' ;
			}
			//if not admin send to 404
			else {
				wp_redirect( home_url( '/404page/' ) );
				exit();
			}
		}
		//viewing template page itself
		elseif ($bsp_style_settings_theme_support['fse_template_page'] == get_the_ID() ){
			if (current_user_can( 'manage_options' ) ) {
				$forums_slug = bbp_get_root_slug() ;
				esc_html_e('You are trying to view the bbpress template page, you need to view the forums page itself at ', 'bbp-style-pack');
				echo '<br/>' ;
				echo '<a href="/'.$forums_slug.'">' ;
				echo get_site_url().'/'.$forums_slug ;
				echo '</a>' ;
				echo '<br/><br/>' ;
				esc_html_e('To add forums as a menu item, edit your menu and add this url as a link.', 'bbp-style-pack');
				echo '<br/><br/>' ;
				esc_html_e('See ', 'bbp-style-pack');
				echo '<a href="' . site_url() . '/wp-admin/options-general.php?page=bbp-style-pack&tab=bsp_block_theme">' ;
				esc_html_e('Style Pack theme support settings', 'bbp-style-pack');
				echo '</a>' ;
				esc_html_e(' for further information', 'bbp-style-pack');
			}
			//if not admin send to 404
			else {
				wp_redirect( home_url( '/404page/' ) );
				exit();
			}
		}
		else {
			$post_id = $bsp_style_settings_theme_support['fse_template_page'];
			$post_content = get_post($post_id);
			if (!str_contains($post_content->post_content, '[bbp-forum-index]')) {
				if (current_user_can( 'manage_options' ) ) {
					$page = get_the_title($post_id) ;
					esc_html_e('The template page you have selected' , 'bbp-style-pack') ;
					echo '( '.esc_html($page).' ) ' ;
					esc_html_e('does not contain the \'[bbp-forum-index]\' shortcode, so forums will not display', 'bbp-style-pack');
					echo '<br/>' ;
					esc_html_e('See - ', 'bbp-style-pack');
					echo '<a href="' . site_url() . '/wp-admin/options-general.php?page=bbp-style-pack&tab=bsp_block_theme">' ;
					echo esc_html_e('Style Pack theme support settings', 'bbp-style-pack');
					echo '</a>' ;
				}
				//if not admin send to 404
				else {
					wp_redirect( home_url( '/404page/' ) );
					exit();
				}
			}
			else {
				$blocks = parse_blocks($post_content->post_content);
				foreach ($blocks as $block) {
					$contents = render_block($block);
					//find any shortcodes
					preg_match_all("#\[[^\]]*\]#",$contents,$fields);
					//loop through shortcodes
					for ($i=0; $i< count($fields[0]); $i++) {
						//if bbp_index found
						if(str_contains ($contents, '[bbp-forum-index]' )) {
							//this is a block with the fourm index in, so we want to replace forum index with the code
							//so we make a string of forum index by doing an output buffer
							ob_start();
							while ( have_posts() ) : the_post(); ?>
								<div class="bbpress-content">
										<?php the_content(); ?>
									</div>
								<?php
								$output = ob_get_clean();
								$contents = str_replace ('[bbp-forum-index]', $output, $contents) ;
							endwhile;
						}
						else {
							//it is another shortcode so just execute the shortcode
							$shortcode = $fields[0][$i] ;
							ob_start();
							echo do_shortcode ($shortcode) ;
							$output = ob_get_clean();
							$contents = str_replace ($shortcode, $output, $contents) ;
						}		
					}
				echo $contents;
				}
			}
		} ?>
		</div>
<?php 
} // end bbpsp code


if (empty ($bsp_style_settings_theme_support['fse_template_version'])) {
	
?>

<footer class="wp-block-template-part site-footer">
<?php block_footer_area(); ?>
</footer>
</div>

<?php wp_footer(); ?>

</body>
</html>

<?php 

}

if (!empty ($bsp_style_settings_theme_support['fse_template_version'])) { 

block_template_part('footer');

wp_footer() ;	
	
}