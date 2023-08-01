<?php

namespace Voxel\Controllers\Frontend\Orders;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Single_Order_Controller extends \Voxel\Controllers\Base_Controller {

	protected function hooks() {
		$this->on( 'voxel_ajax_orders.view', '@view_order' );
	}

	protected function view_order() {
		try {
			$user = \Voxel\current_user();
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( _x( 'Missing order id.', 'orders', 'voxel' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'party_id' => get_current_user_id(),
			] );

			if ( ! $order ) {
				throw new \Exception( _x( 'Could not find order.', 'orders', 'voxel' ) );
			}

			$customer = $order->get_customer();
			$post = $order->get_post();
			$product_type = $order->get_product_type();

			$data = [
				'id' => $order->get_id(),
				'mode' => $order->get_mode(),
				'is_catalog_mode' => $order->is_catalog_mode(),
				'time' => $order->get_time_for_display(),
				'status' => [
					'slug' => $order->get_status(),
					'label' => $order->get_status_label(),
				],
				'customer' => [
					'name' => $order->get_customer_name_for_display(),
					'avatar' => $customer ? $customer->get_avatar_markup() : null,
					'link' => $customer ? $customer->get_link() : null,
				],
				'post' => [
					'title' => $order->get_post_title_for_display(),
					'link' => $post ? $post->get_link() : null,
				],
				'product_type' => [
					'label' => $product_type ? $product_type->get_label() : null,
				],
				'subscription' => $order->get_subscription_details(),
				'is_free' => $order->is_free(),
				'price' => [
					'amount' => $order->get_price_for_display(),
					'period' => $order->get_price_period_for_display(),
				],
				'pricing' => $order->get_pricing_details(),
				'booking' => $order->get_booking_details(),
				'additions' => $order->get_additions_details(),
				'custom_additions' => $order->get_custom_additions_details(),
				'fields' => $order->get_information_fields_details(),
				'notes' => $this->get_order_notes( $order ),
				'downloads' => $this->get_order_downloads( $order ),
				'role' => [
					'is_admin' => current_user_can('administrator'),
					'is_author' => $user->is_vendor_of( $order->get_id() ),
					'is_customer' => $user->is_customer_of( $order->get_id() ),
				],
				'actions' => $this->get_order_actions( $order ),
				'vendor_rules' => $this->get_vendor_rules( $order ),
				'tags' => $this->get_order_tags( $order ),
				'deliverables' => [
					'enabled' => $order->allows_manual_deliverables(),
					'allowed_file_types' => $product_type->config( 'settings.deliverables.uploads.allowed_file_types' ),
					'max_size' => $product_type->config( 'settings.deliverables.uploads.max_size' ),
					'max_count' => $product_type->config( 'settings.deliverables.uploads.max_count' ),
				],
				'comments' => [
					'allowed_file_types' => $product_type->config( 'settings.comments.uploads.allowed_file_types' ),
					'max_size' => $product_type->config( 'settings.comments.uploads.max_size' ),
					'max_count' => $product_type->config( 'settings.comments.uploads.max_count' ),
				],
			];

			// dd($order, $data);
			return wp_send_json( [
				'success' => true,
				'data' => apply_filters( 'voxel/view-order/get-data', $data, $order ),
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	private function get_order_notes( $order ) {
		$notes = \Voxel\Order_Note::query( [
			'order_id' => $order->get_id(),
		] );

		return array_map( function( $note ) {
			return $note->prepare();
		}, $notes );
	}

	private function get_order_downloads( $order ) {
		$file_field = new \Voxel\Product_Types\Order_Comments\Comment_Deliverables_Field;
		$notes = \Voxel\Order_Note::query( [
			'order_id' => $order->get_id(),
			'type' => \Voxel\Order_Note::AUTHOR_DELIVERED,
		] );

		$downloads = array_map( function( $note ) use ( $file_field ) {
			$details = $note->get_details();

			return [
				'time' => $note->get_time_for_display(),
				'files' => $file_field->prepare_for_display( $details['deliverables'] ?? '', $note->get_order_id(), $note->get_id() ),
			];
		}, $notes );

		if ( $field = $order->get_product_field() ) {
			$deliverables = $field->get_value()['deliverables'] ?? '';
			if ( ! empty( $deliverables ) ) {
				array_unshift( $downloads, [
					'time' => $order->get_time_for_display(),
					'files' => $file_field->prepare_for_display( $deliverables, $order->get_id(), 'auto' ),
				] );
			}
		}

		return $downloads;
	}


	private function get_order_actions( $order ) {
		$customer = $order->get_customer();
		$post = $order->get_post();
		$object_details = $order->get_object_details();
		$product_type = $order->get_product_type();

		$is_author = \Voxel\current_user()->is_vendor_of( $order->get_id() );
		$is_customer = \Voxel\current_user()->is_customer_of( $order->get_id() );

		$actions = [];
		if ( $order->get_mode() === 'payment' || $order->is_catalog_mode() ) {
			if ( $is_author ) {
				if ( $order->get_status() === \Voxel\Order::STATUS_PENDING_APPROVAL ) {
					$actions[] = 'author.decline';
				} elseif ( $order->get_status() === \Voxel\Order::STATUS_COMPLETED ) {
					// $actions[] = 'receipt';
				} elseif ( $order->get_status() === \Voxel\Order::STATUS_REFUND_REQUESTED ) {
					$actions[] = 'author.approve_refund';
					$actions[] = 'author.decline_refund';
				}
			}

			if ( $is_customer ) {
				if ( $order->get_status() === \Voxel\Order::STATUS_PENDING_APPROVAL ) {
					$actions[] = 'customer.cancel';
				} elseif ( $order->get_status() === \Voxel\Order::STATUS_COMPLETED ) {
					// $actions[] = 'receipt';

					if ( ! ( $order->is_catalog_mode() && ! $product_type->catalog_refunds_allowed() ) ) {
						$actions[] = 'customer.request_refund';
					}
				} elseif ( $order->get_status() === \Voxel\Order::STATUS_REFUND_REQUESTED ) {
					$actions[] = 'customer.cancel_refund_request';
				}

				if ( ! $order->is_catalog_mode() ) {
					$actions[] = 'customer.portal';
				}
			}
		} elseif ( $order->get_mode() === 'subscription' ) {
			if ( $is_customer ) {
				if ( ! $order->is_catalog_mode() ) {
					if ( $object_details['cancel_at_period_end'] ?? null ) {
						$actions[] = 'customer.subscriptions.reactivate';
					} elseif ( in_array( $order->get_status(), [ 'sub_incomplete', 'sub_past_due', 'sub_unpaid' ], true ) ) {
						$actions[] = 'customer.subscriptions.finalize_payment';
					}

					if ( ! in_array( $order->get_status(), [ 'sub_canceled', 'sub_incomplete_expired', 'pending_payment' ], true ) && ! ( $object_details['cancel_at_period_end'] ?? null ) ) {
						$actions[] = 'customer.subscriptions.cancel';
					}

					$actions[] = 'customer.portal';
				}
			}
		}

		if ( ! $order->is_catalog_mode() ) {
			if ( \Voxel\current_user()->has_cap('administrator') ) {
				$actions[] = 'admin.sync_with_stripe';
			}
		}

		return array_values( array_unique( $actions ) );
	}

	private function get_vendor_rules( $order ) {
		$field = $order->get_product_field();

		$vendor_rules = null;
		if ( $field ) {
			$config = (array) $field->get_value();
			if ( is_array( $config ) && ! empty( $config['notes_enabled'] ) ) {
				$vendor_rules = $config['notes'] ?? null;
			}
		}

		return $vendor_rules;
	}

	private function get_order_tags( $order ) {
		$product_type = $order->get_product_type();
		$active_tag = $order->get_tag();

		$tags = [
			'can_edit' => $order->can_be_tagged_by( get_current_user_id() ),
			'active' => $active_tag ? $active_tag->get_key() : null,
			'list' => [],
		];

		foreach ( $product_type->get_tags() as $tag ) {
			$tags['list'][] = [
				'key' => $tag->get_key(),
				'label' => $tag->get_label(),
				'primary_color' => $tag->get_primary_color(),
				'secondary_color' => $tag->get_secondary_color(),
				'has_qr_code' => $tag->has_qr_code(),
			];
		}

		return $tags;
	}
}
