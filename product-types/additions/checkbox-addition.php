<?php

namespace Voxel\Product_Types\Additions;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Checkbox_Addition extends Base_Addition {

	protected $props = [
		'type' => 'checkbox',
		'key' => 'checkbox-addition',
		'label' => 'Checkbox addition',
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'key' => $this->get_key_model(),
			'icon' => $this->get_icon_model(),
			'description' => $this->get_description_model(),
			'required' => $this->get_required_model(),
			'repeat' => $this->get_repeat_model(),
		];
	}

	public function sanitize_config( $value ) {
		$price = $value['price'] ?? null;
		return [
			'enabled' => $this->is_required() ? true : (bool) ( $value['enabled'] ?? null ),
			'price' => is_numeric( $price ) ? abs( $price ) : null,
		];
	}

	public function validate_config( $value ) {
		if ( $value['enabled'] ) {
			if ( $value['price'] === null ) {
				throw new \Exception( \Voxel\replace_vars(
					_x( 'Price is required for @addition_name addition', 'checkbox addition', 'voxel' ), [
						'@addition_name' => $this->props['label'],
					]
				) );
			}
		}
	}

	public function get_price() {
		$value = $this->field->get_value();
		$config = $this->sanitize_config( $value['additions'][ $this->get_key() ] ?? [] );
		return $config['price'];
	}

	public function get_product_form_config(): array {
		$value = $this->field->get_value();
		$config = $this->sanitize_config( $value['additions'][ $this->get_key() ] ?? [] );
		$price = $config['price'] ?? 0;

		return [
			'type' => $this->get_type(),
			'key' => $this->get_key(),
			'label' => $this->get_label(),
			'price' => $price,
			'repeat' => !! $this->props['repeat'],
			'value' => false,
		];
	}

	public function sanitize( $value ) {
		return !! $value;
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
				'price' => [
					'label' => 'Price',
					'type' => \Voxel\T_NUMBER,
					'callback' => function() {
						return $this->get_price();
					},
				],
			],
		];
	}
}
