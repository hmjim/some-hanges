<?php

namespace Voxel;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Checkout_Session {

	public
		$customer,
		$vendor,
		$destination,
		$post,
		$product_type,
		$field,
		$config,
		$stripe;

	public
		$booking = [],
		$additions = [],
		$custom_additions = [],
		$information_fields = [],
		$pricing = [];

	public function __construct( $args ) {
		$args = array_merge( [
			'customer_id' => null,
			'post_id' => null,
			'field_key' => null,
		], $args );

		$this->customer = \Voxel\User::get( $args['customer_id'] );
		$this->post = \Voxel\Post::get( $args['post_id'] );

		if ( ! ( $this->post && $this->post->get_status() === 'publish' ) ) {
			throw new \Exception( _x( 'This item has not been published.', 'checkout', 'voxel' ) );
		}

		$this->vendor = $this->post->get_author();
		if ( ! $this->vendor ) {
			throw new \Exception( _x( 'This item cannot be purchased.', 'checkout', 'voxel' ) );
		}

		$this->field = $this->post->get_field( $args['field_key'] );
		if ( ! ( $this->field && $this->field->get_type() === 'product' ) ) {
			throw new \Exception( _x( 'Product field has not been configured.', 'checkout', 'voxel' ) );
		}

		$this->product_type = $this->field->get_product_type();
		if ( ! $this->product_type ) {
			throw new \Exception( _x( 'Product type does not exist.', 'checkout', 'voxel' ) );
		}

		if ( ! $this->product_type->is_catalog_mode() ) {
			$this->stripe = \Voxel\Stripe::getClient();
		}

		if ( $this->product_type->get_product_mode() === 'claim' && $this->post->is_verified() ) {
			throw new \Exception( _x( 'This post cannot be claimed.', 'checkout', 'voxel' ) );
		}

		$this->config = $this->field->get_value();
		if ( empty( $this->config ) || ! $this->config['enabled'] ) {
			throw new \Exception( _x( 'Product is not available.', 'checkout', 'voxel' ) );
		}

		$this->destination = $this->get_transfer_destination();
	}

	public function is_using_price_id(): bool {
		return $this->product_type->config('settings.payments.pricing') === 'price_id';
	}

	public function get_transfer_destination() {
		if ( $this->vendor->has_cap('administrator') && apply_filters( 'voxel/admin-requires-vendor-onboarding', false ) !== true ) {
			return null;
		}

		$transfer_destination = $this->product_type->config( 'settings.payments.transfer_destination' );
		if ( $transfer_destination === 'admin_account' ) {
			return null;
		}

		$account = $this->vendor->get_stripe_account_details();
		if ( ! $account->exists ) {
			throw new \Exception( _x( 'This seller is not available at the moment.', 'checkout', 'voxel' ) );
		}

		return $account;
	}

	public function allows_promotion_codes(): bool {
		return !! $this->product_type->config( 'checkout.promotion_codes.enabled' );
	}

