<?php

namespace Voxel\Events;

if ( ! defined('ABSPATH') ) {
	exit;
}

abstract class Base_Event {

	private static $all;

	public $recipient;

	abstract public function get_key(): string;

	abstract public function get_label(): string;

	public function dynamic_tags(): array {
		return [];
	}

	public static function notifications(): array {
		return [];
	}

	public static function webhooks(): array {
		return [];
	}

	public function dispatch() {
		try {
			$this->prepare( ...func_get_args() );
			$this->send_notifications();
			$this->send_webhooks();
		} catch ( \Exception $e ) {
			\Voxel\log($e);
		}
	}

	public function get_dynamic_tags(): array {
		$tags = [];
		$tags['recipient'] = [
			'type' => \Voxel\Dynamic_Tags\User_Group::class,
			'props' => [
				'key' => 'recipient',
				'label' => 'Recipient',
				'user' => $this->recipient ?? \Voxel\User::dummy(),
			],
		];

		$tags += $this->dynamic_tags();

		$tags['admin'] = [
			'type' => \Voxel\Dynamic_Tags\User_Group::class,
			'props' => [
				'key' => 'admin',
				'label' => 'Admin',
				'user' => \Voxel\User::dummy(), // @todo
			],
		];

		$tags['site'] = [
			'type' => \Voxel\Dynamic_Tags\Site_Group::class,
		];

		return $tags;
	}


	public function get_notifications(): array {
		$notifications = static::notifications();
		$events = (array) \Voxel\get( 'events', [] );

		foreach ( $notifications as $destination => $notification ) {
			$notification['inapp']['default_subject'] = $notification['inapp']['subject'];
			$notification['email']['default_subject'] = $notification['email']['subject'];
			$notification['email']['default_message'] = $notification['email']['message'];

			$notification['inapp']['subject'] = null;
			$notification['email']['subject'] = null;
			$notification['email']['message'] = null;

			$config = (array) ( $events[ $this->get_key() ]['notifications'] ?? [] );
			if ( isset( $config[ $destination ] ) ) {
				$notification['inapp']['enabled'] = $config[ $destination ]['inapp']['enabled'];
				$notification['inapp']['subject'] = $config[ $destination ]['inapp']['subject'];
				$notification['email']['enabled'] = $config[ $destination ]['email']['enabled'];
				$notification['email']['subject'] = $config[ $destination ]['email']['subject'];
				$notification['email']['message'] = $config[ $destination ]['email']['message'];
			}

			$notifications[ $destination ] = $notification;
		}

		return $notifications;
	}

	public function get_editor_config(): array {
		return [
			'key' => $this->get_key(),
			'label' => $this->get_label(),
			'category' => $this->get_category(),
			'notifications' => array_map( function( $n ) {
				return [
					'label' => $n['label'],
					'inapp' => [
						'enabled' => $n['inapp']['enabled'],
						'subject' => $n['inapp']['subject'],
						'default_subject' => $n['inapp']['default_subject'],
					],
					'email' => [
						'enabled' => $n['email']['enabled'],
						'subject' => $n['email']['subject'],
						'default_subject' => $n['email']['default_subject'],
						'message' => $n['email']['message'],
						'default_message' => $n['email']['default_message'],
					],
				];
			}, $this->get_notifications() ),
		];
	}

	public function send_notifications(): void {
		$emails = [];

		foreach ( $this->get_notifications() as $destination => $notification ) {
			$recipient = $notification['recipient']( $this );
			if ( ! $recipient instanceof \Voxel\User ) {
				continue;
			}

			$this->recipient = $recipient;

			if ( $notification['inapp']['enabled'] ) {
				\Voxel\Notification::create( [
					'user_id' => $recipient->get_id(),
					'type' => $this->get_key(),
					'details' => array_merge(
						$notification['inapp']['details']( $this ),
						[ 'destination' => $destination ]
					),
				] );

				$recipient->update_notification_count();
			}

			if ( $notification['email']['enabled'] ) {
				$subject = \Voxel\render(
					$notification['email']['subject'] ?: $notification['email']['default_subject'],
					$this->get_dynamic_tags()
				);
				$message = \Voxel\render(
					$notification['email']['message'] ?: $notification['email']['default_message'],
					$this->get_dynamic_tags()
				);

				$emails[] = [
					'recipient' => $recipient->get_email(),
					'subject' => $subject,
					'message' => $message,
					'headers' => [
						'Content-type: text/html;',
					],
				];
			}
		}

		if ( ! empty( $emails ) ) {
			\Voxel\Queues\Async_Email::instance()->data( [ 'emails' => $emails ] )->dispatch();
		}

		do_action( sprintf( 'voxel/app-events/%s', $this->get_key() ), $this );
	}

