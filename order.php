<?php

namespace Voxel;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Order {
	use \Voxel\Product_Types\Order_Singleton_Trait;

	const STATUS_PENDING_PAYMENT = 'pending_payment';
	const STATUS_PENDING_APPROVAL = 'pending_approval';
	const STATUS_COMPLETED = 'completed';
	const STATUS_CANCELED = 'canceled';
	const STATUS_DECLINED = 'declined';
	const STATUS_REFUND_REQUESTED = 'refund_requested';
	const STATUS_REFUNDED = 'refunded';

	private
		$id,
		$post_id,
		$product_type,
		$product_key,
		$customer_id,
		$vendor_id,
		$details,
		$status,
		$session_id,
		$mode,
		$object_id,
		$object_details,
		$catalog_mode,
		$created_at;

	public function __construct( array $data ) {
		$this->id = absint( $data['id'] );
		$this->post_id = absint( $data['post_id'] );
		$this->customer_id = absint( $data['customer_id'] );
		$this->vendor_id = absint( $data['vendor_id'] );
		$this->product_type = $data['product_type'];
		$this->product_key = $data['product_key'];
		$this->status = $data['status'];
		$this->session_id = $data['session_id'];
		$this->mode = $data['mode'];
		$this->object_id = $data['object_id'];
		$this->created_at = $data['created_at'];
		$this->details = is_string( $data['details'] ) ? json_decode( $data['details'], ARRAY_A ) : $data['details'];
		$this->object_details = is_string( $data['object_details'] ) ? json_decode( $data['object_details'], ARRAY_A ) : $data['object_details'];
		$this->catalog_mode = !! ( $data['catalog_mode'] ?? false );
		// dd($this);
	}

	public function get_id() {
		return $this->id;
	}

	public function get_status() {
		return $this->status;
	}

	public function get_mode() {
		return $this->mode;
	}

	public function get_object_id() {
		return $this->object_id;
	}

	public function get_object() {
		$stripe = \Voxel\Stripe::getClient();
		return ( $this->get_mode() === 'subscription' )
			? $stripe->subscriptions->retrieve( $this->get_object_id() )
			: $stripe->paymentIntents->retrieve( $this->get_object_id() );
	}

	public function get_object_details() {
		return $this->object_details;
	}

	public function get_session_id() {
		return $this->session_id;
	}

	public function get_session_object() {
		$stripe = \Voxel\Stripe::getClient();
		return $stripe->checkout->sessions->retrieve( $this->get_session_id() );
	}

	public function get_link() {
		return add_query_arg(
			'order_id',
			$this->get_id(),
			get_permalink( \Voxel\get( 'templates.orders' ) )
		);
	}

	public function get_backend_link() {
		return add_query_arg(
			'order_id',
			$this->get_id(),
			admin_url( 'admin.php?page=voxel-orders' )
		);
	}

	public function get_details() {
		return $this->details;
	}

	public function get_status_label() {
		$labels = $this->get_status_labels();
		return $labels[ $this->status ] ?? _x( 'Unknown', 'order status', 'voxel' );
	}

	public function get_customer_id() {
		return $this->customer_id;
	}

	public function get_customer() {
		return \Voxel\User::get( $this->get_customer_id() );
	}

	public function get_post_id() {
		return $this->post_id;
	}

	public function get_post() {
		return \Voxel\Post::get( $this->get_post_id() );
	}

	public function get_vendor() {
		return \Voxel\User::get( $this->get_vendor_id() );
	}

	public function get_vendor_id() {
		return $this->vendor_id;
	}

	public function get_product_type() {
		return \Voxel\Product_Type::get( $this->product_type );
	}

	public function get_product_key() {
		return $this->product_key;
	}

	public function get_product_field() {
		$post = $this->get_post();
		$field = $post ? $post->get_field( $this->product_key ) : null;
		if ( ! ( $field && $field->get_type() === 'product' ) ) {
			return null;
		}

		return $field;
	}

