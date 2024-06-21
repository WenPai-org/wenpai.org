<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'VillaTheme_Support' ) ) {

	/**
	 * Class VillaTheme_Support
	 * 1.1.8
	 */
	class VillaTheme_Support {
		protected $plugin_base_name;
		protected $ads_data;
		protected $version = '1.1.8';
		protected $data = [];

		public function __construct( $data ) {
			$this->data               = array();
			$this->data['support']    = $data['support'];
			$this->data['docs']       = $data['docs'];
			$this->data['review']     = $data['review'];
			$this->data['css_url']    = $data['css'];
			$this->data['images_url'] = $data['image'];
			$this->data['slug']       = $data['slug'];
			$this->data['menu_slug']  = $data['menu_slug'];
			$this->data['version']    = isset( $data['version'] ) ? $data['version'] : '1.0.0';
			$this->data['pro_url']    = isset( $data['pro_url'] ) ? $data['pro_url'] : '';
			$this->data['survey_url'] = isset( $data['survey_url'] ) ? $data['survey_url'] : '';
			$this->plugin_base_name   = "{$this->data['slug']}/{$this->data['slug']}.php";
			/**
			 * TODO Platform
			 * 移除广告
			 */
			//add_action( 'villatheme_support_' . $this->data['slug'], array( $this, 'villatheme_support' ) );
			//add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
			//add_action( 'admin_notices', array( $this, 'review_notice' ) );
			//add_action( 'admin_init', array( $this, 'hide_review_notice' ) );
			//add_action( 'admin_menu', array( $this, 'admin_menu' ), 9999 );
			//add_filter( 'plugin_action_links_' . $this->plugin_base_name, array( $this, 'link_to_pro' ) );
			//add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
			/*Admin ads notices*/
			//add_action( 'admin_init', array( $this, 'admin_init' ), 1 );
			/*Add toolbar*/
			//add_action( 'admin_bar_menu', array( $this, 'add_toolbar' ), 100 );

			/*if ( $this->data['survey_url'] ) {
				add_action( 'admin_footer', array( $this, 'deactivate_scripts' ) );
			}*/
		}

		public function admin_init() {
			$this->hide_notices();
			$villatheme_call = get_transient( 'villatheme_call' );
			if ( ! $villatheme_call || ! is_plugin_active( "{$villatheme_call}/{$villatheme_call}.php" ) ) {
				/*Make sure ads and dashboard widget show only once when multiple VillaTheme plugins are installed*/
				set_transient( 'villatheme_call', $this->data['slug'], DAY_IN_SECONDS );
			}
			if ( get_transient( 'villatheme_call' ) == $this->data['slug'] ) {
				add_action( 'admin_notices', array( $this, 'form_ads' ) );
				/*Admin dashboard*/
				add_action( 'wp_dashboard_setup', array( $this, 'dashboard' ) );
			}
		}

		/**Add link to Documentation, Support and Reviews
		 *
		 * @param $links
		 * @param $file
		 *
		 * @return array
		 */
		public function plugin_row_meta( $links, $file ) {
			if ( $this->plugin_base_name === $file ) {
				$row_meta = array(
					'support' => '<a href="' . esc_url( $this->data['support'] ) . '" target="_blank" title="' . esc_attr( 'VillaTheme Support' ) . '">' . esc_html( 'Support' ) . '</a>',
					'review'  => '<a href="' . esc_url( $this->data['review'] ) . '" target="_blank" title="' . esc_attr( 'Rate this plugin' ) . '">' . esc_html( 'Reviews' ) . '</a>',
				);
				if ( ! empty( $this->data['docs'] ) ) {
					$row_meta['docs'] = '<a href="' . esc_url( $this->data['docs'] ) . '" target="_blank" title="' . esc_attr( 'Plugin Documentation' ) . '">' . esc_html( 'Docs' ) . '</a>';
				}

				return array_merge( $links, $row_meta );
			}

			return (array) $links;
		}

		/**
		 * @param $links
		 *
		 * @return mixed
		 */
		public function link_to_pro( $links ) {
			if ( ! empty( $this->data['pro_url'] ) ) {
				$link = '<a class="villatheme-button-upgrade" href="' . esc_url( $this->data['pro_url'] ) . '" target="_blank" title="' . esc_attr( 'Upgrade plugin to premium version' ) . '">' . esc_html( 'Upgrade' ) . '</a>';
				array_unshift( $links, $link );
			}

			return $links;
		}

		/**
		 * Get latest VillaTheme plugins and ads
		 * Available information is appended to changelog of some plugins, which is available with plugins_api()
		 *
		 * @param $is_ads
		 *
		 * @return array
		 */
		public function wp_remote_get( $is_ads = false ) {
			$return = array(
				'status' => 'error',
				'data'   => '',
			);
			include_once ABSPATH . '/wp-admin/includes/plugin-install.php';
			foreach (
				array(
					'woo-multi-currency',
					'email-template-customizer-for-woo',
				) as $slug
			) {
				$api = plugins_api( 'plugin_information', array(
					'slug'   => $slug,
					'locale' => 'en_US',
				) );
				if ( ! is_wp_error( $api ) ) {
					if ( isset( $api->sections, $api->sections['changelog'] ) ) {
						$changelog = $api->sections['changelog'];
						if ( $changelog ) {
							if ( $is_ads ) {
								preg_match( '/VillaThemeCampaign:{(.*)}/', $changelog, $match );
							} else {
								preg_match( '/VillaThemePlugins:\[(.*)]/sm', $changelog, $match );
							}
							if ( $match ) {
								$json = html_entity_decode( str_replace( array(
									'&#8222;',
									'&#8221;',
									'&#8220;',
									'&#8243;',
									'„',
								), '"', $match[1] ) );
								if ( $is_ads ) {
									$json = '{' . $json . '}';
								} else {
									$json = '[' . $json . ']';
								}
								$return['data']   = $json;
								$return['status'] = 'success';
								break;
							}
						}
					}
				}
			}

			return $return;
		}

		/**
		 * Add Extensions page
		 */
		public function admin_menu() {
			if ( $this->data['menu_slug'] ) {
				add_submenu_page(
					$this->data['menu_slug'],
					esc_html( 'Extensions' ),
					esc_html( 'Extensions' ),
					'manage_options',
					$this->data['slug'] . '-extensions',
					array( $this, 'page_callback' )
				);

				if ( $this->data['pro_url'] ) {
					add_submenu_page(
						$this->data['menu_slug'],
						esc_html( 'Try Premium Version' ),
						esc_html( 'Try Premium Version' ),
						'manage_options',
						$this->data['pro_url'],
						''
					);
				}
			}
		}

		/**
		 * Extensions page
		 */
		public function page_callback() { ?>
            <div class="villatheme-extension-page">
                <div class="villatheme-extension-top">
                    <h2><?php echo esc_html( 'THE BEST PLUGINS FOR WOOCOMMERCE' ) ?></h2>
                    <p><?php echo esc_html( 'Our plugins are constantly updated and thanks to your feedback. We add new features on a daily basis. Try our live demo and start increasing the conversions on your ecommerce right away.' ) ?></p>
                </div>
                <div class="villatheme-extension-content villatheme-dashboad">
					<?php
					$feeds = get_transient( 'villatheme_ads' );
					$ads   = '';
					if ( ! $feeds ) {
						$request_data = $this->wp_remote_get();
						if ( $request_data['status'] === 'success' ) {
							$ads = $request_data['data'];
						}
						set_transient( 'villatheme_ads', $ads, DAY_IN_SECONDS );
					} else {
						$ads = $feeds;
					}
					if ( $ads ) {
						$ads = json_decode( $ads );
						$ads = array_filter( $ads );
					} else {
						return;
					}
					if ( is_array( $ads ) && count( $ads ) ) {
						foreach ( $ads as $ad ) {
							if ( empty( $ad ) ) {
								continue;
							}
							?>
                            <div class="villatheme-col-3">
								<?php
								if ( $ad->image ) { ?>
                                    <div class="villatheme-item-image">
                                        <img src="<?php echo esc_url( $ad->image ) ?>">
                                    </div>
									<?php
								}
								if ( $ad->title ) {
									?>
                                    <div class="villatheme-item-title">
										<?php if ( $ad->link ) { ?>
                                        <a target="_blank"
                                           href="<?php echo esc_url( $ad->link ) ?>">
											<?php } ?>
											<?php echo esc_html( $ad->title ) ?>
											<?php if ( $ad->link ) { ?>
                                        </a>
									<?php
									}
									?>
                                    </div>
									<?php
								}
								?>
                                <div class="villatheme-item-controls">
                                    <div class="villatheme-item-controls-inner">
										<?php
										if ( $ad->link ) {
											?>
                                            <a class="button button-primary" target="_blank"
                                               href="<?php echo esc_url( $ad->link ) ?>"><?php echo esc_html( 'Download' ) ?></a>
											<?php
										}
										if ( $ad->demo_url ) {
											?>
                                            <a class="button" target="_blank"
                                               href="<?php echo esc_url( $ad->demo_url ) ?>"><?php echo esc_html( 'Demo' ) ?></a>
											<?php
										}
										if ( $ad->free_url ) {
											?>
                                            <a class="button" target="_blank"
                                               href="<?php echo esc_url( $ad->free_url ) ?>"><?php echo esc_html( 'Trial' ) ?></a>
											<?php
										}
										?>
                                    </div>
                                </div>
                            </div>
							<?php
						}
					}
					?>
                </div>
            </div>
			<?php
		}

		/**
		 * Hide notices
		 */
		public function hide_review_notice() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			$_villatheme_nonce = isset( $_GET['_villatheme_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_villatheme_nonce'] ) ) : '';

			if ( empty( $_villatheme_nonce ) ) {
				return;
			}

			if ( wp_verify_nonce( $_villatheme_nonce, $this->data['slug'] . '_dismiss_notices' ) ) {
				update_option( $this->data['slug'] . '_dismiss_notices', 1 );
			}
			if ( wp_verify_nonce( $_villatheme_nonce, $this->data['slug'] . '_hide_notices' ) ) {
				set_transient( $this->data['slug'] . $this->data['version'] . '_hide_notices', 1, 2592000 );
			}
			if ( wp_verify_nonce( $_villatheme_nonce, $this->data['slug'] . '_wp_reviewed' ) ) {
				set_transient( $this->data['slug'] . $this->data['version'] . '_hide_notices', 1, 2592000 );
				update_option( $this->data['slug'] . '_wp_reviewed', 1 );
				ob_start();
				ob_end_clean();
				// phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
				wp_redirect( $this->data['review'] );
				die;
			}
		}

		/**
		 * Show review WordPress
		 */
		public function review_notice() {
			if ( get_option( $this->data['slug'] . '_dismiss_notices', 0 ) ) {
				return;
			}
			if ( get_transient( $this->data['slug'] . $this->data['version'] . '_hide_notices' ) ) {
				return;
			}
			$name         = $this->get_plugin_name();
			$check_review = get_option( $this->data['slug'] . '_wp_reviewed', 0 );
			$check_start  = get_option( $this->data['slug'] . '_start_use', 0 );
			if ( ! $check_start ) {
				update_option( $this->data['slug'] . '_start_use', 1 );
				set_transient( $this->data['slug'] . $this->data['version'] . '_hide_notices', 1, 259200 );

				return;
			}
			if ( $check_review && ! $this->data['pro_url'] ) {
				return;
			}
			?>

            <div class="villatheme-dashboard updated" style="border-left: 4px solid #ffba00">
                <div class="villatheme-content">
                    <form action="" method="get">
						<?php if ( ! $check_review ) { ?>
                            <p><?php echo esc_html( 'Hi there! You\'ve been using ' ) . '<strong>' . esc_html( $name ) . '</strong>' . esc_html( ' on your site for a few days - I hope it\'s been helpful. If you\'re enjoying my plugin, would you mind rating it 5-stars to help spread the word?' ) ?></p>
						<?php } else { ?>
                            <p><?php echo esc_html( 'Hi there! You\'ve been using ' ) . '<strong>' . esc_html( $name ) . '</strong>' . esc_html( ' on your site for a few days - I hope it\'s been helpful. Would you want get more features?' ) ?></p>
						<?php } ?>
                        <p>
                            <a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array() ), $this->data['slug'] . '_hide_notices', '_villatheme_nonce' ) ); ?>"
                               class="button"><?php echo esc_html( 'Thanks, later' ) ?></a>
							<?php if ( ! $check_review ) { ?>
                                <button class="button button-primary"><?php echo esc_html( 'Rate Now' ) ?></button>
								<?php wp_nonce_field( $this->data['slug'] . '_wp_reviewed', '_villatheme_nonce' ) ?>
							<?php } ?>
							<?php if ( $this->data['pro_url'] ) { ?>
                                <a target="_blank" href="<?php echo esc_url( $this->data['pro_url'] ); ?>"
                                   class="button button-primary"><?php echo esc_html( 'Try Premium Version' ) ?></a>
							<?php } ?>
                            <a target="_self"
                               href="<?php echo esc_url( wp_nonce_url( add_query_arg( array() ), $this->data['slug'] . '_dismiss_notices', '_villatheme_nonce' ) ); ?>"
                               class="button notice-dismiss vi-button-dismiss"><?php echo esc_html( 'Dismiss' ) ?></a>
                        </p>
                    </form>
                </div>
            </div>
			<?php
		}

		/**
		 * Dashboard widget
		 */
		public function dashboard() {
			$this->get_ads_data();
			if ( $this->ads_data === false ) {
				return;
			}
			wp_add_dashboard_widget( 'villatheme_dashboard_status', esc_html( 'VillaTheme News' ), array(
				$this,
				'widget'
			) );
		}

		public function widget() {
			?>
            <div class="villatheme-dashboard">
                <div class="villatheme-content">
					<?php
					if ( $this->ads_data['heading'] ) { ?>
                        <h3><?php echo esc_html( $this->ads_data['heading'] ) ?></h3>
						<?php
					}
					if ( $this->ads_data['description'] ) { ?>
                        <p><?php echo esc_html( $this->ads_data['description'] ) ?></p>
						<?php
					}
					?>
                    <p>
						<?php
						if ( $this->ads_data['link'] ) {
							?>
                            <a target="_blank" href="<?php echo esc_url( $this->ads_data['link'] ); ?>"
                               class="button button-primary"><?php echo esc_html( 'Get Your Gift' ) ?></a>
							<?php
						}
						?>
                    </p>
                </div>
            </div>
			<?php
		}

		/**
		 * Hide notices
		 */
		public function hide_notices() {
			global $current_user;
			$_villatheme_nonce = isset( $_GET['_villatheme_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_villatheme_nonce'] ) ) : '';
			if ( wp_verify_nonce( $_villatheme_nonce, 'villatheme_hide_toolbar' ) ) {
				update_option( 'villatheme_hide_admin_toolbar', time() );
				wp_safe_redirect( esc_url_raw( remove_query_arg( array( '_villatheme_nonce' ) ) ) );
				exit();
			}
			if ( wp_verify_nonce( $_villatheme_nonce, 'villatheme_show_toolbar' ) ) {
				delete_option( 'villatheme_hide_admin_toolbar' );
				wp_safe_redirect( esc_url_raw( remove_query_arg( array( '_villatheme_nonce' ) ) ) );
				exit();
			}
			$hide_notice = isset( $_GET['villatheme-hide-notice'] ) ? sanitize_text_field( wp_unslash( $_GET['villatheme-hide-notice'] ) ) : '';
			$ads_id      = isset( $_GET['ads_id'] ) ? sanitize_text_field( wp_unslash( $_GET['ads_id'] ) ) : '';

			if ( empty( $_villatheme_nonce ) && empty( $hide_notice ) ) {
				return;
			}

			if ( wp_verify_nonce( $_villatheme_nonce, 'hide_notices' ) ) {
				if ( $hide_notice == 1 ) {
					if ( $ads_id ) {
						update_option( 'villatheme_hide_notices_' . $ads_id, time() + DAY_IN_SECONDS );
					} else {
						set_transient( 'villatheme_hide_notices_' . $current_user->ID, 1, DAY_IN_SECONDS );
					}
				} else {
					if ( $ads_id ) {
						update_option( 'villatheme_hide_notices_' . $ads_id, $ads_id );
					} else {
						set_transient( 'villatheme_hide_notices_' . $current_user->ID, 1, DAY_IN_SECONDS * 30 );
					}
				}
			}
		}

		/**
		 * Show Notices
		 */
		public function form_ads() {
			$this->get_ads_data();
			if ( $this->ads_data === false ) {
				return;
			}
			ob_start(); ?>
            <div class="villatheme-dashboard updated">
                <div class="villatheme-content">
					<?php
					if ( $this->ads_data['heading'] ) { ?>
                        <h3><?php echo esc_html( $this->ads_data['heading'] ) ?></h3>
						<?php
					}
					if ( $this->ads_data['description'] ) { ?>
                        <p><?php echo esc_html( $this->ads_data['description'] ) ?></p>
						<?php
					}
					?>
                    <p>
                        <a target="_self"
                           href="<?php echo esc_url( wp_nonce_url( add_query_arg( array(
							   'villatheme-hide-notice' => '2',
							   'ads_id'                 => $this->ads_data['id'],
						   ) ), 'hide_notices', '_villatheme_nonce' ) ); ?>"
                           class="button notice-dismiss vi-button-dismiss"><?php echo esc_html( 'Dismiss' ) ?></a>
                        <a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array(
							'villatheme-hide-notice' => '1',
							'ads_id'                 => $this->ads_data['id'],
						) ), 'hide_notices', '_villatheme_nonce' ) ); ?>"
                           class="button"><?php echo esc_html( 'Thanks, later.' ) ?></a>
						<?php
						if ( $this->ads_data['link'] ) { ?>
                            <a target="_blank" href="<?php echo esc_url( $this->ads_data['link'] ); ?>"
                               class="button button-primary"><?php echo esc_html( 'Get Your Gift' ) ?></a>
							<?php
						}
						?>
                    </p>
                </div>
            </div>
			<?php
			echo wp_kses_post( apply_filters( 'form_ads_data', ob_get_clean() ) );
		}

		public function get_ads_data() {
			global $current_user;
			if ( $this->ads_data !== null ) {
				return;
			}
			$this->ads_data = false;
			if ( get_transient( 'villatheme_hide_notices_' . $current_user->ID ) ) {
				return;
			}
			$data   = get_transient( 'villatheme_notices' );
			$called = get_transient( 'villatheme_called' );
			if ( ! $data && ! $called ) {
				$request_data = $this->wp_remote_get( true );
				if ( $request_data['status'] === 'success' ) {
					$data = json_decode( $request_data['data'], true );
				}
				set_transient( 'villatheme_notices', $data, DAY_IN_SECONDS );
			}

			if ( ! $called ) {
				set_transient( 'villatheme_called', 1, DAY_IN_SECONDS );
			}

			if ( ! is_array( $data ) ) {
				return;
			}
			$data = wp_parse_args( $data, array(
				'heading'     => '',
				'description' => '',
				'link'        => '',
				'id'          => '',
			) );
			if ( ! $data['heading'] && ! $data['description'] ) {
				return;
			}
			$getdate      = getdate();
			$current_time = $getdate[0];
			if ( isset( $data['start'] ) && strtotime( $data['start'] ) > $current_time ) {
				return;
			}
			if ( isset( $data['end'] ) && strtotime( $data['end'] ) < $current_time ) {
				return;
			}
			if ( isset( $data['loop'] ) && $data['loop'] ) {
				if ( ! in_array( $getdate['wday'], explode( ',', $data['loop'] ) ) ) {
					return;
				}
			}
			if ( $data['id'] ) {
				$hide = get_option( 'villatheme_hide_notices_' . $data['id'] );
				if ( $hide === $data['id'] || time() < intval( $hide ) ) {
					return;
				}
			}
			$this->ads_data = $data;
		}

		/**
		 * Init script
		 */
		public function scripts() {
			wp_enqueue_style( 'villatheme-support', $this->data['css_url'] . 'villatheme-support.css', '', $this->version );
		}

		/**
		 *
		 */
		public function villatheme_support() {
			?>
            <div id="villatheme-support" class="vi-ui form segment">
                <h3><?php echo esc_html( 'MAYBE YOU LIKE' ) ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <a class="vi-ui button labeled icon small" target="_blank"
                       href="<?php echo esc_url( $this->data['docs'] ) ?>">
                        <i class="book icon"></i>
						<?php echo esc_html( 'Documentation' ) ?>
                    </a>
                    <a class="vi-ui button inverted labeled icon orange small" target="_blank"
                       href="<?php echo esc_url( $this->data['review'] ) ?>">
                        <i class="star icon"></i>
						<?php echo esc_html( 'Review' ) ?>
                    </a>
                    <a class="vi-ui button labeled icon green small" target="_blank"
                       href="<?php echo esc_url( $this->data['support'] ) ?>">
                        <i class="users icon"></i>
						<?php echo esc_html( 'Request Support' ) ?>
                    </a>
					<?php
					if ( get_option( 'villatheme_hide_admin_toolbar' ) ) {
						?>
                        <a class="vi-ui button labeled icon blue inverted small" target="_self"
                           title="<?php echo esc_attr( 'VillaTheme toolbar helps you access all VillaTheme items quickly' ) ?>"
                           href="<?php echo esc_url( add_query_arg( array( '_villatheme_nonce' => wp_create_nonce( 'villatheme_show_toolbar' ) ) ) ) ?>">
                            <i class="toggle on icon"></i>
							<?php echo esc_html( 'Show Toolbar' ) ?>
                        </a>
						<?php
					}
					?>
                </h3>
                <div class="fields">
					<?php
					$items = $this->get_data( $this->data['slug'] );
					if ( is_array( $items ) && count( $items ) ) {
						shuffle( $items );
						$items = array_slice( $items, 0, 12 );
						foreach ( $items as $k => $item ) {
							if ( $k % 4 == 0 && $k > 0 ) {
								echo wp_kses_post( '</div><div class="fields">' );
							}
							?>
                            <div class="four wide field">
                                <div class="villatheme-item">
                                    <a target="_blank" href="<?php echo esc_url( $item->link ) ?>">
                                        <img src="<?php echo esc_url( $item->image ) ?>"/>
                                    </a>
                                </div>
                            </div>
							<?php
						}
					}
					?>
                </div>
            </div>
			<?php
		}

		/**
		 * @param bool $slug
		 *
		 * @return array
		 */
		protected function get_data( $slug = false ) {
			$feeds = get_transient( 'villatheme_ads' );
			$ads   = '';

			if ( ! $feeds ) {
				try {
					$request_data = $this->wp_remote_get( false );
					if ( $request_data['status'] === 'success' ) {
						$ads = $request_data['data'];
					}
					set_transient( 'villatheme_ads', $ads, DAY_IN_SECONDS );
				} catch ( Exception $e ) {
				}
			} else {
				$ads = $feeds;
			}

			$results = array();

			if ( $ads ) {
				$ads = json_decode( $ads );
				if ( is_array( $ads ) ) {
					$ads = array_filter( $ads );
					foreach ( $ads as $ad ) {
						if ( $slug ) {
							if ( $ad->slug == $slug ) {
								continue;
							}
						}
						$item        = new stdClass();
						$item->title = $ad->title;
						$item->link  = $ad->link;
						$item->thumb = $ad->thumb;
						$item->image = $ad->image;
						$item->desc  = $ad->description;
						$results[]   = $item;
					}
				}
			}

			return $results;
		}

		/**
		 * Add toolbar in WordPress Dashboard
		 */
		public function add_toolbar() {
			/**
			 * @var $wp_admin_bar WP_Admin_Bar
			 */
			global $wp_admin_bar;
			if ( get_option( 'villatheme_hide_admin_toolbar' ) ) {
				return;
			}
			if ( ! $wp_admin_bar->get_node( 'villatheme' ) ) {
				$wp_admin_bar->add_node( array(
					'id'    => 'villatheme',
					'title' => '<span class="ab-icon dashicons-star-filled villatheme-rotating"></span>' . 'VillaTheme',
					'href'  => '',
					'meta'  => array(
						'class' => 'villatheme-toolbar'
					),
				) );
				add_action( 'admin_bar_menu', array( $this, 'hide_toolbar_button' ), 200 );
			}
			if ( $this->data['menu_slug'] ) {
				$wp_admin_bar->add_node( array(
					'id'     => $this->data['slug'],
					'title'  => $this->get_plugin_name(),
					'parent' => 'villatheme',
					'href'   => strpos( $this->data['menu_slug'], '.php' ) === false ? admin_url( 'admin.php?page=' . $this->data['menu_slug'] ) : admin_url( $this->data['menu_slug'] ),
				) );
			}
		}

		public function hide_toolbar_button() {
			global $wp_admin_bar;
			/**
			 * @var $wp_admin_bar WP_Admin_Bar
			 */
			$warning = 'return confirm("VillaTheme toolbar helps you access all VillaTheme items quickly, do you want to hide it anyway?")';
			$wp_admin_bar->add_node( array(
				'id'     => 'villatheme_hide_toolbar',
				'title'  => '<span class="dashicons dashicons-dismiss"></span><span class="villatheme-hide-toolbar-button-title">Hide VillaTheme toolbar</span>',
				'parent' => 'villatheme',
				'href'   => add_query_arg( array( '_villatheme_nonce' => wp_create_nonce( 'villatheme_hide_toolbar' ) ) ),
				'meta'   => array(
					'onclick' => esc_js( $warning )
				),
			) );
		}

		private function get_plugin_name() {
			include_once ABSPATH . '/wp-admin/includes/plugin.php';
			$plugins = get_plugins();

			return isset( $plugins[ $this->plugin_base_name ]['Title'] ) ? $plugins[ $this->plugin_base_name ]['Title'] : ucwords( str_replace( '-', ' ', $this->data['slug'] ) );
		}

		private function get_uninstall_reasons() {

			$reasons = array(
				array(
					'id'          => 'could_not_understand',
					'text'        => __( 'I couldn\'t understand how to make it work', 'viwec-email-template-customizer' ),
					'type'        => 'textarea',
					'placeholder' => __( 'Would you like us to assist you?', 'viwec-email-template-customizer' )
				),
				array(
					'id'          => 'found_better_plugin',
					'text'        => __( 'I found a better plugin', 'viwec-email-template-customizer' ),
					'type'        => 'text',
					'placeholder' => __( 'Which plugin?', 'viwec-email-template-customizer' )
				),
				array(
					'id'          => 'not_have_that_feature',
					'text'        => __( 'The plugin is great, but I need specific feature that you don\'t support', 'viwec-email-template-customizer' ),
					'type'        => 'textarea',
					'placeholder' => __( 'Could you tell us more about that feature?', 'viwec-email-template-customizer' )
				),
				array(
					'id'          => 'is_not_working',
					'text'        => __( 'The plugin is not working', 'viwec-email-template-customizer' ),
					'type'        => 'textarea',
					'placeholder' => __( 'Could you tell us a bit more whats not working?', 'viwec-email-template-customizer' )
				),
				array(
					'id'          => 'looking_for_other',
					'text'        => __( 'It\'s not what I was looking for', 'viwec-email-template-customizer' ),
					'type'        => 'textarea',
					'placeholder' => 'Could you tell us a bit more?'
				),
				array(
					'id'          => 'did_not_work_as_expected',
					'text'        => __( 'The plugin didn\'t work as expected', 'viwec-email-template-customizer' ),
					'type'        => 'textarea',
					'placeholder' => __( 'What did you expect?', 'viwec-email-template-customizer' )
				),
				array(
					'id'          => 'other',
					'text'        => __( 'Other', 'viwec-email-template-customizer' ),
					'type'        => 'textarea',
					'placeholder' => __( 'Could you tell us a bit more?', 'viwec-email-template-customizer' )
				),
			);

			return $reasons;
		}

		public function deactivate_scripts() {
			global $pagenow;
			if ( 'plugins.php' != $pagenow ) {
				return;
			}

			static $modal = false;

			if ( ! $modal ) {
				$reasons = $this->get_uninstall_reasons();
				?>
                <div class="villatheme-deactivate-modal" id="villatheme-deactivate-survey-modal">
                    <div class="villatheme-deactivate-modal-wrap">
                        <div class="villatheme-deactivate-modal-header">
                            <h3><?php esc_html_e( 'If you have a moment, please let us know why you are deactivating:', 'viwec-email-template-customizer' ); ?></h3>
                        </div>
                        <div class="villatheme-deactivate-modal-body">
                            <ul class="reasons">
								<?php foreach ( $reasons as $reason ) { ?>
                                    <li data-type="<?php echo esc_attr( $reason['type'] ); ?>" data-placeholder="<?php echo esc_attr( $reason['placeholder'] ); ?>">
                                        <label>
                                            <input type="radio" name="selected-reason" value="<?php echo esc_attr( $reason['id'] ); ?>">
											<?php echo esc_html( $reason['text'] ); ?>
                                        </label>
                                    </li>
								<?php } ?>
                            </ul>
                        </div>
                        <div class="villatheme-deactivate-modal-footer">
                            <a href="#" class="dont-bother-me"><?php esc_html_e( 'I rather wouldn\'t say', 'viwec-email-template-customizer' ); ?></a>
                            <button class="button-primary villatheme-deactivate-submit disabled"><?php esc_html_e( 'Submit & Deactivate', 'viwec-email-template-customizer' ); ?></button>
                            <button class="button-secondary villatheme-model-cancel"><?php esc_html_e( 'Cancel', 'viwec-email-template-customizer' ); ?></button>
                        </div>
                    </div>
                </div>

                <style type="text/css">
                    .villatheme-deactivate-modal {
                        position: fixed;
                        z-index: 99999;
                        top: 0;
                        right: 0;
                        bottom: 0;
                        left: 0;
                        background: rgba(0, 0, 0, 0.5);
                        display: none;
                    }

                    .villatheme-deactivate-modal.modal-active {
                        display: block;
                    }

                    .villatheme-deactivate-modal-wrap {
                        width: 50%;
                        position: relative;
                        margin: 10% auto;
                        background: #fff;
                    }

                    .villatheme-deactivate-modal-header {
                        border-bottom: 1px solid #eee;
                        padding: 8px 20px;
                    }

                    .villatheme-deactivate-modal-header h3 {
                        line-height: 150%;
                        margin: 0;
                    }

                    .villatheme-deactivate-modal-body {
                        padding: 5px 20px 20px 20px;
                    }

                    .villatheme-deactivate-modal-body .input-text,
                    .villatheme-deactivate-modal-body textarea {
                        width: 75%;
                    }

                    .villatheme-deactivate-modal-body .reason-input {
                        margin-top: 5px;
                        margin-left: 20px;
                    }

                    .villatheme-deactivate-modal-footer {
                        border-top: 1px solid #eee;
                        padding: 12px 20px;
                        text-align: right;
                    }
                </style>

                <script type="text/javascript">
                    var ViDeactivate = {deactivateLink: '', surveyUrl: ''};

                    (function ($) {
                        $(function () {
                            let modal = $('#villatheme-deactivate-survey-modal');
                            ViDeactivate.modal = modal;

                            modal.on('click', 'button.villatheme-model-cancel', function (e) {
                                e.preventDefault();
                                modal.removeClass('modal-active');
                            });

                            modal.on('click', 'input[type="radio"]', function () {
                                $('button.villatheme-deactivate-submit').removeClass('disabled');
                                var parent = $(this).parents('li:first');
                                modal.find('.reason-input').remove();
                                var inputType = parent.data('type'),
                                    inputPlaceholder = parent.data('placeholder'),
                                    reasonInputHtml = '<div class="reason-input">' + (('text' === inputType) ? '<input type="text" class="input-text" size="40" />' : '<textarea rows="5" cols="45"></textarea>') + '</div>';

                                if (inputType !== '') {
                                    parent.append($(reasonInputHtml));
                                    parent.find('input, textarea').attr('placeholder', inputPlaceholder).focus();
                                }
                            });

                            modal.on('click', 'button.villatheme-deactivate-submit', function (e) {
                                e.preventDefault();
                                let button = $(this);

                                if (button.hasClass('disabled')) return;

                                let $radio = $('input[type="radio"]:checked', modal);
                                let $selected_reason = $radio.parents('li:first'),
                                    $input = $selected_reason.find('textarea, input[type="text"]');
                                let reason_id = (0 === $radio.length) ? '' : $radio.val();
                                let reason_info = (0 !== $input.length) ? $input.val().trim() : '';
                                let date = new Date(Date.now()).toLocaleString().split(',')[0];

                                if ((reason_id === 'other' && !reason_info) || !reason_id) {
                                    window.location.href = ViDeactivate.deactivateLink;
                                    return;
                                }

                                $.ajax({
                                    url: `${ViDeactivate.surveyUrl}?date=${date}&${reason_id}=1&reason_info=${reason_info}`,
                                    type: 'GET',
                                    beforeSend: function () {
                                        button.addClass('disabled');
                                        button.text('Processing...');
                                    },
                                    complete: function () {
                                        window.location.href = ViDeactivate.deactivateLink;
                                    }
                                });

                            });
                        });
                    }(jQuery));

                </script>
				<?php
				$modal = true;
			}
			?>
            <script type="text/javascript">
                (function ($) {
                    $(function () {
                        $('#the-list').on('click', 'a#deactivate-<?php echo esc_html( $this->data['slug'] ) ?>', function (e) {
                            e.preventDefault();
                            ViDeactivate.modal.addClass('modal-active');
                            ViDeactivate.deactivateLink = $(this).attr('href');
                            ViDeactivate.surveyUrl = '<?php echo esc_url( $this->data['survey_url'] )?>';
                            ViDeactivate.modal.find('a.dont-bother-me').attr('href', ViDeactivate.deactivateLink).css('float', 'left');
                        });
                    });
                }(jQuery));
            </script>
			<?php
		}
	}
}