<?php
/**
 * Admin Getting Started guide (Phase H3 / H5).
 *
 * @package Termimal_Core
 */

defined( 'ABSPATH' ) || exit;

class Termimal_Guide {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
	}

	public static function menu() {
		add_submenu_page(
			'edit.php?post_type=termimal_product',
			__( 'Getting Started', 'termimal' ),
			__( 'Getting Started', 'termimal' ),
			'edit_posts',
			'termimal-guide',
			array( __CLASS__, 'render' )
		);
	}

	public static function render() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}
		$checks = self::smoke_checks();
		$pll = class_exists( 'Termimal_Polylang' ) && Termimal_Polylang::active();
		echo '<div class="wrap termimal-guide"><h1>' . esc_html__( 'TERMIMAL — Getting Started', 'termimal' ) . '</h1>';
		echo '<p>' . esc_html__( 'Quick setup for the portfolio theme and core plugin.', 'termimal' ) . '</p>';
		echo '<h2>' . esc_html__( 'Activation checklist', 'termimal' ) . '</h2>';
		echo '<table class="widefat striped" style="max-width:720px"><thead><tr><th>' . esc_html__( 'Check', 'termimal' ) . '</th><th>' . esc_html__( 'Status', 'termimal' ) . '</th></tr></thead><tbody>';
		foreach ( $checks as $row ) {
			echo '<tr><td>' . esc_html( $row['label'] ) . '</td><td>';
			if ( $row['ok'] ) {
				echo '<span style="color:#00a32a;font-weight:600;">✓ ' . esc_html__( 'OK', 'termimal' ) . '</span>';
			} else {
				echo '<span style="color:#d63638;font-weight:600;">✗ ' . esc_html__( 'Needs attention', 'termimal' ) . '</span>';
				if ( ! empty( $row['hint'] ) ) {
					echo '<br><span class="description">' . esc_html( $row['hint'] ) . '</span>';
				}
			}
			echo '</td></tr>';
		}
		echo '</tbody></table>';
		echo '<h2>' . esc_html__( 'Add your first product', 'termimal' ) . '</h2><ol>';
		echo '<li>' . esc_html__( 'Go to Products → Add New.', 'termimal' ) . '</li>';
		echo '<li>' . esc_html__( 'Enter title, description and set a cover image.', 'termimal' ) . '</li>';
		echo '<li>' . esc_html__( 'Fill Product Details (Visit / Demo / Docs), gallery, status, tech stack.', 'termimal' ) . '</li>';
		echo '<li>' . esc_html__( 'Optionally set Related products, FAQ, Version & Changelog.', 'termimal' ) . '</li>';
		echo '<li>' . esc_html__( 'Publish. Archive is available at /product/.', 'termimal' ) . '</li></ol>';
		echo '<h2>' . esc_html__( 'Languages (English + فارسی)', 'termimal' ) . '</h2>';
		if ( $pll ) {
			echo '<p style="color:#00a32a;font-weight:600;">' . esc_html__( 'Polylang is active. Create a product in English, then use the language box to add the فارسی translation. Related IDs and categories are mapped automatically when possible.', 'termimal' ) . '</p>';
		} else {
			echo '<p>' . esc_html__( 'Without Polylang: use the header language switcher (?lang=en / ?lang=fa) for UI locale and fonts only.', 'termimal' ) . '</p>';
			echo '<p>' . esc_html__( 'For separate EN/FA product content, install Polylang and enable only English and فارسی.', 'termimal' ) . '</p>';
		}
		echo '<h2>' . esc_html__( 'Useful links', 'termimal' ) . '</h2><ul>';
		echo '<li><a href="' . esc_url( admin_url( 'edit.php?post_type=termimal_product' ) ) . '">' . esc_html__( 'All Products', 'termimal' ) . '</a></li>';
		echo '<li><a href="' . esc_url( admin_url( 'options-general.php?page=termimal-settings' ) ) . '">' . esc_html__( 'TERMIMAL Settings', 'termimal' ) . '</a></li>';
		echo '<li><a href="' . esc_url( admin_url( 'edit.php?post_type=termimal_product&page=termimal-export' ) ) . '">' . esc_html__( 'Export CSV', 'termimal' ) . '</a></li>';
		echo '</ul>';
		echo '<p class="description">' . esc_html( sprintf( __( 'TERMIMAL Core version %s', 'termimal' ), defined( 'TERMIMAL_CORE_VERSION' ) ? TERMIMAL_CORE_VERSION : '' ) ) . '</p></div>';
	}

	public static function smoke_checks() {
		$checks = array();
		$checks[] = array( 'label' => __( 'PHP version ≥ 7.4', 'termimal' ), 'ok' => version_compare( PHP_VERSION, '7.4', '>=' ), 'hint' => sprintf( __( 'Current: %s', 'termimal' ), PHP_VERSION ) );
		$checks[] = array( 'label' => __( 'WordPress version ≥ 6.0', 'termimal' ), 'ok' => version_compare( get_bloginfo( 'version' ), '6.0', '>=' ), 'hint' => sprintf( __( 'Current: %s', 'termimal' ), get_bloginfo( 'version' ) ) );
		$checks[] = array( 'label' => __( 'Product post type registered', 'termimal' ), 'ok' => post_type_exists( 'termimal_product' ), 'hint' => __( 'Re-activate TERMIMAL Core plugin.', 'termimal' ) );
		$checks[] = array( 'label' => __( 'Product category taxonomy registered', 'termimal' ), 'ok' => taxonomy_exists( 'product_category' ), 'hint' => __( 'Re-activate TERMIMAL Core plugin.', 'termimal' ) );
		$theme = wp_get_theme();
		$is_termimal = ( false !== stripos( $theme->get( 'Name' ), 'termimal' ) ) || ( false !== stripos( $theme->get_stylesheet(), 'termimal' ) );
		$checks[] = array( 'label' => __( 'TERMIMAL theme active', 'termimal' ), 'ok' => $is_termimal, 'hint' => __( 'Appearance → Themes → activate TERMIMAL Portfolio.', 'termimal' ) );
		$checks[] = array( 'label' => __( 'Pretty permalinks (not Plain)', 'termimal' ), 'ok' => (bool) get_option( 'permalink_structure' ), 'hint' => __( 'Settings → Permalinks → Post name (or any non-Plain).', 'termimal' ) );
		$checks[] = array( 'label' => __( 'Polylang (optional, for dual content)', 'termimal' ), 'ok' => class_exists( 'Termimal_Polylang' ) && Termimal_Polylang::active(), 'hint' => __( 'Optional. Install Polylang for separate EN/FA product posts.', 'termimal' ) );
		$rules = get_option( 'rewrite_rules' );
		$has_product = is_array( $rules ) && (bool) preg_grep( '/product/', array_keys( $rules ) );
		$checks[] = array( 'label' => __( 'Rewrite rules include /product/', 'termimal' ), 'ok' => $has_product || ! get_option( 'permalink_structure' ), 'hint' => __( 'Settings → Permalinks → Save (flush rules).', 'termimal' ) );
		return $checks;
	}
}
