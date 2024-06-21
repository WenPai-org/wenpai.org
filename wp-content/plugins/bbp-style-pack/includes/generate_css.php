<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


// get global values used for overall enqueue flow
// these are the globals used outside of functions
global $bsp_style_settings_form, $bsp_style_settings_search, $bsp_style_settings_t, $bsp_style_settings_quote;


// function for testing if we're using custom locations or default BSP locations
function bsp_use_custom_location( $type, $error_check = true ) {
        global $bsp_css_location;
        switch( strtolower( $type ) ) {
                case 'css' :
                        return ( !empty( $bsp_css_location['activate css location'] ) && !empty( $bsp_css_location['location'] ) && ( (bool) $error_check == false || empty( bsp_custom_file_location_errors( 'css' ) ) ) ) ? true : false;
                        break;
                case 'js' :
                        return ( !empty( $bsp_css_location['activate js location'] ) && !empty( $bsp_css_location['js_location'] ) && ( (bool) $error_check == false || empty( bsp_custom_file_location_errors( 'js' ) ) ) ) ? true : false;
                        break;
                default :
                        return false;
                        break;
        }
}


// sanitize and format location string from input/saved values
function bsp_sanitize_custom_location_string( $location, $type = 'dir' ) {
                // let's set the separator for URLs to '/', but allow for \ or / for directories to preserve multi-OS compatibility
                $sep = ( ( strtolower( $type )  == 'url' ) ? '/' : DIRECTORY_SEPARATOR );
                // if it starts with '/' -  remove
		if ( 0 === strpos( $location, '/' ) ) {
			$location = substr( $location, 1, strlen( $location ) );
		}
		// if it doesn't end with a '/' add one
		if ( substr( $location, strlen( $location )-1, strlen( $location ) ) !== $sep ) {
			$location = $location . $sep;
		}
                // strings can have slashes in them, so let's make sure all of those slashes are the right ones for url
                if ( strtolower( $type )  == 'url' ) $location = str_replace( '\\', '/', $location );
                // strings can have slashes in them, so let's make sure all of those slashes are the right ones for dir for this OS
                if ( strtolower( $type )  == 'dir' ) $location = str_replace( array( '\\', '/' ), $sep, $location );
                //return final sanitized custom string
                return sanitize_text_field( $location );
}


// convert custom location string to full directory path
function bsp_sanitize_full_custom_dir( $location ) {
        // let's set the separator for URLs to '/', but allow for \ or / for directories to preserve multi-OS compatibility
        $sep = DIRECTORY_SEPARATOR;
        $location = bsp_sanitize_custom_location_string( $location, 'dir' );
        $path = ABSPATH;
        return $path . $sep . $location;
}


// convert custom location string to full url string
function bsp_sanitize_full_custom_url( $location ) {
        $location = bsp_sanitize_custom_location_string( $location, 'url' );
        $home = home_url();
        return $home . '/' . $location;
}


// return default BSP dir/url for css/js
function bsp_default_full_location( $type = 'path', $file_type = 'css' ) {
        $sep = DIRECTORY_SEPARATOR;
        if ( strtolower( $type ) == 'path' ) return BSP_PLUGIN_DIR . $sep . strtolower( $file_type ) . $sep;
        else if ( strtolower( $type ) == 'url' ) return BSP_PLUGIN_URL . strtolower( $file_type ) . '/';
        else return;
}


// return default BSP dir/url for block css/js
function bsp_default_full_block_location( $type = 'path', $file_type = 'css' ) {
        // file_type not currently used for block files since all are css, but can be integrated easy in the future
        $sep = DIRECTORY_SEPARATOR;
        if ( strtolower( $type ) == 'path' ) return BSP_PLUGIN_DIR . $sep . 'build' . $sep;
        else if ( strtolower( $type ) == 'url' ) return BSP_PLUGIN_URL . 'build/';
        else return;
}


