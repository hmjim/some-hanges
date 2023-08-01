<?php

namespace Voxel\Post_Types\Fields;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Product_Field extends Base_Post_Field {
	use Traits\Product_Field_Helpers;

	protected $props = [
		'type' => 'product',
		'label' => 'Product',
		'product-type' => '',
		'recurring-date-field' => '',
	];

	protected $supported_conditions = [
		'enabled' => [
			'label' => 'Is enabled',
			'supported_conditions' => [ 'switcher' ],
		],
	];

	public function get_models(): array {
		$choices = [];
		foreach ( \Voxel\Product_Type::get_all() as $product_type ) {
			$choices[ $product_type->get_key() ] = $product_type->get_label();
		}

		return [
			'label' => $this->get_label_model(),
			'key' => $this->get_key_model(),
			'description' => $this->get_description_model(),
			'product-type' => [
				'type' => Form_Models\Select_Model::class,
				'label' => 'Product type',
				'width' => '1/1',
				'choices' => $choices,
			],
			'recurring-date-field' => function() { ?>
				<div class="ts-form-group ts-col-1-1" v-if="$root.options.product_types[ field['product-type'] ]?.calendar_type === 'recurring-date'">
					<label>Get bookable instances from field:</label>
					<select v-model="field['recurring-date-field']">
						<option v-for="field in $root.getFieldsByType('recurring-date')" :value="field.key">
							{{ field.label }}
						</option>
					</select>
				</div>
			<?php },
			'required' => $this->get_required_model(),
		];
	}

	public function sanitize( $value ) {
		$product_type = $this->get_product_type();
		if ( ! $product_type ) {
			return null;
		}

		$is_using_price_id = $product_type->is_using_price_id();

		$sanitized = [];
		$sanitized['enabled'] = $this->is_required() ? true : ( (bool) ( $value['enabled'] ?? true ) );
		$sanitized['base_price'] = $product_type->has_base_price() ? abs( (float) ( $value['base_price'] ?? 0 ) ) : 0;

		// sanitize recurring price
		if ( $product_type->get_payment_mode() === 'subscription' && ! $is_using_price_id ) {
			$interval_unit = \Voxel\from_list( $value['interval']['unit'] ?? null, [ 'day', 'week', 'month' ], 'month' );
			$interval_limit = ( $interval_unit === 'day' ? 365 : ( $interval_unit === 'week' ? 52 : 12 ) );
			$interval_count = \Voxel\clamp( absint( $value['interval']['count'] ?? 1 ), 1, $interval_limit );

			$sanitized['interval'] = [
				'unit' => $interval_unit,
				'count' => $interval_count,
			];
		}

		// sanitize calendar
		// @todo: validate excluded weekdays, days, timeslots
		if ( $product_type->get_product_mode() === 'booking' ) {
			if ( $product_type->config('calendar.type') === 'booking' ) {
				$calendar = $value['calendar'] ?? [];
				$sanitized['calendar'] = [];
				$sanitized['calendar']['make_available_next'] = absint( $calendar['make_available_next'] ?? null );
				$sanitized['calendar']['bookable_per_instance'] = absint( $calendar['bookable_per_instance'] ?? null );

				$weekday_indexes = \Voxel\get_weekday_indexes();
				$sanitized['calendar']['excluded_weekdays'] = array_filter(
					(array) ( $calendar['excluded_weekdays'] ?? [] ),
					function( $weekday ) use ( $weekday_indexes ) { return isset( $weekday_indexes[ $weekday ] ); }
				);

				$sanitized['calendar']['excluded_days'] = array_filter( array_map( function( $day ) {
					$timestamp = strtotime( $day );
					return $timestamp ? date( 'Y-m-d', $timestamp ) : null;
				}, (array) ( $calendar['excluded_days'] ?? [] ) ) );

				if ( $product_type->config('calendar.format') === 'slots' ) {
					$sanitized['calendar']['timeslots'] = [];
					foreach ( (array) ( $calendar['timeslots'] ?? [] ) as $slot_group ) {
						$sanitized['calendar']['timeslots'][] = [
							'days' => $slot_group['days'] ?? [], // @todo: validate weekdays
							'slots' => array_filter( array_map( function( $slot ) {
								$from = strtotime( $slot['from'] ?? null );
								$to = strtotime( $slot['to'] ?? null );
								if ( ! ( $from && $to ) ) {
									return null;
								}

								return [
									'from' => date( 'H:i', $from ),
									'to' => date( 'H:i', $to ),
								];
							}, $slot_group['slots'] ?? [] ) ),
						];
					}
				}
			} elseif ( $product_type->config('calendar.type') === 'recurring-date' ) {
				$calendar = $value['calendar'] ?? [];
				$sanitized['calendar'] = [];
				$sanitized['calendar']['make_available_next'] = absint( $calendar['make_available_next'] ?? null );
				$sanitized['calendar']['bookable_per_instance'] = absint( $calendar['bookable_per_instance'] ?? null );
			}
		}

		// sanitize additions
		if ( ! $is_using_price_id ) {
			if ( ! empty( $product_type->get_additions() ) ) {
				$sanitized['additions'] = [];
				foreach ( $product_type->get_additions() as $addition ) {
					$sanitized['additions'][ $addition->get_key() ] = $addition->sanitize_config(
						$value['additions'][ $addition->get_key() ] ?? []
					);
				}
			}
		}

		// sanitize vendor notes
		if ( $product_type->config('settings.notes.enabled') ) {
			$sanitized['notes_enabled'] = (bool) ( $value['notes_enabled'] ?? true );
			$sanitized['notes'] = sanitize_textarea_field( $value['notes'] ?? null );
		}

		if ( $is_using_price_id ) {
			$sanitized['price_id'] = sanitize_text_field( $value['price_id'] ?? null );
		}

		// automatic deliverables
		if ( $product_type->config( 'settings.deliverables.enabled' ) && in_array( 'automatic', (array) $product_type->config( 'settings.deliverables.delivery_methods' ), true ) ) {
			$file_field = $this->_get_deliverables_field();
			$sanitized['deliverables'] = $file_field->sanitize( $value['deliverables'] ?? [] );
		}

		return $sanitized;
	}

