<?php
/**
 * Order Tracking — lookup by order number + email, shows status timeline.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function brewcart_order_tracking_shortcode() {
	ob_start();
	?>
	<div class="card reveal" style="max-width:560px;margin:0 auto 32px;padding:32px;">
		<h3><?php esc_html_e( 'Track Your Order', 'brewcart' ); ?></h3>
		<form id="brewcart-track-form">
			<?php wp_nonce_field( 'brewcart_nonce', 'track_nonce' ); ?>
			<div style="margin-bottom:16px;">
				<label style="display:block;font-weight:600;font-size:.85rem;margin-bottom:6px;"><?php esc_html_e( 'Order Number', 'brewcart' ); ?></label>
				<input type="text" name="order_id" required placeholder="<?php esc_attr_e( 'e.g. 1032', 'brewcart' ); ?>" style="width:100%;padding:12px;border-radius:8px;border:1px solid rgba(43,27,20,.15);">
			</div>
			<div style="margin-bottom:20px;">
				<label style="display:block;font-weight:600;font-size:.85rem;margin-bottom:6px;"><?php esc_html_e( 'Email or Phone used at checkout', 'brewcart' ); ?></label>
				<input type="text" name="contact" required style="width:100%;padding:12px;border-radius:8px;border:1px solid rgba(43,27,20,.15);">
			</div>
			<button type="submit" class="btn btn-primary" style="width:100%;"><?php esc_html_e( 'Track Order', 'brewcart' ); ?></button>
		</form>
	</div>
	<div id="track-results"></div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'brewcart_order_tracking', 'brewcart_order_tracking_shortcode' );

function brewcart_ajax_track_order() {
	brewcart_verify_ajax_nonce();

	$order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
	$contact  = isset( $_POST['contact'] ) ? sanitize_text_field( wp_unslash( $_POST['contact'] ) ) : '';

	$order = $order_id ? wc_get_order( $order_id ) : false;

	if ( ! $order ) {
		wp_send_json_error( array( 'message' => __( 'We could not find an order with that number.', 'brewcart' ) ), 404 );
	}

	$matches_email = sanitize_email( $contact ) && strtolower( $order->get_billing_email() ) === strtolower( $contact );
	$matches_phone = preg_replace( '/\D/', '', $contact ) && preg_replace( '/\D/', '', $order->get_billing_phone() ) === preg_replace( '/\D/', '', $contact );

	if ( ! $matches_email && ! $matches_phone ) {
		wp_send_json_error( array( 'message' => __( 'Order number and contact info do not match our records.', 'brewcart' ) ), 404 );
	}

	$stages       = array( 'received', 'processing', 'packed', 'shipped', 'out-for-delivery', 'delivered' );
	$status_map   = array(
		'pending'    => 'received',
		'processing' => 'processing',
		'on-hold'    => 'processing',
		'completed'  => 'delivered',
		'cancelled'  => 'received',
		'refunded'   => 'received',
		'failed'     => 'received',
	);
	$custom_stage = get_post_meta( $order->get_id(), '_brewcart_fulfillment_stage', true );
	$current      = $custom_stage ? $custom_stage : ( $status_map[ $order->get_status() ] ?? 'received' );
	$current_idx  = array_search( $current, $stages, true );

	$items = array();
	foreach ( $order->get_items() as $item ) {
		$items[] = array(
			'name'     => $item->get_name(),
			'quantity' => $item->get_quantity(),
		);
	}

	wp_send_json_success(
		array(
			'order_number' => $order->get_order_number(),
			'date'         => $order->get_date_created() ? $order->get_date_created()->date_i18n( get_option( 'date_format' ) ) : '',
			'total'        => $order->get_formatted_order_total(),
			'stages'       => $stages,
			'current_idx'  => false === $current_idx ? 0 : $current_idx,
			'items'        => $items,
		)
	);
}
add_action( 'wp_ajax_brewcart_track_order', 'brewcart_ajax_track_order' );
add_action( 'wp_ajax_nopriv_brewcart_track_order', 'brewcart_ajax_track_order' );
