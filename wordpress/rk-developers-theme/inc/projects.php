<?php
/**
 * "Projects" post type and its fields.
 *
 * Every card in "Projects We've Delivered", the two "Ongoing Developments"
 * cards on the home page, the photo galleries and the RK Avenue / Metro Greens
 * slideshows are built from these entries.
 *
 * @package RK_Developers
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		register_post_type(
			'rk_project',
			array(
				'labels'       => array(
					'name'               => __( 'Projects', 'rk-developers' ),
					'singular_name'      => __( 'Project', 'rk-developers' ),
					'add_new'            => __( 'Add Project', 'rk-developers' ),
					'add_new_item'       => __( 'Add New Project', 'rk-developers' ),
					'edit_item'          => __( 'Edit Project', 'rk-developers' ),
					'new_item'           => __( 'New Project', 'rk-developers' ),
					'view_item'          => __( 'View Project', 'rk-developers' ),
					'search_items'       => __( 'Search Projects', 'rk-developers' ),
					'not_found'          => __( 'No projects found', 'rk-developers' ),
					'all_items'          => __( 'All Projects', 'rk-developers' ),
					'featured_image'     => __( 'Project image (home page card)', 'rk-developers' ),
					'set_featured_image' => __( 'Set project image', 'rk-developers' ),
				),
				'public'       => true,
				'show_in_rest' => false, // Classic editor screen: simple fields, no blocks.
				'menu_icon'    => 'dashicons-building',
				'menu_position' => 5,
				'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
				'has_archive'  => false,
				'rewrite'      => array( 'slug' => 'project' ),
			)
		);
	}
);

/**
 * Project fields: key => [label, type, help].
 */
function rk_project_fields() {
	return array(
		'status'        => array( __( 'Status', 'rk-developers' ), 'status', '' ),
		'size'          => array( __( 'Size / plots', 'rk-developers' ), 'text', __( 'e.g. "94 Residential Plots · 4 Acres". Shown on the project card before the location.', 'rk-developers' ) ),
		'location'      => array( __( 'Location', 'rk-developers' ), 'text', __( 'e.g. "Bagalur, North Bengaluru".', 'rk-developers' ) ),
		'price'         => array( __( 'Price', 'rk-developers' ), 'text', __( 'e.g. "₹999/sq.ft". Use {price} in the home-card features to show it.', 'rk-developers' ) ),
		'description'   => array( __( 'Short description', 'rk-developers' ), 'textarea', __( 'Two or three sentences, shown on the home page card.', 'rk-developers' ) ),
		'icon'          => array( __( 'Card icon', 'rk-developers' ), 'text', __( 'Font Awesome icon class, e.g. "fas fa-tree", "fas fa-map-pin", "fas fa-building".', 'rk-developers' ) ),
		'page'          => array( __( 'Detail page', 'rk-developers' ), 'page', __( 'Optional. The page with the full project details. The card links to it.', 'rk-developers' ) ),
		'logo'          => array( __( 'Project logo', 'rk-developers' ), 'image', __( 'Optional. Shown in the "Our Projects" menu.', 'rk-developers' ) ),
		'gallery'       => array( __( 'Gallery images', 'rk-developers' ), 'gallery', __( 'Photos for the gallery pop-up / slideshow. Drag to reorder.', 'rk-developers' ) ),
		'featured'      => array( __( 'Show in "Ongoing Developments" on the home page', 'rk-developers' ), 'checkbox', '' ),
		'card_badge'    => array( __( 'Home card: badge', 'rk-developers' ), 'text', __( 'e.g. "Phase 1 Open".', 'rk-developers' ) ),
		'card_type'     => array( __( 'Home card: project type', 'rk-developers' ), 'text', __( 'e.g. "Farm Plots".', 'rk-developers' ) ),
		'card_type_icon' => array( __( 'Home card: project type icon', 'rk-developers' ), 'text', __( 'e.g. "fas fa-tree".', 'rk-developers' ) ),
		'card_location' => array( __( 'Home card: address', 'rk-developers' ), 'text', __( 'Leave empty to use the location.', 'rk-developers' ) ),
		'card_features' => array( __( 'Home card: features', 'rk-developers' ), 'textarea', __( 'One per line as "icon | text", e.g. "fas fa-leaf | 30+ Amenities". {price} is replaced by the price.', 'rk-developers' ) ),
		'accent'        => array( __( 'Home card: colour', 'rk-developers' ), 'accent', '' ),
	);
}

/** Read a project field. */
function rk_project_meta( $post_id, $key ) {
	return get_post_meta( $post_id, '_rk_' . $key, true );
}

/** Gallery attachment IDs of a project. */
function rk_project_gallery_ids( $post_id ) {
	$ids = array_filter( array_map( 'absint', explode( ',', (string) rk_project_meta( $post_id, 'gallery' ) ) ) );
	return array_values( $ids );
}

