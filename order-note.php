<?php

namespace Voxel;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Order_Note {

	const COMMENT = 'comment';
	const CHECKOUT_CANCELED = 'customer.checkout_canceled';
	const PAYMENT_AUTHORIZED = 'customer.payment_authorized';
	const QR_TAG_APPLIED = 'qr_tag_applied';

	const AUTHOR_APPROVED = 'author.approved';
	const AUTHOR_DECLINED = 'author.declined';
	const AUTHOR_REFUND_APPROVED = 'author.refund_approved';
	const AUTHOR_REFUND_DECLINED = 'author.refund_declined';
	const AUTHOR_APPLIED_TAG = 'author.applied_tag';
	const AUTHOR_DELIVERED = 'author.delivered';

	const CUSTOMER_CANCELED = 'customer.canceled';
	const CUSTOMER_REFUND_REQUESTED = 'customer.refund_requested';
	const CUSTOMER_REFUND_REQUEST_CANCELED = 'customer.refund_request_canceled';
	const CUSTOMER_APPLIED_TAG = 'customer.applied_tag';

	private
		$id,
		$order_id,
		$type,
		$details,
		$created_at;

	private static $instances = [];

	public static function get( $id ) {
		if ( is_array( $id ) ) {
			$data = $id;
			$id = $data['id'];
			if ( ! array_key_exists( $id, self::$instances ) ) {
				self::$instances[ $id ] = new \Voxel\Order_Note( $data );
			}
		} elseif ( is_numeric( $id ) ) {
			if ( ! array_key_exists( $id, self::$instances ) ) {
				$results = self::query( [ 'id' => $id, 'limit' => 1 ] );
				self::$instances[ $id ] = isset( $results[0] ) ? $results[0] : null;
			}
		}

		return self::$instances[ $id ];
	}

	public function get_id() {
		return $this->id;
	}

	public function get_order_id() {
		return $this->order_id;
	}

	public function get_order() {
		return \Voxel\Order::get( $this->order_id );
	}

	public function get_product_type() {
		$order = $this->get_order();
		return $order ? $order->get_product_type() : null;
	}

	public function get_type() {
		return $this->type;
	}

	public function get_details() {
		return (array) $this->details;
	}

	public function get_created_at() {
		return $this->created_at;
	}

	public function get_user_id() {
		return $this->get_details()['user_id'] ?? null;
	}

	public function get_user() {
		return \Voxel\User::get( $this->get_user_id() );
	}

