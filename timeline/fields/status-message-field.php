<?php

namespace Voxel\Timeline\Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Status_Message_Field extends \Voxel\Object_Fields\Base_Field {

	protected function base_props(): array {
		return [
			'key' => 'message',
			'maxlength' => \Voxel\get( 'settings.timeline.posts.maxlength', 5000 ),
		];
	}

	public function sanitize( $value ) {
		return sanitize_textarea_field( $value );
	}

	public function validate( $value ): void {
		$this->validate_maxlength( $value );
	}
}
