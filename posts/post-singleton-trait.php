<?php

namespace Voxel\Posts;

if ( ! defined('ABSPATH') ) {
	exit;
}

trait Post_Singleton_Trait {

	/**
	 * Store post instances.
	 *
	 * @since 1.0
	 */
	private static $instances = [];

	/**
	 * Get a post based on its key.
	 *
	 * @since 1.0
	 */
	public static function get( $post ) {
		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		if ( ! $post instanceof \WP_Post ) {
			return null;
		}

		if ( ! array_key_exists( $post->ID, self::$instances ) ) {
			self::$instances[ $post->ID ] = new self( $post );
		}

		return self::$instances[ $post->ID ];
	}

	/**
	 * Ignore cache and retrieve post information from db.
	 *
	 * @since 1.0
	 */
	public static function force_get( $post_id ) {
		clean_post_cache( $post_id );
		if ( isset( self::$instances[ $post_id ] ) ) {
			unset( self::$instances[ $post_id ] );
		}

		return self::get( $post_id );
	}

	public static function query( array $args ): array {
		$posts = get_posts( $args );
		return array_map( '\Voxel\Post::get', $posts );
	}

	public static function find( array $args ) {
		$args['posts_per_page'] = 1;
		$args['offset'] = null;
		$results = static::query( $args );
		return array_shift( $results );
	}
}
