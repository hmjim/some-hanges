<?php

namespace Voxel\Controllers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Stripe_Controller extends Base_Controller {

	protected function hooks() {
		$this->on( 'voxel_ajax_stripe.account.onboard', '@onboard_account' );
		$this->on( 'voxel_ajax_stripe.account.login', '@access_dashboard' );
		$this->on( 'voxel/connect/account-updated', '@connect_account_updated' );
		$this->on( 'voxel_ajax_stripe.customer.portal', '@access_customer_portal' );
		$this->on( 'voxel_ajax_stripe.webhooks', '@handle_webhooks' );
		$this->on( 'voxel_ajax_nopriv_stripe.webhooks', '@handle_webhooks' );

		$this->on( 'voxel_ajax_stripe.connect_webhooks', '@handle_connect_webhooks' );
		$this->on( 'voxel_ajax_nopriv_stripe.connect_webhooks', '@handle_connect_webhooks' );
	}

	protected function onboard_account() {
		try {
			$user = \Voxel\current_user();
			$stripe = \Voxel\Stripe::getClient();
			$account = $user->get_or_create_stripe_account();

			$onboarding_key = \Voxel\random_string(8);
			update_user_meta( $user->get_id(), 'voxel:onboarding_key', $onboarding_key );

			$link = $stripe->accountLinks->create( [
				'account' => $account->id,
				'refresh_url' => add_query_arg( [
					'vx' => 1,
					'action' => 'stripe.account.onboard',
				], home_url('/') ),
				'return_url' => add_query_arg( 'onboarding_key', $onboarding_key, \Voxel\get_template_link('stripe_account') ),
				'type' => 'account_onboarding',
			] );

			wp_redirect( $link->url );
			die;
		} catch ( \Exception $e ) {
			wp_die( $e->getMessage() );
		}
	}

	protected function access_dashboard() {
		try {
			$user = \Voxel\current_user();
			$stripe = \Voxel\Stripe::getClient();
			$link = $stripe->accounts->createLoginLink( $user->get_stripe_account_id(), [
				'redirect_url' => \Voxel\get_template_link('stripe_account'),
			] );

			wp_redirect( $link->url );
			die;
		} catch ( \Exception $e ) {
			wp_die( $e->getMessage() );
		}
	}

	protected function connect_account_updated( $account ) {
		try {
			$user = \Voxel\User::get_by_account_id( $account->id );
			if ( ! $user ) {
				throw new \Exception( sprintf(
					'The connect account "%s" is not associated with any registered user.',
					$account->id
				) );
			}

			$meta_key = \Voxel\Stripe::is_test_mode() ? 'voxel:test_stripe_account' : 'voxel:stripe_account';
			update_user_meta( $user->get_id(), $meta_key, wp_slash( wp_json_encode( [
				'charges_enabled' => $account->charges_enabled,
				'details_submitted' => $account->details_submitted,
				'payouts_enabled' => $account->payouts_enabled,
			] ) ) );
		} catch ( \Exception $e ) {
			\Voxel\log( $e->getMessage() );
		}
	}

	protected function access_customer_portal() {
		try {
			$stripe = \Voxel\Stripe::getClient();
			$session = $stripe->billingPortal->sessions->create( [
				'customer' => \Voxel\current_user()->get_stripe_customer_id(),
				'configuration' => \Voxel\Stripe::get_portal_configuration_id(),
				'return_url' => get_permalink( \Voxel\get( 'templates.pricing' ) ) ?: home_url('/'),
			] );

			wp_redirect( $session->url );
			die;
		} catch ( \Exception $e ) {
			wp_die( $e->getMessage() );
		}
	}

