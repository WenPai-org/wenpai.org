<?php

if ( empty( $suggestions ) ) {
	echo '<p class="no-suggestions">该功能仅限平台会员用户使用！</p>';
} else {
	echo '<ul class="suggestions-list">';
	foreach ( $suggestions as $suggestion ) {
		echo '<li>';
		echo '<div class="translation-suggestion with-tooltip" tabindex="0" role="button" aria-pressed="false" aria-label="Copy translation">';

		echo '<span class="translation-suggestion__translation">';
		echo esc_translation( $suggestion['translation'] );
		echo '</span>';

		echo '<span aria-hidden="true" class="translation-suggestion__translation-raw">' . esc_translation( $suggestion['translation'] ) . '</span>';

		echo '<button type="button" class="copy-suggestion btn btn-outline-primary">复制</button>';
		echo '</div>';
		echo '</li>';
	}
	echo '</ul>';
}