	public function validate( $value ): void {
		$product_type = $this->get_product_type();
		if ( $product_type && $value['enabled'] ) {
			if ( ! $product_type->is_using_price_id() ) {
				foreach ( $product_type->get_additions() as $addition ) {
					$addition->validate_config(
						$value['additions'][ $addition->get_key() ] ?? []
					);
				}
			}
		}

		// automatic deliverables
		if ( ! empty( $value['deliverables'] ) ) {
			$file_field = $this->_get_deliverables_field();
			$file_field->validate( $value['deliverables'] );
		}
	}

	public function update( $value ): void {
		if ( isset( $value['deliverables'] ) ) {
			$file_field = $this->_get_deliverables_field();
			$value['deliverables'] = $file_field->prepare_for_storage( $value['deliverables'] );
		}

		if ( $this->is_empty( $value ) ) {
			delete_post_meta( $this->post->get_id(), $this->get_key() );
		} else {
			update_post_meta( $this->post->get_id(), $this->get_key(), wp_slash( wp_json_encode( $value ) ) );
		}

		// calculate and cache fully booked days
		$this->cache_fully_booked_days();
	}

	private function _get_deliverables_field() {
		$product_type = $this->get_product_type();
		return new \Voxel\Product_Types\Order_Comments\Comment_Deliverables_Field( [
			'key' => $this->get_id().'.deliverables',
			'allowed-types' => $product_type->config( 'settings.deliverables.uploads.allowed_file_types' ),
			'max-size' => $product_type->config( 'settings.deliverables.uploads.max_size' ),
			'max-count' => $product_type->config( 'settings.deliverables.uploads.max_count' ),
		] );
	}

