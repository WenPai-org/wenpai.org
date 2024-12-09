<script type="text/html" id="tmpl-<?php echo esc_attr( $name ); ?>-blog">
<?php
$args = array(
	'id'   => '{{data.id}}',
	'code' => '',
	'case' => '',
);
$this->render( $template, $args );
?>
</script>
