<?php

namespace Voxel\Post_Types\Fields\Traits;

if ( ! defined('ABSPATH') ) {
	exit;
}

trait Product_Field_Helpers {

	public function cache_fully_booked_days() {
		$fully_booked_days = $this->get_fully_booked_days();
		if ( empty( $fully_booked_days ) ) {
			delete_post_meta( $this->post->get_id(), $this->get_key().'__fully_booked' );
		} else {
			update_post_meta( $this->post->get_id(), $this->get_key().'__fully_booked', wp_slash( wp_json_encode( $fully_booked_days ) ) );
		}

		$this->cache_fully_booked_slots();
	}

	protected function cache_fully_booked_slots() {
		if ( ! ( $product_type = $this->get_product_type() ) ) {
			return;
		}

		if ( ! ( $product_type->get_product_mode() === 'booking' && $product_type->config('calendar.type') === 'booking' && $product_type->config( 'calendar.format' ) === 'slots' ) ) {
			return;
		}

		global $wpdb;

		$slots = [];
		$value = $this->get_value();
		$bookable_per_instance = absint( $value['calendar']['bookable_per_instance'] ?? 1 );

		// get all fully booked slots
		$sql = $wpdb->prepare( "
			SELECT CONCAT_WS( ' ', checkin, timeslot ) AS `slot`, COUNT(id) AS total FROM `{$wpdb->prefix}voxel_orders`
				WHERE post_id = %d
					AND ( `status` = 'completed' || `status` = 'refund_requested' )
					AND product_key = %s
					AND checkin >= %s
					AND timeslot IS NOT NULL
				GROUP BY checkin, timeslot HAVING total >= %d
			",
			$this->post->get_id(),
			$this->get_key(),
			date( 'Y-m-d', time() ),
			$bookable_per_instance
		);

		$results = $wpdb->get_results( $sql );
		foreach ( $results as $result ) {
			$slots[ $result->slot ] = 1;
		}

		if ( empty( $slots ) ) {
			delete_post_meta( $this->post->get_id(), $this->get_key().'__fully_booked_slots' );
		} else {
			update_post_meta( $this->post->get_id(), $this->get_key().'__fully_booked_slots', wp_slash( wp_json_encode( $slots ) ) );
		}
	}

	public function get_fully_booked_days() {
		if ( ! ( $product_type = $this->get_product_type() ) ) {
			return [];
		}

		if ( $product_type->get_product_mode() !== 'booking' ) {
			return [];
		}

		$value = $this->get_value();
		if ( empty( $value ) ) {
			return [];
		}

		$format = $product_type->config( 'calendar.format' );
		$allow_range = $product_type->config( 'calendar.allow_range' );
		$calendar_type = $product_type->config('calendar.type');

		if ( $calendar_type === 'none' ) {
			return [];
		} elseif ( $calendar_type === 'recurring-date' ) {
			$dates = $this->_get_fully_booked_days_from_ranges();
		} else {
			if ( $format === 'days' && $allow_range ) {
				$dates = $this->_get_fully_booked_days_from_ranges();
			} elseif ( $format === 'slots' ) {
				$dates = $this->_get_fully_booked_days_timeslots();
			} else {
				global $wpdb;

				$bookable_per_instance = absint( $value['calendar']['bookable_per_instance'] ?? 1 );

				// get all fully booked days
				$sql = $wpdb->prepare( "
					SELECT checkin, COUNT(id) AS total FROM `{$wpdb->prefix}voxel_orders`
						WHERE post_id = %d AND ( `status` = 'completed' || `status` = 'refund_requested' ) AND product_key = %s AND checkin >= %s
						GROUP BY checkin HAVING total >= %d
					",
					$this->post->get_id(),
					$this->get_key(),
					date( 'Y-m-d', time() ),
					$bookable_per_instance
				);

				$dates = $wpdb->get_col( $sql );
			}
		}

		// merge manually excluded days
		$dates = array_merge( $dates, $value['calendar']['excluded_days'] ?? [] );

		$ranges = \Voxel\merge_ranges( array_map( function( $day ) {
			$days = date_diff(
				\Voxel\epoch(),
				new \DateTime( $day, new \DateTimeZone('UTC') )
			)->days;

			return [ $days, $days ];
		}, $dates ) );

		$ranges = array_map( function( $range ) {
			$start = strtotime( sprintf( '+%d days', $range[0] ), \Voxel\epoch()->getTimestamp() );
			$end = strtotime( sprintf( '+%d days', $range[1] ), \Voxel\epoch()->getTimestamp() );

			if ( $start === $end ) {
				return date( 'Y-m-d', $start );
			}

			return sprintf( '%s..%s', date( 'Y-m-d', $start ), date( 'Y-m-d', $end ) );
		}, $ranges );

		return $ranges;
	}

