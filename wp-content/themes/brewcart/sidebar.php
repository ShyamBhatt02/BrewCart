<?php
/**
 * Sidebar (used on blog single/archive; suppresses WooCommerce's core deprecation fallback).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_active_sidebar( 'sidebar-blog' ) ) {
	return;
}
?>
<aside class="blog-sidebar">
	<?php dynamic_sidebar( 'sidebar-blog' ); ?>
</aside>
