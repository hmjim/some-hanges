<?php

namespace Voxel\Controllers\Frontend;

if ( ! defined('ABSPATH') ) {
	exit;
}

class User_Controller extends \Voxel\Controllers\Base_Controller {

	protected function hooks() {
		$this->on( 'voxel_ajax_user.follow_user', '@follow_user' );
		$this->on( 'voxel_ajax_user.follow_post', '@follow_post' );

		$this->on( 'voxel_ajax_user.collections.toggle_item', '@toggle_collection_item' );
		$this->on( 'voxel_ajax_user.collections.list', '@list_collections' );
		$this->on( 'voxel_ajax_user.collections.create', '@create_collection' );

		$this->on( 'voxel_ajax_user.posts.delete_post', '@delete_post' );
	}

	protected function follow_user() {
		try {
			$current_user = \Voxel\current_user();
			$user_id = ! empty( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : null;
			$user = \Voxel\User::get( $user_id );
			if ( ! $user ) {
				throw new \Exception( _x( 'User not found.', 'timeline', 'voxel' ) );
			}

			if ( $current_user->get_follow_status( 'user', $user->get_id() ) === \Voxel\FOLLOW_ACCEPTED ) {
				$current_user->set_follow_status( 'user', $user->get_id(), \Voxel\FOLLOW_NONE );
			} else {
				$current_user->set_follow_status( 'user', $user->get_id(), \Voxel\FOLLOW_ACCEPTED );
			}

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

	protected function follow_post() {
		try {
			$current_user = \Voxel\current_user();
			$post_id = ! empty( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : null;
			$post = \Voxel\Post::get( $post_id );
			if ( ! $post ) {
				throw new \Exception( _x( 'Post not found.', 'timeline', 'voxel' ) );
			}

			if ( $post->post_type->get_key() === 'profile' ) {
				$user = \Voxel\User::get_by_profile_id( $post->get_id() );
				if ( ! $user ) {
					throw new \Exception( _x( 'User not found.', 'timeline', 'voxel' ) );
				}

				if ( $current_user->get_follow_status( 'user', $user->get_id() ) === \Voxel\FOLLOW_ACCEPTED ) {
					$current_user->set_follow_status( 'user', $user->get_id(), \Voxel\FOLLOW_NONE );
				} else {
					$current_user->set_follow_status( 'user', $user->get_id(), \Voxel\FOLLOW_ACCEPTED );
				}
			} else {
				$current_status = $current_user->get_follow_status( 'post', $post->get_id() );
				if ( $current_status === \Voxel\FOLLOW_ACCEPTED ) {
					$current_user->set_follow_status( 'post', $post->get_id(), \Voxel\FOLLOW_NONE );
				} else {
					$current_user->set_follow_status( 'post', $post->get_id(), \Voxel\FOLLOW_ACCEPTED );
				}
			}

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

	protected function toggle_collection_item() {
		try {
			if ( ( $_SERVER['REQUEST_METHOD'] ?? null ) !== 'POST' ) {
				throw new \Exception( __( 'Invalid request.', 'voxel' ) );
			}

			$collection = \Voxel\Post::get( $_POST['collection_id'] ?? null );
			if ( ! (
				$collection
				&& $collection->post_type
				&& $collection->get_status() === 'publish'
				&& $collection->post_type->get_key() === 'collection'
				&& absint( $collection->get_author_id() ) === absint( get_current_user_id() )
			) ) {
				throw new \Exception( _x( 'Collection not found.', 'collections', 'voxel' ) );
			}

			$post = \Voxel\Post::get( $_POST['post_id'] ?? null );
			if ( ! ( $post && $post->post_type ) ) {
				throw new \Exception( _x( 'Post not found.', 'collections', 'voxel' ) );
			}

			$field = $collection->get_field('items');
			$allowed_post_types = (array) $field->get_prop('post_types');

			if ( ! in_array( $post->post_type->get_key(), $allowed_post_types, true ) ) {
				throw new \Exception( _x( 'This post cannot be added to this collection.', 'collections', 'voxel' ) );
			}

			global $wpdb;
			$toggle = ( $_POST['toggle'] ?? null ) === 'add' ? 'add' : 'remove';

			if ( $toggle === 'add' ) {
				$exists = !! $wpdb->get_var( <<<SQL
					SELECT id FROM {$wpdb->prefix}voxel_relations
					WHERE parent_id = {$collection->get_id()}
						AND child_id = {$post->get_id()}
						AND relation_key = 'items'
					LIMIT 1
				SQL );

				if ( ! $exists ) {
					$wpdb->query( <<<SQL
						INSERT INTO {$wpdb->prefix}voxel_relations (`parent_id`, `child_id`, `relation_key`, `order`)
						VALUES ({$collection->get_id()}, {$post->get_id()}, 'items', 0)
					SQL );
				}

				return wp_send_json( [
					'success' => true,
					'status' => 'added',
					'message' => sprintf( 'Saved to %s', $collection->get_title() ),
					'message' => \Voxel\replace_vars( _x( 'Saved to @collection', 'collections', 'voxel' ), [
						'@collection' => $collection->get_title(),
					] ),
					'actions' => [ [
						'label' => _x( 'View collection', 'collections', 'voxel' ),
						'link' => $collection->get_link(),
					] ],
				] );
			} else {
				$wpdb->query( <<<SQL
					DELETE FROM {$wpdb->prefix}voxel_relations
					WHERE parent_id = {$collection->get_id()}
						AND child_id = {$post->get_id()}
						AND relation_key = 'items'
				SQL );

				return wp_send_json( [
					'success' => true,
					'status' => 'removed',
				] );
			}
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function list_collections() {
		try {
			$post_id = absint( $_GET['post_id'] ?? null );
			if ( ! $post_id ) {
				throw new \Exception( _x( 'Could not retrieve collections.', 'collections', 'voxel' ) );
			}

			$page = absint( $_GET['pg'] ?? 1 );
			$per_page = 10;

			$user_id = absint( get_current_user_id() );
			$limit = $per_page + 1;
			$offset = ( $page - 1 ) * $per_page;

			global $wpdb;

			$results = $wpdb->get_results( <<<SQL
				SELECT posts.ID AS post_id, posts.post_title AS title, relations.id AS is_selected
					FROM {$wpdb->posts} AS posts
				LEFT JOIN {$wpdb->prefix}voxel_relations AS relations ON (
					relations.parent_id = posts.ID
					AND relations.child_id = {$post_id}
					AND relations.relation_key = 'items'
				)
				WHERE posts.post_type = 'collection'
					AND posts.post_author = {$user_id}
					AND posts.post_status = 'publish'
				ORDER BY posts.post_title ASC
				LIMIT {$limit} OFFSET {$offset}
			SQL );

			$has_more = count( $results ) > $per_page;
			if ( $has_more ) {
				array_pop( $results );
			}

			$list = [];
			foreach ( $results as $collection ) {
				$list[] = [
					'id' => absint( $collection->post_id ),
					'title' => $collection->title,
					'selected' => !! $collection->is_selected,
				];
			}

			return wp_send_json( [
				'success' => true,
				'has_more' => $has_more,
				'list' => $list,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function create_collection() {
		try {
			if ( ( $_SERVER['REQUEST_METHOD'] ?? null ) !== 'POST' ) {
				throw new \Exception( __( 'Invalid request.', 'voxel' ) );
			}

			$user = \Voxel\current_user();
			if ( ! $user->can_create_post( 'collection' ) ) {
				throw new \Exception( _x( 'You have reached the collection limit.', 'collections', 'voxel' ) );
			}

			$post_type = \Voxel\Post_Type::get( 'collection' );
			$field = $post_type->get_field('title');
			$field->set_prop( 'required', true );

			$title = $field->sanitize( $_POST['title'] ?? null );
			$field->check_validity( $title );

			$post_id = wp_insert_post( [
				'post_type' => 'collection',
				'post_title' => $title,
				'post_name' => sanitize_title( $title ),
				'post_status' => 'publish',
				'post_author' => $user->get_id(),
			] );

			if ( is_wp_error( $post_id ) ) {
				throw new \Exception( _x( 'Could not create collection.', 'collections', 'voxel' ) );
			}

			$collection = \Voxel\Post::get( $post_id );
			$collection->index();

			return wp_send_json( [
				'success' => true,
				'item' => [
					'id' => $post_id,
					'title' => $title,
					'selected' => false,
				],
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function delete_post() {
		try {
			\Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_delete_post' );
			$post = \Voxel\Post::get( $_GET['post_id'] ?? null );
			if ( ! ( $post && $post->is_deletable_by_current_user() ) ) {
				throw new \Exception( __( 'Permission denied.', 'voxel' ) );
			}

			wp_trash_post( $post->get_id() );

			return wp_send_json( [
				'success' => true,
				'redirect_to' => '(reload)',
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}
}