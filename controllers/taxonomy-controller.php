<?php

namespace Voxel\Controllers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Taxonomy_Controller extends Base_Controller {

	protected function hooks() {
		$this->on( 'voxel/backend/screen:edit-taxonomy', '@render_edit_screen', 30 );
		$this->on( 'admin_post_voxel_save_taxonomy_settings', '@save_taxonomy_settings' );
	}

	protected function render_edit_screen() {
		$key = $_GET['taxonomy'] ?? null;
		$taxonomy = \Voxel\Taxonomy::get( $key );
		if ( ! ( $key && $taxonomy ) ) {
			return;
		}

		$templates = $taxonomy->get_templates( $create_if_not_exists = true );
		require locate_template( 'templates/backend/taxonomies/edit-taxonomy.php' );
	}

	protected function save_taxonomy_settings() {
		check_admin_referer( 'voxel_save_taxonomy_settings' );
		if ( ! current_user_can( 'manage_options' ) ) {
			die;
		}

		if ( empty( $_POST['taxonomy'] ) ) {
			die;
		}

		$config = wp_unslash( $_POST['taxonomy'] );
		$taxonomy = \Voxel\Taxonomy::get( $config['key'] );
        if ( ! ( $config['key'] && $config['post_type'] && $taxonomy ) ) {
        	die;
        }

        // delete post type
        if ( ! empty( $_POST['remove_taxonomy'] ) && $_POST['remove_taxonomy'] === 'yes' ) {
        	$taxonomy->delete();

			wp_safe_redirect( admin_url( 'admin.php?page=voxel-taxonomies' ) );
			die;
        }

		$singular_name = sanitize_text_field( $config['singular_name'] ?? '' );
		$plural_name = sanitize_text_field( $config['plural_name'] ?? '' );
		$post_types = array_filter( $config['post_type'], function( $post_type_key ) {
			return post_type_exists( $post_type_key );
		} );

        // edit post type
        $taxonomy->update( [
        	'settings' => [
				'key' => $taxonomy->get_key(),
				'singular' => $singular_name,
				'plural' => $plural_name,
				'post_type' => $post_types,
        	],
        ] );

		wp_safe_redirect( admin_url( 'admin.php?page=voxel-taxonomies&action=edit-taxonomy&taxonomy='.$taxonomy->get_key() ) );
		die;
	}
}
