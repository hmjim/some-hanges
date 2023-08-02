<?php

namespace Voxel\Post_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Ui_Heading_Filter extends Base_Filter {

	protected $props = [
		'type' => 'ui-heading',
		'label' => 'UI: Label',
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'description' => $this->get_description_model(),
			// 'icon' => $this->get_icon_model(),
			'key' => $this->get_key_model(),
		];
	}

	public function is_ui() {
		return true;
	}
}
