<?php

namespace Voxel\Post_Types\Fields;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Color_Field extends Base_Post_Field {

	protected $supported_conditions = ['text'];

	protected $props = [
		'type' => 'color',
		'label' => 'Color',
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
		$value = sanitize_hex_color( strtolower( $value ) );
		if ( empty( $value ) ) {
			return null;
		}

		return $value;
	}

	public function validate( $value ): void {
		//
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
		];
	}

	public function exports() {
		return [
			'label' => $this->get_label(),
			'type' => \Voxel\T_STRING,
			'callback' => function() {
				return sanitize_hex_color( $this->get_value() );
			},
		];
	}

	public function export_to_personal_data() {
		return $this->get_value();
	}
}
