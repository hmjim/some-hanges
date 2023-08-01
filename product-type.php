<?php

namespace Voxel;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Product_Type {

	private $config, $additions, $fields, $tags;

	/**
	 * Store product type instances.
	 *
	 * @since 1.0
	 */
	private static $instances = [];

	/**
	 * Get a product type based on its key.
	 *
	 * @since 1.0
	 */
	public static function get( $key ) {
		if ( ! isset( self::$instances[ $key ] ) ) {
			$product_types = \Voxel\get( 'product_types', [] );
			if ( ! isset( $product_types[ $key ] ) ) {
				return null;
			}

			self::$instances[ $key ] = new static( (array) $product_types[ $key ] );
		}

		return self::$instances[ $key ];
	}

	public static function get_all() {
		$keys = array_keys( \Voxel\get( 'product_types', [] ) );
		return array_map( '\Voxel\Product_Type::get', $keys );
	}

	private function __construct( array $config ) {
		$settings = $config['settings'] ?? [];
		$calendar = $config['calendar'] ?? [];
		$additions = $config['additions'] ?? [];
		$tags = $config['tags'] ?? [];
		$fields = $config['fields'] ?? [];
		$checkout = $config['checkout'] ?? [];
		$l10n = $settings['l10n'] ?? [];

		$this->config = [
			'settings' => [
				'key' => $settings['key'] ?? '',
				'label' => $settings['label'] ?? '',
				'payments' => [
					'mode' => $settings['payments']['mode'] ?? 'payment',
					'transfer_destination' => $settings['payments']['transfer_destination'] ?? 'vendor_account',
					'capture_method' => $settings['payments']['capture_method'] ?? 'manual',
					'pricing' => $settings['payments']['pricing'] ?? 'dynamic',
				],
				'base_price' => [
					'active' => $settings['base_price']['active'] ?? true,
					'default_price' => $settings['base_price']['default_price'] ?? 0,
				],
				'catalog_mode' => [
					'active' => $settings['catalog_mode']['active'] ?? false,
					'requires_approval' => $settings['catalog_mode']['requires_approval'] ?? true,
					'refunds_allowed' => $settings['catalog_mode']['refunds_allowed'] ?? true,
				],
				'tags' => [
					'editable_by' => $settings['tags']['editable_by'] ?? 'both', // both|vendor|customer
					'qr_limit' => $settings['tags']['qr_limit'] ?? 'unlimited', // once|unlimited
				],
				'mode' => $settings['mode'] ?? 'booking',
				'notes' => [
					'enabled' => $settings['notes']['enabled'] ?? true,
				],
				'skip_main_step' => $settings['skip_main_step'] ?? false,
				'l10n' => [
					'field' => [
						'base_price' => $l10n['field']['base_price'] ?? 'Base price',
						'instances_per_day' => $l10n['field']['instances_per_day'] ?? 'Quantity per day',
						'instances_per_slot' => $l10n['field']['instances_per_slot'] ?? 'Quantity per timeslot',
						'notes' => [
							'label' => $l10n['field']['notes']['label'] ?? 'Notes',
							'description' => $l10n['field']['notes']['description'] ?? '',
							'placeholder' => $l10n['field']['notes']['placeholder'] ?? '',
						],
					],
					'form' => [
						'check_in' => $l10n['form']['check_in'] ?? 'Check-in',
						'check_out' => $l10n['form']['check_out'] ?? 'Check-out',
						'pick_date' => $l10n['form']['pick_date'] ?? 'Choose date',
					],
				],
				'deliverables' => [
					'enabled' => $settings['deliverables']['enabled'] ?? false,
					'delivery_methods' => (array) ( $settings['deliverables']['delivery_methods'] ?? [ 'automatic', 'manual' ] ),
					'download_limit' => $settings['deliverables']['download_limit'] ?? 3,
					'uploads' => [
						'allowed_file_types' => $settings['deliverables']['uploads']['allowed_file_types'] ?? [
							'image/jpeg',
							'image/png',
							'image/webp',
						],
						'max_count' => $settings['deliverables']['uploads']['max_count'] ?? 5,
						'max_size' => $settings['deliverables']['uploads']['max_size'] ?? 2000,
					],
				],
				'comments' => [
					'uploads' => [
						'allowed_file_types' => $settings['comments']['uploads']['allowed_file_types'] ?? [
							'image/jpeg',
							'image/png',
							'image/webp',
						],
						'max_count' => $settings['comments']['uploads']['max_count'] ?? 5,
						'max_size' => $settings['comments']['uploads']['max_size'] ?? 2000,
					],
				],
			],

			'calendar' => [
				'type' => $calendar['type'] ?? 'booking',
				'format' => $calendar['format'] ?? 'days',
				'allow_range' => $calendar['allow_range'] ?? true,
				'range_mode' =>  $calendar['range_mode'] ?? 'days', // nights|days
			],

			'additions' => $additions,
			'tags' => $tags,
			'fields' => $fields,
			'checkout' => [
				'on_behalf_of' => $checkout['on_behalf_of'] ?? false,
				'application_fee' => [
					'type' => $checkout['application_fee']['type'] ?? 'percentage',
					'amount' => $checkout['application_fee']['amount'] ?? 10,
				],
				'tax' => [
					'mode' => $checkout['tax']['mode'] ?? 'none',
					'auto' => [
						'tax_code' => $checkout['tax']['auto']['tax_code'] ?? '',
						'tax_behavior' => $checkout['tax']['auto']['tax_behavior'] ?? 'inclusive',
						'tax_id_collection' => $checkout['tax']['auto']['tax_id_collection'] ?? false,
					],
					'manual' => [
						'tax_rates' => $checkout['tax']['manual']['tax_rates'] ?? [],
						'test_tax_rates' => $checkout['tax']['manual']['test_tax_rates'] ?? [],
					],
				],
				'shipping' => [
					'enabled' => $checkout['shipping']['enabled'] ?? false,
					'allowed_countries' => $checkout['shipping']['allowed_countries'] ?? [],
					'shipping_rates' => $checkout['shipping']['shipping_rates'] ?? [],
					'test_shipping_rates' => $checkout['shipping']['test_shipping_rates'] ?? [],
				],
				'promotion_codes' => [
					'enabled' => $checkout['promotion_codes']['enabled'] ?? false,
				],
			],
		];
	}

