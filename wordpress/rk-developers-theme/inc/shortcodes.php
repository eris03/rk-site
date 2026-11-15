<?php
/**
 * Shortcodes used inside the page content.
 *
 * Site settings (Appearance → Customize → RK Site Settings):
 *   [rk_phone] [rk_phone_link] [rk_whatsapp] [rk_email] [rk_address] [rk_address_lines]
 *   [rk_hours] [rk_hours_lines] [rk_since] [rk_logo_url] [rk_maps_url] [rk_contact_url]
 *
 * Live sections built from Projects:
 *   [rk_ongoing_projects]                 home page "Ongoing Developments" cards
 *   [rk_projects_grid]                    "Projects We've Delivered" grid + photo galleries
 *   [rk_project_slider project="slug"]    photo slideshow of one project
 *   [rk_contact_form]                     the contact form
 *
 * @package RK_Developers
 */

defined( 'ABSPATH' ) || exit;

/** Values of the simple setting shortcodes. */
function rk_setting_tags() {
	return array(
		'rk_phone'         => esc_html( rk_setting( 'phone' ) ),
		'rk_phone_link'    => esc_attr( rk_phone_link() ),
		'rk_whatsapp'      => esc_attr( rk_whatsapp_number() ),
		'rk_email'         => esc_html( rk_setting( 'email' ) ),
		'rk_address'       => esc_html( rk_address_inline() ),
		'rk_address_lines' => implode( '<br />', array_map( 'esc_html', rk_address_lines() ) ),
		'rk_hours'         => esc_html( implode( ', ', rk_hours_lines() ) ),
		'rk_hours_lines'   => implode( '<br />', array_map( 'esc_html', rk_hours_lines() ) ),
		'rk_since'         => esc_html( rk_setting( 'founded_year' ) ),
		'rk_logo_url'      => esc_url( rk_logo_url() ),
		'rk_maps_url'      => esc_url( rk_setting( 'maps_url' ) ),
		'rk_contact_url'   => esc_url( rk_page_url( 'contact' ) ),
	);
}

/**
 * Replace the setting tags before WordPress runs shortcodes, so they also work
 * inside HTML attributes, e.g. href="tel:[rk_phone_link]".
 */
function rk_replace_setting_tags( $text ) {
	if ( false === strpos( (string) $text, '[rk_' ) ) {
		return $text;
	}
	$map = array();
	foreach ( rk_setting_tags() as $tag => $value ) {
		$map[ '[' . $tag . ']' ] = $value;
	}
	return strtr( $text, $map );
}
add_filter( 'the_content', 'rk_replace_setting_tags', 10 );
add_filter( 'widget_text', 'rk_replace_setting_tags', 10 );
add_filter( 'widget_block_content', 'rk_replace_setting_tags', 10 );

add_action(
	'init',
	function () {
		foreach ( array_keys( rk_setting_tags() ) as $tag ) {
			add_shortcode(
				$tag,
				function () use ( $tag ) {
					$tags = rk_setting_tags();
					return $tags[ $tag ];
				}
			);
		}
		add_shortcode( 'rk_ongoing_projects', 'rk_sc_ongoing_projects' );
		add_shortcode( 'rk_projects_grid', 'rk_sc_projects_grid' );
		add_shortcode( 'rk_project_slider', 'rk_sc_project_slider' );
		add_shortcode( 'rk_contact_form', 'rk_sc_contact_form' );
	}
);

/* ─────────────────────── Galleries (shared data) ─────────────────────── */

