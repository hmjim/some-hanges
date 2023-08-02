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
			'label' => $this->get_model( 'label', [ 'classes' => 'x-col-6' ]),
			'key' => $this->get_model( 'key', [ 'classes' => 'x-col-6' ]),
			'placeholder' => $this->get_model( 'placeholder', [ 'classes' => 'x-col-12' ]),
			'description' => $this->get_description_model(),
			'minlength' => $this->get_model( 'minlength', [ 'classes' => 'x-col-6' ]),
			'maxlength' => $this->get_model( 'maxlength', [ 'classes' => 'x-col-6' ]),
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
