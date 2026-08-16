<?php
/**
 * Coffee Quiz — "Find Your Perfect Coffee". Client-side question flow,
 * server-side product matching via AJAX based on submitted answers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function brewcart_coffee_quiz_shortcode() {
	ob_start();
	?>
	<div class="coffee-quiz card reveal" style="max-width:640px;margin:0 auto;padding:40px;">
		<div id="quiz-steps">
			<div class="quiz-step" data-step="1">
				<h3><?php esc_html_e( 'How do you usually drink your coffee?', 'brewcart' ); ?></h3>
				<div class="option-pills quiz-pills" data-field="drink_style">
					<button type="button" class="pill" data-value="black"><?php esc_html_e( 'Black', 'brewcart' ); ?></button>
					<button type="button" class="pill" data-value="milk"><?php esc_html_e( 'With Milk', 'brewcart' ); ?></button>
					<button type="button" class="pill" data-value="espresso"><?php esc_html_e( 'Espresso-based', 'brewcart' ); ?></button>
					<button type="button" class="pill" data-value="iced"><?php esc_html_e( 'Iced / Cold Brew', 'brewcart' ); ?></button>
				</div>
			</div>
			<div class="quiz-step" data-step="2" hidden>
				<h3><?php esc_html_e( 'What roast level do you prefer?', 'brewcart' ); ?></h3>
				<div class="option-pills quiz-pills" data-field="roast">
					<button type="button" class="pill" data-value="Light"><?php esc_html_e( 'Light', 'brewcart' ); ?></button>
					<button type="button" class="pill" data-value="Medium"><?php esc_html_e( 'Medium', 'brewcart' ); ?></button>
					<button type="button" class="pill" data-value="Medium-Dark"><?php esc_html_e( 'Medium-Dark', 'brewcart' ); ?></button>
					<button type="button" class="pill" data-value="Dark"><?php esc_html_e( 'Dark', 'brewcart' ); ?></button>
				</div>
			</div>
			<div class="quiz-step" data-step="3" hidden>
				<h3><?php esc_html_e( 'How strong do you like it?', 'brewcart' ); ?></h3>
				<div class="option-pills quiz-pills" data-field="strength">
					<button type="button" class="pill" data-value="mild"><?php esc_html_e( 'Mild', 'brewcart' ); ?></button>
					<button type="button" class="pill" data-value="balanced"><?php esc_html_e( 'Balanced', 'brewcart' ); ?></button>
					<button type="button" class="pill" data-value="bold"><?php esc_html_e( 'Bold & Intense', 'brewcart' ); ?></button>
				</div>
			</div>
			<div class="quiz-step" data-step="4" hidden>
				<h3><?php esc_html_e( 'Bean or ground?', 'brewcart' ); ?></h3>
				<div class="option-pills quiz-pills" data-field="form">
					<button type="button" class="pill" data-value="bean"><?php esc_html_e( 'Whole Bean', 'brewcart' ); ?></button>
					<button type="button" class="pill" data-value="ground"><?php esc_html_e( 'Ground', 'brewcart' ); ?></button>
				</div>
			</div>
			<div class="quiz-step" data-step="5" hidden>
				<h3><?php esc_html_e( 'Pick a flavor profile', 'brewcart' ); ?></h3>
				<div class="option-pills quiz-pills" data-field="flavor">
					<button type="button" class="pill" data-value="fruity"><?php esc_html_e( 'Bright & Fruity', 'brewcart' ); ?></button>
					<button type="button" class="pill" data-value="chocolate"><?php esc_html_e( 'Chocolatey & Nutty', 'brewcart' ); ?></button>
					<button type="button" class="pill" data-value="spiced"><?php esc_html_e( 'Spiced & Earthy', 'brewcart' ); ?></button>
					<button type="button" class="pill" data-value="sweet"><?php esc_html_e( 'Sweet & Flavored', 'brewcart' ); ?></button>
				</div>
			</div>
		</div>
		<div class="quiz-progress" style="height:6px;background:var(--latte);border-radius:99px;margin-top:24px;overflow:hidden;">
			<div id="quiz-progress-bar" style="height:100%;width:20%;background:var(--amber);transition:width .3s ease;"></div>
		</div>
		<div id="quiz-results" hidden></div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'brewcart_coffee_quiz', 'brewcart_coffee_quiz_shortcode' );

/**
 * Match quiz answers to products using roast/origin/bean attributes + category.
 */
function brewcart_ajax_quiz_match() {
	brewcart_verify_ajax_nonce();

	$roast   = isset( $_POST['roast'] ) ? sanitize_text_field( wp_unslash( $_POST['roast'] ) ) : '';
	$form    = isset( $_POST['form'] ) ? sanitize_text_field( wp_unslash( $_POST['form'] ) ) : '';
	$style   = isset( $_POST['drink_style'] ) ? sanitize_text_field( wp_unslash( $_POST['drink_style'] ) ) : '';
	$flavor  = isset( $_POST['flavor'] ) ? sanitize_text_field( wp_unslash( $_POST['flavor'] ) ) : '';

	$args = array(
		'status' => 'publish',
		'limit'  => 3,
	);

	if ( $roast ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'pa_roast-level',
				'field'    => 'name',
				'terms'    => $roast,
			),
		);
	}

	if ( 'espresso' === $style ) {
		$args['category'] = array( 'espresso' );
	} elseif ( 'iced' === $style ) {
		$args['category'] = array( 'cold-brew' );
	} elseif ( 'ground' === $form ) {
		$args['category'] = array( 'ground-coffee' );
	}

	$products = wc_get_products( $args );

	if ( empty( $products ) ) {
		$products = wc_get_products( array( 'status' => 'publish', 'limit' => 3, 'orderby' => 'rand' ) );
	}

	$results = array();
	foreach ( $products as $product ) {
		$results[] = array(
			'id'    => $product->get_id(),
			'name'  => $product->get_name(),
			'url'   => $product->get_permalink(),
			'image' => wp_get_attachment_image_url( $product->get_image_id(), 'brewcart-card' ),
			'price' => $product->get_price_html(),
		);
	}

	wp_send_json_success( array( 'products' => $results ) );
}
add_action( 'wp_ajax_brewcart_quiz_match', 'brewcart_ajax_quiz_match' );
add_action( 'wp_ajax_nopriv_brewcart_quiz_match', 'brewcart_ajax_quiz_match' );
