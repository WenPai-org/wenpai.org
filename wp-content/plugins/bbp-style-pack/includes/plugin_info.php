<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


function bsp_plugin_info() {
	// get the info (thanks Pascal for the original code !)
        // now greatly modified
	
	// define common/reusable values
        $sysinfo = $debugwarn = $debugerror = $pinfo = $fginfo = array();
        $val_true = __( 'True', 'bbp-style-pack' );
        $val_false = __( 'False', 'bbp-style-pack' );
        $multisite = is_multisite() ? true : false;


        // site url	
        $key = '<b>' . __( 'Site URL', 'bbp-style-pack' ) . '</b>';
        $newarray = array( $key => get_bloginfo( 'url' ) );
        $sysinfo = array_merge( $sysinfo, $newarray );

        // PHP version
        $key = '<b>' . __( 'PHP Version', 'bbp-style-pack' ) . '</b>';
        $newarray = array( $key => phpversion() );
        $sysinfo = array_merge( $sysinfo, $newarray );

        // wp version
        $key = '<b>' . __( 'WP Version', 'bbp-style-pack' ) . '</b>';
        $newarray = array( $key => get_bloginfo( 'version' ) );
        $sysinfo = array_merge( $sysinfo, $newarray );

        // Multisite Info
        $key = '<b><a href="https://wordpress.org/documentation/article/create-a-network/" target="_blank">' . __( 'Multisite', 'bbp-style-pack' ) . '</a></b>';
        $newarray = array( $key => ( $multisite ? $val_true : $val_false ) );
        $sysinfo = array_merge( $sysinfo, $newarray );
        if ( $multisite ) {
                $newarray = array( 'Site ID' => get_current_blog_id() );
                $sysinfo = array_merge( $sysinfo, $newarray );
        }
        
        // active members
        $key = '<b>' . __( 'Active Members', 'bbp-style-pack' ) . '</b>';
        $newarray = array( $key => ( $multisite ? get_user_count( get_current_blog_id() ) : get_user_count() ) );  //array( $key => get_user_count( is_multisite() ? get_current_blog_id() : '' ) );
        $sysinfo = array_merge( $sysinfo, $newarray );
        
        //bbp stats
        $stats = bbp_get_statistics();
        
        // total forums
        $key = '<b>' . __( 'Total Forums', 'bbp-style-pack' ) . '</b>';
        $newarray = array( $key => $stats['forum_count'] );
        $sysinfo = array_merge( $sysinfo, $newarray );
        
        // total topics
        $key = '<b>' . __( 'Total Topics', 'bbp-style-pack' ) . '</b>';
        $newarray = array( $key => $stats['topic_count'] );
        $sysinfo = array_merge( $sysinfo, $newarray );
        
        // total replies
        $key = '<b>' . __( 'Total Replies', 'bbp-style-pack' ) . '</b>';
        $newarray = array( $key => $stats['reply_count'] );
        $sysinfo = array_merge( $sysinfo, $newarray );

        // theme
        $mytheme = wp_get_theme();
        $key = '<b>' . __( 'Theme', 'bbp-style-pack' ) . '</b>';
        $newarray = array( $key => $mytheme["Name"].' '.$mytheme["Version"] );
        $sysinfo = array_merge( $sysinfo, $newarray );

        // theme type
        global $bsp_theme_check;
        $mytheme_type = ( $bsp_theme_check == 'block_theme' ?
                __( 'FSE Block Theme', 'bbp-style-pack' ) :
                __( 'Traditional Theme', 'bbp-style-pack' )
        );
        $key = '<b>' . __( 'Theme Type', 'bbp-style-pack' ) . '</b>';
        $newarray = array( $key => $mytheme_type );
        $sysinfo = array_merge( $sysinfo, $newarray );

        // bbpress version
        if ( function_exists( 'bbPress' ) ) {
                $bbp = bbpress();
        } else {
                global $bbp;
        }
        if ( isset( $bbp->version ) ) {
                $bbpversion = $bbp->version;
        } else {
                $bbpversion = '???';
        }
        $key = '<b>' . __( 'bbPress Version', 'bbp-style-pack' ) . '</b>';
        $newarray = array( $key => $bbpversion );
        $sysinfo = array_merge( $sysinfo, $newarray );

        // plugin version
        if ( defined( 'BSP_VERSION_NUM' ) ) $version = BSP_VERSION_NUM;
        else $version = get_option( 'bsp_version', false );
        $key = '<b>' . __( 'Plugin Version', 'bbp-style-pack' ) . '</b>';
        $newarray = array( $key => $version );
        $sysinfo = array_merge( $sysinfo, $newarray );

        // debugging enabled?
        if ( defined( 'WP_DEBUG' ) ) $debug = WP_DEBUG; // true or false
        else $debug = false;

        $key = '<b><a href="https://wordpress.org/documentation/article/debugging-in-wordpress/#wp_debug" target="_blank">' . __( 'WP Debugging', 'bbp-style-pack' ) . '</a></b>';
        $newarray = array( $key => ( $debug ? $val_true : $val_false ) );
        $sysinfo = array_merge( $sysinfo, $newarray );

        // get debug setup and set vars
        if ( $debug ) {

                // debug scripts
                if ( defined( 'SCRIPT_DEBUG' ) ) $debug_scripts = SCRIPT_DEBUG; // true or false
                else $debug_scripts = false;
                $key = '<b><a href="https://wordpress.org/documentation/article/debugging-in-wordpress/#script_debug" target="_blank">' . __( 'WP Debug Scripts', 'bbp-style-pack' ) . '</a></b>';
                $newarray = array( $key => ( $debug_scripts ? $val_true : $val_false ) );
                $sysinfo = array_merge( $sysinfo, $newarray );

                // debug queries
                if ( defined( 'SAVEQUERIES' ) ) $debug_queries = SAVEQUERIES; // true or false
                else $debug_queries = false;
                $key = '<b><a href="https://wordpress.org/documentation/article/debugging-in-wordpress/#savequeries" target="_blank">' . __( 'WP Debug Queries', 'bbp-style-pack' ) . '</a></b>';
                $newarray = array( $key => ( $debug_queries ? $val_true : $val_false ) );
                $sysinfo = array_merge( $sysinfo, $newarray );

                // wp debug display
                if ( defined( 'WP_DEBUG_DISPLAY' ) ) $debug_display = WP_DEBUG_DISPLAY; // true or false
                else $debug_display = false;
                $key = '<b><a href="https://wordpress.org/documentation/article/debugging-in-wordpress/#wp_debug_display" target="_blank">' . __( 'WP Debug Display', 'bbp-style-pack' ) . '</a></b>';
                $newarray = array( $key => ( $debug_display ? $val_true : $val_false ) );
                $sysinfo = array_merge( $sysinfo, $newarray );            

                // php debug display
                $php_debug_display = @ini_get('display_errors'); // specific error level to display 
                $key = '<b><a href="https://www.php.net/manual/en/errorfunc.configuration.php#ini.display-errors" target="_blank">' . __( 'PHP Debug Display', 'bbp-style-pack' ) . '</a></b>';
                $newarray = array( $key => $php_debug_display );
                $sysinfo = array_merge( $sysinfo, $newarray );

                // wp debug log
                if ( defined( 'WP_DEBUG_LOG' ) ) $debug_log = WP_DEBUG_LOG ? WP_DEBUG_LOG : false; // true/false or a file path
                else $debug_log = false;
                $key = '<b><a href="https://wordpress.org/documentation/article/debugging-in-wordpress/#wp_debug_log" target="_blank">' . __( 'WP Debug Log', 'bbp-style-pack' ) . '</a></b>';
                $value = ( is_bool( $debug_log ) ? ( $debug_log ? $val_true : $val_false ) : $debug_log );
                $newarray = array( $key => $value );
                $sysinfo = array_merge( $sysinfo, $newarray );

                // php debug log location
                $php_debug_log = @ini_get('error_log'); // file path or false
                // only display if different from wp debug log
                if ( $debug_log !== $php_debug_log ) {
                        $key = '<b><a href="https://www.php.net/manual/en/errorfunc.configuration.php#ini.error-log" target="_blank">' . __( 'PHP Debug Log', 'bbp-style-pack' ) . '</b>';
                        $value = ( is_bool( $php_debug_log ) ? ( $php_debug_log ? $val_true : $val_false ) : $php_debug_log );
                        $newarray = array( $key => $value );
                        $sysinfo = array_merge( $sysinfo, $newarray );
                }

        }


        /*******************
         * DEBUG LOG ENTRIES
         ******************/

        if ( $debug ) {
                $valid_debug_file = false;

                // check if a debug file was specified in wp_config.php and checks out as valid
                if ( empty( bsp_custom_file_errors( $debug_log ) ) ) {
                        $valid_debug_file = $debug_log;
                }
                // else, check if the php debug log value specified checks out as valid
                else if ( empty( bsp_custom_file_errors( $php_debug_log ) ) ) {
                        $valid_debug_file = $php_debug_log;
                }

                //did either one check out as valid?
                if ( $valid_debug_file ) {
                        $error_levels = array( 'PHP Fatal error', 'PHP Warning' );
                        $line_count = 10; // number of lines to show, per error level
                        $entries = bsp_parse_debug_log( $valid_debug_file, $line_count, $error_levels );
                        // loop through entries for each error level
                        foreach ( $error_levels as $error_level ) {
                                $entry_count = count( $entries[$error_level] ); // count returned entries incase it's less than the desired line count
                                // make sure we got entries back
                                if ( $entry_count > 0 ) {
                                        $count = 1;
                                        // loop through the entries and build out the array to display
                                        foreach ( $entries[$error_level] as $entry ) {
                                                $newarray = array( $count => $entry );
                                                if ( $error_level == 'PHP Fatal error' ) $debugerror = array_merge( $debugerror, $newarray );
                                                if ( $error_level == 'PHP Warning' ) $debugwarn = array_merge( $debugwarn, $newarray );
                                                $count++;
                                        }
                                }
                        }
                }
        }


        /******** 
        * PLUGINS 
        ********/

        $plugins = get_plugins();
        $mu_plugins = get_mu_plugins();
        $active_plugins = get_option( 'active_plugins' );


        /***********
        * MU Plugins ( applies to both multisite and singlesite ) 
        ***********/

        // merge top-level info to array
        $count = count( $mu_plugins );
        $key = __( 'MU Plugins', 'bbp-style-pack' );
        if ( $count === 0 ) $value = __( 'None', 'bbp-style-pack' );
        else $value = __( 'Name and Version', 'bbp-style-pack' );
        $newarray = array( $key => $value );
        $pinfo = array_merge( $pinfo, $newarray );

        // add mu plugins if there are any
        if ( $count > 0 ) {
                $i = 1;
                foreach ( $mu_plugins as $p => $v ) {
                        // merge plugin name and version to the array
                        $linetoadd = $v["Name"] . ' ' . $v["Version"] . '<br/>';
                        $newarray = array( '- mu'.$i => $linetoadd );
                        $pinfo = array_merge( $pinfo, $newarray );
                        $i++;
                        // remove the plugin from the plugins list so we end up with an array of inactive plugins
                        if ( isset( $plugins[$p] ) ) {
                                unset( $plugins[$p] );
                        }
                }
        }


        /************************** 
        * Network Activated Plugins ( multisite-only )
        **************************/

        if ( $multisite ) {

                // loop through plugins to build out a network-activated list
                $network_activated = array();
                foreach ( $plugins as $p => $v) {
                        if ( is_plugin_active_for_network( $p ) || $v["Network"]  ) {
                                $network_activated[$p] = $v;
                        }
                }

                // merge top-level info to array
                $count = count( $network_activated );
                $key = __( 'Network Active Plugins', 'bbp-style-pack' );
                if ( $count === 0 ) $value = __( 'None', 'bbp-style-pack' );
                else $value = __( 'Name and Version', 'bbp-style-pack' );
                $newarray = array( $key => $value );
                $pinfo = array_merge( $pinfo, $newarray );

                // add network activated plugins if there are any
                if ( $count > 0 ) {
                        $i = 1;
                        foreach ( $network_activated as $p => $v ) {
                                // merge plugin name and version to the array
                                $linetoadd = $v["Name"] . ' ' . $v["Version"] . '<br/>';
                                $newarray = array( '- network'.$i => $linetoadd );
                                $pinfo = array_merge( $pinfo, $newarray );
                                $i++;
                                // remove the plugin from the plugins list so we end up with an array of inactive plugins
                                if ( isset( $plugins[$p] ) ) {
                                        unset( $plugins[$p] );
                                }
                        }
                }
        }


        /*********************** 
        * Site Activated Plugins ( applies to both multisite and singlesite )
        ***********************/

        // loop through plugins to build out an activated list
        $site_activated = array();
        foreach ( $active_plugins as $p ) {
                if ( isset( $plugins[$p] ) ) {
                        $site_activated[$p] = $plugins[$p];
                }
        }

        // merge top-level info to array
        $count = count( $site_activated ); // do the count just incase it is multisite and all plugins are network-activated
        $key = __( 'Active Plugins', 'bbp-style-pack' );
        if ( $count === 0 ) $value = __( 'None', 'bbp-style-pack' );
        else $value = __( 'Name and Version', 'bbp-style-pack' );
        $newarray = array( $key => $value );
        $pinfo = array_merge( $pinfo, $newarray );

        // add site-level activated plugins if there are any
        if ( $count > 0 ) {
                $i = 1;
                foreach ( $site_activated as $p => $v ) {
                        // merge plugin name and version to the array
                        $linetoadd = $v["Name"] . ' ' . $v["Version"] . '<br/>';
                        $newarray = array( '- active'.$i => $linetoadd );
                        $pinfo = array_merge( $pinfo, $newarray );
                        $i++;
                        // remove the plugin from the plugins list so we end up with an array of inactive plugins
                        if ( isset( $plugins[$p] ) ) {
                                unset( $plugins[$p] );
                        }
                }
        }


        /***************** 
        * Inactive Plugins
        *****************/

        // merge top-level info to array
        $count = count( $plugins ); // we've removed every active plugin of all types, so whatever's left is an inactive plugin
        $key = __( 'Inactive Plugins', 'bbp-style-pack' );
        if ( $count === 0 ) $value = __( 'None', 'bbp-style-pack' );
        else $value = __( 'Name and Version', 'bbp-style-pack' );
        $newarray = array( $key => $value );
        $pinfo = array_merge( $pinfo, $newarray );

        // add inactive plugins if there are any
        if ( $count > 0 ) {
                $i = 1;
                foreach ( $plugins as $p => $v ) {
                        // merge plugin name and version to the array
                        $linetoadd = $v["Name"] . ' ' . $v["Version"] . '<br/>';
                        $newarray = array( '- inactive'.$i => $linetoadd );
                        $pinfo = array_merge( $pinfo, $newarray );
                        $i++;
                }
        }


        /*****************
        * File Generations
        *****************/

        // array of generated files
        $generated_files = array(
                // bspstyle.css
                'bspstyle' => array(
                        'filename' => 'bspstyle'.( is_multisite() ? '-'.get_current_blog_id() : '' ).'.css',
                        'last_gen' => get_option( 'bsp_style_generation', false )
                ),
                // bspstyle-quotes.css
                'bspstyle-quotes' => array(
                        'filename' => 'bspstyle-quotes'.( is_multisite() ? '-'.get_current_blog_id() : '' ).'.css',
                        'last_gen' => get_option( 'bsp_style_quote_generation', false )
                ),
                // bsp_delete.js
                'bsp_delete' => array(
                        'filename' => 'bsp_delete'.( is_multisite() ? '-'.get_current_blog_id() : '' ).'.js',
                        'last_gen' => get_option( 'bsp_delete_js_generation', false )
                )
        );

        // merge top-level info to array
        $key = __( 'Filename', 'bbp-style-pack' );
        $value = __( 'Last Generation', 'bbp-style-pack' );
        $newarray = array( $key => $value );
        $fginfo = array_merge( $fginfo, $newarray );

        // loop through generated files array
        foreach ( $generated_files as $key => $file ) {

                // set default date/time to never
                $formatted_tz_date = __( 'Never', 'bbp-style-pack' );

                // if we have a timestamp for this file
                if ( $file['last_gen'] ) {
                        $formatted_tz_date = bsp_timetamp_to_human_local_dtg( $file['last_gen'] );
                }

                // merge filename and date to the array
                $newarray = array( $file['filename'] => $formatted_tz_date );
                $fginfo = array_merge( $fginfo, $newarray );
        }


        /****************
        * Plugin Settings
        ****************/

        // array of defined plugin settings tabs and their usable attributes
        //$plugin_settings_tabs = bsp_defined_tabs();


        /*************
        * Start Output
        *************/
        ?>

        <!-- clipboard script file -->
        <script src="//cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js"></script>

        <!-- prevent buttons from being included in clipboard copies -->
        <style>button.unselectable { -webkit-user-select: none; -webkit-touch-callout: none; -moz-user-select: none; -ms-user-select: none; user-select: none; } </style>

        <!-- BEGIN Page Output -->
        <?php
        $title = __( 'All', 'bbp-style-pack' );
        $id = 'bsp-debug-info';
        echo bsp_render_clipboard_button( $title, $id );
        echo '<br/>';
        ?>

        <div id="bsp-debug-info">


                <?php
        // Site Details Table
                if ( ! empty( $sysinfo ) ) {
                        $title = __( 'Site Details', 'bbp-style-pack' );
                        $id = 'bsp-site-details';
                        echo '<h3>' . $title . '</h3>';
                        echo '<table id="' . $id . '" class="bsp-plugin-info">';
                                array_walk( $sysinfo, 'bsp_debug_table_walker' );
                        echo '</table>';
                        echo bsp_render_clipboard_button( $title, $id );
                        echo '<br/>';
                        echo '<hr/>';
                        echo '<br/>';
                }


        // Debug Log Table
                // start output of container if either one had entries
                if ( ! empty( $debugerror ) || ! empty( $debugwarn ) ) {
                        $debug_title = __( 'Debug Log File Entries', 'bbp-style-pack' );
                        $debug_id = 'bsp-debug-entries';
                        echo '<h3>' . $debug_title . '</h3>';
                        echo '<p>';
                        echo sprintf(
                                    /* translators: %1$s is the current number of lines in the file, %2$s is the file size */
                                    __( 'Debug File Stats: %1$s lines, %2$s', 'bbp-style-pack' ),
                                    bsp_count_lines_in_file( $valid_debug_file ),
                                    bsp_get_friendly_filesize( $valid_debug_file )
                            );
                        echo '</p>';
                        echo '<div id="' . $debug_id . '">'; // plugins has multiple tables, so we target a div wrapper for clipboa
                }
                if ( ! empty( $debugerror ) ) {
                        $title = sprintf(
                                        /* translators: %s is the number of line entries */
                                        __( 'Last %s Fatal Errors', 'bbp-style-pack' ),
                                        count( $debugerror )
                                );
                        $id = 'bsp-debug-errors';
                        echo '<h4>' . $title . '</h4>';
                        echo '<table id="' . $id . '" class="bsp-plugin-info">';
                                array_walk( $debugerror, 'bsp_debug_table_walker' );
                        echo '</table>';


                }
                if ( ! empty( $debugwarn ) ) {
                        $title = sprintf(
                                        /* translators: %s is the number of line entries */
                                        __( 'Last %s Warnings', 'bbp-style-pack' ),
                                        count( $debugwarn )
                                );
                        $id = 'bsp-debug-warnings';
                        echo '<h4>' . $title . '</h4>';
                        echo '<table id="' . $id . '" class="bsp-plugin-info">';
                                array_walk( $debugwarn, 'bsp_debug_table_walker' );
                        echo '</table>';
                }
                // add the closing div container tag, copy button and hr separator if either one had entries
                if ( ! empty( $debugerror ) || ! empty( $debugwarn ) ) {
                        echo '</div>';
                        echo bsp_render_clipboard_button( $debug_title, $debug_id );
                        echo '<br/>';
                        echo '<hr/>';
                        echo '<br/>';
                }


        // Site Plugins Table
                if ( ! empty( $pinfo ) ) {
                        $title = __( 'Site Plugins', 'bbp-style-pack' );
                        $id = 'bsp-plugin-details';
                        echo '<h3>' . $title . '</h3>';
                        echo '<div id="' . $id . '">'; // plugins has multiple tables, so we target a div wrapper for clipboard.js
                                echo '<table class="bsp-plugin-info">';
                                        array_walk( $pinfo, 'bsp_debug_table_walker' );
                                echo '</table>';
                        echo '</div>';
                        echo bsp_render_clipboard_button( $title, $id );
                        echo '<br/>';
                        echo '<hr/>';
                        echo '<br/>';
                }


        // File Generations Table
                if ( ! empty( $fginfo ) ) {
                        $title = __( 'File Generations', 'bbp-style-pack' );
                        $id = 'bsp-file-generations';
                        echo '<h3>' . $title . '</h3>';
                        echo '<table id="' . $id . '" class="bsp-plugin-info">';
                                array_walk( $fginfo, 'bsp_debug_table_walker' );
                        echo '</table>';
                        echo bsp_render_clipboard_button( $title, $id );
                        echo '<br/>';
                        echo '<hr/>';
                        echo '<br/>';
                }


        // Plugin Settings Table
                $title = __( 'Plugin Settings', 'bbp-style-pack' );
                $id = 'bsp-plugin-settings';
                echo '<h3>' . $title . '</h3>'; 
                ?>
                <table id="<?php echo $id; ?>" class="bsp-plugin-info bsp-settings-table">
                        <thead>
                                <tr>
                                        <th class="th-first"valign='top'><?php _e( 'Option Group', 'bbp-style-pack' ); ?></th>
                                        <th class="th-second"><?php _e( 'Values', 'bbp-style-pack' ); ?></th>
                                </tr>
                        </thead>
                        <tbody>
                                <?php
                                $defined_option_groups = bsp_defined_option_groups();
                                //foreach ( $plugin_settings_tabs as $tab => $tab_values ) {
                                foreach ( $defined_option_groups as $option_group => $option_group_tab ) {

                                        //output wp_options
                                        bsp_render_debug_row( $option_group, $option_group_tab );
                                }
                                ?> 
                        </tbody>
                </table>
                <?php echo bsp_render_clipboard_button( $title, $id ); ?>
        </div>
        <?php

        /*
         * Copy ALL Button
         */
        ?>
        <br/>
        <hr/>
        <br/>
        <?php
        $title = __( 'All', 'bbp-style-pack' );
        $id = 'bsp-debug-info';
        echo bsp_render_clipboard_button( $title, $id );
        ?>
        <br/>
        <?php
}



