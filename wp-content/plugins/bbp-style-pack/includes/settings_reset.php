<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//reset settings page

function bsp_style_settings_reset() {
	//calls the delete function to get rid of settings
        if ( $_POST && wp_verify_nonce( $_POST['style-settings-reset-nonce'], 'style-settings_reset' ) ) :
                bsp_reset_settings();
        endif;
	?>

	<form method="post">
	<?php wp_nonce_field( 'style-settings_reset', 'style-settings-reset-nonce' ); ?>
	<table class="form-table">
		<tr valign="top">
			<th colspan="2">
				<h3>
					<?php _e( 'Reset Settings' , 'bbp-style-pack' ); ?>
				</h3>
		</tr>
	</table>
	<table>
		<tr>
			<td>
				<p>
					<?php _e( 'This section allows you to reset any or all of the tabs in this plugin', 'bbp-style-pack' ); ?>
					
				</p>
				<p>
				<strong>
					<?php _e( 'WARNING - RESETTING deletes data for the tab(s) selected - use with care !', 'bbp-style-pack' ); ?>
				</strong>
				</p>
			</td>
		</tr>
	</table>
            
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>

        <!-- option group table -->
	<table class="form-table">

                <!-- checkbox to select all -->
                <script language="JavaScript">
                        jQuery(document).ready(function($){
                                $('#reset_select_all').click(function(event) {   
                                        if(this.checked) {
                                                // Iterate each checkbox
                                                $(':checkbox').each(function() {
                                                        this.checked = true;                        
                                                });
                                        } else {
                                                $(':checkbox').each(function() {
                                                        this.checked = false;                       
                                                });
                                        }
                                }); 
                        });
                </script>
                <tr>
                        <th>
                        </th>
                        <td>
                                <input name="reset_select_all" id="reset_select_all" type="checkbox" value="1" class="code" />
                                Select/Unselect All
                        </td>
                </tr>
                
                <?php 
                global $bsp_theme_check;
                
                $reset_text = __( 'Click to reset', 'bbp-style-pack' );
                
                foreach ( bsp_defined_option_groups() as $slug => $title ) {
                        // handle special case option groups first
                        // see if we have either twentytwentytwo (or hello elementor to be added?) and currently on bsp_block_theme option group
                        if ( $slug === 'bsp_style_settings_theme_support' && empty( $bsp_theme_check ) ) {
                                echo ''; // if there's no block theme, or astra/divi/kadence theme, just echo nothing
                        } else {
                        ?>
            
                                <!-- checkbox to activate  -->
                                <tr>
                                        <th>
                                                <?php echo $title ?>
                                        </th>
                                        <td>
                                                <?php 
                                                echo '<input name="'.$slug.'" id="'.$slug.'" type="checkbox" value="1" class="code" />';
                                                echo $reset_text;
                                                ?>
                                        </td>
                                </tr>
                                
                        <?php
                        }
                }
                ?>

	</table>
				
        <!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
</form>
 
<?php
}


function bsp_reset_settings() {
        $generate = array();
        
        // reset specified option group(s)
        foreach ( bsp_defined_option_groups() as $slug => $title ) {
                if ( ! empty( $_POST[$slug] ) ) { 
                        delete_option( $slug );
                        if ( $slug === 'bsp_style_settings_quote' ) {
                                $generate[] = 'quote';
                        }
                        elseif ( $slug === 'bsp_style_settings_t' ) {
                                $generate[] = 'delete';
                                $generate[] = 'style';
                        }
                        else {
                                $generate[] = 'style';
                        }
                }
        }
	
        // generate the necessary files based on the option group(s) reset
        if ( in_array( 'style', $generate ) ) generate_style_css();
        if ( in_array( 'delete', $generate ) ) generate_delete_js();
        if ( in_array( 'quote', $generate ) ) generate_quote_style_css();
        if ( in_array( 'style', $generate ) || in_array( 'delete', $generate ) || in_array( 'quote', $generate ) ) bsp_clear_cache();

}
