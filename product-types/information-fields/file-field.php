<?php

namespace Voxel\Product_Types\Information_Fields;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class File_Field extends Base_Information_Field {
	use \Voxel\Object_Fields\File_Field_Trait;

	protected $props = [
		'type' => 'file',
		'max-count' => 1,
		'max-size' => 2000,
		'allowed-types' => [],
	];

	public function prepare_for_storage( $value ) {
		$file_ids = $this->_prepare_ids_from_sanitized_input( $value );
		return ! empty( $file_ids ) ? join( ',', $file_ids ) : null;
	}

	public function prepare_for_display( $value ) {
		$ids = explode( ',', (string) $value );
		$ids = array_filter( array_map( 'absint', $ids ) );

		$items = [];
		foreach ( $ids as $id ) {
			$url = wp_get_attachment_url( $id );
			if ( ! $url ) {
				continue;
			}

			$name = wp_basename( get_attached_file( $id ) );
			$items[] = [
				'link' => esc_url( $url ),
				'name' => esc_html( $name ),
			];
		}

		if ( empty( $items ) ) {
			return null;
		}

		return $items;
	}
}
