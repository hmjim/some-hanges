<?php

namespace Voxel\Dynamic_Tags;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Status_Group extends Base_Group {

	public $key = 'status';
	public $label = 'Status';

	public $status;

	protected function properties(): array {
		return [
			':id' => [
				'label' => 'ID',
				'type' => \Voxel\T_NUMBER,
				'callback' => function() {
					return $this->status->get_id();
				},
			],

			':content' => [
				'label' => 'Content',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return $this->status->get_content_for_display();
				},
			],

			':created_at' => [
				'label' => 'Date created',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return $this->status->get_created_at();
				},
			],

			':link' => [
				'label' => 'Permalink',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return $this->status->get_link();
				},
			],
		];
	}
}
