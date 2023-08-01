<?php

namespace Voxel\Product_Types\Additions;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Numeric_Addition extends Base_Addition {

	protected $props = [
		'type' => 'numeric',
		'key' => 'numeric-addition',
		'label' => 'Numeric addition',
		'required_in_checkout' => true,
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'key' => $this->get_key_model(),
			'icon' => $this->get_icon_model(),
			'description' => $this->get_description_model(),
			'required' => $this->get_required_model(),
			'repeat' => $this->get_repeat_model(),
			'required_in_checkout' => [
				'type' => \Voxel\Form_Models\Switcher_Model::class,
				'label' => 'Is required in checkout?',
				'description' => 'Require buyers to insert a value in the booking form',
				'width' => '1/1',
			],
		];
	}

	public function sanitize_config( $value ) {
		$price = $value['price'] ?? null;
		$min = $value['min'] ?? null;
		$max = $value['max'] ?? null;
		return [
			'enabled' => $this->is_required() ? true : (bool) ( $value['enabled'] ?? null ),
			'price' => is_numeric( $price ) ? abs( $price ) : null,
			'min' => is_numeric( $min ) ? abs( $min ) : null,
			'max' => is_numeric( $max ) ? abs( $max ) : null,
		];
	}

	public function validate_config( $value ) {
		if ( $value['enabled'] ) {
			if ( $value['price'] === null ) {
				throw new \Exception( \Voxel\replace_vars(
					_x( 'Price is required for @addition_name addition', 'numeric addition', 'voxel' ), [
						'@addition_name' => $this->props['label'],
					]
				) );
			}

			if ( $value['min'] === null || $value['max'] === null ) {
				throw new \Exception( \Voxel\replace_vars(
					_x( 'Minimum and maximum values are required for @addition_name addition', 'numeric addition', 'voxel' ), [
						'@addition_name' => $this->props['label'],
					]
				) );
			}

			if ( $value['min'] > $value['max'] ) {
				throw new \Exception( \Voxel\replace_vars(
					_x( 'Minimum value cannot be larger than maximum for @addition_name addition.', 'numeric addition', 'voxel' ), [
						'@addition_name' => $this->props['label'],
					]
				) );
			}
		}
	}

	public function get_price_per_unit() {
		$value = $this->field->get_value();
		$config = $this->sanitize_config( $value['additions'][ $this->get_key() ] ?? [] );
		return $config['price'];
	}

	public function get_min_units() {
		$value = $this->field->get_value();
		$config = $this->sanitize_config( $value['additions'][ $this->get_key() ] ?? [] );
		return $config['min'];
	}

	public function get_max_units() {
		$value = $this->field->get_value();
		$config = $this->sanitize_config( $value['additions'][ $this->get_key() ] ?? [] );
		return $config['max'];
	}

	public function get_product_form_config(): array {
		$value = $this->field->get_value();
		$config = $this->sanitize_config( $value['additions'][ $this->get_key() ] ?? [] );
		$price = $config['price'] ?? 0;
		$min_units = $config['min'] ?? 1;
		$max_units = $config['max'] ?? ( $min_units + 5 );
		$required = !! $this->props['required_in_checkout'];

		return [
			'type' => $this->get_type(),
			'key' => $this->get_key(),
			'label' => $this->get_label(),
			'price' => $price,
			'required' => $required,
			'repeat' => !! $this->props['repeat'],
			'min_units' => $min_units,
			'max_units' => $max_units,
			'value' => $required ? $min_units : 0,
		];
	}

	public function sanitize( $value ) {
		if ( ! is_numeric( $value ) ) {
			return null;
		}

		return $value;
	}

	public function validate( $value ): void {
		// @todo: validate value is not greater than props.max
	}

	public function exports() {
		return [
			'label' => $this->get_label(),
			'type' => \Voxel\T_OBJECT,
			'properties' => [
				'enabled' => [
					'label' => 'Enabled',
					'type' => \Voxel\T_STRING,
					'callback' => function() {
						return $this->is_enabled() ? '1' : '';
					},
				],
				'price_per_unit' => [
					'label' => 'Price per unit',
					'type' => \Voxel\T_NUMBER,
					'callback' => function() {
						return $this->get_price_per_unit();
					},
				],
				'min_units' => [
					'label' => 'Min units',
					'type' => \Voxel\T_NUMBER,
					'callback' => function() {
						return $this->get_min_units();
					},
				],
				'max_units' => [
					'label' => 'Max units',
					'type' => \Voxel\T_NUMBER,
					'callback' => function() {
						return $this->get_max_units();
					},
				],
			],
		];
	}
}
