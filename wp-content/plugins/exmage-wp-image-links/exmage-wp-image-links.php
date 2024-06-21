<?php
/**
 * Plugin Name: EXMAGE - WordPress Image Links (定制版)
 * Plugin URI: https://villatheme.com/extensions/exmage-wordpress-image-links/
 * Description: Save storage by using external image URLs.
 * Version: 100
 * Author: VillaTheme(villatheme.com)
 * Author URI: https://villatheme.com
 * Text Domain: exmage-wp-image-links
 * Copyright 2021-2024 VillaTheme.com. All rights reserved.
 * Tested up to: 6.5
 * Requires PHP: 7.0
 **/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EXMAGE_WP_IMAGE_LINKS_VERSION', '1.0.17' );
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
define( 'EXMAGE_WP_IMAGE_LINKS_DIR', plugin_dir_path( __FILE__ ) );
define( 'EXMAGE_WP_IMAGE_LINKS_INCLUDES', EXMAGE_WP_IMAGE_LINKS_DIR . "includes" . DIRECTORY_SEPARATOR );
require_once EXMAGE_WP_IMAGE_LINKS_INCLUDES . "define.php";

/**
 * Class EXMAGE_WP_IMAGE_LINKS
 */
if ( ! class_exists( 'EXMAGE_WP_IMAGE_LINKS' ) ) {
	class EXMAGE_WP_IMAGE_LINKS {
		public static $background_process;

		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'background_process' ) );
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), PHP_INT_MAX );
			add_action( 'wp_enqueue_media', array( $this, 'wp_enqueue_media' ), PHP_INT_MAX );
			/*Short link to Add new media*/
			add_filter( 'plugin_action_links_exmage-wp-image-links/exmage-wp-image-links.php', array( $this, 'settings_link' ) );
			/*wp.media*/
			add_action( 'post-upload-ui', array( $this, 'post_upload_ui' ), 20 );
			add_action( 'print_media_templates', array( $this, 'override_media_template_attachment_detail' ) );
			add_filter( 'wp_prepare_attachment_for_js', array( $this, 'wp_prepare_attachment_for_js' ) );
			/*External URL column in Media library/mode=list*/
			add_filter( 'manage_media_columns', array( $this, 'is_external' ) );
			add_action( 'manage_media_custom_column', array( $this, 'column_callback_media' ) );
			/*Return error when saving external image*/
			add_filter( 'load_image_to_edit_path', array( $this, 'load_image_to_edit_path' ), 10, 3 );
			/*Filter attachment url and image source set*/
			add_filter( 'wp_get_attachment_url', array( $this, 'wp_get_attachment_url' ), 10, 2 );
			add_filter( 'wp_calculate_image_srcset', array( $this, 'wp_calculate_image_srcset' ), 10, 5 );
			/*Ajax add image from URLs*/
			add_action( 'wp_ajax_exmage_handle_url', array( $this, 'handle_url' ) );
			/*Ajax store external images to server*/
			add_action( 'wp_ajax_exmage_convert_external_image', array( $this, 'convert_external_image' ) );
			/*Jetpack - Photon CDN*/
			add_filter( 'jetpack_photon_skip_image', array( $this, 'jetpack_photon_skip_image' ), 10, 3 );
			/*WPML*/
			add_action( 'wpml_after_duplicate_attachment', array( $this, 'wpml_after_duplicate_attachment' ), 10, 2 );

			add_action( 'woocommerce_product_import_before_process_item', function () {
				remove_action( 'pre_get_posts', [ $this, 'search_exmage_url_when_import_product' ] );
				add_action( 'pre_get_posts', [ $this, 'search_exmage_url_when_import_product' ] );
			} );

			add_action( 'woocommerce_product_import_inserted_product_object', function () {
				remove_action( 'pre_get_posts', [ $this, 'search_exmage_url_when_import_product' ] );
			} );
		}

		/**
		 * Add needed post meta when an external image is cloned by WPML
		 *
		 * @param $attachment_id
		 * @param $duplicated_attachment_id
		 */
		public function wpml_after_duplicate_attachment( $attachment_id, $duplicated_attachment_id ) {
			$_exmage_external_url = get_post_meta( $attachment_id, '_exmage_external_url', true );
			if ( $_exmage_external_url && ! get_post_meta( $attachment_id, '_exmage_imported', true ) ) {
				update_post_meta( $duplicated_attachment_id, '_exmage_external_url', $_exmage_external_url );
			}
		}

		public function stop_processing_button() {
			$href = add_query_arg( [ 'exmage_stop_processing' => 1, 'exmage_nonce' => wp_create_nonce( 'exmage_stop_processing' ) ] );
			printf( "<a href='%s' class='button' style='vertical-align: middle;'>%s</a>", esc_url( $href ), esc_html__( 'Stop processing', 'exmage-wp-image-links' ) );
		}

		/**
		 * Show status of background processing
		 */
		public function admin_notices() {
			if ( get_site_option( 'exmage_background_process_image_kill_process' ) ) {
				return;
			}

			if ( self::$background_process->is_downloading() ) {
				?>
                <div class="updated">
                    <h4>
						<?php
						printf( esc_html__( 'EXMAGE - WordPress Image Links: %s URLs are being processed in the background.', 'exmage-wp-image-links' ), self::$background_process->get_items_left() );
						$this->stop_processing_button();
						?>
                    </h4>
                </div>
				<?php
			} elseif ( ! self::$background_process->is_queue_empty() ) {
				?>
                <div class="updated">
                    <h4>
						<?php
						printf( esc_html__( 'EXMAGE - WordPress Image Links: %s URLs are in the queue.', 'exmage-wp-image-links' ), self::$background_process->get_items_left() );
						$this->stop_processing_button();
						?>
                    </h4>
                </div>
				<?php
			} elseif ( get_transient( 'exmage_background_process_image_complete' ) ) {
				delete_transient( 'exmage_background_process_image_complete' );
				?>
                <div class="updated">
                    <p>
						<?php esc_html_e( 'EXMAGE - WordPress Image Links: all URLs are processed.', 'exmage-wp-image-links' ) ?>
                    </p>
                </div>
				<?php
			}
		}

		public function admin_init() {
			if ( isset( $_GET['exmage_stop_processing'], $_GET['exmage_nonce'] ) && wp_verify_nonce( $_GET['exmage_nonce'], 'exmage_stop_processing' ) ) {
				if ( ! empty( self::$background_process ) ) {
					self::$background_process->kill_process();
					$url = remove_query_arg( [ 'exmage_stop_processing', 'exmage_nonce' ] );
					wp_safe_redirect( $url );
					die;
				}
			}
		}

		/**
		 * Background process
		 */
		public function background_process() {
			self::$background_process = new EXMAGE_Background_Process_Images();
		}

		/**
		 * Skip if the image src is external
		 *
		 * @param $skip_image
		 * @param $src
		 * @param $tag
		 *
		 * @return mixed
		 */
		public function jetpack_photon_skip_image( $skip_image, $src, $tag ) {
			if ( ! $skip_image ) {
				if ( strpos( $src, get_site_url() ) !== 0 ) {
					$skip_image = true;
				}
			}

			return $skip_image;
		}

		/**
		 * Do not allow to edit external images
		 *
		 * @param $filepath
		 * @param $attachment_id
		 * @param $size
		 *
		 * @return bool
		 */
		public function load_image_to_edit_path( $filepath, $attachment_id, $size ) {
			if ( get_post_meta( $attachment_id, '_exmage_external_url', true ) && ! get_post_meta( $attachment_id, '_exmage_imported', true ) ) {
				return false;
			}

			return $filepath;
		}

		/**
		 * @param $links
		 *
		 * @return mixed
		 */
		public function settings_link( $links ) {
			$links[] = sprintf( wp_kses_post( __( '<a href="%s">Add images from URLs</a>', 'exmage-wp-image-links' ) ), esc_url( admin_url( 'media-new.php' ) ) );

			return $links;
		}

		/**
		 *
		 */
		public function post_upload_ui() {
			global $pagenow;
			?>
            <div>
                <p class="exmage-use-url-instructions upload-instructions drop-instructions"><?php _ex( 'or', 'Uploader: Upload file - or - Use an external image URL' ); ?></p>
                <div class="exmage-use-url-container">
                    <label for="exmage-use-url-input"><?php $pagenow === 'media-new.php' ? esc_html_e( 'Save storage by using external image URLs(one line each):', 'exmage-wp-image-links' ) : printf( wp_kses_post( __( 'Save storage by using an external image URL(need to add multiple URLs? <a target="_blank" href="%s">Click here</a>):', 'exmage-wp-image-links' ) ), esc_url( admin_url( 'media-new.php' ) ) ); ?></label>
                    <div class="exmage-use-url-input-container">
						<?php
						if ( $pagenow === 'media-new.php' ) {
							?>
                            <textarea class="exmage-use-url-input-multiple" rows="10"></textarea>
                            <p>
                                <span class="exmage-use-url-input-multiple-add button button-primary"><?php esc_html_e( 'Add', 'exmage-wp-image-links' ) ?></span>
                            </p>
							<?php
						} else {
							?>
                            <input type="search" id="exmage-use-url-input" class="exmage-use-url-input"
                                   placeholder="<?php esc_attr_e( 'Paste an external image URL here or press Enter after you type to process', 'exmage-wp-image-links' ) ?>">
							<?php
						}
						?>
                        <div class="exmage-use-url-input-overlay exmage-hidden"></div>
                    </div>
                    <div class="exmage-use-url-message"></div>
                </div>
            </div>
			<?php
		}

		/**
		 * @param $response
		 *
		 * @return mixed
		 */
		public function wp_prepare_attachment_for_js( $response ) {
			$_exmage_external_url = '';
			if ( ! get_post_meta( $response['id'], '_exmage_imported', true ) ) {
				$_exmage_external_url = get_post_meta( $response['id'], '_exmage_external_url', true );
//				if ( $_exmage_external_url ) {
//					$response['can']['save'] = false;
//				}
			}
			$response['_exmage_external_url'] = $_exmage_external_url;

			return $response;
		}

		/**
		 * Override templates
		 */
		public function override_media_template_attachment_detail() {
			?>
            <script type="text/html" id="tmpl-exmage-attachment">
                <div class="attachment-preview js--select-attachment type-{{ data.type }} subtype-{{ data.subtype }} {{ data.orientation }}">
                    <div class="thumbnail">
                        <# if ( data.uploading ) { #>
                        <div class="media-progress-bar">
                            <div style="width: {{ data.percent }}%"></div>
                        </div>
                        <# } else if ( 'image' === data.type && data.size && data.size.url ) { #>
                        <div class="centered">
                            <img src="{{ data.size.url }}" draggable="false" alt=""/>
                        </div>
                        <# } else { #>
                        <div class="centered">
                            <# if ( data.image && data.image.src && data.image.src !== data.icon ) { #>
                            <img src="{{ data.image.src }}" class="thumbnail" draggable="false" alt=""/>
                            <# } else if ( data.sizes && data.sizes.medium ) { #>
                            <img src="{{ data.sizes.medium.url }}" class="thumbnail" draggable="false" alt=""/>
                            <# } else { #>
                            <img src="{{ data.icon }}" class="icon" draggable="false" alt=""/>
                            <# } #>
                        </div>
                        <div class="filename">
                            <div>{{ data.filename }}</div>
                        </div>
                        <# } #>
                    </div>
                    <# if ( data.buttons.close ) { #>
                    <button type="button" class="button-link attachment-close media-modal-icon"><span
                                class="screen-reader-text"><?php _e( 'Remove' ); ?></span></button>
                    <# } #>
                </div>
                <# if ( data.buttons.check ) { #>
                <button type="button" class="check" tabindex="-1"><span class="media-modal-icon"></span><span
                            class="screen-reader-text"><?php _e( 'Deselect' ); ?></span></button>
                <# } #>
                <# if ( data.hasOwnProperty('_exmage_external_url')&&data._exmage_external_url ) { #>
                <span class="exmage-is-external-link"
                      title="<?php esc_html_e( 'This is an external media file', 'exmage-wp-image-links' ); ?>"><span
                            class="dashicons dashicons-external"></span></span>
                <# } #>
                <#
                var maybeReadOnly = data.can.save || data.allowLocalEdits ? '' : 'readonly';
                if ( data.describe ) {
                if ( 'image' === data.type ) { #>
                <input type="text" value="{{ data.caption }}" class="describe" data-setting="caption"
                       aria-label="<?php esc_attr_e( 'Caption' ); ?>"
                       placeholder="<?php esc_attr_e( 'Caption&hellip;' ); ?>" {{ maybeReadOnly }}/>
                <# } else { #>
                <input type="text" value="{{ data.title }}" class="describe" data-setting="title"
                <# if ( 'video' === data.type ) { #>
                aria-label="<?php esc_attr_e( 'Video title' ); ?>"
                placeholder="<?php esc_attr_e( 'Video title&hellip;' ); ?>"
                <# } else if ( 'audio' === data.type ) { #>
                aria-label="<?php esc_attr_e( 'Audio title' ); ?>"
                placeholder="<?php esc_attr_e( 'Audio title&hellip;' ); ?>"
                <# } else { #>
                aria-label="<?php esc_attr_e( 'Media title' ); ?>"
                placeholder="<?php esc_attr_e( 'Media title&hellip;' ); ?>"
                <# } #> {{ maybeReadOnly }} />
                <# }
                } #>
            </script>
			<?php
		}

		/**
		 * Add External URL column to media list view
		 *
		 * @param $cols
		 *
		 * @return mixed
		 */
		public function is_external( $cols ) {
			$cols['exmage_is_external'] = '<span>' . esc_html__( 'External URL', 'exmage-wp-image-links' ) . '</span>';

			return $cols;
		}

		/**
		 * @param $col
		 */
		public function column_callback_media( $col ) {
			global $post;
			if ( $col === 'exmage_is_external' && $post ) {
				?>
                <div class="exmage-external-url-container">
                    <div class="exmage-external-url-content">
						<?php
						$_exmage_imported = get_post_meta( $post->ID, '_exmage_imported', true );
						$external_link    = get_post_meta( $post->ID, '_exmage_external_url', true );
						if ( ! $_exmage_imported && $external_link ) {
							self::html_for_external_image( $external_link, $post->ID );
						} elseif ( $_exmage_imported || get_post_meta( $post->ID, '_vi_wad_image_id', true ) ) {
//							self::html_for_convertable_external_image( $post->ID );
						}
						?>
                    </div>
                    <p class="exmage-migrate-message"></p>
                </div>
				<?php
			}
		}

		/**
		 * @param $attachment_id
		 */
		private static function html_for_convertable_external_image( $attachment_id ) {
			?>
            <p class="exmage-action-buttons-container">
                <span class="button exmage-convert-external-button"
                      data-attachment_id="<?php echo esc_attr( $attachment_id ) ?>"
                      title="<?php esc_attr_e( 'Change this image to use external link. The existing image file stored on your server will be deleted.', 'exmage-wp-image-links' ) ?>">
                    <span class="dashicons dashicons-cloud-upload"></span>
                    <span class="exmage-migrate-button-overlay"></span>
                </span>
            </p>
			<?php
		}

		/**
		 * @param $external_link
		 * @param $attachment_id
		 */
		private static function html_for_external_image( $external_link, $attachment_id ) {
			?>
            <a target="_blank"
               href="<?php echo esc_url( $external_link ) ?>"><span
                        class="exmage-external-url"><?php echo esc_html( $external_link ) ?></span><span
                        class="dashicons dashicons-external"></span>
            </a>
            <p class="exmage-action-buttons-container">
                <span class="button exmage-migrate-button"
                      data-attachment_id="<?php echo esc_attr( $attachment_id ) ?>"
                      title="<?php esc_attr_e( 'Save this image to your WordPress server like normal images so that it will be editable', 'exmage-wp-image-links' ) ?>">
                    <span class="dashicons dashicons-cloud-saved"></span>
                    <span class="exmage-migrate-button-overlay"></span>
                </span>
            </p>
			<?php
		}

		/**
		 * TODO Platform
		 * 不检查了，慢的一批。。。
		 */
		/**
		 * Check if an url is a valid image
		 *
		 * @param $url
		 * @param $width
		 * @param $height
		 *
		 * @return bool
		 */
		private static function is_image_url_valid( $url, &$width, &$height ) {
			/*$image_size         = function_exists( 'wp_getimagesize' ) ? wp_getimagesize( $url ) : getimagesize( $url );
			$is_valid_image_url = false;
			if ( $image_size !== false ) {
				$is_valid_image_url = true;
				$width              = $image_size[0];
				$height             = $image_size[1];
			} else {
				$request = wp_safe_remote_get( $url );
				if ( ! is_wp_error( $request ) && wp_remote_retrieve_response_code( $request ) === 200 && in_array( wp_remote_retrieve_header( $request, 'content-type' ), self::get_supported_mime_types(), true ) ) {
					$is_valid_image_url = true;
				}
			}

			return $is_valid_image_url;*/
			return true;
		}

		/**
		 * Save external images
		 */
		public function convert_external_image() {
			global $wpdb;
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'Sorry, you do not have permission.' );
			}
			check_ajax_referer( 'exmage_ajax_handle_url', '_exmage_ajax_nonce' );
			$response      = array(
				'status'  => 'error',
				'message' => '',
			);
			$attachment_id = isset( $_POST['attachment_id'] ) ? sanitize_text_field( $_POST['attachment_id'] ) : '';
			$to_external   = isset( $_POST['to_external'] ) ? sanitize_text_field( $_POST['to_external'] ) : '';

			if ( $attachment_id ) {
				$attachment = get_post( $attachment_id );
				if ( $attachment && $attachment->post_type === 'attachment' ) {
					$url              = get_post_meta( $attachment_id, '_exmage_external_url', true );
					$_exmage_imported = get_post_meta( $attachment_id, '_exmage_imported', true );
					if ( $to_external ) {
						$to_url = '';
						if ( $_exmage_imported ) {
							if ( $url ) {
								$to_url = $url;
							}
						} else {
							$ali_image_id = get_post_meta( $attachment_id, '_vi_wad_image_id', true );
							if ( $ali_image_id ) {
								$to_url = $ali_image_id;
								if ( 'https' !== substr( $to_url, 0, 5 ) ) {
									$to_url = set_url_scheme( '//' . $to_url, 'https' );
								}
							}
						}

						if ( $to_url ) {
							$width              = $height = 800;
							$is_valid_image_url = self::is_image_url_valid( $to_url, $width, $height );

							if ( $is_valid_image_url ) {
								$file = get_attached_file( $attachment_id, true );
								if ( is_multisite() ) {
									clean_dirsize_cache( $file );
								}
								if ( wp_delete_attachment_files( $attachment_id, wp_get_attachment_metadata( $attachment_id ), get_post_meta( $attachment_id, '_wp_attachment_backup_sizes', true ), $file ) ) {
									$to_url = self::process_image_url( $to_url, $image_id, $is_ali_cdn );
									update_post_meta( $attachment_id, '_wp_attached_file', $to_url );
									if ( $_exmage_imported ) {
										delete_post_meta( $attachment_id, '_exmage_imported' );
									} else {
//										delete_post_meta( $attachment_id, '_vi_wad_image_id' );
									}
									self::update_attachment_metadata( $attachment_id, $to_url, $is_ali_cdn, $width, $height );
									/*guid cannot be changed with wp_update_post function*/
									$wpdb->update( $wpdb->posts, array(
										'guid'       => strlen( $to_url ) > 255 ? '' : $to_url,//guid is varchar(255)
										'post_title' => apply_filters( 'exmage_insert_attachment_image_name', basename( $image_id ), $image_id, $to_url, $attachment->post_parent ),
									), array( 'ID' => $attachment_id ) );
									$response['status'] = 'success';
									ob_start();
									self::html_for_external_image( $to_url, $attachment_id );
									$response['message'] = ob_get_clean();

								} else {
									$response['message'] = esc_html__( 'Cannot delete file', 'exmage-wp-image-links' );
								}
							} else {
								$response['message'] = esc_html__( 'Invalid or not supported image URL', 'exmage-wp-image-links' );
							}
						} else {
							$response['message'] = esc_html__( 'Cannot find external URL', 'exmage-wp-image-links' );
						}
					} else {
						if ( ! $_exmage_imported ) {
							if ( $url ) {
								$tmp                    = download_url( $url );
								$file_array['name']     = $attachment->post_title ? $attachment->post_title : basename( $url );
								$file_array['tmp_name'] = $tmp;
								if ( ! is_wp_error( $tmp ) ) {
									$file = wp_handle_sideload( $file_array, array( 'test_form' => false ) );
									if ( ! isset( $file['error'] ) ) {
										$file_url = $file['url'];
										$type     = $file['type'];
										$file     = $file['file'];
										$title    = preg_replace( '/\.[^.]+$/', '', wp_basename( $file ) );
										$content  = '';
										// Use image exif/iptc data for title and caption defaults if possible.
										$image_meta = wp_read_image_metadata( $file );

										if ( $image_meta ) {
											if ( trim( $image_meta['title'] ) && ! is_numeric( sanitize_title( $image_meta['title'] ) ) ) {
												$title = $image_meta['title'];
											}

											if ( trim( $image_meta['caption'] ) ) {
												$content = $image_meta['caption'];
											}
										}
										$update_data = array(
											'ID'             => $attachment_id,
											'post_mime_type' => $type,
											'post_content'   => $content,
										);
										if ( ! $attachment->post_title ) {
											$update_data['post_title'] = $title;
										}
										// Save the attachment metadata.
										$update_post = wp_update_post( $update_data, true );
										if ( ! is_wp_error( $update_post ) ) {
											/*guid cannot be changed with wp_update_post function*/
											$wpdb->update( $wpdb->posts, array(
												'guid' => strlen( $file_url ) > 255 ? '' : $file_url,
												//guid is varchar(255)
											), array( 'ID' => $attachment_id ) );
											$response['status'] = 'success';
											ob_start();
											self::html_for_convertable_external_image( $attachment_id );
											$response['message'] = ob_get_clean();
											$upload_dir          = wp_get_upload_dir();
											$image_baseurl       = trailingslashit( $upload_dir['baseurl'] );
											update_post_meta( $attachment_id, '_wp_attached_file', str_replace( $image_baseurl, '', $file_url ) );
											update_post_meta( $attachment_id, '_exmage_imported', time() );
											wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $file ) );
										} else {
											$response['message'] = $update_post->get_error_message();
										}
									} else {
										$response['message'] = $file['error'];
									}
								} else {
									@unlink( $file_array['tmp_name'] );
									$response['message'] = $tmp->get_error_message();
								}
							} else {
								$response['message'] = esc_html__( 'Cannot find external URL', 'exmage-wp-image-links' );
							}
						} else {
							$response['message'] = esc_html__( 'Saved already, please reload the page', 'exmage-wp-image-links' );
						}
					}
				}
			}

			wp_send_json( $response );
		}

		/**
		 * @param $url
		 * @param $image_id
		 * @param $is_ali_cdn
		 *
		 * @return string|string[]|null
		 */
		public static function process_image_url( $url, &$image_id, &$is_ali_cdn ) {
			$new_url = $url;
//			$new_url    = preg_replace( "/(.+?)(.jpg|.jpeg)(.*)/", "$1$2", $new_url );
			$parse_url  = wp_parse_url( $new_url );
			$scheme     = empty( $parse_url['scheme'] ) ? 'http' : $parse_url['scheme'];
			$image_id   = "{$parse_url['host']}{$parse_url['path']}";
			$new_url    = "{$scheme}://{$image_id}";
			$is_ali_cdn = in_array( $parse_url['host'], array(
				'ae01.alicdn.com',
				'ae02.alicdn.com',
				'ae03.alicdn.com',
				'ae04.alicdn.com',
				'ae05.alicdn.com',
			), true );
			preg_match( '/[^?]+\.(jpg|JPG|jpeg|JPEG|jpe|JPE|gif|GIF|png|PNG)/', $new_url, $matches );
			if ( ! is_array( $matches ) || ! count( $matches ) ) {
				preg_match( '/[^?]+\.(jpg|JPG|jpeg|JPEG|jpe|JPE|gif|GIF|png|PNG)/', $url, $matches );
				if ( is_array( $matches ) && count( $matches ) ) {
					$new_url  .= "?{$matches[0]}";
					$image_id .= "?{$matches[0]}";
				} elseif ( ! empty( $parse_url['query'] ) ) {
					$new_url .= '?' . $parse_url['query'];
				}
			} elseif ( ! empty( $parse_url['query'] ) ) {
				$new_url .= '?' . $parse_url['query'];
			}

			return $new_url;
		}

		/**
		 *
		 */
		public function handle_url() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'Sorry, you do not have permission.' );
			}
			check_ajax_referer( 'exmage_ajax_handle_url', '_exmage_ajax_nonce' );
			$response  = array(
				'status'  => 'error',
				'message' => '',
				'id'      => '',
				'details' => array(),
			);
			$post_id   = isset( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';
			$is_single = isset( $_POST['is_single'] ) ? sanitize_text_field( $_POST['is_single'] ) : '';
			$urls      = isset( $_POST['urls'] ) ? sanitize_trackback_urls( $_POST['urls'] ) : '';
			if ( ! empty( $urls ) ) {
				$urls_array = explode( "\n", $urls );
				$urls_array = array_filter( $urls_array );
				$urls_array = array_values( array_unique( $urls_array ) );
				if ( $is_single ) {
					$urls_array = array_slice( $urls_array, 0, 1 );
				}
				$urls_count     = count( $urls_array );
				$urls_threshold = apply_filters( 'exmage_ajax_handle_url_threshold', 20 );
				if ( $urls_count <= $urls_threshold ) {
					foreach ( $urls_array as $url ) {
						$response['details'][] = self::add_image( $url, $image_id, '', $post_id );
					}
				} else {
					foreach ( $urls_array as $url ) {
						self::$background_process->push_to_queue( array( 'url' => $url, 'post_id' => $post_id ) );
					}
					self::$background_process->save()->dispatch();
					$response['message'] = sprintf( esc_html__( 'The number of URLs(%1$s) is greater than threshold(%2$s), they will be processed in the background.(You can change the threshold via exmage_ajax_handle_url_threshold filter hook.)', 'exmage-wp-image-links' ), $urls_count, $urls_threshold );
					$response['status']  = 'queue';
				}
			}
			if ( count( $response['details'] ) ) {
				$response['status'] = 'success';
			} elseif ( $response['status'] !== 'queue' ) {
				$response['message'] = esc_html__( 'No valid image URLs found', 'exmage-wp-image-links' );
			}

			wp_send_json( $response );
		}

		/**
		 * TODO Platform
		 * 添加 post_content 参数
		 */
		/**
		 * Add an external image
		 *
		 * @param $url
		 * @param $image_id
		 * @param string $parent_id
		 *
		 * @return array
		 */
		public static function add_image( $url, &$image_id, $post_content = '', $parent_id = '' ) {
			$result = array(
				'url'       => $url,
				'message'   => '',
				'status'    => 'error',
				'id'        => '',
				'edit_link' => '',
			);

			$url = sanitize_trackback_urls( $url );
			$url = wp_http_validate_url( $url );

			if ( ! $url ) {
				$result['message'] = esc_html__( 'Invalid image URL', 'exmage-wp-image-links' );

				return $result;
			}

			$width              = $height = 800;
			$is_valid_image_url = self::is_image_url_valid( $url, $width, $height );
			if ( $is_valid_image_url ) {
				$url   = self::process_image_url( $url, $image_id, $is_ali_cdn );
				$exist = attachment_url_to_postid( $url );
				if ( ! $exist ) {
					$check_filetype   = wp_check_filetype( basename( $url ), null );
					$attachment_image = array(
						'post_title'     => apply_filters( 'exmage_insert_attachment_image_name', basename( $image_id ), $image_id, $url, $parent_id ),
						'post_content'   => $post_content,
						'post_mime_type' => empty( $check_filetype['type'] ) ? 'image/url' : $check_filetype['type'],
						'guid'           => strlen( $url ) > 255 ? '' : $url,//guid is varchar(255)
						'post_status'    => 'inherit'
					);
					if ( class_exists( 'WPML_Media_Attachments_Duplication' ) ) {
						/*Prevent WPML from duplicating this external image*/
						exmage_remove_filter( 'add_attachment', 'WPML_Media_Attachments_Duplication', 'save_attachment_actions' );
						exmage_remove_filter( 'add_attachment', 'WPML_Media_Attachments_Duplication', 'save_translated_attachments' );
					}
					$attachment_id = wp_insert_attachment( $attachment_image, $url, $parent_id, true );
					if ( $attachment_id && ! is_wp_error( $attachment_id ) ) {
						self::update_attachment_metadata( $attachment_id, $url, $is_ali_cdn, $width, $height );
						$result['id']        = $attachment_id;
						$result['status']    = 'success';
						$result['message']   = esc_html__( 'Successful', 'exmage-wp-image-links' );
						$result['edit_link'] = esc_url( add_query_arg( array(
							'post'   => $attachment_id,
							'action' => 'edit'
						), admin_url( 'post.php' ) ) );
					} else {
						$result['message'] = $attachment_id->get_error_message();
					}
				} else {
					$edit_link           = add_query_arg( array(
						'post'   => $exist,
						'action' => 'edit'
					), admin_url( 'post.php' ) );
					$result['id']        = $exist;
					$result['message']   = esc_html__( 'Image exists', 'exmage-wp-image-links' );
					$result['edit_link'] = esc_url( $edit_link );
				}
			} else {
				$result['message'] = esc_html__( 'Invalid or not supported image URL', 'exmage-wp-image-links' );
			}

			return $result;
		}

		/**
		 * Update metadata
		 *
		 * @param $attachment_id
		 * @param $url
		 * @param $is_ali_cdn
		 * @param $width
		 * @param $height
		 */
		private static function update_attachment_metadata( $attachment_id, $url, $is_ali_cdn, $width, $height ) {
			if ( ! get_post_meta( $attachment_id, '_exmage_external_url', true ) ) {
				update_post_meta( $attachment_id, '_exmage_external_url', $url );
			}
			$wp_sizes    = self::get_sizes();
			$image_sizes = array();
			$pathinfo    = pathinfo( $url );
			if ( ! empty( $pathinfo['extension'] ) ) {
				$common_sizes = $is_ali_cdn ? array(
					'thumbnail'    => 50,
					'small1'       => 100,
					'small2'       => 200,
					'medium'       => 350,
					'medium_large' => 640
				) : apply_filters( 'exmage_get_supported_image_sizes', array(
					'thumbnail'    => 150,
					'medium'       => 300,
					'medium_large' => 768,
					'large'        => 1024
				), $url );
				foreach ( $common_sizes as $size_name => $size_width ) {
					/**
					 * TODO Platform 只使用原始尺寸
					 */
					$size_url = $url;
					$image_sizes[ $size_name ] = array(
						'url'    => $size_url,
						'width'  => $size_width,
						'height' => $size_width
					);
				}
			}
			if ( ! isset( $image_sizes['large'] ) ) {
				$image_sizes['large'] = array(
					'url'    => $url,
					'width'  => $width,
					'height' => $height
				);
			} else {
				$image_sizes['full'] = array(
					'url'    => $url,
					'width'  => $width,
					'height' => $height
				);
			}
			/*Build attachment metadata*/
			$attach_data = array(
				'file'       => $url,
				'width'      => $width,
				'height'     => $height,
				'sizes'      => array(),
				'image_meta' => array(
					'aperture'          => '0',
					'credit'            => '',
					'camera'            => '',
					'caption'           => '',
					'created_timestamp' => '0',
					'copyright'         => '',
					'focal_length'      => '0',
					'iso'               => '0',
					'shutter_speed'     => '0',
					'title'             => '',
					'orientation'       => '0',
					'keywords'          => array(),
				),
			);

			foreach ( $wp_sizes as $size => $props ) {
				$select_size = self::select_size( $props, $image_sizes );
				if ( ! empty( $select_size ) ) {
					$check_filetype                  = wp_check_filetype( basename( $select_size['url'] ), null );
					$attach_data['sizes']["{$size}"] = array(
						'file'      => basename( $select_size['url'] ),
						'width'     => $select_size['width'],
						'height'    => $select_size['height'],
						'mime-type' => $check_filetype['type'],
					);
				}
			}
			if ( isset( $attach_data['sizes']['full'] ) ) {
				unset( $attach_data['sizes']['full'] );
			}
			wp_update_attachment_metadata( $attachment_id, $attach_data );
		}

		/**
		 * Generate sizes if any
		 *
		 * @return array
		 */
		private static function get_sizes() {
			global $_wp_additional_image_sizes;

			$sizes = array();

			foreach ( get_intermediate_image_sizes() as $_size ) {
				if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
					$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
					$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
					$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
				} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
					$sizes[ $_size ] = array(
						'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
						'height' => $_wp_additional_image_sizes[ $_size ]['height'],
						'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
					);
				}
			}

			return $sizes;
		}

		/**
		 * @param $size
		 * @param array $image_sizes
		 *
		 * @return bool|mixed
		 */
		private static function select_size( $size, $image_sizes = array() ) {
			if ( empty( $image_sizes ) ) {
				return $size;
			}

			$min_size = $max_size = false;
			foreach ( $image_sizes as $props ) {
				if ( (int) $size['width'] == (int) $props['width'] ) {
					return $props;
				}

				if ( intval( $size['width'] ) < intval( $props['width'] ) && ( ! $min_size || intval( $min_size['width'] ) > intval( $props['width'] ) ) ) {
					$min_size = $props;
				}

				if ( ! $max_size || ( intval( $max_size['width'] ) < intval( $props['width'] ) ) ) {
					$max_size = $props;
				}
			}

			return ! $min_size ? $max_size : $min_size;
		}

		/**
		 * Enqueue needed scripts
		 */
		public function admin_enqueue_scripts() {
			global $pagenow;
			if ( $pagenow === 'upload.php' || $pagenow === 'media-new.php' || wp_script_is( 'media-editor' ) ) {
				wp_enqueue_style( 'exmage-media', EXMAGE_WP_IMAGE_LINKS_CSS . 'media.css' );
				wp_enqueue_script( 'exmage-script', EXMAGE_WP_IMAGE_LINKS_JS . 'exmage.js', array( 'jquery' ), EXMAGE_WP_IMAGE_LINKS_VERSION );
				wp_localize_script( 'exmage-script', 'exmage_admin_params', array(
					'ajaxurl'                    => admin_url( 'admin-ajax.php' ),
					'uploadurl'                  => admin_url( 'async-upload.php' ),
					'post_id'                    => get_the_ID(),
					'_exmage_ajax_nonce'         => wp_create_nonce( 'exmage_ajax_handle_url' ),
					'i18n_select_existing_image' => esc_html__( 'Click here to select this image', 'exmage-wp-image-links' ),
				) );
			}
		}

		/**
		 * Enqueue script wherever media is used
		 */
		public function wp_enqueue_media() {
			wp_enqueue_script( 'exmage-media', EXMAGE_WP_IMAGE_LINKS_JS . 'media.js', array( 'jquery' ), EXMAGE_WP_IMAGE_LINKS_VERSION );
		}

		/**
		 *
		 */
		public function init() {
			load_plugin_textdomain( 'exmage-wp-image-links' );
			$this->load_plugin_textdomain();
            /**
             * TODO Platform
             * 移除广告
             */
			/*if ( class_exists( 'VillaTheme_Support' ) ) {
				new VillaTheme_Support(
					array(
						'support'    => 'https://wordpress.org/support/plugin/exmage-wp-image-links/',
						'docs'       => 'http://docs.villatheme.com/?item=exmage',
						'review'     => 'https://wordpress.org/support/plugin/exmage-wp-image-links/reviews/?rate=5#rate-response',
						'pro_url'    => '',
						'css'        => EXMAGE_WP_IMAGE_LINKS_CSS,
						'image'      => EXMAGE_WP_IMAGE_LINKS_IMAGES,
						'slug'       => 'exmage-wp-image-links',
						'menu_slug'  => '',
						'version'    => EXMAGE_WP_IMAGE_LINKS_VERSION,
						'survey_url' => 'https://script.google.com/macros/s/AKfycbzppiR3CI9GOk_xRYllxRzH-6cuWEZAlJ3VQOu1l2fJ11mrFCgib_cNlvjxfBIGwGFh/exec'
					)
				);
			}*/
		}

		/**
		 *
		 */
		public function load_plugin_textdomain() {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'exmage-wp-image-links' );
			load_textdomain( 'exmage-wp-image-links', EXMAGE_WP_IMAGE_LINKS_LANGUAGES . "exmage-wp-image-links-$locale.mo" );
			load_plugin_textdomain( 'exmage-wp-image-links', false, EXMAGE_WP_IMAGE_LINKS_LANGUAGES );
		}

		/**
		 * @param $url
		 * @param $id
		 *
		 * @return mixed
		 */
		public function wp_get_attachment_url( $url, $id ) {
			if ( get_post_meta( $id, '_exmage_imported', true ) ) {
				return $url;
			}
			if ( ! get_post_meta( $id, '_exmage_external_url', true ) ) {
				return $url;
			}
			$post = get_post( $id );
			if ( $post && 'attachment' === $post->post_type ) {
				$_wp_attached_file = get_post_meta( $id, '_wp_attached_file', true );
				if ( $_wp_attached_file ) {
					if ( substr( $_wp_attached_file, 0, 7 ) === "http://" || substr( $_wp_attached_file, 0, 8 ) === "https://" ) {
						$url = $_wp_attached_file;
					}
				}
			}

			return $url;
		}

		/**
		 * @param $sources
		 * @param $size_array
		 * @param $image_src
		 * @param $image_meta
		 * @param $attachment_id
		 *
		 * @return mixed
		 */
		public function wp_calculate_image_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
			if ( get_post_meta( $attachment_id, '_exmage_imported', true ) ) {
				return $sources;
			}
			if ( ! get_post_meta( $attachment_id, '_exmage_external_url', true ) ) {
				return $sources;
			}
			if ( $sources ) {
				$upload_dir    = wp_get_upload_dir();
				$image_baseurl = trailingslashit( $upload_dir['baseurl'] );
				if ( is_ssl() && 'https' !== substr( $image_baseurl, 0, 5 ) && ! empty( $_SERVER['HTTP_HOST'] ) && parse_url( $image_baseurl, PHP_URL_HOST ) === $_SERVER['HTTP_HOST'] ) {
					$image_baseurl = set_url_scheme( $image_baseurl, 'https' );
				}
				$_wp_attached_file = get_post_meta( $attachment_id, '_wp_attached_file', true );
				foreach ( $sources as &$src ) {
					$pos = strpos( $_wp_attached_file, 'wp-content/uploads/' );
					if ( false !== $pos ) {
						$src['url'] = str_replace( $image_baseurl, substr( $_wp_attached_file, 0, $pos - 1 ) . '/wp-content/uploads/', $src['url'] );
					} else {
						$src['url'] = str_replace( $image_baseurl, '', $src['url'] );
					}
				}
			}

			return $sources;
		}

		/**
		 * @return mixed|void
		 */
		private static function get_supported_mime_types() {
			return apply_filters( 'exmage_get_supported_mime_types', array(
				'image/png',
				'image/jpeg',
				'image/jpg',
				'image/gif',
				'image/webp',
			) );
		}

		public function search_exmage_url_when_import_product( &$q ) {
			if ( ! empty( $q->query_vars['meta_query'] ) ) {
				$file = '';
				foreach ( $q->query_vars['meta_query'] as $key => &$mt_qr ) {
					if ( ! empty( $mt_qr['key'] ) && $mt_qr['key'] == '_wc_attachment_source' ) {
						$file = $mt_qr['value'];
						break;
					}
				}
				if ( $file ) {
					$q->query_vars['meta_query'][] = [
						'key'     => '_exmage_external_url',
						'value'   => $file,
						'compare' => 'LIKE',
					];

					$q->query_vars['meta_query']['relation'] = 'OR';
				}
			}

		}
	}
}

new EXMAGE_WP_IMAGE_LINKS();