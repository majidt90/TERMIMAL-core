<?php
/**
 * Admin settings page for TERMIMAL Core.
 *
 * @package Termimal_Core
 */

defined( 'ABSPATH' ) || exit;

class Termimal_Admin {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	public static function add_settings_page() {
		add_options_page(
			__( 'TERMIMAL Settings', 'termimal' ),
			__( 'TERMIMAL', 'termimal' ),
			'manage_options',
			'termimal-settings',
			array( __CLASS__, 'render_settings_page' )
		);
	}

	public static function register_settings() {
		register_setting(
			'termimal_settings_group',
			'termimal_options',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize_options' ),
				'default'           => array(
					'products_per_page' => 12,
					'show_related'      => 1,
				),
			)
		);

		add_settings_section(
			'termimal_general',
			__( 'General Portfolio Settings', 'termimal' ),
			'__return_false',
			'termimal-settings'
		);

		add_settings_field(
			'products_per_page',
			__( 'Products per page', 'termimal' ),
			array( __CLASS__, 'field_products_per_page' ),
			'termimal-settings',
			'termimal_general'
		);

		add_settings_field(
			'show_related',
			__( 'Show related products', 'termimal' ),
			array( __CLASS__, 'field_show_related' ),
			'termimal-settings',
			'termimal_general'
		);
	}

	public static function sanitize_options( $input ) {
		$output = array();
		$output['products_per_page'] = isset( $input['products_per_page'] ) ? absint( $input['products_per_page'] ) : 12;
		$output['show_related']      = ! empty( $input['show_related'] ) ? 1 : 0;
		return $output;
	}

	public static function field_products_per_page() {
		$options = get_option( 'termimal_options', array() );
		$value   = isset( $options['products_per_page'] ) ? absint( $options['products_per_page'] ) : 12;
		printf(
			'<input type="number" name="termimal_options[products_per_page]" value="%d" min="1" max="48" class="small-text">',
			$value
		);
	}

	public static function field_show_related() {
		$options = get_option( 'termimal_options', array() );
		$checked = ! empty( $options['show_related'] ) ? 'checked' : '';
		printf(
			'<label><input type="checkbox" name="termimal_options[show_related]" value="1" %s> %s</label>',
			$checked,
			esc_html__( 'Display related products section on single product pages', 'termimal' )
		);
	}

	public static function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p><?php esc_html_e( 'Configure portfolio behaviour for the TERMIMAL theme.', 'termimal' ); ?></p>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'termimal_settings_group' );
				do_settings_sections( 'termimal-settings' );
				submit_button( __( 'Save Settings', 'termimal' ) );
				?>
			</form>
			<hr>
			<h2><?php esc_html_e( 'How to add products', 'termimal' ); ?></h2>
			<ol>
				<li><?php esc_html_e( 'Go to Products → Add New.', 'termimal' ); ?></li>
				<li><?php esc_html_e( 'Enter title, description and set a cover image.', 'termimal' ); ?></li>
				<li><?php esc_html_e( 'Fill the “Product Details” box (external link).', 'termimal' ); ?></li>
				<li><?php esc_html_e( 'Add images to the Gallery / Album.', 'termimal' ); ?></li>
				<li><?php esc_html_e( 'Optionally link related products in the sidebar.', 'termimal' ); ?></li>
			</ol>
		</div>
		<?php
	}
}
