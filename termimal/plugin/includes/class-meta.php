<?php
/**
 * Product meta boxes and save handlers.
 *
 * @package Termimal_Core
 */

defined( 'ABSPATH' ) || exit;

class Termimal_Meta {

	const META_URL      = '_termimal_product_url';
	const META_GALLERY  = '_termimal_product_gallery';
	const META_RELATED  = '_termimal_related_products';
	const META_TECH     = '_termimal_tech_stack';
	const META_STATUS   = '_termimal_status';
	const META_YEAR     = '_termimal_year';
	const META_FEATURED = '_termimal_featured';

	public static function get_statuses() {
		return array(
			'live'        => __( 'Live', 'termimal' ),
			'beta'        => __( 'Beta', 'termimal' ),
			'coming_soon' => __( 'Coming soon', 'termimal' ),
			'archived'    => __( 'Archived', 'termimal' ),
		);
	}

	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post_termimal_product', array( __CLASS__, 'save_meta' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
		add_filter( 'manage_termimal_product_posts_columns', array( __CLASS__, 'admin_columns' ) );
		add_action( 'manage_termimal_product_posts_custom_column', array( __CLASS__, 'admin_column_content' ), 10, 2 );
	}

	public static function add_meta_boxes() {
		add_meta_box( 'termimal_product_details', __( 'Product Details', 'termimal' ), array( __CLASS__, 'render_details_box' ), 'termimal_product', 'normal', 'high' );
		add_meta_box( 'termimal_product_gallery', __( 'Product Gallery / Album', 'termimal' ), array( __CLASS__, 'render_gallery_box' ), 'termimal_product', 'normal', 'default' );
		add_meta_box( 'termimal_related_products', __( 'Related Products', 'termimal' ), array( __CLASS__, 'render_related_box' ), 'termimal_product', 'side', 'default' );
		add_meta_box( 'termimal_featured_box', __( 'Homepage', 'termimal' ), array( __CLASS__, 'render_featured_box' ), 'termimal_product', 'side', 'high' );
	}

	public static function render_details_box( $post ) {
		wp_nonce_field( 'termimal_save_product_meta', 'termimal_meta_nonce' );
		$url    = get_post_meta( $post->ID, self::META_URL, true );
		$tech   = get_post_meta( $post->ID, self::META_TECH, true );
		$status = get_post_meta( $post->ID, self::META_STATUS, true ) ?: 'live';
		$year   = get_post_meta( $post->ID, self::META_YEAR, true );
		?>
		<p>
			<label for="termimal_product_url"><strong><?php esc_html_e( 'External / Product Link', 'termimal' ); ?></strong></label><br>
			<input type="url" id="termimal_product_url" name="termimal_product_url" value="<?php echo esc_attr( $url ); ?>" class="widefat" placeholder="https://…">
		</p>
		<p>
			<label for="termimal_tech_stack"><strong><?php esc_html_e( 'Tech stack', 'termimal' ); ?></strong></label><br>
			<input type="text" id="termimal_tech_stack" name="termimal_tech_stack" value="<?php echo esc_attr( $tech ); ?>" class="widefat" placeholder="e.g. Python, LLM, React">
		</p>
		<p>
			<label for="termimal_status"><strong><?php esc_html_e( 'Status', 'termimal' ); ?></strong></label><br>
			<select id="termimal_status" name="termimal_status" class="widefat">
				<?php foreach ( self::get_statuses() as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $status, $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="termimal_year"><strong><?php esc_html_e( 'Year', 'termimal' ); ?></strong></label><br>
			<input type="number" id="termimal_year" name="termimal_year" value="<?php echo esc_attr( $year ); ?>" class="small-text" min="2000" max="2100" step="1">
		</p>
		<?php
	}

	public static function render_featured_box( $post ) {
		$featured = get_post_meta( $post->ID, self::META_FEATURED, true );
		?>
		<p><label><input type="checkbox" name="termimal_featured" value="1" <?php checked( $featured, '1' ); ?>> <?php esc_html_e( 'Featured on homepage', 'termimal' ); ?></label></p>
		<p class="description"><?php esc_html_e( 'When at least one product is featured, the homepage shows only featured products.', 'termimal' ); ?></p>
		<?php
	}

	public static function render_gallery_box( $post ) {
		$gallery = get_post_meta( $post->ID, self::META_GALLERY, true );
		$ids     = $gallery ? array_filter( array_map( 'absint', explode( ',', $gallery ) ) ) : array();
		?>
		<div id="termimal-gallery-wrapper">
			<input type="hidden" id="termimal_product_gallery" name="termimal_product_gallery" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>">
			<ul class="termimal-gallery-preview" style="display:flex;flex-wrap:wrap;gap:8px;list-style:none;padding:0;margin:0 0 12px;">
				<?php foreach ( $ids as $id ) :
					$src = wp_get_attachment_image_src( $id, 'thumbnail' );
					if ( $src ) : ?>
						<li data-id="<?php echo esc_attr( $id ); ?>"><img src="<?php echo esc_url( $src[0] ); ?>" width="80" height="80" style="object-fit:cover;border-radius:4px;" alt=""></li>
					<?php endif;
				endforeach; ?>
			</ul>
			<button type="button" class="button" id="termimal-add-gallery"><?php esc_html_e( 'Add / Edit Gallery Images', 'termimal' ); ?></button>
			<button type="button" class="button" id="termimal-clear-gallery" style="margin-left:8px;"><?php esc_html_e( 'Clear', 'termimal' ); ?></button>
		</div>
		<?php
	}

	public static function render_related_box( $post ) {
		$related = get_post_meta( $post->ID, self::META_RELATED, true );
		$related = is_array( $related ) ? array_map( 'absint', $related ) : array();
		$products = get_posts( array( 'post_type' => 'termimal_product', 'posts_per_page' => 100, 'post_status' => 'publish', 'post__not_in' => array( $post->ID ), 'orderby' => 'title', 'order' => 'ASC' ) );
		?>
		<p><select name="termimal_related_products[]" multiple style="width:100%;min-height:120px;">
			<?php foreach ( $products as $product ) : ?>
				<option value="<?php echo esc_attr( $product->ID ); ?>" <?php selected( in_array( $product->ID, $related, true ) ); ?>><?php echo esc_html( $product->post_title ); ?></option>
			<?php endforeach; ?>
		</select></p>
		<?php
	}

	public static function save_meta( $post_id, $post ) {
		if ( ! isset( $_POST['termimal_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['termimal_meta_nonce'] ) ), 'termimal_save_product_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
		if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

		if ( isset( $_POST['termimal_product_url'] ) ) {
			update_post_meta( $post_id, self::META_URL, esc_url_raw( wp_unslash( $_POST['termimal_product_url'] ) ) );
		}
		if ( isset( $_POST['termimal_tech_stack'] ) ) {
			update_post_meta( $post_id, self::META_TECH, sanitize_text_field( wp_unslash( $_POST['termimal_tech_stack'] ) ) );
		}
		if ( isset( $_POST['termimal_status'] ) ) {
			$status = sanitize_key( wp_unslash( $_POST['termimal_status'] ) );
			if ( in_array( $status, array_keys( self::get_statuses() ), true ) ) {
				update_post_meta( $post_id, self::META_STATUS, $status );
			}
		}
		if ( isset( $_POST['termimal_year'] ) ) {
			$year = absint( $_POST['termimal_year'] );
			if ( $year >= 2000 && $year <= 2100 ) {
				update_post_meta( $post_id, self::META_YEAR, $year );
			} else {
				delete_post_meta( $post_id, self::META_YEAR );
			}
		}
		update_post_meta( $post_id, self::META_FEATURED, ! empty( $_POST['termimal_featured'] ) ? '1' : '0' );

		if ( isset( $_POST['termimal_product_gallery'] ) ) {
			$ids = array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['termimal_product_gallery'] ) ) ) ) );
			update_post_meta( $post_id, self::META_GALLERY, implode( ',', $ids ) );
		}
		if ( isset( $_POST['termimal_related_products'] ) && is_array( $_POST['termimal_related_products'] ) ) {
			update_post_meta( $post_id, self::META_RELATED, array_filter( array_map( 'absint', wp_unslash( $_POST['termimal_related_products'] ) ) ) );
		} else {
			delete_post_meta( $post_id, self::META_RELATED );
		}
	}

	public static function enqueue_admin_assets( $hook ) {
		global $post_type;
		if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && 'termimal_product' === $post_type ) {
			wp_enqueue_media();
			wp_enqueue_script( 'termimal-admin-gallery', TERMIMAL_CORE_URL . 'assets/js/admin-gallery.js', array( 'jquery' ), TERMIMAL_CORE_VERSION, true );
		}
	}

	public static function admin_columns( $columns ) {
		$new = array();
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['termimal_status']   = __( 'Status', 'termimal' );
				$new['termimal_featured'] = __( 'Featured', 'termimal' );
			}
		}
		return $new;
	}

	public static function admin_column_content( $column, $post_id ) {
		if ( 'termimal_status' === $column ) {
			$status   = get_post_meta( $post_id, self::META_STATUS, true );
			$statuses = self::get_statuses();
			echo esc_html( isset( $statuses[ $status ] ) ? $statuses[ $status ] : '—' );
		}
		if ( 'termimal_featured' === $column ) {
			echo '1' === (string) get_post_meta( $post_id, self::META_FEATURED, true ) ? '★' : '—';
		}
	}

	public static function get_url( $post_id ) { return get_post_meta( $post_id, self::META_URL, true ); }
	public static function get_gallery_ids( $post_id ) {
		$raw = get_post_meta( $post_id, self::META_GALLERY, true );
		return $raw ? array_filter( array_map( 'absint', explode( ',', $raw ) ) ) : array();
	}
	public static function get_related_ids( $post_id ) {
		$related = get_post_meta( $post_id, self::META_RELATED, true );
		return is_array( $related ) ? array_map( 'absint', $related ) : array();
	}
	public static function get_tech( $post_id ) { return get_post_meta( $post_id, self::META_TECH, true ); }
	public static function get_status( $post_id ) { return get_post_meta( $post_id, self::META_STATUS, true ) ?: 'live'; }
	public static function get_year( $post_id ) { return get_post_meta( $post_id, self::META_YEAR, true ); }
	public static function is_featured( $post_id ) { return '1' === (string) get_post_meta( $post_id, self::META_FEATURED, true ); }
}
