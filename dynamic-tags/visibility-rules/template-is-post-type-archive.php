<?php

namespace Voxel\Dynamic_Tags\Visibility_Rules;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Template_Is_Post_Type_Archive extends Base_Visibility_Rule {

	public function get_type(): string {
		return 'template:is_post_type_archive';
	}

	public function get_label(): string {
		return _x( 'Is post type archive', 'visibility rules', 'voxel-backend' );
	}

	public function props(): array {
		return [
			'post_type' => null,
		];
	}

	public function get_models(): array {
		$post_types = array_filter( \Voxel\Post_Type::get_all(), function( $post_type ) {
			return $post_type->wp_post_type->has_archive;
		} );

		return [
			'post_type' => [
				'type' => \Voxel\Form_Models\Select_Model::class,
				'label' => _x( 'Post type', 'visibility rules', 'voxel-backend' ),
				'classes' => 'x-col-3 x-grow',
				'choices' => array_map( function( $post_type ) {
					return $post_type->get_label();
				}, $post_types ),
			],
		];
	}

	public function evaluate(): bool {
		if ( $this->props['post_type'] === ':custom' ) {
			return is_single( $this->props['post_id'] );
		}

		return is_post_type_archive( $this->props['post_type'] );
	}
}
