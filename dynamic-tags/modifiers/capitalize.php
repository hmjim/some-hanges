<?php

namespace Voxel\Dynamic_Tags\Modifiers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Capitalize extends \Voxel\Dynamic_Tags\Base_Modifier {

	public function get_label(): string {
		return _x( 'Capitalize', 'modifiers', 'voxel-backend' );
	}

	public function get_key(): string {
		return 'capitalize';
	}

	public function apply( $value, $args, $group ) {
		return ucwords( $value );
	}

}
