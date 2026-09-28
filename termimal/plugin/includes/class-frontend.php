<?php
/**
 * Front-end: filters, search, sort, shortcodes, badges, lightbox.
 *
 * @package Termimal_Core
 */

defined( 'ABSPATH' ) || exit;

class Termimal_Frontend {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_shortcodes' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_filter( 'query_vars', array( __CLASS__, 'query_vars' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'filter_product_query' ) );
		add_filter( 'render_block', array( __CLASS__, 'inject_card_badges' ), 10, 2 );
	}

	public static function query_vars( $vars ) {
		$vars[] = 'product_status';
		$vars[] = 'product_sort';
		return $vars;
	}

	public static function filter_product_query( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}
		$post_type = $query->get( 'post_type' );
		$is_product_search = $query->is_search() && ( 'termimal_product' === $post_type || ( isset( $_GET['post_type'] ) && 'termimal_product' === $_GET['post_type'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$is_product_archive = $query->is_post_type_archive( 'termimal_product' ) || $query->is_tax( 'product_category' ) || $is_product_search;
		if ( ! $is_product_archive ) {
			return;
		}
		if ( $is_product_search ) {
			$query->set( 'post_type', 'termimal_product' );
		}
		$cat_slug = isset( $_GET['product_category'] ) ? sanitize_title( wp_unslash( $_GET['product_category'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $cat_slug && ! $query->is_tax( 'product_category' ) ) {
			$query->set( 'tax_query', array( array( 'taxonomy' => 'product_category', 'field' => 'slug', 'terms' => $cat_slug ) ) );
		}
		$status = get_query_var( 'product_status' );
		if ( ! $status && isset( $_GET['product_status'] ) ) {
			$status = sanitize_key( wp_unslash( $_GET['product_status'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
		if ( $status && class_exists( 'Termimal_Meta' ) && in_array( $status, array_keys( Termimal_Meta::get_statuses() ), true ) ) {
			$meta_query   = (array) $query->get( 'meta_query' );
			$meta_query[] = array( 'key' => Termimal_Meta::META_STATUS, 'value' => $status );
			$query->set( 'meta_query', $meta_query );
		}
		$sort = get_query_var( 'product_sort' );
		if ( ! $sort && isset( $_GET['product_sort'] ) ) {
			$sort = sanitize_key( wp_unslash( $_GET['product_sort'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
		switch ( $sort ) {
			case 'title':
				$query->set( 'orderby', 'title' );
				$query->set( 'order', 'ASC' );
				break;
			case 'featured':
				if ( class_exists( 'Termimal_Meta' ) ) {
					$query->set( 'meta_key', Termimal_Meta::META_FEATURED );
					$query->set( 'orderby', 'meta_value_num' );
					$query->set( 'order', 'DESC' );
				}
				break;
			case 'oldest':
				$query->set( 'orderby', 'date' );
				$query->set( 'order', 'ASC' );
				break;
		}
	}

	public static function enqueue_assets() {
		$needs = is_singular( 'termimal_product' ) || is_post_type_archive( 'termimal_product' ) || is_tax( 'product_category' ) || is_front_page() || is_home();
		if ( ! $needs ) {
			return;
		}
		wp_enqueue_style( 'termimal-frontend', TERMIMAL_CORE_URL . 'assets/css/frontend.css', array(), TERMIMAL_CORE_VERSION );
		wp_enqueue_script( 'termimal-frontend', TERMIMAL_CORE_URL . 'assets/js/frontend.js', array(), TERMIMAL_CORE_VERSION, true );
	}

	public static function register_shortcodes() {
		add_shortcode( 'termimal_filters', array( __CLASS__, 'shortcode_filters' ) );
		add_shortcode( 'termimal_products', array( __CLASS__, 'shortcode_products' ) );
	}

	public static function shortcode_filters( $atts ) {
		$categories = get_terms( array( 'taxonomy' => 'product_category', 'hide_empty' => true ) );
		$current_cat = isset( $_GET['product_category'] ) ? sanitize_title( wp_unslash( $_GET['product_category'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! $current_cat && is_tax( 'product_category' ) ) {
			$qo = get_queried_object();
			$current_cat = ( $qo && ! is_wp_error( $qo ) ) ? $qo->slug : '';
		}
		$current_status = isset( $_GET['product_status'] ) ? sanitize_key( wp_unslash( $_GET['product_status'] ) ) : get_query_var( 'product_status' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current_sort   = isset( $_GET['product_sort'] ) ? sanitize_key( wp_unslash( $_GET['product_sort'] ) ) : get_query_var( 'product_sort' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current_search = get_search_query();
		$action = get_post_type_archive_link( 'termimal_product' ) ?: home_url( '/' );
		obstart();
		?>
		<form class="termimal-filters" method="get" action="<?php echo esc_url( $action ); ?>" role="search">
			<div class="termimal-filters__row">
				<input type="search" name="s" value="<?php echo esc_attr( $current_search ); ?>" placeholder="<?php esc_attr_e( 'Search products…', 'termimal' ); ?>" class="termimal-filters__search">
				<?php if ( ! is_wp_error( $categories ) && $categories ) : ?>
					<select name="product_category" class="termimal-filters__select">
						<option value=""><?php esc_html_e( 'All categories', 'termimal' ); ?></option>
						<?php foreach ( $categories as $cat ) : ?>
							<option value="<?php echo esc_attr( $cat->slug ); ?>" <?php selected( $current_cat, $cat->slug ); ?>><?php echo esc_html( $cat->name ); ?></option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>
				<?php if ( class_exists( 'Termimal_Meta' ) ) : ?>
					<select name="product_status" class="termimal-filters__select">
						<option value=""><?php esc_html_e( 'All statuses', 'termimal' ); ?></option>
						<?php foreach ( Termimal_Meta::get_statuses() as $key => $label ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $current_status, $key ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>
				<select name="product_sort" class="termimal-filters__select">
					<option value="" <?php selected( $current_sort, '' ); ?>><?php esc_html_e( 'Newest', 'termimal' ); ?></option>
					<option value="oldest" <?php selected( $current_sort, 'oldest' ); ?>><?php esc_html_e( 'Oldest', 'termimal' ); ?></option>
					<option value="title" <?php selected( $current_sort, 'title' ); ?>><?php esc_html_e( 'A–Z', 'termimal' ); ?></option>
					<option value="featured" <?php selected( $current_sort, 'featured' ); ?>><?php esc_html_e( 'Featured first', 'termimal' ); ?></option>
				</select>
				<button type="submit" class="termimal-filters__submit"><?php esc_html_e( 'Filter', 'termimal' ); ?></button>
			</div>
			<input type="hidden" name="post_type" value="termimal_product">
		</form>
		<?php
		return ob_get_clean();
	}

	public static function shortcode_products( $atts ) {
		$atts = shortcode_atts( array( 'limit' => 12, 'featured' => '', 'category' => '', 'status' => '', 'columns' => 3 ), $atts, 'termimal_products' );
		$args = array( 'post_type' => 'termimal_product', 'post_status' => 'publish', 'posts_per_page' => absint( $atts['limit'] ), 'orderby' => 'date', 'order' => 'DESC' );
		if ( '1' === (string) $atts['featured'] && class_exists( 'Termimal_Meta' ) ) {
			$args['meta_key'] = Termimal_Meta::META_FEATURED;
			$args['meta_value'] = '1';
		}
		if ( $atts['status'] && class_exists( 'Termimal_Meta' ) ) {
			$args['meta_query'][] = array( 'key' => Termimal_Meta::META_STATUS, 'value' => sanitize_key( $atts['status'] ) );
		}
		if ( $atts['category'] ) {
			$args['tax_query'][] = array( 'taxonomy' => 'product_category', 'field' => 'slug', 'terms' => sanitize_title( $atts['category'] ) );
		}
		$q = new WP_Query( $args );
		if ( ! $q->have_posts() ) {
			return '<p class="termimal-empty">' . esc_html__( 'No products found.', 'termimal' ) . '</p>';
		}
		$cols = max( 1, min( 4, absint( $atts['columns'] ) ) );
		$html = '<div class="termimal-products-grid" style="display:grid;grid-template-columns:repeat(' . $cols . ',minmax(0,1fr));gap:1.5rem;">';
		while ( $q->have_posts() ) {
			$q->the_post();
			$id = get_the_ID();
			$html .= '<article class="termimal-product-card termimal-product-card--shortcode">';
			$html .= self::get_badges_html( $id );
			if ( has_post_thumbnail() ) {
				$html .= '<a href="' . esc_url( get_permalink() ) . '" class="termimal-product-card__image">' . get_the_post_thumbnail( $id, 'termimal-product-card' ) . '</a>';
			}
			$html .= '<h3 class="termimal-product-card__title"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
			$html .= '<div class="termimal-product-card__excerpt">' . wp_kses_post( wp_trim_words( get_the_excerpt(), 18 ) ) . '</div></article>';
		}
		wp_reset_postdata();
		return $html . '</div>';
	}

	public static function get_badges_html( $post_id ) {
		if ( ! class_exists( 'Termimal_Meta' ) ) {
			return '';
		}
		$badges = array();
		$status = Termimal_Meta::get_status( $post_id );
		$labels = Termimal_Meta::get_statuses();
		if ( isset( $labels[ $status ] ) ) {
			$badges[] = '<span class="termimal-badge termimal-badge--' . esc_attr( $status ) . '">' . esc_html( $labels[ $status ] ) . '</span>';
		}
		if ( Termimal_Meta::is_featured( $post_id ) ) {
			$badges[] = '<span class="termimal-badge termimal-badge--featured">' . esc_html__( 'Featured', 'termimal' ) . '</span>';
		}
		return $badges ? '<div class="termimal-badges">' . implode( '', $badges ) . '</div>' : '';
	}

	public static function inject_card_badges( $block_content, $block ) {
		if ( empty( $block['blockName'] ) || 'core/post-featured-image' !== $block['blockName'] ) {
			return $block_content;
		}
		if ( get_post_type() !== 'termimal_product' ) {
			return $block_content;
		}
		$badges = self::get_badges_html( get_the_ID() );
		return $badges ? '<div class="termimal-card-media">' . $block_content . $badges . '</div>' : $block_content;
	}
}
