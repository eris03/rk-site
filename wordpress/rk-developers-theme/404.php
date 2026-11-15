<?php
/**
 * 404 page.
 *
 * @package RK_Developers
 */

get_header();
?>
<section class="page-hero">
	<div class="page-hero-overlay"></div>
	<div class="page-hero-content" data-aos="fade-up">
		<h1><?php esc_html_e( 'Page not', 'rk-developers' ); ?> <span><?php esc_html_e( 'found', 'rk-developers' ); ?></span></h1>
		<p><?php esc_html_e( 'The page you are looking for has moved or no longer exists.', 'rk-developers' ); ?></p>
	</div>
</section>
<section class="rk-entry-section">
	<div class="container rk-entry-content" style="text-align:center;">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn-primary orange"><?php esc_html_e( 'Back to Home', 'rk-developers' ); ?> <i class="fas fa-arrow-right"></i></a>
	</div>
</section>
<?php
get_footer();
