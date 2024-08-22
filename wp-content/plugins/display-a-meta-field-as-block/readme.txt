=== Meta Field Block ===
Contributors: Mr2P, freemius
Donate link:       https://metafieldblock.com?utm_source=wp.org&utm_campaign=readme&utm_medium=link&utm_content=MFB+Donate
Tags:              custom field, meta field, ACF custom field, block, Gutenberg
Requires PHP:      7.4
Requires at least: 6.5
Tested up to:      6.6
Stable tag:        1.3.0
License:           GPL-3.0
License URI:       https://www.gnu.org/licenses/gpl-3.0.html

Display a custom field as a block on the front end. It supports custom fields for posts, terms, and users. It supports ACF fields explicitly.

== Description ==

This single-block plugin allows you to display a meta field or a custom field as a block on the front end. It supports custom fields for posts, terms, and users. It can be nested inside a parent block that has `postId` and `postType` context, such as `Query Block`, or used as a stand-alone block.

You can display any field whose value can be retrieved by the core API ([get_post_meta](https://developer.wordpress.org/reference/functions/get_post_meta/), [get_term_meta](https://developer.wordpress.org/reference/functions/get_term_meta/), [get_user_meta](https://developer.wordpress.org/reference/functions/get_user_meta/)) and is a string or can be converted to a string. To display the field value in the Block Editor, it has to be accessible via the REST API or have the field type set to `dynamic`.

You can also display custom fields created by the [Advanced Custom Fields](https://www.advancedcustomfields.com/) plugin explicitly. It supports all [ACF field types](https://www.advancedcustomfields.com/resources/#field-types) whose values are strings or can be converted to strings. Some other complex fields such as Image, Link, Page Link, True False, Checkbox, Select, Radio, Button Group, Taxonomy, User, Post Object and Relationship field types are also supported in basic formats.

This plugin also provides developer-friendly hook APIs that allow you to easily customize the output of the block, display complex data type fields, or use the block as a placeholder to display any kind of content with `object_id` and `object_type` as context parameters.

An edge case where this block is really helpful is when you need to get the correct `post_id` in your shortcode when you use it in a Query Loop. In that case, you can set the field type as `dynamic` and input your shortcode in the field name. The block will display it correctly on both the front end and the editor. Alternatively, if you only want to see the preview of your shortcode in the editor, you can also use this block as a better version of the `core/shortcode`.

= Links =

* [Website](https://metafieldblock.com?utm_source=wp.org&utm_campaign=readme&utm_medium=link&utm_content=Website)
* [Features](https://metafieldblock.com/features?utm_source=wp.org&utm_campaign=readme&utm_medium=link&utm_content=Website%20Features)
* [MFB PRO](https://metafieldblock.com/pro?utm_source=wp.org&utm_campaign=readme&utm_medium=link&utm_content=MFB%20Pro)

= What is the HTML output of a custom field? =

The HTML output of a custom field on the front end depends on the context of the field. It uses one of these core API functions to get the field value: [get_post_meta](https://developer.wordpress.org/reference/functions/get_post_meta/), [get_term_meta](https://developer.wordpress.org/reference/functions/get_term_meta/), [get_user_meta](https://developer.wordpress.org/reference/functions/get_user_meta/).

= What is the HTML output of ACF fields? =

1. All basic field types that return strings or can cast to strings are supported - The HTML output is from the `get_field` function.

2. Link type - The HTML output is:

        <a href={url} target={target} rel="noreferrer noopener">{title}</a>

    There is no `rel` attribute if the `target` is not `_blank`

3. Image type - The HTML output is from the [wp_get_attachment_image](https://developer.wordpress.org/reference/functions/wp_get_attachment_image/) function. The image size is from the Preview Size setting.

4. True / False type - The HML output is `Yes` if the value is `true`, and `No` if the value is `false`. Below is the code snippet to change these text values:

        add_filter( 'meta_field_block_acf_field_true_false_on_text', function ( $on_text, $field, $post_id, $value ) {
          return 'Yep';
        }, 10, 4 );

        add_filter( 'meta_field_block_acf_field_true_false_off_text', function ( $off_text, $field, $post_id, $value ) {
          return 'Noop';
        }, 10, 4 );

5. Checkbox / Select type - The HTML output is:

        <span class="value-item">{item_value}</span>, <span class="value-item">{item_value}</span>

    The `item_value` can be either `value` or `label`, depending on the return format of the field. Multiple selected values are separated by `, `. Below is the code snippet to change the separator:

        add_filter( 'meta_field_block_acf_field_choice_item_separator', function ( $separator, $value, $field, $post_id ) {
          return ' | ';
        }, 10, 4 );

5. Radio button / Button group type - The HTML output can be either `value` or `label`, depending on the return format of the field.

6. Page link type, Post object type - The HTML output for a single-value field is:

        <a class="post-link" href={url} rel="bookmark">{title}</a>

    For a multiple-value field is:

        <ul>
          <li><a class="post-link" href={url} rel="bookmark">{title}</a></li>
          <li><a class="post-link" href={url} rel="bookmark">{title}</a></li>
        </ul>

7. Relationship type - The HTML output is:

        <ul>
          <li><a class="post-link" href={url} rel="bookmark">{title}</a></li>
          <li><a class="post-link" href={url} rel="bookmark">{title}</a></li>
        </ul>

8. Taxonomy type - The HTML output is:

        <ul>
          <li><a class="term-link" href={term_url}>{term_name}</a></li>
          <li><a class="term-link" href={term_url}>{term_name}</a></li>
        </ul>

9. User type - The HTML output for a single-value field is:

        <a class="user-link" href={author_url}>{display_name}</a>

    For a multiple-value field is:

        <ul>
          <li><a class="user-link" href={author_url}>{display_name}</a></li>
          <li><a class="user-link" href={author_url}>{display_name}</a></li>
        </ul>

10. For other complex field types, you can generate a custom HTML output by using the hook:

        apply_filters( 'meta_field_block_get_acf_field', $field_value, $post_id, $field, $raw_value, $object_type )

    Or by using the general hook:

        apply_filters( 'meta_field_block_get_block_content', $content, $attributes, $block, $object_id, $object_type )


= Copy & paste snippets =

When using the `meta_field_block_get_block_content` hook to customize block content, we recommend selecting `dynamic` as the field type. This way, both the front end and the editor will show the changes. If you are working with ACF Fields, we suggest using the `meta_field_block_get_acf_field` hook to modify the field content.

1. How to change the HTML output of the block?
    Using the `meta_field_block_get_block_content` hook:

        add_filter( 'meta_field_block_get_block_content', function ( $block_content, $attributes, $block, $post_id, $object_type ) {
          $field_name = $attributes['fieldName'] ?? '';

          if ( 'your_unique_field_name' === $field_name ) {
            $block_content = 'new content';
          }

          return $block_content;
        }, 10, 5);

    Using the `meta_field_block_get_acf_field` hook for ACF Fields only:

        add_filter( 'meta_field_block_get_acf_field', function ( $block_content, $post_id, $field, $raw_value, $object_type ) {
          $field_name = $field['name'] ?? '';

          if ( 'your_unique_field_name' === $field_name ) {
            $block_content = 'new content';
          }

          return $block_content;
        }, 10, 5);

    This basic snippet is very powerful. You can use it to display any fields from any posts, terms, users or setting fields. Please see the details in the below use cases.

2. How to wrap the block with a link to the post within the Query Loop?
    Using the `meta_field_block_get_block_content` hook:

        add_filter( 'meta_field_block_get_block_content', function ( $block_content, $attributes, $block, $post_id ) {
          $field_name = $attributes['fieldName'] ?? '';

          if ( 'your_unique_field_name' === $field_name && $block_content !== '' ) {
            $block_content = sprintf('<a href="%1$s">%2$s</a>', get_permalink($post_id), $block_content);
          }

          return $block_content;
        }, 10, 4);

    Using the `meta_field_block_get_acf_field` hook for ACF Fields only:

        add_filter( 'meta_field_block_get_acf_field', function ( $block_content, $post_id, $field, $raw_value ) {
          $field_name = $field['name'] ?? '';

          if ( 'your_unique_field_name' === $field_name && $block_content !== '' ) {
            $block_content = sprintf('<a href="%1$s">%2$s</a>', get_permalink($post_id), $block_content);
          }

          return $block_content;
        }, 10, 4);

    This snippet only works with the block that has only HTML inline tags or an image.

3. How to display an image URL field as an image tag?
    Using the `meta_field_block_get_block_content` hook:

        add_filter( 'meta_field_block_get_block_content', function ( $block_content, $attributes, $block, $post_id ) {
          $field_name = $attributes['fieldName'] ?? '';

          if ( 'your_image_url_field_name' === $field_name && wp_http_validate_url($block_content) ) {
            $block_content = sprintf('<img src="%1$s" alt="your_image_url_field_name" />', esc_attr($block_content));
          }

          return $block_content;
        }, 10, 4);

    Using the `meta_field_block_get_acf_field` hook for ACF Fields only:

        add_filter( 'meta_field_block_get_acf_field', function ( $block_content, $post_id, $field, $raw_value ) {
          $field_name = $field['name'] ?? '';

          if ( 'your_image_url_field_name' === $field_name && wp_http_validate_url($block_content) ) {
            $block_content = sprintf('<img src="%1$s" alt="your_image_url_field_name" />', esc_attr($block_content));
          }

          return $block_content;
        }, 10, 4);

4. How to display multiple meta fields in a block?
    For example, we need to display the full name of a user from two meta fields `first_name` and `last_name`.

        add_filter( 'meta_field_block_get_block_content', function ( $block_content, $attributes, $block, $post_id ) {
          $field_name = $attributes['fieldName'] ?? '';

          if ( 'full_name' === $field_name ) {
            $first_name = get_post_meta( $post_id, 'first_name', true );
            $last_name  = get_post_meta( $post_id, 'last_name', true );

            // If the meta fields are ACF Fields. The code will be:
            // $first_name = get_field( 'first_name', $post_id );
            // $last_name  = get_field( 'last_name', $post_id );

            $block_content = trim("$first_name $last_name");
          }

          return $block_content;
        }, 10, 4);

    Choose the field type as `dynamic` and input the field name as `full_name`.

5. How to display a setting field?
    For example, we need to display a setting field named `footer_credit` on the footer template part of the site.

        add_filter( 'meta_field_block_get_block_content', function ( $block_content, $attributes, $block, $post_id ) {
          $field_name = $attributes['fieldName'] ?? '';

          // Replace `footer_credit` with your unique name.
          if ( 'footer_credit' === $field_name ) {
            $block_content = get_option( 'footer_credit', '' );

            // If the field is an ACF Field. The code will be:
            // $block_content = get_field( 'footer_credit', 'option' );
          }

          return $block_content;
        }, 10, 4);

6. [How to use MFB as a placeholder to display any kind of content?](https://wordpress.org/support/topic/how-to-use-mfb-to-display-dynamic-fields/)

= SAVE YOUR TIME WITH MFB PRO =

To display simple data type fields for posts, terms, and users, you only need the free version of MFB. MFB Pro can save you 90% of development time when working with ACF complex fields. It achieves this by transforming your ACF complex field types into container blocks, which work similarly to core container blocks. This eliminates the need for creating ACF custom blocks or writing custom code for displaying ACF complex fields.
Below is a video tutorial on how to use MFB Pro to build a post template without coding

[youtube https://www.youtube.com/watch?v=5VePClgZmlQ]

The main features of MFB PRO are:

* [Display settings fields](https://metafieldblock.com/docs/setting-fields.mp4)
* Display ACF advanced layout fields: [Group](https://metafieldblock.com/docs/group-fields.mp4), [Repeater](https://metafieldblock.com/docs/repeater-fields.mp4), and Flexible content.
* [Display ACF Relationship and Post Object fields as a Query Loop](https://metafieldblock.com/docs/query-fields.mp4).
* Display the ACF Image field as a core image block
* Display the [ACF Gallery field](https://metafieldblock.com/docs/gallery-field.mp4)
* Display the [ACF File field as a core video block](https://metafieldblock.com/docs/file-video-field.mp4)
* Display the ACF Link field as a core button block
* Display the [ACF URL](https://metafieldblock.com/docs/url-fields.mp4) field as a core image block, a core button block, or a link
* Display the [ACF Email, and ACF File](https://metafieldblock.com/docs/email-file-fields.mp4) fields as a core button block or a link
* [Display custom fields from a specific post, term or user](https://metafieldblock.com/docs/other-item-fields.mp4)

If this plugin is useful for you, please do a quick review and [rate it](https://wordpress.org/support/plugin/display-a-meta-field-as-block/reviews/#new-post) on WordPress.org to help us spread the word. I would very much appreciate it.

Please check out my other plugins if you're interested:

* [Content Blocks Builder](https://wordpress.org/plugins/content-blocks-builder) - A tool to create blocks, patterns or variations easily for your site directly on the Block Editor.
* [Block Enhancements](https://wordpress.org/plugins/block-enhancements) - A plugin to add more useful features to blocks likes: icons, box-shadow, transform...
* [Icon separator](https://wordpress.org/plugins/icon-separator) - A tiny block just like the core/separator block but with the ability to add an icon to it.
* [SVG Block](https://wordpress.org/plugins/svg-block) - A block to insert inline SVG images easily and safely. It also bundles with more than 3000 icons and some common non-rectangular dividers.
* [Counting Number Block](https://wordpress.org/plugins/counting-number-block) - A block to display a number that has the number-counting effect.
* [Breadcrumb Block](https://wordpress.org/plugins/breadcrumb-block) - A simple breadcrumb trail block that supports JSON-LD structured data.
* [Better Youtube Embed Block](https://wordpress.org/plugins/better-youtube-embed-block) - Embed YouTube videos without slowing down your site.

The plugin is developed using @wordpress/create-block.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/meta-field-block` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress


== Frequently Asked Questions ==

= Who needs this plugin? =

This plugin is created for developers, but end users can also use it.

= Does it support inputting and saving meta value? =

No, It does not. It only displays meta fields as blocks.

= Does it support all types of meta fields? =

Only simple types such as string, integer, or number can be used directly. Other complex types such as object, array need to be converted to HTML markup strings.

= Does it support all types of ACF fields? =

It supports all basic field types that return strings or cast to strings. Some complex field types like image, link, page_link, post_object, relationship, taxonomy, and user are also supported in a basic format. To display complex ACF field types such as Group, Repeater, Flexible Content, Gallery, File, etc., you will need to either purchase [MFB PRO](https://metafieldblock.com/pro?utm_source=wp.org&utm_campaign=readme&utm_medium=link&utm_content=MFB%20Pro) or write your own custom code using the hook API.

= What are the prefix and suffix for? =

The value for those settings should be plain text or some allowed HTML elements. Their values will be formatted with `wp_kses( $prefix, wp_kses_allowed_html( "post" ) )`. They're helpful for some use cases like displaying the name of the meta field or a value with a prefix or suffix, e.g. $100, 100px, etc.

= Does it include some style for the meta field? =

The block does not provide any CSS style for the meta field value. But it does provide a basic display inline style from the settings.

= Does it support other meta-field frameworks? =

Yes, it does, as long as those meta fields can be accessed via the `get_post_meta`, or `get_term_meta`, or `get_user_meta` function and the return value is a string or can be cast to a string. To display the value in the block editor, the meta field has to be accessed via the REST API.

= What if the displayed markup is blank or is different from the meta value?

There is a chance that your meta value contains some HTML tags or HTML attributes that are not allowed to be displayed. To fix this, you should use the hook `apply_filters( 'meta_field_block_kses_allowed_html', $allowed_html_tags )` to add your tags and attributes to the array of allowed tags. By default, the block allows all tags from the `$allowedposttags` value and basic attributes for `iframe` and `SVG` elements.

== Screenshots ==

1. Meta field settings

2. Prefix and suffix settings

3. Enable `Show in REST API` ACF setting

== Changelog ==

= 1.3.0 =
*Release Date - 05 August 2024*

* Added    - (MFB Pro) Register custom bindings for heading and paragraph when displaying a text field as a heading or a paragraph block
* Added    - (MFB Pro) Allow linking an image field to a custom URL from another field
* Improved - (MFB Pro) Display dynamic value in the editor when displaying a field as a heading, paragraph, button, image, or video block
* Improved - (MFB Pro) Allow displaying the value of URL, and email as button text when displaying them as a button
* Fixed    - (MFB Pro) Expanding image is not getting dynamic value
* Refactor - Replaced classnames with clsx
* Refactor - Replace useSetting by useEttings
* Updated  - Tested up to 6.5 for block bindings

= 1.2.14 =
*Release Date - 31 July 2024*

* Improved - Escape the style attribute for prefix and suffix

= 1.2.13 =
*Release Date - 17 July 2024*

* Improved - Ignore array and object fields from the list of suggested names in the meta field type
* Improved - MFB Pro: Change the label with mailto prefix to the mail value
* Updated  - Update Freemius SDK to 2.7.3

= 1.2.11 =
*Release Date - 06 June 2024*

* Added    - Support clientNavigation interactivity
* Added    - Allow changing the object type via the new filter `meta_field_block_get_object_type`
* Improved - MFB Pro: Use useEntityRecord to display suggested names for setting fields

= 1.2.10 =
*Release Date - 07 May 2024*

* Added    - Add correct format for ACF textarea and editor field in the editor
* Updated  - Use useSettings instead of useSetting since WP 6.5
* Improved - Flush server cache for object type and ACF fields when necessary
* Improved - Add field label to the layout variations of SFB: Group, Flexible content, Repeater
* Improved - MFB Pro: Don't allow editing field path for repeater items SFB
* Improved - MFB Pro: Flexible content field type

= 1.2.9 =
*Release Date - 01 May 2024*

* Improved - Invalidate the MFB cache when updating a post, a term, a user, or settings
* Updated  - Help text in the settings page

= 1.2.8 =
*Release Date - 22 April 2024*

* Updated - Since WP 6.5 we could not get the post ID and post type from the current context when accessing the template editor from a post/page.
* Added   - Add the emptyMessage feature to static blocks

= 1.2.7 =
*Release Date - 16 April 2024*

* Added - Support displaying custom fields inside the Woo Product Collection block

= 1.2.6 =
*Release Date - 22 March 2024*

* Added   - Add query, and queryId of Query Loop as context parameters
* Updated - PRO: Render nested ACF oEmbed fields

= 1.2.5 =
*Release Date - 11 March 2024*

* Updated - Update inline documentation
* Fixed   - When front-end forms are submitted to admin-post.php, nopriv users are redirected to the login page.
* Added   - PRO: Display ACF gallery field
* Added   - PRO: Display ACF File as a video

= 1.2.4 =
*Release Date - 22 February 2024*

* Added    - Add typography and gap settings to prefix and suffix
* Removed  - Remove the redundant blockGap support feature
* Improved - Remove `_acf_changed` from the list of suggested names
* Fixed    - Remove the block margin on value, prefix and suffix when the block is used inside a flow-layout block
* Fixed    - PRO: Correct the name for some field types for ACF
* Added    - PRO: Enable the `hideEmpty` setting for static blocks
* Improved - PRO: Change the default `perPage` value for ACF query fields from 100 to 12
* Added    - PRO: Add the `linkToPost` setting to the ACF image field and ACF URL-as-image field

= 1.2.3 =
*Release Date - 24 January 2024*

* Added   - New `dynamic` field type to display private fields, support running shortcodes, and see the changes made by the hook `meta_field_block_get_block_content` both on the front end and the editor.
* Updated - Change the name of a private hook from '_meta_field_block_get_field_value' to '_meta_field_block_get_field_value_other_type'
* Updated - Change the permission for getting custom endpoints from `publish_posts` to `edit_posts`

= 1.2.2 =
*Release Date - 08 January 2024*

* Updated - Adjust the configuration for freemius

= 1.2.1 =
*Release Date - 03 January 2024*

* Updated - Support full attributes for SVG and all basic shapes in the allowed HTML tags
* Added   - Add the settings page with guides
* Added   - Integrate with freemius 2.6.2
* Updated - Add the `section` tag to the list of HTML tag
* Updated - Ignore `footnotes` from the suggested values for the meta field name
* Updated - Update `Requires at least` to 6.3

= 1.2 =
*Release Date - 11 December 2023*

* Added   - Allow getting meta fields from terms and users
* Updated - Add new `$object_type` parameter to two main hooks `meta_field_block_get_acf_field` and `meta_field_block_get_block_content`
* Added   - Add variations for some common ACF field types
* Updated - Increase the required version of PHP to 7.4
* Updated - Refactor code for upcoming releases
* Updated - Move the prefix and suffix to a separate panel

= 1.1.7 =
*Release Date - 09 September 2023*

* FIX - The block does not show the number 0 if using it as the empty message

= 1.1.6 =
*Release Date - 13 August 2023*

* DEV - Refactor block.json, update to block API version 3 for better WP 6.3 compatibility
* FIX - Rename allowed HTML attributes for SVG

= 1.1.5 =
*Release Date - 15 July 2023*

* DEV - Add a custom hook `apply_filters( 'meta_field_block_kses_allowed_html', $allowed_html_tags )` for filtering allowed HTML tags in the value.
* DEV - Allow displaying iframe, and SVG tag by default.
* DEV - Force displaying color (text, background, link) attributes for unsupported themes.
* DEV - Refactor code for React best practice.
* DOC - Update readme for the hook `meta_field_block_get_acf_field`

= 1.1.4 =
*Release Date - 20 May 2023*

* DEV - Change the placeholder text for the block in the site editor.
* DEV - Add a setting to use the ACF field label as the prefix

= 1.1.3 =
*Release Date - 31 Mar 2023*

* DEV - Support choice fields: true/false, select, checkbox, radio, button group
* DEV - Add raw value to the `meta_field_block_get_acf_field` hook

= 1.1.2 =
*Release Date - 28 Mar 2023*

* DEV - Refactor both JS and PHP code
* DEV - Load ACF field value even if we could not load the field object
* DEV - Separate settings group for WP 6.2

= 1.1.1 =
*Release Date - 14 Mar 2023*

* DEV - Add a hideEmpty setting to hide the whole block if the value is empty
* DEV - Add an emptyMessage setting to show a custom text in case the value is empty
* FIX - The meta field did not show on the archive template

= 1.1.0 =
*Release Date - 06 Mar 2023*

* DEV - Refactor all the source code for more upcoming features
* DEV - Make sure the block works with all return formats for the image field, link field
* DEV - Get all custom rest fields to show on the suggested help
* DEV - Allow changing the tagName from the block toolbar
* DEV - Improve performance
* DEV - Add more core support features
* DEV - Add more meaningful messages for some use cases
* FIX - Allow displaying links without text

= 1.0.10 =
*Release Date - 02 Feb 2023*

* DEV - Support multiple values for ACF User type

= 1.0.9 =
*Release Date - 15 Sep 2022*

* FIX - Change the textdomain to the plugin slug

= 1.0.8 =
*Release Date - 10 Sep 2022*

* FIX - Wrong handle for wp_set_script_translations. Thanks to Loïc Antignac (@webaxones)

= 1.0.7 =
*Release Date - 07 Sep 2022*

* FIX - Add a null check for meta fields value before accessing it's property

= 1.0.6 =
*Release Date - 25 Jun 2022*

* DEV - Add an option to show the block's outline on the Editor

= 1.0.5 =
*Release Date - 21 Jun 2022*

* DEV - Display the placeholder text on the template context

= 1.0.4 =
*Release Date - 02 May 2022*

* DEV - Support displaying some field types for ACF such as image, link, page_link, post_object, relationship, taxonomy

= 1.0.3 =
*Release Date - 30 April 2022*

* DEV - Add supports for borders, and full typography options

= 1.0.2 =
*Release Date - 28 April 2022*

* DEV - Add the title to block registration in JS
* REFACTOR source code

= 1.0.1 =
*Release Date - 23 March 2022*

* FIX - The block does not work in the site editor.

= 1.0.0 =
*Release Date - 22 February 2022*

