<?php
/**
 * Small helpers shared by the templates.
 *
 * @package RK_Developers
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default values for every site setting (Appearance → Customize → RK Site Settings).
 */
function rk_setting_defaults() {
	return array(
		'phone'          => '+91 9663108999',
		'whatsapp'       => '919663108999',
		'email'          => 'rkdeveloper12345@gmail.com',
		'form_recipient' => '',
		'address'        => "RK Developers,\n3rd Floor, 1st Cross,\n15th Main, E Block,\nBeside Nandana Palace,\nSahakarnagar, Bengaluru - 560092",
		'maps_url'       => 'https://maps.google.com/?q=RK+Developers,+3rd+Floor,+28%2F1,+15th+Main+Rd,+beside+Nandana+palace,+E+Block,+Sahakar+Nagar,+Byatarayanapura,+Bengaluru,+Karnataka+560092',
		'map_image'      => '',
		'hours'          => "Monday – Sunday\n9:30 AM – 7:00 PM IST",
		'facebook'       => 'https://www.facebook.com/profile.php?id=61577490015504',
		'instagram'      => 'https://www.instagram.com/rkdevelopers_sahakarnagar/',
		'youtube'        => '',
		'linkedin'       => '',
		'x'              => '',
		'founded_year'   => '2016',
		'footer_tagline' => "Building Karnataka's most trusted land communities since [rk_since].",
		'copyright'      => 'RK Developers. All Rights Reserved.',
		'popup_enabled'  => true,
		'popup_delay'    => 60,
	);
}

/**
 * Read one site setting (falls back to the default).
 *
 * @param string $key Setting key.
 * @return mixed
 */
function rk_setting( $key ) {
	$defaults = rk_setting_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	$value    = get_theme_mod( 'rk_' . $key, $default );
	if ( is_string( $value ) && '' === trim( $value ) && ! in_array( $key, array( 'youtube', 'linkedin', 'x', 'form_recipient', 'map_image', 'facebook', 'instagram' ), true ) ) {
		return $default;
	}
	return $value;
}

/** Phone number for tel: links, e.g. +919663108999. */
function rk_phone_link() {
	$digits = preg_replace( '/[^0-9+]/', '', (string) rk_setting( 'phone' ) );
	return $digits;
}

/** WhatsApp number in the wa.me format (digits only). */
function rk_whatsapp_number() {
	return preg_replace( '/\D/', '', (string) rk_setting( 'whatsapp' ) );
}

/** Address as an array of lines. */
function rk_address_lines() {
	$lines = preg_split( '/\r\n|\r|\n/', (string) rk_setting( 'address' ) );
	return array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
}

/** Address on a single line. */
function rk_address_inline() {
	$out = '';
	foreach ( rk_address_lines() as $line ) {
		if ( '' !== $out ) {
			$out .= preg_match( '/[,;]$/', $out ) ? ' ' : ', ';
		}
		$out .= $line;
	}
	return $out;
}

/** Office hours as an array of lines. */
function rk_hours_lines() {
	$lines = preg_split( '/\r\n|\r|\n/', (string) rk_setting( 'hours' ) );
	return array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
}

/** Where contact-form enquiries are sent. */
function rk_form_recipient() {
	$to = trim( (string) rk_setting( 'form_recipient' ) );
	if ( ! is_email( $to ) ) {
		$to = trim( (string) rk_setting( 'email' ) );
	}
	return $to;
}

/**
 * Find a published post by slug and post type. (get_page_by_path() also matches
 * attachments with the same name, e.g. the image "aj-layout", so it is not used.)
 */
function rk_find_post( $slug, $type = 'page', $any_status = false ) {
	$found = get_posts(
		array(
			'name'             => $slug,
			'post_type'        => $type,
			'post_status'      => $any_status ? array( 'publish', 'draft', 'pending', 'private', 'future', 'trash' ) : 'publish',
			'posts_per_page'   => 1,
			'suppress_filters' => true,
		)
	);
	return $found ? $found[0] : null;
}