// function on activation/upgrade to make sure BSP files get copied/updated in custom directories
function copy_to_custom_dirs() {
        global $bsp_css_location;
        $sep = DIRECTORY_SEPARATOR;
        
        // if there is a custom CSS location set, copy/overwrite any current BSP CSS files in that custom dir
        if ( bsp_use_custom_location( 'css', false ) ) {
                // NOTE: all current block css files are directly queued
                // it makes sense to copy them to the custom css location to make all css files consistent
                $src = bsp_default_full_location( 'path', 'css' );
                $block_src = bsp_default_full_block_location( 'path', 'css' );
                $dest = bsp_sanitize_full_custom_dir( $bsp_css_location['location'] );
                
                // if destination doesn't exist, let's create it
                if ( !is_dir( $dest ) ) {
                        mkdir( $dest, 0755, true);
                }
                
                // get all BSP CSS files
                $files = glob( $src . '*.css');
                $block_files = glob( $block_src . '*.css');
                
                // copy files to custom location
                foreach( $files as $file ) {
                        copy( $file, $dest . str_replace( $src, '', $file ) );
                }
                foreach( $block_files as $file ) {
                        copy( $file, $dest . str_replace( $block_src, '', $file ) );
                }
        }
        
        // if there is a custom JS location set, copy/overwrite any current BSP JS files in that custom dir
        if ( bsp_use_custom_location( 'js', false ) ) {
                // NOTE: all current block js files are set within json files and not directly queued
                // block js files are currently disabled because there's no reason to copy them to the custom location
                $src = bsp_default_full_location( 'path', 'js' );
                //$block_src = bsp_default_full_block_location( 'path', 'js' );
                $dest = bsp_sanitize_full_custom_dir( $bsp_css_location['js_location'] );
                
                // if destination doesn't exist, let's create it
                if ( !is_dir( $dest ) ) {
                        mkdir( $dest, 0755, true);
                }
                
                // get all BSP JS files
                $files = glob( $src . '*.js');
                //$block_files = glob( $block_src . '*.js');

                // copy files to custom location
                foreach( $files as $file ) {
                        copy( $file, $dest . str_replace( $src, '', $file ) );
                }
                //foreach( $block_files as $file ) {
                //        copy( $file, $dest . str_replace( $block_src, '', $file ) );
                //}
        }
        
}


// generate the main custom css file
function generate_style_css() {
        global $bsp_css_location;
        $css_filename = 'bspstyle' . ( is_multisite() ? '-' . get_current_blog_id() : '' ) . '.css';
        $sep = DIRECTORY_SEPARATOR;
        // set src as either BSP dir or custom dir based on values and errors
        $src = bsp_use_custom_location( 'css' ) ? bsp_sanitize_full_custom_dir( $bsp_css_location['location'] ) : bsp_default_full_location( 'path', 'css' );
	require_once( ABSPATH . 'wp-admin' . $sep . 'includes' . $sep . 'file.php' );

	ob_start(); // Capture all output (output buffering)
	require ( BSP_PLUGIN_DIR . $sep . 'css' . $sep . 'styles.php' );
	$css = ob_get_clean(); // Get generated CSS (output buffering)
        //$css_test_filename = 'bsp_test'.( is_multisite() ? '-'.get_current_blog_id() : '' ).'.css';
        
        // set file generation value to use for enqueueing
        update_option( 'bsp_style_generation', time(), true );
        
        // Save it
	file_put_contents( $src . $css_filename, $css, LOCK_EX ); 
}