/** Gallery image URLs of a project. */
function rk_project_gallery_urls( $post_id, $size = 'large' ) {
	$urls = array();
	foreach ( rk_project_gallery_ids( $post_id ) as $id ) {
		$src = wp_get_attachment_image_url( $id, $size );
		if ( $src ) {
			$urls[] = $src;
		}
	}
	return $urls;
}

/** Where a project card links to (detail page, own page, or nothing). */
function rk_project_link( $post ) {
	$page = (int) rk_project_meta( $post->ID, 'page' );
	if ( $page && 'publish' === get_post_status( $page ) ) {
		return get_permalink( $page );
	}
	if ( trim( $post->post_content ) ) {
		return get_permalink( $post );
	}
	return '';
}

/** Find a project by slug (or by its detail page slug). */
function rk_get_project( $slug ) {
	$p = rk_find_post( $slug, 'rk_project' );
	if ( $p ) {
		return $p;
	}
	$page = rk_find_post( $slug, 'page' );
	if ( $page ) {
		$found = get_posts(
			array(
				'post_type'      => 'rk_project',
				'posts_per_page' => 1,
				'meta_key'       => '_rk_page',
				'meta_value'     => $page->ID,
			)
		);
		if ( $found ) {
			return $found[0];
		}
	}
	return null;
}

/** All published projects in display order. */
function rk_get_projects( $args = array() ) {
	return get_posts(
		array_merge(
			array(
				'post_type'      => 'rk_project',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				),
			),
			$args
		)
	);
}

/* ─────────────────────────── Admin screen ─────────────────────────── */

add_action(
	'add_meta_boxes',
	function () {
		add_meta_box( 'rk_project_details', __( 'Project details', 'rk-developers' ), 'rk_project_metabox', 'rk_project', 'normal', 'high' );
	}
);

