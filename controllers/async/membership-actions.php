<?php

namespace Voxel\Controllers\Async;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Membership_Actions extends \Voxel\Controllers\Base_Controller {

	protected function authorize() {
		return current_user_can( 'manage_options' );
	}

	protected function hooks() {
		$this->on( 'voxel_ajax_membership.update_plan', '@update_plan' );
		$this->on( 'voxel_ajax_membership.archive_plan', '@archive_plan' );
		$this->on( 'voxel_ajax_membership.delete_plan', '@delete_plan' );
		$this->on( 'voxel_ajax_membership.create_price', '@create_price' );
		$this->on( 'voxel_ajax_membership.sync_prices', '@sync_prices' );
		$this->on( 'voxel_ajax_membership.toggle_price', '@toggle_price' );
		$this->on( 'voxel_ajax_membership.setup_pricing', '@setup_pricing' );

		// admin as customer actions
		$this->on( 'voxel_ajax_orders.admin.cancel', '@admin_cancel_order' );
		$this->on( 'voxel_ajax_orders.admin.request_refund', '@admin_request_refund' );
		$this->on( 'voxel_ajax_orders.admin.cancel_refund_request', '@admin_cancel_refund_request' );

		// admin as vendor actions
		$this->on( 'voxel_ajax_orders.admin.approve', '@admin_approve_order' );
		$this->on( 'voxel_ajax_orders.admin.decline', '@admin_decline_order' );
		$this->on( 'voxel_ajax_orders.admin.approve_refund', '@admin_approve_refund' );
		$this->on( 'voxel_ajax_orders.admin.decline_refund', '@admin_decline_refund' );
		$this->on( 'voxel_ajax_orders.admin.apply_tag', '@admin_apply_tag' );
		$this->on( 'voxel_ajax_orders.admin.delete_note', '@admin_delete_note' );
	}

	protected function update_plan() {
		try {
			$data = $_POST['plan'] ?? [];
			$key = sanitize_text_field( trim( $data['key'] ?? '' ) );
			$plan = \Voxel\Membership\Plan::get( $key );
			if ( ! $plan ) {
				throw new \Exception( __( 'Plan not found.', 'voxel-backend' ) );
			}

			$submissions = [];
			foreach ( (array) ( $data['submissions'] ?? [] ) as $post_type_key => $post_type_limit ) {
				if ( post_type_exists( $post_type_key ) ) {
					$submissions[ $post_type_key ] = absint( $post_type_limit );
				}
			}

			$plan->update( [
				'label' => sanitize_text_field( trim( $data['label'] ) ),
				'description' => sanitize_textarea_field( $data['description'] ),
				'submissions' => $submissions,
			] );

			return wp_send_json( [
				'success' => true,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'errors' => [ $e->getMessage() ],
			] );
		}
	}

	protected function archive_plan() {
		try {
			$data = $_POST['plan'] ?? [];
			$key = sanitize_text_field( trim( $data['key'] ?? '' ) );
			$plan = \Voxel\Membership\Plan::get( $key );
			if ( ! $plan ) {
				throw new \Exception( __( 'Plan not found.', 'voxel-backend' ) );
			}

			$plan->update( 'archived', ! $plan->is_archived() );

			return wp_send_json( [
				'success' => true,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'errors' => [ $e->getMessage() ],
			] );
		}
	}

	protected function delete_plan() {
		try {
			$data = $_POST['plan'] ?? [];
			$key = sanitize_text_field( trim( $data['key'] ?? '' ) );
			$plan = \Voxel\Membership\Plan::get( $key );
			if ( ! $plan ) {
				throw new \Exception( __( 'Plan not found.', 'voxel-backend' ) );
			}

			$plans = \Voxel\get( 'plans' );
			unset( $plans[ $plan->get_key() ] );
			\Voxel\set( 'plans', $plans );

			return wp_send_json( [
				'redirect_to' => admin_url( 'admin.php?page=voxel-membership' ),
				'success' => true,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'errors' => [ $e->getMessage() ],
			] );
		}
	}

