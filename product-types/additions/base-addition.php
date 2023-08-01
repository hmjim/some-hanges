<?php

namespace Voxel\Product_Types\Additions;

if ( ! defined('ABSPATH') ) {
	exit;
}

abstract class Base_Addition extends \Voxel\Object_Fields\Base_Field  {

	/**
	 * Product type object which this field belongs to.
	 *
	 * @since 1.0
	 */
	protected $product_type;

	protected $field;

	protected function base_props(): array {
		return [
			'type' => 'checkbox',
			'key' => 'addition',
			'label' => 'Addition',
			'description' => '',
			'icon' => '',
			'required' => true,
			'repeat' => true,
		];
	}

	protected function get_repeat_model() {
		return [
			'type' => \Voxel\Form_Models\Switcher_Model::class,
			'v-if' => '$root.config.calendar.type === \'booking\' && $root.config.calendar.format === \'days\' && $root.config.calendar.allow_range',
			'label' => 'Apply pricing to each day in booked day range',
			'width' => '1/1',
		];
	}

	public function is_enabled(): bool {
		$value = $this->field->get_value()['additions'][ $this->get_key() ] ?? null;
		return is_array( $value ) && ! empty( $value['enabled'] );
	}

	abstract public function sanitize_config( $value );
	public function validate_config( $value ) {
		//
	}

	abstract public function get_product_form_config(): array;

	public function get_value() {
		return '';
	}

	public function set_product_type( \Voxel\Product_Type $product_type ) {
		$this->product_type = $product_type;
	}

	public function set_field( \Voxel\Post_Types\Fields\Product_Field $field ) {
		$this->field = $field;
	}

	public function update( $value ): void {
		//
	}

	public function exports() {
		return null;
	}
}
