<?php

namespace Voxel;

if ( ! defined('ABSPATH') ) {
	exit;
}

class User {
	use \Voxel\Users\Security_Trait;
	use \Voxel\Users\Vendor_Trait;
	use \Voxel\Users\Customer_Trait;
	use \Voxel\Users\Social_Trait;
	use \Voxel\Users\Member_Trait;

	private $wp_user;
	private $account_details;
	private $vendor_stats;

	private static $instances = [];
	public static function get( $user ) {
		if ( is_numeric( $user ) ) {
			$user = get_userdata( $user );
		}

		if ( ! $user instanceof \WP_User ) {
			return null;
		}

		if ( ! array_key_exists( $user->ID, self::$instances ) ) {
			self::$instances[ $user->ID ] = new self( $user );
		}

		return self::$instances[ $user->ID ];
	}

	public static function get_by_profile_id( $profile_id ) {
		$results = get_users( [
			'meta_key' => 'voxel:profile_id',
			'meta_value' => $profile_id,
			'number' => 1,
			'fields' => 'ID',
		] );

		return \Voxel\User::get( array_shift( $results ) );
	}

	private function __construct( \WP_User $user ) {
		$this->wp_user = $user;
	}

	public function get_id() {
		return $this->wp_user->ID;
	}

	public function get_link() {
		return get_author_posts_url( $this->get_id() );
	}

	public function get_display_name() {
		$display_name = $this->wp_user->display_name;
		return ! empty( $display_name ) ? $display_name : $this->get_username();
	}

	public function get_email() {
		return $this->wp_user->user_email;
	}

	public function get_username() {
		return $this->wp_user->user_login;
	}

	public function get_first_name() {
		return $this->wp_user->first_name;
	}

	public function get_last_name() {
		return $this->wp_user->last_name;
	}

	public function get_roles() {
		return $this->wp_user->roles;
	}

	public function has_role( $role ) {
		return in_array( $role, $this->get_roles(), true );
	}

	public function get_avatar_id() {
		$avatar_id = get_user_meta( $this->get_id(), 'voxel:avatar', true );
		if ( $avatar_id ) {
			return $avatar_id;
		}

		$field = \Voxel\Post_Type::get('profile')->get_field('voxel:avatar');
		$default = $field ? $field->get_prop('default') : null;
		if ( $default ) {
			return $default;
		}

		return null;
	}

	public function get_avatar_markup( $size = 96 ) {
		return get_avatar( $this->get_id(), $size, '', '', [
			'class' => 'ts-status-avatar',
		] );
	}

	public function get_edit_link() {
		return get_edit_user_link( $this->get_id() );
	}

	public function can_create_post( string $post_type_key ): bool {
		if ( current_user_can('administrator') || current_user_can('editor') ) {
			return true;
		}

		$post_type = \Voxel\Post_Type::get( $post_type_key );
		if ( ! $post_type ) {
			return false;
		}

		$membership = $this->get_membership();
		$plan = $membership->plan;
		if ( ! $plan ) {
			return false;
		}

		$config = $plan->get_config();
		$submissions = (array) ( $config['submissions'] ?? [] );

		if ( ! isset( $submissions[ $post_type->get_key() ] ) ) {
			return false;
		}

		$limit = absint( $submissions[ $post_type->get_key() ] );
		if ( $limit < 1 ) {
			return false;
		}

		$stats = $this->get_post_stats();

		// count of pending+publish posts
		$total_count = ( $stats[ $post_type->get_key() ]['publish'] ?? 0 ) + ( $stats[ $post_type->get_key() ]['pending'] ?? 0 );

		return $total_count < $limit;
	}

	public function get_profile_id() {
		return get_user_meta( $this->get_id(), 'voxel:profile_id', true );
	}

	public function get_profile() {
		return \Voxel\Post::find( [
			'post_type' => 'profile',
			'p' => $this->get_profile_id(),
			'author' => $this->get_id(),
		] );
	}

	public function get_or_create_profile() {
		$profile = $this->get_profile();
		if ( $profile ) {
			return $profile;
		}

		$profile_id = wp_insert_post( [
			'post_type' => 'profile',
			'post_author' => $this->get_id(),
			'post_status' => 'publish',
		] );

		if ( is_wp_error( $profile_id ) ) {
			return null;
		}

		update_user_meta( $this->get_id(), 'voxel:profile_id', $profile_id );
		return \Voxel\Post::get( $profile_id );
	}

	public function is_verified(): bool {
		$profile = $this->get_profile();
		return $profile ? $profile->is_verified() : false;
	}

	public function get_post_stats() {
		$stats = json_decode( get_user_meta( $this->get_id(), 'voxel:post_stats', true ), ARRAY_A );
		if ( ! is_array( $stats ) ) {
			$stats = \Voxel\cache_user_post_stats( $this->get_id() );
		}

		return $stats;
	}

	public function get_wp_user_object() {
		return $this->wp_user;
	}

	public function get_object_type() {
		return 'user';
	}

	public function has_cap( $capability, ...$args ) {
		return user_can( $this->wp_user, $capability, ...$args );
	}

	public static function dummy() {
		return static::get( new \WP_User( (object) [ 'ID' => 0 ] ) );
	}
}
