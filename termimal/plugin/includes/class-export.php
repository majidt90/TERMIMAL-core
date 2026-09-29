<?php
/**
 * CSV export of products.
 *
 * @package Termimal_Core
 */

defined( 'ABSPATH' ) || exit;

class Termimal_Export {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'submenu' ) );
		add_action( 'admin_init', array( __CLASS__, 'maybe_export' ) );
	}

	public static function submenu() {
		add_submenu_page(
			'edit.php?post_type=termimal_product',
			__( 'Export CSV', 'termimal' ),
			__( 'Export CSV', 'termimal' ),
			'export',
			'termimal-export',
			array( __CLASS__, 'render_page' )
		);
	}

	public static function render_page() {
		if ( ! current_user_can( 'export' ) ) {
			return;
		}
		$url = wp_nonce_url( admin_url( 'edit.php?post_type=termimal_product&page=termimal-export&termimal_export=1' ), 'termimal_export' );
		echo '<div class="wrap"><h1>' . esc_html__( 'Export products CSV', 'termimal' ) . '</h1>';
		echo '<p>' . esc_html__( 'Download all published products with key meta fields.', 'termimal' ) . '</p>';
		echo '<p><a class="button button-primary" href="' . esc_url( $url ) . '">' . esc_html__( 'Download CSV', 'termimal' ) . '</a></p></div>';
	}

	public static function maybe_export() {
		if ( empty( $_GET['termimal_export'] ) || empty( $_GET['page'] ) || 'termimal-export' !== $_GET['page'] ) {
			return;
		}
		if ( ! current_user_can( 'export' ) ) {
			return;
		}
		check_admin_referer( 'termimal_export' );
		$products = get_posts( array( 'post_type' => 'termimal_product', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=termimal-products-' . gmdate( 'Y-m-d' ) . '.csv' );
		$out = fopen( 'php://output', 'w' );
		fputcsv( $out, array( 'ID', 'Title', 'Status', 'Version', 'Year', 'Tech', 'URL', 'Demo', 'Docs', 'Categories', 'Featured', 'Excerpt' ) );
		foreach ( $products as $p ) {
			$terms = get_the_terms( $p->ID, 'product_category' );
			$cats = ( $terms && ! is_wp_error( $terms ) ) ? implode( '|', wp_list_pluck( $terms, 'name' ) ) : '';
			fputcsv( $out, array(
				$p->ID,
				$p->post_title,
				class_exists( 'Termimal_Meta' ) ? Termimal_Meta::get_status( $p->ID ) : '',
				class_exists( 'Termimal_Meta' ) ? Termimal_Meta::get_version( $p->ID ) : '',
				class_exists( 'Termimal_Meta' ) ? Termimal_Meta::get_year( $p->ID ) : '',
				class_exists( 'Termimal_Meta' ) ? Termimal_Meta::get_tech( $p->ID ) : '',
				class_exists( 'Termimal_Meta' ) ? Termimal_Meta::get_url( $p->ID ) : '',
				class_exists( 'Termimal_Meta' ) ? Termimal_Meta::get_demo_url( $p->ID ) : '',
				class_exists( 'Termimal_Meta' ) ? Termimal_Meta::get_docs_url( $p->ID ) : '',
				$cats,
				class_exists( 'Termimal_Meta' ) && Termimal_Meta::is_featured( $p->ID ) ? '1' : '0',
				wp_strip_all_tags( $p->post_excerpt ),
			) );
		}
		fclose( $out );
		exit;
	}
}
