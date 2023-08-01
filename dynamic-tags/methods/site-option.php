<?php

namespace Voxel\Dynamic_Tags\Methods;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Site_Option extends Base_Method {

	public function get_key(): string {
		return 'option';
	}

	public function get_label(): string {
		return _x( 'Option', 'modifiers', 'voxel-backend' );
	}

	public function run( $args, $group ) {
		return get_option( $args[0] ?? null );
	}

	public function get_arguments(): array {
		return [
			'key' => [
				'type' => \Voxel\Form_Models\Text_Model::class,
				'label' => _x( 'Option key', 'modifiers', 'voxel-backend' ),
			],
		];
	}
}
