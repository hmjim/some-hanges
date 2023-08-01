<?php

namespace Voxel\Post_Types;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Index_Query {

	public
		$post_type,
		$table;

	private
		$select_clauses = [],
		$join_clauses = [],
		$where_clauses = [],
		$orderby_clauses = [],
		$groupby_clauses = [],
		$post_statuses = ['publish'];

	public function __construct( \Voxel\Post_Type $post_type ) {
		$this->post_type = $post_type;
		$this->table = $post_type->get_index_table();
	}

	public function select( string $clause_sql ) {
		$this->select_clauses[] = $clause_sql;
	}

	public function join( string $clause_sql ) {
		$this->join_clauses[] = $clause_sql;
	}

	public function where( string $clause_sql ) {
		$this->where_clauses[] = $clause_sql;
	}

	public function orderby( string $clause_sql ) {
		$this->orderby_clauses[] = $clause_sql;
	}

	public function groupby( string $clause_sql ) {
		$this->groupby_clauses[] = $clause_sql;
	}

	public function set_post_statuses( array $statuses ) {
		$this->post_statuses = $statuses;
	}

	public function get_sql( array $args = [], $cb = null ) {
		// reset
		$this->select_clauses = [];
		$this->join_clauses = [];
		$this->where_clauses = [];
		$this->orderby_clauses = [];
		$this->groupby_clauses = [];
		$this->post_statuses = ['publish'];

		// apply filters
		foreach ( $this->post_type->get_filters() as $filter ) {
			$filter->query( $this, $args );
		}

		$limit = '';
		if ( isset( $args['limit'] ) && absint( $args['limit'] ) > 0 ) {
			$limit = sprintf( 'LIMIT %d', absint( $args['limit'] ) );
		}

		$offset = '';
		if ( isset( $args['offset'] ) && absint( $args['offset'] ) > 0 ) {
			$offset = sprintf( 'OFFSET %d', absint( $args['offset'] ) );
		}

		if ( is_callable( $cb ) ) {
			$cb( $this, $args );
		}

		// generate sql string
		$sql = "
			SELECT DISTINCT `{$this->table->get_escaped_name()}`.post_id {$this->_get_select_clauses()}
				FROM `{$this->table->get_escaped_name()}`
			{$this->_get_join_clauses()}
			{$this->_get_where_clauses()}
			{$this->_get_groupby_clauses()}
			{$this->_get_orderby_clauses()}
			{$limit} {$offset}
		";

		return $sql;
	}

	public function get_count_sql( array $args = [], $cb = null ) {
		// reset
		$this->select_clauses = [];
		$this->join_clauses = [];
		$this->where_clauses = [];
		$this->orderby_clauses = [];
		$this->groupby_clauses = [];
		$this->post_statuses = ['publish'];

		// apply filters
		foreach ( $this->post_type->get_filters() as $filter ) {
			$filter->query( $this, $args );
		}

		$limit = '';
		if ( isset( $args['limit'] ) && absint( $args['limit'] ) > 0 ) {
			$limit = sprintf( 'LIMIT %d', absint( $args['limit'] ) );
		}

		$offset = '';
		if ( isset( $args['offset'] ) && absint( $args['offset'] ) > 0 ) {
			$offset = sprintf( 'OFFSET %d', absint( $args['offset'] ) );
		}

		if ( is_callable( $cb ) ) {
			$cb( $this, $args );
		}

		// generate sql string
		$sql = "
			SELECT COUNT( DISTINCT `{$this->table->get_escaped_name()}`.post_id )
				FROM `{$this->table->get_escaped_name()}`
			{$this->_get_join_clauses()}
			{$this->_get_where_clauses()}
			LIMIT 1
		";

		return $sql;
	}

	private function _get_where_clauses() {
		$clauses = $this->where_clauses;

		$indexable_statuses = $this->post_type->get_indexable_statuses();
		if ( ! empty( $this->post_statuses ) && count( $indexable_statuses ) > 1 ) {
			$statuses = array_filter( array_map( 'esc_sql', $this->post_statuses ) );
			$statuses = array_values( $statuses );
			if ( count( $statuses ) === 1 ) {
				array_unshift( $clauses, sprintf( "`{$this->table->get_escaped_name()}`.post_status = '%s'", $statuses[0] ) );
			} elseif ( count( $statuses ) >= 1 ) {
				array_unshift( $clauses, sprintf( "`{$this->table->get_escaped_name()}`.post_status IN (%s)", "'".join( "','", $statuses )."'" ) );
			}
		}

		if ( empty( $clauses ) ) {
			return '';
		}

		return sprintf( 'WHERE %s', join( ' AND ', $clauses ) );
	}

	private function _get_select_clauses() {
		if ( empty( $this->select_clauses ) ) {
			return '';
		}

		return ', '. join( ", ", $this->select_clauses );
	}

	private function _get_join_clauses() {
		if ( empty( $this->join_clauses ) ) {
			return '';
		}

		return join( " \n ", $this->join_clauses );
	}

	private function _get_orderby_clauses() {
		if ( empty( $this->orderby_clauses ) ) {
			return 'ORDER BY post_id DESC';
		}

		return sprintf( 'ORDER BY %s', join( ", ", $this->orderby_clauses ) );
	}

	private function _get_groupby_clauses() {
		if ( empty( $this->groupby_clauses ) ) {
			return '';
		}

		return sprintf( 'GROUP BY %s', join( ", ", array_unique( $this->groupby_clauses ) ) );
	}

	public function get_posts( array $args = [], $cb = null ) {
		global $wpdb;

		// workaround to https://jira.mariadb.org/browse/MDEV-26123
		if ( \Voxel\is_using_mariadb() ) {
			$wpdb->query( 'SET autocommit = 0;' );
		}

		// dump_sql($this->get_sql( $args ));
		$post_ids = $wpdb->get_col( $this->get_sql( $args, $cb ) );
		// dump_sql( $this->get_sql( $args ) );
		// dump_sql( $this->get_count_sql( $args ) );

		if ( \Voxel\is_using_mariadb() ) {
			$wpdb->query( 'SET autocommit = 1;' );
		}

		return array_map( 'intval', $post_ids );
	}

	public function get_post_count( array $args = [], $cb = null ) {
		global $wpdb;

		// workaround to https://jira.mariadb.org/browse/MDEV-26123
		if ( \Voxel\is_using_mariadb() ) {
			$wpdb->query( 'SET autocommit = 0;' );
		}

		$count = $wpdb->get_var( $this->get_count_sql( $args, $cb ) );

		if ( \Voxel\is_using_mariadb() ) {
			$wpdb->query( 'SET autocommit = 1;' );
		}

		return is_numeric( $count ) ? absint( $count ) : 0;
	}
}
