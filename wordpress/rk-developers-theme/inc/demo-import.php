<?php
/**
 * Appearance → RK Setup: one-click import of the original site.
 *
 * Creates (or updates) the 7 pages with their templates and SEO text, imports
 * every photo/video into the Media Library, creates the 14 Projects with their
 * galleries, builds the two menus, sets the home page and pretty permalinks.
 * Data: demo-content/content.json + demo-content/pages/*.html + demo-content/media/.
 *
 * The import runs in small steps (a few files per request) so it also works on
 * shared hosting with short PHP time limits. Running it again is safe: it reuses
 * what it created before and only overwrites page text if you tick the box.
 *
 * @package RK_Developers
 */

defined( 'ABSPATH' ) || exit;

/** Import data. */
function rk_demo_data() {
	$file = RK_THEME_DIR . '/demo-content/content.json';
	$data = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	return is_array( $data ) ? $data : array();
}

add_action(
	'admin_menu',
	function () {
		add_theme_page( __( 'RK Setup', 'rk-developers' ), __( 'RK Setup', 'rk-developers' ), 'manage_options', 'rk-setup', 'rk_demo_page' );
	}
);

/** The admin screen. */
function rk_demo_page() {
	$data     = rk_demo_data();
	$imported = get_option( 'rk_demo_imported' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'RK Developers – One-click setup', 'rk-developers' ); ?></h1>
		<p style="max-width:760px;font-size:14px;">
			<?php esc_html_e( 'This creates the complete RK Developers website: all pages (Home, About Us, Projects, Contact, Metro Greens, RK Avenue, RK Revenue), the 14 projects with their photos, the menus, and sets the home page. It takes 1–3 minutes. Keep this tab open until it says "Done".', 'rk-developers' ); ?>
		</p>
		<?php if ( $imported ) : ?>
			<div class="notice notice-success inline"><p>
				<?php
				printf(
					/* translators: %s: date */
					esc_html__( 'The content was imported on %s. Running it again keeps your edits unless you tick the box below.', 'rk-developers' ),
					esc_html( wp_date( 'j M Y, g:i a', (int) $imported ) )
				);
				?>
			</p></div>
		<?php endif; ?>
		<p>
			<label><input type="checkbox" id="rk-reset-content" <?php checked( ! $imported ); ?> />
			<?php esc_html_e( 'Reset page texts and projects to the original website content (overwrites changes made in the editor).', 'rk-developers' ); ?></label>
		</p>
		<p>
			<button type="button" class="button button-primary button-hero" id="rk-run-import"><?php esc_html_e( 'Import demo content', 'rk-developers' ); ?></button>
		</p>
		<div id="rk-import-progress" style="display:none;max-width:760px;">
			<div style="background:#dcdcde;border-radius:4px;height:16px;overflow:hidden;"><div id="rk-import-bar" style="background:#F97316;height:100%;width:0;transition:width .3s;"></div></div>
			<p id="rk-import-status"></p>
		</div>
		<ul id="rk-import-log" style="max-width:760px;max-height:260px;overflow:auto;font-family:monospace;font-size:12px;"></ul>

		<hr />
		<h2><?php esc_html_e( 'After the import', 'rk-developers' ); ?></h2>
		<ol style="font-size:14px;line-height:1.8;">
			<li><?php printf( wp_kses_post( __( 'Phone, e-mail, WhatsApp, address, social links, founding year and footer text: <a href="%s">Appearance → Customize → RK Site Settings</a>.', 'rk-developers' ) ), esc_url( admin_url( 'customize.php?autofocus[panel]=rk_site' ) ) ); ?></li>
			<li><?php printf( wp_kses_post( __( 'Projects (names, status, location, price, photos): <a href="%s">Projects</a> in the left menu.', 'rk-developers' ) ), esc_url( admin_url( 'edit.php?post_type=rk_project' ) ) ); ?></li>
			<li><?php printf( wp_kses_post( __( 'Page texts: <a href="%s">Pages</a> → open a page → edit the text inside the "Custom HTML" blocks.', 'rk-developers' ) ), esc_url( admin_url( 'edit.php?post_type=page' ) ) ); ?></li>
			<li><?php printf( wp_kses_post( __( 'Menus: <a href="%s">Appearance → Menus</a>.', 'rk-developers' ) ), esc_url( admin_url( 'nav-menus.php' ) ) ); ?></li>
			<li><?php printf( wp_kses_post( __( 'Messages sent from the website: <a href="%s">Enquiries</a> (they are also e-mailed to you).', 'rk-developers' ) ), esc_url( admin_url( 'edit.php?post_type=rk_enquiry' ) ) ); ?></li>
		</ol>
		<p class="description"><?php printf( esc_html__( 'Import file: %1$d pages, %2$d projects, %3$d images/videos.', 'rk-developers' ), count( (array) $data['pages'] ), count( (array) $data['projects'] ), count( (array) $data['media'] ) ); ?></p>
	</div>
	<script>
	(function () {
		var btn = document.getElementById('rk-run-import');
		var bar = document.getElementById('rk-import-bar');
		var status = document.getElementById('rk-import-status');
		var log = document.getElementById('rk-import-log');
		var nonce = <?php echo wp_json_encode( wp_create_nonce( 'rk_demo_import' ) ); ?>;
		var ajax = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;

		function say(msg, err) {
			var li = document.createElement('li');
			li.textContent = msg;
			if (err) li.style.color = '#b32d2e';
			log.appendChild(li); log.scrollTop = log.scrollHeight;
		}
		function call(step, extra) {
			var body = new URLSearchParams(Object.assign({ action: 'rk_demo_import', step: step, nonce: nonce,
				reset: document.getElementById('rk-reset-content').checked ? '1' : '' }, extra || {}));
			return fetch(ajax, { method: 'POST', credentials: 'same-origin', body: body })
				.then(function (r) { return r.text(); })
				.then(function (t) {
					var j; try { j = JSON.parse(t); } catch (e) { throw new Error('Server error: ' + t.replace(/<[^>]+>/g, ' ').slice(0, 300)); }
					if (!j.success) throw new Error((j.data && j.data.message) || 'Unknown error');
					return j.data;
				});
		}
		function progress(done, total, text) {
			bar.style.width = Math.round(done / total * 100) + '%';
			status.textContent = text;
		}
		btn.addEventListener('click', function () {
			btn.disabled = true;
			document.getElementById('rk-import-progress').style.display = 'block';
			log.innerHTML = '';
			var total = 0, failures = 0;
			call('start').then(function (d) {
				(d.log || []).forEach(function (m) { say(m); });
				total = d.media_total;
				function next(offset) {
					progress(offset, total + 2, 'Importing images and videos… ' + offset + ' / ' + total);
					if (offset >= total) return Promise.resolve();
					return call('media', { offset: offset }).then(function (m) {
						(m.log || []).forEach(function (x) { say(x.msg, x.error); if (x.error) failures++; });
						return next(m.next);
					}, function (e) {
						say(e.message + ' — retrying once…', true);
						return call('media', { offset: offset }).then(function (m) {
							(m.log || []).forEach(function (x) { say(x.msg, x.error); });
							return next(m.next);
						});
					});
				}
				return next(0);
			}).then(function () {
				progress(total + 1, total + 2, 'Creating projects, pages and menus…');
				return call('finish');
			}).then(function (d) {
				(d.log || []).forEach(function (m) { say(m); });
				progress(1, 1, failures ? 'Done, with ' + failures + ' file(s) that could not be imported (see the list).' : 'Done! Your site is ready.');
				var a = document.createElement('a');
				a.href = d.home; a.className = 'button button-primary'; a.target = '_blank';
				a.textContent = 'View the website';
				status.appendChild(document.createTextNode(' ')); status.appendChild(a);
				btn.disabled = false;
			}).catch(function (e) {
				say(e.message, true);
				status.textContent = 'The import stopped. You can click the button again: it continues where it left off.';
				btn.disabled = false;
			});
		});
	})();
	</script>
	<?php
}

