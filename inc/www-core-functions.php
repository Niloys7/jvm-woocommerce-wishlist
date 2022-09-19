<?php
/**
 * JVM Woocommerce Wishlist core functions
 *
 * General core functions available on admin and frontend
 *
 
 * @category Core
 * @package JVMWooCommerceWishlist/Core
 * @version 1.0.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get site name slug
 *
 * @return string
 */
function jvm_woocommerce_wishlist_get_site_slug() {
	return str_replace( '-', '_', sanitize_title_with_dashes( get_bloginfo('name' ) ) );
}

/**
 * Get wishlist page id
 *
 * @param string $value
 * @param string $default
 * @return string
 */
function jvm_woocommerce_wishlist_get_page_id() {
	$page_id = -1;

	if ( -1 != get_option( 'jvm_woocommerce_wishlist_page_id' ) && get_option( 'jvm_woocommerce_wishlist_page_id' ) ) {

		$page_id = get_option( 'jvm_woocommerce_wishlist_page_id' );
	}

	if ( -1 != $page_id ) {
		$page_id = apply_filters( 'wpml_object_id', absint( $page_id ), 'page', false ); // filter for WPML
	}

	return $page_id;
}

if ( ! function_exists( 'jvm_get_wishlist_url' ) ) {
	/**
	 * Returns the URL of the wishlist page
	 */
	function jvm_get_wishlist_url() {

		$page_id = jvm_woocommerce_wishlist_get_page_id();

		if ( -1 != $page_id ) {
			return get_permalink( $page_id );
		}
	}
}

/**
 * Get options
 *
 * @param string $value
 * @param string $default
 * @return string
 */
function jvm_woocommerce_wishlist_get_option( $value, $default = null ) {

	$jvm_woocommerce_wishlist_settings = get_option( 'jvm_woocommerce_wishlist_settings' );
	
	if ( isset( $jvm_woocommerce_wishlist_settings[ $value ] ) && '' != $jvm_woocommerce_wishlist_settings[ $value ] ) {
		
		return $jvm_woocommerce_wishlist_settings[ $value ];
	
	} elseif ( $default ) {

		return $default;
	}
}