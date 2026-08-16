<?php
/**
 * Security hardening: header sanitization, login protections.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Remove WP version leaks.
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

// Disable XML-RPC (not needed, reduces attack surface).
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Verify a BrewCart AJAX nonce or die. Use in every custom AJAX handler.
 */
function brewcart_verify_ajax_nonce() {
	if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'brewcart_nonce' ) ) {
		wp_send_json_error( array( 'message' => __( 'Security check failed. Please refresh and try again.', 'brewcart' ) ), 403 );
	}
}
