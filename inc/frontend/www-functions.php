<?php
/**
 * JVM WooCommerce Wishlist frontend functions
 *
 * General functions available on frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function jvm_woocommerce_wishlist_locate_template( $path, $params = null ) {
	$located = locate_template(array('jvm-woocommerce-wishlist' . DIRECTORY_SEPARATOR . $path));
	$plugin_path = JVM_WW_DIR . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $path;

	if (!$located && file_exists($plugin_path)) {
		$final_file = $plugin_path;
	}elseif ($located) {
		$final_file = $located;
	}
	if ($params) {
		set_query_var( 'params', $params );
	}

	include( $final_file );
}

/**the
 * Output wishlist table
 */
function jvm_woocommerce_wishlist() {

	do_action( 'jvm_woocommerce_wishlist_before_wishlist' );

	jvm_woocommerce_wishlist_locate_template('wishlist.php');

	do_action( 'jvm_woocommerce_wishlist_after_wishlist' );
}

/**
 * Enqeue styles and scripts
 *
 * @since 1.0.0
 *
 * @param int $product_id
 */
function jvm_woocommerce_add_to_wishlist( $product_id = null ) {
	$wishlist = jvm_woocommerce_wishlist_get_wishlist_product_ids();
	$product_id = empty( $product_id ) ? get_the_ID() : $product_id;
	$is_in_wishlist = ( $wishlist ) ? ( in_array( $product_id, $wishlist ) ) : false;
	$class = ( $is_in_wishlist ) ? 'in_wishlist ' : '';
	$text = ( $is_in_wishlist ) ? esc_html__( 'Remove from wishlist', 'jvm-woocommerce-wishlist' ) : esc_html__( 'Add to wishlist', 'jvm-woocommerce-wishlist' );
	
	// Hook for icon HTML
	$icon_html = apply_filters('jvm_add_to_wishlist_icon_html', '<span class="jvm_add_to_wishlist_heart"></span>');

	do_action( 'jvm_woocommerce_wishlist_before_add_to_wishlist', $product_id);

	$class .= apply_filters( 'jvm_add_to_wishlist_class', ' jvm_add_to_wishlist button' );
?><a class="<?php echo esc_attr( $class ); ?>" href="?add_to_wishlist=<?php echo $product_id; ?>" title="<?php echo esc_attr( $text ); ?>" rel="nofollow" data-product-title="<?php echo esc_attr( get_the_title($product_id) ); ?>" data-product-id="<?php echo $product_id; ?>">
	<?php echo $icon_html;?>	
	<span class="jvm_add_to_wishlist_text_add"><?php _e( 'Add to wishlist', 'jvm-woocommerce-wishlist' );?></span>
	<span class="jvm_add_to_wishlist_text_remove"><?php _e( 'Remove from wishlist', 'jvm-woocommerce-wishlist' );?></span>
</a><?php 
	do_action( 'jvm_woocommerce_wishlist_after_add_to_wishlist', $product_id);
}

/**
 * Hook woocommerce_template_loop_add_to_cart to output our button
 */
add_action( 'woocommerce_after_shop_loop_item', 'jvm_woocommerce_add_to_wishlist', 15 );
add_action( 'woocommerce_after_add_to_cart_button', 'jvm_woocommerce_add_to_wishlist' );

/**
 * Clean up product ids
 *
 * Remove ids of product that don't exist
 *
 * @param array $product_ids
 * @return array $product_ids
 */
