<?php

namespace Voxel\Controllers\Frontend\Orders;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Orders_Controller extends \Voxel\Controllers\Base_Controller {

	protected function hooks() {
		$this->on( 'voxel_ajax_orders.get', '@get_orders' );
		$this->on( 'voxel_ajax_orders.post_comment', '@post_comment' );
		$this->on( 'voxel_ajax_orders.deliver_files', '@deliver_files' );
		$this->on( 'voxel_ajax_orders.download_deliverable', '@download_deliverable' );

		$this->on( 'voxel/orders/payment_intent.amount_capturable_updated', '@payment_intent_amount_capturable_updated', 10, 2 );
		$this->on( 'voxel/orders/payment_intent.canceled', '@payment_intent_canceled', 10, 2 );
		$this->on( 'voxel/orders/payment_intent.succeeded', '@payment_intent_succeeded', 10, 2 );
		$this->on( 'voxel/orders/charge.refunded', '@charge_refunded', 10, 2 );
		$this->on( 'voxel/orders/checkout.session.completed', '@checkout_session_completed', 10, 3 );

		$this->on( 'voxel/order.updated', '@order_updated', 10, 2 );
		$this->on( 'voxel/orders/subscription-updated', '@subscription_updated', 10, 2 );

		$this->on( 'voxel_ajax_orders.get_stats', '@get_stats' );
	}

	protected function get_orders() {
		$page = absint( $_GET['page'] ?? 1 );
		$per_page = 10;
		$type = sanitize_text_field( $_GET['type'] ?? 'all' );
		$status = sanitize_text_field( $_GET['status'] ?? 'all' );
		$search = trim( sanitize_text_field( $_GET['search'] ?? '' ) );

		$args = [
			'limit' => $per_page + 1,
		];

		if ( $type === 'incoming' ) {
			$args['vendor_id'] = get_current_user_id();
		} elseif ( $type === 'outgoing' ) {
			$args['customer_id'] = get_current_user_id();
		} else {
			$args['party_id'] = get_current_user_id();
		}

		if ( $status ) {
			if ( isset( \Voxel\Order::get_status_labels()[ $status ] ) ) {
				$args['status'] = $status;
			} elseif ( $status === 'all' ) {
				$args['status_not_in'] = [ 'pending_payment', 'canceled' ];
			}
		}

		if ( $page > 1 ) {
			$args['offset'] = ( $page - 1 ) * $per_page;
		}

		if ( ! empty( $search ) ) {
			$args['search'] = $search;
		}

		$orders = \Voxel\Order::query( $args );
		$has_more = count( $orders ) > $per_page;
		if ( $has_more ) {
			array_pop( $orders );
		}

		$data = [];
		foreach ( $orders as $order ) {
			$customer = $order->get_customer();
			$post = $order->get_post();
			$product_type = $order->get_product_type();
			$data[] = [
				'id' => $order->get_id(),
				'price' => $order->get_price_for_display(),
				'is_free' => $order->is_free(),
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
			];
		}

		return wp_send_json( [
			'success' => true,
			'data' => $data,
			'has_more' => $has_more,
		] );
	}

