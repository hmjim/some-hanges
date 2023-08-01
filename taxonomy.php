<?php

namespace Voxel;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Taxonomy {
	use \Voxel\Taxonomies\Taxonomy_Singleton_Trait;

	private
		$config,
		$wp_taxonomy;

	public function __construct( \WP_Taxonomy $taxonomy ) {
		$this->wp_taxonomy = $taxonomy;
		$taxonomies = \Voxel\get( 'taxonomies', [] );
		$this->config = $taxonomies[ $this->get_key() ] ?? [];
	}

	public function get_key() {
		return $this->wp_taxonomy->name;
	}

	public function get_label() {
		return $this->wp_taxonomy->label;
	}

	public function get_singular_name() {
		return $this->wp_taxonomy->labels->singular_name;
	}

	public function get_plural_name() {
		return $this->get_label();
	}

	public function get_description() {
		return $this->wp_taxonomy->description;
	}

	public function get_post_types() {
		return (array) $this->wp_taxonomy->object_type;
	}

	public function get_edit_link() {
		return admin_url( 'admin.php?page=voxel-taxonomies&action=edit-taxonomy&taxonomy='.$this->get_key() );
	}

	public static function get_all() {
		return array_filter( array_map(
			'\Voxel\Taxonomy::get',
			get_taxonomies( [], 'objects' )
		) );
	}

	public static function get_voxel_taxonomies() {
		return array_filter( static::get_all(), function( $taxonomy ) {
			return $taxonomy->is_managed_by_voxel();
		} );
	}

	public static function get_other_taxonomies() {
		return array_filter( static::get_all(), function( $taxonomy ) {
			return ! $taxonomy->is_managed_by_voxel();
		} );
	}

	public function is_built_in() {
		return $this->wp_taxonomy->_builtin;
	}

	public function is_created_by_voxel() {
		return $this->wp_taxonomy->_is_created_by_voxel ?? false;
	}

	public function is_managed_by_voxel() {
		return ! empty( $this->config );
	}

	public function is_public() {
		return !! $this->wp_taxonomy->public;
	}

	public function is_publicly_queryable() {
		return !! $this->wp_taxonomy->publicly_queryable;
	}

	public function delete() {
		$taxonomies = \Voxel\get( 'taxonomies', [] );
		unset( $taxonomies[ $this->get_key() ] );
		\Voxel\set( 'taxonomies', $taxonomies );
	}

	public function update( $new_config ) {
		$taxonomies = \Voxel\get( 'taxonomies', [] );

		if ( isset( $new_config['settings'] ) ) {
			$this->config['settings'] = $new_config['settings'];
		}

		if ( isset( $new_config['templates'] ) ) {
			$this->config['templates'] = $new_config['templates'];
		}

		$taxonomies[ $this->get_key() ] = $this->config;

        // cleanup taxonomies array
        foreach ( $taxonomies as $key => $settings ) {
        	if ( ! is_string( $key ) || empty( $key ) || empty( $settings ) ) {
        		unset( $taxonomies[ $key ] );
        	}
        }

		\Voxel\set( 'taxonomies', $taxonomies );
	}

	public function get_config() {
		return $this->config;
	}

	/**
	 * Get taxonomy version (updated every time one of its terms changes),
	 * to be used for caching purposes.
	 *
	 * @since 1.0
	 */
	public function get_version() {
		$versions = \Voxel\get( 'taxonomy-versions' );
		return absint( $versions[ $this->get_key() ] ?? 0 );
	}

	public function update_version() {
		$versions = \Voxel\get( 'taxonomy-versions' );
		$versions[ $this->get_key() ] = time();

		// cleanup
		foreach ( $versions as $taxonomy_key => $taxonomy_version ) {
			if ( ! taxonomy_exists( $taxonomy_key ) ) {
				unset( $versions[ $taxonomy_key ] );
			}
		}

		\Voxel\set( 'taxonomy-versions', $versions );
	}

	public function get_templates( $create_if_not_exists = false ) {
		$templates = [
			'single' => null,
			'card' => null,
		];

		foreach ( (array) ( $this->config['templates'] ?? [] ) as $location => $template_id ) {
			if ( array_key_exists( $location, $templates ) && is_numeric( $template_id )  ) {
				$templates[ $location ] = absint( $template_id );
			}
		}

		if ( $create_if_not_exists ) {
			foreach ( $templates as $location => $template_id ) {
				if ( \Voxel\template_exists( absint( $template_id ) ) ) {
					continue;
				}

				$title = sprintf( 'taxonomy: %s | template: %s', $this->get_key(), $location );
				$new_template_id = \Voxel\create_template( $title );
				if ( ! is_wp_error( $new_template_id ) ) {
					$templates[ $location ] = absint( $new_template_id );
				}
			}

			$this->update( [
				'templates' => $templates,
			] );
		}

		return $templates;
	}
}
