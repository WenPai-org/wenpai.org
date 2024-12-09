/* General */

html,
body {
	background: <?php echo esc_attr( $general_background ); ?>;
}

/* Links */

a {
	color: <?php echo esc_attr( $links_static_default ); ?>;
}
a:hover, a:active, a:focus {
	color: <?php echo esc_attr( $links_static_default_hover ); ?>;
}

#rightnow a:hover,
#media-upload a.del-link:hover,
div.dashboard-widget-submit input:hover,
.subsubsub a:hover,
.subsubsub a.current:hover,
.ui-tabs-nav a:hover,
.plugins .inactive a:hover {
	color: <?php echo esc_attr( $links_static_default_hover ); ?>;
}

table.widefat span.delete a,
table.widefat span.trash a,
table.widefat span.spam a,
.plugins a.delete,
#all-plugins-table .plugins a.delete,
#search-plugins-table .plugins a.delete,
.submitbox .submitdelete,
#media-items a.delete,
#media-items a.delete-permanently,
#nav-menu-footer .menu-delete {
	color: <?php echo esc_attr( $links_static_delete ); ?>;
}

table.widefat span.delete a:hover,
table.widefat span.trash a:hover,
table.widefat span.spam a:hover,
.plugins a.delete:hover,
#all-plugins-table .plugins a.delete:hover,
#search-plugins-table .plugins a.delete:hover,
.submitbox .submitdelete:hover,
#media-items a.delete:hover,
#media-items a.delete-permanently:hover,
#nav-menu-footer .menu-delete:hover {
	color: <?php echo esc_attr( $links_static_delete_hover ); ?>;
}

.plugins .inactive a {
	color: <?php echo esc_attr( $links_static_inactive ); ?>;
}
.plugins .inactive a:hover {
	color: <?php echo esc_attr( $links_static_inactive_hover ); ?>;
}

/* Forms */

input[type=checkbox]:checked:before {
	color: <?php echo esc_attr( $form_checkbox ); ?>;
}

input[type=radio]:checked:before {
	background: <?php echo esc_attr( $form_checkbox ); ?>;
}

.wp-core-ui input[type="reset"]:hover,
.wp-core-ui input[type="reset"]:active {
	color: <?php echo esc_attr( $form_checkbox ); ?>;
}

/* Core UI */

.wp-core-ui .button-primary {
	background: <?php echo esc_attr( $core_ui_primary_button_background ); ?>;
	border-color: <?php echo esc_attr( $core_ui_primary_button_background ); ?>;
	color: <?php echo esc_attr( $core_ui_primary_button_color ); ?>;
	-webkit-box-shadow: inset 0 1px 0 <?php echo esc_attr( $core_ui_primary_button_background ); ?>, 0 1px 0 rgba(0, 0, 0, 0.15);
	box-shadow: inset 0 1px 0 <?php echo esc_attr( $core_ui_primary_button_background ); ?>, 0 1px 0 rgba(0, 0, 0, 0.15);
	<?php
	$color = esc_attr( $core_ui_primary_button_shadow_color );
	?>
	text-shadow: 0 -1px 1px <?php echo $color; ?>, 1px 0 1px <?php echo $color; ?>, 0 1px 1px <?php echo $color; ?>, -1px 0 1px <?php echo $color; ?>;
}

<?php $color = esc_attr( esc_attr( $core_ui_primary_button_shadow_color_hover ) ); ?>
.wp-core-ui .button-primary:hover,
.wp-core-ui .button-primary:focus {
	background: <?php echo esc_attr( $core_ui_primary_button_background_hover ); ?>;
	border-color: <?php echo esc_attr( $core_ui_primary_button_background_hover ); ?>;
	color: <?php echo esc_attr( $core_ui_primary_button_color_hover ); ?>;
	-webkit-box-shadow: inset 0 1px 0 <?php echo esc_attr( $core_ui_primary_button_background_hover ); ?>, 0 1px 0 rgba(0, 0, 0, 0.15);
	box-shadow: inset 0 1px 0 <?php echo esc_attr( $core_ui_primary_button_background_hover ); ?>, 0 1px 0 rgba(0, 0, 0, 0.15);
	text-shadow: 0 -1px 1px <?php echo $color; ?>, 1px 0 1px <?php echo $color; ?>, 0 1px 1px <?php echo $color; ?>, -1px 0 1px <?php echo $color; ?>;
}

