<?php
namespace CIXW_WISHLIST;

class Bootstrap {

	protected $wishlist_slug;

	public function __construct() {
		new Wishlist();
		new Settings();
		add_action( 'wp_ajax_cix_update_wishlist', array( $this, 'update_wishlist' ) );
		add_action( 'wp_ajax_nopriv_cix_update_wishlist', array( $this, 'update_wishlist' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'cix-wishlist-js', CIXWW_PLUGIN_URL . 'assets/js/wishlist-v2.js', array( 'jquery' ), CIXWW_PLUGIN_VER, true );
		wp_localize_script(
			'cix-wishlist-js',
			'cix_wishlist_args',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'cix-wishlist-nonce' ),

			)
		);
		wp_enqueue_style( 'cix-wishlist', CIXWW_PLUGIN_URL . 'assets/css/wishlist.css', array(), CIXWW_PLUGIN_VER );
		// add inline css
		$css = cixww_get_option( 'wishlist_css' );
		if ( cixww_get_option( 'loop_button_position' ) == 'in_thumb' ) {
			$css .= '.archive .jvm_add_to_wishlist{position: absolute;
				top: 5px;
				left: 5px;
			}
			.archive .jvm_add_to_wishlist.btn-link{
				top: 10px;
			}';
		}
		if ( ! empty( $css ) ) {
			wp_add_inline_style( 'cix-wishlist', $css );
		}
	}
	public function update_wishlist() {

		if ( ! DOING_AJAX ) {
			wp_die();
		} // Not Ajax

			// Check for nonce security
			$nonce      = sanitize_text_field( $_POST['nonce'] );
			$product_id = sanitize_text_field( $_POST['product_id'] );
			$show_icon  = cixww_get_option( 'product_button_icon' );

			$data = array(
				'pid'       => $product_id,
				'show_icon' => $show_icon,
			);
			Wishlist::temp_cookie();

			if ( cixww_get_option( 'remove_on_second_click' ) && Wishlist::wishlist_product_ids() ) {
				Wishlist::remove_product( $product_id );
				$data['removed'] = true;
			} else {
				Wishlist::set_transient( $product_id );
			}

			if ( ! wp_verify_nonce( $nonce, 'cix-wishlist-nonce' ) ) {
				wp_die( 'oops! nonce error' );
			}

			wp_send_json_success( $data );
			wp_die(); // this is required to terminate immediately and return a proper response

			if ( isset( $_POST['userId'] ) ) {
				$product_ids = isset( $_POST['wishlistIds'] ) ? $_POST['wishlistIds'] : array();
				$user_id     = absint( $_POST['userId'] );
				$cookie_name = self::wishlist_get_site_slug() . '_wc_wishlist';

				// Clean product ids
				$product_ids = self::wishlist_product_ids( $product_ids );

				// if user is logged in, we store the wishlist in the user meta
				if ( $user_id == get_current_user_id() ) {
					update_user_meta( $user_id, $cookie_name, $product_ids );
				}
			}
	}
}