// enqueue the main custom css file
function bsp_enqueue_css() {
        global $bsp_css_location ;
        $css_filename = 'bspstyle' . ( is_multisite() ? '-' . get_current_blog_id() : '' ) . '.css';
        $src = bsp_use_custom_location( 'css' ) ? bsp_sanitize_full_custom_dir( $bsp_css_location['location'] ) : bsp_default_full_location( 'path', 'css' );
        $src_url = bsp_use_custom_location( 'css' ) ? bsp_sanitize_full_custom_url( $bsp_css_location['location'] ) : bsp_default_full_location( 'url', 'css' );
		$dependancy = apply_filters ('bsp_enqueue_css_dependancy' , array( 'bbp-default' )) ;
        // if file exists, enqueue with custom URL location
        if ( file_exists( $src . $css_filename ) ) {
                //$location = home_url().'/'.$location ;
                wp_register_style( 'bsp', $src_url . $css_filename, $dependancy, get_option( 'bsp_style_generation', time() ), 'screen');
        }
        // else, enqueue with default URL location
        else {
                wp_register_style('bsp', bsp_default_full_location( 'url', 'css' ) . $css_filename, $dependancy, get_option( 'bsp_style_generation', time() ), 'screen' );     
        }
        wp_enqueue_style( 'bsp' );
        // enqueue dashicons for frontend use (not sure why, but it was there so leaving it for now)
        wp_enqueue_style( 'dashicons' );             
}
add_action( 'wp_enqueue_scripts', 'bsp_enqueue_css' );


//adds admin file for the 'not working' tab and tab settings styling
function bsp_admin() {
	global $bsp_css_location;
        $css_filename = 'bsp_admin.css';
        $src = bsp_use_custom_location( 'css' ) ? bsp_sanitize_full_custom_dir( $bsp_css_location['location'] ) : bsp_default_full_location( 'path', 'css' );
        $src_url = bsp_use_custom_location( 'css' ) ? bsp_sanitize_full_custom_url( $bsp_css_location['location'] ) : bsp_default_full_location( 'url', 'css' );

        // if file exists, enqueue with custom URL location
        if ( file_exists( $src . $css_filename ) ) {
                //$location = home_url().'/'.$location ;
                wp_register_style( 'bsp_admin', $src_url . $css_filename, array(), get_option( 'bsp_version' ) );
        }
        // else, enqueue with default URL location
        else {
                wp_register_style('bsp_admin', bsp_default_full_location( 'url', 'css' ) . $css_filename, array(), get_option( 'bsp_version' ) );
        }
        wp_enqueue_style( 'bsp_admin' );		
}
add_action( 'admin_enqueue_scripts', 'bsp_admin' );


//add admin styling
function bsp_admin_css() {
	if ( !empty( $_REQUEST['page'] ) && $_REQUEST['page'] == 'bbp-style-pack' ) {
		
		echo '<style>
			#wpbody-content {
				background-color: #fff!important;
			}
                </style>';
	}
}
add_action( 'admin_head', 'bsp_admin_css' );


// admin js for stuff like color picker and to-the-top scroller
function bsp_enqueue_color_picker( $hook_suffix ) {
        global $bsp_css_location;
        $css_filename = 'bsp.js';
        $src = bsp_use_custom_location( 'js' ) ? bsp_sanitize_full_custom_dir( $bsp_css_location['js_location'] ) : bsp_default_full_location( 'path', 'js' );
        $src_url = bsp_use_custom_location( 'js' ) ? bsp_sanitize_full_custom_url( $bsp_css_location['js_location'] ) : bsp_default_full_location( 'url', 'js' );

        // enque prereq
        wp_enqueue_style( 'wp-color-picker' );
        
        // if file exists, enqueue with custom URL location
        if ( file_exists( $src . $css_filename ) ) {
                //$location = home_url().'/'.$location ;
                wp_enqueue_script( 'bsp_enqueue_color_picker', $src_url . $css_filename, array( 'wp-color-picker' ), get_option( 'bsp_version' ), true );
        }
        // else, enqueue with default URL location
        else {
                wp_enqueue_script( 'bsp_enqueue_color_picker', bsp_default_full_location( 'url', 'js' ) . $css_filename, array( 'wp-color-picker' ), get_option( 'bsp_version' ), true );
        }
}
add_action( 'admin_enqueue_scripts', 'bsp_enqueue_color_picker' );


