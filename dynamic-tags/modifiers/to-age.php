<?php

namespace Voxel\Dynamic_Tags\Modifiers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class To_Age extends \Voxel\Dynamic_Tags\Base_Modifier {

	public function get_key(): string {
		return 'to_age';
	}

	public function get_label(): string {
		return _x( 'Get age', 'modifiers', 'voxel-backend' );
	}

	public function accepts(): string {
		return \Voxel\T_DATE;
	}

	public function apply( $value, $args, $group ) {
		$timestamp = strtotime( $value );
		if ( ! $timestamp ) {
			return null;
		}

		$now = time();
		if ( $now < $timestamp ) {
			return null;
		}

		return floor( ( $now - $timestamp ) / ( 365.6 * DAY_IN_SECONDS ) );
	}

}