function jvm_woocommerce_wishlist_clean_wishlist_product_ids( $product_ids = array() ) {

	$clean_product_ids = array();

	foreach ( $product_ids as $product_id ) {

		if ( 'publish' == get_post_status ( $product_id ) ) {
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
 * @since 1.0.0
 */
function jvm_woocommerce_wishlist_get_wishlist_product_ids() {

	$product_ids = array();

	$cookie_name = jvm_woocommerce_wishlist_get_site_slug() . '_wc_wishlist';
	$cookie = ( isset( $_COOKIE[ $cookie_name ] ) ) ? $_COOKIE[ $cookie_name ] : null;

	$user_id = get_current_user_id();
	$user_meta = get_user_meta( $user_id, $cookie_name, true );

	// If we can get the user meta we use it as starting point, always
	if ( $user_meta ) {

		$product_ids = jvm_woocommerce_wishlist_clean_wishlist_product_ids( $user_meta );
		
	// if the user is not logged in, we use the cookie value
	} elseif ( $cookie ) {
		$product_ids = array_unique( json_decode( '[' . $cookie . ']' ) );
	}

	$product_ids = jvm_woocommerce_wishlist_clean_wishlist_product_ids( $product_ids ); // cleaned up

	return apply_filters( 'jvm_woocommerce_wishlist_product_ids', $product_ids );
}

/**
 * Get number of items on the wishlist
 *
 *
 * @since 1.0.0
 */
function jvm_woocommerce_wishlist_get_count() {
	$ids = jvm_woocommerce_wishlist_get_wishlist_product_ids();

	return count($ids);
}

/**
 * Remove all double spaces
 *
 * This function is mainly used to clean up inline CSS
 *
 * @param string $css
 * @return string
 */
function jvm_woocommerce_wishlist_clean_spaces( $string, $hard = true ) {
	return preg_replace( '/\s+/', ' ', $string );
}

/**
 * Convert list of IDs to array
 *
 * @since 1.0.0
 * @param string $list
 * @return array
 */
function jvm_woocommerce_wishlist_list_to_array( $list, $separator = ',' ) {
	return ( $list ) ? explode( ',', trim( jvm_woocommerce_wishlist_clean_spaces( jvm_woocommerce_wishlist_clean_list( $list ) ) ) ) : array();
}

/**
 * Convert array of ids to list
 *
 * @since 1.0.0
 * @param string $list
 * @return array
 */
function jvm_woocommerce_wishlist_array_to_list( $array ) {
	$list = '';

	if ( is_array( $array ) ) {
		$list = rtrim( implode( ',',  $array ), ',' );
	}

	return jvm_woocommerce_wishlist_clean_list( $list );
}

/**
 * Clean a list
 *
 * Remove first and last comma of a list and remove spaces before and after separator
 *
 * @param string $list
 * @return string $list
 */
function jvm_woocommerce_wishlist_clean_list( $list, $separator = ',' ) {
	$list = str_replace( array( $separator . ' ', ' ' . $separator ), $separator, $list );
	$list = ltrim( $list, $separator );
	$list = rtrim( $list, $separator );
	return $list;
}

/**
 * Special body class
 */
add_filter( 'body_class', function($classes) {
	if ( is_page( jvm_woocommerce_wishlist_get_page_id() ) ) {
		$classes[] =  'jvm-woocommerce-wishlist-page';
	}

	return $classes;
});

/**
 * Enqeue styles and scripts
 *
 * @since 1.0.0
 */
function jvm_woocommerce_wishlist_enqueue_scripts() {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );

	// Styles
	wp_enqueue_style( 'jvm-woocommerce-wishlist', JVM_WW_CSS . '/wishlist.css', array(), JVM_WW_VERSION, 'all' );

	// Scripts
	wp_enqueue_script( 'js-cookie', JVM_WW_JS . '/lib/js-cookie' . $suffix . '.js', array(), '2.1.4', true ); // should be already enqueued by WooCommerce
	wp_enqueue_script( 'jvm-woocommerce-wishlist', JVM_WW_JS . '/wishlist.js', array( 'jquery' ), JVM_WW_VERSION, true );

	// Add JS global variables
	wp_localize_script(
		'jvm-woocommerce-wishlist', 'JVMWooCommerceWishlistJSParams', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'siteUrl' => site_url( '/' ),
			'siteSlug' => jvm_woocommerce_wishlist_get_site_slug(),
			'userId' => get_current_user_id(),
			'language' => get_locale(),
			'l10n' => array(
				'addToWishlist' => esc_html__( 'Add to wishlist', 'jvm-woocommerce-wishlist' ),
				'removeFromWishlist' => esc_html__( 'Remove from wishlist', 'jvm-woocommerce-wishlist' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts',  'jvm_woocommerce_wishlist_enqueue_scripts', 20 );