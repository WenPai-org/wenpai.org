<script type="text/html" id="tmpl-<?php echo esc_attr( $name ); ?>-user">
<?php
$args = array(
	'id'    => '{{data.id}}',
	'code'  => '',
	'role'  => '-',
	'roles' => $roles,
	'case'  => '',
);
$this->render( $template, $args );
?>
</script>
