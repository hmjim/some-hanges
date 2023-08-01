<?php

namespace Voxel\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Timeline extends Base_Widget {

	public function get_name() {
		return 'ts-timeline';
	}

	public function get_title() {
		return __( 'Timeline (VX)', 'voxel-elementor' );
	}

	public function get_icon() {
		return 'vxi vxi-news';
	}

	public function get_categories() {
		return [ 'voxel', 'basic' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'ts_timeline_settings',
			[
				'label' => __( 'Timeline settings', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control( 'ts_mode', [
				'label' => __( 'Display mode', 'voxel-elementor' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'user_feed',
				'options' => [
					'post_reviews' => 'Current post reviews',
					'post_wall' => 'Current post wall',
					'post_timeline' => 'Current post timeline',
					'author_timeline' => 'Current author timeline',
					'user_feed' => 'Logged-in user news feed',
				],
			] );

			$repeater = new \Elementor\Repeater;

			$repeater->add_control( 'ts_order', [
				'label' => __( 'Order', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'latest',
				'options' => [
					'latest' => __( 'Latest', 'voxel-elementor' ),
					'earliest' => __( 'Earliest', 'voxel-elementor' ),
					'most_liked' => __( 'Most liked', 'voxel-elementor' ),
					'most_discussed' => __( 'Most discussed', 'voxel-elementor' ),
					'most_popular' => __( 'Most popular (likes+comments)', 'voxel-elementor' ),
					'best_rated' => __( 'Best rated (reviews only)', 'voxel-elementor' ),
					'worst_rated' => __( 'Worst rated (reviews only)', 'voxel-elementor' ),
				],
			] );

			$repeater->add_control( 'ts_time', [
				'label' => __( 'Timeframe', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'all_time',
				'options' => [
					'today' => __( 'Today', 'voxel-elementor' ),
					'this_week' => __( 'This week', 'voxel-elementor' ),
					'this_month' => __( 'This month', 'voxel-elementor' ),
					'this_year' => __( 'This year', 'voxel-elementor' ),
					'all_time' => __( 'All time', 'voxel-elementor' ),
					'custom' => __( 'Custom', 'voxel-elementor' ),
				],
			] );

			$repeater->add_control( 'ts_time_custom', [
				'label' => __( 'Show items from the past number of days', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 7,
				'condition' => [ 'ts_time' => 'custom' ],
			] );

			$repeater->add_control( 'ts_label', [
				'label' => __( 'Label', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Latest',
			] );

			$this->add_control( 'ts_ordering_options', [
				'label' => __( 'Ordering options', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'_disable_loop' => true,
				'title_field' => '{{{ ts_label }}}',
			] );

			$this->add_control(
				'add_status_text',
				[
					'label' => __( 'Create status text', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'placeholder' => __( 'Type your text', 'voxel-elementor' ),
					'default' => esc_html__( 'Create status', 'voxel-elementor' ),
				]
			);

			$this->add_control(
				'no_status_text',
				[
					'label' => __( 'No posts text', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'No posts to show', 'voxel-elementor' ),
					'placeholder' => __( 'Type your text', 'voxel-elementor' ),
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_timeline_icons',
			[
				'label' => __( 'Timeline icons', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'ts_create_icon',
				[
					'label' => __( 'Create status icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);



			$this->add_control(
				'ts_post_footer_like_icon',
				[
					'label' => __( 'Like icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_post_footer_liked_icon',
				[
					'label' => __( 'Liked icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_post_footer_comment_icon',
				[
					'label' => __( 'Comment icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_post_footer_reply_icon',
				[
					'label' => __( 'Reply icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_post_footer_delete_icon',
				[
					'label' => __( 'Delete icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_post_footer_edit_icon',
				[
					'label' => __( 'Edit icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_timeline_load_icon',
				[
					'label' => __( 'Load more', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_more_icon',
				[
					'label' => __( 'More', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_close_ico',
				[
					'label' => __( 'Close icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);




		$this->end_controls_section();



		$this->start_controls_section(
			'ts_timeline_post_general',
			[
				'label' => __( 'Timeline: General', 'voxel-elementor' ),
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






			$this->add_responsive_control(
				'ts_status_bg',
				[
					'label' => __( 'Post background', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-status-list > .ts-single-status' => 'background: {{VALUE}}',
					],

				]
			);


			$this->add_responsive_control(
				'ts_status_padding',
				[
					'label' => __( 'Post padding', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .ts-status-list > .ts-single-status' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'ts_post_border',
					'label' => __( 'Post border', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-status-list > .ts-single-status',
				]
			);

			$this->add_responsive_control(
				'ts_status_radius',
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
						'{{WRAPPER}} .ts-status-list > .ts-single-status' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'ts_status_shadow',
					'label' => __( 'Box Shadow', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-status-list > .ts-single-status',
				]
			);




		$this->end_controls_section();

		$this->start_controls_section(
			'ts_timeline_tabs_section',
			[
				'label' => __( 'Timeline: Tabs', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'ts_timeline_el_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'ts_tabs_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);
					$this->add_control(
						'ts_timeline_tabs',
						[
							'label' => __( 'Timeline tabs', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_control(
						'ts_tabs_item__margin',
						[
							'label' => __( 'Margin', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],

							'selectors' => [
								'{{WRAPPER}} .ts-timeline-tabs' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_timeline_tab_item',
						[
							'label' => __( 'Tab item', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_control(
						'ts_tabs_justify',
						[
							'label' => __( 'Justify', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SELECT,
							'default' => 'left',
							'options' => [
								'left'  => __( 'Left', 'voxel-elementor' ),
								'center' => __( 'Center', 'voxel-elementor' ),
								'right' => __( 'Right', 'voxel-elementor' ),
								'space-between' => __( 'Space between', 'voxel-elementor' ),
								'space-around' => __( 'Space around', 'voxel-elementor' ),
							],

							'selectors' => [
								'{{WRAPPER}} .ts-generic-tabs' => 'justify-content: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'ts_tabs_padding',
						[
							'label' => __( 'Padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .ts-generic-tabs li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_tabs_margin',
						[
							'label' => __( 'Margin', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],

							'selectors' => [
								'{{WRAPPER}} .ts-generic-tabs li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_tabs_text',
							'label' => __( 'Tab typography' ),
							'selector' => '{{WRAPPER}} .ts-generic-tabs li a',
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_tabs_text_active',
							'label' => __( 'Active tab typography' ),
							'selector' => '{{WRAPPER}} .ts-generic-tabs li.ts-tab-active a',
						]
					);


					$this->add_control(
						'ts_tabs_text_color',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-generic-tabs li a' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_active_text_color',
						[
							'label' => __( 'Active text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-generic-tabs li.ts-tab-active a' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_tabs_bg_color',
						[
							'label' => __( 'Background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-generic-tabs li a' => 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_tabs_bg_active_color',
						[
							'label' => __( 'Active background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-generic-tabs li.ts-tab-active a' => 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_tabs_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-generic-tabs li a',
						]
					);

					$this->add_control(
						'ts_tabs_border_active',
						[
							'label' => __( 'Active border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-generic-tabs li.ts-tab-active a' => 'border-color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_tabs_radius',
						[
							'label' => __( 'Border radius', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-generic-tabs li a' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);


				$this->end_controls_tab();

				/* Hover tab */

				$this->start_controls_tab(
					'ts_tabs_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'ts_timeline_tabs_h',
						[
							'label' => __( 'Timeline tabs', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_control(
						'ts_tabs_text_color_h',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-generic-tabs li a:hover' => 'color: {{VALUE}}',
							],

						]
					);



					$this->add_control(
						'ts_tabs_active_text_color_h',
						[
							'label' => __( 'Active text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-generic-tabs li.ts-tab-active a:hover' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_tabs_border_color_h',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-generic-tabs li a:hover' => 'border-color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_tabs_border_h_active',
						[
							'label' => __( 'Active border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-generic-tabs li.ts-tab-active a:hover' => 'border-color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_tabs_bg_color_h',
						[
							'label' => __( 'Background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-generic-tabs li a:hover' => 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_bg_active_color_h',
						[
							'label' => __( 'Active background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-generic-tabs li.ts-tab-active a:hover' => 'background-color: {{VALUE}}',
							],

						]
					);


				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_sf_styling_filters',
			[
				'label' => __( 'Timeline: Post/comment button', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'ts_sf_filters_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'ts_sf_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);



					$this->add_responsive_control(
						'ts_sf_input_padding',
						[
							'label' => __( 'Padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .ts-form .ts-filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_sf_input_height',
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
								'{{WRAPPER}} .ts-form .ts-filter' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);





					$this->add_responsive_control(
						'ts_sf_input_bg',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form .ts-filter' => 'background: {{VALUE}}',
							],

						]
					);


					$this->add_responsive_control(
						'ts_sf_input_value_col',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-filter-text' => 'color: {{VALUE}}',
							],

						]
					);


					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_sf_input_input_typo',
							'label' => __( 'Typography' ),
							'selector' => '{{WRAPPER}} .ts-form .ts-filter',
						]
					);



					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_sf_input_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-filter',
						]
					);




					$this->add_responsive_control(
						'ts_sf_input_radius',
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
								'{{WRAPPER}} .ts-form .ts-filter' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_sf_input_shadow',
							'label' => __( 'Box Shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-form .ts-filter',
						]
					);

					$this->add_responsive_control(
						'ts_sf_input_icon_col',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-filter i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-filter svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_sf_input_icon_size',
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
								'{{WRAPPER}} .ts-filter i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-filter svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_sf_input_icon_margin',
						[
							'label' => __( 'Icon / Text spacing', 'voxel-elementor' ),
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
								'size' => 10,
							],
							'selectors' => [
								'{{WRAPPER}} .ts-filter' => 'grid-gap: {{SIZE}}{{UNIT}};',
							],
						]
					);



					$this->add_control(
						'ts_supdate_bottom_space',
						[
							'label' => __( 'Bottom spacing (Create status)', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-social-feed .ts-add-status' => 'margin-bottom: {{SIZE}}{{UNIT}};',
							],
						]
					);




				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'ts_sf_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_responsive_control(
						'ts_sf_input_value_col_H',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form .ts-filter:hover .ts-filter-text' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_sf_input_bg_h',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form .ts-filter:hover' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_sf_input_border_h',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form .ts-filter:hover' => 'border-color: {{VALUE}}',
							],

						]
					);

				$this->end_controls_tab();


			$this->end_controls_tabs();

		$this->end_controls_section();

		/*
		==========
		No posts
		==========
		*/

		$this->start_controls_section(
			'ts_no_posts',
			[
				'label' => __( 'Timeline: Loading/No posts', 'voxel-elementor' ),
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

			$this->add_control(
				'ts_nopost_heading',
				[
					'label' => __( 'No posts', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
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
				'ts_nopost_spacing',
				[
					'label' => __( 'Item spacing', 'voxel-elementor' ),
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

		$this->end_controls_section();


		$this->start_controls_section(
			'ts_timeline_post_head',
			[
				'label' => __( 'Post: Head', 'voxel-elementor' ),
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
							'{{WRAPPER}} .ts-single-status .ts-status-avatar' => 'min-width: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
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
							'{{WRAPPER}} .ts-single-status .ts-status-avatar' => 'border-radius: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'ts_top_post_margin',
					[
						'label' => __( 'Avatar / Text spacing', 'voxel-elementor' ),
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
						'selector' => '{{WRAPPER}} .ts-social-feed .ts-status-head > div span, {{WRAPPER}} .ts-social-feed .ts-status-head > div a',
					]
				);

				$this->add_control(
					'ts_top_post_name_details_color',
					[
						'label' => __( 'Details color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-social-feed .ts-status-head > div  span, {{WRAPPER}} .ts-social-feed .ts-status-head > div a' => 'color: {{VALUE}}',
						],
					]
				);


		$this->end_controls_section();

		$this->start_controls_section(
			'ts_timeline_body',
			[
				'label' => __( 'Post: Body', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);


				$this->add_control(
					'ts_post_body_text',
					[
						'label' => __( 'Text', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
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
						'label' => __( 'Color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-status-body > p,{{WRAPPER}} .ts-status-body > p > p' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'ts_post_lnk',
					[
						'label' => __( 'Links', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					'ts_post_body_link_color',
					[
						'label' => __( 'Color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-status-body > p a' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'ts_post_body_link_color_h',
					[
						'label' => __( 'Color (Hover)', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-status-body > p a:hover' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' => 'ts_post_body_link_typo',
						'label' => __( 'Typography', 'voxel-elementor' ),
						'selector' => '{{WRAPPER}} .ts-status-body > p a',
					]
				);

				$this->add_control(
					'ts_post_text',
					[
						'label' => __( 'Read more toggle', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					'ts_more_col',
					[
						'label' => __( 'Color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} span.ts-content-toggle' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'ts_more_col_h',
					[
						'label' => __( 'Color (Hover)', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} span.ts-content-toggle:hover' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' => 'ts_more_typo',
						'label' => __( 'Link typography', 'voxel-elementor' ),
						'selector' => '{{WRAPPER}} span.ts-content-toggle',
					]
				);

				$this->add_control(
					'ts_post_body_gall',
					[
						'label' => __( 'Images', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_responsive_control( 'tml_image_ratio', [
					'label' => __( 'Aspect ratio', 'voxel-backend' ),
					'description' => __( 'Set image aspect ratio e.g 16/9', 'voxel-backend' ),
					'type' => \Elementor\Controls_Manager::TEXT,

					'selectors' => [
						'{{WRAPPER}} .ts-status-body img' => 'aspect-ratio: {{VALUE}};',
					],
				] );



				$this->add_control(
					'ts_post_body_radius',
					[
						'label' => __( 'Image radius', 'voxel-elementor' ),
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
							'{{WRAPPER}} .ts-status-body img' => 'border-radius: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'tml_image_contain',
					[
						'label' => __( 'Contain full image?', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,

						'selectors' => [
							'{{WRAPPER}} .ts-status-body img' => 'object-fit: contain;',
						],
					]
				);

				$this->add_control(
					'tml_image_contain_bg',
					[
						'label' => __( 'Container background', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-status-body img' => 'background-color: {{VALUE}}',
						],
					]
				);





		$this->end_controls_section();

		$this->start_controls_section(
			'ts_timeline_review',
			[
				'label' => __( 'Post: Reviews', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);


				$this->add_control(
					'ts_review_general',
					[
						'label' => __( 'General', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					'ts_review_padding',
					[
						'label' => __( 'Review padding', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'selectors' => [
							'{{WRAPPER}} .ts-review-score' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'ts_review_height',
					[
						'label' => __( 'Review height', 'voxel-elementor' ),
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
							'{{WRAPPER}} .ts-review-score' => 'height: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'ts_review_icon_size',
					[
						'label' => __( 'Review icon size', 'voxel-elementor' ),
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
							'{{WRAPPER}} .ts-review-score i' => 'font-size: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .ts-review-score svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
						],
					]
				);



				$this->add_control(
					'ts_review_icon_spacing',
					[
						'label' => __( 'Icon / Text spacing', 'voxel-elementor' ),
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
							'{{WRAPPER}} .ts-review-score' => 'grid-gap: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'ts_review_radius',
					[
						'label' => __( 'Review border radius', 'voxel-elementor' ),
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
							'{{WRAPPER}} .ts-review-score' => 'border-radius: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' => 'ts_review_typo',
						'label' => __( 'Review score typography', 'voxel-elementor' ),
						'selector' => '{{WRAPPER}} .ts-review-score p',
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
					'ts_review_excellent_bg',
					[
						'label' => __( 'Background color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-review-score.excellent' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'ts_review_excellent_icon_color',
					[
						'label' => __( 'Icon color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-review-score.excellent i' => 'color: {{VALUE}}',
							'{{WRAPPER}} .ts-review-score.excellent svg' => 'fill: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'ts_review_excellent_text_color',
					[
						'label' => __( 'Text color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-review-score.excellent p' => 'color: {{VALUE}}',
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
					'ts_review_very_good',
					[
						'label' => __( 'Very good', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					'ts_review_very_good_bg',
					[
						'label' => __( 'Background color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-review-score.very-good' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'ts_review_very_good_icon_color',
					[
						'label' => __( 'Icon color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-review-score.very-good i' => 'color: {{VALUE}}',
							'{{WRAPPER}} .ts-review-score.very-good svg' => 'fill: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'ts_review_very_good_text_color',
					[
						'label' => __( 'Text color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-review-score.very-good p' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'ts_review_very_good_icon',
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
					'ts_review_good_bg',
					[
						'label' => __( 'Background color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-review-score.good' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'ts_review_good_icon_color',
					[
						'label' => __( 'Icon color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-review-score.good i' => 'color: {{VALUE}}',
							'{{WRAPPER}} .ts-review-score.good svg' => 'fill: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'ts_review_good_text_color',
					[
						'label' => __( 'Text color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-review-score.good p' => 'color: {{VALUE}}',
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
					'ts_review_fair_bg',
					[
						'label' => __( 'Background color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-review-score.fair' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'ts_review_fair_icon_color',
					[
						'label' => __( 'Icon color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-review-score.fair i' => 'color: {{VALUE}}',
							'{{WRAPPER}} .ts-review-score.fair svg' => 'fill: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'ts_review_fair_text_color',
					[
						'label' => __( 'Text color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-review-score.fair p' => 'color: {{VALUE}}',
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
					'ts_review_poor_bg',
					[
						'label' => __( 'Background color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-review-score.poor' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'ts_review_poor_icon_color',
					[
						'label' => __( 'Icon color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-review-score.poor i' => 'color: {{VALUE}}',
							'{{WRAPPER}} .ts-review-score.poor svg' => 'fill: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'ts_review_poor_text_color',
					[
						'label' => __( 'Text color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-review-score.poor p' => 'color: {{VALUE}}',
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

		$this->start_controls_section(
			'ts_timeline_footer',
			[
				'label' => __( 'Post: Footer', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'ts_post_footer_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'ts_timeline_posts_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'ts_post_footer',
						[
							'label' => __( 'Post footer', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);



					$this->add_responsive_control(
						'ts_post_footer_icon_size',
						[
							'label' => __( 'Icon size', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px'],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 70,
									'step' => 1,
								],
							],

							'selectors' => [
								'{{WRAPPER}} .ts-status-footer > ul a i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-status-footer > ul a svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_footer_icon_spacing',
						[
							'label' => __( 'Item spacing', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-status-footer > ul' => 'grid-gap: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_post_footer_icon_color',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-status-footer > ul a i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-status-footer > ul a svg' => 'fill: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'ts_post_footer_liked_color',
						[
							'label' => __( 'Icon color (Liked state)', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-status-footer > ul a.ts-liked i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-status-footer > ul a.ts-liked svg' => 'fill: {{VALUE}}',
								'{{WRAPPER}} .ts-liked .ray:before' => 'background: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'disable_like_animation',
						[
							'label' => __( 'Disable like animation?', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SWITCHER,
							'return_value' => 'none',
							'selectors' => [
								'{{WRAPPER}} .ray-holder' => 'display: none;',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_post_footer_text',
							'label' => __( 'Text', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-status-footer > ul .ts-item-count',
						]
					);

					$this->add_control(
						'ts_post_footer_text_color',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-status-footer > ul .ts-item-count' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'ts_footer_text_spacing',
						[
							'label' => __( 'Text left margin', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-status-footer > ul .ts-item-count' => !is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
							],
						]
					);

				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'ts_timeline_posts_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);
					$this->add_control(
						'ts_post_footer_h',
						[
							'label' => __( 'Post footer', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);


					$this->add_control(
						'ts_post_footer_icon_color_h',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-status-footer > ul a:hover i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-status-footer > ul a:hover svg' => 'fill: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'ts_post_footer_liked_color_h',
						[
							'label' => __( 'Icon color (Liked state)', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-status-footer > ul a.ts-liked:hover i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-status-footer > ul a.ts-liked:hover svg' => 'fill: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'ts_post_footer_text_color_h',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-status-footer > ul a:hover span' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_timeline_comments',
			[
				'label' => __( 'Post: Comments', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'ts_commentt_spacing',
				[
					'label' => __( 'Spacing between comments', 'voxel-elementor' ),
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
						'{{WRAPPER}} .status-comments-list' => 'margin: {{SIZE}}{{UNIT}} 0; grid-row-gap: {{SIZE}}{{UNIT}};',

					],
				]
			);


			$this->add_control(
				'ts_comment_avatar_heading',
				[
					'label' => __( 'Comment avatar', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'ts_comment_avatar_size',
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
						'{{WRAPPER}} .status-comments-list .ts-status-avatar' => 'width: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} li.ts-reply> a::before' => 'top: calc({{SIZE}}{{UNIT}} + 10px);',
					],
				]
			);


			$this->add_control(
				'ts_comment_line',
				[
					'label' => __( 'Comment level line', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'ts_comment_line_color',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} li.ts-reply> a::before' => 'border-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'ts_comment_line_width',
				[
					'label' => __( 'Width', 'voxel-elementor' ),
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
						'{{WRAPPER}} li.ts-reply> a::before' => 'border-width: {{SIZE}}{{UNIT}};',
					],
				]
			);




		$this->end_controls_section();

		$this->start_controls_section(
			'ts_highlighted_comment',
			[
				'label' => __( 'Post: Highlighted comment', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'ts_highlight_padding',
				[
					'label' => __( 'Padding', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .ts-reply.highlighted > .comment-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'ts_highlight_radius',
				[
					'label' => __( 'Border radius', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-reply.highlighted > .comment-body' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'ts_highlight_bg',
				[
					'label' => __( 'Background color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-reply.highlighted > .comment-body' => 'background: {{VALUE}}',
					],

				]
			);



		$this->end_controls_section();
		$this->start_controls_section(
			'ts_timeline_load',
			[
				'label' => __( 'Posts: Load more', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'ts_timeline_load_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'ts_timeline_load_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'ts_timeline_load_btn',
						[
							'label' => __( 'Load more button', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_timeline_load_padding',
							'label' => __( 'Button typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-load-more',
						]
					);

					$this->add_control(
						'ts_timeline_load_height',
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
								'{{WRAPPER}} .ts-load-more' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_timeline_load_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-load-more',
						]
					);

					$this->add_control(
						'ts_timeline_load_radius',
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
								'{{WRAPPER}} .ts-load-more' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_timeline_load_bg',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-load-more' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_timeline_load_color',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-load-more' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_timeline_load_icon_col',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-load-more i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-load-more svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_timeline_load_icon_size',
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
							'selectors' => [
								'{{WRAPPER}} .ts-load-more i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-load-more svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);






				$this->end_controls_tab();

				/* Hover tab */

				$this->start_controls_tab(
					'ts_timeline_load_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'ts_timeline_load_btn_h',
						[
							'label' => __( 'Load more button', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_control(
						'ts_timeline_load_bg_h',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-load-more:hover' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_timeline_load_color_h',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-load-more:hover' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_timeline_load_border_h',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-load-more:hover' => 'border-color: {{VALUE}}',
							],

						]
					);

				$this->end_controls_tab();



			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_comments_load',
			[
				'label' => __( 'Comments: Load more', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'ts_comments_load_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'ts_comments_load_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'ts_comments_load_btn',
						[
							'label' => __( 'Load more button', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_comments_load_padding',
							'label' => __( 'Button typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-load-more-comments',
						]
					);

					$this->add_control(
						'ts_comments_load_height',
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
								'{{WRAPPER}} .ts-load-more-comments' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_comments_load_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-load-more-comments',
						]
					);

					$this->add_control(
						'ts_comments_load_radius',
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
								'{{WRAPPER}} .ts-load-more-comments' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_comments_load_bg',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-load-more-comments' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_comments_load_color',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-load-more-comments' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_comment_load_icon_col',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-load-more-comments i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-load-more-comments svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_comment_load_icon_size',
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
							'selectors' => [
								'{{WRAPPER}} .ts-load-more-comments i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-load-more-comments svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);






				$this->end_controls_tab();

				/* Hover tab */

				$this->start_controls_tab(
					'ts_comments_load_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'ts_comments_load_btn_h',
						[
							'label' => __( 'Load more button', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_control(
						'ts_comments_load_bg_h',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-load-more-comments:hover' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_comments_load_color_h',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-load-more-comments:hover' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_comments_load_border_h',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-load-more-comments:hover' => 'border-color: {{VALUE}}',
							],

						]
					);

				$this->end_controls_tab();



			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'popup_review_field',
			[
				'label' => __( 'Popup: Review field', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
			$this->add_responsive_control(
				'rev_cols',
				[
					'label' => __( 'Columns', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 1,
					'max' => 6,
					'step' => 1,
					'selectors' => [
						'{{WRAPPER}} .ts-review-field > ul' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
					],
				]
			);

			$this->add_responsive_control(
				'rev_ic_size',
				[
					'label' => __( 'Icon size', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 40,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ts-review-field li a i' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ts-review-field li a svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'rev_icon_color',
				[
					'label' => __( 'Icon color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}}  .ts-review-field li a i'
						=> 'color: {{VALUE}} !important;',
						'{{WRAPPER}} .ts-review-field li a svg'
						=> 'fill: {{VALUE}}!important;',
					],

				]
			);

			$this->add_control(
				'rev_icon_color_sel',
				[
					'label' => __( 'Icon color (Selected)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-review-field li.rating-selected a i'
						=> 'color: {{VALUE}} !important;',
						'{{WRAPPER}} .ts-review-field li.rating-selected a svg'
						=> 'fill: {{VALUE}}!important;',
					],

				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'rev_text_typo',
					'label' => __( 'Typography', 'voxel-elementor' ),
					'selector' => '.ts-review-field li a p',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'rev_text_typo_selected',
					'label' => __( 'Typography (Selected)', 'voxel-elementor' ),
					'selector' => '.ts-review-field li.rating-selected p',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'rev_border',
					'label' => __( 'Border', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-review-field li a',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'rev_border_selected',
					'label' => __( 'Border', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-review-field li.rating-selected a',
				]
			);

			$this->add_control(
				'rev_bg_col',
				[
					'label' => __( 'Background color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-review-field li a'
						=> 'background-color: {{VALUE}};',
					],

				]
			);

			$this->add_control(
				'rev_bg_col_sel',
				[
					'label' => __( 'Background color (Selected)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-review-field li.rating-selected a'
						=> 'background-color: {{VALUE}};',
					],

				]
			);

			$this->add_responsive_control(
				'ts_rating_radius',
				[
					'label' => __( 'Border radius', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'.ts-field-popup px' => [
							'min' => 0,
							'max' => 100,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ts-review-field li a' => 'border-radius: {{SIZE}}{{UNIT}};',
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



			$this->add_control(
				'cs_pg_width',
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

					'selectors' => [
						'{{WRAPPER}} .ts-field-popup' => 'min-width: {{SIZE}}{{UNIT}};',
					],
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
					'label' => __( 'Top margin', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-field-popup-container' => 'top: {{SIZE}}{{UNIT}};',
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
						'.prmr-popup{{WRAPPER}}-wrap > div:after' => 'pointer-events:all;',
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





	}

	protected function render( $instance = [] ) {
		$ratings = [
			[
				'label' => _x( 'Excellent', 'reviews', 'voxel' ),
				'icon' => \Voxel\get_icon_markup( $this->get_settings('ts_review_excellent_icon') ) ?: \Voxel\svg( 'happy.svg', false ),
				'key' => 'excellent',
				'score' => 2,
			],
			[
				'label' => _x( 'Very good', 'reviews', 'voxel' ),
				'icon' => \Voxel\get_icon_markup( $this->get_settings('ts_review_very_good_icon') ) ?: \Voxel\svg( 'happy-2.svg', false ),
				'key' => 'very-good',
				'score' => 1,
			],
			[
				'label' => _x( 'Good', 'reviews', 'voxel' ),
				'icon' => \Voxel\get_icon_markup( $this->get_settings('ts_review_good_icon') ) ?: \Voxel\svg( 'smile.svg', false ),
				'key' => 'good',
				'score' => 0,
			],
			[
				'label' => _x( 'Fair', 'reviews', 'voxel' ),
				'icon' => \Voxel\get_icon_markup( $this->get_settings('ts_review_fair_icon') ) ?: \Voxel\svg( 'speechless.svg', false ),
				'key' => 'fair',
				'score' => -1,
			],
			[
				'label' => _x( 'Poor', 'reviews', 'voxel' ),
				'icon' => \Voxel\get_icon_markup( $this->get_settings('ts_review_poor_icon') ) ?: \Voxel\svg( 'sad.svg', false ),
				'key' => 'poor',
				'score' => -2,
			],
		];

		$ordering_options = [];
		foreach ( (array) $this->get_settings( 'ts_ordering_options' ) as $ordering_option ) {
			$ordering_options[] = [
				'_id' => $ordering_option['_id'],
				'label' => $ordering_option['ts_label'],
				'order' => $ordering_option['ts_order'],
				'time' => $ordering_option['ts_time'],
				'time_custom' => $ordering_option['ts_time_custom'],
			];
		}

		if ( empty( $ordering_options ) ) {
			$ordering_options = [ [
				'_id' => '_latest',
				'label' => 'Latest',
				'order' => 'latest',
				'time' => 'all_time',
				'time_custom' => null,
			] ];
		}

		$user = \Voxel\current_user();
		$post = \Voxel\get_current_post( true );
		$mode = $this->get_settings( 'ts_mode' );

		$can_post = false;
		$name = null;
		$avatar = null;

		if ( $post && $user ) {
			$author = $post->get_author();
			$name = $user->get_display_name();
			$avatar = $user->get_avatar_markup();

			if ( $mode === 'post_reviews' && $user->can_review_post( $post->get_id() ) ) {
				$can_post = true;
			} elseif ( $mode === 'post_wall' && $user->can_post_to_wall( $post->get_id() ) ) {
				$can_post = true;
			} elseif ( $mode === 'post_timeline' && $post->is_editable_by_current_user() ) {
				$can_post = true;
				$name = $post->get_title();
				$avatar = $post->get_logo_markup();
			} elseif ( $mode === 'author_timeline' && $author && $author->get_id() === $user->get_id() ) {
				$can_post = true;
			} elseif ( $mode === 'user_feed' ) {
				$can_post = true;
			}
		}

		$config = [
			'ratingLevels' => $ratings,
			'statusId' => ! empty( $_GET['status_id'] ) ? absint( $_GET['status_id'] ) : null,
			'replyId' => ! empty( $_GET['reply_id'] ) ? absint( $_GET['reply_id'] ) : null,
			'mode' => $mode,
			'orderingOptions' => $ordering_options,
			'postSubmission' => [
				'editable' => !! \Voxel\get( 'settings.timeline.posts.editable', true ),
				'maxlength' => \Voxel\get( 'settings.timeline.posts.maxlength', 5000 ),
				'gallery' => !! \Voxel\get( 'settings.timeline.posts.images.enabled', true ),
			],
			'replySubmission' => [
				'editable' => !! \Voxel\get( 'settings.timeline.replies.editable', true ),
				'maxlength' => \Voxel\get( 'settings.timeline.replies.maxlength', 2000 ),
				'max_nest_level' => \Voxel\get( 'settings.timeline.replies.max_nest_level', null ),
			],
			'postId' => $post ? $post->get_id() : null,
			'authorId' => $post ? $post->get_author_id() : null,
			'user' => [
				'can_post' => $can_post,
				'name' => $name,
				'avatar' => $avatar,
			],
			'l10n' => [
				'attachImages' => _x( 'Attach images', 'orders', 'voxel' ),
			],
			'settings' => [
				'ts_post_footer_liked_icon' => \Voxel\get_icon_markup( $this->get_settings('ts_post_footer_liked_icon') ) ?: \Voxel\svg( 'heart-filled.svg', false ),
				'ts_post_footer_like_icon' => \Voxel\get_icon_markup( $this->get_settings_for_display('ts_post_footer_like_icon') ) ?: \Voxel\svg( 'heart.svg', false ),
				'ts_post_footer_comment_icon' => \Voxel\get_icon_markup( $this->get_settings('ts_post_footer_comment_icon') ) ?: \Voxel\svg( 'comment.svg', false ),
				'ts_post_footer_edit_icon' => \Voxel\get_icon_markup( $this->get_settings('ts_post_footer_edit_icon') ) ?: \Voxel\svg( 'pencil.svg', false ),
				'ts_post_footer_delete_icon' => \Voxel\get_icon_markup( $this->get_settings('ts_post_footer_delete_icon') ) ?: \Voxel\svg( 'trash-can.svg', false ),
				'ts_post_footer_reply_icon' => \Voxel\get_icon_markup( $this->get_settings('ts_post_footer_reply_icon') ) ?: \Voxel\svg( 'reply', false ),

			],
		];

		wp_print_styles( $this->get_style_depends() );
		require locate_template( 'templates/widgets/timeline.php' );

		if ( \Voxel\is_edit_mode() ) {
			printf( '<script type="text/javascript">%s</script>', 'window.render_timeline();' );
		}
	}

	public function get_script_depends() {
		return [
			'vx:timeline.js',
		];
	}

	public function get_style_depends() {
		return [
			'vx:forms.css', 'vx:social-feed.css',
		];
	}

	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}

	protected function register_runtime_controls() {
		$this->add_control(
			'ts_create_icon',
			[
				'label' => __( 'Create status icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_post_footer_like_icon',
			[
				'label' => __( 'Like icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_post_footer_liked_icon',
			[
				'label' => __( 'Liked icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_post_footer_comment_icon',
			[
				'label' => __( 'Comment icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_post_footer_reply_icon',
			[
				'label' => __( 'Reply icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_post_footer_delete_icon',
			[
				'label' => __( 'Delete icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_post_footer_edit_icon',
			[
				'label' => __( 'Edit icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_timeline_load_icon',
			[
				'label' => __( 'Load more', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_more_icon',
			[
				'label' => __( 'More', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_close_ico',
			[
				'label' => __( 'Close icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
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
			'ts_review_very_good_icon',
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

		$repeater = new \Elementor\Repeater;

		$repeater->add_control( 'ts_order', [
			'label' => __( 'Order', 'voxel-elementor' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'latest',
		] );

		$repeater->add_control( 'ts_time', [
			'label' => __( 'Timeframe', 'voxel-elementor' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'all_time',
			'options' => [
				'today' => __( 'Today', 'voxel-elementor' ),
				'this_week' => __( 'This week', 'voxel-elementor' ),
				'this_month' => __( 'This month', 'voxel-elementor' ),
				'this_year' => __( 'This year', 'voxel-elementor' ),
				'all_time' => __( 'All time', 'voxel-elementor' ),
				'custom' => __( 'Custom', 'voxel-elementor' ),
			],
		] );

		$repeater->add_control( 'ts_time_custom', [
			'label' => __( 'Show items from the past number of days', 'voxel-elementor' ),
			'type' => \Elementor\Controls_Manager::NUMBER,
			'default' => 7,
			'condition' => [ 'ts_time' => 'custom' ],
		] );

		$repeater->add_control( 'ts_label', [
			'label' => __( 'Label', 'voxel-elementor' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => 'Latest',
		] );

		$this->add_control( 'ts_ordering_options', [
			'label' => __( 'Ordering options', 'voxel-elementor' ),
			'type' => \Elementor\Controls_Manager::REPEATER,
			'fields' => $repeater->get_controls(),
			'_disable_loop' => true,
			'title_field' => '{{{ ts_label }}}',
		] );

		$this->add_control( 'ts_mode', [
			'label' => __( 'Display mode', 'voxel-elementor' ),
			'label_block' => true,
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'user_feed',
			'options' => [
				'post_reviews' => 'Current post reviews',
				'post_wall' => 'Current post wall',
				'post_timeline' => 'Current post timeline',
				'author_timeline' => 'Current author timeline',
				'user_feed' => 'Logged-in user news feed',
			],
		] );

		$this->add_control(
			'add_status_text',
			[
				'label' => __( 'Create status text', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type your text', 'voxel-elementor' ),
				'default' => esc_html__( 'Create status', 'voxel-elementor' ),
			]
		);

		$this->add_control(
			'no_status_text',
			[
				'label' => __( 'No posts text', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'No posts to show', 'voxel-elementor' ),
				'placeholder' => __( 'Type your text', 'voxel-elementor' ),
			]
		);
	}
}
