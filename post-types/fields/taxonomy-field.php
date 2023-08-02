<?php

namespace Voxel\Post_Types\Fields;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Taxonomy_Field extends Base_Post_Field {

	protected $supported_conditions = ['taxonomy'];

	protected $props = [
		'type' => 'taxonomy',
		'label' => 'Taxonomy',
		'taxonomy' => '',
		'placeholder' => '',
		'multiple' => true,
		'display_as' => 'popup',
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'key' => $this->get_key_model(),
			'placeholder' => $this->get_placeholder_model(),
			'description' => $this->get_description_model(),
			'taxonomy' => [
				'type' => Form_Models\Taxonomy_Select_Model::class,
				'label' => 'Choose taxonomy',
				'width' => '1/1',
				'post_type' => $this->post_type->get_key(),
			],
			'multiple' => [
				'type' => Form_Models\Switcher_Model::class,
				'label' => 'Allow selection of multiple terms?',
				'width' => '1/1',
			],
			'display_as' => [
				'type' => Form_Models\Select_Model::class,
				'label' => 'Display as',
				'width' => '1/1',
				'choices' => [
					'popup' => 'Popup',
					'inline' => 'Inline',
				],
			],
			'required' => $this->get_required_model(),
		];
	}

	public function get_available_terms() {
		if ( empty( $this->get_prop('taxonomy') ) ) {
			return [];
		}

		return \Voxel\Term::query( [
			'taxonomy' => $this->get_prop('taxonomy'),
			'hide_empty' => false,
		] );
	}

	public function is_selected( $term ) {
		static $terms;
		if ( is_null( $terms ) ) {
			$terms = array_map( function( $term ) {
				return $term->get_id();
			}, $this->get_value() );
		}

		return in_array( $term->get_id(), $terms, true );
	}

	public function sanitize( $value ) {
		$value = array_map( function( $item ) {
			return (string) $item;
		}, $value );

		// @todo: validate single selection by getting the inner-most term only
		/*if ( ! $this->props['multiple'] ) {
			$value = isset( $value[0] ) ? [ $value[0] ] : [];
		}*/

		return array_filter( (array) $value, function( $slug ) {
			return term_exists( (string) $slug, $this->props['taxonomy'] );
		} );
	}

	public function update( $value ): void {
		if ( ! taxonomy_exists( $this->props['taxonomy'] ) ) {
			return;
		}

		$ids = [];
		$terms = \Voxel\Term::query( [
			'taxonomy' => $this->props['taxonomy'],
			'slug' => ! empty( $value ) ? $value : [''],
			'hide_empty' => false,
		] );

		if ( is_wp_error( $terms ) ) {
			error_log( $terms->get_error_message() );
			return;
		}

		foreach ( $terms as $term ) {
			$ids = array_merge( $ids, [ $term->get_id() ], $term->get_ancestor_ids() );
		}

		wp_set_object_terms(
			$this->post->get_id(),
			array_unique( $ids ),
			$this->props['taxonomy']
		);
	}

	public function get_value_from_post() {
		$terms = wp_get_object_terms( $this->post->get_id(), $this->get_prop('taxonomy'), [
			'orderby' => 'term_order',
			'order' => 'ASC',
		] );

		$terms = ! is_wp_error( $terms ) ? $terms : [];
		return array_map( '\Voxel\Term::get', $terms );
	}

	public function get_value_from_repeater() {
		$taxonomy = \Voxel\Taxonomy::get( $this->props['taxonomy'] );
		if ( ! $taxonomy ) {
			return null;
		}

		$value = parent::get_value_from_repeater();
		$terms = \Voxel\Term::query( [
			'taxonomy' => $taxonomy->get_key(),
			'hide_empty' => false,
			'orderby' => 'slug__in',
			'slug' => ! empty( $value ) ? $value : [''],
		] );

		return $terms;
	}

	public function check_dependencies() {
		$taxonomy = \Voxel\Taxonomy::get( $this->props['taxonomy'] );
		if ( ! $taxonomy ) {
			throw new \Exception( 'Taxonomy not set.' );
		}
	}

	protected function frontend_props() {
		$taxonomy = \Voxel\Taxonomy::get( $this->props['taxonomy'] );
		$args = [
			'orderby' => 'default',
		];

		$transient_key = sprintf( 'field:%s.%s.%s', $this->post_type->get_key(), $this->get_key(), $taxonomy->get_key() );
		$t = get_transient( $transient_key );

		$terms = ( is_array( $t ) && isset( $t['terms'] ) ) ? $t['terms'] : [];
		$time = ( is_array( $t ) && isset( $t['time'] ) ) ? $t['time'] : 0;
		$hash = ( is_array( $t ) && isset( $t['hash'] ) ) ? $t['hash'] : false;
		$new_hash = md5( wp_json_encode( $args ) );

		if ( ! $t || ( $time < $taxonomy->get_version() ) || $hash !== $new_hash ) {
			$terms = \Voxel\get_terms( $this->props['taxonomy'], $args );
			set_transient( $transient_key, [
				'terms' => $terms,
				'time' => time(),
				'hash' => $new_hash,
			], 14 * DAY_IN_SECONDS );
			// dump('from query');
		} else {
			// dump('from cache');
		}

		$selected = [];
		if ( $selected_terms = $this->get_value() ) {
			foreach ( $selected_terms as $term ) {
				$selected[ $term->get_slug() ] = [
					'id' => $term->get_id(),
					'label' => $term->get_label(),
					'slug' => $term->get_slug(),
					'icon' => \Voxel\get_icon_markup( $term->get_icon() ),
				];
			}
		}

		return [
			'terms' => $terms,
			'selected' => $selected,
			'placeholder' => $this->props['placeholder'] ?: $this->props['label'],
			'multiple' => (bool) $this->props['multiple'],
			'display_as' => $this->props['display_as'],
			'taxonomy' => [
				'label' => $taxonomy ? $taxonomy->get_label() : '',
			],
		];
	}

	protected function editing_value() {
		if ( ! $this->post ) {
			return null;
		}

		$terms = array_map( function( $term ) {
			return $term->get_slug();
		}, (array) $this->get_value() );

		return ! empty( $terms ) ? $terms : null;
	}

	public function exports() {
		return [
			'label' => $this->get_label(),
			'type' => \Voxel\T_OBJECT,
			'loopable' => true,
			'loopcount' => function() {
				return count( $this->get_value() );
			},
			'properties' => [
				'id' => [
					'label' => 'Term ID',
					'type' => \Voxel\T_NUMBER,
					'callback' => function( $index ) {
						$value = (array) $this->get_value();
						$term = $value[ $index ] ?? null;
						return $term ? $term->get_id() : null;
					},
					'list' => function() {
						$value = (array) $this->get_value();
						return array_map( function( $term ) {
							return $term->get_id();
						}, $value );
					},
				],
				'name' => [
					'label' => 'Term name',
					'type' => \Voxel\T_STRING,
					'callback' => function( $index ) {
						$value = (array) $this->get_value();
						$term = $value[ $index ] ?? null;
						return $term ? $term->get_label() : null;
					},
					'list' => function() {
						$value = (array) $this->get_value();
						return array_map( function( $term ) {
							return $term->get_label();
						}, $value );
					},
				],
				'slug' => [
					'label' => 'Term slug',
					'type' => \Voxel\T_STRING,
					'callback' => function( $index ) {
						$value = (array) $this->get_value();
						$term = $value[ $index ] ?? null;
						return $term ? $term->get_slug() : null;
					},
					'list' => function() {
						$value = (array) $this->get_value();
						return array_map( function( $term ) {
							return $term->get_slug();
						}, $value );
					},
				],
				'description' => [
					'label' => 'Term description',
					'type' => \Voxel\T_STRING,
					'callback' => function( $index ) {
						$value = (array) $this->get_value();
						$term = $value[ $index ] ?? null;
						return $term ? $term->get_description() : null;
					},
				],
				'link' => [
					'label' => 'Term link',
					'type' => \Voxel\T_URL,
					'callback' => function( $index ) {
						$value = (array) $this->get_value();
						$term = $value[ $index ] ?? null;
						return $term ? $term->get_link() : null;
					},
				],
				'icon' => [
					'label' => 'Term icon',
					'type' => \Voxel\T_STRING,
					'callback' => function( $index ) {
						$value = (array) $this->get_value();
						$term = $value[ $index ] ?? null;
						return $term ? $term->get_icon() : null;
					},
				],
				'image' => [
					'label' => 'Term image',
					'type' => \Voxel\T_NUMBER,
					'callback' => function( $index ) {
						$value = (array) $this->get_value();
						$term = $value[ $index ] ?? null;
						return $term ? $term->get_image_id() : null;
					},
				],
			],
		];
	}

	public function export_to_personal_data() {
		$terms = $this->get_value();
		if ( empty( $terms ) ) {
			return null;
		}

		return join( ', ', array_map( function( $term ) {
			return $term->get_label();
		}, $terms ) );
	}
}
