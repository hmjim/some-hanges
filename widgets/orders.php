<?php

namespace Voxel\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Orders extends Base_Widget {

	public function get_name() {
		return 'ts-orders';
	}

	public function get_title() {
		return __( 'Requests and orders (VX)', 'voxel-elementor' );
	}

	public function get_icon() {
		return 'vxi vxi-bag';
	}

	public function get_categories() {
		return [ 'voxel', 'basic' ];
	}

	protected function register_controls() {



		$this->start_controls_section(
			'order_styling_filters',
			[
				'label' => __( 'Requests list: Filters', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'order_filters_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'order_sf_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);



					$this->add_responsive_control(
						'order_filter_margin',
						[
							'label' => __( 'Form padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .ts-order-filters' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);


					$this->add_control(
							'order_sf_input',
							[
								'label' => __( 'Filter style', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::HEADING,
								'separator' => 'before',
							]
						);

						$this->add_group_control(
							\Elementor\Group_Control_Typography::get_type(),
							[
								'name' => 'order_sf_input_input_typo',
								'label' => __( 'Typography' ),
								'selector' => '{{WRAPPER}} .ts-order-filters .ts-filter-text',
							]
						);





						$this->add_control(
							'order_sf_input_spacing',
							[
								'label' => __( 'Spacing', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::SLIDER,
								'size_units' => [ 'px' ],
								'range' => [
									'px' => [
										'min' => 0,
										'max' => 100,
										'step' => 1,
									],
								],
								'selectors' => [
									'{{WRAPPER}} .ts-order-filters .ts-filter' => 'padding-right: {{SIZE}}{{UNIT}};',
								],
							]
						);



						$this->add_responsive_control(
							'order_sf_input_value_col',
							[
								'label' => __( 'Text color', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ts-order-filters .ts-filter-text' => 'color: {{VALUE}}',
								],

							]
						);








						$this->add_control(
							'order_chevron',
							[
								'label' => __( 'Chevron', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::HEADING,
								'separator' => 'before',
							]
						);

						$this->add_control(
							'order_hide_chevron',
							[

								'label' => __( 'Hide chevron', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::SWITCHER,
								'label_on' => __( 'Hide', 'voxel-elementor' ),
								'label_off' => __( 'Show', 'voxel-elementor' ),
								'return_value' => 'yes',

								'selectors' => [
									'{{WRAPPER}} .ts-order-filters .ts-down-icon' => 'display: none !important;',
								],
							]
						);

						$this->add_control(
							'order_chevron_btn_color',
							[
								'label' => __( 'Chevron color', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ts-order-filters .ts-down-icon' => 'border-top-color: {{VALUE}}',
								],
							]
						);



				$this->end_controls_tab();


				/* Hover tab */

					$this->start_controls_tab(
						'order_sf_hover',
						[
							'label' => __( 'Hover', 'voxel-elementor' ),
						]
					);

						$this->add_control(
							'order_sf_input_h',
							[
								'label' => __( 'Style', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::HEADING,
								'separator' => 'before',
							]
						);



						$this->add_responsive_control(
							'order_sf_input_value_col_h',
							[
								'label' => __( 'Text color', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ts-order-filters .ts-filter:hover .ts-filter-text' => 'color: {{VALUE}}',
								],

							]
						);




						$this->add_control(
							'order_chevron_btn_h',
							[
								'label' => __( 'Chevron color', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ts-order-filters .ts-filter:hover .ts-down-icon' => 'border-top-color: {{VALUE}}',
								],
							]
						);

					$this->end_controls_tab();

					/* Hover tab */

					$this->start_controls_tab(
						'order_sf_filled',
						[
							'label' => __( 'Filled', 'voxel-elementor' ),
						]
					);

						$this->add_control(
							'order_sf_input_filled',
							[
								'label' => __( 'Style (Filled)', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::HEADING,
								'separator' => 'before',
							]
						);

						$this->add_group_control(
							\Elementor\Group_Control_Typography::get_type(),
							[
								'name' => 'order_sf_input_typo_filled',
								'label' => __( 'Typography', 'voxel-elementor' ),
								'selector' => '{{WRAPPER}} .ts-order-filters .ts-filter.ts-filled .ts-filter-text',
							]
						);

						$this->add_responsive_control(
							'order_sf_input_value_col_filled',
							[
								'label' => __( 'Text color', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ts-order-filters .ts-filter.ts-filled .ts-filter-text' => 'color: {{VALUE}}',
								],

							]
						);




						$this->add_control(
							'order_chevron_btn_f',
							[
								'label' => __( 'Chevron color', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ts-order-filters .ts-filter.ts-filled .ts-down-icon' => 'border-top-color: {{VALUE}}',
								],
							]
						);


					$this->end_controls_tab();

				$this->end_controls_tabs();

		$this->end_controls_section();



		$this->start_controls_section(
			'ts_table_general',
			[
				'label' => __( 'Request list: Item', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'order_row_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'order_row_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);

					$this->add_responsive_control(
						'ts_row_col_padding',
						[
							'label' => __( 'Padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .ts-order-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_table_bg',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-order-item' => 'background: {{VALUE}}',
							],
						]
					);


					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_freset_btn_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-order-item',
						]
					);

					$this->add_responsive_control(
						'ts_table_radius',
						[
							'label' => __( 'Border radius', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
									'step' => 1,
								],
								'%' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ts-order-item' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_table_shadow',
							'label' => __( 'Box Shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-order-item',
						]
					);

				$this->end_controls_tab();

				/* Hover tab */

				$this->start_controls_tab(
					'order_row_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'ts_table_bg_hover',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-order-item:hover' => 'background: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'ts_table_border-color_h',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}}  .ts-order-item:hover' => 'border-color: {{VALUE}}',
							],
						]
					);

					$this->add_responsive_control(
						'ts_table_border_scale',
						[
							'label' => __( 'Scale', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px' ],
							'range' => [
								'px' => [
									'min' => 0.9,
									'max' => 1.1,
									'step' => 0.01,
								],
							],
							'selectors' => [
								'{{WRAPPER}}  .ts-order-item:hover' => 'transform: scale({{SIZE}})',
							],
						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_req_list',
			[
				'label' => __( 'Requests list', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'ts_req_list_padding',
				[
					'label' => __( 'Container padding', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .orders-flex,{{WRAPPER}} .ts-single-order' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_table_row_column',
			[
				'label' => __( 'Requests list: Item content', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'order_row_column_tabs'
			);
				/* Normal tab */

				$this->start_controls_tab(
					'order_row_column_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);
					$this->add_responsive_control(
						'ts_row_gap',
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
								'{{WRAPPER}} .ts-order-item' => 'grid-gap: {{SIZE}}{{UNIT}};',
							],

						]
					);

					$this->add_control(
						'ts_row_user',
						[
							'label' => __( 'User', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_responsive_control(
						'ts_row_avatar_size',
						[
							'label' => __( 'Avatar size', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
									'step' => 1,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .data-con img.ts-status-avatar' => 'width: {{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_row_avatar_radius',
						[
							'label' => __( 'Border radius', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
									'step' => 1,
								],
								'%' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .data-con img.ts-status-avatar' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_row_text',
						[
							'label' => __( 'Text', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_responsive_control(
						'ts_row_text_gap',
						[
							'label' => __( 'Text gap', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
									'step' => 1,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .data-con p, {{WRAPPER}} .data-con span' => 'margin-right: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_row_primary',
						[
							'label' => __( 'Primary text', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_primary_typo',
							'label' => __( 'Typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .data-con span p',
						]
					);

					$this->add_control(
						'ts_primary_color',
						[
							'label' => __( 'Color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .data-con span p' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'ts_row_secondary',
						[
							'label' => __( 'Secondary', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_secondary_typo',
							'label' => __( 'Typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .data-con span',
						]
					);

					$this->add_control(
						'ts_secondary_color',
						[
							'label' => __( 'Color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .data-con span' => 'color: {{VALUE}}',
							],
						]
					);







				$this->end_controls_tab();

				$this->start_controls_tab(
					'order_row_column_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);



					$this->add_control(
						'ts_row_user_color_h',
						[
							'label' => __( 'User', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} #ts-orders-table tr:hover .ts-table-con p' => 'color: {{VALUE}}',
							],
						]
					);




					$this->add_control(
						'ts_row_price_color_hover',
						[
							'label' => __( 'Price', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} #ts-orders-table tr:hover .ts-table-price' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'ts_row_other_color_hover',
						[
							'label' => __( 'Other details', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} #ts-orders-table tr:hover .ts-table-other' => 'color: {{VALUE}}',
							],
						]
					);



					$this->add_control(
						'ts_row_stripe_color_hover',
						[
							'label' => __( 'Stripe icon', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} #ts-orders-table tr:hover .ts-table-other i' => 'color: {{VALUE}}',
							],
						]
					);


				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_outer_status',
			[
				'label' => __( 'Requests list: Order status', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);



				$this->add_control(
					'ts_ostatus_padding',
					[
						'label' => __( 'Padding', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'selectors' => [
							'{{WRAPPER}} .ts-order-status' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'ts_ostatus_height',
					[
						'label' => __( 'Height', 'voxel-elementor' ),
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
							'{{WRAPPER}} .ts-order-status' => 'height: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'ts_ostatus_icon_size',
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
							'{{WRAPPER}} .ts-order-status i' => 'font-size: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .ts-order-status svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
						],
					]
				);



				$this->add_responsive_control(
					'ts_ostatus_icon_spacing',
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
							'{{WRAPPER}} .ts-order-status i' => 'margin-right: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .ts-order-status svg' => 'margin-right: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'ts_ostatus_radius',
					[
						'label' => __( 'Border radius', 'voxel-elementor' ),
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
							'{{WRAPPER}} .ts-order-status' => 'border-radius: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' => 'ts_ostatus_typo',
						'label' => __( 'Typography', 'voxel-elementor' ),
						'selector' => '{{WRAPPER}} .ts-order-status p',
					]
				);


		$this->end_controls_section();

		/*
		==========
		No posts
		==========
		*/

		$this->start_controls_section(
			'ts_no_posts',
			[
				'label' => __( 'Request list: Loading/No results', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'ts_nopost_padding',
				[
					'label' => __( 'Container padding', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .ts-no-posts' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'ts_nopost_content_Gap',
				[
					'label' => __( 'Content gap', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-no-posts' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'ts_nopost_ico_size',
				[
					'label' => __( 'Icon size', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-no-posts i' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ts-no-posts svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'ts_nopost_ico_col',
				[
					'label' => __( 'Icon color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-no-posts i' => 'color: {{VALUE}}',
						'{{WRAPPER}} .ts-no-posts svg' => 'fill: {{VALUE}}',
					],

				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'ts_nopost_typo',
					'label' => __( 'Typography', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-no-posts p',
				]
			);

			$this->add_responsive_control(
				'ts_nopost_typo_col',
				[
					'label' => __( 'Text color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-no-posts p' => 'color: {{VALUE}}',
					],

				]
			);


			$this->add_control(
				'ts_tm_loading',
				[
					'label' => __( 'Loading', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'tm_color1',
				[
					'label' => __( 'Color 1', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-loader' => 'border-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'tm_color2',
				[
					'label' => __( 'Color 2', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-loader' => 'border-bottom-color: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_status_colors',
			[
				'label' => __( 'Order status colors', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'tss_complete',
				[
					'label' => __( 'Completed/Active', 'voxel-elementor' ),
					'description' => __( 'Completed and active status', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'tss_comp_c',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .completed > *, {{WRAPPER}} .sub_active  > *' => 'color: {{VALUE}}',
						'{{WRAPPER}} .completed > svg, {{WRAPPER}} .sub_active  > svg' => 'fill: {{VALUE}}',
					],

				]
			);


			$this->add_responsive_control(
				'tss_comp_bg',
				[
					'label' => __( 'Background', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .completed, {{WRAPPER}} .sub_active' => 'background: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'tss_pending',
				[
					'label' => __( 'Pending/Incomplete/Past due', 'voxel-elementor' ),
					'description' => __( 'Pending payment, pending approval, session completed, subscription unpaid, incomplete or past due', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'tss_pend_c',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .pending_payment > *, {{WRAPPER}} .pending_approval > *, {{WRAPPER}} .session_completed > *, {{WRAPPER}} .sub_past_due > *, {{WRAPPER}} .sub_unpaid > *, {{WRAPPER}} .sub_incomplete > *' => 'color: {{VALUE}}',
						'{{WRAPPER}} .pending_payment > svg, {{WRAPPER}} .pending_approval > svg, {{WRAPPER}} .session_completed > svg, {{WRAPPER}} .sub_past_due > svg, {{WRAPPER}} .sub_unpaid > svg, {{WRAPPER}} .sub_incomplete > svg' => 'fill: {{VALUE}}',
					],

				]
			);


			$this->add_responsive_control(
				'tss_pend_bg',
				[
					'label' => __( 'Background', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .pending_payment, {{WRAPPER}} .pending_approval, {{WRAPPER}} .session_completed, {{WRAPPER}} .sub_past_due, {{WRAPPER}} .sub_unpaid, {{WRAPPER}} .sub_incomplete' => 'background: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'tss_canceled',
				[
					'label' => __( 'Canceled/Declined/Refunded', 'voxel-elementor' ),
					'description' => __( 'Canceled' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'tss_canc_c',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .canceled > *, {{WRAPPER}} .declined > *, {{WRAPPER}} .failed > *, {{WRAPPER}} .refunded > *, {{WRAPPER}} .sub_incomplete_expired > *, {{WRAPPER}} .sub_cancelled > *' => 'color: {{VALUE}}',
						'{{WRAPPER}} .canceled > svg, {{WRAPPER}} .declined > svg, {{WRAPPER}} .failed > svg, {{WRAPPER}} .refunded > svg, {{WRAPPER}} .sub_incomplete_expired > svg, {{WRAPPER}} .sub_cancelled > svg' => 'fill: {{VALUE}}',
					],

				]
			);


			$this->add_responsive_control(
				'tss_canc_bg',
				[
					'label' => __( 'Background', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .canceled, {{WRAPPER}} .declined, {{WRAPPER}} .failed, {{WRAPPER}} .refunded, {{WRAPPER}} .sub_incomplete_expired, {{WRAPPER}} .sub_cancelled' => 'background: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'tss_neutral',
				[
					'label' => __( 'Neutral/Trial', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'tss_neutral_c',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .neutral > *, {{WRAPPER}} .sub_trialing  > *,{{WRAPPER}} .sub_trialing .refund_requested > *' => 'color: {{VALUE}}',
						'{{WRAPPER}} .neutral > svg, {{WRAPPER}} .sub_trialing  > svg,{{WRAPPER}} .sub_trialing .refund_requested > *' => 'fill: {{VALUE}}',
					],

				]
			);


			$this->add_responsive_control(
				'tss_neutral_bg',
				[
					'label' => __( 'Background', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .neutral, {{WRAPPER}} .sub_trialing, {{WRAPPER}} .sub_trialing .refund_requested > *' => 'background: {{VALUE}}',
					],

				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_req_pag',
			[
				'label' => __( 'Request list: Pagination', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'ts_rpag_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'ts_rpag_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);


					$this->add_responsive_control(
						'ts_pag_padding',
						[
							'label' => __( 'Margin', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .orders-pagination' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_rpag_btn_typo',
							'label' => __( 'Button typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .orders-pagination .ts-btn-1',
						]
					);

					$this->add_control(
						'ts_rpag_padding',
						[
							'label' => __( 'Padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px'],
							'selectors' => [
								'{{WRAPPER}}  .orders-pagination .ts-btn-1' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_rpag_btn_height',
						[
							'label' => __( 'Height', 'voxel-elementor' ),
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
								'{{WRAPPER}} .orders-pagination .ts-btn-1' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);


					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_rpag_btn_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .orders-pagination .ts-btn-1',
						]
					);

					$this->add_responsive_control(
						'ts_rpag_btn_radius',
						[
							'label' => __( 'Border radius', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
									'step' => 1,
								],
								'%' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'default' => [
								'unit' => 'px',
								'size' => 5,
							],
							'selectors' => [
								'{{WRAPPER}} .orders-pagination .ts-btn-1' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);


					$this->add_responsive_control(
						'ts_rpag_btn_c',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .orders-pagination .ts-btn-1' => 'color: {{VALUE}}',
							],

						]
					);


					$this->add_responsive_control(
						'ts_rpag_btn_bg',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .orders-pagination .ts-btn-1' => 'background: {{VALUE}}',
							],

						]
					);



					$this->add_responsive_control(
						'ts_rpag_btn_icon_size',
						[
							'label' => __( 'Icon size', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
									'step' => 1,
								],
								'%' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'default' => [
								'unit' => 'px',
								'size' => 24,
							],
							'selectors' => [
								'{{WRAPPER}} .orders-pagination .ts-btn-1 i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .orders-pagination .ts-btn-1 svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_rpag_btn_icon_color',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .orders-pagination .ts-btn-1 i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .orders-pagination .ts-btn-1 svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_rpag_icon_spacing',
						[
							'label' => __( 'Icon spacing', 'voxel-elementor' ),
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
								'{{WRAPPER}} .orders-pagination .ts-btn-1' => 'grid-gap: {{SIZE}}{{UNIT}};',
							],
						]
					);



				$this->end_controls_tab();

				/* Hover tab */

				$this->start_controls_tab(
					'ts_rpag_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_responsive_control(
						'ts_rpag_btn_c_h',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .orders-pagination .ts-btn-1:hover' => 'color: {{VALUE}}',
							],

						]
					);


					$this->add_responsive_control(
						'ts_rpag_btn_bg_h',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .orders-pagination .ts-btn-1:hover' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_rpag_btn_icon_color_h',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .orders-pagination .ts-btn-1:hover i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .orders-pagination .ts-btn-1:hover svg' => 'fill: {{VALUE}}',
							],

						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'single_general',
			[
				'label' => __( 'Single request: General', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'ts_timeline_post_spacing',
				[
					'label' => __( 'Post spacing', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-status-list' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'ts_post__content_spacing',
				[
					'label' => __( 'Post content spacing', 'voxel-elementor' ),
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

						'{{WRAPPER}} .ts-status-body,{{WRAPPER}} .ts-status,{{WRAPPER}} .ts-status-body p' => 'grid-gap: {{SIZE}}{{UNIT}};',

					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'pg_icon_button',
			[
				'label' => __( 'Single request: Navigation buttons', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'pg_icon_button_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'pg_icon_button_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);



					$this->add_control(
						'ib_styling',
						[
							'label' => __( 'Button styling', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_responsive_control(
						'ts_number_btn_size',
						[
							'label' => __( 'Button size', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
									'step' => 1,
								],
								'%' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ts-order-head .ts-icon-btn' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_number_btn_color',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-order-head .ts-icon-btn i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-order-head .ts-icon-btn svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_number_btn_icon_size',
						[
							'label' => __( 'Button icon size', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
									'step' => 1,
								],
								'%' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ts-order-head .ts-icon-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-order-head .ts-icon-btn svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_number_btn_bg',
						[
							'label' => __( 'Button background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-order-head .ts-icon-btn' => 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_number_btn_border',
							'label' => __( 'Button border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-order-head .ts-icon-btn',
						]
					);

					$this->add_responsive_control(
						'ts_number_btn_radius',
						[
							'label' => __( 'Button border radius', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
									'step' => 1,
								],
								'%' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ts-order-head .ts-icon-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);


				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'pg_icon_button_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'ts_popup_number_btn_h',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-order-head .ts-icon-btn:hover i' => 'color: {{VALUE}};',
								'{{WRAPPER}} .ts-order-head .ts-icon-btn:hover svg' => 'fill: {{VALUE}};',
							],

						]
					);

					$this->add_control(
						'ts_number_btn_bg_h',
						[
							'label' => __( 'Button background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-order-head .ts-icon-btn:hover'
								=> 'background-color: {{VALUE}};',
							],

						]
					);

					$this->add_control(
						'ts_button_border_c_h',
						[
							'label' => __( 'Button border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-order-head .ts-icon-btn:hover'
								=> 'border-color: {{VALUE}};',
							],

						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_timeline_post_head',
			[
				'label' => __( 'Single request: Post Head', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

				$this->add_control(
					'ts_top_post',
					[
						'label' => __( 'Post head', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_responsive_control(
					'ts_top_post_avatar_size',
					[
						'label' => __( 'Avatar size', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px'],
						'range' => [
							'px' => [
								'min' => 16,
								'max' => 60,
								'step' => 1,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .ts-single-status .ts-status-avatar, {{WRAPPER}} .ts-system-ico' => 'min-width: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'ts_top_post_avatar_radius',
					[
						'label' => __( 'Avatar radius', 'voxel-elementor' ),
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
							'{{WRAPPER}} .ts-single-status .ts-status-avatar,{{WRAPPER}} .ts-system-ico' => 'border-radius: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'ts_top_post_margin',
					[
						'label' => __( 'Avatar right margin', 'voxel-elementor' ),
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
							'{{WRAPPER}} .ts-single-status' => 'grid-gap: {{SIZE}}{{UNIT}};',
						],
					]
				);
				$this->add_control(
					'ts_post_auth',
					[
						'label' => __( 'Author', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' => 'ts_top_post_name',
						'label' => __( 'Typography', 'voxel-elementor' ),
						'selector' => '{{WRAPPER}} .ts_status-author',
					]
				);

				$this->add_control(
					'ts_top_post_name_color',
					[
						'label' => __( 'Color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts_status-author' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'ts_top_post_name_color_h',
					[
						'label' => __( 'Color (Hover)', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts_status-author:hover' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'ts_post_meta',
					[
						'label' => __( 'Details', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);


				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' => 'ts_top_post_details',
						'label' => __( 'Typography', 'voxel-elementor' ),
						'selector' => '{{WRAPPER}} .ts-social-feed .ts-status-head > div > *',
					]
				);

				$this->add_control(
					'ts_top_post_name_details_color',
					[
						'label' => __( 'Details color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-social-feed .ts-status-head > div > *' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
				'ts_robot',
				[
					'label' => __( 'System icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
				);

				$this->add_control(
					'ts_robot_color',
					[
						'label' => __( 'Color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-system-ico i' => 'color: {{VALUE}}',
							'{{WRAPPER}} .ts-system-ico svg' => 'fill: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'ts_robot_background',
					[
						'label' => __( 'Background', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-system-ico' => 'background-color: {{VALUE}}',
						],
					]
				);


		$this->end_controls_section();

		$this->start_controls_section(
			'ts_timeline_body',
			[
				'label' => __( 'Single request: Post Body', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);


				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' => 'ts_post_body_typo',
						'label' => __( 'Typography', 'voxel-elementor' ),
						'selector' => '{{WRAPPER}} .ts-status-body > p,{{WRAPPER}} .ts-status-body > p > p',
					]
				);

				$this->add_control(
					'ts_post_body_color',
					[
						'label' => __( 'Text Color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-status-body > p,{{WRAPPER}} .ts-status-body > p > p' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'ts_post_body_link_color',
					[
						'label' => __( 'Link Color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-status-body > p a' => 'color: {{VALUE}}',
						],
					]
				);


		$this->end_controls_section();


		$this->start_controls_section(
			'ts_req_approve',
			[
				'label' => __( 'Single request: Approve', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'ts_approve_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'ts_approve_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_approve_btn_typo',
							'label' => __( 'Button typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-approve-btn',
						]
					);

					$this->add_control(
						'ts_approve_padding',
						[
							'label' => __( 'Padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px'],
							'selectors' => [
								'{{WRAPPER}} .ts-approve-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_approve_btn_height',
						[
							'label' => __( 'Height', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-approve-btn' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);


					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_approve_btn_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-approve-btn',
						]
					);

					$this->add_responsive_control(
						'ts_approve_btn_radius',
						[
							'label' => __( 'Border radius', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
									'step' => 1,
								],
								'%' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'default' => [
								'unit' => 'px',
								'size' => 5,
							],
							'selectors' => [
								'{{WRAPPER}} .ts-approve-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);


					$this->add_responsive_control(
						'ts_approve_btn_c',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-approve-btn' => 'color: {{VALUE}}',
							],

						]
					);


					$this->add_responsive_control(
						'ts_approve_btn_bg',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-approve-btn' => 'background: {{VALUE}}',
							],

						]
					);



					$this->add_responsive_control(
						'ts_approve_btn_icon_size',
						[
							'label' => __( 'Icon size', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
									'step' => 1,
								],
								'%' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'default' => [
								'unit' => 'px',
								'size' => 24,
							],
							'selectors' => [
								'{{WRAPPER}} .ts-approve-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-approve-btn svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_approve_btn_icon_color',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-approve-btn i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-approve-btn svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_approve_icon_spacing',
						[
							'label' => __( 'Icon spacing', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-approve-btn i' => 'padding-right: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-approve-btn svg' => 'margin-right: {{SIZE}}{{UNIT}};',
							],
						]
					);





				$this->end_controls_tab();

				/* Hover tab */

				$this->start_controls_tab(
					'ts_approve_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_responsive_control(
						'ts_approve_btn_c_h',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-approve-btn:hover' => 'color: {{VALUE}}',
							],

						]
					);


					$this->add_responsive_control(
						'ts_approve_h',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-approve-btn:hover' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_approve_btn_icon_color_h',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-approve-btn:hover i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-approve-btn:hover svg' => 'fill: {{VALUE}}',
							],

						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();





		$this->start_controls_section(
			'ts_single_status',
			[
				'label' => __( 'Single request: Order status', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);



				$this->add_responsive_control(
					'ts_review_padding',
					[
						'label' => __( 'Padding', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'selectors' => [
							'{{WRAPPER}} .ts-inner-status' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
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
							'{{WRAPPER}} .ts-inner-status i' => 'font-size: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .ts-inner-status svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
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
							'{{WRAPPER}} .ts-inner-status i, {{WRAPPER}} .ts-inner-status svg' => 'margin-right: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'ts_review_radius',
					[
						'label' => __( 'Border radius', 'voxel-elementor' ),
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
							'{{WRAPPER}} .ts-inner-status' => 'border-radius: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' => 'ts_review_typo',
						'label' => __( 'Typography', 'voxel-elementor' ),
						'selector' => '{{WRAPPER}} .ts-inner-status p',
					]
				);


		$this->end_controls_section();

		$this->start_controls_section(
			'ts_single_cards',
			[
				'label' => __( 'Single request: Order cards', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'ts_link_padding',
				[
					'label' => __( 'Padding', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .ts-order-card > ul' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);


			$this->add_control(
				'ts_card_bg',
				[
					'label' => __( 'Background color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-order-card' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'ts_card_border',
					'label' => __( 'Border', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-order-card',
				]
			);

			$this->add_responsive_control(
				'ts_card_border_radius',
				[
					'label' => __( 'Border radius', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ts-order-card' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'ts_card_border_shadow',
					'label' => __( 'Box Shadow', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-order-card',
				]
			);

			$this->add_control(
				'ts_sc_icon',
				[
					'label' => __( 'Icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'ts_sc_icon_size',
				[
					'label' => __( 'Icon size', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px'],
					'range' => [
						'px' => [
							'min' => 20,
							'max' => 50,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ts-card-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ts-card-icon svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'ts_sc_icon_color',
				[
					'label' => __( 'Icon color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-card-icon i' => 'color: {{VALUE}};',
						'{{WRAPPER}} .ts-card-icon svg' => 'fill: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'ts_sc_icon_bg',
				[
					'label' => __( 'Icon background color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-card-icon' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'ts_sc_icon_radius',
				[
					'label' => __( 'Border radius', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px'],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 50,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ts-card-icon' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'ts_sc_icon_margin',
				[
					'label' => __( 'Right margin', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px'],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 50,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ts-order-card ul' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'ts_sc_icon_shadow',
					'label' => __( 'Box Shadow', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-card-icon',
				]
			);

			$this->add_control(
				'ts_sc_primary',
				[
					'label' => __( 'Primary text', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'ts_sc_primary_typo',
					'label' => __( 'Typography', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-order-card ul p',
				]
			);

			$this->add_control(
				'ts_sc_primary_color',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-order-card ul p' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'ts_sc_secondary',
				[
					'label' => __( 'Secondary text', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'ts_sc_secondary_typo',
					'label' => __( 'Typography', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-order-card ul small',
				]
			);

			$this->add_control(
				'ts_sc_secondary_color',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-order-card ul small' => 'color: {{VALUE}};',
					],
				]
			);





		$this->end_controls_section();

		$this->start_controls_section(
			'ts_comment_divider',
			[
				'label' => __( 'Single request: Comment divider', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'ts_divider_circle',
				[
					'label' => __( 'Circle', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'ts_circle_size',
				[
					'label' => __( 'Size', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px'],
					'range' => [
						'px' => [
							'min' => 20,
							'max' => 50,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ts-divider>div:after,{{WRAPPER}} .ts-divider>div:before' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'ts_circle_bg',
				[
					'label' => __( 'Background color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-divider>div:after,{{WRAPPER}} .ts-divider>div:before' => 'background: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'ts_divider_line',
				[
					'label' => __( 'Line', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'ts_div_line_width',
				[
					'label' => __( 'Width', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px'],
					'range' => [
						'px' => [
							'min' => 20,
							'max' => 50,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ts-divider > div' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'ts_div_line_height',
				[
					'label' => __( 'Height', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px'],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 250,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ts-divider > div' => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'ts_div_line_bg',
				[
					'label' => __( 'Background color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-divider > div' => 'background-color: {{VALUE}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_single_files',
			[
				'label' => __( 'Single request: Attachments', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'ts_att_list_margin',
				[
					'label' => __( 'Attachment gap', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px'],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 50,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ts-status-attachments > ul' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'ts_att_icon_size',
				[
					'label' => __( 'Icon size', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px'],
					'range' => [
						'px' => [
							'min' => 20,
							'max' => 50,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ts-status-attachments a i' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ts-status-attachments a svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'ts_att_icon_color',
				[
					'label' => __( 'Icon color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-status-attachments a i' => 'color: {{VALUE}};',
						'{{WRAPPER}} .ts-status-attachments a svg' => 'fill: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'ts_att_icon_color_h',
				[
					'label' => __( 'Icon color (Hover)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-status-attachments a:hover i' => 'color: {{VALUE}};',
						'{{WRAPPER}} .ts-status-attachments a:hover svg' => 'fill: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'ts_att_icon_margin',
				[
					'label' => __( 'Icon right margin', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px'],
					'range' => [
						'px' => [
							'min' => 20,
							'max' => 50,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ts-status-attachments a i, {{WRAPPER}} .ts-status-attachments a svg' => 'margin-right: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'ts_att_typo',
					'label' => __( 'Typography', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-status-attachments a span',
				]
			);

			$this->add_control(
				'ts_att_color',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-status-attachments a span' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'ts_att_color_h',
				[
					'label' => __( 'Color (Hover)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-status-attachments a:hover span' => 'color: {{VALUE}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_single_accordion',
			[
				'label' => __( 'Single request: Accordion', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'ts_acc_bg',
				[
					'label' => __( 'Background', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .order-info-container' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'ts_acc_border',
					'label' => __( 'Border', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .order-info-container',
				]
			);


			$this->add_responsive_control(
				'ts_acc_border_radius',
				[
					'label' => __( 'Border radius', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .order-info-container' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'ts_acc_shadow',
					'label' => __( 'Box Shadow', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .order-info-container',
				]
			);


			$this->add_control(
				'ts_acc_head',
				[
					'label' => __( 'Head', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'ts_acc_head_padding',
				[
					'label' => __( 'Padding', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .order-info-head' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'acc_head_typo',
					'label' => __( 'Typography', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .order-info-head p',
				]
			);

			$this->add_control(
				'acc_head_text',
				[
					'label' => __( 'Text Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .order-info-head p' => 'color: {{VALUE}}',
					],
				]
			);



			$this->add_control(
				'acc_head_icon',
				[
					'label' => __( 'Icon Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .order-info-head > i' => 'color: {{VALUE}}',
						'{{WRAPPER}} .order-info-head > svg' => 'fill: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'acc_head_icon_size',
				[
					'label' => __( 'Icon size', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px'],
					'range' => [
						'px' => [
							'min' => 20,
							'max' => 50,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .order-info-head > i' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .order-info-head > svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'ts_acc_body',
				[
					'label' => __( 'Body', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'acc_body_pad',
				[
					'label' => __( 'Row padding', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px' ],
					'selectors' => [
						'{{WRAPPER}} .order-info-container > ul > li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'acc_body_row_small',
					'label' => __( 'Row title', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .order-info-container > ul > li small',
				]
			);

			$this->add_control(
				'acc_body_row_small_c',
				[
					'label' => __( 'Row title color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .order-info-container > ul > li small' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'acc_body_row_text',
					'label' => __( 'Row value', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .order-info-container > ul > li p',
				]
			);

			$this->add_control(
				'acc_body_row_text_c',
				[
					'label' => __( 'Row value color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .order-info-container > ul > li p' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'acc_row_border',
				[
					'label' => __( 'Border color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .order-info-container > ul > li' => 'border-color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'acc_row_border_width',
				[
					'label' => __( 'Border width', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .order-info-container > ul > li' => 'border-width: {{SIZE}}{{UNIT}};',
					],
				]
			);



		$this->end_controls_section();

		$this->start_controls_section(
			'acc_buttons',
			[
				'label' => __( 'Single request: Accordion buttons', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'acc_buttons_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'acc_buttons_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);


					$this->add_responsive_control(
						'acc_btn_size',
						[
							'label' => __( 'Button size', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
									'step' => 1,
								],
								'%' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ts-icon-btn.ts-smaller' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'acc_btn_color',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-icon-btn.ts-smaller i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-icon-btn.ts-smaller svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'acc_btn_icon_size',
						[
							'label' => __( 'Button icon size', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
									'step' => 1,
								],
								'%' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ts-icon-btn.ts-smaller i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-icon-btn.ts-smaller svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'acc_btn_bg',
						[
							'label' => __( 'Button background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-icon-btn.ts-smaller' => 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'acc_btn_border',
							'label' => __( 'Button border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-icon-btn.ts-smaller',
						]
					);

					$this->add_responsive_control(
						'ts_acc_btn_radius',
						[
							'label' => __( 'Button border radius', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
									'step' => 1,
								],
								'%' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ts-icon-btn.ts-smaller' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);




				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'acc_buttons_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'acc_btn_h',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-icon-btn.ts-smaller:hover i' => 'color: {{VALUE}};',
								'{{WRAPPER}} .ts-icon-btn.ts-smaller:hover svg' => 'fill: {{VALUE}};',
							],

						]
					);

					$this->add_control(
						'acc_btn_bg_h',
						[
							'label' => __( 'Button background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-icon-btn.ts-smaller:hover'
								=> 'background-color: {{VALUE}};',
							],

						]
					);

					$this->add_control(
						'acc_button_border_c_h',
						[
							'label' => __( 'Button border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-icon-btn.ts-smaller:hover'
								=> 'border-color: {{VALUE}};',
							],

						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_req_comments',
			[
				'label' => __( 'Single request: Post a comment button', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'ts_reqc_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'ts_reqc_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);



					$this->add_responsive_control(
						'ts_reqc_input_padding',
						[
							'label' => __( 'Padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .ts-add-status .ts-filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_reqc_input_height',
						[
							'label' => __( 'Height', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
									'step' => 1,
								],
								'%' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'default' => [
								'unit' => 'px',
								'size' => 50,
							],
							'selectors' => [
								'{{WRAPPER}} .ts-add-status .ts-filter' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);





					$this->add_responsive_control(
						'ts_reqc_input_bg',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-add-status .ts-filter' => 'background: {{VALUE}}',
							],

						]
					);


					$this->add_responsive_control(
						'ts_reqc_input_value_col',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-add-status .ts-filter-text' => 'color: {{VALUE}}',
							],

						]
					);


					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_reqc_input_input_typo',
							'label' => __( 'Typography' ),
							'selector' => '{{WRAPPER}} .ts-add-status .ts-filter',
						]
					);



					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_reqc_input_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-add-status .ts-filter',
						]
					);




					$this->add_responsive_control(
						'ts_reqc_input_radius',
						[
							'label' => __( 'Border radius', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
									'step' => 1,
								],
								'%' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'default' => [
								'unit' => 'px',
								'size' => 5,
							],
							'selectors' => [
								'{{WRAPPER}} .ts-add-status .ts-filter' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_reqc_input_shadow',
							'label' => __( 'Box Shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-add-status .ts-filter',
						]
					);

					$this->add_responsive_control(
						'ts_reqc_input_icon_col',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-add-status .ts-filter i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-add-status .ts-filter svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_reqc_input_icon_size',
						[
							'label' => __( 'Icon size', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
									'step' => 1,
								],
								'%' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'default' => [
								'unit' => 'px',
								'size' => 24,
							],
							'selectors' => [
								'{{WRAPPER}} .ts-add-status .ts-filter i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-add-status .ts-filter svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_reqc_input_icon_margin',
						[
							'label' => __( 'Icon / Text spacing', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,
									'step' => 1,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ts-add-status .ts-filter' => 'grid-gap: {{SIZE}}{{UNIT}};',

							],
						]
					);






				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'ts_reqc_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_responsive_control(
						'ts_reqc_input_value_col_H',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-add-status .ts-filter .ts-filter-text' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_reqc_input_bg_h',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-add-status .ts-filter:hover' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_reqc_input_border_h',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-add-status .ts-filter:hover' => 'border-color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_reqc_input_shadow_h',
							'label' => __( 'Box Shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-add-status .ts-filter:hover',
						]
					);

				$this->end_controls_tab();


			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_item_Tags',
			[
				'label' => __( 'Single request: Tags', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'ts_tags_justify',
				[
					'label' => __( 'Justify', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'left'  => __( 'Left', 'voxel-elementor' ),
						'center' => __( 'Center', 'voxel-elementor' ),
						'flex-end' => __( 'Right', 'voxel-elementor' ),
						'space-between' => __( 'Space between', 'voxel-elementor' ),
						'space-around' => __( 'Space around', 'voxel-elementor' ),
					],
					'selectors' => [
						'{{WRAPPER}} .tci-labels' => 'justify-content: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'ts_tags_gap',
				[
					'label' => __( 'Item gap', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tci-labels' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'ts_tags_padding',
				[
					'label' => __( 'Item padding', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .tci-labels li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'ts_tags_typo',
					'label' => __( 'Typography', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .tci-labels li',
				]
			);

			$this->add_responsive_control(
				'ts_tags_bg',
				[
					'label' => __( 'Default background', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tci-labels li' => 'background: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'ts_tags_bg_h',
				[
					'label' => __( 'Default background (Hover)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tci-labels li:hover' => 'background: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'ts_tags_col',
				[
					'label' => __( 'Default color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tci-labels li' => 'color: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'ts_tags_col_h',
				[
					'label' => __( 'Default color (Hover)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tci-labels li:hover' => 'color: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'ts_tags_radius',
				[
					'label' => __( 'Border radius', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tci-labels li' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'custom_popup',
			[
				'label' => __( 'Popups: Custom style', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'custom_popup_enable',
				[
					'label' => __( 'Enable custom style', 'voxel-elementor' ),
					'description' => __( 'In wp-admin > templates > Style kits > Popup styles you can control the global popup styles that affect all the popups on the site. Enabling this option will override some of those styles only for this specific widget.', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'Yes', 'voxel-elementor' ),
					'label_off' => __( 'No', 'voxel-elementor' ),
				]
			);



			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'pg_shadow',
					'label' => __( 'Box Shadow', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-field-popup',
					'condition' => [ 'custom_popup_enable' => 'yes' ],
				]
			);

			$this->add_responsive_control(
				'custom_pg_top_margin',
				[
					'label' => __( 'Top / Bottom margin', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'description' => __( 'Does not affect mobile', 'voxel-elementor' ),
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 200,
							'step' => 1,
						],
					],
					'condition' => [ 'custom_popup_enable' => 'yes' ],
					'selectors' => [
						'{{WRAPPER}} .ts-field-popup-container' => 'margin: {{SIZE}}{{UNIT}} 0;',
					],
				]
			);







		$this->end_controls_section();

				$this->start_controls_section(
					'custom_popup_status',
					[
						'label' => __( 'Custom popup: Create status', 'voxel-elementor' ),
						'tab' => \Elementor\Controls_Manager::TAB_STYLE,
						'condition' => [ 'custom_popup_enable' => 'yes' ],
					]
				);

					$this->add_responsive_control(
						'popups_center_position',
						[
							'label' => __( 'Switch position to center of screen', 'voxel-elementor' ),
							'description' => __( 'This option will display the primary popups on the center of the screen', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SWITCHER,
							'condition' => [ 'custom_popup_enable' => 'yes' ],
							'return_value' => 'static',
							'selectors' => [

								'(desktop) .prmr-popup{{WRAPPER}}-wrap' => 'position: fixed !important;',
								'(desktop) .prmr-popup{{WRAPPER}}.ts-form ' => 'position: static !important;
		    						max-width: initial; width: auto !important;',
							],
						]
					);


					$this->add_control(
						'custm_pg_backdrop',
						[
							'label' => __( 'Backdrop background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'condition' => [ 'custom_popup_enable' => 'yes' ],
							'selectors' => [
								'.prmr-popup{{WRAPPER}}-wrap > div:after' => 'background-color: {{VALUE}} !important',
							],
						]
					);

					$this->add_control(
						'popup_pointer_events',
						[
							'label' => __( 'Enable pointer events for backdrop?', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SWITCHER,
							'return_value' => 'none',
							'selectors' => [
								'{{WRAPPER}}-wrap > div:after' => 'pointer-events:all;',
							],
							'condition' => [ 'custom_popup_enable' => 'yes' ],
						]
					);

					$this->add_control(
						'custom_pg_width',
						[
							'label' => __( 'Min width', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'description' => __( 'Does not affect mobile', 'voxel-elementor' ),
							'size_units' => [ 'px' ],
							'range' => [
								'px' => [
									'min' => 200,
									'max' => 800,
									'step' => 1,
								],
							],
							'condition' => [ 'custom_popup_enable' => 'yes' ],
							'selectors' => [
								'.prmr-popup{{WRAPPER}} .ts-field-popup' => 'min-width: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'custom_max_width',
						[
							'label' => __( 'Max width', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'description' => __( 'Does not affect mobile', 'voxel-elementor' ),
							'size_units' => [ 'px' ],
							'range' => [
								'px' => [
									'min' => 200,
									'max' => 800,
									'step' => 1,
								],
							],
							'condition' => [ 'custom_popup_enable' => 'yes' ],
							'selectors' => [
								'.prmr-popup{{WRAPPER}} .ts-field-popup' => 'max-width: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'custom_max_height',
						[
							'label' => __( 'Max height', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'description' => __( 'Does not affect mobile', 'voxel-elementor' ),
							'size_units' => [ 'px' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 800,
									'step' => 1,
								],
							],
							'condition' => [ 'custom_popup_enable' => 'yes' ],
							'selectors' => [
								'.prmr-popup{{WRAPPER}} .ts-popup-content-wrapper' => 'max-height: {{SIZE}}{{UNIT}};',
							],
						]
					);

				$this->end_controls_section();


		$this->start_controls_section(
			'ts_order_filter_icons',
			[
				'label' => __( 'Requests: Icons', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);



			$this->add_control(
				'ts_all_requests',
				[
					'label' => __( 'All requests', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_arrow_left',
				[
					'label' => __( 'Arrow left icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_arrow_right',
				[
					'label' => __( 'Arrow right icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_status',
				[
					'label' => __( 'Order status icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_order_keyword',
				[
					'label' => __( 'Text search', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_qr_ico',
				[
					'label' => __( 'QR code', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_calendar_icon',
				[
					'label' => __( 'Calendar icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,

				]
			);

			$this->add_control(
				'ts_clock_icon',
				[
					'label' => __( 'Clock icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,

				]
			);


			$this->add_control(
				'ts_sr_more',
				[
					'label' => __( 'More icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_price_ico',
				[
					'label' => __( 'Price icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);


			$this->add_control(
				'ts_order_comment',
				[
					'label' => __( 'Post comment icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_order_ico',
				[
					'label' => __( 'Order icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_user_ico',
				[
					'label' => __( 'User icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_down_ico',
				[
					'label' => __( 'Down arrow icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_upload_ico',
				[
					'label' => __( 'Upload icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_system_ico',
				[
					'label' => __( 'System icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'approve_icon',
				[
					'label' => __( 'Approve icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'info_icon',
				[
					'label' => __( 'Request details icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);




		$this->end_controls_section();

	}

	protected function render( $instance = [] ) {
		if ( ! is_user_logged_in() ) {
			printf( '<p class="ts-restricted">%s</p>', __( 'You must be logged in to view this content.', 'voxel' ) );
			return;
		}

		$config = [
			'activeOrder' => absint( $_GET['order_id'] ?? null ),
			'actions' => $this->get_user_actions(),
			'backButton' => $this->_get_back_button_target(),
			'l10n' => [
				'attachImages' => _x( 'Attach images', 'orders', 'voxel-elementor' ),
				'attachFiles' => _x( 'Attach files', 'orders', 'voxel-elementor' ),
				'filesDelivered' => _x( 'Your file(s) have been delivered.', 'orders', 'voxel-elementor' ),
			],
		];

		$config['statuses'] = [
			'all' => _x( 'All', 'order status', 'voxel' ),
			'completed' => _x( 'Completed', 'order status', 'voxel' ),
			'pending_approval' => _x( 'Pending Approval', 'order status', 'voxel' ),
			'refund_requested' => _x( 'Refund Requested', 'order status', 'voxel' ),
			'refunded' => _x( 'Refunded', 'order status', 'voxel' ),
			'declined' => _x( 'Declined', 'order status', 'voxel' ),
			'canceled' => _x( 'Canceled', 'order status', 'voxel' ),
			'subscriptions' => [
				'type' => 'subheading',
				'label' => _x( 'Subscriptions', 'orders', 'voxel' ),
			],
			'sub_active' => _x( 'Active', 'order status', 'voxel' ),
			'sub_trialing' => _x( 'Trialing', 'order status', 'voxel' ),
			'sub_past_due' => _x( 'Past due', 'order status', 'voxel' ),
			'sub_incomplete' => _x( 'Incomplete', 'order status', 'voxel' ),
			'sub_incomplete_expired' => _x( 'Expired', 'order status', 'voxel' ),
			'sub_canceled' => _x( 'Canceled', 'order status', 'voxel' ),
			'sub_unpaid' => _x( 'Unpaid', 'order status', 'voxel' ),
		];

		wp_print_styles( $this->get_style_depends() );
		require locate_template( 'templates/widgets/orders.php' );

		if ( \Voxel\is_edit_mode() ) {
           printf( '<script type="text/javascript">%s</script>', 'window.render_orders();' );
        }
	}

	protected function _get_back_button_target() {
		if ( ( $_GET['ref'] ?? '' ) === 'calendar' ) {
			$link = get_permalink( \Voxel\get('templates.reservations') ) ?: home_url('/');
			$referer = wp_get_referer();
			if ( $referer && substr( $referer, 0, mb_strlen( $link ) ) === $link ) {
				return 'history-back';
			}
		}

		return 'all-orders';
	}

	public function get_script_depends() {
		return [ 'vx:orders.js' ];
	}

	public function get_style_depends() {
		return [ 'vx:forms.css', 'vx:orders.css', 'vx:social-feed.css' ];
	}

	public function get_user_actions(): array {
		$actions = [
			'author.approve' => [
				'label' => _x( 'Approve', 'single order', 'voxel' ),
				'icon' => \Voxel\get_icon_markup( $this->get_settings('approve_icon') ) ?: \Voxel\svg( 'checkmark-circle.svg', false ),
			],
			'author.decline' => [
				'label' => _x( 'Decline', 'single order', 'voxel' ),
				'icon' => \Voxel\get_icon_markup( $this->get_settings('ts_generic_ico') ) ?: \Voxel\svg( 'cross-circle.svg', false ),
			],
			'author.approve_refund' => [
				'label' => _x( 'Approve refund', 'single order', 'voxel' ),
				'icon' => \Voxel\get_icon_markup( $this->get_settings('ts_generic_ico') ) ?: \Voxel\svg( 'checkmark-circle.svg', false ),
			],
			'author.decline_refund' => [
				'label' => _x( 'Decline refund', 'single order', 'voxel' ),
				'icon' => \Voxel\get_icon_markup( $this->get_settings('ts_generic_ico') ) ?: \Voxel\svg( 'cross-circle.svg', false ),
			],
			'customer.cancel' => [
				'label' => _x( 'Cancel order', 'single order', 'voxel' ),
				'icon' => \Voxel\get_icon_markup( $this->get_settings('ts_generic_ico') ) ?: \Voxel\svg( 'cross-circle.svg', false ),
			],
			'customer.request_refund' => [
				'label' => _x( 'Request a refund', 'single order', 'voxel' ),
				'icon' => \Voxel\get_icon_markup( $this->get_settings('ts_generic_ico') ) ?: \Voxel\svg( 'file.svg', false ),
			],
			'customer.cancel_refund_request' => [
				'label' => _x( 'Cancel refund request', 'single order', 'voxel' ),
				'icon' => \Voxel\get_icon_markup( $this->get_settings('ts_generic_ico') ) ?: \Voxel\svg( 'cross-circle.svg', false ),
			],
			/*'receipt' => [
				'label' => _x( 'View receipt', 'single order', 'voxel' ),
				'icon' => \Voxel\get_icon_markup( $this->get_settings('ts_generic_ico') ) ?: \Voxel\svg( 'invoice.svg', false ),
			],*/
			'customer.portal' => [
				'label' => _x( 'Customer portal', 'single order', 'voxel' ),
				'icon' => \Voxel\get_icon_markup( $this->get_settings('ts_generic_ico') ) ?: \Voxel\svg( 'link-alt.svg', false ),
			],
			'customer.subscriptions.reactivate' => [
				'label' => _x( 'Reactivate subscription', 'single order', 'voxel' ),
				'icon' => \Voxel\get_icon_markup( $this->get_settings('ts_generic_ico') ) ?: \Voxel\svg( 'file.svg', false ),
			],
			'customer.subscriptions.retry_payment' => [
				'label' => _x( 'Retry payment', 'single order', 'voxel' ),
				'icon' => \Voxel\get_icon_markup( $this->get_settings('ts_generic_ico') ) ?: \Voxel\svg( 'reload.svg', false ),
			],
			'customer.subscriptions.cancel' => [
				'label' => _x( 'Cancel subscription', 'single order', 'voxel' ),
				'icon' => \Voxel\get_icon_markup( $this->get_settings('ts_generic_ico') ) ?: \Voxel\svg( 'cross-circle.svg', false ),
			],
			'admin.sync_with_stripe' => [
				'label' => _x( 'Sync with Stripe', 'single order', 'voxel' ),
				'icon' => \Voxel\get_icon_markup( $this->get_settings('ts_generic_ico') ) ?: \Voxel\svg( 'reload.svg', false ),
			],
		];

		return $actions;
	}

	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}

	protected function register_runtime_controls() {
		$this->add_control(
			'ts_all_requests',
			[
				'label' => __( 'All requests', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_arrow_left',
			[
				'label' => __( 'Arrow left icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_arrow_right',
			[
				'label' => __( 'Arrow right icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_status',
			[
				'label' => __( 'Order status icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_order_keyword',
			[
				'label' => __( 'Text search', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_qr_ico',
			[
				'label' => __( 'QR code', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_calendar_icon',
			[
				'label' => __( 'Calendar icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,

			]
		);

		$this->add_control(
			'ts_clock_icon',
			[
				'label' => __( 'Clock icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,

			]
		);


		$this->add_control(
			'ts_sr_more',
			[
				'label' => __( 'More icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_price_ico',
			[
				'label' => __( 'Price icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);


		$this->add_control(
			'ts_order_comment',
			[
				'label' => __( 'Post comment icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_order_ico',
			[
				'label' => __( 'Order icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_user_ico',
			[
				'label' => __( 'User icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_down_ico',
			[
				'label' => __( 'Down arrow icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_upload_ico',
			[
				'label' => __( 'Upload icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_system_ico',
			[
				'label' => __( 'System icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'approve_icon',
			[
				'label' => __( 'Approve icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'info_icon',
			[
				'label' => __( 'Request details icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

	}
}
