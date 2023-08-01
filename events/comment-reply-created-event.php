<?php

namespace Voxel\Events;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Comment_Reply_Created_Event extends Base_Event {

	public $reply, $comment, $status, $author;

	public function prepare( $reply_id ) {
		$reply = \Voxel\Timeline\Reply::get( $reply_id );
		if ( ! ( $reply && $reply->get_status() && $reply->get_user() && $reply->get_parent() ) ) {
			throw new \Exception( 'Missing information.' );
		}

		$this->reply = $reply;
		$this->status = $reply->get_status();
		$this->author = $reply->get_user();
		$this->comment = $reply->get_parent();
	}

	public function get_key(): string {
		return 'timeline/comment-reply:created';
	}

	public function get_label(): string {
		return 'Timeline: New comment reply';
	}

	public function get_category() {
		return 'timeline';
	}

	public static function notifications(): array {
		return [
			'comment_author' => [
				'label' => 'Notify comment author',
				'recipient' => function( $event ) {
					return $event->comment->get_user();
				},
				'inapp' => [
					'enabled' => true,
					'subject' => '@author(:display_name) replied to your comment.',
					'details' => function( $event ) {
						return [
							'reply_id' => $event->reply->get_id(),
						];
					},
					'apply_details' => function( $event, $details ) {
						$event->prepare( $details['reply_id'] ?? null );
					},
					'links_to' => function( $event ) { return $event->reply->get_link(); },
					'image_id' => function( $event ) { return $event->author->get_avatar_id(); },
				],
				'email' => [
					'enabled' => false,
					'subject' => '@author(:display_name) replied to your comment.',
					'message' => <<<HTML
					You have received a new reply from <strong>@author(:display_name)</strong>.
					<a href="@reply(:link)">Open</a>
					HTML,
				],
			],
			'status_author' => [
				'label' => 'Notify status author',
				'recipient' => function( $event ) {
					return $event->status->get_author();
				},
				'inapp' => [
					'enabled' => false,
					'subject' => '@author(:display_name) replied to a comment on your post.',
					'details' => function( $event ) {
						return [
							'reply_id' => $event->reply->get_id(),
						];
					},
					'apply_details' => function( $event, $details ) {
						$event->prepare( $details['reply_id'] ?? null );
					},
					'links_to' => function( $event ) { return $event->reply->get_link(); },
					'image_id' => function( $event ) { return $event->author->get_avatar_id(); },
				],
				'email' => [
					'enabled' => false,
					'subject' => '@author(:display_name) replied to a comment on your post.',
					'message' => <<<HTML
					New reply received by <strong>@author(:display_name)</strong>.
					<a href="@reply(:link)">Open</a>
					HTML,
				],
			],
			'post_author' => [
				'label' => 'Notify post author',
				'recipient' => function( $event ) {
					$post = $event->status->get_post();
					return $post ? $post->get_author() : null;
				},
				'inapp' => [
					'enabled' => false,
					'subject' => '@author(:display_name) commented on a status on your post.',
					'details' => function( $event ) {
						return [
							'reply_id' => $event->reply->get_id(),
						];
					},
					'apply_details' => function( $event, $details ) {
						$event->prepare( $details['reply_id'] ?? null );
					},
					'links_to' => function( $event ) { return $event->reply->get_link(); },
					'image_id' => function( $event ) { return $event->author->get_avatar_id(); },
				],
				'email' => [
					'enabled' => false,
					'subject' => '@author(:display_name) replied to a comment on your post.',
					'message' => <<<HTML
					New reply received by <strong>@author(:display_name)</strong>.
					<a href="@reply(:link)">Open</a>
					HTML,
				],
			],
			'admin' => [
				'label' => 'Notify admin',
				'recipient' => function( $event ) {
					return \Voxel\User::get( \Voxel\get( 'settings.notifications.admin_user' ) );
				},
				'inapp' => [
					'enabled' => false,
					'subject' => '@author(:display_name) commented on a status.',
					'details' => function( $event ) {
						return [
							'reply_id' => $event->reply->get_id(),
						];
					},
					'apply_details' => function( $event, $details ) {
						$event->prepare( $details['reply_id'] ?? null );
					},
					'links_to' => function( $event ) { return $event->reply->get_link(); },
					'image_id' => function( $event ) { return $event->author->get_avatar_id(); },
				],
				'email' => [
					'enabled' => false,
					'subject' => '@author(:display_name) replied to a comment.',
					'message' => <<<HTML
					New reply received by <strong>@author(:display_name)</strong>.
					<a href="@reply(:link)">Open</a>
					HTML,
				],
			],
		];
	}

	public function dynamic_tags(): array {
		return [
			'reply' => [
				'type' => \Voxel\Dynamic_Tags\Reply_Group::class,
				'props' => [
					'key' => 'reply',
					'label' => 'Reply',
					'reply' => $this->reply,
				],
			],
			'author' => [
				'type' => \Voxel\Dynamic_Tags\User_Group::class,
				'props' => [
					'key' => 'author',
					'label' => 'Reply author',
					'user' => $this->author,
				],
			],
			'comment' => [
				'type' => \Voxel\Dynamic_Tags\Reply_Group::class,
				'props' => [
					'key' => 'comment',
					'label' => 'Comment',
					'reply' => $this->comment,
				],
			],
			'status' => [
				'type' => \Voxel\Dynamic_Tags\Status_Group::class,
				'props' => [
					'key' => 'status',
					'label' => 'Status',
					'status' => $this->status,
				],
			],
		];
	}
}
