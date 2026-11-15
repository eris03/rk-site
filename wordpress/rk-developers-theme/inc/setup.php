<?php
/**
 * Theme supports, menus, page layouts and asset loading.
 *
 * @package RK_Developers
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'rk-developers', RK_THEME_DIR . '/languages' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 512,
				'width'       => 512,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);
		register_nav_menus(
			array(
				'primary' => __( 'Main menu (top navigation bar)', 'rk-developers' ),
				'footer'  => __( 'Footer "Quick Links"', 'rk-developers' ),
			)
		);
	}
);

/**
 * Per-layout configuration. A layout is chosen by the page template
 * (Page → Template) and decides which styles, scripts and header extras load,
 * mirroring what each page of the original static site loaded.
 */
function rk_layouts() {
	$base = array(
		'css'          => array( 'aos', 'style', 'animations' ),
		'js'           => array( 'aos', 'main', 'site', 'widgets' ),
		'transition'   => true,   // full-screen page transition overlay.
		'progress'     => true,   // scroll progress bar + cursor glow.
		'preloader'    => false,  // false | 'inner' | 'logo'.
		'popup'        => false,  // false | 'default' | 'contact'.
		'nav_scrolled' => true,
		'cta_text'     => __( 'Enquire Now', 'rk-developers' ),
		'cta_query'    => '',
		'nav_logo'     => '',
	);
	$layouts = array(
		'home'         => array(
			'css'          => array( 'aos', 'swiper', 'style', 'animations', 'gallery' ),
			'js'           => array( 'aos', 'swiper', 'main', 'site', 'widgets', 'gallery' ),
			'preloader'    => 'inner',
			'popup'        => 'default',
			'nav_scrolled' => false,
			'cta_text'     => __( 'Book a Visit', 'rk-developers' ),
		),
		'about'        => array(
			'css'   => array( 'aos', 'swiper', 'style', 'animations' ),
			'js'    => array( 'aos', 'swiper', 'main', 'site', 'widgets' ),
			'popup' => 'default',
		),
		'projects'     => array(
			'css'        => array( 'aos', 'style', 'gallery' ),
			'js'         => array( 'aos', 'main', 'site', 'widgets', 'transitions', 'gallery' ),
			'transition' => false,
			'progress'   => false,
			'cta_text'   => __( 'Book a Visit', 'rk-developers' ),
		),
		'contact'      => array(
			'css'       => array( 'aos', 'style', 'animations' ),
			'js'        => array( 'aos', 'swiper', 'main', 'site', 'widgets' ),
			'preloader' => 'logo',
			'popup'     => 'contact',
		),
		'metro-greens' => array(
			'js'        => array( 'aos', 'main', 'site', 'widgets', 'slider' ),
			'popup'     => 'default',
			'cta_query' => 'metro-greens',
			'nav_logo'  => 'metro-greens',
		),
		'rk-avenue'    => array(
			'js'        => array( 'aos', 'main', 'site', 'widgets', 'slider' ),
			'popup'     => 'default',
			'cta_query' => 'rk-avenue',
		),
		'rk-revenue'   => array(
			'css'        => array( 'aos', 'style' ),
			'transition' => false,
			'progress'   => false,
			'cta_query'  => 'rk-revenue',
		),
	);
	$layouts = apply_filters( 'rk_layouts', $layouts );
	$out     = array();
	foreach ( $layouts as $key => $conf ) {
		$out[ $key ] = array_merge( $base, $conf );
	}
	$out['default'] = $base;
	$out['project'] = array_merge( $base, array( 'css' => array( 'aos', 'style', 'animations', 'gallery' ), 'js' => array( 'aos', 'main', 'site', 'widgets', 'gallery' ) ) );
	return $out;
}

/** Configuration of the layout used by the current request. */
function rk_layout_conf( $key = null ) {
	$layouts = rk_layouts();
	$layout  = rk_current_layout();
	$conf    = isset( $layouts[ $layout ] ) ? $layouts[ $layout ] : $layouts['default'];
	if ( null === $key ) {
		return $conf;
	}
	return isset( $conf[ $key ] ) ? $conf[ $key ] : null;
}

