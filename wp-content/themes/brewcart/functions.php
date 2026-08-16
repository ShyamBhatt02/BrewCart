<?php
/**
 * BrewCart theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BREWCART_VERSION', '1.0.0' );
define( 'BREWCART_DIR', get_template_directory() );
define( 'BREWCART_URI', get_template_directory_uri() );

/**
 * Theme setup.
 */
function brewcart_setup() {
	load_theme_textdomain( 'brewcart', BREWCART_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );

	// WooCommerce support.
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'brewcart' ),
			'footer'  => __( 'Footer Menu', 'brewcart' ),
		)
	);

	set_post_thumbnail_size( 800, 800, true );
	add_image_size( 'brewcart-card', 600, 600, true );
	add_image_size( 'brewcart-hero', 1920, 1080, true );
}
add_action( 'after_setup_theme', 'brewcart_setup' );

/**
 * Enqueue styles & scripts.
 */
function brewcart_assets() {
	wp_enqueue_style( 'brewcart-fonts', 'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap', array(), null );
	wp_enqueue_style( 'brewcart-main', BREWCART_URI . '/assets/css/main.css', array(), filemtime( BREWCART_DIR . '/assets/css/main.css' ) );

	wp_enqueue_script( 'brewcart-main', BREWCART_URI . '/assets/js/main.js', array( 'jquery' ), filemtime( BREWCART_DIR . '/assets/js/main.js' ), true );

	wp_localize_script(
		'brewcart-main',
		'BrewCart',
		array(
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'brewcart_nonce' ),
			'cartUrl'    => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '',
			'isLoggedIn' => is_user_logged_in(),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'brewcart_assets' );

/**
 * Widget areas.
 */
function brewcart_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Blog Sidebar', 'brewcart' ),
			'id'            => 'sidebar-blog',
			'before_widget' => '<div class="widget card">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4>',
			'after_title'   => '</h4>',
		)
	);
}
add_action( 'widgets_init', 'brewcart_widgets_init' );

/**
 * Output the animated SVG logo.
 */
function brewcart_logo() {
	?>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brewcart-logo" aria-label="<?php esc_attr_e( 'BrewCart home', 'brewcart' ); ?>">
		<svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
			<path class="steam" d="M18 6c0 2-2 2-2 4s2 2 2 4" stroke="#C68B3C" stroke-width="2" stroke-linecap="round"/>
			<path class="steam" d="M24 6c0 2-2 2-2 4s2 2 2 4" stroke="#C68B3C" stroke-width="2" stroke-linecap="round"/>
			<path class="steam" d="M30 6c0 2-2 2-2 4s2 2 2 4" stroke="#C68B3C" stroke-width="2" stroke-linecap="round"/>
			<g class="cup">
				<path d="M8 18h28l-2.5 18a4 4 0 0 1-4 3.5H14.5a4 4 0 0 1-4-3.5L8 18Z" fill="#6F4E37"/>
				<path d="M36 20c3 0 5.5 2.2 5.5 5.5S39 31 36 31" stroke="#2B1B14" stroke-width="2.5" fill="none" stroke-linecap="round"/>
				<rect x="8" y="18" width="28" height="4" rx="2" fill="#2B1B14"/>
			</g>
		</svg>
		<span class="logo-text">Brew<span>Cart</span></span>
	</a>
	<?php
}

/**
 * Custom nav fallback.
 */
function brewcart_nav_fallback() {
	echo '<ul class="main-nav">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li>';
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		echo '<li><a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">Shop</a></li>';
	}
	echo '<li><a href="' . esc_url( home_url( '/about-us/' ) ) . '">About</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/blog/' ) ) . '">Blog</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/store-locator/' ) ) . '">Stores</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/contact/' ) ) . '">Contact</a></li>';
	echo '</ul>';
}

/**
 * Header cart count fragment (AJAX-updatable).
 */
function brewcart_cart_count_fragment( $fragments ) {
	ob_start();
	?>
	<span class="badge cart-count"><?php echo absint( WC()->cart->get_cart_contents_count() ); ?></span>
	<?php
	$fragments['span.cart-count'] = ob_get_clean();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'brewcart_cart_count_fragment' );

/**
 * Remove default WooCommerce wrappers, add our own in template-parts.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
function brewcart_wc_wrapper_start() {
	echo '<main class="site-main woocommerce-main"><div class="container">';
}
function brewcart_wc_wrapper_end() {
	echo '</div></main>';
}
add_action( 'woocommerce_before_main_content', 'brewcart_wc_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content', 'brewcart_wc_wrapper_end', 10 );

// BrewCart's shop/product layouts are intentionally full-width — no sidebar.
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar' );

// Reduce default product grid columns handling to our CSS grid.
add_filter( 'loop_shop_columns', fn() => 4 );
add_filter( 'loop_shop_per_page', fn() => 12 );

/**
 * Newsletter signup — stores subscriber as a custom table row via a simple option list.
 * No external ESP configured; architected so one can be plugged in later.
 */
function brewcart_ajax_newsletter_signup() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'brewcart_nonce' ) ) {
		wp_send_json_error( array( 'message' => __( 'Security check failed. Please refresh and try again.', 'brewcart' ) ), 403 );
	}

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'brewcart' ) ), 400 );
	}

	$subscribers = get_option( 'brewcart_newsletter_subscribers', array() );
	if ( in_array( $email, $subscribers, true ) ) {
		wp_send_json_success( array( 'message' => __( 'You are already subscribed!', 'brewcart' ) ) );
	}

	$subscribers[] = $email;
	update_option( 'brewcart_newsletter_subscribers', $subscribers );

	wp_send_json_success( array( 'message' => __( 'Thanks for subscribing!', 'brewcart' ) ) );
}
add_action( 'wp_ajax_brewcart_newsletter_signup', 'brewcart_ajax_newsletter_signup' );
add_action( 'wp_ajax_nopriv_brewcart_newsletter_signup', 'brewcart_ajax_newsletter_signup' );

/**
 * Includes.
 */
require BREWCART_DIR . '/inc/wishlist.php';
require BREWCART_DIR . '/inc/quiz.php';
require BREWCART_DIR . '/inc/subscriptions.php';
require BREWCART_DIR . '/inc/store-locator.php';
require BREWCART_DIR . '/inc/order-tracking.php';
require BREWCART_DIR . '/inc/security.php';
require BREWCART_DIR . '/inc/customize-coffee.php';
require BREWCART_DIR . '/inc/contact.php';