	public function get_value_from_post() {
		$product_type = $this->get_product_type();
		if ( ! $product_type ) {
			return null;
		}

		$value = (array) json_decode( get_post_meta(
			$this->post->get_id(), $this->get_key(), true
		), ARRAY_A );

		if ( ! isset( $value['enabled'] ) ) {
			$value['enabled'] = $this->is_required();
		}

		if ( ! $product_type->has_base_price() || ! isset( $value['base_price'] ) ) {
			$value['base_price'] = $product_type->get_default_base_price();
		}

		return $value;
	}

	public static function is_repeatable(): bool {
		return false;
	}

	public function check_dependencies() {
		$product_type = $this->get_product_type();
		if ( ! $product_type ) {
			throw new \Exception( 'Product type not set.' );
		}
	}

	protected function frontend_props() {
		wp_enqueue_style( 'pikaday' );
		wp_enqueue_script( 'pikaday' );

		$product_type = $this->get_product_type();
		$config = $product_type->get_config();
		$notes = $config['notes'] ?? [];
		$value = $this->get_value();

		$props = [
			'product_mode' => $product_type->get_product_mode(),
			'payment_mode' => $product_type->get_payment_mode(),
			'is_using_price_id' => $product_type->is_using_price_id(),
			'has_base_price' => $product_type->has_base_price(),
			'recurring_date_field' => $this->props['recurring-date-field'],
			'weekdays' => \Voxel\get_weekdays(),
			'notes' => [
				'enabled' => $config['settings']['notes']['enabled'] ?? true,
			],
			'l10n' => $config['settings']['l10n']['field'],
			'additions' => array_values( array_map( function( $addition ) use ( $value ) {
				$props = $addition->get_props();
				$props['values'] = $addition->sanitize_config( [] );
				if ( $value && isset( $value['additions'][ $addition->get_key() ] ) ) {
					$props['values'] = $addition->sanitize_config( $value['additions'][ $addition->get_key() ]  );
				}

				$props['icon_markup'] = \Voxel\get_icon_markup( $props['icon'] ?? '' );

				return $props;
			}, $product_type->get_additions() ) ),
			'intervals' => [
				'day' => _x( 'Day(s)', 'product field', 'voxel' ),
				'week' => _x( 'Week(s)', 'product field', 'voxel' ),
				'month' => _x( 'Month(s)', 'product field', 'voxel' ),
			],
			'interval_limits' => [
				'day' => 365,
				'week' => 52,
				'month' => 12,
			],
			'deliverables' => [
				'label' => _x( 'Downloads', 'product field', 'voxel' ),
				'enabled' => $product_type->config( 'settings.deliverables.enabled' ) && in_array( 'automatic', (array) $product_type->config( 'settings.deliverables.delivery_methods' ), true ),
				'allowed_file_types' => $product_type->config( 'settings.deliverables.uploads.allowed_file_types' ),
				'max_size' => $product_type->config( 'settings.deliverables.uploads.max_size' ),
				'max_count' => $product_type->config( 'settings.deliverables.uploads.max_count' ),
			],
		];

		if ( $product_type->get_product_mode() === 'booking' ) {
			$props['calendar'] = [
				'type' => $config['calendar']['type'],
				'format' => $config['calendar']['format'],
			];
		}

		return $props;
	}

	protected function editing_value() {
		$value = $this->get_value();
		if ( ! $product_type = $this->get_product_type() ) {
			return null;
		}

		return [
			'enabled' => $value['enabled'] ?? false,
			'base_price' => $value['base_price'] ?? $product_type->get_default_base_price(),
			'notes' => $value['notes'] ?? null,
			'notes_enabled' => $value['notes_enabled'] ?? false,
			'calendar' => $value['calendar'] ?? [],
			'interval' => $value['interval'] ?? [],
			'price_id' => $value['price_id'] ?? null,
			'deliverables' => array_map( function( $file ) {
				$file['source'] = 'existing';
				return $file;
			}, $this->get_deliverables() ?? [] ),
		];
	}