/** Register a gallery for the lightbox and return its key. */
function rk_register_gallery( $post ) {
	global $rk_galleries;
	if ( ! is_array( $rk_galleries ) ) {
		$rk_galleries = array();
	}
	$images = rk_project_gallery_urls( $post->ID, '1536x1536' );
	if ( ! $images ) {
		return '';
	}
	// Make sure the lightbox is available on any page that shows the grid.
	wp_enqueue_style( 'rk-gallery', rk_asset( 'css/gallery.css' ), array(), RK_THEME_VERSION );
	wp_enqueue_script( 'rk-gallery', rk_asset( 'js/gallery.js' ), array(), RK_THEME_VERSION, true );
	$key                  = $post->post_name;
	$rk_galleries[ $key ] = array(
		'title'  => get_the_title( $post ),
		'images' => $images,
	);
	return $key;
}

/** Print gallery data + the lightbox markup once, in the footer. */
add_action(
	'wp_footer',
	function () {
		global $rk_galleries;
		if ( empty( $rk_galleries ) && ! in_array( 'gallery', (array) rk_layout_conf( 'js' ), true ) ) {
			return;
		}
		get_template_part( 'template-parts/gallery-modal' );
	},
	5
);
add_action(
	'wp_print_footer_scripts',
	function () {
		global $rk_galleries;
		if ( wp_script_is( 'rk-gallery', 'enqueued' ) ) {
			wp_add_inline_script( 'rk-gallery', 'window._galleries = ' . wp_json_encode( empty( $rk_galleries ) ? new stdClass() : $rk_galleries ) . ';', 'before' );
		}
	},
	1
);

/* ─────────────────────── [rk_ongoing_projects] ─────────────────────── */

function rk_sc_ongoing_projects() {
	$projects = rk_get_projects(
		array(
			'meta_key'   => '_rk_featured',
			'meta_value' => '1',
		)
	);
	if ( ! $projects ) {
		return '';
	}
	ob_start();
	echo '<div class="projects-duo">';
	foreach ( $projects as $i => $p ) {
		$accent   = 'orange' === rk_project_meta( $p->ID, 'accent' ) ? 'orange' : 'green';
		$link     = rk_project_link( $p );
		$title    = get_the_title( $p );
		$img      = get_the_post_thumbnail_url( $p, 'large' );
		$badge    = rk_project_meta( $p->ID, 'card_badge' );
		$type     = rk_project_meta( $p->ID, 'card_type' );
		$type_ico = rk_project_meta( $p->ID, 'card_type_icon' );
		$loc      = rk_project_meta( $p->ID, 'card_location' );
		$loc      = $loc ? $loc : rk_project_meta( $p->ID, 'location' );
		$desc     = rk_project_meta( $p->ID, 'description' );
		$price    = rk_project_meta( $p->ID, 'price' );
		$features = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) rk_project_meta( $p->ID, 'card_features' ) ) ) );
		$tag      = $link ? 'a' : 'div';
		$overlay  = 'orange' === $accent ? ' style="background:linear-gradient(135deg,rgba(249,115,22,0.85),rgba(220,38,38,0.85));"' : '';
		?>
		<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo $link ? 'href="' . esc_url( $link ) . '" ' : ''; ?>class="psc-card psc-card-link" data-aos="<?php echo 0 === $i % 2 ? 'fade-right' : 'fade-left'; ?>" data-aos-delay="100" style="text-decoration:none;display:block;cursor:pointer;">
			<div class="psc-image">
				<?php if ( $img ) : ?>
				<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $title ); ?>" />
				<?php endif; ?>
				<?php if ( $badge ) : ?>
				<span class="psc-badge <?php echo esc_attr( $accent ); ?>"><?php echo esc_html( $badge ); ?></span>
				<?php endif; ?>
				<div class="psc-hover-overlay"<?php echo $overlay; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><i class="fas fa-arrow-right"></i> <?php esc_html_e( 'Explore Project', 'rk-developers' ); ?></div>
			</div>
			<div class="psc-body">
				<?php if ( $type ) : ?>
				<div class="psc-type <?php echo esc_attr( $accent ); ?>"><i class="<?php echo esc_attr( $type_ico ? $type_ico : 'fas fa-map-pin' ); ?>"></i> <?php echo esc_html( $type ); ?></div>
				<?php endif; ?>
				<h3><?php echo esc_html( $title ); ?></h3>
				<?php if ( $loc ) : ?>
				<p class="psc-loc"><i class="fas fa-map-marker-alt"></i> <?php echo esc_html( $loc ); ?></p>
				<?php endif; ?>
				<?php if ( $desc ) : ?>
				<p class="psc-desc"><?php echo esc_html( $desc ); ?></p>
				<?php endif; ?>
				<?php if ( $features ) : ?>
				<div class="psc-features">
					<?php
					foreach ( $features as $line ) {
						$parts = array_map( 'trim', explode( '|', $line, 2 ) );
						$icon  = count( $parts ) > 1 ? $parts[0] : 'fas fa-check';
						$text  = str_replace( '{price}', $price, count( $parts ) > 1 ? $parts[1] : $parts[0] );
						echo '<span><i class="' . esc_attr( $icon ) . '"></i> ' . esc_html( $text ) . '</span>';
					}
					?>
				</div>
				<?php endif; ?>
				<span class="btn-primary <?php echo esc_attr( $accent ); ?>" style="display:inline-flex;align-items:center;gap:8px;"><?php echo esc_html( sprintf( /* translators: project name */ __( 'View %s', 'rk-developers' ), $title ) ); ?> <i class="fas fa-arrow-right"></i></span>
			</div>
		</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php
	}
	echo '</div>';
	return ob_get_clean();
}