	public static function create( array $data ): \Voxel\Order_Note {
		global $wpdb;
		$data = array_merge( [
			'order_id' => null,
			'type' => null,
			'details' => null,
			'created_at' => \Voxel\utc()->format( 'Y-m-d H:i:s' ),
		], $data );

		if ( is_null( $data['order_id'] ) || is_null( $data['type'] ) ) {
			throw new \Exception( _x( 'Couldn\'t create note: missing data.', 'orders', 'voxel' ) );
		}

		$escaped_data = [];
		$escaped_data[ 'order_id' ] = absint( $data['order_id'] );
		$escaped_data[ 'type' ] = sprintf( '\'%s\'', esc_sql( $data[ 'type' ] ) );
		$escaped_data[ 'created_at' ] = sprintf( '\'%s\'', esc_sql( $data[ 'created_at' ] ) );

		if ( ! is_null( $data['details'] ) ) {
			if ( is_array( $data['details'] ) ) {
				$data['details'] = wp_json_encode( $data['details'] );
			}

			$escaped_data[ 'details' ] = sprintf( '\'%s\'', esc_sql( $data[ 'details' ] ) );
		}

		$columns = join( ', ', array_map( function( $column_name ) {
			return sprintf( '`%s`', esc_sql( $column_name ) );
		}, array_keys( $escaped_data ) ) );

		$values = join( ', ', $escaped_data );

		$on_duplicate = join( ', ', array_map( function( $column_name ) {
			return sprintf( '`%s`=VALUES(`%s`)', $column_name, $column_name );
		}, array_keys( $escaped_data ) ) );

		$sql = "INSERT INTO {$wpdb->prefix}voxel_order_notes ($columns) VALUES ($values)
					ON DUPLICATE KEY UPDATE $on_duplicate";

		$wpdb->query( $sql );
		$data['id'] = $wpdb->insert_id;

		$note = new \Voxel\Order_Note( $data );

		$product_type = $note->get_product_type();

		switch ( $note->get_type() ) {
			case static::PAYMENT_AUTHORIZED:
				( new \Voxel\Events\Orders\Customer_Payment_Authorized_Event )->dispatch( $note->get_id() );
				$product_type && ( new \Voxel\Events\Orders\Customer_Payment_Authorized_Event( $product_type ) )->dispatch( $note->get_id() );
				break;
			case static::AUTHOR_APPROVED:
				( new \Voxel\Events\Orders\Vendor_Order_Approved_Event )->dispatch( $note->get_id() );
				$product_type && ( new \Voxel\Events\Orders\Vendor_Order_Approved_Event( $product_type ) )->dispatch( $note->get_id() );
				break;
			case static::AUTHOR_DECLINED:
				( new \Voxel\Events\Orders\Vendor_Order_Declined_Event )->dispatch( $note->get_id() );
				$product_type && ( new \Voxel\Events\Orders\Vendor_Order_Declined_Event( $product_type ) )->dispatch( $note->get_id() );
				break;
			case static::AUTHOR_REFUND_APPROVED:
				( new \Voxel\Events\Orders\Vendor_Refund_Approved_Event )->dispatch( $note->get_id() );
				$product_type && ( new \Voxel\Events\Orders\Vendor_Refund_Approved_Event( $product_type ) )->dispatch( $note->get_id() );
				break;
			case static::AUTHOR_REFUND_DECLINED:
				( new \Voxel\Events\Orders\Vendor_Refund_Declined_Event )->dispatch( $note->get_id() );
				$product_type && ( new \Voxel\Events\Orders\Vendor_Refund_Declined_Event( $product_type ) )->dispatch( $note->get_id() );
				break;
			case static::CUSTOMER_CANCELED:
				( new \Voxel\Events\Orders\Customer_Order_Canceled_Event )->dispatch( $note->get_id() );
				$product_type && ( new \Voxel\Events\Orders\Customer_Order_Canceled_Event( $product_type ) )->dispatch( $note->get_id() );
				break;
			case static::CUSTOMER_REFUND_REQUESTED:
				( new \Voxel\Events\Orders\Customer_Refund_Requested_Event )->dispatch( $note->get_id() );
				$product_type && ( new \Voxel\Events\Orders\Customer_Refund_Requested_Event( $product_type ) )->dispatch( $note->get_id() );
				break;
			case static::CUSTOMER_REFUND_REQUEST_CANCELED:
				( new \Voxel\Events\Orders\Customer_Refund_Request_Canceled_Event )->dispatch( $note->get_id() );
				$product_type && ( new \Voxel\Events\Orders\Customer_Refund_Request_Canceled_Event( $product_type ) )->dispatch( $note->get_id() );
				break;
		}

		if ( $note->get_type() === static::COMMENT ) {
			$order = $note->get_order();
			$vendor_id = $order ? $order->get_vendor_id() : null;
			$customer_id = $order ? $order->get_customer_id() : null;
			$details = (array) $note->get_details();
			$comment_author = \Voxel\User::get( $details['user_id'] ?? null );
			if ( $comment_author ) {
				if ( $comment_author->get_id() === (int) $customer_id ) {
					( new \Voxel\Events\Orders\Customer_Commented_Event )->dispatch( $note->get_id() );
					$product_type && ( new \Voxel\Events\Orders\Customer_Commented_Event( $product_type ) )->dispatch( $note->get_id() );
				} elseif ( $comment_author->get_id() === (int) $vendor_id ) {
					( new \Voxel\Events\Orders\Vendor_Commented_Event )->dispatch( $note->get_id() );
					$product_type && ( new \Voxel\Events\Orders\Vendor_Commented_Event( $product_type ) )->dispatch( $note->get_id() );
				}
			}
		}

		if ( $note->get_type() === static::AUTHOR_DELIVERED ) {
			( new \Voxel\Events\Orders\Vendor_Files_Delivered_Event )->dispatch( $note->get_id() );
			$product_type && ( new \Voxel\Events\Orders\Vendor_Files_Delivered_Event( $product_type ) )->dispatch( $note->get_id() );
		}

		return $note;
	}

