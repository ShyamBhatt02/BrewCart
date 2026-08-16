<?php
/**
 * Footer template.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<footer class="site-footer">
	<div class="container">
		<div class="footer-grid">
			<div>
				<div class="brewcart-logo" style="color:#fff;margin-bottom:16px;">
					<svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path class="steam" d="M18 6c0 2-2 2-2 4s2 2 2 4" stroke="#C68B3C" stroke-width="2" stroke-linecap="round"/>
						<path class="steam" d="M24 6c0 2-2 2-2 4s2 2 2 4" stroke="#C68B3C" stroke-width="2" stroke-linecap="round"/>
						<path class="steam" d="M30 6c0 2-2 2-2 4s2 2 2 4" stroke="#C68B3C" stroke-width="2" stroke-linecap="round"/>
						<g class="cup"><path d="M8 18h28l-2.5 18a4 4 0 0 1-4 3.5H14.5a4 4 0 0 1-4-3.5L8 18Z" fill="#E8B96A"/><rect x="8" y="18" width="28" height="4" rx="2" fill="#FBF3E7"/></g>
					</svg>
					<span class="logo-text">Brew<span>Cart</span></span>
				</div>
				<p><?php esc_html_e( 'Small-batch roasted coffee, sourced direct from farms we know by name. Freshly roasted, perfectly brewed.', 'brewcart' ); ?></p>
				<div class="social-icons">
					<a href="#" aria-label="Instagram"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c2.7 0 3 0 4.1.06 1.1.05 1.8.22 2.5.47a5 5 0 0 1 1.8 1.17 5 5 0 0 1 1.17 1.8c.25.7.42 1.4.47 2.5C22 8.9 22 9.3 22 12s0 3-.06 4.1c-.05 1.1-.22 1.8-.47 2.5a5 5 0 0 1-1.17 1.8 5 5 0 0 1-1.8 1.17c-.7.25-1.4.42-2.5.47C15 22 14.7 22 12 22s-3 0-4.1-.06c-1.1-.05-1.8-.22-2.5-.47a5 5 0 0 1-1.8-1.17 5 5 0 0 1-1.17-1.8c-.25-.7-.42-1.4-.47-2.5C2 15 2 14.7 2 12s0-3 .06-4.1c.05-1.1.22-1.8.47-2.5a5 5 0 0 1 1.17-1.8A5 5 0 0 1 5.5 2.53c.7-.25 1.4-.42 2.5-.47C8.9 2 9.3 2 12 2Z"/></svg></a>
					<a href="#" aria-label="Facebook"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M13 22v-8h2.7l.4-3.3H13V8.5c0-1 .3-1.6 1.7-1.6H16V4c-.3 0-1.3-.1-2.4-.1-2.4 0-4 1.5-4 4.2v2.6H7v3.3h2.6V22H13Z"/></svg></a>
					<a href="#" aria-label="Twitter"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M22 5.9c-.7.3-1.5.6-2.3.7a4 4 0 0 0 1.7-2.2c-.8.5-1.6.8-2.6 1a4 4 0 0 0-6.8 3.6A11.2 11.2 0 0 1 3.7 4.9a4 4 0 0 0 1.2 5.3c-.6 0-1.3-.2-1.8-.5v.1a4 4 0 0 0 3.2 3.9 4 4 0 0 1-1.8.1 4 4 0 0 0 3.7 2.7A8 8 0 0 1 2 18.4a11.3 11.3 0 0 0 6.1 1.8c7.3 0 11.3-6 11.3-11.3v-.5c.8-.6 1.4-1.3 1.9-2.1Z"/></svg></a>
				</div>
			</div>
			<div>
				<h4><?php esc_html_e( 'Shop', 'brewcart' ); ?></h4>
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'menu_class' => '', 'depth' => 1 ) );
				} else {
					echo '<ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px;">';
					echo '<li><a href="' . esc_url( home_url( '/coffee-beans/' ) ) . '">' . esc_html__( 'Coffee Beans', 'brewcart' ) . '</a></li>';
					echo '<li><a href="' . esc_url( home_url( '/ground-coffee/' ) ) . '">' . esc_html__( 'Ground Coffee', 'brewcart' ) . '</a></li>';
					echo '<li><a href="' . esc_url( home_url( '/espresso/' ) ) . '">' . esc_html__( 'Espresso', 'brewcart' ) . '</a></li>';
					echo '<li><a href="' . esc_url( home_url( '/gift-sets/' ) ) . '">' . esc_html__( 'Gift Sets', 'brewcart' ) . '</a></li>';
					echo '</ul>';
				}
				?>
			</div>
			<div>
				<h4><?php esc_html_e( 'Help', 'brewcart' ); ?></h4>
				<ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px;">
					<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact Us', 'brewcart' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/order-tracking/' ) ); ?>"><?php esc_html_e( 'Track Order', 'brewcart' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'brewcart' ); ?></a></li>
					<li><a href="<?php echo function_exists( 'wc_get_page_permalink' ) ? esc_url( wc_get_page_permalink( 'myaccount' ) ) : '#'; ?>"><?php esc_html_e( 'My Account', 'brewcart' ); ?></a></li>
				</ul>
			</div>
			<div>
				<h4><?php esc_html_e( 'Newsletter', 'brewcart' ); ?></h4>
				<p><?php esc_html_e( 'Roasting notes & offers, twice a month.', 'brewcart' ); ?></p>
				<form class="newsletter-form" id="newsletter-form">
					<?php wp_nonce_field( 'brewcart_nonce', 'newsletter_nonce' ); ?>
					<input type="email" name="email" placeholder="<?php esc_attr_e( 'Your email', 'brewcart' ); ?>" required style="width:100%;padding:12px;border-radius:8px;border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.05);color:#fff;margin-bottom:10px;">
					<button type="submit" class="btn btn-accent" style="width:100%;"><?php esc_html_e( 'Subscribe', 'brewcart' ); ?></button>
				</form>
			</div>
		</div>
		<div class="footer-bottom">
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> BrewCart. <?php esc_html_e( 'All rights reserved.', 'brewcart' ); ?></span>
			<span><?php esc_html_e( 'Payments processed securely — no card data stored on this site.', 'brewcart' ); ?></span>
		</div>
	</div>
</footer>

<nav class="mobile-bottom-nav" aria-label="<?php esc_attr_e( 'Mobile navigation', 'brewcart' ); ?>">
	<a href="#" id="mobile-search-toggle">
		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
		<?php esc_html_e( 'Search', 'brewcart' ); ?>
	</a>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="<?php echo is_front_page() ? 'active' : ''; ?>">
		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
		<?php esc_html_e( 'Home', 'brewcart' ); ?>
	</a>
	<a href="<?php echo function_exists( 'wc_get_page_permalink' ) ? esc_url( wc_get_page_permalink( 'shop' ) ) : '#'; ?>">
		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 7v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7l-3-5H6Z"/><path d="M3 7h18M16 11a4 4 0 0 1-8 0"/></svg>
		<?php esc_html_e( 'Shop', 'brewcart' ); ?>
	</a>
	<a href="<?php echo esc_url( home_url( '/wishlist/' ) ); ?>">
		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78Z"/></svg>
		<?php esc_html_e( 'Wishlist', 'brewcart' ); ?>
	</a>
	<a href="<?php echo function_exists( 'wc_get_cart_url' ) ? esc_url( wc_get_cart_url() ) : '#'; ?>">
		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2 3h2l2.6 12.4a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L22 7H6"/></svg>
		<?php esc_html_e( 'Cart', 'brewcart' ); ?>
	</a>
	<a href="<?php echo function_exists( 'wc_get_page_permalink' ) ? esc_url( wc_get_page_permalink( 'myaccount' ) ) : '#'; ?>">
		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
		<?php esc_html_e( 'Account', 'brewcart' ); ?>
	</a>
</nav>

<?php wp_footer(); ?>
</body>
</html>
