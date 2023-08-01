<?php

namespace Voxel\Product_Types\Information_Fields;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Email_Field extends Base_Information_Field {

	protected $props = [
		'type' => 'email',
		'placeholder' => '',
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'key' => $this->get_key_model(),
			'placeholder' => $this->get_placeholder_model(),
			'description' => $this->get_description_model(),
			'required' => $this->get_required_model(),
		];
	}

	public function sanitize( $value ) {
		return sanitize_email( $value );
	}

	public function validate( $value ): void {
		//
	}

	protected function frontend_props() {
		return [
			'placeholder' => $this->props['placeholder'] ?: $this->props['label'],
		];
	}
}
