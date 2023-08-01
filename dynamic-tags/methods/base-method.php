<?php

namespace Voxel\Dynamic_Tags\Methods;

if ( ! defined('ABSPATH') ) {
	exit;
}

abstract class Base_Method extends \Voxel\Dynamic_Tags\Base_Modifier {

	abstract public function run( $args, $group );

	public function get_type(): string {
		return 'method';
	}

	public function apply( $value, $args, $group ) {
		return $this->run( $args, $group );
	}
}
