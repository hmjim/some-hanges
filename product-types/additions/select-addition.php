<?php

namespace Voxel\Product_Types\Additions;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Select_Addition extends Base_Addition {

	protected $props = [
		'type' => 'select',
		'key' => 'select-addition',
		'label' => 'Select addition',
		'choices' => [],
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
			'choices' => function() { ?>
				<div class="ts-form-group ts-col-1-1">
					<label>Choices</label>
					<select-field-choices :field="addition"></select-field-choices>
				</div>
			<?php },
		];
	}

	public function sanitize_config( $value ) {
		$choices = [];
		foreach ( $this->props['choices'] as $choice ) {
			$price = $value['choices'][ $choice['value'] ]['price'] ?? null;
			$choices[ $choice['value'] ] = [
				'enabled' => (bool) ( $value['choices'][ $choice['value'] ]['enabled'] ?? null ),
				'price' => is_numeric( $price ) ? abs( $price ) : null,
			];
		}

		return [
			'enabled' => $this->is_required() ? true : (bool) ( $value['enabled'] ?? null ),
			'choices' => $choices,
		];
	}

	public function validate_config( $value ) {
		if ( $value['enabled'] ) {
			$has_single_price = false;
			foreach ( $value['choices'] as $choice ) {
				if ( $choice['enabled'] ) {
					if ( $choice['price'] === null ) {
						throw new \Exception( \Voxel\replace_vars(
							_x( 'Price is required for @addition_name addition choices.', 'select addition', 'voxel' ), [
								'@addition_name' => $this->props['label'],
							]
						) );
					}

					$has_single_price = true;
				}
			}

			if ( ! $has_single_price ) {
				throw new \Exception( \Voxel\replace_vars(
					_x( 'Price is required for @addition_name addition', 'select addition', 'voxel' ), [
						'@addition_name' => $this->props['label'],
					]
				) );
			}
		}
	}

	public function get_choice_by_key( $key ) {
		foreach ( $this->props['choices'] as $choice ) {
			if ( $choice['value'] === $key ) {
				return $choice;
			}
		}

		return null;
	}

	public function get_price_for_choice( $key ) {
		$value = $this->field->get_value();
		$config = $this->sanitize_config( $value['additions'][ $this->get_key() ] ?? [] );
		$price = null;

		foreach ( $config['choices'] as $choice_key => $choice ) {
			if ( $choice['enabled'] && $choice_key === $key ) {
				$price = $choice['price'];
				break;
			}
		}

		return $price;
	}

	public function get_product_form_config(): array {
		$value = $this->field->get_value();
		$config = $this->sanitize_config( $value['additions'][ $this->get_key() ] ?? [] );
		$required = !! $this->props['required_in_checkout'];
		$choices = [];

		foreach ( $this->props['choices'] as $choice ) {
			$choice_enabled = $config['choices'][ $choice['value'] ]['enabled'];
			$choice_price = $config['choices'][ $choice['value'] ]['price'];
			if ( ! $choice_enabled || $choice_price === null ) {
				continue;
			}

			$choices[ $choice['value'] ] = [
				'price' => $choice_price,
				'label' => $choice['label'],
				'icon' => \Voxel\get_icon_markup( $choice['icon'] ),
			];
		}

		return [
			'type' => $this->get_type(),
			'key' => $this->get_key(),
			'label' => $this->get_label(),
			'repeat' => !! $this->props['repeat'],
			'required' => $required,
			'choices' => $choices,
			'placeholder' => _x( 'Pick an option', 'select addition', 'voxel' ),
			'value' => $required && count( $choices ) ? array_key_first( $choices ) : null,
		];
	}

	public function sanitize( $value ) {
		$value = sanitize_text_field( $value );
		$choice_exists = false;
		foreach ( $this->props['choices'] as $choice ) {
			if ( $choice['value'] === $value ) {
				$choice_exists = true;
				break;
			}
		}

		if ( ! $choice_exists ) {
			return null;
		}

		return $value;
	}

	public function exports() {
		$value = $this->field->get_value();
		$config = $this->sanitize_config( $value['additions'][ $this->get_key() ] ?? [] );
		$properties = [];
		$properties['enabled'] = [
			'label' => 'Enabled',
			'type' => \Voxel\T_STRING,
			'callback' => function() {
				return $this->is_enabled() ? '1' : '';
			},
		];

		foreach ( $this->props['choices'] as $choice ) {
			$values = $config['choices'][ $choice['value'] ];
			$properties[ 'choice:'.$choice['value'] ] = [
				'label' => $choice['label'],
				'type' => \Voxel\T_OBJECT,
				'properties' => [
					'enabled' => [
						'label' => 'Enabled',
						'type' => \Voxel\T_STRING,
						'callback' => function() use ( $values ) {
							return $values['enabled'] ? '1' : '';
						},
					],
					'price' => [
						'label' => 'Price',
						'type' => \Voxel\T_NUMBER,
						'callback' => function() use ( $values ) {
							return $values['price'];
						},
					],
				],
			];
		}

		return [
			'label' => $this->get_label(),
			'type' => \Voxel\T_OBJECT,
			'properties' => $properties,
		];
	}
}
