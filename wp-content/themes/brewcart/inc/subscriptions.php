<?php
/**
 * Coffee Subscription selector — product, quantity, delivery frequency.
 * Adds the chosen coffee to the cart with subscription meta attached so pricing
 * and schedule are visible in cart/checkout/order. Architected so a recurring
 * payments gateway (e.g. WooCommerce Subscriptions) can be plugged in later —
 * the frequency/discount logic here is gateway-agnostic.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BREWCART_SUB_DISCOUNT', 0.10 );

function brewcart_subscriptions_shortcode() {
	$products = wc_get_products( array( 'status' => 'publish', 'limit' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );

	ob_start();
	?>
	<div class="card reveal" style="max-width:720px;margin:0 auto;padding:36px;">
		<form id="brewcart-subscription-form">
			<?php wp_nonce_field( 'brewcart_nonce', 'sub_nonce' ); ?>

			<div style="margin-bottom:20px;">
				<label style="display:block;font-weight:600;font-size:.85rem;margin-bottom:8px;"><?php esc_html_e( 'Choose your coffee', 'brewcart' ); ?></label>
				<select name="product_id" id="sub-product" style="width:100%;padding:12px;border-radius:8px;border:1px solid rgba(43,27,20,.15);">
					<?php foreach ( $products as $product ) : ?>
						<option value="<?php echo esc_attr( $product->get_id() ); ?>" data-price="<?php echo esc_attr( $product->get_price() ); ?>">
							<?php echo esc_html( $product->get_name() ); ?> — <?php echo wp_kses_post( wc_price( $product->get_price() ) ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<div style="margin-bottom:20px;">
				<label style="display:block;font-weight:600;font-size:.85rem;margin-bottom:8px;"><?php esc_html_e( 'Quantity', 'brewcart' ); ?></label>
				<input type="number" id="sub-quantity" name="quantity" value="1" min="1" max="10" style="width:120px;padding:12px;border-radius:8px;border:1px solid rgba(43,27,20,.15);">
			</div>

			<div style="margin-bottom:24px;">
				<label style="display:block;font-weight:600;font-size:.85rem;margin-bottom:8px;"><?php esc_html_e( 'Delivery Frequency', 'brewcart' ); ?></label>
				<div class="option-pills" id="sub-frequency" data-field="frequency">
					<button type="button" class="pill" data-value="weekly"><?php esc_html_e( 'Weekly', 'brewcart' ); ?></button>
					<button type="button" class="pill active" data-value="biweekly"><?php esc_html_e( 'Every 2 Weeks', 'brewcart' ); ?></button>
					<button type="button" class="pill" data-value="monthly"><?php esc_html_e( 'Monthly', 'brewcart' ); ?></button>
				</div>
				<input type="hidden" name="frequency" id="sub-frequency-value" value="biweekly">
			</div>

			<div class="customize-summary" style="margin-bottom:24px;">
				<p style="margin:0 0 6px;"><strong><?php esc_html_e( 'Subscription price:', 'brewcart' ); ?></strong> <span id="sub-price">—</span> <span style="color:var(--success);font-weight:600;">(<?php echo esc_html( BREWCART_SUB_DISCOUNT * 100 ); ?>% off)</span></p>
				<p style="margin:0 0 6px;"><strong><?php esc_html_e( 'Delivery schedule:', 'brewcart' ); ?></strong> <span id="sub-schedule">Every 2 weeks</span></p>
				<p style="margin:0;"><strong><?php esc_html_e( 'Next delivery:', 'brewcart' ); ?></strong> <span id="sub-next-date">—</span></p>
			</div>

			<ul style="color:var(--charcoal-soft);font-size:.88rem;padding-left:20px;margin-bottom:24px;">
				<li><?php esc_html_e( 'Save 10% on every recurring order', 'brewcart' ); ?></li>
				<li><?php esc_html_e( 'Skip, pause, or cancel anytime from your account', 'brewcart' ); ?></li>
				<li><?php esc_html_e( 'Free shipping on all subscription orders', 'brewcart' ); ?></li>
			</ul>

			<button type="submit" class="btn btn-primary" style="width:100%;"><?php esc_html_e( 'Start Subscription', 'brewcart' ); ?></button>
			<p class="form-status" role="status" style="margin-top:12px;"></p>
		</form>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'brewcart_subscriptions', 'brewcart_subscriptions_shortcode' );

/**
 * Add the selected subscription to the cart via AJAX, discounted and tagged with schedule meta.
 */
function brewcart_ajax_add_subscription() {
	brewcart_verify_ajax_nonce();

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$quantity   = isset( $_POST['quantity'] ) ? max( 1, absint( $_POST['quantity'] ) ) : 1;
	$frequency  = isset( $_POST['frequency'] ) ? sanitize_text_field( wp_unslash( $_POST['frequency'] ) ) : 'biweekly';

	$allowed_frequencies = array( 'weekly', 'biweekly', 'monthly' );
	if ( ! in_array( $frequency, $allowed_frequencies, true ) ) {
		$frequency = 'biweekly';
	}

	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		wp_send_json_error( array( 'message' => __( 'Invalid product.', 'brewcart' ) ), 400 );
	}

	$labels = array(
		'weekly'   => __( 'Weekly delivery', 'brewcart' ),
		'biweekly' => __( 'Every 2 weeks', 'brewcart' ),
		'monthly'  => __( 'Monthly delivery', 'brewcart' ),
	);

	$cart_item_data = array(
		'brewcart_subscription' => true,
		'brewcart_frequency'    => $labels[ $frequency ],
		'unique_key'            => md5( microtime() . wp_rand() ),
	);

	$added = WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), $cart_item_data );

	if ( ! $added ) {
		wp_send_json_error( array( 'message' => __( 'Could not add subscription to cart.', 'brewcart' ) ), 400 );
	}

	wp_send_json_success( array( 'message' => __( 'Subscription added to cart!', 'brewcart' ), 'cart_url' => wc_get_cart_url() ) );
}
add_action( 'wp_ajax_brewcart_add_subscription', 'brewcart_ajax_add_subscription' );
add_action( 'wp_ajax_nopriv_brewcart_add_subscription', 'brewcart_ajax_add_subscription' );

/**
 * Apply the subscriber discount and show frequency in cart/order line items.
 */
function brewcart_subscription_price_discount( $cart ) {
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}
	foreach ( $cart->get_cart() as $cart_item ) {
		if ( ! empty( $cart_item['brewcart_subscription'] ) ) {
			$original = $cart_item['data']->get_price();
			$cart_item['data']->set_price( round( $original * ( 1 - BREWCART_SUB_DISCOUNT ), 2 ) );
		}
	}
}
add_action( 'woocommerce_before_calculate_totals', 'brewcart_subscription_price_discount' );

function brewcart_subscription_item_display( $item_data, $cart_item ) {
	if ( ! empty( $cart_item['brewcart_frequency'] ) ) {
		$item_data[] = array(
			'name'  => __( 'Subscription', 'brewcart' ),
			'value' => $cart_item['brewcart_frequency'],
		);
	}
	return $item_data;
}
add_filter( 'woocommerce_get_item_data', 'brewcart_subscription_item_display', 10, 2 );

function brewcart_subscription_order_meta( $item, $cart_item_key, $values ) {
	if ( ! empty( $values['brewcart_frequency'] ) ) {
		$item->add_meta_data( __( 'Subscription', 'brewcart' ), $values['brewcart_frequency'], true );
	}
}
add_action( 'woocommerce_checkout_create_order_line_item', 'brewcart_subscription_order_meta', 10, 3 );
