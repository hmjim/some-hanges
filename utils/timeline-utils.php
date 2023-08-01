<?php

namespace Voxel\Timeline;

if ( ! defined('ABSPATH') ) {
	exit;
}

function user_has_reached_status_rate_limit( int $user_id ): bool {
	global $wpdb;

	$limits = (array) \Voxel\get( 'settings.timeline.posts.rate_limit' );
	$user_id = absint( $user_id );

	$time_between_reached = !! $wpdb->get_var( $wpdb->prepare( <<<SQL
		SELECT COUNT(tl.id) < 1
			FROM {$wpdb->prefix}voxel_timeline tl
			LEFT JOIN {$wpdb->posts} AS p on tl.published_as = p.ID
		WHERE ( tl.user_id = {$user_id} OR p.post_author = {$user_id} )
			AND created_at >= %s
		LIMIT 1
	SQL, date( 'Y-m-d H:i:s', strtotime( sprintf( '-%d seconds', absint( $limits['time_between'] ?? 20 ) ) ) ) ) );

	if ( ! $time_between_reached ) {
		return true;
	}

	$hourly_limit = absint( $limits['hourly_limit'] ?? 20 );
	$hourly_limit_reached = !! $wpdb->get_var( $wpdb->prepare( <<<SQL
		SELECT COUNT(tl.id) > {$hourly_limit}
			FROM {$wpdb->prefix}voxel_timeline tl
			LEFT JOIN {$wpdb->posts} AS p on tl.published_as = p.ID
		WHERE ( tl.user_id = {$user_id} OR p.post_author = {$user_id} )
			AND created_at >= %s
	SQL, date( 'Y-m-d H:i:s', strtotime('-1 hour') ) ) );

	if ( $hourly_limit_reached ) {
		return true;
	}

	$daily_limit = absint( $limits['daily_limit'] ?? 100 );
	$daily_limit_reached = !! $wpdb->get_var( $wpdb->prepare( <<<SQL
		SELECT COUNT(tl.id) > {$daily_limit}
			FROM {$wpdb->prefix}voxel_timeline tl
			LEFT JOIN {$wpdb->posts} AS p on tl.published_as = p.ID
		WHERE ( tl.user_id = {$user_id} OR p.post_author = {$user_id} )
			AND created_at >= %s
	SQL, date( 'Y-m-d H:i:s', strtotime('-1 day') ) ) );

	if ( $daily_limit_reached ) {
		return true;
	}

	return false;
}

function user_has_reached_reply_rate_limit( int $user_id ): bool {
	if ( current_user_can( 'administrator' ) ) {
		return false;
	}

	global $wpdb;

	$limits = (array) \Voxel\get( 'settings.timeline.replies.rate_limit' );
	$user_id = absint( $user_id );

	$time_between_reached = !! $wpdb->get_var( $wpdb->prepare( <<<SQL
		SELECT COUNT(r.id) < 1
			FROM {$wpdb->prefix}voxel_timeline_replies r
			LEFT JOIN {$wpdb->posts} AS p on r.published_as = p.ID
		WHERE ( r.user_id = {$user_id} OR p.post_author = {$user_id} )
			AND created_at >= %s
		LIMIT 1
	SQL, date( 'Y-m-d H:i:s', strtotime( sprintf( '-%d seconds', absint( $limits['time_between'] ?? 5 ) ) ) ) ) );

	if ( ! $time_between_reached ) {
		return true;
	}

	$hourly_limit = absint( $limits['hourly_limit'] ?? 100 );
	$hourly_limit_reached = !! $wpdb->get_var( $wpdb->prepare( <<<SQL
		SELECT COUNT(r.id) > {$hourly_limit}
			FROM {$wpdb->prefix}voxel_timeline_replies r
			LEFT JOIN {$wpdb->posts} AS p on r.published_as = p.ID
		WHERE ( r.user_id = {$user_id} OR p.post_author = {$user_id} )
			AND created_at >= %s
	SQL, date( 'Y-m-d H:i:s', strtotime('-1 hour') ) ) );

	if ( $hourly_limit_reached ) {
		return true;
	}

	$daily_limit = absint( $limits['daily_limit'] ?? 1000 );
	$daily_limit_reached = !! $wpdb->get_var( $wpdb->prepare( <<<SQL
		SELECT COUNT(r.id) > {$daily_limit}
			FROM {$wpdb->prefix}voxel_timeline_replies r
			LEFT JOIN {$wpdb->posts} AS p on r.published_as = p.ID
		WHERE ( r.user_id = {$user_id} OR p.post_author = {$user_id} )
			AND created_at >= %s
	SQL, date( 'Y-m-d H:i:s', strtotime('-1 day') ) ) );

	if ( $daily_limit_reached ) {
		return true;
	}

	return false;
}

