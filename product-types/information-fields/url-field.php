<?php

namespace Voxel\Product_Types\Information_Fields;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Url_Field extends Base_Information_Field {

	protected $props = [
		'type' => 'url',
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
		return $value;
	}

	public function validate( $value ): void {
		//
	}

	public function prepare_for_display( $value ) {
		if ( ! empty( $value ) ) {
			return sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $value ), esc_html( $value ) );
		}

		return null;
	}

	protected function frontend_props() {
		return [
			'placeholder' => $this->props['placeholder'],
		];
	}
}
