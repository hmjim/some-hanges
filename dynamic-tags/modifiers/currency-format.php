<?php

namespace Voxel\Dynamic_Tags\Modifiers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Currency_Format extends \Voxel\Dynamic_Tags\Base_Modifier {

	public function get_key(): string {
		return 'currency_format';
	}

	public function get_label(): string {
		return _x( 'Currency Format', 'modifiers', 'voxel-backend' );
	}

	public function accepts(): string {
		return \Voxel\T_NUMBER;
	}

	public function get_arguments(): array {
		return [
			'currency' => [
				'type' => \Voxel\Form_Models\Select_Model::class,
				'label' => _x( 'Currency', 'modifiers', 'voxel-backend' ),
				'choices' => [ 'default' => 'Default platform currency' ] + \Voxel\Stripe\Currencies::all(),
			],
			'amount_is_in_cents' => [
				'type' => \Voxel\Form_Models\Switcher_Model::class,
				'label' => _x( 'Amount is in cents', 'modifiers', 'voxel-backend' ),
			],
		];
	}

	public function apply( $value, $args, $group ) {
		if ( ! is_numeric( $value ) ) {
			return $value;
		}

		if ( empty( $args[0] ) || $args[0] === 'default' ) {
			$args[0] = \Voxel\get( 'settings.stripe.currency' );
		}

		return \Voxel\currency_format( $value, $args[0], $args[1] ?? false );
	}
}
