<?php

namespace Voxel\Events\Orders;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Vendor_Files_Delivered_Event extends Base_Order_Event {

	static $default_enabled = [
		'customer' => true,
		'vendor' => false,
		'admin' => false,
	];

	public function get_key(): string {
		return $this->product_type
			? sprintf( 'orders/%s/vendor:files-delivered', $this->product_type->get_key() )
			: 'orders/vendor:files-delivered';
	}

	public function get_label(): string {
		return $this->product_type
			? sprintf( '%s: Vendor delivered files', $this->product_type->get_label() )
			: 'Orders: Vendor delivered files';
	}

	public static function get_customer_subject() {
		return 'Vendor delivered files on order #@order(:id).';
	}

	public static function get_customer_message() {
		return <<<HTML
		<p>Order #@order(:id): Vendor made new files available for download.</p>
		<em>@comment(:content)</em>
		<a href="@order(:link)">View downloads</a>
		HTML;
	}

	public static function get_vendor_subject() {
		return 'Order #@order(:id): You delivered files.';
	}

	public static function get_vendor_message() {
		return <<<HTML
		You have delivered files for download on order #@order(:id).
		<em>@comment(:content)</em>
		<a href="@order(:link)">View order</a>
		HTML;
	}

	public static function get_admin_subject() {
		return 'Order #@order(:id): Vendor delivered files.';
	}

	public static function get_admin_message() {
		return <<<HTML
		<p>Order #@order(:id): Vendor made new files available for download.</p>
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
