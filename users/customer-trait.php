<?php

namespace Voxel\Users;

if ( ! defined('ABSPATH') ) {
	exit;
}

trait Customer_Trait {

	public static function get_by_customer_id( $customer_id ) {
		$meta_key = \Voxel\Stripe::is_test_mode() ? 'voxel:test_stripe_customer_id' : 'voxel:stripe_customer_id';
		$results = get_users( [
			'meta_key' => $meta_key,
			'meta_value' => $customer_id,
			'number' => 1,
			'fields' => 'ID',
		] );

		return \Voxel\User::get( array_shift( $results ) );
	}

	public function get_stripe_customer_id() {
		$meta_key = \Voxel\Stripe::is_test_mode() ? 'voxel:test_stripe_customer_id' : 'voxel:stripe_customer_id';
		return get_user_meta( $this->get_id(), $meta_key, true );
	}

	public function get_stripe_customer() {
		$customer_id = $this->get_stripe_customer_id();
		if ( empty( $customer_id ) ) {
			throw new \Exception( _x( 'Stripe customer account not set up for this user.', 'orders', 'voxel' ) );
		}

		$stripe = \Voxel\Stripe::getClient();
		return $stripe->customers->retrieve( $customer_id );
	}

	public function get_or_create_stripe_customer() {
		try {
			$customer = $this->get_stripe_customer();
		} catch ( \Exception $e ) {
			$stripe = \Voxel\Stripe::getClient();
			$customer = $stripe->customers->create( [
				'email' => $this->get_email(),
				'name' => $this->get_display_name(),
			] );

			$meta_key = \Voxel\Stripe::is_test_mode() ? 'voxel:test_stripe_customer_id' : 'voxel:stripe_customer_id';
			update_user_meta( $this->get_id(), $meta_key, $customer->id );
		}

		return $customer;
	}

	public function is_customer_of( $order_id ): bool {
		$order = \Voxel\Order::get( $order_id );
		$customer = $order->get_customer();
		return $customer && $customer->get_id() === $this->get_id();
	}

}
