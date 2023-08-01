<?php

namespace Voxel\Widgets\Option_Groups;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Popup_Controller {

	public static function controls( $widget ) {

		$widget->start_controls_section(
			'ts_sf_popup_controls',
			[
				'label' => __( 'Popup: Control buttons', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$widget->start_controls_tabs(
				'ts_popup_control_tabs'
			);

				/* Normal tab */

				$widget->start_controls_tab(
					'ts_sfc_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);

					$widget->add_control(
						'ts_popup_btn_general',
						[
							'label' => __( 'General', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);



					$widget->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_popup_btn_typo',
							'label' => __( 'Button typography', 'voxel-elementor' ),
							'selector' => '.ts-popup-controller .ts-btn',
						]
					);

					$widget->add_control(
						'ts_popup_btn_padding',
						[
							'label' => __( 'Button padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								' .ts-popup-controller .ts-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$widget->add_responsive_control(
						'ts_popup_btn_height',
						[
							'label' => __( 'Height', 'voxel-elementor' ),
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
								'.ts-popup-controller .ts-btn' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$widget->add_responsive_control(
						'ts_popup_btn_radius',
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
								'.ts-popup-controller .ts-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$widget->add_control(
						'ts_popup_controller_border',
						[
							'label' => __( 'Separator color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'.ts-field-popup .ts-popup-controller' => 'border-color: {{VALUE}}',
							],
						]
					);

					$widget->add_control(
						'ts_popup_clear',
						[
							'label' => __( 'Clear button', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);


					$widget->add_control(
						'ts_popup_button_1',
						[
							'label' => __( 'Background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'.ts-popup-controller .ts-btn-1' => 'background: {{VALUE}}',
							],
						]
					);

					$widget->add_control(
						'ts_popup_button_1_c',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'.ts-popup-controller .ts-btn-1' => 'color: {{VALUE}}',
							],
						]
					);

					$widget->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_popup_button_1_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '.ts-popup-controller .ts-btn-1',
						]
					);



					$widget->add_control(
						'ts_popup_submit',
						[
							'label' => __( 'Submit button', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$widget->add_control(
						'ts_popup_button_2',
						[
							'label' => __( 'Background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'.ts-popup-controller .ts-btn-2' => 'background: {{VALUE}}',
							],
						]
					);

					$widget->add_control(
						'ts_popup_button_2_c',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'.ts-popup-controller .ts-btn-2' => 'color: {{VALUE}}',
							],
						]
					);

					$widget->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_popup_button_2_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '.ts-popup-controller .ts-btn-2',
						]
					);




				$widget->end_controls_tab();


				/* Hover tab */

				$widget->start_controls_tab(
					'ts_sfc_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);


					$widget->add_control(
						'ts_popup_clear_h',
						[
							'label' => __( 'Clear button', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);


					$widget->add_control(
						'ts_popup_button_1_h',
						[
							'label' => __( 'Background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'.ts-popup-controller .ts-btn-1:hover' => 'background: {{VALUE}}',
							],
						]
					);

					$widget->add_control(
						'ts_popup_button_1_c_h',
						[
							'label' => __( 'Button color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'.ts-popup-controller .ts-btn-1:hover' => 'color: {{VALUE}}',
							],
						]
					);

					$widget->add_control(
						'ts_popup_button_1_b_h',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'.ts-popup-controller .ts-btn-1:hover' => 'border-color: {{VALUE}}',
							],
						]
					);

					$widget->add_control(
						'ts_popup_submit_H',
						[
							'label' => __( 'Submit button', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$widget->add_control(
						'ts_popup_button_2_h',
						[
							'label' => __( 'Background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'.ts-popup-controller .ts-btn-2:hover' => 'background: {{VALUE}}',
							],
						]
					);

					$widget->add_control(
						'ts_popup_button_2_c_h',
						[
							'label' => __( 'Button color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'.ts-popup-controller .ts-btn-2:hover' => 'color: {{VALUE}}',
							],
						]
					);

					$widget->add_control(
						'ts_popup_button_2_b_h',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'.ts-popup-controller .ts-btn-2:hover' => 'border-color: {{VALUE}}',
							],
						]
					);



				$widget->end_controls_tab();

			$widget->end_controls_tabs();

		$widget->end_controls_section();
	}

}
