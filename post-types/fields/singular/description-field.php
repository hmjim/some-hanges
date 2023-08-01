<?php

namespace Voxel\Post_Types\Fields\Singular;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Description_Field extends \Voxel\Post_Types\Fields\Texteditor_Field {

	protected $supported_conditions = ['text'];

	public function before_props_assigned(): void {
		$this->props['label'] = 'Description';
		$this->props['type'] = 'description';
		$this->props['key'] = 'description';
		$this->props['editor-type'] = 'wp-editor-basic';
		$this->props['singular'] = true;
	}

	public function update( $value ): void {
		global $wpdb;
		$wpdb->update( $wpdb->posts, [
			'post_content' => $value,
		], $where = [ 'ID' => $this->post->get_id() ] );
	}

	public function get_value() {
		return $this->post->get_content();
	}
}