// enqueue show/hide submit button js 
if ( !empty( $bsp_style_settings_form['SubmittingActivate'])) add_action( 'wp_enqueue_scripts', 'bsp_enqueue_submit' );
function bsp_enqueue_submit() {
        global $bsp_css_location;
        $css_filename = 'bsp_enqueue_submit.js';
        $src = bsp_use_custom_location( 'js' ) ? bsp_sanitize_full_custom_dir( $bsp_css_location['js_location'] ) : bsp_default_full_location( 'path', 'js' );
        $src_url = bsp_use_custom_location( 'js' ) ? bsp_sanitize_full_custom_url( $bsp_css_location['js_location'] ) : bsp_default_full_location( 'url', 'js' );
        
        // if file exists, enqueue with custom URL location
        if ( file_exists( $src . $css_filename ) ) {
                //$location = home_url().'/'.$location ;
                wp_enqueue_script( 'bsp_enqueue_submit', $src_url . $css_filename, array( 'jquery' ), get_option( 'bsp_version' ) );
        }
        // else, enqueue with default URL location
        else {
                wp_enqueue_script( 'bsp_enqueue_submit', bsp_default_full_location( 'url', 'js' ) . $css_filename, array( 'jquery' ), get_option( 'bsp_version' ) );
        }
}
	
	
//if quotes active	
if ( !empty( $bsp_style_settings_quote['quote_activate'] ) ) {
 
        // determine which editor is currently being used
        // no settings specified, using default text editor
        if ( ! isset( $bsp_style_settings_form['Show_editorsactivate'])|| $bsp_style_settings_form['Show_editorsactivate'] == 0  ) {
                $text_editor = true;
                $tinymce_editor = false;
        // settings specified, set flags based on settings value
        } else {
                switch ( $bsp_style_settings_form['Show_editorsactivate'] ) {
                        case 0 :
                                $text_editor = true;
                                $tinymce_editor = false;
                                break;
                        case 1 :
                                $text_editor = false;
                                $tinymce_editor = true;
                                break;
                        case 2 :
                                $text_editor = true;
                                $tinymce_editor = true;
                                break;
						case 3 :
                                $text_editor = false;
                                $tinymce_editor = true;
                                break;
						case 4 :
                                $text_editor = true;
                                $tinymce_editor = true;
                                break;		
                }
        }       
    
	/* add the style sheet */
        $quotes_css = bsp_add_custom_editor_style();
        // only if quotes css file exists
        if ( $quotes_css ) {
            
                // if tinymce being used
                if ( $tinymce_editor )
                        add_filter( 'mce_css', 'bsp_add_custom_editor_style' );

                // if default text editor being used
                if ( $text_editor )
                        add_action( 'wp_enqueue_scripts', 'bsp_enqueue_quote_style' );
                        
        }
        
	//and enqueue the js
	add_action( 'wp_enqueue_scripts', 'bsp_enqueue_quote' );
}


// this function creates the style sheet, generated when the quotes tab is accessed.
function generate_quote_style_css() {
        global $bsp_css_location;
        $css_filename = 'bspstyle-quotes' . ( is_multisite() ? '-' . get_current_blog_id() : '' ) . '.css';
        $sep = DIRECTORY_SEPARATOR;
        // set src as either BSP dir or custom dir based on values and errors
        $src = bsp_use_custom_location( 'css' ) ? bsp_sanitize_full_custom_dir( $bsp_css_location['location'] ) : bsp_default_full_location( 'path', 'css' );
	require_once( ABSPATH . 'wp-admin' . $sep . 'includes' . $sep . 'file.php' );

	ob_start(); // Capture all output (output buffering)
	require ( BSP_PLUGIN_DIR . $sep . 'css' . $sep . 'styles-quote.php' );
	$css = ob_get_clean(); // Get generated CSS (output buffering)

        // set file generation value to use for enqueueing
        update_option( 'bsp_style_quote_generation', time(), true );
        
        // Save it
	file_put_contents( $src . $css_filename, $css, LOCK_EX ); 
}


