<?php

namespace Voxel\Post_Types\Fields\Singular;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Title_Field extends \Voxel\Post_Types\Fields\Base_Post_Field {

	protected $supported_conditions = ['text'];

	protected $props = [
		'label' => 'Title',
		'type' => 'title',
		'key' => 'title',
		'placeholder' => '',
		'minlength' => null,
		'maxlength' => null,
		'required' => true,
		'singular' => true,
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'key' => $this->get_key_model(),
			'placeholder' => $this->get_placeholder_model(),
			'description' => $this->get_description_model(),
			'minlength' => $this->get_minlength_model(),
			'maxlength' => $this->get_maxlength_model(),
		];
	}

	public function sanitize( $value ) {
		return sanitize_text_field( $value );
	}

	public function validate( $value ): void {
		$this->validate_minlength( $value );
		$this->validate_maxlength( $value );
	}

	public function update( $value ): void {
		// update_post_meta( $this->post->get_id(), $this->get_key(), $value );
	}

	public function get_value() {
		return $this->post->get_title();
	}

	protected function frontend_props() {
		return [
			'placeholder' => $this->props['placeholder'],
			'minlength' => $this->props['minlength'],
			'maxlength' => $this->props['maxlength'],
		];
	}

	public function export_to_personal_data() {
		return $this->get_value();
	}
}
