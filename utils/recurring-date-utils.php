<?php

namespace Voxel\Utils\Recurring_Date;

if ( ! defined('ABSPATH') ) {
	exit;
}

function get_current_start_query( $range_start, $range_end ) {
	$range_start = esc_sql( $range_start );
	$range_end = esc_sql( $range_end );

	return <<<SQL
		CASE
			WHEN `start` > '{$range_start}' THEN `start`
			WHEN (`unit` = 'DAY') THEN (
				IF(
					DATE_ADD( `end`, INTERVAL ( `frequency` * FLOOR(
						( TIMESTAMPDIFF( DAY, `start`, '{$range_start}' ) / `frequency` )
					) ) DAY ) BETWEEN '{$range_start}' AND '{$range_end}',
					DATE_ADD( `end`, INTERVAL ( `frequency` * FLOOR(
						( TIMESTAMPDIFF( DAY, `start`, '{$range_start}' ) / `frequency` )
					) ) DAY ),
					DATE_ADD( `start`, INTERVAL ( `frequency` * CEIL(
						( TIMESTAMPDIFF( DAY, `start`, '{$range_start}' ) / `frequency` ) + 0.00001
					) ) DAY )
				)
			)
			WHEN (`unit` = 'MONTH') THEN (
				IF(
					DATE_ADD( `end`, INTERVAL ( `frequency` * FLOOR(
						( TIMESTAMPDIFF( MONTH, `start`, '{$range_start}' ) / `frequency` )
					) ) MONTH ) BETWEEN '{$range_start}' AND '{$range_end}',
					DATE_ADD( `end`, INTERVAL ( `frequency` * FLOOR(
						( TIMESTAMPDIFF( MONTH, `start`, '{$range_start}' ) / `frequency` )
					) ) MONTH ),
					DATE_ADD( `start`, INTERVAL ( `frequency` * CEIL(
						( TIMESTAMPDIFF( MONTH, `start`, '{$range_start}' ) / `frequency` ) + 0.00001
					) ) MONTH )
				)
			)
			ELSE `start`
		END AS current_start
	SQL;
}

function get_where_clause( $range_start, $range_end, $input_mode = 'date-range', $match_ongoing = true ) {
	$range_start = esc_sql( $range_start );
	$range_end = esc_sql( $range_end );

	if ( $input_mode === 'single-date' ) {
		$search_date = $range_start;
		$query = <<<SQL
			( `start` <= '{$search_date}' AND `end` >= '{$search_date}' )
			OR ( `unit` = 'DAY' AND (
				DATE_ADD( `start`, INTERVAL ( `frequency` * CEIL(
					( TIMESTAMPDIFF( DAY, `start`, '{$search_date}' ) / `frequency` )
				) ) DAY ) <= LEAST( '{$search_date}', `until` )
				AND DATE_ADD( `end`, INTERVAL ( `frequency` * CEIL(
					( TIMESTAMPDIFF( DAY, `start`, '{$search_date}' ) / `frequency` )
				) ) DAY ) >= '{$search_date}'
			) )
			OR ( `unit` = 'MONTH' AND (
				DATE_ADD( `start`, INTERVAL ( `frequency` * CEIL(
					( TIMESTAMPDIFF( MONTH, `start`, '{$search_date}' ) / `frequency` )
				) ) MONTH ) <= LEAST( '{$search_date}', `until` )
				AND DATE_ADD( `end`, INTERVAL ( `frequency` * CEIL(
					( TIMESTAMPDIFF( MONTH, `start`, '{$search_date}' ) / `frequency` )
				) ) MONTH ) >= '{$search_date}'
			) )
		SQL;
	} else {
		$query = <<<SQL
			( `start` BETWEEN '{$range_start}' AND '{$range_end}' )
			OR ( `start` <= '{$range_start}' AND `end` >= '{$range_end}' )
			OR ( `unit` = 'DAY' AND (
				DATE_ADD( `start`, INTERVAL ( `frequency` * CEIL(
					( TIMESTAMPDIFF( DAY, `start`, '{$range_start}' ) / `frequency` ) + 0.00001
				) ) DAY ) <= LEAST( '{$range_end}', `until` )
			) )
			OR ( `unit` = 'MONTH' AND (
				DATE_ADD( `start`, INTERVAL ( `frequency` * CEIL(
					( TIMESTAMPDIFF( MONTH, `start`, '{$range_start}' ) / `frequency` ) + 0.00001
				) ) MONTH ) <= LEAST( '{$range_end}', `until` )
			) )
		SQL;

		if ( $match_ongoing ) {
			$query .= <<<SQL
				OR ( `end` BETWEEN '{$range_start}' AND '{$range_end}' )
				OR ( `unit` = 'DAY' AND (
					DATE_ADD( `end`, INTERVAL ( `frequency` * FLOOR(
						( TIMESTAMPDIFF( DAY, `start`, '{$range_start}' ) / `frequency` )
					) ) DAY ) BETWEEN '{$range_start}' AND LEAST( '{$range_end}', `until` )
				) )
				OR ( `unit` = 'MONTH' AND (
					DATE_ADD( `end`, INTERVAL ( `frequency` * FLOOR(
						( TIMESTAMPDIFF( MONTH, `start`, '{$range_start}' ) / `frequency` )
					) ) MONTH ) BETWEEN '{$range_start}' AND LEAST( '{$range_end}', `until` )
				) )
			SQL;
		}
	}

	return $query;
}

