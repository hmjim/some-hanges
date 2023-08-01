<?php

namespace Voxel\Dynamic_Tags\Control_Structures;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Is_Equal_To extends Base_Control_Structure {

	public function get_key(): string {
		return 'is_equal_to';
	}

	public function get_label(): string {
		return _x( 'Is equal to', 'modifiers', 'voxel-backend' );
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
		return (string) $value === (string) ( $args[0] ?? '' );
	}
}
