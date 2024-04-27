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
		$wishlist_popup = cixww_get_option( 'product_button_action' );
		$js_deps        = array( 'jquery' );
		if ( $wishlist_popup == 'popup' || get_the_id() == cixww_get_option( 'wishlist_page' ) ) {
			wp_enqueue_style( 'cix-wishlist-modal', CIXWW_PLUGIN_URL . 'assets/css/jquery.modal.min.css', array(), CIXWW_PLUGIN_VER, 'all' );
			wp_enqueue_script( 'cix-wishlist-modal', CIXWW_PLUGIN_URL . 'assets/js/jquery.modal.min.js', array( 'jquery' ), CIXWW_PLUGIN_VER, true );

			$js_deps = array( 'jquery', 'cix-wishlist-modal' );
		}

		wp_enqueue_script( 'cix-wishlist', CIXWW_PLUGIN_URL . 'assets/js/wishlist-v2.js', $js_deps, CIXWW_PLUGIN_VER, true );

		wp_localize_script(
			'cix-wishlist',
			'cix_wishlist_args',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'cix-wishlist-nonce' ),

			)
		);
		wp_enqueue_style( 'cix-wishlist', CIXWW_PLUGIN_URL . 'assets/css/wishlist.css', array(), CIXWW_PLUGIN_VER );
		// add inline css
		$i_color = cixww_get_option( 'product_button_txt_color' );
		$css     = cixww_get_option( 'wishlist_css' );
		if ( cixww_get_option( 'loop_button_position' ) == 'in_thumb' ) {
			$css .= '.archive .jvm_add_to_wishlist{position: absolute;
				top: 5px;
				left: 5px;
			}
			.archive .jvm_add_to_wishlist.btn-link{
				top: 10px;
			}';
		}
		if ( $i_color ) {
			$css .= '.in_wishlist.jvm_add_to_wishlist span{
				color: ' . $i_color['active'] . ' !important;
			}
			.jvm_add_to_wishlist:hover span{
				color: ' . $i_color['hover'] . ' !important;
			}';
		}
		if ( cixww_get_option( 'product_button_text' ) ) {
			$css .= '.jvm_add_to_wishlist_heart{
				margin-right: 5px;
				}';
		}
		wp_add_inline_style( 'cix-wishlist', $css );
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

			$data = array(
				'pid'                 => $product_id,
				'show_icon'           => $show_icon,
				'already_in_wishlist' => in_array( $product_id, Wishlist::wishlist_product_ids() ),
			);
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
	public static function activation() {
		// add if_page_exist
		self::if_page_exist( 'wishlist' );
	}
	/**
	 * Check if a page exists by slug and create it if it doesn't.
	 *
	 * @param string $page_title The title of the page
	 * @return int|bool The page ID if it exists, false if it doesn't
	 */
	public static function if_page_exist( $page_slug ) {
		if ( get_page_by_path( $page_slug ) ) {

			return $page_slug;
		} else {

			$page = array(
				'post_title'   => 'Wishlist',
				'post_content' => '[cix_woocommerce_wishlist]',
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_type'    => 'page',
			);
			// Insert the post into the database
			$post_id = wp_insert_post( $page );
			$post    = get_post( $post_id );
			$slug    = $post->post_name;
			return $slug;

		}
	}
}
