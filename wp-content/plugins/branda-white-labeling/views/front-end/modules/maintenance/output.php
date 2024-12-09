<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo esc_attr( $language ); ?>">
	<head>
		<title><?php echo esc_html( $title ); ?></title>
		<?php echo $head; ?>
		<?php wp_print_styles( 'ub-front-end-maintenance-styling' ); ?>
	<?php wp_head(); ?>
	</head>
	<body class="<?php echo esc_attr( implode( ' ', $body_classes ) ); ?>">
		<?php echo $after_body_tag; ?>
		<div class="overall">
			<div class="page">
			<?php
			echo $logo;
			echo $content_title;
			echo $content_content_meta;
			echo $countdown;
			echo $social_media;
			?>
			</div>
		</div>
	</body>
</html>