// setup custom quote styling file
function bsp_add_custom_editor_style() {
        global $bsp_css_location;
        $css_filename = 'bspstyle-quotes' . ( is_multisite() ? '-' . get_current_blog_id() : '' ) . '.css';
        $src = bsp_use_custom_location( 'css' ) ? bsp_sanitize_full_custom_dir( $bsp_css_location['location'] ) : bsp_default_full_location( 'path', 'css' );
        $src_url = bsp_use_custom_location( 'css' ) ? bsp_sanitize_full_custom_url( $bsp_css_location['location'] ) : bsp_default_full_location( 'url', 'css' );
    
        // if file exists, enqueue with custom URL location
        if ( file_exists( $src . $css_filename ) ) {
                //$location = home_url().'/'.$location ;
                return $src_url . $css_filename;
        }
        // else if default file exists, enqueue with default URL location
        else if ( file_exists( bsp_default_full_location( 'path', 'css' ) . $css_filename ) ) {
                return bsp_default_full_location( 'url', 'css' ) .$css_filename;
        }
        // return false, no file found
        return false;
}


// enqueue custom quote styling
function bsp_enqueue_quote_style() {
        wp_register_style( 'bsp_quotes', bsp_add_custom_editor_style(), array(), get_option( 'bsp_style_quote_generation', time() ) );	
        wp_enqueue_style( 'bsp_quotes' );
}


