<?php

namespace Voxel\Users;

if ( ! defined('ABSPATH') ) {
	exit;
}

trait Vendor_Trait {

	public static function get_by_account_id( $account_id ) {
		$meta_key = \Voxel\Stripe::is_test_mode() ? 'voxel:test_stripe_account_id' : 'voxel:stripe_account_id';
		$results = get_users( [
			'meta_key' => $meta_key,
			'meta_value' => $account_id,
			'number' => 1,
			'fields' => 'ID',
		] );

		return \Voxel\User::get( array_shift( $results ) );
	}

	public function get_stripe_account_id() {
		$meta_key = \Voxel\Stripe::is_test_mode() ? 'voxel:test_stripe_account_id' : 'voxel:stripe_account_id';
		return get_user_meta( $this->get_id(), $meta_key, true );
	}

	public function get_stripe_account() {
		$account_id = $this->get_stripe_account_id();
		if ( empty( $account_id ) ) {
			throw new \Exception( _x( 'Stripe account not set up for this user.', 'orders', 'voxel' ) );
		}

		$stripe = \Voxel\Stripe::getClient();
		return $stripe->accounts->retrieve( $account_id );
	}

	public function get_or_create_stripe_account() {
		try {
			$account = $this->get_stripe_account();
		} catch ( \Exception $e ) {
			$stripe = \Voxel\Stripe::getClient();
			$account = $stripe->accounts->create( [
				'type' => 'express',
				'email' => $this->get_email(),
				/*'capabilities' => [
					'card_payments' => ['requested' => true],
					'transfers' => ['requested' => true],
				],*/
			] );

			$meta_key = \Voxel\Stripe::is_test_mode() ? 'voxel:test_stripe_account_id' : 'voxel:stripe_account_id';
			update_user_meta( $this->get_id(), $meta_key, $account->id );
			do_action( 'voxel/connect/account-updated', $account );
		}

		return $account;
	}

	public function get_stripe_account_details() {
		if ( ! is_null( $this->account_details ) ) {
			return $this->account_details;
		}

		$account_id = $this->get_stripe_account_id();
		$meta_key = \Voxel\Stripe::is_test_mode() ? 'voxel:test_stripe_account' : 'voxel:stripe_account';
		$details = (array) json_decode( get_user_meta( $this->get_id(), $meta_key, true ), ARRAY_A );

		$this->account_details = (object) [
			'exists' => ! empty( $account_id ),
			'id' => $account_id,
			'charges_enabled' => $details['charges_enabled'] ?? false,
			'details_submitted' => $details['details_submitted'] ?? false,
			'payouts_enabled' => $details['payouts_enabled'] ?? false,
		];

		return $this->account_details;
	}

	public function is_vendor_of( $order_id ): bool {
		$order = \Voxel\Order::get( $order_id );
		return $order->get_vendor_id() === $this->get_id();
	}

	public function get_vendor_stats() {
		if ( ! is_null( $this->vendor_stats ) ) {
			return $this->vendor_stats;
		}

		$this->vendor_stats = new \Voxel\Product_Types\Vendor_Stats( $this );
		return $this->vendor_stats;
	}

}
