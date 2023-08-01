<?php

namespace Voxel\Controllers\Frontend\Orders;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Checkout_Controller extends \Voxel\Controllers\Base_Controller {

	private $post, $field, $product_type, $product_config;

	protected function hooks() {
		$this->on( 'voxel_ajax_checkout', '@handle' );
		$this->on( 'voxel_ajax_stripe.checkout.successful', '@checkout_successful' );
		$this->on( 'voxel_ajax_stripe.checkout.canceled', '@checkout_canceled' );
	}

	protected function handle() {
		try {
			$post_id = $_GET['post_id'] ?? null;
			$field_key = $_GET['field_key'] ?? null;
			$customer_id = get_current_user_id();

			$session = new \Voxel\Checkout_Session( [
				'customer_id' => $customer_id,
				'post_id' => $post_id,
				'field_key' => $field_key,
			] );

			if ( $session->product_type->get_product_mode() === 'booking' ) {
				$booking_details = json_decode( stripslashes( $_POST['booking'] ?? '' ), true );
				$session->set_booking_details( $booking_details );
			}

			$additions = json_decode( stripslashes( $_POST['additions'] ?? '' ), true );
			$session->set_additions( $additions );

			$custom_additions = json_decode( stripslashes( $_POST['custom_additions'] ?? '' ), true );
			$session->set_custom_additions( $custom_additions );

			$information_fields = json_decode( stripslashes( $_POST['fields'] ?? '' ), true );
			$session->set_information_fields( $information_fields );

			if ( $session->product_type->is_catalog_mode() ) {
				$default_tag = $session->product_type->get_default_tag();
				$order = \Voxel\Order::create( apply_filters( 'voxel/checkout/order-args', [
					'post_id' => $session->post->get_id(),
					'product_type' => $session->product_type->get_key(),
					'product_key' => $session->field->get_key(),
					'customer_id' => $session->customer->get_id(),
					'vendor_id' => $session->vendor->get_id(),
					'status' => $session->product_type->catalog_requires_approval() ? \Voxel\Order::STATUS_PENDING_APPROVAL : \Voxel\Order::STATUS_COMPLETED,
					'catalog_mode' => true,
					'mode' => $session->product_type->get_payment_mode(),
					'details' => array_filter( [
						'booking' => ! empty( $session->booking ) ? $session->booking : null,
						'additions' => ! empty( $session->additions ) ? $session->additions : null,
						'custom_additions' => ! empty( $session->custom_additions ) ? $session->custom_additions : null,
						'fields' => ! empty( $session->information_fields ) ? $session->information_fields : null,
						'pricing' => $session->get_pricing(),
						'tag' => $default_tag ? $default_tag->get_key() : null,
					] ),
				] ) );

				// claims: transfer post ownership from vendor to customer if no approval is required in catalog mode
				if ( $session->product_type->get_product_mode() === 'claim' && ! $session->product_type->catalog_requires_approval() ) {
					wp_update_post( [
						'ID' => $session->post->get_id(),
						'post_author' => $session->customer->get_id(),
					] );
					$session->post->set_verified(true);
					\Voxel\cache_user_post_stats( $session->customer->get_id() );
				}

				( new \Voxel\Events\Orders\Customer_Order_Placed_Event )->dispatch( $order->get_id() );
				if ( $product_type = $order->get_product_type() ) {
					( new \Voxel\Events\Orders\Customer_Order_Placed_Event( $product_type ) )->dispatch( $order->get_id() );
				}

				return wp_send_json( [
					'success' => true,
					'redirect_url' => $order->get_link(),
				] );
			} else {
				$stripe_session = $session->checkout();
				$order = \Voxel\Order::create( apply_filters( 'voxel/checkout/order-args', [
					'post_id' => $session->post->get_id(),
					'product_type' => $session->product_type->get_key(),
					'product_key' => $session->field->get_key(),
					'customer_id' => $session->customer->get_id(),
					'vendor_id' => $session->vendor->get_id(),
					'status' => \Voxel\Order::STATUS_PENDING_PAYMENT,
					'session_id' => $stripe_session->id,
					'mode' => $session->product_type->get_payment_mode(),
					'details' => array_filter( [
						'booking' => ! empty( $session->booking ) ? $session->booking : null,
						'additions' => ! empty( $session->additions ) ? $session->additions : null,
						'custom_additions' => ! empty( $session->custom_additions ) ? $session->custom_additions : null,
						'fields' => ! empty( $session->information_fields ) ? $session->information_fields : null,
						'pricing' => $session->get_pricing(),
						'checkout' => \Voxel\Order::get_session_details( $stripe_session ),
						'destination' => $session->destination->id ?? null,
					] ),
				] ) );

				return wp_send_json( [
					'success' => true,
					'message' => _x( 'Redirecting to checkout...', 'checkout', 'voxel' ),
					'redirect_url' => $stripe_session->url,
				] );
			}
		} catch ( \Stripe\Exception\ApiErrorException | \Stripe\Exception\InvalidArgumentException $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => _x( 'Something went wrong', 'checkout', 'voxel' ),
				'debug' => [
					'type' => 'stripe_error',
					'code' => method_exists( $e, 'getStripeCode' ) ? $e->getStripeCode() : $e->getCode(),
					'message' => $e->getMessage(),
				],
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function checkout_successful() {
		$session_id = $_REQUEST['session_id'] ?? null;
		if ( ! $session_id ) {
			die;
		}

		$order = \Voxel\Order::find( [
			'session_id' => $session_id,
			'customer_id' => get_current_user_id(),
		] );

		if ( $order ) {
			try {
				// sync order with stripe in case the `checkout.session.completed` webhook hasn't fired yet
				if ( $order->get_status() === 'pending_payment' ) {
					$order->sync_with_stripe();
				}
			} catch ( \Exception $e ) {
				\Voxel\log( $e->getMessage() );
			}

			wp_safe_redirect( $order->get_link() );
			die;
		}

		wp_safe_redirect( home_url( '/' ) );
		die;
	}

	protected function checkout_canceled() {
		$session_id = $_REQUEST['session_id'] ?? null;
		if ( ! ( $session_id ) ) {
			die;
		}

		$order = \Voxel\Order::find( [
			'session_id' => $session_id,
			'customer_id' => get_current_user_id(),
		] );

		if ( $order ) {
			$order->delete();
		}

		wp_safe_redirect( \Voxel\get_redirect_url() );
		exit;
	}
}
