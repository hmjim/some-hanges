<?php

namespace Voxel\Post_Types\Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Ui_Heading_Field extends Base_Post_Field {
	use Traits\Ui_Field;

	protected $props = [
		'type' => 'ui-heading',
		'label' => 'UI Heading',
		'description' => '',
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'key' => $this->get_model( 'key', [ 'width' => '1/1' ]),
			'description' => $this->get_description_model(),
		];
	}
}
