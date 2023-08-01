<?php

namespace Voxel\Controllers\Frontend\Orders;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Order_Actions_Controller extends \Voxel\Controllers\Base_Controller {

	protected function hooks() {
		$this->on( 'voxel_ajax_orders.author.approve', '@author_approve_order' );
		$this->on( 'voxel_ajax_orders.author.decline', '@author_decline_order' );
		$this->on( 'voxel_ajax_orders.author.approve_refund', '@author_approve_refund' );
		$this->on( 'voxel_ajax_orders.author.decline_refund', '@author_decline_refund' );

		$this->on( 'voxel_ajax_orders.customer.portal', '@customer_portal' );
		$this->on( 'voxel_ajax_orders.customer.cancel', '@customer_cancel_order' );
		$this->on( 'voxel_ajax_orders.customer.request_refund', '@customer_request_refund' );
		$this->on( 'voxel_ajax_orders.customer.cancel_refund_request', '@customer_cancel_refund_request' );

		$this->on( 'voxel_ajax_orders.customer.subscriptions.reactivate', '@subscriptions_reactivate' );
		$this->on( 'voxel_ajax_orders.customer.subscriptions.retry_payment', '@subscriptions_retry_payment' );
		$this->on( 'voxel_ajax_orders.customer.subscriptions.cancel', '@subscriptions_cancel' );

		$this->on( 'voxel_ajax_orders.receipt', '@get_receipt' );
		$this->on( 'voxel_ajax_orders.apply_tag', '@apply_tag' );
		$this->on( 'voxel_ajax_orders.admin.sync_with_stripe', '@sync_with_stripe' );
		$this->on( 'voxel_ajax_orders.admin.sync_with_stripe_backend', '@sync_with_stripe_backend' );

		$this->on( 'voxel_ajax_orders.get_tag_qr_code', '@get_tag_qr_code' );
	}

	protected function author_approve_order() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( _x( 'Missing order id.', 'orders', 'voxel' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'vendor_id' => get_current_user_id(),
				'status' => \Voxel\Order::STATUS_PENDING_APPROVAL,
			] );

			if ( ! $order ) {
				throw new \Exception( _x( 'Could not find order.', 'orders', 'voxel' ) );
			}

			if ( ! $order->is_catalog_mode() ) {
				$payment_intent = $order->get_object();
				$payment_intent->capture();
			}

			$order->update( 'status', \Voxel\Order::STATUS_COMPLETED );
			$order->note( \Voxel\Order_Note::AUTHOR_APPROVED );

