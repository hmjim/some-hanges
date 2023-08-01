<?php

namespace Voxel\Membership;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Type_Subscription extends Base_Type {

	protected $type = 'subscription';

	protected
		$subscription_id,
		$price_id,
		$status,
		$trial_end,
		$current_period_end,
		$cancel_at_period_end,
		$amount,
		$currency,
		$interval,
		$interval_count,
		$created;

	protected function init( array $config ) {
		$this->subscription_id = $config['subscription_id'] ?? null;
		$this->price_id = $config['price_id'] ?? null;
		$this->status = $config['status'] ?? null;
		$this->trial_end = $config['trial_end'] ?? null;
		$this->current_period_end = $config['current_period_end'] ?? null;
		$this->cancel_at_period_end = $config['cancel_at_period_end'] ?? null;
		$this->amount = $config['amount'] ?? null;
		$this->currency = $config['currency'] ?? null;
		$this->interval = $config['interval'] ?? null;
		$this->interval_count = $config['interval_count'] ?? null;
		$this->created = $config['created'] ?? null;
	}

	public function is_active() {
		return in_array( $this->status, [ 'trialing', 'active' ], true );
	}

	public function get_subscription_id() {
		return $this->subscription_id;
	}

	public function get_price_id() {
		return $this->price_id;
	}

	public function get_status() {
		return $this->status;
	}

	public function get_trial_end() {
		return $this->trial_end;
	}

	public function get_amount() {
		return $this->amount;
	}

	public function get_currency() {
		return $this->currency;
	}

	public function get_interval() {
		return $this->interval;
	}

	public function get_interval_count() {
		return $this->interval_count;
	}

	public function get_current_period_end() {
		return $this->current_period_end;
	}

	public function will_cancel_at_period_end() {
		return $this->cancel_at_period_end;
	}

	public function is_switchable() {
		return ! in_array( $this->get_status(), [ 'canceled', 'incomplete_expired' ], true );
	}

	public function get_created_at() {
		return $this->created;
	}
}
