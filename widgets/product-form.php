<?php

namespace Voxel\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Product_Form extends Base_Widget {

	public function get_name() {
		return 'ts-product-form';
	}

	public function get_title() {
		return __( 'Product Form (VX)', 'voxel-elementor' );
	}

	public function get_icon() {
		return 'vxi vxi-bag';
	}

	public function get_categories() {
		return [ 'voxel', 'basic' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'ts_general', [
			'label' => __( 'Product form', 'voxel-elementor' ),
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

			$post_type = \Voxel\get_current_post_type();
			if ( $post_type ) {
				$options = [ '' => 'Choose product field' ];
				foreach ( $post_type->get_fields() as $field ) {
					if ( $field->get_type() === 'product' ) {
						$options[ $field->get_key() ] = $field->get_label();
					}
				}

				$this->add_control( 'ts_product_field', [
					'label' => __( 'Product', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => '',
					'label_block' => true,
					'options' => $options,
				] );
			}






		$this->end_controls_section();

		$this->start_controls_section( 'ts_prform_settings', [
			'label' => __( 'Settings', 'voxel-elementor' ),
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

			$this->add_responsive_control(
				'show_prform_head',
				[
					'label' => __( 'Form header', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'flex',
					'options' => [
						'flex'  => __( 'Show', 'voxel-elementor' ),
						'none' => __( 'Hide', 'voxel-elementor' ),
					],

					'selectors' => [
						'{{WRAPPER}} .booking-head' => 'display: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'show_prform_footer',
				[
					'label' => __( 'Form footer', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'flex',
					'options' => [
						'flex'  => __( 'Show', 'voxel-elementor' ),
						'none' => __( 'Hide', 'voxel-elementor' ),
					],

					'selectors' => [
						'{{WRAPPER}} .tcc-container' => 'display: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'show_custom_addition',
				[
					'label' => __( 'Custom additions', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'block',
					'options' => [
						'block'  => __( 'Show', 'voxel-elementor' ),
						'none' => __( 'Hide', 'voxel-elementor' ),
					],

					'selectors' => [
						'{{WRAPPER}} .ts-custom-additions' => 'display: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'prform_stepone_text',
				[
					'label' => __( 'First step heading', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Booking details', 'voxel-elementor' ),
					'placeholder' => __( 'Type your text', 'voxel-elementor' ),
				]
			);




			$this->add_control(
				'prform_steptwo_text',
				[
					'label' => __( 'Second step heading', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Additional information', 'voxel-elementor' ),
					'placeholder' => __( 'Type your text', 'voxel-elementor' ),
				]
			);



			$this->add_control(
				'prform_continue',
				[
					'label' => __( 'Continue button label', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Continue', 'voxel-elementor' ),
					'placeholder' => __( 'Type your text', 'voxel-elementor' ),
				]
			);

			$this->add_control(
				'prform_checkout',
				[
					'label' => __( 'Checkout button label', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Checkout', 'voxel-elementor' ),
					'placeholder' => __( 'Type your text', 'voxel-elementor' ),
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
				'stepone_ico',
				[
					'label' => __( 'First step icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'steptwo_ico',
				[
					'label' => __( 'Second step icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);


			$this->add_control(
				'ts_continue_icon',
				[
					'label' => __( 'Continue icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'ts_checkout_ico',
				[
					'label' => __( 'Checkout icon', 'text-domain' ),
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
				'ts_select_icon',
				[
					'label' => __( 'Select icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_arrow_left',
				[
					'label' => __( 'Left arrow', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'ts_addition_ico',
				[
					'label' => __( 'Addition icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			// $this->add_control(
			// 	'ts_upload_ico',
			// 	[
			// 		'label' => __( 'Upload icon', 'text-domain' ),
			// 		'type' => \Elementor\Controls_Manager::ICONS,
			// 	]
			// );

			// $this->add_control(
			// 	'ts_media_ico',
			// 	[
			// 		'label' => __( 'Media icon', 'text-domain' ),
			// 		'type' => \Elementor\Controls_Manager::ICONS,
			// 	]
			// );

			// $this->add_control(
			// 	'ts_load_ico',
			// 	[
			// 		'label' => __( 'Load icon', 'text-domain' ),
			// 		'type' => \Elementor\Controls_Manager::ICONS,

			// 	]
			// );

			// $this->add_control(
			// 	'ts_select_ico',
			// 	[
			// 		'label' => __( 'Select icon', 'text-domain' ),
			// 		'type' => \Elementor\Controls_Manager::ICONS,

			// 	]
			// );


		$this->end_controls_section();

		$this->start_controls_section(
			'ts_prform_general',
			[
				'label' => __( 'Product form: General', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'prform_bg',
				[
					'label' => __( 'Background color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-booking-form' => 'background: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'prform_height',
				[
					'label' => __( 'Max height', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px'],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1000,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ts-booking-form' => 'max-height: {{SIZE}}{{UNIT}};',
					],

				]
			);

			$this->add_responsive_control(
				'prform_padding',
				[
					'label' => __( 'Padding', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-booking-form' => 'padding: {{SIZE}}{{UNIT}};',
					],

				]
			);


			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'prform_border',
					'label' => __( 'Border', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-booking-form',
				]
			);

			$this->add_responsive_control(
				'prform_radius',
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
						'{{WRAPPER}} .ts-booking-form' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'prform_shadow',
					'label' => __( 'Box Shadow', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-booking-form',
				]
			);

			$this->add_control(
				'scroll_color',
				[
					'label' => __( 'Scrollbar color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-booking-form' => '--ts-scroll-color: {{VALUE}}',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_prform_head',
			[
				'label' => __( 'Product form: Head', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);




			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'prhead_typo',
					'label' => __( 'Typography' ),
					'selector' => '{{WRAPPER}} .booking-head p',
				]
			);

			$this->add_responsive_control(
				'prhead_icon_c',
				[
					'label' => __( 'Icon color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}}  .booking-head > i' => 'color: {{VALUE}}',
						'{{WRAPPER}}  .booking-head > svg' => 'fill: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'prhead_text_c',
				[
					'label' => __( 'Text color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}}  .booking-head p' => 'color: {{VALUE}}',
					],

				]
			);




			$this->add_responsive_control(
				'prhead_icon_size',
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
						'{{WRAPPER}} .booking-head > i' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .booking-head > svg' => 'min-width: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'prhead_icon_spacing',
				[
					'label' => __( 'Icon/Text margin', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px'],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 80,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .booking-head' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'prhead_border',
				[
					'label' => __( 'Seperator color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .booking-head' => 'border-color: {{VALUE}}',
					],

				]
			);



			$this->add_responsive_control(
				'prhead_bottom_margin',
				[
					'label' => __( 'Bottom padding', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px'],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 80,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ts-booking-form.ts-form .booking-head' => 'padding-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);



		$this->end_controls_section();



		$this->start_controls_section(
			'prform_fields_general',
			[
				'label' => __( 'Fields: General', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);


				$this->add_control(
					'ts_sf_input',
					[
						'label' => __( 'Field', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_responsive_control(
					'field_spacing_value',
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
							'{{WRAPPER}} .ts-booking-form > div' => 'grid-gap: {{SIZE}}{{UNIT}};',
						],

					]
				);

				$this->add_control(
					'ts_sf_input_lbl',
					[
						'label' => __( 'Field label', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);


				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' => 'ts_sf_input_label_text',
						'label' => __( 'Typography' ),
						'selector' => '{{WRAPPER}} .ts-form-group label',
					]
				);


				$this->add_responsive_control(
					'ts_sf_input_label_col',
					[
						'label' => __( 'Color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-form-group label' => 'color: {{VALUE}}',
						],

					]
				);

				$this->add_responsive_control(
					'ts_intxt_double',
					[
						'label' => __( 'Double field spacing', 'voxel-elementor' ),
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
							'{{WRAPPER}} .ts-double-input' => 'grid-gap: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'sf_input_label_padding',
					[
						'label' => __( 'Label padding', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px'],
						'selectors' => [
							'{{WRAPPER}}  .ts-form-group label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'ts_field_desc_h',
					[
						'label' => __( 'Field description', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);


				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' => 'ts_field_desc_t',
						'label' => __( 'Typography' ),
						'selector' => '{{WRAPPER}} .ts-form-group small',
					]
				);


				$this->add_responsive_control(
					'ts_field_desc_col',
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
				'ts_sf_styling_filters',
				[
					'label' => __( 'Popup button', 'voxel-elementor' ),
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
									'{{WRAPPER}} .ts-form .ts-filter-text' => 'color: {{VALUE}}',
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
								'selectors' => [
									'{{WRAPPER}} div.ts-filter i' => 'font-size: {{SIZE}}{{UNIT}};',
									'{{WRAPPER}} div.ts-filter svg' => 'min-width: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
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
									'{{WRAPPER}} .ts-form div.ts-filter:hover' => 'border-color: {{VALUE}}',
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
			'ts_sf_intxt',
			[
				'label' => __( 'Form: Input & Textarea', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
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
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_intxt_shadow',
							'label' => __( 'Box Shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-form textarea.ts-filter,{{WRAPPER}} .ts-form input.ts-filter',
						]
					);

					$this->add_responsive_control(
						'ts_intxt_padding',
						[
							'label' => __( 'Padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .ts-form textarea.ts-filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
								'{{WRAPPER}} .ts-form input.ts-filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
						'ts_intxt_textarea_height',
						[
							'label' => __( 'Height', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 1500,
									'step' => 1,
								],
							],
							'selectors' => [
								'{{WRAPPER}}  .ts-form textarea.ts-filter' => 'height: {{SIZE}}{{UNIT}};',
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
								'{{WRAPPER}} .ts-form .mce-content-body:hover' => 'background: {{VALUE}};',
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
								'{{WRAPPER}} .ts-form .mce-content-body:hover' => 'border-color: {{VALUE}};',
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
								'{{WRAPPER}} .ts-form textarea.ts-filter:hover::placeholder' => 'color: {{VALUE}}',

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
								'{{WRAPPER}} .ts-form .mce-content-body:focus' => 'background: {{VALUE}};',
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
								'{{WRAPPER}} .ts-form .mce-content-body:focus' => 'border-color: {{VALUE}};',
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
			'prform_timeslots',
			[
				'label' => __( 'Time/Date slots', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
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
						'{{WRAPPER}} .ts-pick-slot' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
					],
				]
			);

			$this->add_responsive_control(
				'prf_timeslot_gap',
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
						'{{WRAPPER}} .ts-pick-slot' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],

				]
			);

			$this->add_control(
				'prf_timeslot_padding',
				[
					'label' => __( 'Padding', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px'],
					'selectors' => [
						'{{WRAPPER}} .ts-pick-slot li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'prf_timeslot_justify',
				[
					'label' => __( 'Justify', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'flex-start'  => __( 'Left', 'voxel-elementor' ),
						'center' => __( 'Center', 'voxel-elementor' ),
						'flex-end' => __( 'Right', 'voxel-elementor' ),
					],

					'selectors' => [
						'{{WRAPPER}} .ts-pick-slot li a' => 'justify-content: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'prf_timeslot_bg',
				[
					'label' => __( 'Background color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-pick-slot li a' => 'background: {{VALUE}}',
					],

				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'prf_timeslot_border',
					'label' => __( 'Border', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-pick-slot li a',
				]
			);

			$this->add_responsive_control(
				'prf_timeslot_border_hover',
				[
					'label' => __( 'Border color (Hover)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-pick-slot a:hover' => 'border-color: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'prf_timeslot_radius',
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
						'{{WRAPPER}} .ts-pick-slot li a' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'prf_timeslot_text',
				[
					'label' => __( 'Text color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-pick-slot li a span' => 'color: {{VALUE}}',
					],

				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'prf_timeslot_typo',
					'label' => __( 'Typography', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-pick-slot li a span',
				]
			);

			$this->add_responsive_control(
				'prf_timeslot_ico',
				[
					'label' => __( 'Icon color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-pick-slot li a i' => 'color: {{VALUE}}',
						'{{WRAPPER}} .ts-pick-slot li a svg' => 'fill: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'prf_timeslot_ico_size',
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
						'{{WRAPPER}} .ts-pick-slot li a i' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ts-pick-slot li a svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'prf_timeslot_ico_spacing',
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
						'{{WRAPPER}} .ts-pick-slot li a' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'prf_timeslot_active',
				[
					'label' => __( 'Active', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'prf_timeslot_bg_a',
				[
					'label' => __( 'Background color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-pick-slot li.slot-picked a' => 'background: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'prf_timeslot_border_a',
				[
					'label' => __( 'Border color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-pick-slot li.slot-picked a' => 'border-color: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'prf_timeslot_text_a',
				[
					'label' => __( 'Text color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-pick-slot li.slot-picked a span' => 'color: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'prf_timeslot_ico_a',
				[
					'label' => __( 'Icon color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-pick-slot li.slot-picked a i' => 'color: {{VALUE}}',
						'{{WRAPPER}} .ts-pick-slot li.slot-picked a svg' => 'fill: {{VALUE}}',
					],

				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'prf_timeslot_typo_A',
					'label' => __( 'Typography', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-pick-slot li.slot-picked a span',
				]
			);



		$this->end_controls_section();





		$this->start_controls_section(
			'ts_booking_submit',
			[
				'label' => __( 'Submit button', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'ts_submit_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'ts_submit_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'submit_general',
						[
							'label' => __( 'General', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);




					$this->add_responsive_control(
						'sub_icon_size',
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
								'{{WRAPPER}} .ts-booking-submit i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-booking-submit svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_sf_search_button',
						[
							'label' => __( 'Button', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);


					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_submit_btn_typo',
							'label' => __( 'Typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-booking-submit',
						]
					);

					$this->add_responsive_control(
						'ts_sf_form_btn_c',
						[
							'label' => __( 'Color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-booking-submit' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-booking-submit svg' => 'fill: {{VALUE}}',
							],

						]
					);




					$this->add_responsive_control(
						'ts_sf_form_btn_bg',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-booking-submit' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_sf_form_btn_height',
						[
							'label' => __( 'Button Height', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-booking-submit' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_sf_search_padding',
						[
							'label' => __( 'Button padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .ts-booking-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_sf_search_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-booking-submit',
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
								'{{WRAPPER}} .ts-booking-submit' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_submit_ico_pad',
						[
							'label' => __( 'Icon/Text spacing', 'voxel-elementor' ),
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
								'{{WRAPPER}}  .ts-booking-submit' => 'grid-gap: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_submit_ico_shadow',
							'label' => __( 'Box Shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}}  .ts-booking-submit',
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


					$this->add_control(
						'ts_sf_form_btn_c_h',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-booking-submit:hover' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-booking-submit:hover svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_sf_form_btn_bg_h',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-booking-submit:hover' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_sf_form_btn_border_h',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}}  .ts-booking-submit:hover' => 'border-color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_submit_ico_shadow_h',
							'label' => __( 'Box Shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}}  .ts-booking-submit:hover',
						]
					);



				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_sf_field_switch',
			[
				'label' => __( 'Form: Switcher', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
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
			'prform_calculator',
			[
				'label' => __( 'Form: Price calculator', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'calc_list_gap',
				[
					'label' => __( 'List spacing', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-cost-calculator' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],

				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'calc_text',
					'label' => __( 'Typography' ),
					'selector' => '{{WRAPPER}} .ts-cost-calculator li p',
				]
			);

			$this->add_control(
				'calc_text_color',
				[
					'label' => __( 'Text color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-cost-calculator li p'
						=> 'color: {{VALUE}}',
					],

				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'calc_text_total',
					'label' => __( 'Typography (Total)' ),
					'selector' => '{{WRAPPER}} .ts-cost-calculator li.ts-total p',
				]
			);

			$this->add_control(
				'calc_text_color_total',
				[
					'label' => __( 'Text color (Total)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-cost-calculator li.ts-total p'
						=> 'color: {{VALUE}}',
					],

				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'number_add',
			[
				'label' => __( 'Form: Stepper buttons', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'number_add_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'no_add_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'no_add_value',
						[
							'label' => __( 'Input', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'noadd_value_typo',
							'label' => __( 'Typography' ),
							'selector' => '{{WRAPPER}} .ts-stepper-input input',
						]
					);

					$this->add_control(
						'noadd_button',
						[
							'label' => __( 'Button styling', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_responsive_control(
						'noadd_btn_size',
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
								'{{WRAPPER}} .ts-stepper-input .ts-icon-btn' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'noadd_btn_color',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-stepper-input .ts-icon-btn i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-stepper-input .ts-icon-btn svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'noadd_btn_icon_size',
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
								'{{WRAPPER}} .ts-stepper-input .ts-icon-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-stepper-input .ts-icon-btn svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'noadd_btn_bg',
						[
							'label' => __( 'Button background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-stepper-input .ts-icon-btn' => 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'noadd_btn_border',
							'label' => __( 'Button border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-stepper-input .ts-icon-btn',
						]
					);

					$this->add_responsive_control(
						'noadd_btn_radius',
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
								'{{WRAPPER}} .ts-stepper-input .ts-icon-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);



				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'noadd_button_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'noadd_btn_h',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-stepper-input .ts-icon-btn:hover i' => 'color: {{VALUE}};',
								'{{WRAPPER}} .ts-stepper-input .ts-icon-btn:hover svg' => 'fill: {{VALUE}};',
							],

						]
					);

					$this->add_control(
						'noadd_btn_bg_h',
						[
							'label' => __( 'Button background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-stepper-input .ts-icon-btn:hover'
								=> 'background-color: {{VALUE}};',
							],

						]
					);

					$this->add_control(
						'noadd_border_c_h',
						[
							'label' => __( 'Button border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-stepper-input .ts-icon-btn:hover'
								=> 'border-color: {{VALUE}};',
							],

						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'auth_checkbox_section',
			[
				'label' => __( 'Form: Checkbox addition', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
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
						'{{WRAPPER}} .ts-addition-list .container-checkbox .checkmark' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
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
						'{{WRAPPER}} .ts-addition-list .container-checkbox .checkmark' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'check_border',
					'label' => __( 'Border', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-addition-list .container-checkbox .checkmark',
				]
			);

			$this->add_responsive_control(
				'unchecked_bg',
				[
					'label' => __( 'Background color (unchecked)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-addition-list .container-checkbox .checkmark' => 'background-color: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'checked_bg',
				[
					'label' => __( 'Background color (checked)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-addition-list .container-checkbox input:checked ~ .checkmark' => 'background-color: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'checked_border',
				[
					'label' => __( 'Border-color (checked)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-addition-list .container-checkbox input:checked ~ .checkmark' => 'border-color: {{VALUE}}',
					],

				]
			);



		$this->end_controls_section();

		$this->start_controls_section(
			'auth_radio_section',
			[
				'label' => __( 'Form: Radio addition', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
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
						'{{WRAPPER}} .ts-addition-list .container-radio .checkmark' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
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
						'{{WRAPPER}} .ts-addition-list .container-radio .checkmark' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'radio_border',
					'label' => __( 'Border', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-addition-list .container-radio .checkmark',
				]
			);

			$this->add_responsive_control(
				'unchecked_radio_bg',
				[
					'label' => __( 'Background color (unchecked)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-addition-list .container-radio .checkmark' => 'background-color: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'checked_radio_bg',
				[
					'label' => __( 'Background color (checked)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-addition-list .container-radio input:checked ~ .checkmark' => 'background-color: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'checked_radio_border',
				[
					'label' => __( 'Border-color (checked)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-addition-list .container-radio input:checked ~ .checkmark' => 'border-color: {{VALUE}}',
					],

				]
			);



		$this->end_controls_section();

		$this->start_controls_section(
			'addition_list',
			[
				'label' => __( 'Form: Custom additions', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'ad_typo',
					'label' => __( 'Typography' ),
					'selector' => '{{WRAPPER}} .ts-addition-list li span',
				]
			);

			$this->add_responsive_control(
				'ad_text_color',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-addition-list li span' => 'color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'ad_gap',
				[
					'label' => __( 'Item gap', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-addition-list' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'ad_min_height',
				[
					'label' => __( 'Item min height', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-addition-list li' => 'min-height: {{SIZE}}{{UNIT}};',
					],
				]
			);

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
								'{{WRAPPER}} .ts-btn-4 i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-btn-4 svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'tertiary_btn_icon_size',
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
								'{{WRAPPER}} .ts-btn-4 i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-btn-4 svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'tertiary_btn_height',
						[
							'label' => __( 'Button Height', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-btn-4' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'tertiary_btn_bg',
						[
							'label' => __( 'Button background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-4'
								=> 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'tertiary_btn_border',
							'label' => __( 'Button border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-btn-4',
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
								'{{WRAPPER}} .ts-btn-4' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'tertiary_btn_text',
							'label' => __( 'Typography' ),
							'selector' => '{{WRAPPER}} .ts-btn-4',
						]
					);

					$this->add_control(
						'tertiary_btn_text_color',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-4'
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
								'{{WRAPPER}} .ts-btn-4:hover i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-btn-4:hover svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'tertiary_btn_bg_h',
						[
							'label' => __( 'Button background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-4:hover'
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
								'{{WRAPPER}} .ts-btn-4:hover'
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
								'{{WRAPPER}} .ts-btn-4:hover'
								=> 'color: {{VALUE}}',
							],

						]
					);


				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->apply_controls( Option_Groups\File_Field::class );


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


		$this->end_controls_section();




	}

	protected function render( $instance = [] ) {
		$post = \Voxel\get_current_post();
		$field = $post ? $post->get_field( $this->get_settings_for_display( 'ts_product_field' ) ) : null;
		$product_type = $field ? $field->get_product_type() : null;
		$config = $field ? $field->get_product_form_config() : null;
		if ( ! ( $post && $field && $product_type && $config && ( $config['enabled'] || $field->is_required() ) ) ) {
			return;
		}

		if ( $product_type->get_product_mode() === 'claim' && $post->is_verified() ) {
			return;
		}

		wp_print_styles( $this->get_style_depends() );
		require locate_template( 'templates/widgets/product-form.php' );

		if ( \Voxel\is_edit_mode() ) {
			printf( '<script type="text/javascript">%s</script>', 'window.render_product_form();' );
		}
	}

	public function get_script_depends() {
		return [
			'pikaday',
			'vx:product-form.js',
		];
	}

	public function get_style_depends() {
		return [
			'vx:forms.css',
			'pikaday',
			'vx:product-form.css',
		];
	}

	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}

	protected function register_runtime_controls() {
		$this->add_control( 'ts_product_field', [
			'label' => __( 'Product', 'voxel-elementor' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'label_block' => true,
		] );

		$this->add_control(
			'stepone_ico',
			[
				'label' => __( 'First step icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'steptwo_ico',
			[
				'label' => __( 'Second step icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);


		$this->add_control(
			'ts_continue_icon',
			[
				'label' => __( 'Continue icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'ts_checkout_ico',
			[
				'label' => __( 'Checkout icon', 'text-domain' ),
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
			'ts_select_icon',
			[
				'label' => __( 'Select icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_arrow_left',
			[
				'label' => __( 'Left arrow', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'ts_addition_ico',
			[
				'label' => __( 'Addition icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'prform_stepone_text',
			[
				'label' => __( 'First step heading', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Booking details', 'voxel-elementor' ),
				'placeholder' => __( 'Type your text', 'voxel-elementor' ),
			]
		);

		$this->add_control(
			'prform_steptwo_text',
			[
				'label' => __( 'Second step heading', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Additional information', 'voxel-elementor' ),
				'placeholder' => __( 'Type your text', 'voxel-elementor' ),
			]
		);

		$this->add_control(
			'prform_continue',
			[
				'label' => __( 'Continue button label', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Continue', 'voxel-elementor' ),
				'placeholder' => __( 'Type your text', 'voxel-elementor' ),
			]
		);

		$this->add_control(
			'prform_checkout',
			[
				'label' => __( 'Checkout button label', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Checkout', 'voxel-elementor' ),
				'placeholder' => __( 'Type your text', 'voxel-elementor' ),
			]
		);

	}
}
