<?php

namespace Voxel\Controllers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Product_Types_Controller extends Base_Controller {

	protected function hooks() {
		$this->on( 'admin_menu', '@add_menu_page' );
		$this->on( 'voxel/backend/product-types/screen:manage-types', '@render_manage_types_screen' );
		$this->on( 'voxel/backend/product-types/screen:create-type', '@render_create_type_screen' );
		$this->on( 'admin_post_voxel_create_product_type', '@create_product_type' );
	}

	protected function add_menu_page() {
		add_menu_page(
			__( 'Product Types', 'voxel-backend' ),
			__( 'Product Types', 'voxel-backend' ),
			'manage_options',
			'voxel-product-types',
			function() {
				$action_key = $_GET['action'] ?? 'manage-types';
				$allowed_actions = ['manage-types', 'create-type', 'edit-type'];
				$action = in_array( $action_key, $allowed_actions, true ) ? $action_key : 'manage-types';
				do_action( 'voxel/backend/product-types/screen:'.$action );
			},
			\Voxel\get_image('post-types/ic_prdct.png'),
			'0.282'
		);

		add_submenu_page(
			'voxel-product-types',
			__( 'Orders', 'voxel-backend' ),
			__( 'Orders', 'voxel-backend' ),
			'manage_options',
			'voxel-orders',
			function() {
				if ( ! empty( $_GET['order_id'] ) ) {
					$order = \Voxel\Order::get( $_GET['order_id'] );
					if ( ! $order ) {
						echo '<div class="wrap">'.__( 'Order not found.', 'voxel-backend' ).'</div>';
						return;
					}

					require locate_template( 'templates/backend/orders/edit-order.php' );
				} else {
					$table = new \Voxel\Product_Types\Order_List_Table;
					$table->prepare_items();
					require locate_template( 'templates/backend/orders/view-orders.php' );
				}
			},
			10
		);
	}

	protected function create_product_type() {
		check_admin_referer( 'voxel_manage_product_types' );
		if ( ! current_user_can( 'manage_options' ) ) {
			die;
		}

		if ( empty( $_POST['product_type'] ) || ! is_array( $_POST['product_type'] ) ) {
			die;
		}

		$key = sanitize_key( $_POST['product_type']['key'] ?? '' );
		$label = sanitize_text_field( $_POST['product_type']['label'] ?? '' );

		$product_types = \Voxel\get( 'product_types', [] );

		if ( $key && $label && ! isset( $product_types[ $key ] ) ) {
			$product_types[ $key ] = [
				'settings' => [
					'key' => $key,
					'label' => $label,
				],
				'fields' => [],
			];
		}

		\Voxel\set( 'product_types', $product_types );

		wp_safe_redirect( admin_url( 'admin.php?page=voxel-product-types&action=edit-type&product_type='.$key ) );
		exit;
	}

	protected function render_manage_types_screen() {
		$add_type_url = admin_url('admin.php?page=voxel-product-types&action=create-type');
		$product_types = \Voxel\Product_Type::get_all();

		require locate_template( 'templates/backend/product-types/view-product-types.php' );
	}

	protected function render_create_type_screen() {
		require locate_template( 'templates/backend/product-types/add-product-type.php' );
	}
}
