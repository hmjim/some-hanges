<?php

namespace Voxel\Dynamic_Tags\Visibility_Rules;

if ( ! defined('ABSPATH') ) {
	exit;
}

class User_Plan_Is extends Base_Visibility_Rule {

	public function get_type(): string {
		return 'user:plan';
	}

	public function get_label(): string {
		return _x( 'User membership plan is', 'visibility rules', 'voxel-backend' );
	}

	public function props(): array {
		return [
			'value' => null,
		];
	}

	public function get_models(): array {
		return [
			'value' => [
				'type' => \Voxel\Form_Models\Select_Model::class,
				'label' => _x( 'Value', 'visibility rules', 'voxel-backend' ),
				'width' => '1/2',
				'choices' => array_map( function( $plan ) {
					return $plan->get_label();
				}, \Voxel\Membership\Plan::all() ),
			],
		];
	}

	public function evaluate(): bool {
		$current_user = \Voxel\current_user();
		if ( ! $current_user ) {
			return false;
		}

		$membership = $current_user->get_membership();
		$plan_key = $membership->is_active() ? $membership->plan->get_key() : 'default';

		return $plan_key === $this->props['value'];
	}
}
