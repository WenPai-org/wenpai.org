<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


function bsp_add_featured_image_boxes()  {  
//The next line didn't work so reverted to previous method
//add_meta_box('bsp_forum_meta_box1', __('Image for forum list', 'bbp-style-pack'), 'post_thumbnail_meta_box', 'forum', 'side', 'low');  
add_meta_box( 'bsp_forum_meta_box2', __( 'Image Size for forum list' , 'bbp-style-pack') , 'bsp_forum_meta_box', 'forum', 'side', 'low' );
}  
//add the featured image box
add_filter ('bbp_get_forum_post_type_supports' , 'bsp_forum_thumbnail') ;


function bsp_forum_meta_box( $object, $box ) {
	global $post ;
	wp_nonce_field( 'forums_image_metabox_check', 'forums_image_metabox' );
	?>

	<div style="overflow: hidden;">
	
	<?php
	
	/* Get the meta*/
		
		$meta = get_post_meta( $post->ID, 'bsp_forum_thumbnail', true );
		
		
		$name =  'bsp_forum_thumbnail' ;
		$item = (!empty($meta) ? $meta : '');
		$name1= __('Thumbnail' , 'bbp-style-pack' );
		$name2= __('Medium' , 'bbp-style-pack' );
		$name3= __('Large' , 'bbp-style-pack' );
		$name4= __('Full' , 'bbp-style-pack' );
		$name5= __('Custom' , 'bbp-style-pack' );
		echo '<p>' ;
		_e ('By default the image will show as thumbnail in the forum lists, but you may wish to set a different size or custom size here.  Sizes are as defined in Dashboard>Settings>Media' , 'bbp-style-pack') ; 
		echo '</p><p>' ;
		echo '<input name="'.$name.'" id="'.$item.'" type="radio" value="1" class="code"  ' . checked( 1,$item, false ) . ' />'.$name1.'</p>' ;
		echo '<p>' ;
		echo '<input name="'.$name.'" id="'.$item.'" type="radio" value="2" class="code"  ' . checked( 2,$item, false ) . ' />'.$name2.'</p>' ;
		echo '<p>' ;
		echo '<input name="'.$name.'" id="'.$item.'" type="radio" value="3" class="code"  ' . checked( 3,$item, false ) . ' />'.$name3.'</p>' ;
		echo '<p>' ;
		echo '<input name="'.$name.'" id="'.$item.'" type="radio" value="4" class="code"  ' . checked( 4,$item, false ) . ' />'.$name4.'</p>' ;
		echo '<p>' ;
		echo '<input name="'.$name.'" id="'.$item.'" type="radio" value="5" class="code"  ' . checked( 5,$item, false ) . ' />'.$name5.'</p>' ;
		echo '<p>' ;
		_e ('If custom, enter width and height here : ' , 'bbp-style-pack') ;
		$name6 = __('Width' , 'bbp-style-pack' );
		$name7 = __('Height' , 'bbp-style-pack' );
		$area6='bsp_forum_thumbnailwidth' ;
		$area7='bsp_forum_thumbnailheight' ;
		$meta6 = get_post_meta( $post->ID, 'bsp_forum_thumbnailwidth', true );
		$meta7 = get_post_meta( $post->ID, 'bsp_forum_thumbnailheight', true );
		$item6 = (!empty($meta6) ? $meta6 : '');
		$item7 = (!empty($meta7) ? $meta7 : '');
		echo '<p>'.$name6.'<input id="'.$area6.'" class="small-text" name="'.$area6.'" type="text" value="'.esc_html( $item6 ).'"</p>' ; ?>
		<label class="description"><?php _e( 'e.g. 50', 'bbp-style-pack' ); ?></label><br/>
		<?php
		echo '<p>'.$name7.'<input id="'.$area7.'" class="small-text" name="'.$area7.'" type="text" value="'.esc_html( $item7 ).'"</p>' ; ?>
		<label class="description"><?php _e( 'e.g. 50', 'bbp-style-pack' ); ?></label><br/>
			
		</div><?php
}
/**
 * Saves the content permissions metabox data to a custom field.
 *
 */
function bsp_forum_save_meta( $post_id, $post ) {
	
	//check nonce
	if ( empty( $_POST['forums_image_metabox'] ) || ! wp_verify_nonce(sanitize_text_field( wp_unslash($_POST['forums_image_metabox'])), 'forums_image_metabox_check' ) ) {
		return;
	}

	
	
	/* Only allow users that can edit the current post to submit data. */
	if ( 'post' == $post->post_type && !current_user_can( 'edit_posts', $post_id ) )
		return;

	/* Only allow users that can edit the current page to submit data. */
	elseif ( 'page' == $post->post_type && !current_user_can( 'edit_pages', $post_id ) )
		return;
	
	if ( ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) || !isset( $_POST['bsp_forum_thumbnail'] ) )
        return;

	if (!empty ($_POST['bsp_forum_thumbnail'])) update_post_meta( $post_id, 'bsp_forum_thumbnail', sanitize_text_field(wp_unslash($_POST['bsp_forum_thumbnail'])));
	else delete_post_meta( $post_id, 'bsp_forum_thumbnail') ;
	if (!empty ($_POST['bsp_forum_thumbnailwidth'])) update_post_meta( $post_id, 'bsp_forum_thumbnailwidth', sanitize_text_field(wp_unslash($_POST['bsp_forum_thumbnailwidth'])));
	else delete_post_meta( $post_id, 'bsp_forum_thumbnailwidth') ;
	if (!empty ($_POST['bsp_forum_thumbnailheight'])) update_post_meta( $post_id, 'bsp_forum_thumbnailheight', sanitize_text_field(wp_unslash($_POST['bsp_forum_thumbnailheight'])));
	else delete_post_meta( $post_id, 'bsp_forum_thumbnailheight') ;
}



function bsp_forum_thumbnail() {
	return apply_filters( 'bsp_forum-thumbnail', array(
		'title',
		'editor',
		'revisions',
		'thumbnail'
	) );
}



?>
