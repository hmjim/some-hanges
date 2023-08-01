<?php

namespace Voxel\Object_Fields;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

trait Base_Model_Helpers {

	protected function get_label_model() {
		return [
			'type' => Form_Models\Text_Model::class,
			'label' => 'Field Name',
			'width' => '1/1',
		];
	}

	protected function get_placeholder_model() {
		return [
			'type' => Form_Models\Text_Model::class,
			'label' => 'Placeholder',
			'width' => '1/2',
		];
	}

	protected function get_key_model() {
		return [
			'type' => Form_Models\Key_Model::class,
			'label' => 'Field Key',
			'description' => 'Enter a unique field key',
			'width' => '1/2',
			'classes' => 'field-key-wrapper ',
			'ref' => 'keyInput',
		];
	}

	protected function get_required_model() {
		return [
			'type' => Form_Models\Switcher_Model::class,
			'label' => 'Is required?',
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

	protected function get_minlength_model() {
		return [
			'type' => Form_Models\Number_Model::class,
			'label' => 'Minlength',
			'width' => '1/2',
			'min' => 0,
		];
	}

	protected function get_maxlength_model() {
		return [
			'type' => Form_Models\Number_Model::class,
			'label' => 'Maxlength',
			'width' => '1/2',
			'min' => 0,
		];
	}

	protected function get_editor_type_model() {
		return [
			'type' => Form_Models\Select_Model::class,
			'label' => 'Editor type',
			'width' => '1/1',
			'choices' => [
				'plain-text' => 'Plain text',
				'wp-editor-basic' => 'WP Editor &mdash; Basic controls',
				'wp-editor-advanced' => 'WP Editor &mdash; Advanced controls',
			],
		];
	}

	protected function get_icon_model() {
		return [
			'type' => Form_Models\Icon_Model::class,
			'label' => 'Icon',
			'width' => '1/2',
		];
	}

	protected function get_model( $model_key, $overrides = [] ) {
		$method_name = sprintf( 'get_%s_model', $model_key );
		if ( method_exists( $this, $method_name ) ) {
			$model = $this->{$method_name}();
			return array_merge( $model, $overrides );
		}
	}
}
