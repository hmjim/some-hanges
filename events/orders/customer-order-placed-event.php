<?php

namespace Voxel\Events\Orders;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Customer_Order_Placed_Event extends \Voxel\Events\Base_Event {

	public $product_type;

	public $order, $customer, $vendor, $post;

	public function __construct( \Voxel\Product_Type $product_type = null ) {
		$this->product_type = $product_type;
	}

	public function prepare( $order_id ) {
		$order = \Voxel\Order::get( $order_id );
		if ( ! ( $order && $order->get_customer() && $order->get_vendor() && $order->get_post() ) ) {
			throw new \Exception( 'Missing information.' );
		}

		$this->order = $order;
		$this->customer = $order->get_customer();
		$this->vendor = $order->get_vendor();
		$this->post = $order->get_post();
	}

	public function get_key(): string {
		return $this->product_type
			? sprintf( 'orders/%s/customer:order_placed', $this->product_type->get_key() )
			: 'orders/customer:order_placed';
	}

	public function get_label(): string {
		return $this->product_type
			? sprintf( '%s: New order placed by customer', $this->product_type->get_label() )
			: 'Orders: New order placed by customer';
	}

	public function get_category() {
		return $this->product_type
			? sprintf( 'orders:%s', $this->product_type->get_key() )
			: 'orders';
	}

	public static function notifications(): array {
		return [
			'vendor' => [
				'label' => 'Notify vendor',
				'recipient' => function( $event ) {
					return $event->vendor;
				},
				'inapp' => [
					'enabled' => true,
					'subject' => '@customer(:display_name) placed an order on @post(:title).',
					'details' => function( $event ) {
						return [
							'order_id' => $event->order->get_id(),
						];
					},
					'apply_details' => function( $event, $details ) {
						$event->prepare( $details['order_id'] ?? null );
					},
					'links_to' => function( $event ) { return $event->order->get_link(); },
					'image_id' => function( $event ) { return $event->customer->get_avatar_id(); },
				],
				'email' => [
					'enabled' => false,
					'subject' => '@customer(:display_name) placed an order on @post(:title).',
					'message' => <<<HTML
					<strong>@customer(:display_name)</strong> placed an order on <strong>@post(:title)</strong>.
					<a href="@order(:link)">View order</a>
					HTML,
				],
			],
			'customer' => [
				'label' => 'Notify customer',
				'recipient' => function( $event ) {
					return $event->customer;
				},
				'inapp' => [
					'enabled' => false,
					'subject' => 'Your order on @post(:title) has been sent.',
					'details' => function( $event ) {
						return [
							'order_id' => $event->order->get_id(),
						];
					},
					'apply_details' => function( $event, $details ) {
						$event->prepare( $details['order_id'] ?? null );
					},
					'links_to' => function( $event ) { return $event->order->get_link(); },
					'image_id' => function( $event ) { return $event->post->get_logo_id(); },
				],
				'email' => [
					'enabled' => false,
					'subject' => 'Your order on @post(:title) has been sent.',
					'message' => <<<HTML
					Your order on <strong>@post(:title)</strong> has been sent.
					<a href="@order(:link)">View order</a>
					HTML,
				],
			],
			'admin' => [
				'label' => 'Notify admin',
				'recipient' => function( $event ) {
					return \Voxel\User::get( \Voxel\get( 'settings.notifications.admin_user' ) );
				},
				'inapp' => [
					'enabled' => false,
					'subject' => '@customer(:display_name) placed an order on @post(:title).',
					'details' => function( $event ) {
						return [
							'order_id' => $event->order->get_id(),
						];
					},
					'apply_details' => function( $event, $details ) {
						$event->prepare( $details['order_id'] ?? null );
					},
					'links_to' => function( $event ) { return $event->order->get_link(); },
					'image_id' => function( $event ) { return $event->customer->get_avatar_id(); },
				],
				'email' => [
					'enabled' => false,
					'subject' => '@customer(:display_name) placed an order on @post(:title).',
					'message' => <<<HTML
					<strong>@customer(:display_name)</strong> placed an order on <strong>@post(:title)</strong>.
					<a href="@order(:link)">View order</a>
					HTML,
				],
			],
		];
	}

	public function dynamic_tags(): array {
		return [
			'customer' => [
				'type' => \Voxel\Dynamic_Tags\User_Group::class,
				'props' => [
					'key' => 'customer',
					'label' => 'Customer',
					'user' => $this->customer,
				],
			],
			'vendor' => [
				'type' => \Voxel\Dynamic_Tags\User_Group::class,
				'props' => [
					'key' => 'vendor',
					'label' => 'Vendor',
					'user' => $this->vendor,
				],
			],
			'order' => [
				'type' => \Voxel\Dynamic_Tags\Order_Group::class,
				'props' => [
					'key' => 'order',
					'label' => 'Order',
					'order' => $this->order,
				],
			],
			'post' => [
				'type' => \Voxel\Dynamic_Tags\Post_Group::class,
				'props' => [
					'key' => 'post',
					'label' => 'Post',
					'post_type' => \Voxel\Post_Type::get('post'),
					'post' => $this->post ?? \Voxel\Post::dummy( [ 'post_type' => 'post' ] ),
				],
			],
		];
	}
}