.wp-core-ui .button-primary:active {
	background: <?php echo esc_attr( $core_ui_primary_button_background ); ?>;
	border-color: <?php echo esc_attr( $core_ui_primary_button_background ); ?>;
	color: <?php echo esc_attr( $core_ui_primary_button_color ); ?>;
	-webkit-box-shadow: inset 0 2px 5px -3px rgba(0, 0, 0, 0.5);
	box-shadow: inset 0 2px 5px -3px rgba(0, 0, 0, 0.5);
}

.wp-core-ui .button-primary[disabled],
.wp-core-ui .button-primary:disabled,
.wp-core-ui .button-primary.button-primary-disabled,
 {
	color: <?php echo esc_attr( $core_ui_disabled_button_color ); ?> !important;
	background: <?php echo esc_attr( $core_ui_disabled_button_background ); ?> !important;
	border-color: <?php echo esc_attr( $core_ui_disabled_button_background ); ?> !important;
	text-shadow: none !important;
}

/* List tables */

.wrap .add-new-h2:hover,
#add-new-comment a:hover,
.tablenav .tablenav-pages a:hover,
.tablenav .tablenav-pages a:focus {
	color: white;
	background-color: <?php echo esc_attr( $list_tables_pagination_hover ); ?>;
}

.view-switch a.current:before {
	color: <?php echo esc_attr( $list_tables_switch_icon ); ?>;
}

.view-switch a:hover:before {
	color: <?php echo esc_attr( $list_tables_switch_icon_hover ); ?>;
}

.column-comments .post-com-count-approved:focus:after,
.column-comments .post-com-count-approved:hover:after,
.column-response .post-com-count-approved:focus:after,
.column-response .post-com-count-approved:hover:after {
	border-top-color: <?php echo esc_attr( $list_tables_switch_icon_hover ); ?>;
}

.column-comments .post-com-count-approved:focus .comment-count-approved,
.column-comments .post-com-count-approved:hover .comment-count-approved,
.column-response .post-com-count-approved:focus .comment-count-approved,
.column-response .post-com-count-approved:hover .comment-count-approved {
	color: white;
	background-color: <?php echo esc_attr( $list_tables_switch_icon_hover ); ?>;
}

.column-comments .post-com-count-approved:after,
.column-comments .post-com-count-no-comments:after,
.column-response .post-com-count-approved:after,
.column-response .post-com-count-no-comments:after:after {
	border-top-color: <?php echo esc_attr( $list_tables_post_comment_count_hover ); ?>;
}

.column-comments .comment-count-approved,
.column-comments .comment-count-no-comments,
.column-response .comment-count-approved,
.column-response .comment-count-no-comments {
	background-color: <?php echo esc_attr( $list_tables_post_comment_count_hover ); ?>;
}

.alt,
.alternate,
.striped>tbody>:nth-child(odd),
ul.striped>:nth-child(odd) {
	background-color: <?php echo esc_attr( $list_tables_alternate_row ); ?>;
}

.wrap .add-new-h2,
.wrap .page-title-action,
.tablenav .tablenav-pages a,
.tablenav .tablenav-pages a {
	color: <?php echo esc_attr( $links_static_default ); ?>;
}

.wrap .add-new-h2:hover,
.wrap .page-title-action:hover,
.tablenav .tablenav-pages a:hover,
.tablenav .tablenav-pages a:focus {
	color: <?php echo esc_attr( $core_ui_primary_button_color ); ?>;
	background-color: <?php echo esc_attr( $admin_menu_background ); ?>;
}

.wrap .add-new-h2:hover,
.wrap .page-title-action:hover {
	border-color: <?php echo esc_attr( $core_ui_primary_button_background ); ?>;
}

/* Admin Menu */

#adminmenuback, #adminmenuwrap, #adminmenu {
	background: <?php echo esc_attr( $admin_menu_background ); ?>;
}

#adminmenu a,
#collapse-button {
	color: <?php echo esc_attr( $admin_menu_color ); ?>;
}