add_action(
	'wp_ajax_rk_demo_import',
	function () {
		if ( ! current_user_can( 'manage_options' ) || ! check_ajax_referer( 'rk_demo_import', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied. Reload the page and try again.', 'rk-developers' ) ), 403 );
		}
		@set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$step  = isset( $_POST['step'] ) ? sanitize_key( $_POST['step'] ) : '';
		$reset = ! empty( $_POST['reset'] );
		try {
			switch ( $step ) {
				case 'start':
					wp_send_json_success( rk_demo_step_start( $reset ) );
					break;
				case 'media':
					$offset = isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0;
					wp_send_json_success( rk_demo_step_media( $offset, 2 ) );
					break;
				case 'finish':
					wp_send_json_success( rk_demo_step_finish( $reset ) );
					break;
			}
		} catch ( Throwable $e ) {
			wp_send_json_error( array( 'message' => $e->getMessage() ), 500 );
		}
		wp_send_json_error( array( 'message' => 'Unknown step' ), 400 );
	}
);

/**
 * Step 1: pages (empty for now), settings, permalinks.
 */
function rk_demo_step_start( $reset ) {
	$data = rk_demo_data();
	$log  = array();
	$ids  = (array) get_option( 'rk_demo_pages', array() );

	foreach ( $data['pages'] as $p ) {
		$id = isset( $ids[ $p['slug'] ] ) && get_post( $ids[ $p['slug'] ] ) ? (int) $ids[ $p['slug'] ] : 0;
		if ( ! $id ) {
			$existing = rk_find_post( $p['slug'], 'page', true );
			$id       = $existing ? $existing->ID : 0;
		}
		if ( ! $id ) {
			$id = wp_insert_post(
				array(
					'post_type'   => 'page',
					'post_status' => 'publish',
					'post_title'  => $p['title'],
					'post_name'   => $p['slug'],
				),
				true
			);
			if ( is_wp_error( $id ) ) {
				throw new Exception( $id->get_error_message() );
			}
			$log[] = sprintf( 'Created page "%s".', $p['title'] );
		} else {
			if ( 'trash' === get_post_status( $id ) ) {
				wp_untrash_post( $id );
			}
			wp_update_post( array( 'ID' => $id, 'post_status' => 'publish' ) );
			$log[] = sprintf( 'Found page "%s".', $p['title'] );
		}
		update_post_meta( $id, '_wp_page_template', 'page-templates/template-' . $p['template'] . '.php' );
		if ( $reset || ! get_post_meta( $id, '_rk_seo_title', true ) ) {
			foreach ( $p['seo'] as $k => $v ) {
				update_post_meta( $id, '_rk_seo_' . $k, $v );
			}
		}
		$ids[ $p['slug'] ] = $id;
	}
	update_option( 'rk_demo_pages', $ids, false );

	// Pretty permalinks (/about/ instead of ?page_id=2).
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		$log[] = 'Turned on pretty permalinks (/%postname%/).';
	}
	// Home page.
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $ids['home'] );
	if ( 'Just another WordPress site' === get_option( 'blogdescription' ) || '' === get_option( 'blogdescription' ) ) {
		update_option( 'blogdescription', 'Trusted Real Estate Developer in Bangalore' );
	}
	if ( in_array( get_option( 'blogname' ), array( '', 'My WordPress Website', 'My Blog', 'WordPress' ), true ) ) {
		update_option( 'blogname', 'RK Developers' );
	}
	$log[] = 'Set "Home" as the front page.';
	// Indian time for enquiry dates (only if the timezone was never set).
	if ( ! get_option( 'timezone_string' ) && ! (float) get_option( 'gmt_offset' ) ) {
		update_option( 'timezone_string', 'Asia/Kolkata' );
		$log[] = 'Set the timezone to India (Asia/Kolkata).';
	}

	return array(
		'log'         => $log,
		'media_total' => count( $data['media'] ),
	);
}

