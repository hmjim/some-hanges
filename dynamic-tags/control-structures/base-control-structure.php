<?php

namespace Voxel\Dynamic_Tags\Control_Structures;

if ( ! defined('ABSPATH') ) {
	exit;
}

abstract class Base_Control_Structure extends \Voxel\Dynamic_Tags\Base_Modifier {

	public function get_type(): string {
		return 'control-structure';
	}

	public function apply( $value, $args, $group ) {
		return $value;
	}

	public function passes( $last_condition, $value, $args, $group ): bool {
		return true;
	}
}