	protected function create_price() {
		try {
			$plan = \Voxel\Membership\Plan::get( $_POST['plan'] );
			if ( ! $plan ) {
				throw new \Exception( __( 'Plan not found.', 'voxel-backend' ) );
			}

			$pricing = $plan->get_pricing();
			$mode = ( $_POST['mode'] ?? 'test' ) === 'test' ? 'test' : 'live';
			$client = ( $mode === 'live' )
				? \Voxel\Stripe::getLiveClient()
				: \Voxel\Stripe::getTestClient();

			// create stripe product if it doesn't exist
			if ( empty( $pricing[ $mode ] ) ) {
				$args = [
					'name' => $plan->get_label(),
					'metadata' => [
						'product_type' => 'membership\plan',
					],
				];

				if ( ! empty( $plan->get_description() ) ) {
					$args['description'] = $plan->get_description();
				}

				$product = $client->products->create( $args );

				$pricing[ $mode ] = [
					'product_id' => $product->id,
					'prices' => [],
				];

				$plan->update( 'pricing', $pricing );
			}

			$product_id = $pricing[ $mode ]['product_id'];
			$data = $_POST['price'] ?? [];
			$amount = isset( $data['amount'] ) ? abs( $data['amount'] ) : null;
			$currency = isset( $data['currency'] ) ? sanitize_text_field( $data['currency'] ) : null;
			$type = isset( $data['type'] ) ? sanitize_text_field( $data['type'] ) : null;
			$interval = isset( $data['interval'] ) ? sanitize_text_field( $data['interval'] ) : null;
			$intervalCount = isset( $data['intervalCount'] ) ? absint( $data['intervalCount'] ) : null;
			$tax_behavior = ( ! empty( $data['includeTax'] ?? null ) && $data['includeTax'] !== 'false' ) ? 'inclusive' : 'exclusive';

			if ( $currency === null || $amount === null ) {
				throw new \Exception( __( 'Please provide an amount and a currency.', 'voxel-backend' ) );
			}

			if ( ! \Voxel\Stripe\Currencies::is_zero_decimal( $currency ) ) {
				$amount *= 100;
			}

			$args = [
				'currency' => $currency,
				'product' => $product_id,
				'active' => true,
				'unit_amount' => $amount,
				'tax_behavior' => $tax_behavior,
				'metadata' => [
					'pricing_type' => 'membership_pricing',
				],
			];

			if ( $type === 'recurring' ) {
				$args['recurring'] = [
					'interval' => $interval,
					'interval_count' => $intervalCount,
				];
			}

			$price = $client->prices->create( $args );

			$pricing[ $mode ]['prices'][ $price->id ] = [
				'currency' => $price->currency,
				'type' => $price->type,
				'amount' => $price->unit_amount,
				'active' => $price->active,
				'tax_behavior' => $price->tax_behavior,
			];

			if ( $price->type === 'recurring' ) {
				$pricing[ $mode ]['prices'][ $price->id ]['recurring'] = [
					'interval' => $price->recurring->interval,
					'interval_count' => $price->recurring->interval_count,
				];
			}

			$plan->update( 'pricing', $pricing );

			return wp_send_json( [
				'success' => true,
				'pricing' => $plan->get_editor_config()['pricing'],
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'errors' => [ $e->getMessage() ],
			] );
		}
	}

	protected function sync_prices() {
		try {
			$plan = \Voxel\Membership\Plan::get( $_GET['plan'] );
			if ( ! $plan ) {
				throw new \Exception( __( 'Plan not found.', 'voxel-backend' ) );
			}

			$pricing = $plan->get_pricing();
			$mode = ( $_GET['mode'] ?? 'test' ) === 'test' ? 'test' : 'live';
			$client = ( $mode === 'live' )
				? \Voxel\Stripe::getLiveClient()
				: \Voxel\Stripe::getTestClient();

			$product_id = $pricing[ $mode ]['product_id'];
			$prices = $client->prices->all( [
				'product' => $product_id,
				'limit' => 100,
			] );

			$pricing[ $mode ]['prices'] = [];
			foreach ( $prices->data as $price) {
				$pricing[ $mode ]['prices'][ $price->id ] = [
					'currency' => $price->currency,
					'type' => $price->type,
					'amount' => $price->unit_amount,
					'active' => $price->active,
					'tax_behavior' => $price->tax_behavior,
				];

				if ( $price->type === 'recurring' ) {
					$pricing[ $mode ]['prices'][ $price->id ]['recurring'] = [
						'interval' => $price->recurring->interval,
						'interval_count' => $price->recurring->interval_count,
					];
				}
			}

			$plan->update( 'pricing', $pricing );

			return wp_send_json( [
				'success' => true,
				'pricing' => $plan->get_editor_config()['pricing'],
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'errors' => [ $e->getMessage() ],
			] );
		}
	}

