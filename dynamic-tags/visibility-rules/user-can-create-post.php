<?php

namespace Voxel\Dynamic_Tags\Visibility_Rules;

if ( ! defined('ABSPATH') ) {
	exit;
}

class User_Can_Create_Post extends Base_Visibility_Rule {

	public function get_type(): string {
		return 'user:can_create_post';
	}

	public function get_label(): string {
		return _x( 'User can create new post', 'visibility rules', 'voxel-backend' );
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
				'label' => _x( 'Post type', 'visibility rules', 'voxel-backend' ),
				'width' => '1/2',
				'choices' => array_map( function( $post_type ) {
					return $post_type->get_label();
				}, \Voxel\Post_Type::get_voxel_types() ),
			],
		];
	}

	public function evaluate(): bool {
		$current_user = \Voxel\current_user();
		if ( ! $current_user ) {
			return false;
		}

		return $current_user->can_create_post( $this->props['value'] );
	}
}