	public function send_webhooks(): void {
		//
	}

	public static function get_categories(): array {
		$categories = [
			'orders' => [
				'key' => 'orders',
				'label' => 'Orders',
				'expanded' => true,
				'children' => [
					'orders' => [
						'key' => 'orders',
						'label' => 'General',
					],
				],
			],
			'timeline' => [
				'key' => 'timeline',
				'label' => 'Timeline',
			],
		];

		foreach ( \Voxel\Product_Type::get_all() as $product_type ) {
			$categories['orders']['children'][ sprintf( 'orders:%s', $product_type->get_key() ) ] = [
				'key' => sprintf( 'orders:%s', $product_type->get_key() ),
				'label' => $product_type->get_label(),
			];
		}

		foreach ( \Voxel\Post_Type::get_voxel_types() as $post_type ) {
			$categories[ sprintf( 'post-type:%s', $post_type->get_key() ) ] = [
				'key' => sprintf( 'post-type:%s', $post_type->get_key() ),
				'label' => $post_type->get_label(),
			];
		}

		$categories['membership'] = [
			'key' => 'membership',
			'label' => 'Membership',
		];

		$categories['messages'] = [
			'key' => 'messages',
			'label' => 'Direct Messages',
		];

		return $categories;
	}

	public static function get_all(): array {
		if ( ! is_null( static::$all ) ) {
			return static::$all;
		}

		$events = [
			'timeline/comment:created' => new \Voxel\Events\Comment_Created_Event,
			'timeline/comment-reply:created' => new \Voxel\Events\Comment_Reply_Created_Event,

			'orders/customer:order_placed' => new \Voxel\Events\Orders\Customer_Order_Placed_Event,
			'orders/customer:commented' => new \Voxel\Events\Orders\Customer_Commented_Event,
			'orders/customer:order_canceled' => new \Voxel\Events\Orders\Customer_Order_Canceled_Event,
			'orders/customer:payment_authorized' => new \Voxel\Events\Orders\Customer_Payment_Authorized_Event,
			'orders/customer:refund_requested' => new \Voxel\Events\Orders\Customer_Refund_Requested_Event,
			'orders/customer:refund_request_canceled' => new \Voxel\Events\Orders\Customer_Refund_Request_Canceled_Event,

			'orders/vendor:order_approved' => new \Voxel\Events\Orders\Vendor_Order_Approved_Event,
			'orders/vendor:order_declined' => new \Voxel\Events\Orders\Vendor_Order_Declined_Event,
			'orders/vendor:refund_approved' => new \Voxel\Events\Orders\Vendor_Refund_Approved_Event,
			'orders/vendor:refund_declined' => new \Voxel\Events\Orders\Vendor_Refund_Declined_Event,
			'orders/vendor:commented' => new \Voxel\Events\Orders\Vendor_Commented_Event,
			'orders/vendor:files-delivered' => new \Voxel\Events\Orders\Vendor_Files_Delivered_Event,

			'membership/user:registered' => new \Voxel\Events\Membership\User_Registered_Event,
			'membership/user:confirmed' => new \Voxel\Events\Membership\User_Confirmed_Event,
			'membership/plan:activated' => new \Voxel\Events\Membership\Plan_Activated_Event,
			'membership/plan:switched' => new \Voxel\Events\Membership\Plan_Switched_Event,

			'messages/user:received_message' => new \Voxel\Events\Direct_Messages\User_Received_Message_Event,
		];

		foreach ( \Voxel\Product_Type::get_all() as $product_type ) {
			$event = new \Voxel\Events\Orders\Customer_Order_Placed_Event( $product_type );
			$events[ $event->get_key() ] = $event;

			$event = new \Voxel\Events\Orders\Customer_Commented_Event( $product_type );
			$events[ $event->get_key() ] = $event;

			$event = new \Voxel\Events\Orders\Customer_Order_Canceled_Event( $product_type );
			$events[ $event->get_key() ] = $event;

			if ( ! $product_type->is_catalog_mode() ) {
				$event = new \Voxel\Events\Orders\Customer_Payment_Authorized_Event( $product_type );
				$events[ $event->get_key() ] = $event;
			}

			if ( ! ( $product_type->is_catalog_mode() && ! $product_type->catalog_refunds_allowed() ) ) {
				$event = new \Voxel\Events\Orders\Customer_Refund_Requested_Event( $product_type );
				$events[ $event->get_key() ] = $event;

				$event = new \Voxel\Events\Orders\Customer_Refund_Request_Canceled_Event( $product_type );
				$events[ $event->get_key() ] = $event;
			}

			if ( ! ( $product_type->is_catalog_mode() && ! $product_type->catalog_requires_approval() ) ) {
				$event = new \Voxel\Events\Orders\Vendor_Order_Approved_Event( $product_type );
				$events[ $event->get_key() ] = $event;

				$event = new \Voxel\Events\Orders\Vendor_Order_Declined_Event( $product_type );
				$events[ $event->get_key() ] = $event;
			}

			if ( ! ( $product_type->is_catalog_mode() && ! $product_type->catalog_refunds_allowed() ) ) {
				$event = new \Voxel\Events\Orders\Vendor_Refund_Approved_Event( $product_type );
				$events[ $event->get_key() ] = $event;

				$event = new \Voxel\Events\Orders\Vendor_Refund_Declined_Event( $product_type );
				$events[ $event->get_key() ] = $event;
			}

			$event = new \Voxel\Events\Orders\Vendor_Commented_Event( $product_type );
			$events[ $event->get_key() ] = $event;

			if ( $product_type->config( 'settings.deliverables.enabled' ) ) {
				$event = new \Voxel\Events\Orders\Vendor_Files_Delivered_Event( $product_type );
				$events[ $event->get_key() ] = $event;
			}
		}

		foreach ( \Voxel\Post_Type::get_voxel_types() as $post_type ) {
			// Post_Submitted_Event
			if ( $post_type->get_setting( 'submissions.enabled' ) ) {
				$event = new \Voxel\Events\Post_Submitted_Event( $post_type );
				$events[ $event->get_key() ] = $event;
			}

			// Post_Updated_Event
			if ( $post_type->get_setting( 'submissions.update_status' ) !== 'disabled' ) {
				$event = new \Voxel\Events\Post_Updated_Event( $post_type );
				$events[ $event->get_key() ] = $event;
			}

			// Post_Approved_Event
			if ( $post_type->get_setting( 'submissions.update_status' ) !== 'pending' ) {
				$event = new \Voxel\Events\Post_Approved_Event( $post_type );
				$events[ $event->get_key() ] = $event;
			}

			// Post_Rejected_Event
			if ( $post_type->get_setting( 'submissions.enabled' ) ) {
				$event = new \Voxel\Events\Post_Rejected_Event( $post_type );
				$events[ $event->get_key() ] = $event;
			}

			// Review_Created_Event
			if ( $post_type->get_setting( 'timeline.reviews' ) !== 'disabled' ) {
				$event = new \Voxel\Events\Review_Created_Event( $post_type );
				$events[ $event->get_key() ] = $event;
			}

			// Wall_Post_Created_Event
			if ( $post_type->get_setting( 'timeline.wall' ) !== 'disabled' ) {
				$event = new \Voxel\Events\Wall_Post_Created_Event( $post_type );
				$events[ $event->get_key() ] = $event;
			}

			// Timeline_Status_Created_Event
			if ( $post_type->get_setting( 'timeline.enabled' ) ) {
				$event = new \Voxel\Events\Timeline_Status_Created_Event( $post_type );
				$events[ $event->get_key() ] = $event;
			}
		}

		static::$all = $events;
		return static::$all;
	}
}
