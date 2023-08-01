<?php

namespace Voxel\Dynamic_Tags;

if ( ! defined('ABSPATH') ) {
	exit;
}

abstract class Base_Modifier {

	public function get_type(): string {
		return 'modifier';
	}

	abstract public function get_key(): string;

	abstract public function get_label(): string;

	abstract public function apply( $value, $args, $group );

	public function accepts(): string {
		return \Voxel\T_STRING;
	}

	public function get_arguments(): array {
		return [];
	}

	public function render_settings( $args = [] ) {
		$settings = $this->get_arguments();

		if ( empty( $settings ) ) {
			$settings[] = [
				'type' => \Voxel\Form_Models\Info_Model::class,
				'label' => _x( 'No additional settings.', 'modifiers', 'voxel-backend' ),
				'classes' => 'alt-label',
			];
		}

		foreach ( $settings as $key => $model ) {
			$model_type = $model['type'];
			$model['v-model'] = sprintf( '%s.arguments[%s]', $args['identifier'] ?? 'modifier', esc_attr( wp_json_encode( $key ) ) );
			unset( $model['type'] );

			$model_type::render( $model );
		}
	}

	public function get_editor_config() {
		return [
			'key' => $this->get_key(),
			'label' => $this->get_label(),
			'type' => $this->get_type(),
			'accepts' => $this->accepts(),
			'arguments' => array_fill_keys( array_keys( $this->get_arguments() ), null ),
		];
	}
}