// enqueue quote js
function bsp_enqueue_quote() {    
        global $bsp_css_location;
        $css_filename = 'bsp_quote.js';
        $src = bsp_use_custom_location( 'js' ) ? bsp_sanitize_full_custom_dir( $bsp_css_location['js_location'] ) : bsp_default_full_location( 'path', 'js' );
        $src_url = bsp_use_custom_location( 'js' ) ? bsp_sanitize_full_custom_url( $bsp_css_location['js_location'] ) : bsp_default_full_location( 'url', 'js' );
        
        // if file exists, enqueue with custom URL location
        if ( file_exists( $src . $css_filename ) ) {
                //$location = home_url().'/'.$location ;
                wp_enqueue_script( 'bsp_quote', $src_url . $css_filename, array( 'jquery' ), get_option( 'bsp_version' ) );
        }
        // else, enqueue with default URL location
        else {
                wp_enqueue_script( 'bsp_quote', bsp_default_full_location( 'url', 'js' ) . $css_filename, array( 'jquery' ), get_option( 'bsp_version' ) );
        }
        
        // localize js file for ajax actions
        wp_localize_script( 'bsp_quote', 'bsp_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ),'quote' => wp_create_nonce('get_id_content') ) );
}


// enqueue search form js
if ( !empty( $bsp_style_settings_quote['SearchingActivate'] ) ) add_action( 'wp_enqueue_scripts', 'bsp_enqueue_search' );
function bsp_enqueue_search() {
        global $bsp_css_location;
        $css_filename = 'bsp_enqueue_search.js';
        $src = bsp_use_custom_location( 'js' ) ? bsp_sanitize_full_custom_dir( $bsp_css_location['js_location'] ) : bsp_default_full_location( 'path', 'js' );
        $src_url = bsp_use_custom_location( 'js' ) ? bsp_sanitize_full_custom_url( $bsp_css_location['js_location'] ) : bsp_default_full_location( 'url', 'js' );
        
        // if file exists, enqueue with custom URL location
        if ( file_exists( $src . $css_filename ) ) {
                //$location = home_url().'/'.$location ;
                wp_enqueue_script( 'bsp_enqueue_search', $src_url . $css_filename, array( 'jquery' ), get_option( 'bsp_version' ) );
        }
        // else, enqueue with default URL location
        else {
                wp_enqueue_script( 'bsp_enqueue_search', bsp_default_full_location( 'url', 'js' ) . $css_filename, array( 'jquery' ), get_option( 'bsp_version' ) );
        }
}


// enqueue reply js
if (!empty ( $bsp_style_settings_t['more_less'])) add_action( 'wp_enqueue_scripts', 'bsp_enqueue_reply_length' );
function bsp_enqueue_reply_length() {
        global $bsp_css_location;
        $css_filename = 'reply_more_less.js';
        $src = bsp_use_custom_location( 'js' ) ? bsp_sanitize_full_custom_dir( $bsp_css_location['js_location'] ) : bsp_default_full_location( 'path', 'js' );
        $src_url = bsp_use_custom_location( 'js' ) ? bsp_sanitize_full_custom_url( $bsp_css_location['js_location'] ) : bsp_default_full_location( 'url', 'js' );
        
        // if file exists, enqueue with custom URL location
        if ( file_exists( $src . $css_filename ) ) {
                //$location = home_url().'/'.$location ;
                wp_enqueue_script( 'bsp_enqueue_reply_length', $src_url . $css_filename, array( 'jquery' ), get_option( 'bsp_version' ) );
        }
        // else, enqueue with default URL location
        else {
                wp_enqueue_script( 'bsp_enqueue_reply_length', bsp_default_full_location( 'url', 'js' ) . $css_filename, array( 'jquery' ), get_option( 'bsp_version' ) );
        }
}
	
	
//add the author delete topic/reply if enabled
if ( !empty( $bsp_style_settings_t['participant_trash_topic_confirm'] ) || !empty( $bsp_style_settings_t['participant_trash_reply_confirm'] ) ) {
        add_action( 'wp_enqueue_scripts', 'bsp_delete_check' );
}


// enqueue delete js
function bsp_delete_check() {
        global $bsp_css_location;
        $css_filename = 'bsp_delete'.( is_multisite() ? '-'.get_current_blog_id() : '' ).'.js';
        
        //$css_filename = 'reply_more_less.js';
        $src = bsp_use_custom_location( 'js' ) ? bsp_sanitize_full_custom_dir( $bsp_css_location['js_location'] ) : bsp_default_full_location( 'path', 'js' );
        $src_url = bsp_use_custom_location( 'js' ) ? bsp_sanitize_full_custom_url( $bsp_css_location['js_location'] ) : bsp_default_full_location( 'url', 'js' );
        
        // if file exists, enqueue with custom URL location
        if ( file_exists( $src . $css_filename ) ) {
                //$location = home_url().'/'.$location ;
                wp_enqueue_script( 'bsp_delete_check', $src_url . $css_filename, array( 'jquery' ), get_option( 'bsp_delete_js_generation', time() ) );
        }
        // else if default file exists, enqueue with default URL location
        else if ( file_exists( bsp_default_full_location( 'path', 'js' ) . $css_filename ) ) {
                wp_enqueue_script( 'bsp_delete_check', bsp_default_full_location( 'url', 'js' ) . $css_filename, array( 'jquery' ), get_option( 'bsp_delete_js_generation', time() ) );
        }
}


// generate delet js
function generate_delete_js() {
        global $bsp_css_location, $bsp_style_settings_t;
        $css_filename = 'bsp_delete' . ( is_multisite() ? '-' . get_current_blog_id() : '' ) . '.js';
        $sep = DIRECTORY_SEPARATOR;
        // set src as either BSP dir or custom dir based on values and errors
        $src = bsp_use_custom_location( 'js' ) ? bsp_sanitize_full_custom_dir( $bsp_css_location['js_location'] ) : bsp_default_full_location( 'path', 'js' );
	require_once( ABSPATH . 'wp-admin' . $sep . 'includes' . $sep . 'file.php' );

        $trash_topic_confirm = __( 'Are you sure you want to delete this topic?', 'bbp-style-pack' );
	$message = (!empty($bsp_style_settings_t['participant_trash_topic_text']) ? $bsp_style_settings_t['participant_trash_topic_text'] : $trash_topic_confirm );
	
	ob_start(); // Capture all output (output buffering)
		if (!empty( $bsp_style_settings_t['participant_trash_topic_confirm'] ) ) {
	echo 'jQuery( function($) {       
        $(\'a.bbp-topic-trash-link\').click( function( event ) {
		if( ! confirm( \''.$message.'\' ) ) {
            event.preventDefault();
        }           
		});
	});' ;
	}
	if ( !empty( $bsp_style_settings_t['participant_trash_reply_confirm'] ) ) {
                $trash_reply_confirm = __( 'Are you sure you want to delete this reply?', 'bbp-style-pack' );
                $message = ( !empty( $bsp_style_settings_t['participant_trash_reply_text']) ? $bsp_style_settings_t['participant_trash_reply_text'] : $trash_reply_confirm );

                echo 'jQuery( function($) {       
                $(\'a.bbp-reply-trash-link\').click( function( event ) {
                        if( ! confirm( \''.$message.'\' ) ) {
                    event.preventDefault();
                }           
                        });
                });' ;
	}
	$css = ob_get_clean(); // Get generated CSS (output buffering)

        // set file generation value to use for enqueueing
        update_option( 'bsp_delete_js_generation', time(), true );
        
        // Save it
	file_put_contents( $src . $css_filename, $css, LOCK_EX ); 
}