	public function get_tag() {
		$product_type = $this->get_product_type();
		if ( ! $product_type ) {
			return null;
		}
		return $product_type->get_tag( $this->details['tag'] ?? null );
	}

	public function can_be_tagged_by( $user_id ): bool {
		$user = \Voxel\User::get( $user_id );
		$editable_by = $this->get_product_type()->config( 'settings.tags.editable_by' );
		$is_vendor = $user->is_vendor_of( $this->get_id() );
		$is_customer = $user->is_customer_of( $this->get_id() );

		return ( ( $editable_by === 'both' && ( $is_vendor || $is_customer ) ) || ( $editable_by === 'vendor' && $is_vendor ) || ( $editable_by === 'customer' && $is_customer ) );
	}

	public function get_price() {
		$currency = $this->object_details['currency'] ?? $this->details['pricing']['currency'];
		if ( isset( $this->object_details['amount'] ) ) {
			$amount = $this->object_details['amount'];
			if ( ! \Voxel\Stripe\Currencies::is_zero_decimal( $currency ) ) {
				$amount /= 100;
			}
		} else {
			$amount = $this->details['pricing']['total'];
		}

		if ( $this->get_mode() === 'subscription' ) {
			$interval = $this->object_details['interval'] ?? $this->details['pricing']['interval']['unit'];
			$interval_count = $this->object_details['interval_count'] ?? $this->details['pricing']['interval']['count'];

			return compact( 'amount', 'currency', 'interval', 'interval_count' );
		} else {
			return compact( 'amount', 'currency' );
		}
	}

	public function is_free() {
		return floatval( $this->get_price()['amount'] ) === 0.0;
	}

	public function get_price_for_display() {
		$price = $this->get_price();
		return \Voxel\currency_format( $price['amount'], $price['currency'], false );
	}

	public function get_price_period_for_display() {
		if ( $this->get_mode() === 'subscription' ) {
			$price = $this->get_price();
			return \Voxel\interval_format( $price['interval'], $price['interval_count'] );
		}
	}

	public function get_additions() {
		return (array) ( $this->details['additions'] ?? [] );
	}

	public function get_custom_additions() {
		return (array) ( $this->details['custom_additions'] ?? [] );
	}

	public function get_customer_name_for_display() {
		$customer = $this->get_customer();
		return $customer
			? $customer->get_display_name()
			: _x( '(deleted account)', 'deleted user account', 'voxel' );
	}

	public function get_created_at() {
		return $this->created_at;
	}

	public function is_catalog_mode() {
		return $this->catalog_mode;
	}