#adminmenu a:hover,
#adminmenu li.menu-top:hover,
#adminmenu li.opensub > a.menu-top,
#adminmenu li > a.menu-top:focus,
#collapse-button:hover {
	color: <?php echo esc_attr( $admin_menu_color_hover ); ?>;
	background-color: <?php echo esc_attr( $admin_menu_background_hover ); ?>;
}

#adminmenu div.wp-menu-image:before,
#collapse-button .collapse-button-icon:after {
	color: <?php echo esc_attr( $admin_menu_icon_color ); ?>;
}

#adminmenu li.menu-top:hover div.wp-menu-image:before,
#adminmenu li.opensub > a.menu-top div.wp-menu-image:before,
#adminmenu #collapse-menu:hover .collapse-button-icon:after {
	color: <?php echo esc_attr( $admin_menu_icon_color_focus ); ?>;
}

/* Active tabs use a bottom border color that matches the page background color. */

.about-wrap h2 .nav-tab-active,
.nav-tab-active,
.nav-tab-active:hover {
	border-bottom-color: <?php echo esc_attr( $admin_menu_background ); ?>;
}

/* Admin Menu: submenu */

#adminmenu .wp-submenu,
#adminmenu .wp-has-current-submenu .wp-submenu,
#adminmenu .wp-has-current-submenu.opensub .wp-submenu,
.folded #adminmenu .wp-has-current-submenu .wp-submenu,
#adminmenu a.wp-has-current-submenu:focus + .wp-submenu {
	background: <?php echo esc_attr( $admin_menu_submenu_background ); ?>;
}

#adminmenu li.wp-has-submenu.wp-not-current-submenu.opensub:hover:after {
	border-right-color: <?php echo esc_attr( $admin_menu_submenu_background ); ?>;
}

#adminmenu .wp-submenu a,
#adminmenu .wp-has-current-submenu .wp-submenu a,
.folded #adminmenu .wp-has-current-submenu .wp-submenu a,
#adminmenu a.wp-has-current-submenu:focus + .wp-submenu a,
#adminmenu .wp-has-current-submenu.opensub .wp-submenu a,
#adminmenu .wp-submenu .wp-submenu-head {
	color: <?php echo esc_attr( $admin_menu_submenu_link ); ?>;
}

#adminmenu .wp-submenu a:focus,
#adminmenu .wp-submenu a:hover,
#adminmenu .wp-has-current-submenu .wp-submenu a:focus,
#adminmenu .wp-has-current-submenu .wp-submenu a:hover,
.folded #adminmenu .wp-has-current-submenu .wp-submenu a:focus,
.folded #adminmenu .wp-has-current-submenu .wp-submenu a:hover,
#adminmenu a.wp-has-current-submenu:focus + .wp-submenu a:focus,
#adminmenu a.wp-has-current-submenu:focus + .wp-submenu a:hover,
#adminmenu .wp-has-current-submenu.opensub .wp-submenu a:focus,
#adminmenu .wp-has-current-submenu.opensub .wp-submenu a:hover {
	color: <?php echo esc_attr( $admin_menu_submenu_link_hover ); ?>;
}

/* Admin Menu: current */

#adminmenu .wp-submenu li.current a,
#adminmenu a.wp-has-current-submenu:focus + .wp-submenu li.current a,
#adminmenu .wp-has-current-submenu.opensub .wp-submenu li.current a {
	color: <?php echo esc_attr( $admin_menu_color_current ); ?>;
}

#adminmenu .wp-submenu li.current a:hover,
#adminmenu .wp-submenu li.current a:focus,
#adminmenu a.wp-has-current-submenu:focus + .wp-submenu li.current a:hover,
#adminmenu a.wp-has-current-submenu:focus + .wp-submenu li.current a:focus,
#adminmenu .wp-has-current-submenu.opensub .wp-submenu li.current a:hover,
#adminmenu .wp-has-current-submenu.opensub .wp-submenu li.current a:focus {
	color: <?php echo esc_attr( $admin_menu_color_current_hover ); ?>;
}

ul#adminmenu a.wp-has-current-submenu:after,
ul#adminmenu > li.current > a.current:after {
	border-right-color: <?php echo esc_attr( $admin_menu_background_curent ); ?>;
}

#adminmenu li.current a.menu-top,
#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu, #adminmenu li.wp-has-current-submenu .wp-submenu .wp-submenu-head, .folded #adminmenu li.current.menu-top {
	color: <?php echo esc_attr( $admin_menu_color_current ); ?>;
	background: <?php echo esc_attr( $admin_menu_background_curent ); ?>;
}

