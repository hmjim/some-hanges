<?php

namespace Voxel\Controllers\Frontend\Membership;

if ( ! defined('ABSPATH') ) {
	exit;
}

class One_Time_Payment_Controller extends \Voxel\Controllers\Base_Controller {

	protected function hooks() {
		$this->on( 'voxel/membership/payment_intent.succeeded', '@one_time_payment_succeeded' );
	}

	protected function one_time_payment_succeeded( $payment_intent ) {
		$plan_key = $payment_intent->metadata['voxel:plan'];
		$plan = \Voxel\Membership\Plan::get( $plan_key );
		if ( ! $plan ) {
			throw new \Exception( sprintf( 'Plan "%s" not found for payment_intent "%s"', $plan_key, $payment_intent->id ) );
		}

		$user = \Voxel\User::get_by_customer_id( $payment_intent->customer );
		if ( ! $user ) {
			throw new \Exception( sprintf( 'Customer ID "%s" does not belong to any registered user (payment_intent "%s")', $payment_intent->customer, $payment_intent->id ) );
		}

		// cancel existing subscription (if any)
		$membership = $user->get_membership();
		if ( $membership->get_type() === 'subscription' && $membership->is_active() ) {
			\Voxel\Stripe::getClient()->subscriptions->cancel( $membership->get_subscription_id() );
		}

		$meta_key = \Voxel\Stripe::is_test_mode() ? 'voxel:test_plan' : 'voxel:plan';
		update_user_meta( $user->get_id(), $meta_key, wp_slash( wp_json_encode( [
			'plan' => $plan->get_key(),
			'type' => 'payment',
			'payment_intent' => $payment_intent->id,
			'amount' => $payment_intent->amount,
			'currency' => $payment_intent->currency,
			'status' => $payment_intent->status,
			'price_id' => $payment_intent->metadata['voxel:price_id'] ?? null,
			'created' => date( 'Y-m-d H:i:s', $payment_intent->created ),
		] ) ) );
		do_action( 'voxel/membership/pricing-plan-updated', $user, $user->get_membership(), $user->get_membership( $refresh_cache = true ) );
	}
}