	protected function post_comment() {
		try {
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

			$product_type = $order->get_product_type();

			$values = json_decode( stripslashes( $_POST['fields'] ), true );

			$message_field = new \Voxel\Product_Types\Order_Comments\Comment_Message_Field;
			$sanitized_message = $message_field->sanitize( $values['message'] ?? '' );
			$message_field->validate( $sanitized_message );

			$file_field = new \Voxel\Product_Types\Order_Comments\Comment_Files_Field( [
				'upload_dir' => sprintf( 'voxel-orders/%d/%s', $order->get_id(), strtolower( \Voxel\random_string(8) ) ),
				'skip_subdir' => true,
				'allowed-types' => $product_type->config( 'settings.comments.uploads.allowed_file_types' ),
				'max-size' => $product_type->config( 'settings.comments.uploads.max_size' ),
				'max-count' => $product_type->config( 'settings.comments.uploads.max_count' ),
			] );
			$sanitized_files = $file_field->sanitize( $values['files'] ?? [] );
			$file_field->validate( $sanitized_files );
			$file_ids = $file_field->prepare_for_storage( $sanitized_files );

			$details = [];
			$details['user_id'] = get_current_user_id();

			if ( ! empty( $sanitized_message ) ) {
				$details['message'] = $sanitized_message;
			}

			if ( ! empty( $file_ids ) ) {
				$details['files'] = $file_ids;
			}

			if ( empty( $sanitized_message ) && empty( $file_ids ) ) {
				throw new \Exception( _x( 'Comment cannot be empty.', 'orders', 'voxel' ) );
			}

			$comment = $order->note( \Voxel\Order_Note::COMMENT, wp_json_encode( $details ) );

			return wp_send_json( [
				'success' => true,
				'comment' => $comment->prepare(),
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function deliver_files() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			if ( ! $order_id ) {
				throw new \Exception( _x( 'Missing order id.', 'orders', 'voxel' ) );
			}

			$order = \Voxel\Order::find( [
				'id' => $order_id,
				'vendor_id' => get_current_user_id(),
			] );

			if ( ! $order ) {
				throw new \Exception( _x( 'Could not find order.', 'orders', 'voxel' ) );
			}

			if ( ! $order->allows_manual_deliverables() ) {
				throw new \Exception( __( 'Not allowed.', 'voxel' ) );
			}

			$product_type = $order->get_product_type();

			$values = json_decode( stripslashes( $_POST['fields'] ), true );

			$message_field = new \Voxel\Product_Types\Order_Comments\Comment_Message_Field;
			$sanitized_message = $message_field->sanitize( $values['message'] ?? '' );
			$message_field->validate( $sanitized_message );

			$file_field = new \Voxel\Product_Types\Order_Comments\Comment_Deliverables_Field( [
				'allowed-types' => $product_type->config( 'settings.deliverables.uploads.allowed_file_types' ),
				'max-size' => $product_type->config( 'settings.deliverables.uploads.max_size' ),
				'max-count' => $product_type->config( 'settings.deliverables.uploads.max_count' ),
			] );

			$sanitized_files = $file_field->sanitize( $values['deliverables'] ?? [] );
			$file_field->validate( $sanitized_files );
			$file_ids = $file_field->prepare_for_storage( $sanitized_files );

			$details = [];
			$details['user_id'] = get_current_user_id();

			if ( ! empty( $sanitized_message ) ) {
				$details['message'] = $sanitized_message;
			}

			if ( ! empty( $file_ids ) ) {
				$details['deliverables'] = $file_ids;
			}

			if ( empty( $file_ids ) ) {
				throw new \Exception( _x( 'No files attached.', 'orders', 'voxel' ) );
			}

			$comment = $order->note( \Voxel\Order_Note::AUTHOR_DELIVERED, wp_json_encode( $details ) );

			return wp_send_json( [
				'success' => true,
				'comment' => $comment->prepare(),
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function download_deliverable() {
		try {
			$order_id = absint( $_GET['order_id'] ?? null );
			$file_id = absint( $_GET['file_id'] ?? null );
			if ( ! ( $order_id && $file_id ) ) {
				throw new \Exception( _x( 'Missing order/file id.', 'orders', 'voxel' ) );
			}

			$args = [
				'id' => $order_id,
				'status' => [ 'completed', 'sub_active', 'refund_requested' ],
			];

			// admin can view all
			if ( ! current_user_can( 'manage_options' ) ) {
				$args['party_id'] = get_current_user_id();
			}

			$order = \Voxel\Order::find( $args );
			if ( ! $order ) {
				throw new \Exception( _x( 'Could not find order.', 'orders', 'voxel' ) );
			}

			$product_type = $order->get_product_type();
			$field = $order->get_product_field();

			if ( ( $_GET['note_id'] ?? null ) === 'auto' ) {
				if ( ! ( $product_type && $field ) ) {
					throw new \Exception( _x( 'Could not find deliverable.', 'orders', 'voxel' ) );
				}

				$ids = explode( ',', (string) ( $field->get_value()['deliverables'] ?? '' ) );
				$ids = array_filter( array_map( 'absint', $ids ) );
			} else {
				$note_id = absint( $_GET['note_id'] ?? null );
				if ( ! $note_id ) {
					throw new \Exception( _x( 'Missing note id.', 'orders', 'voxel' ) );
				}

				$note = \Voxel\Order_Note::find( [
					'id' => $note_id,
					'type' => 'author.delivered',
					'order_id' => $order->get_id(),
				] );

				if ( ! $note || ! $product_type ) {
					throw new \Exception( _x( 'Could not find deliverable.', 'orders', 'voxel' ) );
				}

				$ids = explode( ',', (string) ( $note->get_details()['deliverables'] ?? '' ) );
				$ids = array_filter( array_map( 'absint', $ids ) );
			}

			if ( ! in_array( $file_id, $ids, true ) ) {
				throw new \Exception( _x( 'Could not find file.', 'orders', 'voxel' ) );
			}

			$details = $order->get_details();
			$download_counts = $details['download_counts'] ?? [];
			$download_limit = $product_type->config( 'settings.deliverables.download_limit' );
			$is_customer = \Voxel\current_user()->is_customer_of( $order->get_id() );

			// check for customer download limit (unless customer is administrator)
			if ( $is_customer && ! current_user_can('administrator') ) {
				if ( is_numeric( $download_limit ) && $download_limit >= 1 && ( $download_counts[ $file_id ] ?? 0 ) >= $download_limit ) {
					throw new \Exception( _x( 'You have reached the download limit for this file.', 'orders', 'voxel' ) );
				}
			}

			$file = get_attached_file( $file_id );
			if ( ! $file ) {
				throw new \Exception( _x( 'Could not find file.', 'orders', 'voxel' ) );
			}

			// increment download count
			if ( $is_customer && ! current_user_can('administrator') ) {
				if ( ! isset( $download_counts[ $file_id ] ) ) {
					$download_counts[ $file_id ] = 0;
				}

				$download_counts[ $file_id ]++;
				$details['download_counts'] = $download_counts;
				$order->update( 'details', $details );
			}

			$display_filename = get_post_meta( $file_id, '_display_filename', true );
			$filename = ! empty( $display_filename ) ? $display_filename : wp_basename( $file );

			header( 'Content-type: application/octet-stream' );
			header( 'Content-Disposition: attachment; filename="' . rawurlencode( $filename ) . '"' );

			// possible values: apache, nginx, lighttpd, litespeed
			$xsendfile = apply_filters( 'voxel/order-downloads/xsendfile-header', null );
			$uri = '/'.ltrim( str_replace( [ $_SERVER['DOCUMENT_ROOT'], '\\' ], [ '', '/' ], $file ), '/' );
			if ( $xsendfile === 'apache' ) {
				header( 'X-Sendfile: '.$uri );
				exit;
			} elseif ( $xsendfile === 'nginx' ) {
				header( 'X-Accel-Redirect: '.$uri );
				exit;
			} elseif ( $xsendfile === 'lighttpd' ) {
				header( 'X-LIGHTTPD-send-file: '.$uri );
				exit;
			} elseif ( $xsendfile === 'litespeed' ) {
				header( 'X-LiteSpeed-Location: '.$uri );
				exit;
			} else {
				ob_clean();
				flush();
				readfile( $file );
				exit;
			}
		} catch ( \Exception $e ) {
			return call_user_func( apply_filters( 'wp_die_handler', '_default_wp_die_handler' ), $e->getMessage(), '', [ 'back_link' => true ] );
		}
	}

	protected function payment_intent_amount_capturable_updated( $payment_intent, $order ) {
		$order->update( [
			'object_id' => $payment_intent->id,
			'object_details' => $payment_intent,
			'status' => \Voxel\Order::STATUS_PENDING_APPROVAL,
		] );

		$order->note( \Voxel\Order_Note::PAYMENT_AUTHORIZED );
	}

	protected function payment_intent_canceled( $payment_intent, $order ) {
		$order->update( [
			'object_id' => $payment_intent->id,
			'object_details' => $payment_intent,
			'status' => \Voxel\Order::STATUS_CANCELED,
		] );
	}

	protected function payment_intent_succeeded( $payment_intent, $order ) {
		$order->update( [
			'object_id' => $payment_intent->id,
			'object_details' => $payment_intent,
			'status' => \Voxel\Order::STATUS_COMPLETED,
		] );
	}

	protected function charge_refunded( $charge, $order ) {
		$order->update( [
			'status' => \Voxel\Order::STATUS_REFUNDED,
		] );
	}

	protected function checkout_session_completed( $session, $payment_intent, $order ) {
		$details = $order->get_details();
		$details['checkout'] = \Voxel\Order::get_session_details( $session );

		if ( $default_tag = $order->get_product_type()->get_default_tag() ) {
			$details['tag'] = $default_tag->get_key();
		}

		$order->update( [
			'object_id' => $payment_intent->id,
			'object_details' => $payment_intent,
			'status' => $this->_get_status_from_payment_intent( $payment_intent ),
			'details' => $details,
		] );

		( new \Voxel\Events\Orders\Customer_Order_Placed_Event )->dispatch( $order->get_id() );
		if ( $product_type = $order->get_product_type() ) {
			( new \Voxel\Events\Orders\Customer_Order_Placed_Event( $product_type ) )->dispatch( $order->get_id() );
		}
	}

	private function _get_status_from_payment_intent( $payment_intent ) {
		if ( $payment_intent->status === 'succeeded' ) {
			return \Voxel\Order::STATUS_COMPLETED;
		} elseif ( $payment_intent->status === 'canceled' ) {
			return \Voxel\Order::STATUS_CANCELED;
		} elseif ( $payment_intent->status === 'requires_capture' ) {
			return \Voxel\Order::STATUS_PENDING_APPROVAL;
		} else {
			return \Voxel\Order::STATUS_PENDING_PAYMENT;
		}
	}

	protected function order_updated( $order, $new_data ) {
		if ( ! isset( $new_data['status'] ) || $order->get_status() === $new_data['status'] ) {
			return;
		}

		if (
			in_array( $new_data['status'], [ 'completed', 'refund_requested' ], true )
			|| in_array( $order->get_status(), [ 'completed', 'refund_requested' ], true )
		) {
			$field = $order->get_product_field();
			if ( $field ) {
				$field->cache_fully_booked_days();
			}
		}

		if ( in_array( $new_data['status'], [
			\Voxel\Order::STATUS_PENDING_APPROVAL,
			\Voxel\Order::STATUS_COMPLETED,
			\Voxel\Order::STATUS_DECLINED,
			\Voxel\Order::STATUS_REFUND_REQUESTED,
			\Voxel\Order::STATUS_REFUNDED,
		], true ) ) {
			$vendor = $order->get_vendor();
			if ( $vendor ) {
				$vendor->get_vendor_stats()->expire_general_stats();
				$vendor->get_vendor_stats()->expire_last31_stats();
			}
		}

		$product_type = $order->get_product_type();
		if ( $product_type && $product_type->get_product_mode() === 'claim' ) {
			// transfer post ownership from vendor to customer
			if ( in_array( $new_data['status'], [ 'completed', 'sub_active' ], true ) ) {
				$post = $order->get_post();
				$vendor = $order->get_vendor();
				$customer = $order->get_customer();
				if ( $post && $vendor && $customer && $post->get_author_id() === $vendor->get_id() ) {
					wp_update_post( [
						'ID' => $post->get_id(),
						'post_author' => $customer->get_id(),
					] );
					$post->set_verified(true);
					\Voxel\cache_user_post_stats( $customer->get_id() );
					// \Voxel\log( 'Transferring ownership from vendor to customer' );
				}
			}

			// transfer post ownership back to vendor
			if ( in_array( $new_data['status'], [ 'refunded', 'sub_past_due', 'sub_canceled', 'sub_unpaid' ], true ) ) {
				$post = $order->get_post();
				$vendor = $order->get_vendor();
				$customer = $order->get_customer();
				if ( $post && $vendor && $customer && $post->get_author_id() === $customer->get_id() ) {
					wp_update_post( [
						'ID' => $post->get_id(),
						'post_author' => $vendor->get_id(),
					] );
					$post->set_verified(false);
					// \Voxel\log( 'Transferring ownership from customer to vendor' );
				}
			}
		}
	}

	protected function subscription_updated( $subscription, $order ) {
		// $subscription->status: trialing, active, incomplete, incomplete_expired, past_due, canceled, unpaid
		$order->update( [
			'object_id' => $subscription->id,
			'object_details' => $subscription,
			'status' => sprintf( 'sub_%s', $subscription->status ),
		] );

		// incomplete_expired and canceled are terminal states
		if ( $subscription->status === 'incomplete_expired' ) {
			// @todo trigger notification
		} elseif ( $subscription->status === 'canceled' ) {
			// @todo trigger notification
		}
	}

	protected function get_stats() {
		try {
			$chart = $_GET['chart'] ?? null;
			$direction = ( $_GET['direction'] ?? null ) === 'next' ? 'next' : 'prev';
			$date = strtotime( $_GET['date'] ?? null );

			if ( ! in_array( $chart, [ 'this-week', 'this-month', 'this-year' ], true ) || ! $date ) {
				throw new \Exception( __( 'Invalid request.', 'voxel' ) );
			}

			$user = \Voxel\current_user();
			$stats = $user->get_vendor_stats();

			if ( $chart === 'this-week' ) {
				$change = $direction === 'next' ? '+7 days' : '-7 days';
				$data = $stats->get_week_chart( date( 'Y-m-d', strtotime( $change, $date ) ) );
			} elseif ( $chart === 'this-month' ) {
				$change = $direction === 'next' ? '+1 month' : '-1 month';
				$data = $stats->get_month_chart( date( 'Y-m-01', strtotime( $change, $date ) ) );
			} else {
				$change = $direction === 'next' ? '+1 year' : '-1 year';
				$data = $stats->get_year_chart( (int) date( 'Y', strtotime( $change, $date ) ) );
			}

			return wp_send_json( [
				'success' => true,
				'data' => $data,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}
}
