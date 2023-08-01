<?php

namespace Voxel\Controllers\Frontend\Membership;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Checkout_Controller extends \Voxel\Controllers\Base_Controller {

	protected function hooks() {
		$this->on( 'voxel_ajax_plans.choose_plan', '@choose_plan' );
		$this->on( 'voxel_ajax_plans.checkout.successful', '@checkout_successful' );
		$this->on( 'voxel/membership/pricing-plan-updated', '@pricing_plan_updated', 10, 3 );
	}

	protected function choose_plan() {
		try {
			\Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_choose_plan' );

			$stripe = \Voxel\Stripe::getClient();
			$user = \Voxel\current_user();
			$membership = $user->get_membership();
			$customer = $user->get_or_create_stripe_customer();
			$price_key = sanitize_text_field( $_GET['plan'] ?? '' );

			$price_id = substr( strrchr( $price_key, '@' ), 1 );
			$plan_key = str_replace( '@'.$price_id, '', $price_key );
			$env = substr( $price_id, 0, 5 ) === 'test:' ? 'test' : 'live';
			$price_id = str_replace( 'test:', '', $price_id );

			$plan = \Voxel\Membership\Plan::get( $plan_key );
			if ( ! $plan ) {
				throw new \Exception( _x( 'Plan does not exist.', 'pricing plans', 'voxel' ) );
			}

			if ( $plan->is_archived() ) {
				throw new \Exception( _x( 'This plan is no longer available.', 'pricing plans', 'voxel' ) );
			}

			// determine redirect url
			$welcome_redirect = wp_validate_redirect( $_REQUEST['redirect_to'] ?? '' );
			if ( ! empty( $_REQUEST['redirect_to'] ) && $welcome_redirect ) {
				if ( \Voxel\get( 'settings.membership.after_registration' ) === 'welcome_step' ) {
					$redirect_to = add_query_arg( [
						'welcome' => '',
						'redirect_to' => $welcome_redirect,
					], get_permalink( \Voxel\get( 'templates.auth' ) ) ?: home_url('/') );
				} else {
					$redirect_to = $welcome_redirect ?: home_url('/');
				}
			} else {
				$redirect_to = get_permalink( \Voxel\get( 'templates.current_plan' ) ) ?: home_url('/');
			}

			/**
			 * Switch user to default plan.
			 * Cancel any active subscriptions.
			 */
			if ( $plan->get_key() === 'default' ) {
				$membership = $user->get_membership();
				if ( $membership->get_type() === 'subscription' && $membership->is_active() ) {
					\Voxel\Stripe::getClient()->subscriptions->cancel( $membership->get_subscription_id() );
				}

				$meta_key = \Voxel\Stripe::is_test_mode() ? 'voxel:test_plan' : 'voxel:plan';
				update_user_meta( $user->get_id(), $meta_key, wp_slash( wp_json_encode( [
					'plan' => 'default',
					'created' => \Voxel\utc()->format( 'Y-m-d H:i:s' ),
				] ) ) );

				do_action( 'voxel/membership/pricing-plan-updated', $user, $user->get_membership(), $user->get_membership( $refresh_cache = true ) );

				return wp_send_json( [
					'success' => true,
					'redirect_to' => $redirect_to,
				] );
			}

			$pricing = $plan->get_pricing();
			if ( empty( $pricing[ $env ] ) || empty( $pricing[ $env ]['prices'][ $price_id ] ) ) {
				throw new \Exception( _x( 'Price does not exist.', 'pricing plans', 'voxel' ) );
			}

			$price = $pricing[ $env ]['prices'][ $price_id ];
			if ( ! $price['active'] ) {
				throw new \Exception( _x( 'Price is not available.', 'pricing plans', 'voxel' ) );
			}

			$payment_mode = $price['type'] === 'recurring' ? 'subscription' : 'payment';

			/**
			 * User is switching from one subscription to another.
			 * Automatically upgrade and prorate, skipping checkout altogether.
			 */
			if ( $membership->get_type() === 'subscription' && $payment_mode === 'subscription' && $membership->is_switchable() ) {
				if ( $membership->get_price_id() === $price_id ) {
					throw new \Exception( _x( 'You are already on this plan.', 'pricing plans', 'voxel' ) );
				}

				$subscription = \Stripe\Subscription::retrieve( $membership->get_subscription_id() );
				$updatedSubscription = \Stripe\Subscription::update( $subscription->id, [
					'items' => [ [
						'id' => $subscription->items->data[0]->id,
						'price' => $price_id,
						'quantity' => 1,
						// @todo: apply_tax_rates
					] ],
					'metadata' => [
						'voxel:payment_for' => 'membership',
						'voxel:plan' => $plan->get_key(),
					],
					'payment_behavior' => apply_filters( 'voxel/update-subscription/payment-behavior', 'allow_incomplete' ),
					'proration_behavior' => \Voxel\get( 'settings.stripe.update.proration_behavior', 'always_invoice' ),
				] );

				do_action( 'voxel/membership/subscription-updated', $updatedSubscription );

				return wp_send_json( [
					'success' => true,
					'redirect_to' => $redirect_to,
				] );
			}

			/**
			 * User is activating a subscription for the first time.
			 * Proceed to checkout.
			 */
			if ( $payment_mode === 'subscription' ) {
				$trial_enabled = \Voxel\get( 'settings.membership.trial.enabled', false );
				$trial_days = absint( \Voxel\get( 'settings.membership.trial.period_days', 0 ) );

				// only allow free trial on first plan sign-up
				$trial_allowed = ! metadata_exists( 'user', $user->get_id(), \Voxel\Stripe::is_test_mode() ? 'voxel:test_plan' : 'voxel:plan' );

				$args = [
					'customer' => $customer->id,
					'mode' => $payment_mode,
					'line_items' => [ [
						'price' => $price_id,
						'quantity' => 1,
					] ],
					'success_url' => add_query_arg( [
						'action' => 'plans.checkout.successful',
						'session_id' => '{CHECKOUT_SESSION_ID}',
						'_wpnonce' => wp_create_nonce('vx_pricing_checkout'),
						'redirect_to' => base64_encode( $redirect_to ),
					], home_url('/?vx=1') ),
					'cancel_url' => add_query_arg( 'canceled', 1, get_permalink( \Voxel\get( 'templates.pricing' ) ) ?: home_url('/') ),
					'subscription_data' => [
						'payment_behavior' => apply_filters( 'voxel/create-subscription/payment-behavior', 'allow_incomplete' ),
						'trial_period_days' => ( $trial_allowed && $trial_enabled && $trial_days ) ? $trial_days : null,
						'metadata' => [
							'voxel:payment_for' => 'membership',
							'voxel:plan' => $plan->get_key(),
						],
					],
					'allow_promotion_codes' => !! \Voxel\get( 'settings.membership.checkout.promotion_codes.enabled' ),
					'customer_update' => [
						'address' => 'auto',
						'name' => 'auto',
					],
				];

				$args = $this->_apply_tax_details( $args );
				$session = \Stripe\Checkout\Session::create( $args );
				update_user_meta( $user->get_id(), 'voxel:tmp_last_session_id', $session->id );

				return wp_send_json( [
					'success' => true,
					'redirect_to' => $session->url,
				] );
			}

			/**
			 * One time payment plan with amount set to 0 (free).
			 * Cancel any existing subscriptions, skip checkout, and apply plan right away.
			 */
			if ( floatval( $price['amount'] ) === floatval(0) ) {
				$membership = $user->get_membership();
				if ( $membership->get_type() === 'subscription' && $membership->is_active() ) {
					\Voxel\Stripe::getClient()->subscriptions->cancel( $membership->get_subscription_id() );
				}

				$meta_key = \Voxel\Stripe::is_test_mode() ? 'voxel:test_plan' : 'voxel:plan';
				update_user_meta( $user->get_id(), $meta_key, wp_slash( wp_json_encode( [
					'plan' => $plan->get_key(),
					'type' => 'payment',
					'amount' => $price['amount'],
					'currency' => $price['currency'],
					'status' => 'succeeded',
					'price_id' => $price_id,
					'created' => \Voxel\utc()->format( 'Y-m-d H:i:s' ),
				] ) ) );

				do_action( 'voxel/membership/pricing-plan-updated', $user, $user->get_membership(), $user->get_membership( $refresh_cache = true ) );

				return wp_send_json( [
					'success' => true,
					'redirect_to' => $redirect_to,
				] );
			}

			/**
			 * One time payment plan with price greater than zero.
			 * Proceed to checkout.
			 */
			$args = [
				'customer' => $customer->id,
				'mode' => $payment_mode,
				'line_items' => [ [
					'price' => $price_id,
					'quantity' => 1,
				] ],
				'success_url' => add_query_arg( [
					'action' => 'plans.checkout.successful',
					'session_id' => '{CHECKOUT_SESSION_ID}',
					'_wpnonce' => wp_create_nonce('vx_pricing_checkout'),
					'redirect_to' => base64_encode( $redirect_to ),
				], home_url('/?vx=1') ),
				'cancel_url' => add_query_arg( 'canceled', 1, get_permalink( \Voxel\get( 'templates.pricing' ) ) ?: home_url('/') ),
				'payment_intent_data' => [
					'metadata' => [
						'voxel:payment_for' => 'membership',
						'voxel:plan' => $plan->get_key(),
						'voxel:price_id' => $price_id,
					],
				],
				'allow_promotion_codes' => !! \Voxel\get( 'settings.membership.checkout.promotion_codes.enabled' ),
				'customer_update' => [
					'address' => 'auto',
					'name' => 'auto',
				],
			];

			$args = $this->_apply_tax_details( $args );
			$session = \Stripe\Checkout\Session::create( $args );
			update_user_meta( $user->get_id(), 'voxel:tmp_last_session_id', $session->id );

			return wp_send_json( [
				'success' => true,
				'redirect_to' => $session->url,
			] );
		} catch ( \Stripe\Exception\ApiErrorException | \Stripe\Exception\InvalidArgumentException $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => __( 'An error occurred.', 'voxel' ),
				'stripe_error' => $e->getMessage(),
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function checkout_successful() {
		$session_id = $_GET['session_id'] ?? null;
		if ( ! ( $session_id && is_user_logged_in() ) ) {
			die;
		}

		$user = \Voxel\current_user();
		$last_session_id = get_user_meta( $user->get_id(), 'voxel:tmp_last_session_id', true );

		// update plan information in case webhook hasn't been triggered yet
		if ( wp_verify_nonce( $_GET['_wpnonce'] ?? '', 'vx_pricing_checkout' ) && $last_session_id === $session_id ) {
			try {
				$stripe = \Voxel\Stripe::getClient();
				$membership = \Voxel\current_user()->get_membership();
				$session = \Voxel\Stripe::getClient()->checkout->sessions->retrieve( $session_id );

				if ( ( $session->mode ?? null ) === 'subscription' ) {
					$subscription = $stripe->subscriptions->retrieve( $session->subscription );
					if ( $subscription ) {
						do_action( 'voxel/membership/subscription-updated', $subscription );
					}
				}

				if ( ( $session->mode ?? null ) === 'payment' ) {
					$payment_intent = $stripe->paymentIntents->retrieve( $session->payment_intent );
					if ( $payment_intent ) {
						do_action( 'voxel/membership/payment_intent.succeeded', $payment_intent );
					}
				}

				delete_user_meta( $user->get_id(), 'voxel:tmp_last_session_id' );
			} catch ( \Exception $e ) {
				//
			}
		}

		$redirect_to = base64_decode( $_REQUEST['redirect_to'] ?? '' );

		wp_safe_redirect( $redirect_to ?: home_url( '/' ) );
		die;
	}

	protected function _apply_tax_details( $args ) {
		$tax_mode = \Voxel\get( 'settings.membership.checkout.tax.mode' );
		if ( $tax_mode === 'auto' ) {
			$args['automatic_tax'] = [
				'enabled' => true,
			];
		} elseif ( $tax_mode === 'manual' ) {
			$tax_rates = \Voxel\Stripe::is_test_mode()
				? (array) \Voxel\get('settings.membership.checkout.tax.manual.test_tax_rates')
				: (array) \Voxel\get('settings.membership.checkout.tax.manual.tax_rates');

			if ( ! empty( $tax_rates ) ) {
				$args['line_items'][0]['tax_rates'] = $tax_rates;
			}
		}

		$args['tax_id_collection'] = [
			'enabled' => !! \Voxel\get( 'settings.membership.checkout.tax.tax_id_collection' ),
		];

		return $args;
	}

	protected function pricing_plan_updated( $user, $old_plan, $new_plan ) {
		if ( $old_plan->get_type() === 'default' && $new_plan->get_type() !== 'default' ) {
			( new \Voxel\Events\Membership\Plan_Activated_Event )->dispatch( $user->get_id() );
		} elseif ( $old_plan->get_type() !== 'default' && $new_plan->get_type() !== 'default' ) {
			( new \Voxel\Events\Membership\Plan_Switched_Event )->dispatch( $user->get_id() );
		}
	}
}
