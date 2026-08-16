<?php
/**
 * Product card template part.
 *
 * @var array $args { @type WC_Product $product }
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$product = $args['product'] ?? null;
if ( ! $product instanceof WC_Product ) {
	return;
}

$wishlist_ids = function_exists( 'brewcart_get_wishlist_ids' ) ? brewcart_get_wishlist_ids() : array();
$is_wishlisted = in_array( $product->get_id(), $wishlist_ids, true );
?>
<div class="card product-card reveal">
	<div class="thumb">
		<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
			<?php echo $product->get_image( 'brewcart-card' ); ?>
		</a>
		<?php if ( $product->is_on_sale() ) : ?>
			<span class="sale-badge"><?php esc_html_e( 'Sale', 'brewcart' ); ?></span>
		<?php endif; ?>
		<button class="wishlist-btn <?php echo $is_wishlisted ? 'active' : ''; ?>" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>" aria-label="<?php esc_attr_e( 'Toggle wishlist', 'brewcart' ); ?>">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78Z"/></svg>
		</button>
		<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="quick-view"><?php esc_html_e( 'Quick View', 'brewcart' ); ?></a>
	</div>
	<div class="info">
		<?php
		$cats = wc_get_product_category_list( $product->get_id() );
		if ( $cats ) :
			?>
			<div class="cat"><?php echo wp_strip_all_tags( $cats ); ?></div>
		<?php endif; ?>
		<h3><a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
		<div class="rating">
			<?php
			$rating = $product->get_average_rating();
			for ( $i = 1; $i <= 5; $i++ ) {
				echo $i <= round( $rating ) ? '&#9733;' : '&#9734;';
			}
			?>
		</div>
		<div class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
		<?php
		if ( ! $product->is_in_stock() ) {
			echo '<span class="badge-outofstock">' . esc_html__( 'Out of stock', 'brewcart' ) . '</span>';
		} else {
			echo do_shortcode( '[add_to_cart id="' . $product->get_id() . '" show_price="false" class="btn btn-primary add-cart"]' );
		}
		?>
	</div>
</div>
