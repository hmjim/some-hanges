<?php

namespace Voxel\Controllers\Frontend\Timeline;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Reply_Controller extends \Voxel\Controllers\Base_Controller {

	protected function hooks() {
		$this->on( 'voxel_ajax_timeline.reply.publish', '@publish_reply' );
		$this->on( 'voxel_ajax_timeline.reply.edit', '@edit_reply' );
		$this->on( 'voxel_ajax_timeline.reply.delete', '@delete_reply' );
		$this->on( 'voxel_ajax_timeline.reply.like', '@like_reply' );
	}

	protected function publish_reply() {
		try {
			$status_id = ! empty( $_GET['status_id'] ) ? absint( $_GET['status_id'] ) : null;
			$status = \Voxel\Timeline\Status::get( $status_id );
			if ( ! $status ) {
				throw new \Exception( _x( 'You cannot reply to this post.', 'timeline', 'voxel' ) );
			}

			if ( \Voxel\current_user()->has_reached_reply_rate_limit() ) {
				throw new \Exception( _x( 'You\'re commenting too often, try again later.', 'timeline', 'voxel' ) );
			}

			$parent_id = ! empty( $_GET['parent_id'] ) ? absint( $_GET['parent_id'] ) : null;
			if ( $parent_id !== null ) {
				$parent = \Voxel\Timeline\Reply::get( $parent_id );
				if ( ! $parent ) {
					throw new \Exception( _x( 'You cannot reply to this comment.', 'timeline', 'voxel' ) );
				}
			}

			$values = json_decode( stripslashes( $_POST['fields'] ), true );
			$sanitized_message = $this->_get_sanitized_message( $values );

			$reply = \Voxel\Timeline\Reply::create( [
				'user_id' => get_current_user_id(),
				'status_id' => $status->get_id(),
				'parent_id' => isset( $parent ) ? $parent->get_id() : null,
				'content' => $sanitized_message,
			] );

			if ( isset( $parent ) ) {
				if ( $parent->get_user_id() !== $reply->get_user_id() ) {
					( new \Voxel\Events\Comment_Reply_Created_Event )->dispatch( $reply->get_id() );
				}
			} else {
				if ( $status->get_user_id() !== $reply->get_user_id() ) {
					( new \Voxel\Events\Comment_Created_Event )->dispatch( $reply->get_id() );
				}
			}

			return wp_send_json( [
				'success' => true,
				'reply' => \Voxel\Timeline\prepare_reply_json( $reply ),
				'status_reply_count' => number_format_i18n( ( $status->get_reply_count() ?? 0 ) + 1 ),
				'parent_reply_count' => isset( $parent ) ? number_format_i18n( ( $parent->get_reply_count() ?? 0 ) + 1 ) : null,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function edit_reply() {
		try {
			$status_id = absint( $_GET['status_id'] ?? null );
			$status = \Voxel\Timeline\Status::get( $status_id );
			if ( ! $status ) {
				throw new \Exception( _x( 'You cannot reply to this post.', 'timeline', 'voxel' ) );
			}

			$reply_id = absint( $_GET['reply_id'] ?? null );
			$reply = \Voxel\Timeline\Reply::get( $reply_id );
			$editing_allowed = !! \Voxel\get( 'settings.timeline.replies.editable', true );
			if ( ! ( $reply && $reply->is_editable_by_current_user() && $editing_allowed ) ) {
				throw new \Exception( _x( 'You cannot edit this comment.', 'timeline', 'voxel' ) );
			}

			$parent_id = ! empty( $_GET['parent_id'] ) ? absint( $_GET['parent_id'] ) : null;
			if ( $parent_id !== null ) {
				$parent = \Voxel\Timeline\Reply::get( $parent_id );
				if ( ! $parent ) {
					throw new \Exception( _x( 'You cannot reply to this comment.', 'timeline', 'voxel' ) );
				}
			}

			$values = json_decode( stripslashes( $_POST['fields'] ), true );
			$sanitized_message = $this->_get_sanitized_message( $values );

			$reply->update( [
				'content' => $sanitized_message,
				'edited_at' => \Voxel\utc()->format( 'Y-m-d H:i:s' ),
			] );

			return wp_send_json( [
				'success' => true,
				'reply' => \Voxel\Timeline\prepare_reply_json( \Voxel\Timeline\Reply::force_get( $reply->get_id() ) ),
				'status_reply_count' => number_format_i18n( ( $status->get_reply_count() ?? 0 ) + 1 ),
				'parent_reply_count' => isset( $parent ) ? number_format_i18n( ( $parent->get_reply_count() ?? 0 ) + 1 ) : null,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function delete_reply() {
		try {
			$reply = \Voxel\Timeline\Reply::get( absint( $_GET['reply_id'] ?? null ) );
			if ( ! ( $reply && ( $reply->is_editable_by_current_user() || $reply->is_moderatable_by_current_user() ) ) ) {
				throw new \Exception( _x( 'You cannot delete this comment.', 'timeline', 'voxel' ) );
			}

			$status_id = $reply->get_status_id();
			$parent = $reply->get_parent();

			$reply->delete();

			// force get from db to retrieve the correct number of replies
			$status = \Voxel\Timeline\Status::force_get( $status_id );

			return wp_send_json( [
				'success' => true,
				'status_reply_count' => $status->get_reply_count() ? number_format_i18n( $status->get_reply_count() ) : null,
				'parent_reply_count' => ( $parent && $parent->get_reply_count() > 1 )
					? number_format_i18n( $parent->get_reply_count() - 1 )
					: null,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function like_reply() {
		try {
			$reply = \Voxel\Timeline\Reply::get( absint( $_GET['reply_id'] ?? null ) );
			if ( ! $reply ) {
				throw new \Exception( _x( 'You cannot like this comment.', 'timeline', 'voxel' ) );
			}

			$like_count = $reply->get_like_count();
			if ( $reply->liked_by_user() ) {
				$reply->unlike();
				$like_count--;
				$liked_by_user = false;
			} else {
				$reply->like();
				$like_count++;
				$liked_by_user = true;
			}

			return wp_send_json( [
				'success' => true,
				'liked_by_user' => $liked_by_user,
				'like_count' => $like_count ? number_format_i18n( $like_count ) : null,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	private function _get_sanitized_message( $values ) {
		$sanitized_message = sanitize_textarea_field( $values['message'] ?? '' );
		if ( empty( $sanitized_message ) ) {
			throw new \Exception( _x( 'Comment cannot be empty.', 'timeline', 'voxel' ) );
		}

		$maxlength = absint( \Voxel\get( 'settings.timeline.replies.maxlength', 2000 ) );
		if ( mb_strlen( $sanitized_message ) > $maxlength ) {
			throw new \Exception( sprintf(
				_x( 'Comment cannot be longer than %d characters.', 'timeline', 'voxel' ),
				number_format_i18n( $maxlength )
			) );
		}

		return $sanitized_message;
	}
}
