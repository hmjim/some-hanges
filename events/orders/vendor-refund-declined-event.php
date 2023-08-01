<?php

namespace Voxel\Events\Orders;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Vendor_Refund_Declined_Event extends Base_Order_Event {

	static $default_enabled = [
		'customer' => true,
		'vendor' => false,
		'admin' => false,
	];

	public function get_key(): string {
		return $this->product_type
			? sprintf( 'orders/%s/vendor:refund_declined', $this->product_type->get_key() )
			: 'orders/vendor:refund_declined';
	}

	public function get_label(): string {
		return $this->product_type
			? sprintf( '%s: Refund request declined by vendor', $this->product_type->get_label() )
			: 'Orders: Refund request declined by vendor';
	}

	public static function get_customer_subject() {
		return 'Order #@order(:id): Your refund request has been declined by the vendor.';
	}

	public static function get_customer_message() {
		return <<<HTML
		Order #@order(:id): Your refund request has been declined by the vendor.
		<a href="@order(:link)">View order</a>
		HTML;
	}

	public static function get_vendor_subject() {
		return 'Order #@order(:id): Refund request has been declined.';
	}

	public static function get_vendor_message() {
		return <<<HTML
		Order #@order(:id): Refund request has been declined.
		<a href="@order(:link)">View order</a>
		HTML;
	}

	public static function get_admin_subject() {
		return 'Order #@order(:id): Refund request has been declined by the vendor.';
	}

	public static function get_admin_message() {
		return <<<HTML
		Order #@order(:id): Refund request has been declined by the vendor.
		<a href="@order(:link)">View order</a>
		HTML;
	}
}
