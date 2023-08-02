<?php

namespace Voxel\Post_Types\Fields;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Date_Field extends Base_Post_Field {

	protected $supported_conditions = ['date'];

	protected $props = [
		'type' => 'date',
		'label' => 'Date',
		'placeholder' => '',
		'enable_timepicker' => false,
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'key' => $this->get_key_model(),
			'placeholder' => $this->get_placeholder_model(),
			'description' => $this->get_description_model(),
			'required' => $this->get_required_model(),
			'enable_timepicker' => [
				'type' => Form_Models\Switcher_Model::class,
				'label' => 'Enable timepicker',
				'description' => 'Set whether users can also select the time of day when adding a date.',
			],
		];
	}

	public function sanitize( $value ) {
		$timestamp = strtotime( $value['date'] ?? null );
		if ( ! $timestamp ) {
			return null;
		}

		if ( $this->props['enable_timepicker'] && ( $time = strtotime( $value['time'] ?? null ) ) ) {
			$timestamp += 60 * ( ( absint( date( 'H', $time ) ) * 60 ) + absint( date( 'i', $time ) ) );
		}

		$format = $this->props['enable_timepicker'] ? 'Y-m-d H:i:s' : 'Y-m-d';
		return date( $format, $timestamp );
	}

	public function update( $value ): void {
		if ( empty( $value ) ) {
			delete_post_meta( $this->post->get_id(), $this->get_key() );
		} else {
			update_post_meta( $this->post->get_id(), $this->get_key(), $value );
		}
	}

	public function get_value_from_post() {
		return get_post_meta( $this->post->get_id(), $this->get_key(), true );
	}

	protected function editing_value() {
		$value = $this->get_value();
		$timestamp = strtotime( $value );

		return [
			'date' => $timestamp ? date( 'Y-m-d', $timestamp ) : null,
			'time' => $timestamp ? date( 'H:i', $timestamp ) : null,
		];
	}

	protected function frontend_props() {
		wp_enqueue_style( 'pikaday' );
		wp_enqueue_script( 'pikaday' );

		return [
			'enable_timepicker' => $this->props['enable_timepicker'],
			'placeholder' => $this->props['placeholder'] ?: $this->props['label'],
		];
	}

	public function exports() {
		return [
			'label' => $this->get_label(),
			'type' => \Voxel\T_OBJECT,
			'properties' => [
				'date' => [
					'label' => 'Date',
					'type' => \Voxel\T_DATE,
					'callback' => function() {
						return $this->get_value();
					},
				],

				'is_finished' => [
					'label' => 'Is finished?',
					'type' => \Voxel\T_STRING,
					'callback' => function() {
						$value = $this->get_value();
						$timestamp = strtotime( $value );
						if ( empty( $value ) || ! $timestamp ) {
							return null;
						}

						$date = new \DateTime( '@'.$timestamp, $this->post->get_timezone() );
						$now = new \DateTime( 'now', $this->post->get_timezone() );

						return $date < $now ? '1' : '';
					},
				],
			],

			// backwards compatibility
			'callback' => function() {
				return $this->get_value();
			},
		];
	}

	public function export_to_personal_data() {
		return $this->get_value();
	}
}
