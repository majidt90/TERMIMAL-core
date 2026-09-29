<?php
/**
 * Bilingual support: English + Persian (fa_IR) only.
 *
 * @package Termimal_Core
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'str_starts_with' ) ) {
	function str_starts_with( $haystack, $needle ) {
		return 0 === strncmp( $haystack, $needle, strlen( $needle ) );
	}
}

class Termimal_I18n {

	const COOKIE = 'termimal_lang';
	const QUERY  = 'lang';

	public static function languages() {
		return array(
			'en' => array( 'locale' => 'en_US', 'label' => 'English', 'dir' => 'ltr' ),
			'fa' => array( 'locale' => 'fa_IR', 'label' => 'فارسی', 'dir' => 'rtl' ),
		);
	}

	public static function init() {
		add_action( 'plugins_loaded', array( __CLASS__, 'maybe_set_lang_cookie' ), 1 );
		add_filter( 'locale', array( __CLASS__, 'filter_locale' ), 1 );
		add_filter( 'determine_locale', array( __CLASS__, 'filter_locale' ), 1 );
		add_action( 'init', array( __CLASS__, 'register_shortcode' ) );
		add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
		add_filter( 'language_attributes', array( __CLASS__, 'language_attributes' ) );
	}

	public static function maybe_set_lang_cookie() {
		if ( empty( $_GET[ self::QUERY ] ) ) {
			return;
		}
		$code = sanitize_key( wp_unslash( $_GET[ self::QUERY ] ) );
		if ( ! isset( self::languages()[ $code ] ) ) {
			return;
		}
		setcookie( self::COOKIE, $code, time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
		$_COOKIE[ self::COOKIE ] = $code;
	}

	public static function current_lang() {
		$langs = self::languages();
		if ( ! empty( $_GET[ self::QUERY ] ) ) {
			$code = sanitize_key( wp_unslash( $_GET[ self::QUERY ] ) );
			if ( isset( $langs[ $code ] ) ) {
				return $code;
			}
		}
		if ( ! empty( $_COOKIE[ self::COOKIE ] ) ) {
			$code = sanitize_key( wp_unslash( $_COOKIE[ self::COOKIE ] ) );
			if ( isset( $langs[ $code ] ) ) {
				return $code;
			}
		}
		if ( ! empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
			$al = strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) );
			if ( str_starts_with( $al, 'fa' ) ) {
				return 'fa';
			}
		}
		return 'en';
	}

	public static function filter_locale( $locale ) {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return $locale;
		}
		$lang  = self::current_lang();
		$langs = self::languages();
		return isset( $langs[ $lang ] ) ? $langs[ $lang ]['locale'] : 'en_US';
	}

	public static function body_class( $classes ) {
		$lang  = self::current_lang();
		$langs = self::languages();
		$classes[] = 'lang-' . $lang;
		$classes[] = 'dir-' . $langs[ $lang ]['dir'];
		return $classes;
	}

	public static function language_attributes( $output ) {
		$lang  = self::current_lang();
		$langs = self::languages();
		$code  = ( 'fa' === $lang ) ? 'fa' : 'en';
		return 'lang="' . esc_attr( $code ) . '" dir="' . esc_attr( $langs[ $lang ]['dir'] ) . '"';
	}

	public static function register_shortcode() {
		add_shortcode( 'termimal_lang_switcher', array( __CLASS__, 'switcher_shortcode' ) );
	}

	public static function switcher_shortcode( $atts ) {
		return self::render_switcher();
	}

	public static function render_switcher() {
		$current = self::current_lang();
		$langs   = self::languages();
		$host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
		$uri  = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
		$url  = ( is_ssl() ? 'https://' : 'http://' ) . $host . $uri;
		$url  = remove_query_arg( self::QUERY, $url );
		$html = '<nav class="termimal-lang-switcher" aria-label="' . esc_attr__( 'Language', 'termimal' ) . '"><ul class="termimal-lang-switcher__list">';
		foreach ( $langs as $code => $meta ) {
			$href = esc_url( add_query_arg( self::QUERY, $code, $url ) );
			$active = ( $code === $current ) ? ' is-active' : '';
			$html .= '<li class="termimal-lang-switcher__item' . $active . '"><a href="' . $href . '" hreflang="' . esc_attr( $code ) . '" lang="' . esc_attr( $code ) . '">' . esc_html( $meta['label'] ) . '</a></li>';
		}
		return $html . '</ul></nav>';
	}

	public static function is_fa() {
		return 'fa' === self::current_lang();
	}
}
