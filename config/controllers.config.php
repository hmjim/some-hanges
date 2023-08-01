<?php

namespace Voxel;

if ( ! defined('ABSPATH') ) {
	exit;
}

return [
	\Voxel\Controllers\Ajax_Controller::class,
	\Voxel\Controllers\Assets_Controller::class,
	\Voxel\Controllers\Collections_Controller::class,
	\Voxel\Controllers\Cron_Controller::class,
	\Voxel\Controllers\Db_Controller::class,
	\Voxel\Controllers\Event_Controller::class,
	\Voxel\Controllers\Membership_Controller::class,
	\Voxel\Controllers\Nav_Menus_Controller::class,
	\Voxel\Controllers\Post_Controller::class,
	\Voxel\Controllers\Post_Type_Controller::class,
	\Voxel\Controllers\Post_Types_Controller::class,
	\Voxel\Controllers\Privacy_Controller::class,
	\Voxel\Controllers\Product_Type_Controller::class,
	\Voxel\Controllers\Product_Types_Controller::class,
	\Voxel\Controllers\Settings_Controller::class,
	\Voxel\Controllers\Stripe_Controller::class,
	\Voxel\Controllers\Taxonomies_Controller::class,
	\Voxel\Controllers\Taxonomy_Controller::class,
	\Voxel\Controllers\Templates_Controller::class,
	\Voxel\Controllers\Term_Controller::class,
	\Voxel\Controllers\Term_Order_Controller::class,
	\Voxel\Controllers\User_Controller::class,

	// frontend
	\Voxel\Controllers\Frontend\Auth\Auth_Controller::class,
	\Voxel\Controllers\Frontend\Auth\Google_Controller::class,
	\Voxel\Controllers\Frontend\Membership\Checkout_Controller::class,
	\Voxel\Controllers\Frontend\Membership\One_Time_Payment_Controller::class,
	\Voxel\Controllers\Frontend\Membership\Subscriptions_Controller::class,
	\Voxel\Controllers\Frontend\Create_Post_Controller::class,
	\Voxel\Controllers\Frontend\Inbox_Controller::class,
	\Voxel\Controllers\Frontend\Media_Library_Controller::class,
	\Voxel\Controllers\Frontend\Notification_Controller::class,
	\Voxel\Controllers\Frontend\Search_Controller::class,
	\Voxel\Controllers\Frontend\Share_Controller::class,
	\Voxel\Controllers\Frontend\Tabs_Controller::class,
	\Voxel\Controllers\Frontend\User_Controller::class,

	// timeline
	\Voxel\Controllers\Frontend\Timeline\Reply_Controller::class,
	\Voxel\Controllers\Frontend\Timeline\Status_Controller::class,
	\Voxel\Controllers\Frontend\Timeline\Timeline_Controller::class,

	// orders
	\Voxel\Controllers\Frontend\Orders\Checkout_Controller::class,
	\Voxel\Controllers\Frontend\Orders\Order_Actions_Controller::class,
	\Voxel\Controllers\Frontend\Orders\Orders_Controller::class,
	\Voxel\Controllers\Frontend\Orders\Reservations_Controller::class,
	\Voxel\Controllers\Frontend\Orders\Single_Order_Controller::class,

	// async actions
	\Voxel\Controllers\Async\Create_Taxonomy_Action::class,
	\Voxel\Controllers\Async\General_Actions::class,
	\Voxel\Controllers\Async\Index_Posts_Action::class,
	\Voxel\Controllers\Async\Membership_Actions::class,
	\Voxel\Controllers\Async\List_Tax_Rates_Action::class,
	\Voxel\Controllers\Async\List_Shipping_Rates_Action::class,

	// elementor
	\Voxel\Controllers\Elementor\Container_Controller::class,
	\Voxel\Controllers\Elementor\Document_Controller::class,
	\Voxel\Controllers\Elementor\Elementor_Controller::class,
	\Voxel\Controllers\Elementor\Loop_Controller::class,
	\Voxel\Controllers\Elementor\Widget_Controller::class,
	\Voxel\Controllers\Elementor\Visibility_Controller::class,

	// onboarding
	\Voxel\Controllers\Onboarding\Onboarding_Controller::class,
	\Voxel\Controllers\Onboarding\Demo_Import_Controller::class,

	// library
	\Voxel\Controllers\Library\Export_Controller::class,
	\Voxel\Controllers\Library\Import_Controller::class,
	\Voxel\Controllers\Library\Library_Controller::class,
];
