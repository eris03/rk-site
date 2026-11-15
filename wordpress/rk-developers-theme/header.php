<?php
/**
 * Site header: preloader, page transition, scroll progress and the navbar.
 *
 * @package RK_Developers
 */

$rk_conf = rk_layout_conf();
$rk_logo = rk_logo_url();
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php if ( $rk_conf['progress'] ) : ?>
	<!-- Scroll Progress Bar -->
	<div id="scroll-progress"></div>
	<!-- Cursor Glow -->
	<div class="cursor-glow" id="cursorGlow"></div>
<?php endif; ?>
<?php if ( $rk_conf['transition'] ) : ?>
	<!-- Page Transition Overlay -->
	<div id="page-transition">
		<div class="pt-logo-wrap">
			<img src="<?php echo esc_url( $rk_logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="pt-logo-img" onerror="this.style.display='none';this.nextElementSibling.style.display='block'" />
			<span class="pt-logo-text" style="display:none">RK</span>
		</div>
		<div class="pt-bar-wrap"><div class="pt-bar-fill" id="ptBarFill"></div></div>
		<p class="pt-tagline"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
	</div>
<?php endif; ?>
<?php if ( 'inner' === $rk_conf['preloader'] ) : ?>
	<!-- PRELOADER -->
	<div id="preloader">
		<div class="preloader-inner">
			<img src="<?php echo esc_url( $rk_logo ); ?>" alt="RK" class="preloader-logo-img" onerror="this.style.display='none';this.nextElementSibling.style.display='block'" />
			<div class="preloader-text" style="display:none">RK</div>
			<div class="preloader-bar"><div class="preloader-fill"></div></div>
		</div>
	</div>
<?php elseif ( 'logo' === $rk_conf['preloader'] ) : ?>
	<div class="preloader" id="preloader">
		<div class="preloader-logo">
			<img src="<?php echo esc_url( $rk_logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" onerror="this.style.display='none';this.nextElementSibling.style.display='block'" />
			<span style="display:none;font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;"><span style="color:#F97316;">RK</span><span style="color:#fff;margin-left:6px;">Developers</span></span>
		</div>
		<div class="preloader-bar"><div class="preloader-fill"></div></div>
	</div>
<?php endif; ?>

	<!-- NAVBAR -->
	<nav class="navbar<?php echo $rk_conf['nav_scrolled'] ? ' scrolled' : ''; ?>" id="navbar">
		<div class="nav-container">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-logo">
				<?php
				$rk_nav_logo = '';
				if ( $rk_conf['nav_logo'] ) {
					$rk_p = rk_get_project( $rk_conf['nav_logo'] );
					if ( $rk_p && rk_project_meta( $rk_p->ID, 'logo' ) ) {
						$rk_nav_logo = wp_get_attachment_image_url( (int) rk_project_meta( $rk_p->ID, 'logo' ), 'medium_large' );
					}
				}
				if ( $rk_nav_logo ) :
					?>
				<img src="<?php echo esc_url( $rk_nav_logo ); ?>" alt="<?php echo esc_attr( $rk_p->post_title ); ?>" class="nav-logo-img" style="height:80px;width:auto;max-width:220px;object-fit:contain;" onerror="this.style.display='none';document.querySelector('.nav-logo-fallback').style.display='flex'" />
				<?php else : ?>
				<img src="<?php echo esc_url( $rk_logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="nav-logo-img" onerror="this.style.display='none';document.querySelector('.nav-logo-fallback').style.display='flex'" />
				<?php endif; ?>
				<span class="nav-logo-fallback" style="display:none"><span class="logo-rk">RK</span><span class="logo-text">Developers</span></span>
			</a>

			<?php
			$rk_cta_url = rk_page_url( 'contact' );
			if ( $rk_conf['cta_query'] ) {
				$rk_cta_url = add_query_arg( 'project', $rk_conf['cta_query'], $rk_cta_url );
			}
			rk_primary_nav( $rk_conf['cta_text'], $rk_cta_url );
			?>

			<button class="nav-toggle" id="navToggle" aria-label="<?php esc_attr_e( 'Toggle navigation', 'rk-developers' ); ?>">
				<span></span><span></span><span></span>
			</button>
		</div>
	</nav>
