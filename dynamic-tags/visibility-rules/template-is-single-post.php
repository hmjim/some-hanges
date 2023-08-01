<?php

namespace Voxel\Dynamic_Tags\Visibility_Rules;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Template_Is_Single_Post extends Base_Visibility_Rule {

	public function get_type(): string {
		return 'template:is_single_post';
	}

	public function get_label(): string {
		return _x( 'Is single post', 'visibility rules', 'voxel-backend' );
	}

	public function props(): array {
		return [
			'post_type' => null,
			'post_id' => null,
		];
	}

	public function get_models(): array {
		$post_types = array_filter( \Voxel\Post_Type::get_all(), function( $post_type ) {
			return $post_type->wp_post_type->public;
		} );

		return [
			'post_type' => [
				'type' => \Voxel\Form_Models\Select_Model::class,
				'label' => _x( 'Post type', 'visibility rules', 'voxel-backend' ),
				'classes' => 'x-col-3 x-grow',
				'choices' => array_map( function( $post_type ) {
					return $post_type->get_label();
				}, $post_types ) + [ ':custom' => '&mdash; Specific post' ],
			],
			'post_id' => [
				'v-if' => 'condition.post_type === \':custom\'',
				'type' => \Voxel\Form_Models\Text_Model::class,
				'label' => _x( 'Enter post ID or slug', 'visibility rules', 'voxel-backend' ),
				'classes' => 'x-col-3 x-grow',
			],
		];
	}

	public function evaluate(): bool {
		if ( $this->props['post_type'] === ':custom' ) {
			return is_single( $this->props['post_id'] );
		}

		return is_singular( $this->props['post_type'] );
	}
}
