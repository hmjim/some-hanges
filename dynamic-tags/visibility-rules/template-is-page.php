<?php

namespace Voxel\Dynamic_Tags\Visibility_Rules;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Template_Is_Page extends Base_Visibility_Rule {

	public function get_type(): string {
		return 'template:is_page';
	}

	public function get_label(): string {
		return _x( 'Is page', 'visibility rules', 'voxel-backend' );
	}

	public function props(): array {
		return [
			'page_id' => null,
		];
	}

	public function get_models(): array {
		global $wpdb;

		$results = $wpdb->get_results( <<<SQL
			SELECT ID, post_title FROM {$wpdb->posts}
			WHERE post_type = 'page' AND post_status = 'publish'
		SQL, OBJECT_K );

		$pages = array_map( function( $row ) {
			return sprintf( '%s (#%d)', $row->post_title, $row->ID );
		}, $results );

		return [
			'page_id' => [
				'type' => \Voxel\Form_Models\Select_Model::class,
				'label' => _x( 'Select page', 'visibility rules', 'voxel-backend' ),
				'classes' => 'x-col-3 x-grow',
				'choices' => $pages,
			],
		];
	}

	public function evaluate(): bool {
		return is_page( $this->props['page_id'] );
	}
}