// function to display plugin settings values as table rows
function bsp_render_debug_row( $option_group, $option_group_tab ) {
    
        // handle special cases that are exceptions to the standard display flow
        // bsp_css_location
        if ( $option_group === 'bsp_css_location' ) {
                global $bsp_css_location ;
                if ( ! empty( $bsp_css_location['activate css location'] ) && ! empty( $bsp_css_location['location'] ) ) {
                        $url = home_url();
                        $settings_values = esc_url( $url ).'/'.$bsp_css_location['location']; 
                }
                else $settings_values = plugins_url( 'css/', dirname( __FILE__ ) );
                
        // handle standard display flow
        } else {
                
                // only process if there's a value to check
                if ( $option_group ) {
	
                        global $wpdb;
                        $bsp_style_settings = $wpdb->get_col( "select option_value from $wpdb->options where option_name = '$option_group'" );
                        $bsp_style_settings_display = ( ! empty( $bsp_style_settings ) ? implode( $bsp_style_settings ) : '' );
                        $no_values_msg = __( 'No values set for', 'bbp-style-pack' ) . ': ' . $option_group;

                        // determine whether we're going to show values or a no values set message
                        if ( is_array($bsp_style_settings_display) ) {
                                $settings_values = ( count( $bsp_style_settings_display ) > 0 ) ? $bsp_style_settings_display : $no_values_msg;
                        } else {
                                $settings_values = ( empty( $bsp_style_settings_display ) || $bsp_style_settings_display == '' ) ? $no_values_msg : $bsp_style_settings_display;
                        }
                
                }
        }
        
        
        // only display if there's a value to display
        if ( $option_group_tab ) {
                $id = 'row-' . $option_group;
                ?>
                <tr id="<?php echo $id; ?>">
                        <td class="td-first" valign="top">
                                <?php //echo '<b><a href="' . admin_url( 'options-general.php?page=bbp-style-pack&tab=' . $tab ) . '" target="_blank">' . $name . '</a></b><br>' . $value; ?>
                                <?php echo '<b>' . $option_group_tab . '</b><br>' . $option_group; ?>
                                <?php echo bsp_render_clipboard_button( $option_group, $id ); ?>
                        </td>
                        <td class="td-second">
                                <?php echo $settings_values; ?>
                        </td>
                </tr>
                <?php
        }
}


