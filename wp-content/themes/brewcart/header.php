<?php
/**
 * Header template.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="brewcart-toasts" aria-live="polite"></div>

<div class="announcement-bar">
	<?php esc_html_e( 'Free shipping on orders over $45 — Freshly roasted every Monday & Thursday', 'brewcart' ); ?>
</div>

<header class="site-header" id="site-header">
	<div class="container header-inner">
		<?php brewcart_logo(); ?>

		<nav class="main-nav-wrap" aria-label="<?php esc_attr_e( 'Primary', 'brewcart' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'main-nav',
						'fallback_cb'    => 'brewcart_nav_fallback',
					)
				);
			} else {
				brewcart_nav_fallback();
			}
			?>
		</nav>

		<div class="header-icons">
			<a href="#" id="search-toggle" aria-label="<?php esc_attr_e( 'Search', 'brewcart' ); ?>">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
			</a>
			<a href="<?php echo esc_url( wp_login_url() ); ?>" aria-label="<?php esc_attr_e( 'Account', 'brewcart' ); ?>">
				<?php if ( is_user_logged_in() ) : ?>
					<?php $account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : admin_url(); ?>
				<?php endif; ?>
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
			</a>
			<a href="<?php echo esc_url( home_url( '/wishlist/' ) ); ?>" aria-label="<?php esc_attr_e( 'Wishlist', 'brewcart' ); ?>">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78Z"/></svg>
			</a>
			<a href="<?php echo function_exists( 'wc_get_cart_url' ) ? esc_url( wc_get_cart_url() ) : '#'; ?>" aria-label="<?php esc_attr_e( 'Cart', 'brewcart' ); ?>">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2 3h2l2.6 12.4a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L22 7H6"/></svg>
				<?php if ( function_exists( 'WC' ) && WC()->cart ) : ?>
					<span class="badge cart-count"><?php echo absint( WC()->cart->get_cart_contents_count() ); ?></span>
				<?php endif; ?>
			</a>
			<button class="menu-toggle" id="menu-toggle" aria-label="<?php esc_attr_e( 'Menu', 'brewcart' ); ?>">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
			</button>
		</div>
	</div>

	<div class="mobile-nav-panel" id="mobile-nav-panel" hidden>
		<?php brewcart_nav_fallback(); ?>
	</div>
</header>

<div class="search-overlay" id="search-overlay" hidden>
	<div class="container">
		<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search-overlay-form">
			<input type="hidden" name="post_type" value="product">
			<input type="search" name="s" placeholder="<?php esc_attr_e( 'Search coffee, categories, brands…', 'brewcart' ); ?>" autocomplete="off" id="search-overlay-input">
			<button type="submit"><?php esc_html_e( 'Search', 'brewcart' ); ?></button>
		</form>
		<div id="search-suggestions"></div>
		<button class="search-close" id="search-close" aria-label="<?php esc_attr_e( 'Close search', 'brewcart' ); ?>">&times;</button>
	</div>
</div>
