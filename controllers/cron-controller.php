<?php

namespace Voxel\Controllers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Cron_Controller extends Base_Controller {

	protected function hooks() {
		$this->on( 'init', '@schedule_cron_jobs' );
		$this->on( 'init', '@register_background_processes' );
		$this->on( 'voxel/schedule:daily', '@cleanup_notifications' );
		$this->on( 'voxel/schedule:daily', '@cleanup_orders' );
		$this->on( 'voxel/schedule:cleanup_messages', '@cleanup_messages' );
	}

	protected function schedule_cron_jobs() {
		if ( ! wp_next_scheduled( 'voxel/schedule:daily' ) ) {
			wp_schedule_event( time(), 'daily', 'voxel/schedule:daily' );
		}

		if ( ! wp_next_scheduled( 'voxel/schedule:cleanup_messages' ) ) {
			wp_schedule_event( time(), 'daily', 'voxel/schedule:cleanup_messages' );
		}
	}

	protected function register_background_processes() {
		\Voxel\Queues\Async_Email::instance();
		\Voxel\Queues\Email_Queue::instance();
	}

	protected function cleanup_notifications() {
		global $wpdb;

		$persist_days = absint( \Voxel\get( 'settings.notifications.inapp_persist_days', 30 ) );
		if ( $persist_days ) {
			$wpdb->query( $wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}voxel_notifications WHERE created_at < %s",
				date( 'Y-m-d H:i:s', strtotime( '-'.$persist_days.' days' ) )
			) );
		}
	}

	protected function cleanup_orders() {
		global $wpdb;

		$persist_hours = absint( apply_filters( 'voxel/orders/pending-payment-persist-hours', 48 ) );
		$wpdb->query( $wpdb->prepare( <<<SQL
			DELETE FROM {$wpdb->prefix}voxel_orders
			WHERE `status` = 'pending_payment'
				AND `object_id` IS NULL
				AND `created_at` < %s
		SQL, date( 'Y-m-d H:i:s', strtotime( '-'.$persist_hours.' hours' ) ) ) );
	}

	protected function cleanup_messages() {
		global $wpdb;

		// remove old messages
		$persist_days = absint( \Voxel\get( 'settings.messages.persist_days', 365 ) );
		if ( $persist_days ) {
			$wpdb->query( $wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}voxel_messages WHERE created_at < %s",
				date( 'Y-m-d H:i:s', strtotime( '-'.$persist_days.' days' ) )
			) );
		}

		// remove messages with a null sender/receiver
		$wpdb->query( <<<SQL
			DELETE messages FROM {$wpdb->prefix}voxel_messages AS messages
				LEFT JOIN {$wpdb->users} AS sender_user ON ( messages.sender_type = 'user' AND messages.sender_id = sender_user.ID )
				LEFT JOIN {$wpdb->users} AS receiver_user ON ( messages.receiver_type = 'user' AND messages.receiver_id = receiver_user.ID )
				LEFT JOIN {$wpdb->posts} AS sender_post ON ( messages.sender_type = 'post' AND messages.sender_id = sender_post.ID )
				LEFT JOIN {$wpdb->posts} AS receiver_post ON ( messages.receiver_type = 'post' AND messages.receiver_id = receiver_post.ID )
			WHERE
				( messages.sender_type = 'post' AND sender_post.ID IS NULL )
				OR ( messages.receiver_type = 'post' AND receiver_post.ID IS NULL )
				OR ( messages.sender_type = 'user' AND sender_user.ID IS NULL )
				OR ( messages.receiver_type = 'user' AND receiver_user.ID IS NULL )
		SQL );

		// remove chats with a null sender/receiver
		$wpdb->query( <<<SQL
			DELETE chats FROM {$wpdb->prefix}voxel_chats AS chats
				LEFT JOIN {$wpdb->users} AS p1_user ON ( chats.p1_type = 'user' AND chats.p1_id = p1_user.ID )
				LEFT JOIN {$wpdb->users} AS p2_user ON ( chats.p2_type = 'user' AND chats.p2_id = p2_user.ID )
				LEFT JOIN {$wpdb->posts} AS p1_post ON ( chats.p1_type = 'post' AND chats.p1_id = p1_post.ID )
				LEFT JOIN {$wpdb->posts} AS p2_post ON ( chats.p2_type = 'post' AND chats.p2_id = p2_post.ID )
			WHERE
				( chats.p1_type = 'post' AND p1_post.ID IS NULL )
				OR ( chats.p2_type = 'post' AND p2_post.ID IS NULL )
				OR ( chats.p1_type = 'user' AND p1_user.ID IS NULL )
				OR ( chats.p2_type = 'user' AND p2_user.ID IS NULL )
		SQL );

		// remove follows with a null sender/receiver
		$wpdb->query( <<<SQL
			DELETE followers FROM {$wpdb->prefix}voxel_followers AS followers
				LEFT JOIN {$wpdb->users} AS user ON ( followers.object_type = 'user' AND followers.object_id = user.ID )
				LEFT JOIN {$wpdb->users} AS follower_user ON ( followers.follower_type = 'user' AND followers.follower_id = follower_user.ID )
				LEFT JOIN {$wpdb->posts} AS post ON ( followers.object_type = 'post' AND followers.object_id = post.ID )
				LEFT JOIN {$wpdb->posts} AS follower_post ON ( followers.follower_type = 'post' AND followers.follower_id = follower_post.ID )
			WHERE
				( followers.object_type = 'post' AND post.ID IS NULL )
				OR ( followers.follower_type = 'post' AND follower_post.ID IS NULL )
				OR ( followers.object_type = 'user' AND user.ID IS NULL )
				OR ( followers.follower_type = 'user' AND follower_user.ID IS NULL )
		SQL );
	}
}
