<?php

namespace Voxel\Timeline;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Status {
	private static $instances = [];

	private
		$id,
		$user_id,
		$post_id,
		$published_as,
		$content,
		$details,
		$created_at,
		$edited_at,
		$is_review,
		$review_score,
		$review_details,
		$like_count,
		$reply_count,
		$liked_by_user;

	public function __construct( array $data ) {
		$this->id = absint( $data['id'] );
		$this->user_id = absint( $data['user_id'] );
		$this->post_id = absint( $data['post_id'] ) ?: null;
		$this->published_as = absint( $data['published_as'] ) ?: null;
		$this->content = $data['content'] ?? null;
		$this->details = is_string( $data['details'] ) ? json_decode( $data['details'], ARRAY_A ) : $data['details'];
		$this->created_at = date( 'Y-m-d H:i:s', strtotime( $data['created_at'] ) );
		$this->edited_at = $data['edited_at'] ? date( 'Y-m-d H:i:s', strtotime( $data['edited_at'] ) ) : null;
		$this->is_review = !! ( $data['is_review'] ?? null );
		$this->review_score = isset( $data['review_score'] ) ? round( floatval( $data['review_score'] ), 2 ) : null;
		$this->like_count = absint( $data['like_count'] ?? null ) ?: null;
		$this->reply_count = absint( $data['reply_count'] ?? null ) ?: null;
		$this->liked_by_user = absint( $data['liked_by_user'] ?? null ) ?: false;
	}

	public function get_id() {
		return $this->id;
	}

	public function get_unique_key() {
		return substr( md5( wp_json_encode( [ $this->id, $this->created_at, $this->edited_at ] ) ), 0, 8 );
	}

	public function get_link() {
		return add_query_arg(
			'status_id',
			$this->get_id(),
			get_permalink( \Voxel\get( 'templates.timeline' ) )
		);
	}

	public function get_user_id() {
		return $this->user_id;
	}

	public function get_user() {
		return \Voxel\User::get( $this->get_user_id() );
	}

	public function get_post_id() {
		return $this->post_id;
	}

	public function get_post() {
		return \Voxel\Post::get( $this->get_post_id() );
	}

	public function get_published_as() {
		return $this->published_as;
	}

	public function get_post_published_as() {
		return \Voxel\Post::get( $this->get_published_as() );
	}

	public function get_author() {
		$user = $this->get_user();
		$published_as = $this->get_post_published_as();
		return $user ? $user : ( $published_as ? $published_as->get_author() : null );
	}

	public function get_content() {
		return $this->content;
	}

	public function get_content_for_display() {
		$content = $this->get_content();
		$content = links_add_target( make_clickable( $content ) );
		$content = wpautop( $content );
		return $content;
	}

	public function get_details() {
		return (array) $this->details;
	}

	public function get_created_at() {
		return $this->created_at;
	}

	public function get_time_for_display() {
		$from = strtotime( $this->created_at ) + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
		$to = current_time( 'timestamp' );
		$diff = (int) abs( $to - $from );
		if ( $diff < WEEK_IN_SECONDS ) {
			return sprintf( _x( '%s ago', 'status published at', 'voxel' ), human_time_diff( $from, $to ) );
		}

		return \Voxel\datetime_format( $from );
	}

