<?php

namespace Voxel\Membership;

if ( ! defined('ABSPATH') ) {
	exit;
}

abstract class Base_Type {

	public $plan;

	protected $type;

	public function __construct( array $config ) {
		$this->plan = \Voxel\Membership\Plan::get( $config['plan'] ?? null );
		if ( ! $this->plan ) {
			$this->plan = \Voxel\Membership\Plan::get( 'default' );
		}

		$this->init( $config );
	}

	protected function init( array $config ) {
		//
	}

	public function get_type() {
		return $this->type;
	}

	public function is_active() {
		return true;
	}

	public function get_created_at() {
		return null;
	}
}