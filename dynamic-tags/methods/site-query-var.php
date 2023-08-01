<?php

namespace Voxel\Dynamic_Tags\Methods;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Site_Query_Var extends Base_Method {

	public function get_key(): string {
		return 'query_var';
	}

	public function get_label(): string {
		return _x( 'URL parameter', 'modifiers', 'voxel-backend' );
	}

	public function run( $args, $group ) {
		$value = $_GET[ $args[0] ?? null ] ?? null;
		if ( ! is_scalar( $value ) ) {
			return null;
		}

		return $value;
	}

	public function get_arguments(): array {
		return [
			'key' => [
				'type' => \Voxel\Form_Models\Text_Model::class,
				'label' => _x( 'Parameter name', 'modifiers', 'voxel-backend' ),
				'classes' => 'x-col-12',
			],
		];
	}
}