/**
 * Step 2 (repeated): import a few media files into the Media Library.
 */
function rk_demo_step_media( $offset, $count ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$data  = rk_demo_data();
	$media = $data['media'];
	$map   = (array) get_option( 'rk_demo_media', array() );
	$log   = array();
	$end   = min( count( $media ), $offset + $count );

	for ( $i = $offset; $i < $end; $i++ ) {
		$rel = $media[ $i ];
		if ( ! empty( $map[ $rel ] ) && get_post( $map[ $rel ] ) ) {
			$log[] = array( 'msg' => 'Already in Media Library: ' . $rel );
			continue;
		}
		$src = RK_THEME_DIR . '/demo-content/media/' . $rel;
		if ( ! file_exists( $src ) ) {
			$log[] = array(
				'msg'   => 'Missing file in theme: ' . $rel,
				'error' => true,
			);
			continue;
		}
		$tmp = wp_tempnam( basename( $rel ) );
		copy( $src, $tmp );
		$name = sanitize_file_name( str_replace( array( '/', ' ' ), '-', strtolower( $rel ) ) );
		$id   = media_handle_sideload(
			array(
				'name'     => $name,
				'tmp_name' => $tmp,
			),
			0,
			ucwords( preg_replace( '/[-_]+/', ' ', pathinfo( $rel, PATHINFO_FILENAME ) ) )
		);
		if ( is_wp_error( $id ) ) {
			wp_delete_file( $tmp );
			$log[] = array(
				'msg'   => 'Could not import ' . $rel . ': ' . $id->get_error_message(),
				'error' => true,
			);
			continue;
		}
		update_post_meta( $id, '_rk_demo_source', $rel );
		$map[ $rel ] = $id;
		update_option( 'rk_demo_media', $map, false );
		$log[] = array( 'msg' => 'Imported ' . $rel );
	}
	return array(
		'next' => $end,
		'log'  => $log,
	);
}

