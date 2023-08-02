<?php

namespace Voxel\Post_Types\Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Post_Relation_Field extends Base_Post_Field {

	protected $cached_value;

	protected $props = [
		'type' => 'post-relation',
		'label' => 'Post relation',
		'placeholder' => '',
		'relation_type' => 'has_one',
		'post_types' => [],
		'use_custom_key' => false,
		'custom_key' => 'post-relation',
	];

	public function get_models(): array {
		$post_types = [];
		foreach ( \Voxel\Post_Type::get_voxel_types() as $post_type ) {
			$post_types[ $post_type->get_key() ] = $post_type->get_label();
		}

		return [
			'label' => $this->get_label_model(),
			'key' => $this->get_key_model(),
			'placeholder' => $this->get_placeholder_model(),
			'description' => $this->get_description_model(),
			'required' => $this->get_required_model(),
			'relation_type' => [
				'type' => \Voxel\Form_Models\Select_Model::class,
				'label' => 'Relation type',
				'classes' => 'x-col-12',
				':class' => '{"vx-disabled": ($root.config.settings.key === "collection" && field.key === "items")}',
				'choices' => [
					'has_one' => 'Has one',
					'has_many' => 'Has many',
					'belongs_to_one' => 'Belongs to one',
					'belongs_to_many' => 'Belongs to many',
				],
			],
			'post_types' => [
				'type' => \Voxel\Form_Models\Checkboxes_Model::class,
				'label' => 'Related to',
				'choices' => $post_types,
				'classes' => 'x-col-12',
			],
			'use_custom_key' => [
				'type' => \Voxel\Form_Models\Switcher_Model::class,
				'label' => 'Use custom relation key',
				':class' => '{"vx-disabled": ($root.config.settings.key === "collection" && field.key === "items")}',
				'description' => 'By default, the field key will be used as the relation key. Enable this setting to use a custom relation key instead.',
				'classes' => 'x-col-12',
			],
			'custom_key' => [
				'v-if' => 'field.use_custom_key',
				'type' => \Voxel\Form_Models\Text_Model::class,
				'label' => 'Relation key',
				'classes' => 'x-col-12',
			],
		];
	}

	public function sanitize( $value ) {
		global $wpdb;

		if ( empty( $this->props['post_types'] ) ) {
			return null;
		}

		$post_ids = [];
		foreach ( (array) $value as $post_id ) {
			if ( ! is_numeric( $post_id ) ) {
				continue;
			}

			$post_ids[] = absint( $post_id );
			if ( in_array( $this->props['relation_type'], [ 'has_one', 'belongs_to_one' ], true ) ) {
				break;
			}
		}

		if ( empty( $post_ids ) ) {
			return null;
		}

		$query_ids = join( ',', $post_ids );
		$query_post_types = '\''.join( '\',\'', array_map( 'esc_sql', $this->props['post_types'] ) ).'\'';
		$author_id = absint( $this->post ? $this->post->get_author_id() : get_current_user_id() );

		$existing_ids = $wpdb->get_col( <<<SQL
			SELECT ID
			FROM {$wpdb->posts}
			WHERE post_status = 'publish'
				AND post_type IN ({$query_post_types})
				AND ID IN ({$query_ids})
			ORDER BY FIELD(ID,{$query_ids})
		SQL );

		$existing_ids = array_map( 'absint', $existing_ids );

		if ( empty( $existing_ids ) ) {
			return null;
		}

		return $existing_ids;
	}

	public function update( $value ): void {
		global $wpdb;

		// delete existing relations
		$delete_column = in_array( $this->props['relation_type'], [ 'has_one', 'has_many' ], true ) ? 'parent_id' : 'child_id';
		$wpdb->delete( $wpdb->prefix.'voxel_relations', [
			$delete_column => $this->post->get_id(),
			'relation_key' => $this->get_relation_key(),
		] );

		// insert new relations
		if ( ! empty( $value ) ) {
			$rows = [];
			foreach ( (array) $value as $index => $post_id ) {
				$parent_id = in_array( $this->props['relation_type'], [ 'has_one', 'has_many' ], true ) ? $this->post->get_id() : $post_id;
				$child_id = in_array( $this->props['relation_type'], [ 'has_one', 'has_many' ], true ) ? $post_id : $this->post->get_id();
				$rows[] = $wpdb->prepare( '(%d,%d,%s,%d)', $parent_id, $child_id, $this->get_relation_key(), $index );
			}

			$query = "INSERT INTO {$wpdb->prefix}voxel_relations (`parent_id`, `child_id`, `relation_key`, `order`) VALUES ";
			$query .= implode( ',', $rows );
			$wpdb->query( $query );
		}

		// clear cache
		$select_key = in_array( $this->get_prop('relation_type'), [ 'has_one', 'has_many' ], true ) ? 'child_id' : 'parent_id';
		$cache_key = sprintf( 'relations:%s:%d:%s', $this->get_relation_key(), $this->post->get_id(), $select_key );
		wp_cache_delete( $cache_key, 'voxel' );
	}

