<?php

global $bsp_style_settings_topic_fields;


//form 

add_action ( 'bbp_theme_before_topic_form_content', 'bsp_topic_fields_form_input');

//if required field add error if blank
add_action( 'bbp_new_topic_pre_extras', 'bsp_required_topic_field' , 10 , 1) ;
add_action( 'bbp_edit_topic_pre_extras', 'bsp_required_topic_field' , 10 , 1) ;

function bsp_required_topic_field ($forum_id) {
	global $bsp_style_settings_topic_fields;
	$fields_number = bsp_get_topic_fields_number() ;
	$i=1 ;
	?>
	
	<?php
	//START OF LOOP
	while($i<= $fields_number)   {
		//get the forum id of this topic
		$check = false ;
		$name = 'forums'.$i ;
		//get the allowed as an array
		if (!empty ($bsp_style_settings_topic_fields[$name])) {
		$field_allowed_forums = explode (',' , $bsp_style_settings_topic_fields[$name])  ;
		if (in_array($forum_id, $field_allowed_forums )) $check = true ;
		}
		else $check = true ;
		if ($check == true) {
			if (!empty($bsp_style_settings_topic_fields ['itemrequired_item'.$i]) && empty( $_POST['bsp_topic_fields_label'.$i])) {
				$label =  $bsp_style_settings_topic_fields['item'.$i.'_label'] ;
				/* translators: %1s - $label */
				bbp_add_error( 'bbp_topic_fields_status', sprintf(__( '<strong>Error</strong>: Please complete the <strong>%1s</strong> field.', 'bbp-style-pack') , $label ));
			}
		}
		
	//increments $i	
		$i++;	
	}		
	
}

function bsp_topic_fields_form_input() {
	global $bsp_style_settings_topic_fields;
	$fields_number = bsp_get_topic_fields_number() ;
	$i=1 ;
	?>
	
	<?php
	//START OF LOOP
	while($i<= $fields_number)   {
			//item
				if(empty ($bsp_style_settings_topic_fields['hide_item_on_form'.$i] )) {
					$forum_id = bbp_get_forum_id() ;
					$check = false ;
					$name = 'forums'.$i ;
					//get the allowed as an array
					if (empty($bsp_style_settings_topic_fields[$name])) $check = true ;
					else {
					$field_allowed_forums = explode (',' , $bsp_style_settings_topic_fields[$name])  ;
					if (in_array($forum_id, $field_allowed_forums )) $check = true ;
					}
					if ( $check == true ) {
						$label =  (!empty ($bsp_style_settings_topic_fields['item'.$i.'_label']) ? $bsp_style_settings_topic_fields['item'.$i.'_label'] : '')  ;
						echo '<div id= "bsp-style-topic-fields-item'.$i.'">' ;			
						echo '<label for="bsp-style-topic-fields-item'.$i.'">'.$label.'</label><br/>' ;
										
						$current_value = get_post_meta( bbp_get_topic_id(), 'bsp_topic_fields_label'.$i, true);
						echo bsp_get_amend_field_input($i, $current_value) ;
						echo '</div>' ;
					}
				}	
		//increments $i	
		$i++;	
	}		
		
}

function bsp_get_amend_field_input ($i, $current_value) {
	global $bsp_style_settings_topic_fields;
	$label_id = 'bsp_topic_field_label'.$i ;
	//now decide whether to show text or dropdown field
		$name1 = 'field'.$i ;
		$item1='$bsp_style_settings_topic_fields['.$name1.']' ;
		$value = (!empty($bsp_style_settings_topic_fields[$name1]) ? $bsp_style_settings_topic_fields[$name1] : 0) ;
			//if zero then a text field
			if ($value==0) {
			$field= '<input type="text" name="bsp_topic_fields_label'.$i.'" id="bsp_topic_fields_label'.$i.'" value="'.$current_value.'" />' ;
			}
			//if 1 then a dropdown field
			if ($value==1) {
				//display the dropdown list
				$field = bsp_get_display_dropdown_options ($i, $current_value) ;
			}
return $field ;
}

function bsp_get_display_dropdown_options ($i,$current_value) {
	//explode the options
	global $bsp_style_settings_topic_fields;
	$list_options = explode("\n", $bsp_style_settings_topic_fields['fieldlist'.$i]);
	$total_options = count ($list_options) ;
	$list='<select name="bsp_topic_fields_label'.$i.'">' ;
	if (!empty ($current_value)) {
	 $list.='<option selected value="'.$current_value.'">'.$current_value.'</option>' ;
	}
	//array starts at zero, so start there until $total options-1
	$j=0 ;
	while ($j<= ($total_options-1))   {
		$list.=	'<option value="'.$list_options[$j].'">'.$list_options[$j].'</option>' ;
		$j++ ;
	}
	$list.=	'</select>' ;
return $list ;
}

//save
add_action ( 'bbp_new_topic_post_extras', 'bsp_topic_fields_form_save', 10, 1 );
add_action ( 'bbp_edit_topic_post_extras', 'bsp_topic_fields_form_save', 10, 1 );

