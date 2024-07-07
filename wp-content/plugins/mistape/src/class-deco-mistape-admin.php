<?php

class Deco_Mistape_Admin extends Deco_Mistape_Abstract {

	private static $instance;

	/**
	 * Constructor
	 */
	protected function __construct() {

		parent::__construct();

		// Load textdomain
		$this->load_textdomain();

		// admin-wide actions
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_notices', array( $this, 'plugin_activated_notice' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// if multisite inheritance is enabled, add corresponding action
		if ( is_multisite() && $this->options['multisite_inheritance'] === 'yes' ) {
			add_action( 'wpmu_new_blog', __CLASS__ . '::activation' );
		}

		// Mistape page-specific actions
		if ( isset( $_GET['page'] ) && $_GET['page'] === 'mistape_settings' ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_load_scripts_styles' ) );
			add_action( 'admin_footer', array( $this, 'insert_dialog' ) );
		}

		// filters
		add_filter( 'plugin_action_links', array( $this, 'plugins_page_settings_link' ), 10, 2 );

		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

		register_uninstall_hook( __FILE__, array( 'Abstract_Deco_Mistape', 'uninstall_cleanup' ) );
	}

	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * Load plugin defaults
	 */
	public function init() {
		// init only once
		if ( $this->email_recipient_types ) {
			return;
		}

		$this->post_types            = $this->get_post_types_list();
		$this->email_recipient_types = array(
			'admin'  => __( 'Administrator', 'mistape' ),
			'editor' => __( 'Editor', 'mistape' ),
			'other'  => __( 'Specify other', 'mistape' )
		);

		$this->caption_formats = array(
			'text'     => __( 'Text', 'mistape' ),
			'image'    => __( 'Image', 'mistape' ),
			'disabled' => __( 'Do not show caption at the bottom of post', 'mistape' )
		);

		$this->caption_text_modes = array(
			'default' => array(
				'name'        => __( 'Default', 'mistape' ),
				'description' => __( 'automatically translated to supported languages', 'mistape' )
			),
			'custom'  => array(
				'name'        => __( 'Custom text', 'mistape' ),
				'description' => ''
			)
		);

		$this->dialog_modes = array(
			'notify'  => __( 'Just notify of successful submission', 'mistape' ),
			'confirm' => __( 'Show preview of reported text and ask confirmation', 'mistape' ),
			'comment' => __( 'Preview and comment field', 'mistape' ),
		);
	}

	/**
	 * Add submenu
	 */
	public function admin_menu() {
		if ( apply_filters( 'mistape_show_settings_menu_item', true, $this ) ) {
			add_options_page( 'Mistape', 'Mistape', 'manage_options', 'mistape_settings',
				array( $this, 'print_options_page' ) );
		}
	}

