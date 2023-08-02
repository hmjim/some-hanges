<?php

namespace Voxel\Post_Types\Fields;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Phone_Field extends Base_Post_Field {

	protected $supported_conditions = ['text'];

	protected $props = [
		'type' => 'phone',
		'label' => 'Phone',
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
		return sanitize_text_field( $value );
	}

	public function update( $value ): void {
		if ( $this->is_empty( $value ) ) {
			delete_post_meta( $this->post->get_id(), $this->get_key() );
		} else {
			update_post_meta( $this->post->get_id(), $this->get_key(), $value );
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
				return $this->get_value();
			},
		];
	}

	public function export_to_personal_data() {
		return $this->get_value();
	}
}