/** Render the "Project details" box. */
function rk_project_metabox( $post ) {
	wp_nonce_field( 'rk_project_save', 'rk_project_nonce' );
	echo '<p class="description">' . esc_html__( 'The title above is the project name. "Order" (in the Page Attributes box) sets the position in the project grid: lower numbers come first.', 'rk-developers' ) . '</p>';
	echo '<table class="form-table rk-project-fields"><tbody>';
	foreach ( rk_project_fields() as $key => $f ) {
		$name  = 'rk_' . $key;
		$value = rk_project_meta( $post->ID, $key );
		if ( 'card_badge' === $key ) {
			echo '<tr><th colspan="2"><h3 style="margin:16px 0 0;">' . esc_html__( 'Home page "Ongoing Developments" card', 'rk-developers' ) . '</h3></th></tr>';
		}
		echo '<tr><th scope="row"><label for="' . esc_attr( $name ) . '">' . esc_html( $f[0] ) . '</label></th><td>';
		switch ( $f[1] ) {
			case 'status':
				echo '<select id="' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '">';
				foreach ( array( 'delivered' => __( 'Delivered', 'rk-developers' ), 'ongoing' => __( 'Ongoing', 'rk-developers' ) ) as $k => $label ) {
					printf( '<option value="%s"%s>%s</option>', esc_attr( $k ), selected( $value ? $value : 'delivered', $k, false ), esc_html( $label ) );
				}
				echo '</select>';
				break;
			case 'accent':
				echo '<select id="' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '">';
				foreach ( array( 'green' => __( 'Green', 'rk-developers' ), 'orange' => __( 'Orange', 'rk-developers' ) ) as $k => $label ) {
					printf( '<option value="%s"%s>%s</option>', esc_attr( $k ), selected( $value ? $value : 'green', $k, false ), esc_html( $label ) );
				}
				echo '</select>';
				break;
			case 'textarea':
				printf( '<textarea class="large-text" rows="3" id="%1$s" name="%1$s">%2$s</textarea>', esc_attr( $name ), esc_textarea( $value ) );
				break;
			case 'checkbox':
				printf( '<input type="checkbox" id="%1$s" name="%1$s" value="1"%2$s />', esc_attr( $name ), checked( (bool) $value, true, false ) );
				break;
			case 'page':
				wp_dropdown_pages(
					array(
						'name'              => esc_attr( $name ),
						'id'                => esc_attr( $name ),
						'selected'          => (int) $value,
						'show_option_none'  => esc_html__( '— No detail page —', 'rk-developers' ),
						'option_none_value' => '0',
					)
				);
				break;
			case 'image':
				$src = $value ? wp_get_attachment_image_url( (int) $value, 'thumbnail' ) : '';
				printf(
					'<div class="rk-image-field"><input type="hidden" name="%1$s" value="%2$s" /><img src="%3$s" style="max-height:60px;%4$s" /> <button type="button" class="button rk-pick-image">%5$s</button> <button type="button" class="button-link rk-clear-image">%6$s</button></div>',
					esc_attr( $name ),
					esc_attr( $value ),
					esc_url( $src ),
					$src ? '' : 'display:none;',
					esc_html__( 'Choose image', 'rk-developers' ),
					esc_html__( 'Remove', 'rk-developers' )
				);
				break;
			case 'gallery':
				echo '<div class="rk-gallery-field"><input type="hidden" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" /><ul class="rk-gallery-list">';
				foreach ( rk_project_gallery_ids( $post->ID ) as $id ) {
					printf( '<li data-id="%d"><img src="%s" /><button type="button" class="rk-remove" aria-label="%s">&times;</button></li>', (int) $id, esc_url( wp_get_attachment_image_url( $id, 'thumbnail' ) ), esc_attr__( 'Remove image', 'rk-developers' ) );
				}
				echo '</ul><button type="button" class="button rk-add-gallery">' . esc_html__( 'Add / choose images', 'rk-developers' ) . '</button></div>';
				break;
			default:
				printf( '<input type="text" class="regular-text" id="%1$s" name="%1$s" value="%2$s" />', esc_attr( $name ), esc_attr( $value ) );
		}
		if ( $f[2] ) {
			echo '<p class="description">' . esc_html( $f[2] ) . '</p>';
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
	echo '<p class="description">' . esc_html__( 'The text box at the top (optional) is used only when a project has no detail page: if you write something there, the project gets its own simple page.', 'rk-developers' ) . '</p>';
}

add_action(
	'save_post_rk_project',
	function ( $post_id ) {
		if ( ! isset( $_POST['rk_project_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['rk_project_nonce'] ), 'rk_project_save' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( rk_project_fields() as $key => $f ) {
			$name = 'rk_' . $key;
			$raw  = isset( $_POST[ $name ] ) ? wp_unslash( $_POST[ $name ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			switch ( $f[1] ) {
				case 'status':
					$val = 'ongoing' === $raw ? 'ongoing' : 'delivered';
					break;
				case 'accent':
					$val = 'orange' === $raw ? 'orange' : 'green';
					break;
				case 'textarea':
					$val = sanitize_textarea_field( $raw );
					break;
				case 'checkbox':
					$val = $raw ? '1' : '';
					break;
				case 'page':
				case 'image':
					$val = absint( $raw );
					break;
				case 'gallery':
					$val = implode( ',', array_filter( array_map( 'absint', explode( ',', $raw ) ) ) );
					break;
				default:
					$val = sanitize_text_field( $raw );
			}
			update_post_meta( $post_id, '_rk_' . $key, $val );
		}
	}
);

add_action(
	'admin_enqueue_scripts',
	function ( $hook ) {
		$screen = get_current_screen();
		if ( ! $screen || 'rk_project' !== $screen->post_type || ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'rk-project-admin', rk_asset( 'admin/project-admin.js' ), array( 'jquery', 'jquery-ui-sortable' ), RK_THEME_VERSION, true );
		wp_enqueue_style( 'rk-project-admin', rk_asset( 'admin/project-admin.css' ), array(), RK_THEME_VERSION );
	}
);

/* Admin list columns. */
add_filter(
	'manage_rk_project_posts_columns',
	function ( $cols ) {
		$new = array();
		foreach ( $cols as $k => $v ) {
			if ( 'date' === $k ) {
				continue;
			}
			$new[ $k ] = $v;
			if ( 'title' === $k ) {
				$new['rk_status']   = __( 'Status', 'rk-developers' );
				$new['rk_location'] = __( 'Location', 'rk-developers' );
				$new['rk_photos']   = __( 'Photos', 'rk-developers' );
				$new['rk_order']    = __( 'Order', 'rk-developers' );
			}
		}
		return $new;
	}
);
add_action(
	'manage_rk_project_posts_custom_column',
	function ( $col, $post_id ) {
		switch ( $col ) {
			case 'rk_status':
				echo 'ongoing' === rk_project_meta( $post_id, 'status' ) ? esc_html__( 'Ongoing', 'rk-developers' ) : esc_html__( 'Delivered', 'rk-developers' );
				break;
			case 'rk_location':
				echo esc_html( rk_project_meta( $post_id, 'location' ) );
				break;
			case 'rk_photos':
				echo (int) count( rk_project_gallery_ids( $post_id ) );
				break;
			case 'rk_order':
				echo (int) get_post_field( 'menu_order', $post_id );
				break;
		}
	},
	10,
	2
);
add_action(
	'pre_get_posts',
	function ( $q ) {
		if ( is_admin() && $q->is_main_query() && 'rk_project' === $q->get( 'post_type' ) && ! $q->get( 'orderby' ) ) {
			$q->set( 'orderby', 'menu_order' );
			$q->set( 'order', 'ASC' );
		}
	}
);