	protected function _get_fully_booked_days_timeslots() {
		global $wpdb;

		$value = $this->get_value();
		$bookable_per_instance = absint( $value['calendar']['bookable_per_instance'] ?? 1 );
		$timeslots = $value['calendar']['timeslots'] ?? [];
		$weekdays_lookup = array_flip( \Voxel\get_weekday_indexes() );
		$slots_by_day = [];

		foreach ( $timeslots as $group ) {
			$prepared_slots = [];
			foreach ( $group['slots'] as $slot ) {
				$prepared_slots[ sprintf( '%s-%s', $slot['from'], $slot['to'] ) ] = false;
			}

			foreach ( $group['days'] as $day ) {
				if ( ! isset( $slots_by_day[ $day ] ) ) {
					$slots_by_day[ $day ] = $prepared_slots;
				}
			}
		}

		// get all fully booked slots
		$sql = $wpdb->prepare( "
			SELECT checkin, timeslot, COUNT(id) AS total FROM `{$wpdb->prefix}voxel_orders`
				WHERE post_id = %d AND ( `status` = 'completed' || `status` = 'refund_requested' ) AND product_key = %s AND checkin >= %s
				GROUP BY checkin, timeslot HAVING total >= %d
			",
			$this->post->get_id(),
			$this->get_key(),
			date( 'Y-m-d', time() ),
			$bookable_per_instance
		);

		$results = $wpdb->get_results( $sql );
		$dates = [];
		foreach ( $results as $result ) {
			$day_index = absint( date( 'N', strtotime( $result->checkin ) ) ) - 1;
			if ( ! isset( $slots_by_day[ $weekdays_lookup[ $day_index ] ] ) ) {
				continue;
			}

			if ( ! isset( $dates[ $result->checkin ] ) ) {
				$dates[ $result->checkin ] = $slots_by_day[ $weekdays_lookup[ $day_index ] ];
			}

			if ( isset( $dates[ $result->checkin ][ $result->timeslot ] ) ) {
				$dates[ $result->checkin ][ $result->timeslot ] = true;
			}
		}

		// get all dates with all their slots fully booked
		return array_keys( array_filter( $dates, function( $slots ) {
			return ! in_array( false, $slots, true );
		} ) );
	}

	protected function _get_fully_booked_days_from_ranges() {
		global $wpdb;

		$value = $this->get_value();
		$bookable_per_instance = absint( $value['calendar']['bookable_per_instance'] ?? 1 );

		$sql = $wpdb->prepare( "
			SELECT checkin, checkout, COUNT(id) AS total FROM `{$wpdb->prefix}voxel_orders`
				WHERE post_id = %d AND ( `status` = 'completed' || `status` = 'refund_requested' ) AND product_key = %s AND checkout >= %s
				GROUP BY checkin, checkout HAVING total >= %d
			",
			$this->post->get_id(),
			$this->get_key(),
			date( 'Y-m-d', time() ),
			$bookable_per_instance
		);

		$results = $wpdb->get_results( $sql );

		$dates = [];
		foreach ( $results as $range ) {
			$total = absint( $range->total );
			$checkin = strtotime( $range->checkin );
			$checkout = strtotime( $range->checkout );
			$now = time();
			if ( ! ( $checkin && $checkout && $checkin <= $checkout ) ) {
				continue;
			}

			$date = $checkin;

			do {
				if ( $date >= $now ) {
					$key = date( 'Y-m-d', $date );
					if ( ! isset( $dates[ $key ] ) ) {
						$dates[ $key ] = 0;
					}

					$dates[ $key ] += $total;
				}

				$date = strtotime( '+1 day', $date );
			} while ( $date <= $checkout );
		}

		// get all dates that are fully booked
		return array_keys( array_filter( $dates, function( $count ) use ( $bookable_per_instance ) {
			return $count >= $bookable_per_instance;
		} ) );
	}

	public function _get_weekday_linestring() {
		$value = $this->get_value();
		$indexes = \Voxel\get_weekday_indexes();
		$ranges = [];
		for ( $i=0; $i <= 6; $i++ ) {
			$ranges[ $i ] = [ $i, $i ];
		}

		$excluded_weekdays = $value['calendar']['excluded_weekdays'] ?? [];
		foreach ( $excluded_weekdays as $day ) {
			if ( isset( $indexes[ $day ] ) && isset( $ranges[ $indexes[ $day ] ] ) ) {
				unset( $ranges[ $indexes[ $day ] ] );
			}
		}

		$ranges = \Voxel\merge_ranges( $ranges );
		if ( empty( $ranges ) ) {
			return null;
		}

		$strings = array_map( function( $range ) {
			return sprintf( '(%s 0,%s 0)', $range[0], $range[1] );
		}, $ranges );

		return sprintf( 'MULTILINESTRING(%s)', join( ',', $strings ) );
	}

	public function _get_excluded_days_linestring() {
		$ranges = (array) json_decode( get_post_meta(
			$this->post->get_id(), $this->get_key().'__fully_booked', true
		), ARRAY_A );

		$strings = array_filter( array_map( function( $range ) {
			$parts = explode( '..', $range );
			if ( ! strtotime( $parts[0] ?? null ) ) {
				return null;
			}

			$start_day = date_diff(
				\Voxel\epoch(),
				new \DateTime( $parts[0], new \DateTimeZone('UTC') )
			)->days;

			$end_day = $start_day;
			if ( strtotime( $parts[1] ?? null ) ) {
				$end_day = date_diff(
					\Voxel\epoch(),
					new \DateTime( $parts[1], new \DateTimeZone('UTC') )
				)->days;
			}

			return sprintf( '(%s 0,%s 0)', $start_day / 1000, $end_day / 1000 );
		}, $ranges ) );

		if ( empty( $strings ) ) {
			return null;
		}

		return sprintf( 'MULTILINESTRING(%s)', join( ',', $strings ) );
	}
}