	public function delete() {
		global $wpdb;
		$wpdb->query( $wpdb->prepare(
			"DELETE FROM {$wpdb->prefix}voxel_order_notes WHERE id = %d",
			$this->get_id()
		) );
	}

	public static function query( array $args ): array {
		global $wpdb;
		$args = array_merge( [
			'id' => null,
			'order_id' => null,
			'type' => null,
			'offset' => null,
			'limit' => null,
		], $args );

		$where_clauses = [];
		$join_posts = false;

		if ( ! is_null( $args['id'] ) ) {
			$where_clauses[] = sprintf( 'notes.id = %d', absint( $args['id'] ) );
		}

		if ( ! is_null( $args['order_id'] ) ) {
			$where_clauses[] = sprintf( 'notes.order_id = %d', absint( $args['order_id'] ) );
		}

		if ( ! is_null( $args['type'] ) ) {
			$where_clauses[] = sprintf( 'notes.type = \'%s\'', esc_sql( $args['type'] ) );
		}

		// generate sql string
		$wheres = '';
		if ( ! empty( $where_clauses ) ) {
			$wheres = sprintf( 'WHERE %s', join( ' AND ', $where_clauses ) );
		}

		$limit = '';
		if ( ! is_null( $args['limit'] ) ) {
			$limit = sprintf( 'LIMIT %d', absint( $args['limit'] ) );
		}

		$offset = '';
		if ( ! is_null( $args['offset'] ) ) {
			$offset = sprintf( 'OFFSET %d', absint( $args['offset'] ) );
		}

		$sql = "
			SELECT notes.* FROM {$wpdb->prefix}voxel_order_notes AS notes
			{$wheres} ORDER BY notes.created_at ASC {$limit} {$offset}
		";

		// dump_sql( $sql );die;
		$results = $wpdb->get_results( $sql, ARRAY_A );
		if ( ! is_array( $results ) ) {
			return [];
		}

		return array_map( '\Voxel\Order_Note::get', $results );
	}

	public static function find( array $args ) {
		$args['limit'] = 1;
		$args['offset'] = null;
		$results = static::query( $args );
		return array_shift( $results );
	}

	public function __construct( array $data ) {
		$this->id = absint( $data['id'] );
		$this->order_id = absint( $data['order_id'] );
		$this->type = $data['type'];
		$this->created_at = $data['created_at'];
		$this->details = (array) json_decode( $data['details'] ?? '', ARRAY_A );
	}

	public function get_comment_message() {
		$details = (array) $this->details;
		return $details['message'] ?? null;
	}

	public function get_comment_message_for_display() {
		$content = $this->get_comment_message();
		if ( $content === null ) {
			return null;
		}

		$content = links_add_target( make_clickable( $content ) );
		$content = wpautop( $content );
		return $content;
	}