function bsp_topic_fields_form_save ($topic_id = 0) {
	global $bsp_style_settings_topic_fields;
	$fields_number = bsp_get_topic_fields_number() ;
	
	$i=1 ;
	//START OF LOOP
	while($i<= $fields_number)   {
		$forum_id = bbp_get_forum_id() ;
		$check = false ;
		$name = 'forums'.$i ;
		//get the allowed as an array
		if (empty($bsp_style_settings_topic_fields[$name])) $check = true ;
		else {
			$field_allowed_forums = explode (',' , $bsp_style_settings_topic_fields[$name])  ;
			if (in_array($forum_id, $field_allowed_forums )) $check = true ;
		}
		if ( $check == true ) {
			//store label in all cases
			$label =  $bsp_style_settings_topic_fields['item'.$i.'_label'] ;
			update_post_meta( $topic_id, 'bsp_topic_fields_label_name'.$i,  $label);
			// Update post meta
			if ( !empty( $_POST['bsp_topic_fields_label'.$i] ) ) {
				update_post_meta( $topic_id, 'bsp_topic_fields_label'.$i,  $_POST['bsp_topic_fields_label'.$i]);
			}
			
			// Delete post meta
			else {
				delete_post_meta( $topic_id, 'bsp_topic_fields_label'.$i );
				$label =  $bsp_style_settings_topic_fields['item'.$i.'_label'] ;
			}
			
			//increments $i	
				$i++;	
		}
	}	
}

function bsp_get_topic_fields_number() {
	global $bsp_style_settings_topic_fields;
	$fields_number = (!empty($bsp_style_settings_topic_fields['number_of_fields']) ? $bsp_style_settings_topic_fields['number_of_fields'] : '1') ;
return $fields_number ;
}

//display

if ($bsp_style_settings_topic_fields['show_item_on_topic'] == 1) add_action('bbp_template_before_replies_loop', 'bsp_topic_content_append_topic_fields', 1, 3);
if ($bsp_style_settings_topic_fields['show_item_on_topic'] == 2) add_filter( 'bbp_theme_before_reply_content', 'bsp_topic_content_append_topic_fields', 1, 3 );
if ($bsp_style_settings_topic_fields['show_item_on_topic'] == 3) add_filter( 'bbp_theme_after_reply_content', 'bsp_topic_content_append_topic_fields', 1, 3 );


function bsp_topic_content_append_topic_fields($args = array()) {
	$topic_id = bbp_get_topic_id();
	$reply_id = bbp_get_reply_id() ;
	//bail if this is a reply
	if (bbp_is_reply($reply_id)) return ;
	global $bsp_style_settings_topic_fields;
	// Default arguments
	$defaults = array(
		'separator2' => '<hr />',
		'before'    => '<div class="bsp-topic-fields">',
		'after'     => '</div>'
	);
	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	$fields_number = bsp_get_topic_fields_number() ;
	$i=1 ;
	$topic_fields = '' ;
	//START OF LOOP
	while($i<= $fields_number)   {
		//get the forum id of this topic
		$forum_id = bbp_get_topic_forum_id($topic_id) ;
		$check = false ;
		$name = 'forums'.$i ;
		//get the allowed as an array
		if (!empty ($bsp_style_settings_topic_fields[$name])) {
		$field_allowed_forums = explode (',' , $bsp_style_settings_topic_fields[$name])  ;
		if (in_array($forum_id, $field_allowed_forums )) $check = true ;
		}
		else $check = true ;
		//so we should be showing for this forum, unless....
		$name = 'hide_item_on_topic'.$i ;
		$hide = (!empty($bsp_style_settings_topic_fields[$name]) ? $bsp_style_settings_topic_fields[$name] : '') ;
		if ($hide == true) $check = false ;
		//so if we should still be showing, what should we be showing...
			if ( $check == true ) {
				$name = 'item'.$i.'_label' ;
				$label= get_post_meta ($topic_id , 'bsp_topic_fields_label_name'.$i, true) ;
				//are we showing label?
				$name = 'labelhide_item'.$i ;
				$hide_label = (!empty($bsp_style_settings_topic_fields[$name]) ? $bsp_style_settings_topic_fields[$name] : '' ) ;
				//are we hiding label if field empty
				$name = 'labelhide_label'.$i ;
				$hide_label_blank = (!empty($bsp_style_settings_topic_fields[$name]) ? $bsp_style_settings_topic_fields[$name] : '' ) ;
				$data = get_post_meta ($topic_id , 'bsp_topic_fields_label'.$i, true) ;
				// if we have data, we'll be showing it, so add a <br> unless it is the first line
				if (!empty ($data) && !empty ($topic_fields)) $topic_fields.= '<br/>' ;
				//if data is empty, but we're not hiding label, then add a <br> 
				if (empty ($data) && empty($hide_label_blank)) $topic_fields.= '<br/>' ;
				//if showing label and not hiding on empty then add label
				$show_label = true ;
				//if we are hiding the label in all cases
				if (!empty ($hide_label)) $show_label = false ;
				//if no data and we are hiding label if no data...
				if (empty ($data) && !empty ($hide_label_blank)) $show_label = false ;
				if ($show_label == true) {
					$name = 'separator' ;
					$separator = (!empty($bsp_style_settings_topic_fields[$name]) ? $bsp_style_settings_topic_fields[$name] : ': ' ) ;
					$topic_fields.= '<span class="bsp_topic_fields_label">'.$label.'</span>'.$separator ;
				}
				//then add the data if not empty
				if (!empty ($data)) $topic_fields.= '<span class="bsp_topic_fields_data">'.$data.'</span>' ;
			}
				//increments $i	
		
			$i++;	
		}
			
	
	if (!empty ($topic_fields)) {
		if ($bsp_style_settings_topic_fields['show_item_on_topic'] == 1) $topic_fields_content = $before . $topic_fields . $after;
		if ($bsp_style_settings_topic_fields['show_item_on_topic'] == 2) $topic_fields_content = $before . $topic_fields . $after;
		if ($bsp_style_settings_topic_fields['show_item_on_topic'] == 3) $topic_fields_content = $before . $topic_fields . $after ;
		echo apply_filters( 'bsp_topic_content_append_topic_fields', $topic_fields_content, $separator );
	}
}