	/**
	 * Booking information
	 *
	 */
	public function set_booking_details( $details ) {
		$calendar_type = $this->product_type->config('calendar.type');
		$checkin = strtotime( $details['checkIn'] ?? '' );
		$checkout = strtotime( $details['checkOut'] ?? '' );
		$from = strtotime( $details['timeslot']['from'] ?? '' );
		$to = strtotime( $details['timeslot']['to'] ?? '' );

		$value = $this->field->get_value();
		$make_available_next = absint( $value['calendar']['make_available_next'] ?? 0 );

		$excluded_weekdays = $value['calendar']['excluded_weekdays'] ?? [];
		$excluded_days = array_flip( $this->field->get_excluded_days() );
		$now = new \DateTime( date( 'Y-m-d' ), new \DateTimeZone('UTC') );
		$limit = new \DateTime( date( 'Y-m-d', strtotime( '+'.$make_available_next.' days', time() ) ), new \DateTimeZone('UTC') );

		if ( $calendar_type === 'booking' ) {
			$calendar_format = $this->product_type->config('calendar.format');
			$calendar_allow_range = $this->product_type->config('calendar.allow_range');
			$is_nights_mode = $this->product_type->config('calendar.range_mode') === 'nights';

			if ( $calendar_format === 'days' && $calendar_allow_range ) {
				if ( ! ( $checkin && $checkout ) ) {
					throw new \Exception( _x( 'Please choose check-in and check-out dates.', 'checkout', 'voxel' ) );
				}

				$start = new \DateTime( date( 'Y-m-d', $checkin ), new \DateTimeZone('UTC') );
				$end = new \DateTime( date( 'Y-m-d', $checkout ), new \DateTimeZone('UTC') );
				if ( $is_nights_mode ) {
					$end->modify( '-1 day' );
				}

				if ( $make_available_next < 1 || ( $start < $now ) || ( $end > $limit ) || ( $start > $end ) ) {
					throw new \Exception( _x( 'This date can\'t be booked.', 'checkout', 'voxel' ) );
				}

				while ( $start <= $end ) {
					if ( isset( $excluded_days[ $start->format('Y-m-d') ] ) ) {
						throw new \Exception( _x( 'This date has already been booked.', 'checkout', 'voxel' ) );
					}

					if ( in_array( strtolower( $start->format('D') ), $excluded_weekdays, true ) ) {
						throw new \Exception( _x( 'This date can\'t be booked.', 'checkout', 'voxel' ) );
					}

					$start->modify('+1 day');
				}

				if ( $is_nights_mode ) {
					$this->booking = [
						'checkin' => date( 'Y-m-d', $checkin ),
						'checkout' => date( 'Y-m-d', strtotime( '-1 day', $checkout ) ),
						'range_mode' => 'nights',
					];
				} else {
					$this->booking = [
						'checkin' => date( 'Y-m-d', $checkin ),
						'checkout' => date( 'Y-m-d', $checkout ),
					];
				}

			} elseif ( $calendar_format === 'slots' ) {
				if ( ! ( $checkin && $from && $to ) ) {
					throw new \Exception( _x( 'Please choose check-in date and timeslot.', 'checkout', 'voxel' ) );
				}

				$start = new \DateTime( date( 'Y-m-d', $checkin ), new \DateTimeZone('UTC') );
				if ( ( $start < $now ) || ( $start > $limit ) ) {
					throw new \Exception( _x( 'This date can\'t be booked.', 'checkout', 'voxel' ) );
				}

				if ( isset( $excluded_days[ $start->format('Y-m-d') ] ) ) {
					throw new \Exception( _x( 'This date has already been booked.', 'checkout', 'voxel' ) );
				}

				if ( in_array( strtolower( $start->format('D') ), $excluded_weekdays, true ) ) {
					throw new \Exception( _x( 'This date can\'t be booked.', 'checkout', 'voxel' ) );
				}

				$timeslots = $value['calendar']['timeslots'] ?? [];
				$slot_from = date( 'H:i', $from );
				$slot_to = date( 'H:i', $to );
				$slot_exists = false;
				foreach ( $timeslots as $slot_group ) {
					if ( ! in_array( strtolower( $start->format('D') ), $slot_group['days'] ?? [], true ) ) {
						continue;
					}

					foreach ( $slot_group['slots'] ?? [] as $slot ) {
						if ( $slot['from'] === $slot_from && $slot['to'] === $slot_to ) {
							$slot_exists = true;
							break(2);
						}
					}
				}

				if ( ! $slot_exists ) {
					throw new \Exception( _x( 'This slot can\'t be booked.', 'checkout', 'voxel' ) );
				}

				$slot_key = sprintf( '%s %s-%s', date( 'Y-m-d', $checkin ), date( 'H:i', $from ), date( 'H:i', $to ) );
				$fully_booked_slots = $this->field->get_excluded_slots();
				if ( isset( $fully_booked_slots[ $slot_key ] ) ) {
					throw new \Exception( _x( 'This slot has already been booked.', 'checkout', 'voxel' ) );
				}

				$this->booking = [
					'checkin' => date( 'Y-m-d', $checkin ),
					'timeslot' => [
						'from' => date( 'H:i', $from ),
						'to' => date( 'H:i', $to ),
					],
				];
			} else {
				if ( ! $checkin ) {
					throw new \Exception( _x( 'Please choose check-in date.', 'checkout', 'voxel' ) );
				}

				$start = new \DateTime( date( 'Y-m-d', $checkin ), new \DateTimeZone('UTC') );
				if ( ( $start < $now ) || ( $start > $limit ) ) {
					throw new \Exception( _x( 'This date can\'t be booked.', 'checkout', 'voxel' ) );
				}

				if ( isset( $excluded_days[ $start->format('Y-m-d') ] ) ) {
					throw new \Exception( _x( 'This date has already been booked.', 'checkout', 'voxel' ) );
				}

				if ( in_array( strtolower( $start->format('D') ), $excluded_weekdays, true ) ) {
					throw new \Exception( _x( 'This date can\'t be booked.', 'checkout', 'voxel' ) );
				}

				$this->booking = [
					'checkin' => date( 'Y-m-d', $checkin ),
				];
			}
		} elseif ( $calendar_type === 'recurring-date' ) {
			if ( ! ( $checkin && $checkout ) ) {
				throw new \Exception( _x( 'Please choose check-in and check-out dates.', 'checkout', 'voxel' ) );
			}

			$date_exists = false;
			$start_date = date( 'Y-m-d H:i:s', $checkin );
			$end_date = date( 'Y-m-d H:i:s', $checkout );
			$bookable_dates = $this->field->get_bookable_recurring_dates();
			foreach ( $bookable_dates as $date ) {
				if ( $date['start'] === $start_date && $date['end'] === $end_date ) {
					$date_exists = true;
					break(1);
				}
			}

			if ( ! $date_exists ) {
				throw new \Exception( _x( 'This date can\'t be booked.', 'checkout', 'voxel' ) );
			}

			$this->booking = [
				'checkin' => date( 'Y-m-d', $checkin ),
				'checkout' => date( 'Y-m-d', $checkout ),
				'timeslot' => [
					'from' => date( 'H:i', $checkin ),
					'to' => date( 'H:i', $checkout ),
				],
			];
		}
	}

