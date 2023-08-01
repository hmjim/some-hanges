<?php

namespace Voxel\Dynamic_Tags;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Order_Comment_Group extends Base_Group {

	public $key = 'comment';
	public $label = 'Order comment';

	public $note;

	protected function properties(): array {
		return [
			':id' => [
				'label' => 'ID',
				'type' => \Voxel\T_NUMBER,
				'callback' => function() {
					return $this->note->get_id();
				},
			],

			':content' => [
				'label' => 'Content',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return $this->note->get_comment_message_for_display();
				},
			],

			':created_at' => [
				'label' => 'Date created',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return $this->note->get_created_at();
				},
			],

			':link' => [
				'label' => 'Permalink',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return $this->note->get_link();
				},
			],
		];
	}
}
