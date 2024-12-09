<?php echo $php; ?><!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $language; ?>">
	<head>
		<title><?php echo $title; ?></title>
		<?php echo $head; ?>
		<?php wp_print_styles( 'ub-db-error-page-styling' ); ?>
		<script type="text/javascript"><?php echo $javascript; ?></script>
	</head>
	<body class="<?php echo esc_attr( $body_class ); ?>">
		<div class="overall">
			<div class="page">
				<?php echo $logo; ?>
				<div class="content">
					<h1><?php echo $title; ?></h1>
					<?php echo $content; ?>
					<?php echo $social_media; ?>
				</div>
			</div>
		</div>
	</body>
</html>
