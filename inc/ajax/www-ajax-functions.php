<?php
/**
 * JVM Woocommerce Wishlist AJAX Functions
 *
 *
 
 * @category Ajax
 * @package JVMWooCommerceWishlist/Functions
 * @version 1.0.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * update wishlist user meta
 *
 * @since 1.0.0
 */
function jvm_woocommerce_wishlist_ajax_update_wishlist() {

	extract( $_POST );

	if ( isset( $_POST['userId'] ) ) {
		$product_ids = isset($_POST['wishlistIds']) ? $_POST['wishlistIds'] : array();
		$user_id = absint( $_POST['userId'] );
		$cookie_name = jvm_woocommerce_wishlist_get_site_slug() . '_wc_wishlist';

		// Clean product ids
		$product_ids = jvm_woocommerce_wishlist_clean_wishlist_product_ids( $product_ids );

		// if user is logged in, we store the wishlist in the user meta
		if ( $user_id  == get_current_user_id() ) {
			update_user_meta( $user_id, $cookie_name, $product_ids );
		}
	}

	exit;
}
add_action( 'wp_ajax_jvm_woocommerce_wishlist_ajax_update_wishlist', 'jvm_woocommerce_wishlist_ajax_update_wishlist' );
add_action( 'wp_ajax_nopriv_jvm_woocommerce_wishlist_ajax_update_wishlist', 'jvm_woocommerce_wishlist_ajax_update_wishlist' );