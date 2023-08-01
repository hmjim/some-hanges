<?php

namespace Voxel;

if ( ! defined('ABSPATH') ) {
	exit;
}

function current_user() {
	return \Voxel\User::get( get_current_user_id() );
}

function cache_user_follow_stats( $user_id ) {
	global $wpdb;

	$stats = [
		'following' => [],
		'followed' => [],
	];

	// following
	$following = $wpdb->get_results( $wpdb->prepare( <<<SQL
		SELECT `status`, COUNT(*) AS `count`
		FROM {$wpdb->prefix}voxel_followers
		WHERE `follower_type` = 'user' AND `follower_id` = %d
		GROUP BY `status`
	SQL, $user_id ) );

	foreach ( $following as $status ) {
		$stats['following'][ (int) $status->status ] = absint( $status->count );
	}

	// followed_by
	$followed = $wpdb->get_results( $wpdb->prepare( <<<SQL
		SELECT `status`, COUNT(*) AS `count`
		FROM {$wpdb->prefix}voxel_followers
		WHERE `object_type` = 'user' AND `object_id` = %d
		GROUP BY `status`
	SQL, $user_id ) );

	foreach ( $followed as $status ) {
		$stats['followed'][ (int) $status->status ] = absint( $status->count );
	}

	update_user_meta( $user_id, 'voxel:follow_stats', wp_slash( wp_json_encode( $stats ) ) );
	return $stats;
}

function cache_post_follow_stats( $post_id ) {
	global $wpdb;

	$stats = [
		'followed' => [],
	];

	// followed_by
	$followed = $wpdb->get_results( $wpdb->prepare( <<<SQL
		SELECT `status`, COUNT(*) AS `count`
		FROM {$wpdb->prefix}voxel_followers
		WHERE `object_type` = 'post' AND `object_id` = %d
		GROUP BY `status`
	SQL, $post_id ) );

	foreach ( $followed as $status ) {
		$stats['followed'][ (int) $status->status ] = absint( $status->count );
	}

	update_post_meta( $post_id, 'voxel:follow_stats', wp_slash( wp_json_encode( $stats ) ) );
	return $stats;
}

function cache_user_post_stats( $user_id ) {
	global $wpdb;

	$stats = [];

	$user_id = absint( $user_id );
	$post_types = [];
	foreach ( \Voxel\Post_Type::get_voxel_types() as $post_type ) {
		$post_types[] = $wpdb->prepare( '%s', $post_type->get_key() );
	}

	if ( empty( $post_types ) ) {
		update_user_meta( $user_id, 'voxel:post_stats', wp_slash( wp_json_encode( $stats ) ) );
		return $stats;
	}

	$post_types = join( ',', $post_types );
	$results = $wpdb->get_results( <<<SQL
		SELECT COUNT(ID) AS total, post_type, post_status FROM {$wpdb->posts}
		WHERE post_author = {$user_id}
			AND post_type IN ({$post_types})
			AND post_status IN ('publish','pending')
		GROUP BY post_type, post_status
		ORDER BY post_type
	SQL );

	foreach ( $results as $result ) {
		if ( ! isset( $stats[ $result->post_type ] ) ) {
			$stats[ $result->post_type ] = [];
		}

		$stats[ $result->post_type ][ $result->post_status ] = absint( $result->total );
	}

	update_user_meta( $user_id, 'voxel:post_stats', wp_slash( wp_json_encode( $stats ) ) );
	return $stats;
}

function get_user_by_id_or_email( $id_or_email ) {
	if ( is_numeric( $id_or_email ) ) {
		$user = get_user_by( 'id', absint( $id_or_email ) );
	} elseif ( $id_or_email instanceof \WP_User ) {
		$user = $id_or_email;
	} elseif ( $id_or_email instanceof \WP_Post ) {
		$user = get_user_by( 'id', (int) $id_or_email->post_author );
	} elseif ( $id_or_email instanceof \WP_Comment && ! empty( $id_or_email->user_id ) ) {
		$user = get_user_by( 'id', (int) $id_or_email->user_id );
	} elseif ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
		$user = get_user_by( 'email', $id_or_email );
	} else {
		$user = null;
	}

	return \Voxel\User::get( $user );
}
