<?php

namespace Voxel\Product_Types;

if ( ! defined('ABSPATH') ) {
	exit;
}

trait Order_Singleton_Trait {

	/**
	 * Store product type instances.
	 *
	 * @since 1.0
	 */
	private static $instances = [];

	/**
	 * Get an order based on its id.
	 *
	 * @since 1.0
	 */
	public static function get( $id ) {
		if ( is_array( $id ) ) {
			$data = $id;
			$id = $data['id'];
			if ( ! array_key_exists( $id, self::$instances ) ) {
				self::$instances[ $id ] = new \Voxel\Order( $data );
			}
		} elseif ( is_numeric( $id ) ) {
			if ( ! array_key_exists( $id, self::$instances ) ) {
				$results = self::query( [ 'id' => $id, 'limit' => 1 ] );
				self::$instances[ $id ] = isset( $results[0] ) ? $results[0] : null;
			}
		}

		return self::$instances[ $id ];
	}

	public static function query( array $args ) {
		return \Voxel\Product_Types\Order_Repository::query( $args );
	}

	public static function find( array $args ) {
		$args['limit'] = 1;
		$args['offset'] = null;
		$results = static::query( $args );
		return array_shift( $results );
	}

	public static function create( array $data ): \Voxel\Order {
		return \Voxel\Product_Types\Order_Repository::create( $data );
	}

	public function delete() {
		global $wpdb;
		$wpdb->query( $wpdb->prepare(
			"DELETE FROM {$wpdb->prefix}voxel_orders WHERE id = %d",
			$this->get_id()
		) );
	}

	public function sync_with_stripe() {
		$stripe = \Voxel\Stripe::getClient();

		try {
			$object = $this->get_object();
		} catch ( \Exception $e ) {
			$session = $this->get_session_object();
			$object = $session->mode === 'subscription'
				? $stripe->subscriptions->retrieve( $session->subscription )
				: $stripe->paymentIntents->retrieve( $session->payment_intent );
		}

		$status = static::map_status_from_stripe( $object->status );
		if ( $object->charges->data[0]->refunded ?? false ) {
			$status = \Voxel\Order::STATUS_REFUNDED;
		}

		$this->update( [
			'object_id' => $object->id,
			'object_details' => $object,
			'status' => $status,
		] );
	}
}
