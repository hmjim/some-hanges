<?php

namespace Voxel\Widgets\Option_Groups;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Popup_Menu {

	public static function controls( $widget ) {

		$widget->start_controls_section(
			'ts_sf_popup_list',
			[
				'label' => __( 'Popup: Menu styling', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$widget->start_controls_tabs(
				'ts_popup_list_tabs'
			);

				/* Normal tab */

				$widget->start_controls_tab(
					'ts_sfl_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);


					$widget->add_control(
						'ts_popup_term_list',
						[
							'label' => __( 'List', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$widget->add_responsive_control(
						'menu_cols_gap',
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
								'.ts-field-popup .ts-term-dropdown-list' => 'grid-gap: {{SIZE}}{{UNIT}};',
							],

						]
					);

					$widget->add_control(
						'ts_popup_term_list_item',
						[
							'label' => __( 'Item', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);


					$widget->add_control(
						'ts_popup_term_padding',
						[
							'label' => __( 'Item padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'.ts-field-popup .ts-term-dropdown li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);



					$widget->add_responsive_control(
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
								'.ts-field-popup .ts-term-dropdown li > a' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$widget->add_responsive_control(
						'ts_single_term_radius',
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
								'.ts-field-popup .ts-term-dropdown li > a' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$widget->add_control(
						'ts_h_item_title',
						[
							'label' => __( 'Title', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$widget->add_control(
						'ts_popup_term_title',
						[
							'label' => __( 'Title color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'.ts-field-popup .ts-term-dropdown li > a p'
								=> 'color: {{VALUE}}',
							],

						]
					);

					$widget->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_popup_term_title_typo',
							'label' => __( 'Title typography', 'voxel-elementor' ),
							'selector' => '.ts-field-popup .ts-term-dropdown li > a p',
						]
					);


					$widget->add_control(
						'ts_h_item_icon',
						[
							'label' => __( 'Icon', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$widget->add_control(
						'ts_popup_term_icon',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'.ts-field-popup .ts-term-icon i'
								=> 'color: {{VALUE}};',
								'.ts-field-popup .ts-term-icon svg'
								=> 'fill: {{VALUE}};',
							],

						]
					);

					$widget->add_responsive_control(
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
								'.ts-field-popup .ts-term-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
								'.ts-field-popup .ts-term-icon svg' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
							],
						]
					);


					$widget->add_control(
						'ts_h_icon_container',
						[
							'label' => __( 'Icon container', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);


					$widget->add_responsive_control(
						'ts_popup_term_con_size',
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
								'.ts-field-popup .ts-term-icon,.ts-field-popup .ts-term-icon img' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$widget->add_control(
						'ts_icon_con_background',
						[
							'label' => __( 'Background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'.ts-field-popup .ts-term-icon'
								=> 'background-color: {{VALUE}};',
							],

						]
					);



					$widget->add_responsive_control(
						'ts_popup_term_radius',
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
								'.ts-field-popup .ts-term-icon,.ts-field-popup .ts-term-icon img' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$widget->add_responsive_control(
						'ts_icon_right_margin',
						[
							'label' => __( 'Spacing', 'voxel-elementor' ),
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
								'.ts-field-popup .ts-term-dropdown li > a' => 'grid-gap: {{SIZE}}{{UNIT}};',
							],
						]
					);


					$widget->add_control(
						'ts_item_chevron',
						[
							'label' => __( 'Chevron', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);




					$widget->add_control(
						'ts_chevron_icon_color',
						[
							'label' => __( 'Chevron color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'.ts-field-popup .ts-right-icon' => 'border-left-color: {{VALUE}}',
								'.ts-field-popup .pika-label:after' => 'border-top-color: {{VALUE}}',
							],
						]
					);





				$widget->end_controls_tab();


				/* Hover tab */

				$widget->start_controls_tab(
					'ts_sfl_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$widget->add_control(
						'ts_term_item_hover',
						[
							'label' => __( 'Term item', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);
					$widget->add_control(
						'ts_popup_term_bg_h',
						[
							'label' => __( 'List item background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'.ts-field-popup .ts-term-dropdown li > a:hover'
								=> 'background: {{VALUE}}',
							],

						]
					);

					$widget->add_control(
						'ts_popup_term_title_hover',
						[
							'label' => __( 'Title color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'.ts-field-popup .ts-term-dropdown li > a:hover p'
								=> 'color: {{VALUE}}',
							],

						]
					);

					$widget->add_control(
						'ts_popup_term_icon_hover',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'.ts-field-popup .ts-term-dropdown li > a:hover .ts-term-icon i'
								=> 'color: {{VALUE}}',
								'.ts-field-popup .ts-term-dropdown li > a:hover .ts-term-icon svg'
								=> 'fill: {{VALUE}}',
							],

						]
					);





				$widget->end_controls_tab();

				$widget->start_controls_tab(
					'ts_popup_term_selected',
					[
						'label' => __( 'Selected', 'voxel-elementor' ),
					]
				);

					$widget->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_popup_term_title_typo_s',
							'label' => __( 'Title typography', 'voxel-elementor' ),
							'selector' => '.ts-field-popup .ts-term-dropdown li.ts-selected > a p',
						]
					);


					$widget->add_control(
						'ts_popup_term_title_s',
						[
							'label' => __( 'Title color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'.ts-field-popup .ts-term-dropdown li.ts-selected > a p'
								=> 'color: {{VALUE}}',
							],

						]
					);

					$widget->add_control(
						'ts_popup_term_icon_s',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,


							'selectors' => [
								'.ts-field-popup .ts-term-dropdown li.ts-selected > a .ts-term-icon i'
								=> 'color: {{VALUE}}',
								'.ts-field-popup .ts-term-dropdown li.ts-selected > a .ts-term-icon svg'
								=> 'fill: {{VALUE}}',
							],

						]
					);

				$widget->end_controls_tab();
				$widget->start_controls_tab(
					'ts_popup_term_parent',
					[
						'label' => __( 'Parent', 'voxel-elementor' ),
					]
				);

					$widget->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_popup_term_parent_typo',
							'label' => __( 'Title typography', 'voxel-elementor' ),
							'selector' => '.ts-field-popup .ts-term-dropdown li.ts-parent-item p',
						]
					);

				$widget->end_controls_tab();

			$widget->end_controls_tabs();

		$widget->end_controls_section();


	}

}
