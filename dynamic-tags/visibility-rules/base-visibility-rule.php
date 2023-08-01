<?php

namespace Voxel\Dynamic_Tags\Visibility_Rules;

if ( ! defined('ABSPATH') ) {
	exit;
}

abstract class Base_Visibility_Rule {

	/**
	 * List of condition properties for individual condition classes
	 * to store their custom data.
	 *
	 * @since 1.0
	 */
	protected $props;

	/**
	 * Unique string identifier for condition types.
	 *
	 * @since 1.0
	 */
	abstract public function get_type(): string;

	abstract public function get_label(): string;

	abstract public function evaluate(): bool;

	protected function props(): array {
		return [];
	}

	public function __construct( $props = [] ) {
		$this->props = $this->props();

		// override props if any provided as a parameter
		foreach ( $props as $key => $value ) {
			if ( array_key_exists( $key, $this->props ) ) {
				$this->props[ $key ] = $value;
			}
		}
	}

	public function get_props(): array {
		return $this->props;
	}

	public function get_models(): array {
		return [];
	}

	public function render_settings() {
		$settings = $this->get_models();

		foreach ( $settings as $key => $model ) {
			$model_type = $model['type'];
			$model['v-model'] = sprintf( 'condition[%s]', esc_attr( wp_json_encode( $key ) ) );
			$model['width'] = '1/2';
			unset( $model['type'] );

			$model_type::render( $model );
		}
	}

	public function get_editor_config() {
		return [
			'type' => $this->get_type(),
			'label' => $this->get_label(),
			'props' => $this->get_props(),
		];
	}
}
