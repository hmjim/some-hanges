<?php

namespace Voxel\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Create_Post extends Base_Widget {

	public function get_name() {
		return 'ts-create-post';
	}

	public function get_title() {
		return __( 'Create post (VX)', 'voxel-elementor' );
	}

	public function get_icon() {
		return 'vxi vxi-plus';
	}

	public function get_categories() {
		return [ 'voxel', 'basic' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_sf_post_types',
			[
				'label' => __( 'Post type', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

			$post_types = [];
			foreach ( \Voxel\Post_Type::get_voxel_types() as $post_type ) {
				$post_types[ $post_type->get_key() ] = $post_type->get_label();
			}

			$this->add_control( 'ts_post_type', [
				'label' => __( 'Post type', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => $post_types,
			] );

			$this->add_responsive_control(
				'cpt_filter_width',
				[
					'label' => __( 'Min width', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'condition' => [ 'cpt_filter_cols' => 'elementor-col-auto' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 500,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} #cpt_filter ' => 'min-width: {{SIZE}}{{UNIT}};',
					],
				]
			);


		$this->end_controls_section();

		$this->start_controls_section(
			'ts_ui_labels',
			[
				'label' => __( 'Labels', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'ts_ui_icons',
			[
				'label' => __( 'Icons', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'next_icon',
				[
					'label' => __( 'Right arrow icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'prev_icon',
				[
					'label' => __( 'Left arrow icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'down_icon',
				[
					'label' => __( 'Down arrow icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'trash_icon',
				[
					'label' => __( 'Trash icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'publish_icon',
				[
					'label' => __( 'Publish icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'save_icon',
				[
					'label' => __( 'Save changes icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'success_icon',
				[
					'label' => __( 'Success icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'view_icon',
				[
					'label' => __( 'View post icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'ts_calendar_icon',
				[
					'label' => __( 'Calendar icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_calalt_icon',
				[
					'label' => __( 'Calendar alt icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_calminus_icon',
				[
					'label' => __( 'Calendar minus icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_add_icon',
				[
					'label' => __( 'Add icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_email_icon',
				[
					'label' => __( 'Email icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_phone_icon',
				[
					'label' => __( 'Phone icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_location_icon',
				[
					'label' => __( 'Location icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_mylocation_icon',
				[
					'label' => __( 'My location icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_minus_icon',
				[
					'label' => __( 'Minus icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_plus_icon',
				[
					'label' => __( 'Plus icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_list_icon',
				[
					'label' => __( 'List icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_search_icon',
				[
					'label' => __( 'Search icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_clock_icon',
				[
					'label' => __( 'Clock icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_link_icon',
				[
					'label' => __( 'Link icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_rtimeslot_icon',
				[
					'label' => __( 'Remove timeslot icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_sf_styling_general',
			[
				'label' => __( 'Form: General', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);


				$this->add_responsive_control(
					'ts_sf_general_padding',
					[
						'label' => __( 'Form padding', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'selectors' => [
							'{{WRAPPER}} .ts-create-post' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'ts_sf_general_bg',
					[
						'label' => __( 'Background color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-create-post' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' => 'ts_sf_general_border',
						'label' => __( 'Button border', 'voxel-elementor' ),
						'selector' => '{{WRAPPER}} .ts-create-post',
					]
				);

				$this->add_responsive_control(
					'ts_sf_general_radius',
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
							'{{WRAPPER}} .ts-create-post' => 'border-radius: {{SIZE}}{{UNIT}};',
						],
					]
				);


				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'ts_sf_general_shadow',
						'label' => __( 'Box Shadow', 'voxel-elementor' ),
						'selector' => '{{WRAPPER}} .ts-create-post',
					]
				);

				$this->add_responsive_control(
					'ts_cp_max_width',
					[
						'label' => __( 'Max width', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 1200,
								'step' => 1,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .ts-create-post' => 'max-width: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'ts_cp_min_height',
					[
						'label' => __( 'Min height', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'vh' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 1200,
								'step' => 1,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .ts-create-post' => 'min-height: {{SIZE}}{{UNIT}};',
						],
					]
				);


		$this->end_controls_section();

		$this->start_controls_section(
			'ts_form_head',
			[
				'label' => __( 'Form: Head', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

				$this->add_control(
					'ts_head_hide',
					[
						'label' => __( 'Hide', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_on' => __( 'Hide', 'voxel-elementor' ),
						'label_off' => __( 'Show', 'voxel-elementor' ),
						'return_value' => 'none',
						'default' => 'Show',
						'selectors' => [
							'{{WRAPPER}} .ts-form-progres' => 'display: {{VALUE}}',
						],

					]
				);

				$this->add_responsive_control(
					'ts_head_spacing',
					[
						'label' => __( 'Bottom spacing', 'voxel-elementor' ),
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
							'{{WRAPPER}} .ts-form-progres' => 'padding-bottom: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'ts_steps_bar',
					[
						'label' => __( 'Form steps bar', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					'ts_steps_bar_hide',
					[
						'label' => __( 'Hide', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_on' => __( 'Hide', 'voxel-elementor' ),
						'label_off' => __( 'Show', 'voxel-elementor' ),
						'return_value' => 'none',
						'default' => 'Show',
						'selectors' => [
							'{{WRAPPER}} .step-percentage' => 'display: {{VALUE}}',
						],

					]
				);

				$this->add_responsive_control(
					'ts_steps_bar_height',
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
						'selectors' => [
							'{{WRAPPER}} ul.step-percentage' => 'height: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'ts_steps_bar_radius',
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
							'{{WRAPPER}} ul.step-percentage' => 'border-radius: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'ts_percentage_spacing',
					[
						'label' => __( 'Bottom spacing', 'voxel-elementor' ),
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
							'{{WRAPPER}} .step-percentage' => 'margin-bottom: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'ts_step_bar_bg',
					[
						'label' => __( 'Progress bar background', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} ul.step-percentage li'
							=> 'background: {{VALUE}}',
						],

					]
				);

				$this->add_control(
					'ts_step_bar_done',
					[
						'label' => __( 'Progress background (Filled)', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} ul.step-percentage li:after'
							=> 'background: {{VALUE}}',
						],

					]
				);

				$this->add_control(
					'ts_current_step',
					[
						'label' => __( 'Step heading', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' => 'ts_current_step_text',
						'label' => __( 'Typography' ),
						'selector' => '{{WRAPPER}} .active-step-details p',
					]
				);


				$this->add_responsive_control(
					'ts_current_step_col',
					[
						'label' => __( 'Color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .active-step-details p' => 'color: {{VALUE}}',
						],

					]
				);


		$this->end_controls_section();

		$this->start_controls_section(
			'ts_form_nav',
			[
				'label' => __( 'Head: Next/Prev buttons', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'ts_fnav_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'ts_fnav_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);


					$this->add_control(
						'ts_fnav_btn_color',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .step-nav .ts-icon-btn i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .step-nav .ts-icon-btn svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_fnav_btn_icon_size',
						[
							'label' => __( 'Button icon size', 'voxel-elementor' ),
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
								'{{WRAPPER}} .step-nav .ts-icon-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .step-nav .ts-icon-btn svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_fnav_btn_bg',
						[
							'label' => __( 'Button background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .step-nav .ts-icon-btn'
								=> 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_fnav_btn_border',
							'label' => __( 'Button border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .step-nav .ts-icon-btn',
						]
					);

					$this->add_responsive_control(
						'ts_fnav_btn_radius',
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
								'{{WRAPPER}} .step-nav  .ts-icon-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_fnav_btn_size',
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
								'{{WRAPPER}} .step-nav .ts-icon-btn' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);





				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'ts_fnav_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'ts_fnav_btn_h',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .step-nav .ts-icon-btn:hover i' => 'color: {{VALUE}};',
								'{{WRAPPER}} .step-nav .ts-icon-btn:hover svg' => 'fill: {{VALUE}};',
							],

						]
					);

					$this->add_control(
						'ts_fnav_btn_bg_h',
						[
							'label' => __( 'Button background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .step-nav .ts-icon-btn:hover'
								=> 'background-color: {{VALUE}};',
							],

						]
					);

					$this->add_control(
						'ts_fnav_border_c_h',
						[
							'label' => __( 'Button border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .step-nav .ts-icon-btn:hover'
								=> 'border-color: {{VALUE}};',
							],

						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_form_footer',
			[
				'label' => __( 'Form: Footer', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);


			$this->add_responsive_control(
				'footer_top_spacing',
				[
					'label' => __( 'Top spacing', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-form-footer' => 'padding-top: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();



		$this->start_controls_section(
			'ts_ffooter_btn',
			[
				'label' => __( 'Footer: Next/Previous step', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'ts_ffooter_btn_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'ts_ffooter_btn_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);



					$this->add_control(
						'ts_ffooter_icon',
						[
							'label' => __( 'Icon', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);


					$this->add_responsive_control(
						'ffooter_icon_con_size',
						[
							'label' => __( 'Container size', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-nextprev a div' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ffooter_icon_size',
						[
							'label' => __( 'Icon size', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-nextprev a i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-nextprev a svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ffooter_icon_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-nextprev a > div',
						]
					);

					$this->add_responsive_control(
						'ffooter_icon_radius',
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
								'{{WRAPPER}} .ts-nextprev a > div' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_ffooter_text',
						[
							'label' => __( 'Text', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_ffooter_text_typo',
							'label' => __( 'Typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-nextprev a span',
						]
					);

					$this->add_responsive_control(
						'ffooter_text_margin',
						[
							'label' => __( 'Side margin', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-nextprev a span' => 'margin-left: {{SIZE}}{{UNIT}};margin-right: {{SIZE}}{{UNIT}};',
							],
						]
					);




					$this->add_control(
						'ts_ffooter_colors',
						[
							'label' => __( 'Colors (Default)', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);


					$this->add_responsive_control(
						'ts_ffooter_text_c',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-nextprev a span' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_ffooter_icon_con_c',
						[
							'label' => __( 'Icon container background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-nextprev a div' => 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_ffooter_icon_c',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-nextprev a i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-nextprev a svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_ffooter_fw_colors',
						[
							'label' => __( 'Colors (Next)', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);


					$this->add_responsive_control(
						'ts_ffooter_text_fw_c',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-nextprev .ts-next span' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_ffooter_icon_con_fw_c',
						[
							'label' => __( 'Icon container background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-nextprev .ts-next div' => 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_ffooter_icon_fw_c',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-nextprev .ts-next i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-nextprev .ts-next svg' => 'fill: {{VALUE}}',
							],

						]
					);




				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'ts_ffooter_btn_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'ts_ffooter_colors_h',
						[
							'label' => __( 'Colors (Default)', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);


					$this->add_responsive_control(
						'ts_ffooter_text_c_h',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-nextprev a:hover span' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_ffooter_icon_con_c_h',
						[
							'label' => __( 'Icon container background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-nextprev a:hover div' => 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_ffooter_icon_c_h',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-nextprev a:hover i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-nextprev a:hover svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_ffooter_fw_colors_h',
						[
							'label' => __( 'Colors (Next)', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);


					$this->add_responsive_control(
						'ts_ffooter_text_fw_c_h',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-nextprev .ts-next:hover span' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_ffooter_icon_con_fw_c_h',
						[
							'label' => __( 'Icon container background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-nextprev .ts-next:hover div' => 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_ffooter_icon_c_fw_h',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-nextprev .ts-next:hover i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-nextprev .ts-next:hover svg' => 'fill: {{VALUE}}',
							],

						]
					);


				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_sf1_fields_general',
			[
				'label' => __( 'Form: Fields general', 'voxel-elementor' ),
				'tab' => 'tab_fields',
			]
		);


			$this->add_control(
				'ts_sf1_input',
				[
					'label' => __( 'Field', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);



			$this->add_responsive_control(
				'ts_sf1_form_group_padding',
				[
					'label' => __( 'Field spacing', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'range' => [
						'min' => 0,
						'max' => 200,
						'step' => 5,
					],
					'selectors' => [
						'{{WRAPPER}} .form-field-grid' => 'grid-gap: {{SIZE}}{{UNIT}};;',
					],
				]
			);

			$this->add_responsive_control(
				'ts_sf1_form_group_spacing',
				[
					'label' => __( 'Grouped fields spacing', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'range' => [
						'min' => 0,
						'max' => 200,
						'step' => 5,
					],
					'selectors' => [
						'{{WRAPPER}} .form-field-grid.medium' => 'grid-gap: {{SIZE}}{{UNIT}};;',
					],
				]
			);

			$this->add_responsive_control(
				'ts_intxt_double',
				[
					'label' => __( 'Double field spacing', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-double-input' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'ts_sf1_input_lbl',
				[
					'label' => __( 'Field label', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);


			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'ts_sf1_input_label_text',
					'label' => __( 'Typography' ),
					'selector' => '{{WRAPPER}} .ts-form-group label',
				]
			);


			$this->add_responsive_control(
				'ts_sf1_input_label_col',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-form-group label' => 'color: {{VALUE}}',
					],

				]
			);



			$this->add_control(
				'sf1_input_label_padding',
				[
					'label' => __( 'Label padding', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px'],
					'selectors' => [
						'{{WRAPPER}}  .ts-form-group > label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'ts1_field_desc_h',
				[
					'label' => __( 'Field description', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);


			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'ts1_field_desc_t',
					'label' => __( 'Typography' ),
					'selector' => '{{WRAPPER}} .ts-form-group small',
				]
			);


			$this->add_responsive_control(
				'ts1_field_desc_col',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-form-group  small' => 'color: {{VALUE}}',
					],

				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_sf_intxt',
			[
				'label' => __( 'Form: Input & Textarea', 'voxel-elementor' ),
				'tab' => 'tab_fields',
			]
		);

			$this->start_controls_tabs(
				'ts_intxt_tabs'
			);
				/* Normal tab */

				$this->start_controls_tab(
					'ts_intxt_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'ts_intxt_placeholde_heading',
						[
							'label' => __( 'Placeholder', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_responsive_control(
						'ts_intxt_placeholder',
						[
							'label' => __( 'Placeholder color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form input.ts-filter::placeholder' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-form textarea.ts-filter::placeholder' => 'color: {{VALUE}}',

							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_intxt_input_input_typo',
							'label' => __( 'Typography' ),
							'selector' =>
								'{{WRAPPER}} .ts-form input.ts-filter::placeholder, .ts-form textarea.ts-filter::placeholder',
						]
					);

					$this->add_control(
						'ts_intxt_text',
						[
							'label' => __( 'Value', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);



					$this->add_responsive_control(
						'ts_intxt_value_color',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form input.ts-filter' => 'color: {{VALUE}};',
								'{{WRAPPER}} .ts-form textarea.ts-filter' => 'color: {{VALUE}};',
							],

						]
					);



					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_intxt_value_typo',
							'label' => __( 'Typography' ),

							'selector' => '{{WRAPPER}} .ts-form input.ts-filter, {{WRAPPER}} .ts-form textarea.ts-filter',


						]
					);


					$this->add_control(
						'ts_intxt_general',
						[
							'label' => __( 'General', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_responsive_control(
						'ts_intxt_bg',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form textarea.ts-filter' => 'background: {{VALUE}}',
								'{{WRAPPER}} .ts-form input.ts-filter' => 'background: {{VALUE}}',
							],

						]
					);




					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_intxt_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-form textarea.ts-filter, {{WRAPPER}} .ts-form input.ts-filter',


						]
					);

					$this->add_control(
						'ts_intxt_input_heading',
						[
							'label' => __( 'Input', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_responsive_control(
						'ts_intxt_padding',
						[
							'label' => __( 'Padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .ts-form input.ts-filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_intxt_input_height',
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
							'selectors' => [
								'{{WRAPPER}}  .ts-form input.ts-filter' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_intxt_input_radius',
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
								'{{WRAPPER}} .ts-form input.ts-filter' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);





					$this->add_control(
						'ts_intxt_textarea_heading',
						[
							'label' => __( 'Textarea', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_responsive_control(
						'ts_txt_padding',
						[
							'label' => __( 'Padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .ts-form textarea.ts-filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_intxt_textarea_height',
						[
							'label' => __( 'Height', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 1500,
									'step' => 1,
								],
								'%' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'selectors' => [
								'{{WRAPPER}}  .ts-form textarea.ts-filter' => 'min-height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_intxt_textarea_radius',
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
								'{{WRAPPER}} .ts-form textarea.ts-filter' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_input2_icon_heading',
						[
							'label' => __( 'Input with icon', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_responsive_control(
						'ts_input2_padding',
						[
							'label' => __( 'Padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .ts-input-icon input.ts-filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);



					$this->add_responsive_control(
						'ts_input2_icon_col',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-input-icon i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-input-icon svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_intxt_icon_size',
						[
							'label' => __( 'Icon size', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-input-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-input-icon svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_intxt_icon_margin',
						[
							'label' => __( 'Icon side padding', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-input-icon i' => !is_rtl() ? 'left: {{SIZE}}{{UNIT}};' : 'right: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-input-icon svg' => !is_rtl() ? 'left: {{SIZE}}{{UNIT}};' : 'right: {{SIZE}}{{UNIT}};',
							],
						]
					);

				$this->end_controls_tab();

				/* Hover */

				$this->start_controls_tab(
					'ts_intxt_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_responsive_control(
						'ts_intxt_bg_h',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form textarea.ts-filter:hover' => 'background: {{VALUE}}',
								'{{WRAPPER}} .ts-form input.ts-filter:hover' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_intxt_border_h',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form textarea.ts-filter:hover' => 'border-color: {{VALUE}}',
								'{{WRAPPER}} .ts-form input.ts-filter:hover' => 'border-color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_intxt_placeholder_h',
						[
							'label' => __( 'Placeholder color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form input.ts-filter:hover::placeholder' => 'color: {{VALUE}}',
							],

						]

					);

					$this->add_responsive_control(
						'ts_intxt_value_color_h',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form input.ts-filter:hover' => 'color: {{VALUE}};',
								'{{WRAPPER}} .ts-form textarea.ts-filter:hover' => 'color: {{VALUE}};',
							],

						]
					);

					$this->add_responsive_control(
						'ts_input2_icon_col_h',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-input-icon:hover i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-input-icon:hover svg' => 'fill: {{VALUE}}',
							],

						]
					);



				$this->end_controls_tab();

				/* Filled */

				$this->start_controls_tab(
					'ts_intxt_filled',
					[
						'label' => __( 'Active', 'voxel-elementor' ),
					]
				);

					$this->add_responsive_control(
						'ts_intxt_bg_a',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form textarea.ts-filter:focus' => 'background: {{VALUE}}',
								'{{WRAPPER}} .ts-form input.ts-filter:focus' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_intxt_border_a',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form textarea.ts-filter:focus' => 'border-color: {{VALUE}}',
								'{{WRAPPER}} .ts-form input.ts-filter:focus' => 'border-color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_intxt_placeholder_a',
						[
							'label' => __( 'Placeholder color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form input.ts-filter:active::placeholder' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-form textarea.ts-filter:active::placeholder' => 'color: {{VALUE}}',

							],

						]

					);

					$this->add_responsive_control(
						'ts_intxt_value_color_a',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form input.ts-filter:focus' => 'color: {{VALUE}};',
								'{{WRAPPER}} .ts-form textarea.ts-filter:focus' => 'color: {{VALUE}};',
							],

						]
					);



				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_input_suffix',
			[
				'label' => __( 'Form: Input suffix', 'voxel-elementor' ),
				'tab' => 'tab_fields',
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'ts_suffix_typo',
					'label' => __( 'Button typography', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .input-container .input-suffix',
				]
			);

			$this->add_responsive_control(
				'ts_suffix_text',
				[
					'label' => __( 'Text color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .input-container .input-suffix' => 'color: {{VALUE}}',
					],

				]
			);


			$this->add_responsive_control(
				'ts_suffix_bg',
				[
					'label' => __( 'Background color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .input-container .input-suffix' => 'background: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'ts_suffix_radius',
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
						'{{WRAPPER}} .input-container .input-suffix' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'ts_suffix_shadow',
					'label' => __( 'Box Shadow', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .input-container .input-suffix',
				]
			);

			$this->add_responsive_control(
				'ts_suffix_margin',
				[
					'label' => __( 'Side margin', 'voxel-elementor' ),
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
						'{{WRAPPER}} .input-container .input-suffix' => !is_rtl() ? 'right: {{SIZE}}{{UNIT}};' : 'left: {{SIZE}}{{UNIT}};',
					],
				]
			);


		$this->end_controls_section();

		$this->start_controls_section(
			'ts_sfeditor',
			[
				'label' => __( 'Form: Text Editor', 'voxel-elementor' ),
				'tab' => 'tab_fields',
			]
		);
			$this->add_control(
				'ts_sfeditor_btns',
				[
					'label' => __( 'Editor buttons', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'ts_sfeditor_btn_color',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .mce-btn i,{{WRAPPER}} .mce-menubtn button' => 'color: {{VALUE}} !important;',
					],

				]
			);

			$this->add_control(
				'ts_sfeditor_btn_color_a',
				[
					'label' => __( 'Color (Active)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .mce-btn.mce-active i' => 'color: {{VALUE}} !important;',
					],

				]
			);


			$this->add_control(
				'ts_sfeditor_btn_border',
				[
					'label' => __( 'Border', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .mce-btn' => 'border-color: {{VALUE}}!important;',
					],

				]
			);

			$this->add_control(
				'ts_sfeditor_btn_bg_active_border',
				[
					'label' => __( 'Border (Active)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .mce-btn.mce-active' => 'border-color: {{VALUE}}!important;',
					],

				]
			);



			$this->add_control(
				'ts_sfeditor_btn_radius',
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
						'{{WRAPPER}} .mce-btn' => 'border-radius: {{SIZE}}{{UNIT}}!important;',
					],
				]
			);

			$this->add_control(
				'ts_sfeditor_textarea',
				[
					'label' => __( 'Textarea', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);



			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'ts_sfeditor_text',
					'label' => __( 'Typography', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-form-group .mce-content-body',
				]
			);

			$this->add_control(
				'ts_sfeditor_text_color',
				[
					'label' => __( 'Text color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-form-group .mce-content-body' => 'color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'ts_sfeditor_text_link',
				[
					'label' => __( 'Link color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-form-group .editor-container a' => 'color: {{VALUE}}',
					],

				]
			);



			$this->add_control(
				'ts_sfeditor_backg',
				[
					'label' => __( 'Background color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-form-group .mce-content-body' => 'background-color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'ts_sfeditor_padd',
				[
					'label' => __( 'Padding', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-form-group .mce-content-body' => 'padding: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'ts_sfeditor_border',
					'label' => __( 'Border', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-form-group .mce-content-body',
				]
			);

			$this->add_control(
				'ts_sfeditor_rad',
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
						'{{WRAPPER}} .ts-form-group .mce-content-body' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'ts_sfeditor_dialog',
				[
					'label' => __( 'Dialog box', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'ts_sfeditor_dialog_bg',
				[
					'label' => __( 'Background', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'div.mce-inline-toolbar-grp' => 'background-color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'ts_sfeditor_dialog_text',
				[
					'label' => __( 'Text color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'div.mce-inline-toolbar-grp input::placeholder' => 'color: {{VALUE}}',
						'div.mce-inline-toolbar-grp input' => 'color: {{VALUE}}',
						'div.mce-inline-toolbar-grp .wp-link-preview a' => 'color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'ts_sfeditor_dialog_border',
				[
					'label' => __( 'Border color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'div.mce-inline-toolbar-grp' => 'border-color: {{VALUE}} !important;',
					],

				]
			);



		$this->end_controls_section();

		$this->start_controls_section(
				'ts_sf_styling_filters',
				[
					'label' => __( 'Form: Popup button', 'voxel-elementor' ),
					'tab' => 'tab_fields',
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


						$this->add_control(
							'ts_sf_input',
							[
								'label' => __( 'Style', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::HEADING,
								'separator' => 'before',
							]
						);

						$this->add_group_control(
							\Elementor\Group_Control_Typography::get_type(),
							[
								'name' => 'ts_sf_input_input_typo',
								'label' => __( 'Typography' ),
								'selector' => '{{WRAPPER}} .ts-form div.ts-filter',
							]
						);



						$this->add_responsive_control(
							'ts_sf_input_padding',
							[
								'label' => __( 'Padding', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::DIMENSIONS,
								'size_units' => [ 'px', '%', 'em' ],
								'selectors' => [
									'{{WRAPPER}} .ts-form div.ts-filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
								'selectors' => [
									'{{WRAPPER}} div.ts-filter' => 'height: {{SIZE}}{{UNIT}};',
								],
							]
						);


						$this->add_group_control(
							\Elementor\Group_Control_Box_Shadow::get_type(),
							[
								'name' => 'ts_sf_input_shadow',
								'label' => __( 'Box Shadow', 'voxel-elementor' ),
								'selector' => '{{WRAPPER}} div.ts-filter',
							]
						);




						$this->add_responsive_control(
							'ts_sf_input_bg',
							[
								'label' => __( 'Background color', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ts-form div.ts-filter' => 'background: {{VALUE}}',
								],

							]
						);


						$this->add_responsive_control(
							'ts_sf_input_value_col',
							[
								'label' => __( 'Text color', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ts-form div.ts-filter-text' => 'color: {{VALUE}}',
								],

							]
						);

						$this->add_group_control(
							\Elementor\Group_Control_Border::get_type(),
							[
								'name' => 'ts_sf_input_border',
								'label' => __( 'Border', 'voxel-elementor' ),
								'selector' => '{{WRAPPER}} div.ts-filter',
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
								'selectors' => [
									'{{WRAPPER}} .ts-form div.ts-filter' => 'border-radius: {{SIZE}}{{UNIT}};',
								],
							]
						);






						$this->add_control(
							'ts_icon_filters',
							[
								'label' => __( 'Icons', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::HEADING,
								'separator' => 'before',
							]
						);

						$this->add_responsive_control(
							'ts_sf_input_icon_col',
							[
								'label' => __( 'Icon color', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} div.ts-filter i' => 'color: {{VALUE}}',
									'{{WRAPPER}} div.ts-filter svg' => 'fill: {{VALUE}}',
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
									'{{WRAPPER}} div.ts-filter i' => 'font-size: {{SIZE}}{{UNIT}};',
									'{{WRAPPER}} div.ts-filter svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
								],
							]
						);

						$this->add_responsive_control(
							'ts_sf_input_icon_margin',
							[
								'label' => __( 'Icon/Text spacing', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::SLIDER,
								'size_units' => [ 'px'],
								'range' => [
									'px' => [
										'min' => 0,
										'max' => 100,
										'step' => 1,
									],
								],
								'default' => [
									'unit' => 'px',
									'size' => 10,
								],
								'selectors' => [
									'{{WRAPPER}} div.ts-filter' => 'grid-gap: {{SIZE}}{{UNIT}};',
								],
							]
						);

						$this->add_control(
							'ts_chevron',
							[
								'label' => __( 'Chevron', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::HEADING,
								'separator' => 'before',
							]
						);

						$this->add_control(
							'ts_hide_chevron',
							[

								'label' => __( 'Hide chevron', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::SWITCHER,
								'label_on' => __( 'Hide', 'voxel-elementor' ),
								'label_off' => __( 'Show', 'voxel-elementor' ),
								'return_value' => 'yes',

								'selectors' => [
									'{{WRAPPER}} div.ts-filter .ts-down-icon' => 'display: none !important;',
								],
							]
						);

						$this->add_control(
							'ts_chevron_btn_color',
							[
								'label' => __( 'Chevron color', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} div.ts-filter .ts-down-icon' => 'border-top-color: {{VALUE}}',
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

						$this->add_control(
							'ts_sf_input_h',
							[
								'label' => __( 'Style', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::HEADING,
								'separator' => 'before',
							]
						);

						$this->add_control(
							'ts_sf_input_bg_h',
							[
								'label' => __( 'Background color', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ts-form div.ts-filter:hover' => 'background: {{VALUE}}',
								],

							]
						);

						$this->add_responsive_control(
							'ts_sf_input_value_col_h',
							[
								'label' => __( 'Text color', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ts-form div.ts-filter:hover .ts-filter-text' => 'color: {{VALUE}}',
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

						$this->add_responsive_control(
							'ts_sf_input_icon_col_h',
							[
								'label' => __( 'Icon color', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} div.ts-filter:hover i' => 'color: {{VALUE}}',
									'{{WRAPPER}} div.ts-filter:hover svg' => 'fill: {{VALUE}}',
								],

							]
						);

						$this->add_group_control(
							\Elementor\Group_Control_Box_Shadow::get_type(),
							[
								'name' => 'ts_sf_input_shadow_hover',
								'label' => __( 'Box Shadow', 'voxel-elementor' ),
								'selector' => '{{WRAPPER}} div.ts-filter:hover',
							]
						);

						$this->add_control(
							'ts_chevron_btn_h',
							[
								'label' => __( 'Chevron color', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} div.ts-filter:hover .ts-down-icon' => 'border-top-color: {{VALUE}}',
								],
							]
						);

					$this->end_controls_tab();

					/* Hover tab */

					$this->start_controls_tab(
						'ts_sf_filled',
						[
							'label' => __( 'Filled', 'voxel-elementor' ),
						]
					);

						$this->add_control(
							'ts_sf_input_filled',
							[
								'label' => __( 'Style (Filled)', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::HEADING,
								'separator' => 'before',
							]
						);

						$this->add_group_control(
							\Elementor\Group_Control_Typography::get_type(),
							[
								'name' => 'ts_sf_input_typo_filled',
								'label' => __( 'Typography', 'voxel-elementor' ),
								'selector' => '{{WRAPPER}} div.ts-filter.ts-filled',
							]
						);

						$this->add_control(
							'ts_sf_input_background_filled',
							[
								'label' => __( 'Background', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ts-form div.ts-filter.ts-filled' => 'background-color: {{VALUE}}',
								],

							]
						);

						$this->add_responsive_control(
							'ts_sf_input_value_col_filled',
							[
								'label' => __( 'Text color', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} div.ts-filter.ts-filled .ts-filter-text' => 'color: {{VALUE}}',
								],

							]
						);

						$this->add_responsive_control(
							'ts_sf_input_icon_col_filled',
							[
								'label' => __( 'Icon color', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} div.ts-filter.ts-filled i' => 'color: {{VALUE}}',
									'{{WRAPPER}} div.ts-filter.ts-filled svg' => 'fill: {{VALUE}}',
								],

							]
						);

						$this->add_control(
							'ts_sf_input_border_filled',
							[
								'label' => __( 'Border color', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ts-form div.ts-filter.ts-filled' => 'border-color: {{VALUE}}',
								],

							]
						);

						$this->add_control(
							'ts_sf_border_filled_width',
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
									'{{WRAPPER}} .ts-form div.ts-filter.ts-filled' => 'border-width: {{SIZE}}{{UNIT}};',
								],
							]
						);

						$this->add_group_control(
							\Elementor\Group_Control_Box_Shadow::get_type(),
							[
								'name' => 'ts_sf_input_shadow_active',
								'label' => __( 'Box Shadow', 'voxel-elementor' ),
								'selector' => '{{WRAPPER}} div.ts-filter.ts-filled',
							]
						);

						$this->add_control(
							'ts_chevron_btn_f',
							[
								'label' => __( 'Chevron color', 'voxel-elementor' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} div.ts-filter.ts-filled .ts-down-icon' => 'border-top-color: {{VALUE}}',
								],
							]
						);


					$this->end_controls_tab();

				$this->end_controls_tabs();

			$this->end_controls_section();

		$this->start_controls_section(
			'inline_popup_list',
			[
				'label' => __( 'Form: Inline Terms/List', 'voxel-elementor' ),
				'tab' => 'tab_fields',
			]
		);

			$this->start_controls_tabs(
				'inline_list_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'inline_sfl_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);





					$this->add_control(
						'inline_term_list_item',
						[
							'label' => __( 'List item', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);



					$this->add_control(
						'inline_term_padding',
						[
							'label' => __( 'Item padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .inline-multilevel li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);



					$this->add_responsive_control(
						'inline_max_height',
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
								'{{WRAPPER}} .inline-multilevel li > a' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);



					$this->add_responsive_control(
						'inline_single_term_radius',
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
								'{{WRAPPER}} .inline-multilevel li > a' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_i_item_title',
						[
							'label' => __( 'Title', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_control(
						'inline_term_title',
						[
							'label' => __( 'Title color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .inline-multilevel li > a p'
								=> 'color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'inline_term_title_typo',
							'label' => __( 'Title typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .inline-multilevel li > a p',
						]
					);

					$this->add_control(
						'ts_i_item_icon',
						[
							'label' => __( 'Icon', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);


					$this->add_control(
						'inline_term_icon',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .inline-multilevel .ts-term-icon i'
								=> 'color: {{VALUE}};',
								'{{WRAPPER}} .inline-multilevel .ts-term-icon svg'
								=> 'fill: {{VALUE}};',
							],

						]
					);

					$this->add_responsive_control(
						'inline_term_icon_size',
						[
							'label' => __( 'Icon size', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 40,
									'step' => 1,
								],
								'%' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .inline-multilevel .ts-term-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .inline-multilevel .ts-term-icon svg' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_i_icon_container',
						[
							'label' => __( 'Icon container', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_responsive_control(
						'ts_i_term_con_size',
						[
							'label' => __( 'Size', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'description' => __( 'Has to be equal or greater than Icon size (if used)', 'voxel-elementor' ),
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 40,
									'step' => 1,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .inline-multilevel .ts-term-icon,{{WRAPPER}} .inline-multilevel .ts-term-icon img' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_icon_con_bg',
						[
							'label' => __( 'Background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .inline-multilevel .ts-term-icon'
								=> 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_i_term_radius',
						[
							'label' => __( 'Radius', 'voxel-elementor' ),
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
								'{{WRAPPER}} .inline-multilevel .ts-term-icon' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'inline_icon_right_margin',
						[
							'label' => __( 'Icon/Text spacing', 'voxel-elementor' ),
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
								'{{WRAPPER}} .inline-multilevel li > a' => 'grid-gap: {{SIZE}}{{UNIT}};',
							],
						]
					);


					$this->add_control(
						'inline_chevron',
						[
							'label' => __( 'Chevron', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);




					$this->add_control(
						'ts_chevron_icon_color',
						[
							'label' => __( 'Chevron color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .inline-multilevel .ts-right-icon' => 'border-left-color: {{VALUE}}',
							],
						]
					);


				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'inline_sfl_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'inline_item_hover',
						[
							'label' => __( 'Term item', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);
					$this->add_control(
						'inline_term_bg_h',
						[
							'label' => __( 'List item background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .inline-multilevel li > a:hover'
								=> 'background: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'inline_term_title_hover',
						[
							'label' => __( 'Title color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .inline-multilevel li > a:hover p'
								=> 'color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'inline_term_icon_hover',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .inline-multilevel li > a:hover .ts-term-icon i'
								=> 'color: {{VALUE}}',
								'{{WRAPPER}} .inline-multilevel li > a:hover .ts-term-icon svg'
								=> 'fill: {{VALUE}}',
							],

						]
					);




				$this->end_controls_tab();


				$this->start_controls_tab(
					'inline_sfl_selected',
					[
						'label' => __( 'Selected', 'voxel-elementor' ),
					]
				);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'inline_term_title_typo_s',
							'label' => __( 'Title typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .inline-multilevel li.ts-selected > a p',
						]
					);



					$this->add_control(
						'inline_term_title_s',
						[
							'label' => __( 'Title color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .inline-multilevel li.ts-selected > a p'
								=> 'color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'inline_term_icon_s',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .inline-multilevel li.ts-selected > a .ts-term-icon  i'
								=> 'color: {{VALUE}}',
								'{{WRAPPER}} .inline-multilevel li.ts-selected > a .ts-term-icon  svg'
								=> 'fill: {{VALUE}}',
							],

						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
		$this->start_controls_section(
			'auth_checkbox_section',
			[
				'label' => __( 'Form: Inline Checkbox', 'voxel-elementor' ),
				'tab' => 'tab_fields',
			]
		);

			$this->add_responsive_control(
				'check_size',
				[
					'label' => __( 'Checkbox size', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-inline-filter .container-checkbox .checkmark' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'check_radius',
				[
					'label' => __( 'Checkbox radius', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-inline-filter .container-checkbox .checkmark' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'check_border',
					'label' => __( 'Border', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-inline-filter .container-checkbox .checkmark',
				]
			);

			$this->add_responsive_control(
				'unchecked_bg',
				[
					'label' => __( 'Background color (unchecked)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-inline-filter .container-checkbox .checkmark' => 'background-color: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'checked_bg',
				[
					'label' => __( 'Background color (checked)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-inline-filter .container-checkbox input:checked ~ .checkmark' => 'background-color: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'checked_border',
				[
					'label' => __( 'Border-color (checked)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-inline-filter .container-checkbox input:checked ~ .checkmark' => 'border-color: {{VALUE}}',
					],

				]
			);



		$this->end_controls_section();

		$this->start_controls_section(
			'auth_radio_section',
			[
				'label' => __( 'Form: Inline Radio', 'voxel-elementor' ),
				'tab' => 'tab_fields',
			]
		);



			$this->add_responsive_control(
				'radio_size',
				[
					'label' => __( 'Radio size', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-inline-filter .container-radio .checkmark' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'radio_radius',
				[
					'label' => __( 'Radio radius', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-inline-filter .container-radio .checkmark' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'radio_border',
					'label' => __( 'Border', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-inline-filter .container-radio .checkmark',
				]
			);

			$this->add_responsive_control(
				'unchecked_radio_bg',
				[
					'label' => __( 'Background color (unchecked)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-inline-filter .container-radio .checkmark' => 'background-color: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'checked_radio_bg',
				[
					'label' => __( 'Background color (checked)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-inline-filter .container-radio input:checked ~ .checkmark' => 'background-color: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'checked_radio_border',
				[
					'label' => __( 'Border-color (checked)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-inline-filter .container-radio input:checked ~ .checkmark' => 'border-color: {{VALUE}}',
					],

				]
			);



		$this->end_controls_section();

		$this->start_controls_section(
			'ts_sf_field_switch',
			[
				'label' => __( 'Form: Switcher', 'voxel-elementor' ),
				'tab' => 'tab_fields',
			]
		);

				$this->add_control(
					'ts_field_switch',
					[
						'label' => __( 'Switch slider', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					'ts_field_switch_bg',
					[
						'label' => __( 'Background (Inactive)', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .onoffswitch .onoffswitch-label'
							=> 'background-color: {{VALUE}}',
						],

					]
				);

				$this->add_control(
					'ts_field_switch_bg_active',
					[
						'label' => __( 'Background (Active)', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .onoffswitch .onoffswitch-checkbox:checked + .onoffswitch-label'
							=> 'background-color: {{VALUE}}',
						],

					]
				);

				$this->add_control(
					'ts_field_switch_bg_handle',
					[
						'label' => __( 'Handle background', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .onoffswitch .onoffswitch-label:before'
							=> 'background-color: {{VALUE}}',
						],

					]
				);

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_sf_stepper',
			[
				'label' => __( 'Form: Number stepper', 'voxel-elementor' ),
				'tab' => 'tab_fields',
			]
		);

			$this->start_controls_tabs(
				'ts_stepper_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'ts_stepper_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);
					$this->add_control(
						'popup_number_input_size',
						[
							'label' => __( 'Input value size', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px'],
							'range' => [
								'px' => [
									'min' => 13,
									'max' => 30,
									'step' => 1,
								],
							],
							'default' => [
								'unit' => 'px',
								'size' => 20,
							],
							'selectors' => [
								'{{WRAPPER}} .ts-stepper-input input' => 'font-size: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_stepper_btn_color',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-stepper-input button'
								=> 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-stepper-input button svg'
								=> 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_stepper_btn_icon_size',
						[
							'label' => __( 'Button icon size', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-stepper-input button' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-stepper-input button svg' => 'font-size: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_stepper_btn_bg',
						[
							'label' => __( 'Button background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-stepper-input button'
								=> 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_stepper_btn_border',
							'label' => __( 'Button border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-stepper-input button',
						]
					);

					$this->add_responsive_control(
						'ts_stepper_btn_radius',
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
								'{{WRAPPER}} .ts-stepper-input button' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_stepper_btn_size',
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
								'{{WRAPPER}} .ts-stepper-input button' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'ts_stepper_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'ts_stepper_btn_h',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-stepper-input button:hover'
								=> 'color: {{VALUE}};',
								'{{WRAPPER}} .ts-stepper-input button:hover svg'
								=> 'fill: {{VALUE}};',
							],

						]
					);

					$this->add_control(
						'ts_stepper_btn_bg_h',
						[
							'label' => __( 'Button background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-stepper-input button:hover'
								=> 'background-color: {{VALUE}};',
							],

						]
					);

					$this->add_control(
						'ts_stepper_border_c_h',
						[
							'label' => __( 'Button border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-stepper-input button:hover'
								=> 'border-color: {{VALUE}};',
							],

						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();



		$this->end_controls_section();


		$this->start_controls_section(
			'ts_repeater',
			[
				'label' => __( 'Form: Repeater', 'voxel-elementor' ),
				'tab' => 'tab_fields',
			]
		);

			$this->add_responsive_control(
				'ts_repeater_spacing',
				[
					'label' => __( 'Padding', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-field-repeater .form-field-grid' => 'padding: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'ts_fh_btn_bg',
				[
					'label' => __( 'Background', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-field-repeater'
						=> 'background-color: {{VALUE}}',
					],

				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'ts_repeater_border',
					'label' => __( 'Border', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-field-repeater',
				]
			);

			$this->add_responsive_control(
				'ts_repeater_radius',
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
						'{{WRAPPER}} .ts-field-repeater' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'ts_repeater_shadow',
					'label' => __( 'Box Shadow', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-field-repeater',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_repeater_head',
			[
				'label' => __( 'Form: Repeater head', 'voxel-elementor' ),
				'tab' => 'tab_fields',
			]
		);

			$this->add_responsive_control(
				'rhead_padding',
				[
					'label' => __( 'Padding', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .ts-repeater-head' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'rhead_typo',
					'label' => __( 'Typography' ),
					'selector' => '{{WRAPPER}} .ts-repeater-head label',
				]
			);

			$this->add_responsive_control(
				'rhead_text_color',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-repeater-head label' => 'color: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'rhead_icon_size',
				[
					'label' => __( 'Icon size', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-repeater-head label i' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ts-repeater-head label svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'rhead_icon_color',
				[
					'label' => __( 'Icon color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-repeater-head label i' => 'color: {{VALUE}}',
						'{{WRAPPER}} .ts-repeater-head label svg' => 'color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'rhead_icon_margin',
				[
					'label' => __( 'Icon/Text spacing', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 40,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ts-repeater-head label' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'rhead_border_color',
				[
					'label' => __( 'Border color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-repeater-head' => 'border-color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'rhead_border_width',
				[
					'label' => __( 'Border width', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-repeater-head' => 'border-width: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_repeater_btn',
			[
				'label' => __( 'Form: Repeater "Add" button', 'voxel-elementor' ),
				'tab' => 'tab_fields',
			]
		);

			$this->start_controls_tabs(
				'repeater_btn_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'repeater_btn_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'repeater_btn_icon_color',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-3 i'
								=> 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-btn-3 svg'
								=> 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'repeater_btn_icon_size',
						[
							'label' => __( 'Button icon size', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-btn-3 i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-btn-3 svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'repeater_btn_bg',
						[
							'label' => __( 'Button background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-3'
								=> 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'repeater_btn_border',
							'label' => __( 'Button border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-btn-3',
						]
					);

					$this->add_responsive_control(
						'repeater_btn_radius',
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
								'{{WRAPPER}} .ts-btn-3' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'repeater_btn_text',
							'label' => __( 'Typography' ),
							'selector' => '{{WRAPPER}} .ts-btn-3',
						]
					);

					$this->add_control(
						'repeater_btn_text_color',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-3'
								=> 'color: {{VALUE}}',
							],

						]
					);


				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'repeater_btn_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'repeater_btn_icon_color_h',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-3:hover i'
								=> 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-btn-3:hover svg'
								=> 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'repeater_btn_bg_h',
						[
							'label' => __( 'Button background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-3:hover'
								=> 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'repeater_btn_border_h',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-3:hover'
								=> 'border-color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'repeater_btn_text_color_h',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-3:hover'
								=> 'color: {{VALUE}}',
							],

						]
					);


				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();



		$this->start_controls_section(
			'repeater_icon_button',
			[
				'label' => __( 'Repeater: Icon button', 'voxel-elementor' ),
				'tab' => 'tab_fields',
			]
		);

			$this->start_controls_tabs(
				'repeater_icon_button_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'repeater_icon_button_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);



					$this->add_control(
						'repeater_ib_styling',
						[
							'label' => __( 'Button styling', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_responsive_control(
						'repeater_number_btn_size',
						[
							'label' => __( 'Button size', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-field-repeater .ts-smaller' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'repeat_number_btn_color',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-field-repeater .ts-smaller i'
								=> 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-field-repeater .ts-smaller svg'
								=> 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'repeat_number_btn_icon_size',
						[
							'label' => __( 'Button icon size', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-field-repeater .ts-smaller i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-field-repeater .ts-smaller svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'repeat_number_btn_bg',
						[
							'label' => __( 'Button background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-field-repeater .ts-smaller' => 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'repeat_number_btn_border',
							'label' => __( 'Button border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-field-repeater .ts-smaller',
						]
					);

					$this->add_responsive_control(
						'repeat_number_btn_radius',
						[
							'label' => __( 'Button border radius', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,

								],
								'%' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ts-field-repeater .ts-smaller' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);



				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'repeat_icon_button_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'repeat_popup_number_btn_h',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-field-repeater .ts-smaller:hover i'
								=> 'color: {{VALUE}};',
								'{{WRAPPER}} .ts-field-repeater .ts-smaller:hover svg'
								=> 'fill: {{VALUE}};',
							],

						]
					);

					$this->add_control(
						'repeat_number_btn_bg_h',
						[
							'label' => __( 'Button background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-field-repeater .ts-smaller:hover'
								=> 'background-color: {{VALUE}};',
							],

						]
					);

					$this->add_control(
						'repeat_button_border_c_h',
						[
							'label' => __( 'Button border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-field-repeater .ts-smaller:hover'
								=> 'border-color: {{VALUE}};',
							],

						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();


		$this->start_controls_section(
			'ts_form_heading',
			[
				'label' => __( 'Form: Heading', 'voxel-elementor' ),
				'tab' => 'tab_fields',
			]
		);


				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' => 'ts_form_heading_text',
						'label' => __( 'Typography' ),
						'selector' => '{{WRAPPER}} .create-form-step > .ts-form-group.ui-heading-field label',
					]
				);


				$this->add_responsive_control(
					'ts_form_heading_col',
					[
						'label' => __( 'Color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .create-form-step > .ts-form-group.ui-heading-field label' => 'color: {{VALUE}}',
						],

					]
				);


		$this->end_controls_section();

		$this->start_controls_section(
			'ts_form_image',
			[
				'label' => __( 'Form: Image', 'voxel-elementor' ),
				'tab' => 'tab_fields',
			]
		);
			$this->add_responsive_control(
				'ts_form_image_width',
				[
					'label' => __( 'Width', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ui-image-field img' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'ts_form_image_radius',
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
						'{{WRAPPER}} .ui-image-field img' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();



		$this->start_controls_section(
			'ts_avail_calendar',
			[
				'label' => __( 'Form: Availability calendar', 'voxel-elementor' ),
				'tab' => 'tab_fields',
			]
		);

			$this->add_responsive_control(
				'ts_avail_spacing',
				[
					'label' => __( 'Content spacing', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-availability-calendar' => 'padding: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'ts_avail_bg',
				[
					'label' => __( 'Background', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-availability-calendar'
						=> 'background-color: {{VALUE}}',
					],

				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'ts_avail_border',
					'label' => __( 'Border', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-availability-calendar',
				]
			);

			$this->add_responsive_control(
				'ts_avail_radius',
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
						'{{WRAPPER}} .ts-availability-calendar' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'availability_field_shadow',
					'label' => __( 'Box Shadow', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-availability-calendar',
				]
			);

			$this->add_control(
				'avail_calendar_months',
				[
					'label' => __( 'Months', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'avail_months_typo',
					'label' => __( 'Typography' ),
					'selector' => '{{WRAPPER}} .ts-availability-calendar .pika-label',
				]
			);

			$this->add_control(
				'avail_months_color',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-availability-calendar .pika-label'
						=> 'color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'avail+days_of_week',
				[
					'label' => __( 'Days of the week', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'avail_days_typo',
					'label' => __( 'Typography' ),
					'selector' => '{{WRAPPER}} .ts-availability-calendar .pika-table abbr[title]',
				]
			);

			$this->add_control(
				'avail_days_color',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-availability-calendar .pika-table abbr[title]'
						=> 'color: {{VALUE}}',
					],

				]
			);


			$this->add_control(
				'avail_available_date',
				[
					'label' => __( 'Dates (available)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'avail_number_typo',
					'label' => __( 'Typography' ),
					'selector' => '{{WRAPPER}} .ts-availability-calendar td:not(.is-disabled)[aria-selected="false"] .pika-button',
				]
			);

			$this->add_control(
				'avail_number_color',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-availability-calendar td:not(.is-disabled)[aria-selected="false"] .pika-button'
						=> 'color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'avail_number_color_h',
				[
					'label' => __( 'Color (Hover)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-availability-calendar td:not(.is-disabled)[aria-selected="false"] .pika-button:hover'
						=> 'color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'avail_number_bg',
				[
					'label' => __( 'Background', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-availability-calendar td:not(.is-disabled)[aria-selected="false"] .pika-button'
						=> 'background-color: {{VALUE}}',
					],

				]
			);


			$this->add_control(
				'avail_number_bg_h',
				[
					'label' => __( 'Background (Hover)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-availability-calendar td:not(.is-disabled)[aria-selected="false"] .pika-button:hover'
						=> 'background-color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'avail_disabled_date',
				[
					'label' => __( 'Dates (Disabled)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'avail_dis_number_typo',
					'label' => __( 'Typography' ),
					'selector' => '{{WRAPPER}} .ts-availability-calendar .is-selected .pika-button',
				]
			);

			$this->add_control(
				'avail_disabled_number_color',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-availability-calendar .is-selected .pika-button'
						=> 'color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'avail_dis_number_color_h',
				[
					'label' => __( 'Color (Hover)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-availability-calendar .is-selected .pika-button:hover'
						=> 'color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'avail_dis_number_bg',
				[
					'label' => __( 'Background', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-availability-calendar .is-selected .pika-button'
						=> 'background-color: {{VALUE}}',
					],

				]
			);


			$this->add_control(
				'avail_dis_number_bg_h',
				[
					'label' => __( 'Background (Hover)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-availability-calendar .is-selected .pika-button:hover'
						=> 'background-color: {{VALUE}}',
					],

				]
			);




			$this->add_control(
				'avail_unavailable_date',
				[
					'label' => __( 'Dates (unavailable)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'avail_unvailable_date_t',
					'label' => __( 'Typography' ),
					'selector' => '{{WRAPPER}}  .ts-availability-calendar .is-disabled .pika-button',
				]
			);

			$this->add_control(
				'avail_unvailable_date_color',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-availability-calendar .is-disabled .pika-button'
						=> 'color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'avail_days_settings',
				[
					'label' => __( 'Other settings', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'avail_days_border_radius',
				[
					'label' => __( 'Border radius', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-availability-calendar .pika-button' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);


		$this->end_controls_section();

		$this->start_controls_section(
			'avail_icon_button',
			[
				'label' => __( 'Form: Calendar buttons', 'voxel-elementor' ),
				'tab' => 'tab_fields',
			]
		);

			$this->start_controls_tabs(
				'avail_icon_button_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'avail_icon_button_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);



					$this->add_control(
						'avail_ib_styling',
						[
							'label' => __( 'Button styling', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_responsive_control(
						'avail_number_btn_size',
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
								'{{WRAPPER}} .ts-availability-calendar .ts-icon-btn' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'avail_number_btn_color',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-availability-calendar .ts-icon-btn i'
								=> 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-availability-calendar .ts-icon-btn svg'
								=> 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'avail_number_btn_icon_size',
						[
							'label' => __( 'Button icon size', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-availability-calendar .ts-icon-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-availability-calendar .ts-icon-btn svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'avail_number_btn_bg',
						[
							'label' => __( 'Button background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-availability-calendar .ts-icon-btn' => 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'avail_number_btn_border',
							'label' => __( 'Button border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-availability-calendar .ts-icon-btn',
						]
					);

					$this->add_responsive_control(
						'avail_number_btn_radius',
						[
							'label' => __( 'Button border radius', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100,

								],
								'%' => [
									'min' => 0,
									'max' => 100,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ts-availability-calendar .ts-icon-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);



				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'avail_icon_button_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'avail_popup_number_btn_h',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-availability-calendar .ts-icon-btn:hover i'
								=> 'color: {{VALUE}};',
								'{{WRAPPER}} .ts-availability-calendar .ts-icon-btn:hover svg'
								=> 'fill: {{VALUE}};',
							],

						]
					);

					$this->add_control(
						'avail_number_btn_bg_h',
						[
							'label' => __( 'Button background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-availability-calendar .ts-icon-btn:hover'
								=> 'background-color: {{VALUE}};',
							],

						]
					);

					$this->add_control(
						'avail_button_border_c_h',
						[
							'label' => __( 'Button border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-availability-calendar .ts-icon-btn:hover'
								=> 'border-color: {{VALUE}};',
							],

						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'book_slots',
			[
				'label' => __( 'Form: Timeslots', 'voxel-elementor' ),
				'tab' => 'tab_fields',
			]
		);

			$this->add_responsive_control(
				'prf_timeslot_cols',
				[
					'label' => __( 'Number of columns', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 1,
					'max' => 6,
					'step' => 1,
					'default' => 1,
					'selectors' => [
						'{{WRAPPER}} .timeslot-list' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
					],
				]
			);


			$this->add_responsive_control(
				'slots_item_gap',
				[
					'label' => __( 'Gap between slots', 'voxel-elementor' ),
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
						'{{WRAPPER}} .timeslot-list' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'slots_padding',
				[
					'label' => __( 'Padding', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .timeslot-list li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);



			$this->add_control(
				'slots_bg',
				[
					'label' => __( 'Background', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .timeslot-list li'
						=> 'background-color: {{VALUE}}',
					],

				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'slots_border',
					'label' => __( 'Button border', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .timeslot-list li',
				]
			);


			$this->add_responsive_control(
				'slots_radius',
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
						'{{WRAPPER}} .timeslot-list li' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'slots_text',
				[
					'label' => __( 'Text color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .timeslot-list li > span'
						=> 'color: {{VALUE}}',
					],

				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'slots_typo_text',
					'label' => __( 'Typography' ),
					'selector' => '{{WRAPPER}} .timeslot-list li > span',
				]
			);

			$this->add_control(
				'slots_remove_color',
				[
					'label' => __( 'Icon color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .timeslot-list li i'
						=> 'color: {{VALUE}}',
						'{{WRAPPER}} .timeslot-list li svg'
						=> 'fill: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'slots_remove_size',
				[
					'label' => __( 'Icon size', 'voxel-elementor' ),
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
						'{{WRAPPER}} .timeslot-list li i' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .timeslot-list li svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_sf_styling_buttons',
			[
				'label' => __( 'Form: Primary button', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'ts_sf_buttons_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'ts_sf_buttons_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);


					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_submit_btn_typo',
							'label' => __( 'Button typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-btn-2.create-btn',
						]
					);

					$this->add_responsive_control(
						'ts_sf_form_btn_height',
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
							'selectors' => [
								'{{WRAPPER}} .ts-btn-2.create-btn' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);


					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_sf_form_btn_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-btn-2.create-btn',
						]
					);

					$this->add_responsive_control(
						'ts_sf_form_btn_radius',
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
								'{{WRAPPER}} .ts-btn-2.create-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_sf_form_btn_shadow',
							'label' => __( 'Box Shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-btn-2.create-btn',
						]
					);


					$this->add_responsive_control(
						'ts_sf_form_btn_c',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-2.create-btn' => 'color: {{VALUE}}',
							],

						]
					);


					$this->add_responsive_control(
						'ts_sf_form_btn_bg',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-2.create-btn' => 'background: {{VALUE}}',
							],

						]
					);



					$this->add_responsive_control(
						'ts_sf_form_btn_icon_size',
						[
							'label' => __( 'Icon size', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-btn-2.create-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-btn-2.create-btn svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_sf_form_btn_icon_color',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-2.create-btn i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-btn-2.create-btn svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_sf_form_btn_icon_margin',
						[
							'label' => __( 'Icon/Text spacing', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-btn-2.create-btn' => 'grid-gap: {{SIZE}}{{UNIT}};',
							],
						]
					);



				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'ts_sf_buttons_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_responsive_control(
						'ts_sf_form_btn_t_hover',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-2.create-btn:hover' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_sf_form_btn_bg_hover',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-2.create-btn:hover' => 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_sf_form_btn_bo_hover',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-2.create-btn:hover' => 'border-color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_sf_form_btn_shadow_h',
							'label' => __( 'Box Shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-btn-2.create-btn:hover',
						]
					);

					$this->add_responsive_control(
						'ts_sf_form_btn_icon_color_h',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-2.create-btn:hover i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-btn-2.create-btn:hover svg' => 'fill: {{VALUE}}',
							],

						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_scndry_btn',
			[
				'label' => __( 'Form: Secondary button', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'scndry_btn_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'scndry_btn_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'scndry_btn_icon_color',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-1.create-btn i'
								=> 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-btn-1.create-btn svg'
								=> 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'scndry_btn_icon_size',
						[
							'label' => __( 'Button icon size', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-btn-1.create-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-btn-1.create-btn svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'scndry_btn_icon_margin',
						[
							'label' => __( 'Icon/Text spacing', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-btn-1.create-btn' => 'grid-gap: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'scndry_btn_bg',
						[
							'label' => __( 'Button background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-1.create-btn'
								=> 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'scndry_btn_border',
							'label' => __( 'Button border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-btn-1.create-btn',
						]
					);

					$this->add_responsive_control(
						'scndry_btn_radius',
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
								'{{WRAPPER}} .ts-btn-1.create-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'scndry_btn_height',
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
							'selectors' => [
								'{{WRAPPER}} .ts-btn-1.create-btn' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'scndry_btn_text',
							'label' => __( 'Typography' ),
							'selector' => '{{WRAPPER}} .ts-btn-1.create-btn',
						]
					);

					$this->add_control(
						'scndry_btn_text_color',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-1.create-btn'
								=> 'color: {{VALUE}}',
							],

						]
					);


				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'scndry_btn_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'scndry_btn_icon_color_h',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-1.create-btn:hover i'
								=> 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-btn-1.create-btn:hover svg'
								=> 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'scndry_btn_bg_h',
						[
							'label' => __( 'Button background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-1.create-btn:hover'
								=> 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'scndry_btn_border_h',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-1.create-btn:hover'
								=> 'border-color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'scndry_btn_text_color_h',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-1.create-btn:hover'
								=> 'color: {{VALUE}}',
							],

						]
					);


				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_tertiary_btn',
			[
				'label' => __( 'Form: Tertiary button', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'tertiary_btn_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'tertiary_btn_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'tertiary_btn_icon_color',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-4.create-btn i'
								=> 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-btn-4.create-btn svg'
								=> 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'tertiary_btn_icon_size',
						[
							'label' => __( 'Button icon size', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-btn-4.create-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-btn-4.create-btn svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'tertiary_btn_bg',
						[
							'label' => __( 'Button background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-4.create-btn'
								=> 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'tertiary_btn_border',
							'label' => __( 'Button border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-btn-4.create-btn',
						]
					);

					$this->add_responsive_control(
						'tertiary_btn_radius',
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
								'{{WRAPPER}} .ts-btn-4.create-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'tertiary_btn_text',
							'label' => __( 'Typography' ),
							'selector' => '{{WRAPPER}} .ts-btn-4.create-btn',
						]
					);

					$this->add_control(
						'tertiary_btn_text_color',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-4.create-btn'
								=> 'color: {{VALUE}}',
							],

						]
					);


				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'tertiary_btn_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'tertiary_btn_icon_color_h',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-4.create-btn:hover i'
								=> 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-btn-4.create-btn:hover svg'
								=> 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'tertiary_btn_bg_h',
						[
							'label' => __( 'Button background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-4.create-btn:hover'
								=> 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'tertiary_btn_border_h',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-4.create-btn:hover'
								=> 'border-color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'tertiary_btn_text_color_h',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-4.create-btn:hover'
								=> 'color: {{VALUE}}',
							],

						]
					);


				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		/*
		===================
		Popup: Popup: File gallery
		===================
		*/


		$this->apply_controls( Option_Groups\File_Field::class );

		$this->start_controls_section(
			'sf_success',
			[
				'label' => __( 'Form: Post submitted/Updated', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'sf_welc_align',
				[
					'label' => __( 'Align icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'center',
					'options' => [
						'flex-start'  => __( 'Left', 'voxel-elementor' ),
						'center' => __( 'Center', 'voxel-elementor' ),
						'flex-end' => __( 'Right', 'voxel-elementor' ),
					],
					'selectors' => [
						'{{WRAPPER}} .ts-edit-success' => 'align-items: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'sf_welc_align_text',
				[
					'label' => __( 'Text align', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'center',
					'options' => [
						'left'  => __( 'Left', 'voxel-elementor' ),
						'center' => __( 'Center', 'voxel-elementor' ),
						'right' => __( 'Right', 'voxel-elementor' ),
					],
					'selectors' => [
						'{{WRAPPER}} .ts-edit-success' => 'text-align: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'sf_success_icon_heading',
				[
					'label' => __( 'Icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'sf_welc_ico_size',
				[
					'label' => __( 'Icon size', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-edit-success > i' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ts-edit-success > svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'sf_welc_ico_color',
				[
					'label' => __( 'Icon color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-edit-success > i' => 'color: {{VALUE}}',
						'{{WRAPPER}} .ts-edit-success > svg' => 'fill: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'sf_welc_heading',
				[
					'label' => __( 'Heading', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'sf_welc_heading_t',
					'label' => __( 'Typography' ),
					'selector' => '{{WRAPPER}} .ts-edit-success h4',
				]
			);

			$this->add_responsive_control(
				'sf_welc_heading_col',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-edit-success h4' => 'color: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'welc_top_margin',
				[
					'label' => __( 'Top margin', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-edit-success h2' => 'margin-top: {{SIZE}}{{UNIT}};',
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
					'custm_pg_backdrop',
					[
						'label' => __( 'Backdrop background', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'condition' => [ 'custom_popup_enable' => 'yes' ],
						'selectors' => [
							'{{WRAPPER}}-wrap > div:after' => 'background-color: {{VALUE}} !important',
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


				$this->add_responsive_control(
					'google_top_margin',
					[
						'label' => __( 'Google autosuggest top margin', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
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
							'.pac-container' => 'margin-top: {{SIZE}}{{UNIT}} !important;',
						],
					]
				);




		$this->end_controls_section();







	}

	protected function render( $instance = [] ) {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$post_type = \Voxel\Post_Type::get( $this->get_settings_for_display('ts_post_type') );
		if ( ! $post_type ) {
			return;
		}

		$is_admin_mode = !! $this->get_settings('_ts_admin_mode');

		$config = [
			'is_admin_mode' => $is_admin_mode,
			'admin_mode_nonce' => $this->get_settings('_ts_admin_mode_nonce'),
			'autocomplete' => $this->_get_autocomplete_config(),
		];

		$config['post_type'] = [
			'key' => $post_type->get_key(),
		];

		$post = null;
		if ( $post_type->get_key() === 'profile' ) {
			$post = \Voxel\current_user()->get_or_create_profile();
		}

		if ( \Voxel\Post::current_user_can_edit( $_GET['post_id'] ?? null ) ) {
			$post = \Voxel\Post::get( $_GET['post_id'] );
		}

		if ( $post && $post->post_type->get_key() !== $post_type->get_key() ) {
			return;
		}

		if ( $post ) {
			$config['post'] = [
				'id' => $post->get_id(),
			];
		}

		$config['fields'] = [];
		$config['steps'] = [];
		$config['errors'] = [];
		$hidden_steps = [];
		foreach ( $post_type->get_fields() as $field ) {
			if ( $post ) {
				$field->set_post( $post );
			}

			try {
				$field->check_dependencies();
			} catch ( \Exception $e ) {
				$config['errors'][] = sprintf( '[VX_CONFIG_ERROR] %s: %s', $field->get_key(), $e->getMessage() );
				continue;
			}

			if ( isset( $hidden_steps[ $field->get_step() ] ) || ! $field->passes_visibility_rules() ) {
				if ( $field->get_type() === 'ui-step' ) {
					$hidden_steps[ $field->get_key() ] = true;
				}

				continue;
			}

			$config['fields'][ $field->get_key() ] = $field->get_frontend_config();

			if ( $field->get_type() === 'ui-step' ) {
				$config['steps'][] = $field->get_key();
			}
		}

		wp_print_styles( $this->get_style_depends() );
		require locate_template( 'templates/widgets/create-post.php' );

		if ( \Voxel\is_edit_mode() ) {
           printf( '<script type="text/javascript">%s</script>', 'window.render_create_post();' );
        }
	}

	public function get_script_depends() {
		return [
			'vx:create-post.js',
		];
	}

	public function get_style_depends() {
		return [
			'vx:forms.css',
			'vx:create-post.css',
			'vx:map.css',
		];
	}

	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}

	public function _get_autocomplete_config() {
		if ( \Voxel\get( 'settings.maps.provider' ) === 'mapbox' ) {
			return [
				'countries' => array_filter( (array) \Voxel\get( 'settings.maps.mapbox.autocomplete.countries' ) ),
				'feature_types' => array_filter( (array) \Voxel\get( 'settings.maps.mapbox.autocomplete.feature_types_in_submission' ) ),
			];
		} else {
			return [
				'countries' => array_filter( (array) \Voxel\get( 'settings.maps.google_maps.autocomplete.countries' ) ),
				'feature_types' => array_filter( (array) \Voxel\get( 'settings.maps.google_maps.autocomplete.feature_types_in_submission' ) ),
			];
		}
	}

	protected function register_runtime_controls() {
		$this->add_control(
			'next_icon',
			[
				'label' => __( 'Right arrow icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'prev_icon',
			[
				'label' => __( 'Left arrow icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'down_icon',
			[
				'label' => __( 'Down arrow icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'trash_icon',
			[
				'label' => __( 'Trash icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'publish_icon',
			[
				'label' => __( 'Publish icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'save_icon',
			[
				'label' => __( 'Save changes icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'success_icon',
			[
				'label' => __( 'Success icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'view_icon',
			[
				'label' => __( 'View post icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'ts_calendar_icon',
			[
				'label' => __( 'Calendar icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_calalt_icon',
			[
				'label' => __( 'Calendar alt icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_calminus_icon',
			[
				'label' => __( 'Calendar minus icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_add_icon',
			[
				'label' => __( 'Add icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_email_icon',
			[
				'label' => __( 'Email icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_phone_icon',
			[
				'label' => __( 'Phone icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_location_icon',
			[
				'label' => __( 'Location icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_mylocation_icon',
			[
				'label' => __( 'My location icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_minus_icon',
			[
				'label' => __( 'Minus icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_plus_icon',
			[
				'label' => __( 'Plus icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_list_icon',
			[
				'label' => __( 'List icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_search_icon',
			[
				'label' => __( 'Search icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_clock_icon',
			[
				'label' => __( 'Clock icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_link_icon',
			[
				'label' => __( 'Link icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_rtimeslot_icon',
			[
				'label' => __( 'Remove timeslot icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control( 'ts_post_type', [
			'label' => __( 'Post type', 'voxel-elementor' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '',
		] );
	}
}
