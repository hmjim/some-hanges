<?php

namespace Voxel\Dynamic_Tags\Control_Structures;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Is_Greater_Than extends Base_Control_Structure {

	public function get_key(): string {
		return 'is_greater_than';
	}

	public function get_label(): string {
		return _x( 'Is greater than', 'modifiers', 'voxel-backend' );
	}

	public function get_arguments(): array {
		return [
			'value' => [
				'type' => \Voxel\Form_Models\Text_Model::class,
				'label' => _x( 'Value', 'modifiers', 'voxel-backend' ),
			],
		];
	}

	public function passes( $last_condition, $value, $args, $group ): bool {
		return is_numeric( $args[0] ?? null ) && ( (float) $value > (float) ( $args[0] ) );
	}
}
