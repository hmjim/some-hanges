<?php

namespace Voxel\Dynamic_Tags;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Post_Group extends Base_Group {

	public $key = 'post';
	public $label = 'Post';

	protected function properties(): array {
		return $this->_post_properties();
	}

	protected function methods(): array {
		return [
			'meta' => Methods\Post_Meta::class,
		];
	}

	public function get_id(): string {
		return sprintf( '%s:%s', $this->key, $this->post_type->get_key() );
	}
}
