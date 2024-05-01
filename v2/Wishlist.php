<?php
namespace CIXW_WISHLIST;

class Wishlist {
	public function __construct() {
		add_shortcode( 'cix_woocommerce_wishlist', array( $this, 'wishlist_shortcode' ) );
		add_shortcode( 'cix_add_to_wishlist', array( $this, 'add_to_wishlist_shortcode' ) );
		self::temp_cookie();
		$this->display_loop_wishlist_button();
		$this->display_single_product_wishlist_button();
		add_action( 'wp_footer', array( $this, 'wishlist_popup_html' ) );
		add_filter( 'cix_replace_text_list', array( $this, 'replace_info' ), 10, 2 );
	}
	/**
	 * Generates the HTML for the wishlist popup.
	 *
	 * This function checks the value of the 'product_button_action' option and the current post ID to determine if the wishlist popup should be displayed. If the value is 'popup' or the current post ID matches the 'wishlist_page' option, the wishlist modal is embedded in the page.
	 *
	 */
	public function wishlist_popup_html() {
		$wishlist_popup = cixww_get_option( 'product_button_action' );
		if ( $wishlist_popup == 'popup' || get_the_id() == cixww_get_option( 'wishlist_page' ) ) {?>
			<!-- wishlist modal embedded in page -->
			<div id="wishlist-modal" class="modal"></div>
			<?php
		}
	}
	/**
	 * Display wishlist button on single product
	 */
	public function display_single_product_wishlist_button() {

		if ( cixww_get_option( 'product_button' ) == 1 && cixww_get_option( 'product_button_position' ) == 'after' ) {
			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'woocommerce_add_to_wishlist' ), 10 );
		} elseif ( cixww_get_option( 'product_button' ) == 1 && cixww_get_option( 'product_button_position' ) == 'before' ) {
			add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'woocommerce_add_to_wishlist' ), 5 );
		} elseif ( cixww_get_option( 'product_button' ) == 1 && cixww_get_option( 'product_button_position' ) == 'after_summary' ) {
			add_action( 'woocommerce_after_single_product_summary', array( $this, 'woocommerce_add_to_wishlist' ), 10 );
		}
	}
	/**
	 * Display wishlist button on loop
	 */
	public function display_loop_wishlist_button() {
		if ( cixww_get_option( 'loop_button' ) == 1 && cixww_get_option( 'loop_button_position' ) == 'after' ) {
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'woocommerce_add_to_wishlist' ), 10 );
		} elseif ( cixww_get_option( 'loop_button' ) == 1 && cixww_get_option( 'loop_button_position' ) == 'before' ) {
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'woocommerce_add_to_wishlist' ), 5 );
		} elseif ( cixww_get_option( 'loop_button' ) == 1 && cixww_get_option( 'loop_button_position' ) == 'in_thumb' ) {
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'woocommerce_add_to_wishlist' ), 10 );
		}
	}
	/**
	 * Render wishlist shortcode
	 */
	public function add_to_wishlist_shortcode() {

		ob_start();
		self::woocommerce_add_to_wishlist();
		return ob_get_clean();
	}

	/**
	 * Render wishlist shortcode
	 */
	public function wishlist_shortcode() {

		ob_start();
		do_action( 'cix_woocommerce_wishlist_before_wishlist' );
		self::woocommerce_wishlist_locate_template( 'wishlist-v2.php' );
		do_action( 'cix_woocommerce_wishlist_after_wishlist' );
		return ob_get_clean();
	}
	/**
	 * Render wishlist popup for added item
	 */
	public static function wishlist_popup( $args = array() ) {

		ob_start();
		self::woocommerce_wishlist_locate_template( 'wishlist-popup.php', $args );
		return ob_get_clean();
	}
	/**
	 * Render wishlist loop items
	 */
	public static function wishlist_loop_items( $args = array() ) {

		ob_start();
		self::woocommerce_wishlist_locate_template( 'wishlist-loop-item.php', $args );
		return ob_get_clean();
	}


	/**
	 * Locates and includes a template file for the WooCommerce Wishlist plugin.
	 *
	 * @param string $path The path of the template file.
	 * @param mixed  $params Optional parameters to be passed to the template.
	 * @return void
	 */
	public static function woocommerce_wishlist_locate_template( $path, $args = array() ) {
		$located     = locate_template( array( 'wishlist' . DIRECTORY_SEPARATOR . $path ), true, true, $args );
		$plugin_path = CIXWW_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $path;

		if ( ! $located && file_exists( $plugin_path ) ) {
			$final_file = $plugin_path;
		} elseif ( $located ) {
			$final_file = $located;
		}

		include $final_file;
	}

	public static function set_transient( $product_id ) {
		$wishlist   = self::wishlist_product_ids();
		$wishlist[] = $product_id;
		$wishlist   = array_unique( $wishlist );

		$expiration = DAY_IN_SECONDS * cixww_get_option( 'guest_wishlist_delete', 30 ); // 30 days
		set_transient( 'cix_wc_wishlist_' . self::get_wishlist_temp_id(), $wishlist, $expiration );
	}
	/**
	 * Get a PHP array of products in the wishlist
	 */
	public static function wishlist_product_ids( $product_ids = array(), $wishlist_id = null ) {

		$clean_product_ids = ( get_transient( 'cix_wc_wishlist_' . self::get_wishlist_temp_id() ) ) ? get_transient( 'cix_wc_wishlist_' . self::get_wishlist_temp_id() ) : array();

		foreach ( $product_ids as $product_id ) {

			if ( 'publish' == get_post_status( $product_id ) ) {
				$clean_product_ids[] = $product_id;
			}
		}

		return $clean_product_ids;
	}

	/**
	 * Generates and retrieves a temporary cookie for the wishlist.
	 *
	 * This function generates a temporary cookie for the wishlist and retrieves its value if it already exists.
	 * The cookie name is determined by appending the site slug with '_wc_wishlist_temp'.
	 * If the cookie does not exist, a new temporary ID is generated using wp_generate_password() function.
	 * The cookie is then set with the generated ID, and its expiration is set to 7 days from the current time.
	 *
	 * @return string|null The value of the temporary cookie, or null if it does not exist.
	 */
	public static function temp_cookie() {

		// add wishlist slug to cookie name
		$cookie_name = 'cix_wc_wishlist_temp';
		$cookie      = ( isset( $_COOKIE[ $cookie_name ] ) ) ? $_COOKIE[ $cookie_name ] : null;

		if ( ! $cookie ) {
			$temp_id = wp_generate_password( 8, false );

			setcookie( $cookie_name, $temp_id, strtotime( '+7 day', time() ), '/' );
			return $cookie;
		}
	}
	/**
	 * Get the temporary wishlist ID from the cookie value.
	 */
	public static function get_wishlist_temp_id() {
		$cookie_name = 'cix_wc_wishlist_temp';
		$cookie      = ( isset( $_COOKIE[ $cookie_name ] ) ) ? $_COOKIE[ $cookie_name ] : null;
		return $cookie;
	}

	/**
	 * Removes a product from the wishlist.
	 *
	 * @param int      $product_id   The ID of the product to be removed.
	 * @param int|null $wishlist_id  The ID of the wishlist. If null, the default wishlist is used.
	 * @return void
	 */
	public static function remove_product( $product_id, $wishlist_id = null ) {
		if ( $product_id ) {
			$wishlist = self::wishlist_product_ids();

			$wishlist   = array_diff( $wishlist, array( $product_id ) );
			$wishlist   = array_unique( $wishlist );
			$expiration = DAY_IN_SECONDS * cixww_get_option( 'guest_wishlist_delete', 30 ); // 30 days
			set_transient( 'cix_wc_wishlist_' . self::get_wishlist_temp_id(), $wishlist, $expiration );

		}
	}
	
	/**
	 * Adds or removes a product from the wishlist and display button HTML
	 *
	 * @param int|null $product_id The ID of the product to add or remove from the wishlist. If not provided, the current post ID is used.
	 * @return void
	 */
	public static function woocommerce_add_to_wishlist( $product_id = null ) {
		$wishlist       = self::wishlist_product_ids();
		$product_id     = empty( $product_id ) ? get_the_ID() : $product_id;
		$is_in_wishlist = ( $wishlist ) ? ( in_array( $product_id, $wishlist ) ) : false;
		$class          = ( $is_in_wishlist ) ? 'in_wishlist ' : '';
		$text           = ( $is_in_wishlist && cixww_get_option( 'remove_on_second_click' ) ) ? cixww_get_option( 'product_button_remove_text' ) : esc_html( cixww_get_option( 'product_button_text' ) );
		$show_icon      = ( cixww_get_option( 'product_button_icon' ) == 1 ) ? true : false;
		// Hook for icon HTML
		$icon_html = ( $show_icon ) ? apply_filters( 'cix_add_to_wishlist_icon_html', '<span class="jvm_add_to_wishlist_heart"></span>' ) : '';

		do_action( 'cix_woocommerce_wishlist_before_add_to_wishlist', $product_id );
		$button_class = ( cixww_get_option( 'product_button_type' ) == 'button' ) ? 'button' : 'btn-link';
		$class       .= apply_filters( 'cix_add_to_wishlist_class', ' jvm_add_to_wishlist ' . $button_class );
		?>
			<a class="<?php echo esc_attr( $class ); ?>" href="?add_to_wishlist=<?php echo $product_id; ?>" title="<?php echo esc_attr( $text ); ?>" rel="nofollow" data-product-title="<?php echo esc_attr( get_the_title( $product_id ) ); ?>" data-product-id="<?php echo $product_id; ?>" <?php echo ( cixww_get_option( 'remove_on_second_click' ) && in_array( $product_id, $wishlist ) ) ? 'data-remove=' . $product_id : ''; ?> data-modal="#login-modal">
					<?php echo $icon_html; ?>
				<span class="jvm_add_to_wishlist_text_add"><?php echo esc_html( cixww_get_option( 'product_button_text' ) ); ?></span>

				<?php if ( cixww_get_option( 'remove_on_second_click' ) ) : ?>
				<span class="jvm_add_to_wishlist_text_remove"><?php echo esc_html( cixww_get_option( 'product_button_remove_text' ) ); ?></span>
				<?php endif; ?>

				<?php if ( cixww_get_option( 'product_button_already_wishlist_text' ) && ! cixww_get_option( 'remove_on_second_click' ) ) : ?>
				<span class="jvm_add_to_wishlist_text_already_in"><?php echo esc_html( cixww_get_option( 'product_button_already_wishlist_text' ) ); ?></span>
				<?php endif; ?>
				
			</a>
		<?php
		do_action( 'cix_woocommerce_wishlist_after_add_to_wishlist', $product_id );
	}
	/**
	 * Replaces the placeholders in the text with the actual values.
	 *
	 * @param array $param_list The list of placeholders and their corresponding values.
	 * @param int   $post_id    The ID of the post.
	 * @return array The list of placeholders and their corresponding values.
	 */
	public function replace_info( $param_list, $post_id ) {

		$param_list['{guest_session_in_days}'] = Helper::get_transient_expiration( 'cix_wc_wishlist_' . self::get_wishlist_temp_id() );
		$param_list['{product_name}']          = get_the_title( $post_id );
		$param_list['{view_cart_url}']                 = '<a class="ciww-cart-link" href="' . wc_get_cart_url() . '">' . __( 'View Cart', 'jvm-woocommerce-wishlist' ) . '</a>';

		return $param_list;
	}
	/**
	 * Returns the text for the 'Already in Wishlist' notice.
	 *
	 * @param int $product_id The ID of the product.
	 * @return string The text for the 'Already in Wishlist' notice.
	 */
	public static function already_in_wishlist_text( $product_id ) {

		return Helper::replace_text( cixww_get_option( 'product_already_in_wishlist_text' ), '{product_name}', $product_id );
	}
	/**
	 * Returns the text for the 'Added to Wishlist' notice.
	 *
	 * @param int $product_id The ID of the product.
	 * @return string The text for the 'Added to Wishlist' notice.
	 */
	public static function added_to_wishlist_text( $product_id ) {

		return Helper::replace_text( cixww_get_option( 'product_added_to_wishlist_text' ), '{product_name}', $product_id );
	}
	/**
	 * Returns the text for the 'Removed from Wishlist' notice.
	 *
	 * @param int $product_id The ID of the product.
	 * @return string The text for the 'Removed from Wishlist' notice.
	 */
	public static function removed_from_wishlist_text( $product_id ) {

		return Helper::replace_text( cixww_get_option( 'product_removed_from_wishlist_text' ), '{product_name}', $product_id );
	}
}
