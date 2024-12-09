<?php
/**
 * Branda Images class.
 *
 * @package Branda
 * @subpackage Utilites
 */
if ( ! class_exists( 'Branda_Images' ) ) {

	class Branda_Images extends Branda_Helper {
		protected $option_name = 'ub_images';
		private $filesize      = array();
		private $quant         = array();
		/**
		 * WP default fav
		 *
		 * @var string
		 *
		 * @since 1.8.1
		 */
		private $_default_fav = '';

		public function __construct() {
			parent::__construct();
			$this->set_roles( false, false );
			$this->module = 'images';
			$this->quant  = array(
				'TB' => TB_IN_BYTES,
				'GB' => GB_IN_BYTES,
				'MB' => MB_IN_BYTES,
				'KB' => KB_IN_BYTES,
				'B'  => 1,
			);
			/**
			 * hooks
			 */
			add_filter( 'ultimatebranding_settings_images', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_images_process', array( $this, 'update' ) );
			add_filter( 'ultimatebranding_settings_images_process', array( $this, 'update_blogs_icons' ) );
			add_filter( 'ultimatebranding_settings_images_process', array( $this, 'update_upload_limits' ), 11 );
			/**
			 * upgrade options
			 *
			 * @since 3.0.0
			 */
			add_action( 'init', array( $this, 'upgrade_options' ) );
			/******************************************************************
			 * Hooks: Favicons                                                *
			 */
			if ( function_exists( 'has_site_icon' ) ) {
				add_filter( 'get_site_icon_url', array( $this, 'get_site_icon_url' ), 10, 3 );
				add_action( 'wp_head', array( $this, 'change_blavatar_icon' ) );
				add_action( 'admin_head', array( $this, 'change_blavatar_icon' ) );
			}
			/**
			 * allow to upload svg and ico
			 */
			add_filter( 'upload_mimes', array( $this, 'upload_mimes' ) );
			/**
			 * Favicons on Sites screen.
			 */
			add_filter( 'wpmu_blogs_columns', array( $this, 'wpmu_blogs_columns' ) );
			add_action( 'admin_head-sites.php', array( $this, 'wpmu_blogs_columns_css' ) );
			add_action( 'manage_sites_custom_column', array( $this, 'manage_sites_custom_column' ), 10, 2 );
			/**
			 * AJAX
			 */
			add_action( 'wp_ajax_branda_images_search_sites', array( $this, 'ajax_search_subsite' ) );
			add_action( 'wp_ajax_branda_images_delete_subsite', array( $this, 'ajax_delete_subsite' ) );
			/******************************************************************
			 * Hooks: Images Upload Limit                                     *
			 */
			// Hooking into upload prefilter to validate the uploaded image file.
			add_filter( 'upload_size_limit', array( $this, 'upload_size_limit' ), 10, 3 );
			/**
			 * add options names
			 *
			 * @since 2.1.0
			 */
			add_filter( 'ultimate_branding_options_names', array( $this, 'add_options_names' ) );
			/**
			 * Add template
			 *
			 * @since 3.0,0
			 */
			add_filter( 'branda_get_module_content', array( $this, 'add_template' ), 10, 2 );
			/**
			 * Remove "Site Icon' from Customizer
			 *
			 * @since 3.1.0
			 */
			add_action( 'customize_register', array( $this, 'remove_styles_sections' ), 33, 1 );
			/**
			 * add settings
			 *
			 * @since x.x.x
			 */
			add_filter( 'branda_localize_script', array( $this, 'localize_script' ), 10, 2 );
		}

		/**
		 * Upgrade option
		 *
		 * @since 3.0.0
		 */
		public function upgrade_options() {
			$limits = array();
			foreach ( $this->roles as $slug => $role ) {
				$option_name = sprintf( 'ub_img_upload_filesize_%s', $slug );
				$value       = branda_get_option( $option_name );
				if ( empty( $value ) ) {
					continue;
				}
				$limits[ $slug ] = $value * KB_IN_BYTES;
				branda_delete_option( $option_name );
			}
			if ( ! empty( $limits ) ) {
				$this->set_value( 'images', 'override', 'on' );
				$this->set_value( 'images', 'limits', $limits );
			}
			/**
			 * Favicons
			 */
			$update = false;
			$value  = $this->get_value();
			$data   = branda_get_option( 'ub_favicons' );
			if ( ! empty( $data ) ) {
				if ( ! isset( $value['favicon'] ) ) {
					$value['favicon'] = array();
				}
				if ( isset( $data['global'] ) ) {
					$update = true;
					if ( isset( $data['global']['use_as_default'] ) ) {
						$value['favicon']['subsites'] = 'off' === $data['global']['override'] ? 'force' : 'custom';
					}
					if ( isset( $data['global']['favicon'] ) ) {
						$value['favicon']['favicon'] = $data['global']['favicon'];
					}
					$value['subsites']      = array(
						'id'      => array(),
						'favicon' => array(),
					);
					$value['configuration'] = array(
						'subsites' => array(),
					);
					if ( isset( $data['sites'] ) && is_array( $data['sites'] ) ) {
						foreach ( $data['sites'] as $key => $val ) {
							if ( preg_match( '/^blog_id_(\d+)$/', $key, $matches ) ) {
								$blog_id                        = $matches[1];
								$id                             = sprintf( 'blog_id_%d', $blog_id );
								$value['subsites']['id'][]      = $blog_id;
								$value['subsites']['favicon'][] = $val;
								$one                            = array(
									'blog_id' => $blog_id,
									'image'   => $val,
								);
								$key                            = sprintf( 'blog_id_%d_meta', $blog_id );
								if ( isset( $data['sites'][ $key ] ) ) {
									$one['meta'] = $data['sites'][ $key ];
								}
								$value['configuration']['subsites'][ $blog_id ] = $one;
							}
						}
					}
				}
				branda_delete_option( 'ub_favicons' );
			}
			if ( $update ) {
				$this->update_value( $value );
			}
		}

		protected function set_roles( $sort = true, $add_super = true ) {
			parent::set_roles( $sort, $add_super );

			if ( ! $this->is_network_admin && isset( $this->roles['super'] ) ) {
				unset( $this->roles['super'] );
			}
		}

		/**
		 * Set options
		 *
		 * @since 3.0.0
		 */
		protected function set_options() {
			$this->_default_fav = admin_url() . 'images/w-logo-blue.png';
			$button_add_args    = array(
				'text'    => __( 'Add', 'ub' ),
				'classes' => array(
					$this->get_name( 'subsite-add' ),
				),
			);
			/**
			 * Container class
			 */
			$container_class = array();
			$value           = $this->get_value( 'images', 'override' );
			if ( empty( $value ) ) {
				$container_class[] = 'hidden';
			}
			/**
			 * Override Description
			 */
			$override_description = __( 'Choose whether the favicon defined here should override the site icon defined in <b>Appearance &gt; Customize</b>.', 'ub' );
			if ( ! $this->is_network ) {
				$uba                  = branda_get_uba_object();
				$module_data          = $uba->get_module_by_module( $this->module );
				$images               = add_query_arg(
					array(
						'page'   => 'branding_group_' . $module_data['group'],
						'module' => $this->module,
					),
					'admin.php'
				);
				$override_description = sprintf(
					__( 'Choose whether the favicon defined here should override the site icon defined in <b><a href="%1$s">Appearance</a> &gt; <a href="%2$s">Customize</a></b>.', 'ub' ),
					admin_url( 'themes.php' ),
					admin_url( add_query_arg( 'return', urlencode( $images ), 'customize.php' ) )
				);
			}
			/**
			 * Options
			 */
			$options = array(
				'favicon' => array(
					'title'       => __( 'Favicon', 'ub' ),
					'description' => $this->is_network ? __( 'You can override the favicons of all the websites on your network.', 'ub' ) : __( 'You can override the favicon of your website defined in <strong>Appearance &gt; Customization</strong>.', 'ub' ),
					'fields'      => array(
						'favicon'  => array(
							'label'       => $this->is_network ? __( 'Main Site', 'ub' ) : __( 'Favicon', 'ub' ),
							'type'        => 'media',
							'master'      => $this->get_name( 'favicons-override' ),
							'description' => array(
								'content'  => $this->is_network ? __( 'Override the favicon of your main site here. Preferred size of favicon is 32x32px.', 'ub' ) : __( 'Preferred size of favicon is 32x32px.', 'ub' ),
								'position' => $this->is_network ? 'top' : 'bottom',
							),
						),
						'search'   => array(
							'id'           => $this->get_name( 'search' ),
							'type'         => 'select2-ajax',
							'small-select' => true,
							'master'       => $this->get_name( 'subsites' ),
							'master-value' => 'custom',
							'display'      => 'sui-tab-content',
							'before'       => '<div class="sui-row"><div class="sui-col-sm-9">',
							'after'        => '</div><div class="sui-col-sm-3">' . $this->button( $button_add_args ) . '</div></div>',
							'placeholder'  => esc_attr__( 'Search the subsite', 'ub' ),
							'data'         => array(
								'user-id' => get_current_user_id(),
								'nonce'   => wp_create_nonce( $this->get_nonce_action_name( 'search' ) ),
								'action'  => 'branda_images_search_sites',
								'extra'   => 'branda_images_add_already_used_sites',
							),
							'network-only' => true,
						),
						'list'     => array(
							'type'         => 'callback',
							'callback'     => array( $this, 'get_list' ),
							'master'       => $this->get_name( 'subsites' ),
							'master-value' => 'custom',
							'display'      => 'sui-tab-content',
							'network-only' => true,
							'classes'      => array( 'branda-images-subsite-add' ),
						),
						'subsites' => array(
							'type'         => 'sui-tab',
							'description'  => array(
								'content' => __( 'Choose whether to use the main site’s favicon as a default favicon for all the subsites or add a custom favicon for each subsite.', 'ub' ),
							),
							'label'        => __( 'Subsites', 'ub' ),
							'options'      => array(
								'force'  => __( 'Main Site’s Favicon', 'ub' ),
								'custom' => __( 'Custom', 'ub' ),
							),
							'default'      => 'force',
							'slave-class'  => $this->get_name( 'subsites' ),
							'network-only' => true,
						),
						'override' => array(
							'type'        => 'sui-tab',
							'label'       => __( 'Override customizer icon', 'ub' ),
							'options'     => array(
								'disabled' => __( 'Leave it', 'ub' ),
								'enabled'  => __( 'Override', 'ub' ),
							),
							'default'     => 'disabled',
							'description' => array(
								'content'  => $override_description,
								'position' => 'bottom',
							),
						),
					),
				),
				'images'  => array(
					'title'       => __( 'Image Filesize Limit', 'ub' ),
					'description' => sprintf( __( 'Override the default WordPress upload limit of %dMb for different user roles.', 'ub' ), round( $this->get_wp_limit() / 1000 ) ),
					'fields'      => array(
						'override' => array(
							'checkbox_label' => __( 'Override upload limit', 'ub' ),
							'type'           => 'checkbox',
							'classes'        => array( 'switch-button' ),
							'slave-class'    => $this->get_name( 'images-override' ),
						),
						'limits'   => array(
							'type'              => 'callback',
							'callback'          => array( $this, 'limits' ),
							'master'            => $this->get_name( 'images-override' ),
							'container-classes' => $container_class,
							'description'       => array(
								'position' => 'top',
								'content'  => __( 'Set your own limit on the upload size of images for different user roles.', 'ub' ),
							),
						),
					),
				),
			);
			if ( $this->is_network ) {
				$has_susbsite_configuration = false;
				if ( ! is_network_admin() ) {
					$subsite = apply_filters( 'branda_module_check_for_subsite', false, $this->module, null );
					if ( $subsite ) {
						unset( $options['favicon'] );
					}
				}
			} else {
				unset( $options['favicon']['fields']['subsites'] );
			}
			$this->options = $options;
		}

		/******************************************************************
		 * Favicons                                                       *
		 ******************************************************************/

		/**
		 * Grab subsites data and save it.
		 *
		 * @since 3.0.0
		 */
		public function update_blogs_icons( $status ) {
			if (
				! $status
				|| ! isset( $_POST['simple_options'] )
				|| ! isset( $_POST['simple_options']['subsites'] )
			) {
				return $status;
			}
			$input    = $_POST['simple_options']['subsites'];
			$subsites = array();
			if (
				isset( $input )
				&& is_array( $input )
				&& isset( $input['id'] )
				&& is_array( $input['id'] )
				&& isset( $input['favicon'] )
				&& is_array( $input['favicon'] )
			) {
				$favicon = $input['favicon'];
				foreach ( $input['id'] as $index => $value ) {
					if ( ! isset( $favicon[ $index ] ) ) {
						continue;
					}
					$image = wp_get_attachment_image_src( $favicon[ $index ], array( 512, 512 ) );
					if ( empty( $image ) ) {
						continue;
					}
					$one                = array(
						'blog_id' => $value,
						'image'   => $favicon[ $index ],
						'meta'    => $image,
					);
					$subsites[ $value ] = $one;
				}
			}
			$this->set_value( 'configuration', 'subsites', $subsites );
			$this->set_value( 'subsites', null );
			return $status;
		}

		/**
		 * Calculate favicon
		 *
		 * @since 3.0.0
		 */
		private function calculate_favicon( $url, $size, $favicon ) {
			$switched_blog = false;

			if ( ! empty( $favicon ) ) {
				if ( $this->is_network && ! is_main_site() ) {
					switch_to_blog( 1 );
					$switched_blog = true;
				}
				if ( $size >= 512 ) {
					$size_data = 'full';
				} else {
					$size_data = array( $size, $size );
				}
				$url = wp_get_attachment_image_url( $favicon, $size_data );
			}

			if ( $switched_blog ) {
				restore_current_blog();
			}

			return $url;
		}

		/**
		 * Sets site url based on definitions
		 *
		 * @param $url
		 * @param $size
		 * @param $blog_id
		 * @return mixed
		 */
		public function get_site_icon_url( $url, $size, $blog_id ) {
			$override = $this->get_value( 'favicon', 'override', 'disabled' );
			if ( $url && 'disabled' === $override ) {
				return $url;
			}
			$main_favicon = $this->get_value( 'favicon', 'favicon', false );
			if ( ! $this->is_network || is_main_site( $blog_id ) ) {
				return $this->calculate_favicon( $url, $size, $main_favicon );
			}
			/**
			 * subsites
			 */
			$subsites = $this->get_value( 'favicon', 'subsites', 'off' );
			if ( 'force' === $subsites ) {
				// use main site's favicon
				return $this->calculate_favicon( $url, $size, $main_favicon );
			}
			if ( empty( $blog_id ) ) {
				$blog_id = get_current_blog_id();
			}
			$value       = $this->get_value( 'configuration', 'subsites', array() );
			$sub_favicon = '';
			if (
				is_array( $value )
				&& isset( $value[ $blog_id ] )
			) {
				if ( isset( $value[ $blog_id ]['image'] ) ) {
					$sub_favicon = $value[ $blog_id ]['image'];
				}
			}
			return $this->calculate_favicon( $url, $size, $sub_favicon );
		}

		/**
		 * Add ability to upload SVG and ICO files.
		 *
		 * @since 1.8.6
		 */
		public function upload_mimes( $mime_types ) {
			$mime_types['ico'] = 'image/x-icon';
			return $mime_types;
		}

		/**
		 * Icons on sites list
		 *
		 * @since 1.8.8
		 */
		public function wpmu_blogs_columns( $columns ) {
			$new = array();
			foreach ( $columns as $key => $value ) {
				$new[ $key ] = $value;
				if ( 'blogname' == $key ) {
					$new[ $this->option_name ] = __( 'Favicon', 'ub' );
				}
			}
			return $new;
		}

		/**
		 * Icons on sites list
		 *
		 * @since 1.8.8
		 */
		public function manage_sites_custom_column( $column, $site_id ) {
			if ( $this->option_name !== $column ) {
				return;
			}
			$favicon = $this->get_favicon( $site_id );
			$text    = esc_html__( 'Change', 'ub' );
			if ( empty( $favicon ) ) {
				$text = esc_html__( 'Set', 'ub' );
			} else {
				printf( '<img src="%s" />', esc_url( $favicon ) );
			}
			$url = add_query_arg(
				array(
					'page'   => 'branding_group_utilities',
					'module' => 'images',
				),
				network_admin_url( 'admin.php' )
			);
			echo '<div class="row-actions">';
			printf(
				'<a href="%s">%s</a>',
				esc_url( $url ),
				$text
			);
			echo '</div>';
		}

		/**
		 * Icons on sites list
		 *
		 * @since 1.8.8
		 */
		public function wpmu_blogs_columns_css() {
			printf( '<style type="text/css" id="%s">', $this->get_name( 'column' ) );
			printf( '.column-%s{width:10%%;min-width:34px;}', $this->option_name );
			printf( '.column-%s img{max-width:24px;max-height:24px;display:block;margin-left:10px;}', $this->option_name );
			echo '</style>';
			echo PHP_EOL;
		}

		/**
		 * Changes icons of the subsites in the admin menus
		 */
		public function change_blavatar_icon() {
			$css = '';
			global $wp_admin_bar;
			if ( ! isset( $wp_admin_bar->user, $wp_admin_bar->user->blogs ) ) {
				return; }
			foreach ( (array) $wp_admin_bar->user->blogs as $blog ) {
				$icon = $this->get_site_icon_url( false, 32, $blog->userblog_id );
				if ( empty( $icon ) ) {
					continue;
				}
				$css .= sprintf(
					'#wpadminbar #wp-admin-bar-blog-%d .blavatar:before{',
					$blog->userblog_id
				);
				$css .= 'content:" ";';
				$css .= sprintf( 'background:transparent url(%s) no-repeat 50%%;', $icon );
				$css .= 'background-size:contain;';
				$css .= '}';
				$css .= PHP_EOL;
			}
			if ( empty( $css ) ) {
				return;
			}
			printf( '<style type="text/css" id="%s">', $this->get_name( 'blavatar' ) );
			echo PHP_EOL;
			echo $css;
			echo '</style>';
			echo PHP_EOL;
		}

		/**
		 * Retrieves favicon based on blog_id
		 *
		 * @param string $blog_id
		 * @param bool   $add_tail
		 *
		 * @since 1.8.1
		 *
		 * @return string
		 */
		public function get_favicon( $blog_id = null ) {
			return $this->get_site_icon_url( false, 32, $blog_id );
		}

		/**
		 * List of existing elements.
		 *
		 * @since 3.0.0
		 */
		public function get_list() {
			$content      = '';
			$notice_class = '';
			/**
			 * list
			 */
			$items = $this->get_value( 'configuration', 'subsites' );
			if ( is_array( $items ) && ! empty( $items ) ) {
				$notice_class = 'hidden';
				foreach ( $items as $item ) {
					$details = get_blog_details( $item['blog_id'] );
					if ( empty( $details ) || ! is_object( $details ) ) {
						continue;
					}
					$item['blog_url']   = $details->siteurl;
					$item['blog_title'] = $details->blogname;
					$content           .= $this->get_list_one_row( $item, true );
					$content           .= $this->get_dialog_delete( $item['blog_id'] );
				}
			}
			$content .= Branda_Helper::sui_notice(
				__( 'You haven\'t added any subsite to override the favicon. Search the subsite click on add.', 'ub' ),
				$notice_class
			);
			return $content;
		}

		/**
		 * AJAX delete subsite
		 *
		 * @since 3.0.0
		 */
		public function ajax_delete_subsite() {
			$id           = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT );
			$nonce_action = $this->get_nonce_action( $id, 'delete' );
			$this->check_input_data( $nonce_action, array( 'id' ) );
			$value = $this->get_value( 'configuration', 'subsites', array() );
			if ( isset( $value[ $id ] ) ) {
				unset( $value[ $id ] );
				$this->set_value( 'configuration', 'subsites', $value );
			}
			wp_send_json_success();
		}

		/**
		 * helper to get one row
		 *
		 * @since 3.0.0
		 */
		public function get_list_one_row( $data, $print_js_data = false ) {
			$id       = $this->get_name( 'subsite' );
			$image_id = '';
			$args     = array(
				'only-icon' => true,
				'sui'       => array( 'red' ),
				'icon'      => 'trash',
			);
			if ( '{{{data.id}}}' !== $data['blog_id'] ) {
				$image_id     = preg_replace(
					'/\-/',
					'_',
					sprintf(
						'%s_%s',
						sanitize_title( $id ),
						sanitize_title( $data['blog_id'] )
					)
				);
				$args['data'] = array(
					'modal-open' => $this->get_nonce_action( $data['blog_id'], 'delete' ),
				);
			} else {
				$args['class'] = $this->get_name( 'delete' );
			}
			$content  = sprintf(
				'<div class="sui-row simple-option simple-option-media" data-blog-id="%s" id="%s-container-%s" >',
				esc_attr( $data['blog_id'] ),
				esc_attr( $id ),
				esc_attr( $data['blog_id'] )
			);
			$content .= sprintf(
				'<input type="hidden" name="simple_options[subsites][id][]" value="%s" />',
				esc_attr( $data['blog_id'] )
			);
			$content .= '<div class="sui-col">';
			$content .= sprintf(
				'<span class="sui-label sui-tooltip" data-tooltip="%s">%s</span>',
				esc_attr( $data['blog_url'] ),
				esc_html( $data['blog_title'] )
			);
			$content .= sprintf(
				'<div class="images" id="%s"></div>',
				esc_attr( $image_id )
			);
			$content .= '</div>';
			$content .= '<div class="sui-actions-right ub-delete-image">';
			$content .= $this->button( $args );
			$content .= '</div>';
			$content .= '</div>';
			if ( $print_js_data ) {
				$output   = array(
					'type'   => 'media',
					'images' => array(
						array(
							'id'              => 'favicon',
							'image_id'        => sprintf( 'attachment-id-%d', $data['image'] ),
							'section_key'     => 'subsites',
							'value'           => $data['image'],
							'image_src'       => $data['meta'][0],
							'file_name'       => basename( $data['meta'][0] ),
							'disabled'        => '',
							'container_class' => 'sui-has_file',
						),
					),
				);
				$content .= '<script type="text/javascript">';
				$content .= sprintf( '_%s', esc_attr( $image_id ) );
				$content .= '=';
				$content .= json_encode( $output );
				$content .= ';</script>';
			}
			return $content;
		}

		/**
		 * Add WP Template
		 *
		 * @since 3.0.0
		 *
		 * @param string $content Current module content.
		 * @param array  $module Current module.
		 */
		public function add_template( $content, $module ) {
			if ( $this->module !== $module['module'] ) {
				return $content;
			}
			$args     = array(
				'blog_id'    => '{{{data.id}}}',
				'blog_url'   => '{{{data.subtitle}}}',
				'blog_title' => '{{{data.title}}}',
			);
			$content .= sprintf(
				'<script type="text/html" id="tmpl-%s">%s</script>',
				$this->get_name( 'subsite' ),
				$this->get_list_one_row( $args )
			);
			return $content;
		}

		/******************************************************************
		 * Images Limit                                                   *
		 ******************************************************************/
		/**
		 * Add option names
		 *
		 * @since 2.1.0
		 */
		public function add_options_names( $options ) {
			foreach ( $this->roles as $slug => $title ) {
				$options[] = $this->get_name( $slug );
			}
			return $options;
		}

		public function update_upload_limits( $status ) {
			$nonce_action = $this->get_name( 'limit' );
			if ( ! isset( $_POST[ $nonce_action ] ) ) {
				return false;
			}
			if ( ! wp_verify_nonce( $_POST[ $nonce_action ], $nonce_action ) ) {
				return false;
			}
			if ( ! isset( $_POST['limits'] ) ) {
				return;
			}
			$raw = $limits = array();
			$max = round( wp_max_upload_size() );
			foreach ( $this->roles as $slug => $role ) {
				if (
					! isset( $_POST['limits'][ $slug ] )
					|| ! isset( $_POST['limits'][ $slug ]['amount'] )
					|| ! isset( $_POST['limits'][ $slug ]['quantity'] )
				) {
					continue;
				}

				$amount = ! empty( $_POST['limits'][ $slug ]['amount'] ) ? intval( $_POST['limits'][ $slug ]['amount'] ) : 0;
				$quant  = ! empty( $_POST['limits'][ $slug ]['quantity'] ) ? sanitize_text_field( $_POST['limits'][ $slug ]['quantity'] ) : '';

				$value  = $amount * $this->quant[ $quant ];
				if ( $max <= $value ) {
					continue;
				}
				$raw[ $slug ]    = array(
					'amount' => $amount,
					'quant'  => $quant,
				);
				$limits[ $slug ] = $value;
			}
			$this->set_value( 'images', 'limits', $limits );
			$this->set_value( 'images', 'limits_raw', $raw );
			return true;
		}

		public function get_fs_limit( $current_limit ) {
			$status = $this->get_value( 'images', 'override', 'off' );
			if ( 'on' !== $status ) {
				return $current_limit;
			}
			$limits       = $this->get_value( 'images', 'limits' );
			$current_user = wp_get_current_user();
			if ( ! $limits ) {
				return $current_limit;
			}
			$limit = 0;
			if ( ! isset( $limits['super'] ) && is_super_admin() && ! is_network_admin() ) {
				// Get limit from network settings for Network admins because it's not possible to override this option from subsites
				$network_settings = branda_get_option( 'ub_images', false, 'normal', true );
				if ( isset( $network_settings['images']['limits']['super'] ) ) {
					$limits['super'] = $network_settings['images']['limits']['super'];
				}
			}
			foreach ( $limits as $role => $role_limit ) {
				if ( in_array( $role, $current_user->roles, true ) ||
						'super' === $role && is_super_admin() ) {

					if ( empty( $role_limit ) ) {
						// return max limit if it sets as 0
						return $current_limit;
					}
					if ( $role_limit > $limit ) {
						$limit = $role_limit;
					}
				}
			}
			if ( 0 === $limit ) {
				return $current_limit;
			}
			return $limit;
		}

		public function get_wp_limit() {
			remove_filter( 'upload_size_limit', array( $this, 'upload_size_limit' ), 10, 3 );
			$size = round( wp_max_upload_size() );
			add_filter( 'upload_size_limit', array( $this, 'upload_size_limit' ), 10, 3 );
			return $size;
		}

		/**
		 * @since 1.9.2
		 */
		public function upload_size_limit( $size, $u_bytes, $p_bytes ) {
			$limit = $this->get_fs_limit( $size );
			return $limit;
		}

		/**
		 * Show limits
		 *
		 * @since 3.0.0
		 */
		public function limits() {
			$wp_max = $this->get_wp_limit();
			$max    = round( wp_max_upload_size() );
			$quant  = 1;
			foreach ( $this->quant as $unit => $size ) {
				if ( $max < $size ) {
					continue;
				}
				if ( $size > $quant ) {
					$quant = $size;
				}
			}
			$limits     = $this->get_value( 'images', 'limits' );
			$limits_raw = $this->get_value( 'images', 'limits_raw' );

			$content  = wp_nonce_field( $this->get_name( 'limit' ), $this->get_name( 'limit' ), false );
			$content .= '<div class="sui-border-frame">';
			foreach ( $this->roles as $slug => $title ) {
				$content    .= '<div class="sui-row">';
				$option_name = $this->get_name( $slug );
				$value       = $max;
				if ( isset( $limits[ $slug ] ) ) {
					$value = min( $limits[ $slug ], $max );
				}
				$content    .= '<div class="sui-col-xs-7">';
				$content    .= sprintf(
					'<span class="sui-label">%s</span>',
					esc_html( $title )
				);
				$local_unit  = '';
				$local_quant = 1;
				foreach ( $this->quant as $unit => $size ) {
					if ( $value < $size ) {
						continue;
					}
					if ( $size > $local_quant ) {
						$local_quant = $size;
						$local_unit  = $unit;
					}
				}
				$max_current_quant = floor( $wp_max / $local_quant );
				$raw               = array();
				if ( isset( $limits_raw[ $slug ] ) ) {
					$raw = $limits_raw[ $slug ];
				}
				$content .= '<div class="sui-form-field">';
				$content .= sprintf(
					'<input type="number" name="limits[%s][amount]" value="%d" min="0" max="%d" class="sui-form-control %s" step="1" />',
					esc_attr( $slug ),
					isset( $raw['amount'] ) ? $raw['amount'] : floor( $value / $local_quant ),
					$max_current_quant,
					esc_attr( $this->get_name( 'amount' ) )
				);
				$content .= '</div>';
				$content .= '</div>';
				$content .= '<div class="sui-col-xs-5">';
				$content .= '<span class="sui-label">&nbsp;</span>';
				$content .= $this->get_select_sizes( $slug, $value, $max, $local_unit, $wp_max, $raw );
				$content .= '</select>';
				$content .= '</div>';
				$content .= '</div>';
			}
			$content .= '</div>';
			return $content;
		}

		/**
		 * Get select by provided data.
		 *
		 * @since 3.0.0
		 *
		 * @param string  $slug Slug of a role.
		 * @param integer $value Current limit value.
		 * @param integer $max Current max limit value.
		 * @param string  $select_unit Currently selected unit.
		 *
		 * @return string $content HTML select string.
		 */
		private function get_select_sizes( $slug, $value, $max, $select_unit, $wp_max, $raw ) {
			$content = sprintf(
				'<select name="limits[%s][quantity]" class="%s">',
				esc_attr( $slug ),
				esc_attr( $this->get_name( 'quantity' ) )
			);
			foreach ( $this->quant as $unit => $size ) {
				if ( $wp_max < $size ) {
					continue;
				}
				$selected = selected( $select_unit, $unit, false );
				if ( isset( $raw['quant'] ) ) {
					$selected = selected( $raw['quant'], $unit, false );
				}
				$content .= sprintf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $unit ),
					$selected,
					esc_html( $unit )
				);
			}
			$content .= '</select>';
			return $content;
		}

		/**
		 * Remove "Site Icon' from Customizer
		 *
		 * @since 3.1.0
		 */
		public function remove_styles_sections( $wp_customize ) {
			$value = $this->get_value( 'favicon', 'override', 'disabled' );
			if ( 'disabled' === $value ) {
				return;
			}
			$wp_customize->remove_control( 'site_icon' );
		}

		/**
		 * add settings to localize
		 *
		 * @since x.x.x
		 */
		public function localize_script( $localize, $module ) {
			if ( $this->module !== $module ) {
				return $localize;
			}
			$max                = $this->get_wp_limit();
			$localize['quants'] = array(
				'TB' => array(
					'max'   => intval( $max / TB_IN_BYTES ),
					'quant' => TB_IN_BYTES,
				),
				'GB' => array(
					'max'   => intval( $max / GB_IN_BYTES ),
					'quant' => GB_IN_BYTES,
				),
				'MB' => array(
					'max'   => intval( $max / MB_IN_BYTES ),
					'quant' => MB_IN_BYTES,
				),
				'KB' => array(
					'max'   => intval( $max / KB_IN_BYTES ),
					'quant' => KB_IN_BYTES,
				),
				'B'  => array(
					'max'   => $max,
					'quant' => 1,
				),
			);
			return $localize;
		}
	}
}
new Branda_Images();
