<?php

namespace Voxel\Post_Types\Fields\Singular;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Timezone_Field extends \Voxel\Post_Types\Fields\Base_Post_Field {

	protected $supported_conditions = ['text'];

	protected $props = [
		'label' => 'Timezone',
		'type' => 'timezone',
		'key' => 'timezone',
		'singular' => true,
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'key' => $this->get_key_model(),
			'description' => $this->get_description_model(),
		];
	}

	public function sanitize( $value ) {
		return in_array( $value, timezone_identifiers_list(), true ) ? $value : null;
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

	public function get_timezone() {
		try {
			return new \DateTimeZone( $this->get_value() );
		} catch( \Exception $e ) {
			return wp_timezone();
		}
	}

	protected function frontend_props() {
		return [
			'list' => timezone_identifiers_list(),
			'default' => sprintf(
				'%s (%s)',
				wp_timezone_string(),
				_x( 'site default', 'timezones', 'voxel' )
			),
		];
	}

	public function export_to_personal_data() {
		return $this->get_value();
	}
}
