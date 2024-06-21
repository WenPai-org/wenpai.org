<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//profile settings page

function bsp_buddypress_support() {
        // let's do another check for BuddyPress and exit if not active
        function_exists('bp_is_active') || exit;
	global $bsp_buddypress_support;
	?>

<!-- Start Form -->
	<form method="post" action="options.php">
                <?php 
                wp_nonce_field( 'buddypress', 'buddypress-nonce' );
                settings_fields( 'bsp_buddypress_support' ); 
                bsp_clear_cache();
                ?>

                <?php 
                // reused items
                $bp_default_message = __( 'This is the default in BuddyPress.' , 'bbp-style-pack' ); 
                $bp_sections = array(
                        1 => array( 
                            'title' => __( 'Global Activity Stream', 'bbp-style-pack' ),
                            'slug' => 'activity',
                            'linked_page' => true
                        ),
                        2 => array( 
                            'title' => __( 'Global Groups', 'bbp-style-pack' ),
                            'slug' => 'groups',
                            'linked_page' => true
                        ),
                        3 => array( 
                            'title' => __( 'Global Members List', 'bbp-style-pack' ),
                            'slug' => 'members',
                            'linked_page' => true
                        ),
                );
                ?>

                <h3> <?php _e ('BuddyPress Settings' , 'bbp-style-pack' ) ; ?>	</h3>
                
        <!-- save the options -->
                <p class="submit">
                        <input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
                </p>
        
        <!-- Start Table -->
                <table class="form-table">
				
				<?php
				//check buddypress version and recommend BP classic if needed
				if( ! function_exists('get_plugin_data') ){
					require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				}
				$buddy = get_plugin_data(ABSPATH . 'wp-content/plugins/buddypress/bp-loader.php') ;
				$version = substr($buddy['Version'], 0, 2);
				if ($version == '12') {
					echo '<h2>' ;
					_e( 'YOU ARE USING BUDDYPRESS VERSION 12.x.x which needs the additional Buddypress plugin "BP Classic" to fully work with bbPress. ', 'bbp-style-pack' ); 
					echo '</h2>' ;
					 _e( 'This link will take you to the wordpress official version: ' , 'bbp-style-pack' ); 
					echo '<a href="https://wordpress.org/plugins/bp-classic/">' ;
					_e( 'BP Classic' , 'bbp-style-pack' );
					echo '</a></b>' ;
				}
				
				?>
                <?php 
                // loop through BuddyPress sections and build out settings for active componenes or display message/link if inactive
                foreach ( $bp_sections as $index => $section ) {
                        echo '<!-- Start ' . $section['title'] . ' -->';
                        if ( bp_is_active( '' . $section['slug'] . '' ) ) {
                                $item =  'bsp_buddypress_support[' . $section['slug'] . ']';
                                $item1 = ( ! empty( $bsp_buddypress_support['' . $section['slug'] . ''] ) ? $bsp_buddypress_support['' . $section['slug'] . ''] : 0 );
                                $mod_visibility_f = 'bsp_buddypress_support[' . $section['slug'] . '_mod_visibility]';
                                $mod_visibility_v = ( ! empty( $bsp_buddypress_support['' . $section['slug'] . '_mod_visibility'] ) ? $bsp_buddypress_support['' . $section['slug'] . '_mod_visibility'] : 0 );
                        ?>
                        <!-- description -->
                                <tr valign="top">
                                        <th colspan="2">
                                                        <p>						
                                                                <?php echo $index . '. ' . $section['title'] . ' ' . __('visibility', 'bbp-style-pack' ) ; ?>
                                                        </p>
                                                        <p>
                                                                <?php echo sprintf(
                                                                        /* translators: %1$s, %2$s, %3$s are BuddyPress features section titles */
                                                                        __( 'You can choose to allow all users to see the %1$s, only show %2$s to logged in users, or turn off %3$s visibility for all users.', 'bbp-style-pack' ),
                                                                        $section['title'],
                                                                        $section['title'],
                                                                        $section['title']
                                                                ); ?>
                                                        </p>
                                                        <p>
                                                                <?php echo sprintf(
                                                                        /* translators: %s is a BuddyPress feature section title */
                                                                        __( 'NOTE: Keymaster role will always be able to see %s.', 'bbp-style-pack' ),
                                                                        $section['title']
                                                                ); ?>
                                                        </p>
                                        </th>
                                </tr>
                        <!-- show all -->
                                <tr>
                                        <td>
                                        </td>
                                        <td>
                                                <?php
                                                echo '<input name="' . $item . '" id="' . $item1 . '" type="radio" value="0" class="code" ' . checked( 0, $item1, false ) . ' />';
                                                _e( 'Show to everyone', 'bbp-style-pack' ); 
                                                ?>
                                                <br>
                                                <label class="description">
                                                        <i><?php echo $bp_default_message; ?></i>
                                                </label>
                                        </td>
                                </tr>
                        <!-- show only logged in -->
                                <tr>
                                        <td>
                                        </td>
                                        <td>
                                                <?php
                                                echo '<input name="' . $item . '" id="' . $item1 . '" type="radio" value="1" class="code" ' . checked( 1, $item1, false ) . ' />';
                                                _e( 'Show only to logged in users', 'bbp-style-pack' ); 
                                                ?>
                                        </td>
                                </tr>
                        <!-- do not show -->
                                <tr>
                                        <td>
                                        </td>

                                        <td>
                                                <?php
                                                echo '<input name="' . $item . '" id="' . $item1 . '" type="radio" value="2" class="code" ' . checked( 2, $item1, false ) . ' />';
                                                _e( 'Do not show to anyone', 'bbp-style-pack' );
                                                ?>
                                        </td>
                                </tr>
                        <!-- show to moderators -->
                                <tr>
                                        <td>
                                        </td>

                                        <td>
                                                <?php
                                                echo sprintf(
                                                        /* translators: %s is a BuddyPress feature section title */
                                                        __( 'If you have selected "Do not show to anyone" above, then click if you wish moderators to see the %s.', 'bbp-style-pack' ),
                                                        $section['title']
                                                );
                                                echo '<br/><input name="' . $mod_visibility_f . '" id="' . $mod_visibility_f . '" type="checkbox" value="1" class="code" ' . checked( 1, $mod_visibility_v, false ) . ' /> ';
                                                ?>
                                        </td>
                                </tr>
                        <?php
                        } // end if active
                        // not active so let's show a message and link to BuddyPress settings
                        else {
                                ?>
                                <tr valign="top">
                                        <th colspan="2">
                                                        <p>						
                                                                <?php echo sprintf(
                                                                        /* translators: %s is a BuddyPress feature section title */
                                                                        __( '%1$s are not currently active in BuddyPress. You can enable it in the %2$sBuddyPress Settings%3$s page and then setup visibility options here.', 'bbp-style-pack' ),
                                                                        $section['title'],
                                                                        '<a href="' . admin_url( 'admin.php?page=bp-components' ) . '" target="_blank">',
                                                                        '</a>'
                                                                ); ?>
                                                        </p>
                                                        <?php if ( $section['linked_page'] ) { ?>
                                                        <p>						
                                                                <?php echo sprintf(
                                                                        /* translators: %s is a BuddyPress feature section title */
                                                                        __( 'You will also need to make sure that you have a linked pages setup for the BuddyPress section in %1$sBuddyPress Settings > Pages%2$s.', 'bbp-style-pack' ),
                                                                        $section['title'],
                                                                        '<a href="' . admin_url( 'admin.php?page=bp-page-settings' ) . '" target="_blank">',
                                                                        '</a>'
                                                                ); ?>
                                                        </p>
                                                        <?php } // end if linked_page ?>
                                        </th>
                                </tr>
                        <?php
                        }
                } // end foreach
                ?>
                                
        <!-- End Table -->
                </table>
                                
        <!-- Start Table -->
                <table class="form-table">
                                
                <!-- Redirection -->
                        <?php 
			$area1='section-redirect' ;
			$item1 = 'bsp_buddypress_support[' . $area1 . ']';
                        $value1 = ( ! empty( $bsp_buddypress_support['' . $area1 . ''] ) ? $bsp_buddypress_support['' . $area1 . ''] : '' );
                        ?>
                        <tr valign="top">
                                <th colspan="2">
                                        <p>						
                                                <?php _e( 'Section Redirection', 'bbp-style-pack' ); ?> 
                                        </p>                                        
                                </th>
                        </tr>
                        <tr>
                                <td colspan=2>
                                        <?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.esc_html ($value1).'"<br>' ; ?> 
                                        <label class="description">
                                                <?php _e( 'If you are restricting any BuddyPres section visbility above, anyone unauthorised trying to access those sections directly need to be sent somewhere.  By default this will be the homepage, but you can choose a different page here, or enter "/404/" to send them to your sites 404 page.', 'bbp-style-pack' ); ?>
                                        </label>
                                        <br/>
                                </td>
                        </tr>
                        
        <!-- End Table -->
                </table>

        <!-- save the options -->
                <p class="submit">
                        <input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
                </p>
<!-- End Form -->
	</form>

<?php
}
