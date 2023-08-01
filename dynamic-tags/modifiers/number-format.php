<?php

namespace Voxel\Dynamic_Tags\Modifiers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Number_Format extends \Voxel\Dynamic_Tags\Base_Modifier {

	public function get_key(): string {
		return 'number_format';
	}

	public function get_label(): string {
		return _x( 'Number Format', 'modifiers', 'voxel-backend' );
	}

	public function accepts(): string {
		return \Voxel\T_NUMBER;
	}

	public function get_arguments(): array {
		return [
			'decimals' => [
				'type' => \Voxel\Form_Models\Number_Model::class,
				'label' => _x( 'Decimals', 'modifiers', 'voxel-backend' ),
				'description' => _x( 'Precision of the number of decimal places. Default 0.', 'modifiers', 'voxel-backend' ),
			],
		];
	}

	public function apply( $value, $args, $group ) {
		if ( ! is_numeric( $value ) ) {
			return $value;
		}

		$decimals = $args[0] ?? 0;
		if ( $decimals < 0 ) {
			$exp = absint( $decimals );
			$rounded = round( $value / pow( 10, $exp ) ) * pow( 10, $exp );

			$formatted = number_format_i18n( $rounded, 0 );
			if ( ! is_null( $formatted ) ) {
				$value = $formatted;
			}

			return $value;
		} else {
			$formatted = number_format_i18n( $value, $decimals );
			if ( ! is_null( $formatted ) ) {
				$value = $formatted;
			}

			return $value;
		}
	}
}
