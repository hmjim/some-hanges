<?php

namespace Voxel\Events\Orders;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Customer_Refund_Request_Canceled_Event extends Base_Order_Event {

	static $default_enabled = [
		'customer' => false,
		'vendor' => true,
		'admin' => false,
	];

	public function get_key(): string {
		return $this->product_type
			? sprintf( 'orders/%s/customer:refund_request_canceled', $this->product_type->get_key() )
			: 'orders/customer:refund_request_canceled';
	}

	public function get_label(): string {
		return $this->product_type
			? sprintf( '%s: Refund request canceled by customer', $this->product_type->get_label() )
			: 'Orders: Refund request canceled by customer';
	}

	public static function get_customer_subject() {
		return 'Your refund request for order #@order(:id) has been canceled.';
	}

	public static function get_customer_message() {
		return <<<HTML
		Your refund request for order #@order(:id) has been canceled.
		<a href="@order(:link)">View order</a>
		HTML;
	}

	public static function get_vendor_subject() {
		return 'Order #@order(:id): Customer canceled their refund request.';
	}

	public static function get_vendor_message() {
		return <<<HTML
		Order #@order(:id): Customer canceled their refund request.
		<a href="@order(:link)">View order</a>
		HTML;
	}

	public static function get_admin_subject() {
		return 'Order #@order(:id): Customer canceled their refund request.';
	}

	public static function get_admin_message() {
		return <<<HTML
		Order #@order(:id): Customer canceled their refund request.
		<a href="@order(:link)">View order</a>
		HTML;
	}
}
