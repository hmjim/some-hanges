<?php

namespace Voxel\Post_Types\Order_By;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Recurring_Date_Order extends Base_Search_Order {

	protected $props = [
		'type' => 'recurring-date',
		'source' => '',
	];

	public function get_label(): string {
		return 'Recurring date';
	}

	public function get_models(): array {
		return [
			'source' => function() { ?>
				<div class="ts-form-group ts-col-1-1">
					<label>Recurring date filter:</label>
					<select v-model="clause.source">
						<option v-for="filter in $root.getFiltersByType('recurring-date', 'availability')" :value="filter.key">
							{{ filter.label }}
						</option>
					</select>
				</div>
			<?php }
		];
	}

	public function query( \Voxel\Post_Types\Index_Query $query, array $args, array $clause_args ): void {
		$filter = $this->post_type->get_filter( $this->props['source'] );
		if ( $filter && $filter->get_type() === 'recurring-date' ) {
			$value = $filter->parse_value( $args[ $filter->get_key() ] ?? null );
			if ( $value === null ) {
				// @todo: query by "all" preset by default
				return;
			}

			$query->orderby( '`current_start` ASC' );
		}
	}
}
