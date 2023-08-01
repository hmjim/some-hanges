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

	public function get_role_keys() {
		return $this->wp_user->roles;
	}

	/**
	 * Get list of roles assigned to this user.
	 *
	 * @since 1.2
	 * @return \Voxel\Role[]
	 */
	public function get_roles(): array {
		return array_filter( array_map( function( $role_key ) {
			return \Voxel\Role::get( $role_key );
		}, $this->get_role_keys() ) );
	}

	/**
	 * Get list of roles that this user is allowed to switch to
	 * through the frontend interface.
	 *
	 * @since 1.2
	 * @return \Voxel\Role[]
	 */
	public function get_switchable_roles(): array {
		return array_filter( \Voxel\Role::get_voxel_roles(), function( $role ) {
			if ( ! $role->is_switching_enabled() ) {
				return false;
			}

			if ( in_array( $role->get_key(), $this->get_role_keys() ) ) {
				return false;
			}

			/**
			 * Edge case: Users with a paid plan are not allowed to switch to a role
			 * that does not support any plans. They need to cancel their current plan
			 * for the role to become available.
			 */
			$membership = $this->get_membership();
			if ( $membership->plan->get_key() !== 'default' && ! $role->has_plans_enabled() ) {
				return false;
			}

			return true;
		} );
	}

	public function has_role( $role ): bool {
		return in_array( $role, $this->get_role_keys(), true );
	}

	public function set_role( $role_key, $force = false ) {
		// safeguard admin and editor roles
		if ( ! $force && in_array( $role_key, [ 'administrator', 'editor' ], true ) ) {
			return;
		}

		$this->wp_user->set_role( $role_key );
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

	/**
	 * Plan is considered modifiable if the submission limit of at least one
	 * post type can be modified.
	 *
	 * @since 1.2
	 */
	public function can_modify_current_plan(): bool {
		$membership = $this->get_membership();
		$plan = $membership->plan;

		if ( ! $membership->is_active() ) {
			return false;
		}

		if ( in_array( $membership->get_type(), [ 'subscription', 'payment' ], true ) ) {
			try {
				$price = new \Voxel\Plan_Price( [
					'id' => $membership->get_price_id(),
					'mode' => \Voxel\Stripe::is_test_mode() ? 'test' : 'live',
					'plan' => $plan->get_key(),
				] );
			} catch ( \Exception $e ) {
				return false;
			}

			foreach ( $plan->get_submission_limits() as $post_type_key => $limit_config ) {
				if ( $price->supports_addition( $post_type_key ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Determine if user can modify the post submission limits for
	 * their active plan.
	 *
	 * @since 1.2
	 */
	public function can_modify_limits_for_post_type( $post_type_key ): bool {
		$membership = $this->get_membership();
		$plan = $membership->plan;

		if ( ! $membership->is_active() ) {
			return false;
		}

		if ( in_array( $membership->get_type(), [ 'subscription', 'payment' ], true ) ) {
			try {
				$price = new \Voxel\Plan_Price( [
					'id' => $membership->get_price_id(),
					'mode' => \Voxel\Stripe::is_test_mode() ? 'test' : 'live',
					'plan' => $plan->get_key(),
				] );

				return $price->supports_addition( $post_type_key );
			} catch ( \Exception $e ) {
				//
			}
		}

		return false;
	}

	public function get_submission_limit_for_all_post_types() {
		$membership = $this->get_membership();
		$plan = $membership->plan;
		$post_types = [];
		if ( ! $plan ) {
			return $post_types;
		}

		$limits = $plan->get_submission_limits();
		$additional_limits = $membership->get_additional_limits();
		foreach ( $limits as $post_type_key => $limit ) {
			$post_type = \Voxel\Post_Type::get( $post_type_key );
			if ( ! $post_type ) {
				continue;
			}

			$limit['count'] = absint( $limit['count'] ) + ( $additional_limits[ $post_type->get_key() ] ?? 0 );
			$post_types[ $post_type->get_key() ] = new \Voxel\Submission_Limit( $this, $post_type, $limit, $plan );
		}

		return $post_types;
	}

	public function get_submission_limit_for_post_type( $post_type_key ) {
		$membership = $this->get_membership();
		$plan = $membership->plan;
		if ( ! $plan ) {
			return null;
		}

		$limits = $plan->get_submission_limits();
		$additional_limits = $membership->get_additional_limits();
		if ( ! isset( $limits[ $post_type_key ] ) ) {
			return null;
		}

		$post_type = \Voxel\Post_Type::get( $post_type_key );
		if ( ! $post_type ) {
			return null;
		}

		$limit = $limits[ $post_type_key ];
		$limit['count'] = absint( $limit['count'] ) + ( $additional_limits[ $post_type->get_key() ] ?? 0 );
		return new \Voxel\Submission_Limit( $this, $post_type, $limit, $plan );
	}

	public function can_create_post( string $post_type_key ): bool {
		if ( current_user_can('administrator') || current_user_can('editor') ) {
			return true;
		}

		$limit = $this->get_submission_limit_for_post_type( $post_type_key );
		if ( $limit === null ) {
			return false;
		}

		return $limit->can_create_post();
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
