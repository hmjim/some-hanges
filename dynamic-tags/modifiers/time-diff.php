<?php

namespace Voxel\Dynamic_Tags\Modifiers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Time_Diff extends \Voxel\Dynamic_Tags\Base_Modifier {

	public function get_key(): string {
		return 'time_diff';
	}

	public function get_label(): string {
		return _x( 'Time diff', 'modifiers', 'voxel-backend' );
	}

	public function accepts(): string {
		return \Voxel\T_DATE;
	}

	public function apply( $value, $args, $group ) {
		$timestamp = strtotime( $value );
		if ( ! $timestamp ) {
			return $value;
		}

		return human_time_diff( $timestamp, time() + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
	}

}
