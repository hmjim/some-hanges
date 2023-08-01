<?php

namespace Voxel\Post_Types\Fields;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Text_Field extends Base_Post_Field {

	protected $supported_conditions = ['text'];

	protected $props = [
		'type' => 'text',
		'label' => 'Text',
		'placeholder' => '',
		'suffix' => '',
		'minlength' => null,
		'maxlength' => null,
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'key' => $this->get_key_model(),
			'placeholder' => $this->get_placeholder_model(),
			'description' => $this->get_description_model(),
			'suffix' => [
				'type' => Form_Models\Text_Model::class,
				'label' => 'Suffix',
			],
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

	public function update( $value ): void {
		if ( $this->is_empty( $value ) ) {
			delete_post_meta( $this->post->get_id(), $this->get_key() );
		} else {
			update_post_meta( $this->post->get_id(), $this->get_key(), wp_slash( $value ) );
		}
	}

	public function get_value_from_post() {
		return get_post_meta( $this->post->get_id(), $this->get_key(), true );
	}

	protected function frontend_props() {
		return [
			'placeholder' => $this->props['placeholder'] ?: $this->props['label'],
			'minlength' => $this->props['minlength'],
			'maxlength' => $this->props['maxlength'],
			'suffix' => $this->props['suffix'],
		];
	}

	public function exports() {
		return [
			'label' => $this->get_label(),
			'type' => \Voxel\T_STRING,
			'callback' => function() {
				return $this->get_value();
			},
		];
	}

	public function export_to_personal_data() {
		return $this->get_value();
	}
}
