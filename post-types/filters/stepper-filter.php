<?php

namespace Voxel\Post_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Stepper_Filter extends Base_Filter {
	use Traits\Numeric_Filter_Helpers;

	protected $props = [
		'type' => 'stepper',
		'label' => 'Stepper',
		'placeholder' => '',
		'source' => '',
		'step_size' => 1,
		'range_start' => 0,
		'range_end' => 1000,
		'compare' => 'equals',
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'description' => $this->get_description_model(),
			'placeholder' => $this->get_placeholder_model(),
			'key' => $this->get_key_model(),
			'icon' => $this->get_icon_model(),
			'source' => $this->_get_source_model(),
			'range_start' => [
				'type' => \Voxel\Form_Models\Number_Model::class,
				'label' => 'Range start',
				'width' => '1/3',
				'step' => 'any',
			],
			'range_end' => [
				'type' => \Voxel\Form_Models\Number_Model::class,
				'label' => 'Range end',
				'width' => '1/3',
				'step' => 'any',
			],
			'step_size' => [
				'type' => \Voxel\Form_Models\Number_Model::class,
				'label' => 'Step size',
				'width' => '1/3',
				'min' => 0,
				'step' => 'any',
			],
			'compare' => [
				'type' => \Voxel\Form_Models\Select_Model::class,
				'label' => 'Comparison',
				'width' => '1/1',
				'choices' => [
					'equals' => 'Equals selected value',
					'greater_than' => 'Greater than or equal to selected value',
					'less_than' => 'Less than or equal to selected value',
				],
			],
		];
	}

	public function query( \Voxel\Post_Types\Index_Query $query, array $args ): void {
		$value = $this->parse_value( $args[ $this->get_key() ] ?? null );
		if ( $value === null ) {
			return;
		}

		$value = $this->_prepare_value( $value );

		if ( $this->props['compare'] === 'greater_than' ) {
			$operator = '>=';
		} elseif ( $this->props['compare'] === 'less_than' ) {
			$operator = '<=';
		} else {
			$operator = '=';
		}

		$query->where( sprintf(
			"`%s` {$operator} %d",
			esc_sql( $this->db_key() ),
			$value
		) );
	}

	public function frontend_props() {
		$step = (float) abs( $this->props['step_size'] );
		$precision = absint( strlen( substr( strrchr( $step, '.' ), 1 ) ) );
		$value = $this->parse_value( $this->get_value() );

		return [
			'value' => $value,
			'step_size' => $step,
			'precision' => $precision,
			'range_start' => (float) $this->props['range_start'],
			'range_end' => (float) $this->props['range_end'],
			'placeholder' => $this->props['placeholder'] ?: $this->props['label'],
			'display_as' => $this->elementor_config['display_as'] ?? 'popup',
		];
	}

	public function parse_value( $value ) {
		if ( ! is_numeric( $value ) ) {
			return null;
		}

		return (float) $value;
	}

	public function get_elementor_controls(): array {
		return [
			'value' => [
				'label' => _x( 'Default value', 'range filter', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
			],
			'display_as' => [
				'label' => _x( 'Display as', 'keywords_filter', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'popup' => _x( 'Popup', 'keywords_filter', 'voxel-backend' ),
					'inline' => _x( 'Inline', 'keywords_filter', 'voxel-backend' ),
				],
				'conditional' => false,
			],
		];
	}
}
