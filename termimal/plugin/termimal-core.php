<?php
/**
 * Plugin Name:       TERMIMAL Core
 * Plugin URI:        https://termimal.com
 * Description:       TERMIMAL portfolio: products, Q&A, FAQ, EN/FA + Polylang dual content, Audiowide/Vazirmatn, GA4, Gutenberg grid, SEO.
 * Version:           1.9.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            TERMIMAL
 * Author URI:        https://termimal.com
 * License:           GPL-2.0-or-later
 * Text Domain:       termimal
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || exit;

define( 'TERMIMAL_CORE_VERSION', '1.9.0' );
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
		TERMIMAL_CORE_PATH . 'includes/class-seo.php',
		TERMIMAL_CORE_PATH . 'includes/class-contact.php',
		TERMIMAL_CORE_PATH . 'includes/class-comments.php',
		TERMIMAL_CORE_PATH . 'includes/class-votes.php',
		TERMIMAL_CORE_PATH . 'includes/class-export.php',
		TERMIMAL_CORE_PATH . 'includes/class-blocks.php',
		TERMIMAL_CORE_PATH . 'includes/class-performance.php',
		TERMIMAL_CORE_PATH . 'includes/class-i18n.php',
		TERMIMAL_CORE_PATH . 'includes/class-analytics.php',
		TERMIMAL_CORE_PATH . 'includes/class-polylang.php',
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

function termimal_core_boot_i18n() {
	$file = TERMIMAL_CORE_PATH . 'includes/class-i18n.php';
	if ( file_exists( $file ) ) {
		require_once $file;
		if ( class_exists( 'Termimal_I18n' ) ) {
			Termimal_I18n::init();
		}
	}
}
add_action( 'plugins_loaded', 'termimal_core_boot_i18n', 0 );

function termimal_core_init() {
	if ( ! termimal_core_load() ) {
		return;
	}
	load_plugin_textdomain( 'termimal', false, dirname( plugin_basename( TERMIMAL_CORE_FILE ) ) . '/languages' );
	foreach ( array( 'Termimal_Meta', 'Termimal_Admin', 'Termimal_Frontend', 'Termimal_SEO', 'Termimal_Contact', 'Termimal_Comments', 'Termimal_Votes', 'Termimal_Export', 'Termimal_Blocks', 'Termimal_Performance', 'Termimal_Analytics', 'Termimal_Polylang' ) as $class ) {
		if ( class_exists( $class ) && method_exists( $class, 'init' ) ) {
			call_user_func( array( $class, 'init' ) );
		}
	}
	if ( class_exists( 'Termimal_Polylang' ) ) {
		add_action( 'admin_notices', array( 'Termimal_Polylang', 'admin_hint' ) );
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
	update_option( 'thread_comments', 1 );
	update_option( 'thread_comments_depth', 5 );
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'termimal_core_activate' );

function termimal_core_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'termimal_core_deactivate' );
