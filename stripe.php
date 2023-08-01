<?php

namespace Voxel;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Stripe {

	private static $liveClient, $testClient;

	const API_VERSION = '2022-11-15';

	const WEBHOOK_EVENTS = [
		'customer.subscription.created',
		'customer.subscription.deleted',
		'customer.subscription.updated',
		'checkout.session.completed',
		'payment_intent.amount_capturable_updated',
		'payment_intent.canceled',
		'payment_intent.succeeded',
		'charge.refunded',
	];

	const CONNECT_WEBHOOK_EVENTS = [
		'account.updated',
	];

	public static function is_test_mode() {
		return ( !! \Voxel\get( 'settings.stripe.test_mode', true ) ) === true;
	}

	public static function getClient() {
		return static::is_test_mode()
			? static::getTestClient()
			: static::getLiveClient();
	}

	public static function getLiveClient() {
		if ( is_null( static::$liveClient ) ) {
			require_once locate_template( 'app/stripe/library/init.php' );

			\Stripe\Stripe::setApiKey( \Voxel\get( 'settings.stripe.secret', '' ) );
			\Stripe\Stripe::setApiVersion( static::API_VERSION );
			static::$liveClient = new \Stripe\StripeClient( [
				'api_key' => \Voxel\get( 'settings.stripe.secret', '' ),
				'stripe_version' => static::API_VERSION,
			] );
		}

		return static::$liveClient;
	}

	public static function getTestClient() {
		if ( is_null( static::$testClient ) ) {
			require_once locate_template( 'app/stripe/library/init.php' );

			\Stripe\Stripe::setApiKey( \Voxel\get( 'settings.stripe.test_secret', '' ) );
			\Stripe\Stripe::setApiVersion( static::API_VERSION );
			static::$testClient = new \Stripe\StripeClient( [
				'api_key' => \Voxel\get( 'settings.stripe.test_secret', '' ),
				'stripe_version' => static::API_VERSION,
			] );
		}

		return static::$testClient;
	}

	public static function base_dashboard_url( $path = '' ) {
		$url = 'https://dashboard.stripe.com/';
		$path = ltrim( $path, "/\\" );
		return $url.$path;
	}

	public static function dashboard_url( $path = '' ) {
		$url = static::base_dashboard_url();
		if ( static::is_test_mode() ) {
			$url .= 'test/';
		}

		$path = ltrim( $path, "/\\" );
		return $url.$path;
	}

	public static function get_portal_configuration_id() {
		return \Voxel\Stripe::is_test_mode()
			? \Voxel\get( 'settings.stripe.portal.test_config_id' )
			: \Voxel\get( 'settings.stripe.portal.live_config_id' );
	}
}
