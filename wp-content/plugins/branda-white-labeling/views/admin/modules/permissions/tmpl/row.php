<script type="text/html" id="tmpl-<?php echo esc_attr( $dialog_id ); ?>-row">
<?php
$args = array(
	'id'     => '{{data.id}}',
	'title'  => '{{data.title}}',
	'email'  => '{{data.email}}',
	'avatar' => '{{data.avatar}}',
	'nonce'  => '{{data.nonce}}',
);
$this->render( $template, $args );
?>
</script>