/** URL of a file inside the theme's assets folder. */
function rk_asset( $path ) {
	return RK_THEME_URI . '/assets/' . ltrim( $path, '/' );
}

/** Site logo: the Customizer logo if one is set, otherwise the bundled RK logo. */
function rk_logo_url() {
	$id = get_theme_mod( 'custom_logo' );
	if ( $id ) {
		$src = wp_get_attachment_image_url( $id, 'full' );
		if ( $src ) {
			return $src;
		}
	}
	return rk_asset( 'img/rk-logo.png' );
}

/** Footer map thumbnail. */
function rk_map_image_url() {
	$img = rk_setting( 'map_image' );
	if ( is_numeric( $img ) && $img ) {
		$src = wp_get_attachment_image_url( (int) $img, 'medium_large' );
		if ( $src ) {
			return $src;
		}
	} elseif ( is_string( $img ) && $img ) {
		return $img;
	}
	return rk_asset( 'img/rk-map-thumb.png' );
}

/**
 * The page template key of the current request, e.g. "home", "metro-greens".
 * Used to load the right page CSS/JS and header variant.
 */
function rk_current_layout() {
	static $layout = null;
	if ( null !== $layout ) {
		return $layout;
	}
	$layout = 'default';
	if ( is_singular( 'page' ) ) {
		$tpl = get_page_template_slug( get_queried_object_id() );
		if ( $tpl && preg_match( '#page-templates/template-([a-z0-9-]+)\.php$#', $tpl, $m ) ) {
			$layout = $m[1];
		}
	} elseif ( is_singular( 'rk_project' ) ) {
		$layout = 'project';
	}
	return $layout;
}

/** Permalink of the page that uses a given RK template ("contact", "about", ...). */
function rk_page_url( $layout ) {
	static $cache = array();
	if ( isset( $cache[ $layout ] ) ) {
		return $cache[ $layout ];
	}
	if ( 'home' === $layout ) {
		return $cache[ $layout ] = home_url( '/' );
	}
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'page-templates/template-' . $layout . '.php',
			'fields'         => 'ids',
		)
	);
	if ( $pages ) {
		return $cache[ $layout ] = get_permalink( $pages[0] );
	}
	$page = rk_find_post( $layout, 'page' );
	return $cache[ $layout ] = ( $page ? get_permalink( $page ) : home_url( '/' . $layout . '/' ) );
}

/**
 * Replace the {{url:slug}} and {{asset:path}} tokens used by the bundled page content.
 *
 * @param string   $html      Content.
 * @param callable $asset_cb  Maps an asset path to a URL (defaults to the bundled demo file).
 */
function rk_resolve_tokens( $html, $asset_cb = null ) {
	$html = preg_replace_callback(
		'/\{\{url:([a-z0-9-]+)\}\}/',
		function ( $m ) {
			return esc_url( rk_page_url( $m[1] ) );
		},
		$html
	);
	return preg_replace_callback(
		'/\{\{asset:([^}]+)\}\}/',
		function ( $m ) use ( $asset_cb ) {
			if ( $asset_cb ) {
				return call_user_func( $asset_cb, $m[1] );
			}
			return esc_url( RK_THEME_URI . '/demo-content/media/' . str_replace( '%2F', '/', rawurlencode( $m[1] ) ) );
		},
		$html
	);
}

/** Bundled default content for a page layout (used until the page has its own content). */
function rk_default_content( $layout ) {
	$file = RK_THEME_DIR . '/demo-content/pages/' . sanitize_file_name( $layout ) . '.html';
	if ( ! file_exists( $file ) ) {
		return '';
	}
	return rk_resolve_tokens( file_get_contents( $file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
}

/**
 * Print the current page's content. A page that is still empty shows the
 * bundled design for its template, so the site looks right even before the
 * one-click import has run.
 */
function rk_the_page_content() {
	while ( have_posts() ) {
		the_post();
		if ( '' === trim( get_the_content() ) ) {
			echo apply_filters( 'the_content', rk_default_content( rk_current_layout() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			the_content();
		}
	}
}
