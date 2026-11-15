<?php
/**
 * Appearance → Customize → "RK Site Settings".
 * One place for the phone, e-mail, WhatsApp, address, social links and footer text.
 *
 * @package RK_Developers
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'customize_register',
	function ( WP_Customize_Manager $wp_customize ) {
		$d = rk_setting_defaults();

		$wp_customize->add_panel(
			'rk_site',
			array(
				'title'       => __( 'RK Site Settings', 'rk-developers' ),
				'description' => __( 'Contact details, social links and footer text. They are used everywhere on the site: header, footer, contact page, floating buttons, AI assistant and pop-ups.', 'rk-developers' ),
				'priority'    => 25,
			)
		);

		$sections = array(
			'rk_contact' => __( 'Contact details', 'rk-developers' ),
			'rk_social'  => __( 'Social media links', 'rk-developers' ),
			'rk_footer'  => __( 'Footer & company', 'rk-developers' ),
			'rk_popup'   => __( 'Pop-up', 'rk-developers' ),
		);
		$i = 10;
		foreach ( $sections as $id => $title ) {
			$wp_customize->add_section(
				$id,
				array(
					'title'    => $title,
					'panel'    => 'rk_site',
					'priority' => $i += 10,
				)
			);
		}

		$fields = array(
			// key => [section, label, type, sanitize, description].
			'phone'          => array( 'rk_contact', __( 'Phone number', 'rk-developers' ), 'text', 'sanitize_text_field', __( 'Shown as written, e.g. +91 9663108999. Call buttons dial this number.', 'rk-developers' ) ),
			'whatsapp'       => array( 'rk_contact', __( 'WhatsApp number', 'rk-developers' ), 'text', 'rk_sanitize_digits', __( 'Country code + number, digits only, e.g. 919663108999.', 'rk-developers' ) ),
			'email'          => array( 'rk_contact', __( 'E-mail address', 'rk-developers' ), 'email', 'sanitize_email', __( 'Shown on the site.', 'rk-developers' ) ),
			'form_recipient' => array( 'rk_contact', __( 'Send contact-form enquiries to', 'rk-developers' ), 'email', 'sanitize_email', __( 'Leave empty to use the e-mail address above.', 'rk-developers' ) ),
			'address'        => array( 'rk_contact', __( 'Office address', 'rk-developers' ), 'textarea', 'sanitize_textarea_field', __( 'One line per row. The contact page shows it line by line, the footer on one line.', 'rk-developers' ) ),
			'maps_url'       => array( 'rk_contact', __( 'Google Maps link', 'rk-developers' ), 'url', 'esc_url_raw', __( 'Opened when visitors click the map in the footer.', 'rk-developers' ) ),
			'hours'          => array( 'rk_contact', __( 'Office hours', 'rk-developers' ), 'textarea', 'sanitize_textarea_field', __( 'One line per row.', 'rk-developers' ) ),
			'facebook'       => array( 'rk_social', __( 'Facebook page URL', 'rk-developers' ), 'url', 'esc_url_raw', __( 'Leave empty to hide the icon.', 'rk-developers' ) ),
			'instagram'      => array( 'rk_social', __( 'Instagram URL', 'rk-developers' ), 'url', 'esc_url_raw', __( 'Leave empty to hide the icon.', 'rk-developers' ) ),
			'youtube'        => array( 'rk_social', __( 'YouTube URL', 'rk-developers' ), 'url', 'esc_url_raw', __( 'Optional.', 'rk-developers' ) ),
			'linkedin'       => array( 'rk_social', __( 'LinkedIn URL', 'rk-developers' ), 'url', 'esc_url_raw', __( 'Optional.', 'rk-developers' ) ),
			'x'              => array( 'rk_social', __( 'X (Twitter) URL', 'rk-developers' ), 'url', 'esc_url_raw', __( 'Optional.', 'rk-developers' ) ),
			'founded_year'   => array( 'rk_footer', __( 'Founding year', 'rk-developers' ), 'text', 'rk_sanitize_digits', __( 'Used wherever the site says "since …".', 'rk-developers' ) ),
			'footer_tagline' => array( 'rk_footer', __( 'Footer tagline', 'rk-developers' ), 'textarea', 'sanitize_textarea_field', __( 'Text under the logo in the footer. [rk_since] is replaced by the founding year.', 'rk-developers' ) ),
			'copyright'      => array( 'rk_footer', __( 'Copyright text', 'rk-developers' ), 'text', 'sanitize_text_field', __( 'Shown after "© <current year>".', 'rk-developers' ) ),
			'popup_enabled'  => array( 'rk_popup', __( 'Show the "Interested in a Property?" pop-up', 'rk-developers' ), 'checkbox', 'rk_sanitize_checkbox', '' ),
			'popup_delay'    => array( 'rk_popup', __( 'Show it every … seconds', 'rk-developers' ), 'number', 'absint', '' ),
		);

		foreach ( $fields as $key => $f ) {
			$wp_customize->add_setting(
				'rk_' . $key,
				array(
					'default'           => $d[ $key ],
					'sanitize_callback' => $f[3],
					'transport'         => 'refresh',
				)
			);
			$wp_customize->add_control(
				'rk_' . $key,
				array(
					'label'       => $f[1],
					'description' => $f[4],
					'section'     => $f[0],
					'type'        => $f[2],
				)
			);
		}

		$wp_customize->add_setting(
			'rk_map_image',
			array(
				'default'           => '',
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'rk_map_image',
				array(
					'label'       => __( 'Footer map image', 'rk-developers' ),
					'description' => __( 'Small map picture in the footer. Leave empty for the default.', 'rk-developers' ),
					'section'     => 'rk_contact',
					'mime_type'   => 'image',
				)
			)
		);
	}
);

/** Keep only digits. */
function rk_sanitize_digits( $value ) {
	return preg_replace( '/\D/', '', (string) $value );
}

/** Checkbox to boolean. */
function rk_sanitize_checkbox( $value ) {
	return (bool) $value;
}