/**
 * Styles and scripts. Everything is self-hosted inside the theme (no CDN).
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		$v      = RK_THEME_VERSION;
		$layout = rk_current_layout();
		$conf   = rk_layout_conf();

		wp_enqueue_style( 'rk-fonts', rk_asset( 'vendor/fonts/fonts.css' ), array(), $v );
		wp_enqueue_style( 'rk-fontawesome', rk_asset( 'vendor/fontawesome/css/all.min.css' ), array(), '6.5.1' );

		$styles = array(
			'aos'        => array( 'vendor/aos/aos.css', '2.3.4' ),
			'swiper'     => array( 'vendor/swiper/swiper-bundle.min.css', '11' ),
			'style'      => array( 'css/style.css', $v ),
			'animations' => array( 'css/animations.css', $v ),
			'gallery'    => array( 'css/gallery.css', $v ),
		);
		$deps = array( 'rk-fonts', 'rk-fontawesome' );
		foreach ( $conf['css'] as $key ) {
			if ( isset( $styles[ $key ] ) ) {
				wp_enqueue_style( 'rk-' . $key, rk_asset( $styles[ $key ][0] ), $deps, $styles[ $key ][1] );
				$deps = array( 'rk-' . $key );
			}
		}
		if ( file_exists( RK_THEME_DIR . '/assets/css/pages/' . $layout . '.css' ) ) {
			wp_enqueue_style( 'rk-page-' . $layout, rk_asset( 'css/pages/' . $layout . '.css' ), $deps, $v );
		}
		// Small fixes for WordPress itself (admin bar offset, editor content).
		wp_enqueue_style( 'rk-wp', rk_asset( 'css/wp.css' ), array(), $v );

		$scripts = array(
			'aos'         => array( 'vendor/aos/aos.js', array(), '2.3.4' ),
			'swiper'      => array( 'vendor/swiper/swiper-bundle.min.js', array(), '11' ),
			'main'        => array( 'js/main.js', array(), $v ),
			'site'        => array( 'js/site.js', array(), $v ),
			'widgets'     => array( 'js/widgets.js', array(), $v ),
			'transitions' => array( 'js/transitions.js', array(), $v ),
			'gallery'     => array( 'js/gallery.js', array(), $v ),
			'slider'      => array( 'js/slider.js', array(), $v ),
		);
		$prev = array();
		foreach ( $conf['js'] as $key ) {
			if ( isset( $scripts[ $key ] ) ) {
				wp_enqueue_script( 'rk-' . $key, rk_asset( $scripts[ $key ][0] ), $prev, $scripts[ $key ][2], true );
				$prev = array( 'rk-' . $key );
			}
		}
		if ( file_exists( RK_THEME_DIR . '/assets/js/pages/' . $layout . '.js' ) ) {
			wp_enqueue_script( 'rk-page-' . $layout, rk_asset( 'js/pages/' . $layout . '.js' ), $prev, $v, true );
		}

		// Settings and URLs the scripts need (replaces every hard-coded phone/e-mail in JS).
		$first = reset( $conf['js'] );
		wp_add_inline_script(
			'rk-' . $first,
			'window.RK_THEME = ' . wp_json_encode( rk_js_config() ) . ';',
			'before'
		);

		// The original site has no WordPress block markup; skip the block CSS on the front end.
		if ( ! is_singular( 'post' ) ) {
			wp_dequeue_style( 'wp-block-library' );
			wp_dequeue_style( 'wp-block-library-theme' );
			wp_dequeue_style( 'global-styles' );
			wp_dequeue_style( 'classic-theme-styles' );
		}
	},
	20
);

/** Data passed to the front-end scripts as window.RK_THEME. */
function rk_js_config() {
	return array(
		'layout'     => rk_current_layout(),
		'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
		'nonce'      => wp_create_nonce( 'rk_contact' ),
		'homeUrl'    => home_url( '/' ),
		'contactUrl' => rk_page_url( 'contact' ),
		'img'        => rk_asset( 'img/' ),
		'video'      => rk_asset( 'video/' ),
		'popupDelay' => max( 5, (int) rk_setting( 'popup_delay' ) ) * 1000,
		'settings'   => array(
			'phone'     => rk_setting( 'phone' ),
			'phoneLink' => rk_phone_link(),
			'whatsapp'  => rk_whatsapp_number(),
			'email'     => rk_setting( 'email' ),
			'address'   => implode( '<br>', array_map( 'esc_html', rk_address_lines() ) ),
			'hours'     => implode( ', ', array_map( 'esc_html', rk_hours_lines() ) ),
			'since'     => (string) rk_setting( 'founded_year' ),
		),
	);
}

