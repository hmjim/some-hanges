<?php

namespace Voxel\Post_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Switcher_Filter extends Base_Filter {

	protected $props = [
		'type' => 'switcher',
		'label' => 'Switcher',
		'placeholder' => '',
		'source' => 'switcher',
		'compare' => 'checked',
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'placeholder' => $this->get_placeholder_model(),
			'description' => $this->get_description_model(),
			'key' => $this->get_key_model(),
			'icon' => $this->get_icon_model(),
			'source' => function() { ?>
				<div class="ts-form-group ts-col-1-1">
					<label>Data source:</label>
					<select v-model="filter.source">
						<option v-for="field in $root.getFieldsByType('switcher')" :value="field.key">
							{{ field.label }}
						</option>
						<template v-for="field in $root.getFieldsByType('product')">
							<optgroup v-if="$root.getProductAdditionsByType(field, 'checkbox').length" :label="field.label">
								<option v-for="addition in $root.getProductAdditionsByType(field, 'checkbox')" :value="field.key+'->'+addition.key">
									{{ addition.label }} (Is enabled)
								</option>
							</optgroup>
						</template>
					</select>
				</div>
			<?php },
			'compare' => [
				'type' => \Voxel\Form_Models\Select_Model::class,
				'label' => 'Comparison',
				'width' => '1/1',
				'choices' => [
					'checked' => 'Is checked',
					'unchecked' => 'Is unchecked',
				],
			],
		];
	}

	public function setup( \Voxel\Post_Types\Index_Table $table ): void {
		$table->add_column( sprintf( '`%s` TINYINT(1) NOT NULL DEFAULT 0', esc_sql( $this->db_key() ) ) );
		$table->add_key( sprintf( 'KEY(`%s`)', esc_sql( $this->db_key() ) ) );
	}

	public function index( \Voxel\Post $post ): array {
		$parts = explode( '->', $this->props['source'] );
		$field = $post->get_field( $parts[0] );
		if ( $field ) {
			if ( $field->get_type() === 'switcher' ) {
				$value = $field->get_value() ? 1 : 0;
			} elseif ( $field->get_type() === 'product' ) {
				$config = $field->get_value();
				$addition_enabled = !! ( $config['additions'][ $parts[1] ]['enabled'] ?? null );
				$value = $addition_enabled ? 1 : 0;
			}
		} else {
			$value = 0;
		}

		return [
			$this->db_key() => (int) $value,
		];
	}

	public function query( \Voxel\Post_Types\Index_Query $query, array $args ): void {
		$value = $this->parse_value( $args[ $this->get_key() ] ?? null );
		if ( $value === null ) {
			return;
		}

		$compare = $this->props['compare'] === 'unchecked' ? 0 : 1;
		$query->where( sprintf( '`%s` = %d', esc_sql( $this->db_key() ), $compare ) );
	}

	public function parse_value( $value ) {
		return absint( $value ) === 1 ? 1 : null;
	}

	public function get_elementor_controls(): array {
		return [
			'value' => [
				'label' => _x( 'Default value', 'open now filter', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'checked' => _x( 'Checked', 'switcher filter', 'voxel-backend' ),
					'unchecked' => _x( 'Unchecked', 'switcher filter', 'voxel-backend' ),
				],
			],
			'open_in_popup' => [
				'label' => _x( 'Open in Popup', 'open now filter', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'conditional' => false,
			],
		];
	}

	public function get_default_value_from_elementor( $controls ) {
		return ( $controls['value'] ?? null ) === 'checked' ? 1 : null;
	}

	public function frontend_props() {
		return [
			'openInPopup' => ( $this->elementor_config['open_in_popup'] ?? null ) === 'yes',
			'placeholder' => $this->props['placeholder'] ?: $this->props['label'],
		];
	}
}