#adminmenu li.wp-has-current-submenu div.wp-menu-image:before {
	color: <?php echo esc_attr( $admin_menu_icon_color_current ); ?>;
}

/* Admin Menu: bubble */

#adminmenu .awaiting-mod, #adminmenu .update-plugins {
	color: <?php echo esc_attr( $admin_menu_bubble_color ); ?>;
	background: <?php echo esc_attr( $admin_menu_bubble_background ); ?>;
}

#adminmenu li.current a .awaiting-mod,
#adminmenu li a.wp-has-current-submenu .update-plugins,
#adminmenu li:hover a .awaiting-mod {
	color: <?php echo esc_attr( $admin_menu_color_current ); ?>;
	background-color: <?php echo esc_attr( $admin_menu_background ); ?>;
}

#adminmenu li.menu-top:hover > a .update-plugins {
	color: <?php echo esc_attr( $core_ui_primary_button_color ); ?>;
	background-color: <?php echo esc_attr( $admin_menu_background ); ?>;
}

/* Admin Menu: collapse button */

#collapse-menu {
	color: <?php echo esc_attr( $admin_menu_color_current ); ?>;
}

#collapse-menu:hover {
	color: <?php echo esc_attr( $admin_menu_color_current_hover ); ?>;
}

#collapse-button div:after {
	color: <?php echo esc_attr( $admin_menu_icon_color_current ); ?>;
}

#collapse-menu:hover #collapse-button div:after {
	color: <?php echo esc_attr( $admin_menu_icon_color_current ); ?>;
}

/* Admin Bar */

#wpadminbar {
	color: <?php echo esc_attr( $admin_bar_color ); ?>;
	background: <?php echo esc_attr( $admin_bar_background ); ?>;
}

#wpadminbar .ab-item,
#wpadminbar a.ab-item,
#wpadminbar > #wp-toolbar span.ab-label,
#wpadminbar > #wp-toolbar span.noticon {
	color: <?php echo esc_attr( $admin_bar_color ); ?>;
}

#wpadminbar .ab-icon,
#wpadminbar .ab-icon:before,
#wpadminbar .ab-item:before,
#wpadminbar .ab-item:after {
	color: <?php echo esc_attr( $admin_bar_icon_color ); ?>;
}

#wpadminbar .ab-top-menu > li.hover > .ab-item,
#wpadminbar .ab-top-menu > li.menupop.hover > .ab-item,
#wpadminbar-nojs .ab-top-menu > li.menupop:hover > .ab-item {
	color: #fff;
	background: <?php echo esc_attr( $admin_bar_item_background_hover ); ?>;
}

#wpadminbar .ab-top-menu>li.hover>.ab-item,
#wpadminbar:not(.mobile) .ab-top-menu>li:hover>.ab-item {
	color: <?php echo esc_attr( $admin_bar_item_color_hover ); ?>;
	background: <?php echo esc_attr( $admin_bar_item_background_hover ); ?>;
}

#wpadminbar > #wp-toolbar li.hover span.ab-label,
#wpadminbar .ab-top-menu > li.hover > .ab-item,
#wpadminbar .ab-top-menu > li.menupop.hover > .ab-item,
#wpadminbar-nojs .ab-top-menu > li.menupop:hover > .ab-item,
#wpadminbar:not(.mobile)>#wp-toolbar li:hover span.ab-label {
	color: <?php echo esc_attr( $admin_bar_item_color_hover ); ?>;
}

#wpadminbar li:hover .ab-icon:before,
#wpadminbar li:hover .ab-item:before,
#wpadminbar li:hover .ab-item:after,
#wpadminbar li:hover #adminbarsearch:before {
	color: <?php echo esc_attr( $admin_bar_item_color_hover ); ?> !important;
}

/**
 * Admin Bar: focus
 */
