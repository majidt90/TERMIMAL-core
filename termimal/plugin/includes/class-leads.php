<?php
/**
 * Advanced lead form [termimal_lead].
 *
 * @package Termimal_Core
 */

defined( 'ABSPATH' ) || exit;

class Termimal_Leads {

	public static function init() {
		add_shortcode( 'termimal_lead', array( __CLASS__, 'shortcode' ) );
		add_action( 'init', array( __CLASS__, 'handle_submit' ) );
	}

	public static function product_choices() {
		$posts = get_posts( array( 'post_type' => 'termimal_product', 'post_status' => 'publish', 'posts_per_page' => 50, 'orderby' => 'title', 'order' => 'ASC' ) );
		$out = array();
		foreach ( $posts as $p ) {
			$out[ $p->ID ] = $p->post_title;
		}
		return $out;
	}

	public static function handle_submit() {
		if ( empty( $_POST['termimal_lead_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['termimal_lead_nonce'] ) ), 'termimal_lead' ) ) {
			return;
		}
		if ( ! empty( $_POST['termimal_lead_website'] ) ) {
			wp_safe_redirect( add_query_arg( 'lead', 'ok', wp_get_referer() ? wp_get_referer() : home_url( '/' ) ) );
			exit;
		}
		$name = isset( $_POST['termimal_lead_name'] ) ? sanitize_text_field( wp_unslash( $_POST['termimal_lead_name'] ) ) : '';
		$email = isset( $_POST['termimal_lead_email'] ) ? sanitize_email( wp_unslash( $_POST['termimal_lead_email'] ) ) : '';
		$company = isset( $_POST['termimal_lead_company'] ) ? sanitize_text_field( wp_unslash( $_POST['termimal_lead_company'] ) ) : '';
		$interest = isset( $_POST['termimal_lead_interest'] ) ? absint( $_POST['termimal_lead_interest'] ) : 0;
		$message = isset( $_POST['termimal_lead_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['termimal_lead_message'] ) ) : '';
		if ( ! $name || ! is_email( $email ) || ! $message ) {
			wp_safe_redirect( add_query_arg( 'lead', 'error', wp_get_referer() ? wp_get_referer() : home_url( '/' ) ) );
			exit;
		}
		$interest_title = $interest ? get_the_title( $interest ) : '';
		$payload = array( 'type' => 'lead', 'name' => $name, 'email' => $email, 'company' => $company, 'interest_id' => $interest, 'interest_title' => $interest_title, 'message' => $message, 'page' => wp_get_referer() ? wp_get_referer() : home_url( '/' ), 'time' => gmdate( 'c' ) );
		$sent = wp_mail( get_option( 'admin_email' ), sprintf( '[TERMIMAL Lead] %s%s', $name, $company ? " ($company)" : '' ), "Name: $name\nEmail: $email\nCompany: $company\nInterest: $interest_title\n\n$message\n", array( 'Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $name . ' <' . $email . '>' ) );
		do_action( 'termimal_lead_submitted', $payload, $sent );
		wp_safe_redirect( add_query_arg( 'lead', $sent ? 'ok' : 'error', wp_get_referer() ? wp_get_referer() : home_url( '/' ) ) );
		exit;
	}

	public static function shortcode( $atts ) {
		$atts = shortcode_atts( array( 'title' => __( 'Request a demo', 'termimal' ) ), $atts, 'termimal_lead' );
		$status = isset( $_GET['lead'] ) ? sanitize_key( wp_unslash( $_GET['lead'] ) ) : '';
		$products = self::product_choices();
		obstart = ob_start();
		echo '<div class="termimal-lead termimal-contact">';
		if ( ! empty( $atts['title'] ) ) {
			echo '<h3 class="termimal-lead__title">' . esc_html( $atts['title'] ) . '</h3>';
		}
		if ( 'ok' === $status ) {
			echo '<p class="termimal-contact__notice termimal-contact__notice--ok" role="status">' . esc_html__( 'Thank you. Our team will get back to you shortly.', 'termimal' ) . '</p>';
		} elseif ( 'error' === $status ) {
			echo '<p class="termimal-contact__notice termimal-contact__notice--error" role="alert">' . esc_html__( 'Something went wrong. Please check the fields and try again.', 'termimal' ) . '</p>';
		}
		echo '<form class="termimal-contact__form termimal-lead__form" method="post" action="">';
		wp_nonce_field( 'termimal_lead', 'termimal_lead_nonce' );
		echo '<p class="termimal-contact__hp" aria-hidden="true" style="position:absolute;left:-9999px;"><label for="termimal_lead_website">Website</label><input type="text" name="termimal_lead_website" id="termimal_lead_website" value="" tabindex="-1" autocomplete="off"></p>';
		echo '<p><label for="termimal_lead_name"><strong>' . esc_html__( 'Name', 'termimal' ) . '</strong></label><br><input type="text" id="termimal_lead_name" name="termimal_lead_name" required class="termimal-contact__input" autocomplete="name"></p>';
		echo '<p><label for="termimal_lead_email"><strong>' . esc_html__( 'Email', 'termimal' ) . '</strong></label><br><input type="email" id="termimal_lead_email" name="termimal_lead_email" required class="termimal-contact__input" autocomplete="email"></p>';
		echo '<p><label for="termimal_lead_company"><strong>' . esc_html__( 'Company', 'termimal' ) . '</strong></label><br><input type="text" id="termimal_lead_company" name="termimal_lead_company" class="termimal-contact__input" autocomplete="organization"></p>';
		if ( $products ) {
			echo '<p><label for="termimal_lead_interest"><strong>' . esc_html__( 'Interested in', 'termimal' ) . '</strong></label><br><select id="termimal_lead_interest" name="termimal_lead_interest" class="termimal-contact__input"><option value="0">' . esc_html__( '— Select a product —', 'termimal' ) . '</option>';
			foreach ( $products as $pid => $title ) {
				echo '<option value="' . esc_attr( (string) $pid ) . '">' . esc_html( $title ) . '</option>';
			}
			echo '</select></p>';
		}
		echo '<p><label for="termimal_lead_message"><strong>' . esc_html__( 'Message', 'termimal' ) . '</strong></label><br><textarea id="termimal_lead_message" name="termimal_lead_message" rows="5" required class="termimal-contact__input termimal-contact__textarea"></textarea></p>';
		echo '<p><button type="submit" class="termimal-contact__submit">' . esc_html__( 'Submit request', 'termimal' ) . '</button></p></form></div>';
		return ob_get_clean();
	}
}