	public function get_relation_key() {
		return $this->props['use_custom_key'] ? $this->props['custom_key'] : $this->props['key'];
	}

	public function get_value_from_post() {
		if ( $this->cached_value === null ) {
			$select_key = in_array( $this->get_prop('relation_type'), [ 'has_one', 'has_many' ], true ) ? 'child_id' : 'parent_id';
			$cache_key = sprintf( 'relations:%s:%d:%s', $this->get_relation_key(), $this->post->get_id(), $select_key );
			$cache_result = wp_cache_get( $cache_key, 'voxel' );

			if ( is_array( $cache_result ) ) {
				$this->cached_value = $cache_result;
			} else {
				$this->cached_value = $this->get_related_posts();
			}
		}

		return $this->cached_value;
	}

	protected function get_related_posts() {
		global $wpdb;

		if ( in_array( $this->props['relation_type'], [ 'has_one', 'has_many' ], true ) ) {
			$rows = $wpdb->get_col( $wpdb->prepare( <<<SQL
				SELECT child_id
				FROM {$wpdb->prefix}voxel_relations
				WHERE parent_id = %d AND relation_key = %s
				ORDER BY `order` ASC
			SQL, $this->post->get_id(), $this->get_relation_key() ) );
		} else {
			$rows = $wpdb->get_col( $wpdb->prepare( <<<SQL
				SELECT parent_id
				FROM {$wpdb->prefix}voxel_relations
				WHERE child_id = %d AND relation_key = %s
				ORDER BY `order` ASC
			SQL, $this->post->get_id(), $this->get_relation_key() ) );
		}

		$ids = array_map( 'absint', (array) $rows );

		$is_multiple = in_array( $this->props['relation_type'], [ 'has_many', 'belongs_to_many' ], true );
		if ( ! $is_multiple && ! empty( $ids ) ) {
			$ids = [ array_shift( $ids ) ];
		}

		return $ids;
	}

	protected function editing_value() {
		if ( ! $this->post ) {
			return null;
		}

		$ids = $this->get_value();
		if ( empty( $ids ) ) {
			return null;
		}

		return $ids;
	}

	protected function frontend_props() {
		$ids = $this->get_value();
		$selected = [];

		if ( ! empty( $ids ) ) {
			$posts = \Voxel\Post::query( [
				'post_type' => 'any',
				'post__in' => $ids,
				'posts_per_page' => -1,
			] );

			foreach ( $posts as $post ) {
				$selected[ $post->get_id() ] = [
					'id' => $post->get_id(),
					'title' => $post->get_title(),
					'logo' => $post->get_logo_markup(),
					'type' => $post->post_type->get_singular_name(),
					'icon' => \Voxel\get_icon_markup( $post->post_type->get_icon() ),
				];
			}
		}

		return [
			'multiple' => in_array( $this->props['relation_type'], [ 'has_many', 'belongs_to_many' ], true ),
			'relation_type' => $this->props['relation_type'],
			'post_types' => $this->props['post_types'],
			'placeholder' => $this->props['placeholder'] ?: $this->props['label'],
			'selected' => $selected,
		];
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
					'label' => 'Post ID',
					'type' => \Voxel\T_NUMBER,
					'callback' => function( $index ) {
						if ( isset( $GLOBALS['vx_preview_card_current_ids'] ) ) {
							$ids = \Voxel\prime_relations_cache( $GLOBALS['vx_preview_card_current_ids'], $this );
							_prime_post_caches( $ids );
						}

						$value = (array) $this->get_value();
						return $value[ $index ] ?? null;
					},
				],
			],
		];
	}
}
