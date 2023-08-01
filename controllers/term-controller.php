<?php

namespace Voxel\Controllers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Term_Controller extends Base_Controller {

	protected function hooks() {
		$this->on( 'current_screen', '@register_custom_fields' );
		$this->on( 'created_term', '@save_fields' );
		$this->on( 'edited_term', '@save_fields' );
	}

	protected function register_custom_fields() {
		$screen = get_current_screen();
		if ( ! in_array( $screen->base, [ 'term', 'edit-tags' ], true ) || ! taxonomy_exists( $screen->taxonomy ) ) {
			return;
		}

		$taxonomy = \Voxel\Taxonomy::get( get_current_screen()->taxonomy );
		$this->on( sprintf( '%s_add_form_fields', $taxonomy->get_key() ), '@add_form_fields' );
		$this->on( sprintf( '%s_edit_form_fields', $taxonomy->get_key() ), '@edit_form_fields' );
	}

	protected function add_form_fields() {
		\Voxel\enqueue_maps();

		$fields = [];
		require locate_template( 'templates/backend/terms/term-fields.php' );
	}

	protected function edit_form_fields( $term ) {
		\Voxel\enqueue_maps();

		$fields = [
			'icon' => get_term_meta( $term->term_id, 'voxel_icon', true ),
			'image' => get_term_meta( $term->term_id, 'voxel_image', true ),
			'area' => (object) json_decode( get_term_meta( $term->term_id, 'voxel_area', true ), ARRAY_A ),
		];

		require locate_template( 'templates/backend/terms/term-fields.php' );
	}

	protected function save_fields( $term_id ) {
		$icon = sanitize_text_field( $_POST['voxel_icon'] ?? '' );
		if ( empty( $icon ) ) {
			delete_term_meta( $term_id, 'voxel_icon' );
		} else {
			update_term_meta( $term_id, 'voxel_icon', $icon );
		}

		$image = sanitize_text_field( $_POST['voxel_image'] ?? '' );
		if ( empty( $image ) ) {
			delete_term_meta( $term_id, 'voxel_image' );
		} else {
			update_term_meta( $term_id, 'voxel_image', $image );
		}

		$area = (array) json_decode( wp_unslash( $_POST['voxel_area'] ?? '' ), ARRAY_A );
		if ( empty( $area ) || ! is_numeric( $area['swlat'] ?? null ) ) {
			delete_term_meta( $term_id, 'voxel_area' );
		} else {
			update_term_meta( $term_id, 'voxel_area', wp_slash( wp_json_encode( [
				'address' => sanitize_text_field( $area['address'] ?? '' ),
				'swlat' => floatval( $area['swlat'] ?? 0 ),
				'swlng' => floatval( $area['swlng'] ?? 0 ),
				'nelat' => floatval( $area['nelat'] ?? 0 ),
				'nelng' => floatval( $area['nelng'] ?? 0 ),
			] ) ) );
		}
	}

}