/* ─────────────────────── [rk_projects_grid] ─────────────────────── */

function rk_sc_projects_grid( $atts ) {
	$atts     = shortcode_atts( array( 'status' => '' ), $atts, 'rk_projects_grid' );
	$args     = array();
	if ( in_array( $atts['status'], array( 'ongoing', 'delivered' ), true ) ) {
		$args = array(
			'meta_key'   => '_rk_status',
			'meta_value' => $atts['status'],
		);
	}
	$projects = rk_get_projects( $args );
	ob_start();
	echo '<div class="rk-projects-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;margin-top:52px;">';
	foreach ( $projects as $i => $p ) {
		$title   = get_the_title( $p );
		$ongoing = 'ongoing' === rk_project_meta( $p->ID, 'status' );
		$icon    = rk_project_meta( $p->ID, 'icon' );
		$icon    = $icon ? $icon : 'fas fa-map-marked-alt';
		$line    = implode( ' · ', array_filter( array( rk_project_meta( $p->ID, 'size' ), rk_project_meta( $p->ID, 'location' ) ) ) );
		$link    = rk_project_link( $p );
		$key     = ( $ongoing && $link ) ? '' : rk_register_gallery( $p );
		$delay   = ( $i + 1 ) * 20;

		$classes = 'award-card' . ( $key ? ' proj-gallery-card' : '' );
		$style   = 'text-align:left;padding:24px;' . ( $key ? 'cursor:pointer;' : '' ) . ( $ongoing ? 'border-color:#22C55E;background:#F0FDF4;' : '' );
		$extra   = $key ? sprintf( ' data-gallery="%1$s" onclick="openLightbox(\'%1$s\')"', esc_attr( $key ) ) : '';

		printf( '<div class="%s" data-aos="fade-up" data-aos-delay="%d" style="%s"%s>', esc_attr( $classes ), (int) $delay, esc_attr( $style ), $extra ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		printf( '<i class="%s" style="font-size:1.4rem;margin-bottom:10px;%s"></i>', esc_attr( $icon ), $ongoing ? 'color:#22C55E;' : '' );
		echo '<h4 style="font-size:0.95rem;margin-bottom:5px;">' . esc_html( $title );
		if ( $ongoing ) {
			echo ' <span style="font-size:0.68rem;background:#DCFCE7;color:#15803D;font-weight:700;padding:2px 8px;border-radius:20px;margin-left:6px;">' . esc_html__( 'Ongoing', 'rk-developers' ) . '</span>';
		} elseif ( $key ) {
			echo ' <span style="font-size:0.68rem;color:#22C55E;font-weight:600;margin-left:6px;"><i class="fas fa-images"></i> ' . esc_html__( 'View Photos', 'rk-developers' ) . '</span>';
		}
		echo '</h4>';
		$has_link_line = $link && ! $key;
		if ( $line ) {
			printf( '<p style="font-size:0.82rem;color:#64748B;margin:%s;">%s</p>', $has_link_line ? '0 0 8px' : '0', esc_html( $line ) );
		}
		if ( $has_link_line ) {
			printf(
				'<a href="%s" style="font-size:0.78rem;color:%s;font-weight:600;text-decoration:none;">%s</a>',
				esc_url( $link ),
				$ongoing ? '#22C55E' : '#F97316',
				esc_html( sprintf( /* translators: project name */ __( 'View %s →', 'rk-developers' ), $title ) )
			);
		}
		echo '</div>';
	}
	echo '</div>';
	return ob_get_clean();
}

/* ─────────────────────── [rk_project_slider] ─────────────────────── */

/** Colour schemes of the two original slideshows. */
function rk_slider_styles() {
	return array(
		'mg' => array(
			'shadow'    => 'rgba(26,58,42,0.25)',
			'badge_bg'  => 'rgba(15,36,25,0.7)',
			'badge_fg'  => '#F0C060',
			'arrow'     => 'rgba(15,36,25,0.65)',
			'arrow_hov' => 'rgba(15,36,25,0.9)',
			'progress'  => 'var(--mg-gold)',
			'thumbs_bg' => 'rgba(15,36,25,0.92)',
			'scroll'    => 'rgba(212,168,67,0.4)',
			'active'    => '#D4A843',
		),
		'av' => array(
			'shadow'    => 'rgba(15,23,42,0.22)',
			'badge_bg'  => 'rgba(15,23,42,0.7)',
			'badge_fg'  => '#93C5FD',
			'arrow'     => 'rgba(15,23,42,0.65)',
			'arrow_hov' => 'rgba(15,23,42,0.9)',
			'progress'  => '#1D4ED8',
			'thumbs_bg' => 'rgba(15,23,42,0.92)',
			'scroll'    => 'rgba(29,78,216,0.4)',
			'active'    => '#1D4ED8',
		),
	);
}

function rk_sc_project_slider( $atts ) {
	$atts    = shortcode_atts(
		array(
			'project' => '',
			'style'   => '',
		),
		$atts,
		'rk_project_slider'
	);
	$project = rk_get_project( sanitize_title( $atts['project'] ) );
	if ( ! $project ) {
		return '';
	}
	$images = rk_project_gallery_urls( $project->ID, '1536x1536' );
	$thumbs = array();
	foreach ( rk_project_gallery_ids( $project->ID ) as $id ) {
		$t = wp_get_attachment_image_url( $id, 'thumbnail' );
		if ( $t ) {
			$thumbs[] = $t;
		}
	}
	if ( ! $images ) {
		return '';
	}
	$styles = rk_slider_styles();
	$pre    = in_array( $atts['style'], array( 'mg', 'av' ), true ) ? $atts['style'] : ( 'metro-greens' === $project->post_name ? 'mg' : 'av' );
	$s      = $styles[ $pre ];
	$title  = get_the_title( $project );
	$n      = count( $images );

	wp_add_inline_script( 'rk-slider', 'window.RK_SLIDERS = window.RK_SLIDERS || {}; window.RK_SLIDERS[' . wp_json_encode( $pre ) . '] = ' . wp_json_encode( array( 'images' => $images, 'active' => $s['active'] ) ) . ';', 'before' );

	ob_start();
	?>
	<div id="<?php echo esc_attr( $pre ); ?>Gallery" class="rk-slider" data-prefix="<?php echo esc_attr( $pre ); ?>" style="position:relative;border-radius:20px;overflow:hidden;box-shadow:0 16px 48px <?php echo esc_attr( $s['shadow'] ); ?>;">
		<div style="position:relative;height:380px;overflow:hidden;">
			<img id="<?php echo esc_attr( $pre ); ?>MainImg" src="<?php echo esc_url( $images[0] ); ?>" alt="<?php echo esc_attr( $title ); ?>"
				style="width:100%;height:100%;object-fit:cover;transition:opacity 0.6s ease,transform 0.8s cubic-bezier(0.4,0,0.2,1);display:block;" />
			<div style="position:absolute;top:14px;right:14px;background:<?php echo esc_attr( $s['badge_bg'] ); ?>;color:<?php echo esc_attr( $s['badge_fg'] ); ?>;font-size:0.75rem;font-weight:700;padding:5px 12px;border-radius:50px;backdrop-filter:blur(4px);">
				<span id="<?php echo esc_attr( $pre ); ?>CurNum">1</span> / <span id="<?php echo esc_attr( $pre ); ?>TotalNum"><?php echo (int) $n; ?></span>
			</div>
			<?php foreach ( array( array( -1, 'left', '&#8249;' ), array( 1, 'right', '&#8250;' ) ) as $b ) : ?>
			<button type="button" onclick="<?php echo esc_attr( $pre ); ?>Step(<?php echo (int) $b[0]; ?>)" style="position:absolute;<?php echo esc_attr( $b[1] ); ?>:10px;top:50%;transform:translateY(-50%);background:<?php echo esc_attr( $s['arrow'] ); ?>;border:none;color:#fff;width:38px;height:38px;border-radius:50%;font-size:1.1rem;cursor:pointer;backdrop-filter:blur(4px);transition:background 0.2s;display:flex;align-items:center;justify-content:center;" onmouseover="this.style.background='<?php echo esc_attr( $s['arrow_hov'] ); ?>'" onmouseout="this.style.background='<?php echo esc_attr( $s['arrow'] ); ?>'"><?php echo $b[2]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			<?php endforeach; ?>
			<div style="position:absolute;bottom:0;left:0;right:0;height:3px;background:rgba(255,255,255,0.2);">
				<div id="<?php echo esc_attr( $pre ); ?>ProgressBar" style="height:100%;background:<?php echo esc_attr( $s['progress'] ); ?>;width:<?php echo esc_attr( round( 100 / $n, 1 ) ); ?>%;transition:width 0.4s ease;"></div>
			</div>
		</div>
		<div id="<?php echo esc_attr( $pre ); ?>Thumbs" style="display:flex;gap:6px;padding:10px;background:<?php echo esc_attr( $s['thumbs_bg'] ); ?>;overflow-x:auto;scrollbar-width:thin;scrollbar-color:<?php echo esc_attr( $s['scroll'] ); ?> transparent;">
			<?php foreach ( $thumbs as $i => $t ) : ?>
			<img src="<?php echo esc_url( $t ); ?>" onclick="<?php echo esc_attr( $pre ); ?>GoTo(<?php echo (int) $i; ?>)" class="<?php echo esc_attr( $pre ); ?>-thumb<?php echo 0 === $i ? ' ' . esc_attr( $pre ) . '-thumb-active' : ''; ?>" alt="<?php echo (int) ( $i + 1 ); ?>" style="width:62px;height:46px;object-fit:cover;border-radius:8px;cursor:pointer;border:2px solid <?php echo 0 === $i ? esc_attr( $s['active'] ) : 'transparent'; ?>;opacity:<?php echo 0 === $i ? '1' : '0.55'; ?>;<?php echo 0 === $i ? 'transform:scale(1.08);' : ''; ?>flex-shrink:0;transition:all 0.3s ease;" />
			<?php endforeach; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/* ─────────────────────── [rk_contact_form] ─────────────────────── */

function rk_sc_contact_form() {
	ob_start();
	get_template_part( 'template-parts/contact-form' );
	return ob_get_clean();
}
