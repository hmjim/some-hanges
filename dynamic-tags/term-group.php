<?php

namespace Voxel\Dynamic_Tags;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Term_Group extends Base_Group {

	public $key = 'term';
	public $label = 'Term';

	public $term;

	protected function editor_init(): void {
		$this->term = \Voxel\get_current_term() ?? \Voxel\Term::dummy();
	}

	protected function frontend_init(): void {
		$this->term = \Voxel\get_current_term() ?? \Voxel\Term::dummy();
	}

	protected function properties(): array {
		return [
			':id' => [
				'label' => 'ID',
				'type' => \Voxel\T_NUMBER,
				'callback' => function() {
					return $this->term->get_id();
				},
			],

			':label' => [
				'label' => 'Label',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return $this->term->get_label();
				},
			],

			':slug' => [
				'label' => 'Slug',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return $this->term->get_slug();
				},
			],

			':description' => [
				'label' => 'Description',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					$content = $this->term->get_description();
					$content = links_add_target( make_clickable( $content ) );
					$content = wpautop( $content );
					return $content;
				},
			],

			':icon' => [
				'label' => 'Icon',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return $this->term->get_icon();
				},
			],

			':url' => [
				'label' => 'Permalink',
				'type' => \Voxel\T_URL,
				'callback' => function() {
					return $this->term->get_link();
				},
			],

			':image' => [
				'label' => 'Image',
				'type' => \Voxel\T_NUMBER,
				'callback' => function() {
					return $this->term->get_image_id();
				},
			],

			':area' => [
				'label' => 'Area',
				'type' => \Voxel\T_OBJECT,
				'properties' => [
					'address' => [
						'label' => 'Address',
						'type' => \Voxel\T_STRING,
						'callback' => function() {
							return $this->term->get_area()['address'];
						},
					],
					'southwest' => [
						'label' => 'Southwest',
						'type' => \Voxel\T_OBJECT,
						'properties' => [
							'lat' => [
								'label' => 'Latitude',
								'type' => \Voxel\T_NUMBER,
								'callback' => function() {
									return $this->term->get_area()['swlat'];
								},
							],
							'lng' => [
								'label' => 'Longitude',
								'type' => \Voxel\T_NUMBER,
								'callback' => function() {
									return $this->term->get_area()['swlng'];
								},
							],
						],
					],
					'northeast' => [
						'label' => 'Northeast',
						'type' => \Voxel\T_OBJECT,
						'properties' => [
							'lat' => [
								'label' => 'Latitude',
								'type' => \Voxel\T_NUMBER,
								'callback' => function() {
									return $this->term->get_area()['nelat'];
								},
							],
							'lng' => [
								'label' => 'Longitude',
								'type' => \Voxel\T_NUMBER,
								'callback' => function() {
									return $this->term->get_area()['nelng'];
								},
							],
						],
					],
				],
			],
		];
	}

	protected function methods(): array {
		return [
			'meta' => Methods\Term_Meta::class,
		];
	}
}
