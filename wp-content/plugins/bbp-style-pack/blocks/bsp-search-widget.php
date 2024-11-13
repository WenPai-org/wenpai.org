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
	
	// Bail if search is disabled
		if ( ! bbp_allow_search() ) {
			return;
		}
		
		
		
		echo '<div class="widget bsp-widget bsp-statistics-container">';
		
		do_action ('bsp_search_before_title') ;
		
		if ( !empty( $attributes['title'] ) ) {
	
			echo '<span class="bsp-stats-title"><h3 class="widget-title bsp-widget-title">' .  esc_html($attributes['title'])  . '</h3></span>' ;
		} 
		
		do_action ('bsp_search_after_title') ;
		
		bbp_get_template_part( 'form', 'search' );

		?>
		<?php do_action( 'bbp_after_search_widget' ); ?>
		</div>
<?php
		