/** Favicons (the Customizer "Site Icon" replaces them when set). */
add_action(
	'wp_head',
	function () {
		if ( has_site_icon() ) {
			return;
		}
		printf( '<link rel="icon" type="image/x-icon" href="%s" />' . "\n", esc_url( rk_asset( 'img/rk-favicon.ico' ) ) );
		printf( '<link rel="icon" type="image/png" sizes="16x16" href="%s" />' . "\n", esc_url( rk_asset( 'img/rk-fav16.png' ) ) );
		printf( '<link rel="icon" type="image/png" sizes="32x32" href="%s" />' . "\n", esc_url( rk_asset( 'img/rk-fav32.png' ) ) );
		printf( '<link rel="apple-touch-icon" sizes="180x180" href="%s" />' . "\n", esc_url( rk_asset( 'img/rk-apple-icon.png' ) ) );
	},
	2
);

add_action(
	'wp_head',
	function () {
		echo '<meta name="theme-color" content="#F97316" />' . "\n";
	},
	3
);

/** Body classes: rk-layout-<name> lets CSS target a single page layout. */
add_filter(
	'body_class',
	function ( $classes ) {
		$classes[] = 'rk-layout-' . rk_current_layout();
		return $classes;
	}
);

/*
 * The page content is hand-made HTML. Keep WordPress from "prettifying" it:
 * no curly quotes, no emoji images, no automatic paragraphs.
 */
foreach ( array( 'the_content', 'the_title', 'the_excerpt', 'widget_text_content' ) as $rk_filter ) {
	remove_filter( $rk_filter, 'wptexturize' );
	remove_filter( $rk_filter, 'convert_smilies' );
}
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/**
 * Old static URLs (about.html, contact.html, ...) redirect to the new pages,
 * so existing Google results and shared links keep working.
 */
add_action(
	'template_redirect',
	function () {
		if ( ! is_404() ) {
			return;
		}
		$path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! $path || ! preg_match( '#/(index|about|contact|projects|metro-greens|rk-avenue|rk-revenue)\.html$#', $path, $m ) ) {
			return;
		}
		$target = 'index' === $m[1] ? home_url( '/' ) : rk_page_url( $m[1] );
		$query  = isset( $_SERVER['QUERY_STRING'] ) ? sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ) : '';
		if ( $query ) {
			$target .= ( false === strpos( $target, '?' ) ? '?' : '&' ) . $query;
		}
		wp_safe_redirect( $target, 301 );
		exit;
	}
);

/** Admin notice pointing at the one-click setup until it has been run. */
add_action(
	'admin_notices',
	function () {
		if ( get_option( 'rk_demo_imported' ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( $screen && 'appearance_page_rk-setup' === $screen->id ) {
			return;
		}
		printf(
			'<div class="notice notice-info"><p><strong>%s</strong> %s <a class="button button-primary" href="%s">%s</a></p></div>',
			esc_html__( 'RK Developers theme:', 'rk-developers' ),
			esc_html__( 'import the pages, projects, images and menus in one click.', 'rk-developers' ),
			esc_url( admin_url( 'themes.php?page=rk-setup' ) ),
			esc_html__( 'Open RK Setup', 'rk-developers' )
		);
	}
);