	public function config( $option, $default = null ) {
		$config = $this->config;
		$keys = explode( '.', $option );
		foreach ( $keys as $key ) {
			if ( ! isset( $config[ $key ] ) ) {
				return $default;
			}

			$config = $config[ $key ];
		}

		return $config;
	}

	public function get_label() {
		return $this->config['settings']['label'];
	}

	public function get_key() {
		return $this->config['settings']['key'];
	}

	public function get_product_mode() {
		return $this->config['settings']['mode'];
	}

	public function get_edit_link() {
		return admin_url( 'admin.php?page=voxel-product-types&action=edit-type&product_type='.$this->get_key() );
	}

	public function get_payment_mode() {
		return $this->config['settings']['payments']['mode'] === 'subscription' ? 'subscription' : 'payment';
	}

	public function get_editor_config(): array {
		return [
			'settings' => $this->config['settings'],
			'calendar' => $this->config['calendar'],
			'additions' => array_values( array_map( function( $addition ) {
				return $addition->get_props();
			}, $this->get_additions() ) ),
			'tags' => array_values( array_map( function( $tag ) {
				return $tag->get_props();
			}, $this->get_tags() ) ),
			'fields' => $this->config['fields'],
			'checkout' => $this->config['checkout'],
		];
	}

	/**
	 * Memoized method to retrieve, validate addition data,
	 * and convert them to their respective classes.
	 *
	 * @since 1.0
	 */
	public function get_additions() {
		if ( is_array( $this->additions ) ) {
			return $this->additions;
		}

		$this->additions = [];

		$config = $this->config['additions'];
		$addition_types = \Voxel\config('product_types.addition_types');

		foreach ( $config as $addition_data ) {
			if ( ! is_array( $addition_data ) || empty( $addition_data['type'] ) || empty( $addition_data['key'] ) ) {
				continue;
			}

			if ( isset( $addition_types[ $addition_data['type'] ] ) ) {
				$addition = new $addition_types[ $addition_data['type'] ]( $addition_data );
				$addition->set_product_type( $this );

				$this->additions[ $addition->get_key() ] = $addition;
			}
		}

		return $this->additions;
	}

