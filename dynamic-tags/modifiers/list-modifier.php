<?php

namespace Voxel\Dynamic_Tags\Modifiers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class List_Modifier extends \Voxel\Dynamic_Tags\Base_Modifier {

	public function get_key(): string {
		return 'list';
	}

	public function get_label(): string {
		return _x( 'List', 'modifiers', 'voxel-backend' );
	}

	public function accepts(): string {
		return \Voxel\T_ANY;
	}

	public function get_arguments(): array {
		return [
			'separator' => [
				'type' => \Voxel\Form_Models\Text_Model::class,
				'label' => _x( 'Item separator', 'modifiers', 'voxel-backend' ),
			],
			'last_separator' => [
				'type' => \Voxel\Form_Models\Text_Model::class,
				'label' => _x( 'Last item separator', 'modifiers', 'voxel-backend' ),
			],
			'prefix' => [
				'type' => \Voxel\Form_Models\Text_Model::class,
				'label' => _x( 'Item prefix', 'modifiers', 'voxel-backend' ),
			],
			'suffix' => [
				'type' => \Voxel\Form_Models\Text_Model::class,
				'label' => _x( 'Item suffix', 'modifiers', 'voxel-backend' ),
			],
		];
	}

	public function apply( $value, $args, $group ) {
		if ( ! is_array( $value ) || empty( $value ) ) {
			return '';
		}

		$prefix = $args[2] ?? '';
		$suffix = $args[3] ?? '';
		if ( ! empty( $prefix ) || ! empty( $suffix ) ) {
			$value = array_map( function( $item ) use ( $prefix, $suffix ) {
				return $prefix.$item.$suffix;
			}, $value );
		}

		if ( count( $value ) === 1 ) {
			return array_shift( $value );
		}

		$last_item = array_pop( $value );
		$separator = $args[0] ?? ', ';
		$last_separator = $args[1] ?? $separator;

		return join( $separator, $value ) . $last_separator . $last_item;
	}

}
