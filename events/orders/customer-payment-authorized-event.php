<?php

namespace Voxel\Events\Orders;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Customer_Payment_Authorized_Event extends Base_Order_Event {

	static $default_enabled = [
		'customer' => false,
		'vendor' => true,
		'admin' => false,
	];

	public function get_key(): string {
		return $this->product_type
			? sprintf( 'orders/%s/customer:payment_authorized', $this->product_type->get_key() )
			: 'orders/customer:payment_authorized';
	}

	public function get_label(): string {
		return $this->product_type
			? sprintf( '%s: Payment authorized by customer', $this->product_type->get_label() )
			: 'Orders: Payment authorized by customer';
	}

	public static function get_customer_subject() {
		return 'Order #@order(:id): Your funds have been authorized and the order is awaiting approval by the vendor.';
	}

	public static function get_customer_message() {
		return <<<HTML
		Order #@order(:id): Funds have been authorized and the order is awaiting approval by the vendor.
		<a href="@order(:link)">View order</a>
		HTML;
	}

	public static function get_vendor_subject() {
		return 'Order #@order(:id): Customer funds have been authorized and the order is awaiting approval.';
	}

	public static function get_vendor_message() {
		return <<<HTML
		Order #@order(:id): Customer funds have been authorized and the order is awaiting approval.
		<a href="@order(:link)">View order</a>
		HTML;
	}

	public static function get_admin_subject() {
		return 'Order #@order(:id): Customer funds have been authorized and the order is awaiting approval by the vendor.';
	}

	public static function get_admin_message() {
		return <<<HTML
		Order #@order(:id): Customer funds have been authorized and the order is awaiting approval by the vendor.
		<a href="@order(:link)">View order</a>
		HTML;
	}
}
