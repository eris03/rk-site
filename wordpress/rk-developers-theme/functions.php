<?php
/**
 * RK Developers theme bootstrap.
 *
 * @package RK_Developers
 */

defined( 'ABSPATH' ) || exit;

define( 'RK_THEME_VERSION', '1.0.0' );
define( 'RK_THEME_DIR', get_template_directory() );
define( 'RK_THEME_URI', get_template_directory_uri() );

require RK_THEME_DIR . '/inc/helpers.php';
require RK_THEME_DIR . '/inc/setup.php';
require RK_THEME_DIR . '/inc/customizer.php';
require RK_THEME_DIR . '/inc/projects.php';
require RK_THEME_DIR . '/inc/nav-walker.php';
require RK_THEME_DIR . '/inc/shortcodes.php';
require RK_THEME_DIR . '/inc/contact.php';
require RK_THEME_DIR . '/inc/seo.php';

if ( is_admin() ) {
	require RK_THEME_DIR . '/inc/demo-import.php';
}
