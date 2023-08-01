<?php

namespace Voxel\Controllers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Taxonomies_Controller extends Base_Controller {

	protected function hooks() {
		$this->on( 'init', '@register_taxonomies', 0 );
		$this->filter( 'register_taxonomy_args', '@manage_existing_taxonomies', 50, 2 );
		$this->on( 'admin_menu', '@add_menu_page' );
		$this->on( 'voxel/backend/screen:manage-taxonomies', '@render_manage_taxonomies_screen' );
		$this->on( 'voxel/backend/screen:create-taxonomy', '@render_create_taxonomy_screen' );
		$this->on( 'admin_post_voxel_create_taxonomy', '@create_taxonomy' );
		$this->on( 'edited_term', '@update_taxonomy_version', 100, 3 );
        $this->on( 'created_term', '@update_taxonomy_version', 100, 3 );
        $this->on( 'delete_term', '@update_taxonomy_version', 100, 3 );
	}

	protected function register_taxonomies() {
		$taxonomies = \Voxel\get('taxonomies');
		foreach ( $taxonomies as $taxonomy ) {
			if ( ! taxonomy_exists( $taxonomy['settings']['key'] ) ) {
				register_taxonomy( $taxonomy['settings']['key'], $taxonomy['settings']['post_type'], [
					'labels' => [
						'name' => $taxonomy['settings']['plural'],
						'singular_name' => $taxonomy['settings']['singular'],
					],
					'public'              => true,
					'show_ui'             => true,
					'publicly_queryable'  => true,
					'hierarchical'        => true,
					'query_var'           => true,
					'show_in_nav_menus'   => true,
					'_is_created_by_voxel' => true,
				] );
			}
		}
	}

	protected function manage_existing_taxonomies( $args, $taxonomy_key ) {
		$config = \Voxel\get( 'taxonomies.'.$taxonomy_key );
		if ( ! empty( $args['_is_created_by_voxel'] ) || empty( $config ) ) {
			return $args;
		}

		if ( ! empty( $config['settings']['plural'] ?? null ) ) {
			$args['labels']['name'] = $config['settings']['plural'];
		}

		if ( ! empty( $config['settings']['singular'] ?? null ) ) {
			$args['labels']['singular_name'] = $config['settings']['singular'];
		}

		return $args;
	}

	protected function create_taxonomy() {
		check_admin_referer( 'voxel_manage_taxonomies' );
		if ( ! current_user_can( 'manage_options' ) ) {
			die;
		}

		if ( empty( $_POST['taxonomy'] ) || ! is_array( $_POST['taxonomy'] ) ) {
			die;
		}

		$taxonomies = \Voxel\get('taxonomies');

		$config = wp_unslash( $_POST['taxonomy'] );
		$key = sanitize_key( $config['key'] ?? '' );
		$singular_name = sanitize_text_field( $config['singular_name'] ?? '' );
		$plural_name = sanitize_text_field( $config['plural_name'] ?? '' );
		$post_types = array_filter( $config['post_type'] ?? [], function( $post_type_key ) {
			return post_type_exists( $post_type_key );
		} );

		if ( $key && $singular_name && $plural_name ) {
			$taxonomies[ $key ] = [
				'settings' => [
					'key' => $key,
					'singular' => $singular_name,
					'plural' => $plural_name,
					'post_type' => $post_types,
				],
			];
		}

		\Voxel\set( 'taxonomies', $taxonomies );

		flush_rewrite_rules();

		wp_safe_redirect( admin_url( 'admin.php?page=voxel-taxonomies&action=edit-taxonomy&taxonomy='.$key ) );
		exit;
	}

	protected function add_menu_page() {
		add_menu_page(
			__( 'Taxonomies', 'voxel-backend' ),
			__( 'Taxonomies', 'voxel-backend' ),
			'manage_options',
			'voxel-taxonomies',
			function() {
				$action_key = $_GET['action'] ?? 'manage-taxonomies';
				$allowed_actions = ['manage-taxonomies', 'create-taxonomy', 'edit-taxonomy', 'reorder-terms'];
				$action = in_array( $action_key, $allowed_actions, true ) ? $action_key : 'manage-taxonomies';
				do_action( 'voxel/backend/screen:'.$action );
			},
			\Voxel\get_image('post-types/ic_txnm.png'),
			'0.275'
		);
	}

	protected function render_manage_taxonomies_screen() {
		$add_taxonomy_url = admin_url('admin.php?page=voxel-taxonomies&action=create-taxonomy');
		$taxonomies = \Voxel\Taxonomy::get_all();

		require locate_template( 'templates/backend/taxonomies/view-taxonomies.php' );
	}

	protected function render_create_taxonomy_screen() {
		require locate_template( 'templates/backend/taxonomies/add-taxonomy.php' );
	}

	protected function update_taxonomy_version( $term_id, $tt_id, $taxonomy_key ) {
		if ( $taxonomy = \Voxel\Taxonomy::get( $taxonomy_key ) ) {
			$taxonomy->update_version();
		}
	}
}
