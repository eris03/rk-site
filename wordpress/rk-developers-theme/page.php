<?php
/**
 * Default page template (pages without an "RK –" template, e.g. Privacy Policy).
 *
 * @package RK_Developers
 */

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<section class="page-hero">
		<div class="page-hero-overlay"></div>
		<div class="page-hero-content" data-aos="fade-up">
			<div class="breadcrumb">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'rk-developers' ); ?></a> <i class="fas fa-chevron-right"></i> <?php the_title(); ?>
			</div>
			<h1><?php the_title(); ?></h1>
		</div>
	</section>
	<section class="rk-entry-section">
		<div class="container rk-entry-content">
			<?php the_content(); ?>
		</div>
	</section>
	<?php
endwhile;
get_footer();
