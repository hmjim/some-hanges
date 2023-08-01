<?php

namespace Voxel\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Advanced_List extends Base_Widget {

	public function get_name() {
		return 'ts-advanced-list';
	}

	public function get_title() {
		return __( 'Action list (VX)', 'voxel-elementor' );
	}

	public function get_icon() {
		return 'vxi vxi-list';
	}

	public function get_categories() {
		return [ 'voxel', 'basic' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'ts_action_content',
			[
				'label' => __( 'Content', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);


			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'ts_action_content_default',
				[
					'label' => __( 'Action content (Default)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',

				]
			);


			$repeater->add_control(
				'ts_action_type',
				[
					'label' => __( 'Choose action', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'none',
					'options' => [
						'none'  => __( 'None', 'voxel-elementor' ),
						'action_link' => __( 'Link', 'voxel-elementor' ),
						'action_follow_post' => __( 'Follow post', 'voxel-elementor' ),
						'action_follow' => __( 'Follow post author', 'voxel-elementor' ),
						'action_save' => __( 'Save post to collection', 'voxel-elementor' ),
						'direct_message' => __( 'Message post', 'voxel-elementor' ),
						'direct_message_user' => __( 'Message post author', 'voxel-elementor' ),
						'edit_post' => __( 'Edit post', 'voxel-elementor' ),
						'delete_post' => __( 'Delete post', 'voxel-elementor' ),
						'share_post' => __( 'Share post', 'voxel-elementor' ),
						'select_addition' => __( 'Select addition', 'voxel-elementor' ),
						'back_to_top' => __( 'Back to top', 'voxel-elementor' ),
						'scroll_to_section' => __( 'Scroll to section', 'voxel-elementor' ),
						'action_gcal' => __( 'Add to Google Calendar', 'voxel-elementor' ),
						'action_ical' => __( 'Add to iCalendar', 'voxel-elementor' ),
					],
				]
			);

			$repeater->add_control( 'ts_addition_id', [
				'label' => __( 'Addition ID', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [ 'ts_action_type' => 'select_addition' ],
			] );

			$repeater->add_control(
				'ts_action_link',
				[
					'label' => __( 'Link', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::URL,
					'placeholder' => __( 'https://your-link.com', 'voxel-elementor' ),
					'condition' => [ 'ts_action_type' => 'action_link' ],
					'show_external' => true,
					'default' => [
						'url' => '',
						'is_external' => true,
						'nofollow' => true,
					],
				]
			);

			$repeater->add_control( 'ts_scroll_to', [
				'label' => __( 'Section ID', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [ 'ts_action_type' => 'scroll_to_section' ],
			] );

			$repeater->add_control( 'ts_action_cal_start_date', [
				'label' => __( 'Event start Date', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::DATE_TIME,
				'picker_options' => [
					'dateFormat' => 'Y-m-d H:i:s',
				],
				'default' => date( 'Y-m-d H:i:s', strtotime( '+1 day' ) ),
				'condition' => [ 'ts_action_type' => [ 'action_gcal', 'action_ical' ] ],
			] );

			$repeater->add_control( 'ts_action_cal_end_date', [
				'label' => __( 'Event end Date', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::DATE_TIME,
				'picker_options' => [
					'dateFormat' => 'Y-m-d H:i:s',
				],
				'condition' => [ 'ts_action_type' => [ 'action_gcal', 'action_ical' ] ],
			] );

			$repeater->add_control( 'ts_action_cal_title', [
				'label' => __( 'Event title', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [ 'ts_action_type' => [ 'action_gcal', 'action_ical' ] ],
			] );

			$repeater->add_control( 'ts_action_cal_desc', [
				'label' => __( 'Event description', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [ 'ts_action_type' => [ 'action_gcal', 'action_ical' ] ],
			] );

			$repeater->add_control( 'ts_action_cal_location', [
				'label' => __( 'Event location', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [ 'ts_action_type' => [ 'action_gcal', 'action_ical' ] ],
			] );

			$repeater->add_control( 'ts_action_cal_url', [
				'label' => __( 'Event URL', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [ 'ts_action_type' => [ 'action_ical' ] ],
			] );

			$repeater->add_control(
				'ts_acw_initial_text',
				[
					'label' => __( 'Text', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Action', 'voxel-elementor' ),
					'placeholder' => __( 'Action title', 'voxel-elementor' ),
				]
			);

			$repeater->add_control(
				'ts_enable_tooltip',
				[
					'label' => __( 'Enable tooltip', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'On', 'voxel-elementor' ),
					'label_off' => __( 'Off', 'voxel-elementor' ),
					'return_value' => 'yes',
				]
			);

			$repeater->add_control(
				'ts_tooltip_text',
				[
					'label' => __( 'Tooltip text', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Action', 'voxel-elementor' ),
					'placeholder' => __( 'Action title', 'voxel-elementor' ),
					'condition' => [ 'ts_enable_tooltip' => 'yes' ],
				]
			);

			$repeater->add_control(
				'ts_acw_initial_icon',
				[
					'label' => __( 'Icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);


			$repeater->add_control(
				'ts_acw_reveal_heading',
				[
					'label' => __( 'Active state', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [ 'ts_action_type' => [ 'action_follow', 'action_follow_post' ] ],
				]
			);

			$repeater->add_control(
				'ts_acw_reveal_text',
				[
					'label' => __( 'Text', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Action', 'voxel-elementor' ),
					'placeholder' => __( 'Action title', 'voxel-elementor' ),
					'condition' => [ 'ts_action_type' => [ 'action_follow', 'action_follow_post', 'select_addition' ] ],
				]
			);

			$repeater->add_control(
				'ts_acw_reveal_icon',
				[
					'label' => __( 'Icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'condition' => [ 'ts_action_type' => [ 'action_follow', 'action_follow_post', 'select_addition' ] ],
				]
			);

			// $repeater->add_control(
			// 	'ts_acw_intermediate_heading',
			// 	[
			// 		'label' => __( 'Intermediate state', 'voxel-elementor' ),
			// 		'type' => \Elementor\Controls_Manager::HEADING,
			// 		'separator' => 'before',
			// 		'condition' => [ 'ts_action_type' => [ 'action_follow', 'action_follow_post' ] ],
			// 	]
			// );

			// $repeater->add_control(
			// 	'ts_acw_intermediate_text',
			// 	[
			// 		'label' => __( 'Text', 'voxel-elementor' ),
			// 		'type' => \Elementor\Controls_Manager::TEXT,
			// 		'default' => __( 'Action', 'voxel-elementor' ),
			// 		'placeholder' => __( 'Action title', 'voxel-elementor' ),
			// 		'condition' => [ 'ts_action_type' => [ 'action_follow', 'action_follow_post' ] ],
			// 	]
			// );

			// $repeater->add_control(
			// 	'ts_acw_intermediate_icon',
			// 	[
			// 		'label' => __( 'Icon', 'text-domain' ),
			// 		'type' => \Elementor\Controls_Manager::ICONS,
			// 		'condition' => [ 'ts_action_type' => [ 'action_follow', 'action_follow_post' ] ],
			// 	]
			// );

			$repeater->add_control(
				'ts_acw_custom_style',
				[
					'label' => __( 'Custom style', 'voxel-elementor' ),
					'description' => __( 'Use custom styling for this specific item only, overwrites default style', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'On', 'voxel-elementor' ),
					'label_off' => __( 'Off', 'voxel-elementor' ),
					'default' => '',
				]
			);



			$repeater->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'ts_acw_custom_typo',
					'label' => __( 'Label typography' ),
					'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .ts-action-con',
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);

			$repeater->add_control(
				'ts_acw_initial_heading_custom',
				[
					'label' => __( 'Colors', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);

			$repeater->add_control(
				'ts_acw_initial_color_custom',
				[
					'label' => __( 'Text color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .ts-action-con' => 'color: {{VALUE}}',
					],
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);

			$repeater->add_control(
				'ts_acw_initial_color_h_custom',
				[
					'label' => __( 'Text color (Hover)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .ts-action-con:hover' => 'color: {{VALUE}}',
					],
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);

			$repeater->add_control(
				'ts_acw_initial_color_a_custom',
				[
					'label' => __( 'Text color (Active)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .active.ts-action-con' => 'color: {{VALUE}}',
					],
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);

			$repeater->add_control(
				'ts_acw_initial_bg_custom',
				[
					'label' => __( 'Background color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .ts-action-con' => 'background: {{VALUE}}',
					],
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);



			$repeater->add_control(
				'ts_acw_initial_bg_h_custom',
				[
					'label' => __( 'Background color (Hover)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .ts-action-con:hover' => 'background: {{VALUE}}',
					],
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);

			$repeater->add_control(
				'ts_acw_initial_bg_a_custom',
				[
					'label' => __( 'Background color (Active)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .active.ts-action-con' => 'background: {{VALUE}}',
					],
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);

			$repeater->add_control(
				'ts_acw_border_heading_custom',
				[
					'label' => __( 'Border', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);

			$repeater->add_control(
				'ts_acw_border_radius_custom',
				[
					'label' => __( 'Border radius', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px'],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 70,
							'step' => 1,
						],
					],
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}}  .ts-action-con' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$repeater->add_control(
				'ts_acw_border_c_custom',
				[
					'label' => __( 'Border color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .ts-action-con' => 'border-color: {{VALUE}}',
					],
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);

			$repeater->add_control(
				'ts_acw_border_h_custom',
				[
					'label' => __( 'Border color (Hover)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .ts-action-con:hover' => 'border-color: {{VALUE}}',
					],
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);

			$repeater->add_control(
				'ts_acw_initial_border_a_custom',
				[
					'label' => __( 'Border color (Active)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .active.ts-action-con' => 'border-color: {{VALUE}}',
					],
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);





			$repeater->add_control(
				'ts_acw_icon_container_custom',
				[
					'label' => __( 'Icon container', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);

			$repeater->add_control(
				'ts_acw_icon_con_size_custom',
				[
					'label' => __( 'Size', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px'],
					'range' => [
						'px' => [
							'min' => 20,
							'max' => 70,
							'step' => 1,
						],
					],
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .ts-action-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$repeater->add_control(
				'ts_acw_icon_con_bg_custom',
				[
					'label' => __( 'Background', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .ts-action-icon' => 'background: {{VALUE}}',
					],
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);

			$repeater->add_control(
				'ts_acw_icon_con_bg_h_custom',
				[
					'label' => __( 'Background (Hover)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .ts-action-con:hover .ts-action-icon' => 'background: {{VALUE}}',
					],
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);

			$repeater->add_control(
				'ts_acw_icon_con_bg_a_custom',
				[
					'label' => __( 'Background (Active)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .active.ts-action-con .ts-action-icon' => 'background: {{VALUE}}',
					],
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);

			$repeater->add_responsive_control(
				'ts_acw_icon_margin_custom',
				[
					'label' => __( 'Icon/Text spacing', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px'],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 20,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .ts-action-con' => 'grid-gap: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .ts-action-con span' => 'grid-gap: {{SIZE}}{{UNIT}} !important;',
					],
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);




			$repeater->add_control(
				'ts_acw_icon_heading_custom',
				[
					'label' => __( 'Icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);

			$repeater->add_control(
				'ts_acw_icon_size_custom',
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
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .ts-action-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .ts-action-icon svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$repeater->add_control(
				'ts_acw_icon_color_custom',
				[
					'label' => __( 'Icon Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .ts-action-icon i' => 'color: {{VALUE}}',
						'{{WRAPPER}} {{CURRENT_ITEM}} .ts-action-icon svg' => 'fill: {{VALUE}}',
					],
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);

			$repeater->add_control(
				'ts_acw_icon_color_h_custom',
				[
					'label' => __( 'Icon Color (Hover)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .ts-action-con:hover .ts-action-icon i' => 'color: {{VALUE}}',
						'{{WRAPPER}} {{CURRENT_ITEM}} .ts-action-con:hover .ts-action-icon svg' => 'fill: {{VALUE}}',
					],
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);

			$repeater->add_control(
				'ts_acw_icon_color_a_custom',
				[
					'label' => __( 'Icon Color (Active)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .active.ts-action-con .ts-action-icon i' => 'color: {{VALUE}}',
						'{{WRAPPER}} {{CURRENT_ITEM}} .active.ts-action-con .ts-action-icon svg' => 'fill: {{VALUE}}',
					],
					'condition' => [ 'ts_acw_custom_style' => 'yes' ],
				]
			);


			$this->add_control(
				'ts_actions',
				[
					'label' => __( 'Items', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::REPEATER,
					'fields' => $repeater->get_controls(),
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
				'ts_close_ico',
				[
					'label' => __( 'Close icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);


			$this->add_control(
				'ts_message_ico',
				[
					'label' => __( 'Direct message icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'ts_link_ico',
				[
					'label' => __( 'Copy link icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'ts_share_ico',
				[
					'label' => __( 'Share via icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'ts_step_ico',
				[
					'label' => __( 'Edit step icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);



		$this->end_controls_section();

		$this->start_controls_section(
			'ts_advanced_list_general',
			[
				'label' => __( 'List', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control( 'csgrid_action_on', [
				'label' => __( 'Enable CSS grid', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'selectors' => [
					'{{WRAPPER}} .ts-advanced-list' => 'display: grid;',

				],
			] );

			$this->add_responsive_control(
				'ts_cgrid_columns',
				[
					'label' => __( 'Number of columns', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 1,
					'max' => 24,
					'step' => 1,
					'selectors' => [
						'{{WRAPPER}} .ts-advanced-list' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',

					],
					'condition' => [ 'csgrid_action_on' => 'yes' ],
				]
			);





			$this->add_responsive_control(
				'ts_cgrid_gap',
				[
					'label' => __( 'Grid gap', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-advanced-list' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],
					'condition' => [ 'csgrid_action_on' => 'yes' ],

				]
			);

			$this->add_control(
				'ts_al_columns_no',
				[
					'label' => __( 'Item width', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'elementor-col-auto',
					'options' => [
						'elementor-col-auto'  => __( 'Auto', 'voxel-elementor' ),
						'elementor-col-cstm'  => __( 'Custom item width', 'voxel-elementor' ),
					],
					  'condition' => [ 'csgrid_action_on' => '' ],

				]
			);

			$this->add_responsive_control(
				'ts_al_columns_cstm',
				[
					'label' => __( 'Width (px)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => 50,
							'max' => 200,
							'step' => 1,
						],
					],
					'condition' => [ 'ts_al_columns_no' => 'elementor-col-cstm' ],
					'selectors' => [
						'{{WRAPPER}} .ts-advanced-list .ts-action.elementor-col-cstm' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'ts_al_justify',
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
						'{{WRAPPER}} .ts-advanced-list' => 'justify-content: {{VALUE}}',
					],
					  'condition' => [ 'csgrid_action_on' => '' ],
				]
			);




		$this->end_controls_section();

		$this->start_controls_section(
			'ts_advanced_list_item',
			[
				'label' => __( 'List item', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'al_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'al_normal_tab',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'al_item_general',
						[
							'label' => __( 'General', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_responsive_control(
						'ts_al_align',
						[
							'label' => __( 'Justify content', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SELECT,
							'default' => 'left',
							'options' => [
								'left'  => __( 'Left', 'voxel-elementor' ),
								'center' => __( 'Center', 'voxel-elementor' ),
								'right' => __( 'Right', 'voxel-elementor' ),
							],

							'selectors' => [
								'{{WRAPPER}} .ts-action  .ts-action-con' => 'justify-content: {{VALUE}};text-align: {{VALUE}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_action_padding',
						[
							'label' => __( 'Padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .ts-action  .ts-action-con' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_action_margin',
						[
							'label' => __( 'Margin', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .ts-action' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_acw_height',
						[
							'label' => __( 'Height', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px'],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 200,
									'step' => 1,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ts-action  .ts-action-con' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);


					$this->add_control(
						'al_item_border',
						[
							'label' => __( 'Border', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_acw_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-action-con',
						]
					);

					$this->add_control(
						'ts_acw_border_radius',
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
							'default' => [
								'unit' => 'px',
								'size' => 5,
							],
							'selectors' => [
								'{{WRAPPER}} .ts-action-con' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_acw_border_shadow',
							'label' => __( 'Box Shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}}  .ts-action-con',
						]
					);

					$this->add_control(
						'al_item_typo',
						[
							'label' => __( 'Typography', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_acw_typography',
							'label' => __( 'Typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}}  .ts-action-con',
						]
					);

					$this->add_control(
						'ts_item_colors',
						[
							'label' => __( 'Item colors', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_control(
						'ts_acw_initial_color',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-action-con' => 'color: {{VALUE}}',
							],
						]
					);


					$this->add_control(
						'ts_acw_initial_bg',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}}  .ts-action-con' => 'background: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'ts_acw_icon_container',
						[
							'label' => __( 'Icon container', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_control(
						'ts_acw_icon_con_bg',
						[
							'label' => __( 'Background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-action-icon' => 'background: {{VALUE}}',
							],
						]
					);





					$this->add_responsive_control(
						'ts_acw_icon_con_size',
						[
							'label' => __( 'Size', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px'],
							'range' => [
								'px' => [
									'min' => 30,
									'max' => 70,
									'step' => 1,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ts-action-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_acw_icon_con_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-action-icon',
						]
					);

					$this->add_control(
						'ts_acw_icon_con_radius',
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
							'default' => [
								'unit' => 'px',
								'size' => 26,
							],
							'selectors' => [
								'{{WRAPPER}} .ts-action-icon' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_acw_icon_margin',
						[
							'label' => __( 'Icon/Text spacing', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px'],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 20,
									'step' => 1,
								],
							],
							'default' => [
								'unit' => 'px',
								'size' => 0,
							],
							'selectors' => [
								'{{WRAPPER}} .ts-action-con, {{WRAPPER}} .ts-action span' => 'grid-gap: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_acw_icon_heading',
						[
							'label' => __( 'Icon', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);


					$this->add_responsive_control(
						'ts_acw_icon_size',
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
							'default' => [
								'unit' => 'px',
								'size' => 26,
							],
							'selectors' => [
								'{{WRAPPER}} .ts-action-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-action-icon svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_acw_icon_color',
						[
							'label' => __( 'Icon Color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-action-icon i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-action-icon svg' => 'fill: {{VALUE}}',
							],
						]
					);






				$this->end_controls_tab();

				/* Hover tab */

				$this->start_controls_tab(
					'al_hover_tab',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_acw_border_shadow_h',
							'label' => __( 'Box Shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-action-con:hover',
						]
					);

					$this->add_control(
						'ts_item_colors_h',
						[
							'label' => __( 'Item colors', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_control(
						'ts_acw_border_h',
						[
							'label' => __( 'Border color (Hover)', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-action-con:hover' => 'border-color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'ts_acw_initial_color_h',
						[
							'label' => __( 'Text color (Hover)', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-action-con:hover' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'ts_acw_initial_bg_h',
						[
							'label' => __( 'Background color (Hover)', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-action-con:hover' => 'background: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'ts_acw_icon_con_hover',
						[
							'label' => __( 'Icon', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_control(
						'ts_acw_icon_con_bg_h',
						[
							'label' => __( 'Background (Hover)', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-action-con:hover .ts-action-icon' => 'background: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'ts_acw_icon_con_bord_h',
						[
							'label' => __( 'Border (Hover)', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-action-con:hover .ts-action-icon' => 'border-color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'ts_acw_icon_color_h',
						[
							'label' => __( 'Icon Color (Hover)', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-action-con:hover .ts-action-icon i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-action-con:hover .ts-action-icon svg' => 'fill: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				/* Active tab */

				$this->start_controls_tab(
					'al_active_tab',
					[
						'label' => __( 'Active', 'voxel-elementor' ),
					]
				);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_acw_border_shadow_a',
							'label' => __( 'Box Shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .active.ts-action-con',
						]
					);

					$this->add_control(
						'ts_item_colors_a',
						[
							'label' => __( 'Item colors', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_control(
						'ts_acw_initial_color_a',
						[
							'label' => __( 'Text color (Active)', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .active.ts-action-con' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'ts_acw_initial_bg_a',
						[
							'label' => __( 'Background color (Active)', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .active.ts-action-con' => 'background: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'ts_acw_initial_border_a',
						[
							'label' => __( 'Border color (Active)', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .active.ts-action-con' => 'border-color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'ts_acw_icon_con_active',
						[
							'label' => __( 'Icon', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_control(
						'ts_acw_icon_con_bg_a',
						[
							'label' => __( 'Background (Active)', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .active.ts-action-con .ts-action-icon' => 'background: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'ts_acw_icon_con_bord_a',
						[
							'label' => __( 'Border (Hover)', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .active.ts-action-con .ts-action-icon' => 'border-color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'ts_acw_icon_color_a',
						[
							'label' => __( 'Icon Color (Active)', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .active.ts-action-con .ts-action-icon i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .active.ts-action-con .ts-action-icon svg' => 'fill: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();


		$this->end_controls_section();

		$this->start_controls_section(
			'ts_tooltips',
			[
				'label' => __( 'Tooltips', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'tooltip_bottom',
				[
					'label' => __( 'Display below item?', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'none',
					'selectors' => [
						'{{WRAPPER}} .ts-action::after' => 'top: calc(100% + 5px); bottom: auto;',
					],

				]
			);

			$this->add_control(
				'ts_tooltip_color',
				[
					'label' => __( 'Text color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-action::after' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'ts_tooltip_typo',
					'label' => __( 'Typography', 'voxel-elementor' ),
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_3,
					'selector' => '{{WRAPPER}} .ts-action::after',
				]
			);
			$this->add_control(
				'ts_tooltip_bg',
				[
					'label' => __( 'Background color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-action::after' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'ts_tooltip_radius',
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
						'{{WRAPPER}} .ts-action::after' => 'border-radius: {{SIZE}}{{UNIT}};',
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
						'{{WRAPPER}} .ts-field-popup' => 'min-width: {{SIZE}}{{UNIT}};',
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
						'{{WRAPPER}} .ts-field-popup' => 'max-width: {{SIZE}}{{UNIT}};',
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
						'{{WRAPPER}} .ts-popup-content-wrapper' => 'max-height: {{SIZE}}{{UNIT}};',
					],
				]
			);




		$this->end_controls_section();

		$this->start_controls_section(
			'ts_sf_popup_list',
			[
				'label' => __( 'Custom popup: Menu', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [ 'custom_popup_enable' => 'yes' ],
			]
		);

			$this->start_controls_tabs(
				'ts_popup_list_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'ts_sfl_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);



					$this->add_control(
						'ts_popup_term_list_item',
						[
							'label' => __( 'List item', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);


					$this->add_control(
						'ts_popup_term_padding',
						[
							'label' => __( 'Item padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .ts-term-dropdown li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);


					$this->add_responsive_control(
						'ts_term_max_height',
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
								'{{WRAPPER}} .ts-term-dropdown li > a' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_popup_single_term_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '.ts-term-dropdown li > a',
						]
					);

					$this->add_responsive_control(
						'ts_popup_single_term_radius',
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
								'{{WRAPPER}} .ts-term-dropdown li > a' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);


					$this->add_control(
						'ts_popup_term_icon',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-term-dropdown li > a i'
								=> 'color: {{VALUE}};',
								'{{WRAPPER}} .ts-term-dropdown li > a svg'
								=> 'fill: {{VALUE}};',
							],

						]
					);

					$this->add_responsive_control(
						'ts_popup_term_icon_size',
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
							],
							'selectors' => [
								'{{WRAPPER}} .ts-term-dropdown li > a i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-term-dropdown li > a svg' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
							],
						]
					);



					$this->add_responsive_control(
						'ts_popup_term_con_size',
						[
							'label' => __( 'Icon container size', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 40,
									'step' => 1,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ts-term-dropdown li > a > span' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_popup_term_image_size',
						[
							'label' => __( 'Image size', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px', '%' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 40,
									'step' => 1,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ts-term-dropdown li > a img' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_popup_term_radius',
						[
							'label' => __( 'Image radius', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-term-dropdown li > a > span, {{WRAPPER}} .ts-term-dropdown li > a img' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_icon_right_margin',
						[
							'label' => __( 'Icon / Text spacing', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-term-dropdown li > a' => 'grid-gap: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_popup_term_title',
						[
							'label' => __( 'Title color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-term-dropdown li > a p'
								=> 'color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_popup_term_title_typo',
							'label' => __( 'Title typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-term-dropdown li > a p',
						]
					);





					$this->add_control(
						'ts_popup_chevron',
						[
							'label' => __( 'Chevron', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);


					$this->add_control(
						'ts_popup_term_arrow',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-term-dropdown li > a > i:last-child'
								=> 'color: {{VALUE}} !important;',
								'{{WRAPPER}} .ts-term-dropdown li > a > svg:last-child'
								=> 'fill: {{VALUE}} !important;',
							],

						]
					);

					$this->add_responsive_control(
						'ts_popup_arrow_icon_size',
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
								'{{WRAPPER}} .ts-term-dropdown li > a > i:last-child' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-term-dropdown li > a > svg:last-child' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);



					$this->add_control(
						'ts_go_back',
						[
							'label' => __( 'Go back', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);


					$this->add_control(
						'ts_back_icon_color',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-term-dropdown li.term-dropdown-back i'
								=> 'color: {{VALUE}} !important;',
								'{{WRAPPER}} .ts-term-dropdown li.term-dropdown-back svg'
								=> 'fill: {{VALUE}}!important;',
							],

						]
					);


					$this->add_responsive_control(
						'ts_back_icon_size',
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
								'{{WRAPPER}} .ts-term-dropdown li.term-dropdown-back i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-term-dropdown li.term-dropdown-back svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_back_icon_text_typo',
							'label' => __( 'Title typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .term-dropdown-back > a > p',
						]
					);

					$this->add_control(
						'ts_back_icon_text',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .term-dropdown-back > a > p'
								=> 'color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_popup_term_back',
						[
							'label' => __( 'Button background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .term-dropdown-back a'
								=> 'background-color: {{VALUE}}',
							],

						]
					);


				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'ts_sfl_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'ts_term_item_hover',
						[
							'label' => __( 'Term item', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);
					$this->add_control(
						'ts_popup_term_bg_h',
						[
							'label' => __( 'List item background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-term-dropdown li > a:hover'
								=> 'background: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_popup_term_title_hover',
						[
							'label' => __( 'Title color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-term-dropdown li > a:hover p'
								=> 'color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_popup_term_icon_hover',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-term-dropdown li > a:hover > i'
								=> 'color: {{VALUE}}',
							],

						]
					);


					$this->add_control(
						'ts_go_back_hover',
						[
							'label' => __( 'Go back', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);



					$this->add_control(
						'ts_back_icon_text_hover',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-term-dropdown li.term-dropdown-back > a:hover > p'
								=> 'color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_popup_term_back_hover',
						[
							'label' => __( 'Button background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-term-dropdown li.term-dropdown-back a:hover'
								=> 'background-color: {{VALUE}}',
							],

						]
					);


				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();




	}

	protected function render( $instance = [] ) {
		wp_print_styles( $this->get_style_depends() );
		require locate_template( 'templates/widgets/advanced-list.php' );
	}

	public function get_style_depends() {
		return [ 'vx:action.css' ];
	}

	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}

	protected function register_runtime_controls() {
		$this->add_control(
			'ts_al_columns_no',
			[
				'label' => __( 'Item width', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'elementor-col-auto',
				'options' => [
					'elementor-col-auto'  => __( 'Auto', 'voxel-elementor' ),
					'elementor-col-cstm'  => __( 'Custom item width', 'voxel-elementor' ),
				],
			]
		);


		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'ts_action_type',
			[
				'label' => __( 'Choose action', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'none',
			]
		);

		$repeater->add_control( 'ts_addition_id', [
			'label' => __( 'Addition ID', 'voxel-elementor' ),
			'type' => \Elementor\Controls_Manager::TEXT,
		] );

		$repeater->add_control(
			'ts_action_link',
			[
				'label' => __( 'Link', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'voxel-elementor' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
			]
		);

		$repeater->add_control( 'ts_scroll_to', [
			'label' => __( 'Section ID', 'voxel-elementor' ),
			'type' => \Elementor\Controls_Manager::TEXT,
		] );

		$repeater->add_control( 'ts_action_cal_start_date', [
			'label' => __( 'Event start Date', 'voxel-elementor' ),
			'type' => \Elementor\Controls_Manager::DATE_TIME,
			'picker_options' => [
				'dateFormat' => 'Y-m-d H:i:s',
			],
			'default' => date( 'Y-m-d H:i:s', strtotime( '+1 day' ) ),
		] );

		$repeater->add_control( 'ts_action_cal_end_date', [
			'label' => __( 'Event end Date', 'voxel-elementor' ),
			'type' => \Elementor\Controls_Manager::DATE_TIME,
			'picker_options' => [
				'dateFormat' => 'Y-m-d H:i:s',
			],
		] );

		$repeater->add_control( 'ts_action_cal_title', [
			'label' => __( 'Event title', 'voxel-elementor' ),
			'type' => \Elementor\Controls_Manager::TEXT,
		] );

		$repeater->add_control( 'ts_action_cal_desc', [
			'label' => __( 'Event description', 'voxel-elementor' ),
			'type' => \Elementor\Controls_Manager::TEXT,
		] );

		$repeater->add_control( 'ts_action_cal_location', [
			'label' => __( 'Event location', 'voxel-elementor' ),
			'type' => \Elementor\Controls_Manager::TEXT,
		] );

		$repeater->add_control( 'ts_action_cal_url', [
			'label' => __( 'Event URL', 'voxel-elementor' ),
			'type' => \Elementor\Controls_Manager::TEXT,
		] );

		$repeater->add_control(
			'ts_acw_initial_text',
			[
				'label' => __( 'Text', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Action', 'voxel-elementor' ),
				'placeholder' => __( 'Action title', 'voxel-elementor' ),
			]
		);

		$repeater->add_control(
			'ts_enable_tooltip',
			[
				'label' => __( 'Enable tooltip', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'voxel-elementor' ),
				'label_off' => __( 'Off', 'voxel-elementor' ),
				'return_value' => 'yes',
			]
		);

		$repeater->add_control(
			'ts_tooltip_text',
			[
				'label' => __( 'Tooltip text', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Action', 'voxel-elementor' ),
				'placeholder' => __( 'Action title', 'voxel-elementor' ),
				'condition' => [ 'ts_enable_tooltip' => 'yes' ],
			]
		);

		$repeater->add_control(
			'ts_acw_initial_icon',
			[
				'label' => __( 'Icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$repeater->add_control(
			'ts_acw_reveal_text',
			[
				'label' => __( 'Text', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Action', 'voxel-elementor' ),
				'placeholder' => __( 'Action title', 'voxel-elementor' ),
			]
		);

		$repeater->add_control(
			'ts_acw_reveal_icon',
			[
				'label' => __( 'Icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'ts_actions',
			[
				'label' => __( 'Items', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
			]
		);
	}
}
