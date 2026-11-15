<?php
/**
 * Fallback template (blog posts, archives, search).
 *
 * @package RK_Developers
 */

get_header();
?>
<section class="page-hero">
	<div class="page-hero-overlay"></div>
	<div class="page-hero-content" data-aos="fade-up">
		<h1><?php echo is_singular() ? esc_html( get_the_title() ) : wp_kses_post( get_the_archive_title() ? get_the_archive_title() : get_bloginfo( 'name' ) ); ?></h1>
	</div>
</section>
<section class="rk-entry-section">
	<div class="container rk-entry-content">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				if ( is_singular() ) {
					the_content();
				} else {
					printf( '<article class="rk-entry"><h2><a href="%s">%s</a></h2>%s</article>', esc_url( get_permalink() ), esc_html( get_the_title() ), wp_kses_post( get_the_excerpt() ) );
				}
			endwhile;
			the_posts_pagination();
		else :
			echo '<p>' . esc_html__( 'Nothing found.', 'rk-developers' ) . '</p>';
		endif;
		?>
	</div>
</section>
<?php
get_footer();
