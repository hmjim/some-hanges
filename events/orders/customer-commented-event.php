<?php

namespace Voxel\Events\Orders;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Customer_Commented_Event extends Base_Order_Event {

	static $default_enabled = [
		'customer' => false,
		'vendor' => true,
		'admin' => false,
	];

	public function get_key(): string {
		return $this->product_type
			? sprintf( 'orders/%s/customer:commented', $this->product_type->get_key() )
			: 'orders/customer:commented';
	}

	public function get_label(): string {
		return $this->product_type
			? sprintf( '%s: Customer posted a comment', $this->product_type->get_label() )
			: 'Orders: Customer posted a comment';
	}

	public static function get_customer_subject() {
		return 'You posted a comment on order #@order(:id).';
	}

	public static function get_customer_message() {
		return <<<HTML
		You have posted a comment on order #@order(:id).
		<a href="@order(:link)">View order</a>
		HTML;
	}

	public static function get_vendor_subject() {
		return 'Order #@order(:id): Customer posted a comment.';
	}

	public static function get_vendor_message() {
		return <<<HTML
		<p>Order #@order(:id): Customer posted a comment:</p>
		<em>@comment(:content)</em>
		<a href="@order(:link)">View order</a>
		HTML;
	}

	public static function get_admin_subject() {
		return 'Order #@order(:id): Customer posted a comment.';
	}

	public static function get_admin_message() {
		return <<<HTML
		<p>Order #@order(:id): Customer posted a comment:</p>
		<em>@comment(:content)</em>
		<a href="@order(:link)">View order</a>
		HTML;
	}

	public function dynamic_tags(): array {
		$tags = parent::dynamic_tags();
		$tags['comment'] = [
				'type' => \Voxel\Dynamic_Tags\Order_Comment_Group::class,
				'props' => [
					'key' => 'comment',
					'label' => 'Comment',
					'note' => $this->note,
				],
		];

		return $tags;
	}
}
