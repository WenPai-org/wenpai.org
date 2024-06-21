<?php
gp_title( __('在线协作 &lt; 文派翻译平台') );
gp_enqueue_script('common');
gp_tmpl_header();
?>
<?php echo do_shortcode("[insert page='1778' display='content']"); ?>

	<div class="filter-header" style="display: none">
		<ul class="filter-header-links">
			<li><span class="current"><?php _e( '选择地区' ); ?></span></li>
			<!--<li><a href="/stats"><?php /*_e( '统计' ); */?></a></li>-->
			<!--<li><a href="/consistency"><?php /*_e( '一致性' ); */?></a></li>-->
		</ul>
		<div class="search-form">
			<label class="screen-reader-text" for="locales-filter"><?php esc_attr_e( '搜索地区...' ); ?></label>
			<input placeholder="<?php esc_attr_e( '搜索地区...' ); ?>" type="search" id="locales-filter" class="filter-search">
		</div>
	</div>

	<p class="intro" style="display: none">如果您的地区没有列出，请前往 <a href="https://wenpai.org/support/">支持论坛</a> 申请添加。</p>

	<div id="locales" class="locales" style="display: none">
		<?php foreach ( $locales as $locale ) :
			$percent_complete = 0;
			if ( isset( $translation_status[ $locale->slug ] ) ) {
				$status = $translation_status[ $locale->slug ];
				$percent_complete = floor( $status->current_count / $status->all_count * 100 );
			}

			$wp_locale = ( isset( $locale->wp_locale ) ) ? $locale->wp_locale : $locale->slug;
			?>
			<div class="locale <?php echo 'percent-' . $percent_complete; ?>">
				<ul class="name">
					<li class="english"><?php echo gp_link_get( gp_url_join( '/locale', $locale->slug ), $locale->english_name ) ?></li>
					<li class="native"><?php echo gp_link_get( gp_url_join( '/locale', $locale->slug ), $locale->native_name ) ?></li>
					<li class="code"><?php echo gp_link_get( gp_url_join( '/locale', $locale->slug ), $wp_locale ) ?></li>
				</ul>
				<div class="contributors">
					<?php
					$contributors = sprintf(
						'<span class="dashicons dashicons-admin-users"></span><br />%s',
						isset( $contributors_count[ $locale->slug ] ) ? $contributors_count[ $locale->slug ] : 0
					);
					echo gp_link_get( 'https://make.wordpress.org/polyglots/teams/?locale=' . $locale->wp_locale, $contributors );
					?>
				</div>
				<div class="percent">
					<div class="percent-complete" style="width:<?php echo $percent_complete; ?>%;"></div>
				</div>
				<div class="locale-button">
					<?php echo gp_link_get( gp_url_join( '/locale', $locale->slug ), '贡献翻译', [ 'class' => 'button contribute-button' ] ); ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<script>
		jQuery( document ).ready( function( $ ) {
			$rows = $( '#locales' ).find( '.locale' );
			$( '#locales-filter' ).on( 'input keyup',function() {
				var words = this.value.toLowerCase().split( ' ' );

				if ( '' === this.value.trim() ) {
					$rows.show();
				} else {
					$rows.hide();
					$rows.filter( function( i, v ) {
						var $t = $(this).find( '.name' );
						for ( var d = 0; d < words.length; ++d ) {
							if ( $t.text().toLowerCase().indexOf( words[d] ) != -1 ) {
								return true;
							}
						}
						return false;
					}).show();
				}
			});
		});
	</script>

<?php gp_tmpl_footer();
