<?php
/**
 * SEO: JSON-LD schema + Open Graph / Twitter cards.
 *
 * @package Termimal_Core
 */

defined( 'ABSPATH' ) || exit;

class Termimal_SEO {

	public static function init() {
		add_action( 'wp_head', array( __CLASS__, 'output_json_ld' ), 5 );
		add_action( 'wp_head', array( __CLASS__, 'output_social_meta' ), 6 );
	}

	public static function output_json_ld() {
		$graph = array();
		$org = array(
			'@type' => 'Organization',
			'@id'   => home_url( '/#organization' ),
			'name'  => 'TERMIMAL',
			'url'   => home_url( '/' ),
			'description' => __( 'AI-powered tools built for precision.', 'termimal' ),
		);
		$logo_id = get_theme_mod( 'custom_logo' );
		if ( $logo_id ) {
			$logo_url = wp_get_attachment_image_url( $logo_id, 'full' );
			if ( $logo_url ) {
				$org['logo'] = $logo_url;
			}
		}
		$graph[] = $org;

		if ( is_singular( 'termimal_product' ) && class_exists( 'Termimal_Meta' ) ) {
			$post_id = get_the_ID();
			$product = array(
				'@type' => 'SoftwareApplication',
				'@id'   => get_permalink( $post_id ) . '#product',
				'name'  => get_the_title( $post_id ),
				'description' => wp_strip_all_tags( get_the_excerpt( $post_id ) ),
				'url'   => get_permalink( $post_id ),
				'applicationCategory' => 'BusinessApplication',
				'operatingSystem' => 'Web',
				'provider' => array( '@id' => home_url( '/#organization' ) ),
			);
			if ( has_post_thumbnail( $post_id ) ) {
				$product['image'] = get_the_post_thumbnail_url( $post_id, 'large' );
			}
			$url = Termimal_Meta::get_url( $post_id );
			if ( $url ) {
				$product['downloadUrl'] = $url;
			}
			if ( method_exists( 'Termimal_Meta', 'get_demo_url' ) ) {
				$demo = Termimal_Meta::get_demo_url( $post_id );
				if ( $demo ) {
					$product['installUrl'] = $demo;
				}
			}
			$tech = Termimal_Meta::get_tech( $post_id );
			if ( $tech ) {
				$product['keywords'] = $tech;
			}
			$year = Termimal_Meta::get_year( $post_id );
			if ( $year ) {
				$product['datePublished'] = $year . '-01-01';
			}
			$graph[] = $product;
		}

		echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}

	public static function output_social_meta() {
		if ( is_singular( 'termimal_product' ) ) {
			$title = get_the_title();
			$desc  = wp_strip_all_tags( get_the_excerpt() );
			$url   = get_permalink();
			$image = has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'large' ) : '';
			$type  = 'product';
		} elseif ( is_front_page() || is_home() ) {
			$title = get_bloginfo( 'name' );
			$desc  = get_bloginfo( 'description' );
			$url   = home_url( '/' );
			$image = '';
			$type  = 'website';
		} else {
			return;
		}
		if ( ! $desc ) {
			$desc = __( 'AI-powered tools built for precision.', 'termimal' );
		}
		echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
		echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( wp_trim_words( $desc, 40, '…' ) ) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
		if ( $image ) {
			echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
		}
		echo '<meta name="twitter:card" content="' . ( $image ? 'summary_large_image' : 'summary' ) . '">' . "\n";
		echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta name="twitter:description" content="' . esc_attr( wp_trim_words( $desc, 40, '…' ) ) . '">' . "\n";
		if ( $image ) {
			echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
		}
	}
}
