<?php
// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Add settings menu
add_action( 'admin_menu', 'wpchat_autoreply_add_settings_menu' );

function wpchat_autoreply_add_settings_menu() {
	add_options_page( __( 'WPChat Autoreply Settings', 'wpchat-autoreply' ), __( 'WPChat Autoreply', 'wpchat-autoreply' ), 'manage_options', 'wpchat_autoreply_settings', 'wpchat_autoreply_settings_page' );
}

// Create settings page
function wpchat_autoreply_settings_page() {
    
    // Enqueue the CSS file
	wp_enqueue_style( 'wpchat-autoreply-style', plugins_url( 'assets/css/style.css', __FILE__ ) );

    // Enqueue the JavaScript file
	wp_enqueue_script( 'wpchat-autoreply-script', plugins_url( 'assets/js/setting.js', __FILE__ ), array(), '1.0', true );

	?>
	<div class="wrap">
		<h1><?php _e( 'WPChat Autoreply Settings', 'wpchat-autoreply' ); ?></h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'wpchat_autoreply_options' );
			do_settings_sections( 'wpchat_autoreply_settings' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

// Register settings
add_action( 'admin_init', 'wpchat_autoreply_register_settings' );

function wpchat_autoreply_register_settings() {
	register_setting( 'wpchat_autoreply_options', 'wpchat_autoreply_options', 'wpchat_autoreply_options_validate' );

	add_settings_section( 'wpchat_autoreply_main', __( 'Main Settings', 'wpchat-autoreply' ), null, 'wpchat_autoreply_settings' );

	add_settings_field( 'wpchat_autoreply_reply_type', __( 'Reply Type', 'wpchat-autoreply' ), 'wpchat_autoreply_reply_type_field', 'wpchat_autoreply_settings', 'wpchat_autoreply_main' );
	add_settings_field( 'wpchat_autoreply_reply_user', __( 'Reply User', 'wpchat-autoreply' ), 'wpchat_autoreply_reply_user_field', 'wpchat_autoreply_settings', 'wpchat_autoreply_main' );
	add_settings_field( 'wpchat_autoreply_information_sources', __( 'Custom Information Sources', 'wpchat-autoreply' ), 'wpchat_autoreply_information_sources_field', 'wpchat_autoreply_settings', 'wpchat_autoreply_main' );
	add_settings_field( 'wpchat_autoreply_accounts', __( 'Accounts', 'wpchat-autoreply' ), 'wpchat_autoreply_accounts_field', 'wpchat_autoreply_settings', 'wpchat_autoreply_main' );
	add_settings_field( 'wpchat_autoreply_api_urls', __( 'API URLs', 'wpchat-autoreply' ), 'wpchat_autoreply_api_urls_field', 'wpchat_autoreply_settings', 'wpchat_autoreply_main' );
}

// Reply Type
function wpchat_autoreply_reply_type_field() {
	$options    = get_option( 'wpchat_autoreply_options' );
	$reply_type = isset( $options['reply_type'] ) ? $options['reply_type'] : 'summary';
	?>
    	<div class="radio-container">
            <label>
                <input type="radio" name="wpchat_autoreply_options[reply_type]" value="summary" <?php checked( 'summary', $reply_type ); ?>>
                Summary
            </label>
            <label>
                <input type="radio" name="wpchat_autoreply_options[reply_type]" value="reply" <?php checked( 'reply', $reply_type ); ?>>
                Reply
            </label>
        </div>
    <?php
}

// Reply User
function wpchat_autoreply_reply_user_field() {
	$options       = get_option( 'wpchat_autoreply_options' );
	$selected_user = isset( $options['reply_user'] ) ? $options['reply_user'] : '';
	$users         = get_users();
	?>
	<input type="text" id="user-search" onkeyup="filterFunction()" placeholder="搜索用户...">

	<?php
	echo '<select id="user-select" name="wpchat_autoreply_options[reply_user]">';
	foreach ( $users as $user ) {
		echo '<option value="' . $user->ID . '" ' . selected( $user->ID, $selected_user, false ) . '>' . $user->display_name . '</option>';
	}
	echo '</select>';
}


// Custom Information Sources
function wpchat_autoreply_information_sources_field() {
	$options             = get_option( 'wpchat_autoreply_options' );
	$information_sources = isset( $options['information_sources'] ) ? $options['information_sources'] : '';
	echo '<textarea name="wpchat_autoreply_options[information_sources]" rows="5" cols="50">' . esc_textarea( $information_sources ) . '</textarea>';
	?>
    <?php
}

// Accounts
function wpchat_autoreply_accounts_field() {
	$options  = get_option( 'wpchat_autoreply_options' );
	$accounts = isset( $options['accounts'] ) && is_array( $options['accounts'] ) ? $options['accounts'] : array();
	?>
	<div id="accounts-container">
		<?php foreach ( $accounts as $index => $account ): ?>
			<div class="account-group">
				<input type="text" name="wpchat_autoreply_options[accounts][<?php echo $index; ?>][token]"
				       value="<?php echo esc_attr( $account['token'] ); ?>">
				<button type="button" class="remove-account"><?php _e( 'Remove', 'wpchat-autoreply' ); ?></button>
			</div>
		<?php endforeach; ?>
	</div>
	<button type="button" id="add-account"><?php _e( 'Add Account', 'wpchat-autoreply' ); ?></button>
<script>
        (function () {
            var accountsContainer = document.getElementById('accounts-container');
            var addAccountButton = document.getElementById('add-account');
            var accountIndex = <?php echo count( $accounts ); ?>;
            addAccountButton.addEventListener('click', function () {
                var accountGroup = document.createElement('div');
                accountGroup.classList.add('account-group');

                var usernameInput = document.createElement('input');
                usernameInput.type = 'text';
                usernameInput.name = 'wpchat_autoreply_options[accounts][' + accountIndex + '][token]';
                accountGroup.appendChild(usernameInput);

                var removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.textContent = '<?php _e( 'Remove', 'wpchat-autoreply' ); ?>';
                removeButton.classList.add('remove-account');
                removeButton.addEventListener('click', function () {
                    accountGroup.remove();
                });
                accountGroup.appendChild(removeButton);

                accountsContainer.appendChild(accountGroup);
                accountIndex++;
            });

            var removeButtons = document.getElementsByClassName('remove-account');
            for (var i = 0; i < removeButtons.length; i++) {
                removeButtons[i].addEventListener('click', function () {
                    this.parentElement.remove();
                });
            }
        })();
</script>
	<?php
}

// API URLs setting
function wpchat_autoreply_api_urls_field() {
	$options  = get_option( 'wpchat_autoreply_options' );
	$api_urls = isset( $options['api_urls'] ) && is_array( $options['api_urls'] ) ? $options['api_urls'] : array();
	?>
	<div id="api-urls-container">
		<?php foreach ( $api_urls as $index => $url ): ?>
			<div class="api-url-group">
				<input type="url" name="wpchat_autoreply_options[api_urls][<?php echo $index; ?>]"
				       value="<?php echo esc_attr( $url ); ?>">
				<button type="button" class="remove-api-url"><?php _e( 'Remove', 'wpchat-autoreply' ); ?></button>
			</div>
		<?php endforeach; ?>
	</div>
	<button type="button" id="add-api-url"><?php _e( 'Add API URL', 'wpchat-autoreply' ); ?></button>
		<script>
        (function () {
            var apiUrlsContainer = document.getElementById('api-urls-container');
            var addApiUrlButton = document.getElementById('add-api-url');
            var apiUrlIndex = <?php echo count( $api_urls ); ?>;
            addApiUrlButton.addEventListener('click', function () {
                var apiUrlGroup = document.createElement('div');
                apiUrlGroup.classList.add('api-url-group');

                var apiUrlInput = document.createElement('input');
                apiUrlInput.type = 'url';
                apiUrlInput.name = 'wpchat_autoreply_options[api_urls][' + apiUrlIndex + ']';
                apiUrlGroup.appendChild(apiUrlInput);

                var removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.textContent = '<?php _e( 'Remove', 'wpchat-autoreply' ); ?>';
                removeButton.classList.add('remove-api-url');
                removeButton.addEventListener('click', function () {
                    apiUrlGroup.remove();
                });
                apiUrlGroup.appendChild(removeButton);

                apiUrlsContainer.appendChild(apiUrlGroup);
                apiUrlIndex++;
            });

            var removeButtons = document.getElementsByClassName('remove-api-url');
            for (var i = 0; i < removeButtons.length; i++) {
                removeButtons[i].addEventListener('click', function () {
                    this.parentElement.remove();
                });
            }
        })();
	</script>
	<?php
}

// Validate settings
function wpchat_autoreply_options_validate( $input ) {
	$input['reply_type']          = sanitize_text_field( $input['reply_type'] );
	$input['reply_user']          = intval( $input['reply_user'] );
	if ( is_array( $input['accounts'] ) ) {
		foreach ( $input['accounts'] as $index => $account ) {
			$input['accounts'][ $index ]['token'] = sanitize_text_field( $account['token'] );
		}
	}
	if ( is_array( $input['api_urls'] ) ) {
		foreach ( $input['api_urls'] as $index => $url ) {
			$input['api_urls'][ $index ] = esc_url_raw( $url );
		}
	}

	return $input;
}
