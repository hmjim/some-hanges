<?php

namespace Voxel\Product_Types;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Order_Repository {

	public static function query( array $args ) {
		global $wpdb;
		$args = array_merge( [
			'id' => null,
			'post_id' => null,
			'product_type' => null,
			'product_key' => null,
			'vendor_id' => null,
			'customer_id' => null,
			'party_id' => null,
			'status' => null,
			'status_not_in' => null,
			'reserved_at' => null,
			'object_id' => null,
			'session_id' => null,
			'search' => null,
			'search_customer' => null,
			'search_author' => null,
			'search_post' => null,
			'offset' => null,
			'limit' => 10,
			'order_by' => 'created_at',
			'order' => 'desc',
			'catalog_mode' => null,
			'calculate_count' => false,
		], $args );

		$join_clauses = [];
		$where_clauses = [];
		$orderby_clauses = [];
		$join_posts = false;
		$join_authors = false;
		$join_customers = false;

		if ( ! is_null( $args['id'] ) ) {
			$where_clauses[] = sprintf( 'orders.id = %d', absint( $args['id'] ) );
		}

		if ( ! is_null( $args['post_id'] ) ) {
			$where_clauses[] = sprintf( 'orders.post_id = %d', absint( $args['post_id'] ) );
		}

		if ( ! is_null( $args['customer_id'] ) ) {
			$where_clauses[] = sprintf( 'orders.customer_id = %d', absint( $args['customer_id'] ) );
		}

		if ( ! is_null( $args['vendor_id'] ) ) {
			$where_clauses[] = sprintf( 'orders.vendor_id = %d', absint( $args['vendor_id'] ) );
		}

		if ( ! is_null( $args['party_id'] ) ) {
			$where_clauses[] = sprintf(
				'( orders.customer_id = %d OR orders.vendor_id = %d )',
				absint( $args['party_id'] ),
				absint( $args['party_id'] )
			);
		}

		if ( ! is_null( $args['status'] ) ) {
			if ( is_array( $args['status'] ) ) {
				$where_clauses[] = sprintf( "orders.status IN ('%s')", join( "','", array_map( 'esc_sql', $args['status'] ) ) );
			} else {
				$where_clauses[] = sprintf( 'orders.status = \'%s\'', esc_sql( $args['status'] ) );
			}
		}

		if ( ! is_null( $args['status_not_in'] ) ) {
			if ( is_array( $args['status_not_in'] ) ) {
				$where_clauses[] = sprintf( "orders.status NOT IN ('%s')", join( "','", array_map( 'esc_sql', $args['status_not_in'] ) ) );
			} else {
				$where_clauses[] = sprintf( 'orders.status <> \'%s\'', esc_sql( $args['status_not_in'] ) );
			}
		}

		if ( ! is_null( $args['object_id'] ) ) {
			$where_clauses[] = sprintf( 'orders.object_id = \'%s\'', esc_sql( $args['object_id'] ) );
		}

		if ( ! is_null( $args['session_id'] ) ) {
			$where_clauses[] = sprintf( 'orders.session_id = \'%s\'', esc_sql( $args['session_id'] ) );
		}

		if ( ! is_null( $args['product_key'] ) ) {
			$where_clauses[] = sprintf( 'orders.product_key = \'%s\'', esc_sql( $args['product_key'] ) );
		}

		if ( ! is_null( $args['product_type'] ) ) {
			$where_clauses[] = sprintf( 'orders.product_type = \'%s\'', esc_sql( $args['product_type'] ) );
		}

		if ( ! is_null( $args['reserved_at'] ) ) {
			$reserved_at = date( 'Y-m-d', strtotime( $args['reserved_at'] ) );
			$where_clauses[] = sprintf(
				'( orders.checkin = \'%s\' OR ( orders.checkin <= \'%s\' AND orders.checkout >= \'%s\' ) )',
				esc_sql( $reserved_at ),
				esc_sql( $reserved_at ),
				esc_sql( $reserved_at )
			);
		}

		if ( ! is_null( $args['catalog_mode'] ) ) {
			$where_clauses[] = sprintf( 'orders.catalog_mode IS %s', $args['catalog_mode'] ? 'true' : 'false' );
		}

		if ( ! is_null( $args['search'] ) ) {
			$join_posts = true;
			$join_authors = true;
			$join_customers = true;
			$like = '%'.$wpdb->esc_like( $args['search'] ).'%';

			$where_clauses[] = $wpdb->prepare( <<<SQL
				( posts.post_title LIKE %s
					OR authors.user_email = %s OR authors.display_name LIKE %s
					OR customers.user_email = %s OR customers.display_name LIKE %s )
			SQL, $like, $args['search'], $like, $args['search'], $like );
		}

		if ( ! is_null( $args['search_customer'] ) ) {
			$join_customers = true;
			$like = '%'.$wpdb->esc_like( $args['search_customer'] ).'%';
			$where_clauses[] = $wpdb->prepare( <<<SQL
				( customers.user_login = %s OR customers.user_email = %s OR customers.ID = %s OR customers.display_name LIKE %s )
			SQL, $args['search_customer'], $args['search_customer'], $args['search_customer'], $like );
		}

		if ( ! is_null( $args['search_author'] ) ) {
			$join_authors = true;
			$like = '%'.$wpdb->esc_like( $args['search_author'] ).'%';
			$where_clauses[] = $wpdb->prepare( <<<SQL
				( authors.user_login = %s OR authors.user_email = %s OR authors.ID = %s OR authors.display_name LIKE %s )
			SQL, $args['search_author'], $args['search_author'], $args['search_author'], $like );
		}

		if ( ! is_null( $args['search_post'] ) ) {
			$join_posts = true;
			$like = '%'.$wpdb->esc_like( $args['search_post'] ).'%';
			$where_clauses[] = $wpdb->prepare( <<<SQL
				( posts.ID = %s OR posts.post_title LIKE %s )
			SQL, $args['search_post'], $like );
		}


		$where_clauses[] = sprintf( 'orders.testmode IS %s', \Voxel\Stripe::is_test_mode() ? 'true' : 'false' );

		if ( $join_posts ) {
			$join_clauses[] = "LEFT JOIN {$wpdb->posts} AS posts ON orders.post_id = posts.ID";
		}

		if ( $join_authors ) {
			$join_clauses[] = "LEFT JOIN {$wpdb->users} AS authors ON orders.vendor_id = authors.ID";
		}

		if ( $join_customers ) {
			$join_clauses[] = "LEFT JOIN {$wpdb->users} AS customers ON orders.customer_id = customers.ID";
		}

		if ( ! is_null( $args['order_by'] ) ) {
			$order = $args['order'] === 'asc' ? 'ASC' : 'DESC';

			if ( $args['order_by'] === 'created_at' ) {
				$orderby_clauses[] = "orders.created_at {$order}";
			} elseif ( $args['order_by'] === 'reservation' ) {
				$orderby_clauses[] = "orders.checkin, CONVERT( REGEXP_REPLACE( orders.timeslot, '[^0-9]+', '' ), SIGNED )";
			} elseif ( $args['order_by'] === 'checkin' ) {
				$orderby_clauses[] = "orders.checkin {$order}";
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

		$orderbys = '';
		if ( ! empty( $orderby_clauses ) ) {
			$orderbys = sprintf( 'ORDER BY %s', join( ", ", $orderby_clauses ) );
		}

		if ( $args['calculate_count'] ) {
			return $wpdb->get_var( "
				SELECT COUNT(*) AS total_count FROM {$wpdb->prefix}voxel_orders AS orders
				{$joins}
				{$wheres}
			" );
		}

		$sql = $wpdb->remove_placeholder_escape( "
			SELECT orders.* FROM {$wpdb->prefix}voxel_orders AS orders
			{$joins}
			{$wheres}
			{$orderbys}
			{$limit} {$offset}
		" );

		if ( ! empty( $args['__dump_sql'] ) ) {
			dump_sql( $sql );
		}

		$results = $wpdb->get_results( $sql, ARRAY_A );
		if ( ! is_array( $results ) ) {
			return [];
		}

		return array_map( '\Voxel\Order::get', $results );
	}

	public static function create( array $data ): \Voxel\Order {
		global $wpdb;
		$data = array_merge( [
			'id' => null,
			'post_id' => null,
			'product_type' => null,
			'product_key' => null,
			'customer_id' => null,
			'vendor_id' => null,
			'details' => null,
			'status' => null,
			'session_id' => null,
			'mode' => null,
			'object_id' => null,
			'object_details' => null,
			'created_at' => \Voxel\utc()->format( 'Y-m-d H:i:s' ),
			'catalog_mode' => null,
		], $data );

		$required_data = $data;
		unset( $required_data['id'] );

		$sql = static::_generate_insert_query( $data );
		$wpdb->query( $sql );
		$data['id'] = $wpdb->insert_id;

		return new \Voxel\Order( $data );
	}

	public static function _generate_insert_query( array $data ) {
		global $wpdb;

		$escaped_data = [];
		foreach ( ['id', 'post_id', 'customer_id', 'vendor_id'] as $column_name ) {
			if ( isset( $data[ $column_name ] ) ) {
				$escaped_data[ $column_name ] = absint( $data[ $column_name ] );
			}
		}

		if ( isset( $data['details'] ) && is_array( $data['details'] ) ) {
			$data['details'] = wp_json_encode( $data['details'] );
		}

		if ( isset( $data['object_details'] ) ) {
			if ( $data['object_details'] instanceof \Stripe\PaymentIntent ) {
				$data['object_details'] = wp_json_encode( \Voxel\Order::extract_intent_details( $data['object_details'] ) );
			}

			if ( $data['object_details'] instanceof \Stripe\Subscription ) {
				$data['object_details'] = wp_json_encode( \Voxel\Order::extract_subscription_details( $data['object_details'] ) );
			}
		}

		foreach ( ['product_type', 'product_key', 'details', 'status', 'session_id', 'mode', 'object_id', 'object_details', 'created_at'] as $column_name ) {
			if ( isset( $data[ $column_name ] ) ) {
				$escaped_data[ $column_name ] = sprintf( '\'%s\'', esc_sql( $data[ $column_name ] ) );
			}
		}

		if ( isset( $data['catalog_mode'] ) ) {
			$escaped_data['catalog_mode'] = $data['catalog_mode'] ? 'true' : 'false';
		}

		$escaped_data['testmode'] = \Voxel\Stripe::is_test_mode() ? 'true' : 'false';

		$columns = join( ', ', array_map( function( $column_name ) {
			return sprintf( '`%s`', esc_sql( $column_name ) );
		}, array_keys( $escaped_data ) ) );

		$values = join( ', ', $escaped_data );

		$on_duplicate = join( ', ', array_map( function( $column_name ) {
			return sprintf( '`%s`=VALUES(`%s`)', $column_name, $column_name );
		}, array_keys( $escaped_data ) ) );

		$sql = "INSERT INTO {$wpdb->prefix}voxel_orders ($columns) VALUES ($values)
					ON DUPLICATE KEY UPDATE $on_duplicate";

		return $sql;
	}
}
