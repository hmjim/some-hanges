<?php

namespace Voxel\Post_Types\Filters\Traits;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

trait Model_Helpers {

	protected function get_label_model() {
		return [
			'type' => Form_Models\Text_Model::class,
			'label' => 'Label',
			'width' => '1/1',
		];
	}

	protected function get_description_model() {
		return [
			'type' => Form_Models\Textarea_Model::class,
			'label' => 'Description',
			'width' => '1/1',
		];
	}

	protected function get_placeholder_model() {
		return [
			'type' => Form_Models\Text_Model::class,
			'label' => 'Placeholder',
			'width' => '1/1',
		];
	}

	protected function get_key_model() {
		return [
			'type' => Form_Models\Key_Model::class,
			'label' => 'Form Key',
			'description' => 'Enter a unique form key',
			'width' => '1/1',
			'classes' => 'field-key-wrapper',
		];
	}

	protected function get_icon_model() {
		return [
			'type' => Form_Models\Icon_Model::class,
			'label' => 'Icon',
			'width' => '1/1',
		];
	}

	protected function get_source_model( $field_types ) {
		return function() use ( $field_types ) { ?>
			<div class="ts-form-group ts-col-1-1">
				<label>Data source:</label>
				<select v-model="filter.source">
					<option v-for="field in $root.getFieldsByType( <?= esc_attr( wp_json_encode( (array) $field_types ) ) ?> )" :value="field.key">
						{{ field.label }}
					</option>
				</select>
			</div>
		<?php };
	}
}
