
<?php 
$dataf = get_option('bsp_style_settings_f');
$datati = get_option('bsp_style_settings_ti');
$datat = get_option('bsp_style_settings_t');

$dataform = get_option('bsp_style_settings_form');
$datafd = get_option('bsp_forum_display');
$datacss = get_option('bsp_css');
$data4 = get_option('bsp_roles');
$databutton = get_option('bsp_style_settings_buttons');
global $bsp_forum_display;
global $bsp_roles;
global $bsp_breadcrumb;
global $bsp_style_settings_ti;
global $bsp_style_settings_search;
global $bsp_login_fail;
global $bsp_style_settings_topic_preview;
global $bsp_style_settings_quote;
global $bsp_style_settings_theme_support;
global $bsp_profile;
global $bsp_style_settings_modtools;
global $bsp_style_settings_la ;
global $bsp_style_settings_block_widgets ;
global $bsp_style_settings_column_display ;
global $bsp_style_settings_topic_fields ;




?>

/*  1 ----------------------  forum list backgrounds --------------------------*/
	<?php 
		$field = (!empty($dataf['Forum ContentBackground color - odd numbers']) ? $dataf['Forum ContentBackground color - odd numbers'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums ul.odd
			{
				background-color: <?php echo esc_html ($field); ?>;
			}
			<?php 
		}
		?>

	<?php 
		$field= (!empty($dataf['Forum ContentBackground image - odd numbers']) ? $dataf['Forum ContentBackground image - odd numbers'] : '');
		if (!empty ($field)){
			if (substr( $field, 0, 1) === '/') $field = substr($field, 1);
	?>
				#bbpress-forums ul.odd
			{
				background-image: url("/<?php echo esc_html ($field) ?>");
			}
			<?php 
		} 
		?>
 
	<?php 
		$field= (!empty($dataf['Forum ContentBackground color - even numbers']) ? $dataf['Forum ContentBackground color - even numbers'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums ul.even
			{
				background-color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>

	<?php 
		$field= (!empty($dataf['Forum ContentBackground image - even numbers']) ? $dataf['Forum ContentBackground image - even numbers'] : '');
		if (!empty ($field)){
			if (substr( $field, 0, 1) === '/') $field = substr($field, 1);
	?>
			#bbpress-forums ul.even
			{
				background-image: url("/<?php echo esc_html ($field) ?>");
			}
		<?php
		} 
		?>
/*  2 ----------------------  headers backgrounds --------------------------*/

	<?php 
		$field= (!empty($dataf['Forum/Topic Headers/FootersBackground Color']) ? $dataf['Forum/Topic Headers/FootersBackground Color'] : '');
		if (!empty ($field)){
		?>
			#bbpress-forums li.bbp-header,
			#bbpress-forums li.bbp-footer 
			{
				background-color: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>

	<?php 
		$field= (!empty($dataf['Forum/Topic Headers/FootersBackground Image']) ? $dataf['Forum/Topic Headers/FootersBackground Image'] : '');
		if (!empty ($field)){
			if (substr( $field, 0, 1) === '/') $field = substr($field, 1);
	?>
			#bbpress-forums li.bbp-header,
			#bbpress-forums li.bbp-footer 
			{
				background-image: url("/<?php echo esc_html ($field) ?>");
			}
		<?php
		}
		?>
  
/*  3 ----------------------  Font - Forum headings --------------------------*/
 
	<?php 
		$field= (!empty($dataf['Forum Headings FontSize']) ? $dataf['Forum Headings FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums ul.forum-titles li.bbp-forum-info
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		 
			#bbpress-forums ul.forum-titles li.bbp-forum-topic-count{
				font-size: <?php echo esc_html ($field); ?>;
			}

			#bbpress-forums ul.forum-titles li.bbp-forum-reply-count{
				font-size: <?php echo esc_html ($field); ?>;

			}

			#bbpress-forums ul.forum-titles li.bbp-forum-freshness{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php 
		}
		?>
 
	<?php 
	$field= (!empty($dataf['Forum Headings FontColor']) ? $dataf['Forum Headings FontColor'] : '');
	if (!empty ($field)){
	?>
		#bbpress-forums ul.forum-titles li.bbp-forum-info
		{
			color: <?php echo esc_html ($field); ?>;
		}
	 
		<?php //  and also allow for alternate template ?>
		#bbpress-forums ul.forum-titles a.bsp-forum-name
		{
			color: <?php echo esc_html ($field); ?>;
		}

		#bbpress-forums ul.forum-titles li.bbp-forum-topic-count
		{
			color: <?php echo esc_html ($field); ?>;
		}

                #bbpress-forums ul.forum-titles li.bbp-forum-reply-count
		{
		   color: <?php echo esc_html ($field); ?>;
		}

                #bbpress-forums ul.forum-titles li.bbp-forum-freshness 
		{
			color: <?php echo esc_html ($field); ?>;
		}
	<?php
	} 
	?>
 
	<?php 
		$field= (!empty($dataf['Forum Headings FontFont']) ? $dataf['Forum Headings FontFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums ul.forum-titles li.bbp-forum-info
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
	 
			#bbpress-forums ul.forum-titles li.bbp-forum-topic-count
			{
				font-family: <?php echo esc_html ($field); ?>;
			}

			#bbpress-forums ul.forum-titles li.bbp-forum-reply-count
			{
				font-family: <?php echo esc_html ($field); ?>;
			}

			#bbpress-forums ul.forum-titles li.bbp-forum-freshness
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
		
	<?php 
		$field= (!empty($dataf['Forum Headings FontStyle']) ? $dataf['Forum Headings FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbpress-forums ul.forum-titles li.bbp-forum-info
				{
					font-style: italic; 
				}
	 
				#bbpress-forums ul.forum-titles li.bbp-forum-topic-count 
				
					font-style: italic; 
				}

				#bbpress-forums ul.forum-titles li.bbp-forum-reply-count
				{
					font-style: italic; 
				}

				#bbpress-forums ul.forum-titles li.bbp-forum-freshness{
					font-style: italic; 
				}
		<?php
		} 
		if (strpos($field,'Bold') !== false){
		?>
			#bbpress-forums ul.forum-titles li.bbp-forum-info
			{
				font-weight: bold; 
			}
	 
			#bbpress-forums ul.forum-titles li.bbp-forum-topic-count
			{
				font-weight: bold; 
			}

			#bbpress-forums ul.forum-titles li.bbp-forum-reply-count
			{
				font-weight: bold; 
			}

			#bbpress-forums ul.forum-titles li.bbp-forum-freshness 
			{
				font-weight: bold; 
			}
		<?php
		}
		else { ?>
			#bbpress-forums ul.forum-titles li.bbp-forum-info 
			{
				font-weight: normal; 
			}
	 
			#bbpress-forums ul.forum-titles li.bbp-forum-topic-count
			{
				font-weight: normal; 
			}

			#bbpress-forums ul.forum-titles li.bbp-forum-reply-count 
			{
				font-weight: normal; 
			}

			#bbpress-forums ul.forum-titles li.bbp-forum-freshness 
			{
				font-weight: normal; 
			}
	 
		<?php
		} // end of else
	 
	}
	?>

/*  4 ----------------------  Font - breadcrumb --------------------------*/
 
	<?php 
		$field= (!empty($dataf['Breadcrumb FontSize']) ? $dataf['Breadcrumb FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums .bbp-breadcrumb p
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php 
		}
		?>
 
	<?php 
		$field= (!empty($dataf['Breadcrumb FontColor']) ? $dataf['Breadcrumb FontColor'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-breadcrumb p
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	 <?php 
		$field= (!empty($dataf['Breadcrumb FontFont']) ? $dataf['Breadcrumb FontFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-breadcrumb p
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($dataf['Breadcrumb FontStyle']) ? $dataf['Breadcrumb FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
			#bbpress-forums .bbp-breadcrumb p
			{
				font-style: italic; 
			}
		<?php 
		} 

		if (strpos($field,'Bold') !== false){
		?>
			#bbpress-forums .bbp-breadcrumb p
			{
				font-weight: bold; 
			}
		<?php
		}
		else {?>
			#bbpress-forums .bbp-breadcrumb p
			{
				font-weight: normal; 
			}
		<?php
		}
	}
	?>
 
/*  5 ----------------------  Font - links --------------------------*/
 
	<?php 
		$field= (!empty($dataf['LinksLink Color']) ? $dataf['LinksLink Color'] : '');
		if (!empty ($field)){
		?>
			#bbpress-forums a:link
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($dataf['LinksVisited Color']) ? $dataf['LinksVisited Color'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums a:visited
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
 
	<?php 
		$field= (!empty($dataf['LinksHover Color']) ? $dataf['LinksHover Color'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums a:hover
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php 
		}
		?>

/*  6 ----------------------  Font - Forum and category lists --------------------------*/
 
	<?php 
		$field= (!empty($dataf['Forum and Category List FontSize']) ? $dataf['Forum and Category List FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums .bbp-forum-title
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
	
	<?php 
		$field= (!empty($dataf['Forum and Category List FontFont']) ? $dataf['Forum and Category List FontFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-forum-title
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($dataf['Forum and Category List FontStyle']) ? $dataf['Forum and Category List FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbpress-forums .bbp-forum-title
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
		?>
				#bbpress-forums .bbp-forum-title
				{
					font-weight: bold; 
				}
			<?php
			}
			else {?>
				#bbpress-forums .bbp-forum-title
				{
					font-weight: normal; 
				}
			<?php
			}
		}
		?>

/*  7 ----------------------  Font - Sub Forum lists --------------------------*/
/*   !important added as bbpress 2.6 loads the min. css file, so doesn't allow the change in this plugin */
 
	<?php 
		$field= (!empty($dataf['Sub Forum List FontSize']) ? $dataf['Sub Forum List FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums .bbp-forums-list li
			{
				font-size: <?php echo esc_html ($field); ?> !important;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($dataf['Sub Forum List FontFont']) ? $dataf['Sub Forum List FontFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-forums-list li
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($dataf['Sub Forum List FontStyle']) ? $dataf['Sub Forum List FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){	
	?>
				#bbpress-forums .bbp-forums-list li
				{
					font-style: italic; 
				}
		<?php
			} 

			if (strpos($field,'Bold') !== false){
		?>
				#bbpress-forums .bbp-forums-list li
				{
					font-weight: bold; 
				}
			<?php 
			}
			else {?>
				#bbpress-forums .bbp-forums-list li
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>
 
/*  8 ----------------------  Font - forum description --------------------------*/
 
/*Note we also set bsp-forum-content as if add descriptions are set in forum display, then we need to replicate these settings */ 
  
	<?php 
		$field= (!empty($dataf['Forum Description FontSize']) ? $dataf['Forum Description FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums .bbp-forum-content, 
			#bbpress-forums .bsp-forum-content,
			#bbpress-forums .bbp-forum-info .bbp-forum-content
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($dataf['Forum Description FontColor']) ? $dataf['Forum Description FontColor'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-forum-content,
			#bbpress-forums .bsp-forum-content,
			#bbpress-forums .bbp-forum-info .bbp-forum-content
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($dataf['Forum Description FontFont']) ? $dataf['Forum Description FontFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-forum-content,
			#bbpress-forums .bsp-forum-content,
			#bbpress-forums .bbp-forum-info .bbp-forum-content
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($dataf['Forum Description FontStyle']) ? $dataf['Forum Description FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbpress-forums .bbp-forum-content,
				#bbpress-forums .bsp-forum-content
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums .bbp-forum-content,
				#bbpress-forums .bsp-forum-content,
				#bbpress-forums .bbp-forum-info .bbp-forum-content
				{
					font-weight: bold; 
				}
			<?php
			}
			else {?>
				 #bbpress-forums .bbp-forum-content,
				 #bbpress-forums .bsp-forum-content,
				 #bbpress-forums .bbp-forum-info .bbp-forum-content
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>
		 

/*  9 ----------------------  Font - Freshness --------------------------*/
 
	<?php 
		$field= (!empty($dataf['Freshness FontSize']) ? $dataf['Freshness FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums .bbp-forum-freshness, 
			#bbpress-forums .bbp-topic-freshness
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 

 
	<?php 
		$field= (!empty($dataf['Freshness FontFont']) ? $dataf['Freshness FontFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-forum-freshness, 
			#bbpress-forums .bbp-topic-freshness
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($dataf['Freshness FontStyle']) ? $dataf['Freshness FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbpress-forums .bbp-forum-freshness, 
				#bbpress-forums .bbp-topic-freshness
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums .bbp-forum-freshness
				{
					font-weight: bold; 
				}
			<?php 
			}
			else {?>
				#bbpress-forums .bbp-forum-freshness,
				#bbpress-forums .bbp-topic-freshness
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>
 
/*  10 ----------------------  Font - Freshness Author--------------------------*/
 
	<?php 
		$field= (!empty($dataf['Freshness Author FontSize']) ? $dataf['Freshness Author FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
				#bbpress-forums .bbp-topic-freshness-author
				{
					font-size: <?php echo esc_html ($field); ?>;
				}
			<?php
			} 
			?>
 
	<?php 
		$field= (!empty($dataf['Freshness Author FontFont']) ? $dataf['Freshness Author FontFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-topic-freshness-author
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($dataf['Freshness Author FontStyle']) ? $dataf['Freshness Author FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbpress-forums .bbp-topic-freshness-author
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums .bbp-topic-freshness-author
				{
					font-weight: bold; 
				}
			<?php
			}
			else {?>
				#bbpress-forums .bbp-topic-freshness-author
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>
		
/*  11 ----------------------  Freshness Avatar Size--------------------------*/

	<?php 
		$field= (!empty($dataf['Freshness AvatarSize']) ? $dataf['Freshness AvatarSize'] : '');
		if (is_numeric($field)) $field=$field.'px';
		if (!empty ($field)){
			?>
			
			#bbpress-forums p.bbp-topic-meta img.avatar {
					max-height: <?php echo esc_html ($field); ?>;
					max-width: <?php echo esc_html ($field); ?>;
				}
			<?php
			} 
			?>
 
 
/*  12 ----------------------  Forum border --------------------------*/
 
	<?php 
		$field1 = (!empty($dataf['Forum BorderLine width']) ? $dataf['Forum BorderLine width'] : '');
		$field2 = (!empty($dataf['Forum BorderLine style']) ? $dataf['Forum BorderLine style'] : '');
		$field3 = (!empty($dataf['Forum BorderColor']) ? $dataf['Forum BorderColor'] : '');

		if (!empty ($field1) || !empty ($field2) ||!empty ($field3)){
			if (empty ($field1)) $field1 = '1px';
			if (is_numeric($field1)) $field1=$field1.'px';
			if (empty ($field2)) $field2 = 'solid';
			$field=$field1.' '.$field2.' '.$field3
		?>

			#bbpress-forums ul.bbp-forums,
			#bbpress-forums ul.bbp-topics,
			#bbpress-forums .bbp-reply-header,
			#bbpress-forums div.odd,
			#bbpress-forums div.even,
			#bbpress-forums ul.bbp-replies
			{
				border: <?php echo esc_html ($field); ?>;
			}
		 
			#bbpress-forums li.bbp-header,
			#bbpress-forums li.bbp-body ul.forum,
			#bbpress-forums li.bbp-body ul.topic,
			#bbpress-forums li.bbp-footer,
			#bbpress-forums ul.forum
			{
				Border-top: <?php echo esc_html ($field); ?>;
			}
		
			#bbpress-forums li.bbp-footer
			{
				Border-bottom: <?php echo esc_html ($field); ?>;
			}
	 
		<?php 
		}
		?>
		<?php //fix for user profile display of topics when border is set to 0px ?>
		
		#bbpress-forums #bbp-user-wrapper ul.bbp-lead-topic, #bbpress-forums #bbp-user-wrapper ul.bbp-topics, #bbpress-forums #bbp-user-wrapper ul.bbp-replies {
    clear: both;
}
		

/*   13 ----------------------  Font - topic count --------------------------*/
 
	<?php 
		$field= (!empty($dataf['Topic Count FontSize']) ? $dataf['Topic Count FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
				#bbpress-forums li.bbp-forum-topic-count
				{
					font-size: <?php echo esc_html ($field); ?>;
				}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($dataf['Topic Count FontColor']) ? $dataf['Topic Count FontColor'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums li.bbp-forum-topic-count
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($dataf['Topic Count FontFont']) ? $dataf['Topic Count FontFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums li.bbp-forum-topic-count
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($dataf['Topic Count FontStyle']) ? $dataf['Topic Count FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbpress-forums li.bbp-forum-topic-count
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums li.bbp-forum-topic-count
				{
					font-weight: bold; 
				}
			<?php 
			}
			else { ?>
				#bbpress-forums li.bbp-forum-topic-count
				{
					font-weight: normal; 
				}
			 
		<?php
			}
		}
		?>

/*  14 ----------------------  Font - Post counts --------------------------*/
 
	<?php 
		$field= (!empty($dataf['Post Count FontSize']) ? $dataf['Post Count FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums li.bbp-forum-reply-count
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($dataf['Post Count FontColor']) ? $dataf['Post Count FontColor'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums li.bbp-forum-reply-count
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($dataf['Post Count FontFont']) ? $dataf['Post Count FontFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums li.bbp-forum-reply-count
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($dataf['Post Count FontStyle']) ? $dataf['Post Count FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbpress-forums li.bbp-forum-reply-count
				{
					font-style: italic; 
				}
			<?php 
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums li.bbp-forum-reply-count
				{
					font-weight: bold; 
				}
			<?php 
			}
			else { ?>
				#bbpress-forums li.bbp-forum-reply-count
				{
					font-weight: normal; 
				}
		<?php
			}
			 
		}
		?>
                                
/*  15 ----------------------  Message - empty forum --------------------------*/

        <?php 
		$field= (!empty($dataf['empty_indexActivate']) ? $dataf['empty_indexActivate'] : '');
		if (!empty ($field)){
        ?>
			#bbpress-forums .bbp-template-notice
			{
				display: none;
			}
		<?php
		}
		?> 

 
/********______________TOPIC INDEX___________________________________________*/ 

/*  1 ----------------------  Font - pagination --------------------------*/
 
	<?php 
		$field= (!empty($datati['Pagination FontSize']) ? $datati['Pagination FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums .bbp-pagination
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($datati['Pagination FontColor']) ? $datati['Pagination FontColor'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-pagination
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php 
		} 
		?>
 
	<?php 
		$field= (!empty($datati['Pagination FontFont']) ? $datati['Pagination FontFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-pagination
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php 
		}
		?>
 
	<?php 
		$field= (!empty($datati['Pagination FontStyle']) ? $datati['Pagination FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbpress-forums .bbp-pagination
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums .bbp-pagination
				{
					font-weight: bold; 
				}
			<?php
			}
			else { ?>
				#bbpress-forums .bbp-pagination
				{
					font-weight: normal; 
				}
		 
			<?php
			}
		}
		?>


/*  2 ----------------------  Font - voice/post count --------------------------*/
 
	<?php 
		$field= (!empty($datati['Voice/Post Count FontSize']) ? $datati['Voice/Post Count FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums li.bbp-topic-voice-count, li.bbp-topic-reply-count
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($datati['Voice/Post Count FontColor']) ? $datati['Voice/Post Count FontColor'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums li.bbp-topic-voice-count, li.bbp-topic-reply-count
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($datati['Voice/Post Count FontFont']) ? $datati['Voice/Post Count FontFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums li.bbp-topic-voice-count, li.bbp-topic-reply-count
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($datati['Voice/Post Count FontStyle']) ? $datati['Voice/Post Count FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbpress-forums li.bbp-topic-voice-count, li.bbp-topic-reply-count
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums li.bbp-topic-voice-count, li.bbp-topic-reply-count
				{
					font-weight: bold; 
				}
			<?php
			}
			else { ?>
				#bbpress-forums li.bbp-topic-voice-count, li.bbp-topic-reply-count
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>

/*  3 ----------------------  topic title Font - links --------------------------*/
 
	<?php 
		$field= (!empty($datati['Topic Title LinksLink Color']) ? $datati['Topic Title LinksLink Color'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums a.bbp-topic-permalink:link
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php 
		} 
		?>
 
	<?php 
		$field= (!empty($datati['Topic Title LinksVisited Color']) ? $datati['Topic Title LinksVisited Color'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums a.bbp-topic-permalink:visited
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($datati['Topic Title LinksHover Color']) ? $datati['Topic Title LinksHover Color'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums a.bbp-topic-permalink:hover
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
/*  4 ----------------------  Font - Topic Title --------------------------*/
 
	<?php 
		$field= (!empty($datati['Topic Title FontSize']) ? $datati['Topic Title FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
				#bbpress-forums .bbp-topic-title
		 		{
				font-size: <?php echo esc_html ($field); ?>;
				}
		<?php 
		} 
		?>
 
	<?php 
		$field= (!empty($datati['Topic Title FontFont']) ? $datati['Topic Title FontFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-topic-title
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php 
		} 
		?>
 
	<?php 
		$field= (!empty($datati['Topic Title FontStyle']) ? $datati['Topic Title FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbpress-forums .bbp-topic-title
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums .bbp-topic-title
				{
					font-weight: bold; 
				}
			<?php 
			}
			else {?>
				#bbpress-forums .bbp-topic-title
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>
 
/*   5 ----------------------  Font - template notice --------------------------*/
 
	<?php 
		$field= (!empty($datati['Template Notice FontSize']) ? $datati['Template Notice FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
				#bbpress-forums .bbp-template-notice p
				{
					font-size: <?php echo esc_html ($field); ?>;
				}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($datati['Template Notice FontColor']) ? $datati['Template Notice FontColor'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-template-notice p
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($datati['Template Notice FontFont']) ? $datati['Template Notice FontFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-template-notice p
			{
				font-family:: <?php echo esc_html ($field); ?>;
			}
		<?php 
		} 
		?>
 
	<?php 
		$field= (!empty($datati['Template Notice FontStyle']) ? $datati['Template Notice FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbpress-forums .bbp-template-notice p
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums .bbp-template-notice p
				{
					font-weight: bold; 
				}
			<?php
			}
			else { ?>
				#bbpress-forums .bbp-template-notice p
				{
					font-weight: normal; 
				}
			 
		<?php
			}
		}
		?>

/*  6 ----------------------  Font - template background --------------------------*/
 
	<?php 
		$field= (!empty($datati['Template NoticeBackground color']) ? $datati['Template NoticeBackground color'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-template-notice
			{
				background-color: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
/*  7 ----------------------  Font - template border --------------------------*/
 
	<?php 
		$field1 = (!empty($datati['Template Notice BorderLine width']) ? $datati['Template Notice BorderLine width'] : '');
		$field2 = (!empty($datati['Template Notice BorderLine style']) ? $datati['Template Notice BorderLine style'] : '');
		$field3 = (!empty($datati['Template Notice BorderLine color']) ? $datati['Template Notice BorderLine color'] : '');

		if (!empty ($field1) || !empty ($field2) ||!empty ($field3)){
			if (empty ($field1)) $field1 = '1px';
			if (is_numeric($field1)) $field1=$field1.'px';
			$field=$field1.' '.$field2.' '.$field3
	?>
			#bbpress-forums .bbp-template-notice
			{
				border: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>

/*  8 ----------------------  Font - Started by --------------------------*/
 
	<?php 
		$field= (!empty($datati['Topic Started bySize']) ? $datati['Topic Started bySize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
				#bbpress-forums .bbp-topic-started-by,
				.bbp-topic-started-in
				{
					font-size: <?php echo esc_html ($field); ?>;
				}
			<?php
			}
			?>
 
	<?php 
		$field= (!empty($datati['Topic Started byColor']) ? $datati['Topic Started byColor'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-topic-started-by,
			.bbp-topic-started-in
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($datati['Topic Started byFont']) ? $datati['Topic Started byFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-topic-started-by,
			.bbp-topic-started-in
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($datati['Topic Started byStyle']) ? $datati['Topic Started byStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbpress-forums .bbp-topic-started-by,
				.bbp-topic-started-in
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums .bbp-topic-started-by,
				.bbp-topic-started-in
				{
					font-weight: bold; 
				}
			<?php
			}
			else { ?>
				#bbpress-forums .bbp-topic-started-by,
				.bbp-topic-started-in
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>
		
/*  9 ----------------------  sticky/super sticky background --------------------------*/

	<?php 
		$field= (!empty($datati['Sticky Topic/ReplyBackground color - sticky topic']) ? $datati['Sticky Topic/ReplyBackground color - sticky topic'] : '');
		if (!empty ($field)){
	?>
			.bbp-topics ul.sticky,
			.bbp-forum-content ul.sticky
			{
				background-color: <?php echo esc_html ($field); ?> !important;
			}
		<?php
		} 
		?>

	<?php 
		$field= (!empty($datati['Sticky Topic/ReplyBackground color - super sticky topic']) ? $datati['Sticky Topic/ReplyBackground color - super sticky topic'] : '');
		if (!empty ($field)){
	?>
			.bbp-topics-front ul.super-sticky,
			.bbp-topics ul.super-sticky
			{
				background-color: <?php echo esc_html ($field); ?> !important;
			}

		<?php 
		} 
		?>

/*  10. ----------------------  Font - forum info notice (also does topic info)--------------------------*/
 
	<?php 
		$field= (!empty($datati['Forum Information FontSize']) ? $datati['Forum Information FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums div.bbp-template-notice.info .bbp-forum-description,
			#bbpress-forums div.bbp-template-notice.info .bbp-topic-description 
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($datati['Forum Information FontColor']) ? $datati['Forum Information FontColor'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums div.bbp-template-notice.info .bbp-forum-description,
			#bbpress-forums div.bbp-template-notice.info .bbp-topic-description 
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($datati['Forum Information FontFont']) ? $datati['Forum Information FontFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums div.bbp-template-notice.info .bbp-forum-description,
			#bbpress-forums div.bbp-template-notice.info .bbp-topic-description 
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php 
		}
		?>
 
	<?php 
		$field= (!empty($datati['Forum Information FontStyle']) ? $datati['Forum Information FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbpress-forums div.bbp-template-notice.info .bbp-forum-description,
				#bbpress-forums div.bbp-template-notice.info .bbp-topic-description 
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums div.bbp-template-notice.info .bbp-forum-description,
				#bbpress-forums div.bbp-template-notice.info .bbp-topic-description 
				{
					font-weight: bold; 
				}
			<?php
			}
			else { ?>
				#bbpress-forums div.bbp-template-notice.info .bbp-forum-description,
				#bbpress-forums div.bbp-template-notice.info .bbp-topic-description 
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>

/* 11 ----------------------  Font - forum info background  (also does topic info)--------------------------*/
 
	<?php 
		$field= (!empty($datati['Forum InformationBackground color']) ? $datati['Forum InformationBackground color'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums div.bbp-template-notice.info
			{
				background-color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
/*  12 ----------------------  Font - forum info border  (also does topic info)--------------------------*/
 
	<?php 
		$field1 = (!empty($datati['Forum Information BorderLine width']) ? $datati['Forum Information BorderLine width'] : '');
		$field2 = (!empty($datati['Forum Information BorderLine style']) ? $datati['Forum Information BorderLine style'] : '');
		$field3 = (!empty($datati['Forum Information BorderLine color']) ? $datati['Forum Information BorderLine color'] : '');

		if (!empty ($field1) || !empty ($field2) ||!empty ($field3)){
			if (empty ($field1)) $field1 = '1px';
			if (is_numeric($field1)) $field1=$field1.'px';
			$field=$field1.' '.$field2.' '.$field3
	?>
			#bbpress-forums div.bbp-template-notice.info
			{
				border: <?php echo esc_html ($field); ?>;
			}
		<?php 
		}
		?>
 
/*   13 ----------------------  Topic Index headings font --------------------------*/
 
	<?php 
		$field= (!empty($datati['Topic Index Headings FontSize']) ? $datati['Topic Index Headings FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>

				#bbpress-forums ul.forum-titles li.bbp-topic-title
				{
					font-size: <?php echo esc_html ($field); ?>;
				}
				 
				#bbpress-forums ul.forum-titles li.bbp-topic-voice-count
				{
					font-size: <?php echo esc_html ($field); ?>;
				}

				#bbpress-forums ul.forum-titles li.bbp-topic-reply-count
				{
					font-size: <?php echo esc_html ($field); ?>;
				}

				#bbpress-forums ul.forum-titles li.bbp-topic-freshness
				{
					font-size: <?php echo esc_html ($field); ?>;
				}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($datati['Topic Index Headings FontColor']) ? $datati['Topic Index Headings FontColor'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums ul.forum-titles li.bbp-topic-title
			{
				color: <?php echo esc_html ($field); ?>;
			}
			 
			#bbpress-forums ul.forum-titles li.bbp-topic-voice-count
			{
				color: <?php echo esc_html ($field); ?>;
			}

			#bbpress-forums ul.forum-titles li.bbp-topic-reply-count
			{
				color: <?php echo esc_html ($field); ?>;
			}

			#bbpress-forums ul.forum-titles li.bbp-topic-freshness
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($datati['Topic Index Headings FontFont']) ? $datati['Topic Index Headings FontFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums ul.forum-titles li.bbp-topic-title
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
			 
			#bbpress-forums ul.forum-titles li.bbp-topic-voice-count
			{
				font-family: <?php echo esc_html ($field); ?>;
			}

			#bbpress-forums ul.forum-titles li.bbp-topic-reply-count
			{
				font-family: <?php echo esc_html ($field); ?>;
			}

			#bbpress-forums ul.forum-titles li.bbp-topic-freshness
			{
				font-family: <?php echo esc_html ($field); ?>;
			}

		<?php 
		} 
		?>
 
	<?php 
		$field= (!empty($datati['Topic Index Headings FontStyle']) ? $datati['Topic Index Headings FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbpress-forums ul.forum-titles li.bbp-topic-title
				{
					font-style: italic; 
				}
				 
				#bbpress-forums ul.forum-titles li.bbp-topic-voice-count
				{
					font-style: italic; 
				}

				#bbpress-forums ul.forum-titles li.bbp-topic-reply-count
				{
					font-style: italic; 
				}

				#bbpress-forums ul.forum-titles li.bbp-topic-freshness
				{
					font-style: italic; 
				}

			<?php 
			} 

			if (strpos($field,'Bold') !== false){
			?>

				#bbpress-forums ul.forum-titles li.bbp-topic-title
				{
					font-weight: bold; 
				}
				 
				#bbpress-forums ul.forum-titles li.bbp-topic-voice-count
				{
					font-weight: bold; 
				}

				#bbpress-forums ul.forum-titles li.bbp-topic-reply-count
				{
					font-weight: bold; 
				}

				#bbpress-forums ul.forum-titles li.bbp-topic-freshness
				{
					font-weight: bold; 
				}

			<?php 
			}
			else { ?>
				#bbpress-forums ul.forum-titles li.bbp-topic-title{
					font-weight: normal;
				}
				 
				#bbpress-forums ul.forum-titles li.bbp-topic-voice-count{
					font-weight: normal;
				}

				#bbpress-forums ul.forum-titles li.bbp-topic-reply-count{
					font-weight: normal; 
				}

				#bbpress-forums ul.forum-titles li.bbp-topic-freshness{
					font-weight: normal;
				}
		<?php
			}
		}
		?>
		<?php
/*  21 NOT USED----------------------  Topic Avatar Size--------------------------

 
	<?php 
		$field= (!empty($dataf['Freshness AvatarSize']) ? $dataf['Freshness AvatarSize'] : '');
		$field2= (!empty($datati['Freshness AvatarSize']) ? $datati['Freshness AvatarSize'] : '');
		if (is_numeric($field) && is_numeric($field2)) {
		//and change number if topics index is bigger
				if ($field<$field2) $field = $field2;
		}
		if (is_numeric($field)) $field=$field.'px';
		if (!empty ($field)){
			?>
			
			#bbpress-forums p.bbp-topic-meta img.avatar {
					max-height: <?php echo esc_html ($field); ?>;
					max-width: <?php echo esc_html ($field); ?>;
				}
			<?php
			} 
			*/
			?>
		

			 
/*******_________________TOPIC/REPLY___________________________________________*/ 

/*   1 ----------------------topic/reply backgrounds   --------------------------*/

	<?php 
		$field= (!empty($datat['Topic/Reply ContentBackground color - odd numbers']) ? $datat['Topic/Reply ContentBackground color - odd numbers']  : '');
		if (!empty ($field)){
	?>
			#bbpress-forums div.odd
			{
				background-color: <?php echo esc_html ($field); ?>;
			}
		<?php 
		} 
		?>

	<?php 
		$field= (!empty($datat['Topic/Reply ContentBackground color - even numbers']) ? $datat['Topic/Reply ContentBackground color - even numbers'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums div.even
			{
				background-color: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
/*   2 ----------------------  Topic/reply header background --------------------------*/
 
	<?php 
		$field= (!empty($datat['Topic/Reply HeaderBackground color']) ? $datat['Topic/Reply HeaderBackground color'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums div.bbp-reply-header,
			#bbpress-forums div.bbp-topic-header
			{
				background-color: <?php echo esc_html ($field); ?>;
			}
		<?php 
		} 
		?>
		 
/*   3 ----------------------  Trash/Spam backgrounds --------------------------*/
 
	<?php 
		$field= (!empty($datat['Trash/Spam ContentBackground color - odd numbers']) ? $datat['Trash/Spam ContentBackground color - odd numbers'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .status-trash.odd,
			#bbpress-forums .status-spam.odd 
			{
				background-color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>

	<?php 
		$field= (!empty($datat['Trash/Spam ContentBackground color - even numbers']) ? $datat['Trash/Spam ContentBackground color - even numbers'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .status-trash.even,
			#bbpress-forums .status-spam.even
			{
				background-color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
/*   4 ----------------------  Closed Topic backgrounds --------------------------*/
 
	<?php 
		$field= (!empty($datat['Closed Topic ContentBackground color']) ? $datat['Closed Topic ContentBackground color'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .status-closed,
			#bbpress-forums .status-closed a
			{
				background-color: <?php echo esc_html ($field); ?>;
			}
		<?php 
		} 
		?>
 
/*   5 ----------------------  Font - topic/reply date --------------------------*/

	<?php 
		$field= (!empty($datat['Topic/Reply Date FontSize']) ? $datat['Topic/Reply Date FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums .bbp-reply-post-date
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($datat['Topic/Reply Date FontColor']) ? $datat['Topic/Reply Date FontColor'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-reply-post-date
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($datat['Topic/Reply Date FontFont']) ? $datat['Topic/Reply Date FontFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-reply-post-date
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php 
		} 
		?>
 
	<?php 
		$field= (!empty($datat['Topic/Reply Date FontStyle']) ? $datat['Topic/Reply Date FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
			?>
				#bbpress-forums .bbp-reply-post-date
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums .bbp-reply-post-date
				{
					font-weight: bold; 
				}
			<?php 
			}
			else {?>
				#bbpress-forums .bbp-reply-post-date
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>

/*   6 ----------------------  Font - topic/reply text --------------------------*/
 

	<?php 
		$field= (!empty($datat['Topic/Reply Text FontSize']) ? $datat['Topic/Reply Text FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums .bbp-topic-content, 
			#bbpress-forums .bbp-reply-content
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php 
		} 
		?>
 
	<?php 
		$field= (!empty($datat['Topic/Reply Text FontColor']) ? $datat['Topic/Reply Text FontColor'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-topic-content, 
			#bbpress-forums .bbp-reply-content
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($datat['Topic/Reply Text FontFont']) ? $datat['Topic/Reply Text FontFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-topic-content, 
			#bbpress-forums .bbp-reply-content
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($datat['Topic/Reply Text FontStyle']) ? $datat['Topic/Reply Text FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
			?>
				#bbpress-forums .bbp-topic-content,
				#bbpress-forums .bbp-reply-content
				{
					font-style: italic; 
				}
			<?php 
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums .bbp-topic-content,
				#bbpress-forums .bbp-reply-content
				{
					font-weight: bold; 
				}
			<?php
			}
			else {?>
				#bbpress-forums .bbp-topic-content,
				#bbpress-forums .bbp-reply-content
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>
 
/*   7 ----------------------  Font - Author name --------------------------*/
 
	<?php 
		$field= (!empty($datat['Author Name FontSize']) ? $datat['Author Name FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums div.bbp-reply-author .bbp-author-name
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($datat['Author Name FontFont']) ? $datat['Author Name FontFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums div.bbp-reply-author .bbp-author-name
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($datat['Author Name FontStyle']) ? $datat['Author Name FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
			?>
				#bbpress-forums div.bbp-reply-author .bbp-author-name
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums div.bbp-reply-author .bbp-author-name
				{
					font-weight: bold; 
				}
			<?php
			}
			else {?>
				#bbpress-forums div.bbp-reply-author .bbp-author-name
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>
		
/*   8 ----------------------  Font - reply permalink --------------------------*/
 
	<?php 
		$field= (!empty($datat['Reply Link FontSize']) ? $datat['Reply Link FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums a.bbp-reply-permalink
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php 
		} 
		?>
 

 
	<?php 
		$field= (!empty($datat['Reply Link FontFont']) ? $datat['Reply Link FontFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums a.bbp-reply-permalink
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($datat['Reply Link FontStyle']) ? $datat['Reply Link FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbpress-forums a.bbp-reply-permalink
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums a.bbp-reply-permalink
				{
				font-weight: bold; 
				}
			<?php 
			}
			else {?>
				#bbpress-forums a.bbp-reply-permalink
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>
 
/*   9 ----------------------  Font - author role --------------------------*/
 
	<?php 
		$field= (!empty($datat['Author RoleSize']) ? $datat['Author RoleSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums div.bbp-reply-author .bbp-author-role
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($datat['Author RoleColor']) ? $datat['Author RoleColor'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums div.bbp-reply-author .bbp-author-role
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($datat['Author RoleFont']) ? $datat['Author RoleFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums div.bbp-reply-author .bbp-author-role
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($datat['Author RoleStyle']) ? $datat['Author RoleStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
			?>
				#bbpress-forums div.bbp-reply-author .bbp-author-role
				{
					font-style: italic; 
				}
			<?php 
			}
			else {?>
				#bbpress-forums div.bbp-reply-author .bbp-author-role
				{
					font-style: normal; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums div.bbp-reply-author .bbp-author-role
				{
					font-weight: bold; 
				}
			<?php
			}
			else {?>
				#bbpress-forums div.bbp-reply-author .bbp-author-role
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>
 
/*   10 ----------------------  Topic Header --------------------------*/
 
	<?php 
		$field= (!empty($datat['Topic HeaderSize']) ? $datat['Topic HeaderSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
				#bbpress-forums li.bbp-header .bbp-reply-content,
				#bbpress-forums li.bbp-header  .bbp-reply-author,
				#bbpress-forums li.bbp-footer .bbp-reply-content,
				#bbpress-forums li.bbp-footer  .bbp-reply-author
				{		
					font-size: <?php echo esc_html ($field); ?>;
				}
		<?php 
		} 
		?>
 
	<?php 
		$field= (!empty($datat['Topic HeaderColor']) ? $datat['Topic HeaderColor'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums li.bbp-header .bbp-reply-content,
			#bbpress-forums li.bbp-header  .bbp-reply-author,
			#bbpress-forums li.bbp-footer .bbp-reply-content,
			#bbpress-forums li.bbp-footer  .bbp-reply-author
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($datat['Topic HeaderFont']) ? $datat['Topic HeaderFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums li.bbp-header .bbp-reply-content,
			#bbpress-forums li.bbp-header  .bbp-reply-author,
			#bbpress-forums li.bbp-footer .bbp-reply-content,
			#bbpress-forums li.bbp-footer  .bbp-reply-author
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($datat['Topic HeaderStyle']) ? $datat['Topic HeaderStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
			?>
				#bbpress-forums li.bbp-header .bbp-reply-content,
				#bbpress-forums li.bbp-header  .bbp-reply-author,
				#bbpress-forums li.bbp-footer .bbp-reply-content,
				#bbpress-forums li.bbp-footer  .bbp-reply-author
				{
					font-style: italic; 
				}
			<?php 
			}
			else {?>
				#bbpress-forums li.bbp-header .bbp-reply-content,
				#bbpress-forums li.bbp-header  .bbp-reply-author,
				#bbpress-forums li.bbp-footer .bbp-reply-content,
				#bbpress-forums li.bbp-footer  .bbp-reply-author
				{
					font-style: normal; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums li.bbp-header .bbp-reply-content,
				#bbpress-forums li.bbp-header  .bbp-reply-author,
				#bbpress-forums li.bbp-footer .bbp-reply-content,
				#bbpress-forums li.bbp-footer  .bbp-reply-author
				{
					font-weight: bold; 
				}
			<?php 
			}
			else {?>
				#bbpress-forums li.bbp-header .bbp-reply-content,
				#bbpress-forums li.bbp-header  .bbp-reply-author,
				#bbpress-forums li.bbp-footer .bbp-reply-content,
				#bbpress-forums li.bbp-footer  .bbp-reply-author
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>
			 
 
/*   11 ----------------------  Topic Admin Links --------------------------*/
 
	<?php 
		$field= (!empty($datat['Topic Admin linksSize']) ? $datat['Topic Admin linksSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums span.bbp-admin-links a,
			#bbpress-forums span.bbp-admin-links 
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($datat['Topic Admin linksColor']) ? $datat['Topic Admin linksColor'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums span.bbp-admin-links a,
			#bbpress-forums span.bbp-admin-links 
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($datat['Topic Admin linksFont']) ? $datat['Topic Admin linksFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums span.bbp-admin-links a,
			#bbpress-forums span.bbp-admin-links 
			{
			font-family: <?php echo esc_html ($field); ?>;
			}
		<?php 
		}
		?>
 
	<?php 
		$field= (!empty($datat['Topic Admin linksStyle']) ? $datat['Topic Admin linksStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbpress-forums span.bbp-admin-links a,
				#bbpress-forums span.bbp-admin-links 
				{
					font-style: italic; 
				}
			<?php
			}
			else {?>
				#bbpress-forums span.bbp-admin-links a,
				#bbpress-forums span.bbp-admin-links 
				{
					font-style: normal; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums span.bbp-admin-links a,
				#bbpress-forums span.bbp-admin-links 
				{
					font-weight: bold; 
				}
			<?php
			}
			else {?>
				#bbpress-forums span.bbp-admin-links a,
				#bbpress-forums span.bbp-admin-links 
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>
	
/*   13 ----------------------  @mentions --------------------------*/
 
	<?php 
		$field= (!empty($datat['mentionsSize']) ? $datat['mentionsSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums .bsp-mentions a,
			#bbpress-forums .bsp-mentions 
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($datat['mentionsColor']) ? $datat['mentionsColor'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bsp-mentions a,
			#bbpress-forums .bsp-mentions 
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
	
	<?php 
		$field= (!empty($datat['mentionsFont']) ? $datat['mentionsFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bsp-mentions a,
			#bbpress-forums .bsp-mentions 
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($datat['mentionsStyle']) ? $datat['mentionsStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbpress-forums .bsp-mentions a,
				#bbpress-forums .bsp-mentions
				{
					font-style: italic; 
				}
			<?php
			}
			else {?>
				#bbpress-forums .bsp-mentions a,
				#bbpress-forums .bsp-mentions 
				{
					font-style: normal; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums .bsp-mentions a,
				#bbpress-forums .bsp-mentions 
				{
					font-weight: bold; 
				}
			<?php
			}
			else {?>
				#bbpress-forums .bsp-mentions a,
				#bbpress-forums .bsp-mentions 
				{
					font-weight: normal; 
				}
		<?php
			}
			}
		?>
	
/* *******_________________TOPIC REPLY FORM___________________________________________*/ 
 
/*   1 ----------------------  Topic/reply Labels --------------------------*/
 
	<?php 
		$field= (!empty($dataform['LabelsSize']) ? $dataform['LabelsSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums .bbp-form label
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($dataform['LabelsColor']) ? $dataform['LabelsColor'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-form label
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($dataform['LabelsFont']) ? $dataform['LabelsFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-form label
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($dataform['LabelsStyle']) ? $dataform['LabelsStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
			?>
				#bbpress-forums .bbp-form label
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums .bbp-form label
				{
					font-weight: bold; 
				}
			<?php 
			}
			else {?>
				#bbpress-forums .bbp-form label
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>
 
/* 2 ----------------------  Text area background --------------------------*/
 
	<?php 
		$field= (!empty($dataform['Text areaBackground Color']) ? $dataform['Text areaBackground Color'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums input[type="text"], textarea, 
			#bbpress-forums input[type="text"]:focus, textarea:focus,
			#bbpress-forums .quicktags-toolbar
			{
				background-color: <?php echo esc_html ($field); ?>;
			}
		 
		<?php 
		} 
		?>
		 
/*   3 ----------------------  Text area font --------------------------*/
 
	<?php 
		$field= (!empty($dataform['Text areaSize']) ? $dataform['Text areaSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums input[type="text"], textarea, 
			#bbpress-forums .quicktags-toolbar ,
			#bbpress-forums div.bbp-the-content-wrapper textarea.bbp-the-content
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($dataform['Text areaColor']) ? $dataform['Text areaColor'] : '');
		if (!empty ($field)){
		?>
			#bbpress-forums input[type="text"], textarea, 
			#bbpress-forums .quicktags-toolbar ,
			#bbpress-forums div.bbp-the-content-wrapper textarea.bbp-the-content
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php 
		}
		?>
 
	<?php 
		$field= (!empty($dataform['Text areaFont']) ? $dataform['Text areaFont'] : '');
		if (!empty ($field)){
		?>
			#bbpress-forums input[type="text"], textarea, 
			#bbpress-forums .quicktags-toolbar ,
			#bbpress-forums div.bbp-the-content-wrapper textarea.bbp-the-content
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($dataform['Text areaStyle']) ? $dataform['Text areaStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
			?>
				#bbpress-forums input[type="text"], textarea, 
				#bbpress-forums .quicktags-toolbar ,
				#bbpress-forums div.bbp-the-content-wrapper textarea.bbp-the-content
				{
					font-style: italic; 
				}
			<?php 
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums input[type="text"], textarea, 
				#bbpress-forums .quicktags-toolbar ,
				#bbpress-forums div.bbp-the-content-wrapper textarea.bbp-the-content
				{
					font-weight: bold; 
				}
			<?php
			}
			else {?>
				#bbpress-forums input[type="text"], textarea, 
				#bbpress-forums .quicktags-toolbar ,
				#bbpress-forums div.bbp-the-content-wrapper textarea.bbp-the-content
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>
			 
/*   4 ----------------------  button background --------------------------*/

	<?php 
		$field= (!empty($dataform['ButtonBackground Color']) ? $dataform['ButtonBackground Color'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .button
			{
				  background-color: <?php echo esc_html ($field); ?>;
		  	}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($dataform['ButtonText Color']) ? $dataform['ButtonText Color'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .button
			{
				color: <?php echo esc_html ($field); ?>;
			}

		<?php
		}
		?>
 
/*   1 ----------------------  topic posting rules --------------------------*/
 
	<?php 
		$field= (!empty($dataform['topic_posting_rulesSize']) ? $dataform['topic_posting_rulesSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums .bsp-topic-rules
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php 
		} 
		?>
 
	<?php 
		$field= (!empty($dataform['topic_posting_rulesColor']) ? $dataform['topic_posting_rulesColor'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bsp-topic-rules
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php 
		}
		?>
 

 
	<?php 
		$field= (!empty($dataform['topic_posting_rulesFont']) ? $dataform['topic_posting_rulesFont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bsp-topic-rules
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($dataform['topic_posting_rulesStyle']) ? $dataform['topic_posting_rulesStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
			?>
				#bbpress-forums .bsp-topic-rules
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				#bbpress-forums .bsp-topic-rules
				{
				font-weight: bold; 
				}
			<?php 
			}
			else {?>
				#bbpress-forums .bsp-topic-rules
				{
				font-weight: normal; 
				}
		<?php
			}
		}
		?>
 
	<?php 
		$field= (!empty($dataform['topic_posting_rulesBackground Color']) ? $dataform['topic_posting_rulesBackground Color'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bsp-topic-rules
			{
			background-color: <?php echo esc_html ($field); ?>;
			}
		 
		<?php 
		}
		?>
 
	<?php 
		$field= (!empty($dataform['topic_posting_rulesborder_color']) ? $dataform['topic_posting_rulesborder_color'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bsp-topic-rules
			{
				border-color: <?php echo esc_html ($field); ?>;
				border-radius: 3px;
				border-style: solid;
				border-width: 1px;
			}
		 
		<?php 
		} 
		?>
 
/* ********_________________Forum Display___________________________________________*/ 

/*   1 ----------------------  Alter the list from horizontal to vertical - remove comma (,) seperator in 2.6--------------------------*/
	<?php 
		$field= (!empty($bsp_forum_display['forum_list']) ? $bsp_forum_display['forum_list'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums .bbp-forums-list .bbp-forum.css-sep:not(:last-child)::after {
				content: none;
			} 
		<?php
		}
		?>
 
/*   9 ----------------------  Remove Forum Description --------------------------*/
	<?php 
		$field= (!empty($bsp_forum_display['forum-description']) ? $bsp_forum_display['forum-description'] : '');
		if (!empty ($field)){
	?>
			div.bbp-template-notice.info
			{
				display: none;
			}
		<?php
		}
		?>
		
/*********_________________SINGLE FORUM WIDGET___________________________________________*/ 

ul.bsp-sf-info-list a.subscription-toggle {
float : left ;
}	

 
/*********_________________LATEST ACTIVITY WIDGET___________________________________________*/ 
 
/*   2 ----------------------  Widget title --------------------------*/
 
	<?php 
		$field= (!empty($bsp_style_settings_la['Widget TitleSize']) ? $bsp_style_settings_la['Widget TitleSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
				.bsp-la-title h1, 
				.bsp-la-title h2,
				.bsp-la-title h3,
				.bsp-la-title h4,
				.bsp-la-title h5
				{
					font-size: <?php echo esc_html ($field); ?>;
				}
		<?php 
		} 
		?>
 
	<?php   
                $field= (!empty($bsp_style_settings_la['Widget TitleColor']) ? $bsp_style_settings_la['Widget TitleColor'] : '');
		if (!empty ($field)){
	?>
			.bsp-la-title h1, 
			.bsp-la-title h2,
			.bsp-la-title h3,
			.bsp-la-title h4,
			.bsp-la-title h5
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php 
		}
		?>
		 
	<?php 
		$field= (!empty($bsp_style_settings_la['Widget TitleFont']) ? $bsp_style_settings_la['Widget TitleFont'] : '');
		if (!empty ($field)){
	?>
			.bsp-la-title h1, 
			.bsp-la-title h2,
			.bsp-la-title h3,
			.bsp-la-title h4,
			.bsp-la-title h5
			{
			font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($bsp_style_settings_la['Widget TitleStyle']) ? $bsp_style_settings_la['Widget TitleStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
			?>
				.bsp-la-title h1, 
				.bsp-la-title h2,
				.bsp-la-title h3,
				.bsp-la-title h4,
				.bsp-la-title h5
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				.bsp-la-title h1, 
				.bsp-la-title h2,
				.bsp-la-title h3,
				.bsp-la-title h4,
				.bsp-la-title h5
				{
					font-weight: bold; 
				}
			<?php
			}
			else {?>
				.bsp-la-title h1, 
				.bsp-la-title h2,
				.bsp-la-title h3,
				.bsp-la-title h4,
				.bsp-la-title h5
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>

/*   2 ----------------------  topic/reply title --------------------------*/
 
	<?php 
		$field= (!empty($bsp_style_settings_la['Topic TitleSize']) ? $bsp_style_settings_la['Topic TitleSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			.bsp-la-reply-topic-title
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php 
		} 
		?>
 
 
	<?php 
		$field= (!empty($bsp_style_settings_la['Topic TitleFont']) ? $bsp_style_settings_la['Topic TitleFont'] : '');
		if (!empty ($field)){
	?>
			.bsp-la-reply-topic-title
				{
					font-family: <?php echo esc_html ($field); ?>;
				}
		<?php 
		}
		?>
 
	<?php 
		$field= (!empty($bsp_style_settings_la['Topic TitleStyle']) ? $bsp_style_settings_la['Topic TitleStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
			?>
				.bsp-la-reply-topic-title
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				.bsp-la-reply-topic-title
				{
					font-weight: bold; 
				}
			<?php 
			}
			else {?>
				.bsp-la-reply-topic-title
				{
				font-weight: normal; 
				}
		<?php
			}
		}
		?>
 
 /*   3 ----------------------  Text font --------------------------*/
 
	<?php 
		$field= (!empty($bsp_style_settings_la['Text fontSize']) ? $bsp_style_settings_la['Text fontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
		?>
			.bsp-la-text
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php 
		} 
		?>
 
	<?php 
		$field= (!empty($bsp_style_settings_la['Text fontColor']) ? $bsp_style_settings_la['Text fontColor'] : '');
		if (!empty ($field)){
	?>
			.bsp-la-text
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($bsp_style_settings_la['Text fontFont']) ? $bsp_style_settings_la['Text fontFont'] : '');
		if (!empty ($field)){
	?>
			.bsp-la-text
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($bsp_style_settings_la['Text fontStyle']) ? $bsp_style_settings_la['Text fontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
			?>
				.bsp-la-text
				{
				font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				.bsp-la-text
				{
				font-weight: bold; 
				}
			<?php 
			}
			else {?>
				.bsp-la-text
				{
				font-weight: normal; 
				}
		<?php
			}
		}
		 ?>
 
/*   4 ----------------------  Topic author Font --------------------------*/
 
	<?php 
		$field= (!empty($bsp_style_settings_la['Topic author FontSize']) ? $bsp_style_settings_la['Topic author FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			.bsp-la-topic-author
			{
			font-size: <?php echo esc_html ($field); ?>;
			}
		<?php 
		}
		?>
 

	<?php 
		$field= (!empty($bsp_style_settings_la['Topic author FontFont']) ? $bsp_style_settings_la['Topic author FontFont'] : '');
		if (!empty ($field)){
	?>
			.bsp-la-topic-author
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($bsp_style_settings_la['Topic author FontStyle']) ? $bsp_style_settings_la['Topic author FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
			?>
				.bsp-la-topic-author
				{
				font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				.bsp-la-topic-author			 
				{
				font-weight: bold; 
				}
			<?php
			}
			else {?>
				.bsp-la-reply-topic-title
				{
				font-weight: normal; 
				}
		<?php
			}
		}
		?>
 
/*   5 ----------------------  Freshness Font--------------------------*/
 
	<?php 
		$field= (!empty($bsp_style_settings_la['Freshness FontSize']) ? $bsp_style_settings_la['Freshness FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			.bsp-la-freshness		 
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($bsp_style_settings_la['Freshness FontColor']) ? $bsp_style_settings_la['Freshness FontColor'] : '');
		if (!empty ($field)){
	?>
			.bsp-la-freshness		 
			{
			color: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($bsp_style_settings_la['Freshness FontFont']) ? $bsp_style_settings_la['Freshness FontFont'] : '');
		if (!empty ($field)){
	?>
			.bsp-la-freshness		 
			{
			font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($bsp_style_settings_la['Freshness FontStyle']) ? $bsp_style_settings_la['Freshness FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
			?>
				.bsp-la-freshness
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				.bsp-la-text			 
				{
					font-weight: bold; 
				}
			<?php
			}
			else {?>
				.bsp-la-text
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>
 
/*   6 ----------------------  Forum Font --------------------------*/
 
	<?php 
		$field= (!empty($bsp_style_settings_la['Forum FontSize']) ? $bsp_style_settings_la['Forum FontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			.bsp-la-forum-title		 
			{
			font-size: <?php echo esc_html ($field); ?>;
			}	
		<?php 
		}
		?>
 
  
	<?php 
		$field= (!empty($bsp_style_settings_la['Forum FontFont']) ? $bsp_style_settings_la['Forum FontFont'] : '');
		if (!empty ($field)){
		?>
			.bsp-la-forum-title		 
			{
			font-family: <?php echo esc_html ($field); ?>;
			}
		<?php 
		} 
		?>
 
	<?php 
		$field= (!empty($bsp_style_settings_la['Forum FontStyle']) ? $bsp_style_settings_la['Forum FontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
			?>
				.bsp-la-forum-title
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				.bsp-la-forum-title			 
				{
					font-weight: bold; 
				}
			<?php 
			}
			else {?>
				.bsp-la-forum-title
				{
				font-weight: normal; 
				}
		<?php
			}
		}
		?>

/*   7 ----------------------  Topic-reply links --------------------------*/
 
	<?php 
		$field= (!empty($bsp_style_settings_la['Topic-reply linksLink Color']) ? $bsp_style_settings_la['Topic-reply linksLink Color'] : '');
		if (!empty ($field)){
	?>
			a:link.bsp-la-reply-topic-title		 
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($bsp_style_settings_la['Topic-reply linksVisited Color']) ? $bsp_style_settings_la['Topic-reply linksVisited Color'] : '');
		if (!empty ($field)){
	?>
			a:visited.bsp-la-reply-topic-title		 
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php
		} 
		?>
 
	<?php 
		$field= (!empty($bsp_style_settings_la['Topic-reply linksHover Color']) ? $bsp_style_settings_la['Topic-reply linksHover Color'] : '');
		if (!empty ($field)){
	?>
			a:hover.bsp-la-reply-topic-title		 
			{
			color: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
/* ********_________________FORUM DISPLAY___________________________________________*/ 
 
/* ----------------------  Move breadcrumb --------------------------*/
	<?php 
		$field = (!empty($datafd['move_subscribe']) ? $datafd['move_subscribe'] : '');
		if (!empty ($field)){
	?>
			.subscription-toggle
			{	
				float:right;
			}
		<?php 
		} 
		?>
 
 
/* ----------------------  forum description styling --------------------------*/
 
		#bbpress-forums div.bsp-forum-content
		{
		clear:both;
		margin-left: 0px;
		padding: 0 0 0 0;
		}
	
/* ----------------------  Rounded corners --------------------------*/
 
	<?php 
		$field = (!empty($datafd['rounded_corners'] ) ? $datafd['rounded_corners']  : '');
		if (!empty ($field)){
	?>	
			.bbp-forums , .bbp-topics  , .bbp-replies
			{			
				border-top-left-radius: 10px;
				border-top-right-radius: 10px;
				border-bottom-left-radius: 10px;
				border-bottom-right-radius: 10px;
			}
			
		<?php
		}
		?>

/*----------------------  thumbnails on forum lists --------------------------*/
 
	<?php 
		$field = (!empty($datafd['thumbnail'] ) ? $datafd['thumbnail']  : '');
		if (!empty ($field)){
	?>	
			.bsp_thumbnail
			{
				display: flex;
				align-items: flex-start;
			}	

			.bsp_thumbnail a
			{
				padding-left: 10px;
			}
			
		<?php
		}
		?>
/*----------------------------------------- ROLES--------------------------------------------------------------------*/

	<?php 
	$roles = bbp_get_dynamic_roles ();

	foreach ( $roles as $key=>$name ){
		$role = $key;
	
		//do all the font stuff as it doesn't matter if needed or not
		$field= (!empty($data4[ $role.'font_size']) ? $data4[ $role.'font_size'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
			echo '.bsp-author-'.$role;
			?> 
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php 
		}
		?>
	 
	<?php 
		$field= (!empty($data4[$role.'font_color']) ? $data4[$role.'font_color'] : '');
		if (!empty ($field)){
		echo '.bsp-author-'.$role;
	?>
		{
			color: <?php echo esc_html ($field); ?>;
		}
	<?php
	} 
	?>
	 
	<?php 
		$field= (!empty($data4[$role.'font']) ? $data4[$role.'font'] : '');
		if (!empty ($field)){
		echo '.bsp-author-'.$role;
	?>
		{
			font-family: <?php echo esc_html ($field); ?>;
		}
	<?php } ?>
	 
	<?php 
		$field= (!empty($data4[$role.'font_style']) ? $data4[$role.'font_style'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
				echo '.bsp-author-'.esc_html($role);
	?>
				{
					font-style: italic; 
				}
		<?php
		} 

			if (strpos($field,'Bold') !== false){
				echo '.bsp-author-'.esc_html($role);
		?>
				{
					font-weight: bold; 
				}
			<?php
			}
			else { 
				echo '.bsp-author-'.esc_html($role);
			?>
				{
					font-weight: normal; 
				}
	 
			<?php
			} //end of else
	 
		} // end of font style
	
	/*  styling for displaying forum roles above user display and on left */

		$field = (!empty($bsp_roles['all_roleswhere_to_display'] ) ? $bsp_roles['all_roleswhere_to_display'] : '');	
		$field2 = (!empty($bsp_roles['all_rolesbefore_username_left'] ) ? $bsp_roles['all_rolesbefore_username_left'] : '');	
		if ($field == 2 && $field2 == 1){
			echo '.bsp-author-'.esc_html($role);
			?>
			{
				float: left;
				padding: 0 8px;
			}
		<?php
		}
	
		//now see if we need to add styling for role type
		$roletype = $role.'type';
		$roletype =  (!empty($bsp_roles[$roletype]) ? $bsp_roles[$roletype] : '2');
		//if type 1 - then just image so no css needed
		//if type 2 or 4, we need to add background color
		if (($roletype == 2) || ($roletype == 4)){
			//add background color if specified 
			$background = $role.'background_color';
			$background=  (!empty($bsp_roles[$background]) ? $bsp_roles[$background] : '');
				if (!empty ($background)){
					echo '.bsp-author-'.esc_html($role);
		?>
					{
						background-color: <?php echo $background; ?>; 
					}
		<?php 
				} 		
		} //end of roletype 2
		
		//if type 3 then add image as background 
		if ($roletype == 3){
			$background = $role.'image';
			$background=  (!empty($bsp_roles[$background]) ? $bsp_roles[$background] : '');
			$image_height = (!empty($bsp_roles[$role.'image_height']) ? $bsp_roles[$role.'image_height'] : '');
			$image_width = (!empty($bsp_roles[$role.'image_width']) ? $bsp_roles[$role.'image_width'] : '');
			$padding = (!empty($image_height) ? $image_height/2 : '');
			echo '.bsp-author-'.esc_html($role);
		?>			
			{
				background-image: url( <?php echo esc_html($background); ?> );
				background-repeat: no-repeat;
				height : <?php echo esc_html($image_height); ?>;
				width : <?php echo esc_html($image_width); ?>;
				text-align : center;
				padding-top : <?php echo esc_html ($padding); ?>px;
			}
		
		<?php		
		} //end of roletype 3
	} //end of foreach role

	// now do topic author
	$role = 'topic_author';
	
	//do all the font stuff as it doesn't matter if needed or not
		$field= (!empty($data4[ $role.'font_size']) ? $data4[ $role.'font_size'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
			echo '.bsp-author-'.esc_html($role);
		?>
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
		 
		<?php 
		$field= (!empty($data4[$role.'font_color']) ? $data4[$role.'font_color'] : '');
		if (!empty ($field)){
			echo '.bsp-author-'.esc_html($role);
		?>
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php 
		} 
		?>
		 
		<?php 
		$field= (!empty($data4[$role.'font']) ? $dataf[$role.'font'] : '');
		if (!empty ($field)){
			echo '.bsp-author-'.esc_html($role);
		?>
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
		 
		<?php 
		$field= (!empty($data4[$role.'font_style']) ? $data4[$role.'font_style'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
				echo '.bsp-author-'.esc_html($role);
		?>
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
				echo '.bsp-author-'.esc_html($role);
		?>
				{
					font-weight: bold; 
				}
			<?php
			}
			else { 
				echo '.bsp-author-'.esc_html($role);
			?>
				{
					font-weight: normal; 
				}
		<?php
			} //end of else
		} // end of font style
	
	//now see if we need to add styling for role type
		$roletype = $role.'type';
		$roletype =  (!empty($bsp_roles[$roletype]) ? $bsp_roles[$roletype] : '2');
		//if type 1 - then just image so no css needed
		//if type 2 or 4, we need to add background color
		if (($roletype == 2) || ($roletype == 4)){
			//add background color if specified 
			$background = $role.'background_color';
			$background=  (!empty($bsp_roles[$background]) ? $bsp_roles[$background] : '');
				if (!empty ($background)){
					echo '.bsp-author-'.esc_html($role);
		?>
					{
						background-color: <?php echo $background; ?>; 
					}
		<?php 
				} 		
		} //end of roletype 2
		
		//if type 3 then add image as background 
		if ($roletype == 3){
			$background = $role.'image';
			$background=  (!empty($bsp_roles[$background]) ? $bsp_roles[$background] : '');
			$image_height = (!empty($bsp_roles[$role.'image_height']) ? $bsp_roles[$role.'image_height'] : '');
			$image_width = (!empty($bsp_roles[$role.'image_width']) ? $bsp_roles[$role.'image_width'] : '');
			$padding = (!empty($image_height) ? $image_height/2 : '');
			echo '.bsp-author-'.esc_html($role);
		?>			
			{
				background-image: url( <?php echo $background; ?> );
				background-repeat: no-repeat;
				height : <?php echo esc_html($image_height); ?>;
				width : <?php echo esc_html($image_width); ?>;
				text-align : center;
				padding-top : <?php echo esc_html ($padding); ?>px;
			}
		
		<?php		
		} //end of roletype 3
		
	/*  styling for displaying forum roles above user display and on left */

	$field = (!empty($bsp_roles['all_roleswhere_to_display'] ) ? $bsp_roles['all_roleswhere_to_display'] : '');	
	$field2 = (!empty($bsp_roles['all_rolesbefore_username_left'] ) ? $bsp_roles['all_rolesbefore_username_left'] : '');	
	if ($field == 2 && $field2 == 1){
		echo '.bsp-author-'.esc_html($role);
		?>
		{
			float: left;
			padding: 0 8px;
		}
	
	<?php
	}

	//now see if we need to add styling for role type
		$roletype = $role.'type';
		$roletype =  (!empty($bsp_roles[$roletype]) ? $bsp_roles[$roletype] : '2');
		//if type 1 - then just image so no css needed
			//if type 2 or 4, we need to add background color
			if (($roletype == 2) || ($roletype == 4)){
				//add background color if specified 
				$background = $role.'background_color';
				$background=  (!empty($bsp_roles[$background]) ? $bsp_roles[$background] : '');
				if (!empty ($background)){
					echo '.bsp-author-'.esc_html($role);
				?>
					{
						background-color: <?php echo $background; ?>; 
					}
				<?php 
				} 		
			} //end of roletype 2
			
			//if type 3 then add image as background 
			if ($roletype == 3){
				$background = $role.'image';
				$background=  (!empty($bsp_roles[$background]) ? $bsp_roles[$background] : '');
				$image_height = (!empty($bsp_roles[$role.'image_height']) ? $bsp_roles[$role.'image_height'] : '');
				$image_width = (!empty($bsp_roles[$role.'image_width']) ? $bsp_roles[$role.'image_width'] : '');
				$padding = (!empty($image_height) ? $image_height/2 : '');
				echo '.bsp-author-'.esc_html($role);
				?>
				{
					background-image: url( <?php echo $background; ?> );
					background-repeat: no-repeat;
					height : <?php echo esc_html($image_height); ?>;
					width : <?php echo esc_html($image_width); ?>;
					text-align : center;
					padding-top : <?php echo esc_html ($padding); ?>px;
				}
			<?php		
			} //end of roletype 3
			?>
		
/*----------------------  Create new topic link styling--------------------------*/
/*styles the element if it is set */

			.bsp-new-topic
			{
				text-align: center;
			}


/*----------------------  Create new topic button Button--------------------------*/

	<?php 
		$field = (!empty($databutton['ButtonSize'] ) ? $databutton['ButtonSize']  : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>	
			.bsp_button1
			<?php 
			//add the submit button styling if set to match in topic/reply form item 5
			if (!empty ($dataform['Submittingbutton_styling'])) echo ',#bbp_topic_submit,#bbp_reply_submit' ; ?>
			{
				font-size: <?php echo esc_html ($field); ?>!important;
			}
		<?php
		}
		else {
			?>
			.bsp_button1
			<?php 
			//add the submit button styling if set to match in topic/reply form item 5
			if (!empty ($dataform['Submittingbutton_styling'])) echo ',#bbp_topic_submit,#bbp_reply_submit' ; ?>
			{
				font-size: 10px !important;
				
			}
		<?php
		}
		?>


	<?php 
		$field = (!empty($databutton['ButtonFont'] ) ? $databutton['ButtonFont']  : '');
		if (!empty ($field)){
	?>	
			.bsp_button1
			<?php 
			//add the submit button styling if set to match in topic/reply form item 5
			if (!empty ($dataform['Submittingbutton_styling'])) echo ',#bbp_topic_submit,#bbp_reply_submit' ; ?>
			{
				font-family: <?php echo esc_html ($field); ?>;
			}	 
		<?php
		}
		else { ?>
			.bsp_button1
			<?php 
			//add the submit button styling if set to match in topic/reply form item 5
			if (!empty ($dataform['Submittingbutton_styling'])) echo ',#bbp_topic_submit,#bbp_reply_submit' ; ?>
			{
				font-family: Arial;
			}
		<?php
		}
		?>

	<?php 
		$field = (!empty($databutton['ButtonColor'] ) ? $databutton['ButtonColor']  : '');
		if (!empty ($field)){
	?>	
			.bsp_button1
			<?php 
			//add the submit button styling if set to match in topic/reply form item 5
			if (!empty ($dataform['Submittingbutton_styling'])) echo ',#bbp_topic_submit,#bbp_reply_submit' ; ?>
			<?php if (!empty ($dataform['Submittingbutton_styling'])) echo ',#bbp_topic_submit,#bbp_reply_submit' ; ?>
			{
				color: <?php echo esc_html ($field); ?> !important;
			}	 
		<?php
		}
		else { ?>
			.bsp_button1
			<?php 
			//add the submit button styling if set to match in topic/reply form item 5
			if (!empty ($dataform['Submittingbutton_styling'])) echo ',#bbp_topic_submit,#bbp_reply_submit' ; ?>
			<?php if (!empty ($dataform['Submittingbutton_styling'])) echo ',#bbp_topic_submit,#bbp_reply_submit' ; ?>
			{
				color: #ffffff !important;
			}
		<?php
		}
		?>

	<?php
		//#buddypress input[type="submit"] as possible additional code for buddypress forum users to get background colour right

        $field = (!empty($databutton['Buttonbackground color'] ) ? $databutton['Buttonbackground color']  : '');
		if (!empty ($field)){
	?>	
		.bsp_button1
		<?php 
			//add the submit button styling if set to match in topic/reply form item 5
			if (!empty ($dataform['Submittingbutton_styling'])) echo ',#bbp_topic_submit,#bbp_reply_submit' ; ?>
			{
				background: <?php echo esc_html ($field); ?>;
			}	 
		<?php
		}
		else {
		?>
			.bsp_button1
			<?php 
			//add the submit button styling if set to match in topic/reply form item 5
			if (!empty ($dataform['Submittingbutton_styling'])) echo ',#bbp_topic_submit,#bbp_reply_submit' ; ?>
			{
				background: #3498db;
				background-image: -webkit-linear-gradient(top, #3498db, #2980b9);
				background-image: -moz-linear-gradient(top, #3498db, #2980b9);
				background-image: -ms-linear-gradient(top, #3498db, #2980b9);
				background-image: -o-linear-gradient(top, #3498db, #2980b9);
				background-image: linear-gradient(to bottom, #3498db, #2980b9);
			}
		<?php
		}
		?>

	<?php 
		$field = (!empty($databutton['Buttonhover color'] ) ? $databutton['Buttonhover color']  : '');
		if (!empty ($field)){
	?>	
			.bsp_button1:hover
			<?php 
			//add the submit button styling if set to match in topic/reply form item 5
			if (!empty ($dataform['Submittingbutton_styling'])) echo ',#bbp_topic_submit:hover,#bbp_reply_submit:hover' ; ?>
			{
				background: <?php echo esc_html ($field); ?>;
			}	 
		<?php
		}
		else { ?>
			.bsp_button1:hover
			<?php 
			//add the submit button styling if set to match in topic/reply form item 5
			if (!empty ($dataform['Submittingbutton_styling'])) echo ',#bbp_topic_submit:hover,#bbp_reply_submit:hover' ; ?>
			{
				background: #3cb0fd;
				background-image: -webkit-linear-gradient(top, #3cb0fd, #3498db);
				background-image: -moz-linear-gradient(top, #3cb0fd, #3498db);
				background-image: -ms-linear-gradient(top, #3cb0fd, #3498db);
				background-image: -o-linear-gradient(top, #3cb0fd, #3498db);
				background-image: linear-gradient(to bottom, #3cb0fd, #3498db);
			}
		<?php
		}
		?>

	<?php 
		$field= (!empty($databutton['ButtonFont Style']) ? $databutton['ButtonFont Style'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				.bsp_button1
				<?php 
			//add the submit button styling if set to match in topic/reply form item 5
			if (!empty ($dataform['Submittingbutton_styling'])) echo ',#bbp_topic_submit,#bbp_reply_submit' ; ?>
			 	{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				.bsp_button1
				<?php 
			//add the submit button styling if set to match in topic/reply form item 5
			if (!empty ($dataform['Submittingbutton_styling'])) echo ',#bbp_topic_submit,#bbp_reply_submit' ; ?>
				{
					font-weight: bold; 
				}
			<?php 
			}
			else {?>
				.bsp_button1
				<?php 
			//add the submit button styling if set to match in topic/reply form item 5
			if (!empty ($dataform['Submittingbutton_styling'])) echo ',#bbp_topic_submit,#bbp_reply_submit' ; ?>
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>

		.bsp_button1
		<?php 
			//add the submit button styling if set to match in topic/reply form item 5
			if (!empty ($dataform['Submittingbutton_styling'])) echo ',#bbp_topic_submit,#bbp_reply_submit' ; ?>
		{  
			-webkit-border-radius: 28;
			-moz-border-radius: 28;
			border-radius: 28px;
			padding: 7px 15px 7px 15px;
			text-decoration: none;  
			border : none;
			cursor : pointer;
			line-height : 15px !important;
		}

		.bsp_button1:hover
		{
		   text-decoration: none;
		}

                /* fix for Mark All Topics Read offset */
                div.bsp-center > form > input.bsp_button1 {
                        margin-top: -7px;	
                }
                
		.bsp-center
		{
			width: 100%;
			max-width: 100%;
			float: none;
			text-align: center;
			margin: 10px 0px 10px 0px;
		}
	
		.bsp-one-half
		{
			float: left;
			width: 50%;
			margin-right: 0;
		}

		.bsp-one-third
		{
			width: 33.33%;
			float: left;
			margin-right: 0;
			position: relative;
		}
		
		
		/* stack if on mobile */
		@media only screen and (max-width: 480px) {
			 .bsp-center
			 {
			 clear:both;
			 width: 100%;
			 max-width: 100%;
			 float: left;
			 text-align: left;
			 margin-top: 10px;
			 margin-bottom : 10px;
			 }

			.bsp-one-half
			 {
			 float: left;
			 width: 48%;
			 margin-right: 4%;
			 }

			.bsp-one-third
			 {
			 width: 30.66%;
			 float: left;
			 margin-right: 4%;
			 position: relative;
			 }
		}


/* ******************to get the spinner.gif loaded before submit executes */
		#bsp-spinner-load
		{
			background: url(/wp-admin/images/spinner.gif) no-repeat;
			display : none;
		}

		.bsp-spinner
		{		 
			background: url(/wp-admin/images/spinner.gif) no-repeat;
			-webkit-background-size: 20px 20px;
			background-size: 20px 20px;
			float: right;
			opacity: 0.7;
			filter: alpha(opacity=70);
			width: 20px;
			height: 20px;
			margin: 2px 5px 0;
		}


		#bsp_topic_submit
		{
			display : none;
		}

		#bsp_reply_submit
		{
			display : none;
		}


/* /////////////////////////and support for search spinner*/

		#bsp_search_submit2
		{
			display : none;
		}

		.bsp-search-submitting
		{
			font-size : 16px;
			line-height : 24px;
		
		}
/*********_________________TOPIC PREVIEW___________________________________________*/ 		
		<?php
		if (!empty ($bsp_style_settings_topic_preview['activate'])) {


		/*   topic preview styling--------------------------*/
		?>
		.bsp-preview {
		  position: relative;
		  display: block;
		  }

		.bsp-preview .bsp-previewtext {
			visibility: hidden;
			left : 75px;
			top : -5px;
			border-radius: 6px;
			padding: 5px;
			z-index: 1;
			opacity: 0;
			transition: opacity 0.3s;
			position: absolute;
		  }

		.bsp-preview:hover .bsp-previewtext  {
		  visibility: visible;
		  opacity: 1;
		} 
		
		.bsp-preview .bsp-previewtextm {
				  visibility: hidden;
				  left : 75px;
				  top : -5px;
				  border-radius: 6px;
				  padding: 5px;
				  z-index: 1;
				  opacity: 0;
				  transition: opacity 0.3s;
				   position: absolute;
				 }

		.bsp-preview .bsp-previewtext::after, .bsp-preview .bsp-previewtextm::after {
				  content: " ";
				  position: absolute;
				  top: 15px;
					right: 100%; /* To the left of the tooltip */
				  margin-top: -5px;
				  border-width: 5px;
				  border-style: solid;
				  border-color: transparent black transparent transparent;
		}
		
		.main .bbp-topic-title { display: flex; }
		.sticky li.bbp-topic-title::before, .super-sticky li.bbp-topic-title::before, .status-closed li.bbp-topic-title::before { order: 2; }
		.bbpresss_unread_posts_icon { order: 1; }
		.bsp-preview { order: 3; flex-grow: 2; }
		.bsp-preview:hover { position: relative; }
		#bbpress-forums li.bbp-body ul.forum, #bbpress-forums li.bbp-body ul.topic { overflow: visible; }
		#bbpress-forums li.bbp-body ul.forum:after, #bbpress-forums li.bbp-body ul.topic:after { content:''; display:block; clear: both; }
		.bbpresss_unread_posts_icon { flex-shrink: 0; }
		
		<?php

		$field = (!empty($bsp_style_settings_topic_preview['previewwidth']) ? $bsp_style_settings_topic_preview['previewwidth']  : '400px');
		if (is_numeric($field)) $field=$field.'px';
	?>	
			.bsp-preview .bsp-previewtext
			{
				width: <?php echo esc_html ($field); ?>;
			}
			<?php

$field = (!empty($bsp_style_settings_topic_preview['previewheight']) ? $bsp_style_settings_topic_preview['previewheight']  : '');
		if (is_numeric($field)) $field=$field.'px';
	?>	
			.bsp-preview .bsp-previewtext {
			
				height: <?php echo esc_html ($field); ?>;
			}
<?php
$field = (!empty($bsp_style_settings_topic_preview['previewSize']) ? $bsp_style_settings_topic_preview['previewSize']  : '10px');
		if (is_numeric($field)) $field=$field.'px';
	?>	
			.bsp-preview .bsp-previewtext, .bsp-preview .bsp-previewtextm
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		
	<?php 
		$field = (!empty($bsp_style_settings_topic_preview['previewFont'] ) ? $bsp_style_settings_topic_preview['previewFont']  : '');
		?>
		.bsp-preview .bsp-previewtext, .bsp-preview .bsp-previewtextm
			{
				font-family: <?php echo esc_html ($field); ?>;
			}	 
		
	<?php 
		$field = (!empty($bsp_style_settings_topic_preview['previewColor'] ) ? $bsp_style_settings_topic_preview['previewColor']  : '#fff');
		?>
			.bsp-preview .bsp-previewtext, .bsp-preview .bsp-previewtextm
			{
				color: <?php echo esc_html ($field); ?>;
			}	
			
			
	<?php 
		$field = (!empty($bsp_style_settings_topic_preview['previewbackground color'] ) ? $bsp_style_settings_topic_preview['previewbackground color']  : '#000');
		?>
			.bsp-preview .bsp-previewtext, .bsp-preview .bsp-previewtextm
			{
				background-color: <?php echo esc_html ($field); ?>;
			}	 
			.bsp-preview .bsp-previewtext::after, .bsp-preview .bsp-previewtextm::after 
			{
				   border-color: transparent <?php echo esc_html ($field); ?> transparent transparent;
			}			
		
<?php 
	$field= (!empty($bsp_style_settings_topic_preview['previewFontStyle']) ? $bsp_style_settings_topic_preview['previewFontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
				
		?>
				.bsp-preview .bsp-previewtext, .bsp-preview .bsp-previewtextm
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				.bsp-preview .bsp-previewtext, .bsp-preview .bsp-previewtextm
				{
					font-weight: bold; 
				}
			<?php
			}
			else { 
			?>
				.bsp-preview .bsp-previewtext, .bsp-preview .bsp-previewtextm
				{
					font-weight: normal; 
				}
		<?php
			} //end of else
		} // end of font style

	if (!empty ($bsp_style_settings_topic_preview['previewmscreen'])) {
		$mscreen =$bsp_style_settings_topic_preview['previewmscreen'];
		if (is_numeric($mscreen)) $mscreen.='px';
		
	?>
		@media (max-width: <?php echo $mscreen; ?> ) 
		{
		.bsp-preview:hover .bsp-previewtextm  {
		  visibility: visible;
		  opacity: 1;
		} 
		
		.bsp-preview:hover .bsp-previewtext  {
		visibility: hidden;
		} 
		
					<?php

			$field = (!empty($bsp_style_settings_topic_preview['previewmwidth']) ? $bsp_style_settings_topic_preview['previewmwidth']  : '400px');
			if (is_numeric($field)) $field=$field.'px';
			?>	
				.bsp-preview .bsp-previewtextm
				{
					width: <?php echo esc_html ($field); ?>;
				}
				<?php

			$field = (!empty($bsp_style_settings_topic_preview['previewmheight']) ? $bsp_style_settings_topic_preview['previewmheight']  : '');
			if (is_numeric($field)) $field=$field.'px';
			?>	
				.bsp-preview .bsp-previewtextm
				{
					height: <?php echo esc_html ($field); ?>;
				}
		
		}
	<?php 
	}
}
?>



		
/*   search styling--------------------------*/

			/*search content */

	<?php
		$field= (!empty($bsp_style_settings_search['search_contentbackground_color']) ? $bsp_style_settings_search['search_contentbackground_color'] : '');
		if (!empty ($field)){
	?>
			#bbp_search 
			{			
			background-color: <?php echo esc_html ($field); ?> !important;
			}	
		<?php
		}
		?>
		
		<?php
		$field= (!empty($bsp_style_settings_search['search_contentline_height']) ? $bsp_style_settings_search['search_contentline_height'] : '');
		if (!empty ($field)){
			
			if (is_numeric($field)) $field=$field.'px';
	?>	
	
			#bbp_search 
			{			
			line-height: <?php echo esc_html ($field); ?> !important;
			}	
		<?php
		}
		?>

		<?php 
		$field = (!empty($bsp_style_settings_search['search_content_textSize'] ) ? $bsp_style_settings_search['search_content_textSize']  : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>	
			#bbp_search 
			{
				font-size: <?php echo esc_html ($field); ?> !important;
			}	 
		<?php
		}
		
		?>


	<?php 
		$field = (!empty($bsp_style_settings_search['search_content_textFont'] ) ? $bsp_style_settings_search['search_content_textFont']  : '');
		if (!empty ($field)){
	?>	
			#bbp_search 
			{
				font-family: <?php echo esc_html ($field); ?> !important;
			}	 
		<?php
		}
		?>
		
		

	<?php 
		$field = (!empty($bsp_style_settings_search['search_content_textColor'] ) ? $bsp_style_settings_search['search_content_textColor']  : '');
		if (!empty ($field)){
	?>	
			#bbp_search 
			{
				color: <?php echo esc_html ($field); ?> !important;
			}	 
		<?php
		}
		
		?>
		
		
	<?php 
		$field= (!empty($bsp_style_settings_search['search_content_textStyle']) ? $bsp_style_settings_search['search_content_textStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbp_search 
				{
					font-style: italic !important; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
		?>
				#bbp_search 
				{
					font-weight: bold !important; 
				}
			<?php
			}
			else {?>
				#bbp_search 
				{
					font-weight: normal !important; 
				}
			<?php
			}
		}
		?>
		
		/*search box */
	
		<?php
		$field= (!empty($bsp_style_settings_search['search_boxbackground_color']) ? $bsp_style_settings_search['search_boxbackground_color'] : '');
		if (!empty ($field)){
	?>
			#bbp_search_submit, #bsp_search_submit1, #bsp_search_submit2
			{			
			background-color: <?php echo esc_html ($field); ?> !important;
			}	
		<?php
		}
		?>
		
		<?php
		$field= (!empty($bsp_style_settings_search['search_boxline_height']) ? $bsp_style_settings_search['search_boxline_height'] : '');
		if (!empty ($field)){
			
			if (is_numeric($field)) $field=$field.'px';
	?>	
	
			#bbp_search_submit, #bsp_search_submit1, #bsp_search_submit2
			{			
			line-height: <?php echo esc_html ($field); ?> !important;
			}	
		<?php
		}
		?>

		<?php 
		$field = (!empty($bsp_style_settings_search['search_box_textSize'] ) ? $bsp_style_settings_search['search_box_textSize']  : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>	
			#bbp_search_submit, #bsp_search_submit1, #bsp_search_submit2
			{
				font-size: <?php echo esc_html ($field); ?> !important;
			}	 
		<?php
		}
		
		?>


	<?php 
		$field = (!empty($bsp_style_settings_search['search_box_textFont'] ) ? $bsp_style_settings_search['search_box_textFont']  : '');
		if (!empty ($field)){
	?>	
			#bbp_search_submit, #bsp_search_submit1, #bsp_search_submit2
			{
				font-family: <?php echo esc_html ($field); ?> !important;
			}	 
		<?php
		}
		?>
		
		

	<?php 
		$field = (!empty($bsp_style_settings_search['search_box_textColor'] ) ? $bsp_style_settings_search['search_box_textColor']  : '');
		if (!empty ($field)){
	?>	
			#bbp_search_submit, #bsp_search_submit1, #bsp_search_submit2
			{
				color: <?php echo esc_html ($field); ?> !important;
			}	 
		<?php
		}
		
		?>
		
		
	<?php 
		$field= (!empty($bsp_style_settings_search['search_box_textStyle']) ? $bsp_style_settings_search['search_box_textStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbp_search_submit, #bsp_search_submit1, #bsp_search_submit2
				{
					font-style: italic !important; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
		?>
				#bbp_search_submit, #bsp_search_submit1, #bsp_search_submit2
				{
					font-weight: bold !important; 
				}
			<?php
			}
			else {?>
				#bbp_search_submit, #bsp_search_submit1, #bsp_search_submit2
				{
					font-weight: normal !important; 
				}
			<?php
			}
		}
		?>
		
		
/*----------------------  pin for stickies-----------------------------------------------------------------------------------------------------*/

	<?php 
	if (!empty ($datati['Sticky PinActivate'])){
	?>
			#bbpress-forums ul.sticky li.bbp-topic-title a.bbp-topic-permalink::before, #bbpress-forums ul.super-sticky li.bbp-topic-title a.bbp-topic-permalink::before
			{
				float: left;
				margin-right: 5px;
				padding-top: 3px;
				font-family: dashicons;
				content: "\f109";
			}

	<?php

		$field= (!empty($datati['Sticky PinFontSize']) ? $datati['Sticky PinFontSize'] : '12');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums ul.sticky li.bbp-topic-title a.bbp-topic-permalink::before, #bbpress-forums ul.super-sticky li.bbp-topic-title a.bbp-topic-permalink::before
			{
				font-size: <?php echo esc_html ($field); ?>;
			}	

		<?php
		}

		$field= (!empty($datati['Sticky PinColor']) ? $datati['Sticky PinColor'] : '#ffb900');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
		?>
				#bbpress-forums ul.sticky li.bbp-topic-title a.bbp-topic-permalink::before, #bbpress-forums ul.super-sticky li.bbp-topic-title a.bbp-topic-permalink::before
				{
					color: <?php echo esc_html ($field); ?>;
				}	

		<?php
		}
	} //end of Sticky PinActivate
?>

/*----------------------  Breadcrumb home icon-----------------------------------------------------------------------------------------------------*/

		.bsp-home-icon::before
		{
			content: "";
			display: inline-block;
			font-family: dashicons;
			vertical-align: middle;
		}		 
	
	<?php
		$field= (!empty($bsp_breadcrumb['Home_IconSize']) ? $bsp_breadcrumb['Home_IconSize'] : '12');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			.bsp-home-icon::before
			{
			font-size: <?php echo esc_html ($field); ?>;
			}	
		<?php
		}
		?>

	<?php
		$field= (!empty($bsp_breadcrumb['Home_IconColor']) ? $bsp_breadcrumb['Home_IconColor'] : '');
		if (!empty ($field)){
		?>
			.bsp-home-icon::before
			{
				color: <?php echo esc_html ($field); ?>;
			}	 

		<?php
		}
		?>
		
/*----------------------  login failures ID-----------------------------------------------------------------------------------------------------*/
		
	<?php 
		$field= (!empty($bsp_login_fail['failSize']) ? $bsp_login_fail['failSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bsp-login-error
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		 
		<?php 
		}
		?>
 
	<?php 
	$field= (!empty($bsp_login_fail['failColor']) ? $bsp_login_fail['failColor'] : '');
	if (!empty ($field)){
	?>
		#bsp-login-error
		{
			color: <?php echo esc_html ($field); ?>;
		}
	 
		<?php
	} 
	?>
 
	<?php 
		$field= (!empty($bsp_login_fail['failFont']) ? $bsp_login_fail['failFont'] : '');
		if (!empty ($field)){
	?>
	 
			#bsp-login-error
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
	 
		<?php
		} 
		?>
		
	<?php 
		$field= (!empty($bsp_login_fail['failStyle']) ? $bsp_login_fail['failStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>

				#bsp-login-error
				{
					font-style: italic; 
				}
	 
		<?php
		} 

		if (strpos($field,'Bold') !== false){
		?>
			#bsp-login-error
			{
				font-weight: bold; 
			}
	 
		<?php
		}
		else { ?>
			#bsp-login-error
			{
				font-weight: normal; 
			}
	 
			 
		<?php
		} // end of else
	 
	}
	?>

		

/*----------------------  topic lock icon-----------------------------------------------------------------------------------------------------*/

	<?php
	if (!empty($bsp_style_settings_ti['Lock IconActivate']) ){
	?>

		#bbpress-forums ul.status-closed li.bbp-topic-title a.bbp-topic-permalink::before
		{
			content: "\f160";
			display: inline-block;
			font-family: dashicons;
			vertical-align: middle;
		}	

	<?php	 
		$field= (!empty($bsp_style_settings_ti['Lock IconSize']) ? $bsp_style_settings_ti['Lock IconSize'] : '12');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums ul.status-closed li.bbp-topic-title a.bbp-topic-permalink::before
			{
			font-size: <?php echo esc_html ($field); ?>;
			}	
		<?php
		}
		?>

	<?php
		$field= (!empty($bsp_style_settings_ti['Lock IconColor']) ? $bsp_style_settings_ti['Lock IconColor'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums ul.status-closed li.bbp-topic-title a.bbp-topic-permalink::before
			{
				color: <?php echo esc_html ($field); ?>;
			}	 
	<?php
		}
	}  // end of Lock IconActivate
	?>
	
	
			.bbpresss_unread_posts_icon{
				float:left;
				margin-right:6px;
				max-width: 45px;
			}

			.bbpresss_unread_posts_icon a img{
				margin-top:2px;
				-webkit-box-shadow:none;
				-moz-box-shadow:none;
				box-shadow:none;
			}

			.markedUnread{
				float: right;
			}

			.bbpress_mark_all_read_wrapper{
				transform: scale(0.8);
				transform-origin: right;
			}

			.bbpress_mark_all_read{
				display:inline-block;
				margin-right:5px;
				width: 100%;
			}

			.bbpress_mark_all_read input{
				float:right;
			}

			.bbpress_mark_all_read input[type="submit"]{
				margin:0px;
			}


			.bbpresss_unread_posts_amount{
				float: right;
				font-size: 9px;
			}

			#bsp_unread_optinout {
				width : 10% !important;
			}

			.show-iconf::before {
					 font-family: 'dashicons';
				content : "\f449";
			}

			.show-iconr::before {
					 font-family: 'dashicons';
				content : "\f451";
			}

			.show-icont::before {
					 font-family: 'dashicons';
				content : "\f450";
			}

			.show-iconv::before {
					 font-family: 'dashicons';
				content : "\f307";
			}

			.show-iconlr::before {
					 font-family: 'dashicons';
				content : "\f338";
			}

			.show-iconla::before {
					 font-family: 'dashicons';
				content : "\f469";
			}

			.show-iconfa::before {
					 font-family: 'dashicons';
				content : "\f147";
			}

			.show-iconsu::before {
					 font-family: 'dashicons';
				content : "\f155";
			}



			ul.bsp-st-info-list li.topic-subscribe a.subscription-toggle {
			float : none;
			} 

			.hide-list-style {
				list-style: none !important;
				margin-left : 0 !important;
			}
			
	/*----------------------  prevent hide/cancel being displayed in profile following 5.6 release--------------------------*/
		
			#bbpress-forums #bbp-your-profile fieldset fieldset.password {
		display: none;
}


/*----------------------  Quotes Styling --------------------------*/

<?php
if (!empty ($bsp_style_settings_quote['quote_activate'])) {
		?>

	blockquote  {
		padding: 30px 20px 30px 20px !important;
		margin: 0 0 15px 0!important;
		quotes: none !important;
	}

	<?php

	$field= (!empty($bsp_style_settings_quote['Quote_background_color']) ? $bsp_style_settings_quote['Quote_background_color'] : '#eeeeee52');
			if (!empty ($field)){
				?>
	blockquote  {
		background-color: <?php echo esc_html ($field); ?> !important;
	}

	<?php
	}

	$field= (!empty($bsp_style_settings_quote['Quote_border_color']) ? $bsp_style_settings_quote['Quote_border_color'] : '#cccccc9e');
			if (!empty ($field)){
				?>
	blockquote {
			 border-left: 4px solid <?php echo esc_html ($field); ?> !important;
	}
	<?php
			}
	?>

	blockquote:before {
		content: none !important;
		line-height: 0em !important;
		margin-right: 15px !important;
		vertical-align: -0.5em !important;
		color: #ccc !important;
		
	}

	blockquote p {
		padding: 0 !important;
		margin: 0 !important;
	}

	.bsp-quote-title {
		margin-bottom: 15px;
	}
	

	/* ----------------------  Font - quote headings --------------------------*/
	 
		<?php 
			$field= (!empty($bsp_style_settings_quote['QuoteSize']) ? $bsp_style_settings_quote['QuoteSize'] : '');
			if (!empty ($field)){
				if (is_numeric($field)) $field=$field.'px';
		?>
				.bsp-quote-title
				{
					font-size: <?php echo esc_html ($field); ?>;
				}
			 
			<?php 
			}
			?>
	 
		<?php 
		$field= (!empty($bsp_style_settings_quote['QuoteColor']) ? $bsp_style_settings_quote['QuoteColor'] : '');
		if (!empty ($field)){
		?>
			.bsp-quote-title
			{
				color: <?php echo esc_html ($field); ?>;
			}
		 
		<?php
		} 
		?>
	 
		<?php 
			$field= (!empty($bsp_style_settings_quote['QuoteFont']) ? $bsp_style_settings_quote['QuoteFont'] : '');
			if (!empty ($field)){
		?>
				.bsp-quote-title
				{
					font-family: <?php echo esc_html ($field); ?>;
				}
		 
		<?php
			} 
			?>
			
		<?php 
			$field= (!empty($bsp_style_settings_quote['QuoteStyle']) ? $bsp_style_settings_quote['QuoteStyle'] : '');
			if (!empty ($field)){
				if (strpos($field,'Italic') !== false){
		?>
					.bsp-quote-title,bsp-quote-title a
					{
						font-style: italic; 
					}
		 
		<?php
			} 
			if (strpos($field,'Bold') !== false){
			?>
				.bsp-quote-title,bsp-quote-title a
				{
					font-weight: bold; 
				}
		 
			<?php
			}
			else { ?>
				.bsp-quote-title, bsp-quote-title a
				{
					font-weight: normal; 
				}
		 
		<?php
			} // end of else
				
		}
}
?>
/*----------------------  mod tools--------------------------*/
<?php
if (!empty($bsp_style_settings_modtools['modtools_activate'])) {
	?>
	
	.bbp-mt-template-notice {
	border-width: 1px;
	border-style: solid;
	padding: 1em 0.6em;
	margin: 5px 0 15px;
	background-color: #ffffe0;
	border-color: #e6db55;
	color: #000;
	text-align: center;
	}

	#bbpress-forums li.bbp-body ul.topic.status-pending {
		background-color: #ffffe0;
	}

	.bbp-report-type {
		margin: 6px 0 0 5px;
		display: none;
		color: #a00;
	}

	.bbp-report-select {
		font-size: 12px;
	}

	span.bbp-admin-links .bbp-report-linked-reported {
		color: #a00;
		text-decoration: none;
		box-shadow: none;
		cursor: default;
	}
	
	
	
	<?php
}

?>

/*----------------------  theme support--------------------------*/

<?php
global $bsp_theme_check;

if (!empty ($bsp_style_settings_theme_support['fse'])  && $bsp_theme_check == 'block_theme') {
	$field= (!empty($bsp_style_settings_theme_support['fse_width']) ? $bsp_style_settings_theme_support['fse_width'] : '75%');
	
	if (is_numeric($field)) $field=$field.'%';
	?>
			.bsp-fse-container
				{
					width: <?php echo esc_html ($field); ?>;
					margin : 0 auto;
				}
<?php			
}
?>


/*    ----------------------  BLOCK Widget title --------------------------*/
 
	<?php 
		$field= (!empty($bsp_style_settings_block_widgets['Widget TitleSize']) ? $bsp_style_settings_block_widgets['Widget TitleSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
				.bsp-widget-title
				{
					font-size: <?php echo esc_html ($field); ?>;
				}
		<?php 
		} 
		?>
 
	<?php   
                $field= (!empty($bsp_style_settings_block_widgets['Widget TitleColor']) ? $bsp_style_settings_block_widgets['Widget TitleColor'] : '');
		if (!empty ($field)){
	?>
			.bsp-widget-title
			{
				color: <?php echo esc_html ($field); ?>;
			}
		<?php 
		}
		?>
		 
	<?php 
		$field= (!empty($bsp_style_settings_block_widgets['Widget TitleFont']) ? $bsp_style_settings_block_widgets['Widget TitleFont'] : '');
		if (!empty ($field)){
	?>
			.bsp-widget-title
			{
			font-family: <?php echo esc_html ($field); ?>;
			}
		<?php
		}
		?>
 
	<?php 
		$field= (!empty($bsp_style_settings_block_widgets['Widget TitleStyle']) ? $bsp_style_settings_block_widgets['Widget TitleStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
			?>
				.bsp-widget-title
				{
					font-style: italic; 
				}
			<?php
			} 

			if (strpos($field,'Bold') !== false){
			?>
				.bsp-widget-title
				{
					font-weight: bold; 
				}
			<?php
			}
			else {?>
				.bsp-widget-title
				{
					font-weight: normal; 
				}
		<?php
			}
		}
		?>


/*----------------------  change avatar display - makes sure username displays correctly for profiles tabs settings --------------------------*/

#bbpress-forums div.bbp-reply-author img.avatar {
    clear: left;
    display: block;
}


/*----------------------  button fixes to work with default bbPress buttons and override theme values that make buttons look wrong --------------------------*/

/* override bbpress floating subscription link to the right */
#bbpress-forums div.bsp-center #subscription-toggle {
    float: none;
}

/* override any theme margins for generic input css so the mark as read button alignment matches */
input.bsp_button1 {
    margin: 0px;
}

<?php 
$field = (!empty($datat['topic_button_type']) ? $datat['topic_button_type'] : '');
if (!empty ($field)){
        if ( $field == 2 ){
?>
                /* override the top margin for the favorite/subscribe links for TOPIC favorite/subscribe buttons so the top portion doesn't get cut off, and vertically aligns them within the breadcrumb line height and button separator */
                div#bbpress-forums > span#favorite-toggle, div#bbpress-forums > span#subscription-toggle {
                        margin-top: 6px;
                }
                /* make sure this top margin is not added to the forum-level subscribe button. */
                div.bsp-center.bsp-one-third > span#subscription-toggle {
                        margin-top: unset;
                }
<?php
        }
}
?>

/*----------------------  block widget titles--------------------------*/
.bsp-widget-heading {
	color: #222;
	font-weight: bold;
}

ul.bsp-widget-settings{
	font-size : 12px !important;
}

/*----------------------  forums list widget styling--------------------------*/
.bsp-forum-topic-count {
	text-align: right ;
}

ul.bsp-forums-widget{
	list-style : none !important ;
	
}

.bsp-widget-display-forums {
	list-style: none;
}

.bsp-forum-info {
	background: #eaeaea;
}

<?php
if (!empty( $bsp_style_settings_ti['topic_icons']))  {
?>
/*topic titles*/
.bbpress .forum-titles {
	overflow: hidden;
}
.bbpress .forum-titles .bbp-topic-voice-count::before,
.bbpress .forum-titles .bbp-topic-reply-count::before {
	font: 400 16px/1 dashicons;
	margin-right: 100px;
	-moz-osx-font-smoothing: grayscale;
	-webkit-font-smoothing: antialiased;
}

	.bbpress .forum-titles .bbp-topic-voice-count::before,
	.bbpress .forum-titles .bbp-topic-reply-count::before {
		font: 400 21px/1 dashicons;
		margin-left: 20px;
	}

<?php
$content = (!empty($bsp_style_settings_ti['topic_topics']) ? $bsp_style_settings_ti['topic_topics'] : 'f307');
$content = '"'.'\\'.$content.'"' ;
?>

.bbpress .forum-titles .bbp-topic-voice-count::before {
	content: <?php echo $content; ?> ;
}
<?php
$content = (!empty($bsp_style_settings_ti['topic_posts']) ? $bsp_style_settings_ti['topic_posts'] : 'f125');
$content = '"'.'\\'.$content.'"' ;
?>
.bbpress .forum-titles .bbp-topic-reply-count::before {
	content: <?php echo $content; ?> ;
}

}

<?php
}

global $bsp_style_settings_f ;
if (!empty( $bsp_style_settings_f['forum_icons']))  {
?>
/*topic titles*/
.bbpress .forum-titles {
	overflow: hidden;
}
.bbpress .forum-titles .bbp-forum-topic-count::before,
.bbpress .forum-titles .bbp-forum-reply-count::before {
	font: 400 16px/1 dashicons;
	margin-right: 100px;
	-moz-osx-font-smoothing: grayscale;
	-webkit-font-smoothing: antialiased;
}

	.bbpress .forum-titles .bbp-forum-topic-count::before,
	.bbpress .forum-titles .bbp-forum-reply-count::before {
		font: 400 21px/1 dashicons;
		margin-left: 20px;
	}


<?php
$content = (!empty($bsp_style_settings_f['forum_topics']) ? $bsp_style_settings_f['forum_topics'] : 'f325');
$content = '"'.'\\'.$content.'"' ;
?>
.bbpress .forum-titles .bbp-forum-topic-count::before {
	content: <?php echo ($content) ; ?> ;
}
<?php
$content = (!empty($bsp_style_settings_f['forum_posts']) ? $bsp_style_settings_f['forum_posts'] : 'f125');
$content = '"'.'\\'.$content.'"' ;
?>
.bbpress .forum-titles .bbp-forum-reply-count::before {
	content: <?php echo ($content); ?> ;
}



<?php
}
?>
/*----------------------  Column display--------------------------*/


<?php
if (!empty ($bsp_style_settings_column_display['forum_activate'])) {
	
	//forum name column width
		$forum_name_width_mobile = (!empty( $bsp_style_settings_column_display['forum_name_width_mobile'] ) ?  $bsp_style_settings_column_display['forum_name_width_mobile'] : 55); 
		$forum_name_width_mobile = str_replace ('%' , '' ,$forum_name_width_mobile ) ;
		$forum_name_width_mobile = str_replace ('px' , '' ,$forum_name_width_mobile ) ;
		$forum_name_width = (!empty( $bsp_style_settings_column_display['forum_name_width'] ) ?  $bsp_style_settings_column_display['forum_name_width'] : 55); 
		$forum_name_width = str_replace ('%' , '' ,$forum_name_width ) ;
		$forum_name_width = str_replace ('px' , '' ,$forum_name_width ) ;
		//do widths in all cases
		?>
			li.bbp-forum-info {
				width : <?php echo esc_html($forum_name_width) ;?>%; 
			}
			@media only screen and (max-width: 480px) {
				li.bbp-forum-info {
					width : <?php echo esc_html($forum_name_width_mobile) ;?>%
				}  
			}
	<?php
	
	
	//topics column
		$forum_topics_width_mobile = (!empty( $bsp_style_settings_column_display['forum_topics_width_mobile'] ) ?  $bsp_style_settings_column_display['forum_topics_width_mobile'] : 15); 
		$forum_topics_width_mobile = str_replace ('%' , '' ,$forum_topics_width_mobile ) ;
		$forum_topics_width_mobile = str_replace ('px' , '' ,$forum_topics_width_mobile ) ;
		$forum_topics_width = (!empty( $bsp_style_settings_column_display['forum_topics_width'] ) ?  $bsp_style_settings_column_display['forum_topics_width'] : 15); 
		$forum_topics_width = str_replace ('%' , '' ,$forum_topics_width ) ;
		$forum_topics_width = str_replace ('px' , '' ,$forum_topics_width ) ;
	
		//do widths in all cases
		?>
			li.bbp-forum-topic-count {
				width : <?php echo esc_html($forum_topics_width) ; ?> %; 
			}
			@media only screen and (max-width: 480px) {
				li.bbp-forum-topic-count {
					width : <?php echo esc_html($forum_topics_width_mobile) ; ?>%
				}  
			}
		<?php	
		//hide all
		if ($bsp_style_settings_column_display['forum_topics'] == 1) { ?>
			li.bbp-forum-topic-count {
				display : none ;
			}
			<?php
		}
		//hide mobile only
		if ($bsp_style_settings_column_display['forum_topics'] == 2) { ?>
		@media only screen and (max-width: 480px) {
			li.bbp-forum-topic-count {
				display : none ;
			}
		}
		<?php
		}
		
		//posts column
		$forum_posts_width_mobile = (!empty( $bsp_style_settings_column_display['forum_posts_width_mobile'] ) ?  $bsp_style_settings_column_display['forum_posts_width_mobile'] : 15); 
		$forum_posts_width_mobile = str_replace ('%' , '' ,$forum_posts_width_mobile ) ;
		$forum_posts_width_mobile = str_replace ('px' , '' ,$forum_posts_width_mobile ) ;
		$forum_posts_width = (!empty( $bsp_style_settings_column_display['forum_posts_width'] ) ?  $bsp_style_settings_column_display['forum_posts_width'] : 15); 
		$forum_posts_width = str_replace ('%' , '' ,$forum_posts_width ) ;
		$forum_posts_width = str_replace ('px' , '' ,$forum_posts_width ) ;
	
		//do widths in all cases
		?>
			li.bbp-forum-reply-count {
				width : <?php echo $forum_posts_width; ?> %; 
			}
			@media only screen and (max-width: 480px) {
				li.bbp-forum-reply-count {
					width : <?php echo esc_html($forum_posts_width_mobile) ; ?>%
				}  
			}
		<?php	
		//hide all
		if ($bsp_style_settings_column_display['forum_posts'] == 1) { ?>
			li.bbp-forum-reply-count {
				display : none ;
			}
			<?php
		}
		//hide mobile only
		if ($bsp_style_settings_column_display['forum_posts'] == 2) { ?>
		@media only screen and (max-width: 480px) {
			li.bbp-forum-reply-count {
				display : none ;
			}
		}
		<?php
		}
		
		//last post column
		$forum_freshness_width_mobile = (!empty( $bsp_style_settings_column_display['forum_freshness_width_mobile'] ) ?  $bsp_style_settings_column_display['forum_freshness_width_mobile'] : 15); 
		$forum_freshness_width_mobile = str_replace ('%' , '' ,$forum_freshness_width_mobile ) ;
		$forum_freshness_width_mobile = str_replace ('px' , '' ,$forum_freshness_width_mobile ) ;
		$forum_freshness_width = (!empty( $bsp_style_settings_column_display['forum_freshness_width'] ) ?  $bsp_style_settings_column_display['forum_freshness_width'] : 15); 
		$forum_freshness_width = str_replace ('%' , '' ,$forum_freshness_width ) ;
		$forum_freshness_width = str_replace ('px' , '' ,$forum_freshness_width ) ;
	
		//do widths in all cases
		?>
			li.bbp-forum-freshness {
				width : <?php echo $forum_freshness_width; ?> %; 
			}
			@media only screen and (max-width: 480px) {
				li.bbp-forum-freshness {
					width : <?php echo esc_html($forum_freshness_width_mobile) ; ?>%
				}  
			}
		<?php	
		//hide all
		if ($bsp_style_settings_column_display['forum_freshness'] == 1) { ?>
			li.bbp-forum-freshness {
				display : none ;
			}
			<?php
		}
		//hide mobile only
		if ($bsp_style_settings_column_display['forum_freshness'] == 2) { ?>
		@media only screen and (max-width: 480px) {
			li.bbp-forum-freshness {
				display : none ;
			}
		}
		<?php
		}
		
		
		
		
}

if (!empty ($bsp_style_settings_column_display['topic_activate'])) {
	
	//topic name column width
		$topic_name_width_mobile = (!empty( $bsp_style_settings_column_display['topic_name_width_mobile'] ) ?  $bsp_style_settings_column_display['topic_name_width_mobile'] : 55); 
		$topic_name_width_mobile = str_replace ('%' , '' ,$topic_name_width_mobile ) ;
		$topic_name_width_mobile = str_replace ('px' , '' ,$topic_name_width_mobile ) ;
		$topic_name_width = (!empty( $bsp_style_settings_column_display['topic_name_width'] ) ?  $bsp_style_settings_column_display['topic_name_width'] : 55); 
		$topic_name_width = str_replace ('%' , '' ,$topic_name_width ) ;
		$topic_name_width = str_replace ('px' , '' ,$topic_name_width ) ;
		//do widths in all cases
		?>
			li.bbp-topic-title {
				width : <?php echo $topic_name_width;?>%; 
			}
			@media only screen and (max-width: 480px) {
				li.bbp-topic-title {
					width : <?php echo esc_html($topic_name_width_mobile) ;?>%
				}  
			}
	<?php
	
	
	//topics column
		$topic_topics_width_mobile = (!empty( $bsp_style_settings_column_display['topic_topics_width_mobile'] ) ?  $bsp_style_settings_column_display['topic_topics_width_mobile'] : 15); 
		$topic_topics_width_mobile = str_replace ('%' , '' ,$topic_topics_width_mobile ) ;
		$topic_topics_width_mobile = str_replace ('px' , '' ,$topic_topics_width_mobile ) ;
		$topic_topics_width = (!empty( $bsp_style_settings_column_display['topic_topics_width'] ) ?  $bsp_style_settings_column_display['topic_topics_width'] : 15); 
		$topic_topics_width = str_replace ('%' , '' ,$topic_topics_width ) ;
		$topic_topics_width = str_replace ('px' , '' ,$topic_topics_width ) ;
	
		//do widths in all cases
		?>
			li.bbp-topic-voice-count {
				width : <?php echo $topic_topics_width; ?> %; 
			}
			@media only screen and (max-width: 480px) {
				li.bbp-topic-voice-count {
					width : <?php echo esc_html($topic_topics_width_mobile); ?>%
				}  
			}
		<?php	
		//hide all
		if ($bsp_style_settings_column_display['topic_topics'] == 1) { ?>
			li.bbp-topic-voice-count {
				display : none ;
			}
			<?php
		}
		//hide mobile only
		if ($bsp_style_settings_column_display['topic_topics'] == 2) { ?>
		@media only screen and (max-width: 480px) {
			li.bbp-topic-voice-count {
				display : none ;
			}
		}
		<?php
		}
		
		//posts column
		$topic_posts_width_mobile = (!empty( $bsp_style_settings_column_display['topic_posts_width_mobile'] ) ?  $bsp_style_settings_column_display['topic_posts_width_mobile'] : 15); 
		$topic_posts_width_mobile = str_replace ('%' , '' ,$topic_posts_width_mobile ) ;
		$topic_posts_width_mobile = str_replace ('px' , '' ,$topic_posts_width_mobile ) ;
		$topic_posts_width = (!empty( $bsp_style_settings_column_display['topic_posts_width'] ) ?  $bsp_style_settings_column_display['topic_posts_width'] : 15); 
		$topic_posts_width = str_replace ('%' , '' ,$topic_posts_width ) ;
		$topic_posts_width = str_replace ('px' , '' ,$topic_posts_width ) ;
	
		//do widths in all cases
		?>
			li.bbp-topic-reply-count {
				width : <?php echo $topic_posts_width; ?> %; 
			}
			@media only screen and (max-width: 480px) {
				li.bbp-topic-reply-count {
					width : <?php echo $topic_posts_width_mobile; ?>%
				}  
			}
		<?php	
		//hide all
		if ($bsp_style_settings_column_display['topic_posts'] == 1) { ?>
			li.bbp-topic-reply-count {
				display : none ;
			}
			<?php
		}
		//hide mobile only
		if ($bsp_style_settings_column_display['topic_posts'] == 2) { ?>
		@media only screen and (max-width: 480px) {
			li.bbp-topic-reply-count {
				display : none ;
			}
		}
		<?php
		}
		
		//last post column
		$topic_freshness_width_mobile = (!empty( $bsp_style_settings_column_display['topic_freshness_width_mobile'] ) ?  $bsp_style_settings_column_display['topic_freshness_width_mobile'] : 15); 
		$topic_freshness_width_mobile = str_replace ('%' , '' ,$topic_freshness_width_mobile ) ;
		$topic_freshness_width_mobile = str_replace ('px' , '' ,$topic_freshness_width_mobile ) ;
		$topic_freshness_width = (!empty( $bsp_style_settings_column_display['topic_freshness_width'] ) ?  $bsp_style_settings_column_display['topic_freshness_width'] : 15); 
		$topic_freshness_width = str_replace ('%' , '' ,$topic_freshness_width ) ;
		$topic_freshness_width = str_replace ('px' , '' ,$topic_freshness_width ) ;
	
		//do widths in all cases
		?>
			li.bbp-topic-freshness {
				width : <?php echo $topic_freshness_width; ?> %; 
			}
			@media only screen and (max-width: 480px) {
				li.bbp-topic-freshness {
					width : <?php echo esc_html($topic_freshness_width_mobile); ?>%
				}  
			}
		<?php	
		//hide all
		if ($bsp_style_settings_column_display['topic_freshness'] == 1) { ?>
			li.bbp-topic-freshness {
				display : none ;
			}
			<?php
		}
		//hide mobile only
		if ($bsp_style_settings_column_display['topic_freshness'] == 2) { ?>
		@media only screen and (max-width: 480px) {
			li.bbp-topic-freshness {
				display : none ;
			}
		}
		<?php
		}
}

?>

/*----------------------  Additional Topics Fields display--------------------------*/

	<?php 
		$field= (!empty($bsp_style_settings_topic_fields['label fontSize']) ? $bsp_style_settings_topic_fields['label fontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums span.bsp_topic_fields_label
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		 
		<?php 
		}
		?>
 
	<?php 
	$field= (!empty($bsp_style_settings_topic_fields['label fontColor']) ? $bsp_style_settings_topic_fields['label fontColor'] : '');
	if (!empty ($field)){
	?>
		#bbpress-forums span.bsp_topic_fields_label
		{
			color: <?php echo esc_html ($field); ?>;
		}
	 
	<?php
	} 
	?>
 
	<?php 
		$field= (!empty($bsp_style_settings_topic_fields['label fontfont']) ? $bsp_style_settings_topic_fields['label fontfont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums span.bsp_topic_fields_label
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
	 
			
		<?php
		} 
		?>
		
	<?php 
		$field= (!empty($bsp_style_settings_topic_fields['label fontStyle']) ? $bsp_style_settings_topic_fields['label fontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbpress-forums span.bsp_topic_fields_label
				{
					font-style: italic; 
				}
	 
				
		<?php
		} 
		if (strpos($field,'Bold') !== false){
		?>
			#bbpress-forums span.bsp_topic_fields_label
			{
				font-weight: bold; 
			}
	 
			
		<?php
		}
		else { ?>
			#bbpress-forums span.bsp_topic_fields_label 
			{
				font-weight: normal; 
			}
	 
		<?php
		} // end of else
	 
	} //if (!empty ($field))

?>
 
	<?php 
		$field= (!empty($bsp_style_settings_topic_fields['item fontSize']) ? $bsp_style_settings_topic_fields['item fontSize'] : '');
		if (!empty ($field)){
			if (is_numeric($field)) $field=$field.'px';
	?>
			#bbpress-forums span.bsp_topic_fields_data
			{
				font-size: <?php echo esc_html ($field); ?>;
			}
		 
		<?php 
		}
		?>
 
	<?php 
	$field= (!empty($bsp_style_settings_topic_fields['item fontColor']) ? $bsp_style_settings_topic_fields['item fontColor'] : '');
	if (!empty ($field)){
	?>
		#bbpress-forums span.bsp_topic_fields_data
		{
			color: <?php echo esc_html ($field); ?>;
		}
	 
	<?php
	} 
	?>
 
	<?php 
		$field= (!empty($bsp_style_settings_topic_fields['item fontfont']) ? $bsp_style_settings_topic_fields['item fontfont'] : '');
		if (!empty ($field)){
	?>
			#bbpress-forums span.bsp_topic_fields_data
			{
				font-family: <?php echo esc_html ($field); ?>;
			}
	 
			
		<?php
		} 
		?>
		
	<?php 
		$field= (!empty($bsp_style_settings_topic_fields['item fontStyle']) ? $bsp_style_settings_topic_fields['item fontStyle'] : '');
		if (!empty ($field)){
			if (strpos($field,'Italic') !== false){
	?>
				#bbpress-forums span.bsp_topic_fields_data
				{
					font-style: italic; 
				}
	 
				
		<?php
		} 
		if (strpos($field,'Bold') !== false){
		?>
			#bbpress-forums span.bsp_topic_fields_data
			{
				font-weight: bold; 
			}
	 
			
		<?php
		}
		else { ?>
			#bbpress-forums span.bsp_topic_fields_data 
			{
				font-weight: normal; 
			}
	 
		<?php
		} // end of else
	 
	} //if (!empty ($field))

?>

/*----------------------  custom css--------------------------*/
	<?php
		$field= (!empty($datacss['css']) ? $datacss['css'] : '');
		if (!empty ($field)){
			echo esc_html ($field);	
		}
?>