	public function get_repeat_day_count(): int {
		if ( $this->product_type->get_product_mode() !== 'booking' ) {
			return 1;
		}

		$calendar_type = $this->product_type->config('calendar.type');
		$calendar_format = $this->product_type->config('calendar.format');
		$allow_range = $this->product_type->config('calendar.allow_range');
		$range_mode = $this->product_type->config('calendar.range_mode');
		if ( ! ( $calendar_type === 'booking' && $calendar_format === 'days' && $allow_range ) ) {
			return 1;
		}

		$checkin = strtotime( $this->booking['checkin'] ?? null );
		$checkout = strtotime( $this->booking['checkout'] ?? null );
		if ( ! ( $checkin && $checkout ) ) {
			return 1;
		}

		return abs( floor( ( $checkout - $checkin ) / 86400 ) ) + 1;
	}

	public function set_additions( $details ) {
		$day_count = $this->get_repeat_day_count();

		foreach ( $this->product_type->get_additions() as $addition ) {
			$addition->set_field( $this->field );
			if ( ! $addition->is_enabled() ) {
				continue;
			}

			$value = $addition->sanitize( $details[ $addition->get_key() ] ?? null );
			$addition->validate( $value );

			if ( $value === null ) {
				continue;
			}

			if ( $addition->get_type() === 'numeric' ) {
				if ( $value < 1 ) {
					continue;
				}

				$price_per_unit = $addition->get_price_per_unit();
				$price_per_day = $price_per_unit * $value;
				$price = $price_per_day;
				if ( !! $addition->get_prop('repeat') ) {
					$price = $price_per_day * $day_count;
				}

				$this->additions[ $addition->get_key() ] = [
					'type' => 'numeric',
					'label' => $addition->get_label(),
					'price_per_unit' => $price_per_unit,
					'units' => $value,
					'price_per_day' => $price_per_day,
					'price' => $price,
				];
			} elseif ( $addition->get_type() === 'checkbox' ) {
				if ( $value !== true ) {
					continue;
				}

				$price_per_day = $addition->get_price();
				$price = $price_per_day;
				if ( !! $addition->get_prop('repeat') ) {
					$price = $price_per_day * $day_count;
				}

				$this->additions[ $addition->get_key() ] = [
					'type' => 'checkbox',
					'label' => $addition->get_label(),
					'price_per_day' => $price_per_day,
					'price' => $price,
				];
			} elseif ( $addition->get_type() === 'select' ) {
				$price_per_day = $addition->get_price_for_choice( $value );
				$price = $price_per_day;
				if ( $price !== null ) {
					$choice = $addition->get_choice_by_key( $value );
					if ( !! $addition->get_prop('repeat') ) {
						$price = $price_per_day * $day_count;
					}

					$this->additions[ $addition->get_key() ] = [
						'type' => 'select',
						'label' => $addition->get_label(),
						'choice' => $value,
						'choice_label' => $choice['label'],
						'price_per_day' => $price_per_day,
						'price' => $price,
					];
				}
			}
		}
	}