	public function get_time_for_display() {
		$from = strtotime( $this->created_at ) + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
		$to = current_time( 'timestamp' );
		$diff = (int) abs( $to - $from );
		if ( $diff < WEEK_IN_SECONDS ) {
			return sprintf( _x( '%s ago', 'order created at', 'voxel' ), human_time_diff( $from, $to ) );
		}

		return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $from );
	}

	public function get_checkin_date() {
		$timestamp = strtotime( $this->details['booking']['checkin'] ?? '' );
		return $timestamp ? date( 'Y-m-d', $timestamp ) : null;
	}

	public function get_checkout_date() {
		$timestamp = strtotime( $this->details['booking']['checkout'] ?? '' );
		return $timestamp ? date( 'Y-m-d', $timestamp ) : null;
	}

	public function is_nights_mode() {
		return ( $this->details['booking']['range_mode'] ?? null ) === 'nights';
	}

	public function get_timeslot() {
		$timeslot = $this->details['booking']['timeslot'] ?? [];
		$from = strtotime( $timeslot['from'] ?? '' );
		$to = strtotime( $timeslot['to'] ?? '' );

		if ( ! ( $from && $to ) ) {
			return null;
		}

		return [
			'from' => date( 'H:i', $from ),
			'to' => date( 'H:i', $to ),
		];
	}

	public function get_post_title_for_display() {
		$post = $this->get_post();
		return $post
			? $post->get_title()
			: _x( '(deleted item)', 'deleted order post', 'voxel' );
	}

	public static function get_status_labels() {
		return [
			static::STATUS_COMPLETED => _x( 'Completed', 'order status', 'voxel' ),
			static::STATUS_PENDING_APPROVAL => _x( 'Pending Approval', 'order status', 'voxel' ),
			static::STATUS_DECLINED => _x( 'Declined', 'order status', 'voxel' ),
			static::STATUS_REFUND_REQUESTED => _x( 'Refund Requested', 'order status', 'voxel' ),
			static::STATUS_PENDING_PAYMENT => _x( 'Pending Payment', 'order status', 'voxel' ),
			static::STATUS_CANCELED => _x( 'Canceled', 'order status', 'voxel' ),
			static::STATUS_REFUNDED => _x( 'Refunded', 'order status', 'voxel' ),

			// trialing, active, incomplete, incomplete_expired, past_due, canceled, unpaid
			'sub_trialing' => _x( 'Trialing', 'subscription status', 'voxel' ),
			'sub_active' => _x( 'Active', 'subscription status', 'voxel' ),
			'sub_incomplete' => _x( 'Incomplete', 'subscription status', 'voxel' ),
			'sub_incomplete_expired' => _x( 'Expired', 'subscription status', 'voxel' ),
			'sub_past_due' => _x( 'Past due', 'subscription status', 'voxel' ),
			'sub_canceled' => _x( 'Canceled', 'subscription status', 'voxel' ),
			'sub_unpaid' => _x( 'Unpaid', 'subscription status', 'voxel' ),
		];
	}

	public static function map_status_from_stripe( $stripe_status ) {
		$map = [
			// payment intent
			'requires_payment_method' => static::STATUS_PENDING_PAYMENT,
			'requires_confirmation' => static::STATUS_PENDING_PAYMENT,
			'requires_action' => static::STATUS_PENDING_PAYMENT,
			'processing' => static::STATUS_PENDING_PAYMENT,
			'requires_capture' => static::STATUS_PENDING_APPROVAL,
			'succeeded' => static::STATUS_COMPLETED,
			'canceled' => static::STATUS_CANCELED,

			// subscription
			'trialing' => 'sub_trialing',
			'active' => 'sub_active',
			'incomplete' => 'sub_incomplete',
			'incomplete_expired' => 'sub_incomplete_expired',
			'past_due' => 'sub_past_due',
			'canceled' => 'sub_canceled',
			'unpaid' => 'sub_unpaid',
		];

		return $map[ $stripe_status ] ?? static::STATUS_PENDING_PAYMENT;
	}

	public function note( $type, $details = null ) {
		return \Voxel\Order_Note::create( [
			'order_id' => $this->get_id(),
			'type' => $type,
			'details' => $details,
		] );
	}

	public function update( $data_or_key, $value = null ) {
		global $wpdb;

		if ( is_array( $data_or_key ) ) {
			$data = $data_or_key;
		} else {
			$data = [];
			$data[ $data_or_key ] = $value;
		}

		$data['id'] = $this->get_id();
		$wpdb->query( \Voxel\Product_Types\Order_Repository::_generate_insert_query( $data ) );

		do_action( 'voxel/order.updated', $this, $data );
	}

	public static function extract_intent_details( \Stripe\PaymentIntent $payment_intent ): array {
		return [
			'id' => $payment_intent->id,
			'amount' => $payment_intent->amount,
			'currency' => $payment_intent->currency,
			'application_fee_amount' => $payment_intent->application_fee_amount,
			'shipping' => $payment_intent->shipping,
			'status' => $payment_intent->status,
		];
	}

	public static function extract_subscription_details( \Stripe\Subscription $subscription ): array {
		// $subscription->status: trialing, active, incomplete, incomplete_expired, past_due, canceled, unpaid
		return [
			'id' => $subscription->id,
			'status' => $subscription->status,
			'trial_end' => $subscription->trial_end,
			'current_period_end' => $subscription->current_period_end,
			'cancel_at_period_end' => $subscription->cancel_at_period_end,
			'amount' => $subscription->plan->amount,
			'currency' => $subscription->plan->currency,
			'interval' => $subscription->plan->interval,
			'interval_count' => $subscription->plan->interval_count,
			'application_fee_percent' => $subscription->application_fee_percent,
		];
	}

	public static function get_session_details( \Stripe\Checkout\Session $session ): array {
		return [
			'id' => $session->id,
			'currency' => $session->currency,
			'amount_subtotal' => $session->amount_subtotal,
			'amount_total' => $session->amount_total,
			'total_details' => $session->total_details,
		];
	}

	public function get_pricing_details() {
		$product_type = $this->get_product_type();
		$post = $this->get_post();
		$details = $this->get_details();
		$currency = $details['pricing']['currency'];

		$pricing = [
			'period' => $this->get_price_period_for_display(),
			'base_price' => \Voxel\currency_format( $details['pricing']['base_price'], $currency, false ),
			'total' => \Voxel\currency_format( $details['pricing']['total'], $currency, false ),
			'additions' => [],
		];

		foreach ( ( $details['additions'] ?? [] ) as $addition_key => $_data ) {
			$addition = $product_type->get_addition( $addition_key );
			$label = $addition ? $addition->get_label() : $addition_key;

			if ( $_data['type'] === 'numeric' ) {
				$price = $_data['price'];
				$pricing['additions'][] = [
					'label' => sprintf(
						'%s × %s',
						$label,
						number_format_i18n( $_data['units'] )
					),
					'price' => \Voxel\currency_format( $price, $currency, false ),
				];
			} elseif ( $_data['type'] === 'checkbox' ) {
				$price = $_data['price'];
				$pricing['additions'][] = [
					'label' => $label,
					'price' => \Voxel\currency_format( $price, $currency, false ),
				];
			} elseif ( $_data['type'] === 'select' ) {
				$price = $_data['price'];
				$pricing['additions'][] = [
					'label' => $label,
					'price' => \Voxel\currency_format( $price, $currency, false ),
				];
			}
		}

		foreach ( ( $details['custom_additions'] ?? [] ) as $field_key => $addition ) {
			foreach ( $addition['items'] as $item_key => $item ) {
				$label = $item['label'] ?? '';
				if ( $item['type'] === 'numeric' ) {
					$price = $item['price'];
					$pricing['additions'][] = [
						'label' => sprintf(
							'%s × %s',
							$label,
							number_format_i18n( $item['units'] )
						),
						'price' => \Voxel\currency_format( $price, $currency, false ),
					];
				} else {
					$price = $item['price'];
					$pricing['additions'][] = [
						'label' => $label,
						'price' => \Voxel\currency_format( $price, $currency, false ),
					];
				}
			}
		}

		return $pricing;
	}

	public function get_booking_details() {
		$details = $this->get_details();

		$booking = null;
		if ( ! empty( $details['booking'] ) ) {
			$checkin = $this->get_checkin_date();
			$checkout = $this->get_checkout_date();
			$timeslot = $this->get_timeslot();

			if ( $checkin && $checkout ) {
				$to = strtotime( $checkout );
				if ( $this->is_nights_mode() ) {
					$to = strtotime( '+1 day', $to );
				}

				$booking = [
					'type' => 'date_range',
					'from' => date_i18n( get_option( 'date_format' ), strtotime( $checkin ) ),
					'to' => date_i18n( get_option( 'date_format' ), $to ),
				];
			} elseif ( $checkin && $timeslot ) {
				$booking = [
					'type' => 'timeslot',
					'date' => date_i18n( get_option( 'date_format' ), strtotime( $checkin ) ),
					'from' => date_i18n( get_option( 'time_format' ), strtotime( $timeslot['from'] ) ),
					'to' => date_i18n( get_option( 'time_format' ), strtotime( $timeslot['to'] ) ),
				];
			} elseif ( $checkin ) {
				$booking = [
					'type' => 'single_date',
					'date' => date_i18n( get_option( 'date_format' ), strtotime( $checkin ) ),
				];
			}
		}

		return $booking;
	}

	public function get_subscription_details() {
		$object_details = $this->get_object_details();
		$subscription_details = [
			'exists' => false,
		];

		if ( $this->get_mode() === 'subscription' ) {
			$subscription_details = [
				'exists' => ! empty( $object_details ),
			];

			if ( $subscription_details['exists'] ) {
				$subscription_details['status'] = $object_details['status'] ?? null;
				$subscription_details['cancel_at_period_end'] = $object_details['cancel_at_period_end'] ?? null;
				$subscription_details['current_period_end'] = \Voxel\date_format( $object_details['current_period_end'] ?? null );
				$subscription_details['trial_end'] = \Voxel\date_format( $object_details['trial_end'] ?? null );
			}
		}

		return $subscription_details;
	}

	public function get_additions_details() {
		$product_type = $this->get_product_type();
		$additions = [];

		foreach ( $this->get_additions() as $addition_key => $details ) {
			$addition = $product_type->get_addition( $addition_key );

			$label = $addition ? $addition->get_label() : ( $details['label'] ?? $addition_key );
			$icon = \Voxel\get_icon_markup( $addition ? $addition->get_prop('icon') : '' );
			$type = $details['type'] ?? null;

			if ( $type === 'numeric' ) {
				if ( ! is_numeric( $details['units'] ?? null ) ) {
					continue;
				}

				$content = number_format_i18n( $details['units'] );
			} elseif ( $type === 'checkbox' ) {
				$content = _x( 'Yes', 'addition enabled', 'voxel' );
			} elseif ( $type === 'select' ) {
				if ( ! isset( $details['choice'] ) ) {
					continue;
				}

				$content = $details['choice_label'] ?? $details['choice'];
				if ( $addition && ( $choice = $addition->get_choice_by_key( $details['choice'] ) ) ) {
					$content = $choice['label'];
				}
			} else {
				continue;
			}

			$additions[] = [
				'label' => $label,
				'content' => $content,
				'icon' => $icon,
			];
		}

		return $additions;
	}

	public function get_custom_additions_details() {
		$additions = [];

		foreach ( $this->get_custom_additions() as $field_key => $addition ) {
			foreach ( $addition['items'] as $item ) {
				if ( $item['type'] === 'numeric' ) {
					$additions[] = [
						'label' => $item['label'],
						'icon' => \Voxel\svg( 'list.svg', false ) ,
						'content' => number_format_i18n( $item['units'] ),
					];
				} elseif ( $item['type'] === 'checkbox' ) {
					$additions[] = [
						'label' => $item['label'],
						'icon' => \Voxel\svg( 'list.svg', false ) ,
						'content' => 'Yes',
					];
				}
			}
		}

		return $additions;
	}

	public function get_information_fields_details() {
		$product_type = $this->get_product_type();
		if ( ! $product_type ) {
			return [];
		}

		$details = $this->get_details();

		$fields = [];
		$_fields = $details['fields'] ?? [];
		foreach ( $product_type->get_fields() as $field ) {
			$content = $field->prepare_for_display( $_fields[ $field->get_key() ] ?? null );
			if ( is_null( $content ) ) {
				continue;
			}

			$fields[] = [
				'type' => $field->get_type(),
				'label' => $field->get_label(),
				'content' => $content,
			];
		}

		return $fields;
	}

	public function allows_manual_deliverables() {
		$product_type = $this->get_product_type();
		return (
			$product_type
			&& $product_type->config( 'settings.deliverables.enabled' )
			&& in_array( 'manual', (array) $product_type->config( 'settings.deliverables.delivery_methods' ), true )
			&& in_array( $this->get_status(), [ 'completed', 'sub_active', 'refund_requested' ], true )
		);
	}
}
