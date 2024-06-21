<?php 

/**
 * Forums Loop
 *
 * @package bbPress
 * @subpackage Theme
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


?>

<?php do_action( 'bbp_template_before_forums_loop' ); ?>

<?php while ( bbp_forums() ) : bbp_the_forum();
		/* We print the header only if we want to show a category or if it's the first item of a no-parent forum list */
		if (bbp_is_forum_category() OR empty($bbp_forums_noheader))
		
		
			{ ?>
			<?php
			//now we need to check if this is a category within a category			
			if(bbp_is_forum_category()) {
			$parent = bbp_get_forum_parent_id() ;
				if ($parent && bbp_is_forum_category($parent) ) {
					//then it is a category within a category and we display the current category
					$link = '<a class="bbp-forum-name bsp-forum-name" href="'.bbp_get_forum_permalink().'">'.bbp_get_forum_title().'</a>' ;
					}
				else {
				//we display the parent as we have a forum list coming
					$link = '<a class="bbp-forum-name bsp-forum-name" href="'.bbp_get_forum_permalink(bbp_get_forum_parent_id()).'">'.bbp_get_forum_title(bbp_get_forum_parent_id()).'</a>' ;
								}
				}
				else { 
					$link = __( 'Forum', 'bbpress' ); 
				}
			?>
				
			
			
			<ul id="forums-list-<?php bbp_forum_id(); ?>" class="bbp-forums bsp-template1">

				<li class="bbp-header">

					<ul class="forum-titles">
						<li class="bbp-forum-info"><?php echo $link ; ?></li>
						<li class="bbp-forum-topic-count">
							<?php 
								global $bsp_style_settings_f ;
								//only show topics if icons not set
								if (empty ($bsp_style_settings_f['forum_icons'])) {
									esc_html_e( 'Topics', 'bbpress' );
								}
								?>
						</li>
						<li class="bbp-forum-reply-count">
							<?php
							//only show posts if icons not set
							if (empty ($bsp_style_settings_f['forum_icons'])) {
								bbp_show_lead_topic()
								? esc_html_e( 'Replies', 'bbpress' )
								: esc_html_e( 'Posts',   'bbpress' );
							}
							?>
						</li>
						<li class="bbp-forum-freshness"><?php _e( 'Last Post', 'bbpress' ); ?></li>
					</ul>

				</li><!-- .bbp-header -->
<?php 		} ?>

			<li class="bbp-body">
	
<?php 		/* If the forum is a category, we're gonna make another loop to show its subforums and sub-subforums as if those were forums */	
if(bbp_is_forum_category())
{

	$temp_query = clone bbpress()->forum_query;
	bbp_has_forums('post_parent='.bbp_get_forum_id());
	while ( bbp_forums() ) : bbp_the_forum();
	bbp_get_template_part( 'loop', 'single-forum' );
	endwhile;
	bbpress()->forum_query = clone $temp_query;

} 
else /* Otherwise, we print the forums the normal way */
{	

	bbp_get_template_part( 'loop', 'single-forum' );
	$bbp_forums_noheader = 1; /* This prevents the header part to be printed again on next post in the loop */

} ?>

			</li><!-- .bbp-body -->

<?php		/* Prints the footer only if :
				- it's a category
				- or if it's the last forum of a no-parent forum list
				- or if the next forum in the loop is a category */

			if(	bbp_is_forum_category() 
			OR 	(bbpress()->forum_query->current_post+1) == bbpress()->forum_query->post_count
			OR 	bbp_is_forum_category(bbpress()->forum_query->posts[ bbpress()->forum_query->current_post + 1 ]->ID)) 
			{ ?>

			<li class="bbp-footer">

				<div class="bsp-footer">
					<p class="bsp-footer2">&nbsp;</p>
				</div><!-- .bsp-footer -->

			</li><!-- .bbp-footer -->

		</ul><!-- .forums-directory -->

<?php 	unset($bbp_forums_noheader); /* Needed if we have 2+ no-parent forums with at least 1 category between them */
		} ?>
<?php endwhile; ?>


<?php do_action( 'bbp_template_after_forums_loop' ); ?>
