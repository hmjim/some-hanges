<?php

namespace Voxel\Users;

if ( ! defined('ABSPATH') ) {
	exit;
}

trait Social_Trait {

	public function get_follow_status( $object_type, $object_id ) {
		global $wpdb;
		$status = $wpdb->get_var( $wpdb->prepare(
			"SELECT `status` FROM {$wpdb->prefix}voxel_followers
				WHERE `object_type` = '%s' AND `object_id` = %d AND `follower_type` = 'user' AND `follower_id` = %d",
			$object_type,
			$object_id,
			$this->get_id()
		) );

		if ( is_null( $status ) ) {
			return null;
		}

		$status = intval( $status );
		if ( ! in_array( $status, [ -1, 0, 1 ], true ) ) {
			return null;
		}

		return $status;
	}

	public function set_follow_status( $object_type, $object_id, $status ) {
		global $wpdb;
		if ( $status === \Voxel\FOLLOW_NONE ) {
			$wpdb->query( $wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}voxel_followers WHERE `object_type` = '%s' AND `object_id` = %d AND `follower_type` = 'user' AND `follower_id` = %d",
				$object_type,
				$object_id,
				$this->get_id()
			) );
		} else {
			$status = intval( $status );
			if ( ! in_array( $status, [ -1, 0, 1 ], true ) ) {
				return null;
			}

			$wpdb->query( $wpdb->prepare(
				"INSERT INTO {$wpdb->prefix}voxel_followers (`object_type`, `object_id`, `follower_type`, `follower_id`, `status`)
					VALUES ('%s', %d, 'user', %d, %d) ON DUPLICATE KEY UPDATE `status` = VALUES(`status`)",
				$object_type,
				$object_id,
				$this->get_id(),
				$status
			) );
		}

		\Voxel\cache_user_follow_stats( $this->get_id() );
		$object_type === 'post' ? \Voxel\cache_post_follow_stats( $object_id ) : \Voxel\cache_user_follow_stats( $object_id );
	}

	public function can_review_post( $post_id ): bool {
		$post = \Voxel\Post::get( $post_id );
		if ( ! $post ) {
			return false;
		}

		if ( (int) $post->get_author_id() === (int) $this->get_id() && ! $this->has_cap('administrator') ) {
			return false;
		}

		return $post->post_type->get_setting( 'timeline.reviews' ) === 'public' || (
			$post->post_type->get_setting( 'timeline.reviews' ) === 'followers_only'
			&& $this->get_follow_status( 'post', $post->get_id() ) === \Voxel\FOLLOW_ACCEPTED
		);
	}

	public function has_reviewed_post( $post_id ): bool {
		$existing_review = \Voxel\Timeline\Status::query( [
			'match' => 'reviews',
			'user_id' => $this->get_id(),
			'post_id' => $post_id,
			'limit' => 1,
		] );

		return ! empty( $existing_review );
	}

	public function can_post_to_wall( $post_id ): bool {
		$post = \Voxel\Post::get( $post_id );
		if ( ! $post ) {
			return false;
		}

		return $post->post_type->get_setting( 'timeline.wall' ) === 'public' || (
			$post->post_type->get_setting( 'timeline.wall' ) === 'followers_only'
			&& $this->get_follow_status( 'post', $post->get_id() ) === \Voxel\FOLLOW_ACCEPTED
		);
	}

	public function follows_post( $post_id ) {
		return $this->get_follow_status( 'post', $post_id ) === \Voxel\FOLLOW_ACCEPTED;
	}

	public function get_follow_stats() {
		$stats = (array) json_decode( get_user_meta( $this->get_id(), 'voxel:follow_stats', true ), ARRAY_A );
		if ( ! isset( $stats['followed'] ) ) {
			$stats = \Voxel\cache_user_follow_stats( $this->get_id() );
		}

		return $stats;
	}

	public function has_reached_status_rate_limit(): bool {
		if ( current_user_can( 'administrator' ) ) {
			return false;
		}

		return \Voxel\Timeline\user_has_reached_status_rate_limit( $this->get_id() );
	}

	public function has_reached_reply_rate_limit(): bool {
		if ( current_user_can( 'administrator' ) ) {
			return false;
		}

		return \Voxel\Timeline\user_has_reached_reply_rate_limit( $this->get_id() );
	}

	/**
	 * Get unread notification count.
	 *
	 * @since 1.0
	 */
	public function get_notification_count() {
		$count = (array) json_decode( get_user_meta( $this->get_id(), 'voxel:notifications', true ), ARRAY_A );
		if ( ! strtotime( $count['since'] ?? '' ) ) {
			$count['since'] = date( 'Y-m-d H:i:s', time() );
		}

		return [
			'unread' => absint( $count['unread'] ?? 0 ),
			'since' => $count['since'],
		];
	}

	/**
	 * Calculate unread notification count (e.g. when a new notification is received)
	 *
	 * @since 1.0
	 */
	public function update_notification_count() {
		$count = $this->get_notification_count();
		$updated_count = \Voxel\Notification::get_unread_count( $this->get_id(), $count['since'] );

		update_user_meta( $this->get_id(), 'voxel:notifications', wp_slash( wp_json_encode( [
			'unread' => $updated_count,
			'since' => $count['since'],
		] ) ) );
	}

	/**
	 * Reset unread notification count (e.g. when user opens the notification popup)
	 *
	 * @since 1.0
	 */
	public function reset_notification_count() {
		update_user_meta( $this->get_id(), 'voxel:notifications', wp_slash( wp_json_encode( [
			'unread' => 0,
			'since' => date( 'Y-m-d H:i:s', time() ),
		] ) ) );
	}

	public function set_inbox_activity( $has_activity ) {
		if ( ! \Voxel\get( 'settings.messages.enable_real_time', true ) ) {
			return;
		}

		$dir =  trailingslashit( WP_CONTENT_DIR ) . 'uploads/voxel-cache/inbox-activity';
		$file = trailingslashit( $dir ) . $this->get_id() . '.txt';

		if ( $has_activity ) {
			if ( ! is_file( $file ) || filemtime( $file ) < time() ) {
				// \Voxel\log( 'user ' . $this->get_id() . ' new activity: true' );
				wp_mkdir_p( $dir );
				@touch( $file, time() + WEEK_IN_SECONDS );
			}
		} else {
			if ( ! is_file( $file ) || filemtime( $file ) > time() ) {
				// \Voxel\log( 'user ' . $this->get_id() . ' new activity: false' );
				wp_mkdir_p( $dir );
				@touch( $file, time() - WEEK_IN_SECONDS );
			}
		}
	}

	public function get_inbox_meta() {
		$meta = (array) json_decode( get_user_meta( $this->get_id(), 'voxel:dms', true ), ARRAY_A );
		if ( ! strtotime( $meta['since'] ?? '' ) ) {
			$meta['since'] = date( 'Y-m-d H:i:s', time() );
		}

		return [
			'since' => $meta['since'],
			'unread' => $meta['unread'] ?? false,
		];
	}

	public function update_inbox_meta( $args ) {
		$meta = $this->get_inbox_meta();
		if ( isset( $args['unread'] ) ) {
			$meta['unread'] = $args['unread'];
		}

		if ( isset( $args['since'] ) ) {
			$meta['since'] = $args['since'];
		}

		update_user_meta( $this->get_id(), 'voxel:dms', wp_slash( wp_json_encode( $meta ) ) );
	}
}
