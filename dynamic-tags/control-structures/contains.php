<?php

namespace Voxel\Dynamic_Tags\Control_Structures;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Contains extends Base_Control_Structure {

	public function get_key(): string {
		return 'contains';
	}

	public function get_label(): string {
		return _x( 'Contains', 'modifiers', 'voxel-backend' );
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
		return str_contains( (string) $value, (string) ( $args[0] ?? '' ) );
	}
}
