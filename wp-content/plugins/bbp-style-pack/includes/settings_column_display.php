<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


function bsp_style_settings_column_display() {
	//create a style.css on entry and on saving
	generate_style_css();
    bsp_clear_cache();
	?>
			
	<h3>
		<?php _e ('Column Display' , 'bbp-style-pack' ) ; ?>
	</h3>
	
	
                <p><b>
                        <?php _e ('This tab lets you decide which columns for forum and topic indexes you want to show and whether on all devices or just mobile ' , 'bbp-style-pack') ; ?>
                        </b></p>

                <?php global $bsp_style_settings_column_display ;
                ?>
                <form method="post" action="options.php">
                <?php wp_nonce_field( 'style-settings-column-display', 'style-settings-nonce' ) ?>
                <?php settings_fields( 'bsp_style_settings_column_display' );
                //create a style.css on entry and on saving
                generate_style_css();
                bsp_clear_cache();
                ?>
				
				<table class="form-table">

                                <!-- ACTIVATE  -->	
                <?php $forum_display = (!empty( $bsp_style_settings_column_display['forum_activate'] ) ?  $bsp_style_settings_column_display['forum_activate'] : ''); ?>
               <tr><td><hr></td></tr>
				<tr valign="top">  
                                <th style="width: 350px">
                                        <?php _e('FORUM DISPLAY', 'bbp-style-pack'); ?>
                                </th>

                                <td>
                                        <?php 
                                        echo '<input name="bsp_style_settings_column_display[forum_activate]" id="bsp_style_settings_column_display[forum_activate]" type="checkbox" value="1" class="code" ' . checked( 1,$forum_display, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_column_display[fse]">
                                                <?php _e( 'Activate', 'bbp-style-pack' ); ?>
                                        </label>
                                </td>
				</tr>
				<tr valign="top">  
                                <th style="width: 350px">
                                    <?php _e('Forum name Column', 'bbp-style-pack'); ?>     
                                </th>

						 
						<?php $forum_name_width = (!empty( $bsp_style_settings_column_display['forum_name_width'] ) ?  $bsp_style_settings_column_display['forum_name_width'] : ''); ?>
						<?php $forum_name_width_mobile = (!empty( $bsp_style_settings_column_display['forum_name_width_mobile'] ) ?  $bsp_style_settings_column_display['forum_name_width_mobile'] : ''); ?>
               
                                          <td>
                                        <label class="description" for="bsp_style_settings_column_display[forum_name]">
                                                <?php _e( 'Width desktop', 'bbp-style-pack' ); ?>
                                        </label>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[forum_name_width]" id="bsp_style_settings_column_display[forum_name_width]" class="small-text" type="text" value="'. $forum_name_width.'"/>% - '.__( 'default 55%', 'bbp-style-pack' );
                                        ?>
                                       
										<br/>
										<label class="description" for="bsp_style_settings_column_display[forum_name_mobile]">
                                                <?php _e( 'Width mobile', 'bbp-style-pack' ); ?>
                                        </label>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[forum_name_width_mobile]" id="bsp_style_settings_column_display[forum_name_width_mobile]" class="small-text" type="text" value="'. $forum_name_width_mobile.'"/>% - '.__( 'default 55%', 'bbp-style-pack' );
                                        ?>
							</td>   
                        </tr>
                        </tr>
						<?php $forum_topics = (!empty( $bsp_style_settings_column_display['forum_topics'] ) ?  $bsp_style_settings_column_display['forum_topics'] : 0); ?>
               
                        <tr valign="top">  
                                <th style="width: 350px">
                                    <?php _e('Topics Column', 'bbp-style-pack'); ?>     
                                </th>

                              <td>
                                        <?php 
                                        echo '<input name="bsp_style_settings_column_display[forum_topics]" id="bsp_style_settings_column_display[forum_topics]" type="radio" value="0" class="code" ' . checked( 0,$forum_topics, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_column_display[forum_topics]">
                                                <?php _e( 'Show', 'bbp-style-pack' ); ?>
                                        </label>
										<br/>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[forum_topics]" id="bsp_style_settings_column_display[forum_topics]" type="radio" value="1" class="code" ' . checked( 1,$forum_topics, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_column_display[forum_topics]">
                                                <?php _e( 'Hide', 'bbp-style-pack' ); ?>
                                        </label>
										<br/>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[forum_topics]" id="bsp_style_settings_column_display[forum_topics]" type="radio" value="2" class="code" ' . checked( 2,$forum_topics, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_column_display[forum_topics]">
                                                <?php _e( 'Hide only on mobile', 'bbp-style-pack' ); ?>
                                        </label>
                                </td>   
                        </tr>
						<?php $forum_topics_width = (!empty( $bsp_style_settings_column_display['forum_topics_width'] ) ?  $bsp_style_settings_column_display['forum_topics_width'] : ''); ?>
						<?php $forum_topics_width_mobile = (!empty( $bsp_style_settings_column_display['forum_topics_width_mobile'] ) ?  $bsp_style_settings_column_display['forum_topics_width_mobile'] : ''); ?>
               
						 <tr valign="top">  
                                <td style="width: 350px">
								<i>
                                    <?php _e('If being shown', 'bbp-style-pack'); ?> 
								</i>									
                                </td>

                              <td>
                                        <label class="description" for="bsp_style_settings_column_display[forum_topics_width]">
                                                <?php _e( 'Width desktop', 'bbp-style-pack' ); ?>
                                        </label>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[forum_topics_width]" id="bsp_style_settings_column_display[forum_topics_width]" class="small-text" type="text" value="'. $forum_topics_width.'"/>% - '.__( 'default 15%', 'bbp-style-pack' );
                                        ?>
                                       
										<br/>
										<label class="description" for="bsp_style_settings_column_display[forum_topics_width_mobile]">
                                                <?php _e( 'Width mobile', 'bbp-style-pack' ); ?>
                                        </label>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[forum_topics_width_mobile]" id="bsp_style_settings_column_display[forum_topics_width_mobile]" class="small-text" type="text" value="'. $forum_topics_width_mobile.'"/>% - '.__( 'default 15%', 'bbp-style-pack' );
                                        ?>
							</td>   
                        </tr>
						<?php $forum_posts = (!empty( $bsp_style_settings_column_display['forum_posts'] ) ?  $bsp_style_settings_column_display['forum_posts'] : 0); ?>
               
						  <tr valign="top">  
                                <th style="width: 350px">
                                    <?php _e('Posts Column', 'bbp-style-pack'); ?>     
                                </th>

                              <td>
                                        <?php 
                                        echo '<input name="bsp_style_settings_column_display[forum_posts]" id="bsp_style_settings_column_display[forum_posts]" type="radio" value="0" class="code" ' . checked( 0,$forum_posts, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_column_display[forum_posts]">
                                                <?php _e( 'Show', 'bbp-style-pack' ); ?>
                                        </label>
										<br/>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[forum_posts]" id="bsp_style_settings_column_display[forum_posts]" type="radio" value="1" class="code" ' . checked( 1,$forum_posts, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_column_display[forum_posts]">
                                                <?php _e( 'Hide', 'bbp-style-pack' ); ?>
                                        </label>
										<br/>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[forum_posts]" id="bsp_style_settings_column_display[forum_posts]" type="radio" value="2" class="code" ' . checked( 2,$forum_posts, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_column_display[forum_posts]">
                                                <?php _e( 'Hide only on mobile', 'bbp-style-pack' ); ?>
                                        </label>
                                </td>   
                        </tr>
						<?php $forum_posts_width = (!empty( $bsp_style_settings_column_display['forum_posts_width'] ) ?  $bsp_style_settings_column_display['forum_posts_width'] : ''); ?>
						<?php $forum_posts_width_mobile = (!empty( $bsp_style_settings_column_display['forum_posts_width_mobile'] ) ?  $bsp_style_settings_column_display['forum_posts_width_mobile'] : ''); ?>
               
						 <tr valign="top">  
                                <td style="width: 350px">
								<i>
                                    <?php _e('If being shown', 'bbp-style-pack'); ?> 
								</i>									
                                </td>

                              <td>
                                        <label class="description" for="bsp_style_settings_column_display[forum_posts_width]">
                                                <?php _e( 'Width desktop', 'bbp-style-pack' ); ?>
                                        </label>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[forum_posts_width]" id="bsp_style_settings_column_display[forum_posts_width]" class="small-text" type="text" value="'. $forum_posts_width.'"/>% - '.__( 'default 15%', 'bbp-style-pack' );
                                        ?>
                                       
										<br/>
										<label class="description" for="bsp_style_settings_column_display[forum_posts_width_mobile]">
                                                <?php _e( 'Width mobile', 'bbp-style-pack' ); ?>
                                        </label>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[forum_posts_width_mobile]" id="bsp_style_settings_column_display[forum_posts_width_mobile]" class="small-text" type="text" value="'. $forum_posts_width_mobile.'"/>% - '.__( 'default 15%', 'bbp-style-pack' );
                                        ?>
							</td>   
                        </tr>
						<?php $forum_freshness = (!empty( $bsp_style_settings_column_display['forum_freshness'] ) ?  $bsp_style_settings_column_display['forum_freshness'] : 0); ?>
               
						 <tr valign="top">  
                                <th style="width: 350px">
                                    <?php _e('Last Post Column', 'bbp-style-pack'); ?>     
                                </th>

                              <td>
                                        <?php 
                                        echo '<input name="bsp_style_settings_column_display[forum_freshness]" id="bsp_style_settings_column_display[forum_freshness]" type="radio" value="0" class="code" ' . checked( 0,$forum_freshness, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_column_display[forum_freshness]">
                                                <?php _e( 'Show', 'bbp-style-pack' ); ?>
                                        </label>
										<br/>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[forum_freshness]" id="bsp_style_settings_column_display[forum_freshness]" type="radio" value="1" class="code" ' . checked( 1,$forum_freshness, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_column_display[forum_freshness]">
                                                <?php _e( 'Hide', 'bbp-style-pack' ); ?>
                                        </label>
										<br/>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[forum_freshness]" id="bsp_style_settings_column_display[forum_freshness]" type="radio" value="2" class="code" ' . checked( 2,$forum_freshness, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_column_display[forum_freshness]">
                                                <?php _e( 'Hide only on mobile', 'bbp-style-pack' ); ?>
                                        </label>
                                </td>   
                        </tr>
						<?php $forum_freshness_width = (!empty( $bsp_style_settings_column_display['forum_freshness_width'] ) ?  $bsp_style_settings_column_display['forum_freshness_width'] : ''); ?>
						<?php $forum_freshness_width_mobile = (!empty( $bsp_style_settings_column_display['forum_freshness_width_mobile'] ) ?  $bsp_style_settings_column_display['forum_freshness_width_mobile'] : ''); ?>
               
						 <tr valign="top">  
                                <td style="width: 350px">
								<i>
                                    <?php _e('If being shown', 'bbp-style-pack'); ?> 
								</i>									
                                </td>

                              <td>
                                        <label class="description" for="bsp_style_settings_column_display[forum_freshness_width]">
                                                <?php _e( 'Width desktop', 'bbp-style-pack' ); ?>
                                        </label>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[forum_freshness_width]" id="bsp_style_settings_column_display[forum_freshness_width]" class="small-text" type="text" value="'. $forum_freshness_width.'"/>% - '.__( 'default 15%', 'bbp-style-pack' );
                                        ?>
                                       
										<br/>
										<label class="description" for="bsp_style_settings_column_display[forum_freshness_width_mobile]">
                                                <?php _e( 'Width mobile', 'bbp-style-pack' ); ?>
                                        </label>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[forum_freshness_width_mobile]" id="bsp_style_settings_column_display[forum_freshness_width_mobile]" class="small-text" type="text" value="'. $forum_freshness_width_mobile.'"/>% - '.__( 'default 15%', 'bbp-style-pack' );
                                        ?>
							</td>   
                        </tr>
						
		<?php //**********************************************  TOPIC ?>
						<tr><td><hr></td></tr>
						  <?php $topic_display = (!empty( $bsp_style_settings_column_display['topic_activate'] ) ?  $bsp_style_settings_column_display['topic_activate'] : 0); ?>
              
						<tr valign="top">  
                                <th style="width: 350px">
                                        <?php _e('TOPICS DISPLAY', 'bbp-style-pack'); ?>
                                </th>

                                <td>
                                        <?php 
                                        echo '<input name="bsp_style_settings_column_display[topic_activate]" id="bsp_style_settings_column_display[topic_activate]" type="checkbox" value="1" class="code" ' . checked( 1,$topic_display, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_column_display[fse]">
                                                <?php _e( 'Activate', 'bbp-style-pack' ); ?>
                                        </label>
                                </td>
				</tr>
				<tr valign="top">  
                                <th style="width: 350px">
                                    <?php _e('Topic name Column', 'bbp-style-pack'); ?>     
                                </th>

						 
						<?php $topic_name_width = (!empty( $bsp_style_settings_column_display['topic_name_width'] ) ?  $bsp_style_settings_column_display['topic_name_width'] : ''); ?>
						<?php $topic_name_width_mobile = (!empty( $bsp_style_settings_column_display['topic_name_width_mobile'] ) ?  $bsp_style_settings_column_display['topic_name_width_mobile'] : ''); ?>
               
                                          <td>
                                        <label class="description" for="bsp_style_settings_column_display[topic_name]">
                                                <?php _e( 'Width desktop', 'bbp-style-pack' ); ?>
                                        </label>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[topic_name_width]" id="bsp_style_settings_column_display[topic_name_width]" class="small-text" type="text" value="'. $topic_name_width.'"/>% - '.__( 'default 55%', 'bbp-style-pack' );
                                        ?>
                                       
										<br/>
										<label class="description" for="bsp_style_settings_column_display[topic_name_mobile]">
                                                <?php _e( 'Width mobile', 'bbp-style-pack' ); ?>
                                        </label>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[topic_name_width_mobile]" id="bsp_style_settings_column_display[topic_name_width_mobile]" class="small-text" type="text" value="'. $topic_name_width_mobile.'"/>% - '.__( 'default 55%', 'bbp-style-pack' );
                                        ?>
							</td>   
                        </tr>
                        </tr>
						<?php $topic_topics = (!empty( $bsp_style_settings_column_display['topic_topics'] ) ?  $bsp_style_settings_column_display['topic_topics'] : 0); ?>
               
                        <tr valign="top">  
                                <th style="width: 350px">
                                    <?php _e('Topics Column', 'bbp-style-pack'); ?>     
                                </th>

                              <td>
                                        <?php 
                                        echo '<input name="bsp_style_settings_column_display[topic_topics]" id="bsp_style_settings_column_display[topic_topics]" type="radio" value="0" class="code" ' . checked( 0,$topic_topics, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_column_display[topic_topics]">
                                                <?php _e( 'Show', 'bbp-style-pack' ); ?>
                                        </label>
										<br/>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[topic_topics]" id="bsp_style_settings_column_display[topic_topics]" type="radio" value="1" class="code" ' . checked( 1,$topic_topics, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_column_display[topic_topics]">
                                                <?php _e( 'Hide', 'bbp-style-pack' ); ?>
                                        </label>
										<br/>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[topic_topics]" id="bsp_style_settings_column_display[topic_topics]" type="radio" value="2" class="code" ' . checked( 2,$topic_topics, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_column_display[topic_topics]">
                                                <?php _e( 'Hide only on mobile', 'bbp-style-pack' ); ?>
                                        </label>
                                </td>   
                        </tr>
						<?php $topic_topics_width = (!empty( $bsp_style_settings_column_display['topic_topics_width'] ) ?  $bsp_style_settings_column_display['topic_topics_width'] : ''); ?>
						<?php $topic_topics_width_mobile = (!empty( $bsp_style_settings_column_display['topic_topics_width_mobile'] ) ?  $bsp_style_settings_column_display['topic_topics_width_mobile'] : ''); ?>
               
						 <tr valign="top">  
                                <td style="width: 350px">
								<i>
                                    <?php _e('If being shown', 'bbp-style-pack'); ?> 
								</i>									
                                </td>

                              <td>
                                        <label class="description" for="bsp_style_settings_column_display[topic_topics_width]">
                                                <?php _e( 'Width desktop', 'bbp-style-pack' ); ?>
                                        </label>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[topic_topics_width]" id="bsp_style_settings_column_display[topic_topics_width]" class="small-text" type="text" value="'. $topic_topics_width.'"/>% - '.__( 'default 15%', 'bbp-style-pack' );
                                        ?>
                                       
										<br/>
										<label class="description" for="bsp_style_settings_column_display[topic_topics_width_mobile]">
                                                <?php _e( 'Width mobile', 'bbp-style-pack' ); ?>
                                        </label>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[topic_topics_width_mobile]" id="bsp_style_settings_column_display[topic_topics_width_mobile]" class="small-text" type="text" value="'. $topic_topics_width_mobile.'"/>% - '.__( 'default 15%', 'bbp-style-pack' );
                                        ?>
							</td>   
                        </tr>
						<?php $topic_posts = (!empty( $bsp_style_settings_column_display['topic_posts'] ) ?  $bsp_style_settings_column_display['topic_posts'] : 0); ?>
               
						  <tr valign="top">  
                                <th style="width: 350px">
                                    <?php _e('Posts Column', 'bbp-style-pack'); ?>     
                                </th>

                              <td>
                                        <?php 
                                        echo '<input name="bsp_style_settings_column_display[topic_posts]" id="bsp_style_settings_column_display[topic_posts]" type="radio" value="0" class="code" ' . checked( 0,$topic_posts, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_column_display[topic_posts]">
                                                <?php _e( 'Show', 'bbp-style-pack' ); ?>
                                        </label>
										<br/>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[topic_posts]" id="bsp_style_settings_column_display[topic_posts]" type="radio" value="1" class="code" ' . checked( 1,$topic_posts, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_column_display[topic_posts]">
                                                <?php _e( 'Hide', 'bbp-style-pack' ); ?>
                                        </label>
										<br/>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[topic_posts]" id="bsp_style_settings_column_display[topic_posts]" type="radio" value="2" class="code" ' . checked( 2,$topic_posts, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_column_display[topic_posts]">
                                                <?php _e( 'Hide only on mobile', 'bbp-style-pack' ); ?>
                                        </label>
                                </td>   
                        </tr>
						<?php $topic_posts_width = (!empty( $bsp_style_settings_column_display['topic_posts_width'] ) ?  $bsp_style_settings_column_display['topic_posts_width'] : ''); ?>
						<?php $topic_posts_width_mobile = (!empty( $bsp_style_settings_column_display['topic_posts_width_mobile'] ) ?  $bsp_style_settings_column_display['topic_posts_width_mobile'] : ''); ?>
               
						 <tr valign="top">  
                                <td style="width: 350px">
								<i>
                                    <?php _e('If being shown', 'bbp-style-pack'); ?> 
								</i>									
                                </td>

                              <td>
                                        <label class="description" for="bsp_style_settings_column_display[topic_posts_width]">
                                                <?php _e( 'Width desktop', 'bbp-style-pack' ); ?>
                                        </label>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[topic_posts_width]" id="bsp_style_settings_column_display[topic_posts_width]" class="small-text" type="text" value="'. $topic_posts_width.'"/>% - '.__( 'default 15%', 'bbp-style-pack' );
                                        ?>
                                       
										<br/>
										<label class="description" for="bsp_style_settings_column_display[topic_posts_width_mobile]">
                                                <?php _e( 'Width mobile', 'bbp-style-pack' ); ?>
                                        </label>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[topic_posts_width_mobile]" id="bsp_style_settings_column_display[topic_posts_width_mobile]" class="small-text" type="text" value="'. $topic_posts_width_mobile.'"/>% - '.__( 'default 15%', 'bbp-style-pack' );
                                        ?>
							</td>   
                        </tr>
						<?php $topic_freshness = (!empty( $bsp_style_settings_column_display['topic_freshness'] ) ?  $bsp_style_settings_column_display['topic_freshness'] : 0); ?>
               
						 <tr valign="top">  
                                <th style="width: 350px">
                                    <?php _e('Last Post Column', 'bbp-style-pack'); ?>     
                                </th>

                              <td>
                                        <?php 
                                        echo '<input name="bsp_style_settings_column_display[topic_freshness]" id="bsp_style_settings_column_display[topic_freshness]" type="radio" value="0" class="code" ' . checked( 0,$topic_freshness, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_column_display[topic_freshness]">
                                                <?php _e( 'Show', 'bbp-style-pack' ); ?>
                                        </label>
										<br/>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[topic_freshness]" id="bsp_style_settings_column_display[topic_freshness]" type="radio" value="1" class="code" ' . checked( 1,$topic_freshness, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_column_display[topic_freshness]">
                                                <?php _e( 'Hide', 'bbp-style-pack' ); ?>
                                        </label>
										<br/>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[topic_freshness]" id="bsp_style_settings_column_display[topic_freshness]" type="radio" value="2" class="code" ' . checked( 2,$topic_freshness, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_column_display[topic_freshness]">
                                                <?php _e( 'Hide only on mobile', 'bbp-style-pack' ); ?>
                                        </label>
                                </td>   
                        </tr>
						<?php $topic_freshness_width = (!empty( $bsp_style_settings_column_display['topic_freshness_width'] ) ?  $bsp_style_settings_column_display['topic_freshness_width'] : ''); ?>
						<?php $topic_freshness_width_mobile = (!empty( $bsp_style_settings_column_display['topic_freshness_width_mobile'] ) ?  $bsp_style_settings_column_display['topic_freshness_width_mobile'] : ''); ?>
               
						 <tr valign="top">  
                                <td style="width: 350px">
								<i>
                                    <?php _e('If being shown', 'bbp-style-pack'); ?> 
								</i>									
                                </td>

                              <td>
                                        <label class="description" for="bsp_style_settings_column_display[topic_freshness_width]">
                                                <?php _e( 'Width desktop', 'bbp-style-pack' ); ?>
                                        </label>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[topic_freshness_width]" id="bsp_style_settings_column_display[topic_freshness_width]" class="small-text" type="text" value="'. $topic_freshness_width.'"/>% - '.__( 'default 15%', 'bbp-style-pack' );
                                        ?>
                                       
										<br/>
										<label class="description" for="bsp_style_settings_column_display[topic_freshness_width_mobile]">
                                                <?php _e( 'Width mobile', 'bbp-style-pack' ); ?>
                                        </label>
										<?php 
                                        echo '<input name="bsp_style_settings_column_display[topic_freshness_width_mobile]" id="bsp_style_settings_column_display[topic_freshness_width_mobile]" class="small-text" type="text" value="'. $topic_freshness_width_mobile.'"/>% - '.__( 'default 15%', 'bbp-style-pack' );
                                        ?>
							</td>   
                        </tr>
			<tr><td><hr></td></tr>
                       
               
      
		</table>
		
		<!-- save the options -->
                        <p class="submit">
                                <input type="submit" class="button-primary" value="<?php _e( 'Save', 'bbp-style-pack' ); ?>" />
                        </p>
                </form>
   
<?php
} // end function bsp_style_settings_column_display
