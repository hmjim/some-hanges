<?php

namespace Voxel\Events\Membership;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Plan_Activated_Event extends \Voxel\Events\Base_Event {

	public $user;

	public function prepare( $user_id ) {
		$user = \Voxel\User::get( $user_id );
		if ( ! $user ) {
			throw new \Exception( 'User not found.' );
		}

		$this->user = $user;
	}

	public function get_key(): string {
		return 'membership/plan:activated';
	}

	public function get_label(): string {
		return 'Membership: Plan activated';
	}

	public function get_category() {
		return 'membership';
	}

	public static function notifications(): array {
		return [
			'user' => [
				'label' => 'Notify user',
				'recipient' => function( $event ) {
					return $event->user;
				},
				'inapp' => [
					'enabled' => false,
					'subject' => 'You have been assigned the @user(:plan.label) plan',
					'details' => function( $event ) {
						return [
							'user_id' => $event->user->get_id(),
						];
					},
					'apply_details' => function( $event, $details ) {
						$event->prepare( $details['user_id'] ?? null );
					},
					'links_to' => function( $event ) { return get_permalink( \Voxel\get( 'templates.current_plan' ) ) ?: home_url('/'); },
				],
				'email' => [
					'enabled' => false,
					'subject' => 'You have been assigned the @user(:plan.label) plan',
					'message' => <<<HTML
					Your selected plan <strong>@user(:plan.label)</strong> has been assigned and activated.<br>
					Pricing: @user(:plan.pricing.amount).currency_format(,true) @user(:plan.pricing.period)
					<a href="@site(current_plan_url)">Open</a>
					HTML,
				],
			],
			'admin' => [
				'label' => 'Notify admin',
				'recipient' => function( $event ) {
					return \Voxel\User::get( \Voxel\get( 'settings.notifications.admin_user' ) );
				},
				'inapp' => [
					'enabled' => true,
					'subject' => '@user(:display_name) activated @user(:plan.label) plan: @user(:plan.pricing.amount).currency_format(,true) @user(:plan.pricing.period)',
					'details' => function( $event ) {
						return [
							'user_id' => $event->user->get_id(),
						];
					},
					'apply_details' => function( $event, $details ) {
						$event->prepare( $details['user_id'] ?? null );
					},
					'links_to' => function( $event ) { return $event->user->get_link(); },
					'image_id' => function( $event ) { return $event->user->get_avatar_id(); },
				],
				'email' => [
					'enabled' => false,
					'subject' => '@user(:display_name) activated @user(:plan.label) plan: @user(:plan.pricing.amount).currency_format(,true) @user(:plan.pricing.period)',
					'message' => <<<HTML
					<strong>@user(:display_name)</strong> activated <strong>@user(:plan.label)</strong> plan.<br>
					Pricing: @user(:plan.pricing.amount).currency_format(,true) @user(:plan.pricing.period)
					<a href="@user(:profile_url)">Open</a>
					HTML,
				],
			],
		];
	}

	public function dynamic_tags(): array {
		return [
			'user' => [
				'type' => \Voxel\Dynamic_Tags\User_Group::class,
				'props' => [
					'key' => 'user',
					'label' => 'User',
					'user' => $this->user,
				],
			],
		];
	}
}
