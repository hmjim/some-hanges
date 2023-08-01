<?php

namespace Voxel\Post_Types\Fields;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Select_Field extends Base_Post_Field {

	protected $supported_conditions = ['text'];

	protected $props = [
		'type' => 'select',
		'label' => 'Select',
		'placeholder' => '',
		'choices' => [],
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'key' => $this->get_key_model(),
			'placeholder' => $this->get_placeholder_model(),
			'description' => $this->get_description_model(),
			'required' => $this->get_required_model(),
			'choices' => function() { ?>
				<div class="ts-form-group ts-col-1-1">
					<label>Choices</label>
					<select-field-choices :field="field"></select-field-choices>
				</div>
			<?php },
		];
	}

	public function sanitize( $value ) {
		$value = sanitize_text_field( $value );
		$choice_exists = false;
		foreach ( $this->props['choices'] as $choice ) {
			if ( $choice['value'] === $value ) {
				$choice_exists = true;
				break;
			}
		}

		if ( ! $choice_exists ) {
			return null;
		}

		return $value;
	}

	public function update( $value ): void {
		if ( $this->is_empty( $value ) ) {
			delete_post_meta( $this->post->get_id(), $this->get_key() );
		} else {
			update_post_meta( $this->post->get_id(), $this->get_key(), wp_slash( $value ) );
		}
	}

	public function get_value_from_post() {
		return get_post_meta( $this->post->get_id(), $this->get_key(), true );
	}

	public function get_selected_choice() {
		$value = $this->get_value();
		foreach ( $this->props['choices'] as $choice ) {
			if ( (string) $choice['value'] === (string) $value ) {
				return $choice;
			}
		}

		return null;
	}

	protected function frontend_props() {
		$choices = $this->props['choices'];
		$prepared_choices = [];
		foreach ( $choices as $choice ) {
			$value = (string) $choice['value'];
			$label = (string) $choice['label'];
			$icon = (string) $choice['icon'];

			if ( mb_strlen( $value ) < 1 ) {
				continue;
			}

			if ( mb_strlen( $label ) < 1 ) {
				$label = $value;
			}

			if ( ! empty( $icon ) ) {
				$icon = \Voxel\get_icon_markup( $icon );
			}

			$prepared_choices[ $value ] = [
				'value' => $value,
				'label' => $label,
				'icon' => $icon,
			];
		}

		return [
			'placeholder' => $this->props['placeholder'] ?: $this->props['label'],
			'choices' => $prepared_choices,
			'default_icon' => \Voxel\svg( 'bookmark.svg', false ),
		];
	}

	public function exports() {
		return [
			'type' => \Voxel\T_OBJECT,
			'label' => $this->get_label(),
			'properties' => [
				'value' => [
					'label' => 'Value',
					'type' => \Voxel\T_STRING,
					'callback' => function() {
						return $this->get_value();
					},
				],
				'label' => [
					'label' => 'Label',
					'type' => \Voxel\T_STRING,
					'callback' => function() {
						$choice = $this->get_selected_choice();
						return $choice['label'] ?? null;
					},
				],
				'icon' => [
					'label' => 'Icon',
					'type' => \Voxel\T_STRING,
					'callback' => function() {
						$choice = $this->get_selected_choice();
						return $choice['icon'] ?? null;
					},
				],
			],
		];
	}

	public function export_to_personal_data() {
		$choice = $this->get_selected_choice();
		return $choice['label'] ?? null;
	}
}
