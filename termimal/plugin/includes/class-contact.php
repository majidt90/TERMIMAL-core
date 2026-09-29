<?php
/**
 * Contact form shortcode [termimal_contact].
 *
 * @package Termimal_Core
 */

defined( 'ABSPATH' ) || exit;

class Termimal_Contact {

	public static function init() {
		add_shortcode( 'termimal_contact', array( __CLASS__, 'shortcode' ) );
		add_action( 'init', array( __CLASS__, 'handle_submit' ) );
	}

	public static function handle_submit() {
		if ( empty( $_POST['termimal_contact_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['termimal_contact_nonce'] ) ), 'termimal_contact' ) ) {
			return;
		}
		$name    = isset( $_POST['termimal_name'] ) ? sanitize_text_field( wp_unslash( $_POST['termimal_name'] ) ) : '';
		$email   = isset( $_POST['termimal_email'] ) ? sanitize_email( wp_unslash( $_POST['termimal_email'] ) ) : '';
		$message = isset( $_POST['termimal_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['termimal_message'] ) ) : '';
		if ( ! empty( $_POST['termimal_website'] ) ) {
			wp_safe_redirect( add_query_arg( 'contact', 'ok', wp_get_referer() ? wp_get_referer() : home_url( '/' ) ) );
			exit;
		}
		if ( ! $name || ! is_email( $email ) || ! $message ) {
			wp_safe_redirect( add_query_arg( 'contact', 'error', wp_get_referer() ? wp_get_referer() : home_url( '/' ) ) );
			exit;
		}
		$to      = get_option( 'admin_email' );
		$subject = sprintf( '[TERMIMAL Contact] %s', $name );
		$body    = "Name: {$name}\nEmail: {$email}\n\n{$message}\n";
		$headers = array( 'Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $name . ' <' . $email . '>' );
		$sent    = wp_mail( $to, $subject, $body, $headers );
		wp_safe_redirect( add_query_arg( 'contact', $sent ? 'ok' : 'error', wp_get_referer() ? wp_get_referer() : home_url( '/' ) ) );
		exit;
	}

	public static function shortcode( $atts ) {
		$status = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		ob_start();
		?>
		<div class="termimal-contact">
			<?php if ( 'ok' === $status ) : ?>
				<p class="termimal-contact__notice termimal-contact__notice--ok" role="status"><?php esc_html_e( 'Thank you. Your message has been sent.', 'termimal' ); ?></p>
			<?php elseif ( 'error' === $status ) : ?>
				<p class="termimal-contact__notice termimal-contact__notice--error" role="alert"><?php esc_html_e( 'Something went wrong. Please check the fields and try again.', 'termimal' ); ?></p>
			<?php endif; ?>
			<form class="termimal-contact__form" method="post" action="">
				<?php wp_nonce_field( 'termimal_contact', 'termimal_contact_nonce' ); ?>
				<p class="termimal-contact__hp" aria-hidden="true" style="position:absolute;left:-9999px;">
					<label for="termimal_website">Website</label>
					<input type="text" name="termimal_website" id="termimal_website" value="" tabindex="-1" autocomplete="off">
				</p>
				<p><label for="termimal_name"><strong><?php esc_html_e( 'Name', 'termimal' ); ?></strong></label><br>
				<input type="text" id="termimal_name" name="termimal_name" required class="termimal-contact__input" autocomplete="name"></p>
				<p><label for="termimal_email"><strong><?php esc_html_e( 'Email', 'termimal' ); ?></strong></label><br>
				<input type="email" id="termimal_email" name="termimal_email" required class="termimal-contact__input" autocomplete="email"></p>
				<p><label for="termimal_message"><strong><?php esc_html_e( 'Message', 'termimal' ); ?></strong></label><br>
				<textarea id="termimal_message" name="termimal_message" rows="6" required class="termimal-contact__input termimal-contact__textarea"></textarea></p>
				<p><button type="submit" class="termimal-contact__submit"><?php esc_html_e( 'Send message', 'termimal' ); ?></button></p>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}
}
