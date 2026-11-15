<?php
/**
 * Simple project page, used for projects that have no detail page of their own
 * but have text in the editor.
 *
 * @package RK_Developers
 */

get_header();
while ( have_posts() ) :
	the_post();
	$rk_id   = get_the_ID();
	$rk_line = implode( ' · ', array_filter( array( rk_project_meta( $rk_id, 'size' ), rk_project_meta( $rk_id, 'location' ) ) ) );
	$rk_key  = rk_register_gallery( get_post() );
	?>
	<section class="page-hero">
		<div class="page-hero-overlay"></div>
		<div class="page-hero-content" data-aos="fade-up">
			<div class="breadcrumb">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'rk-developers' ); ?></a> <i class="fas fa-chevron-right"></i>
				<a href="<?php echo esc_url( rk_page_url( 'projects' ) ); ?>"><?php esc_html_e( 'Projects', 'rk-developers' ); ?></a> <i class="fas fa-chevron-right"></i> <?php the_title(); ?>
			</div>
			<h1><?php the_title(); ?></h1>
			<?php if ( $rk_line ) : ?>
			<p><?php echo esc_html( $rk_line ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<section class="rk-entry-section">
		<div class="container rk-entry-content">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'large', array( 'style' => 'width:100%;height:auto;border-radius:20px;margin-bottom:28px;' ) ); ?>
			<?php endif; ?>
			<?php if ( rk_project_meta( $rk_id, 'price' ) ) : ?>
				<p><strong><?php esc_html_e( 'Price:', 'rk-developers' ); ?></strong> <?php echo esc_html( rk_project_meta( $rk_id, 'price' ) ); ?></p>
			<?php endif; ?>
			<?php the_content(); ?>
			<?php if ( $rk_key ) : ?>
				<p><button type="button" class="btn-primary green" onclick="openLightbox('<?php echo esc_js( $rk_key ); ?>')"><i class="fas fa-images"></i> <?php esc_html_e( 'View Photos', 'rk-developers' ); ?></button></p>
			<?php endif; ?>
			<p style="margin-top:28px;"><a class="btn-primary orange" href="<?php echo esc_url( add_query_arg( 'project', get_post_field( 'post_name' ), rk_page_url( 'contact' ) ) ); ?>"><?php esc_html_e( 'Enquire Now', 'rk-developers' ); ?> <i class="fas fa-arrow-right"></i></a></p>
		</div>
	</section>
	<?php
endwhile;
get_footer();
