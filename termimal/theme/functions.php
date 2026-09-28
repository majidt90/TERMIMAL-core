<?php
/**
 * TERMIMAL Portfolio Theme functions and definitions
 *
 * @package Termimal
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

function termimal_setup() {
	load_theme_textdomain( 'termimal', get_template_directory() . '/languages' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'termimal-product-card', 640, 480, true );
	add_image_size( 'termimal-product-hero', 1280, 720, true );
	add_image_size( 'termimal-product-gallery', 960, 720, false );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'termimal' ),
		'footer'  => __( 'Footer Menu', 'termimal' ),
	) );
}
add_action( 'after_setup_theme', 'termimal_setup' );

function termimal_enqueue_assets() {
	$v = wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'termimal-main', get_template_directory_uri() . '/assets/css/main.css', array(), $v );
	wp_enqueue_style( 'termimal-animations', get_template_directory_uri() . '/assets/css/animations.css', array( 'termimal-main' ), $v );
	wp_enqueue_style( 'termimal-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap', array(), null );
	wp_enqueue_script( 'termimal-main', get_template_directory_uri() . '/assets/js/main.js', array(), $v, true );
	wp_localize_script( 'termimal-main', 'termimalData', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ), 'homeUrl' => home_url( '/' ) ) );
}
add_action( 'wp_enqueue_scripts', 'termimal_enqueue_assets' );

function termimal_body_classes( $classes ) {
	$classes[] = 'termimal-theme';
	$classes[] = 'dark-mode';
	return $classes;
}
add_filter( 'body_class', 'termimal_body_classes' );

function termimal_register_pattern_categories() {
	register_block_pattern_category( 'termimal', array( 'label' => __( 'TERMIMAL', 'termimal' ) ) );
}
add_action( 'init', 'termimal_register_pattern_categories' );

function termimal_append_product_meta( $content ) {
	if ( ! is_singular( 'termimal_product' ) || ! in_the_loop() || ! is_main_query() || ! class_exists( 'Termimal_Meta' ) ) {
		return $content;
	}

	$post_id = get_the_ID();
	$extra   = '';

	$status_key = Termimal_Meta::get_status( $post_id );
	$statuses   = Termimal_Meta::get_statuses();
	$status_lbl = isset( $statuses[ $status_key ] ) ? $statuses[ $status_key ] : $status_key;
	$year       = Termimal_Meta::get_year( $post_id );
	$tech       = Termimal_Meta::get_tech( $post_id );
	$terms      = get_the_terms( $post_id, 'product_category' );

	$chips   = array();
	$chips[] = '<span class="termimal-chip termimal-status-' . esc_attr( $status_key ) . '">' . esc_html( $status_lbl ) . '</span>';
	if ( $year ) {
		$chips[] = '<span class="termimal-chip">' . esc_html( $year ) . '</span>';
	}
	if ( $tech ) {
		$chips[] = '<span class="termimal-chip termimal-tech">' . esc_html( $tech ) . '</span>';
	}
	if ( $terms && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$chips[] = '<a class="termimal-chip termimal-cat" href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>';
		}
	}
	$extra .= '<div class="termimal-meta-bar" style="display:flex;flex-wrap:wrap;gap:0.5rem;margin:1.5rem 0;">' . implode( '', $chips ) . '</div>';

	$url = Termimal_Meta::get_url( $post_id );
	if ( $url ) {
		$extra .= '<p class="termimal-product-link" style="margin:1.5rem 0;"><a class="wp-block-button__link" href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Visit product', 'termimal' ) . '</a></p>';
	}

	$gallery_ids = Termimal_Meta::get_gallery_ids( $post_id );
	if ( ! empty( $gallery_ids ) ) {
		$extra .= '<div class="termimal-gallery" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem;margin:2rem 0;">';
		foreach ( $gallery_ids as $id ) {
			$extra .= wp_get_attachment_image( $id, 'termimal-product-gallery', false, array( 'style' => 'width:100%;height:auto;border-radius:8px;' ) );
		}
		$extra .= '</div>';
	}

	$options      = get_option( 'termimal_options', array() );
	$show_related = ! isset( $options['show_related'] ) || ! empty( $options['show_related'] );
	if ( $show_related ) {
		$related_ids = Termimal_Meta::get_related_ids( $post_id );
		if ( ! empty( $related_ids ) ) {
			$extra .= '<section class="termimal-related" style="margin-top:3rem;"><h3 style="font-family:var(--wp--preset--font-family--display);font-size:1.5rem;margin-bottom:1rem;">' . esc_html__( 'Related products', 'termimal' ) . '</h3>';
			$extra .= '<ul class="termimal-related-list" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1.5rem;list-style:none;padding:0;margin:0;">';
			foreach ( $related_ids as $rid ) {
				$related = get_post( $rid );
				if ( ! $related || 'publish' !== $related->post_status ) { continue; }
				$extra .= '<li class="termimal-product-card" style="background:var(--wp--preset--color--gray-900,#111);border:1px solid var(--wp--preset--color--gray-700,#2A2A2A);border-radius:12px;padding:1rem;">';
				if ( has_post_thumbnail( $rid ) ) {
					$extra .= '<a href="' . esc_url( get_permalink( $rid ) ) . '">' . get_the_post_thumbnail( $rid, 'termimal-product-card', array( 'style' => 'width:100%;height:auto;border-radius:8px;margin-bottom:0.75rem;' ) ) . '</a>';
				}
				$extra .= '<a href="' . esc_url( get_permalink( $rid ) ) . '" style="font-weight:600;text-decoration:none;">' . esc_html( get_the_title( $rid ) ) . '</a></li>';
			}
			$extra .= '</ul></section>';
		}
	}

	return $content . $extra;
}
add_filter( 'the_content', 'termimal_append_product_meta' );

function termimal_featured_query_loop( $query, $block, $page ) {
	$post_type = isset( $query['post_type'] ) ? $query['post_type'] : '';
	if ( is_array( $post_type ) ) { $post_type = reset( $post_type ); }
	if ( 'termimal_product' !== $post_type || ( ! is_front_page() && ! is_home() ) || ! class_exists( 'Termimal_Meta' ) ) {
		return $query;
	}
	$featured = get_posts( array(
		'post_type' => 'termimal_product', 'post_status' => 'publish', 'posts_per_page' => 1, 'fields' => 'ids',
		'meta_key' => Termimal_Meta::META_FEATURED, 'meta_value' => '1',
	) );
	if ( ! empty( $featured ) ) {
		$query['meta_key']   = Termimal_Meta::META_FEATURED;
		$query['meta_value'] = '1';
	}
	return $query;
}
add_filter( 'query_loop_block_query_vars', 'termimal_featured_query_loop', 10, 3 );
