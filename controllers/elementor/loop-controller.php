<?php

namespace Voxel\Controllers\Elementor;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Loop_Controller extends \Voxel\Controllers\Base_Controller {

	protected $looped_elements = [];

	protected function hooks() {
		$this->on( 'elementor/element/common/_section_style/after_section_end', '@register_loop_settings', 100 );
		$this->on( 'elementor/element/section/section_advanced/after_section_end', '@register_loop_settings', 100 );
		$this->on( 'elementor/element/column/section_advanced/after_section_end', '@register_loop_settings', 100 );
		$this->on( 'elementor/element/container/section_layout/after_section_end', '@register_loop_settings', 100 );

		$this->on( 'elementor/controls/controls_registered', '@add_repeater_loop_setting', 1005 );

		foreach ( [ 'widget', 'column', 'section', 'container' ] as $element_type ) {
			$this->on( sprintf( 'elementor/frontend/%s/before_render', $element_type ), '@run_loops', 100 );
			$this->on( sprintf( 'elementor/frontend/%s/should_render', $element_type ), '@should_render', 100, 2 );
		}
	}

	protected function register_loop_settings( $element ) {
		$element->start_controls_section( '_voxel_loop_settings', [
			'label' => __( 'Loop element', 'voxel-backend' ),
			'tab' => 'tab_voxel',
		] );

		$element->add_control( '_voxel_loop', [
			'label' => __( 'Loop this element based on', 'voxel-backend' ),
			'label_block' => true,
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => static::_get_loopable_properties(),
		] );

		$element->add_control( '_voxel_loop_limit', [
			'label' => __( 'Loop limit', 'voxel-backend' ),
			'description' => __( 'If a hard limit is set, the loop will stop there even if there are additional items left', 'voxel-backend' ),
			'type' => \Elementor\Controls_Manager::NUMBER,
			'min' => 0,
			'default' => '',
			'classes' => 'hide-dtag-button',
		] );

		$element->end_controls_section();
	}

	protected function add_repeater_loop_setting( $controls_manager ) {
		if ( $loop_options = static::_get_loopable_properties() ) {
			$repeater = $controls_manager->get_control('repeater');
			$fields = $repeater->get_settings('fields');
			$fields[ '_voxel_loop' ] = [
				'name' => '_voxel_loop',
				'type' => 'select',
				'label' => __( 'Loop repeater row', 'voxel-backend' ),
				'default' => '',
				'options' => $loop_options,
			];
			$fields[ '_voxel_loop_limit' ] = [
				'name' => '_voxel_loop_limit',
				'type' => 'number',
				'label' => __( 'Loop limit', 'voxel-backend' ),
				'description' => __( 'If a hard limit is set, the loop will stop there even if there are additional items left', 'voxel-backend' ),
				'default' => '',
				'min' => 0,
				'classes' => 'hide-dtag-button',
			];
			$repeater->set_settings( 'fields', $fields );
		}
	}

	public function run_loops( $element ) {
		$loopable = $element->get_settings('_voxel_loop');
		if ( empty( $loopable ) ) {
			return;
		}

		if ( \Voxel\is_importing_elementor_template() ) {
			return;
		}

		$hard_limit = $element->get_settings('_voxel_loop_limit');
		$hard_limit = is_numeric( $hard_limit ) ? absint( $hard_limit ) : null;

		if ( \Voxel\Dynamic_Tags\Loop::is_running( $loopable ) ) {
			unset( $this->looped_elements[ $element->get_id() ] );
			return;
		}

		( \Closure::bind( function( $element ) {
			$element->children = [];
		}, null, \Elementor\Element_Base::class ) )( $element );

		\Voxel\Dynamic_Tags\Loop::run( $loopable, function() use ( $element ) {
			$classname = get_class( $element );
			$loop_element = new $classname( $element->get_data(), [] );
			$loop_element->print_element();
		}, $hard_limit );

		$this->looped_elements[ $element->get_id() ] = true;
	}

	protected function should_render( $should_render, $element ) {
		if ( isset( $this->looped_elements[ $element->get_id() ] ) ) {
			return false;
		}

		return $should_render;
	}

	private static function _get_loopable_properties() {
		static $loopables;
		if ( ! is_null( $loopables ) ) {
			return $loopables;
		}

		$loopables = [
			'' => __( 'No loop', 'voxel-backend' ),
		];

		$groups = \Voxel\config('dynamic_tags.groups');
		foreach ( $groups as $group_class ) {
			$group = new $group_class;
			static::_find_loopables( $group->get_properties(), $loopables, 0, $group->get_key() );
		}

		if ( count( $loopables ) === 1 ) {
			$loopables = [
				'' => __( 'No loopable properties available', 'voxel-backend' ),
			];
		}

		return $loopables;
	}

	private static function _find_loopables( $properties, &$loopables, $depth, $group_key, $path = [] ) {
		foreach ( $properties as $key => $property ) {
			if ( ! empty( $property['loopable'] ) || ! empty( $property['has_loopable_props'] ) ) {
				$path_string = ! empty( $path ) ? join( '.', $path ).'.' : '';
				$tag = sprintf( '@%s(%s%s)', $group_key, $path_string, $key );
				$loopables[ $tag ] = str_repeat( ' - ', $depth ).$property['label'];
			}

			if ( isset( $property['properties'] ) ) {
				$_path = $path;
				$_path[] = $key;
				static::_find_loopables(
					$property['properties'], $loopables, $depth + 1, $group_key, $_path
				);
			}
		}
	}
}
