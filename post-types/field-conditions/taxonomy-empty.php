<?php

namespace Voxel\Post_Types\Field_Conditions;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Taxonomy_Empty extends Base_Condition {

	public function get_type(): string {
		return 'taxonomy:empty';
	}

	public function get_label(): string {
		return _x( 'Is empty', 'field conditions', 'voxel-backend' );
	}

	public function evaluate( $value ): bool {
		return ! is_array( $value ) || empty( $value );
	}
}