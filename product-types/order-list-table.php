<?php

namespace Voxel\Product_Types;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Order_List_Table extends \WP_List_Table {

	public function get_columns() {
		$columns = [
			// 'cb' => '<input type="checkbox">',
			'id' => _x( 'ID', 'orders table', 'voxel-backend' ),
			'customer' => _x( 'Customer', 'orders table', 'voxel-backend' ),
			'amount' => _x( 'Amount', 'orders table', 'voxel-backend' ),
			'post' => _x( 'Post', 'orders table', 'voxel-backend' ),
			'product' => _x( 'Product type', 'orders table', 'voxel-backend' ),
			'status' => _x( 'Status', 'orders table', 'voxel-backend' ),
			'vendor' => _x( 'Vendor', 'orders table', 'voxel-backend' ),
			'created_at' => _x( 'Created', 'orders table', 'voxel-backend' ),
			'details' => '',
		];

		return $columns;
	}

	protected function get_sortable_columns() {
		$sortable_columns = [
			'id' => [ 'created_at', 'asc' ],
			'created_at' => [ 'created_at', 'desc' ],
		];

		return $sortable_columns;
	}

	protected function get_primary_column_name() {
		return 'customer';
	}

	protected function column_default( $order, $column_name ) {
		$customer = $order->get_customer();
		$vendor = $order->get_vendor();
		$post = $order->get_post();

		if ( $column_name === 'id' ) {
			return sprintf( '%d', $order->get_id() );
		} elseif ( $column_name === 'product' ) {
			$product_type = $order->get_product_type();
			return $product_type ? sprintf( '<a href="%s">%s</a>',
				$product_type->get_edit_link(),
				$product_type->get_label()
			) : '&mdash;';
		} elseif ( $column_name === 'post' ) {
			return $post ? sprintf( '%s<span class="item-title"><a href="%s">%s</a></span>',
				$post->get_logo_markup(),
				get_edit_post_link( $post->get_id() ),
				$post->get_title()
			) : '&mdash;';
		} elseif ( $column_name === 'amount' ) {
			if ( $order->get_price()['amount'] ) {
				return sprintf( '<span class="price-amount">%s</span> %s', $order->get_price_for_display(), $order->get_price_period_for_display() );
			}

			return '&mdash;';
		} elseif ( $column_name === 'customer' ) {
			return $customer ? sprintf( '%s<span class="item-title"><a href="%s">%s</a></span>',
				$customer->get_avatar_markup(32),
				$customer->get_edit_link(),
				$customer->get_display_name()
			) : '&mdash;';
		} elseif ( $column_name === 'vendor' ) {
			return $vendor ? sprintf( '%s<span class="item-title"><a href="%s">%s</a></span>',
				$vendor->get_avatar_markup(24),
				$vendor->get_edit_link(),
				$vendor->get_display_name()
			) : '&mdash;';
		} elseif ( $column_name === 'status' ) {
			return sprintf( '<div class="status-%s">%s</div>', esc_attr( $order->get_status() ), $order->get_status_label() );
		} elseif ( $column_name === 'created_at' ) {
			if ( $timestamp = strtotime( $order->get_created_at() ) ) {
				return \Voxel\datetime_format( $timestamp );
			}

			return '&mdash;';
		} elseif ( $column_name === 'details' ) {
			return sprintf(
				'<a href="%s" class="button right">%s</a>',
				esc_url( admin_url( 'admin.php?page=voxel-orders&order_id='.$order->get_id() ) ),
				_x( 'Details', 'orders table', 'voxel-backend' )
			);
		}

		return null;
	}

	protected function get_views() {
		global $wpdb;

		$testmode = \Voxel\Stripe::is_test_mode() ? 'true' : 'false';
		$total_counts = $wpdb->get_results( <<<SQL
			SELECT status, COUNT(*) AS total
			FROM {$wpdb->prefix}voxel_orders
			WHERE testmode IS {$testmode}
			GROUP BY status
		SQL );

		$counts = [];
		$total_count = 0;

		foreach ( $total_counts as $count ) {
			$counts[ $count->status ] = absint( $count->total );
			$total_count += absint( $count->total );
		}

		$active = $_GET['status'] ?? null;
		$labels = \Voxel\Order::get_status_labels();

		$views['all'] = sprintf(
			'<a href="%s" class="%s">%s%s</a>',
			admin_url('admin.php?page=voxel-orders'),
			empty( $active ) ? 'current' : '',
			_x( 'All', 'orders table', 'voxel-backend' ),
			$total_count > 0 ? sprintf( ' <span class="count">(%s)</span>', number_format_i18n( $total_count ) ) : '',
		);

		foreach ( $labels as $status_key => $status_label ) {
			if ( isset( $counts[ $status_key ] ) ) {
				$views[ $status_key ] = sprintf(
					'<a href="%s" class="%s">%s%s</a>',
					admin_url( 'admin.php?page=voxel-orders&status='.$status_key ),
					$active === $status_key ? 'current' : '',
					$status_label,
					sprintf( ' <span class="count">(%s)</span>', number_format_i18n( $counts[ $status_key ] ) ),
				);
			}
		}

		return $views;
	}