	protected function toggle_price() {
		try {
			$plan = \Voxel\Membership\Plan::get( $_GET['plan'] );
			if ( ! $plan ) {
				throw new \Exception( __( 'Plan not found.', 'voxel-backend' ) );
			}

			$pricing = $plan->get_pricing();
			$mode = ( $_GET['mode'] ?? 'test' ) === 'test' ? 'test' : 'live';
			$priceId = sanitize_text_field( $_GET['price'] ?? null );

			if ( empty( $pricing[ $mode ]['prices'][ $priceId ] ) ) {
				throw new \Exception( __( 'Price not found.', 'voxel-backend' ) );
			}

			$client = ( $mode === 'live' )
				? \Voxel\Stripe::getLiveClient()
				: \Voxel\Stripe::getTestClient();

			$isActive = (bool) $pricing[ $mode ]['prices'][ $priceId ]['active'];
			$prices = $client->prices->update( $priceId, [
				'active' => ! $isActive,
			] );

			$pricing[ $mode ]['prices'][ $priceId ]['active'] = ! $isActive;
			$plan->update( 'pricing', $pricing );

			return wp_send_json( [
				'success' => true,
				'pricing' => $plan->get_editor_config()['pricing'],
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'errors' => [ $e->getMessage() ],
			] );
		}
	}

	protected function setup_pricing() {
		try {
			$plan = \Voxel\Membership\Plan::get( $_GET['plan'] );
			if ( ! $plan ) {
				throw new \Exception( __( 'Plan not found.', 'voxel-backend' ) );
			}

			$pricing = $plan->get_pricing();
			$mode = ( $_GET['mode'] ?? 'test' ) === 'test' ? 'test' : 'live';
			$client = ( $mode === 'live' )
				? \Voxel\Stripe::getLiveClient()
				: \Voxel\Stripe::getTestClient();

			$createProduct = function() use ( $plan, $pricing, $client, $mode ) {
				$args = [
					'name' => $plan->get_label(),
					'metadata' => [
						'product_type' => 'membership\plan',
					],
				];

				if ( ! empty( $plan->get_description() ) ) {
					$args['description'] = $plan->get_description();
				}

				$product = $client->products->create( $args );

				$pricing[ $mode ] = [
					'product_id' => $product->id,
					'prices' => [],
				];

				$plan->update( 'pricing', $pricing );

				return $product;
			};

			$product_id = $pricing[ $mode ]['product_id'] ?? null;

			if ( ! empty( $product_id ) ) {
				try {
					$product = $client->products->retrieve( $product_id );
					// \Voxel\log('product id exists and retrieved');
				} catch ( \Stripe\Exception\ApiErrorException $e ) {
					if ( $e->getStripeCode() === 'resource_missing' ) {
						$product = $createProduct();
						// \Voxel\log('product id exists but retrieval failed');
					}
				}
			} else {
				// \Voxel\log('product id does not exist');
				$product = $createProduct();
			}

			return wp_send_json( [
				'success' => true,
				'product_id' => $product->id,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function admin_cancel_order() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( __( 'Missing order id.', 'voxel-backend' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'status' => \Voxel\Order::STATUS_PENDING_APPROVAL,
			] );

			if ( ! $order ) {
				throw new \Exception( __( 'Could not find order.', 'voxel-backend' ) );
			}

			if ( ! $order->is_catalog_mode() ) {
				$stripe = \Voxel\Stripe::getClient();
				$stripe->paymentIntents->cancel( $order->get_object_id(), [
					'cancellation_reason' => 'requested_by_customer',
				] );
			}

			$order->update( 'status', \Voxel\Order::STATUS_CANCELED );
			if ( $order->get_vendor_id() === get_current_user_id() ) {
				$order->note( \Voxel\Order_Note::CUSTOMER_CANCELED );
			}

			wp_safe_redirect( $order->get_backend_link() );
			exit;
		} catch ( \Exception $e ) {
			return call_user_func( apply_filters( 'wp_die_handler', '_default_wp_die_handler' ), $e->getMessage(), '', [ 'back_link' => true ] );
		}
	}

	protected function admin_request_refund() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( __( 'Missing order id.', 'voxel-backend' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'status' => \Voxel\Order::STATUS_COMPLETED,
			] );

			if ( ! $order ) {
				throw new \Exception( __( 'Could not find order.', 'voxel-backend' ) );
			}

			if ( $order->is_catalog_mode() ) {
				$product_type = $order->get_product_type();
				if ( ! ( $product_type && $product_type->catalog_refunds_allowed() ) ) {
					throw new \Exception( __( 'Not allowed.', 'voxel-backend' ) );
				}
			}

			$order->update( 'status', \Voxel\Order::STATUS_REFUND_REQUESTED );
			if ( $order->get_vendor_id() === get_current_user_id() ) {
				$order->note( \Voxel\Order_Note::CUSTOMER_REFUND_REQUESTED );
			}

			wp_safe_redirect( $order->get_backend_link() );
			exit;
		} catch ( \Exception $e ) {
			return call_user_func( apply_filters( 'wp_die_handler', '_default_wp_die_handler' ), $e->getMessage(), '', [ 'back_link' => true ] );
		}
	}

