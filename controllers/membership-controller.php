<?php

namespace Voxel\Controllers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Membership_Controller extends Base_Controller {

	protected function hooks() {
		$this->on( 'admin_menu', '@add_menu_page' );
		$this->on( 'admin_post_voxel_create_membership_plan', '@create_plan' );
	}

	protected function add_menu_page() {
		add_menu_page(
			__( 'Plans', 'voxel-backend' ),
			__( 'Plans', 'voxel-backend' ),
			'manage_options',
			'voxel-membership',
			function() {
				$action = sanitize_text_field( $_GET['action'] ?? 'manage-types' );

				if ( $action === 'create-plan' ) {
					require locate_template( 'templates/backend/membership/create-plan.php' );
				} elseif ( $action === 'edit-plan' ) {
					$plan = \Voxel\Membership\Plan::get( $_GET['plan'] ?? '' );
					if ( ! $plan ) {
						return;
					}

					$post_types = [];
					foreach ( \Voxel\Post_Type::get_voxel_types() as $post_type ) {
						$post_types[ $post_type->get_key() ] = $post_type->get_label();
					}

					$config = [
						'plan' => $plan->get_editor_config(),
						'postTypes' => $post_types,
					];

					wp_enqueue_script( 'vx:membership-editor.js' );
					require locate_template( 'templates/backend/membership/edit-plan.php' );
				} else {
					$default_plan = \Voxel\Membership\Plan::get_or_create_default_plan();
					$active_plans = \Voxel\Membership\Plan::active();
					$archived_plans = \Voxel\Membership\Plan::archived();
					$add_plan_url = admin_url('admin.php?page=voxel-membership&action=create-plan');

					require locate_template( 'templates/backend/membership/view-plans.php' );
				}
			},
			\Voxel\get_image('post-types/ic_mbr.png'),
			'0.291'
		);

		add_submenu_page(
			'voxel-membership',
			__( 'Customers', 'voxel-backend' ),
			__( 'Customers', 'voxel-backend' ),
			'manage_options',
			'voxel-customers',
			function() {
				if ( ! empty( $_GET['customer'] ) ) {
					$user = \Voxel\User::get( $_GET['customer'] );
					if ( ! $user ) {
						echo '<div class="wrap">'.__( 'Customer not found.', 'voxel-backend' ).'</div>';
						return;
					}

					$membership = $user->get_membership();
					$stripe_base_url = \Voxel\Stripe::is_test_mode() ? 'https://dashboard.stripe.com/test/' : 'https://dashboard.stripe.com/';
					require locate_template( 'templates/backend/membership/customer-details.php' );
				} else {
					$table = new \Voxel\Membership\Customer_List_Table;
					$table->prepare_items();
					require locate_template( 'templates/backend/membership/customers.php' );
				}
			},
			10
		);
	}

	protected function create_plan() {
		check_admin_referer( 'voxel_manage_membership_plans' );
		if ( ! current_user_can( 'manage_options' ) ) {
			die;
		}

		if ( empty( $_POST['membership_plan'] ) || ! is_array( $_POST['membership_plan'] ) ) {
			die;
		}

		$key = sanitize_key( $_POST['membership_plan']['key'] ?? '' );
		$label = sanitize_text_field( $_POST['membership_plan']['label'] ?? '' );
		$description = sanitize_textarea_field( $_POST['membership_plan']['description'] ?? '' );

		try {
			$plan = \Voxel\Membership\Plan::create( [
				'key' => $key,
				'label' => $label,
				'description' => $description,
			] );
		} catch ( \Exception $e ) {
			wp_die( $e->getMessage() );
		}

		wp_safe_redirect( admin_url( 'admin.php?page=voxel-membership' ) );
		exit;
	}
}
