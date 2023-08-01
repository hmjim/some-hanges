<?php

namespace Voxel\Dynamic_Tags\Visibility_Rules;

if ( ! defined('ABSPATH') ) {
	exit;
}

class User_Role_Is extends Base_Visibility_Rule {

	public function get_type(): string {
		return 'user:role';
	}

	public function get_label(): string {
		return _x( 'User role is', 'visibility rules', 'voxel-backend' );
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
				'choices' => array_map( function( $role ) {
					return $role['name'];
				}, wp_roles()->roles ),
			],
		];
	}

	public function evaluate(): bool {
		$current_user = \Voxel\current_user();
		if ( ! $current_user ) {
			return false;
		}

		return $current_user->has_role( $this->props['value'] );
	}
}
