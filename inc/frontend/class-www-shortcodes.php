<?php
/**
 * JVM Woocommerce Wishlist Shortcode.
 *
 * @class JVM_WW_Shortcodes
 
 * @category Core
 * @package JVMWooCommerceWishlist/Shortcode
 * @version 1.0.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * JVM_WW_Shortcodes class.
 */
class JVM_WW_Shortcodes {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_shortcode( 'jvm_woocommerce_wishlist', array( $this, 'wishlist_shortcode' ) );
		add_shortcode( 'jvm_woocommerce_add_to_wishlist', array( $this, 'add_to_wishlist_shortcode' ) );
	}

	/**
	 * Render wishlist shortcode
	 */
	public function wishlist_shortcode() {

		ob_start();
		jvm_woocommerce_wishlist();
		return ob_get_clean();
	}

	/**
	 * Render wishlist shortcode
	 */
	public function add_to_wishlist_shortcode() {

		ob_start();
		jvm_woocommerce_add_to_wishlist();
		return ob_get_clean();
	}

	/**
	 * Helper method to determine if a shortcode attribute is true or false.
	 *
	 * @since 1.0.0
	 * @param string|int|bool $var Attribute value.
	 * @return bool
	 */
	protected function shortcode_bool( $var ) {
		$falsey = array( 'false', '0', 'no', 'n' );
		return ( ! $var || in_array( strtolower( $var ), $falsey, true ) ) ? false : true;
	}

} // end class

return new JVM_WW_Shortcodes();