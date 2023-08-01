<?php

namespace Voxel\Membership;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Plan {

	private
		$key,
		$label,
		$description,
		$pricing,
		$submissions,
		$archived;

	private static $instances = [];

	public static function get( $key ) {
		if ( is_array( $key ) ) {
			$key = $key['key'] ?? null;
		}

		$plans = \Voxel\get( 'plans', [] );
		if ( ! isset( $plans[ $key ] ) ) {
			return null;
		}

		if ( ! array_key_exists( $key, static::$instances ) ) {
			static::$instances[ $key ] = new static( $plans[ $key ] );
		}

		return static::$instances[ $key ];
	}

	public static function all() {
		return array_filter( array_map(
			'\Voxel\Membership\Plan::get',
			\Voxel\get( 'plans', [] )
		) );
	}

	public static function active() {
		return array_filter( static::all(), function( $plan ) {
			return ! $plan->is_archived();
		} );
	}

	public static function archived() {
		return array_filter( static::all(), function( $plan ) {
			return $plan->is_archived();
		} );
	}

	public static function get_or_create_default_plan() {
		$plans = \Voxel\get( 'plans', [] );
		if ( isset( $plans['default'] ) ) {
			return static::get( $plans['default'] );
		}

		$default = [
			'key' => 'default',
			'label' => 'Free plan',
			'description' => null,
			'pricing' => [],
			'submissions' => [],
			'archived' => false,
		];

		\Voxel\set( 'plans', array_merge( [ 'default' => $default ], $plans ) );
		return static::get( $default );
	}

	public static function create( array $data, $is_update = false ): \Voxel\Membership\Plan {
		$plans = \Voxel\get( 'plans', [] );
		$data = array_merge( [
			'key' => null,
			'label' => null,
			'description' => null,
			'pricing' => [],
			'submissions' => [],
			'archived' => false,
		], $data );

		if ( empty( $data['key'] ) || ( ! $is_update && isset( $plans[ $data['key'] ] ) ) ) {
			throw new \Exception( _x( 'Please provide a unique key.', 'membership plans', 'voxel-backend' ) );
		}

		if ( empty( $data['label'] ) ) {
			throw new \Exception( _x( 'Please provide a label.', 'membership plans', 'voxel-backend' ) );
		}

		$plans[ $data['key'] ] = [
			'key' => $data['key'],
			'label' => $data['label'],
			'description' => $data['description'],
			'pricing' => $data['pricing'],
			'submissions' => $data['submissions'],
			'archived' => !! $data['archived'],
		];

		\Voxel\set( 'plans', $plans );
		return static::get( $data['key'] );
	}

	public function update( $data_or_key, $value = null ) {
		$data = $this->get_config();

		if ( is_array( $data_or_key ) ) {
			$data = array_merge( $data, $data_or_key );
		} else {
			$data[ $data_or_key ] = $value;
		}

		$data['key'] = $this->key;
		static::create( $data, $is_update = true );

		$this->label = $data['label'] ?? $this->label;
		$this->description = $data['description'] ?? $this->description;
		$this->submissions = $data['submissions'] ?? $this->submissions;
		$this->archived = $data['archived'] ?? $this->archived;
		$this->pricing = $data['pricing'] ?? $this->pricing;
	}

	public function get_key() {
		return $this->key;
	}

	public function get_label() {
		return $this->label;
	}

	public function get_description() {
		return $this->description;
	}

	public function is_archived() {
		return $this->archived;
	}

	public function get_pricing() {
		return $this->pricing;
	}

	public static function get_price_period( $price ) {
		if ( $price['type'] !== 'recurring' ) {
			return null;
		}

		$interval = $price['recurring']['interval'];
		$count = absint( $price['recurring']['interval_count'] );
		return \Voxel\interval_format( $interval, $count );
	}

	private function __construct( $data ) {
		$this->key = $data['key'];
		$this->label = $data['label'];
		$this->description = $data['description'];
		$this->submissions = $data['submissions'];
		$this->archived = !! ( $data['archived'] ?? false );
		$this->pricing = [
			'live' => $data['pricing']['live'] ?? null,
			'test' => $data['pricing']['test'] ?? null,
		];
	}

	public function get_config() {
		return [
			'key' => $this->key,
			'label' => $this->label,
			'description' => $this->description,
			'pricing' => $this->pricing,
			'submissions' => $this->submissions,
			'archived' => $this->archived,
		];
	}

	public function get_editor_config() {
		$config = $this->get_config();
		$config['submissions'] = (object) $config['submissions'];

		foreach ( [ 'live', 'test' ] as $mode ) {
			$pricing = $config['pricing'][ $mode ] ?? [
				'product_id' => null,
				'prices' => [],
			];

			foreach ( $pricing['prices'] as $price_id => $price ) {
				$pricing['prices'][ $price_id ]['id'] = $price_id;
				$pricing['prices'][ $price_id ]['is_zero_decimal'] = \Voxel\Stripe\Currencies::is_zero_decimal( strtoupper( $price['currency'] ) );
				$pricing['prices'][ $price_id ]['tax_behavior'] = $price['tax_behavior'] ?? 'n/a';
			}

			$pricing['prices'] = array_values( $pricing['prices'] );

			$config['pricing'][ $mode ] = $pricing;
		}

		return $config;
	}

	public function get_edit_link() {
		return admin_url( sprintf( 'admin.php?page=voxel-membership&action=edit-plan&plan=%s', $this->get_key() ) );
	}
}