/** URL of an imported media file (falls back to the copy inside the theme). */
function rk_demo_media_url( $rel ) {
	$map = (array) get_option( 'rk_demo_media', array() );
	if ( ! empty( $map[ $rel ] ) ) {
		$url = wp_get_attachment_url( $map[ $rel ] );
		if ( $url ) {
			return $url;
		}
	}
	return RK_THEME_URI . '/demo-content/media/' . str_replace( '%2F', '/', rawurlencode( $rel ) );
}

/**
 * Step 3: projects, page content, menus.
 */
function rk_demo_step_finish( $reset ) {
	$data  = rk_demo_data();
	$map   = (array) get_option( 'rk_demo_media', array() );
	$pages = (array) get_option( 'rk_demo_pages', array() );
	$log   = array();

	// The page content is trusted HTML written by the theme: do not strip it.
	kses_remove_filters();

	/* Projects */
	foreach ( $data['projects'] as $p ) {
		$existing = rk_find_post( $p['slug'], 'rk_project', true );
		if ( $existing && ! $reset ) {
			$log[] = sprintf( 'Kept project "%s" (already exists).', $p['title'] );
			continue;
		}
		$args = array(
			'post_type'   => 'rk_project',
			'post_status' => 'publish',
			'post_title'  => $p['title'],
			'post_name'   => $p['slug'],
			'menu_order'  => (int) $p['order'],
		);
		if ( $existing ) {
			$args['ID'] = $existing->ID;
			$id         = wp_update_post( $args, true );
		} else {
			$id = wp_insert_post( $args, true );
		}
		if ( is_wp_error( $id ) ) {
			$log[] = 'Could not save project ' . $p['title'] . ': ' . $id->get_error_message();
			continue;
		}
		$meta = array(
			'status'         => $p['status'],
			'size'           => isset( $p['size'] ) ? $p['size'] : '',
			'location'       => isset( $p['location'] ) ? $p['location'] : '',
			'price'          => isset( $p['price'] ) ? $p['price'] : '',
			'description'    => isset( $p['description'] ) ? $p['description'] : '',
			'icon'           => isset( $p['icon'] ) ? $p['icon'] : '',
			'featured'       => ! empty( $p['featured'] ) ? '1' : '',
			'accent'         => isset( $p['accent'] ) ? $p['accent'] : 'green',
			'card_badge'     => isset( $p['card_badge'] ) ? $p['card_badge'] : '',
			'card_type'      => isset( $p['card_type'] ) ? $p['card_type'] : '',
			'card_type_icon' => isset( $p['card_type_icon'] ) ? $p['card_type_icon'] : '',
			'card_location'  => isset( $p['card_location'] ) ? $p['card_location'] : '',
			'card_features'  => isset( $p['card_features'] ) ? $p['card_features'] : '',
			'page'           => ( ! empty( $p['page'] ) && ! empty( $pages[ $p['page'] ] ) ) ? (int) $pages[ $p['page'] ] : 0,
			'logo'           => ( ! empty( $p['logo'] ) && ! empty( $map[ $p['logo'] ] ) ) ? (int) $map[ $p['logo'] ] : 0,
		);
		$gallery = array();
		foreach ( isset( $p['gallery'] ) ? $p['gallery'] : array() as $rel ) {
			if ( ! empty( $map[ $rel ] ) ) {
				$gallery[] = (int) $map[ $rel ];
			}
		}
		$meta['gallery'] = implode( ',', $gallery );
		foreach ( $meta as $k => $v ) {
			update_post_meta( $id, '_rk_' . $k, $v );
		}
		if ( ! empty( $p['image'] ) && ! empty( $map[ $p['image'] ] ) ) {
			set_post_thumbnail( $id, (int) $map[ $p['image'] ] );
		}
		$log[] = sprintf( 'Saved project "%s" (%d photos).', $p['title'], count( $gallery ) );
	}

	/* Page content */
	foreach ( $data['pages'] as $p ) {
		$id = isset( $pages[ $p['slug'] ] ) ? (int) $pages[ $p['slug'] ] : 0;
		if ( ! $id ) {
			continue;
		}
		$current = get_post_field( 'post_content', $id );
		if ( trim( (string) $current ) && ! $reset ) {
			$log[] = sprintf( 'Kept the text of page "%s" (already edited).', $p['title'] );
			continue;
		}
		$html = file_get_contents( RK_THEME_DIR . '/demo-content/pages/' . $p['slug'] . '.html' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$html = rk_resolve_tokens( $html, 'rk_demo_media_url_esc' );
		wp_update_post(
			array(
				'ID'           => $id,
				'post_content' => wp_slash( $html ),
			)
		);
		$log[] = sprintf( 'Wrote the content of page "%s".', $p['title'] );
	}

	kses_init_filters();

	/* Menus */
	foreach ( $data['menus'] as $location => $menu ) {
		$locations = get_theme_mod( 'nav_menu_locations', array() );
		$obj       = wp_get_nav_menu_object( $menu['name'] );
		if ( $obj && ! $reset ) {
			$locations[ $location ] = $obj->term_id;
			set_theme_mod( 'nav_menu_locations', $locations );
			$log[] = sprintf( 'Kept menu "%s".', $menu['name'] );
			continue;
		}
		if ( $obj ) {
			wp_delete_nav_menu( $obj->term_id );
		}
		$menu_id = wp_create_nav_menu( $menu['name'] );
		if ( is_wp_error( $menu_id ) ) {
			$log[] = 'Could not create menu: ' . $menu_id->get_error_message();
			continue;
		}
		rk_demo_add_menu_items( $menu_id, $menu['items'], 0, $pages );
		$locations[ $location ] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
		$log[] = sprintf( 'Created menu "%s".', $menu['name'] );
	}

	flush_rewrite_rules();
	update_option( 'rk_demo_imported', time() );
	$log[] = 'All done.';
	return array(
		'log'  => $log,
		'home' => home_url( '/' ),
	);
}

/** Token callback: imported media URL, attribute-escaped. */
function rk_demo_media_url_esc( $rel ) {
	return esc_url( rk_demo_media_url( $rel ) );
}

/** Add menu items recursively. */
function rk_demo_add_menu_items( $menu_id, $items, $parent, $pages ) {
	foreach ( $items as $pos => $item ) {
		$args = array(
			'menu-item-title'       => $item['title'],
			'menu-item-status'      => 'publish',
			'menu-item-parent-id'   => $parent,
			'menu-item-position'    => $pos + 1,
			'menu-item-description' => isset( $item['description'] ) ? $item['description'] : '',
		);
		if ( ! empty( $item['page'] ) && ! empty( $pages[ $item['page'] ] ) ) {
			$args['menu-item-type']      = 'post_type';
			$args['menu-item-object']    = 'page';
			$args['menu-item-object-id'] = (int) $pages[ $item['page'] ];
		} else {
			$args['menu-item-type'] = 'custom';
			$args['menu-item-url']  = isset( $item['url'] ) ? $item['url'] : '#';
		}
		$item_id = wp_update_nav_menu_item( $menu_id, 0, $args );
		if ( ! is_wp_error( $item_id ) && ! empty( $item['children'] ) ) {
			rk_demo_add_menu_items( $menu_id, $item['children'], $item_id, $pages );
		}
	}
}