	protected function extra_tablenav( $which ) {
		if ( $which !== 'top' ) {
			return;
		}

		$products = [];
		foreach ( \Voxel\Product_Type::get_all() as $product_type ) {
			$products[ $product_type->get_key() ] = [
				'label' => $product_type->get_label(),
				'fields' => [],
			];
		}

		foreach ( \Voxel\Post_Type::get_voxel_types() as $post_type ) {
			foreach ( $post_type->get_fields() as $field ) {
				if ( $field->get_type() === 'product' && isset( $products[ $field->get_prop('product-type') ] ) ) {
					$products[ $field->get_prop('product-type') ]['fields'][ $field->get_key() ] = $field->get_label();
				}
			}
		} ?>
		<select name="product" style="width: 180px;">
			<option value=""><?= _x( 'All product types', 'orders table', 'voxel-backend' ) ?></option>
			<?php foreach ( $products as $product_type_key => $product_type ): ?>
				<option value="<?= esc_attr( $product_type_key ) ?>" <?= $product_type_key === ( $_GET['product'] ?? null ) ? 'selected' : '' ?>><?= $product_type['label'] ?></option>
				<?php foreach ( $product_type['fields'] as $field_key => $field_label ): ?>
					<option value="<?= esc_attr( $product_type_key.'->'.$field_key ) ?>" <?= ( $product_type_key.'->'.$field_key ) === ( $_GET['product'] ?? null ) ? 'selected' : '' ?>>&mdash; <?= $field_label ?></option>
				<?php endforeach ?>
			<?php endforeach ?>
		</select>
		<input type="text" name="search_customer" placeholder="<?= esc_attr( _x( 'Search customer', 'orders table', 'voxel-backend' ) ) ?>" value="<?= esc_attr( $_GET['search_customer'] ?? '' ) ?>">
		<input type="text" name="search_post" placeholder="<?= esc_attr( _x( 'Search post', 'orders table', 'voxel-backend' ) ) ?>" value="<?= esc_attr( $_GET['search_post'] ?? '' ) ?>">
		<input type="text" name="search_author" placeholder="<?= esc_attr( _x( 'Search vendor', 'orders table', 'voxel-backend' ) ) ?>" value="<?= esc_attr( $_GET['search_author'] ?? '' ) ?>">
		<input type="submit" class="button" value="Filter">
		<?php
	}

	public function prepare_items() {
		global $wpdb;

		$page = $this->get_pagenum();
		$limit = 20;
		$offset = $limit * ( $page - 1 );
		$columns = $this->get_columns();
		$hidden = [];
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = [ $columns, $hidden, $sortable ];

		$product_type = null;
		$product_key = null;

		if ( ! empty( $_GET['product'] ) ) {
			$parts = explode( '->', $_GET['product'] );
			$product_type = $parts[0] ?? null;
			$product_key = $parts[1] ?? null;
		}

		$args = [
			'limit' => $limit,
			'offset' => $offset,
			'order' => ( $_GET['order'] ?? null ) === 'asc' ? 'asc' : 'desc',
			'status' => ! empty( $_GET['status'] ?? null ) ? $_GET['status'] : null,
			'search_customer' => ! empty( $_GET['search_customer'] ?? null ) ? $_GET['search_customer'] : null,
			'search_post' => ! empty( $_GET['search_post'] ?? null ) ? $_GET['search_post'] : null,
			'search_author' => ! empty( $_GET['search_author'] ?? null ) ? $_GET['search_author'] : null,
			'product_type' => $product_type,
			'product_key' => $product_key,
		];

		// dump($args);

		$results = \Voxel\Order::query( $args );
		$count = \Voxel\Order::query( $args + [ 'calculate_count' => true ] );

		// dd($results);

		$this->items = $results;
		$this->set_pagination_args( [
			'total_items' => $count,
			'per_page' => $limit,
			'total_pages' => ceil( $count / $limit ),
		] );
	}
}
