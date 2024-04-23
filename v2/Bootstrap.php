<?php
namespace CIXW_WISHLIST;

class Bootstrap {

	protected $wishlist_slug;

	public function __construct() {
		new Wishlist();
		new Settings();
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
	public static function wishlist_product_ids( $product_ids = array() ) {

		$clean_product_ids = array();

		foreach ( $product_ids as $product_id ) {

			if ( 'publish' == get_post_status( $product_id ) ) {
				$clean_product_ids[] = $product_id;
			}
		}

		return $clean_product_ids;
	}
	/**
	 * Get a PHP array of products in the wishlist
	 *
	 * Retrieve from user data if user is logged in
	 *
	 * @since 2.0
	 */
	public static function woocommerce_wishlist_get_wishlist_product_ids() {

		$product_ids = array();
		// add wishlist slug to cookie name

		$cookie_name = self::wishlist_get_site_slug() . '_wc_wishlist';
		$cookie      = ( isset( $_COOKIE[ $cookie_name ] ) ) ? $_COOKIE[ $cookie_name ] : null;

		$user_id   = get_current_user_id();
		$user_meta = get_user_meta( $user_id, $cookie_name, true );

		// If we can get the user meta we use it as starting point, always
		if ( $user_meta ) {

			$product_ids = self::wishlist_product_ids( $user_meta );

			// if the user is not logged in, we use the cookie value
		} elseif ( $cookie ) {
			$product_ids = array_unique( json_decode( '[' . $cookie . ']' ) );
		}

		$product_ids = self::wishlist_product_ids( $product_ids ); // cleaned up

		return apply_filters( 'cix_woocommerce_wishlist_product_ids', $product_ids );
	}
	/**
	 * Enqeue styles and scripts
	 *
	 * @since 1.0.0
	 *
	 * @param int $product_id
	 */
	public static function woocommerce_add_to_wishlist( $product_id = null ) {
		$wishlist       = self::woocommerce_wishlist_get_wishlist_product_ids();
		$product_id     = empty( $product_id ) ? get_the_ID() : $product_id;
		$is_in_wishlist = ( $wishlist ) ? ( in_array( $product_id, $wishlist ) ) : false;
		$class          = ( $is_in_wishlist ) ? 'in_wishlist ' : '';
		$text           = ( $is_in_wishlist ) ? esc_html__( 'Remove from wishlist', 'jvm-woocommerce-wishlist' ) : esc_html__( 'Add to wishlist', 'jvm-woocommerce-wishlist' );

		// Hook for icon HTML
		$icon_html = apply_filters( 'cix_add_to_wishlist_icon_html', '<span class="jvm_add_to_wishlist_heart"></span>' );

		do_action( 'cix_woocommerce_wishlist_before_add_to_wishlist', $product_id );

		$class .= apply_filters( 'jvm_add_to_wishlist_class', ' jvm_add_to_wishlist button' );
		?>
			<a class="<?php echo esc_attr( $class ); ?>" href="?add_to_wishlist=<?php echo $product_id; ?>" title="<?php echo esc_attr( $text ); ?>" rel="nofollow" data-product-title="<?php echo esc_attr( get_the_title( $product_id ) ); ?>" data-product-id="<?php echo $product_id; ?>">
					<?php echo $icon_html; ?>
				<span class="jvm_add_to_wishlist_text_add"><?php _e( 'Add to wishlist', 'jvm-woocommerce-wishlist' ); ?></span>
				<span class="jvm_add_to_wishlist_text_remove"><?php _e( 'Remove from wishlist', 'jvm-woocommerce-wishlist' ); ?></span>
			</a>
		<?php
		do_action( 'cix_woocommerce_wishlist_after_add_to_wishlist', $product_id );
	}
}
