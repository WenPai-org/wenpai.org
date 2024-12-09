<?php echo '<?php '; ?>
header( 'HTTP/1.1 410 Gone ');
header( 'Status: 410 Gone' );
<?php echo '?>'; ?><!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo esc_attr( $language ); ?>">
	<head>
		<title><?php echo esc_html( $title ); ?></title>
		<?php echo $head; ?>
		<?php wp_print_styles( $styles ); ?>
	</head>
		<body class="<?php echo esc_attr( implode( ' ', $body_classes ) ); ?>">
		<?php echo $after_body_tag; ?>
		<div class="mask"></div>
		<div class="overall">
			<div class="page">
				<?php echo $logo; ?>
				<?php echo $content_suspended_title; ?>
				<?php echo $content_suspended_content_meta; ?>
				<?php echo $social_media; ?>
			</div>
		</div>
	</body>
</html>
