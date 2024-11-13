<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


function bsp_shortcodes_display() {
    
        // reused strings
        $copy_message = __( 'Copy to clipboard', 'bbp-style-pack' );
        $show_details_message = __( 'Show Details', 'bbp-style-pack' );
        $hide_details_message = __( 'Hide Details', 'bbp-style-pack' );
        $addtl_example_message = __( 'Additional Examples', 'bbp-style-pack' );
?>


<!-- primary script includes and functions -->
        <script src="//cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js"></script>
        <script type="text/javascript">
                function togglediv(id) {
                        var div = document.getElementById(id);
                        div.style.display = div.style.display == "none" ? "block" : "none";
                }
        </script>

        
<!-- settings page description -->
        <h3>
                <?php _e ( 'Additional Shortcodes' , 'bbp-style-pack' ) ; ?>
        </h3>
        <p>
                <?php _e( 'Listed below are additional shortcodes available for use, provided by bbP Style Pack. You can use any of them that you want, as many times as you want, anywhere that shortcodes are allowed.', 'bbp-style-pack' ); ?>
        </p>
        <p style="padding-left:6px;">
                * <a href="#display_latest_topics"><?php _e( 'Display Latest Topics', 'bbp-style-pack' ); ?></a><br/>
                * <a href="#display_forum_indexes"><?php _e( 'Display Forum Indexes ', 'bbp-style-pack' ); ?></a><br/>
                * <a href="#display_newest_members"><?php _e( 'Display Newest Members ', 'bbp-style-pack' ); ?></a><br/>
                * <a href="#display_forum_subscriber_count"><?php _e( 'Display Forum Subscriber Count ', 'bbp-style-pack' ); ?></a><br/>
                * <a href="#display_profile_link"><?php _e( 'Display Profile Link ', 'bbp-style-pack' ); ?></a><br/>
                * <a href="#force_login_on_forum_index"><?php _e( 'Force Login on Forum Index ', 'bbp-style-pack' ); ?></a><br/>
                * <a href="#moderation_pending"><?php _e( 'Moderation Pending', 'bbp-style-pack' ); ?></a><br/>
        </p>
        <hr/>
        
        
<!-- start bsp-display-topic-index shortcode --> 
        <?php $shortcode_slug = "latest_topics" ?>
        <h4 id="display_latest_topics">
                <span style="color:blue">
                        <?php _e( 'Display Latest Topics', 'bbp-style-pack' ); ?>
                </span>
        </h4>
        <p>
                <?php _e( 'Displays the latest topics, optionally from a forum or forums - see below for detailed explanation', 'bbp-style-pack' ); ?>
        </p>
        
        <!-- minimum example --> 
                <h5>
                        <?php _e( 'Bare Minimum:', 'bbp-style-pack' ); ?>
                </h5>
                <p id="bsp-shortcode-<?php echo $shortcode_slug; ?>-min"><tt style="border:1px solid #000;padding:12px;background-color:#f5f5f5;margin:6px;line-height:42px;">[bsp-display-topic-index]</tt></p>
                <p>
                        <button type="button" class="button unselectable" id="copy-<?php echo $shortcode_slug; ?>-min" data-clipboard-action="copy" data-clipboard-target="#bsp-shortcode-<?php echo $shortcode_slug; ?>-min" onmousedown="return false" onselectstart="return false"><?php echo $copy_message; ?></button>
                </p>

        <!-- all options example --> 
                <h5>
                        <?php _e( 'All Options:', 'bbp-style-pack' ); ?>
                </h5>
                <p id="bsp-shortcode-<?php echo $shortcode_slug; ?>"><tt style="border:1px solid #000;padding:12px;background-color:#f5f5f5;margin:6px;line-height:42px;">[bsp-display-topic-index show="5" forum="10,11,12" template="short" show_stickies="true" noreply="true"]</tt></p>
                <p>
                        <button type="button" class="button unselectable" id="copy-<?php echo $shortcode_slug; ?>" data-clipboard-action="copy" data-clipboard-target="#bsp-shortcode-<?php echo $shortcode_slug; ?>" onmousedown="return false" onselectstart="return false"><?php echo $copy_message; ?></button>
                        <button type="button" class="button" id="toggle-button-<?php echo $shortcode_slug; ?>" onclick="togglediv('toggle-<?php echo $shortcode_slug; ?>')"><?php echo $show_details_message; ?></button>
                </p>
                
        <!-- scripts for example -->
                <script type="text/javascript">
                        (function() {
                                new ClipboardJS( '#copy-<?php echo $shortcode_slug; ?>-min' );
                                new ClipboardJS( '#copy-<?php echo $shortcode_slug; ?>' );
                        })();
                        document.addEventListener("DOMContentLoaded", function() {
                                const changeText<?php echo $shortcode_slug; ?> = document.querySelector("#toggle-button-<?php echo $shortcode_slug; ?>");
                                changeText<?php echo $shortcode_slug; ?>.addEventListener("click", function() {
                                        changeText<?php echo $shortcode_slug; ?>.textContent = changeText<?php echo $shortcode_slug; ?>.textContent == "<?php echo $show_details_message; ?>" ? "<?php echo $hide_details_message; ?>" : "<?php echo $show_details_message; ?>";
                                });
                        });
                </script>

        <!-- table with details for example -->
                <div id="toggle-<?php echo $shortcode_slug; ?>" style="display:none;">
                        <table class="bsp-plugin-info">
                                <tbody>
                                        <tr>
                                                <th>
                                                        <?php _e( 'Option', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Required', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Values', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Examples', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Description', 'bbp-style-pack' ); ?>
                                                </th>
                                        </tr>
                                        <tr>
                                                <td>
                                                        show
                                                </td>
                                                <td>
                                                        <?php _e( 'no', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        <?php _e( 'not set - show bbPress setting for # of topics to display (default)', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <br/>
                                                        <?php _e( 'single integer - show specific # of topics', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        show="5"
                                                </td>
                                                <td>
                                                        <?php _e( 'Limit total # of topics shown', 'bbp-style-pack' ); ?>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        forum
                                                </td>
                                                <td>
                                                        <?php _e( 'no', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        <?php _e( 'not set - show topics from all forums (default)', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <br/>
                                                        <?php _e( 'single integer - show topics from a specific forum', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <br/>
                                                        <?php _e( 'comma separated integers - show topics from multiple specific forums', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        forum="10"
                                                        <br/>
                                                        <br/>
                                                        forum="10,11,12"
                                                </td>
                                                <td>
                                                        <?php _e( 'You can limit topics to a single forum or multiple forums.', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <br/>
                                                        <?php _e( 'To find the ID of a forum(s) go into Dashboard > Forums.', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <?php echo sprintf(
                                                                /* translators: %s is a URL */
                                                                __( 'You will see at the bottom of the page %s', 'bbp-style-pack' ),
                                                                'http://www.mysite.com/wp-admin/post.php?post=10&action=edit'
                                                        ); ?>
                                                        <br/>
                                                        <?php _e( 'where post=10 is the ID number of the forum.', 'bbp-style-pack' ); ?>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        template
                                                </td>
                                                <td>
                                                        <?php _e( 'no', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        <?php _e( 'not set - show standard topics view template including breadcrumb/search, and topic count (default)', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <br/>
                                                        short - <?php _e( 'show just the header and posts', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <br/>
                                                        list - <?php _e( 'show just a topic list (can be styled using the CSS class "bsp-list")', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        template="short"
                                                        <br/>
                                                        <br/>
                                                        template="list"
                                                </td>
                                                <td>
                                                        <?php _e( 'This allows you to change the template layout for the topics display.', 'bbp-style-pack' ); ?>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        show_stickies
                                                </td>
                                                <td>
                                                        <?php _e( 'no', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        <?php _e( 'not set - do not include sticky topics (default)', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <br/>
                                                        true - <?php _e( 'include sticky topics', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        show_stickies="true"
                                                </td>
                                                <td>
                                                        <?php _e( 'This allows you to include sticky topics within the topics displayed.', 'bbp-style-pack' ); ?>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        noreply
                                                </td>
                                                <td>
                                                        <?php _e( 'no', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        <?php _e( 'not set - include all topics, whther there\'s a reply or not (default)', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <br/>
                                                        true - <?php _e( 'only show topics with no replies', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        noreply="true"
                                                </td>
                                                <td>
                                                        <?php _e( 'This allows you to include only topics that have not received a reply yet.', 'bbp-style-pack' ); ?>
                                                </td>
                                        </tr>
                                </tbody>
                        </table>
                </div>
<!-- end bsp-display-topic-index shortcode --> 
        <br/>
        <hr/>


<!-- start bsp-display-forum-index shortcode --> 
        <?php $shortcode_slug = "forum_index" ?>
        <h4 id="display_forum_indexes">
                <span style="color:blue">
                        <?php _e( 'Display Forum Indexes', 'bbp-style-pack' ); ?>
                </span>
        </h4>
        <p>
                <?php _e( 'Displays one or more forum indexes, or create an index display in any order - see below for detailed explanation', 'bbp-style-pack' ); ?>
        </p>
        
        <!-- minimum example --> 
                <h5>
                        <?php _e( 'Bare Minimum:', 'bbp-style-pack' ); ?>
                </h5>
                <p id="bsp-shortcode-<?php echo $shortcode_slug; ?>-min"><tt style="border:1px solid #000;padding:12px;background-color:#f5f5f5;margin:6px;line-height:42px;">[bsp-display-forum-index forum="10"]</tt></p>
                <p>
                        <button type="button" class="button unselectable" id="copy-<?php echo $shortcode_slug; ?>-min" data-clipboard-action="copy" data-clipboard-target="#bsp-shortcode-<?php echo $shortcode_slug; ?>-min" onmousedown="return false" onselectstart="return false"><?php echo $copy_message; ?></button>
                </p>

        <!-- all options example --> 
                <h5>
                        <?php _e( 'All Options:', 'bbp-style-pack' ); ?>
                </h5>
                <p id="bsp-shortcode-<?php echo $shortcode_slug; ?>"><tt style="border:1px solid #000;padding:12px;background-color:#f5f5f5;margin:6px;line-height:42px;">[bsp-display-topic-index forum="2932,2921" breadcrumb="false" search="false" title="Main Forums"]</tt></p>
                <p>
                        <button type="button" class="button unselectable" id="copy-<?php echo $shortcode_slug; ?>" data-clipboard-action="copy" data-clipboard-target="#bsp-shortcode-<?php echo $shortcode_slug; ?>" onmousedown="return false" onselectstart="return false"><?php echo $copy_message; ?></button>
                        <button type="button" class="button" id="toggle-button-<?php echo $shortcode_slug; ?>" onclick="togglediv('toggle-<?php echo $shortcode_slug; ?>')"><?php echo $show_details_message; ?></button>
                </p>
                
        <!-- scripts for example -->
                <script type="text/javascript">
                        (function() {
                                new ClipboardJS( '#copy-<?php echo $shortcode_slug; ?>-min' );
                                new ClipboardJS( '#copy-<?php echo $shortcode_slug; ?>' );
                        })();
                        document.addEventListener("DOMContentLoaded", function() {
                                const changeText<?php echo $shortcode_slug; ?> = document.querySelector("#toggle-button-<?php echo $shortcode_slug; ?>");
                                changeText<?php echo $shortcode_slug; ?>.addEventListener("click", function() {
                                        changeText<?php echo $shortcode_slug; ?>.textContent = changeText<?php echo $shortcode_slug; ?>.textContent == "<?php echo $show_details_message; ?>" ? "<?php echo $hide_details_message; ?>" : "<?php echo $show_details_message; ?>";
                                });
                        });
                </script>

        <!-- table with details for example -->
                <div id="toggle-<?php echo $shortcode_slug; ?>" style="display:none;">
                        <table class="bsp-plugin-info">
                                <tbody>
                                        <tr>
                                                <th>
                                                        <?php _e( 'Option', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Required', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Values', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Examples', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Description', 'bbp-style-pack' ); ?>
                                                </th>
                                        </tr>
                                        <tr>
                                                <td>
                                                        forum
                                                </td>
                                                <td>
                                                        <?php _e( 'yes', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        <?php _e( 'single integer - specific forum ID to display', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <br/>
                                                        <?php _e( 'comma separated integers - specific forum IDs to display, and their display order', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        forum="10"
                                                        <br/>
                                                        <br/>
                                                        forum="2932,2922,2921"
                                                </td>
                                                <td>
                                                        <?php _e( 'You must specify a forum ID to display a single forum index, or multiple forum IDs separated with a comma to display multiple forum indexes. Multiple forum Indexes will be displayed in the order specified.', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <br/>
                                                        <?php _e( 'To find the ID of a forum(s) go into Dashboard > Forums.', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <?php echo sprintf(
                                                                /* translators: %s is a URL */
                                                                __( 'You will see at the bottom of the page %s', 'bbp-style-pack' ),
                                                                'http://www.mysite.com/wp-admin/post.php?post=10&action=edit'
                                                        ); ?>
                                                        <br/>
                                                        <?php _e( 'where post=10 is the ID number of the forum.', 'bbp-style-pack' ); ?>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        search
                                                </td>
                                                <td>
                                                        <?php _e( 'no', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        <?php _e( 'not set - include the search within the display (default)', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <br/>
                                                        false - <?php _e( 'do not show the search within the display', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        search="false"
                                                </td>
                                                <td>
                                                        <?php _e( 'You can remove the built-in search from the forum index display.', 'bbp-style-pack' ); ?>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        breadcrumb
                                                </td>
                                                <td>
                                                        <?php _e( 'no', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        <?php _e( 'not set - include the breadcrumbs within the display (default)', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <br/>
                                                        false - <?php _e( 'do not show the breadcrumbs within the display', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        breadcrumb="false"
                                                </td>
                                                <td>
                                                        <?php _e( 'You can remove the built-in breadcrumbs from the forum index display.', 'bbp-style-pack' ); ?>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        title
                                                </td>
                                                <td>
                                                        <?php _e( 'no', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        <?php _e( 'not set - use the word "Forum" in the headings  (default)', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <br/>
                                                        <?php _e( 'string - use the custom string in the headings', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        title="Featured Forums"
                                                </td>
                                                <td>
                                                        <?php _e( 'This allows you to change the default forum heading word from "Forum" to any string you want.', 'bbp-style-pack' ); ?>
                                                </td>
                                        </tr>
                                </tbody>
                        </table>
                </div>
<!-- end bsp-display-forum-index shortcode --> 
        <br/>
        <hr/>
        
        
<!-- start bsp-display-newest-users shortcode --> 
        <?php $shortcode_slug = "new_users" ?>
        <h4 id="display_newest_members">
                <span style="color:blue">
                        <?php _e( 'Display Newest Members', 'bbp-style-pack' ); ?>
                </span>
        </h4>
        <p>
                <?php _e( 'Displays the newest users together with their joining date in a table - see below for detailed explanation', 'bbp-style-pack' ); ?>
        </p>
        
        <!-- minimum example --> 
                <h5>
                        <?php _e( 'Bare Minimum:', 'bbp-style-pack' ); ?>
                </h5>
                <p id="bsp-shortcode-<?php echo $shortcode_slug; ?>-min"><tt style="border:1px solid #000;padding:12px;background-color:#f5f5f5;margin:6px;line-height:42px;">[bsp-display-newest-users]</tt></p>
                <p>
                        <button type="button" class="button unselectable" id="copy-<?php echo $shortcode_slug; ?>-min" data-clipboard-action="copy" data-clipboard-target="#bsp-shortcode-<?php echo $shortcode_slug; ?>-min" onmousedown="return false" onselectstart="return false"><?php echo $copy_message; ?></button>
                </p>

        <!-- all options example --> 
                <h5>
                        <?php _e( 'All Options:', 'bbp-style-pack' ); ?>
                </h5>
                <p id="bsp-shortcode-<?php echo $shortcode_slug; ?>"><tt style="border:1px solid #000;padding:12px;background-color:#f5f5f5;margin:6px;line-height:42px;">[bsp-display-newest-users show="10"]</tt></p>
                <p>
                        <button type="button" class="button unselectable" id="copy-<?php echo $shortcode_slug; ?>" data-clipboard-action="copy" data-clipboard-target="#bsp-shortcode-<?php echo $shortcode_slug; ?>" onmousedown="return false" onselectstart="return false"><?php echo $copy_message; ?></button>
                        <button type="button" class="button" id="toggle-button-<?php echo $shortcode_slug; ?>" onclick="togglediv('toggle-<?php echo $shortcode_slug; ?>')"><?php echo $show_details_message; ?></button>
                </p>
                
        <!-- scripts for example -->
                <script type="text/javascript">
                        (function() {
                                new ClipboardJS( '#copy-<?php echo $shortcode_slug; ?>-min' );
                                new ClipboardJS( '#copy-<?php echo $shortcode_slug; ?>' );
                        })();
                        document.addEventListener("DOMContentLoaded", function() {
                                const changeText<?php echo $shortcode_slug; ?> = document.querySelector("#toggle-button-<?php echo $shortcode_slug; ?>");
                                changeText<?php echo $shortcode_slug; ?>.addEventListener("click", function() {
                                        changeText<?php echo $shortcode_slug; ?>.textContent = changeText<?php echo $shortcode_slug; ?>.textContent == "<?php echo $show_details_message; ?>" ? "<?php echo $hide_details_message; ?>" : "<?php echo $show_details_message; ?>";
                                });
                        });
                </script>

        <!-- table with details for example -->
                <div id="toggle-<?php echo $shortcode_slug; ?>" style="display:none;">
                        <table class="bsp-plugin-info">
                                <tbody>
                                        <tr>
                                                <th>
                                                        <?php _e( 'Option', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Required', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Values', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Examples', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Description', 'bbp-style-pack' ); ?>
                                                </th>
                                        </tr>
                                        <tr>
                                                <td>
                                                        show
                                                </td>
                                                <td>
                                                        <?php _e( 'no', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        <?php _e( 'not set - disply last 5 newest users (default)', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <br/>
                                                        <?php _e( 'single integer - specific number of new users to display', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        show="25"
                                                </td>
                                                <td>
                                                        <?php _e( 'You can display the last X newest users to register to the site. The last 5 newest members will be shown by default unless you specificy a different number.', 'bbp-style-pack' ); ?>
                                                </td>
                                        </tr>
                                </tbody>
                        </table>
                </div>
<!-- end bsp-display-newest-users shortcode --> 
        <br/>
        <hr/>


<!-- start bsp-forum-subscriber-count shortcode --> 
        <?php $shortcode_slug = "subscriber_count" ?>
        <h4 id="display_forum_subscriber_count">
                <span style="color:blue">
                        <?php _e( 'Display Forum Subscriber Count', 'bbp-style-pack' ); ?>
                </span>
        </h4>
        <p>
                <?php _e( 'Displays the number of users subscribed to a forum - see below for detailed explanation', 'bbp-style-pack' ); ?>
        </p>
        
        <!-- minimum example --> 
                <h5>
                        <?php _e( 'Bare Minimum:', 'bbp-style-pack' ); ?>
                </h5>
                <p id="bsp-shortcode-<?php echo $shortcode_slug; ?>-min"><tt style="border:1px solid #000;padding:12px;background-color:#f5f5f5;margin:6px;line-height:42px;">[bsp-forum-subscriber-count forum="2932"]</tt></p>
                <p>
                        <button type="button" class="button unselectable" id="copy-<?php echo $shortcode_slug; ?>-min" data-clipboard-action="copy" data-clipboard-target="#bsp-shortcode-<?php echo $shortcode_slug; ?>-min" onmousedown="return false" onselectstart="return false"><?php echo $copy_message; ?></button>
                </p>

        <!-- all options example --> 
                <h5>
                        <?php _e( 'All Options:', 'bbp-style-pack' ); ?>
                </h5>
                <p id="bsp-shortcode-<?php echo $shortcode_slug; ?>"><tt style="border:1px solid #000;padding:12px;background-color:#f5f5f5;margin:6px;line-height:42px;">[bsp-forum-subscriber-count forum="2932" before="This forum has " after=" subscribers"]</tt></p>
                <p>
                        <button type="button" class="button unselectable" id="copy-<?php echo $shortcode_slug; ?>" data-clipboard-action="copy" data-clipboard-target="#bsp-shortcode-<?php echo $shortcode_slug; ?>" onmousedown="return false" onselectstart="return false"><?php echo $copy_message; ?></button>
                        <button type="button" class="button" id="toggle-button-<?php echo $shortcode_slug; ?>" onclick="togglediv('toggle-<?php echo $shortcode_slug; ?>')"><?php echo $show_details_message; ?></button>
                </p>
                
        <!-- scripts for example -->
                <script type="text/javascript">
                        (function() {
                                new ClipboardJS( '#copy-<?php echo $shortcode_slug; ?>-min' );
                                new ClipboardJS( '#copy-<?php echo $shortcode_slug; ?>' );
                        })();
                        document.addEventListener("DOMContentLoaded", function() {
                                const changeText<?php echo $shortcode_slug; ?> = document.querySelector("#toggle-button-<?php echo $shortcode_slug; ?>");
                                changeText<?php echo $shortcode_slug; ?>.addEventListener("click", function() {
                                        changeText<?php echo $shortcode_slug; ?>.textContent = changeText<?php echo $shortcode_slug; ?>.textContent == "<?php echo $show_details_message; ?>" ? "<?php echo $hide_details_message; ?>" : "<?php echo $show_details_message; ?>";
                                });
                        });
                </script>

        <!-- table with details for example -->
                <div id="toggle-<?php echo $shortcode_slug; ?>" style="display:none;">
                        <table class="bsp-plugin-info">
                                <tbody>
                                        <tr>
                                                <th>
                                                        <?php _e( 'Option', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Required', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Values', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Examples', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Description', 'bbp-style-pack' ); ?>
                                                </th>
                                        </tr>
                                        <tr>
                                                <td>
                                                        forum
                                                </td>
                                                <td>
                                                        <?php _e( 'yes', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        <?php _e( 'single integer - specific forum ID to display', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        forum="2932"
                                                </td>
                                                <td>
                                                        <?php _e( 'You must specify a forum ID to display the subscriber count for that specific forum.', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <br/>
                                                        <?php _e( 'To find the ID of a forum(s) go into Dashboard > Forums.', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <?php echo sprintf(
                                                                /* translators: %s is a URL */
                                                                __( 'You will see at the bottom of the page %s', 'bbp-style-pack' ),
                                                                'http://www.mysite.com/wp-admin/post.php?post=10&action=edit'
                                                        ); ?>
                                                        <br/>
                                                        <?php _e( 'where post=10 is the ID number of the forum.', 'bbp-style-pack' ); ?>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        before
                                                </td>
                                                <td>
                                                        <?php _e( 'no', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        <?php _e( 'not set - no text string before the number (default)', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <br/>
                                                        <?php _e( 'string - string to display before the subscriber count number', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        before="This forum has "
                                                </td>
                                                <td>
                                                        <?php _e( 'You can specific a specific string to be displayed before the number count with this option.', 'bbp-style-pack' ); ?>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        after
                                                </td>
                                                <td>
                                                        <?php _e( 'no', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        <?php _e( 'not set - no text string after the number (default)', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <br/>
                                                        <?php _e( 'string - string to display after the subscriber count number', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        after=" subscribers."
                                                </td>
                                                <td>
                                                        <?php _e( 'You can specific a specific string to be displayed after the number count with this option.', 'bbp-style-pack' ); ?>
                                                </td>
                                        </tr>
                                </tbody>
                        </table>
                </div>
<!-- end bsp-forum-subscriber-count shortcode --> 
        <br/>
        <hr/>        
        

<!-- start bsp-profile shortcode --> 
        <?php $shortcode_slug = "profile_link" ?>
        <h4 id="display_profile_link">
                <span style="color:blue">
                        <?php _e( 'Display Profile Link', 'bbp-style-pack' ); ?>
                </span>
        </h4>
        <p>
                <?php _e( 'Displays the label and a link to user profile page - see below for detailed explanation', 'bbp-style-pack' ); ?>
        </p>
        
        <!-- minimum example --> 
                <h5>
                        <?php _e( 'Bare Minimum:', 'bbp-style-pack' ); ?>
                </h5>
                <p id="bsp-shortcode-<?php echo $shortcode_slug; ?>-min"><tt style="border:1px solid #000;padding:12px;background-color:#f5f5f5;margin:6px;line-height:42px;">[bsp-profile]</tt></p>
                <p>
                        <button type="button" class="button unselectable" id="copy-<?php echo $shortcode_slug; ?>-min" data-clipboard-action="copy" data-clipboard-target="#bsp-shortcode-<?php echo $shortcode_slug; ?>-min" onmousedown="return false" onselectstart="return false"><?php echo $copy_message; ?></button>
                </p>

        <!-- all options example --> 
                <h5>
                        <?php _e( 'All Options:', 'bbp-style-pack' ); ?>
                </h5>
                <p id="bsp-shortcode-<?php echo $shortcode_slug; ?>"><tt style="border:1px solid #000;padding:12px;background-color:#f5f5f5;margin:6px;line-height:42px;">[bsp-profile label="Edit My Profile" edit="true"]</tt></p>
                <p>
                        <button type="button" class="button unselectable" id="copy-<?php echo $shortcode_slug; ?>" data-clipboard-action="copy" data-clipboard-target="#bsp-shortcode-<?php echo $shortcode_slug; ?>" onmousedown="return false" onselectstart="return false"><?php echo $copy_message; ?></button>
                        <button type="button" class="button" id="toggle-button-<?php echo $shortcode_slug; ?>" onclick="togglediv('toggle-<?php echo $shortcode_slug; ?>')"><?php echo $show_details_message; ?></button>
                </p>
                
        <!-- scripts for example -->
                <script type="text/javascript">
                        (function() {
                                new ClipboardJS( '#copy-<?php echo $shortcode_slug; ?>-min' );
                                new ClipboardJS( '#copy-<?php echo $shortcode_slug; ?>' );
                        })();
                        document.addEventListener("DOMContentLoaded", function() {
                                const changeText<?php echo $shortcode_slug; ?> = document.querySelector("#toggle-button-<?php echo $shortcode_slug; ?>");
                                changeText<?php echo $shortcode_slug; ?>.addEventListener("click", function() {
                                        changeText<?php echo $shortcode_slug; ?>.textContent = changeText<?php echo $shortcode_slug; ?>.textContent == "<?php echo $show_details_message; ?>" ? "<?php echo $hide_details_message; ?>" : "<?php echo $show_details_message; ?>";
                                });
                        });
                </script>

        <!-- table with details for example -->
                <div id="toggle-<?php echo $shortcode_slug; ?>" style="display:none;">
                        <table class="bsp-plugin-info">
                                <tbody>
                                        <tr>
                                                <th>
                                                        <?php _e( 'Option', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Required', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Values', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Examples', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Description', 'bbp-style-pack' ); ?>
                                                </th>
                                        </tr>
                                        <tr>
                                                <td>
                                                        label
                                                </td>
                                                <td>
                                                        <?php _e( 'no', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        <?php _e( 'not set - displays "My Profile" as the link label (default)', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <br/>
                                                        <?php _e( 'string - custom string to display as the link label', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        label="Edit my profile"
                                                </td>
                                                <td>
                                                        <?php _e( 'This option allows you to overide the default link label "My Profile" with any string of your choice.', 'bbp-style-pack' ); ?>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        edit
                                                </td>
                                                <td>
                                                        <?php _e( 'no', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        <?php _e( 'not set - links to main view profile page (default)', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <br/>
                                                        true - <?php _e( 'links to profile edit page', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        edit="true"
                                                </td>
                                                <td>
                                                        <?php _e( 'By default, the profile link links to the main view profile page. You can override that here to force it to link to the profile edit page instead.', 'bbp-style-pack' ); ?>
                                                </td>
                                        </tr>
                                </tbody>
                        </table>
                </div>
<!-- end bsp-profile shortcode --> 
        <br/>
        <hr/>
        
        
<!-- start bsp-force-login shortcode --> 
        <?php $shortcode_slug = "force_login" ?>
        <h4 id="force_login_on_forum_index">
                <span style="color:blue">
                        <?php _e( 'Force Login on Forum Index', 'bbp-style-pack' ); ?>
                </span>
        </h4>
        <p>
                <?php echo sprintf(
                        /* translators: %s is a shortcode string */
                        __( 'This shortcode can be used in place of the %s shortcode.', 'bbp-style-pack' ),
                        '[bbp-forum-index]'
                ); ?>
        </p>
        <p>
                <?php _e( 'It displays the forum index if logged in, or the message (if any) together with the bbpress login widget if not - see below for detailed explanation', 'bbp-style-pack' ); ?>
        </p>
        
        <!-- minimum example --> 
                <h5>
                        <?php _e( 'Bare Minimum:', 'bbp-style-pack' ); ?>
                </h5>
                <p id="bsp-shortcode-<?php echo $shortcode_slug; ?>-min"><tt style="border:1px solid #000;padding:12px;background-color:#f5f5f5;margin:6px;line-height:42px;">[bsp-force-login]</tt></p>
                <p>
                        <button type="button" class="button unselectable" id="copy-<?php echo $shortcode_slug; ?>-min" data-clipboard-action="copy" data-clipboard-target="#bsp-shortcode-<?php echo $shortcode_slug; ?>-min" onmousedown="return false" onselectstart="return false"><?php echo $copy_message; ?></button>
                </p>

        <!-- all options example --> 
                <h5>
                        <?php _e( 'All Options:', 'bbp-style-pack' ); ?>
                </h5>
                <p id="bsp-shortcode-<?php echo $shortcode_slug; ?>"><tt style="border:1px solid #000;padding:12px;background-color:#f5f5f5;margin:6px;line-height:42px;">[bsp-force-login message="You must be logged in to view the forums"]</tt></p>
                <p>
                        <button type="button" class="button unselectable" id="copy-<?php echo $shortcode_slug; ?>" data-clipboard-action="copy" data-clipboard-target="#bsp-shortcode-<?php echo $shortcode_slug; ?>" onmousedown="return false" onselectstart="return false"><?php echo $copy_message; ?></button>
                        <button type="button" class="button" id="toggle-button-<?php echo $shortcode_slug; ?>" onclick="togglediv('toggle-<?php echo $shortcode_slug; ?>')"><?php echo $show_details_message; ?></button>
                </p>
                
        <!-- scripts for example -->
                <script type="text/javascript">
                        (function() {
                                new ClipboardJS( '#copy-<?php echo $shortcode_slug; ?>-min' );
                                new ClipboardJS( '#copy-<?php echo $shortcode_slug; ?>' );
                        })();
                        document.addEventListener("DOMContentLoaded", function() {
                                const changeText<?php echo $shortcode_slug; ?> = document.querySelector("#toggle-button-<?php echo $shortcode_slug; ?>");
                                changeText<?php echo $shortcode_slug; ?>.addEventListener("click", function() {
                                        changeText<?php echo $shortcode_slug; ?>.textContent = changeText<?php echo $shortcode_slug; ?>.textContent == "<?php echo $show_details_message; ?>" ? "<?php echo $hide_details_message; ?>" : "<?php echo $show_details_message; ?>";
                                });
                        });
                </script>

        <!-- table with details for example -->
                <div id="toggle-<?php echo $shortcode_slug; ?>" style="display:none;">
                        <table class="bsp-plugin-info">
                                <tbody>
                                        <tr>
                                                <th>
                                                        <?php _e( 'Option', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Required', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Values', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Examples', 'bbp-style-pack' ); ?>
                                                </th>
                                                <th>
                                                        <?php _e( 'Description', 'bbp-style-pack' ); ?>
                                                </th>
                                        </tr>
                                        <tr>
                                                <td>
                                                        message
                                                </td>
                                                <td>
                                                        <?php _e( 'no', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        <?php _e( 'not set - just show the login widget without a message (default)', 'bbp-style-pack' ); ?>
                                                        <br/>
                                                        <br/>
                                                        <?php _e( 'string - show custom message string with the login widget', 'bbp-style-pack' ); ?>
                                                </td>
                                                <td>
                                                        message="You must be logged in to view the forums"
                                                </td>
                                                <td>
                                                        <?php _e( 'This option allows you to display a custom message with the login widget to logged-out users.', 'bbp-style-pack' ); ?>
                                                </td>
                                        </tr>
                                </tbody>
                        </table>
                        <p>
                                <?php echo '<img src="' . plugins_url( 'images/shortcode-force-login.JPG',dirname(__FILE__)  ) . '" > '; ?>
                        </p>
                </div>
<!-- end bsp-force-login shortcode --> 
        <br/>
        <hr/>        


<!-- start bsp-moderation-pending --> 
        <?php $shortcode_slug = "moderation_pending" ?>
        <h4 id="moderation_pending">
                <span style="color:blue">
                        <?php _e( 'Moderation Pending', 'bbp-style-pack' ); ?>
                </span>
        </h4>
        <p>
                <?php echo sprintf(
                        /* translators: %1$s and %2$s are HTML a href tags */
                        __( 'If you have activated the %1$sModeration tab%2$s, then this shortcode for keymasters and moderators will display all the pending topics and replies in one place, letting you approve, edit, delete, spam and do all the other administration tasks on the front end. Add this shortcode to a page or post to use.', 'bbp-style-pack' ),
                        '<a href="'.admin_url('options-general.php?page=bbp-style-pack&tab=modtools').'" target="_blank">',
                        '</a>'
                ); ?>
        </p>
        <p>
                <?php _e( 'There are currently no extra options for this shortcode.', 'bbp-style-pack' ); ?>
        </p>

        <!-- all options example --> 
                <h5>
                        <?php _e( 'All Options:', 'bbp-style-pack' ); ?>
                </h5>
                <p id="bsp-shortcode-<?php echo $shortcode_slug; ?>"><tt style="border:1px solid #000;padding:12px;background-color:#f5f5f5;margin:6px;line-height:42px;">[bsp-moderation-pending]</tt></p>
                <p>
                        <button type="button" class="button unselectable" id="copy-<?php echo $shortcode_slug; ?>" data-clipboard-action="copy" data-clipboard-target="#bsp-shortcode-<?php echo $shortcode_slug; ?>" onmousedown="return false" onselectstart="return false"><?php echo $copy_message; ?></button>
                </p>
                
        <!-- scripts for example -->
                <script type="text/javascript">
                        (function() {
                                new ClipboardJS( '#copy-<?php echo $shortcode_slug; ?>' );
                        })();
                </script>

<!-- end bsp-force-login shortcode --> 
        <br/>
        <hr/> 

<?php
}
