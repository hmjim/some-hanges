<?php

namespace Voxel\Product_Types\Information_Fields;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Number_Field extends Base_Information_Field {

	protected $props = [
		'type' => 'number',
		'placeholder' => '',
		'min' => null,
		'max' => null,
		'step' => 1,
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'key' => $this->get_key_model(),
			'placeholder' => $this->get_placeholder_model(),
			'description' => $this->get_description_model(),
			'min' => [
				'type' => Form_Models\Number_Model::class,
				'label' => 'Minimum value',
				'width' => '1/3',
			],
			'max' => [
				'type' => Form_Models\Number_Model::class,
				'label' => 'Maximum value',
				'width' => '1/3',
			],
			'step' => [
				'type' => Form_Models\Number_Model::class,
				'label' => 'Step size',
				'width' => '1/3',
			],
			'required' => $this->get_required_model(),
		];
	}

	public function sanitize( $value ) {
		return $value;
	}

	public function validate( $value ): void {
		//
	}

	public function prepare_for_display( $value ) {
		if ( ! is_numeric( $value ) ) {
			return null;
		}

		return number_format_i18n( $value );
	}

	protected function frontend_props() {
		return [
			'placeholder' => $this->props['placeholder'],
			'min' => $this->props['min'],
			'max' => $this->props['max'],
			'step' => $this->props['step'],
		];
	}
}
