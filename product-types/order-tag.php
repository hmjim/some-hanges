<?php

namespace Voxel\Product_Types;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Order_Tag {

	public $product_type;

	protected $props = [
		'key' => '',
		'label' => '',
		'primary_color' => '',
		'secondary_color' => '',
		'is_default' => false,
		'has_qr_code' => false,
	];

	public function __construct( $props ) {
		foreach ( $props as $key => $value ) {
			if ( array_key_exists( $key, $this->props ) ) {
				$this->props[ $key ] = $value;
			}
		}
	}

	public function is_valid(): bool {
		return ! empty( $this->props['key'] );
	}

	public function get_key() {
		return $this->props['key'];
	}

	public function get_label() {
		return $this->props['label'];
	}

	public function get_primary_color() {
		return $this->props['primary_color'];
	}

	public function get_secondary_color() {
		return $this->props['secondary_color'];
	}

	public function is_default() {
		return $this->props['is_default'];
	}

	public function has_qr_code() {
		return $this->props['has_qr_code'];
	}

	public function set_product_type( $product_type ) {
		$this->product_type = $product_type;
	}

	public function get_props() {
		return $this->props;
	}
}