	/**
	 * Options page output
	 */
	public function print_options_page() {
		global $wpdb;
		$this->init();

		// show changelog only if less than one week passed since updating the plugin
		$show_changelog = time() - (int) $this->options['plugin_updated_timestamp'] < WEEK_IN_SECONDS;

		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'configuration';

		$table_name    = $wpdb->base_prefix . Deco_Mistape_Abstract::DB_TABLE;
		$reports_count = 0;
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
		}
		$table_exists  = ! ! $wpdb->get_var(
			"SELECT COUNT(*) FROM information_schema.tables
			WHERE table_schema = '" . DB_NAME . "'
			AND table_name = '{$wpdb->base_prefix}mistape_reports' LIMIT 1"
		);
		$blog_id       = get_current_blog_id();
		$reports_count = $table_exists ? $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->base_prefix}mistape_reports where status = 'pending' && blog_id = $blog_id" ) : null;
		?>
		<div class="wrap">
			<h2>Mistape</h2>
			<h2 class="nav-tab-wrapper">
				<?php
				printf( '<a href="%s" class="nav-tab%s" data-bodyid="mistape-configuration" >%s</a>',
					add_query_arg( 'tab', 'configuration' ),
					$active_tab == 'configuration' ? ' nav-tab-active' : '', __( 'Configuration', 'mistape' ) );
				printf( '<a href="%s" class="nav-tab%s" data-bodyid="mistape-help">%s</a>',
					add_query_arg( 'tab', 'help' ),
					$active_tab == 'help' ? ' nav-tab-active' : '', __( 'Help', 'mistape' ) );
				?>
			</h2>
			<?php printf( '<div id="mistape-configuration" class="mistape-tab-contents" %s>',
				$active_tab == 'configuration' ? '' : 'style="display: none;"' ); ?>
			<form action="<?php echo admin_url( 'options.php' ); ?>" method="post">
				<?php
				settings_fields( 'mistape_options' );
				do_settings_sections( 'mistape_options' );
				?>
				<p class="submit">
					<?php submit_button( '', 'primary', 'save_mistape_options', false ); ?>
					<span class="description alignright">
						<?php printf(
							_x( 'Please %s our plugin', '%s = rate', 'mistape' ),
							'<a href="https://wordpress.org/support/view/plugin-reviews/mistape#postform" target="_blank" class="mistape-vote"><span class="dashicons dashicons-thumbs-up"></span>' .
							_x( 'rate', 'please rate our plugin', 'mistape' ) . '</a>'
						); ?>
					</span>
				</p>
				<div id="mistape-sidebar">
					<?php if ( $show_changelog ) { ?>
						<div id="mistape_info" class="postbox deco-right-sidebar-widget">
							<h3 class="hndle">
								<span>New in Mistape 1.4.0</span>
							</h3>
							<div class="inside">
								<ul>
									<li>Included PRO version</li>
									<li>Introduce support for mobile version</li>
									<li>Introduce database table for saving reports. Used for checking for
										duplicates—you will not get multiple reports about the same error anymore.
									</li>
									<li>Introduce support for addons.</li>
									<li>(for developers) arguments for "mistape_process_report" action were changed.
									</li>
									<li>lots of improvements under the hood.</li>
								</ul>
							</div>
						</div>
					<?php } ?>
					<div id="mistape_statistics" class="postbox deco-right-sidebar-widget">
						<h3 class="hndle">
							<span><?php _e( 'Statistics', 'mistape' ); ?></span>
						</h3>
						<div class="inside">
							<p>
								<?php
								$reports_count = empty( $reports_count ) ? 0 : $reports_count;
								_e( 'Reports received up to date:', 'mistape' );
								echo ' <strong>' . $reports_count . '</strong>';
								?>
							</p>
							<?php if ( ! class_exists( 'Deco_Mistape_Table_Addon' ) ) { ?>
								<p class="hover-image mistape-icon-after-question"
								   data-img-url="<?php echo MISTAPE__PLUGIN_URL . '/assets/img/mistape-pro-table-list.png'; ?>">
									<?php _e( 'Detailed mistake statistics is available in Mistape PRO', 'mistape' ); ?>
								</p>
								<p>
									<?php _e( 'An option to mark notification as resolved, archive, delete from the table in one click', 'mistape' ); ?>
								</p>
								<a href="#!" class="button button-primary paddle_button" data-product="508163"
								   data-quantity="1" data-allow-quantity="false">
									<?php _e( 'Buy PRO for just', 'mistape' ); ?>
									<span class="cost-block">15</span>!</a>
							<?php } ?>
						</div>
					</div>
				</div>
			</form>

		</div>
		<?php
		printf( '<div id="mistape-help" class="mistape-tab-contents" %s>',
			$active_tab == 'help' ? '' : 'style="display: none;" ' );
		$this->print_help_page();
		?>
		</div>
		<div class="clear"></div>
		</div>
		<?php
	}

	/**
	 * Regiseter plugin settings
	 */
	public function register_settings() {
		register_setting( 'mistape_options', 'mistape_options', array( $this, 'validate_options' ) );

		add_settings_section( 'mistape_configuration', '', array( $this, 'section_configuration' ), 'mistape_options' );
		add_settings_field( 'mistape_email_recipient', __( 'Email recipient', 'mistape' ),
			array( $this, 'field_email_recipient' ), 'mistape_options', 'mistape_configuration' );
		add_settings_field( 'mistape_post_types', __( 'Post types', 'mistape' ), array( $this, 'field_post_types' ),
			'mistape_options', 'mistape_configuration' );
		add_settings_field( 'mistape_register_shortcode', __( 'Shortcodes', 'mistape' ),
			array( $this, 'field_register_shortcode' ), 'mistape_options', 'mistape_configuration' );
		add_settings_field( 'mistape_caption_format', __( 'Caption format', 'mistape' ),
			array( $this, 'field_caption_format' ), 'mistape_options', 'mistape_configuration' );
		add_settings_field( 'mistape_caption_text_mode', __( 'Caption text mode', 'mistape' ),
			array( $this, 'field_caption_text_mode' ), 'mistape_options', 'mistape_configuration' );
		add_settings_field( 'mistape_caption_text_mode_for_mobile', __( 'Caption text mode for Mobile', 'mistape' ),
			array( $this, 'field_caption_text_mode_for_mobile' ), 'mistape_options', 'mistape_configuration' );
		add_settings_field( 'mistape_show_logo_in_caption', __( 'Icon before the caption text', 'mistape' ),
			array( $this, 'field_show_logo_in_caption' ), 'mistape_options', 'mistape_configuration' );
		add_settings_field( 'mistape_powered_by', __( 'Powered by', 'mistape' ),
			array( $this, 'field_powered_by' ), 'mistape_options', 'mistape_configuration' );
		add_settings_field( 'mistape_color_scheme', __( 'Color scheme', 'mistape' ),
			array( $this, 'field_show_color_scheme' ), 'mistape_options', 'mistape_configuration' );
		add_settings_field( 'mistape_dialog_mode', __( 'Dialog mode', 'mistape' ), array( $this, 'field_dialog_mode' ),
			'mistape_options', 'mistape_configuration' );

		if ( is_multisite() && is_main_site() ) {
			add_settings_field( 'mistape_multisite_inheritance', __( 'Multisite inheritance', 'mistape' ),
				array( $this, 'field_multisite_inheritance' ), 'mistape_options', 'mistape_configuration' );
		}
	}

	/**
	 * Section callback
	 */
	public function section_configuration() {
	}

	/**
	 * Email recipient selection
	 */
	public function field_email_recipient() {
		echo '
		<fieldset>';


		foreach ( $this->email_recipient_types as $value => $label ) {
			echo '
				<label><input id="mistape_email_recipient_type-' . $value . '" type="radio"
				  name="mistape_options[email_recipient][type]" value="' . esc_attr( $value ) . '" ' .
			     checked( $value, $this->options['email_recipient']['type'], false ) . ' />' . esc_html( $label ) . '
				</label><br>';
		}

		echo '
			<div id="mistape_email_recipient_list-admin"' . ( $this->options['email_recipient']['type'] == 'admin' ? '' : 'style="display: none;"' ) . '>';

		echo '
			<select name="mistape_options[email_recipient][id][admin]">';

		$admins = $this->get_user_list_by_role( 'administrator' );
		foreach ( $admins as $user ) {
			echo '
				<option value="' . $user->ID . '" ' . selected( $user->ID, $this->options['email_recipient']['id'],
					false ) . '>' . esc_html( $user->user_nicename . ' (' . $user->user_email . ')' ) . '</option>';
		}

		echo '
			</select>
			</div>';

		echo '
			<div id="mistape_email_recipient_list-editor"' . ( $this->options['email_recipient']['type'] === 'editor' ? '' : 'style="display: none;"' ) . '>';


		$editors = $this->get_user_list_by_role( 'editor' );
		if ( ! empty( $editors ) ) {
			echo '<select name="mistape_options[email_recipient][id][editor]">';
			foreach ( $editors as $user ) {
				echo '
				<option value="' . $user->ID . '" ' . selected( $user->ID, $this->options['email_recipient']['id'],
						false ) . '>' . esc_html( $user->user_nicename . ' (' . $user->user_email . ')' ) . '</option>';
			}
			echo '</select>';
		} else {
			echo '<select><option value="">-- ' . _x( 'no editors found', 'select option, shown when no users with editor role are present', 'mistape' ) . ' --</option></select>';
		}

		echo '
			</div>
			<div id="mistape_email_recipient_list-other" ' . ( $this->options['email_recipient']['type'] === 'other' ? '' : 'style="display: none;"' ) . '>
				<input type="text" class="regular-text" name="mistape_options[email_recipient][email]" value="' . esc_attr( $this->options['email_recipient']['email'] ) . '" />
				<p class="description">' . __( 'separate multiple recipients with commas', 'mistape' ) . '</p>
			</div>
			<br>
			<label><input id="mistape_email_recipient-post_author_first" type="checkbox" name="mistape_options[email_recipient][post_author_first]" value="1" ' . checked( 'yes',
				$this->options['email_recipient']['post_author_first'],
				false ) . '/>' . __( 'If post ID is determined, notify post author instead', 'mistape' ) . '</label>
		</fieldset>';
	}

	/**
	 * Post types to show caption in
	 */
	public function field_post_types() {
		echo '
		<fieldset style="max-width: 600px;">';

		foreach ( $this->post_types as $value => $label ) {
			echo '
			<label style="padding-right: 8px; min-width: 60px;"><input id="mistape_post_type-' . $value . '" type="checkbox" name="mistape_options[post_types][' . $value . ']" value="1" ' . checked( true,
					in_array( $value, $this->options['post_types'] ),
					false ) . ' />' . esc_html( $label ) . '</label>	';
		}

		echo '
			<p class="description">' . __( '"Press Ctrl+Enter&hellip;" captions will be displayed at the bottom of selected post types.', 'mistape' ) . '</p>
		</fieldset>';
	}

	/**
	 * Shortcode option
	 */
	public function field_register_shortcode() {
		echo '
		<fieldset>
			<label><input id="mistape_register_shortcode" type="checkbox" name="mistape_options[register_shortcode]" value="1" ' . checked( 'yes',
				$this->options['register_shortcode'], false ) . '/>' . __( 'Register <code>[mistape]</code> shortcode.',
				'mistape' ) . '</label>
			<p class="description">' . __( 'Enable if manual caption insertion via shortcodes is needed.', 'mistape' ) . '</p>
			<p class="description">' . __( 'Usage examples are in Help section.', 'mistape' ) . '</p>
			<p class="description">' . __( 'When enabled, Mistape Ctrl+Enter listener works on all pages, not only on enabled post types.', 'mistape' ) . '</p>
		</fieldset>';
	}

	/**
	 * Powered by option
	 */
	public function field_powered_by() {
		echo '
		<fieldset>
			<label><input id="mistape_enable_powered_by" type="checkbox" name="mistape_options[enable_powered_by]" value="yes" ' . checked( 'yes', $this->options['enable_powered_by'], false ) . '/>' . __( 'Enable', 'mistape' ) . '</label>
			<p class="description">' . __( 'Here you can enable Mistape.com link in an icon', 'mistape' ) . '</p>
		</fieldset>';
	}

	/**
	 * Caption format option
	 */
	public function field_caption_format() {
		echo '
		<fieldset>';

		foreach ( $this->caption_formats as $value => $label ) {
			echo '
			<label><input id="mistape_caption_format-' . $value . '" type="radio" name="mistape_options[caption_format]" value="' . esc_attr( $value ) . '" ' . checked( $value,
					$this->options['caption_format'], false ) . ' />' . esc_html( $label ) . '</label><br>';
		}

		echo '
		<div id="mistape_caption_image"' . ( $this->options['register_shortcode'] == 'yes' || $this->options['caption_format'] === 'image' ? '' : 'style="display: none;"' ) . '>
			<p class="description">' . __( 'Enter the full image URL starting with http://', 'mistape' ) . '</p>
			<input type="text" class="regular-text" name="mistape_options[caption_image_url]" value="' . esc_attr( $this->options['caption_image_url'] ) . '" />
		</div>
		</fieldset>';
	}

	/**
	 * Caption custom text field
	 */
	public function field_caption_text_mode() {
		echo '<fieldset>';

		foreach ( $this->caption_text_modes as $value => $label ) {
			echo '<label><input id="mistape_caption_text_mode-' . $value . '" type="radio" name="mistape_options[caption_text_mode]" ' . 'value="' . esc_attr( $value ) . '" ' . checked( $value, $this->options['caption_text_mode'],
					false ) . ' />' . $label['name'];
			echo empty( $label['description'] ) ? ':' : ' <span class="description">(' . $label['description'] . ')</span>';
			echo '</label><br>';
		}

		$textarea_contents = $this->get_caption_text();
		$textarea_state    = $this->options['caption_text_mode'] === 'default' ? ' disabled="disabled"' : '';

		echo '<textarea id="mistape_custom_caption_text" name="mistape_options[custom_caption_text]" cols="70" rows="4"
			data-default="' . esc_attr( $this->get_default_caption_text() ) . '"' . $textarea_state . ' />' . esc_textarea( $textarea_contents ) . '</textarea><br>
		</fieldset>';
	}

	/**
	 * Caption custom text for Mobile field
	 */
	public function field_caption_text_mode_for_mobile() {
		echo '<fieldset>';

		foreach ( $this->caption_text_modes as $value => $label ) {
			echo '<label><input id="mistape_caption_text_mode_for_mobile-' . $value . '" type="radio" name="mistape_options[caption_text_mode_for_mobile]" ' . 'value="' . esc_attr( $value ) . '" ' . checked( $value, $this->options['caption_text_mode_for_mobile'],
					false ) . ' />' . $label['name'];
			echo empty( $label['description'] ) ? ':' : ' <span class="description">(' . $label['description'] . ')</span>';
			echo '</label><br>';
		}

		$textarea_contents = $this->get_caption_text_for_mobile();
		$textarea_state    = $this->options['caption_text_mode_for_mobile'] === 'default' ? ' disabled="disabled"' : '';

		echo '<textarea id="mistape_custom_caption_text_for_mobile" name="mistape_options[custom_caption_text_for_mobile]" cols="70" rows="4"
			data-default="' . esc_attr( $this->get_default_caption_text_for_mobile() ) . '"' . $textarea_state . ' />' . esc_textarea( $textarea_contents ) . '</textarea><br>
		</fieldset>';
	}

	/**
	 * Show Mistape logo in caption
	 */
	public function field_show_logo_in_caption() {
		$custom_logo_icon = intval( $this->options['show_logo_in_caption'] );
		$mistape_icons    = apply_filters( 'mistape_get_icon', array( 'icon_all' => true ) );

		echo '
		<fieldset class="select-logo">
			<label class="select-logo__item select-logo__item--no-img">
			    <input type="radio" name="mistape_options[show_logo_in_caption]" value="0" ' . checked( 0, $custom_logo_icon, false ) . '>
			    <div class="select-logo__img">
			        ' . __( 'no icon', 'mistape' ) . '
			    </div>
			</label>

			<label class="select-logo__item">
			    <input type="radio" name="mistape_options[show_logo_in_caption]" value="1" ' . checked( 1, $custom_logo_icon, false ) . '>
			    <div class="select-logo__img">
                    ' . $mistape_icons[1] . '
			    </div>
			</label>

			<label class="select-logo__item">
			    <input type="radio" name="mistape_options[show_logo_in_caption]" value="2" ' . checked( 2, $custom_logo_icon, false ) . '>
			    <div class="select-logo__img">
                    ' . $mistape_icons[2] . '
			    </div>
			</label>

			<label class="select-logo__item">
			    <input type="radio" name="mistape_options[show_logo_in_caption]" value="3" ' . checked( 3, $custom_logo_icon, false ) . '>
			    <div class="select-logo__img">
                    ' . $mistape_icons[3] . '
			    </div>
			</label>

			<label class="select-logo__item">
			    <input type="radio" name="mistape_options[show_logo_in_caption]" value="4" ' . checked( 4, $custom_logo_icon, false ) . '>
			    <div class="select-logo__img">
                    ' . $mistape_icons[4] . '
			    </div>
			</label>

			<label class="select-logo__item">
			    <input type="radio" name="mistape_options[show_logo_in_caption]" value="5" ' . checked( 5, $custom_logo_icon, false ) . '>
			    <div class="select-logo__img">
                    ' . $mistape_icons[5] . '
			    </div>
			</label>
		</fieldset>';
	}

	/**
	 * Color scheme
	 */
	public function field_show_color_scheme() {
		$str = __( 'default color', 'mistape' );
		echo '
		<fieldset>
			<label>
			    <input id="mistape_color_scheme" type="text" name="mistape_options[color_scheme]" value="' . ( $this->options['color_scheme'] != '' ? $this->options['color_scheme'] : '#E42029' ) . '" class="mistape_color_picker" />
			</label>
			<p class="description">' . $str . '  <span style="color: #E42029;">#E42029</span></p>
		</fieldset>';
	}


	/**
	 * Dialog mode: ask for a comment or fire notification straight off
	 */
	public function field_dialog_mode() {
		echo '<fieldset>';

		foreach ( $this->dialog_modes as $value => $label ) {
			echo '
			<label><input class="dialog_mode_choice" id="mistape_caption_format-' . $value .
			     '" type="radio" name="mistape_options[dialog_mode]" value="' . esc_attr( $value ) . '" ' .
			     checked( $value, $this->options['dialog_mode'], false ) . ' />' . esc_html( $label ) .
			     '</label><br>';
		}
		echo '<button class="button" id="preview-dialog-btn">' . __( 'Preview dialog', 'mistape' ) . '</button>';
		echo '<span id="preview-dialog-spinner" class="spinner"></span>';
	}

	/**
	 * Multisite inheritance: copy settings from main site to newly created blogs
	 */
	public function field_multisite_inheritance() {
		echo '
		<fieldset>
			<label><input id="mistape_multisite_inheritance" type="checkbox" name="mistape_options[multisite_inheritance]" value="1" ' .
		     checked( 'yes', $this->options['multisite_inheritance'],
			     false ) . '/>' . __( 'Copy settings from main site when new blog is created', 'mistape' ) . '
	        </label>
		</fieldset>';
	}

	/**
	 * Validate options
	 *
	 * @param $input
	 *
	 * @return mixed
	 */
	public function validate_options( $input ) {
		$this->init();

		if ( ! current_user_can( 'manage_options' ) ) {
			return $input;
		}

		if ( isset( $_POST['option_page'] ) && $_POST['option_page'] == 'mistape_options' ) {

			// mail recipient
			$input['email_recipient']['type']              = sanitize_text_field( isset( $input['email_recipient']['type'] ) && in_array( $input['email_recipient']['type'],
				array_keys( $this->email_recipient_types ) ) ? $input['email_recipient']['type'] : self::$defaults['email_recipient']['type'] );
			$input['email_recipient']['post_author_first'] = $input['email_recipient']['post_author_first'] === '1' ? 'yes' : 'no';

			if ( $input['email_recipient']['type'] == 'admin' && isset( $input['email_recipient']['id']['admin'] ) && ( user_can( $input['email_recipient']['id']['admin'],
					'administrator' ) )
			) {
				$input['email_recipient']['id'] = $input['email_recipient']['id']['admin'];
			} elseif ( $input['email_recipient']['type'] == 'editor' && isset( $input['email_recipient']['id']['editor'] ) && ( user_can( $input['email_recipient']['id']['editor'],
					'editor' ) )
			) {
				$input['email_recipient']['id'] = $input['email_recipient']['id']['editor'];
			} elseif ( $input['email_recipient']['type'] == 'other' && isset( $input['email_recipient']['email'] ) ) {
				$input['email_recipient']['id'] = '0';
				$emails                         = explode( ',',
					str_replace( array( ', ', ' ' ), ',', $input['email_recipient']['email'] ) );
				$invalid_emails                 = array();
				foreach ( $emails as $key => &$email ) {
					if ( ! is_email( $email ) ) {
						$invalid_emails[] = $email;
						unset( $emails[ $key ] );
					}
					$email = sanitize_email( $email );
				}
				if ( $invalid_emails ) {
					add_settings_error(
						'mistape_options',
						esc_attr( 'invalid_recipient' ),
						sprintf( __( 'ERROR: You entered invalid email address: %s', 'mistape' ),
							trim( implode( ',', $invalid_emails ), "," ) ),
						'error'
					);
				}

				$input['email_recipient']['email'] = trim( implode( ',', $emails ), "," );
			} else {
				add_settings_error(
					'mistape_options',
					esc_attr( 'invalid_recipient' ),
					__( 'ERROR: You didn\'t select valid email recipient.', 'mistape' ),
					'error'
				);
				$input['email_recipient']['id'] = '1';
				$input['email_recipient']       = $this->options['email_recipient'];
			}

			// post types
			$input['post_types'] = isset( $input['post_types'] ) && is_array( $input['post_types'] ) && count( array_intersect( array_keys( $input['post_types'] ),
				array_keys( $this->post_types ) ) ) === count( $input['post_types'] ) ? array_keys( $input['post_types'] ) : array();

			// shortcode option
			$input['register_shortcode'] = (bool) isset( $input['register_shortcode'] ) ? 'yes' : 'no';

			// caption type
			$input['caption_format'] = isset( $input['caption_format'] ) && in_array( $input['caption_format'],
				array_keys( $this->caption_formats ) ) ? $input['caption_format'] : self::$defaults['caption_format'];
			if ( $input['caption_format'] === 'image' ) {
				if ( ! empty( $input['caption_image_url'] ) ) {
					$input['caption_image_url'] = esc_url( $input['caption_image_url'] );
				} else {
					add_settings_error(
						'mistape_options',
						esc_attr( 'no_image_url' ),
						__( 'ERROR: You didn\'t enter caption image URL.', 'mistape' ),
						'error'
					);
					$input['caption_format']    = self::$defaults['caption_format'];
					$input['caption_image_url'] = self::$defaults['caption_image_url'];
				}
			};

			// caption text mode
			$input['caption_text_mode']   = isset( $input['caption_text_mode'] ) && in_array( $input['caption_text_mode'],
				array_keys( $this->caption_text_modes ) ) ? $input['caption_text_mode'] : self::$defaults['caption_text_mode'];
			$input['custom_caption_text'] = $input['caption_text_mode'] == 'custom' && $input['custom_caption_text'] !== $this->default_caption_text ? wp_kses_post( $input['custom_caption_text'] ) : '';

			$input['multisite_inheritance'] = isset( $input['multisite_inheritance'] ) && $input['multisite_inheritance'] === '1' ? 'yes' : 'no';

			$input['first_run'] = 'no';

			//color scheme
			$input['color_scheme'] = isset( $input['color_scheme'] ) ? $input['color_scheme'] : '#E42029';
		}

		self::statistics( 1 );

		return $input;
	}

	/**
	 * Add links to settings page
	 *
	 * @param $links
	 * @param $file
	 *
	 * @return mixed
	 */
	public function plugins_page_settings_link( $links, $file ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $links;
		}

		$plugin = plugin_basename( self::$plugin_path );

		if ( $file === $plugin ) {
			array_unshift( $links,
				sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=mistape_settings' ),
					__( 'Settings', 'mistape' ) ) );
		}

		return $links;
	}

	/**
	 * Add initial options
	 *
	 * @param null $blog_id
	 */
	public static function activation( $blog_id = null ) {
		$blog_id = (int) $blog_id;

		if ( empty( $blog_id ) ) {
			$blog_id = get_current_blog_id();
		}
		$options = self::get_options();
		if ( get_current_blog_id() == $blog_id ) {
			add_option( 'mistape_options', $options, '', 'yes' );
			add_option( 'mistape_version', self::$version, '', 'no' );
		} else {
			switch_to_blog( $blog_id );
			add_option( 'mistape_options', $options, '', 'yes' );
			add_option( 'mistape_version', self::$version, '', 'no' );
			restore_current_blog();
		}

		if ( ! empty( $options['addons_to_activate'] ) ) {
			if ( function_exists( 'activate_plugins' ) ) {
				activate_plugins( $options['addons_to_activate'] );
				unset( $options['addons_to_activate'] );
				update_option( 'mistape_options', $options );
			}
		}

		self::create_db();

		self::statistics( 1 );
	}

	public static function deactivate_addons() {
		if ( function_exists( 'deactivate_plugins' ) ) {
			$active_and_valid_plugins = wp_get_active_and_valid_plugins();
			$active_and_valid_plugins = implode( ',', $active_and_valid_plugins );
			$deactivated              = array();
			foreach ( static::$supported_addons as $addon ) {
				$plugin = $addon . '/' . $addon . '.php';
//				$plugin = $addon . '.php';
				if ( false !== strpos( $active_and_valid_plugins, $plugin ) ) {
					deactivate_plugins( $plugin, true );
					$deactivated[] = $plugin;
				}
			}
			if ( ! empty( $deactivated ) ) {
				$options                       = self::get_options();
				$options['addons_to_activate'] = $deactivated;
				update_option( 'mistape_options', $options );
			}
			self::statistics( 0 );
		}
	}

	/**
	 * Delete settings on plugin uninstall
	 */
	public static function uninstall_cleanup() {
		global $wpdb;

		$table_name = "mistape_reports";
		$sql        = "DROP TABLE IF EXISTS $table_name;";
		$wpdb->query( $sql );

		delete_option( 'mistape_options' );
		delete_option( 'mistape_version' );
	}

	/**
	 * Load scripts and styles - admin
	 *
	 * @param $page
	 */
	public function admin_load_scripts_styles( $page ) {
		if ( strpos( $page, '_page_mistape_settings', true ) === false ) {
			return;
		}

		// Add the color picker css file
		wp_enqueue_style( 'wp-color-picker' );

		$this->enqueue_dialog_assets();

		// admin page script
		wp_enqueue_script( 'mistape-admin', plugins_url( 'assets/js/admin.js', self::$plugin_path ), array(
			'mistape-front',
			'wp-color-picker'
		), self::$version, true );

		// admin page style
		wp_register_style( 'mistape_admin_style', plugins_url( 'assets/css/mistape-admin.css', self::$plugin_path ) );
		wp_enqueue_style( 'mistape_admin_style' );
	}

	/**
	 * Add admin notice after activation if not configured
	 */
	public function plugin_activated_notice() {
		$wp_screen = get_current_screen();
		if ( $this->options['first_run'] == 'yes' && current_user_can( 'manage_options' ) ) {
			$html = '<div class="updated">';
			$html .= '<p>';
			if ( $wp_screen && $wp_screen->id == 'settings_page_mistape' ) {
				$html .= __( '<strong>Mistape</strong> settings notice will be dismissed after saving changes.',
					'mistape' );
			} else {
				$html .= sprintf( __( '<strong>Mistape</strong> must now be <a href="%s">configured</a> before use.',
					'mistape' ), admin_url( 'options-general.php?page=mistape_settings' ) );
			}
			$html .= '</p>';
			$html .= '</div>';
			echo $html;
		}
	}

	/**
	 * Get admins list for options page
	 *
	 * @param $role
	 *
	 * @return array
	 */
	public function get_user_list_by_role( $role ) {
		$users_query = get_users( array(
			'role'    => $role,
			'fields'  => array(
				'ID',
				'user_nicename',
				'user_email',
			),
			'orderby' => 'display_name'
		) );

		return $users_query;
	}

	/**
	 * Return an array of registered post types with their labels
	 */
	public function get_post_types_list() {
		$post_types = get_post_types(
			array( 'public' => true ),
			'objects'
		);

		$post_types_list = array();
		foreach ( $post_types as $id => $post_type ) {
			$post_types_list[ $id ] = $post_type->label;
		}

		return $post_types_list;
	}

	/**
	 * Echo Help tab contents
	 */
	private static function print_help_page() {
		?>
		<div class="card">
			<h3><?php _e( 'Shortcodes', 'mistape' ) ?></h3>
			<h4><?php _e( 'Optional shortcode parameters are:', 'mistape' ) ?></h4>
			<ul>
				<li><code>'format', </code> — <?php _e( "can be 'text' or 'image'", 'mistape' ) ?></li>
				<li><code>'class', </code> — <?php _e( 'override default css class', 'mistape' ) ?></li>
				<li><code>'text', </code> — <?php _e( 'override caption text', 'mistape' ) ?></li>
				<li><code>'image', </code> — <?php _e( 'override image URL', 'mistape' ) ?></li>
			</ul>
			<p><?php _e( 'When no parameters specified, general configuration is used.', 'mistape' ) ?><br>
				<?php _e( 'If image url is specified, format parameter can be omitted.', 'mistape' ) ?></p>
			<h4><?php _e( 'Shortcode usage example:', 'mistape' ) ?></h4>
			<ul>
				<li><p><code>[mistape format="text" class="mistape_caption_sidebar"]</code></p></li>
			</ul>
			<h4><?php _e( 'PHP code example:', 'mistape' ) ?></h4>
			<ul>
				<li><p><code>&lt;?php do_shortcode( '[mistape format="image" class="mistape_caption_footer"
							image="/wp-admin/images/yes.png"]' ); ?&gt;</code></p></li>
			</ul>
		</div>

		<div class="card">
			<h3><?php _e( 'Hooks', 'mistape' ) ?></h3>

			<ul>

				<li class="mistape-hook-block">
					<code>'mistape_caption_text', <span class="mistape-var-str">$text</span></code>
					<p class="description"><?php _e( 'Allows to modify caption text globally (preferred over HTML filter).',
							'mistape' ) ?></p>
				</li>

				<li class="mistape-hook-block">
					<code>'mistape_caption_output', <span class="mistape-var-str">$html</span>, <span
							class="mistape-var-arr">$options</span></code></code>
					<p class="description"><?php _e( 'Allows to modify the caption HTML before output.',
							'mistape' ) ?></p>
				</li>

				<li class="mistape-hook-block">
					<code>'mistape_dialog_args', <span class="mistape-var-arr">$args</span></code>
					<p class="description"><?php _e( 'Allows to modify modal dialog strings (preferred over HTML filter).',
							'mistape' ) ?></p>
				</li>

				<li class="mistape-hook-block">
					<code>'mistape_dialog_output', <span class="mistape-var-str">$html</span>, <span
							class="mistape-var-arr">$options</span></code></code>
					<p class="description"><?php _e( 'Allows to modify the modal dialog HTML before output.',
							'mistape' ) ?></p>
				</li>

				<li class="mistape-hook-block">
					<code>'mistape_custom_email_handling', <span class="mistape-var-bool">$stop</span>,
						<span class="mistape-var-obj">$mistape_object</span></code>
					<p class="description"><?php _e( 'Allows to override email sending logic.', 'mistape' ) ?></p>
				</li>

				<li class="mistape-hook-block">
					<code>'mistape_mail_recipient', <span class="mistape-var-str">$recipient</span>, <span
							class="mistape-var-str">$url</span>, <span class="mistape-var-obj">$user</span></code>
					<p class="description"><?php _e( 'Allows to change email recipient.', 'mistape' ) ?></p>
				</li>

				<li class="mistape-hook-block">
					<code>'mistape_mail_subject', <span class="mistape-var-str">$subject</span>, <span
							class="mistape-var-str">$referrer</span>, <span
							class="mistape-var-obj">$user</span></code>
					<p class="description"><?php _e( 'Allows to change email subject.', 'mistape' ) ?></p>
				</li>

				<li class="mistape-hook-block">
					<code>'mistape_mail_message', <span class="mistape-var-str">$message</span>, <span
							class="mistape-var-str">$referrer</span>, <span
							class="mistape-var-obj">$user</span></code>
					<p class="description"><?php _e( 'Allows to modify email message to send.', 'mistape' ) ?></p>
				</li>

				<li class="mistape-hook-block">
					<code>'mistape_custom_email_handling', <span class="mistape-var-bool">$stop</span>, <span
							class="mistape-var-obj">$ajax_obj</span></code>
					<p class="description"><?php _e( 'Allows for custom reports handling. Refer to code for implementation details.',
							'mistape' ) ?></p>
				</li>

				<li class="mistape-hook-block">
					<code>'mistape_options', <span class="mistape-var-arr">$options</span></code>
					<p class="description"><?php _e( 'Allows to modify global options array during initialization.',
							'mistape' ) ?></p>
				</li>

				<li class="mistape-hook-block">
					<code>'mistape_is_appropriate_post', <span class="mistape-var-bool">$result</span></code>
					<p class="description"><?php _e( 'Allows to add custom logic for whether to output Mistape to front end or not.',
							'mistape' ) ?></p>
				</li>

			</ul>
		</div>
		<?php
	}

	public function insert_dialog() {
		$args = array(
			'reported_text_preview' => 'Lorem <span class="mistape_mistake_highlight">upsum</span> dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
		);
		echo $this->get_dialog_html( $args );
	}

	public function plugin_row_meta( $links, $file ) {

		if ( 'mistape/mistape.php' == $file ) {

			$links[] = '<a href="https://wordpress.org/support/plugin/mistape/reviews/#postform" target="_blank" class="mistape-vote"><span class="dashicons dashicons-thumbs-up" style="margin-top: -3px;"></span> ' . __( 'Vote!', 'mistape' ) . '</a>';
		}

		return $links;
	}

}