	public function get_deliverables() {
		$value = $this->get_value();
		$product_type = $this->get_product_type();
		if ( ! $product_type && $product_type->config( 'settings.deliverables.enabled' ) ) {
			return null;
		}

		if ( ! in_array( 'automatic', (array) $product_type->config( 'settings.deliverables.delivery_methods' ), true ) ) {
			return null;
		}

		$ids = explode( ',', (string) ( $value['deliverables'] ?? '' ) );
		$ids = array_filter( array_map( 'absint', $ids ) );
		$files = [];

		foreach ( $ids as $attachment_id ) {
			if ( $attachment = get_post( $attachment_id ) ) {
				$display_filename = get_post_meta( $attachment_id, '_display_filename', true );
				$files[] = [
					'id' => $attachment->ID,
					'name' => ! empty( $display_filename ) ? $display_filename : wp_basename( get_attached_file( $attachment->ID ) ),
					'type' => $attachment->post_mime_type,
				];
			}
		}

		return $files;
	}

	public function get_product_type() {
		return \Voxel\Product_Type::get( $this->props['product-type'] );
	}

	public function get_product_form_config() {
		$value = $this->get_value();
		if ( ! ( $product_type = $this->get_product_type() ) || ! ( $value && $value['enabled'] ) ) {
			return null;
		}

		$calendar = $value['calendar'] ?? [];
		$additions = [];
		foreach ( $product_type->get_additions() as $addition ) {
			$addition->set_field( $this );
			if ( ! $addition->is_enabled() ) {
				continue;
			}

			$additions[ $addition->get_key() ] = $addition->get_product_form_config();
		}

		$fields = [];
		foreach ( $product_type->get_fields() as $field ) {
			$fields[ $field->get_key() ] = $field->get_frontend_config();
		}

		return [
			'id' => substr( md5( $this->post->get_id().'-'.$this->get_key() ), 0, 6 ),
			'product_mode' => $product_type->get_product_mode(),
			'mode' => $product_type->get_payment_mode(),
			'enabled' => $value['enabled'],
			'base_price' => $value['base_price'],
			'additions' => $additions,
			'custom_additions' => $this->get_custom_additions(),
			'fields' => $fields,
			'calendar' => [
				'type' => $product_type->config('calendar.type'),
				'format' => $product_type->config('calendar.format'),
				'allow_range' => $product_type->config('calendar.allow_range'),
				'range_mode' => $product_type->config('calendar.range_mode'),
				'make_available_next' => $calendar['make_available_next'] ?? 180,
				'excluded_weekdays' => $calendar['excluded_weekdays'] ?? [],
				'excluded_days' => $this->get_excluded_days(),
				'excluded_slots' => $this->get_excluded_slots(),
				'timeslots' => $calendar['timeslots'] ?? [],
			],
			'recurring_date' => [
				'bookable' => $this->get_bookable_recurring_dates(),
			],
			'is_user_logged_in' => is_user_logged_in(),
			'auth_url' => \Voxel\get_auth_url(),
			'platform_fee' => [
				'type' => $product_type->config( 'checkout.application_fee.type' ),
				'amount' => $product_type->config( 'checkout.application_fee.amount' ),
			],
			'currency' => [
				'code' => \Voxel\get( 'settings.stripe.currency', 'usd' ),
				'is_zero_decimal' => \Voxel\Stripe\Currencies::is_zero_decimal( \Voxel\get( 'settings.stripe.currency', 'usd' ) ),
			],
			'values' => (object) $this->get_product_form_default_values(),
			'l10n' => $product_type->config( 'settings.l10n.form' ),
			'skip_main_step' => $product_type->config('settings.skip_main_step'),
		];
	}

	public function get_bookable_recurring_dates() {
		$value = $this->get_value();
		if ( ! ( $product_type = $this->get_product_type() ) || ! $value ) {
			return [];
		}

		if ( ! ( $product_type->get_product_mode() === 'booking' && $product_type->config('calendar.type') === 'recurring-date' ) ) {
			return [];
		}

		$recurring_date_field = $this->post->get_field( $this->props['recurring-date-field'] );
		if ( ! $recurring_date_field ) {
			return [];
		}

		$calendar = $value['calendar'] ?? [];
		$bookable_dates = \Voxel\Utils\Recurring_Date\get_upcoming(
			$recurring_date_field->get_value(),
			20,
			date('Y-m-d', \Voxel\utc()->modify( sprintf(
				'+%d days',
				$calendar['make_available_next'] ?? 180
			) )->getTimestamp() )
		);

		$bookable_dates = array_map( function( $date ) {
			$start = \Voxel\date_format( strtotime( $date['start'] ) );
			$end = \Voxel\date_format( strtotime( $date['end'] ) );
			$date['formatted'] = $start === $end ? $start : sprintf( '%s - %s', $start, $end );

			return $date;
		}, $bookable_dates );

		return $bookable_dates;
	}

