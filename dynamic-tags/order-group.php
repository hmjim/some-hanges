<?php

namespace Voxel\Dynamic_Tags;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Order_Group extends Base_Group {

	public $key = 'order';
	public $label = 'Order';

	public $order;

	protected function properties(): array {
		return [
			':id' => [
				'label' => 'ID',
				'type' => \Voxel\T_NUMBER,
				'callback' => function() {
					return $this->order->get_id();
				},
			],

			':created_at' => [
				'label' => 'Date created',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return $this->order->get_created_at();
				},
			],

			':link' => [
				'label' => 'Permalink',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return $this->order->get_link();
				},
			],
		];
	}
}
