<?php

namespace Voxel\Post_Types\Filters\Traits;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

trait Numeric_Filter_Helpers {

	public function setup( \Voxel\Post_Types\Index_Table $table ): void {
		$datatype = $this->_get_column_type();
		$table->add_column( sprintf( '`%s` %s NOT NULL DEFAULT 0', esc_sql( $this->db_key() ), $datatype ) );
		$table->add_key( sprintf( 'KEY(`%s`)', esc_sql( $this->db_key() ) ) );
	}

	public function index( \Voxel\Post $post ): array {
		$parts = explode( '->', $this->props['source'] );
		$field = $post->get_field( $parts[0] );
		if ( $field ) {
			if ( $field->get_type() === 'number' ) {
				$value = ! empty( $field->get_value() ) ? $this->_prepare_value( $field->get_value() ) : 0;
			} elseif ( $field->get_type() === 'product' ) {
				$config = $field->get_value();
				if ( ! $config['enabled'] ) {
					$value = 0;
				} elseif ( ( $parts[1] ?? null ) === ':base_price' ) {
					$value = is_numeric( $config['base_price'] ) ? $this->_prepare_value( $config['base_price'] ) : 0;
				} else {
					$addition_enabled = !! ( $config['additions'][ $parts[1] ]['enabled'] ?? null );
					$addition_max = $config['additions'][ $parts[1] ]['max'] ?? null;
					$value = $addition_enabled && is_numeric( $addition_max ) ? $this->_prepare_value( $addition_max ) : 0;
				}
			}
		} else {
			$value = 0;
		}

		return [
			$this->db_key() => $value,
		];
	}

	public function _get_max_int_size() {
		$max = max(
			absint( $this->props['range_start'] ),
			absint( $this->props['range_end'] )
		);

		return ceil( $max * $this->_get_value_multiplier() );
	}

	public function _get_value_multiplier() {
		$step = (float) abs( (float) $this->props['step_size'] );
		$precision = strlen( substr( strrchr( $step, '.' ), 1 ) );

		return pow( 10, $precision );
	}

	public function _get_column_type() {
		$max = $this->_get_max_int_size();

		if ( $max < ((2**7) - 1) ) {
			return 'TINYINT';
		} elseif ( $max < ((2**15) - 1) ) {
			return 'SMALLINT';
		} elseif ( $max < ((2**23) - 1) ) {
			return 'MEDIUMINT';
		} elseif ( $max < ((2**31) - 1) ) {
			return 'INT';
		} else {
			return 'BIGINT';
		}
	}

	public function _prepare_value( $value ) {
		$value = (float) $value;
		return intval( round( $value * $this->_get_value_multiplier(), 0 ) );
	}

	public function _get_source_model() {
		return function() { ?>
			<div class="ts-form-group ts-col-1-1">
				<label>Data source:</label>
				<select v-model="filter.source">
					<option v-for="field in $root.getFieldsByType('number')" :value="field.key">
						{{ field.label }}
					</option>
					<template v-for="field in $root.getFieldsByType('product')">
						<optgroup :label="field.label">
							<option :value="field.key+'->:base_price'">Base price</option>
							<template v-if="$root.getProductAdditionsByType(field, 'numeric').length">
								<option v-for="addition in $root.getProductAdditionsByType(field, 'numeric')" :value="field.key+'->'+addition.key">
									{{ addition.label }} (Max.)
								</option>
							</template>
						</optgroup>
					</template>
				</select>
			</div>
		<?php };
	}
}