#wpadminbar > #wp-toolbar a:focus span.ab-label,
#wpadminbar > #wp-toolbar span.ab-label:focus,
#wpadminbar > #wp-toolbar span.noticon:focus,
#wpadminbar a.ab-item:focus,
#wpadminbar .ab-item:focus,
#wpadminbar .ab-top-menu > li > .ab-item:focus,
#wpadminbar .ab-top-menu > li.hover > .ab-item:focus,
#wpadminbar .ab-top-menu > li.hover:focus > .ab-item,
#wpadminbar li:focus,
#wpadminbar li:focus .ab-item,
##wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus,
#wpadminbar.nojq .quicklinks .ab-top-menu>li>.ab-item:focus,
#wpadminbar:not(.mobile) .ab-top-menu>li>.ab-item:focus {
	color: <?php echo esc_attr( $admin_bar_item_color_focus ); ?>;
	background: <?php echo esc_attr( $admin_bar_item_background_focus ); ?>;
}

#wpadminbar li:focus .ab-icon:before,
#wpadminbar li:focus .ab-item:after,
#wpadminbar li:focus .ab-item:before,
#wpadminbar li:focus #adminbarsearch:before,
#wpadminbar li.hover:focus .ab-icon:before,
#wpadminbar li.hover:focus .ab-item:after,
#wpadminbar li.hover:focus .ab-item:before,
#wpadminbar li.hover:focus #adminbarsearch:before {
	color: <?php echo esc_attr( $admin_bar_submenu_icon_color_focus ); ?>;
}

/* Admin Bar: submenu */

#wpadminbar .menupop .ab-sub-wrapper {
	background: <?php echo esc_attr( $admin_bar_item_background_hover ); ?>;
}

#wpadminbar ul.ab-submenu li a:hover,
#wpadminbar .quicklinks .menupop ul.ab-sub-secondary,
#wpadminbar .quicklinks .menupop ul.ab-sub-secondary .ab-submenu {
	background: <?php echo esc_attr( $admin_bar_item_background_hover ); ?>;
	color: <?php echo esc_attr( $admin_bar_item_color_hover ); ?> !important;}

#wpadminbar .ab-submenu .ab-item,
#wpadminbar .quicklinks .menupop ul li a,
#wpadminbar .quicklinks .menupop.hover ul li a,
#wpadminbar-nojs .quicklinks .menupop:hover ul li a {
	color: <?php echo esc_attr( $admin_bar_submenu_icon_color ); ?>;
}

#wpadminbar .quicklinks li .blavatar, #wpadminbar .menupop .menupop > .ab-item:before {
	color: <?php echo esc_attr( $admin_bar_submenu_icon_color ); ?>;
}

#wpadminbar .quicklinks .menupop ul li a:hover,
#wpadminbar .quicklinks .menupop ul li a:hover strong,
#wpadminbar .quicklinks .menupop.hover ul li a:hover,
#wpadminbar.nojs .quicklinks .menupop:hover ul li a:hover,
#wpadminbar li:hover .ab-icon:before,
#wpadminbar li.hover .ab-icon:before,
#wpadminbar li:hover .ab-item:after,
#wpadminbar li.hover .ab-item:after,
#wpadminbar li:hover .ab-item:before,
#wpadminbar li.hover .ab-item:before,
#wpadminbar li:hover #adminbarsearch:before,
#wpadminbar .quicklinks li a:hover .blavatar,
#wpadminbar .menupop .menupop > .ab-item:hover:before {
	color: <?php echo esc_attr( $admin_bar_color ); ?>;
}

#wpadminbar .quicklinks .menupop ul li a:focus,
#wpadminbar .quicklinks .menupop ul li a:focus strong,
#wpadminbar .quicklinks .menupop.hover ul li a:focus,
#wpadminbar.nojs .quicklinks .menupop:hover ul li a:focus,
#wpadminbar li a:focus .ab-icon:before,
#wpadminbar li .ab-item:focus:before {
	color: <?php echo esc_attr( $admin_bar_submenu_icon_color_focus ); ?>;
}
#wpadminbar .quicklinks .menupop ul li a:focus,
#wpadminbar .quicklinks .menupop ul li a:focus strong,
#wpadminbar .quicklinks .menupop.focus ul li a:focus,
#wpadminbar.nojs .quicklinks .menupop:focus ul li a:focus,
#wpadminbar li a:focus .ab-icon:before,
#wpadminbar li .ab-item:focus:before {
	color: <?php echo esc_attr( $admin_bar_submenu_icon_color_focus ); ?>;
}

