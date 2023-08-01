<?php

namespace Voxel\Events\Orders;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Customer_Refund_Requested_Event extends Base_Order_Event {

	static $default_enabled = [
		'customer' => false,
		'vendor' => true,
		'admin' => false,
	];

	public function get_key(): string {
		return $this->product_type
			? sprintf( 'orders/%s/customer:refund_requested', $this->product_type->get_key() )
			: 'orders/customer:refund_requested';
	}

	public function get_label(): string {
		return $this->product_type
			? sprintf( '%s: Refund requested by customer', $this->product_type->get_label() )
			: 'Orders: Refund requested by customer';
	}

	public static function get_customer_subject() {
		return 'You have requested a refund for order #@order(:id).';
	}

	public static function get_customer_message() {
		return <<<HTML
		You have requested a refund for order #@order(:id).
		<a href="@order(:link)">View order</a>
		HTML;
	}

	public static function get_vendor_subject() {
		return 'Order #@order(:id): Refund requested by customer.';
	}

	public static function get_vendor_message() {
		return <<<HTML
		Order #@order(:id): Refund requested by customer.
		<a href="@order(:link)">View order</a>
		HTML;
	}

	public static function get_admin_subject() {
		return 'Order #@order(:id): Refund requested by customer.';
	}

	public static function get_admin_message() {
		return <<<HTML
		Order #@order(:id): Refund requested by customer.
		<a href="@order(:link)">View order</a>
		HTML;
	}
}
