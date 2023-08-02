<?php

namespace Voxel\Post_Types\Fields;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Image_Field extends File_Field {

	protected $props = [
		'type' => 'image',
		'label' => 'Image',
		'max-count' => 1,
		'max-size' => 2000,
		'allowed-types' => [],
		'default' => null,
	];

	public function get_models(): array {
		$models = parent::get_models();
		unset( $models['allowed-types'] );
		$models['default'] = [
			'type' => \Voxel\Form_Models\Media_Model::class,
			'label' => 'Default image',
			'width' => '1/1',
			'multiple' => false,
		];

		return $models;
	}

	protected function get_allowed_types() {
		return [
			'image/jpeg',
			'image/png',
			'image/webp',
		];
	}
}
