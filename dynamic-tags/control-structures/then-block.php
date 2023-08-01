<?php

namespace Voxel\Dynamic_Tags\Control_Structures;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Then_Block extends Base_Control_Structure {

	public function get_key(): string {
		return 'then';
	}

	public function get_label(): string {
		return _x( 'Then', 'modifiers', 'voxel-backend' );
	}

	public function get_arguments(): array {
		return [
			'text' => [
				'type' => \Voxel\Form_Models\Text_Model::class,
				'label' => _x( 'Text', 'modifiers', 'voxel-backend' ),
			],
		];
	}

	public function passes( $last_condition, $value, $args, $group ): bool {
		return $last_condition;
	}

	public function apply( $value, $args, $group ) {
		return $args[0] ?? '';
	}
}
