<?php
/**
 * Basic SEO: per-page title / description / keywords, Open Graph, Twitter card
 * and the RealEstateAgent schema (built from RK Site Settings).
 *
 * Switches itself off when an SEO plugin (Yoast, Rank Math, All in One SEO,
 * SEOPress) is active, so the two never output duplicate tags.
 *
 * @package RK_Developers
 */

defined( 'ABSPATH' ) || exit;

/** True when a dedicated SEO plugin handles the meta tags. */
function rk_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

/** SEO fields of the current page. */
function rk_seo_fields( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_queried_object_id();
	$out     = array(
		'title'       => '',
		'description' => '',
		'keywords'    => '',
	);
	if ( $post_id ) {
		foreach ( array_keys( $out ) as $k ) {
			$out[ $k ] = rk_replace_setting_tags( (string) get_post_meta( $post_id, '_rk_seo_' . $k, true ) );
		}
	}
	return $out;
}

add_action(
	'add_meta_boxes',
	function () {
		foreach ( array( 'page', 'rk_project' ) as $type ) {
			add_meta_box( 'rk_seo', __( 'Search engine (Google) title & description', 'rk-developers' ), 'rk_seo_metabox', $type, 'normal', 'low' );
		}
	}
);

function rk_seo_metabox( $post ) {
	wp_nonce_field( 'rk_seo_save', 'rk_seo_nonce' );
	if ( rk_seo_plugin_active() ) {
		echo '<p>' . esc_html__( 'An SEO plugin is active, so it controls these tags. The fields below are ignored.', 'rk-developers' ) . '</p>';
	}
	$f = array(
		'title'       => array( __( 'Title in Google / browser tab', 'rk-developers' ), 'text' ),
		'description' => array( __( 'Description in Google results', 'rk-developers' ), 'textarea' ),
		'keywords'    => array( __( 'Keywords (comma separated)', 'rk-developers' ), 'text' ),
	);
	foreach ( $f as $k => $conf ) {
		$v = get_post_meta( $post->ID, '_rk_seo_' . $k, true );
		echo '<p><label><strong>' . esc_html( $conf[0] ) . '</strong><br />';
		if ( 'textarea' === $conf[1] ) {
			printf( '<textarea class="large-text" rows="2" name="rk_seo_%s">%s</textarea>', esc_attr( $k ), esc_textarea( $v ) );
		} else {
			printf( '<input type="text" class="large-text" name="rk_seo_%s" value="%s" />', esc_attr( $k ), esc_attr( $v ) );
		}
		echo '</label></p>';
	}
	echo '<p class="description">' . esc_html__( 'Leave empty to use the page title. [rk_since] is replaced by the founding year.', 'rk-developers' ) . '</p>';
}

add_action(
	'save_post',
	function ( $post_id ) {
		if ( ! isset( $_POST['rk_seo_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['rk_seo_nonce'] ), 'rk_seo_save' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( array( 'title', 'keywords' ) as $k ) {
			update_post_meta( $post_id, '_rk_seo_' . $k, isset( $_POST[ 'rk_seo_' . $k ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'rk_seo_' . $k ] ) ) : '' );
		}
		update_post_meta( $post_id, '_rk_seo_description', isset( $_POST['rk_seo_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['rk_seo_description'] ) ) : '' );
	}
);

add_filter(
	'pre_get_document_title',
	function ( $title ) {
		if ( rk_seo_plugin_active() || ! is_singular() ) {
			return $title;
		}
		$f = rk_seo_fields();
		return $f['title'] ? $f['title'] : $title;
	}
);

add_action(
	'wp_head',
	function () {
		if ( rk_seo_plugin_active() ) {
			return;
		}
		$f     = is_singular() ? rk_seo_fields() : array( 'title' => '', 'description' => '', 'keywords' => '' );
		$title = $f['title'] ? $f['title'] : wp_get_document_title();
		$desc  = $f['description'] ? $f['description'] : get_bloginfo( 'description' );
		$url   = is_front_page() ? home_url( '/' ) : ( is_singular() ? get_permalink() : home_url( add_query_arg( array() ) ) );
		$img   = rk_logo_url();
		if ( is_singular() && has_post_thumbnail() ) {
			$img = get_the_post_thumbnail_url( null, 'large' );
		}
		echo "\n<!-- RK Developers SEO -->\n";
		if ( $desc ) {
			printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $desc ) );
		}
		if ( $f['keywords'] ) {
			printf( '<meta name="keywords" content="%s" />' . "\n", esc_attr( $f['keywords'] ) );
		}
		echo '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />' . "\n";
		echo '<meta name="geo.region" content="IN-KA" />' . "\n";
		echo '<meta name="geo.placename" content="Bangalore" />' . "\n";
		echo '<meta name="geo.position" content="13.0594;77.5912" />' . "\n";
		echo '<meta name="ICBM" content="13.0594, 77.5912" />' . "\n";
		echo '<meta property="og:type" content="website" />' . "\n";
		printf( '<meta property="og:site_name" content="%s" />' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
		printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $title ) );
		if ( $desc ) {
			printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( $desc ) );
		}
		printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( $url ) );
		printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( $img ) );
		echo '<meta property="og:locale" content="en_IN" />' . "\n";
		echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
		printf( '<meta name="twitter:title" content="%s" />' . "\n", esc_attr( $title ) );
		if ( $desc ) {
			printf( '<meta name="twitter:description" content="%s" />' . "\n", esc_attr( $desc ) );
		}
		printf( '<meta name="twitter:image" content="%s" />' . "\n", esc_url( $img ) );

		if ( is_front_page() ) {
			$social = array_values( array_filter( array( home_url( '/' ), rk_setting( 'facebook' ), rk_setting( 'instagram' ), rk_setting( 'youtube' ), rk_setting( 'linkedin' ), rk_setting( 'x' ) ) ) );
			$lines  = rk_address_lines();
			$schema = array(
				'@context'      => 'https://schema.org',
				'@type'         => 'RealEstateAgent',
				'name'          => get_bloginfo( 'name' ),
				'url'           => home_url( '/' ),
				'logo'          => rk_logo_url(),
				'image'         => rk_logo_url(),
				'description'   => $desc,
				'telephone'     => rk_phone_link(),
				'email'         => rk_setting( 'email' ),
				'foundingDate'  => (string) rk_setting( 'founded_year' ),
				'address'       => array(
					'@type'           => 'PostalAddress',
					'streetAddress'   => rk_address_inline(),
					'addressLocality' => 'Sahakarnagar, Bengaluru',
					'addressRegion'   => 'Karnataka',
					'postalCode'      => preg_match( '/\b(\d{6})\b/', implode( ' ', $lines ), $m ) ? $m[1] : '',
					'addressCountry'  => 'IN',
				),
				'geo'           => array(
					'@type'     => 'GeoCoordinates',
					'latitude'  => '13.0594',
					'longitude' => '77.5912',
				),
				'areaServed'    => array( 'Bangalore', 'Bengaluru', 'North Bangalore', 'Sahakarnagar', 'Hebbal', 'KIA Corridor' ),
				'sameAs'        => $social,
			);
			echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
		}
	},
	1
);
