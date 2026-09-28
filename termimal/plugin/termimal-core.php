<?php
/**
 * Plugin Name:       TERMIMAL Core
 * Plugin URI:        https://termimal.com
 * Description:       Companion plugin for the TERMIMAL portfolio theme. Registers the Product CPT, meta fields (external link, gallery, related products) and admin settings.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      8.0
 * Author:            TERMIMAL
 * Author URI:        https://termimal.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       termimal
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || exit;

define( 'TERMIMAL_CORE_VERSION', '1.0.0' );
define( 'TERMIMAL_CORE_FILE', __FILE__ );
define( 'TERMIMAL_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'TERMIMAL_CORE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main bootstrap.
 */
function termimal_core_init() {
	require_once TERMIMAL_CORE_PATH . 'includes/class-cpt.php';
	require_once TERMIMAL_CORE_PATH . 'includes/class-meta.php';
	require_once TERMIMAL_CORE_PATH . 'includes/class-admin.php';

	Termimal_CPT::register();
	Termimal_Meta::init();
	Termimal_Admin::init();
}
add_action( 'plugins_loaded', 'termimal_core_init' );

/**
 * Activation: flush rewrite rules so product archive & single work immediately.
 */
function termimal_core_activate() {
	require_once TERMIMAL_CORE_PATH . 'includes/class-cpt.php';
	Termimal_CPT::register();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'termimal_core_activate' );

/**
 * Deactivation: flush rewrite rules.
 */
function termimal_core_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'termimal_core_deactivate' );
