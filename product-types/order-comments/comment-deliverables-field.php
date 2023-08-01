<?php

namespace Voxel\Product_Types\Order_Comments;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Comment_Deliverables_Field extends \Voxel\Object_Fields\Base_Field {
	use \Voxel\Object_Fields\File_Field_Trait;

	protected function base_props(): array {
		return [
			'key' => 'deliverables',
			'label' => _x( 'Attach files', 'order deliverables', 'voxel' ),
			'max-count' => 5,
			'max-size' => 2000,
			'allowed-types' => [
				'image/jpeg',
				'image/png',
				'image/webp',
			],
			'private_upload' => true,
		];
	}

	public function prepare_for_storage( $value ) {
		$file_ids = $this->_prepare_ids_from_sanitized_input( $value );
		return ! empty( $file_ids ) ? join( ',', $file_ids ) : null;
	}

	public function prepare_for_display( $value, $order_id, $note_id ) {
		$order = \Voxel\Order::get( $order_id );
		$download_limit = $order->get_product_type()->config( 'settings.deliverables.download_limit' );
		$download_counts = $order->get_details()['download_counts'] ?? [];

		$ids = explode( ',', (string) $value );
		$ids = array_filter( array_map( 'absint', $ids ) );

		$items = [];
		foreach ( $ids as $id ) {
			if ( $url = wp_get_attachment_url( $id ) ) {
				$display_filename = get_post_meta( $id, '_display_filename', true );
				$items[] = [
					'name' => ! empty( $display_filename ) ? $display_filename : wp_basename( get_attached_file( $id ) ),
					'url' => add_query_arg( [
						'action' => 'orders.download_deliverable',
						'order_id' => $order_id,
						'note_id' => $note_id,
						'file_id' => $id,
					], home_url('/?vx=1') ),
					'limit' => is_numeric( $download_limit ) && $download_limit >= 1 ? (int) $download_limit : null,
					'count' => absint( $download_counts[ $id ] ?? 0 ),
					'downloadable' => in_array( $order->get_status(), [ 'completed', 'sub_active', 'refund_requested' ], true ),
				];
			}
		}

		if ( empty( $items ) ) {
			return null;
		}

		return $items;
	}
}
