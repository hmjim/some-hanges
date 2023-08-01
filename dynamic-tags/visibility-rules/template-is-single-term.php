<?php

namespace Voxel\Dynamic_Tags\Visibility_Rules;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Template_Is_Single_Term extends Base_Visibility_Rule {

	public function get_type(): string {
		return 'template:is_single_term';
	}

	public function get_label(): string {
		return _x( 'Is single term', 'visibility rules', 'voxel-backend' );
	}

	public function props(): array {
		return [
			'taxonomy' => null,
			'term_id' => null,
		];
	}

	public function get_models(): array {
		$taxonomies = array_filter( \Voxel\Taxonomy::get_all(), function( $taxonomy ) {
			return $taxonomy->is_public();
		} );

		return [
			'taxonomy' => [
				'type' => \Voxel\Form_Models\Select_Model::class,
				'label' => _x( 'Taxonomy', 'visibility rules', 'voxel-backend' ),
				'classes' => 'x-col-3 x-grow',
				'choices' => array_map( function( $taxonomy ) {
					return sprintf( '%s (%s)', $taxonomy->get_label(), $taxonomy->get_key() );
				}, $taxonomies ),
			],
			'term_id' => [
				'type' => \Voxel\Form_Models\Text_Model::class,
				'label' => _x( 'Enter term ID or slug. Leave empty to match all terms in selected taxonomy.', 'visibility rules', 'voxel-backend' ),
				'classes' => 'x-col-3 x-grow',
			],
		];
	}

	public function evaluate(): bool {
		return is_tax( $this->props['taxonomy'], $this->props['term_id'] );
	}
}