function get_upcoming( $recurring_dates, $limit = 10, $max = null, $reference_date = null, $include_ongoing = false ) {
	$next = [];

	if ( $reference_date instanceof \DateTime || $reference_date instanceof \DateTimeImmutable ) {
		$now = $reference_date;
	} else {
		$now = \Voxel\now();
	}

	foreach ( $recurring_dates as $date ) {
		$start = date_create_from_format( 'Y-m-d H:i:s', $date['start'] );
		$end = date_create_from_format( 'Y-m-d H:i:s', $date['end'] );
		$until = isset( $date['until'] ) ? date_create_from_format( 'Y-m-d', $date['until'] ) : null;
		$count = $limit;

		if ( ! ( $start && $end ) ) {
			continue;
		}

		if ( $start >= $now || ( $include_ongoing && $start <= $now && $end >= $now ) ) {
			$next[] = [
				'start' => $start->format( 'Y-m-d H:i:s' ),
				'end' => $end->format( 'Y-m-d H:i:s' ),
			];
			$count--;
		}

		$frequency = isset( $date['frequency'] ) ? absint( $date['frequency'] ) : null;
		$unit = \Voxel\from_list( $date['unit'] ?? null, [ 'day', 'week', 'month', 'year' ] );

		if ( ! ( $frequency >= 1 && $unit && $until && $until > $now ) ) {
			continue;
		}

		if ( $unit === 'week' ) {
			$unit = 'day';
			$frequency *= 7;
		} elseif ( $unit === 'year' ) {
			$unit = 'month';
			$frequency *= 12;
		}

		if ( $start < $now ) {
			if ( $unit === 'day' ) {
				$days_to_add = $frequency * ceil( $now->diff( $start )->days / $frequency );
				$start->modify( sprintf( '+%d days', $days_to_add ) );
				$end->modify( sprintf( '+%d days', $days_to_add ) );

				if ( ( ! $include_ongoing && $start < $now ) || ( $include_ongoing && $end < $now ) ) {
					$start->modify( sprintf( '+%d days', $frequency ) );
					$end->modify( sprintf( '+%d days', $frequency ) );
				}
			} elseif ( $unit === 'month' ) {
				$diff = $now->diff( $start );
				$months_to_add = $frequency * ceil( ( $diff->m + ( $diff->y * 12 ) ) / $frequency );
				$start->modify( sprintf( '+%d months', $months_to_add ) );
				$end->modify( sprintf( '+%d months', $months_to_add ) );

				if ( ( ! $include_ongoing && $start < $now ) || ( $include_ongoing && $end < $now ) ) {
					$start->modify( sprintf( '+%d months', $frequency ) );
					$end->modify( sprintf( '+%d months', $frequency ) );
				}
			}

			$next[] = [
				'start' => $start->format( 'Y-m-d H:i:s' ),
				'end' => $end->format( 'Y-m-d H:i:s' ),
			];
			$count--;
		}

		for ( $i=0; $i < $count; $i++ ) {
			if ( $unit === 'day' ) {
				$start->modify( sprintf( '+%d days', $frequency ) );
				$end->modify( sprintf( '+%d days', $frequency ) );
			} elseif ( $unit === 'month' ) {
				$start->modify( sprintf( '+%d months', $frequency ) );
				$end->modify( sprintf( '+%d months', $frequency ) );
			}

			if ( $start > $until ) {
				break;
			}

			$next[] = [
				'start' => $start->format( 'Y-m-d H:i:s' ),
				'end' => $end->format( 'Y-m-d H:i:s' ),
			];
		}
	}

	usort( $next, function( $a, $b ) {
		return strtotime( $a['start'] ) - strtotime( $b['start'] );
	} );

	$next = array_slice( $next, 0, $limit );

	if ( $max && $timestamp = strtotime( $max ) ) {
		$next = array_filter( $next, function( $date ) use ( $timestamp ) {
			return strtotime( $date['start'] ) <= $timestamp;
		} );
	}

	return $next;
}

