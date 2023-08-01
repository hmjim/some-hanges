<?php

namespace Voxel\Dynamic_Tags\Control_Structures;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Is_Empty extends Base_Control_Structure {

	public function get_key(): string {
		return 'is_empty';
	}

	public function get_label(): string {
		return _x( 'Is empty', 'modifiers', 'voxel-backend' );
	}

	public function passes( $last_condition, $value, $args, $group ): bool {
		return empty( $value ) && ! in_array( $value, [ '0', 0, 0.0 ], true );
	}
}
