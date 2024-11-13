<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//where to show...
$check = true ;
if ( !empty( $attributes['bbpressOnly'] ) ) {
	//then set to false and make below turn to true
	$check = false ;
	//only show on bbpress pages or if in wdigets or page/post edits
	if (is_bbpress()) $check=true ;
	elseif (isset($_REQUEST['context'])&& $_REQUEST['context'] == 'edit') {
		$check = true ;
	}
}
//if check is false, then return ;
if (!$check) return ;
		
		// Note: private and hidden forums will be excluded via the
		// bbp_pre_get_posts_normalize_forum_visibility action and function.
		$widget_query = new WP_Query( array(

			// What and how
			'post_type'      => bbp_get_forum_post_type(),
			'post_status'    => bbp_get_public_status_id(),
			'post_parent'    => $attributes['parentForum'],
			'posts_per_page' => (int) get_option( '_bbp_forums_per_page', 50 ),

			// Order
			'orderby' => 'menu_order title',
			'order'   => 'ASC',

			// Performance
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false
		) );

		// Bail if no posts
		if ( ! $widget_query->have_posts() ) {
			return;
		}

		echo '<div class="widget bsp-widget bsp-fl-container">';
		
		do_action ('bsp_forums_list_before_title') ;
		
		if ( !empty( $attributes['title'] ) ) {
	
			echo '<span class="bsp-fl-title"><h3 class="widget-title bsp-widget-title">' . esc_html($attributes['title'])  . '</h3></span>' ;
		} 
		
		do_action ('bsp_forums_list_after_title') ;
		
		?>
		<ul class="bbp-forums-widget bsp-widget-content">

			<?php while ( $widget_query->have_posts() ) : $widget_query->the_post(); ?>

				<li <?php echo ( bbp_get_forum_id() === $widget_query->post->ID ? ' class="bbp-forum-widget-current-forum"' : '' ); ?>>
					<a class="bbp-forum-title" href="<?php bbp_forum_permalink( $widget_query->post->ID ); ?>">
						<?php bbp_forum_title( $widget_query->post->ID ); ?>
					</a>
				</li>

			<?php endwhile; ?>

		</ul>
		
		<?php do_action( 'bbp_after_forums_list_widget' ); ?>
		</div>
		

		<?php 

		// Reset the $post global
		wp_reset_postdata();
		

