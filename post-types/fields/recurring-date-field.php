<?php

namespace Voxel\Post_Types\Fields;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Recurring_Date_Field extends Base_Post_Field {

	protected $props = [
		'type' => 'recurring-date',
		'label' => 'Recurring Date',
		'allow_multiple' => true,
		'max_date_count' => 3,
		'allow_recurrence' => true,
		'enable_timepicker' => true,
	];

	protected $_upcoming, $_all, $_previous;

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'key' => $this->get_key_model(),
			'description' => $this->get_description_model(),
			'required' => $this->get_required_model(),
			'allow_multiple' => [
				'type' => Form_Models\Switcher_Model::class,
				'label' => 'Enable multiple dates',
				'description' => 'Allow users to enter multiple dates',
			],
			'max_date_count' => [
				'v-if' => 'field.allow_multiple',
				'type' => Form_Models\Number_Model::class,
				'label' => 'Maximum number of dates allowed',
			],
			'allow_recurrence' => [
				'type' => Form_Models\Switcher_Model::class,
				'label' => 'Enable recurring dates',
				'description' => 'Allow users to repeat a date at regular intervals (e.g. every 2 weeks, every 6 months, etc.)',
			],
			'enable_timepicker' => [
				'type' => Form_Models\Switcher_Model::class,
				'label' => 'Enable timepicker',
				'description' => 'Set whether users can also select the time of day when adding a date.',
			],
		];
	}

	public function sanitize( $value ) {
		$sanitized = [];
		$allowed_units = ['day', 'week', 'month', 'year'];

		foreach ( (array) $value as $date ) {
			$start_date = strtotime( $date['startDate'] ?? null );
			$end_date = strtotime( $date['endDate'] ?? null );
			if ( ! $start_date ) {
				continue;
			}

			$is_multiday = !! ( $date['multiday'] ?? false );
			if ( ! ( $is_multiday && $end_date ) ) {
				$end_date = $start_date;
			}

			if ( $end_date < $start_date ) {
				$end_date = $start_date;
			}

			if ( $this->props['enable_timepicker'] ) {
				$start_time = strtotime( $date['startTime'] ?? null ) ?: strtotime( '00:00' );
				$end_time = strtotime( $date['endTime'] ?? null ) ?: strtotime( '00:00' );

				$start_date += 60 * (
					( absint( date( 'H', $start_time ) ) * 60 ) + absint( date( 'i', $start_time ) )
				);

				$end_date += 60 * (
					( absint( date( 'H', $end_time ) ) * 60 ) + absint( date( 'i', $end_time ) )
				);

				// if start time is later than end time within same day events, this just means
				// the event actually ends in the next day, e.g. 8pm to 3am
				if ( $end_date < $start_date ) {
					$end_date += DAY_IN_SECONDS;
				}
			}

			$is_recurring = false;
			if ( $this->props['allow_recurrence'] && ! empty( $date['repeat'] ) ) {
				$is_recurring = true;

				$unit = $date['unit'] ?? null;
				if ( ! in_array( $unit, $allowed_units, true ) ) {
					continue;
				}

				$frequency = absint( $date['frequency'] ?? 0 );
				$until = strtotime( $date['until'] ?? null );
			}

			if ( $is_recurring ) {
				$sanitized[] = [
					'start' => date( 'Y-m-d H:i:s', $start_date ),
					'end' => date( 'Y-m-d H:i:s', $end_date ),
					'frequency' => $frequency,
					'unit' => $unit,
					'until' => $until ? date( 'Y-m-d', $until ) : null,
				];
			} else {
				$sanitized[] = [
					'start' => date( 'Y-m-d H:i:s', $start_date ),
					'end' => date( 'Y-m-d H:i:s', $end_date ),
				];
			}
		}

		if ( empty( $sanitized ) ) {
			return null;
		}

		return $sanitized;
	}

	public function validate( $value ): void {
		if ( ! $this->props['allow_multiple'] && count( $value ) > 1 ) {
			throw new \Exception(
				\Voxel\replace_vars( _x( 'Only one entry allowed in @field_name field', 'field validation', 'voxel' ), [
					'@field_name' => $this->get_label(),
				] )
			);
		}

		if ( $this->props['allow_multiple'] && count( $value ) > $this->props['max_date_count'] ) {
			throw new \Exception(
				\Voxel\replace_vars( _x( 'Only up to @amount entries allowed in @field_name field', 'field validation', 'voxel' ), [
					'@field_name' => $this->get_label(),
					'@amount' => $this->props['max_date_count'],
				] )
			);
		}

		foreach ( $value as $date ) {
			if ( ! empty( $date['unit'] ) ) {
				if ( $date['frequency'] < 1 ) {
					throw new \Exception(
						\Voxel\replace_vars( _x( 'Date frequency is required for @field_name', 'field validation', 'voxel' ), [
							'@field_name' => $this->get_label(),
						] )
					);
				}

				if ( $date['until'] === null ) {
					throw new \Exception(
						\Voxel\replace_vars( _x( 'Repeat until date must be set for @field_name', 'field validation', 'voxel' ), [
							'@field_name' => $this->get_label(),
						] )
					);
				}
			}
		}
	}

	public function update( $value ): void {
		global $wpdb;
		if ( $this->is_empty( $value ) ) {
			delete_post_meta( $this->post->get_id(), $this->get_key() );
			$wpdb->delete( $wpdb->prefix.'voxel_recurring_dates', [
				'post_id' => $this->post->get_id(),
				'field_key' => $this->get_key(),
			] );
		} else {
			update_post_meta( $this->post->get_id(), $this->get_key(), wp_slash( wp_json_encode( $value ) ) );

			// delete previous dates
			$wpdb->delete( $wpdb->prefix.'voxel_recurring_dates', [
				'post_id' => $this->post->get_id(),
				'field_key' => $this->get_key(),
			] );

			// prepare and insert new dates
			$rows = [];
			$timezone = $this->post->get_timezone();
			$reference_date = new \DateTime( '2020-01-01 00:00:00', $this->post->get_timezone() );
			$timezone_offset = $reference_date->format('P');

			foreach ( $value as $date ) {
				$start = new \DateTime( $date['start'], $timezone );
				$start->setTimezone( new \DateTimeZone('UTC') );

				$end = new \DateTime( $date['end'], $timezone );
				$end->setTimezone( new \DateTimeZone('UTC') );

				if ( isset( $date['frequency'] ) ) {
					if ( $date['unit'] === 'week' ) {
						$date['frequency'] *= 7;
						$date['unit'] = 'day';
					} elseif ( $date['unit'] === 'year' ) {
						$date['frequency'] *= 12;
						$date['unit'] = 'month';
					}
				}

				if ( isset( $date['until'] ) ) {
					$until = new \DateTime( $date['until'], $timezone );
					$until->setTimezone( new \DateTimeZone('UTC') );
				}

				$rows[] = sprintf(
					"(%d,'%s','%s','%s','%s',%s,'%s',%s)",
					absint( $this->post->get_id() ),
					esc_sql( $this->post->post_type->get_key() ),
					esc_sql( $this->get_key() ),
					esc_sql( $start->format( 'Y-m-d H:i:s' ) ),
					esc_sql( $end->format( 'Y-m-d H:i:s' ) ),
					isset( $date['frequency'] ) ? esc_sql( $date['frequency'] ) : 'NULL',
					isset( $date['unit'] ) ? esc_sql( $date['unit'] ) : 'NULL',
					isset( $until ) ? '\''.esc_sql( $until->format( 'Y-m-d H:i:s' ) ).'\'' : 'NULL'
				);
			}

			// update database with new values
			if ( ! empty( $rows ) ) {
				$query = "INSERT INTO {$wpdb->prefix}voxel_recurring_dates
					(`post_id`, `post_type`, `field_key`, `start`, `end`, `frequency`, `unit`, `until`) VALUES ";
				$query .= implode( ',', $rows );
				$wpdb->query( $query );
			}
		}
	}

	public function get_value_from_post() {
		return (array) json_decode( get_post_meta(
			$this->post->get_id(), $this->get_key(), true
		), ARRAY_A );
	}

	protected function editing_value() {
		return array_filter( array_map( function( $date ) {
			if ( ! isset( $date['start'], $date['end'] ) ) {
				return null;
			}

			return [
				'multiday' => date( 'Y-m-d', strtotime( $date['start'] ) ) !== date( 'Y-m-d', strtotime( $date['end'] ) ),
				'startDate' => date( 'Y-m-d', strtotime( $date['start'] ) ),
				'startTime' => date( 'H:i', strtotime( $date['start'] ) ),
				'endDate' => date( 'Y-m-d', strtotime( $date['end'] ) ),
				'endTime' => date( 'H:i', strtotime( $date['end'] ) ),
				'repeat' => ( $date['unit'] ?? null ) !== null,
				'frequency' => $date['frequency'] ?? 1,
				'unit' => $date['unit'] ?? 'week',
				'until' => strtotime( $date['until'] ?? null ) ? date( 'Y-m-d', strtotime( $date['until'] ?? null ) ) : null,
			];
		}, (array) $this->get_value() ) );
	}

	protected function frontend_props() {
		wp_enqueue_style( 'pikaday' );
		wp_enqueue_script( 'pikaday' );

		return [
			'max_date_count' => $this->props['allow_multiple'] ? $this->props['max_date_count'] : 1,
			'allow_recurrence' => $this->props['allow_recurrence'],
			'enable_timepicker' => $this->props['enable_timepicker'],
			'units' => [
				'day' => _x( 'Day(s)', 'recurring date field', 'voxel' ),
				'week' => _x( 'Week(s)', 'recurring date field', 'voxel' ),
				'month' => _x( 'Month(s)', 'recurring date field', 'voxel' ),
				'year' => _x( 'Year(s)', 'recurring date field', 'voxel' ),
			],
		];
	}

	public function get_upcoming() {
		if ( $this->_upcoming === null ) {
			$this->_upcoming = \Voxel\Utils\Recurring_Date\get_upcoming(
				$this->get_value(),
				25,
				null,
				null,
				true
			);
		}

		return $this->_upcoming;
	}

	public function get_all() {
		if ( $this->_all === null ) {
			$this->_all = \Voxel\Utils\Recurring_Date\get_upcoming(
				$this->get_value(),
				50,
				null,
				\Voxel\epoch()
			);
		}

		return $this->_all;
	}

	public function get_previous() {
		if ( $this->_previous === null ) {
			$this->_previous = \Voxel\Utils\Recurring_Date\get_previous(
				$this->get_value(),
				25
			);
		}

		return $this->_previous;
	}

	public function exports() {
		return [
			'label' => $this->get_label(),
			'type' => \Voxel\T_OBJECT,
			'has_loopable_props' => true,
			'properties' => [
				'upcoming' => [
					'label' => 'Upcoming',
					'type' => \Voxel\T_OBJECT,
					'loopable' => true,
					'loopcount' => function() {
						return count( $this->get_upcoming() );
					},
					'properties' => [
						'start' => [
							'label' => 'Start date',
							'type' => \Voxel\T_DATE,
							'callback' => function( $index ) {
								return $this->get_upcoming()[ $index ]['start'] ?? null;
							},
						],
						'end' => [
							'label' => 'End date',
							'type' => \Voxel\T_DATE,
							'callback' => function( $index ) {
								return $this->get_upcoming()[ $index ]['end'] ?? null;
							},
						],
						'is_multiday' => [
							'label' => 'Is multi-day?',
							'type' => \Voxel\T_STRING,
							'callback' => function( $index ) {
								$start = $this->get_upcoming()[ $index ]['start'] ?? null;
								$end = $this->get_upcoming()[ $index ]['end'] ?? null;
								return ( $start && $end && date( 'Y-m-d', strtotime( $start ) ) !== date( 'Y-m-d', strtotime( $end ) ) ) ? '1' : '';
							},
						],
						'is_happening_now' => [
							'label' => 'Is happening now?',
							'type' => \Voxel\T_STRING,
							'callback' => function( $index ) {
								$start = $this->get_upcoming()[ $index ]['start'] ?? null;
								$end = $this->get_upcoming()[ $index ]['end'] ?? null;
								$now = \Voxel\now()->getTimestamp();
								return ( $start && $end && strtotime( $start ) <= $now && strtotime( $end ) >= $now ) ? '1' : '';
							},
						],
					],
				],
				'previous' => [
					'label' => 'Previous',
					'type' => \Voxel\T_OBJECT,
					'loopable' => true,
					'loopcount' => function() {
						return count( $this->get_previous() );
					},
					'properties' => [
						'start' => [
							'label' => 'Start date',
							'type' => \Voxel\T_DATE,
							'callback' => function( $index ) {
								return $this->get_previous()[ $index ]['start'] ?? null;
							},
						],
						'end' => [
							'label' => 'End date',
							'type' => \Voxel\T_DATE,
							'callback' => function( $index ) {
								return $this->get_previous()[ $index ]['end'] ?? null;
							},
						],
						'is_multiday' => [
							'label' => 'Is multi-day?',
							'type' => \Voxel\T_STRING,
							'callback' => function( $index ) {
								$start = $this->get_upcoming()[ $index ]['start'] ?? null;
								$end = $this->get_upcoming()[ $index ]['end'] ?? null;
								return ( $start && $end && date( 'Y-m-d', strtotime( $start ) ) !== date( 'Y-m-d', strtotime( $end ) ) ) ? '1' : '';
							},
						],
					],
				],
				'all' => [
					'label' => 'All',
					'type' => \Voxel\T_OBJECT,
					'loopable' => true,
					'loopcount' => function() {
						return count( $this->get_all() );
					},
					'properties' => [
						'start' => [
							'label' => 'Start date',
							'type' => \Voxel\T_DATE,
							'callback' => function( $index ) {
								return $this->get_all()[ $index ]['start'] ?? null;
							},
						],
						'end' => [
							'label' => 'End date',
							'type' => \Voxel\T_DATE,
							'callback' => function( $index ) {
								return $this->get_all()[ $index ]['end'] ?? null;
							},
						],
						'is_multiday' => [
							'label' => 'Is multi-day?',
							'type' => \Voxel\T_STRING,
							'callback' => function( $index ) {
								$start = $this->get_upcoming()[ $index ]['start'] ?? null;
								$end = $this->get_upcoming()[ $index ]['end'] ?? null;
								return ( $start && $end && date( 'Y-m-d', strtotime( $start ) ) !== date( 'Y-m-d', strtotime( $end ) ) ) ? '1' : '';
							},
						],
					],
				],
			],
		];
	}
}
