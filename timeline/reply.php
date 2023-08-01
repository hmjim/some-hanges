<?php

namespace Voxel\Timeline;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Reply {

	private static $instances = [];

	private
		$id,
		$user_id,
		$status_id,
		$parent_id,
		$content,
		$details,
		$created_at,
		$edited_at,
		$like_count,
		$reply_count,
		$liked_by_user;

	public function __construct( array $data ) {
		$this->id = absint( $data['id'] );
		$this->user_id = absint( $data['user_id'] );
		$this->status_id = absint( $data['status_id'] );
		$this->parent_id = absint( $data['parent_id'] ) ?: null;
		$this->content = $data['content'] ?? null;
		$this->details = is_string( $data['details'] ) ? json_decode( $data['details'], ARRAY_A ) : $data['details'];
		$this->created_at = date( 'Y-m-d H:i:s', strtotime( $data['created_at'] ) );
		$this->edited_at = $data['edited_at'] ? date( 'Y-m-d H:i:s', strtotime( $data['edited_at'] ) ) : null;
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
		return add_query_arg( [
			'status_id' => $this->get_status_id(),
			'reply_id' => $this->get_id(),
		], get_permalink( \Voxel\get( 'templates.timeline' ) ) );
	}

	public function get_user_id() {
		return $this->user_id;
	}

	public function get_user() {
		return \Voxel\User::get( $this->get_user_id() );
	}

	public function get_status_id() {
		return $this->status_id;
	}

	public function get_status() {
		return \Voxel\Timeline\Status::get( $this->get_status_id() );
	}

	public function get_parent_id() {
		return $this->parent_id;
	}

