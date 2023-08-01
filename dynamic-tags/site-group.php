<?php

namespace Voxel\Dynamic_Tags;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Site_Group extends Base_Group {

	public $key = 'site';
	public $label = 'Site';

	protected function properties(): array {
		$post_types = [
			'label' => 'Post types',
			'type' => \Voxel\T_OBJECT,
			'properties' => [],
		];

		foreach ( \Voxel\Post_Type::get_voxel_types() as $post_type ) {
			$custom_templates = [];
			foreach ( $post_type->repository->get_custom_templates() as $template_group => $template_list ) {
				foreach ( $template_list as $template_details ) {
					$custom_templates[ sprintf( '%s:%s', $template_group, $template_details['label'] ) ] = [
						'label' => sprintf( '%s: %s', $template_group === 'single' ? 'Single page' : 'Preview card', $template_details['label'] ),
						'type' => \Voxel\T_NUMBER,
						'callback' => function() use ($template_details) {
							return $template_details['id'];
						},
					];
				}
			}

			$post_types['properties'][ $post_type->get_key() ] = [
				'label' => $post_type->get_label(),
				'type' => \Voxel\T_OBJECT,
				'properties' => [
					'singular' => [
						'label' => 'Singular name',
						'type' => \Voxel\T_STRING,
						'callback' => function() use ($post_type) {
							return $post_type->get_singular_name();
						},
					],

					'plural' => [
						'label' => 'Plural name',
						'type' => \Voxel\T_STRING,
						'callback' => function() use ($post_type) {
							return $post_type->get_plural_name();
						},
					],

					'icon' => [
						'label' => 'Icon',
						'type' => \Voxel\T_STRING,
						'callback' => function() use ($post_type) {
							return $post_type->get_icon();
						},
					],

					'archive' => [
						'label' => 'Archive link',
						'type' => \Voxel\T_URL,
						'callback' => function() use ($post_type) {
							return $post_type->get_archive_link();
						},
					],

					'create' => [
						'label' => 'Create post link',
						'type' => \Voxel\T_URL,
						'callback' => function() use ($post_type) {
							return $post_type->get_create_post_link();
						},
					],
					'templates' => [
						'label' => 'Templates',
						'type' => \Voxel\T_OBJECT,
						'properties' => [
							'single' => [
								'label' => 'Single page',
								'type' => \Voxel\T_NUMBER,
								'callback' => function() use ($post_type) {
									return $post_type->get_templates()['single'];
								},
							],
							'card' => [
								'label' => 'Preview card',
								'type' => \Voxel\T_NUMBER,
								'callback' => function() use ($post_type) {
									return $post_type->get_templates()['card'];
								},
							],
							'archive' => [
								'label' => 'Archive page',
								'type' => \Voxel\T_NUMBER,
								'callback' => function() use ($post_type) {
									return $post_type->get_templates()['archive'];
								},
							],
							'form' => [
								'label' => 'Submit page',
								'type' => \Voxel\T_NUMBER,
								'callback' => function() use ($post_type) {
									return $post_type->get_templates()['form'];
								},
							],
							'custom' => [
								'label' => 'Custom',
								'type' => \Voxel\T_OBJECT,
								'properties' => $custom_templates,
							],
						],
					],
				],
			];
		}

		return [
			'post_types' => $post_types,

			'title' => [
				'label' => 'Title',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return get_bloginfo('name');
				},
			],

			'tagline' => [
				'label' => 'Tagline',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return get_bloginfo('description');
				},
			],

			'url' => [
				'label' => 'URL',
				'type' => \Voxel\T_URL,
				'callback' => function() {
					return get_bloginfo('url');
				},
			],

			'admin_url' => [
				'label' => 'WP Admin URL',
				'type' => \Voxel\T_URL,
				'callback' => function() {
					return admin_url();
				},
			],

			'login_url' => [
				'label' => 'Login URL',
				'type' => \Voxel\T_URL,
				'callback' => function() {
					return \Voxel\get_auth_url();
				},
			],

			'register_url' => [
				'label' => 'Register URL',
				'type' => \Voxel\T_URL,
				'callback' => function() {
					return add_query_arg( 'register', '', \Voxel\get_auth_url() );
				},
			],

			'logout_url' => [
				'label' => 'Logout URL',
				'type' => \Voxel\T_URL,
				'callback' => function() {
					return \Voxel\get_logout_url();
				},
			],

			'current_plan_url' => [
				'label' => 'Current plan URL',
				'type' => \Voxel\T_URL,
				'callback' => function() {
					return get_permalink( \Voxel\get( 'templates.current_plan' ) ) ?: home_url('/');
				},
			],

			'language' => [
				'label' => 'Language',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return get_bloginfo('language');
				},
			],

			'date' => [
				'label' => 'Date',
				'type' => \Voxel\T_DATE,
				'callback' => function() {
					return current_time('Y-m-d H:i:s');
				},
			],
		];
	}

	protected function methods(): array {
		return [
			'option' => Methods\Site_Option::class,
		];
	}
}
