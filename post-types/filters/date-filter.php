<?php

namespace Voxel\Post_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Date_Filter extends Base_Filter {
	use Traits\Date_Filter_Helpers;

	protected $props = [
		'type' => 'date',
		'label' => 'Date',
		'source' => 'date',
		'input_mode' => 'date-range',
		'compare' => 'greater_than',
		'l10n_from' => 'From',
		'l10n_to' => 'To',
		'l10n_pickdate' => 'Choose date',
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'description' => $this->get_description_model(),
			'key' => $this->get_key_model(),
			'icon' => $this->get_icon_model(),
			'source' => $this->get_source_model( 'date' ),
			'input_mode' => [
				'type' => \Voxel\Form_Models\Select_Model::class,
				'label' => 'Input mode',
				'width' => '1/1',
				'choices' => [
					'date-range' => 'Date range',
					'single-date' => 'Single date',
				],
			],
			'compare' => [
				'type' => \Voxel\Form_Models\Select_Model::class,
				'v-if' => 'filter.input_mode === \'single-date\'',
				'label' => 'Comparison',
				'width' => '1/1',
				'choices' => [
					'equals' => 'Equals selected date',
					'greater_than' => 'Greater than selected date',
					'less_than' => 'Less than selected date',
				],
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

	public function setup( \Voxel\Post_Types\Index_Table $table ): void {
		$field = $this->post_type->get_field( $this->props['source'] );
		$datatype = $field && $field->get_prop( 'enable_timepicker' ) ? 'DATETIME' : 'DATE';
		$table->add_column( sprintf( '`%s` %s', esc_sql( $this->db_key() ), $datatype ) );
		$table->add_key( sprintf( 'KEY(`%s`)', esc_sql( $this->db_key() ) ) );
	}

	public function index( \Voxel\Post $post ): array {
		$field = $post->get_field( $this->props['source'] );
		if ( $field && $field->get_type() === 'date' ) {
			$timestamp = strtotime( $field->get_value() );
			$format = $field->get_prop( 'enable_timepicker' ) ? 'Y-m-d H:i:s' : 'Y-m-d';
			if ( $timestamp ) {
				$value = date( $format, $timestamp );
			}
		}

		return [
			$this->db_key() => isset( $value ) ? sprintf( '\'%s\'', esc_sql( $value ) ) : 'NULL',
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

		if ( $this->props['input_mode'] === 'single-date' ) {
			if ( $this->props['compare'] === 'greater_than' ) {
				$operator = '>=';
			} elseif ( $this->props['compare'] === 'less_than' ) {
				$operator = '<=';
			} else {
				$operator = '=';
			}

			$query->where( sprintf(
				"`%s` {$operator} '%s'",
				esc_sql( $this->db_key() ),
				esc_sql( $value['start'] )
			) );
		} else {
			$query->where( sprintf(
				"`%s` BETWEEN '%s' AND '%s'",
				esc_sql( $this->db_key() ),
				esc_sql( $value['start'] ),
				esc_sql( $value['end'] )
			) );
		}
	}
}