	public function set_custom_additions( $details ) {
		$custom_additions = $this->field->get_custom_additions();
		$day_count = $this->get_repeat_day_count();

		foreach ( $custom_additions as $field_key => $field_data ) {
			$items = [];
			foreach ( $field_data['items'] as $addition ) {
				$value = $details[ $field_key ][ $addition['id'] ] ?? null;

				if ( ( $addition['required'] ?? false ) && $value === null ) {
					throw new \Exception( \Voxel\replace_vars( _x( '@addition_name is required', 'checkout', 'voxel' ), [
						'@addition_name' => $addition['label'],
					] ) );
				}

				if ( $addition['has_quantity'] ) {
					$value = (int) $value;
					if ( $value < $addition['min_units'] || $value > $addition['max_units'] ) {
						if ( $addition['required'] ?? false ) {
							throw new \Exception( \Voxel\replace_vars( _x( '@addition_name must be between @min_units and @max_units', 'checkout', 'voxel' ), [
								'@addition_name' => $addition['label'],
								'@min_units' => $addition['min_units'],
								'@max_units' => $addition['max_units'],
							] ) );
						}
					}

					if ( $value < 1 ) {
						continue;
					}

					$price_per_unit = $addition['price'];
					$price_per_day = abs( $price_per_unit * $value );
					$price = $price_per_day;
					if ( $addition['repeat'] ?? false ) {
						$price = $price_per_day * $day_count;
					}

					$items[ $addition['id'] ] = [
						'type' => 'numeric',
						'label' => $addition['label'],
						'price_per_unit' => $price_per_unit,
						'price_per_day' => $price_per_day,
						'units' => $value,
						'price' => $price,
					];
				} else {
					$value = (bool) $value;
					if ( ( $addition['required'] ?? false ) && $value !== true ) {
						throw new \Exception( \Voxel\replace_vars( _x( '@addition_name is required', 'checkout', 'voxel' ), [
							'@addition_name' => $addition['label'],
						] ) );
					}

					if ( $value !== true ) {
						continue;
					}

					$price_per_day = abs( $addition['price'] );
					$price = $price_per_day;
					if ( $addition['repeat'] ?? false ) {
						$price = $price_per_day * $day_count;
					}

					$items[ $addition['id'] ] = [
						'type' => 'checkbox',
						'label' => $addition['label'],
						'price' => $price,
						'price_per_day' => $price_per_day,
					];
				}

				// make sure only first item (if exists) is processed for single-select additions
				if ( $field_data['mode'] === 'single' ) {
					break;
				}
			}

			if ( $field_data['mode'] === 'single' && $field_data['required'] && empty( $items ) ) {
				throw new \Exception( \Voxel\replace_vars( _x( '@addition_name is required', 'checkout', 'voxel' ), [
					'@addition_name' => $field_data['label'],
				] ) );
			}

			$this->custom_additions[ $field_key ] = [
				'label' => $field_data['label'],
				'items' => $items,
			];
		}
	}

	public function set_information_fields( $details ) {
		$fields = [];
		foreach ( $this->product_type->get_fields() as $field ) {
			$fields[ $field->get_key() ] = null;
			if ( isset( $details[ $field->get_key() ] ) ) {
				$fields[ $field->get_key() ] = $field->sanitize( $details[ $field->get_key() ] );
			}

			$field->check_validity( $fields[ $field->get_key() ] );
		}

		foreach ( $this->product_type->get_fields() as $field ) {
			$fields[ $field->get_key() ] = $field->prepare_for_storage( $fields[ $field->get_key() ] );
			if ( is_null( $fields[ $field->get_key() ] ) ) {
				unset( $fields[ $field->get_key() ] );
			}
		}

		$this->information_fields = $fields;
	}

