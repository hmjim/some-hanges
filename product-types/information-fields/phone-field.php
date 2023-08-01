<?php

namespace Voxel\Product_Types\Information_Fields;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Phone_Field extends Base_Information_Field {

	protected $props = [
		'type' => 'phone',
		'placeholder' => '',
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'key' => $this->get_model( 'key', [ 'width' => '1/1' ]),
			'placeholder' => $this->get_placeholder_model(),
			'description' => $this->get_description_model(),
			'required' => $this->get_required_model(),
		];
	}

	public function sanitize( $value ) {
		return $value;
	}

	public function validate( $value ): void {
		//
	}

	protected function frontend_props() {
		return [
			'placeholder' => $this->props['placeholder'],
		];
	}
}
