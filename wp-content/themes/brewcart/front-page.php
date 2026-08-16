<?php
/**
 * BrewCart homepage.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$hero_slides_option = get_option( 'brewcart_hero_slides', array() );
$fallback_hero       = BREWCART_URI . '/assets/images/hero-coffee.jpg';

$hero_slides = array(
	array(
		'image'   => ! empty( $hero_slides_option['hero-1'] ) ? $hero_slides_option['hero-1'] : $fallback_hero,
		'eyebrow' => __( 'Small-batch • Direct trade • Roasted weekly', 'brewcart' ),
		'title'   => __( 'Freshly Roasted. Perfectly Brewed.', 'brewcart' ),
		'text'    => __( 'From farm to cup — single-origin beans, expertly roasted in small batches and shipped within 48 hours of roasting.', 'brewcart' ),
	),
	array(
		'image'   => ! empty( $hero_slides_option['hero-2'] ) ? $hero_slides_option['hero-2'] : $fallback_hero,
		'eyebrow' => __( 'New This Season', 'brewcart' ),
		'title'   => __( 'Brew Your Perfect Cup at Home', 'brewcart' ),
		'text'    => __( 'From pour-over to French press, our beans are roasted to bring out the best in every method.', 'brewcart' ),
	),
	array(
		'image'   => ! empty( $hero_slides_option['hero-3'] ) ? $hero_slides_option['hero-3'] : $fallback_hero,
		'eyebrow' => __( 'Subscribe & Save 10%', 'brewcart' ),
		'title'   => __( 'Never Run Out of Great Coffee', 'brewcart' ),
		'text'    => __( 'Weekly, bi-weekly, or monthly delivery — skip, pause, or cancel anytime.', 'brewcart' ),
	),
);
?>

<main class="site-main">

	<!-- Hero -->
	<section class="hero-slider" id="hero-slider" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Featured promotions', 'brewcart' ); ?>">
		<?php foreach ( $hero_slides as $i => $slide ) : ?>
			<div class="hero-slide <?php echo 0 === $i ? 'is-active' : ''; ?>" style="--hero-img:url('<?php echo esc_url( $slide['image'] ); ?>');" aria-hidden="<?php echo 0 === $i ? 'false' : 'true'; ?>">
				<div class="container">
					<div class="hero-content">
						<span class="hero-eyebrow"><?php echo esc_html( $slide['eyebrow'] ); ?></span>
						<h1><?php echo esc_html( $slide['title'] ); ?></h1>
						<p><?php echo esc_html( $slide['text'] ); ?></p>
						<div class="hero-cta">
							<a href="<?php echo function_exists( 'wc_get_page_permalink' ) ? esc_url( wc_get_page_permalink( 'shop' ) ) : '#'; ?>" class="btn btn-accent"><?php esc_html_e( 'Shop Coffee', 'brewcart' ); ?></a>
							<a href="<?php echo esc_url( home_url( '/coffee-quiz/' ) ); ?>" class="btn btn-outline" style="border-color:#fff;color:#fff;"><?php esc_html_e( 'Find Your Coffee', 'brewcart' ); ?></a>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>

		<div class="hero-dots" role="tablist" aria-label="<?php esc_attr_e( 'Slide navigation', 'brewcart' ); ?>">
			<?php foreach ( $hero_slides as $i => $slide ) : ?>
				<button type="button" class="hero-dot <?php echo 0 === $i ? 'is-active' : ''; ?>" data-slide="<?php echo esc_attr( $i ); ?>" role="tab" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Go to slide %d', 'brewcart' ), $i + 1 ) ); ?>"></button>
			<?php endforeach; ?>
		</div>

		<button type="button" class="hero-arrow hero-arrow-prev" id="hero-prev" aria-label="<?php esc_attr_e( 'Previous slide', 'brewcart' ); ?>">&#8249;</button>
		<button type="button" class="hero-arrow hero-arrow-next" id="hero-next" aria-label="<?php esc_attr_e( 'Next slide', 'brewcart' ); ?>">&#8250;</button>
	</section>

	<!-- Featured Products -->
	<section>
		<div class="container">
			<div class="section-head reveal">
				<span class="eyebrow"><?php esc_html_e( 'Handpicked', 'brewcart' ); ?></span>
				<h2><?php esc_html_e( 'Featured Coffee', 'brewcart' ); ?></h2>
				<p><?php esc_html_e( 'Our roasters’ current favorites — limited batches, big flavor.', 'brewcart' ); ?></p>
			</div>
			<div class="grid grid-4">
				<?php
				if ( function_exists( 'wc_get_products' ) ) {
					$featured = wc_get_products( array( 'featured' => true, 'limit' => 8, 'status' => 'publish' ) );
					if ( empty( $featured ) ) {
						$featured = wc_get_products( array( 'limit' => 8, 'status' => 'publish', 'orderby' => 'date', 'order' => 'DESC' ) );
					}
					foreach ( $featured as $product ) {
						get_template_part( 'template-parts/product-card', null, array( 'product' => $product ) );
					}
				}
				?>
			</div>
		</div>
	</section>

	<!-- Categories -->
	<section class="bg-latte">
		<div class="container">
			<div class="section-head reveal">
				<span class="eyebrow"><?php esc_html_e( 'Explore', 'brewcart' ); ?></span>
				<h2><?php esc_html_e( 'Shop by Category', 'brewcart' ); ?></h2>
			</div>
			<div class="grid grid-4">
				<?php
				$cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'exclude' => array( get_option( 'default_product_cat' ) ) ) );
				if ( ! is_wp_error( $cats ) ) {
					foreach ( array_slice( $cats, 0, 8 ) as $cat ) {
						$thumb_id  = get_term_meta( $cat->term_id, 'thumbnail_id', true );
						$image_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'brewcart-card' ) : BREWCART_URI . '/assets/images/category-placeholder.jpg';
						?>
						<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="card reveal" style="display:block;text-align:center;padding:24px 16px;">
							<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $cat->name ); ?>" style="width:100%;aspect-ratio:1/1;object-fit:cover;border-radius:var(--radius-md);margin-bottom:14px;">
							<h3 style="margin-bottom:4px;"><?php echo esc_html( $cat->name ); ?></h3>
							<span style="color:var(--charcoal-soft);font-size:.85rem;"><?php echo esc_html( $cat->count ) . ' ' . esc_html__( 'products', 'brewcart' ); ?></span>
						</a>
						<?php
					}
				}
				?>
			</div>
		</div>
	</section>

	<!-- Best Sellers -->
	<section>
		<div class="container">
			<div class="section-head reveal">
				<span class="eyebrow"><?php esc_html_e( 'Fan Favorites', 'brewcart' ); ?></span>
				<h2><?php esc_html_e( 'Best Sellers', 'brewcart' ); ?></h2>
			</div>
			<div class="grid grid-4">
				<?php
				if ( function_exists( 'wc_get_products' ) ) {
					$bestsellers = wc_get_products( array( 'limit' => 4, 'orderby' => 'popularity', 'status' => 'publish' ) );
					foreach ( $bestsellers as $product ) {
						get_template_part( 'template-parts/product-card', null, array( 'product' => $product ) );
					}
				}
				?>
			</div>
		</div>
	</section>

	<!-- Promo Banner -->
	<section>
		<div class="container">
			<div class="reveal" style="background:linear-gradient(135deg,var(--espresso),var(--coffee-brown));border-radius:var(--radius-lg);padding:56px;text-align:center;color:#fff;">
				<h2 style="color:#fff;"><?php esc_html_e( 'First Order? Get 15% Off.', 'brewcart' ); ?></h2>
				<p style="color:var(--latte);max-width:520px;margin:0 auto 24px;"><?php esc_html_e( 'Use code WELCOME15 at checkout. Free shipping on orders over $45.', 'brewcart' ); ?></p>
				<a href="<?php echo function_exists( 'wc_get_page_permalink' ) ? esc_url( wc_get_page_permalink( 'shop' ) ) : '#'; ?>" class="btn btn-accent"><?php esc_html_e( 'Shop Now', 'brewcart' ); ?></a>
			</div>
		</div>
	</section>

	<!-- Why Choose Us -->
	<section class="bg-latte">
		<div class="container">
			<div class="section-head reveal">
				<span class="eyebrow"><?php esc_html_e( 'Our Promise', 'brewcart' ); ?></span>
				<h2><?php esc_html_e( 'Why Choose BrewCart', 'brewcart' ); ?></h2>
			</div>
			<div class="grid grid-4">
				<?php
				$features = array(
					array( 'icon' => '&#9749;', 'title' => __( 'Roasted Fresh Weekly', 'brewcart' ), 'text' => __( 'Beans roasted in small batches and shipped within 48 hours.', 'brewcart' ) ),
					array( 'icon' => '&#127793;', 'title' => __( 'Direct Trade', 'brewcart' ), 'text' => __( 'We buy direct from farmers at fair, above-market prices.', 'brewcart' ) ),
					array( 'icon' => '&#128666;', 'title' => __( 'Fast, Free Shipping', 'brewcart' ), 'text' => __( 'Free shipping on all orders over $45, delivered in 2-4 days.', 'brewcart' ) ),
					array( 'icon' => '&#10003;', 'title' => __( '30-Day Guarantee', 'brewcart' ), 'text' => __( 'Not loving your brew? We will make it right.', 'brewcart' ) ),
				);
				foreach ( $features as $f ) :
					?>
					<div class="card reveal" style="padding:32px 24px;text-align:center;">
						<div style="font-size:2.2rem;margin-bottom:12px;"><?php echo $f['icon']; ?></div>
						<h3><?php echo esc_html( $f['title'] ); ?></h3>
						<p style="color:var(--charcoal-soft);font-size:.92rem;"><?php echo esc_html( $f['text'] ); ?></p>
					</div>
					<?php
				endforeach;
				?>
			</div>
		</div>
	</section>

	<!-- Reviews -->
	<section>
		<div class="container">
			<div class="section-head reveal">
				<span class="eyebrow"><?php esc_html_e( 'Loved by Customers', 'brewcart' ); ?></span>
				<h2><?php esc_html_e( 'What People Are Saying', 'brewcart' ); ?></h2>
			</div>
			<div class="grid grid-3">
				<?php
				$reviews = array(
					array( 'name' => 'Maria S.', 'text' => __( 'The Ethiopian single origin is the best coffee I’ve had shipped to my door. Tastes like it was roasted yesterday — because it was.', 'brewcart' ) ),
					array( 'name' => 'James T.', 'text' => __( 'Subscribed to the monthly plan six months ago and never looked back. Consistent, delicious, and the packaging keeps it fresh.', 'brewcart' ) ),
					array( 'name' => 'Aisha K.', 'text' => __( 'Took the coffee quiz and it nailed my taste — recommended the Colombian Dark Roast and it is now my daily driver.', 'brewcart' ) ),
				);
				foreach ( $reviews as $r ) :
					?>
					<div class="card reveal" style="padding:28px;">
						<div class="rating" style="margin-bottom:12px;">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
						<p style="font-style:italic;">"<?php echo esc_html( $r['text'] ); ?>"</p>
						<strong><?php echo esc_html( $r['name'] ); ?></strong>
					</div>
					<?php
				endforeach;
				?>
			</div>
		</div>
	</section>

	<!-- Subscription -->
	<section class="bg-latte">
		<div class="container">
			<div class="grid grid-2" style="align-items:center;">
				<div class="reveal">
					<span class="eyebrow" style="color:var(--amber);font-weight:700;text-transform:uppercase;letter-spacing:.08em;font-size:.8rem;"><?php esc_html_e( 'Never Run Out', 'brewcart' ); ?></span>
					<h2><?php esc_html_e( 'Coffee Subscriptions, Made Simple', 'brewcart' ); ?></h2>
					<p><?php esc_html_e( 'Choose your coffee, pick a delivery frequency, and save 10% on every order. Skip, pause, or cancel anytime.', 'brewcart' ); ?></p>
					<a href="<?php echo esc_url( home_url( '/subscriptions/' ) ); ?>" class="btn btn-primary"><?php esc_html_e( 'Start a Subscription', 'brewcart' ); ?></a>
				</div>
				<div class="card reveal" style="padding:32px;">
					<h3><?php esc_html_e( 'How it works', 'brewcart' ); ?></h3>
					<ol style="padding-left:20px;color:var(--charcoal-soft);display:flex;flex-direction:column;gap:10px;">
						<li><?php esc_html_e( 'Pick your favorite coffee & grind', 'brewcart' ); ?></li>
						<li><?php esc_html_e( 'Choose weekly, bi-weekly, or monthly delivery', 'brewcart' ); ?></li>
						<li><?php esc_html_e( 'We roast & ship right before your delivery date', 'brewcart' ); ?></li>
						<li><?php esc_html_e( 'Adjust or cancel anytime from your account', 'brewcart' ); ?></li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<!-- Newsletter -->
	<section>
		<div class="container">
			<div class="reveal text-center" style="max-width:560px;margin:0 auto;">
				<h2><?php esc_html_e( 'Join the BrewCart Club', 'brewcart' ); ?></h2>
				<p><?php esc_html_e( 'Roasting notes, brewing guides, and members-only offers — straight to your inbox.', 'brewcart' ); ?></p>
				<form class="newsletter-form-inline" id="newsletter-form-inline" style="display:flex;gap:12px;max-width:420px;margin:0 auto;flex-wrap:wrap;justify-content:center;">
					<?php wp_nonce_field( 'brewcart_nonce', 'newsletter_nonce' ); ?>
					<input type="email" name="email" required placeholder="<?php esc_attr_e( 'Your email address', 'brewcart' ); ?>" style="flex:1;min-width:220px;padding:14px 18px;border-radius:var(--radius-pill);border:1px solid rgba(43,27,20,.15);">
					<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Subscribe', 'brewcart' ); ?></button>
				</form>
			</div>
		</div>
	</section>

</main>

<?php
get_footer();