// enqueue select2 script if needed for topic tags
if ( !empty( $bsp_style_settings_form['topic_tag_list'] ) ) {
	add_action( 'wp_enqueue_scripts', 'bsp_select2_enqueue' );
}
function bsp_select2_enqueue(){
        global $bsp_css_location;
        $css_filename = 'bspselect2.js';
        
        //$css_filename = 'reply_more_less.js';
        $src = bsp_use_custom_location( 'js' ) ? bsp_sanitize_full_custom_dir( $bsp_css_location['js_location'] ) : bsp_default_full_location( 'path', 'js' );
        $src_url = bsp_use_custom_location( 'js' ) ? bsp_sanitize_full_custom_url( $bsp_css_location['js_location'] ) : bsp_default_full_location( 'url', 'js' );
        
        // enqueue prereqs
        wp_enqueue_style( 'bsp_select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
	wp_enqueue_script( 'bsp_select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array( 'jquery' ) );
        
        // if file exists, enqueue with custom URL location
        if ( file_exists( $src . $css_filename ) ) {
                //$location = home_url().'/'.$location ;
                wp_enqueue_script( 'bsp_select2_class', $src_url . $css_filename, array( 'jquery', 'bsp_select2' ), get_option( 'bsp_version' ) );
        }
        // else, enqueue with default URL location
        else {
                wp_enqueue_script( 'bsp_select2_class', bsp_default_full_location( 'url', 'js' ) . $css_filename, array( 'jquery', 'bsp_select2' ), get_option( 'bsp_version' ) );
        }
}


// modtools report post js
// moved here so all css/js file handling is done in one single place
// done this way so custom css/js location actually applies to all files consistently
function bsp_modtools_report_post_enqueue() {
        // is modtools active?
        if ( get_option( '_bbp_report_post' ) ) {
                global $bsp_css_location;
                $css_filename = 'report-post.js';

                //$css_filename = 'reply_more_less.js';
                $src = bsp_use_custom_location( 'js' ) ? bsp_sanitize_full_custom_dir( $bsp_css_location['js_location'] ) : bsp_default_full_location( 'path', 'js' );
                $src_url = bsp_use_custom_location( 'js' ) ? bsp_sanitize_full_custom_url( $bsp_css_location['js_location'] ) : bsp_default_full_location( 'url', 'js' );

                // if file exists, enqueue with custom URL location
                if ( file_exists( $src . $css_filename ) ) {
                        //$location = home_url().'/'.$location ;
                        wp_enqueue_script( 'bsp-report-post', $src_url . $css_filename, array( 'jquery' ), get_option( 'bsp_version' ) );
                }
                // else, enqueue with default URL location
                else {
                        wp_enqueue_script( 'bsp-report-post', bsp_default_full_location( 'url', 'js' ) . $css_filename, array( 'jquery' ), get_option( 'bsp_version' ) );
                }
                // localize script for ajax actions
                wp_localize_script( 'bsp-report-post', 'REPORT_POST', array(
                        'ajax_url' => admin_url( 'admin-ajax.php' ),
                        'nonce' => wp_create_nonce( 'report-post-nonce' ),
                        'post_id' => get_the_ID(),
                ) );
        }
}


// bugfixes reply js
// moved here so all css/js file handling is done in one single place
// done this way so custom css/js location actually applies to all files consistently
function bsp_bugfixes_reply_enqueue() {
        global $bsp_css_location;
        $css_filename = 'bspreply.js';
        
        //$css_filename = 'reply_more_less.js';
        $src = bsp_use_custom_location( 'js' ) ? bsp_sanitize_full_custom_dir( $bsp_css_location['js_location'] ) : bsp_default_full_location( 'path', 'js' );
        $src_url = bsp_use_custom_location( 'js' ) ? bsp_sanitize_full_custom_url( $bsp_css_location['js_location'] ) : bsp_default_full_location( 'url', 'js' );

        // if file exists, enqueue with custom URL location
        if ( file_exists( $src . $css_filename ) ) {
                //$location = home_url().'/'.$location ;
                wp_enqueue_script( 'bsp-replyjs', $src_url . $css_filename, array( 'jquery' ), get_option( 'bsp_version' ) );
        }
        // else, enqueue with default URL location
        else {
                wp_enqueue_script( 'bsp-replyjs', bsp_default_full_location( 'url', 'js' ) . $css_filename, array( 'jquery' ), get_option( 'bsp_version' ) );
        }
}


// enqueue block css
// moved here so all css/js file handling is done in one single place
// done this way so custom css/js location actually applies to all files consistently
function bsp_enqueue_block_css() {
        global $bsp_css_location;
        $src = bsp_use_custom_location( 'css' ) ? bsp_sanitize_full_custom_dir( $bsp_css_location['location'] ) : bsp_default_full_block_location( 'path', 'css' );
        $src_url = bsp_use_custom_location( 'css' ) ? bsp_sanitize_full_custom_url( $bsp_css_location['location'] ) : bsp_default_full_block_location( 'url', 'css' );
        
        $block_css_array = array(
                'style-pack-latest-activity' => 'la-index.css',
                'style-pack-login' => 'login-index.css',
                'style-pack-single-topic-information' => 'ti-index.css',
                'style-pack-single-forum-information' => 'fi-index.css',
                'style-pack-forums-list' => 'flist-index.css',
                'style-pack-topic-views-list' => 'topic-views-index.css',
                'style-pack-statistics-list' => 'statistics-index.css',
                'style-pack-search-form' => 'search-index.css',
        );
        
        
        foreach ( $block_css_array as $slug => $css_filename) {
                // if file exists, enqueue with custom URL location
                if ( file_exists( $src . $css_filename ) ) {
                        //$location = home_url().'/'.$location ;
                        wp_enqueue_style( $slug, $src_url . $css_filename, array(), get_option( 'bsp_version' ) );
                }
                // else, enqueue with default URL location
                else {
                        wp_enqueue_script( $slug, bsp_default_full_block_location( 'url', 'css' ) . $css_filename, array(), get_option( 'bsp_version' ) );
                }
        }
}
