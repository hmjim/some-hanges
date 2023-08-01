<?php

namespace Voxel\Controllers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Post_Types_Controller extends Base_Controller {

	protected function hooks() {
		$this->on( 'init', '@register_post_types', 0 );
		$this->on( 'init', '@register_post_statuses', 0 );
		$this->on( 'register_post_type_args', '@manage_existing_types', 50, 2 );
		$this->on( 'admin_menu', '@add_menu_page' );
		$this->on( 'voxel/backend/post-types/screen:manage-types', '@render_manage_types_screen' );
		$this->on( 'voxel/backend/post-types/screen:create-type', '@render_create_type_screen' );
		$this->on( 'admin_post_voxel_create_post_type', '@create_post_type' );
		$this->filter( 'admin_body_class', '@admin_body_class' );
	}

	protected function create_post_type() {
		check_admin_referer( 'voxel_manage_post_types' );
		if ( ! current_user_can( 'manage_options' ) ) {
			die;
		}

		if ( empty( $_POST['post_type'] ) || ! is_array( $_POST['post_type'] ) ) {
			die;
		}

		$post_types = \Voxel\get( 'post_types', [] );

		$key = sanitize_key( $_POST['post_type']['key'] ?? '' );
		$singular_name = sanitize_text_field( $_POST['post_type']['singular_name'] ?? '' );
		$plural_name = sanitize_text_field( $_POST['post_type']['plural_name'] ?? '' );

		if ( $key && $singular_name && $plural_name && ! isset( $post_types[ $key ] ) ) {
			$post_types[ $key ] = [
				'settings' => [
					'key' => $key,
					'singular' => $singular_name,
					'plural' => $plural_name,
				],
				'fields' => [],
				'filters' => [],
			];
		}

		\Voxel\set( 'post_types', $post_types );

		flush_rewrite_rules();

		wp_safe_redirect( admin_url( 'admin.php?page=voxel-post-types&page=edit-post-type-'.$key ) );
		exit;
	}

	protected function register_post_types() {
		$post_types = \Voxel\get('post_types');

		foreach ( $post_types as $post_type_key => $post_type ) {
			if ( ! post_type_exists( $post_type_key ) ) {
				$args = [
					'labels' => [
						'name' => $post_type['settings']['plural'] ?? '',
						'singular_name' => $post_type['settings']['singular'] ?? '',
					],
					'public'              => true,
					'show_ui'             => true,
					'capability_type'     => 'page',
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false,
					'query_var'           => true,
					'supports'            => [ 'title', 'publicize', 'thumbnail', 'comments', 'editor' ],
					'menu_position'       => 3,
					'show_in_nav_menus'   => true,
					'_is_created_by_voxel' => true,
					'has_archive' => true,
				];

				if ( ( $post_type['settings']['options']['gutenberg'] ?? '' ) === 'enabled' ) {
					$args['show_in_rest'] = true;
				}

				if ( ( $post_type['settings']['options']['excerpt'] ?? '' ) === 'enabled' ) {
					$args['supports'][] = 'excerpt';
				}

				if ( ( $post_type['settings']['options']['delete_with_user'] ?? '' ) === 'enabled' ) {
					$args['delete_with_user'] = true;
				} elseif ( ( $post_type['settings']['options']['delete_with_user'] ?? '' ) === 'disabled' ) {
					$args['delete_with_user'] = false;
				}

				if ( $post_type['settings']['permalinks']['custom'] ?? false ) {
					if ( $post_type_key === 'profile' ) {
						global $wp_rewrite;
						$wp_rewrite->author_base = $post_type['settings']['permalinks']['slug'] ?? $post_type_key;
						$args['has_archive'] = $post_type['settings']['permalinks']['slug'] ?? $post_type_key;
					} else {
						$args['rewrite'] = [
							'slug' => $post_type['settings']['permalinks']['slug'] ?? $post_type_key,
							'with_front' => true,
						];
					}
				}

				register_post_type( $post_type_key, $args );
			}
		}
	}

	protected function register_post_statuses() {
		register_post_status( 'rejected', [
			'label' => _x( 'Rejected', 'post statuses', 'voxel-backend' ),
			'internal' => false,
			'public' => false,
			'label_count'  => _n_noop( 'Rejected <span class="count">(%s)</span>', 'Rejected <span class="count">(%s)</span>', 'voxel-backend' ),
		] );

		add_action( 'admin_footer-post.php', function() {
			global $post;
			$post = \Voxel\Post::get( $post );
			if ( ! ( $post && $post->post_type && $post->post_type->is_managed_by_voxel() ) ) {
				return;
			}

			$status = $post->get_status(); ?>
			<script type="text/javascript">
			jQuery( function($) {
				$('select#post_status').append('<option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>'+<?= wp_json_encode( _x( 'Rejected', 'post statuses', 'voxel-backend' ) ) ?>+'</option>');
				<?php if ( $status === 'rejected' ): ?>
					$('#post-status-display').text( <?= wp_json_encode( _x( 'Rejected', 'post statuses', 'voxel-backend' ) ) ?> );
				<?php endif ?>
			} );
			</script>
			<?php
		} );
	}

