<?php

/**
 * Search 
 *
 * @package bbPress
 * @subpackage Theme
 
 Amended bbp style pack to add spinner
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


?>

<form role="search" method="get" id="bbp-search-form" action="<?php bbp_search_url(); ?>">

	<div>
		<?php global $bsp_style_settings_search ; ?>
		<label class="screen-reader-text hidden" for="bbp_search"><?php _e( 'Search for:', 'bbpress' ); ?></label>
		<input type="hidden" name="action" value="bbp-search-request" />
		<input tabindex="<?php bbp_tab_index(); ?>" type="text" value="<?php echo esc_attr( bbp_get_search_terms() ); ?>" name="bbp_search" id="bbp_search" />
		<input tabindex="<?php bbp_tab_index(); ?>" class="button" type="submit" id="bsp_search_submit1" value="<?php esc_attr_e( 'Search', 'bbpress' ); ?>" />
		<?php
		//preload spinner so it is ready - css hides this
		echo '<div id="bsp-spinner-load"></div>' ;
		?>
		<button tabindex="<?php bbp_tab_index(); ?>" class="button" type="submit" id="bsp_search_submit2" >
		<?php //then add spinner if activated
		$value = (!empty($bsp_style_settings_search['SearchingSearching']) ? $bsp_style_settings_search['SearchingSearching']  : '') ;
		echo '<div class= "bsp-search-submitting">'.$value ;
		if (!empty( $bsp_style_settings_search['SearchingSpinner'])) echo '<span class="bsp-spinner"></span></div>' ;
		?>
		</button>
		
	</div>
</form>

