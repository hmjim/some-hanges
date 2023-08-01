<?php

namespace Voxel\Events;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Wall_Post_Created_Event extends Base_Event {

	public $post_type;

	public $status, $post, $author;

	public function __construct( \Voxel\Post_Type $post_type ) {
		$this->post_type = $post_type;
	}

	public function prepare( $status_id ) {
		$status = \Voxel\Timeline\Status::get( $status_id );
		if ( ! ( $status && $status->get_post() && $status->get_user() ) ) {
			throw new \Exception( 'Missing information.' );
		}

		$this->status = $status;
		$this->post = $status->get_post();
		$this->author = $status->get_user();
	}
	public function get_key(): string {
		return sprintf( 'post-types/%s/wall-post:created', $this->post_type->get_key() );
	}

	public function get_label(): string {
		return sprintf( '%s: New wall post', $this->post_type->get_label() );
	}

	public function get_category() {
		return sprintf( 'post-type:%s', $this->post_type->get_key() );
	}

	public static function notifications(): array {
		return [
			'post_author' => [
				'label' => 'Notify post author',
				'recipient' => function( $event ) {
					$post = $event->status->get_post();
					return $post ? $post->get_author() : null;
				},
				'inapp' => [
					'enabled' => true,
					'subject' => '@author(:display_name) submitted a wall post on @post(:title)',
					'details' => function( $event ) {
						return [
							'status_id' => $event->status->get_id(),
						];
					},
					'apply_details' => function( $event, $details ) {
						$event->prepare( $details['status_id'] ?? null );
					},
					'links_to' => function( $event ) { return $event->post->get_link(); },
					'image_id' => function( $event ) { return $event->author->get_avatar_id(); },
				],
				'email' => [
					'enabled' => true,
					'subject' => '@author(:display_name) submitted a wall post on @post(:title)',
					'message' => <<<HTML
					A new wall post has been submitted on <strong>@post(:title)</strong>
					by <strong>@author(:display_name)</strong>.
					<a href="@post(:url)">Open</a>
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
					'subject' => '@author(:display_name) submitted a wall post on @post(:title)',
					'details' => function( $event ) {
						return [
							'status_id' => $event->status->get_id(),
						];
					},
					'apply_details' => function( $event, $details ) {
						$event->prepare( $details['status_id'] ?? null );
					},
					'links_to' => function( $event ) { return $event->post->get_link(); },
					'image_id' => function( $event ) { return $event->author->get_avatar_id(); },
				],
				'email' => [
					'enabled' => false,
					'subject' => '@author(:display_name) submitted a wall post on @post(:title)',
					'message' => <<<HTML
					A new wall post has been submitted on <strong>@post(:title)</strong>
					by <strong>@author(:display_name)</strong>.
					<a href="@post(:url)">Open</a>
					HTML,
				],
			],
		];
	}

	public function dynamic_tags(): array {
		return [
			'status' => [
				'type' => \Voxel\Dynamic_Tags\Status_Group::class,
				'props' => [
					'key' => 'status',
					'label' => 'Status',
					'status' => $this->status,
				],
			],
			'author' => [
				'type' => \Voxel\Dynamic_Tags\User_Group::class,
				'props' => [
					'key' => 'author',
					'label' => 'Status author',
					'user' => $this->status && $this->status->get_user() ? $this->status->get_user() : \Voxel\User::dummy(),
				],
			],
			'post' => [
				'type' => \Voxel\Dynamic_Tags\Post_Group::class,
				'props' => [
					'key' => 'post',
					'label' => 'Post',
					'post_type' => $this->post_type,
					'post' => $this->status ? $this->status->get_post() : \Voxel\Post::dummy( [ 'post_type' => $this->post_type->get_key() ] ),
				],
			],
		];
	}
}