			return wp_send_json( [
				'success' => true,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function author_decline_order() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( _x( 'Missing order id.', 'orders', 'voxel' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'vendor_id' => get_current_user_id(),
				'status' => \Voxel\Order::STATUS_PENDING_APPROVAL,
			] );

			if ( ! $order ) {
				throw new \Exception( _x( 'Could not find order.', 'orders', 'voxel' ) );
			}

			if ( ! $order->is_catalog_mode() ) {
				$stripe = \Voxel\Stripe::getClient();
				$stripe->paymentIntents->cancel( $order->get_object_id() );
			}

			$order->update( 'status', \Voxel\Order::STATUS_DECLINED );
			$order->note( \Voxel\Order_Note::AUTHOR_DECLINED );

			return wp_send_json( [
				'success' => true,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function author_approve_refund() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( _x( 'Missing order id.', 'orders', 'voxel' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'vendor_id' => get_current_user_id(),
				'status' => \Voxel\Order::STATUS_REFUND_REQUESTED,
			] );

			if ( ! $order ) {
				throw new \Exception( _x( 'Could not find order.', 'orders', 'voxel' ) );
			}

			if ( ! $order->is_catalog_mode() ) {
				$has_destination = ! empty( $order->get_details()['destination'] );
				$stripe = \Voxel\Stripe::getClient();
				$stripe->refunds->create( [
					'payment_intent' => $order->get_object_id(),
					'reason' => 'requested_by_customer',
					'refund_application_fee' => $has_destination ? true : false,
					'reverse_transfer' => $has_destination ? true : false,
				] );
			}

			$order->update( 'status', \Voxel\Order::STATUS_REFUNDED );
			$order->note( \Voxel\Order_Note::AUTHOR_REFUND_APPROVED );

			return wp_send_json( [
				'success' => true,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function author_decline_refund() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( _x( 'Missing order id.', 'orders', 'voxel' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'vendor_id' => get_current_user_id(),
				'status' => \Voxel\Order::STATUS_REFUND_REQUESTED,
			] );

			if ( ! $order ) {
				throw new \Exception( _x( 'Could not find order.', 'orders', 'voxel' ) );
			}

			$order->update( 'status', \Voxel\Order::STATUS_COMPLETED );
			$order->note( \Voxel\Order_Note::AUTHOR_REFUND_DECLINED );

			return wp_send_json( [
				'success' => true,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function customer_cancel_order() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( _x( 'Missing order id.', 'orders', 'voxel' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'customer_id' => get_current_user_id(),
				'status' => \Voxel\Order::STATUS_PENDING_APPROVAL,
			] );

			if ( ! $order ) {
				throw new \Exception( _x( 'Could not find order.', 'orders', 'voxel' ) );
			}

			if ( ! $order->is_catalog_mode() ) {
				$stripe = \Voxel\Stripe::getClient();
				$stripe->paymentIntents->cancel( $order->get_object_id(), [
					'cancellation_reason' => 'requested_by_customer',
				] );
			}

			$order->update( 'status', \Voxel\Order::STATUS_CANCELED );
			$order->note( \Voxel\Order_Note::CUSTOMER_CANCELED );

			return wp_send_json( [
				'success' => true,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function customer_request_refund() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( _x( 'Missing order id.', 'orders', 'voxel' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'customer_id' => get_current_user_id(),
				'status' => \Voxel\Order::STATUS_COMPLETED,
			] );

			if ( ! $order ) {
				throw new \Exception( _x( 'Could not find order.', 'orders', 'voxel' ) );
			}

			if ( $order->is_catalog_mode() ) {
				$product_type = $order->get_product_type();
				if ( ! ( $product_type && $product_type->catalog_refunds_allowed() ) ) {
					throw new \Exception( _x( 'Not allowed.', 'orders', 'voxel' ) );
				}
			}

			$order->update( 'status', \Voxel\Order::STATUS_REFUND_REQUESTED );
			$order->note( \Voxel\Order_Note::CUSTOMER_REFUND_REQUESTED );

			return wp_send_json( [
				'success' => true,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function customer_cancel_refund_request() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( _x( 'Missing order id.', 'orders', 'voxel' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'customer_id' => get_current_user_id(),
				'status' => \Voxel\Order::STATUS_REFUND_REQUESTED,
			] );

			if ( ! $order ) {
				throw new \Exception( _x( 'Could not find order.', 'orders', 'voxel' ) );
			}

			$order->update( 'status', \Voxel\Order::STATUS_COMPLETED );
			$order->note( \Voxel\Order_Note::CUSTOMER_REFUND_REQUEST_CANCELED );

			return wp_send_json( [
				'success' => true,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function get_receipt() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( _x( 'Missing order id.', 'orders', 'voxel' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'party_id' => get_current_user_id(),
				'status' => \Voxel\Order::STATUS_COMPLETED,
			] );

			if ( ! $order ) {
				throw new \Exception( _x( 'Could not find order.', 'orders', 'voxel' ) );
			}

			// @todo

			return wp_send_json( [
				// 'pdf' => $pdf,
				'success' => true,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function customer_portal() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( _x( 'Missing order id.', 'orders', 'voxel' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
			] );

			if ( ! $order ) {
				throw new \Exception( _x( 'Could not find order.', 'orders', 'voxel' ) );
			}

			$vendor = $order->get_vendor();
			if ( ! $vendor ) {
				throw new \Exception( _x( 'Could not find vendor.', 'orders', 'voxel' ) );
			}

			$stripe = \Voxel\Stripe::getClient();
			$session = $stripe->billingPortal->sessions->create( [
				'customer' => \Voxel\current_user()->get_stripe_customer_id(),
				'configuration' => \Voxel\Stripe::get_portal_configuration_id(),
				'return_url' => $order->get_link(),
				// 'on_behalf_of' => $vendor->get_stripe_account_id(),
			] );

			return wp_send_json( [
				'redirect_to' => $session->url,
				'success' => true,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function subscriptions_reactivate() {
		try {
			// \Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_reactivate_plan' );
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( _x( 'Missing order id.', 'orders', 'voxel' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'customer_id' => get_current_user_id(),
				'mode' => 'subscription',
			] );

			if ( ! $order ) {
				throw new \Exception( _x( 'Could not find order.', 'orders', 'voxel' ) );
			}

			$stripe = \Voxel\Stripe::getClient();
			$subscription_details = $order->get_object();

			if ( ! ( $subscription_details['cancel_at_period_end'] ?? null ) ) {
				throw new \Exception( _x( 'Request not valid.', 'orders', 'voxel' ) );
			}

			$subscription = \Stripe\Subscription::update( $order->get_object_id(), [
				'cancel_at_period_end' => false,
			] );

			do_action( 'voxel/orders/subscription-updated', $subscription, $order );

			return wp_send_json( [
				'success' => true,
				'message' => _x( 'Subscription has been reactivated.', 'orders', 'voxel' ),
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function subscriptions_retry_payment() {
		try {
			// \Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_retry_payment' );
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( _x( 'Missing order id.', 'orders', 'voxel' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'customer_id' => get_current_user_id(),
				'mode' => 'subscription',
				'status' => [ 'sub_incomplete', 'sub_past_due', 'sub_unpaid' ],
			] );

			if ( ! $order ) {
				throw new \Exception( _x( 'Could not find order.', 'orders', 'voxel' ) );
			}

			$stripe = \Voxel\Stripe::getClient();
			$subscription = $order->get_object();

			if ( $order->get_status() === 'unpaid' ) {
				$stripe->invoices->finalizeInvoice( $subscription->latest_invoice, [
					'auto_advance' => true,
				] );
			}

			$stripe->invoices->pay( $subscription->latest_invoice );

			return wp_send_json( [
				'success' => true,
				'message' => _x( 'Invoice was paid successfully.', 'orders', 'voxel' ),
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function subscriptions_cancel() {
		try {
			// \Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_cancel_plan' );
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( _x( 'Missing order id.', 'orders', 'voxel' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'customer_id' => get_current_user_id(),
				'mode' => 'subscription',
			] );

			if ( ! $order ) {
				throw new \Exception( _x( 'Could not find order.', 'orders', 'voxel' ) );
			}

			$stripe = \Voxel\Stripe::getClient();
			if ( in_array( $order->get_status(), [ 'sub_canceled', 'sub_incomplete_expired' ], true ) ) {
				throw new \Exception( _x( 'Request not valid.', 'orders', 'voxel' ) );
			}

			// @todo: add as setting in wp-admin
			$cancel_behavior = apply_filters( 'voxel/orders/subscription_cancel_behavior', 'at_period_end' );

			if ( $cancel_behavior === 'immediately' ) {
				$subscription = $stripe->subscriptions->cancel( $order->get_object_id() );
				do_action( 'voxel/orders/subscription-updated', $subscription, $order );
			} else {
				$subscription = \Stripe\Subscription::update( $order->get_object_id(), [
					'cancel_at_period_end' => true,
				] );
				do_action( 'voxel/orders/subscription-updated', $subscription, $order );
			}

			return wp_send_json( [
				'success' => true,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function apply_tag() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( _x( 'Missing order id.', 'orders', 'voxel' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'party_id' => get_current_user_id(),
				'status' => \Voxel\Order::STATUS_COMPLETED,
			] );

			if ( ! $order ) {
				throw new \Exception( _x( 'Could not find order.', 'orders', 'voxel' ) );
			}

			if ( ! $order->can_be_tagged_by( get_current_user_id() ) ) {
				throw new \Exception( _x( 'You do not have permission to tag this order.', 'orders', 'voxel' ) );
			}

			$product_type = $order->get_product_type();
			$tag_key = sanitize_text_field( $_GET['tag'] ?? '' );

			$tag = $product_type->get_tag( $tag_key );
			if ( ! $tag ) {
				throw new \Exception( _x( 'Tag not found.', 'orders', 'voxel' ) );
			}

			$details = $order->get_details();
			$details['tag'] = $tag->get_key();
			$order->update( 'details', $details );

			$order->note( \Voxel\current_user()->is_vendor_of(
				$order->get_id() ) ? \Voxel\Order_Note::AUTHOR_APPLIED_TAG : \Voxel\Order_Note::CUSTOMER_APPLIED_TAG,
				[ 'tag' => $tag->get_key() ]
			);

			return wp_send_json( [
				'success' => true,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function get_tag_qr_code() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( _x( 'Missing order id.', 'orders', 'voxel' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'party_id' => get_current_user_id(),
				'status' => \Voxel\Order::STATUS_COMPLETED,
			] );

			if ( ! $order ) {
				throw new \Exception( _x( 'Could not find order.', 'orders', 'voxel' ) );
			}

			if ( ! $order->can_be_tagged_by( get_current_user_id() ) ) {
				throw new \Exception( _x( 'You do not have permission to tag this order.', 'orders', 'voxel' ) );
			}

			$product_type = $order->get_product_type();
			$tag_key = sanitize_text_field( $_GET['tag'] ?? '' );

			$tag = $product_type->get_tag( $tag_key );
			if ( ! ( $tag && $tag->has_qr_code() ) ) {
				throw new \Exception( _x( 'Tag not found.', 'orders', 'voxel' ) );
			}

			$details = $order->get_details();
			$qrcodes = $details['qrcodes'] ?? [];
			if ( ! isset( $qrcodes[ $tag->get_key() ] ) ) {
				$qrcodes[ $tag->get_key() ] = [
					'code' => \Voxel\random_string(16),
					'applied' => false,
				];
			}

			$details['qrcodes'] = $qrcodes;
			$order->update( 'details', $details );

			$request_url = add_query_arg( [
				'order' => $order->get_id(),
				'tag' => $tag->get_key(),
				'code' => $qrcodes[ $tag->get_key() ]['code'],
			], get_permalink( \Voxel\get( 'templates.qr_tags' ) ) ?: home_url('/') );

			$qr = \Voxel\Utils\Vendor\QRCode::getMinimumQRCode( $request_url, QR_ERROR_CORRECT_LEVEL_L );
			$image = $qr->createImage(8, 16);

			$filename = sprintf( 'order(%d)-tag(%s).png', $order->get_id(), sanitize_file_name( $tag->get_key() ) );
			header( 'Content-type: image/png' );
			header( 'Content-Disposition: attachment; filename="'.$filename.'"' );
			imagepng( $image );
			imagedestroy( $image );

		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function sync_with_stripe() {
		try {
			if ( ! \Voxel\current_user()->has_cap('administrator') ) {
				throw new \Exception( _x( 'Permission denied.', 'orders', 'voxel' ) );
			}

			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( _x( 'Missing order id.', 'orders', 'voxel' ) );
			}

			$order = \Voxel\Order::get( $order_id );
			if ( ! $order ) {
				throw new \Exception( _x( 'Could not find order.', 'orders', 'voxel' ) );
			}

			$order->sync_with_stripe();

			return wp_send_json( [
				'success' => true,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function sync_with_stripe_backend() {
		try {
			if ( ! \Voxel\current_user()->has_cap('administrator') ) {
				throw new \Exception( _x( 'Permission denied.', 'orders', 'voxel' ) );
			}

			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( _x( 'Missing order id.', 'orders', 'voxel' ) );
			}

			$order = \Voxel\Order::get( $order_id );
			if ( ! $order ) {
				throw new \Exception( _x( 'Could not find order.', 'orders', 'voxel' ) );
			}

			$order->sync_with_stripe();

			wp_safe_redirect( $order->get_backend_link() );
			exit;
		} catch ( \Exception $e ) {
			return call_user_func( apply_filters( 'wp_die_handler', '_default_wp_die_handler' ), $e->getMessage(), '', [ 'back_link' => true ] );
		}
	}
}
