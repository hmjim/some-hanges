<?php

namespace Voxel\Dynamic_Tags\Methods;

if ( ! defined('ABSPATH') ) {
	exit;
}

class User_Meta extends Base_Method {

	public function get_key(): string {
		return 'meta';
	}

	public function get_label(): string {
		return _x( 'User Meta', 'modifiers', 'voxel-backend' );
	}

	public function run( $args, $group ) {
		return get_user_meta( $group->get_user()->get_id(), $args[0] ?? null, true );
	}

	public function get_arguments(): array {
		return [
			'key' => [
				'type' => \Voxel\Form_Models\Text_Model::class,
				'label' => _x( 'Meta key', 'modifiers', 'voxel-backend' ),
			],
		];
	}
}
