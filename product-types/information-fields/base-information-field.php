<?php

namespace Voxel\Product_Types\Information_Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

abstract class Base_Information_Field extends \Voxel\Object_Fields\Base_Field {

	/**
	 * Slugified string used to identify a field. Alias of `$this->props['key']`
	 *
	 * @since 1.0
	 */
	protected $key;

	/**
	 * Product type object which this field belongs to.
	 *
	 * @since 1.0
	 */
	protected $product_type;

	protected function base_props(): array {
		return [
			'type' => 'text',
			'key' => 'custom-field',
			'label' => 'Custom Field',
			'description' => '',
			'required' => false,
		];
	}

	public function get_value() {
		//
	}

	public function update( $value ): void {
		//
	}

	public function prepare_for_storage( $value ) {
		return $value;
	}

	public function prepare_for_display( $value ) {
		if ( empty( $value ) ) {
			return null;
		}

		return esc_html( $value );
	}

	public function get_frontend_config() {
		return [
			'id' => $this->get_key(),
			'type' => $this->get_type(),
			'key' => $this->get_key(),
			'label' => $this->get_label(),
			'description' => $this->get_description(),
			'required' => $this->is_required(),
			'props' => $this->frontend_props(),
			'value' => null,
		];
	}

	protected function frontend_props() {
		return [];
	}

	public function set_product_type( \Voxel\Product_Type $product_type ) {
		$this->product_type = $product_type;
	}

}