function bsp_render_clipboard_button( $title, $id ) {
    
        // set button display title
        if ( strpos($id, 'row-') === 0 ) {
                $clipboard_title = __( 'Copy row to clipboard', 'bbp-style-pack' );
        } else {
                $clipboard_title = sprintf(
                        /* translators: %s is a title for the current table/item to be copied */
                        __( 'Copy %s to clipboard', 'bbp-style-pack' ),
                        $title
                );
        }
        ?>
                
        <!-- START <?php echo $id; ?> copy to clipboard -->
        <script type="text/javascript">
                (function() {
                        new ClipboardJS( '#copy-<?php echo $id; ?>-button' );
                })();
        </script>
        <p>
        <button type="button" class="button unselectable" id="copy-<?php echo $id; ?>-button" data-clipboard-action="copy" data-clipboard-target="#<?php echo $id; ?>" onmousedown="return false" onselectstart="return false"><?php echo $clipboard_title ?></button>
        </p>
        <!-- END <?php echo $id; ?> copy to clipboard -->
        <?php
}


// walker function for displaying site details and site plugins as table rows
function bsp_debug_table_walker( $item1, $key ) {

        // handle special case for file generations
        if ( $key == 'Filename' ) {
                $tag = 'th';
                $key = '<b>' . $key . '</b>';
                $item1 = '<b>' . $item1 . '</b>';
        }
        elseif ( $key == 'bspstyle'.( is_multisite() ? '-'.get_current_blog_id() : '' ).'.css'
                || $key == 'bspstyle-quotes'.( is_multisite() ? '-'.get_current_blog_id() : '' ).'.css' 
                || $key == 'bsp_delete'.( is_multisite() ? '-'.get_current_blog_id() : '' ).'.js' 
        ) {
            
                global $bsp_css_location;
                $css_filename = $key;
                $ext = strtolower( pathinfo( $css_filename, PATHINFO_EXTENSION ) );
                $src = bsp_use_custom_location( $ext ) ? bsp_sanitize_full_custom_dir( ( $ext == 'js' ? $bsp_css_location['js_location'] : $bsp_css_location['location'] ) ) : bsp_default_full_location( 'path', $ext );
                $src_url = bsp_use_custom_location( $ext ) ? bsp_sanitize_full_custom_url( ( $ext == 'js' ? $bsp_css_location['js_location'] : $bsp_css_location['location'] ) ) : bsp_default_full_location( 'url', $ext );

                // if file exists, set using custom location
                if ( file_exists( $src . $css_filename ) ) {
                        //$location = home_url().'/'.$location ;
                        $file_url = esc_url( $src_url . $css_filename );
                }
                // else, set using default URL location
                else {
                        $file_url = esc_url( bsp_default_full_location( 'url', $ext ) . $css_filename );
                }
                
                $tag = 'td';
                $key = '<b><a href="' . $file_url . '" target="_blank">' . $key . '</a></b>';
        }
        
        // handle special case for plugins which render in separate tables
        elseif ( $item1 == 'Name and Version' ) {
            
                // skip the first plugin type (MU Plugins)
                if ( $key !== 'MU Plugins' ) {
                        // for the rest, end the table and start a new one
                        echo '</table>';
                        echo '<br/>';
                        echo '<table class="bsp-plugin-info">';
                    
                }
            
                $tag = 'th';
                $key = '<b>' . $key . '</b>';
                $item1 = '<b>' . $item1 . '</b>';   
        }
        
        // set default tag as td
        else $tag = 'td';
        ?>
        
        <tr>
                <?php
                // column 1
                echo '<' . $tag . ' valign="top">';
                        echo $key;
                echo '</' . $tag . '>';
                // coulmn 2
                echo '<' . $tag . '>';
                        echo $item1;
                echo '</' . $tag . '>';
                ?>
            
        </tr>
<?php
}
