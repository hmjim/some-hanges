<?php

namespace Voxel\Events\Orders;

if ( ! defined('ABSPATH') ) {
	exit;
}

abstract class Base_Order_Event extends \Voxel\Events\Base_Event {

	static $default_enabled = [
		'customer' => false,
		'vendor' => false,
		'admin' => false,
	];

	public $product_type;

	public $note, $order, $customer, $vendor, $post;

	public function __construct( \Voxel\Product_Type $product_type = null ) {
		$this->product_type = $product_type;
	}

	public function prepare( $note_id ) {
		$note = \Voxel\Order_Note::get( $note_id );
		if ( ! ( $note && $note->get_order() ) ) {
			throw new \Exception( 'Missing information.' );
		}

		$order = $note->get_order();
		if ( ! ( $order->get_customer() && $order->get_vendor() && $order->get_post() ) ) {
			throw new \Exception( 'Missing information.' );
		}

		$this->note = $note;
		$this->order = $order;
		$this->customer = $order->get_customer();
		$this->vendor = $order->get_vendor();
		$this->post = $order->get_post();
	}

	public function get_category() {
		return $this->product_type
			? sprintf( 'orders:%s', $this->product_type->get_key() )
			: 'orders';
	}

	abstract public static function get_customer_subject();
	abstract public static function get_customer_message();

	abstract public static function get_vendor_subject();
	abstract public static function get_vendor_message();

	abstract public static function get_admin_subject();
	abstract public static function get_admin_message();

	public static function notifications(): array {
		return [
			'customer' => [
				'label' => 'Notify customer',
				'recipient' => function( $event ) {
					return $event->customer;
				},
				'inapp' => [
					'enabled' => static::$default_enabled['customer'],
					'subject' => static::get_customer_subject(),
					'details' => function( $event ) {
						return [
							'note_id' => $event->note->get_id(),
						];
					},
					'apply_details' => function( $event, $details ) {
						$event->prepare( $details['note_id'] ?? null );
					},
					'links_to' => function( $event ) { return $event->order->get_link(); },
					'image_id' => function( $event ) { return $event->post->get_logo_id(); },
				],
				'email' => [
					'enabled' => false,
					'subject' => static::get_customer_subject(),
					'message' => static::get_customer_message(),
				],
			],
			'vendor' => [
				'label' => 'Notify vendor',
				'recipient' => function( $event ) {
					return $event->vendor;
				},
				'inapp' => [
					'enabled' => static::$default_enabled['vendor'],
					'subject' => static::get_vendor_subject(),
					'details' => function( $event ) {
						return [
							'note_id' => $event->note->get_id(),
						];
					},
					'apply_details' => function( $event, $details ) {
						$event->prepare( $details['note_id'] ?? null );
					},
					'links_to' => function( $event ) { return $event->order->get_link(); },
					'image_id' => function( $event ) { return $event->post->get_logo_id(); },
				],
				'email' => [
					'enabled' => false,
					'subject' => static::get_vendor_subject(),
					'message' => static::get_vendor_message(),
				],
			],
			'admin' => [
				'label' => 'Notify admin',
				'recipient' => function( $event ) {
					return \Voxel\User::get( \Voxel\get( 'settings.notifications.admin_user' ) );
				},
				'inapp' => [
					'enabled' => static::$default_enabled['admin'],
					'subject' => static::get_admin_subject(),
					'details' => function( $event ) {
						return [
							'note_id' => $event->note->get_id(),
						];
					},
					'apply_details' => function( $event, $details ) {
						$event->prepare( $details['note_id'] ?? null );
					},
					'links_to' => function( $event ) { return $event->order->get_link(); },
					'image_id' => function( $event ) { return $event->post->get_logo_id(); },
				],
				'email' => [
					'enabled' => false,
					'subject' => static::get_admin_subject(),
					'message' => static::get_admin_message(),
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
		];
	}
}
