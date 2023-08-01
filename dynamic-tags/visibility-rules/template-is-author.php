<?php

namespace Voxel\Dynamic_Tags\Visibility_Rules;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Template_Is_Author extends Base_Visibility_Rule {

	public function get_type(): string {
		return 'template:is_author';
	}

	public function get_label(): string {
		return _x( 'Is author profile', 'visibility rules', 'voxel-backend' );
	}

	public function props(): array {
		return [
			'author_id' => null,
		];
	}

	public function get_models(): array {
		return [
			'author_id' => [
				'type' => \Voxel\Form_Models\Text_Model::class,
				'label' => _x( 'Enter author ID', 'visibility rules', 'voxel-backend' ),
				'classes' => 'x-col-3 x-grow',
			],
		];
	}

	public function evaluate(): bool {
		return is_author( $this->props['author_id'] );
	}
}