	public function get_parent() {
		if ( $this->get_parent_id() ) {
			return \Voxel\Timeline\Reply::get( $this->get_parent_id() );
		}
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
		if ( ! ( $edited_at = strtotime( $this->edited_at ) ) ) {
			return null;
		}

		return \Voxel\datetime_format(
			$edited_at + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS )
		);
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
				"INSERT INTO {$wpdb->prefix}voxel_timeline_reply_likes (`user_id`, `reply_id`) VALUES (%d, %d)",
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
				"DELETE FROM {$wpdb->prefix}voxel_timeline_reply_likes WHERE `user_id` = %d AND `reply_id` = %d",
				$user_id,
				$this->get_id()
			) );
		}
	}

	public function is_editable_by_current_user(): bool {
		return absint( $this->get_user_id() ) === absint( get_current_user_id() );
	}

	public function is_moderatable_by_current_user(): bool {
		if ( current_user_can('administrator') ) {
			return true;
		}

		return false;
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
			"DELETE FROM {$wpdb->prefix}voxel_timeline_replies WHERE id = %d",
			$this->get_id()
		) );
		$this->_maybe_update_stats_cache();
	}

	public static function create( array $data ): \Voxel\Timeline\Reply {
		global $wpdb;
		$data = array_merge( [
			'id' => null,
			'user_id' => null,
			'status_id' => null,
			'parent_id' => null,
			'content' => null,
			'details' => null,
			'created_at' => \Voxel\utc()->format( 'Y-m-d H:i:s' ),
			'edited_at' => null,
		], $data );

		if ( $data['user_id'] === null || $data['status_id'] === null ) {
			throw new \Exception( _x( 'Couldn\'t create reply: missing information.', 'timeline', 'voxel' ) );
		}

		$sql = static::_generate_insert_query( $data );
		$wpdb->query( $sql );
		$data['id'] = $wpdb->insert_id;
		$reply = new \Voxel\Timeline\Reply( $data );
		$reply->_maybe_update_stats_cache();

		return $reply;
	}

	public static function _generate_insert_query( array $data ) {
		global $wpdb;

		$escaped_data = [];
		foreach ( ['id', 'user_id', 'status_id', 'parent_id'] as $column_name ) {
			if ( isset( $data[ $column_name ] ) ) {
				$escaped_data[ $column_name ] = absint( $data[ $column_name ] );
			}
		}

		if ( isset( $data['details'] ) && is_array( $data['details'] ) ) {
			$data['details'] = wp_json_encode( $data['details'] );
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

		$sql = "INSERT INTO {$wpdb->prefix}voxel_timeline_replies ($columns) VALUES ($values)
					ON DUPLICATE KEY UPDATE $on_duplicate";

		return $sql;
	}

	public static function query( array $args ): array {
		global $wpdb;
		$args = array_merge( [
			'id' => null,
			'user_id' => null,
			'status_id' => null,
			'parent_id' => null,
			'order' => 'earliest', // earliest|latest|popular
			'offset' => null,
			'limit' => 10,
			'with_like_count' => false,
			'with_reply_count' => false,
			'with_user_like_status' => false,
		], $args );

		$join_clauses = [];
		$where_clauses = [];
		$select_clauses = [
			'replies.*',
		];

		if ( ! is_null( $args['id'] ) ) {
			$where_clauses[] = sprintf( 'replies.id = %d', absint( $args['id'] ) );
		}

		if ( ! is_null( $args['user_id'] ) ) {
			$where_clauses[] = sprintf( 'replies.user_id = %d', absint( $args['user_id'] ) );
		}

		if ( ! is_null( $args['status_id'] ) ) {
			$where_clauses[] = sprintf( 'replies.status_id = %d', absint( $args['status_id'] ) );
		}

		if ( ! is_null( $args['parent_id'] ) ) {
			if ( $args['parent_id'] === 0 ) {
				$where_clauses[] = 'replies.parent_id IS NULL';
			} else {
				$where_clauses[] = sprintf( 'replies.parent_id = %d', absint( $args['parent_id'] ) );
			}
		}

		if ( $args['with_like_count'] ) {
			$join_clauses[] = "LEFT JOIN {$wpdb->prefix}voxel_timeline_reply_likes AS likes ON likes.reply_id = replies.id";
			$select_clauses[] = "COUNT(DISTINCT likes.user_id) AS like_count";
		}

		if ( $args['with_reply_count'] ) {
			$join_clauses[] = "LEFT JOIN {$wpdb->prefix}voxel_timeline_replies AS subreplies ON subreplies.parent_id = replies.id";
			$select_clauses[] = "COUNT(DISTINCT subreplies.id) AS reply_count";
		}

		if ( $args['with_user_like_status'] ) {
			$user_to_check = is_numeric( $args['with_user_like_status'] ) ? absint( $args['with_user_like_status'] ) : get_current_user_id();
			if ( $user_to_check >= 1 ) {
				$join_clauses[] = $wpdb->prepare(
					"LEFT JOIN {$wpdb->prefix}voxel_timeline_reply_likes AS user_like
						ON ( user_like.user_id = %d AND user_like.reply_id = replies.id )",
					$user_to_check
				);
				$select_clauses[] = "user_like.user_id AS liked_by_user";
			}
		}

		// generate sql string
		$joins = join( " \n ", $join_clauses );
		$wheres = '';
		if ( ! empty( $where_clauses ) ) {
			$wheres = sprintf( 'WHERE %s', join( ' AND ', $where_clauses ) );
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
		$sql = <<<SQL
			SELECT {$selects} FROM {$wpdb->prefix}voxel_timeline_replies AS replies
			{$joins} {$wheres}
			GROUP BY replies.id
			ORDER BY replies.created_at ASC
			{$limit} {$offset}
		SQL;

		// dump_sql( $sql );die;
		$results = $wpdb->get_results( $sql, ARRAY_A );
		if ( ! is_array( $results ) ) {
			return [];
		}

		return array_map( '\Voxel\Timeline\Reply::get', $results );
	}

	private function _maybe_update_stats_cache() {
		$status = $this->get_status();
		if ( $post_id = $status->get_post_id() ) {
			if ( $status->is_review() ) {
				\Voxel\cache_post_review_reply_stats( $post_id );
			} elseif ( $status->get_published_as() === $post_id ) {
				\Voxel\cache_post_timeline_reply_stats( $post_id );
			} else {
				\Voxel\cache_post_wall_reply_stats( $post_id );
			}
		}
	}
}