	/**
	 * Memoized method to retrieve, validate field data,
	 * and convert them to their respective classes.
	 *
	 * @since 1.0
	 */
	public function get_fields() {
		if ( is_array( $this->fields ) ) {
			return $this->fields;
		}

		$this->fields = [];

		$config = $this->config['fields'] ?? [];
		$field_types = \Voxel\config('product_types.field_types');

		foreach ( $config as $field_data ) {
			if ( ! is_array( $field_data ) || empty( $field_data['type'] ) || empty( $field_data['key'] ) ) {
				continue;
			}

			if ( isset( $field_types[ $field_data['type'] ] ) ) {
				$field = new $field_types[ $field_data['type'] ]( $field_data );
				$field->set_product_type( $this );

				$this->fields[ $field->get_key() ] = $field;
			}
		}

		return $this->fields;
	}

	public function get_addition( $addition_key ) {
		$additions = $this->get_additions();
		return $additions[ $addition_key ] ?? null;
	}

	public function get_tags() {
		if ( is_array( $this->tags ) ) {
			return $this->tags;
		}

		$this->tags = [];

		foreach ( $this->config['tags'] as $tag_data ) {
			$tag = new \Voxel\Product_Types\Order_Tag( (array) $tag_data );
			if ( $tag->is_valid() ) {
				$tag->set_product_type( $this );
				$this->tags[ $tag->get_key() ] = $tag;
			}
		}

		return $this->tags;
	}

	public function get_tag( $tag_key ) {
		$tags = $this->get_tags();
		return $tags[ $tag_key ] ?? null;
	}

	public function get_default_tag() {
		foreach ( $this->get_tags() as $tag ) {
			if ( $tag->is_default() ) {
				return $tag;
			}
		}

		return null;
	}

	public function get_config(): array {
		return $this->config;
	}

	/**
	 * Save product type configuration to database.
	 *
	 * @since 1.0
	 */
	public function set_config( $new_config ) {
		$product_types = \Voxel\get( 'product_types', [] );

		if ( isset( $new_config['settings'] ) ) {
			$this->config['settings'] = $new_config['settings'];
		}

		if ( isset( $new_config['calendar'] ) ) {
			$this->config['calendar'] = $new_config['calendar'];
		}

		if ( isset( $new_config['additions'] ) ) {
			$this->config['additions'] = $new_config['additions'];
		}

		if ( isset( $new_config['tags'] ) ) {
			$this->config['tags'] = $new_config['tags'];
		}

		if ( isset( $new_config['fields'] ) ) {
			$this->config['fields'] = $new_config['fields'];
		}

		if ( isset( $new_config['checkout'] ) ) {
			$this->config['checkout'] = $new_config['checkout'];
		}

		$product_types[ $this->get_key() ] = $this->config;

        // cleanup product_types array
        foreach ( $product_types as $post_type_key => $post_type_settings ) {
        	if ( ! is_string( $post_type_key ) || empty( $post_type_key ) || empty( $post_type_settings ) ) {
        		unset( $product_types[ $post_type_key ] );
        	}
        }

		\Voxel\set( 'product_types', $product_types );
	}

	public function calculate_fee( $amount_in_cents ) {
		$type = $this->config( 'checkout.application_fee.type' );
		if ( $this->get_payment_mode() === 'subscription' ) {
			$type = 'percentage';
		}

		$amount = $this->config( 'checkout.application_fee.amount' );

		if ( $type === 'fixed_amount' ) {
			return abs( $amount );
		} else {
			$percentage = abs( $amount ) / 100;
			return round( abs( $amount_in_cents ) * $percentage );
		}
	}

	public function remove() {
		$product_types = \Voxel\get( 'product_types', [] );
		unset( $product_types[ $this->get_key() ] );
		\Voxel\set( 'product_types', $product_types );
	}

	public function is_using_price_id(): bool {
		return $this->config('settings.payments.pricing') === 'price_id';
	}

	public function has_base_price(): bool {
		return !! $this->config('settings.base_price.active');
	}

	public function get_default_base_price() {
		return abs( (float) ( $this->config('settings.base_price.default_price') ) );
	}

	public function is_catalog_mode(): bool {
		return !! $this->config('settings.catalog_mode.active');
	}

	public function catalog_requires_approval(): bool {
		return !! $this->config('settings.catalog_mode.requires_approval');
	}

	public function catalog_refunds_allowed(): bool {
		return !! $this->config('settings.catalog_mode.refunds_allowed');
	}
}
