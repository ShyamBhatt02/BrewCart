<?php
/**
 * Store Locator — custom post type + shortcode grid.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function brewcart_register_store_cpt() {
	register_post_type(
		'brewcart_store',
		array(
			'labels'       => array(
				'name'          => __( 'Store Locations', 'brewcart' ),
				'singular_name' => __( 'Store Location', 'brewcart' ),
			),
			'public'       => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-store',
			'supports'     => array( 'title', 'editor', 'thumbnail' ),
			'has_archive'  => false,
			'show_in_rest' => true,
		)
	);

	register_post_meta(
		'brewcart_store',
		'address',
		array(
			'type'         => 'string',
			'single'       => true,
			'show_in_rest' => true,
		)
	);
	register_post_meta( 'brewcart_store', 'phone', array( 'type' => 'string', 'single' => true, 'show_in_rest' => true ) );
	register_post_meta( 'brewcart_store', 'hours', array( 'type' => 'string', 'single' => true, 'show_in_rest' => true ) );
	register_post_meta( 'brewcart_store', 'map_query', array( 'type' => 'string', 'single' => true, 'show_in_rest' => true ) );
}
add_action( 'init', 'brewcart_register_store_cpt' );

function brewcart_store_meta_box() {
	add_meta_box( 'brewcart_store_details', __( 'Store Details', 'brewcart' ), 'brewcart_store_meta_box_html', 'brewcart_store', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'brewcart_store_meta_box' );

function brewcart_store_meta_box_html( $post ) {
	wp_nonce_field( 'brewcart_store_meta', 'brewcart_store_meta_nonce' );
	$address = get_post_meta( $post->ID, 'address', true );
	$phone   = get_post_meta( $post->ID, 'phone', true );
	$hours   = get_post_meta( $post->ID, 'hours', true );
	?>
	<p><label><strong><?php esc_html_e( 'Address', 'brewcart' ); ?></strong><br>
	<input type="text" name="brewcart_address" value="<?php echo esc_attr( $address ); ?>" style="width:100%;"></label></p>
	<p><label><strong><?php esc_html_e( 'Phone', 'brewcart' ); ?></strong><br>
	<input type="text" name="brewcart_phone" value="<?php echo esc_attr( $phone ); ?>" style="width:100%;"></label></p>
	<p><label><strong><?php esc_html_e( 'Hours', 'brewcart' ); ?></strong><br>
	<input type="text" name="brewcart_hours" value="<?php echo esc_attr( $hours ); ?>" style="width:100%;"></label></p>
	<?php
}

function brewcart_save_store_meta( $post_id ) {
	if ( ! isset( $_POST['brewcart_store_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['brewcart_store_meta_nonce'] ) ), 'brewcart_store_meta' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( array( 'address', 'phone', 'hours' ) as $field ) {
		if ( isset( $_POST[ 'brewcart_' . $field ] ) ) {
			update_post_meta( $post_id, $field, sanitize_text_field( wp_unslash( $_POST[ 'brewcart_' . $field ] ) ) );
		}
	}
}
add_action( 'save_post_brewcart_store', 'brewcart_save_store_meta' );

function brewcart_store_locator_shortcode() {
	$stores = get_posts(
		array(
			'post_type'      => 'brewcart_store',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	ob_start();

	if ( empty( $stores ) ) {
		echo '<p>' . esc_html__( 'No store locations found yet.', 'brewcart' ) . '</p>';
		return ob_get_clean();
	}

	echo '<div class="grid grid-3 store-grid">';
	foreach ( $stores as $store ) {
		$address = get_post_meta( $store->ID, 'address', true );
		$phone   = get_post_meta( $store->ID, 'phone', true );
		$hours   = get_post_meta( $store->ID, 'hours', true );
		?>
		<div class="card reveal" style="padding:24px;">
			<h3><?php echo esc_html( $store->post_title ); ?></h3>
			<p style="color:var(--charcoal-soft);"><?php echo esc_html( $address ); ?></p>
			<?php if ( $phone ) : ?><p><strong><?php esc_html_e( 'Phone:', 'brewcart' ); ?></strong> <?php echo esc_html( $phone ); ?></p><?php endif; ?>
			<?php if ( $hours ) : ?><p><strong><?php esc_html_e( 'Hours:', 'brewcart' ); ?></strong> <?php echo esc_html( $hours ); ?></p><?php endif; ?>
			<a class="btn btn-outline" style="margin-top:8px;" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query=<?php echo rawurlencode( $address ); ?>"><?php esc_html_e( 'Get Directions', 'brewcart' ); ?></a>
		</div>
		<?php
	}
	echo '</div>';

	return ob_get_clean();
}
add_shortcode( 'brewcart_store_locator', 'brewcart_store_locator_shortcode' );
