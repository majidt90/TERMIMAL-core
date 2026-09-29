<?php
/**
 * Gutenberg blocks: Product Grid.
 *
 * @package Termimal_Core
 */

defined( 'ABSPATH' ) || exit;

class Termimal_Blocks {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_blocks' ) );
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'editor_assets' ) );
	}

	public static function register_blocks() {
		register_block_type(
			'termimal/product-grid',
			array(
				'api_version'     => 2,
				'title'           => __( 'TERMIMAL Product Grid', 'termimal' ),
				'description'     => __( 'Display a grid of portfolio products.', 'termimal' ),
				'category'        => 'widgets',
				'icon'            => 'portfolio',
				'keywords'        => array( 'product', 'portfolio', 'termimal', 'grid' ),
				'attributes'      => array(
					'limit'    => array( 'type' => 'number', 'default' => 6 ),
					'columns'  => array( 'type' => 'number', 'default' => 3 ),
					'featured' => array( 'type' => 'boolean', 'default' => false ),
					'category' => array( 'type' => 'string', 'default' => '' ),
					'status'   => array( 'type' => 'string', 'default' => '' ),
				),
				'render_callback' => array( __CLASS__, 'render_product_grid' ),
				'supports'        => array( 'align' => array( 'wide', 'full' ), 'html' => false ),
			)
		);
	}

	public static function editor_assets() {
		wp_enqueue_script(
			'termimal-blocks-editor',
			TERMIMAL_CORE_URL . 'assets/js/blocks-editor.js',
			array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render' ),
			TERMIMAL_CORE_VERSION,
			true
		);
	}

	public static function render_product_grid( $attributes ) {
		$limit    = isset( $attributes['limit'] ) ? max( 1, min( 24, (int) $attributes['limit'] ) ) : 6;
		$columns  = isset( $attributes['columns'] ) ? max( 1, min( 4, (int) $attributes['columns'] ) ) : 3;
		$featured = ! empty( $attributes['featured'] );
		$category = isset( $attributes['category'] ) ? sanitize_title( $attributes['category'] ) : '';
		$status   = isset( $attributes['status'] ) ? sanitize_key( $attributes['status'] ) : '';
		$args = array(
			'post_type' => 'termimal_product', 'post_status' => 'publish',
			'posts_per_page' => $limit, 'orderby' => 'date', 'order' => 'DESC',
		);
		if ( $featured && class_exists( 'Termimal_Meta' ) ) {
			$args['meta_key'] = Termimal_Meta::META_FEATURED;
			$args['meta_value'] = '1';
		}
		if ( $status && class_exists( 'Termimal_Meta' ) ) {
			$args['meta_query'][] = array( 'key' => Termimal_Meta::META_STATUS, 'value' => $status );
		}
		if ( $category ) {
			$args['tax_query'][] = array( 'taxonomy' => 'product_category', 'field' => 'slug', 'terms' => $category );
		}
		$q = new WP_Query( $args );
		if ( ! $q->have_posts() ) {
			return '<p class="termimal-empty">' . esc_html__( 'No products found.', 'termimal' ) . '</p>';
		}
		$html = '<div class="termimal-products-grid termimal-block-grid" style="display:grid;grid-template-columns:repeat(' . esc_attr( (string) $columns ) . ',minmax(0,1fr));gap:1.5rem;">';
		while ( $q->have_posts() ) {
			$q->the_post();
			$id = get_the_ID();
			$html .= '<article class="termimal-product-card termimal-product-card--block">';
			if ( class_exists( 'Termimal_Frontend' ) && method_exists( 'Termimal_Frontend', 'get_badges_html' ) ) {
				$html .= Termimal_Frontend::get_badges_html( $id );
			}
			if ( has_post_thumbnail() ) {
				$html .= '<a href="' . esc_url( get_permalink() ) . '" class="termimal-product-card__image">';
				$html .= get_the_post_thumbnail( $id, 'termimal-product-card', array( 'loading' => 'lazy', 'decoding' => 'async' ) );
				$html .= '</a>';
			}
			$html .= '<h3 class="termimal-product-card__title"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
			$html .= '<div class="termimal-product-card__excerpt">' . wp_kses_post( wp_trim_words( get_the_excerpt(), 18 ) ) . '</div></article>';
		}
		wp_reset_postdata();
		return $html . '</div>';
	}
}
