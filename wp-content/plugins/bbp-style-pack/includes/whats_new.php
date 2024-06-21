<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


function bsp_new() {
        // get the changelog array
        $releases = bsp_parse_changelog();
        $rel_count = count( $releases );
        ?>

        <table class="form-table">
		<tr valign="top">
			<th colspan="2">
				<h3><?php _e( 'What\'s New?', 'bbp-style-pack' ); ?></h3>
                                
                        </th>
		</tr>
	</table>
        
        <?php
        echo '<table class="bsp-plugin-info">';
                echo '<tr>';
                        echo '<th>' . __( 'Version', 'bbp-style-pack' ) . '</th>';
                        echo '<th>' . __( 'Release Notes', 'bbp-style-pack' ) . '</th>';
                echo '</tr>';
                foreach ( $releases as $version => $notes ) {
                        echo '<tr>';
                                echo '<td style="text-align:center;"><p><b>' . $version . '</b></p></td>';
                                $note_count = 1;
                                echo '<td>';
                                foreach ( $notes as $note ) {
                                        echo '<p>' . $note_count . '.) ' . $note . '</p>';
                                        $note_count++;
                                }
                                echo '</td>';
                        echo '</tr>';
                }
        echo '</table>';
        echo '<br/>';
}