	public function get_edit_time_for_display() {
		if ( ! ( $edited_at = strtotime( $this->edited_at ?? '' ) ) ) {
			return null;
		}

		return \Voxel\datetime_format(
			$edited_at + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS )
		);
	}

	public function is_review() {
		return $this->is_review;
	}

	public function get_review_score() {
		return $this->review_score;
	}

	public function get_like_count() {
		return $this->like_count;
	}

	public function get_reply_count() {
		return $this->reply_count;
	}

	public function liked_by_user() {
		return !! $this->liked_by_user;
	}

	public function like( $user_id = null ) {
		if ( $user_id === null ) {
			$user_id = get_current_user_id();
		}

		if ( $user_id ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare(
				"INSERT INTO {$wpdb->prefix}voxel_timeline_likes (`user_id`, `status_id`) VALUES (%d, %d)",
				$user_id,
				$this->get_id()
			) );
		}
	}

	public function unlike( $user_id = null ) {
		if ( $user_id === null ) {
			$user_id = get_current_user_id();
		}

		if ( $user_id ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}voxel_timeline_likes WHERE `user_id` = %d AND `status_id` = %d",
				$user_id,
				$this->get_id()
			) );
		}
	}

	public function is_editable_by_current_user(): bool {
		$published_as = $this->get_post_published_as();
		if ( $published_as !== null ) {
			return $published_as->is_editable_by_current_user();
		}

		return absint( $this->get_user_id() ) === absint( get_current_user_id() );
	}

	public function is_moderatable_by_current_user(): bool {
		if ( current_user_can('administrator') ) {
			return true;
		}

		// allow post authors to moderate non-review posts (wall posts)
		$post = $this->get_post();
		if ( $post && $post->is_editable_by_current_user() && ! $this->is_review() ) {
			return true;
		}

		return false;
	}

	public function is_viewable_by_current_user(): bool {
		$post = $this->get_post();
		if ( $post ) {
			$published_as = $this->get_post_published_as();
			if ( $published_as && $published_as->get_id() === $post->get_id() ) {
				$visibility_key = 'visibility';
			} elseif ( $this->is_review() ) {
				$visibility_key = 'review_visibility';
			} else {
				$visibility_key = 'wall_visibility';
			}

			$visibility = $post->post_type->get_setting( 'timeline.'.$visibility_key );
			if ( $visibility === 'logged_in' && ! is_user_logged_in() ) {
				return false;
			} elseif ( $visibility === 'followers_only' && ! ( is_user_logged_in() && \Voxel\current_user()->follows_post( $post->get_id() ) ) ) {
				return false;
			} elseif ( $visibility === 'private' && ! $post->is_editable_by_current_user() ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get a status based on its id.
	 *
	 * @since 1.0
	 */
	public static function get( $id ) {
		if ( is_array( $id ) ) {
			$data = $id;
			$id = $data['id'];
			if ( ! array_key_exists( $id, static::$instances ) ) {
				static::$instances[ $id ] = new static( $data );
			}
		} elseif ( is_numeric( $id ) ) {
			if ( ! array_key_exists( $id, static::$instances ) ) {
				$results = static::query( [
					'id' => $id,
					'limit' => 1,
					'with_like_count' => true,
					'with_reply_count' => true,
					'with_user_like_status' => true,
				] );
				static::$instances[ $id ] = isset( $results[0] ) ? $results[0] : null;
			}
		} elseif ( $id === null ) {
			return null;
		}

		return static::$instances[ $id ];
	}

	public static function force_get( $id ) {
		unset( static::$instances[ $id ] );
		return static::get( $id );
	}

	public function update( $data_or_key, $value = null ) {
		global $wpdb;

		if ( is_array( $data_or_key ) ) {
			$data = $data_or_key;
		} else {
			$data = [];
			$data[ $data_or_key ] = $value;
		}

		$data['id'] = $this->get_id();
		$wpdb->query( static::_generate_insert_query( $data ) );
		$this->_maybe_update_stats_cache();
	}

	public function delete() {
		global $wpdb;
		$wpdb->query( $wpdb->prepare(
			"DELETE FROM {$wpdb->prefix}voxel_timeline WHERE id = %d",
			$this->get_id()
		) );

		$this->_maybe_update_stats_cache();
	}

	public static function create( array $data ): \Voxel\Timeline\Status {
		global $wpdb;
		$data = array_merge( [
			'id' => null,
			'user_id' => null,
			'post_id' => null,
			'published_as' => null,
			'content' => null,
			'details' => null,
			'review_score' => null,
			'created_at' => \Voxel\utc()->format( 'Y-m-d H:i:s' ),
			'edited_at' => null,
		], $data );

		$sql = static::_generate_insert_query( $data );
		$wpdb->query( $sql );
		$data['id'] = $wpdb->insert_id;
		$data['is_review'] = $data['review_score'] !== null;

		$status = new \Voxel\Timeline\Status( $data );
		$status->_maybe_update_stats_cache();

		return $status;
	}

	public static function _generate_insert_query( array $data ) {
		global $wpdb;

		$escaped_data = [];
		foreach ( ['id', 'user_id', 'post_id', 'published_as'] as $column_name ) {
			if ( isset( $data[ $column_name ] ) ) {
				$escaped_data[ $column_name ] = absint( $data[ $column_name ] );
			}
		}

		if ( isset( $data['details'] ) && is_array( $data['details'] ) ) {
			$data['details'] = wp_json_encode( $data['details'] );
		}

		if ( isset( $data['review_score'] ) ) {
			$escaped_data['review_score'] = round( floatval( $data['review_score'] ), 2 );
		}

		foreach ( ['content', 'details', 'created_at', 'edited_at'] as $column_name ) {
			if ( isset( $data[ $column_name ] ) ) {
				$escaped_data[ $column_name ] = sprintf( '\'%s\'', esc_sql( $data[ $column_name ] ) );
			}
		}

		$columns = join( ', ', array_map( function( $column_name ) {
			return sprintf( '`%s`', esc_sql( $column_name ) );
		}, array_keys( $escaped_data ) ) );

		$values = join( ', ', $escaped_data );

		$on_duplicate = join( ', ', array_map( function( $column_name ) {
			return sprintf( '`%s`=VALUES(`%s`)', $column_name, $column_name );
		}, array_keys( $escaped_data ) ) );

		$sql = "INSERT INTO {$wpdb->prefix}voxel_timeline ($columns) VALUES ($values)
					ON DUPLICATE KEY UPDATE $on_duplicate";

		return $sql;
	}

	public static function query( array $args ): array {
		global $wpdb;
		$sql = static::_generate_search_query( $args );

		// dump_sql( $sql );die;
		$results = $wpdb->get_results( $sql, ARRAY_A );
		if ( ! is_array( $results ) ) {
			return [];
		}

		return array_map( '\Voxel\Timeline\Status::get', $results );
	}

	public static function _generate_search_query( array $args ) {
		global $wpdb;
		$args = array_merge( [
			'id' => null,
			'user_id' => null, // a negative id can be provided to exclude statuses from that user
			'post_id' => null,
			'published_as' => null,
			'match' => 'all', // all|reviews|statuses
			'order_by' => 'created_at',
			'order' => 'desc',
			'offset' => null,
			'limit' => 10,
			'with_like_count' => false,
			'with_reply_count' => false,
			'with_user_like_status' => false,
			'follower_type' => null,
			'follower_id' => null,
			'created_at' => null,
		], $args );

		$join_clauses = [];
		$where_clauses = [];
		$orderby_clauses = [];
		$select_clauses = [
			'statuses.*',
			'IF(statuses.review_score IS NOT NULL, 1, NULL) AS is_review',
		];

		if ( ! is_null( $args['id'] ) ) {
			$where_clauses[] = sprintf( 'statuses.id = %d', absint( $args['id'] ) );
		}

		if ( ! is_null( $args['user_id'] ) ) {
			if ( $args['user_id'] < 0 ) {
				$where_clauses[] = sprintf( 'NOT(statuses.user_id <=> %d)', absint( $args['user_id'] ) );
			} else {
				$where_clauses[] = sprintf( 'statuses.user_id = %d', absint( $args['user_id'] ) );
			}
		}

		if ( ! is_null( $args['post_id'] ) ) {
			if ( $args['post_id'] < 0 ) {
				$where_clauses[] = sprintf( 'NOT(statuses.post_id <=> %d)', absint( $args['post_id'] ) );
			} else {
				$where_clauses[] = sprintf( 'statuses.post_id = %d', absint( $args['post_id'] ) );
			}
		}

		if ( ! is_null( $args['published_as'] ) ) {
			if ( $args['published_as'] < 0 ) {
				$where_clauses[] = sprintf( 'NOT(statuses.published_as <=> %d)', absint( $args['published_as'] ) );
			} else {
				$where_clauses[] = sprintf( 'statuses.published_as = %d', absint( $args['published_as'] ) );
			}
		}

		if ( $args['match'] === 'reviews' ) {
			$where_clauses[] = 'statuses.review_score IS NOT NULL';
		}

		if ( $args['match'] === 'statuses' ) {
			$where_clauses[] = 'statuses.review_score IS NULL';
		}

		if ( $args['with_like_count'] ) {
			$join_clauses[] = "LEFT JOIN {$wpdb->prefix}voxel_timeline_likes AS likes ON likes.status_id = statuses.id";
			$select_clauses[] = "COUNT(DISTINCT likes.user_id) AS like_count";
		}

		if ( $args['with_reply_count'] ) {
			$join_clauses[] = "LEFT JOIN {$wpdb->prefix}voxel_timeline_replies AS replies ON replies.status_id = statuses.id";
			$select_clauses[] = "COUNT(DISTINCT replies.id) AS reply_count";
		}

		if ( $args['with_user_like_status'] ) {
			$user_to_check = is_numeric( $args['with_user_like_status'] ) ? absint( $args['with_user_like_status'] ) : get_current_user_id();
			if ( $user_to_check >= 1 ) {
				$join_clauses[] = $wpdb->prepare(
					"LEFT JOIN {$wpdb->prefix}voxel_timeline_likes AS user_like
						ON ( user_like.user_id = %d AND user_like.status_id = statuses.id )",
					$user_to_check
				);
				$select_clauses[] = "user_like.user_id AS liked_by_user";
			}
		}

		if ( ! is_null( $args['follower_type'] ) && ! is_null( $args['follower_id'] ) ) {
			$follower_type = $args['follower_type'] === 'post' ? 'post' : 'user';
			$follower_id = absint( $args['follower_id'] );
			$join_clauses[] = $wpdb->prepare( "LEFT JOIN {$wpdb->prefix}voxel_followers AS followers ON ( followers.follower_type = '%s' AND followers.follower_id = %d )", $follower_type, $follower_id );
			$where_clauses[] = $wpdb->prepare(
				"( ( followers.object_type = 'user' AND followers.object_id = statuses.user_id ) OR ( followers.object_type = 'post' AND followers.object_id = statuses.post_id ) OR statuses.user_id = %d )",
				$follower_id
			);
		}

		if ( ! is_null( $args['order_by'] ) ) {
			$order = $args['order'] === 'asc' ? 'ASC' : 'DESC';

			if ( $args['order_by'] === 'created_at' ) {
				$orderby_clauses[] = "statuses.created_at {$order}";
			} elseif ( $args['order_by'] === 'like_count' && $args['with_like_count'] ) {
				$orderby_clauses[] = "like_count {$order}";
			} elseif ( $args['order_by'] === 'reply_count' && $args['with_reply_count'] ) {
				$orderby_clauses[] = "reply_count {$order}";
			} elseif ( $args['order_by'] === 'interaction_count' && $args['with_like_count'] && $args['with_reply_count'] ) {
				$select_clauses[] = "(COUNT(DISTINCT likes.user_id) + COUNT(DISTINCT replies.id)) AS interaction_count";
				$orderby_clauses[] = "interaction_count {$order}";
			} elseif ( $args['order_by'] === 'rating' ) {
				$orderby_clauses[] = "statuses.review_score {$order}";
			}
		}

		if ( ! is_null( $args['created_at'] ) ) {
			$timestamp = strtotime( $args['created_at'] );
			if ( $timestamp ) {
				$where_clauses[] = $wpdb->prepare( "statuses.created_at >= %s", date( 'Y-m-d H:i:s', $timestamp ) );
			}
		}

		// generate sql string
		$joins = join( " \n ", $join_clauses );
		$wheres = '';
		if ( ! empty( $where_clauses ) ) {
			$wheres = sprintf( 'WHERE %s', join( ' AND ', $where_clauses ) );
		}

		$orderbys = '';
		if ( ! empty( $orderby_clauses ) ) {
			$orderbys = sprintf( 'ORDER BY %s', join( ", ", $orderby_clauses ) );
		}

		$limit = '';
		if ( ! is_null( $args['limit'] ) ) {
			$limit = sprintf( 'LIMIT %d', absint( $args['limit'] ) );
		}

		$offset = '';
		if ( ! is_null( $args['offset'] ) ) {
			$offset = sprintf( 'OFFSET %d', absint( $args['offset'] ) );
		}

		$selects = join( ', ', $select_clauses );
		return <<<SQL
			SELECT {$selects} FROM {$wpdb->prefix}voxel_timeline AS statuses
			{$joins} {$wheres}
			GROUP BY statuses.id
			{$orderbys}
			{$limit} {$offset}
		SQL;
	}

	private function _maybe_update_stats_cache() {
		if ( $post_id = $this->get_post_id() ) {
			if ( $this->is_review() ) {
				\Voxel\cache_post_review_stats( $post_id );
			} elseif ( $this->get_published_as() === $post_id ) {
				\Voxel\cache_post_timeline_stats( $post_id );
			} else {
				\Voxel\cache_post_wall_stats( $post_id );
			}
		}
	}
}
