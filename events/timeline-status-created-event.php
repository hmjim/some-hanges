<?php

namespace Voxel\Events;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Timeline_Status_Created_Event extends Base_Event {

	public $post_type;

	public $status, $post;

	public function __construct( \Voxel\Post_Type $post_type ) {
		$this->post_type = $post_type;
	}

	public function prepare( $status_id ) {
		$status = \Voxel\Timeline\Status::get( $status_id );
		if ( ! ( $status && $status->get_post() ) ) {
			throw new \Exception( 'Missing information.' );
		}

		$this->status = $status;
		$this->post = $status->get_post();
	}

	public function get_key(): string {
		return sprintf( 'post-types/%s/status:created', $this->post_type->get_key() );
	}

	public function get_label(): string {
		return sprintf( '%s: New status update', $this->post_type->get_label() );
	}

	public function get_category() {
		return sprintf( 'post-type:%s', $this->post_type->get_key() );
	}

	public static function notifications(): array {
		return [
			'admin' => [
				'label' => 'Notify admin',
				'recipient' => function( $event ) {
					return \Voxel\User::get( \Voxel\get( 'settings.notifications.admin_user' ) );
				},
				'inapp' => [
					'enabled' => false,
					'subject' => '@post(:title) posted an update',
					'details' => function( $event ) {
						return [
							'status_id' => $event->status->get_id(),
						];
					},
					'apply_details' => function( $event, $details ) {
						$event->prepare( $details['status_id'] ?? null );
					},
					'links_to' => function( $event ) { return $event->post->get_link(); },
					'image_id' => function( $event ) { return $event->post->get_logo_id(); },
				],
				'email' => [
					'enabled' => false,
					'subject' => '@post(:title) posted an update',
					'message' => <<<HTML
					A new status update has been submitted on <strong>@post(:title)</strong>.
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
