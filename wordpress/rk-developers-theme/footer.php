<?php
/**
 * Site footer.
 *
 * @package RK_Developers
 */

$rk_logo = rk_logo_url();
$rk_name = get_bloginfo( 'name' );
?>
	<!-- FOOTER -->
	<footer class="footer">
		<div class="footer-top">
			<div class="container">
				<div class="footer-grid">
					<div class="footer-col footer-brand">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-logo-wrap">
							<img src="<?php echo esc_url( $rk_logo ); ?>" alt="<?php echo esc_attr( $rk_name ); ?>" class="footer-logo-img"
								onerror="this.style.display='none';this.nextElementSibling.style.display='flex'" />
							<span style="display:none" class="footer-logo-text">
								<span class="logo-rk">RK</span><span class="logo-text">Developers</span>
							</span>
						</a>
						<p><?php echo esc_html( rk_replace_setting_tags( rk_setting( 'footer_tagline' ) ) ); ?></p>
						<div class="footer-socials">
							<?php
							$rk_socials = array(
								'facebook'  => 'fab fa-facebook-f',
								'instagram' => 'fab fa-instagram',
								'youtube'   => 'fab fa-youtube',
								'linkedin'  => 'fab fa-linkedin-in',
								'x'         => 'fab fa-x-twitter',
							);
							foreach ( $rk_socials as $rk_key => $rk_icon ) {
								$rk_url = rk_setting( $rk_key );
								if ( $rk_url ) {
									printf( '<a href="%s" target="_blank" rel="noopener" aria-label="%s"><i class="%s"></i></a>' . "\n", esc_url( $rk_url ), esc_attr( ucfirst( $rk_key ) ), esc_attr( $rk_icon ) );
								}
							}
							?>
						</div>
					</div>
					<div class="footer-col">
						<h4><?php esc_html_e( 'Quick Links', 'rk-developers' ); ?></h4>
						<ul>
							<?php rk_footer_nav(); ?>
						</ul>
					</div>
					<div class="footer-col">
						<h4><?php esc_html_e( 'Contact Us', 'rk-developers' ); ?></h4>
						<div class="footer-contact-item"><i class="fas fa-phone-alt"></i><a href="tel:<?php echo esc_attr( rk_phone_link() ); ?>"><?php echo esc_html( rk_setting( 'phone' ) ); ?></a></div>
						<div class="footer-contact-item"><i class="fas fa-envelope"></i><a href="mailto:<?php echo esc_attr( rk_setting( 'email' ) ); ?>"><?php echo esc_html( rk_setting( 'email' ) ); ?></a></div>
						<div class="footer-contact-item" style="align-items:flex-start;flex-wrap:wrap;gap:8px;">
							<i class="fas fa-map-marker-alt" style="margin-top:3px;flex-shrink:0;"></i>
							<div style="flex:1;min-width:0;">
								<span style="display:block;font-size:0.82rem;line-height:1.5;"><?php echo esc_html( rk_address_inline() ); ?></span>
								<?php if ( rk_setting( 'maps_url' ) ) : ?>
								<a href="<?php echo esc_url( rk_setting( 'maps_url' ) ); ?>" target="_blank" rel="noopener" title="<?php esc_attr_e( 'Open in Google Maps', 'rk-developers' ); ?>" class="rk-footer-map"
									style="display:inline-block;margin-top:8px;border-radius:10px;overflow:hidden;border:2px solid rgba(249,115,22,0.55);box-shadow:0 4px 14px rgba(0,0,0,0.35);transition:transform 0.2s,box-shadow 0.2s;cursor:pointer;"
									onmouseover="this.style.transform='scale(1.04)';this.style.boxShadow='0 6px 20px rgba(249,115,22,0.4)'"
									onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 4px 14px rgba(0,0,0,0.35)'">
									<img src="<?php echo esc_url( rk_map_image_url() ); ?>" alt="<?php esc_attr_e( 'RK Developers location map', 'rk-developers' ); ?>" style="width:180px;height:110px;object-fit:cover;display:block;" />
								</a>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="footer-bottom">
			<div class="container">
				<p>© <span id="year"><?php echo esc_html( wp_date( 'Y' ) ); ?></span> <?php echo esc_html( rk_setting( 'copyright' ) ); ?></p>
			</div>
		</div>
	</footer>

	<button class="back-to-top" id="backToTop" aria-label="<?php esc_attr_e( 'Back to top', 'rk-developers' ); ?>"><i class="fas fa-chevron-up"></i></button>

	<?php
	$rk_popup = rk_layout_conf( 'popup' );
	if ( $rk_popup && rk_setting( 'popup_enabled' ) ) {
		get_template_part( 'template-parts/popup', null, array( 'variant' => $rk_popup ) );
	}
	wp_footer();
	?>
</body>
</html>
