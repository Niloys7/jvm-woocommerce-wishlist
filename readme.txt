=== Wishlist for WooCommerce  ===
Contributors: im_niloy,jorisvanmontfort,wpinteractive,codeixer
Tags: wishlist for woocommerce, yith wishlist, woocommerce wishlist,ti wishlist
Requires at least: 5.0
Tested up to: 6.5.2
Stable tag: 2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enhance your e-commerce store's functionality with WooCommerce Wishlist - the ultimate tool that adds a powerful and lightweight wishlist feature. Improve your customer's shopping experience and boost your sales with this essential addition to your online store. 🚀

== Description ==
A simple and lightweight wishlist for WooCommerce, with plenty of hooks for customization to fit your WooCommerce theme.

By default, the plugin adds a wishlist icon to the WooCommerce archive pages and the WooCommerce single product page. Instead of an icon you can also switch to a text-based link to or add/remove wishlist items.
The wishlist can be added to a page using the `[jvm_woocommerce_add_to_wishlist]` shortcode. For more advanced customization see the hooks, javascript API and templates sections below.

== Installation ==

1. Install the plugin from the Plugins or upload the plugin folder to the `/wp-content/plugins/` directory menu and then activate it.
2. Go to the plugin setting screen to define a wishlist page.
3. Go to the plugin settings page from the plugins screen and create your wishlist page.


== Changelog ==

= 1.3.6 - 23 Sep 22 =

Fixed: default WooCommerce style for wishlist page table
Compatibility with WooCommerce 7.1


= 1.3.5 - 23 Sep 22 =

Bug fix
Added: appsero insights
Compatibility with WooCommerce 6.9.3


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