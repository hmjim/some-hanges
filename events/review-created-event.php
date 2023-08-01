<?php

namespace Voxel\Events;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Review_Created_Event extends Base_Event {

	public $post_type;

	public $review, $post, $author;

	public function __construct( \Voxel\Post_Type $post_type ) {
		$this->post_type = $post_type;
	}

	public function prepare( $review_id ) {
		$review = \Voxel\Timeline\Status::get( $review_id );
		if ( ! ( $review && $review->get_post() && $review->get_user() ) ) {
			throw new \Exception( 'Missing information.' );
		}

		$this->review = $review;
		$this->post = $review->get_post();
		$this->author = $review->get_user();
	}

	public function get_key(): string {
		return sprintf( 'post-types/%s/review:created', $this->post_type->get_key() );
	}

	public function get_label(): string {
		return sprintf( '%s: New review', $this->post_type->get_label() );
	}

	public function get_category() {
		return sprintf( 'post-type:%s', $this->post_type->get_key() );
	}

	public static function notifications(): array {
		return [
			'post_author' => [
				'label' => 'Notify post author',
				'recipient' => function( $event ) {
					$post = $event->review->get_post();
					return $post ? $post->get_author() : null;
				},
				'inapp' => [
					'enabled' => true,
					'subject' => '@author(:display_name) submitted a review on @post(:title)',
					'details' => function( $event ) {
						return [
							'review_id' => $event->review->get_id(),
						];
					},
					'apply_details' => function( $event, $details ) {
						$event->prepare( $details['review_id'] ?? null );
					},
					'links_to' => function( $event ) { return $event->post->get_link(); },
					'image_id' => function( $event ) { return $event->author->get_avatar_id(); },
				],
				'email' => [
					'enabled' => true,
					'subject' => '@author(:display_name) submitted a review on @post(:title)',
					'message' => <<<HTML
					A new review has been submitted on <strong>@post(:title)</strong>
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
					'subject' => '@author(:display_name) submitted a review on @post(:title)',
					'details' => function( $event ) {
						return [
							'review_id' => $event->review->get_id(),
						];
					},
					'apply_details' => function( $event, $details ) {
						$event->prepare( $details['review_id'] ?? null );
					},
					'links_to' => function( $event ) { return $event->post->get_link(); },
					'image_id' => function( $event ) { return $event->author->get_avatar_id(); },
				],
				'email' => [
					'enabled' => false,
					'subject' => '@author(:display_name) submitted a review on @post(:title)',
					'message' => <<<HTML
					A new review has been submitted on <strong>@post(:title)</strong>
					by <strong>@author(:display_name)</strong>.
					<a href="@post(:url)">Open</a>
					HTML,
				],
			],
		];
	}

	public function dynamic_tags(): array {
		return [
			'review' => [
				'type' => \Voxel\Dynamic_Tags\Review_Group::class,
				'props' => [
					'key' => 'review',
					'label' => 'Review',
					'review' => $this->review,
				],
			],
			'author' => [
				'type' => \Voxel\Dynamic_Tags\User_Group::class,
				'props' => [
					'key' => 'author',
					'label' => 'Review author',
					'user' => $this->review && $this->review->get_user() ? $this->review->get_user() : \Voxel\User::dummy(),
				],
			],
			'post' => [
				'type' => \Voxel\Dynamic_Tags\Post_Group::class,
				'props' => [
					'key' => 'post',
					'label' => 'Post',
					'post_type' => $this->post_type,
					'post' => $this->review ? $this->review->get_post() : \Voxel\Post::dummy( [ 'post_type' => $this->post_type->get_key() ] ),
				],
			],
		];
	}
}
