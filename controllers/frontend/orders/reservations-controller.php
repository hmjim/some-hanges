<?php

namespace Voxel\Controllers\Frontend\Orders;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Reservations_Controller extends \Voxel\Controllers\Base_Controller {

	protected function hooks() {
		$this->on( 'voxel_ajax_reservations.get_items', '@get_items' );
		$this->on( 'voxel_ajax_reservations.get_day', '@get_day' );
		$this->on( 'voxel_ajax_reservations.get_post_list', '@get_post_list' );
	}

	protected function get_items() {
		try {
			$post_id = isset( $_GET['post_id'] ) && is_numeric( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : null;
			$timeframe = sanitize_text_field( $_GET['timeframe'] ?? 'upcoming' );
			$custom_date = strtotime( $_GET['custom_date'] ?? null );
			$author_id = absint( get_current_user_id() );
			$today = date( 'Y-m-d' );
			$per_page = 10;
			$weekdays = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ];

			if ( $timeframe === 'upcoming' ) {
				global $wpdb;
				$_testmode = \Voxel\Stripe::is_test_mode() ? 'true' : 'false';
				$_today = esc_sql( $today );

				$_where_post_id = '';
				if ( ! is_null( $post_id ) ) {
					$_where_post_id = "AND posts.ID = {$post_id}";
				}

				$timestamp = strtotime( $wpdb->get_var( <<<SQL
					SELECT orders.checkin FROM {$wpdb->prefix}voxel_orders AS orders
						WHERE ( orders.vendor_id = {$author_id} OR orders.customer_id = {$author_id} ) {$_where_post_id} AND orders.status = 'completed'
							AND ( orders.checkin >= '{$_today}' OR ( orders.checkin <= '{$_today}' AND orders.checkout >= '{$_today}' ) ) AND orders.testmode IS {$_testmode}
						ORDER BY orders.checkin ASC LIMIT 1
				SQL ) );

				if ( $timestamp ) {
					$reserved_at = $timestamp < time() ? $today : date( 'Y-m-d', $timestamp );
				}
			} elseif ( $timeframe === 'custom' ) {
				if ( $custom_date ) {
					$reserved_at = date( 'Y-m-d', $custom_date );
				}
			} elseif ( $timeframe === 'this-week' ) {
				$start_of_week = absint( get_option( 'start_of_week' ) );
				$day_of_week = absint( date('w') );
				if ( $day_of_week === $start_of_week ) {
					$reserved_at = $today;
				} else {
					$reserved_at = date( 'Y-m-d', strtotime( 'previous '.$weekdays[ $start_of_week ] ) );
				}
			} elseif ( $timeframe === 'next-week' ) {
				$start_of_week = absint( get_option( 'start_of_week' ) );
				$reserved_at = date( 'Y-m-d', strtotime( 'next '.$weekdays[ $start_of_week ] ) );
			}

			if ( empty( $reserved_at ) ) {
				$reserved_at = $today;
			}

			$items = [];

			for ( $i = 0; $i <= 6; $i++) {
				$date = date( 'Y-m-d', strtotime( '+'.$i.' days', strtotime( $reserved_at ) ) );
				$orders = \Voxel\Order::query( [
					'party_id' => $author_id,
					'post_id' => $post_id,
					'status' => 'completed',
					'reserved_at' => $date,
					'order_by' => 'reservation',
					'limit' => $per_page + 1,
				] );

				$has_more = count( $orders ) > $per_page;
				if ( $has_more ) {
					array_pop( $orders );
				}

				$items[] = [
					'date' => $date,
					'label' => \Voxel\date_format( strtotime( $date ) ),
					'weekday' => date_i18n( 'l', strtotime( $date ) ),
					'has_more' => $has_more,
					'items' => $this->prepare_items( $orders, $date ),
				];
			}

			return wp_send_json( [
				'success' => true,
				'data' => $items,
			] );

		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function get_day() {
		try {
			$post_id = isset( $_GET['post_id'] ) && is_numeric( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : null;
			$timestamp = strtotime( $_GET['date'] ?? null );
			$offset = isset( $_GET['offset'] ) ? absint( $_GET['offset'] ) : 0;
			$author_id = absint( get_current_user_id() );
			$per_page = 10;

			if ( ! $timestamp ) {
				throw new \Exception( __( 'Invalid request.', 'voxel' ) );
			}

			$date = date( 'Y-m-d', $timestamp );
			$orders = \Voxel\Order::query( [
				'party_id' => $author_id,
				'post_id' => $post_id,
				'status' => 'completed',
				'reserved_at' => $date,
				'order_by' => 'reservation',
				'limit' => $per_page + 1,
				'offset' => $offset,
			] );

			$has_more = count( $orders ) > $per_page;
			if ( $has_more ) {
				array_pop( $orders );
			}

			$day = [
				'date' => $date,
				'label' => \Voxel\date_format( $timestamp ),
				'has_more' => $has_more,
				'items' => $this->prepare_items( $orders, $date ),
			];

			return wp_send_json( [
				'success' => true,
				'data' => $day,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	private function prepare_items( $orders, $reference_date ) {
		return array_map( function( $order ) use ( $reference_date ) {
			$customer = $order->get_customer();
			return [
				'id' => $order->get_id(),
				'link' => add_query_arg( 'ref', 'calendar', $order->get_link() ),
				'customer' => [
					'name' => $order->get_customer_name_for_display(),
					'avatar' => $customer ? $customer->get_avatar_markup() : null,
					'link' => $customer ? $customer->get_link() : null,
				],
				'labels' => $this->get_item_labels( $order, $reference_date ),
			];
		}, $orders );
	}

	private function get_item_labels( $order, $reference_date ) {
		$labels = [];

		if ( $tag = $order->get_tag() ) {
			$labels[] = [
				'content' => $tag->get_label(),
				'color' => $tag->get_primary_color(),
				'background' => $tag->get_secondary_color(),
			];
		}

		$checkin = $order->get_checkin_date();
		$checkout = $order->get_checkout_date();
		$timeslot = $order->get_timeslot();

		if ( $checkout && $checkout !== $checkin ) {
			$labels[] = [
				'content' => _x( 'Multi day', 'booking calendar', 'voxel' ),
			];

			$length = ( new \DateTime( $checkin ) )->diff( new \DateTime( $checkout ) )->days + 1;
			$progress = ( new \DateTime( $checkin ) )->diff( new \DateTime( $reference_date ) )->days + 1;

			$labels[] = [
				'content' => \Voxel\replace_vars( _x( 'Day @current of @total', 'booking calendar', 'voxel' ), [
					'@current' => number_format_i18n( $progress ),
					'@total' => number_format_i18n( $length ),
				] ),
			];
		} else {
			// $labels[] = [
			// 	'content' => _x( 'Single day', 'booking calendar', 'voxel' ),
			// ];
		}

		if ( $timeslot ) {
			$labels[] = [
				'content' => sprintf(
					'%s-%s',
					\Voxel\time_format( strtotime( $timeslot['from'] ) ),
					\Voxel\time_format( strtotime( $timeslot['to'] ) )
				),
			];
		}

		return $labels;
	}

	protected function get_post_list() {
		try {
			global $wpdb;

			$author_id = absint( get_current_user_id() );
			$testmode = \Voxel\Stripe::is_test_mode() ? 'true' : 'false';
			$offset = isset( $_GET['offset'] ) ? absint( $_GET['offset'] ) : 0;
			$per_page = 10;
			$limit = $per_page + 1;

			$post_ids = $wpdb->get_col( <<<SQL
				SELECT orders.post_id FROM {$wpdb->prefix}voxel_orders AS orders
					LEFT JOIN {$wpdb->posts} AS posts ON orders.post_id = posts.ID
					WHERE ( orders.vendor_id = {$author_id} OR orders.customer_id = {$author_id} ) AND orders.status = 'completed'
						AND orders.checkin IS NOT NULL AND orders.testmode IS {$testmode}
					GROUP BY orders.post_id
					ORDER BY posts.post_title DESC LIMIT {$limit} OFFSET {$offset}
			SQL );

			$has_more = count( $post_ids ) > $per_page;
			if ( $has_more ) {
				array_pop( $post_ids );
			}

			_prime_post_caches( $post_ids );

			$posts = [];
			foreach ( $post_ids as $post_id ) {
				if ( $post = \Voxel\Post::get( $post_id ) ) {
					$posts[] = [
						'id' => $post->get_id(),
						'title' => $post->get_title(),
						'logo' => $post->get_logo_markup(),
						'type' => $post->post_type->get_singular_name(),
					];
				}
			}

			return wp_send_json( [
				'success' => true,
				'has_more' => $has_more,
				'data' => $posts,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}
}
