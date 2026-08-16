<?php
/**
 * BrewCart Wishlist — stored in user meta for logged-in users,
 * cookie-backed for guests. AJAX add/remove with nonce protection.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BREWCART_WISHLIST_COOKIE', 'brewcart_wishlist' );
define( 'BREWCART_WISHLIST_META', '_brewcart_wishlist' );

function brewcart_get_wishlist_ids() {
	if ( is_user_logged_in() ) {
		$ids = get_user_meta( get_current_user_id(), BREWCART_WISHLIST_META, true );
		return is_array( $ids ) ? array_map( 'absint', $ids ) : array();
	}
	if ( empty( $_COOKIE[ BREWCART_WISHLIST_COOKIE ] ) ) {
		return array();
	}
	$raw = sanitize_text_field( wp_unslash( $_COOKIE[ BREWCART_WISHLIST_COOKIE ] ) );
	$ids = array_filter( array_map( 'absint', explode( ',', $raw ) ) );
	return array_values( array_unique( $ids ) );
}

function brewcart_save_wishlist_ids( $ids ) {
	$ids = array_values( array_unique( array_map( 'absint', $ids ) ) );
	if ( is_user_logged_in() ) {
		update_user_meta( get_current_user_id(), BREWCART_WISHLIST_META, $ids );
	} else {
		setcookie( BREWCART_WISHLIST_COOKIE, implode( ',', $ids ), time() + ( 90 * DAY_IN_SECONDS ), '/' );
		$_COOKIE[ BREWCART_WISHLIST_COOKIE ] = implode( ',', $ids );
	}
}

function brewcart_ajax_wishlist_toggle() {
	brewcart_verify_ajax_nonce();

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	if ( ! $product_id || ! get_post( $product_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid product.', 'brewcart' ) ), 400 );
	}

	$ids     = brewcart_get_wishlist_ids();
	$in_list = in_array( $product_id, $ids, true );

	if ( $in_list ) {
		$ids = array_diff( $ids, array( $product_id ) );
	} else {
		$ids[] = $product_id;
	}

	brewcart_save_wishlist_ids( $ids );

	wp_send_json_success(
		array(
			'active' => ! $in_list,
			'count'  => count( $ids ),
			'message' => $in_list ? __( 'Removed from wishlist', 'brewcart' ) : __( 'Added to wishlist', 'brewcart' ),
		)
	);
}
add_action( 'wp_ajax_brewcart_wishlist_toggle', 'brewcart_ajax_wishlist_toggle' );
add_action( 'wp_ajax_nopriv_brewcart_wishlist_toggle', 'brewcart_ajax_wishlist_toggle' );

/**
 * Merge guest wishlist cookie into user meta on login.
 */
function brewcart_merge_wishlist_on_login( $user_login, $user ) {
	if ( empty( $_COOKIE[ BREWCART_WISHLIST_COOKIE ] ) ) {
		return;
	}
	$raw       = sanitize_text_field( wp_unslash( $_COOKIE[ BREWCART_WISHLIST_COOKIE ] ) );
	$guest_ids = array_filter( array_map( 'absint', explode( ',', $raw ) ) );
	$user_ids  = get_user_meta( $user->ID, BREWCART_WISHLIST_META, true );
	$user_ids  = is_array( $user_ids ) ? $user_ids : array();
	update_user_meta( $user->ID, BREWCART_WISHLIST_META, array_values( array_unique( array_merge( $user_ids, $guest_ids ) ) ) );
	setcookie( BREWCART_WISHLIST_COOKIE, '', time() - 3600, '/' );
}
add_action( 'wp_login', 'brewcart_merge_wishlist_on_login', 10, 2 );

/**
 * Shortcode: [brewcart_wishlist] — renders the wishlist page grid.
 */
function brewcart_wishlist_shortcode() {
	$ids = brewcart_get_wishlist_ids();

	ob_start();

	if ( empty( $ids ) ) {
		?>
		<div class="wishlist-empty text-center" style="padding:60px 0;">
			<h3><?php esc_html_e( 'Your wishlist is empty', 'brewcart' ); ?></h3>
			<p><?php esc_html_e( 'Save the coffees you love and find them here anytime.', 'brewcart' ); ?></p>
			<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn btn-primary"><?php esc_html_e( 'Browse Coffee', 'brewcart' ); ?></a>
		</div>
		<?php
		return ob_get_clean();
	}

	$products = wc_get_products(
		array(
			'include' => $ids,
			'status'  => 'publish',
			'limit'   => -1,
		)
	);

	echo '<div class="grid grid-4 wishlist-grid">';
	foreach ( $products as $product ) {
		get_template_part( 'template-parts/product-card', null, array( 'product' => $product ) );
	}
	echo '</div>';

	return ob_get_clean();
}
add_shortcode( 'brewcart_wishlist', 'brewcart_wishlist_shortcode' );
