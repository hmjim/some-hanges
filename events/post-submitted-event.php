<?php

namespace Voxel\Events;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Post_Submitted_Event extends Base_Event {

	public $post_type;

	public $post, $author;

	public function __construct( \Voxel\Post_Type $post_type ) {
		$this->post_type = $post_type;
	}

	public function prepare( $post_id ) {
		$post = \Voxel\Post::force_get( $post_id );
		if ( ! ( $post && $post->get_author() ) ) {
			throw new \Exception( 'Missing information.' );
		}

		$this->post = $post;
		$this->author = $post->get_author();
	}

	public function get_key(): string {
		return sprintf( 'post-types/%s/post:submitted', $this->post_type->get_key() );
	}

	public function get_label(): string {
		return sprintf( '%s: New post submitted', $this->post_type->get_label() );
	}

	public function get_category() {
		return sprintf( 'post-type:%s', $this->post_type->get_key() );
	}

	public static function notifications(): array {
		return [
			'post_author' => [
				'label' => 'Notify post author',
				'recipient' => function( $event ) {
					return $event->author;
				},
				'inapp' => [
					'enabled' => false,
					'subject' => 'Your post has been submitted successfully.',
					'details' => function( $event ) {
						return [
							'post_id' => $event->post->get_id(),
						];
					},
					'apply_details' => function( $event, $details ) {
						$event->prepare( $details['post_id'] ?? null );
					},
					'links_to' => function( $event ) { return $event->post->get_link(); },
					'image_id' => function( $event ) { return $event->post->get_logo_id(); },
				],
				'email' => [
					'enabled' => false,
					'subject' => 'Your post has been submitted successfully.',
					'message' => <<<HTML
					Your post <strong>@post(:title)</strong> has been submitted successfully.
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
					'subject' => 'A new post has been submitted by @author(:display_name)',
					'details' => function( $event ) {
						return [
							'post_id' => $event->post->get_id(),
						];
					},
					'apply_details' => function( $event, $details ) {
						$event->prepare( $details['post_id'] ?? null );
					},
					'links_to' => function( $event ) { return $event->post->get_link(); },
					'image_id' => function( $event ) { return $event->author->get_avatar_id(); },
				],
				'email' => [
					'enabled' => false,
					'subject' => 'A new post has been submitted by @author(:display_name)',
					'message' => <<<HTML
					A new post has been submitted by <strong>@author(:display_name)</strong>:
					<strong>@post(:title)</strong>.
					<a href="@post(:url)">Open</a>
					HTML,
				],
			],
		];
	}

	public function dynamic_tags(): array {
		return [
			'author' => [
				'type' => \Voxel\Dynamic_Tags\User_Group::class,
				'props' => [
					'key' => 'author',
					'label' => 'Author',
					'user' => $this->author,
				],
			],
			'post' => [
				'type' => \Voxel\Dynamic_Tags\Post_Group::class,
				'props' => [
					'key' => 'post',
					'label' => 'Post',
					'post_type' => $this->post_type,
					'post' => $this->post ?: \Voxel\Post::dummy( [ 'post_type' => $this->post_type->get_key() ] ),
				],
			],
		];
	}
}
