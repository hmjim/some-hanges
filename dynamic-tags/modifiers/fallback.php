<?php

namespace Voxel\Dynamic_Tags\Modifiers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Fallback extends \Voxel\Dynamic_Tags\Base_Modifier {

	public function get_key(): string {
		return 'fallback';
	}

	public function get_label(): string {
		return _x( 'Fallback', 'modifiers', 'voxel-backend' );
	}

	public function accepts(): string {
		return \Voxel\T_ANY;
	}

	public function apply( $value, $args, $group ) {
		if ( empty( $value ) && ! in_array( $value, [ 0, '0', 0.0 ], true ) ) {
			$value = $args[0] ?? '';
		}

		return $value;
	}

	public function get_arguments(): array {
		return [
			'text' => [
				'type' => \Voxel\Form_Models\Text_Model::class,
				'label' => _x( 'Fallback text', 'modifiers', 'voxel-backend' ),
			],
		];
	}
}
