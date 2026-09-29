<?php
/**
 * Helpful votes on product comments.
 *
 * @package Termimal_Core
 */

defined( 'ABSPATH' ) || exit;

class Termimal_Votes {

	const META_HELPFUL = '_termimal_helpful_count';

	public static function init() {
		add_filter( 'get_comment_text', array( __CLASS__, 'append_vote_ui' ), 20, 2 );
		add_action( 'wp_ajax_termimal_helpful', array( __CLASS__, 'ajax_vote' ) );
		add_action( 'wp_ajax_nopriv_termimal_helpful', array( __CLASS__, 'ajax_vote' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
	}

	public static function enqueue() {
		if ( ! is_singular( 'termimal_product' ) ) {
			return;
		}
		wp_enqueue_script( 'termimal-votes', TERMIMAL_CORE_URL . 'assets/js/votes.js', array(), TERMIMAL_CORE_VERSION, true );
		wp_localize_script( 'termimal-votes', 'termimalVotes', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'termimal_helpful' ),
		) );
	}

	public static function append_vote_ui( $text, $comment ) {
		if ( ! $comment || ! is_object( $comment ) ) {
			return $text;
		}
		$post = get_post( $comment->comment_post_ID );
		if ( ! $post || 'termimal_product' !== $post->post_type || '1' !== (string) $comment->comment_approved ) {
			return $text;
		}
		$count = (int) get_comment_meta( $comment->comment_ID, self::META_HELPFUL, true );
		$btn = sprintf(
			'<p class="termimal-helpful" data-comment="%d"><button type="button" class="termimal-helpful__btn">%s</button> <span class="termimal-helpful__count">%s</span></p>',
			(int) $comment->comment_ID,
			esc_html__( 'Helpful', 'termimal' ),
			esc_html( sprintf( _n( '%d person found this helpful', '%d people found this helpful', $count, 'termimal' ), $count ) )
		);
		return $text . $btn;
	}

	public static function ajax_vote() {
		check_ajax_referer( 'termimal_helpful', 'nonce' );
		$comment_id = isset( $_POST['comment_id'] ) ? absint( $_POST['comment_id'] ) : 0;
		$comment = get_comment( $comment_id );
		if ( ! $comment || '1' !== (string) $comment->comment_approved ) {
			wp_send_json_error( array( 'message' => 'invalid' ), 400 );
		}
		$post = get_post( $comment->comment_post_ID );
		if ( ! $post || 'termimal_product' !== $post->post_type ) {
			wp_send_json_error( array( 'message' => 'invalid' ), 400 );
		}
		$cookie_key = 'termimal_h_' . $comment_id;
		if ( ! empty( $_COOKIE[ $cookie_key ] ) ) {
			$count = (int) get_comment_meta( $comment_id, self::META_HELPFUL, true );
			wp_send_json_success( array( 'count' => $count, 'label' => sprintf( _n( '%d person found this helpful', '%d people found this helpful', $count, 'termimal' ), $count ), 'already' => true ) );
		}
		$count = (int) get_comment_meta( $comment_id, self::META_HELPFUL, true );
		$count++;
		update_comment_meta( $comment_id, self::META_HELPFUL, $count );
		setcookie( $cookie_key, '1', time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
		wp_send_json_success( array( 'count' => $count, 'label' => sprintf( _n( '%d person found this helpful', '%d people found this helpful', $count, 'termimal' ), $count ), 'already' => false ) );
	}
}
