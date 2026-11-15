<?php
/**
 * Menus: renders Appearance → Menus with the original navbar / footer markup.
 *
 * Main menu: top-level items are links; an item with sub-items becomes the
 * "Our Projects" style drop-down (its sub-items show the project logo).
 * Footer menu: an item whose URL is "#" is shown as plain text.
 *
 * @package RK_Developers
 */

defined( 'ABSPATH' ) || exit;

/** Menu items of a location as a simple tree. */
function rk_menu_tree( $location ) {
	$locations = get_nav_menu_locations();
	if ( empty( $locations[ $location ] ) ) {
		return null;
	}
	$items = wp_get_nav_menu_items( $locations[ $location ] );
	if ( ! $items ) {
		return null;
	}
	_wp_menu_item_classes_by_context( $items );
	$tree = array();
	$kids = array();
	foreach ( $items as $item ) {
		if ( $item->menu_item_parent ) {
			$kids[ $item->menu_item_parent ][] = $item;
		}
	}
	foreach ( $items as $item ) {
		if ( ! $item->menu_item_parent ) {
			$item->rk_children = isset( $kids[ $item->ID ] ) ? $kids[ $item->ID ] : array();
			$tree[]            = $item;
		}
	}
	return $tree;
}

/** Is a menu item the page being viewed? */
function rk_menu_item_is_current( $item ) {
	return ! empty( $item->current ) || in_array( 'current-menu-item', (array) $item->classes, true );
}

/** Logo shown next to a project in the drop-down. Returns [url, blend]. */
function rk_menu_item_logo( $item ) {
	$project = null;
	if ( 'post_type' === $item->type && 'page' === $item->object ) {
		$found = get_posts(
			array(
				'post_type'      => 'rk_project',
				'posts_per_page' => 1,
				'meta_key'       => '_rk_page',
				'meta_value'     => (int) $item->object_id,
			)
		);
		$project = $found ? $found[0] : null;
	} elseif ( 'post_type' === $item->type && 'rk_project' === $item->object ) {
		$project = get_post( (int) $item->object_id );
	}
	if ( $project ) {
		$logo = (int) rk_project_meta( $project->ID, 'logo' );
		if ( $logo && wp_get_attachment_image_url( $logo, 'thumbnail' ) ) {
			return array( wp_get_attachment_image_url( $logo, 'medium' ), false );
		}
	}
	return array( rk_logo_url(), true );
}

/** Default menu used until one is assigned in Appearance → Menus. */
function rk_default_menu_tree() {
	$mk = function ( $title, $url, $children = array() ) {
		$o              = new stdClass();
		$o->ID          = 0;
		$o->title       = $title;
		$o->url         = $url;
		$o->classes     = array();
		$o->current     = untrailingslashit( $url ) === untrailingslashit( home_url( add_query_arg( array() ) ) );
		$o->type        = 'custom';
		$o->object      = '';
		$o->object_id   = 0;
		$o->description = '';
		$o->target      = '';
		$o->rk_children = $children;
		return $o;
	};
	$children = array();
	foreach ( rk_get_projects( array( 'meta_key' => '_rk_featured', 'meta_value' => '1' ) ) as $p ) {
		$link = rk_project_link( $p );
		if ( $link ) {
			$c            = $mk( get_the_title( $p ), $link );
			$c->type      = 'post_type';
			$c->object    = 'rk_project';
			$c->object_id = $p->ID;
			$children[]   = $c;
		}
	}
	return array(
		$mk( __( 'Home', 'rk-developers' ), home_url( '/' ) ),
		$mk( __( 'Our Projects', 'rk-developers' ), '#', $children ),
		$mk( __( 'About Us', 'rk-developers' ), rk_page_url( 'about' ) ),
		$mk( __( 'Contact', 'rk-developers' ), rk_page_url( 'contact' ) ),
	);
}

/** Print the navbar links (<ul class="nav-links">). */
function rk_primary_nav( $cta_text, $cta_url ) {
	$tree = rk_menu_tree( 'primary' );
	if ( null === $tree ) {
		$tree = rk_default_menu_tree();
	}
	echo '<ul class="nav-links" id="navLinks">' . "\n";
	$dropdowns = 0;
	foreach ( $tree as $item ) {
		$target = $item->target ? ' target="' . esc_attr( $item->target ) . '" rel="noopener"' : '';
		if ( ! empty( $item->rk_children ) ) {
			$dropdowns++;
			$suffix = 1 === $dropdowns ? '' : $dropdowns;
			echo '<li class="nav-dropdown-wrap">' . "\n";
			printf( '<button class="nav-link nav-dropdown-trigger" id="projectsDropdownBtn%s">%s <i class="fas fa-chevron-down dropdown-arrow"></i></button>' . "\n", esc_attr( $suffix ), esc_html( $item->title ) );
			printf( '<div class="nav-dropdown" id="projectsDropdown%s">' . "\n", esc_attr( $suffix ) );
			printf( '<div class="dropdown-header">%s</div>' . "\n", esc_html( $item->description ? $item->description : __( 'Choose a Project', 'rk-developers' ) ) );
			foreach ( $item->rk_children as $child ) {
				list( $logo, $blend ) = rk_menu_item_logo( $child );
				printf(
					'<a href="%s" class="dropdown-item%s"%s><img src="%s" alt="%s" style="height:36px;width:36px;object-fit:contain;border-radius:6px;flex-shrink:0;%s" /><div class="dropdown-info"><span>%s</span></div><i class="fas fa-arrow-right dd-arrow"></i></a>' . "\n",
					esc_url( $child->url ),
					rk_menu_item_is_current( $child ) ? ' active-project' : '',
					$child->target ? ' target="' . esc_attr( $child->target ) . '" rel="noopener"' : '',
					esc_url( $logo ),
					esc_attr( $child->title ),
					$blend ? 'mix-blend-mode:multiply;' : '',
					esc_html( $child->title )
				);
			}
			echo "</div>\n</li>\n";
			continue;
		}
		printf(
			'<li><a href="%s" class="nav-link%s"%s>%s</a></li>' . "\n",
			esc_url( $item->url ),
			rk_menu_item_is_current( $item ) ? ' active' : '',
			$target, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_html( $item->title )
		);
	}
	if ( $cta_text ) {
		printf( '<li><a href="%s" class="nav-cta-btn">%s <i class="fas fa-arrow-right"></i></a></li>' . "\n", esc_url( $cta_url ), esc_html( $cta_text ) );
	}
	echo "</ul>\n";
}

/** Print the footer "Quick Links" list items. */
function rk_footer_nav() {
	$tree = rk_menu_tree( 'footer' );
	if ( null === $tree ) {
		$tree = rk_default_menu_tree();
	}
	foreach ( $tree as $item ) {
		if ( '#' === $item->url || '' === $item->url ) {
			printf( '<li><span style="color:#94A3B8;cursor:default;display:flex;align-items:center;gap:6px;padding:2px 0;font-size:0.9rem;"><i class="fas fa-chevron-right" style="font-size:0.65rem;color:#F97316;"></i> %s</span></li>' . "\n", esc_html( $item->title ) );
		} else {
			printf( '<li><a href="%s"><i class="fas fa-chevron-right" style="font-size:0.65rem;margin-right:6px;color:#F97316;"></i> %s</a></li>' . "\n", esc_url( $item->url ), esc_html( $item->title ) );
		}
	}
}
