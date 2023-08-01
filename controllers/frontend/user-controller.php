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
		$this->on( 'voxel_ajax_user.posts.unpublish_post', '@unpublish_post' );
		$this->on( 'voxel_ajax_user.posts.republish_post', '@republish_post' );
		$this->on( 'voxel_ajax_user.posts.relist_post', '@relist_post' );
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

	protected function unpublish_post() {
		try {
			\Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_modify_post' );
			$post = \Voxel\Post::get( $_GET['post_id'] ?? null );
			$user = \Voxel\current_user();
			if ( ! ( $post && $post->is_editable_by_current_user() ) ) {
				throw new \Exception( __( 'Permission denied.', 'voxel' ) );
			}

			if ( ! ( $post->post_type && $post->post_type->is_managed_by_voxel() ) ) {
				throw new \Exception( __( 'Permission denied.', 'voxel' ) );
			}

			// excluded post types
			if ( in_array( $post->post_type->get_key(), [ 'profile' ], true ) ) {
				throw new \Exception( __( 'Permission denied.', 'voxel' ) );
			}

			if ( $post->get_status() !== 'publish' ) {
				throw new \Exception( __( 'Only published posts can be unpublished.', 'voxel' ) );
			}

			wp_update_post( [
				'ID' => $post->get_id(),
				'post_status' => 'unpublished',
			] );

			$post = \Voxel\Post::force_get( $post->get_id() );
			$post->should_index() ? $post->index() : $post->unindex();

			\Voxel\cache_user_post_stats( $user->get_id() );

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

	protected function republish_post() {
		try {
			\Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_modify_post' );
			$post = \Voxel\Post::get( $_GET['post_id'] ?? null );
			$user = \Voxel\current_user();
			if ( ! ( $post && $post->is_editable_by_current_user() ) ) {
				throw new \Exception( __( 'Permission denied.', 'voxel' ) );
			}

			if ( ! ( $post->post_type && $post->post_type->is_managed_by_voxel() ) ) {
				throw new \Exception( __( 'Permission denied.', 'voxel' ) );
			}

			// excluded post types
			if ( in_array( $post->post_type->get_key(), [ 'profile' ], true ) ) {
				throw new \Exception( __( 'Permission denied.', 'voxel' ) );
			}

			if ( $post->get_status() !== 'unpublished' ) {
				throw new \Exception( __( 'Only unpublished posts can be republished using this action.', 'voxel' ) );
			}

			if ( ! $user->can_create_post( $post->post_type->get_key() ) ) {
				throw new \Exception( __( 'You have reached your allowed submission limit.', 'publish post action', 'voxel' ) );
			}

			wp_update_post( [
				'ID' => $post->get_id(),
				'post_status' => 'publish',
			] );

			$post = \Voxel\Post::force_get( $post->get_id() );
			$post->should_index() ? $post->index() : $post->unindex();

			\Voxel\cache_user_post_stats( $user->get_id() );

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

	protected function relist_post() {
		try {
			\Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_relist_post' );
			$post = \Voxel\Post::get( $_GET['post_id'] ?? null );
			$user = \Voxel\current_user();
			if ( ! ( $post && $post->is_editable_by_current_user() ) ) {
				throw new \Exception( __( 'Permission denied.', 'voxel' ) );
			}

			if ( ! ( $post->post_type && $post->post_type->is_managed_by_voxel() ) ) {
				throw new \Exception( __( 'Permission denied.', 'voxel' ) );
			}

			// excluded post types
			if ( in_array( $post->post_type->get_key(), [ 'profile' ], true ) ) {
				throw new \Exception( __( 'Permission denied.', 'voxel' ) );
			}

			if ( $post->get_status() !== 'expired' ) {
				throw new \Exception( __( 'Only expired posts can be relisted using this action.', 'voxel' ) );
			}

			$membership = $user->get_membership();
			$plan = $membership->plan;
			$post_type = $post->post_type;
			$limit = $user->get_submission_limit_for_post_type( $post_type->get_key() );

			if ( ! $user->can_create_post( $post->post_type->get_key() ) ) {
				/**
				 * Edge case: If submission limit has been configured to count Expired posts, and the
				 * relist behavior is set to "same_slot", user should be able to relist an expire post
				 * even if their submission limit says e.g. 10 out of 10 posted.
				 *
				 * @since 1.2.5
				 */
				if ( $limit && $limit->get_relist_behavior() === 'same_slot' ) {
					$count = $limit->get_count();
					$submission_count = $limit->get_submission_count();
					$can_create_post = ( $submission_count - 1 ) < $count;
					if ( ! $can_create_post ) {
						throw new \Exception( __( 'You have reached your allowed submission limit.', 'publish post action', 'voxel' ) );
					}
				} else {
					throw new \Exception( __( 'You have reached your allowed submission limit.', 'publish post action', 'voxel' ) );
				}
			}

			wp_update_post( [
				'ID' => $post->get_id(),
				'post_status' => 'publish',
				'post_date' => current_time( 'mysql' ),
				'post_date_gmt' => current_time( 'mysql', true ),
			] );

			$post = \Voxel\Post::force_get( $post->get_id() );
			$post->should_index() ? $post->index() : $post->unindex();

			\Voxel\cache_user_post_stats( $user->get_id() );

			// update relist count for user
			if ( $limit && $limit->get_relist_behavior() === 'new_slot' ) {
				$relist_count = (array) json_decode( get_user_meta( $user->get_id(), 'voxel:relist_count', true ), true );
				if ( ! is_array( $relist_count[ $plan->get_key() ] ?? null ) ) {
					$relist_count[ $plan->get_key() ] = [];
				}

				$current_count = $relist_count[ $plan->get_key() ][ $post_type->get_key()] ?? null;
				if ( ! is_numeric( $current_count ) || $current_count < 1 ) {
					$relist_count[ $plan->get_key() ][ $post_type->get_key()] = 0;
				}

				$relist_count[ $plan->get_key() ][ $post_type->get_key()]++;
				update_user_meta( $user->get_id(), 'voxel:relist_count', wp_slash( wp_json_encode( $relist_count ) ) );
			}

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
