<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


function bsp_register_forum_patterns() {
	register_block_pattern(
						'bbp-style-pack/forum-layout-a',
							array(
								'title'   => __( 'Forum - left sidebar', 'bbp-style-pack' ),
								'content' => '
								<!-- wp:columns -->
									<div class="wp-block-columns">
										
										<!-- wp:column {"width":"33.33%"} -->
											<div class="wp-block-column" style="flex-basis:33.33%">
												
												<!-- wp:bbp-style-pack/bsp-login-widget {"_wbbBlockId":"lg6t3625"} /-->
												<!-- wp:bbp-style-pack/bsp-latest-activity-widget {"_wbbBlockId":"lg6t2vh7","laExcludeForum":"0","laShowFreshness":false,"laShowAuthor":true,"laShowForum":false,"laShowReplyCount":false,"laOrderBy":"freshness","laShortenFreshness":false} /-->
												<!-- wp:bbp-style-pack/bsp-single-topic-information {"_wbbBlockId":"lg6t5dsr"} /-->
												<!-- wp:bbp-style-pack/bsp-single-forum-information {"_wbbBlockId":"lg6t5wbe"} /-->
												<!-- wp:bbp-style-pack/bsp-forums-list-widget {"_wbbBlockId":"lg6t7wxn"} /-->

											</div>
										<!-- /wp:column -->

										<!-- wp:column {"width":"66.66%"} -->
											<div class="wp-block-column" style="flex-basis:66.66%">
												<!-- wp:shortcode -->
													[bbp-forum-index]
												<!-- /wp:shortcode -->
											</div>
										<!-- /wp:column -->
									</div>
								<!-- /wp:columns -->',
								'description' => 'Left hand sidebar forum layout',
								'categories' => array( 'bsp-forums' ),
								
							)
	);
	register_block_pattern(
						'bbp-style-pack/forum-layout-b',
							array(
								'title'   => __( 'Forum - right sidebar', 'bbp-style-pack' ),
								'content' => '
								<!-- wp:columns -->
									<div class="wp-block-columns">
										
										<!-- wp:column {"width":"66.66%"} -->
											<div class="wp-block-column" style="flex-basis:66.66%">
												<!-- wp:shortcode -->
													[bbp-forum-index]
												<!-- /wp:shortcode -->
											</div>
										<!-- /wp:column -->
										
										<!-- wp:column {"width":"33.33%"} -->
											<div class="wp-block-column" style="flex-basis:33.33%">
												
												<!-- wp:bbp-style-pack/bsp-login-widget {"_wbbBlockId":"lg6t3625"} /-->
												<!-- wp:bbp-style-pack/bsp-latest-activity-widget {"_wbbBlockId":"lg6t2vh7","laExcludeForum":"0","laShowFreshness":false,"laShowAuthor":true,"laShowForum":false,"laShowReplyCount":false,"laOrderBy":"freshness","laShortenFreshness":false} /-->
												<!-- wp:bbp-style-pack/bsp-single-topic-information {"_wbbBlockId":"lg6t5dsr"} /-->
												<!-- wp:bbp-style-pack/bsp-single-forum-information {"_wbbBlockId":"lg6t5wbe"} /-->
												<!-- wp:bbp-style-pack/bsp-forums-list-widget {"_wbbBlockId":"lg6t7wxn"} /-->

											</div>
										<!-- /wp:column -->
									</div>
								<!-- /wp:columns -->',
								'description' => 'Left hand sidebar forum layout',
								'categories' => array( 'bsp-forums' ),
								
							)
	);
	
}