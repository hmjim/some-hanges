<?php

namespace Voxel\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Review_Stats extends Base_Widget {

	public function get_name() {
		return 'ts-review-stats';
	}

	public function get_title() {
		return __( 'Review stats (VX)', 'voxel-elementor' );
	}

	public function get_icon() {
		return 'vxi vxi-smile';
	}

	public function get_categories() {
		return [ 'voxel', 'basic' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'ts_rs_grid',
			[
				'label' => __( 'Reviews grid', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'ts_rs_column_no',
				[
					'label' => __( 'Number of columns', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 1,
					'max' => 6,
					'step' => 1,
					'default' => 1,
					'selectors' => [
						'{{WRAPPER}} .ts-review-bars' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
					],
				]
			);




			$this->add_responsive_control(
				'ts_rs_col_gap',
				[
					'label' => __( 'Item gap', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px'],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ts-review-bars' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],

				]
			);
		$this->end_controls_section();

		$this->start_controls_section(
			'ts_rs_settings',
			[
				'label' => __( 'Review stats', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'ts_review_icon_size',
				[
					'label' => __( 'Icon size', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px'],
					'range' => [
						'px' => [
							'min' => 16,
							'max' => 80,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ts-bar-data i' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ts-bar-data svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'ts_review_icon_spacing',
				[
					'label' => __( 'Icon right spacing', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px'],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ts-bar-data i' => 'padding-left: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ts-bar-data svg' => 'margin-left: {{SIZE}}{{UNIT}};',
					],
				]
			);



			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'ts_review_typo',
					'label' => __( 'Typography', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-bar-data p',
				]
			);

			$this->add_control(
				'ts_review_typo_color',
				[
					'label' => __( 'Text color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-bar-data p' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'ts_review_chart_bg',
				[
					'label' => __( 'Chart background color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-bar-chart' => 'background-color: {{VALUE}}',
					],
				]
			);



			$this->add_control(
				'ts_review_excellent',
				[
					'label' => __( 'Excellent', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'ts_review_excellent_chart_bg',
				[
					'label' => __( 'Chart background (Filled)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#2fcba3',
					'selectors' => [
						'{{WRAPPER}} .excellent .ts-bar-chart > div' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'ts_review_excellent_icon_color',
				[
					'label' => __( 'Icon color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#2fcba3',
					'selectors' => [
						'{{WRAPPER}} .ts-percentage-bar.excellent i' => 'color: {{VALUE}}',
						'{{WRAPPER}} .ts-percentage-bar.excellent svg' => 'fill: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'ts_review_excellent_text_color',
				[
					'label' => __( 'Text color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-percentage-bar.excellent p' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'ts_review_excellent_icon',
				[
					'label' => __( 'Choose icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'ts_review_verygood',
				[
					'label' => __( 'Very good', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'ts_review_verygood_chart_bg',
				[
					'label' => __( 'Chart background (Filled)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#20b5dc',
					'selectors' => [
						'{{WRAPPER}} .very-good .ts-bar-chart > div' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'ts_review_verygood_icon_color',
				[
					'label' => __( 'Icon color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#20b5dc',
					'selectors' => [
						'{{WRAPPER}} .ts-percentage-bar.very-good i' => 'color: {{VALUE}}',
						'{{WRAPPER}} .ts-percentage-bar.very-good svg' => 'fill: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'ts_review_verygood_text_color',
				[
					'label' => __( 'Text color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-percentage-bar.very-good p' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'ts_review_verygood_icon',
				[
					'label' => __( 'Choose icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'ts_review_good',
				[
					'label' => __( 'Good', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'ts_review_good_chart_bg',
				[
					'label' => __( 'Chart background (Filled)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#6369cd',
					'selectors' => [
						'{{WRAPPER}} .good .ts-bar-chart > div' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'ts_review_good_icon_color',
				[
					'label' => __( 'Icon color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#6369cd',
					'selectors' => [
						'{{WRAPPER}} .ts-percentage-bar.good i' => 'color: {{VALUE}}',
						'{{WRAPPER}} .ts-percentage-bar.good svg' => 'fill: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'ts_review_good_text_color',
				[
					'label' => __( 'Text color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-percentage-bar.good p' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'ts_review_good_icon',
				[
					'label' => __( 'Choose icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'ts_review_fair',
				[
					'label' => __( 'Fair', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'ts_review_fair_chart_bg',
				[
					'label' => __( 'Chart background (Filled)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#ff906b',
					'selectors' => [
						'{{WRAPPER}} .fair .ts-bar-chart > div' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'ts_review_fair_icon_color',
				[
					'label' => __( 'Icon color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#ff906b',
					'selectors' => [
						'{{WRAPPER}} .ts-percentage-bar.fair i' => 'color: {{VALUE}}',
						'{{WRAPPER}} .ts-percentage-bar.fair svg' => 'fill: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'ts_review_fair_text_color',
				[
					'label' => __( 'Text color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-percentage-bar.fair p' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'ts_review_fair_icon',
				[
					'label' => __( 'Choose icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);


			$this->add_control(
				'ts_review_poor',
				[
					'label' => __( 'Poor', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'ts_review_poor_chart_bg',
				[
					'label' => __( 'Chart background (Filled)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#f43b3b',
					'selectors' => [
						'{{WRAPPER}} .poor .ts-bar-chart > div' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'ts_review_poor_icon_color',
				[
					'label' => __( 'Icon color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#f43b3b',
					'selectors' => [
						'{{WRAPPER}} .ts-percentage-bar.poor i' => 'color: {{VALUE}}',
						'{{WRAPPER}} .ts-percentage-bar.poor svg' => 'fill: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'ts_review_poor_text_color',
				[
					'label' => __( 'Text color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-percentage-bar.poor p' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'ts_review_poor_icon',
				[
					'label' => __( 'Choose icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);




		$this->end_controls_section();
	}

	protected function render( $instance = [] ) {
		$post = \Voxel\get_current_post();
		if ( ! $post ) {
			return;
		}

		$stats = $post->repository->get_review_stats();
		$pct = [
			'excellent' => 0,
			'very_good' => 0,
			'good' => 0,
			'fair' => 0,
			'poor' => 0,
		];

		if ( $stats['total'] > 0 ) {
			$pct['excellent'] = round( ( ( $stats['by_score'][2] ?? 0 ) / $stats['total'] ) * 100 );
			$pct['very_good'] = round( ( ( $stats['by_score'][1] ?? 0 ) / $stats['total'] ) * 100 );
			$pct['good']      = round( ( ( $stats['by_score'][0] ?? 0 ) / $stats['total'] ) * 100 );
			$pct['fair']      = round( ( ( $stats['by_score'][-1] ?? 0 ) / $stats['total'] ) * 100 );
			$pct['poor']      = round( ( ( $stats['by_score'][-2] ?? 0 ) / $stats['total'] ) * 100 );
		}

		wp_print_styles( $this->get_style_depends() );
		require locate_template( 'templates/widgets/review-stats.php' );
	}

	public function get_style_depends() {
		return [ 'vx:review-stats.css' ];
	}

	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}

	protected function register_runtime_controls() {
		$this->add_control(
			'ts_review_excellent_icon',
			[
				'label' => __( 'Choose icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'ts_review_verygood_icon',
			[
				'label' => __( 'Choose icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'ts_review_good_icon',
			[
				'label' => __( 'Choose icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'ts_review_fair_icon',
			[
				'label' => __( 'Choose icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'ts_review_poor_icon',
			[
				'label' => __( 'Choose icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);
	}
}
