<?php

namespace Voxel\Controllers\Frontend\Timeline;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Status_Controller extends \Voxel\Controllers\Base_Controller {

	protected function hooks() {
		$this->on( 'voxel_ajax_timeline.status.publish', '@publish_status' );
		$this->on( 'voxel_ajax_timeline.status.edit', '@edit_status' );
		$this->on( 'voxel_ajax_timeline.status.delete', '@delete_status' );
		$this->on( 'voxel_ajax_timeline.status.like', '@like_status' );
		$this->on( 'voxel_ajax_timeline.status.get_replies', '@get_replies' );
		$this->on( 'voxel_ajax_nopriv_timeline.status.get_replies', '@get_replies' );
	}

	protected function publish_status() {
		try {
			$current_user = \Voxel\current_user();
			$post_id = ! empty( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : null;
			$post = \Voxel\Post::get( $post_id );
			$values = json_decode( stripslashes( $_POST['fields'] ), true );
			$publish_as_post = !! ( $_GET['publish_as_post'] ?? null );
			$publish_to_profile = !! ( $_GET['publish_to_profile'] ?? null );
			$is_review = !! ( $_GET['is_review'] ?? null );

			if ( $publish_to_profile ) {
				$post = $current_user->get_or_create_profile();
			}

			// check if this status is attached to a post
			if ( ! $post ) {
				throw new \Exception( _x( 'Could not find post.', 'timeline', 'voxel' ) );
			} elseif ( $post->post_type->get_key() === 'profile' && ! $is_review && ! $publish_as_post ) {
				if ( (int) $current_user->get_profile_id() !== (int) $post->get_id() ) {
					throw new \Exception( _x( 'Could not find profile.', 'timeline', 'voxel' ) );
				}
			} elseif ( $post->get_status() !== 'publish' ) {
				throw new \Exception( _x( 'Could not publish to post.', 'timeline', 'voxel' ) );
			}


			if ( $publish_as_post ) {
				if ( ! $post->is_editable_by_current_user() ) {
					throw new \Exception( _x( 'You do not have permission to post as this page.', 'timeline', 'voxel' ) );
				}

				if ( ! $post->post_type->get_setting( 'timeline.enabled' ) ) {
					throw new \Exception( _x( 'Timeline posts are not enabled for this post type.', 'timeline', 'voxel' ) );
				}
			} elseif ( $is_review ) {
				if ( ! $current_user->can_review_post( $post->get_id() ) ) {
					throw new \Exception( _x( 'You\'re not allowed to review this item.', 'timeline', 'voxel' ) );
				}

				if ( $current_user->has_reviewed_post( $post->get_id() ) ) {
					throw new \Exception( _x( 'You have already reviewed this item.', 'timeline', 'voxel' ) );
				}

				$rating = $this->_get_sanitized_rating( $values );
			} elseif ( ! $current_user->can_post_to_wall( $post->get_id() ) ) {
				throw new \Exception( _x( 'You cannot post to this item\'s wall.', 'timeline', 'voxel' ) );
			}

			if ( $current_user->has_reached_status_rate_limit() ) {
				throw new \Exception( _x( 'You\'re posting too often, try again later.', 'timeline', 'voxel' ) );
			}

			$sanitized_message = $this->_get_sanitized_message( $values );
			$file_ids = $this->_get_sanitized_file_ids( $values );

			if ( empty( $sanitized_message ) && empty( $file_ids ) && ! isset( $rating ) ) {
				throw new \Exception( _x( 'Post cannot be empty.', 'timeline', 'voxel' ) );
			}

			$details = [];
			if ( ! empty( $file_ids ) ) {
				$details['files'] = $file_ids;
			}

			if ( ! empty( $publish_as_post ) ) {
				$details['posted_by'] = $current_user->get_id();
			}

			$review_score = null;
			if ( isset( $rating ) ) {
				$details['rating'] = [ 'score' => $rating ];
				$review_score = $rating;
			}

			$status = \Voxel\Timeline\Status::create( [
				'user_id' => ! empty( $publish_as_post ) ? null : $current_user->get_id(),
				'published_as' => ! empty( $publish_as_post ) ? $post->get_id() : null,
				'post_id' => $post->get_id(),
				'content' => $sanitized_message,
				'details' => ! empty( $details ) ? $details : null,
				'review_score' => $review_score ?? null,
			] );

			if ( $status->is_review() ) {
				( new \Voxel\Events\Review_Created_Event( $post->post_type ) )->dispatch( $status->get_id() );
			} elseif ( $status->get_published_as() === $post->get_id() ) {
				( new \Voxel\Events\Timeline_Status_Created_Event( $post->post_type ) )->dispatch( $status->get_id() );
			} else {
				if ( $post->get_author_id() !== $status->get_user_id() ) {
					( new \Voxel\Events\Wall_Post_Created_Event( $post->post_type ) )->dispatch( $status->get_id() );
				}
			}

			return wp_send_json( [
				'success' => true,
				'status' => \Voxel\Timeline\prepare_status_json( $status ),
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function edit_status() {
		try {
			$current_user = \Voxel\current_user();
			$status_id = absint( $_GET['status_id'] ?? null );
			$values = json_decode( stripslashes( $_POST['fields'] ), true );

			$status = \Voxel\Timeline\Status::get( $status_id );
			$editing_allowed = !! \Voxel\get( 'settings.timeline.posts.editable', true );
			if ( ! ( $status && $status->is_editable_by_current_user() && $editing_allowed ) ) {
				throw new \Exception( _x( 'You cannot edit this post.', 'timeline', 'voxel' ) );
			}

			$sanitized_message = $this->_get_sanitized_message( $values );
			$file_ids = $this->_get_sanitized_file_ids( $values );
			if ( $status->is_review() ) {
				$rating = $this->_get_sanitized_rating( $values );
			}

			if ( empty( $sanitized_message ) && empty( $file_ids ) && ! isset( $rating ) ) {
				throw new \Exception( _x( 'Post cannot be empty.', 'timeline', 'voxel' ) );
			}

			$details = $status->get_details();
			if ( ! empty( $file_ids ) ) {
				$details['files'] = $file_ids;
			}

			$review_score = null;
			if ( $status->is_review() ) {
				$review_score = $rating;
				$details['rating'] = [ 'score' => $rating ];
			}

			$status->update( [
				'content' => $sanitized_message,
				'details' => $details,
				'review_score' => $review_score,
				'edited_at' => \Voxel\utc()->format( 'Y-m-d H:i:s' ),
			] );

			return wp_send_json( [
				'success' => true,
				'status' => \Voxel\Timeline\prepare_status_json(
					\Voxel\Timeline\Status::force_get( $status->get_id() )
				),
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function delete_status() {
		try {
			$status = \Voxel\Timeline\Status::get( absint( $_GET['status_id'] ?? null ) );
			if ( ! ( $status && ( $status->is_editable_by_current_user() || $status->is_moderatable_by_current_user() ) ) ) {
				throw new \Exception( _x( 'You cannot delete this post.', 'timeline', 'voxel' ) );
			}

			$status->delete();

			return wp_send_json( [
				'success' => true,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function like_status() {
		try {
			$status = \Voxel\Timeline\Status::get( absint( $_GET['status_id'] ?? null ) );
			if ( ! $status ) {
				throw new \Exception( _x( 'You cannot like this post.', 'timeline', 'voxel' ) );
			}

			$like_count = $status->get_like_count();
			if ( $status->liked_by_user() ) {
				$status->unlike();
				$like_count--;
				$liked_by_user = false;
			} else {
				$status->like();
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

	protected function get_replies() {
		try {
			$status = \Voxel\Timeline\Status::get( absint( $_GET['status_id'] ?? null ) );
			if ( ! ( $status && $status->is_viewable_by_current_user() ) ) {
				throw new \Exception( _x( 'Could not load comments.', 'timeline', 'voxel' ) );
			}

			$parent_id = is_numeric( $_GET['parent_id'] ?? null ) ? absint( $_GET['parent_id'] ?? null ) : 0;
			$page = absint( $_GET['page'] ?? 1 );
			$per_page = 10;
			$args = [
				'status_id' => $status->get_id(),
				'parent_id' => $parent_id,
				'limit' => $per_page + 1,
				'with_like_count' => true,
				'with_reply_count' => true,
				'with_user_like_status' => true,
			];

			if ( $page > 1 ) {
				$args['offset'] = ( $page - 1 ) * $per_page;
			}

			$replies = \Voxel\Timeline\Reply::query( $args );
			$has_more = count( $replies ) > $per_page;
			if ( $has_more ) {
				array_pop( $replies );
			}

			$data = array_map( '\Voxel\Timeline\prepare_reply_json', $replies );

			return wp_send_json( [
				'success' => true,
				'data' => $data,
				'has_more' => $has_more,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	private function _get_sanitized_message( $values ) {
		$message_field = new \Voxel\Timeline\Fields\Status_Message_Field;
		$sanitized_message = $message_field->sanitize( $values['message'] ?? '' );
		$message_field->validate( $sanitized_message );

		return $sanitized_message;
	}

	private function _get_sanitized_file_ids( $values ) {
		if ( ! \Voxel\get( 'settings.timeline.posts.images.enabled', true ) ) {
			return [];
		}

		$file_field = new \Voxel\Timeline\Fields\Status_Files_Field;
		$sanitized_files = $file_field->sanitize( $values['files'] ?? [] );
		$file_field->validate( $sanitized_files );
		$file_ids = $file_field->prepare_for_storage( $sanitized_files );

		return $file_ids;
	}

	private function _get_sanitized_rating( $values ) {
		$rating = isset( $values['rating'] ) ? intval( $values['rating'] ) : null;
		if ( ! in_array( $rating, [ -2, -1, 0, 1, 2 ], true ) ) {
			throw new \Exception( _x( 'Choose a rating.', 'timeline', 'voxel' ) );
		}

		return $rating;
	}
}