	public function get_excluded_days() {
		$value = $this->get_value();
		if ( ! ( $product_type = $this->get_product_type() ) || ! $value ) {
			return [];
		}

		$calendar = $value['calendar'] ?? [];
		$excluded_days = $calendar['excluded_days'] ?? [];

		$fully_booked = (array) json_decode( get_post_meta(
			$this->post->get_id(), $this->get_key().'__fully_booked', true
		), ARRAY_A );

		if ( ! empty( $fully_booked ) ) {
			foreach ( $fully_booked as $booked_range ) {
				$parts = explode( '..', $booked_range );
				if ( ! strtotime( $parts[0] ?? null ) ) {
					return null;
				}

				$start_day = new \DateTime( $parts[0], new \DateTimeZone('UTC') );
				$end_day = $start_day;
				if ( strtotime( $parts[1] ?? null ) ) {
					$end_day = new \DateTime( $parts[1], new \DateTimeZone('UTC') );
				}

				while ( $start_day < $end_day ) {
					$excluded_days[] = $start_day->format('Y-m-d');
					$start_day->modify('+1 day');
				}

				$excluded_days[] = $end_day->format('Y-m-d');
			}
		}

		return $excluded_days;
	}

	public function get_excluded_slots() {
		return (array) json_decode( get_post_meta(
			$this->post->get_id(), $this->get_key().'__fully_booked_slots', true
		), ARRAY_A );
	}

	protected function get_product_form_default_values() {
		$referer = wp_validate_redirect( wp_get_referer() );
		if ( empty( $referer ) ) {
			return [];
		}

		$post_type = $this->post->post_type;
		$product_type = $this->get_product_type();
		$query_string = parse_url( $referer, PHP_URL_QUERY );
		parse_str( (string) $query_string, $params );

		$values = [
			'booking' => [],
			'additions' => [],
		];

		foreach ( $post_type->get_filters() as $filter ) {
			if ( ! isset( $params[ $filter->get_key() ] ) ) {
				continue;
			}

			if ( $filter->get_type() === 'availability' && $filter->get_prop('source') === $this->get_key() ) {
				if ( $value = $filter->parse_value( $params[ $filter->get_key() ] ) ) {
					$values['booking'] = [
						'start' => $value['start'],
						'end' => $value['end'],
					];
				}
			}

			if ( $filter->get_type() === 'stepper' && str_starts_with( $filter->get_prop('source'), $this->get_key().'->' ) ) {
				$addition_key = substr( $filter->get_prop('source'), mb_strlen( $this->get_key().'->' ) );
				if ( ( $addition = $product_type->get_addition( $addition_key ) ) && $addition->get_type() === 'numeric' ) {
					if ( $value = $filter->parse_value( $params[ $filter->get_key() ] ) ) {
						$values['additions'][ $addition->get_key() ] = $value;
					}
				}
			}
		}

		return $values;
	}