	protected function handle_webhooks() {
		$stripe = \Voxel\Stripe::getClient();

		if ( \Voxel\get( 'settings.stripe.webhooks.local.enabled' ) ) {
			$endpoint_secret = \Voxel\get( 'settings.stripe.webhooks.local.secret' );
		} else {
			$mode = \Voxel\Stripe::is_test_mode() ? 'test' : 'live';
			$endpoint_secret = \Voxel\get( 'settings.stripe.webhooks.'.$mode.'.secret' );
		}

		$payload = @file_get_contents('php://input');
		$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
		$event = null;

		try {
			$event = \Stripe\Webhook::constructEvent(
				$payload, $sig_header, $endpoint_secret
			);
		} catch( \UnexpectedValueException $e ) {
			// Invalid payload
			http_response_code(400);
			exit();
		} catch( \Stripe\Exception\SignatureVerificationException $e ) {
			// Invalid signature
			http_response_code(400);
			exit();
		}

		try {
			if ( $event->type === 'checkout.session.completed' ) {
				$session = $event->data->object;

				if ( $session->mode === 'subscription' ) {
					$subscription = $stripe->subscriptions->retrieve( $session->subscription );
					$payment_for = $subscription->metadata['voxel:payment_for'];
					if ( $payment_for === 'vendor_product' ) {
						$order = \Voxel\Order::find( [ 'session_id' => $session->id ] );
						if ( $order ) {
							do_action( 'voxel/orders/subscription-updated', $subscription, $order );
						}
					}
				}

				if ( $session->mode === 'payment' ) {
					$payment_intent = $stripe->paymentIntents->retrieve( $session->payment_intent );
					$payment_for = $payment_intent->metadata['voxel:payment_for'];

					if ( $payment_for === 'vendor_product' && ( $order = \Voxel\Order::find( [ 'session_id' => $session->id ] ) ) ) {
						do_action( 'voxel/orders/checkout.session.completed', $session, $payment_intent, $order );
					} else {
						throw new \Exception( sprintf( 'Unknown payment_for "%s" on intent "%s"', $payment_for, $payment_intent->id ) );
					}
				}
			}

			foreach ( [
				'payment_intent.amount_capturable_updated',
				'payment_intent.canceled',
				'payment_intent.succeeded',
			] as $payment_intent_event ) {
				if ( $event->type === $payment_intent_event ) {
					$payment_intent = $event->data->object;
					$payment_for = $payment_intent->metadata['voxel:payment_for'];

					if ( $payment_for === 'vendor_product' ) {
						$order = \Voxel\Order::find( [ 'object_id' => $payment_intent->id ] );
						if ( $order ) {
							do_action( 'voxel/orders/'.$payment_intent_event, $payment_intent, $order );
						}
					} elseif ( $payment_for === 'membership' ) {
						do_action( 'voxel/membership/'.$payment_intent_event, $payment_intent );
					}
				}
			}

			// handle refunds
			if ( $event->type === 'charge.refunded' ) {
				$charge = $event->data->object;
				if ( $charge->payment_intent && ( $order = \Voxel\Order::find( [ 'object_id' => $charge->payment_intent ] ) ) ) {
					do_action( 'voxel/orders/charge.refunded', $charge, $order );
				}
			}

			// subscription created/updated
			if ( $event->type === 'customer.subscription.updated' || $event->type === 'customer.subscription.created' || $event->type === 'customer.subscription.deleted' ) {
				$subscription = $event->data->object;
				$payment_for = $subscription->metadata['voxel:payment_for'];
				if ( $payment_for === 'membership' ) {
					do_action( 'voxel/membership/subscription-updated', $subscription );
				} elseif ( $payment_for === 'vendor_product' ) {
					$order = \Voxel\Order::find( [ 'object_id' => $subscription->id ] );
					if ( $order ) {
						do_action( 'voxel/orders/subscription-updated', $subscription, $order );
					}
				} else {
					throw new \Exception( sprintf( 'Unknown payment_for "%s" on subscription "%s"', $payment_for, $subscription->id ) );
				}
			}

			// subscription deleted
			if ( $event->type === 'customer.subscription.deleted' ) {
				$subscription = $event->data->object;
				$payment_for = $subscription->metadata['voxel:payment_for'];
				if ( $payment_for === 'vendor_product' ) {
					$order = \Voxel\Order::find( [ 'object_id' => $subscription->id ] );
					if ( $order ) {
						do_action( 'voxel/orders/subscription-deleted', $subscription, $order );
					}
				} else {
					throw new \Exception( sprintf( 'Unknown payment_for "%s" on subscription "%s"', $payment_for, $subscription->id ) );
				}
			}

		} catch ( \Exception $e ) {
			\Voxel\log( $e->getMessage() );

			http_response_code(400);
			exit();
		}

		http_response_code(200);
	}

	protected function handle_connect_webhooks() {
		$stripe = \Voxel\Stripe::getClient();

		if ( \Voxel\get( 'settings.stripe.webhooks.local.enabled' ) ) {
			$endpoint_secret = \Voxel\get( 'settings.stripe.webhooks.local.secret' );
		} else {
			$mode = \Voxel\Stripe::is_test_mode() ? 'test' : 'live';
			$endpoint_secret = \Voxel\get( 'settings.stripe.webhooks.'.$mode.'_connect.secret' );
		}

		$payload = @file_get_contents('php://input');
		$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
		$event = null;

		try {
			$event = \Stripe\Webhook::constructEvent(
				$payload, $sig_header, $endpoint_secret
			);
		} catch( \UnexpectedValueException $e ) {
			// Invalid payload
			http_response_code(400);
			exit();
		} catch( \Stripe\Exception\SignatureVerificationException $e ) {
			// Invalid signature
			http_response_code(400);
			exit();
		}

		try {
			// vendor account updated
			if ( $event->type === 'account.updated' ) {
				$account = $event->data->object;
				do_action( 'voxel/connect/account-updated', $account );
			}
		} catch ( \Exception $e ) {
			\Voxel\log( $e->getMessage() );

			http_response_code(400);
			exit();
		}

		http_response_code(200);
	}
}
