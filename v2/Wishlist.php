<?php
namespace CIXW_WISHLIST;

class Wishlist {
	public function __construct() {
		add_shortcode( 'cix_woocommerce_wishlist', array( $this, 'wishlist_shortcode' ) );
        add_shortcode( 'cix_add_to_wishlist', array( $this, 'add_to_wishlist_shortcode' ) );
	}
    /**
	 * Render wishlist shortcode
	 */
	public function add_to_wishlist_shortcode() {

		ob_start();
		Bootstrap::woocommerce_add_to_wishlist();
		return ob_get_clean();
	}

	/**
	 * Render wishlist shortcode
	 */
	public function wishlist_shortcode() {

		ob_start();
		do_action( 'cix_woocommerce_wishlist_before_wishlist' );
		
		Bootstrap::woocommerce_wishlist_locate_template( 'wishlist-v2.php' );

		do_action( 'cix_woocommerce_wishlist_after_wishlist' );
		
		return ob_get_clean();
	}
}