function prepare_status_json( \Voxel\Timeline\Status $status ): array {
	$user = $status->get_user();
	$publisher = $status->get_post_published_as();
	$post = $status->get_post();
	$details = $status->get_details();
	$file_field = new \Voxel\Timeline\Fields\Status_Files_Field;

	return [
		'id' => $status->get_id(),
		'key' => $status->get_unique_key(),
		'link' => $status->get_link(),
		'time' => $status->get_time_for_display(),
		'edit_time' => $status->get_edit_time_for_display(),
		'content' => $status->get_content_for_display(),
		'raw_content' => $status->get_content(),
		'files' => $file_field->prepare_for_display( $details['files'] ?? '' ),
		'is_review' => $status->is_review(),
		'review_score' => $details['rating']['score'] ?? null,
		'user' => [
			'exists' => !! $user,
			'name' => $user ? $user->get_display_name() : null,
			'avatar' => $user ? $user->get_avatar_markup() : null,
			'link' => $user ? $user->get_link() : null,
		],
		'publisher' => [
			'exists' => !! $publisher,
			'name' => $publisher ? $publisher->get_title() : null,
			'avatar' => $publisher ? $publisher->get_logo_markup() : null,
			'link' => $publisher ? $publisher->get_link() : null,
		],
		'post' => [
			'exists' => !! $post,
			'title' => $post ? $post->get_title() : null,
			'link' => $post ? $post->get_link() : null,
			'is_profile' => $user && $post && (int) $user->get_profile_id() === (int) $post->get_id(),
		],
		'user_can_edit' => $status->is_editable_by_current_user(),
		'user_can_moderate' => $status->is_moderatable_by_current_user(),
		'liked_by_user' => $status->liked_by_user(),
		'like_count' => $status->get_like_count() ? number_format_i18n( $status->get_like_count() ) : null,
		'reply_count' => $status->get_reply_count() ? number_format_i18n( $status->get_reply_count() ) : null,
		'replies' => [
			'requested' => false,
			'visible' => false,
			'page' => 1,
			'loading' => false,
			'hasMore' => false,
			'list' => [],
		],
	];
}

function prepare_reply_json( \Voxel\Timeline\Reply $reply ): array {
	$user = $reply->get_user();
	return [
		'id' => $reply->get_id(),
		'key' => $reply->get_unique_key(),
		'link' => $reply->get_link(),
		'time' => $reply->get_time_for_display(),
		'edit_time' => $reply->get_edit_time_for_display(),
		'content' => $reply->get_content_for_display(),
		'raw_content' => $reply->get_content(),
		'user' => [
			'name' => $user->get_display_name(),
			'avatar' => $user->get_avatar_markup(),
			'link' => $user->get_link(),
		],
		'user_can_edit' => $reply->is_editable_by_current_user(),
		'user_can_moderate' => $reply->is_moderatable_by_current_user(),
		'liked_by_user' => $reply->liked_by_user(),
		'like_count' => $reply->get_like_count() ? number_format_i18n( $reply->get_like_count() ) : null,
		'reply_count' => $reply->get_reply_count() ? number_format_i18n( $reply->get_reply_count() ) : null,
		'replies' => [
			'requested' => false,
			'visible' => false,
			'page' => 1,
			'loading' => false,
			'hasMore' => false,
			'list' => [],
		],
	];
}
