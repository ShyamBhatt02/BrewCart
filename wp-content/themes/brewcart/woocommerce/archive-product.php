<?php
/**
 * Shop archive — product grid with sidebar filters.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$current_cat   = isset( $_GET['product_cat'] ) ? sanitize_title( wp_unslash( $_GET['product_cat'] ) ) : '';
$current_roast = isset( $_GET['roast'] ) ? sanitize_title( wp_unslash( $_GET['roast'] ) ) : '';
$min_price     = isset( $_GET['min_price'] ) ? floatval( $_GET['min_price'] ) : '';
$max_price     = isset( $_GET['max_price'] ) ? floatval( $_GET['max_price'] ) : '';
$in_stock_only = isset( $_GET['in_stock'] ) && '1' === $_GET['in_stock'];
$min_rating    = isset( $_GET['min_rating'] ) ? absint( $_GET['min_rating'] ) : 0;
$orderby       = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'menu_order';
?>

<main class="site-main">
	<div class="container" style="padding:48px 0;">
		<div class="section-head reveal" style="text-align:left;max-width:none;">
			<span class="eyebrow"><?php esc_html_e( 'Our Coffee', 'brewcart' ); ?></span>
			<h1><?php woocommerce_page_title(); ?></h1>
		</div>

		<div class="shop-layout" style="display:grid;grid-template-columns:260px 1fr;gap:32px;align-items:start;">
			<aside class="shop-filters card reveal" style="padding:24px;position:sticky;top:100px;">
				<form method="get" action="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
					<h3 style="font-size:1.1rem;"><?php esc_html_e( 'Filter', 'brewcart' ); ?></h3>

					<div class="filter-group" style="margin-bottom:20px;">
						<h4 style="font-size:.85rem;text-transform:uppercase;color:var(--charcoal-soft);margin-bottom:10px;"><?php esc_html_e( 'Category', 'brewcart' ); ?></h4>
						<?php
						$cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true ) );
						if ( ! is_wp_error( $cats ) ) {
							foreach ( $cats as $cat ) {
								if ( 'uncategorized' === $cat->slug ) {
									continue;
								}
								printf(
									'<label style="display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:.9rem;"><input type="radio" name="product_cat" value="%1$s" %2$s> %3$s (%4$d)</label>',
									esc_attr( $cat->slug ),
									checked( $current_cat, $cat->slug, false ),
									esc_html( $cat->name ),
									(int) $cat->count
								);
							}
						}
						?>
					</div>

					<div class="filter-group" style="margin-bottom:20px;">
						<h4 style="font-size:.85rem;text-transform:uppercase;color:var(--charcoal-soft);margin-bottom:10px;"><?php esc_html_e( 'Roast Level', 'brewcart' ); ?></h4>
						<?php
						$roasts = get_terms( array( 'taxonomy' => 'pa_roast-level', 'hide_empty' => true ) );
						if ( ! is_wp_error( $roasts ) ) {
							foreach ( $roasts as $roast ) {
								printf(
									'<label style="display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:.9rem;"><input type="radio" name="roast" value="%1$s" %2$s> %3$s</label>',
									esc_attr( $roast->slug ),
									checked( $current_roast, $roast->slug, false ),
									esc_html( $roast->name )
								);
							}
						}
						?>
					</div>

					<div class="filter-group" style="margin-bottom:20px;">
						<h4 style="font-size:.85rem;text-transform:uppercase;color:var(--charcoal-soft);margin-bottom:10px;"><?php esc_html_e( 'Price', 'brewcart' ); ?></h4>
						<div style="display:flex;gap:8px;">
							<input type="number" name="min_price" placeholder="<?php esc_attr_e( 'Min', 'brewcart' ); ?>" value="<?php echo esc_attr( $min_price ); ?>" style="width:50%;padding:8px;border-radius:6px;border:1px solid rgba(43,27,20,.15);">
							<input type="number" name="max_price" placeholder="<?php esc_attr_e( 'Max', 'brewcart' ); ?>" value="<?php echo esc_attr( $max_price ); ?>" style="width:50%;padding:8px;border-radius:6px;border:1px solid rgba(43,27,20,.15);">
						</div>
					</div>

					<div class="filter-group" style="margin-bottom:20px;">
						<h4 style="font-size:.85rem;text-transform:uppercase;color:var(--charcoal-soft);margin-bottom:10px;"><?php esc_html_e( 'Rating', 'brewcart' ); ?></h4>
						<?php for ( $r = 4; $r >= 1; $r-- ) : ?>
							<label style="display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:.9rem;">
								<input type="radio" name="min_rating" value="<?php echo esc_attr( $r ); ?>" <?php checked( $min_rating, $r ); ?>>
								<?php echo str_repeat( '&#9733;', $r ) . str_repeat( '&#9734;', 5 - $r ); ?> <?php esc_html_e( '& up', 'brewcart' ); ?>
							</label>
						<?php endfor; ?>
					</div>

					<div class="filter-group" style="margin-bottom:20px;">
						<label style="display:flex;align-items:center;gap:8px;font-size:.9rem;">
							<input type="checkbox" name="in_stock" value="1" <?php checked( $in_stock_only ); ?>>
							<?php esc_html_e( 'In stock only', 'brewcart' ); ?>
						</label>
					</div>

					<button type="submit" class="btn btn-primary" style="width:100%;"><?php esc_html_e( 'Apply Filters', 'brewcart' ); ?></button>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn btn-outline" style="width:100%;margin-top:10px;"><?php esc_html_e( 'Clear', 'brewcart' ); ?></a>
				</form>
			</aside>

			<div class="shop-results">
				<div class="shop-toolbar" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
					<span style="color:var(--charcoal-soft);font-size:.9rem;"><?php woocommerce_result_count(); ?></span>
					<form method="get" class="orderby-form">
						<?php foreach ( $_GET as $k => $v ) : if ( 'orderby' === $k ) { continue; } ?>
							<input type="hidden" name="<?php echo esc_attr( $k ); ?>" value="<?php echo esc_attr( is_array( $v ) ? '' : $v ); ?>">
						<?php endforeach; ?>
						<select name="orderby" onchange="this.form.submit()" style="padding:10px 14px;border-radius:8px;border:1px solid rgba(43,27,20,.15);">
							<option value="menu_order" <?php selected( $orderby, 'menu_order' ); ?>><?php esc_html_e( 'Default sorting', 'brewcart' ); ?></option>
							<option value="price" <?php selected( $orderby, 'price' ); ?>><?php esc_html_e( 'Price: Low to High', 'brewcart' ); ?></option>
							<option value="price-desc" <?php selected( $orderby, 'price-desc' ); ?>><?php esc_html_e( 'Price: High to Low', 'brewcart' ); ?></option>
							<option value="rating" <?php selected( $orderby, 'rating' ); ?>><?php esc_html_e( 'Top Rated', 'brewcart' ); ?></option>
							<option value="date" <?php selected( $orderby, 'date' ); ?>><?php esc_html_e( 'Newest', 'brewcart' ); ?></option>
						</select>
					</form>
				</div>

				<?php
				$args = array(
					'status'   => 'publish',
					'limit'    => 12,
					'page'     => max( 1, get_query_var( 'paged' ) ),
					'paginate' => true,
				);

				if ( $current_cat ) {
					$args['category'] = array( $current_cat );
				}
				if ( $current_roast ) {
					$args['tax_query'] = array(
						array(
							'taxonomy' => 'pa_roast-level',
							'field'    => 'slug',
							'terms'    => $current_roast,
						),
					);
				}
				if ( '' !== $min_price ) {
					$args['min_price'] = $min_price;
				}
				if ( '' !== $max_price ) {
					$args['max_price'] = $max_price;
				}
				if ( $in_stock_only ) {
					$args['stock_status'] = 'instock';
				}
				switch ( $orderby ) {
					case 'price':
						$args['orderby'] = 'price';
						$args['order']   = 'ASC';
						break;
					case 'price-desc':
						$args['orderby'] = 'price';
						$args['order']   = 'DESC';
						break;
					case 'rating':
						$args['orderby'] = 'rating';
						break;
					case 'date':
						$args['orderby'] = 'date';
						$args['order']   = 'DESC';
						break;
				}

				$results  = wc_get_products( $args );
				$products = is_object( $results ) ? $results->products : $results;

				if ( $min_rating ) {
					$products = array_filter(
						$products,
						function ( $p ) use ( $min_rating ) {
							return $p->get_average_rating() >= $min_rating;
						}
					);
				}

				if ( empty( $products ) ) :
					?>
					<div class="card text-center" style="padding:60px 24px;">
						<h3><?php esc_html_e( 'No coffee matches those filters', 'brewcart' ); ?></h3>
						<p><?php esc_html_e( 'Try widening your search — or browse everything we roast.', 'brewcart' ); ?></p>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn btn-primary"><?php esc_html_e( 'View All Coffee', 'brewcart' ); ?></a>
					</div>
					<?php
				else :
					?>
					<div class="grid grid-3">
						<?php
						foreach ( $products as $product ) {
							get_template_part( 'template-parts/product-card', null, array( 'product' => $product ) );
						}
						?>
					</div>
					<?php
					if ( is_object( $results ) && $results->max_num_pages > 1 ) {
						echo '<nav class="woocommerce-pagination" style="margin-top:32px;text-align:center;">';
						echo paginate_links(
							array(
								'total'   => $results->max_num_pages,
								'current' => max( 1, get_query_var( 'paged' ) ),
							)
						);
						echo '</nav>';
					}
				endif;
				?>
			</div>
		</div>
	</div>
</main>

<?php
get_footer();
