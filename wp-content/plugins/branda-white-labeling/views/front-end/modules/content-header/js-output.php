<script type="text/javascript" id="<?php echo esc_attr( $id ); ?>">
var branda_header_node = document.createElement('div');
<?php
/**
 * ID
 */
?>
var branda_header = document.createAttribute('id');
branda_header.value = 'branda_content_header';
branda_header_node.setAttributeNode( branda_header );
<?php
/**
 * style
 */
?>
branda_header = document.createAttribute('style');
branda_header.value = '<?php echo esc_attr( $style ); ?>';
branda_header_node.setAttributeNode( branda_header );
branda_header_node.innerHTML = <?php echo json_encode( stripslashes( $content ) ); ?>;
<?php
/**
 * Content
 */
?>
branda_header = document.getElementsByTagName( '<?php echo esc_attr( $tag ); ?>' )[0];
branda_header.insertBefore( branda_header_node, branda_header.firstChild );
</script>

