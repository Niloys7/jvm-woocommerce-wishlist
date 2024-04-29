<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @version 2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
print_r( $args );
?>

<?php if ( isset( $args['already_in_wishlist'] ) && $args['already_in_wishlist'] == 1 ) : ?>
	<div class="modal-wishlist-icon"></div>
	<p class="modal-product-info"><?php echo esc_html( cixww_get_option( 'product_already_in_wishlist_text' ) ); ?></p>
<?php endif; ?>

<?php if ( isset( $args['added'] ) ) : ?>
	<div class="modal-wishlist-icon"></div>
	<p class="modal-product-info"><?php echo esc_html( cixww_get_option( 'product_added_to_wishlist_text' ) ); ?></p>
<?php endif; ?>

<?php if ( isset( $args['removed'] ) ) : ?>
	<div class="not modal-wishlist-icon"></div>
	<p class="modal-product-info"><?php echo esc_html( cixww_get_option( 'product_removed_from_wishlist_text' ) ); ?></p>
<?php endif; ?>

<?php if ( isset( $args['product_id'] ) ) : ?>
	<div class="modal-action-btns">
		<a href="<?php echo esc_url( get_the_permalink( cixww_get_option( 'wishlist_page' ) ) ); ?>" class="button modal-btn-view-wishlish"><?php echo esc_html( cixww_get_option( 'product_view_wishlist_text' ) ); ?></a>
	</div>
<?php endif; ?>
