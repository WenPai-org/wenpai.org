<?php
/**
 * Branda Document class.
 *
 * @since 2.3.0
 *
 * @package Branda
 * @subpackage Front-end
 */
if ( ! class_exists( 'Branda_Document' ) ) {
	class Branda_Document extends Branda_Helper {

		protected $option_name = 'ub_document';

		public function __construct() {
			parent::__construct();
			$this->module = 'document';
			/**
			 * UB admin actions
			 */
			add_filter( 'ultimatebranding_settings_document', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_document_process', array( $this, 'update' ) );
			/**
			 * front end
			 */
			add_filter( 'the_content', array( $this, 'the_content' ), PHP_INT_MAX );
			add_filter( 'shortcode_atts_gallery', array( $this, 'shortcode_atts_gallery' ), PHP_INT_MAX, 4 );
			/**
			 * Password Protected Form
			 *
			 * @since 3.2.0
			 */
			add_filter( 'the_password_form', array( $this, 'password_form' ) );
			/**
			 * Password Protected Title
			 *
			 * @since 3.2.0
			 */
			add_filter( 'protected_title_format', array( $this, 'protected_title_format' ) );
			/**
			 * Upgrade options
			 */
			add_action( 'init', array( $this, 'upgrade_options' ) );
		}
		/**
		 * Upgrade options to new.
		 *
		 * @since 3.0.0
		 */
		public function upgrade_options() {
			$value = $this->get_value( 'configuration' );
			if ( empty( $value ) ) {
				return;
			}
			$data  = array(
				'content'           => array(),
				'shortcode_gallery' => $this->get_value( 'shortcode_gallery' ),
			);
			$value = $this->get_value( 'configuration', 'entry_header', 'off' );
			if ( 'on' === $value ) {
				$data['content']['entry_header']              = 'on';
				$data['content']['entry_header_content']      = $this->get_value( 'entry_header', 'content' );
				$data['content']['entry_header_content_meta'] = $this->get_value( 'entry_header', 'content_meta' );
			}
			$value = $this->get_value( 'configuration', 'entry_footer', 'off' );
			if ( 'on' === $value ) {
				$data['content']['entry_footer']              = 'on';
				$data['content']['entry_footer_content']      = $this->get_value( 'entry_footer', 'content' );
				$data['content']['entry_footer_content_meta'] = $this->get_value( 'entry_footer', 'content_meta' );
			}
			$this->update_value( $data );
		}

		/**
		 * change entry content
		 *
		 * @since 2.3.0
		 */
		public function the_content( $content ) {
			/**
			 * do not change on entries lists
			 */
			if ( ! is_singular() ) {
				return $content;
			}
			/**
			 * entry header
			 */
			$value = $this->get_value( 'content', 'entry_header', 'off' );
			if ( 'on' === $value ) {
				$value   = $this->get_value( 'content', 'entry_header_content_meta', '' );
				$content = $value . $content;
			}
			/**
			 * entry footer
			 */
			$value = $this->get_value( 'content', 'entry_footer', 'off' );
			if ( 'on' === $value ) {
				$value    = $this->get_value( 'content', 'entry_footer_content_meta', '' );
				$content .= $value;
			}
			return $content;
		}

		/**
		 * change shortcode gallery attributes
		 *
		 * @since 2.3.0
		 */
		public function shortcode_atts_gallery( $out, $pairs, $atts, $shortcode ) {
			$values = $this->get_value( 'shortcode_gallery' );
			if ( empty( $values ) || ! is_array( $values ) ) {
				return $out;
			}
			foreach ( $values as $key => $value ) {
				if ( 'do-not-change' === $value ) {
					continue;
				}
				/**
				 * exception for link
				 */
				if ( 'link' === $key && 'attachment' === $value ) {
					$value = '';
				}
				$out[ $key ] = $value;
			}
			return $out;
		}

		/**
		 * set options
		 *
		 * @since 2.3.0
		 */
		protected function set_options() {
			$data = array(
				'content'           => array(
					'title'   => __( 'Entry Content', 'ub' ),
					'show-as' => 'accordion',
					'fields'  => array(
						'entry_header_content' => array(
							'type'         => 'wp_editor',
							'label'        => __( 'Content', 'ub' ),
							'display'      => 'sui-tab-content',
							'master'       => $this->get_name( 'header' ),
							'master-value' => 'on',
							'accordion'    => array(
								'begin' => true,
								'title' => __( 'Before Entry Content', 'ub' ),
							),
							'placeholder'  => esc_html__( 'Enter your before content here…', 'ub' ),
						),
						'entry_header'         => array(
							'type'        => 'sui-tab',
							'options'     => array(
								'off' => __( 'Disable', 'ub' ),
								'on'  => __( 'Enable', 'ub' ),
							),
							'default'     => 'off',
							'slave-class' => $this->get_name( 'header' ),
							'accordion'   => array(
								'end' => true,
							),
						),
						'entry_footer_content' => array(
							'type'         => 'wp_editor',
							'label'        => __( 'Content', 'ub' ),
							'display'      => 'sui-tab-content',
							'master'       => $this->get_name( 'footer' ),
							'master-value' => 'on',
							'accordion'    => array(
								'begin' => true,
								'title' => __( 'After Entry Content', 'ub' ),
							),
							'placeholder'  => esc_html__( 'Enter your after content here…', 'ub' ),
						),
						'entry_footer'         => array(
							'type'        => 'sui-tab',
							'options'     => array(
								'off' => __( 'Disable', 'ub' ),
								'on'  => __( 'Enable', 'ub' ),
							),
							'default'     => 'off',
							'slave-class' => $this->get_name( 'footer' ),
							'accordion'   => array(
								'end' => true,
							),
						),
					),
				),
				'protected'         => array(
					'title'       => __( 'Password Protected', 'ub' ),
					'description' => __( 'Change the message displayed on content which is Password Protected.', 'ub' ),
					'show-as'     => 'accordion',
					'fields'      => array(
						'title_format' => array(
							'display'      => 'sui-tab-content',
							'label'        => __( 'Format', 'ub' ),
							'default'      => __( 'Protected: %s', 'ub' ),
							'accordion'    => array(
								'begin' => true,
								'title' => __( 'Title', 'ub' ),
							),
							'master'       => $this->get_name( 'title-status' ),
							'master-value' => 'custom',
						),
						'title_status' => array(
							'type'        => 'sui-tab',
							'options'     => array(
								'default' => __( 'Default', 'ub' ),
								'clear'   => __( 'Clear', 'ub' ),
								'custom'  => __( 'Custom', 'ub' ),
							),
							'default'     => 'default',
							'slave-class' => $this->get_name( 'title-status' ),
							'accordion'   => array(
								'end' => true,
							),
						),
						'form_message' => array(
							'display'      => 'sui-tab-content',
							'label'        => __( 'Message', 'ub' ),
							'default'      => __( 'This content is password protected. To view it please enter your password below:', 'ub' ),
							'accordion'    => array(
								'begin' => true,
								'title' => __( 'Form', 'ub' ),
							),
							'master'       => $this->get_name( 'form-status' ),
							'master-value' => 'custom',
						),
						'form_field'   => array(
							'display'      => 'sui-tab-content',
							'label'        => __( 'Field title', 'ub' ),
							'default'      => __( 'Password:', 'ub' ),
							'master'       => $this->get_name( 'form-status' ),
							'master-value' => 'custom',
						),
						'form_button'  => array(
							'display'      => 'sui-tab-content',
							'label'        => __( 'Button', 'ub' ),
							'default'      => _x( 'Enter', 'Button on password protection form.', 'ub' ),
							'master'       => $this->get_name( 'form-status' ),
							'master-value' => 'custom',
						),
						'form_status'  => array(
							'type'        => 'sui-tab',
							'options'     => array(
								'default' => __( 'Default', 'ub' ),
								'custom'  => __( 'Custom', 'ub' ),
							),
							'default'     => 'default',
							'slave-class' => $this->get_name( 'form-status' ),
							'accordion'   => array(
								'end' => true,
							),
						),
					),
				),
				'shortcode_gallery' => array(
					'title'       => __( 'Shortcode [gallery]', 'ub' ),
					'description' => __( 'Detailed description of gallery shortcode option you can find on the Codex page: <a href="https://codex.wordpress.org/Gallery_Shortcode" target="_blank">Gallery Shortcode</a>.', 'ub' ),
					'show-as'     => 'accordion',
					'fields'      => array(
						'orderby'    => array(
							'type'        => 'radio',
							'label'       => __( 'Sort By', 'ub' ),
							'options'     => array(
								'do-not-change' => __( 'Do not change', 'ub' ),
								'menu_order'    => __( 'Images order set in gallery tab (WP default)', 'ub' ),
								'title'         => __( 'Title of image', 'ub' ),
								'post_date'     => __( 'Date/time', 'ub' ),
								'rand'          => __( 'Randomly', 'ub' ),
								'ID'            => __( 'ID of image', 'ub' ),
							),
							'default'     => 'do-not-change',
							'description' => __( 'Specify how to sort the display thumbnails.', 'ub' ),
							'accordion'   => array(
								'begin' => true,
								'title' => __( 'Order', 'ub' ),
							),
						),
						'order'      => array(
							'type'        => 'radio',
							'label'       => __( 'Sort Order', 'ub' ),
							'options'     => array(
								'do-not-change' => __( 'Do not change', 'ub' ),
								'ASC'           => __( 'Ascendant (WP default)', 'ub' ),
								'DESC'          => __( 'Descended', 'ub' ),
							),
							'default'     => 'do-not-change',
							'description' => __( 'Specify the sort order used to display thumbnails.', 'ub' ),
							'accordion'   => array(
								'end' => true,
							),
						),
						'columns'    => array(
							'type'        => 'number',
							'label'       => __( 'Columns', 'ub' ),
							'min'         => 0,
							'description' => __( 'Specify the number of columns. The gallery will include a break tag at the end of each row, and calculate the column width as appropriate. The default value is 3. If columns is set to 0, no row breaks will be included. ', 'ub' ),
							'default'     => 3,
							'accordion'   => array(
								'begin' => true,
								'title' => __( 'Design', 'ub' ),
							),
						),
						'size'       => array(
							'type'        => 'radio',
							'label'       => __( 'Thumbnail Size', 'ub' ),
							'options'     => array(
								'do-not-change' => __( 'Do not change', 'ub' ),
								'thumbnail'     => __( 'Thumbnail (WP default)', 'ub' ),
								'medium'        => __( 'Medium', 'ub' ),
								'large'         => __( 'Large', 'ub' ),
								'full'          => __( 'Full', 'ub' ),
							),
							'default'     => 'do-not-change',
							'description' => __( 'Specify the image size to use for the thumbnail display.', 'ub' ),
							'accordion'   => array(
								'end' => true,
							),
						),
						'link'       => array(
							'type'        => 'radio',
							'label'       => __( 'Thumbnail Link', 'ub' ),
							'options'     => array(
								'do-not-change' => __( 'Do not change', 'ub' ),
								'attachment'    => __( 'Attachment page (WP default)', 'ub' ),
								'file'          => __( 'Link directly to image file', 'ub' ),
								'none'          => __( 'No link', 'ub' ),
							),
							'default'     => 'do-not-change',
							'description' => __( 'Specify where you want the image to link.', 'ub' ),
							'accordion'   => array(
								'begin' => true,
								'title' => __( 'Content', 'ub' ),
								'end'   => true,
							),
						),
						'itemtag'    => array(
							'type'        => 'text',
							'label'       => __( 'Item Tag', 'ub' ),
							'default'     => 'dl',
							'description' => __( 'The name of the HTML tag used to enclose each item in the gallery.', 'ub' ),
							'accordion'   => array(
								'begin' => true,
								'title' => __( 'HTML', 'ub' ),
							),
						),
						'icontag'    => array(
							'type'        => 'text',
							'label'       => __( 'Icon Tag', 'ub' ),
							'default'     => 'dt',
							'description' => __( 'The name of the HTML tag used to enclose each thumbnail icon in the gallery.', 'ub' ),
						),
						'captiontag' => array(
							'type'        => 'text',
							'label'       => __( 'Caption Tag', 'ub' ),
							'default'     => 'dd',
							'description' => __( 'The name of the HTML tag used to enclose each caption.', 'ub' ),
							'accordion'   => array(
								'end' => true,
							),
						),
					),
				),
			);
			if ( ! $this->is_network ) {
				global $_wp_additional_image_sizes;
				if ( is_array( $_wp_additional_image_sizes ) ) {
					foreach ( array_keys( $_wp_additional_image_sizes ) as $name ) {
						$data['shortcode_gallery']['fields']['size']['options'][ $name ] = ucwords( preg_replace( '/[-_]+/', ' ', $name ) );
					}
				}
			}
			$this->options = $data;
		}

		/**
		 * Change Password Protected Form
		 *
		 * @since 3.2.0
		 */
		public function password_form( $content ) {
			$value = $this->get_value( 'protected', 'form_status', 'default' );
			if ( 'custom' !== $value ) {
				return $content;
			}
			global $post;
			$label    = 'pwbox-' . ( empty( $post->ID ) ? rand() : $post->ID );
			$content  = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form" method="post">';
			$value    = $this->get_value( 'protected', 'form_message' );
			$content .= sprintf( '<p>%s</p>', $value, __( 'This content is password protected. To view it please enter your password below:', 'ub' ) );
			$content .= sprintf( '<p><label for="%s">', esc_attr( $label ) );
			$content .= $this->get_value( 'protected', 'form_field', __( 'Password:', 'ub' ) );
			$content .= ' ';
			$content .= sprintf(
				'<input name="post_password" id="%s" type="password" size="20" />',
				esc_attr( $label )
			);
			$content .= '</label>';
			$content .= ' ';

			$content .= sprintf(
				'<input type="submit" name="Submit" value="%s" />',
				$this->get_value( 'protected', 'form_button', __( 'Enter', 'ub' ) )
			);
			$content .= '</p></form>';
			return $content;
		}

		/**
		 * Change Password Protected Title
		 *
		 * @since 3.2.0
		 */
		public function protected_title_format( $content ) {
			$value = $this->get_value( 'protected', 'title_status', 'default' );
			switch ( $value ) {
				case 'custom':
					return $this->get_value( 'protected', 'title_format', '%s' );
				case 'clear':
					return '%s';
				default:
					return $content;
			}
			return $content;
		}
	}
}
new Branda_Document();
