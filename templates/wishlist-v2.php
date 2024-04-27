<?php
/**
 * Template to render the wishlit table.
 *
 * @version 2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php
$product_ids = \CIXW_WISHLIST\Wishlist::wishlist_product_ids();

?>
<?php echo ( cixww_get_option( 'wishlist_name' ) ) ? '<h2>' . esc_html( cixww_get_option( 'wishlist_name' ) ) . '</h2>' : ''; ?>
<div class="jvm-woocommerce-wishlist-container woocommerce-cart-form">
<?php
if ( ! empty( $product_ids ) ) {
	?>
	<table class="jvm-woocommerce-wishlist-table shop_table shop_table_responsive cart woocommerce-cart-form__contents">
		<thead>
			<tr>
				<th class="product-remove">&nbsp;</th>
				<th class="product-thumbnail">&nbsp;</th>
				<th class="product-name"><?php esc_html_e( 'Product', 'jvm-woocommerce-wishlist' ); ?></th>

				<?php if ( cixww_get_option( 'wishlist_page_table_unit_price' ) ) : ?>
				<th class="product-price"><?php esc_html_e( 'Price', 'jvm-woocommerce-wishlist' ); ?></th>
				<?php endif; ?>

				<?php if ( cixww_get_option( 'wishlist_page_table_stock_status' ) ) : ?>
				<th class="product-stock-status"><?php esc_html_e( 'Stock Status', 'jvm-woocommerce-wishlist' ); ?></th>
				<?php endif; ?>

				<th class="product-add-to-cart"></th>
			</tr>
		</thead>
		<tbody>
			<?php
			do_action( 'cix_woocommerce_wishlist_before_wishlist_contents' );

			?>

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
						<?php if ( cixww_get_option( 'wishlist_page_table_unit_price' ) ) : ?>
							<td class="product-price" data-title="<?php esc_html_e( 'Price', 'jvm-woocommerce-wishlist' ); ?>">
								
									<?php
									if ( $product->get_price() != '0' ) {
										echo wp_kses_post( $product->get_price_html() );
									}
									?>
								
							</td>
						<?php endif; ?>
						<?php if ( cixww_get_option( 'wishlist_page_table_stock_status' ) ) : ?>
						<td class="product-stock-status">
							<?php
							$availability = $product->get_availability();
							$stock_status = $availability['class'];

							if ( $stock_status == 'out-of-stock' ) {
								$stock_status = 'Out';
								echo '<span class="wishlist-out-of-stock">' . esc_html__( 'Out of Stock', 'jvm-woocommerce-wishlist' ) . '</span>';
							} else {
								$stock_status = 'In';
								echo '<span class="wishlist-in-stock">' . esc_html__( 'In Stock', 'jvm-woocommerce-wishlist' ) . '</span>';
							}
							?>
						</td>
						<?php endif; ?>
						
						<td class="product-add-to-cart">
							<button class="button cixww-button" name="cixww-add-to-cart" value="<?php echo absint( $product_id ); ?>" title="Add to Cart"><?php echo esc_html( cixww_get_option( 'wishlist_page_table_add_to_cart_text' ) ); ?></button>
					</tr>
					<?php
				}
			}

			do_action( 'cix_woocommerce_wishlist_after_wishlist_contents' );

			?>
		</tbody>
		<tfoot>
			
			<?php if ( cixww_get_option( 'table_add_all_to_cart' ) ) : ?>
			<tr>
				<td colspan="6">
					
					<a href="#" class="button cixww-wishlist-cart"><?php echo esc_html( cixww_get_option( 'table_add_all_to_cart_text' ) ); ?></a>
					
				</td>
			</tr>
			<?php endif; ?>
		</tfoot>
	</table>
	<?php

}
	$class = empty( $product_ids ) ? '' : ' hidden';

?>
	<div class="empty-wishlist<?php echo $class; ?>">
		<p><?php echo esc_html( cixww_get_option( 'wishlist_page_no_item_text' ) ); ?></p>

		<p class="return-to-shop">
			<a class="button wc-backward" href="<?php echo get_permalink( get_option( 'woocommerce_shop_page_id' ) ); ?>"><?php _e( 'Return to shop', 'jvm-woocommerce-wishlist' ); ?></a>
		</p>
	</div>
</div>