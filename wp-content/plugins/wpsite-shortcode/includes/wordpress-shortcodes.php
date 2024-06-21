<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

?>

    <div class="wpsite-shortcode-columns" id="general-tab">
      <!-- General Shortcodes -->
      <!-- Add general shortcodes here -->
      <div class="wpsite-shortcode-columns">
        <div class="wpsite-shortcode-column">
          <h3><span class="dashicons dashicons-admin-settings"></span> <?php _e( 'Settings Shortcode', 'wpsite-shortcode' ); ?></h3>
          <p><?php _e( 'Use the shortcode below to display your WordPress Site Settings.', 'wpsite-shortcode' ); ?></p>
          <p><?php _e( 'Site Url', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_url]</code>
          <p><?php _e( 'Site Home', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_home]</code>
          <p><?php _e( 'Site Title', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_title]</code>
          <p><?php _e( 'Tagline', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_tagline]</code>
          <p><?php _e( 'Administration Email Address', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_email]</code>
          <p><?php _e( 'Date', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_date]</code>
          <p><?php _e( 'Time', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_time]</code>
        </div><!-- Settings Shortcode End -->

        <div class="wpsite-shortcode-column">
          <h3><span class="dashicons dashicons-admin-users"></span> <?php _e( 'User Shortcode', 'wpsite-shortcode' ); ?></h3>
          <p><?php _e( 'Use the shortcode below to display your WordPress Current User Detail.', 'wpsite-shortcode' ); ?></p>
          <p><?php _e( 'User Login', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_userlogin]</code>
          <p><?php _e( 'User Name', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_username]</code>
          <p><?php _e( 'Nick Name', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_nickname]</code>
          <p><?php _e( 'User EMail', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_useremail]</code>
          <p><?php _e( 'User Avatar', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_avatar]</code>
          <p><?php _e( 'User Role Name', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_role]</code>
          <p><?php _e( 'User Role', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_userrole]</code>
          <p><?php _e( 'User Bio', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_userbio]</code>
          <p><?php _e( 'User Website', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_website]</code>
          <p><?php _e( 'User Registered Date', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_userdate]</code>
          <p><?php _e( 'User Last Login', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_lastlogin]</code>
        </div><!-- User Shortcode End -->

        <div class="wpsite-shortcode-column">
          <h3><span class="dashicons dashicons-admin-post"></span> <?php _e( 'Post Shortcode', 'wpsite-shortcode' ); ?></h3>
          <p><?php _e( 'Use the shortcode below to display your WordPress Post/Pages Metadata.', 'wpsite-shortcode' ); ?></p>
          <p><?php _e( 'Post Title', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_posttitle]</code>
          <p><?php _e( 'Post Excerpt', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_postexcerpt]</code>
          <p><?php _e( 'Post Author', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_postauthor]</code>
          <p><?php _e( 'Post Date', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_postdate]</code>
          <p><?php _e( 'Post Modified Date', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_modifieddate]</code>
          <p><?php _e( 'Post Slug', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_postslug]</code>
          <p><?php _e( 'Post Url', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_posturl]</code>
          <p><?php _e( 'Post Featured Image', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_postimage]</code>
          <p><?php _e( 'Post Tags', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_posttag]</code>
          <p><?php _e( 'Post Categories', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_postcat]</code>
          <p><?php _e( 'Post Parent Category', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_parentcat]</code>
          <p><?php _e( 'Post Comments Count', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_commentscount]</code>
        </div><!-- Post Shortcode End -->

        <div class="wpsite-shortcode-column">
          <h3><span class="dashicons dashicons-admin-media"></span> <?php _e( 'Media Shortcode', 'wpsite-shortcode' ); ?></h3>
          <p><?php _e( 'Use the shortcode below to display your WordPress Media Library items.', 'wpsite-shortcode' ); ?></p>
          <p><?php _e( 'Show Image', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_images]</code>
          <p><?php _e( 'Show Audio', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_audio]</code>
          <p><?php _e( 'Show Video', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_video]</code>
          <p><?php _e( 'Show Document', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_documents]</code>
          <p><?php _e( 'Show Spreadsheets', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_spreadsheets]</code>
      </div><!-- Media Shortcode End -->

      <div class="wpsite-shortcode-column">
          <h3><span class="dashicons dashicons-admin-appearance"></span> <?php _e( 'Widgets Shortcode', 'wpsite-shortcode' ); ?></h3>
          <p><?php _e( 'Use the shortcode below to display your WordPress Default Widget.', 'wpsite-shortcode' ); ?></p>
          <p><?php _e( 'Search Widget', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_search]</code>
          <p><?php _e( 'Calendar Widget', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_calendar]</code>
          <p><?php _e( 'Tags Widget', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_tags]</code>
          <p><?php _e( 'Recent Posts Widget', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_recentposts]</code>
          <p><?php _e( 'Recent Comments Widget', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_recentcomments]</code>
     </div><!-- Widget Shortcode End -->

     <div class="wpsite-shortcode-column">
          <h3><span class="dashicons dashicons-coffee"></span> <?php _e( 'Date Shortcode', 'wpsite-shortcode' ); ?></h3>
          <p><?php _e( 'Use the shortcode below to display your WordPress Default Widget.', 'wpsite-shortcode' ); ?></p>
          <p><?php _e( 'Show Year', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_y]</code>
          <p><?php _e( 'Show Month', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_m]</code>
          <p><?php _e( 'Show Day', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_d]</code>
     </div><!-- Date Shortcode End -->

     <div class="wpsite-shortcode-column">
          <h3><span class="dashicons dashicons-carrot"></span> <?php _e( 'Character Shortcode', 'wpsite-shortcode' ); ?></h3>
          <p><?php _e( 'Use the shortcode below to display your Website Character.', 'wpsite-shortcode' ); ?></p>
          <p><?php _e( 'Copyright Character', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_c]</code>
          <p><?php _e( 'Registered Trademark Character', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_r]</code>
          <p><?php _e( 'TM Trademark Character', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_tm]</code>
          <p><?php _e( 'Service Mark Trademark Character', 'wpsite-shortcode' ); ?></p>
          <code>[wpsite_sm]</code>
     </div><!-- Date Shortcode End -->

     <div class="wpsite-shortcode-column">
         <h3><span class="dashicons dashicons-admin-generic"></span> <?php _e( 'Other Shortcode', 'wpsite-shortcode' ); ?></h3>
         <p><?php _e( 'More shortcodes will follow…', 'wpsite-shortcode' ); ?></p>
         <p><?php _e( 'If you need dynamic data shortcode for WooCommerce, bbPress, BuddyPress, we will add it in future version.', 'wpsite-shortcode' ); ?></p>
    </div><!-- Widget Shortcode End -->
    </div>
      </div><!-- /.wpsite-shortcode-columns -->
