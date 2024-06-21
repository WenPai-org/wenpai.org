=== EXMAGE - WordPress Image Links ===
Contributors: villatheme, mrt3vn
Donate link: http://www.villatheme.com/donate
Tags: ecommerce, elementor gallery with links, elementor image carousel link, woocommerce, woocommerce product image external url, wordpress, wordpress gallery custom links, wordpress gallery link, wordpress gallery with links, wordpress image links
Requires at least: 5.0.0
Tested up to: 6.5
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Stable tag: 1.0.17

Save storage by using external image URLs.

== Description ==

EXMAGE - WordPress Image Links helps you save storage by using external image URLs. These images are shown in Media library like normal images so that you can choose them for post/product featured image, WooCommerce product gallery... or everywhere that images are chosen from Media library.

[Documents](https://docs.villatheme.com/exmage-wordpress-image-links/ "Documents") | [Facebook group](https://www.facebook.com/groups/villatheme "VillaTheme")

[youtube https://youtu.be/IDZdT3lAXbg]

### Important Notice:

- This plugin only supports real image URLs that have correct image mime type. It does not support image URLs from an image hosting service(such as Flickr, Imgur, Photobucket ...) or a file storage service(such as Google drive)

- External images added by this plugin will no longer work if the plugin is not active

### FEATURES

- Ability to add single image URL on Upload files tab of the Media library

- Ability to add multiple image URLs at once on below the File upload on Upload New Media page

- External images have an icon to distinguish them from normal attachments.

- External images also have attachment ID like normal attachments so that you can use them wherever that allows to insert images from Media library such as Post/Product featured image, product gallery images, variation image, product category image...

- Compatible with ALD plugin: when this plugin is active, there will be an option named "Use external links for images" in the ALD plugin settings/Products. By enabling this option, AliExpress products imported by ALD plugin will use original AliExpress image URLs for product featured images, gallery images and variation images instead of saving images to your server.

=Integration=

`
if(class_exists( 'EXMAGE_WP_IMAGE_LINKS' )){
    $add_image = EXMAGE_WP_IMAGE_LINKS::add_image( $url, $image_id, $post_content, $post_parent );
}
`
-$url: URL of the image you want to process
-$image_id: Passed by reference
-$post_parent: ID of the post that you want the image to be attached to. If empty, the image will not be attached to any post
-Return:
`
        [
        'url'       => $url,//Input URL
		'message'   => '',//Additional information
		'status'    => 'error',//error or success
		'id'        => '',//Attachment ID if added new or the attachment exists
		'edit_link' => '',//Attachment's edit link if added new or the attachment exists
		]
`
### MAY BE YOU NEED

[SUBRE – Product Subscription for WooCommerce](https://bit.ly/subre-product-subscription-for-woo): Convert WooCommerce simple products(physical or downloadable/virtual) to subscription products and allow recurring payments

[Clear Autoptimize Cache Automatically](https://bit.ly/clear-autoptimize-cache-automatically): Clear Autoptimize cache automatically by cache amount or by time interval

[FEWC – WooCommerce Extra Checkout Fields](https://bit.ly/fewc-extra-checkout-fields-for-woocommerce): Manage checkout fields using WordPress Customizer

[EPOW – Custom Product Options for WooCommerce](https://bit.ly/epow-custom-product-options-for-woocommerce): Add extra options for products using frontend form builder

[ChinaDS – Taobao Dropshipping for WooCommerce](https://bit.ly/chinads): Another Taobao dropshipping solution for WooCommerce stores

[9MAIL – WordPress Email Templates Designer](https://bit.ly/9mail-wp-email-templates-designer): Replace plaintext WordPress emails with more beautiful and professional templates

[EPOI – WP Points and Rewards](https://bit.ly/epoi-wordpress-points-and-rewards): Points and Rewards system for a WordPress website

[WebPOS – Point of Sale for WooCommerce](https://bit.ly/webpos-point-of-sale-for-woocommerce): Point of Sale solution for WooCommerce stores

[Jagif – WooCommerce Free Gift](https://bit.ly/jagif): Giving gifts to your customers can never be more easier

[COREEM – Coupon Reminder for WooCommerce](http://bit.ly/woo-coupon-reminder): Send emails to customers to remind them of their coupons, especially ones which are about to expire

[COMPE – WooCommerce Compare Products](https://bit.ly/compe-woo-compare-products): Help your customers compare two or more products to find out the right one they need

[W2S – Migrate WooCommerce to Shopify](https://bit.ly/w2s-migrate-woo-to-shopify): Migrate WooCommerce products to Shopify easily via the official Shopify REST Admin API

[Pofily – WooCommerce Product Filters](https://bit.ly/pofily-woo-product-filters): Advanced filters for WooCommerce

[REDIS - WooCommerce Dynamic Pricing and Discounts](https://bit.ly/redis-woo-dynamic-pricing-and-discounts): Create flexible pricing rules for products

[Bopo – Woo Product Bundle Builder](https://bit.ly/bopo-woo-product-bundle-builder): Let the plugin provide your customers with a very flexible and convenient way to purchase bundles

[WPBulky – WordPress Bulk Edit Post Types](https://bit.ly/wpbulky): Save time editing posts/pages/attachment... and other custom post types except for ones created by WooCommerce(product, shop_order and shop_coupon)

[Bulky - Bulk Edit Products for WooCommerce](http://bit.ly/bulk-edit-products-for-woo): Quickly and easily edit your products in bulk. This plugin will save you tons of time editing products.

[Catna – Woo Name Your Price and Offers](http://bit.ly/catna-woo-name-your-price-and-offers): Name Your Price and Offers

[Product Size Chart For WooCommerce](http://bit.ly/product-size-chart-for-woo): A simple but flexible solution to create size charts for your products

[Checkout Upsell Funnel for WooCommerce](http://bit.ly/woo-checkout-upsell-funnel): Offer product suggestions and smart order bumps on checkout page

[Cart All In One For WooCommerce](http://bit.ly/woo-cart-all-in-one): All cart features you need in one simple plugin

[Email Template Customizer for WooCommerce](http://bit.ly/woo-email-template-customizer): Customize WooCommerce emails to make them more beautiful and professional after only several mouse clicks

[Product Variations Swatches for WooCommerce](http://bit.ly/product-variations-swatches-for-woocommerce): Professional and beautiful colors, buttons, images, variation images and radio variations swatches

[Orders Tracking for WooCommerce](http://bit.ly/woo-orders-tracking): Import orders tracking number and send tracking info to customers

[Abandoned Cart Recovery For WooCommerce](http://bit.ly/woo-abandoned-cart-recovery): Capture abandoned carts & send reminder emails to customers.

[Import Shopify to WooCommerce](http://bit.ly/import-shopify-to-woocommerce): Import Shopify to WooCommerce plugin help you import all products from your Shopify store to WooCommerce

[Customer Coupons for WooCommerce](http://bit.ly/woo-customer-coupons): Display coupons on your website

[Virtual Reviews for WooCommerce](http://bit.ly/woo-virtual-reviews): Virtual Reviews for WooCommerce helps generate virtual reviews, display canned reviews for newly created store

[Thank You Page Customizer for WooCommerce](http://bit.ly/woo-thank-you-page-customizer): Customize your “Thank You” page and give coupons to customers after a successful order

[Sales Countdown Timer](http://bit.ly/sales-countdown-timer): Create a sense of urgency with a countdown to the beginning or end of sales, store launch or other events

[EU Cookies Bar](http://bit.ly/eu-cookies-bar): A very simple plugin which helps your website comply with Cookie Law

[Lucky Wheel for WooCommerce](http://bit.ly/woo-lucky-wheel): Offer customers to spin for coupons by entering their emails.

[WordPress Lucky Wheel](http://bit.ly/wp-lucky-wheel): WordPress Lucky Wheel gives you the best solution to get emails address from visitors of your WordPress website

[Advanced Product Information for WooCommerce](http://bit.ly/woo-advanced-product-information): Display more intuitive information of products such as sale countdown, sale badges, who recently bought products, rank of products in their categories, available payment methods...

[LookBook for WooCommerce](http://bit.ly/woo-lookbook): Create beautiful Lookbooks, Shoppable with Product Tags

[Photo Reviews for WooCommerce](http://bit.ly/woo-photo-reviews): Allow posting reviews include product pictures, review reminder, review for coupons.

[Product Builder for WooCommerce](http://bit.ly/woo-product-builder): Allows your customers to build a full product set from small parts step by step. The plugin works base on WooCommerce with many useful features like compatible, email completed product, attributes filters.

[Boost Sales for WooCommerce](http://bit.ly/woo-boost-sales): Increase profit on every single order with Up-selling and Cross-selling

[Free Shipping Bar for WooCommerce](http://bit.ly/woo-free-shipping-bar): Use free shipping as a marketing tool, encourage customers to pay more for free shipping.

[Notification for WooCommerce](http://bit.ly/woo-notification): Social Proof Marketing plugin. Live recent order on the front-end of your site.

[Multi Currency for WooCommerce](http://bit.ly/woo-multi-currency): Switches to different currencies easily and accepts payment with only one currency or all currencies.

[Coupon Box for WooCommerce](http://bit.ly/woo-coupon-box-free): Subscribe emails for discount coupons

### Plugin Links

- [Project Page](https://villatheme.com/extensions/exmage-wordpress-image-links/)
- [Report Bugs/Issues](https://wordpress.org/support/plugin/exmage-wp-image-links/)

== Installation ==

1. Unzip the download package
1. Upload `exmage-wp-image-links` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

== Screenshots ==
1. Add multiple image URLs
2. Add single image URL

== Changelog ==
/**1.0.17 - 2024.04.13**/
– Updated: Compatible with WP 6.5
– Updated: Update support class

/**1.0.16 - 2023.09.06**/
- Updated: Add stop processing button

/**1.0.15 - 2023.05.12**/
- Updated: Keep exmage link of product when import product from csv

/**1.0.14 - 2023.01.11**/
- Updated: If the number of images is greater than threshold(20 by default, able to change via exmage_ajax_handle_url_threshold hook), they will be processed in the background
- Updated: Compatible with WPML's attachment translations feature
- Dev: Added exmage_insert_attachment_image_name filter hook

/**1.0.13 - 2023.01.10**/
- Fixed: Compatibility issue with Photon CDN(Jetpack)
- Dev: Added exmage_get_supported_image_sizes, exmage_image_size_url filter hooks

/**1.0.12 - 2022.11.17**/
- Fixed: Image URL processing in some cases
- Updated: Compatibility check with WP 6.1

/**1.0.11 - 2022.08.29**/
- Dev: Added exmage_get_supported_mime_types filter hook

/**1.0.10 - 2022.07.22**/
- Updated: VillaTheme_Support
- Updated: Data sanitization/escaping check

/**1.0.9 - 2022.05.07**/
- Fixed: Error with URLs that contains more than 255 characters

/**1.0.8 - 2022.04.19**/
- Updated: VillaTheme_Support

/**1.0.7 - 2022.04.14**/
- Fixed: Use wp_http_validate_url before remote call

/**1.0.6 - 2022.03.29**/
- Updated: VillaTheme_Support

/**1.0.5 - 2022.03.28**/
- Fixed: Use wp_safe_remote_get instead of wp_remote_get to avoid redirection and request forgery attacks

/**1.0.4 - 2022.03.21**/
- Updated: VillaTheme_Support

/**1.0.3 - 2022.01.14**/
- Optimized: Enqueue script

/**1.0.2.2 - 2022.01.10**/
- Updated: VillaTheme_Support

/**1.0.2.1 - 2021.12.13**/
- Updated: Missing css/image files for class VillaTheme_Support

/**1.0.2 - 2021.12.11**/
- Added: Button to store external images to server in Media library/list view mode

/**1.0.1 - 2021.12.08**/
- Updated: Do not allow to edit(crop, rotate...) external images to avoid unexpected errors

/**1.0.0 - 2021.12.07**/
 - Released