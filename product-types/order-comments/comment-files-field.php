<?php

namespace Voxel\Product_Types\Order_Comments;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Comment_Files_Field extends \Voxel\Object_Fields\Base_Field {
	use \Voxel\Object_Fields\File_Field_Trait;

	protected function base_props(): array {
		return [
			'key' => 'files',
			'label' => _x( 'Attach files', 'order comment files', 'voxel' ),
			'max-count' => 5,
			'max-size' => 2000,
			'allowed-types' => [
				'image/jpeg',
				'image/png',
				'image/webp',
			],
			'upload_dir' => null,
			'skip_subdir' => null,
		];
	}

	public function prepare_for_storage( $value ) {
		$file_ids = $this->_prepare_ids_from_sanitized_input( $value );
		return ! empty( $file_ids ) ? join( ',', $file_ids ) : null;
	}

	public function prepare_for_display( $value ) {
		$ids = explode( ',', (string) $value );
		$ids = array_filter( array_map( 'absint', $ids ) );

		$items = [];
		foreach ( $ids as $id ) {
			if ( $url = wp_get_attachment_url( $id ) ) {
				$items[] = [
					'name' => wp_basename( get_attached_file( $id ) ),
					'url' => $url,
				];
			}
		}

		if ( empty( $items ) ) {
			return null;
		}

		return $items;
	}
}
