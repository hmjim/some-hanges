<?php

namespace Voxel\Membership;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Type_Payment extends Base_Type {

	protected $type = 'payment';

	protected
		$payment_intent,
		$amount,
		$currency,
		$price_id,
		$status,
		$created;

	protected function init( array $config ) {
		$this->payment_intent = $config['payment_intent'] ?? null;
		$this->amount = $config['amount'] ?? null;
		$this->currency = $config['currency'] ?? null;
		$this->price_id = $config['price_id'] ?? null;
		$this->status = $config['status'] ?? null;
		$this->created = $config['created'] ?? null;
	}

	public function is_active() {
		return true;
	}

	public function get_payment_intent() {
		return $this->payment_intent;
	}

	public function get_price_id() {
		return $this->price_id;
	}

	public function get_status() {
		return $this->status;
	}

	public function get_amount() {
		return $this->amount;
	}

	public function get_currency() {
		return $this->currency;
	}

	public function get_created_at() {
		return $this->created;
	}
}
