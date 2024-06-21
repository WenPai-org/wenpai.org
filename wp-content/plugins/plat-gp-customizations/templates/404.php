<?php
gp_title( __( '404 项目未找到 &lt; 文派翻译平台', 'glotpress' ) );

gp_tmpl_header();
?>

<!--<h2>Page not found</h2>-->

<!--<p>The page you were looking for could not be found. I’m sorry, it’s not your fault&hellip; probably. <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Return to the homepage</a></p>-->


<?php echo do_shortcode("[insert page='1949' display='content']"); ?>


<?php
gp_tmpl_footer();
