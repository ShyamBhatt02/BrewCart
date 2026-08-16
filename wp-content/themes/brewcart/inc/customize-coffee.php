<?php
/**
 * "Customize Your Coffee" module — grind size, weight, quantity shown before add-to-cart.
 * Selections are stored as WooCommerce cart item meta (no separate product variations needed).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function brewcart_customize_coffee_fields() {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}
	?>
	<div class="customize-coffee card" style="padding:22px;margin-bottom:22px;">
		<h3 style="font-size:1.05rem;margin-bottom:14px;"><?php esc_html_e( 'Customize Your Coffee', 'brewcart' ); ?></h3>

		<div class="customize-row" style="margin-bottom:16px;">
			<label style="display:block;font-weight:600;font-size:.85rem;margin-bottom:8px;"><?php esc_html_e( 'Bean or Ground?', 'brewcart' ); ?></label>
			<div class="option-pills" data-field="bean_ground">
				<button type="button" class="pill active" data-value="Whole Bean"><?php esc_html_e( 'Whole Bean', 'brewcart' ); ?></button>
				<button type="button" class="pill" data-value="Ground"><?php esc_html_e( 'Ground', 'brewcart' ); ?></button>
			</div>
		</div>

		<div class="customize-row grind-row" style="margin-bottom:16px;display:none;">
			<label style="display:block;font-weight:600;font-size:.85rem;margin-bottom:8px;"><?php esc_html_e( 'Grind Size', 'brewcart' ); ?></label>
			<select name="brewcart_grind_size" class="customize-select" style="width:100%;padding:12px;border-radius:8px;border:1px solid rgba(43,27,20,.15);">
				<option value="Extra Fine (Turkish)"><?php esc_html_e( 'Extra Fine (Turkish)', 'brewcart' ); ?></option>
				<option value="Fine (Espresso)" selected><?php esc_html_e( 'Fine (Espresso)', 'brewcart' ); ?></option>
				<option value="Medium (Drip)"><?php esc_html_e( 'Medium (Drip)', 'brewcart' ); ?></option>
				<option value="Coarse (French Press)"><?php esc_html_e( 'Coarse (French Press)', 'brewcart' ); ?></option>
				<option value="Extra Coarse (Cold Brew)"><?php esc_html_e( 'Extra Coarse (Cold Brew)', 'brewcart' ); ?></option>
			</select>
		</div>

		<div class="customize-row" style="margin-bottom:16px;">
			<label style="display:block;font-weight:600;font-size:.85rem;margin-bottom:8px;"><?php esc_html_e( 'Weight', 'brewcart' ); ?></label>
			<div class="option-pills" data-field="weight">
				<button type="button" class="pill" data-value="250g"><?php esc_html_e( '250g', 'brewcart' ); ?></button>
				<button type="button" class="pill active" data-value="500g"><?php esc_html_e( '500g', 'brewcart' ); ?></button>
				<button type="button" class="pill" data-value="1kg"><?php esc_html_e( '1kg', 'brewcart' ); ?></button>
			</div>
		</div>

		<div class="customize-summary" style="background:var(--latte);border-radius:var(--radius-sm);padding:14px 16px;font-size:.88rem;color:var(--charcoal-soft);">
			<strong style="color:var(--espresso);"><?php esc_html_e( 'Your selection:', 'brewcart' ); ?></strong>
			<span class="summary-text"><?php esc_html_e( 'Whole Bean · 500g', 'brewcart' ); ?></span>
		</div>

		<input type="hidden" name="brewcart_bean_ground" id="brewcart_bean_ground" value="Whole Bean">
		<input type="hidden" name="brewcart_weight" id="brewcart_weight" value="500g">
	</div>
	<?php
}
add_action( 'woocommerce_before_add_to_cart_button', 'brewcart_customize_coffee_fields', 5 );

/**
 * Persist selections into cart item data.
 */
function brewcart_add_cart_item_data( $cart_item_data, $product_id ) {
	if ( isset( $_POST['brewcart_bean_ground'] ) ) {
		$cart_item_data['brewcart_bean_ground'] = sanitize_text_field( wp_unslash( $_POST['brewcart_bean_ground'] ) );
	}
	if ( isset( $_POST['brewcart_weight'] ) ) {
		$cart_item_data['brewcart_weight'] = sanitize_text_field( wp_unslash( $_POST['brewcart_weight'] ) );
	}
	if ( ! empty( $_POST['brewcart_grind_size'] ) ) {
		$cart_item_data['brewcart_grind_size'] = sanitize_text_field( wp_unslash( $_POST['brewcart_grind_size'] ) );
	}
	if ( ! empty( $cart_item_data ) ) {
		$cart_item_data['unique_key'] = md5( microtime() . wp_rand() );
	}
	return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'brewcart_add_cart_item_data', 10, 2 );

/**
 * Show selections in cart / order line items.
 */
function brewcart_cart_item_display( $item_data, $cart_item ) {
	$map = array(
		'brewcart_bean_ground' => __( 'Type', 'brewcart' ),
		'brewcart_grind_size'  => __( 'Grind', 'brewcart' ),
		'brewcart_weight'      => __( 'Weight', 'brewcart' ),
	);
	foreach ( $map as $key => $label ) {
		if ( ! empty( $cart_item[ $key ] ) ) {
			$item_data[] = array(
				'name'  => $label,
				'value' => $cart_item[ $key ],
			);
		}
	}
	return $item_data;
}
add_filter( 'woocommerce_get_item_data', 'brewcart_cart_item_display', 10, 2 );

/**
 * Carry selections through to the order line item (visible on order details / admin).
 */
function brewcart_add_order_line_item_meta( $item, $cart_item_key, $values ) {
	$map = array(
		'brewcart_bean_ground' => __( 'Type', 'brewcart' ),
		'brewcart_grind_size'  => __( 'Grind', 'brewcart' ),
		'brewcart_weight'      => __( 'Weight', 'brewcart' ),
	);
	foreach ( $map as $key => $label ) {
		if ( ! empty( $values[ $key ] ) ) {
			$item->add_meta_data( $label, $values[ $key ], true );
		}
	}
}
add_action( 'woocommerce_checkout_create_order_line_item', 'brewcart_add_order_line_item_meta', 10, 3 );
