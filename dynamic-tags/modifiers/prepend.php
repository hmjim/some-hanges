<?php

namespace Voxel\Dynamic_Tags\Modifiers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Prepend extends \Voxel\Dynamic_Tags\Base_Modifier {

	public function get_key(): string {
		return 'prepend';
	}

	public function get_label(): string {
		return _x( 'Prepend', 'modifiers', 'voxel-backend' );
	}

	public function get_arguments(): array {
		return [
			'text' => [
				'type' => \Voxel\Form_Models\Text_Model::class,
				'label' => _x( 'Text to prepend', 'modifiers', 'voxel-backend' ),
			],
		];
	}

	public function apply( $value, $args, $group ) {
		return ( $args[0] ?? '' ).$value;
	}
}
