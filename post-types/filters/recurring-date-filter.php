<?php

namespace Voxel\Post_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Recurring_Date_Filter extends Base_Filter {
	use Traits\Date_Filter_Helpers;

	protected $props = [
		'type' => 'recurring-date',
		'label' => 'Recurring Date',
		'source' => 'recurring-date',
		'input_mode' => 'date-range',
		'match_ongoing' => true,
		'l10n_from' => 'From',
		'l10n_to' => 'To',
		'l10n_pickdate' => 'Pick date',
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'description' => $this->get_description_model(),
			'key' => $this->get_key_model(),
			'icon' => $this->get_icon_model(),
			'source' => $this->get_source_model( 'recurring-date' ),
			'input_mode' => [
				'type' => \Voxel\Form_Models\Select_Model::class,
				'label' => 'Input mode',
				'width' => '1/1',
				'choices' => [
					'date-range' => 'Date range',
					'single-date' => 'Single date',
				],
			],
			'match_ongoing' => [
				'type' => \Voxel\Form_Models\Switcher_Model::class,
				'label' => 'Match ongoing dates',
				'description' => 'Set whether to match dates that have already begun but haven\'t ended yet.',
			],
			'l10n_from' => [
				'v-if' => 'filter.input_mode === \'date-range\'',
				'type' => \Voxel\Form_Models\Text_Model::class,
				'label' => 'From label',
				'width' => '1/2',
			],
			'l10n_to' => [
				'v-if' => 'filter.input_mode === \'date-range\'',
				'type' => \Voxel\Form_Models\Text_Model::class,
				'label' => 'To label',
				'width' => '1/2',
			],
			'l10n_pickdate' => [
				'v-if' => 'filter.input_mode === \'single-date\'',
				'type' => \Voxel\Form_Models\Text_Model::class,
				'label' => 'Placeholder',
			],
		];
	}

	public function query( \Voxel\Post_Types\Index_Query $query, array $args ): void {
		$value = $this->parse_value( $args[ $this->get_key() ] ?? null );
		if ( $value === null ) {
			return;
		}

		// preset
		if ( is_string( $value ) ) {
			$preset = \Voxel\get_range_presets( $value );
			if ( ! $preset ) {
				return;
			}

			$value = $preset['callback']( \Voxel\now() );
			if ( ! ( strtotime( $value['start'] ?? null ) && strtotime( $value['end'] ?? null ) ) ) {
				return;
			}
		}

		global $wpdb;

		$range_start = esc_sql( $value['start'] );
		$range_end = esc_sql( $value['end'] );
		$join_key = esc_sql( $this->db_key() );
		$post_type_key = esc_sql( $this->post_type->get_key() );
		$field_key = esc_sql( $this->props['source'] );

		// querying all ranges
		if ( $value['start'] === '1000-01-01' ) {
			$where_clause = '';
			$current_start = \Voxel\Utils\Recurring_Date\get_current_start_query(
				\Voxel\now()->format( 'Y-m-d' ),
				$value['end']
			);
		} else {
			$where_clause = 'AND ('.\Voxel\Utils\Recurring_Date\get_where_clause(
				$value['start'],
				$value['end'],
				$this->props['input_mode'],
				$this->props['match_ongoing']
			).')';

			$current_start = \Voxel\Utils\Recurring_Date\get_current_start_query( $value['start'], $value['end'] );
		}

		$query->join( <<<SQL
			INNER JOIN (
				SELECT post_id, {$current_start} FROM {$wpdb->prefix}voxel_recurring_dates
				WHERE `post_type` = '{$post_type_key}' AND `field_key` = '{$field_key}' {$where_clause}
			) AS `{$join_key}` ON `{$query->table->get_escaped_name()}`.post_id = `{$join_key}`.post_id
		SQL );

		$query->groupby( "`{$query->table->get_escaped_name()}`.post_id" );
	}
}