	protected function admin_cancel_refund_request() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( __( 'Missing order id.', 'voxel-backend' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'status' => \Voxel\Order::STATUS_REFUND_REQUESTED,
			] );

			if ( ! $order ) {
				throw new \Exception( __( 'Could not find order.', 'voxel-backend' ) );
			}

			$order->update( 'status', \Voxel\Order::STATUS_COMPLETED );
			if ( $order->get_vendor_id() === get_current_user_id() ) {
				$order->note( \Voxel\Order_Note::CUSTOMER_REFUND_REQUEST_CANCELED );
			}

			wp_safe_redirect( $order->get_backend_link() );
			exit;
		} catch ( \Exception $e ) {
			return call_user_func( apply_filters( 'wp_die_handler', '_default_wp_die_handler' ), $e->getMessage(), '', [ 'back_link' => true ] );
		}
	}

	public function admin_approve_order() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( __( 'Missing order id.', 'voxel-backend' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'status' => \Voxel\Order::STATUS_PENDING_APPROVAL,
			] );

			if ( ! $order ) {
				throw new \Exception( __( 'Could not find order.', 'voxel-backend' ) );
			}

			if ( ! $order->is_catalog_mode() ) {
				$payment_intent = $order->get_object();
				$payment_intent->capture();
			}

			$order->update( 'status', \Voxel\Order::STATUS_COMPLETED );
			if ( $order->get_vendor_id() === get_current_user_id() ) {
				$order->note( \Voxel\Order_Note::AUTHOR_APPROVED );
			}

			wp_safe_redirect( $order->get_backend_link() );
			exit;
		} catch ( \Exception $e ) {
			return call_user_func( apply_filters( 'wp_die_handler', '_default_wp_die_handler' ), $e->getMessage(), '', [ 'back_link' => true ] );
		}
	}

	public function admin_decline_order() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( __( 'Missing order id.', 'voxel-backend' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'status' => \Voxel\Order::STATUS_PENDING_APPROVAL,
			] );

			if ( ! $order ) {
				throw new \Exception( __( 'Could not find order.', 'voxel-backend' ) );
			}

			if ( ! $order->is_catalog_mode() ) {
				$stripe = \Voxel\Stripe::getClient();
				$stripe->paymentIntents->cancel( $order->get_object_id() );
			}

			$order->update( 'status', \Voxel\Order::STATUS_DECLINED );
			if ( $order->get_vendor_id() === get_current_user_id() ) {
				$order->note( \Voxel\Order_Note::AUTHOR_DECLINED );
			}

			wp_safe_redirect( $order->get_backend_link() );
			exit;
		} catch ( \Exception $e ) {
			return call_user_func( apply_filters( 'wp_die_handler', '_default_wp_die_handler' ), $e->getMessage(), '', [ 'back_link' => true ] );
		}
	}

	public function admin_approve_refund() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( __( 'Missing order id.', 'voxel-backend' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'status' => \Voxel\Order::STATUS_REFUND_REQUESTED,
			] );

			if ( ! $order ) {
				throw new \Exception( __( 'Could not find order.', 'voxel-backend' ) );
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
			if ( $order->get_vendor_id() === get_current_user_id() ) {
				$order->note( \Voxel\Order_Note::AUTHOR_REFUND_APPROVED );
			}

			wp_safe_redirect( $order->get_backend_link() );
			exit;
		} catch ( \Exception $e ) {
			return call_user_func( apply_filters( 'wp_die_handler', '_default_wp_die_handler' ), $e->getMessage(), '', [ 'back_link' => true ] );
		}
	}

	public function admin_decline_refund() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( __( 'Missing order id.', 'voxel-backend' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'status' => \Voxel\Order::STATUS_REFUND_REQUESTED,
			] );

			if ( ! $order ) {
				throw new \Exception( __( 'Could not find order.', 'voxel-backend' ) );
			}

			$order->update( 'status', \Voxel\Order::STATUS_COMPLETED );
			if ( $order->get_vendor_id() === get_current_user_id() ) {
				$order->note( \Voxel\Order_Note::AUTHOR_REFUND_DECLINED );
			}

			wp_safe_redirect( $order->get_backend_link() );
			exit;
		} catch ( \Exception $e ) {
			return call_user_func( apply_filters( 'wp_die_handler', '_default_wp_die_handler' ), $e->getMessage(), '', [ 'back_link' => true ] );
		}
	}

	protected function admin_apply_tag() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( __( 'Missing order id.', 'voxel-backend' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'status' => \Voxel\Order::STATUS_COMPLETED,
			] );

			if ( ! $order ) {
				throw new \Exception( __( 'Could not find order.', 'voxel-backend' ) );
			}

			$product_type = $order->get_product_type();
			$tag_key = sanitize_text_field( $_GET['tag'] ?? '' );
			$tag = $product_type->get_tag( $tag_key );
			if ( ! $tag ) {
				throw new \Exception( __( 'Tag not found.', 'voxel-backend' ) );
			}

			$details = $order->get_details();
			$details['tag'] = $tag->get_key();
			$order->update( 'details', $details );

			if ( $order->get_vendor_id() === get_current_user_id() ) {
				$order->note( \Voxel\Order_Note::AUTHOR_APPLIED_TAG, [ 'tag' => $tag->get_key() ] );
			}

			wp_safe_redirect( $order->get_backend_link() );
			exit;
		} catch ( \Exception $e ) {
			return call_user_func( apply_filters( 'wp_die_handler', '_default_wp_die_handler' ), $e->getMessage(), '', [ 'back_link' => true ] );
		}
	}

	protected function admin_delete_note() {
		try {
			$note_id = absint( $_GET['note_id'] ?? null );
			if ( ! $note_id ) {
				throw new \Exception( __( 'Missing comment id.', 'voxel-backend' ) );
			}

			$note = \Voxel\Order_Note::get( $note_id );
			if ( ! $note ) {
				throw new \Exception( __( 'Could not find note.', 'voxel-backend' ) );
			}

			$order = $note->get_order();
			$note->delete();

			wp_safe_redirect( $order->get_backend_link() );
			exit;
		} catch ( \Exception $e ) {
			return call_user_func( apply_filters( 'wp_die_handler', '_default_wp_die_handler' ), $e->getMessage(), '', [ 'back_link' => true ] );
		}
	}
}