	public function prepare() {
		$details = (array) $this->details;
		if ( $this->type === static::COMMENT ) {
			$user = \Voxel\User::get( $details['user_id'] ?? null );
			$file_field = new \Voxel\Product_Types\Order_Comments\Comment_Files_Field;

			return [
				'type' => 'comment',
				'author' => [
					'name' => $user ? $user->get_display_name() : _x( '(deleted account)', 'deleted user account', 'voxel' ),
					'avatar' => $user ? $user->get_avatar_markup() : null,
					'link' => $user ? $user->get_link() : null,
				],
				'time' => $this->get_time_for_display(),
				'message' => $this->get_comment_message_for_display(),
				'files' => $file_field->prepare_for_display( $details['files'] ?? '' ),
			];
		} elseif ( $this->type === static::AUTHOR_DELIVERED ) {
			$user = \Voxel\User::get( $details['user_id'] ?? null );
			$file_field = new \Voxel\Product_Types\Order_Comments\Comment_Deliverables_Field;

			return [
				'type' => 'author.delivered',
				'author' => [
					'name' => $user ? $user->get_display_name() : _x( '(deleted account)', 'deleted user account', 'voxel' ),
					'avatar' => $user ? $user->get_avatar_markup() : null,
					'link' => $user ? $user->get_link() : null,
				],
				'time' => $this->get_time_for_display(),
				'message' => $this->get_comment_message_for_display(),
				'files' => $file_field->prepare_for_display( $details['deliverables'] ?? '', $this->get_order_id(), $this->get_id() ),
			];
		} else {
			$messages = [
				static::CHECKOUT_CANCELED => _x( 'Checkout canceled by user.', 'orders', 'voxel' ),
				static::PAYMENT_AUTHORIZED => _x( 'Funds have been authorized and the order is awaiting approval by the vendor.', 'orders', 'voxel' ),
				static::AUTHOR_APPROVED => _x( 'Order has been approved', 'orders', 'voxel' ),
				static::AUTHOR_DECLINED => _x( 'Order has been declined', 'orders', 'voxel' ),
				static::AUTHOR_REFUND_APPROVED => _x( 'Refund request approved by vendor.', 'orders', 'voxel' ),
				static::AUTHOR_REFUND_DECLINED => _x( 'Refund request declined by vendor.', 'orders', 'voxel' ),
				static::CUSTOMER_CANCELED => _x( 'Order canceled by customer.', 'orders', 'voxel' ),
				static::CUSTOMER_REFUND_REQUESTED => _x( 'Customer requested a refund.', 'orders', 'voxel' ),
				static::CUSTOMER_REFUND_REQUEST_CANCELED => _x( 'Customer canceled their refund request.', 'orders', 'voxel' ),
			];

			$callables = [
				static::AUTHOR_APPLIED_TAG => function() {
					$tag_key = ( (array) $this->details )['tag'] ?? null;
					$tag = $this->get_order()->get_product_type()->get_tag( $tag_key );
					return $tag
						? sprintf( _x( 'Vendor applied the tag "%s"', 'orders', 'voxel' ), $tag->get_label() )
						: _x( 'Vendor changed the order tag.', 'orders', 'voxel' );
				},
				static::CUSTOMER_APPLIED_TAG => function() {
					$tag_key = ( (array) $this->details )['tag'] ?? null;
					$tag = $this->get_order()->get_product_type()->get_tag( $tag_key );
					return $tag
						? sprintf( _x( 'Customer applied the tag "%s"', 'orders', 'voxel' ), $tag->get_label() )
						: _x( 'Customer changed the order tag.', 'orders', 'voxel' );
				},
				static::QR_TAG_APPLIED => function() {
					$tag_key = ( (array) $this->details )['tag'] ?? null;
					$tag = $this->get_order()->get_product_type()->get_tag( $tag_key );
					return $tag
						? sprintf( _x( 'Order tag set to "%s" via QR code.', 'orders', 'voxel' ), $tag->get_label() )
						: _x( 'Order tag changed via QR code.', 'orders', 'voxel' );
				},
			];

			if ( isset( $messages[ $this->type ] ) ) {
				$message = $messages[ $this->type ];
			} elseif ( isset( $callables[ $this->type ] ) ) {
				$message = $callables[ $this->type ]();
			} else {
				$message = $this->type;
			}

			return [
				'type' => 'system',
				'icon' => \Voxel\get_icon_markup( 'la-solid:las la-robot' ),
				'time' => $this->get_time_for_display(),
				'message' => $message,
			];
		}
	}

	public function get_time_for_display() {
		$from = strtotime( $this->created_at ) + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
		$to = current_time( 'timestamp' );
		$diff = (int) abs( $to - $from );
		if ( $diff < WEEK_IN_SECONDS ) {
			return sprintf( _x( '%s ago', 'order note created at', 'voxel' ), human_time_diff( $from, $to ) );
		}

		return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $from );
	}

}
