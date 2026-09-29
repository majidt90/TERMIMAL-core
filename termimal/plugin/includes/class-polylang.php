<?php
/**
 * Polylang integration for dual content (EN + FA).
 *
 * @package Termimal_Core
 */

defined( 'ABSPATH' ) || exit;

class Termimal_Polylang {

	public static function meta_keys() {
		if ( ! class_exists( 'Termimal_Meta' ) ) {
			return array();
		}
		return array(
			Termimal_Meta::META_URL,
			Termimal_Meta::META_DEMO_URL,
			Termimal_Meta::META_DOCS_URL,
			Termimal_Meta::META_GALLERY,
			Termimal_Meta::META_RELATED,
			Termimal_Meta::META_TECH,
			Termimal_Meta::META_STATUS,
			Termimal_Meta::META_YEAR,
			Termimal_Meta::META_FEATURED,
			Termimal_Meta::META_VERSION,
			Termimal_Meta::META_CHANGELOG,
			Termimal_Meta::META_FAQ,
		);
	}

	public static function active() {
		return function_exists( 'pll_current_language' ) && function_exists( 'pll_get_post' ) && function_exists( 'pll_languages_list' );
	}

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_strings' ), 20 );
		add_filter( 'pll_get_post_types', array( __CLASS__, 'register_post_types' ), 10, 2 );
		add_filter( 'pll_get_taxonomies', array( __CLASS__, 'register_taxonomies' ), 10, 2 );
		add_action( 'pll_save_post', array( __CLASS__, 'copy_meta_on_translation' ), 10, 3 );
		add_filter( 'termimal_current_lang', array( __CLASS__, 'filter_current_lang' ) );
		add_filter( 'termimal_lang_switcher_html', array( __CLASS__, 'maybe_pll_switcher' ), 10, 1 );
	}

	public static function register_post_types( $types, $is_settings = false ) {
		$types['termimal_product'] = 'termimal_product';
		return $types;
	}

	public static function register_taxonomies( $taxonomies, $is_settings = false ) {
		$taxonomies['product_category'] = 'product_category';
		return $taxonomies;
	}

	public static function register_strings() {
		if ( ! function_exists( 'pll_register_string' ) ) {
			return;
		}
		foreach ( array( 'Visit product', 'Try demo', 'Documentation', 'Related products', 'FAQ', 'Discussion', 'Changelog' ) as $s ) {
			pll_register_string( sanitize_title( $s ), $s, 'termimal', false );
		}
	}

	public static function copy_meta_on_translation( $post_id, $post, $translations ) {
		if ( ! $post || 'termimal_product' !== $post->post_type || ! self::active() ) {
			return;
		}
		$source_id = 0;
		if ( is_array( $translations ) ) {
			foreach ( $translations as $lang => $id ) {
				$id = (int) $id;
				if ( $id && $id !== (int) $post_id ) {
					$source_id = $id;
					break;
				}
			}
		}
		if ( ! $source_id ) {
			return;
		}
		foreach ( self::meta_keys() as $key ) {
			$existing = get_post_meta( $post_id, $key, true );
			if ( '' !== $existing && null !== $existing && array() !== $existing ) {
				continue;
			}
			$val = get_post_meta( $source_id, $key, true );
			if ( '' === $val || null === $val ) {
				continue;
			}
			update_post_meta( $post_id, $key, $val );
		}
		if ( ! has_post_thumbnail( $post_id ) && has_post_thumbnail( $source_id ) ) {
			set_post_thumbnail( $post_id, get_post_thumbnail_id( $source_id ) );
		}
	}

	public static function filter_current_lang( $lang ) {
		if ( ! self::active() ) {
			return $lang;
		}
		$pll = pll_current_language( 'slug' );
		if ( ! $pll ) {
			return $lang;
		}
		if ( 0 === strpos( $pll, 'fa' ) ) {
			return 'fa';
		}
		if ( 0 === strpos( $pll, 'en' ) ) {
			return 'en';
		}
		return $lang;
	}

	public static function maybe_pll_switcher( $html ) {
		if ( ! self::active() || ! function_exists( 'pll_the_languages' ) ) {
			return $html;
		}
		$raw = pll_the_languages( array( 'raw' => 1, 'hide_if_empty' => 0, 'hide_current' => 0, 'display_names_as' => 'name' ) );
		if ( empty( $raw ) || ! is_array( $raw ) ) {
			return $html;
		}
		$out = '<nav class="termimal-lang-switcher termimal-lang-switcher--pll" aria-label="' . esc_attr__( 'Language', 'termimal' ) . '"><ul class="termimal-lang-switcher__list">';
		foreach ( $raw as $item ) {
			$slug = isset( $item['slug'] ) ? $item['slug'] : '';
			$code = ( 0 === strpos( $slug, 'fa' ) ) ? 'fa' : ( ( 0 === strpos( $slug, 'en' ) ) ? 'en' : '' );
			if ( ! $code ) {
				continue;
			}
			$active = ! empty( $item['current_lang'] ) ? ' is-active' : '';
			$url = isset( $item['url'] ) ? $item['url'] : '#';
			$name = ( 'fa' === $code ) ? 'فارسی' : 'English';
			$out .= '<li class="termimal-lang-switcher__item' . $active . '"><a href="' . esc_url( $url ) . '" hreflang="' . esc_attr( $slug ) . '" lang="' . esc_attr( $code ) . '">' . esc_html( $name ) . '</a></li>';
		}
		return $out . '</ul></nav>';
	}

	public static function admin_hint() {
		if ( self::active() ) {
			return;
		}
		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! $screen || ( 'edit-termimal_product' !== $screen->id && false === strpos( (string) $screen->id, 'termimal' ) ) ) {
			return;
		}
		echo '<div class="notice notice-info"><p>' . esc_html__( 'For dual-language product content (separate EN and FA versions), install and activate Polylang, then enable Languages: English + فارسی only. TERMIMAL products and categories will appear in Polylang settings automatically.', 'termimal' ) . '</p></div>';
	}
}
