<?php
/**
 * Analytics: GA4 optional + CTA click events.
 *
 * @package Termimal_Core
 */

defined( 'ABSPATH' ) || exit;

class Termimal_Analytics {

	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_action( 'wp_head', array( __CLASS__, 'ga4_snippet' ), 20 );
	}

	public static function options() {
		return get_option( 'termimal_options', array() );
	}

	public static function ga4_id() {
		$opts = self::options();
		$id   = isset( $opts['ga4_id'] ) ? sanitize_text_field( $opts['ga4_id'] ) : '';
		return preg_match( '/^G-[A-Z0-9]+$/i', $id ) ? $id : '';
	}

	public static function events_enabled() {
		$opts = self::options();
		return ! isset( $opts['track_cta'] ) || ! empty( $opts['track_cta'] );
	}

	public static function ga4_snippet() {
		$id = self::ga4_id();
		if ( ! $id ) {
			return;
		}
		?>
		<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $id ); ?>"></script>
		<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', '<?php echo esc_js( $id ); ?>');
		</script>
		<?php
	}

	public static function enqueue() {
		if ( ! self::events_enabled() && ! self::ga4_id() ) {
			return;
		}
		if ( ! is_singular( 'termimal_product' ) && ! is_front_page() && ! is_post_type_archive( 'termimal_product' ) ) {
			return;
		}
		wp_enqueue_script( 'termimal-analytics', TERMIMAL_CORE_URL . 'assets/js/analytics.js', array(), TERMIMAL_CORE_VERSION, true );
		wp_localize_script( 'termimal-analytics', 'termimalAnalytics', array( 'ga4' => self::ga4_id(), 'enabled' => self::events_enabled() ? 1 : 0 ) );
	}
}
