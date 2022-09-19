<?php
/**
 * Template to render the wishlit table.
 *
 
 * @category Core
 * @package JVMWooCommerceWishlist/Admin
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php
$product_ids = jvm_woocommerce_wishlist_get_wishlist_product_ids();

?>
<div class="jvm-woocommerce-wishlist-container">
<?php
if ( !empty($product_ids)) { ?>
	<table class="jvm-woocommerce-wishlist-table shop_table cart">
		<thead>
			<tr>
				<th class="product-remove">&nbsp;</th>
				<th class="product-thumbnail">&nbsp;</th>
				<th class="product-name"><?php esc_html_e( 'Product', 'jvm-woocommerce-wishlist' ); ?></th>
				<th class="product-price"><?php esc_html_e( 'Price', 'jvm-woocommerce-wishlist' ); ?></th>
				<th class="product-stock-status"></th>
			</tr>
		</thead>
		<tbody>
			<?php do_action( 'jvm_woocommerce_wishlist_before_wishlist_contents' ); ?>

			<?php

			foreach ( $product_ids as $product_id ) {

				$product = wc_get_product( $product_id );

				if ( $product && $product->exists() ) {
					$permalink = get_permalink( $product_id );
					?>
					<tr class="jvm-woocommerce-wishlist-product">

						<td class="product-remove">
							<a href="#"
							class="remove www-remove"
							title="<?php esc_html_e( 'Remove this item', 'jvm-woocommerce-wishlist' ); ?>"
							data-product-title="<?php echo esc_attr( get_the_title( $product_id ) ); ?>"
							data-product-id="<?php echo absint( $product_id ); ?>">
								&times;
							</a>
						</td>

						<td class="product-thumbnail">
							<a href="<?php echo esc_url( $permalink ); ?>">
								<?php echo $product->get_image(); ?>
							</a>
						</td>

						<td class="product-name" data-title="<?php esc_html_e( 'Product', 'jvm-woocommerce-wishlist' ); ?>">
							<a href="<?php echo esc_url( $permalink ); ?>">
								<?php echo get_the_title( $product_id ); ?>
							</a>
						</td>

						<td class="product-price" data-title="<?php esc_html_e( 'Price', 'jvm-woocommerce-wishlist' ); ?>">
							<a href="<?php echo esc_url( $permalink ); ?>">
								<?php
								if ( $product->get_price() != '0' ) {
									echo wp_kses_post( $product->get_price_html() );
								}
								?>
							</a>
						</td>
						<td class="product-stock-status">
						<?php
							$availability = $product->get_availability();
							$stock_status = $availability['class'];

							if( $stock_status == 'out-of-stock' ) {
								$stock_status = 'Out';
								echo '<span class="wishlist-out-of-stock">' . esc_html__( 'Out of Stock', 'jvm-woocommerce-wishlist' ) . '</span>';
							} else {
								$stock_status = 'In';
								echo '<span class="wishlist-in-stock">' . esc_html__( 'In Stock', 'jvm-woocommerce-wishlist' ) . '</span>';
							}
						?>
						</td>
					</tr>
					<?php
				}
			}

			do_action( 'jvm_woocommerce_wishlist_after_wishlist_contents' ); ?>
		</tbody>
	</table>
<?php 
	} 
	$class = empty($product_ids) ? '' : ' hidden';
?>
	<div class="empty-wishlist<?php echo $class;?>">
		<p><?php esc_html_e( 'No products on your wishlist yet.', 'jvm-woocommerce-wishlist' ); ?></p>

		<p class="return-to-shop">
			<a class="button wc-backward" href="<?php echo get_permalink( get_option( 'woocommerce_shop_page_id' ) );?>"><?php _e('Return to shop', 'jvm-woocommerce-wishlist');?></a>
		</p>
	</div>
</div>