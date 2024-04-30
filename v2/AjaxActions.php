<?php
namespace CIXW_WISHLIST;

class AjaxActions {

	/**
	 * constructor.
	 */
	public function __construct() {
		Helper::add_ajax( 'cix_update_wishlist', array( $this, 'update_wishlist' ) );
		Helper::add_ajax( 'cix_wishlist_add_to_cart', array( $this, 'add_to_cart_wishlist_page' ) );
		Helper::add_ajax( 'cix_remove_product', array( $this, 'remove_product_wishlist_page' ) );
	}
	public function add_to_cart_wishlist_page() {
		if ( ! DOING_AJAX ) {
			wp_die();
		} // Not Ajax

		// Check for nonce security
		$nonce = sanitize_text_field( $_POST['nonce'] );
		if ( ! wp_verify_nonce( $nonce, 'cix-wishlist-nonce' ) ) {
				wp_die( 'Oops! nonce error' );
		}

		if ( isset( $_POST['product_id'] ) && absint( $_POST['product_id'] ) != 0 ) {
			$product_id = absint( $_POST['product_id'] );

			WC()->cart->add_to_cart( $product_id );

			// Redirect to cart page after adding to cart from wishlist page.
			if ( cixww_get_option( 'wishlist_page_table_redirect_to_cart' ) ) {
				// cart page link in a tag
				$data['cart_url'] = wc_get_cart_url();
			} else {
				$data['add_to_cart_notice'] = Helper::replace_text( cixww_get_option( 'add_to_cart_notice' ), '{product_name}', $product_id );
			}
			// wishlist_page_table_remove_if_added_to_cart
			if ( cixww_get_option( 'wishlist_page_table_remove_if_added_to_cart' ) ) {
				Wishlist::remove_product( $product_id );
				$data['removed'] = true;

			}

			wp_send_json_success( $data );
		}

		wp_die();
	}
	public function remove_product_wishlist_page() {
		if ( ! DOING_AJAX ) {
			wp_die();
		} // Not Ajax

		// Check for nonce security
		$nonce = sanitize_text_field( $_POST['nonce'] );
		if ( ! wp_verify_nonce( $nonce, 'cix-wishlist-nonce' ) ) {
				wp_die( 'Oops! nonce error' );
		}
		$data = array();

		if ( isset( $_POST['product_id'] ) && absint( $_POST['product_id'] ) != 0 ) {
			$product_id = absint( $_POST['product_id'] );

				Wishlist::remove_product( $product_id );

				$data['remove_notice'] = '<a href="#" data-product-id="' . $product_id . '" class="wishlist-undo ciww-cart-link">' . __( 'Undo?', 'jvm-woocommerce-wishlist' ) . '</a>' . Helper::replace_text( cixww_get_option( 'removed_cart_notice' ), '{product_name}', $product_id );
			wp_send_json_success( $data );
		}

		wp_die();
	}
	public function update_wishlist() {

		if ( ! DOING_AJAX ) {
			wp_die();
		} // Not Ajax

			// Check for nonce security
			$nonce              = sanitize_text_field( $_POST['nonce'] );
			$product_id         = sanitize_text_field( $_POST['product_id'] );
			$show_icon          = cixww_get_option( 'product_button_icon' );
			$after_added_action = cixww_get_option( 'product_button_action' );
			$cart_all           = isset( $_POST['cart_all'] ) ? sanitize_text_field( $_POST['cart_all'] ) : false;

			$data = array(
				'product_id'          => $product_id,
				'show_icon'           => $show_icon,
				'already_in_wishlist' => in_array( $product_id, Wishlist::wishlist_product_ids() ),
			);

			if ( $cart_all ) {
				$product_ids = Wishlist::wishlist_product_ids();
				foreach ( $product_ids as $product_id ) {
					WC()->cart->add_to_cart( $product_id );

					// wishlist_page_table_remove_if_added_to_cart
					if ( cixww_get_option( 'wishlist_page_table_remove_if_added_to_cart' ) ) {
						Wishlist::remove_product( $product_id );
						$data['removed'] = true;

					}
				}
				wp_send_json_success( $data );
				wp_die();
			}
			if ( $after_added_action == 'redirect' ) {
				$data['redirect']     = true;
				$data['redirect_url'] = get_the_permalink( cixww_get_option( 'wishlist_page' ) );
			}
			if ( $after_added_action == 'popup' ) {
				$data['popup'] = true;
			}
			if ( cixww_get_option( 'remove_on_second_click' ) && in_array( $product_id, Wishlist::wishlist_product_ids() ) ) {
				Wishlist::remove_product( $product_id );
				$data['removed'] = true;
				unset( $data['already_in_wishlist'] );
				$data['template'] = Wishlist::wishlist_popup( $data );
			} elseif ( in_array( $product_id, Wishlist::wishlist_product_ids() ) ) {

				$data['template'] = Wishlist::wishlist_popup( $data );

			} else {
				Wishlist::set_transient( $product_id );
				$data['added']     = true;
				$data['template']  = Wishlist::wishlist_popup( $data );
				$data['loop_item'] = Wishlist::wishlist_loop_items( array( 'product_id' => $product_id ) );

			}

			if ( ! wp_verify_nonce( $nonce, 'cix-wishlist-nonce' ) ) {
				wp_die( 'Oops! nonce error' );
			}

			wp_send_json_success( $data );
			wp_die(); // this is required to terminate immediately and return a proper response

			if ( isset( $_POST['userId'] ) ) {
				$product_ids = isset( $_POST['wishlistIds'] ) ? $_POST['wishlistIds'] : array();
				$user_id     = absint( $_POST['userId'] );
				$cookie_name = 'cix_wc_wishlist';

				// Clean product ids
				$product_ids = self::wishlist_product_ids( $product_ids );

				// if user is logged in, we store the wishlist in the user meta
				if ( $user_id == get_current_user_id() ) {
					update_user_meta( $user_id, $cookie_name, $product_ids );
				}
			}
	}
}
