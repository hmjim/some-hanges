<?php

namespace Voxel\Post_Types;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Index_Table {

	private $post_type;
	private $table_name;

	private
		$columns = [],
		$keys = [],
		$foreign_keys = [];

	public function __construct( \Voxel\Post_Type $post_type ) {
		global $wpdb;
		$this->post_type = $post_type;
		$this->table_name = sprintf( '%1$svoxel_index_%2$s', $wpdb->prefix, $this->post_type->get_key() );
	}

	public function get_name() {
		return $this->table_name;
	}

	public function get_escaped_name() {
		return esc_sql( $this->table_name );
	}

	public function add_column( string $column_sql ) {
		if ( ! isset( $this->columns[ $column_sql ] ) ) {
			$this->columns[ $column_sql ] = $column_sql;
		}
	}

	public function add_key( string $key_sql ) {
		if ( ! isset( $this->keys[ $key_sql ] ) ) {
			$this->keys[ $key_sql ] = $key_sql;
		}
	}

	public function add_foreign_key( string $key_sql ) {
		if ( ! isset( $this->foreign_keys[ $key_sql ] ) ) {
			$this->foreign_keys[ $key_sql ] = $key_sql;
		}
	}

	public function get_sql() {
		global $wpdb;

		$this->add_column( 'id INT UNSIGNED NOT NULL AUTO_INCREMENT' );
		$this->add_column( 'post_id BIGINT(20) UNSIGNED NOT NULL' );
		$this->add_column( 'post_status VARCHAR(20) NOT NULL' );
		$this->add_key( 'PRIMARY KEY (id)' );
		$this->add_key( 'UNIQUE KEY (post_id)' );
		$this->add_key( 'KEY (post_status)' );
		$this->add_foreign_key( sprintf( 'FOREIGN KEY (post_id) REFERENCES %s(ID) ON DELETE CASCADE', $wpdb->posts ) );

		$filters = $this->post_type->get_filters();

		// sort filters by key so that changing filter order doesn't affect
		// the generated index table schema
		ksort( $filters );

		foreach ( $filters as $filter ) {
			$filter->setup( $this );
		}

		$columns = "\n\t".join( ",\n\t", $this->columns ).',';
		$keys = "\n\t".join( ",\n\t", $this->keys ).',';
		$foreign_keys = "\n\t".join( ",\n\t", $this->foreign_keys );

		$sql = "CREATE TABLE IF NOT EXISTS `{$this->get_escaped_name()}` ($columns \n $keys \n $foreign_keys \n) ENGINE = InnoDB {$wpdb->get_charset_collate()};";
		return $sql;
	}

	public function create() {
		global $wpdb;
		$wpdb->query( $this->get_sql() );
	}

	public function index( $post_ids, $filters = null ) {
		global $wpdb;

		$post_ids = (array) $post_ids;
		$data = [];

		foreach ( $post_ids as $i => $post_id ) {
			$post = \Voxel\Post::get( $post_id );
			if ( ! $post ) {
				continue;
			}

			$data[ $i ] = [
				'post_id' => $post->get_id(),
				'post_status' => '\''.esc_sql( $post->get_status() ).'\'',
			];

			foreach ( $this->post_type->get_filters() as $filter ) {
				// if a specific list of filters has been passed to index, skip on other filters
				if ( is_array( $filters ) && ! in_array( $filter->get_key(), $filters, true ) ) {
					continue;
				}

				$data[ $i ] += $filter->index( \Voxel\Post::get( $post_id ) );
			}
		}

		if ( ! empty( $data ) ) {
			$columns = join( ', ', array_map( function( $column_name ) {
				return sprintf( '`%s`', esc_sql( $column_name ) );
			}, array_keys( $data[0] ) ) );

			$values = join( ', ', array_map( function( $row ) {
				return '('.join( ', ', $row ).')';
			}, $data ) );

			$on_duplicate = join( ', ', array_map( function( $column_name ) {
				return sprintf( '`%s`=VALUES(`%s`)', $column_name, $column_name );
			}, array_keys( $data[0] ) ) );

			$sql = "INSERT INTO `{$this->get_escaped_name()}` ($columns) VALUES $values
						ON DUPLICATE KEY UPDATE $on_duplicate";

			// dump_sql( $sql );

			$wpdb->query( $sql );
		}
	}

	public function unindex( $post_ids ) {
		global $wpdb;
		$post_ids = array_map( 'absint', (array) $post_ids );
		if ( empty( $post_ids ) ) {
			return;
		}

		$ids = join( ',', $post_ids );
		$sql = "DELETE FROM `{$this->get_escaped_name()}` WHERE post_id IN ({$ids})";
		$wpdb->query( $sql );
	}

	public function truncate() {
		global $wpdb;
		$wpdb->query( "TRUNCATE TABLE `{$this->get_escaped_name()}`" );
	}

	public function drop() {
		global $wpdb;
		$wpdb->query( "DROP TABLE IF EXISTS `{$this->get_escaped_name()}`" );
	}

	public function recreate() {
		$this->drop();
		$this->create();
	}

	public function exists(): bool {
		global $wpdb;
		if ( ! $wpdb->get_var( "SHOW TABLES LIKE '{$this->get_escaped_name()}'" ) ) {
			return false;
		}

		// post_status became a mandatory column later on, if missing, always recreate table
		if ( ! $wpdb->get_var( "SHOW COLUMNS FROM `{$this->get_escaped_name()}` LIKE 'post_status'" ) ) {
			return false;
		}

		return true;
	}
}
