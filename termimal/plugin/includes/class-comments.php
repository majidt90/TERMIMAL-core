<?php
/**
 * Product comments / Q&A: threaded replies, question/answer meta, moderation, admin notify.
 *
 * @package Termimal_Core
 */

defined( 'ABSPATH' ) || exit;

class Termimal_Comments {

	const META_KIND = '_termimal_comment_kind';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'ensure_comment_support' ), 20 );
		add_filter( 'comments_open', array( __CLASS__, 'comments_open' ), 10, 2 );
		add_filter( 'comment_form_defaults', array( __CLASS__, 'form_defaults' ) );
		add_filter( 'comment_form_fields', array( __CLASS__, 'form_fields' ) );
		add_action( 'comment_post', array( __CLASS__, 'save_kind_and_notify' ), 10, 3 );
		add_filter( 'preprocess_comment', array( __CLASS__, 'preprocess' ) );
		add_filter( 'get_comment_text', array( __CLASS__, 'prepend_kind_badge' ), 10, 2 );
		add_action( 'add_meta_boxes_comment', array( __CLASS__, 'admin_meta_box' ) );
		add_action( 'edit_comment', array( __CLASS__, 'save_admin_kind' ) );
		add_filter( 'manage_edit-comments_columns', array( __CLASS__, 'admin_columns' ) );
		add_action( 'manage_comments_custom_column', array( __CLASS__, 'admin_column_content' ), 10, 2 );
	}

	public static function ensure_comment_support() {
		add_post_type_support( 'termimal_product', 'comments' );
	}

	public static function comments_open( $open, $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post || 'termimal_product' !== $post->post_type ) {
			return $open;
		}
		$options = get_option( 'termimal_options', array() );
		if ( isset( $options['enable_comments'] ) && empty( $options['enable_comments'] ) ) {
			return false;
		}
		return $open;
	}

	public static function form_defaults( $defaults ) {
		if ( ! is_singular( 'termimal_product' ) ) {
			return $defaults;
		}
		$defaults['title_reply'] = __( 'Ask a question or leave a comment', 'termimal' );
		$defaults['title_reply_to'] = __( 'Reply to %s', 'termimal' );
		$defaults['cancel_reply_link'] = __( 'Cancel reply', 'termimal' );
		$defaults['label_submit'] = __( 'Post', 'termimal' );
		$defaults['comment_notes_before'] = '<p class="comment-notes">' . esc_html__( 'Your email is not published. Comments may be moderated.', 'termimal' ) . '</p>';
		$defaults['class_form'] = 'termimal-comment-form comment-form';
		$defaults['class_submit'] = 'termimal-comment-submit submit';
		return $defaults;
	}

	public static function form_fields( $fields ) {
		if ( ! is_singular( 'termimal_product' ) ) {
			return $fields;
		}
		$kind_field = '<p class="comment-form-kind"><label for="termimal_comment_kind">' . esc_html__( 'Type', 'termimal' ) . '</label> '
			. '<select name="termimal_comment_kind" id="termimal_comment_kind" class="termimal-comment-kind">'
			. '<option value="question">' . esc_html__( 'Question', 'termimal' ) . '</option>'
			. '<option value="comment">' . esc_html__( 'Comment', 'termimal' ) . '</option>'
			. '</select></p>';
		$new = array();
		foreach ( $fields as $key => $html ) {
			if ( 'comment' === $key ) {
				$new['termimal_kind'] = $kind_field;
			}
			$new[ $key ] = $html;
		}
		if ( ! isset( $new['termimal_kind'] ) ) {
			$new['termimal_kind'] = $kind_field;
		}
		return $new;
	}

	public static function preprocess( $commentdata ) {
		if ( empty( $commentdata['comment_post_ID'] ) ) {
			return $commentdata;
		}
		$post = get_post( (int) $commentdata['comment_post_ID'] );
		if ( $post && 'termimal_product' === $post->post_type && ! is_user_logged_in() ) {
			$options = get_option( 'termimal_options', array() );
			if ( ! isset( $options['moderate_comments'] ) || ! empty( $options['moderate_comments'] ) ) {
				add_filter( 'pre_comment_approved', array( __CLASS__, 'force_hold' ), 99 );
			}
		}
		return $commentdata;
	}

	public static function force_hold( $approved ) {
		remove_filter( 'pre_comment_approved', array( __CLASS__, 'force_hold' ), 99 );
		if ( is_user_logged_in() && current_user_can( 'moderate_comments' ) ) {
			return $approved;
		}
		return 0;
	}

	public static function save_kind_and_notify( $comment_id, $approved, $commentdata ) {
		$post_id = isset( $commentdata['comment_post_ID'] ) ? (int) $commentdata['comment_post_ID'] : 0;
		$post    = get_post( $post_id );
		if ( ! $post || 'termimal_product' !== $post->post_type ) {
			return;
		}
		$kind = 'comment';
		if ( ! empty( $_POST['termimal_comment_kind'] ) ) {
			$raw = sanitize_key( wp_unslash( $_POST['termimal_comment_kind'] ) );
			if ( in_array( $raw, array( 'question', 'comment', 'answer' ), true ) ) {
				$kind = $raw;
			}
		}
		$parent_id = isset( $commentdata['comment_parent'] ) ? (int) $commentdata['comment_parent'] : 0;
		if ( $parent_id > 0 && 'question' === get_comment_meta( $parent_id, self::META_KIND, true ) ) {
			$kind = 'answer';
		}
		update_comment_meta( $comment_id, self::META_KIND, $kind );
		$options = get_option( 'termimal_options', array() );
		if ( ! isset( $options['notify_new_comments'] ) || ! empty( $options['notify_new_comments'] ) ) {
			self::notify_admin( $comment_id, $post, $kind, $approved );
		}
	}

	public static function notify_admin( $comment_id, $post, $kind, $approved ) {
		$comment = get_comment( $comment_id );
		if ( ! $comment ) {
			return;
		}
		$status = ( '1' === (string) $approved || 1 === $approved ) ? 'approved' : 'pending';
		$subject = sprintf( __( '[TERMIMAL] New %1$s on “%2$s”', 'termimal' ), $kind, $post->post_title );
		$body = sprintf( "Product: %s\nAuthor: %s <%s>\nKind: %s\nStatus: %s\n\n%s\n\n%s\n", get_permalink( $post ), $comment->comment_author, $comment->comment_author_email, $kind, $status, $comment->comment_content, admin_url( 'comment.php?action=editcomment&c=' . $comment_id ) );
		wp_mail( get_option( 'admin_email' ), $subject, $body, array( 'Content-Type: text/plain; charset=UTF-8' ) );
	}

	public static function prepend_kind_badge( $text, $comment ) {
		if ( ! $comment || ! is_object( $comment ) ) {
			return $text;
		}
		$post = get_post( $comment->comment_post_ID );
		if ( ! $post || 'termimal_product' !== $post->post_type ) {
			return $text;
		}
		$kind = get_comment_meta( $comment->comment_ID, self::META_KIND, true );
		if ( ! $kind || 'comment' === $kind ) {
			return $text;
		}
		$label = ( 'question' === $kind ) ? __( 'Question', 'termimal' ) : __( 'Answer', 'termimal' );
		return '<span class="termimal-comment-badge termimal-comment-badge--' . esc_attr( $kind ) . '">' . esc_html( $label ) . '</span> ' . $text;
	}

	public static function admin_meta_box() {
		add_meta_box( 'termimal_comment_kind', __( 'TERMIMAL type', 'termimal' ), array( __CLASS__, 'render_admin_kind' ), 'comment', 'normal', 'high' );
	}

	public static function render_admin_kind( $comment ) {
		$kind = get_comment_meta( $comment->comment_ID, self::META_KIND, true ) ?: 'comment';
		wp_nonce_field( 'termimal_comment_kind', 'termimal_comment_kind_nonce' );
		echo '<select name="termimal_comment_kind_admin"><option value="question" ' . selected( $kind, 'question', false ) . '>Question</option><option value="answer" ' . selected( $kind, 'answer', false ) . '>Answer</option><option value="comment" ' . selected( $kind, 'comment', false ) . '>Comment</option></select>';
	}

	public static function save_admin_kind( $comment_id ) {
		if ( empty( $_POST['termimal_comment_kind_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['termimal_comment_kind_nonce'] ) ), 'termimal_comment_kind' ) ) {
			return;
		}
		if ( ! current_user_can( 'moderate_comments' ) ) {
			return;
		}
		if ( isset( $_POST['termimal_comment_kind_admin'] ) ) {
			$kind = sanitize_key( wp_unslash( $_POST['termimal_comment_kind_admin'] ) );
			if ( in_array( $kind, array( 'question', 'answer', 'comment' ), true ) ) {
				update_comment_meta( $comment_id, self::META_KIND, $kind );
			}
		}
	}

	public static function admin_columns( $columns ) {
		$columns['termimal_kind'] = __( 'Type', 'termimal' );
		return $columns;
	}

	public static function admin_column_content( $column, $comment_id ) {
		if ( 'termimal_kind' === $column ) {
			$kind = get_comment_meta( $comment_id, self::META_KIND, true );
			echo esc_html( $kind ? $kind : '—' );
		}
	}
}
