<?php

namespace Voxel\Product_Types\Information_Fields;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Switcher_Field extends Base_Information_Field {

	protected $props = [
		'type' => 'switcher',
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'key' => $this->get_model( 'key', [ 'width' => '1/1' ]),
			'description' => $this->get_description_model(),
			'required' => $this->get_required_model(),
		];
	}

	public function sanitize( $value ) {
		return !! $value;
	}

	public function validate( $value ): void {
		//
	}

	public function prepare_for_display( $value ) {
		return $value ? _x( 'Yes', 'switcher information field', 'voxel' ) : _x( 'No', 'switcher information field', 'voxel' );
	}
}
