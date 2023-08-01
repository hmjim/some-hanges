<?php

namespace Voxel\Dynamic_Tags\Visibility_Rules;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Author_Plan_Is extends Base_Visibility_Rule {

	public function get_type(): string {
		return 'author:plan';
	}

	public function get_label(): string {
		return _x( 'Author membership plan is', 'visibility rules', 'voxel-backend' );
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
		$author = \Voxel\get_current_author();
		if ( ! $author ) {
			return false;
		}

		$membership = $author->get_membership();
		$plan_key = $membership->is_active() ? $membership->plan->get_key() : 'default';
		return $plan_key === $this->props['value'];
	}
}
