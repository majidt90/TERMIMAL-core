<?php
/**
 * Performance: lazy images, sizes, script defer.
 *
 * @package Termimal_Core
 */

defined( 'ABSPATH' ) || exit;

class Termimal_Performance {

	public static function init() {
		add_filter( 'wp_get_attachment_image_attributes', array( __CLASS__, 'image_attributes' ), 10, 3 );
		add_filter( 'wp_lazy_loading_enabled', '__return_true' );
		add_filter( 'script_loader_tag', array( __CLASS__, 'script_strategy' ), 10, 3 );
		add_filter( 'wp_calculate_image_sizes', array( __CLASS__, 'gallery_sizes' ), 10, 5 );
	}

	public static function image_attributes( $attr, $attachment, $size ) {
		if ( empty( $attr['loading'] ) ) {
			$attr['loading'] = 'lazy';
		}
		if ( empty( $attr['decoding'] ) ) {
			$attr['decoding'] = 'async';
		}
		if ( empty( $attr['sizes'] ) ) {
			if ( is_string( $size ) && false !== strpos( $size, 'gallery' ) ) {
				$attr['sizes'] = '(max-width: 782px) 100vw, (max-width: 1200px) 50vw, 480px';
			} elseif ( is_string( $size ) && false !== strpos( $size, 'card' ) ) {
				$attr['sizes'] = '(max-width: 782px) 100vw, (max-width: 1200px) 33vw, 320px';
			}
		}
		return $attr;
	}

	public static function gallery_sizes( $sizes, $size, $image_src, $image_meta, $attachment_id ) {
		if ( is_singular( 'termimal_product' ) ) {
			return '(max-width: 782px) 100vw, (max-width: 1200px) 50vw, 640px';
		}
		if ( is_post_type_archive( 'termimal_product' ) || is_tax( 'product_category' ) ) {
			return '(max-width: 782px) 100vw, (max-width: 1200px) 33vw, 320px';
		}
		return $sizes;
	}

	public static function script_strategy( $tag, $handle, $src ) {
		$defer = array( 'termimal-frontend', 'termimal-votes', 'termimal-analytics', 'termimal-main' );
		if ( in_array( $handle, $defer, true ) && false === strpos( $tag, ' defer' ) ) {
			$tag = str_replace( ' src', ' defer src', $tag );
		}
		return $tag;
	}
}
