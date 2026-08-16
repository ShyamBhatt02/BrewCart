<?php
/**
 * Contact form — AJAX submit, nonce-protected, stores message + attempts wp_mail.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function brewcart_contact_form_shortcode() {
	ob_start();
	?>
	<div class="grid grid-2" style="align-items:flex-start;gap:40px;">
		<div class="card reveal" style="padding:32px;">
			<h3><?php esc_html_e( 'Send us a message', 'brewcart' ); ?></h3>
			<form id="brewcart-contact-form">
				<?php wp_nonce_field( 'brewcart_nonce', 'contact_nonce' ); ?>
				<div style="margin-bottom:16px;">
					<label style="display:block;font-weight:600;font-size:.85rem;margin-bottom:6px;"><?php esc_html_e( 'Name', 'brewcart' ); ?></label>
					<input type="text" name="name" required style="width:100%;padding:12px;border-radius:8px;border:1px solid rgba(43,27,20,.15);">
				</div>
				<div style="margin-bottom:16px;">
					<label style="display:block;font-weight:600;font-size:.85rem;margin-bottom:6px;"><?php esc_html_e( 'Email', 'brewcart' ); ?></label>
					<input type="email" name="email" required style="width:100%;padding:12px;border-radius:8px;border:1px solid rgba(43,27,20,.15);">
				</div>
				<div style="margin-bottom:16px;">
					<label style="display:block;font-weight:600;font-size:.85rem;margin-bottom:6px;"><?php esc_html_e( 'Message', 'brewcart' ); ?></label>
					<textarea name="message" rows="5" required style="width:100%;padding:12px;border-radius:8px;border:1px solid rgba(43,27,20,.15);"></textarea>
				</div>
				<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Send Message', 'brewcart' ); ?></button>
				<p class="form-status" role="status" style="margin-top:12px;"></p>
			</form>
		</div>
		<div class="reveal">
			<h3><?php esc_html_e( 'Get in Touch', 'brewcart' ); ?></h3>
			<p><strong><?php esc_html_e( 'Email:', 'brewcart' ); ?></strong> hello@brewcart.test</p>
			<p><strong><?php esc_html_e( 'Phone:', 'brewcart' ); ?></strong> (206) 555-0182</p>
			<p><strong><?php esc_html_e( 'Address:', 'brewcart' ); ?></strong> 123 Roastery Lane, Seattle, WA 98101</p>
			<p><strong><?php esc_html_e( 'Hours:', 'brewcart' ); ?></strong> Mon–Fri 8am–6pm, Sat 9am–4pm, Sun closed</p>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'brewcart_contact_form', 'brewcart_contact_form_shortcode' );

function brewcart_ajax_contact_submit() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'brewcart_nonce' ) ) {
		wp_send_json_error( array( 'message' => __( 'Security check failed. Please refresh and try again.', 'brewcart' ) ), 403 );
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( ! $name || ! is_email( $email ) || ! $message ) {
		wp_send_json_error( array( 'message' => __( 'Please fill in all fields with a valid email.', 'brewcart' ) ), 400 );
	}

	wp_insert_post(
		array(
			'post_type'    => 'brewcart_message',
			'post_title'   => sprintf( '%s <%s>', $name, $email ),
			'post_content' => $message,
			'post_status'  => 'private',
		)
	);

	wp_mail( get_option( 'admin_email' ), 'BrewCart contact form: ' . $name, $message . "\n\nFrom: $email" );

	wp_send_json_success( array( 'message' => __( "Thanks — we'll get back to you within one business day.", 'brewcart' ) ) );
}
add_action( 'wp_ajax_brewcart_contact_submit', 'brewcart_ajax_contact_submit' );
add_action( 'wp_ajax_nopriv_brewcart_contact_submit', 'brewcart_ajax_contact_submit' );

function brewcart_register_message_cpt() {
	register_post_type(
		'brewcart_message',
		array(
			'labels'       => array(
				'name'          => __( 'Contact Messages', 'brewcart' ),
				'singular_name' => __( 'Contact Message', 'brewcart' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-email',
			'supports'     => array( 'title', 'editor' ),
			'capabilities' => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap' => true,
		)
	);
}
add_action( 'init', 'brewcart_register_message_cpt' );
