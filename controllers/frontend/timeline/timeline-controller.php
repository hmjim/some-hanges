<?php

namespace Voxel\Controllers\Frontend\Timeline;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Timeline_Controller extends \Voxel\Controllers\Base_Controller {

	protected function hooks() {
		$this->on( 'voxel_ajax_timeline.get', '@get_timeline' );
		$this->on( 'voxel_ajax_nopriv_timeline.get', '@get_timeline' );
		$this->on( 'voxel_ajax_timeline.get_status', '@get_status' );
		$this->on( 'voxel_ajax_nopriv_timeline.get_status', '@get_status' );
	}

	protected function get_timeline() {
		try {
			$page = absint( $_GET['page'] ?? 1 );
			$per_page = 10;
			$author_id = ! empty( $_GET['author_id'] ) ? absint( $_GET['author_id'] ) : null;
			$post_id = ! empty( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : null;

			$author = \Voxel\User::get( $author_id );
			$post = \Voxel\Post::get( $post_id );
			$current_user = \Voxel\current_user();

			$args = [
				'limit' => $per_page + 1,
				'with_like_count' => true,
				'with_reply_count' => true,
				'with_user_like_status' => true,
			];

			if ( $page > 1 ) {
				$args['offset'] = ( $page - 1 ) * $per_page;
			}

			$empty = function() {
				return wp_send_json( [
					'success' => true,
					'data' => [],
					'has_more' => false,
				] );
			};

			$allowed_modes = [
				'post_reviews' => true,
				'post_wall' => true,
				'post_timeline' => true,
				'author_timeline' => true,
				'user_feed' => true,
			];

			$mode = $_GET['mode'] ?? null;
			if ( $mode === null || ! isset( $allowed_modes[ $mode ] ) ) {
				throw new \Exception( _x( 'Could not load timeline.', 'timeline', 'voxel' ) );
			}

			if ( $mode === 'post_reviews' ) {
				if ( ! $post ) {
					throw new \Exception( _x( 'Could not retrieve reviews for post.', 'timeline', 'voxel' ) );
				}

				$args['match'] = 'reviews';
				$args['post_id'] = $post->get_id();
			} elseif ( $mode === 'post_wall' ) {
				if ( ! $post ) {
					throw new \Exception( _x( 'Could not retrieve timeline items for post.', 'timeline', 'voxel' ) );
				}

				$args['match'] = 'statuses';
				$args['post_id'] = $post->get_id();
				$args['published_as'] = -$post->get_id();
			} elseif ( $mode === 'post_timeline' ) {
				if ( ! $post ) {
					throw new \Exception( _x( 'Could not retrieve timeline for post.', 'timeline', 'voxel' ) );
				}

				$args['match'] = 'statuses';
				$args['post_id'] = $post->get_id();
				$args['published_as'] = $post->get_id();
			} elseif ( $mode === 'author_timeline' ) {
				if ( ! $author ) {
					throw new \Exception( _x( 'Could not retrieve timeline for user.', 'timeline', 'voxel' ) );
				}

				$args['user_id'] = $author->get_id();
			} elseif ( $mode === 'user_feed' ) {
				if ( ! $current_user ) {
					return $empty();
				}

				$args['follower_type'] = 'user';
				$args['follower_id'] = $current_user->get_id();
			}

			if ( in_array( $mode, [ 'post_reviews', 'post_wall', 'post_timeline' ], true ) ) {
				$visibility_key = ( $mode === 'post_wall' ? 'wall_visibility' : ( $mode === 'post_reviews' ? 'review_visibility' : 'visibility' ) );
				$visibility = $post->post_type->get_setting( 'timeline.'.$visibility_key );
				if ( $visibility === 'logged_in' && ! is_user_logged_in() ) {
					return $empty();
				} elseif ( $visibility === 'followers_only' && ! ( is_user_logged_in() && \Voxel\current_user()->follows_post( $post->get_id() ) ) ) {
					return $empty();
				} elseif ( $visibility === 'private' && ! $post->is_editable_by_current_user() ) {
					return $empty();
				}
			}

			$allowed_orders = [
				'latest' => true,
				'earliest' => true,
				'most_liked' => true,
				'most_discussed' => true,
				'most_popular' => true,
				'best_rated' => true,
				'worst_rated' => true,
			];
			$order = $_GET['order_type'] ?? null;
			if ( $order === null || ! isset( $allowed_orders[ $order ] ) ) {
				throw new \Exception( _x( 'Could not load timeline.', 'timeline', 'voxel' ) );
			}

			if ( $order === 'latest' ) {
				$args['order_by'] = 'created_at';
				$args['order'] = 'desc';
			} elseif ( $order === 'earliest' ) {
				$args['order_by'] = 'created_at';
				$args['order'] = 'asc';
			} elseif ( $order === 'most_liked' ) {
				$args['order_by'] = 'like_count';
				$args['order'] = 'desc';
			} elseif ( $order === 'most_discussed' ) {
				$args['order_by'] = 'reply_count';
				$args['order'] = 'desc';
			} elseif ( $order === 'most_popular' ) {
				$args['order_by'] = 'interaction_count';
				$args['order'] = 'desc';
			} elseif ( $order === 'best_rated' ) {
				$args['order_by'] = 'rating';
				$args['order'] = 'desc';
			} elseif ( $order === 'worst_rated' ) {
				$args['order_by'] = 'rating';
				$args['order'] = 'asc';
			}

			$allowed_times = [
				'today' => true,
				'this_week' => true,
				'this_month' => true,
				'this_year' => true,
				'all_time' => true,
				'custom' => true,
			];
			$time = $_GET['order_time'] ?? null;
			if ( $time === null || ! isset( $allowed_times[ $time ] ) ) {
				throw new \Exception( _x( 'Could not load timeline.', 'timeline', 'voxel' ) );
			}

			if ( $time === 'today' ) {
				$args['created_at'] = \Voxel\utc()->modify( '-24 hours' )->format( 'Y-m-d H:i:s' );
			} elseif ( $time === 'this_week' ) {
				$args['created_at'] = \Voxel\utc()->modify( 'first day of this week' )->format( 'Y-m-d 00:00:00' );
			} elseif ( $time === 'this_month' ) {
				$args['created_at'] = \Voxel\utc()->modify( 'first day of this month' )->format( 'Y-m-d 00:00:00' );
			} elseif ( $time === 'this_month' ) {
				$args['created_at'] = \Voxel\utc()->modify( 'first day of this year' )->format( 'Y-m-d 00:00:00' );
			} elseif ( $time === 'all_time' ) {
				//
			} elseif ( $time === 'custom' ) {
				$custom_time = absint( $_GET['order_time_custom'] ?? null );
				if ( $custom_time ) {
					$args['created_at'] = \Voxel\utc()->modify( sprintf( '-%d days', $custom_time ) )->format( 'Y-m-d 00:00:00' );
				}
			}

			$statuses = \Voxel\Timeline\Status::query( $args );
			$has_more = count( $statuses ) > $per_page;
			if ( $has_more ) {
				array_pop( $statuses );
			}

			$data = array_map( '\Voxel\Timeline\prepare_status_json', $statuses );

			if ( \Voxel\is_dev_mode() ) {
				$dev = [
					'args' => $args,
					'query' => \Voxel\Timeline\Status::_generate_search_query( $args ),
				];
			}

			return wp_send_json( [
				'success' => true,
				'data' => $data,
				'has_more' => $has_more,
				'dev' => $dev ?? null,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function get_status() {
		try {
			$status_id = ! empty( $_GET['status_id'] ) ? absint( $_GET['status_id'] ) : null;
			$status = \Voxel\Timeline\Status::get( $status_id );
			if ( ! ( $status && $status->is_viewable_by_current_user() ) ) {
				throw new \Exception( _x( 'Post not found.', 'timeline', 'voxel' ) );
			}

			$response = \Voxel\Timeline\prepare_status_json( $status );
			$response['replies']['visible'] = true;

			// highlight requested reply
			$reply_id = ! empty( $_GET['reply_id'] ) ? absint( $_GET['reply_id'] ) : null;
			if ( $reply_id !== null ) {
				$reply = \Voxel\Timeline\Reply::get( $reply_id );
				if ( $reply && $reply->get_status_id() === $status->get_id() ) {
					$response['replies']['visible'] = false;
					$reply_config = \Voxel\Timeline\prepare_reply_json( $reply );
					$reply_config['highlighted'] = true;

					if ( $parent = $reply->get_parent() ) {
						$parent_config = \Voxel\Timeline\prepare_reply_json( $parent );
						$parent_config['replies']['requested'] = true;
						$parent_config['replies']['visible'] = true;
						$parent_config['replies']['hasMore'] = false;
						$parent_config['replies']['list'] = [ $reply_config ];
					}

					$response['highlightedReplies'] = [
						'requested' => true,
						'visible' => true,
						'page' => 1,
						'loading' => false,
						'hasMore' => false,
						'list' => [ $parent_config ?? $reply_config ],
					];
				}
			}

			return wp_send_json( [
				'success' => true,
				'status' => $response,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}
}