	public function get_custom_additions() {
		$custom_additions = [];
		foreach ( $this->post->get_fields() as $field ) {
			if ( $field->get_type() === 'repeater' && $field->get_prop('additions_enabled') && $field->get_prop('additions_field') === $this->get_key() ) {
				$items = [];
				foreach ( $field->get_value() as $index => $row ) {
					$item_details = [
						'price' => abs( (float) ( $row['meta:additions']['price'] ?? 0 ) ),
						'label' => sanitize_text_field( $row['meta:additions']['label'] ?? '' ),
						'has_quantity' => !! ( $row['meta:additions']['has_quantity'] ?? false ),
						'min_units' => is_numeric( $row['meta:additions']['min'] ) ? abs( $row['meta:additions']['min'] ) : null,
						'max_units' => is_numeric( $row['meta:additions']['max'] ) ? abs( $row['meta:additions']['max'] ) : null,
						'value' => 0,
					];

					if ( $field->get_prop('additions_mode') === 'multiple' ) {
						$item_details['required'] = !! ( $row['meta:additions']['required'] ?? false );
						$item_details['repeat'] = $field->_custom_additions_are_repeatable() && $row['meta:additions']['repeat'] ?? false;
					} elseif ( $field->get_prop('additions_mode') === 'single' ) {
						$item_details['repeat'] = $field->_custom_additions_are_repeatable() && $field->get_prop('additions_repeat');
					}

					$item_details['id'] = substr( md5(
						join( '-', [ $field->get_key(), $index, $item_details['label'], $item_details['price'] ] )
					), 0, 10 );

					if ( ! empty( $item_details['label'] ) ) {
						$items[] = $item_details;
					}
				}

				if ( ! empty( $items ) ) {
					$custom_additions[ $field->get_key() ] = [
						'key' => $field->get_key(),
						'label' => $field->get_label(),
						'mode' => $field->get_prop('additions_mode'),
						'items' => $items,
					];

					if ( $field->get_prop('additions_mode') === 'single' ) {
						$custom_additions[ $field->get_key() ]['required'] = $field->get_prop( 'additions_required' );
					}
				}
			}
		}

		return $custom_additions;
	}

	public function exports() {
		$additions = [];
		if ( $product_type = $this->get_product_type() ) {
			foreach ( $product_type->get_additions() as $addition ) {
				$addition->set_field( $this );
				if ( $exports = $addition->exports() ) {
					$additions[ $addition->get_key() ] = $exports;
				}
			}
		}

		return [
			'type' => \Voxel\T_OBJECT,
			'label' => $this->get_label(),
			'properties' => [
				'is_enabled' => [
					'label' => 'Is enabled?',
					'type' => \Voxel\T_STRING,
					'callback' => function() {
						$value = $this->get_value();
						return ( $value['enabled'] ?? true ) ? '1' : '';
					},
				],
				'base_price' => [
					'label' => 'Base price',
					'type' => \Voxel\T_NUMBER,
					'callback' => function() {
						$value = $this->get_value();
						if ( ! ( $value['enabled'] ?? true ) ) {
							return '';
						}

						return $value['base_price'] ?? '';
					},
				],
				'min_price' => [
					'label' => 'Minimum price',
					'type' => \Voxel\T_STRING,
					'callback' => function() {
						$value = $this->get_value();
						if ( ! ( $value['enabled'] ?? true ) ) {
							return '';
						}

						$amount = ! empty( $value['base_price'] ) ? (float) $value['base_price'] : 0;
						if ( $product_type = $this->get_product_type() ) {
							foreach ( $product_type->get_additions() as $addition ) {
								if ( $addition->get_prop('required_in_checkout') ) {
									if ( $addition->get_type() === 'numeric' ) {
										$min_units = $value['additions'][ $addition->get_key() ]['min'] ?? 1;
										$price_per_unit = $value['additions'][ $addition->get_key() ]['price'] ?? 0;
										$amount += (float) $min_units * (float) $price_per_unit;
									}

									if ( $addition->get_type() === 'select' ) {
										$choices = $value['additions'][ $addition->get_key() ]['choices'] ?? [];
										$prices = [];

										foreach ( $choices as $choice ) {
											if ( $choice['enabled'] && is_numeric( $choice['price'] ) ) {
												$prices[] = $choice['price'];
											}
										}

										$amount += ! empty( $prices ) ? min( $prices ) : 0;
									}
								}
							}
						}

						return $amount;
					},
				],
				'additions' => [
					'type' => \Voxel\T_OBJECT,
					'label' => 'Additions',
					'properties' => $additions,
				],
			],
		];
	}
}
