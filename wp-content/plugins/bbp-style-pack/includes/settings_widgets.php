<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


function bsp_widgets() {
 ?>
			
        <table class="form-table">	
                <tr valign="top">
                        <th colspan="2">

                        <h3>
                                <?php _e ('Widgets' , 'bbp-style-pack' ) ; ?>
                        </h3>
					
                        <h4><span style="color:blue">
                                <?php _e('Latest activity', 'bbp-style-pack' ) ; ?>
                        </span></h4>

                        <p>
                                <?php _e('This widget combines Recent Topics and Recent replies to give a single more clear latest activity widget', 'bbp-style-pack'); ?>
                        </p>
                        <p>
                                <?php _e('This widget is automaticlly available, so you will find it in Dashboard>Appearance>Widgets>(Style Pack) Latest Activity', 'bbp-style-pack'); ?>
                        </p>
                        
                        </th>
                </tr>
        </table>


        <table>
                <tr>
                        <th style="text-align:center"> <?php _e('FROM', 'bbp-style-pack' ) ; ?></th>
                        <th style="text-align:center"> <?php _e('TO', 'bbp-style-pack' ) ; ?></th>
                </tr>
                <tr>
                        <td><?php echo '<img src="' . plugins_url( 'images/widgets1.JPG',dirname(__FILE__)  ) . '"  > '; ?></td>
                        <td><?php echo '<img src="' . plugins_url( 'images/widgets2.JPG',dirname(__FILE__)  ) . '" > '; ?></td>
                </tr>
        </table>
		
		<h4><span style="color:blue">
                <?php _e('Forums List', 'bbp-style-pack' ) ; ?>
        </span></h4>

        <p>
                <?php _e('This widget is automaticlly available, so you will find it in Dashboard>Appearance>Widgets>(Style Pack) Forums List', 'bbp-style-pack'); ?>
        </p>
       
        
        <table>
                <tr>
                        <td colspan=2><?php echo '<img src="' . plugins_url( 'images/forums_list.png',dirname(__FILE__)  ) . '"  > '; ?></td>
                </tr>
        </table>



        <h4><span style="color:blue">
                <?php _e('Single Forum Information', 'bbp-style-pack' ) ; ?>
        </span></h4>

        <p>
                <?php _e('This widget is automaticlly available, so you will find it in Dashboard>Appearance>Widgets>(Style Pack) Single Forum Information', 'bbp-style-pack'); ?>
        </p>
        <p>
                <?php _e('This widget will only show on single forum pages', 'bbp-style-pack'); ?>
        </p>

        
        <table>
                <tr>
                        <td colspan=2><?php echo '<img src="' . plugins_url( 'images/forum-description.PNG',dirname(__FILE__)  ) . '"  > '; ?></td>
                </tr>
        </table>

        
        <h4><span style="color:blue">
                <?php _e('Single Topic Information', 'bbp-style-pack' ) ; ?>
        </span></h4>

        <p>
                <?php _e('This widget is automaticlly available, so you will find it in Dashboard>Appearance>Widgets>(Style Pack) Single Topic Information', 'bbp-style-pack'); ?>
        </p>
        <p>
                <?php _e('This widget will only show on single topic pages', 'bbp-style-pack'); ?>
        </p>

        
        <table>
                <tr>
                        <td colspan=2><?php echo '<img src="' . plugins_url( 'images/topic-description.PNG',dirname(__FILE__)  ) . '"  > '; ?></td>
                </tr>
        </table>
                        
 <?php
}
