<?php
/**
 * Contact form handler.
 *
 * The contact page form, the floating "Message" button and the "Book a free site
 * visit" pop-up all post here. Each enquiry is
 *   1. e-mailed to the address in RK Site Settings (wp_mail), and
 *   2. saved under Dashboard → Enquiries, so nothing is lost if e-mail fails.
 *
 * @package RK_Developers
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		register_post_type(
			'rk_enquiry',
			array(
				'labels'          => array(
					'name'          => __( 'Enquiries', 'rk-developers' ),
					'singular_name' => __( 'Enquiry', 'rk-developers' ),
					'all_items'     => __( 'All Enquiries', 'rk-developers' ),
					'edit_item'     => __( 'Enquiry', 'rk-developers' ),
					'not_found'     => __( 'No enquiries yet.', 'rk-developers' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'menu_icon'       => 'dashicons-email-alt',
				'menu_position'   => 26,
				'supports'        => array( 'title', 'editor' ),
				'capability_type' => 'post',
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'    => true,
			)
		);
	}
);

add_action( 'wp_ajax_rk_contact', 'rk_handle_contact' );
add_action( 'wp_ajax_nopriv_rk_contact', 'rk_handle_contact' );

/** Validate, store and e-mail one enquiry. */
function rk_handle_contact() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- verified just below.
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'rk_contact' ) ) {
		wp_send_json_error( array( 'message' => __( 'Your session expired. Please reload the page and try again.', 'rk-developers' ) ), 403 );
	}
	// Honeypot: real visitors never fill this hidden field.
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success( array( 'message' => 'ok' ) );
	}

	$f = array();
	foreach ( array( 'name', 'phone', 'email', 'interest', 'budget', 'source', 'page' ) as $k ) {
		$f[ $k ] = isset( $_POST[ $k ] ) ? sanitize_text_field( wp_unslash( $_POST[ $k ] ) ) : '';
	}
	$f['message'] = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	// phpcs:enable

	$errors = array();
	if ( mb_strlen( $f['name'] ) < 2 ) {
		$errors['name'] = __( 'Enter your full name (min. 2 chars).', 'rk-developers' );
	}
	$digits = preg_replace( '/\D/', '', $f['phone'] );
	if ( strlen( $digits ) < 10 || strlen( $digits ) > 13 ) {
		$errors['phone'] = __( 'Enter a valid 10-digit mobile number.', 'rk-developers' );
	}
	if ( '' !== $f['email'] && ! is_email( $f['email'] ) ) {
		$errors['email'] = __( 'Enter a valid email address.', 'rk-developers' );
	}
	if ( $errors ) {
		wp_send_json_error(
			array(
				'message' => __( 'Please check the highlighted fields.', 'rk-developers' ),
				'errors'  => $errors,
			),
			422
		);
	}

	// Simple flood protection: max 5 enquiries per IP per 10 minutes.
	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$tkey  = 'rk_contact_' . md5( $ip );
	$count = (int) get_transient( $tkey );
	if ( $count >= 5 ) {
		wp_send_json_error( array( 'message' => __( 'Too many messages. Please call us instead.', 'rk-developers' ) ), 429 );
	}
	set_transient( $tkey, $count + 1, 10 * MINUTE_IN_SECONDS );

	$labels = array(
		'name'     => __( 'Name', 'rk-developers' ),
		'phone'    => __( 'Phone', 'rk-developers' ),
		'email'    => __( 'Email', 'rk-developers' ),
		'interest' => __( 'Interested in', 'rk-developers' ),
		'budget'   => __( 'Budget', 'rk-developers' ),
		'message'  => __( 'Message', 'rk-developers' ),
		'source'   => __( 'Form', 'rk-developers' ),
		'page'     => __( 'Sent from page', 'rk-developers' ),
	);
	$body = '';
	foreach ( $labels as $k => $label ) {
		if ( '' !== $f[ $k ] ) {
			$body .= $label . ': ' . $f[ $k ] . "\n";
		}
	}
	$body .= "\n" . sprintf( /* translators: date */ __( 'Received: %s', 'rk-developers' ), wp_date( 'j M Y, g:i a' ) ) . "\n";

	$source  = $f['source'] ? $f['source'] : __( 'Contact form', 'rk-developers' );
	$subject = sprintf( /* translators: 1: form name, 2: visitor name */ __( 'New Enquiry – %1$s – %2$s', 'rk-developers' ), $source, $f['name'] );

	wp_insert_post(
		array(
			'post_type'    => 'rk_enquiry',
			'post_status'  => 'private',
			'post_title'   => $f['name'] . ' – ' . $f['phone'],
			'post_content' => $body,
		)
	);

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( is_email( $f['email'] ) ) {
		$headers[] = 'Reply-To: ' . $f['name'] . ' <' . $f['email'] . '>';
	}
	$sent = wp_mail( rk_form_recipient(), $subject, $body, $headers );

	wp_send_json_success(
		array(
			'message' => 'ok',
			'mailed'  => (bool) $sent,
		)
	);
}

/* Enquiries list: show phone/date columns, read-only screen. */
add_filter(
	'manage_rk_enquiry_posts_columns',
	function ( $cols ) {
		return array(
			'cb'      => $cols['cb'],
			'title'   => __( 'Name – Phone', 'rk-developers' ),
			'rk_body' => __( 'Details', 'rk-developers' ),
			'date'    => __( 'Received', 'rk-developers' ),
		);
	}
);
add_action(
	'manage_rk_enquiry_posts_custom_column',
	function ( $col, $post_id ) {
		if ( 'rk_body' === $col ) {
			echo nl2br( esc_html( wp_trim_words( get_post_field( 'post_content', $post_id ), 40 ) ) );
		}
	},
	10,
	2
);
