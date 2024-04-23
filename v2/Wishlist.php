<?php
namespace CIXW_WISHLIST;

class Wishlist {
	public function __construct() {
		add_shortcode( 'cix_woocommerce_wishlist', array( $this, 'wishlist_shortcode' ) );
		add_shortcode( 'cix_add_to_wishlist', array( $this, 'add_to_wishlist_shortcode' ) );
		$this->display_loop_wishlist_button();
		$this->display_single_product_wishlist_button();
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
	 * Locates and includes a template file for the WooCommerce Wishlist plugin.
	 *
	 * @param string $path The path of the template file.
	 * @param mixed  $params Optional parameters to be passed to the template.
	 * @return void
	 */
	public static function woocommerce_wishlist_locate_template( $path, $params = null ) {
		$located     = locate_template( array( 'wishlist' . DIRECTORY_SEPARATOR . $path ) );
		$plugin_path = CIXWW_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $path;

		if ( ! $located && file_exists( $plugin_path ) ) {
			$final_file = $plugin_path;
		} elseif ( $located ) {
			$final_file = $located;
		}
		if ( $params ) {
			set_query_var( 'params', $params );
		}

		include $final_file;
	}
	/**
	 * Get site name slug
	 *
	 * @return string
	 */
	public static function wishlist_get_site_slug() {
		return str_replace( '-', '_', sanitize_title_with_dashes( get_bloginfo( 'name' ) ) );
	}
	public static function set_transient( $product_id ) {
		$wishlist   = self::wishlist_product_ids();
		$wishlist[] = $product_id;
		$wishlist   = array_unique( $wishlist );

		$expiration = DAY_IN_SECONDS * 7; // 30 days
		set_transient( self::wishlist_get_site_slug() . '_wc_wishlist_' . self::temp_cookie(), $wishlist, $expiration );
	}
	/**
	 * Get a PHP array of products in the wishlist
	 */
	public static function wishlist_product_ids( $product_ids = array(), $wishlist_id = null ) {

		$clean_product_ids = ( get_transient( self::wishlist_get_site_slug() . '_wc_wishlist_' . self::temp_cookie() ) ) ? get_transient( self::wishlist_get_site_slug() . '_wc_wishlist_' . self::temp_cookie() ) : array();

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
		$cookie_name = self::wishlist_get_site_slug() . '_wc_wishlist_temp';
		$cookie      = ( isset( $_COOKIE[ $cookie_name ] ) ) ? $_COOKIE[ $cookie_name ] : null;

		if ( ! $cookie ) {
			$temp_id = wp_generate_password( 8, false );

			setcookie( $cookie_name, $temp_id, strtotime( '+7 day', time() ), '/' );
		}
		return $cookie;
	}
	public static function remove_product( $product_id, $wishlist_id = null ) {
		if ( $product_id ) {
			$wishlist = self::wishlist_product_ids();

			$wishlist   = array_diff( $wishlist, array( $product_id ) );
			$wishlist   = array_unique( $wishlist );
			$expiration = DAY_IN_SECONDS * 7; // 30 days
			set_transient( self::wishlist_get_site_slug() . '_wc_wishlist_' . self::temp_cookie(), $wishlist, $expiration );

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

		if ( $is_in_wishlist && ! $show_icon ) {
			do_action( 'cix_woocommerce_wishlist_after_add_to_wishlist', $product_id );
			return;
		}

		do_action( 'cix_woocommerce_wishlist_before_add_to_wishlist', $product_id );
		$button_class = ( cixww_get_option( 'product_button_type' ) == 'button' ) ? 'button' : 'btn-link';
		$class       .= apply_filters( 'cix_add_to_wishlist_class', ' jvm_add_to_wishlist ' . $button_class );
		?>
			<a class="<?php echo esc_attr( $class ); ?>" href="?add_to_wishlist=<?php echo $product_id; ?>" title="<?php echo esc_attr( $text ); ?>" rel="nofollow" data-product-title="<?php echo esc_attr( get_the_title( $product_id ) ); ?>" data-product-id="<?php echo $product_id; ?>" <?php echo ( cixww_get_option( 'remove_on_second_click' ) && in_array( $product_id, $wishlist ) ) ? 'data-remove=' . $product_id : ''; ?>>
					<?php echo $icon_html; ?>
				<span class="jvm_add_to_wishlist_text_add"><?php echo esc_html( cixww_get_option( 'product_button_text' ) ); ?></span>
				<?php if ( cixww_get_option( 'remove_on_second_click' ) ) : ?>
					
				<span class="jvm_add_to_wishlist_text_remove"><?php echo esc_html( cixww_get_option( 'product_button_remove_text' ) ); ?></span>
				<?php endif; ?>
				
			</a>
		<?php
		do_action( 'cix_woocommerce_wishlist_after_add_to_wishlist', $product_id );
	}
}