	protected function manage_existing_types( $args, $post_type_key ) {
		$config = \Voxel\get( 'post_types.'.$post_type_key );
		if ( ! empty( $args['_is_created_by_voxel'] ) || empty( $config ) ) {
			return $args;
		}

		if ( ! empty( $config['settings']['plural'] ?? null ) ) {
			$args['labels']['name'] = $config['settings']['plural'];
		}

		if ( ! empty( $config['settings']['singular'] ?? null ) ) {
			$args['labels']['singular_name'] = $config['settings']['singular'];
		}

		if ( ( $config['settings']['options']['gutenberg'] ?? '' ) === 'enabled' ) {
			if ( ! is_array( $args['supports'] ?? null ) ) {
				$args['supports'] = [];
			}

			$args['supports'][] = 'editor';
			$args['show_in_rest'] = true;
		}

		if ( ( $config['settings']['options']['excerpt'] ?? '' ) === 'enabled' ) {
			if ( ! is_array( $args['supports'] ?? null ) ) {
				$args['supports'] = [];
			}

			$args['supports'][] = 'excerpt';
		}

		if ( ( $config['settings']['options']['delete_with_user'] ?? '' ) === 'enabled' ) {
			$args['delete_with_user'] = true;
		} elseif ( ( $config['settings']['options']['delete_with_user'] ?? '' ) === 'disabled' ) {
			$args['delete_with_user'] = false;
		}

		if ( $config['settings']['permalinks']['custom'] ?? false ) {
			if ( $post_type_key !== 'profile' ) {
				$args['rewrite'] = [
					'slug' => $config['settings']['permalinks']['slug'] ?? $post_type_key,
					'with_front' => true,
				];
			}
		}

		return $args;
	}

	protected function add_menu_page() {
		add_menu_page(
			__( 'Post Types', 'voxel-backend' ),
			__( 'Post Types', 'voxel-backend' ),
			'manage_options',
			'voxel-post-types',
			function() {
				$action_key = $_GET['action'] ?? 'manage-types';
				$allowed_actions = ['manage-types', 'create-type', 'edit-type'];
				$action = in_array( $action_key, $allowed_actions, true ) ? $action_key : 'manage-types';
				do_action( 'voxel/backend/post-types/screen:'.$action );
			},
			\Voxel\get_image('post-types/ic_cpt.png'),
			'0.271'
		);

		foreach ( \Voxel\Post_Type::get_all() as $post_type ) {
			if ( in_array( $post_type->get_key(), [ 'elementor_library' ], true ) ) {
				$parent_slug = '_vx_hidden';
			} else {
				$parent_slug = $post_type->get_key() === 'post' ? 'edit.php' : sprintf( 'edit.php?post_type=%s', $post_type->get_key() );
			}

			add_submenu_page(
				$parent_slug,
				__( 'Edit post type', 'voxel-backend' ),
				__( 'Edit post type', 'voxel-backend' ),
				'manage_options',
				sprintf( 'edit-post-type-%s', $post_type->get_key() ),
				function() use ( $post_type ) {
					$_GET['post_type'] = $post_type->get_key();
					do_action( 'voxel/backend/post-types/screen:edit-type' );
				},
				10e5
			);
		}
	}

	protected function admin_body_class( $classes ) {
		if ( str_starts_with( ( $_GET['page'] ?? '' ), 'edit-post-type' ) ) {
			$classes .= ' vx-dark-mode ';
		}

		if ( str_starts_with( ( $_GET['page'] ?? '' ), 'vx-page-' ) ) {
			$classes .= ' vx-dark-mode ';
		}

		return $classes;
	}

	protected function render_manage_types_screen() {
		$add_type_url = admin_url('admin.php?page=voxel-post-types&action=create-type');
		$voxel_types = \Voxel\Post_Type::get_voxel_types();
		$other_types = \Voxel\Post_Type::get_other_types();
		require locate_template( 'templates/backend/post-types/view-post-types.php' );
	}

	protected function render_create_type_screen() {
		require locate_template( 'templates/backend/post-types/add-post-type.php' );
	}
}