	public function get_pricing() {
		if ( ! empty( $this->pricing ) ) {
			return $this->pricing;
		}

		if ( $this->is_using_price_id() ) {
			$price_id = $this->config['price_id'] ?? null;
			$price = $this->stripe->prices->retrieve( $price_id );

			$total = $price->unit_amount;
			$currency = $price->currency;

			if ( ! \Voxel\Stripe\Currencies::is_zero_decimal( $currency ) ) {
				$total /= 100;
			}

			$this->pricing = [
				'base_price' => $total,
				'total' => $total,
				'currency' => $currency,
			];

			if ( $this->product_type->get_payment_mode() === 'subscription' ) {
				$this->pricing['interval'] = [
					'unit' => $price->recurring->interval ?? null,
					'count' => $price->recurring->interval_count ?? null,
				];
			}
		} else {
			$day_count = $this->get_repeat_day_count();
			$base_price = $this->config['base_price'] * $day_count;
			$total = $base_price;
			$currency = \Voxel\get( 'settings.stripe.currency', 'USD' );

			foreach ( $this->additions as $addition ) {
				$total += $addition['price'];
			}

			foreach ( $this->custom_additions as $addition ) {
				foreach ( $addition['items'] as $item ) {
					$total += $item['price'];
				}
			}

			$this->pricing = [
				'base_price' => $base_price,
				'total' => $total,
				'currency' => $currency,
			];

			if ( $this->product_type->get_payment_mode() === 'subscription' ) {
				$this->pricing['interval'] = [
					'unit' => $this->config['interval']['unit'] ?? null,
					'count' => $this->config['interval']['count'] ?? null,
				];
			}
		}

		return $this->pricing;
	}

	public function get_pricing_unit_amount() {
		$unit_amount = $this->get_pricing()['total'];
		if ( ! \Voxel\Stripe\Currencies::is_zero_decimal( $this->get_pricing_currency() ) ) {
			$unit_amount *= 100;
		}

		return $unit_amount;
	}

	public function get_pricing_currency() {
		return \Voxel\get( 'settings.stripe.currency', 'USD' );
	}

	public function get_success_url() {
		return add_query_arg( [
			'vx' => 1,
			'action' => 'stripe.checkout.successful',
			'session_id' => '{CHECKOUT_SESSION_ID}',
		], home_url('/') );
	}

	public function get_cancel_url() {
		return add_query_arg( [
			'vx' => 1,
			'action' => 'stripe.checkout.canceled',
			'session_id' => '{CHECKOUT_SESSION_ID}',
			'redirect_to' => \Voxel\get_redirect_url(),
		], home_url('/') );
	}

	public function checkout() {
		if ( $this->product_type->get_payment_mode() === 'subscription' ) {
			$args = $this->checkout_mode_subscription();
		} else {
			$args = $this->checkout_mode_payment();
		}

		$args = $this->apply_tax_details( $args );
		$args = $this->apply_shipping_details( $args );

		$stripe_session = \Stripe\Checkout\Session::create( $args );
		return $stripe_session;
	}

	protected function apply_tax_details( $args ) {
		$tax_mode = $this->product_type->config('checkout.tax.mode');
		if ( $tax_mode === 'auto' ) {
			$tax_code = $this->product_type->config('checkout.tax.auto.tax_code');
			$tax_behavior = $this->product_type->config('checkout.tax.auto.tax_behavior');

			$args['automatic_tax'] = [
				'enabled' => true,
			];

			$args['line_items'][0]['price_data']['tax_behavior'] = $tax_behavior;
			$args['line_items'][0]['price_data']['product_data']['tax_code'] = $tax_code;
		} elseif ( $tax_mode === 'manual' ) {
			$tax_rates = \Voxel\Stripe::is_test_mode()
				? (array) $this->product_type->config('checkout.tax.manual.test_tax_rates')
				: (array) $this->product_type->config('checkout.tax.manual.tax_rates');

			if ( ! empty( $tax_rates ) ) {
				$args['line_items'][0]['tax_rates'] = $tax_rates;
			}
		}

		$args['tax_id_collection'] = [
			'enabled' => !! $this->product_type->config( 'checkout.tax.auto.tax_id_collection' ),
		];

		return $args;
	}

