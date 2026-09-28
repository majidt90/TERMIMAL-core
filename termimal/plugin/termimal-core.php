<?php
/**
 * Plugin Name:       TERMIMAL Core
 * Plugin URI:        https://termimal.com
 * Description:       Companion plugin for the TERMIMAL portfolio theme. Products, categories, gallery lightbox, filters, search, sort, badges, and related products.
 * Version:           1.2.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            TERMIMAL
 * Author URI:        https://termimal.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       termimal
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || exit;

define( 'TERMIMAL_CORE_VERSION', '1.2.0' );
define( 'TERMIMAL_CORE_FILE', __FILE__ );
define( 'TERMIMAL_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'TERMIMAL_CORE_URL', plugin_dir_url( __FILE__ ) );

function termimal_core_load() {
	$includes = array(
		TERMIMAL_CORE_PATH . 'includes/class-cpt.php',
		TERMIMAL_CORE_PATH . 'includes/class-taxonomy.php',
		TERMIMAL_CORE_PATH . 'includes/class-meta.php',
		TERMIMAL_CORE_PATH . 'includes/class-admin.php',
		TERMIMAL_CORE_PATH . 'includes/class-frontend.php',
	);
	foreach ( $includes as $file ) {
		if ( ! file_exists( $file ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'TERMIMAL Core: missing file ' . $file );
			}
			return false;
		}
		require_once $file;
	}
	return true;
}

function termimal_core_init() {
	if ( ! termimal_core_load() ) {
		return;
	}
	if ( class_exists( 'Termimal_Meta' ) ) {
		Termimal_Meta::init();
	}
	if ( class_exists( 'Termimal_Admin' ) ) {
		Termimal_Admin::init();
	}
	if ( class_exists( 'Termimal_Frontend' ) ) {
		Termimal_Frontend::init();
	}
}
add_action( 'plugins_loaded', 'termimal_core_init' );

function termimal_core_register_types() {
	if ( ! class_exists( 'Termimal_CPT' ) ) {
		termimal_core_load();
	}
	if ( class_exists( 'Termimal_CPT' ) ) {
		Termimal_CPT::register();
	}
	if ( class_exists( 'Termimal_Taxonomy' ) ) {
		Termimal_Taxonomy::register();
	}
}
add_action( 'init', 'termimal_core_register_types', 5 );

function termimal_core_activate() {
	termimal_core_load();
	if ( class_exists( 'Termimal_CPT' ) ) {
		Termimal_CPT::register();
	}
	if ( class_exists( 'Termimal_Taxonomy' ) ) {
		Termimal_Taxonomy::register();
	}
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'termimal_core_activate' );

function termimal_core_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'termimal_core_deactivate' );