function get_previous( $recurring_dates, $limit = 10, $reference_date = null ) {
	$previous = [];

	if ( $reference_date instanceof \DateTime || $reference_date instanceof \DateTimeImmutable ) {
		$now = $reference_date;
	} else {
		$now = \Voxel\now();
	}

	foreach ( $recurring_dates as $date ) {
		$start = date_create_from_format( 'Y-m-d H:i:s', $date['start'] );
		$end = date_create_from_format( 'Y-m-d H:i:s', $date['end'] );
		$until = isset( $date['until'] ) ? date_create_from_format( 'Y-m-d', $date['until'] ) : null;
		$count = $limit;

		if ( ! ( $start && $end ) ) {
			continue;
		}

		if ( $end >= $now ) {
			continue;
		}

		$frequency = isset( $date['frequency'] ) ? absint( $date['frequency'] ) : null;
		$unit = \Voxel\from_list( $date['unit'] ?? null, [ 'day', 'week', 'month', 'year' ] );

		if ( ! ( $frequency >= 1 && $unit && $until ) ) {
			$previous[] = [
				'start' => $start->format( 'Y-m-d H:i:s' ),
				'end' => $end->format( 'Y-m-d H:i:s' ),
			];
			continue;
		}

		// make sure reference is between first start and repeat end
		$ref = ( $until < $now ) ? clone $until : clone $now;
		if ( $ref < $start ) {
			continue;
		}

		if ( $unit === 'week' ) {
			$unit = 'day';
			$frequency *= 7;
		} elseif ( $unit === 'year' ) {
			$unit = 'month';
			$frequency *= 12;
		}

		if ( $unit === 'day' ) {
			$original_start = clone $start;

			$days_to_add = $frequency * ceil( $now->diff( $start )->days / $frequency );
			$start->modify( sprintf( '+%d days', $days_to_add ) );
			$end->modify( sprintf( '+%d days', $days_to_add ) );

			if ( $end <= $now ) {
				$previous[] = [
					'start' => $start->format( 'Y-m-d H:i:s' ),
					'end' => $end->format( 'Y-m-d H:i:s' ),
				];
			}

			// find previous n recurrences
			for ( $i=0; $i < $count; $i++ ) {
				$start->modify( sprintf( '-%d days', $frequency ) );
				$end->modify( sprintf( '-%d days', $frequency ) );

				// don't include dates before the initial start date
				if ( $start < $original_start ) {
					break;
				}

				if ( $end > $now ) {
					continue;
				}

				$previous[] = [
					'start' => $start->format( 'Y-m-d H:i:s' ),
					'end' => $end->format( 'Y-m-d H:i:s' ),
				];
			}
		} elseif ( $unit === 'month' ) {
			$original_start = clone $start;

			$diff = $now->diff( $start );
			$months_to_add = $frequency * ceil( ( $diff->m + ( $diff->y * 12 ) ) / $frequency );
			$start->modify( sprintf( '+%d months', $months_to_add ) );
			$end->modify( sprintf( '+%d months', $months_to_add ) );

			if ( $end <= $now ) {
				$previous[] = [
					'start' => $start->format( 'Y-m-d H:i:s' ),
					'end' => $end->format( 'Y-m-d H:i:s' ),
				];
			}

			// find previous n recurrences
			for ( $i=0; $i < $count; $i++ ) {
				$start->modify( sprintf( '-%d months', $frequency ) );
				$end->modify( sprintf( '-%d months', $frequency ) );

				// don't include dates before the initial start date
				if ( $start < $original_start ) {
					break;
				}

				if ( $end > $now ) {
					continue;
				}

				$previous[] = [
					'start' => $start->format( 'Y-m-d H:i:s' ),
					'end' => $end->format( 'Y-m-d H:i:s' ),
				];
			}
		}
	}

	usort( $previous, function( $a, $b ) {
		return strtotime( $b['start'] ) - strtotime( $a['start'] );
	} );

	$previous = array_slice( $previous, 0, $limit );

	return $previous;
}
