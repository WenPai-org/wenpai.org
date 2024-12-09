<script type="text/javascript" id="<?php echo esc_attr( $id ); ?>">
var branda_footer_node = document.createElement('div');
<?php
/**
 * ID
 */
?>
var branda_footer = document.createAttribute('id');
branda_footer.value = 'branda_content_footer';
branda_footer_node.setAttributeNode( branda_footer );
<?php
/**
 * style
 */
?>
branda_footer = document.createAttribute('style');
branda_footer.value = '<?php echo esc_attr( $style ); ?>';
branda_footer_node.setAttributeNode( branda_footer );
branda_footer_node.innerHTML = <?php echo json_encode( stripslashes( $content ) ); ?>;
<?php
/**
 * Content
 */
?>
branda_footer = document.getElementsByTagName( '<?php echo esc_attr( $tag ); ?>' );
if ( branda_footer.length ) {
	branda_footer = branda_footer[ branda_footer.length - 1 ];
	branda_footer.appendChild( branda_footer_node, branda_footer.firstChild );
}
</script>

