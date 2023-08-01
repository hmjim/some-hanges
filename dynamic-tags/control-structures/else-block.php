<?php

namespace Voxel\Dynamic_Tags\Control_Structures;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Else_Block extends Base_Control_Structure {

	public function get_key(): string {
		return 'else';
	}

	public function get_label(): string {
		return _x( 'Else', 'modifiers', 'voxel-backend' );
	}

	public function passes( $last_condition, $value, $args, $group ): bool {
		return ! $last_condition;
	}

	public function apply( $value, $args, $group ) {
		return $args[0] ?? '';
	}

	public function get_arguments(): array {
		return [
			'text' => [
				'type' => \Voxel\Form_Models\Text_Model::class,
				'label' => _x( 'Else content', 'modifiers', 'voxel-backend' ),
			],
		];
	}
}
