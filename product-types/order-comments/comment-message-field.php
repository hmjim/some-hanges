<?php

namespace Voxel\Product_Types\Order_Comments;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Comment_Message_Field extends \Voxel\Object_Fields\Base_Field {

	protected function base_props(): array {
		return [
			'key' => 'message',
		];
	}

	public function sanitize( $value ) {
		return sanitize_textarea_field( $value );
	}

	public function validate( $value ): void {
		//
	}
}
