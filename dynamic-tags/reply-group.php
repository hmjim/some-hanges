<?php

namespace Voxel\Dynamic_Tags;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Reply_Group extends Base_Group {

	public $key = 'reply';
	public $label = 'Reply';

	public $reply;

	protected function properties(): array {
		return [
			':id' => [
				'label' => 'ID',
				'type' => \Voxel\T_NUMBER,
				'callback' => function() {
					return $this->reply->get_id();
				},
			],

			':content' => [
				'label' => 'Content',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return $this->reply->get_content_for_display();
				},
			],

			':created_at' => [
				'label' => 'Date created',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return $this->reply->get_created_at();
				},
			],

			':link' => [
				'label' => 'Permalink',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return $this->reply->get_link();
				},
			],
		];
	}
}
