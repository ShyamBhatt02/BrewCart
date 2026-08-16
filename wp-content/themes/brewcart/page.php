<?php
/**
 * Default page template.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<main class="site-main">
	<div class="container" style="padding:56px 0;">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class(); ?>>
				<?php if ( ! is_page( array( 'cart', 'checkout', 'my-account' ) ) ) : ?>
					<h1 class="page-title reveal"><?php the_title(); ?></h1>
				<?php endif; ?>
				<div class="page-content reveal">
					<?php the_content(); ?>
				</div>
			</article>
			<?php
		endwhile;
		?>
	</div>
</main>
<?php
get_footer();