	protected function apply_shipping_details( $args ) {
		if ( !! $this->product_type->config('checkout.shipping.enabled') ) {
			$allowed_countries = $this->product_type->config('checkout.shipping.allowed_countries');
			$shipping_rates = \Voxel\Stripe::is_test_mode()
				? (array) $this->product_type->config('checkout.shipping.test_shipping_rates')
				: (array) $this->product_type->config('checkout.shipping.shipping_rates');

			if ( ! empty( $allowed_countries ) ) {
				$args['shipping_address_collection'] = [
					'allowed_countries' => $allowed_countries,
				];
			}

			if ( ! empty( $shipping_rates ) ) {
				$args['shipping_options'] = [];
				foreach ( $shipping_rates as $shipping_rate ) {
					$args['shipping_options'][] = [
						'shipping_rate' => $shipping_rate,
					];
				}
			}
		}

		return $args;
	}

	protected function checkout_mode_subscription() {
		$customer = $this->customer->get_or_create_stripe_customer();

		$args = [
			'customer' => $customer->id,
			'mode' => 'subscription',
			'success_url' => $this->get_success_url(),
			'cancel_url' => $this->get_cancel_url(),
			'customer_update' => [
				'address' => 'auto',
				'name' => 'auto',
				'shipping' => 'auto',
			],
			'allow_promotion_codes' => $this->allows_promotion_codes(),
			'subscription_data' => [
				'metadata' => [
					'voxel:payment_for' => 'vendor_product',
				],
			],
		];

		if ( $this->is_using_price_id() ) {
			$args['line_items'] = [ [
				'price' => $this->config['price_id'] ?? null,
				'quantity' => 1,
			] ];
		} else {
			$args['line_items'] = [ [
				'price_data' => [
					'currency' => $this->get_pricing_currency(),
					'unit_amount' => $this->get_pricing_unit_amount(),
					'recurring' => [
						'interval' => $this->config['interval']['unit'] ?? null,
						'interval_count' => $this->config['interval']['count'] ?? null,
					],
					'product_data' => [
						'name' => $this->post->get_title(),
						// 'description' => 'Some product description...', // @todo
						// 'images' => [], // @todo
					],
					'tax_behavior' => 'exclusive',
				],
				'quantity' => 1,
			] ];
		}

		if ( $this->destination ) {
			if ( $this->product_type->config( 'checkout.on_behalf_of' ) ) {
				$args['subscription_data']['on_behalf_of'] = $this->destination->id;
			}

			$args['subscription_data']['application_fee_percent'] = $this->product_type->config( 'checkout.application_fee.amount' );

			$args['subscription_data']['transfer_data'] = [
				'destination' => $this->destination->id,
			];
		}

		return $args;
	}

	protected function checkout_mode_payment() {
		$customer = $this->customer->get_or_create_stripe_customer();

		$args = [
			'customer' => $customer->id,
			'mode' => 'payment',
			'success_url' => $this->get_success_url(),
			'cancel_url' => $this->get_cancel_url(),
			'customer_update' => [
				'address' => 'auto',
				'name' => 'auto',
				'shipping' => 'auto',
			],
			'allow_promotion_codes' => $this->allows_promotion_codes(),
			'payment_intent_data' => [
				'capture_method' => $this->product_type->config( 'settings.payments.capture_method', 'manual' ),
				'metadata' => [
					'voxel:payment_for' => 'vendor_product',
				],
			],
		];

		if ( $this->is_using_price_id() ) {
			$args['line_items'] = [ [
				'price' => $this->config['price_id'] ?? null,
				'quantity' => 1,
			] ];
		} else {
			$args['line_items'] = [ [
				'price_data' => [
					'currency' => $this->get_pricing_currency(),
					'unit_amount' => $this->get_pricing_unit_amount(),
					'product_data' => [
						'name' => $this->post->get_title(),
						// 'description' => 'Some product description...', // @todo
						// 'images' => [], // @todo
					],
					'tax_behavior' => 'exclusive',
				],
				'quantity' => 1,
			] ];
		}

		if ( $this->destination ) {
			if ( $this->product_type->config( 'checkout.on_behalf_of' ) ) {
				$args['payment_intent_data']['on_behalf_of'] = $this->destination->id;
			}

			$args['payment_intent_data']['application_fee_amount'] = $this->product_type->calculate_fee(
				$this->get_pricing_unit_amount()
			);

			$args['payment_intent_data']['transfer_data'] = [
				'destination' => $this->destination->id,
			];
		}

		return $args;
	}
}
