<?php

namespace Voxel;

if ( ! defined('ABSPATH') ) {
	exit;
}

function _get_term_tree( $terms = [], $parent = 0 ) {
	$result = [];
	foreach ( $terms as $term ) {
		$term['slug'] = urldecode( $term['slug'] );
		if ( isset( $term['icon'] ) ) {
			$term['icon'] = \Voxel\get_icon_markup( $term['icon'] );
		}

		if ( $parent === $term['parent'] ) {
			$term['children'] = \Voxel\_get_term_tree( $terms, $term['id'] );
			$result[] = $term;
		}
	}

	return $result;
}

function get_terms( $taxonomy, $args = [] ) {
	global $wpdb;

	$args = wp_parse_args( $args, [
		'fields' => [ 'order', 'icon' ],
		'orderby' => 'default',
		'slug__in' => null,
	] );

	$selects = [ 't.term_id AS id, tt.parent AS parent, t.slug AS slug, t.name AS label' ];
	$joins = [];
	$where = [];
	$orderby = [];

	foreach ( $args['fields'] as $field ) {
		if ( $field === 'order' ) {
			$selects[] = 't.voxel_order AS `order`';
		}

		if ( $field === 'icon' ) {
			$joins[] = "LEFT JOIN {$wpdb->termmeta} AS tm ON (tm.term_id = t.term_id AND tm.meta_key = 'voxel_icon')";
			$selects[] = 'tm.meta_value as icon';
		}
	}

	// taxonomy where clause
	$where[] = sprintf( 'tt.taxonomy IN (\'%s\')', esc_sql( $taxonomy ) );

	if ( is_array( $args['slug__in'] ) && ! empty( $args['slug__in'] ) ) {
		$_term_slugs = array_map( function( $term_slug ) {
			return '\''.esc_sql( sanitize_title( $term_slug ) ).'\'';
		}, $args['slug__in'] );

		$_joined_terms = join( ',', $_term_slugs );
		$where[] = sprintf( 'slug IN (%s)', $_joined_terms );
	}

	if ( $args['orderby'] === 'name' ) {
		$orderby[] = 'label ASC';
	} else {
		$orderby[] = 't.voxel_order ASC, label ASC';
	}

	$_select_clauses = join( ', ', $selects );
	$_join_clauses = join( " \n ", $joins );
	$_where_clauses = join( ' AND ', $where );
	$_orderby_clauses = join( ', ', $orderby );
	$results = $wpdb->get_results( "
		SELECT {$_select_clauses}
		FROM {$wpdb->terms} AS t
		INNER JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id
		{$_join_clauses}
		WHERE {$_where_clauses}
		ORDER BY {$_orderby_clauses}
	", ARRAY_A );

	if ( $args['orderby'] === 'name' ) {
		if ( in_array( 'icon', $args['fields'], true ) ) {
			foreach ( $results as $key => $term ) {
				$results[ $key ]['slug'] = urldecode( $results[ $key ]['slug'] );
				$results[ $key ]['icon'] = \Voxel\get_icon_markup( $term['icon'] );
			}
		}

		return $results;
	} else {
		return \Voxel\_get_term_tree( $results, '0' );
	}
}
