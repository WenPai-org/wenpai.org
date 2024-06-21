<?php
/*
Plugin Name: GP Use Slug for Downloads
Plugin URI: http://glot-o-matic.com/gp-use-slug-for-downloads
Description: Use the translation set slug for the name of the download file name.
Version: 1.0
Author: Greg Ross
Author URI: http://toolstack.com
Tags: glotpress, glotpress plugin 
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

class GP_Use_Slug_for_Downloads {
	public $id = 'use-slug-for-downloads';

	public function __construct() {

		add_action( 'gp_export_translations_filename', array( $this, 'gp_export_translations_filename' ), 10, 5 );
	}

	public function gp_export_translations_filename( $filename, $format, $locale, $project, $translation_set ) {

		if( $translation_set->slug != '' && $translation_set->slug != 'default' ) {
			$filename = $translation_set->slug . '.' . $format->extension;
		}
		
		return $filename;
	}

}

// Add an action to WordPress's init hook to setup the plugin.  Don't just setup the plugin here as the GlotPress plugin may not have loaded yet.
add_action( 'gp_init', 'gp_use_slug_for_downloads_init' );

// This function creates the plugin.
function gp_use_slug_for_downloads_init() {
	GLOBAL $gp_use_slug_for_downloads;
	
	$gp_use_slug_for_downloads = new GP_Use_Slug_for_Downloads;
}