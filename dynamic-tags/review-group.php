<?php

namespace Voxel\Dynamic_Tags;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Review_Group extends Base_Group {

	public $key = 'review';
	public $label = 'Review';

	public $review;

	protected function properties(): array {
		return [
			':id' => [
				'label' => 'ID',
				'type' => \Voxel\T_NUMBER,
				'callback' => function() {
					return $this->review->get_id();
				},
			],

			':content' => [
				'label' => 'Content',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return $this->review->get_content_for_display();
				},
			],

			':created_at' => [
				'label' => 'Date created',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return $this->review->get_created_at();
				},
			],

			':link' => [
				'label' => 'Permalink',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return $this->review->get_link();
				},
			],

			':score' => [
				'label' => 'Score (1-5)',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					$score = $this->review->get_review_score();
					return $score === null ? null : ( $score + 3 );
				},
			],
		];
	}
}
