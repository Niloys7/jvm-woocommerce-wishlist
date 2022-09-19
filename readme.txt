=== JVM WooCommerce Wishlist ===
Contributors: jorisvanmontfort
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VXZJG9GC34JJU
Tags: woocommerce, wishlist, e-commerce, shop, products, theme
Requires at least: 4.4.1
Tested up to: 5.4.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple and lightweight wishlist for WooCommerce, with plenty of hooks for customization to fit your WooCommerce theme.

== Description ==
A simple and lightweight wishlist for WooCommerce, with plenty of hooks for customization to fit your WooCommerce theme. Tested on WooCommerce 3.x but should work on any WooCommerce 4.x installation.

By default the plugin adds a wishlist icon to the WooCommerce archive pages and the WooCommerce single product page. Instead of an icon you can also switch to a text based link to or add remove wishlist items.
The wishlist can be added to a page using the `[jvm_woocommerce_add_to_wishlist]` shortcode. For more advanced customisation see the hooks, javascript API and templates sections below.

== Installation ==

The plugin assumes you're already using WooCommerce. Without WooCommerce this plugin won't do much. 

1. Install the plugin from the Plugins or upload the plugin folder to the `/wp-content/plugins/` directory menu and then activate it.
2. Go to the plugin setting screen to define a wishlist page.
3. Go to the plugin settings page from the plugins screen and create your wishlist page.

== Hooks ==

For advanced users and developers a few WordPress actions and filters are supplied to modify the behaviour of the wishlist. 

Example usage removing the button from the achive page:
`
remove_action( 'woocommerce_after_shop_loop_item', 'jvm_woocommerce_add_to_wishlist', 15 );
`

Example usage switching to text links instead of icons:
`
add_filter( 'jvm_add_to_wishlist_class', function($class) {
	return 'jvm_add_to_wishlist text';
});
`
== Actions ==

Available actions:

* jvm_woocommerce_wishlist_loaded 
* jvm_woocommerce_wishlist_before_wishlist
* jvm_woocommerce_wishlist_after_wishlist
* jvm_woocommerce_wishlist_before_wishlist_contents
* jvm_woocommerce_wishlist_after_wishlist_contents
* jvm_woocommerce_wishlist_before_add_to_wishlist
* jvm_woocommerce_wishlist_after_add_to_wishlist

== Filters ==

* jvm_add_to_wishlist_class
* jvm_add_to_wishlist_icon_html
* jvm_woocommerce_wishlist_product_ids

== Functions ==

* jvm_woocommerce_wishlist_get_count()
* jvm_woocommerce_wishlist_get_wishlist_product_ids()

== Templates ==

The default wishlist page can be overridden in your theme. From the plugin directory copy 
`
templates/wishlist.php 
`
to:
`
your-theme-directory/jvm-woocommerce-wishlist/wishlist.php 
`
Modify what you need in the copy.

== Javascript API ==

For further theme integration the javascript file in the plugin has a few custom events that are fired for wishlist user activity.

* add.JVMWooCommerceWishlist
* remove.JVMWooCommerceWishlist
* beforeupdate.JVMWooCommerceWishlist
* afterupdate.JVMWooCommerceWishlist

Example usage:
`
$(document).on("beforeupdate.JVMWooCommerceWishlist", function(e) {
  console.log(e);

  // Do something like update some custom counter, show a message or whatever.
});
`

== Changelog ==

= 1.3.4 =
Added a new filter for modifying the icon HTML: jvm_add_to_wishlist_icon_html

= 1.3.3 =
Bug fix php error notice in ajax/www-ajax-functions.php

= 1.3.2 =
Added an optional $product _id parameter to the jvm_woocommerce_add_to_wishlist function for use of this function outside of the loop, for increased flexibility.

= 1.3.1 =
Bug fix. Whitespace in main plugin file. Please update.

= 1.3.0 =
Fixed a fatal error in update 1.2.9. Please upgrade if you are on 1.2.9.

= 1.2.9 =
Some slight changes to wishlist storage. The cookie is now always cleared on logout. Also newly added products when not logged in will be added after login.

= 1.2.8 =
Bug fix: When logged in last item on wishlist would need to be removed twice. Should be fixed now. Also no ajax requests will de done if a user is not logged in to reduce overhead.

= 1.2.7 =
Security fix. User ID passed to ajax calls must match the current user.

= 1.2.6 =
Another whitespace fix.

= 1.2.5 =
Fixed a whitespace issue in front end link.

= 1.2.4 =
Added a partially Japanese translation.

= 1.2.3 =
Added a grunt task for automated POT files.
Added and a Dutch translation and auto generated POT file.

= 1.2.2 =
Added a dontation button.

= 1.2.1 =
Added a Dutch translation.

= 1.2.0 =
Fixed a bug where the custom wishlist template would not load from the (child) theme.

= 1.1.0 =
Fixed a bug  "No products on your wishlist yet." shown with products in wishlist on other pages than the main wishlist page (plugin settings).

= 1.0.0 =
Initial release

= Stable =
1.0.0