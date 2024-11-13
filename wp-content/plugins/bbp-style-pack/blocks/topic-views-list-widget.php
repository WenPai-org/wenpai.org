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

// Only output widget contents if views exist
		if ( ! bbp_get_views() ) {
			return;
		}
		
		// Start an output buffer
		ob_start();

		echo '<div class="widget bsp-widget bsp-tvl-container">';
		
		do_action ('bsp_topic_views_list_before_title') ;
		
		if ( !empty( $attributes['title'] ) ) {
	
			echo '<span class="bsp-tvl-title"><h3 class="widget-title bsp-widget-title">' .  esc_html($attributes['title'])  . '</h3></span>' ;
		} 
		
		do_action ('bsp_topic_views_list_after_title') ;

		?>		
		<ul class="bbp-views-widget">

			<?php foreach ( array_keys( bbp_get_views() ) as $view ) : ?>

				<li><a class="bbp-view-title" href="<?php bbp_view_url( $view ); ?>"><?php bbp_view_title( $view ); ?></a></li>

			<?php endforeach; ?>

		</ul>
		<?php do_action( 'bbp_after_topic_views_widget' ); ?>
		</div>

		<?php
		// Output the current buffer
		echo ob_get_clean();	