#wpadminbar .quicklinks .menupop ul li a:focus,
#wpadminbar .quicklinks .menupop ul li a:focus strong,
#wpadminbar .quicklinks .menupop.hover ul li a:focus,
#wpadminbar.nojs .quicklinks .menupop:focus ul li a:focus,
#wpadminbar li:focus .ab-icon:before,
#wpadminbar li.hover:focus .ab-icon:before,
#wpadminbar li:focus .ab-item:after,
#wpadminbar li.hover:focus .ab-item:after,
#wpadminbar li:focus .ab-item:before,
#wpadminbar li.hover:focus .ab-item:before,
#wpadminbar li:focus #adminbarsearch:before,
#wpadminbar .quicklinks li a:focus .blavatar,
#wpadminbar li.hover:focus .ab-submenu .ab-item,
#wpadminbar li:focus .ab-submenu .ab-item,
#wpadminbar .menupop .menupop > .ab-item:focus:before {
	color: <?php echo esc_attr( $admin_bar_submenu_icon_color_focus ); ?>;
}

/* Admin Bar: search */

#wpadminbar #adminbarsearch:before {
	color: <?php echo esc_attr( $admin_bar_submenu_icon_color ); ?>;
}

#wpadminbar > #wp-toolbar > #wp-admin-bar-top-secondary > #wp-admin-bar-search #adminbarsearch input.adminbar-input:focus {
	color: <?php echo esc_attr( $admin_bar_color ); ?>;
	background: <?php echo esc_attr( $admin_bar_item_background_hover ); ?>;
}

#wpadminbar #adminbarsearch .adminbar-input::-webkit-input-placeholder,
#wpadminbar #adminbarsearch .adminbar-input:-moz-placeholder,
#wpadminbar #adminbarsearch .adminbar-input::-moz-placeholder,
#wpadminbar #adminbarsearch .adminbar-input:-ms-input-placeholder {
	color: white;
	opacity: 0.7;
}

/* Admin Bar: My Account */

#wpadminbar #wp-admin-bar-user-info .display-name {
	color: <?php echo esc_attr( $admin_bar_color ); ?>;
}

#wpadminbar #wp-admin-bar-user-info a:hover .display-name {
	color: <?php echo esc_attr( $admin_bar_color ); ?>;
}

#wpadminbar #wp-admin-bar-user-info .username {
	color: <?php echo esc_attr( $admin_bar_color ); ?>;
}

/* Media Uploader */

.media-item .bar,
.media-progress-bar div {
	background-color: <?php echo esc_attr( $admin_media_progress_bar_color ); ?>;
}

.details.attachment {
	box-shadow: 0 0 0 1px white, 0 0 0 5px <?php echo esc_attr( $admin_media_selected_attachment_color ); ?>;
}

.attachment.details .check {
	background-color: <?php echo esc_attr( $admin_media_selected_attachment_color ); ?>;
	box-shadow: 0 0 0 1px white, 0 0 0 2px <?php echo esc_attr( $admin_media_selected_attachment_color ); ?>;
}

/* Themes */

.theme-browser .theme.active .theme-name,
.theme-browser .theme.add-new-theme:hover:after {
	background: <?php echo esc_attr( $admin_themes_background ); ?>;
}

.theme-browser .theme.add-new-theme:hover span:after {
	color: <?php echo esc_attr( $admin_themes_background ); ?>;
}

.theme-overlay .theme-header .close:hover,
.theme-overlay .theme-header .right:hover,
.theme-overlay .theme-header .left:hover {
	background: <?php echo esc_attr( $admin_themes_background ); ?>;
}

.theme-browser .theme.active .theme-actions {
	background: <?php echo esc_attr( $admin_themes_actions_background ); ?>;
}

.theme-browser .theme .more-details {
	background: <?php echo esc_attr( $admin_themes_details_background ); ?>;
	text-shadow: none;
}

/* Thickbox: Plugin information */

#sidemenu a.current {
	background: <?php echo esc_attr( $admin_menu_background ); ?>;
	border-bottom-color: <?php echo esc_attr( $admin_menu_background ); ?>;
}

#plugin-information .action-button {
	background: <?php echo esc_attr( $admin_menu_background ); ?>;
}

.plugins .active th.check-column {
	border-left: 4px solid <?php echo esc_attr( $admin_plugins_border_color ); ?>;
}
