<?php

namespace Voxel\Post_Types\Fields\Traits;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

trait Model_Helpers {

	protected function get_key_model() {
		return [
			'type' => Form_Models\Key_Model::class,
			'label' => 'Field Key',
			'description' => 'Enter a unique field key',
			'width' => '1/2',
			'classes' => 'field-key-wrapper ',
			'editable' => '(!!field.singular) === false',
			'ref' => 'keyInput',
		];
	}

}
