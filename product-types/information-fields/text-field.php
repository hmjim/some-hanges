<?php

namespace Voxel\Product_Types\Information_Fields;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Text_Field extends Base_Information_Field {

	protected $props = [
		'type' => 'text',
		'placeholder' => '',
		'minlength' => null,
		'maxlength' => null,
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'key' => $this->get_key_model(),
			'placeholder' => $this->get_placeholder_model(),
			'description' => $this->get_description_model(),
			'minlength' => $this->get_minlength_model(),
			'maxlength' => $this->get_maxlength_model(),
			'required' => $this->get_required_model(),
		];
	}

	public function sanitize( $value ) {
		return sanitize_text_field( $value );
	}

	public function validate( $value ): void {
		$this->validate_minlength( $value );
		$this->validate_maxlength( $value );
	}

	protected function frontend_props() {
		return [
			'placeholder' => $this->props['placeholder'],
			'minlength' => $this->props['minlength'],
			'maxlength' => $this->props['maxlength'],
		];
	}
}
