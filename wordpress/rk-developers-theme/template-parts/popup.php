<?php
/**
 * "Interested in a Property?" pop-up (shows every N seconds, see RK Site Settings → Pop-up).
 *
 * @package RK_Developers
 */

$rk_variant = isset( $args['variant'] ) ? $args['variant'] : 'default';
$rk_close   = "document.getElementById('rk-popup-overlay').style.display='none';";
?>
	<!-- ── POPUP ── -->
	<div id="rk-popup-overlay" style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,0.55);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
		<div id="rk-popup-box" style="background:#fff;border-radius:20px;max-width:460px;width:90%;padding:0;text-align:center;position:relative;box-shadow:0 24px 60px rgba(0,0,0,0.22);animation:popupIn 0.4s cubic-bezier(.34,1.56,.64,1) both;overflow:hidden;">
			<button onclick="<?php echo esc_attr( $rk_close ); ?>" style="position:absolute;top:12px;right:14px;z-index:10;background:rgba(0,0,0,0.35);border:none;color:#fff;width:30px;height:30px;border-radius:50%;font-size:1.1rem;cursor:pointer;line-height:1;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px);transition:background 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.6)'" onmouseout="this.style.background='rgba(0,0,0,0.35)'" title="<?php esc_attr_e( 'Close', 'rk-developers' ); ?>">&times;</button>
			<div style="width:100%;height:180px;overflow:hidden;position:relative;border-radius:20px 20px 0 0;">
				<video autoplay muted loop playsinline style="width:100%;height:100%;object-fit:cover;display:block;">
					<source src="<?php echo esc_url( rk_asset( 'video/animation-1.mp4' ) ); ?>" type="video/mp4" />
				</video>
				<div style="position:absolute;inset:0;background:linear-gradient(to bottom,transparent 40%,rgba(0,0,0,0.45) 100%);"></div>
			</div>
			<div style="padding:28px 32px 32px;">
				<h3 style="font-family:'Playfair Display',serif;font-size:1.35rem;color:#1E293B;margin-bottom:8px;"><?php esc_html_e( 'Interested in a Property?', 'rk-developers' ); ?></h3>
				<?php if ( 'contact' === $rk_variant ) : ?>
				<p style="color:#64748B;font-size:0.9rem;line-height:1.6;margin-bottom:22px;"><?php echo wp_kses_post( __( 'Our property experts are available right now. Book a <strong>free site visit</strong> and find your dream home today.', 'rk-developers' ) ); ?></p>
				<div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
					<a href="tel:<?php echo esc_attr( rk_phone_link() ); ?>" style="background:linear-gradient(135deg,#F97316,#DC2626);color:#fff;padding:11px 22px;border-radius:50px;font-weight:600;font-size:0.88rem;text-decoration:none;display:inline-flex;align-items:center;gap:7px;"><i class="fas fa-phone-alt"></i> <?php esc_html_e( 'Call Us Now', 'rk-developers' ); ?></a>
					<button onclick="<?php echo esc_attr( $rk_close ); ?>" style="border:2px solid #F97316;color:#F97316;padding:11px 22px;border-radius:50px;font-weight:600;font-size:0.88rem;background:transparent;cursor:pointer;"><?php esc_html_e( 'Maybe Later', 'rk-developers' ); ?></button>
				</div>
				<?php else : ?>
				<p style="color:#64748B;font-size:0.9rem;line-height:1.6;margin-bottom:22px;"><?php echo wp_kses_post( __( 'Our property experts are available right now. Book a <strong>free site visit</strong> and find your dream home today — limited plots remaining!', 'rk-developers' ) ); ?></p>
				<div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
					<a href="<?php echo esc_url( rk_page_url( 'contact' ) ); ?>" onclick="<?php echo esc_attr( $rk_close ); ?>" style="background:linear-gradient(135deg,#F97316,#DC2626);color:#fff;padding:11px 22px;border-radius:50px;font-weight:600;font-size:0.88rem;text-decoration:none;display:inline-flex;align-items:center;gap:7px;"><?php esc_html_e( 'Book a Free Visit', 'rk-developers' ); ?> <i class="fas fa-arrow-right"></i></a>
					<a href="tel:<?php echo esc_attr( rk_phone_link() ); ?>" style="border:2px solid #F97316;color:#F97316;padding:11px 22px;border-radius:50px;font-weight:600;font-size:0.88rem;text-decoration:none;display:inline-flex;align-items:center;gap:7px;"><i class="fas fa-phone-alt"></i> <?php esc_html_e( 'Call Us', 'rk-developers' ); ?></a>
				</div>
				<?php endif; ?>
				<p style="margin-top:12px;font-size:0.76rem;color:#94A3B8;"><?php esc_html_e( 'Trusted by 1,500+ families across Bangalore', 'rk-developers' ); ?></p>
			</div>
		</div>
	</div>
	<style>
		@keyframes popupIn {
			0%   { opacity:0; transform:scale(0.7) translateY(30px); }
			100% { opacity:1; transform:scale(1) translateY(0); }
		}
	</style>
