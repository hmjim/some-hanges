<?php

namespace Voxel\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Login extends Base_Widget {

	public function get_name() {
		return 'ts-login';
	}

	public function get_title() {
		return __( 'Login / Register (VX)', 'voxel-elementor' );
	}

	public function get_icon() {
		return 'vxi vxi-user';
	}

	public function get_categories() {
		return [ 'voxel', 'basic' ];
	}

	protected function register_controls() {

		$this->start_controls_section( 'auth_content', [
			'label' => __( 'General', 'voxel-elementor' ),
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

			$this->add_control( 'ts_view_screen', [
				'label' => __( 'View screen', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'login',
				'options' => [
					'login'  => __( 'Login', 'voxel-elementor' ),
					'register' => __( 'Register', 'voxel-elementor' ),
					'confirm_account' => __( 'Confirm account', 'voxel-elementor' ),
					'recover' => __( 'Recover', 'voxel-elementor' ),
					'recover_confirm' => __( 'Recover confirm code', 'voxel-elementor' ),
					'recover_set_password' => __( 'Recover set password', 'voxel-elementor' ),
					'welcome' => __( 'Welcome', 'voxel-elementor' ),
					'security' => __( 'Security', 'voxel-elementor' ),
					'security_update_password' => __( 'Update password', 'voxel-elementor' ),
					'security_update_email' => __( 'Update email', 'voxel-elementor' ),
				],
			] );



			$this->add_control(
				'auth_title',
				[
					'label' => esc_html__( 'Login title', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Hello visitor!', 'voxel-elementor' ),
					'placeholder' => esc_html__( 'Type text', 'voxel-elementor' ),
				]
			);

			$this->add_control(
				'auth_reg_title',
				[
					'label' => esc_html__( 'Register title', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Create an account', 'voxel-elementor' ),
					'placeholder' => esc_html__( 'Type text', 'voxel-elementor' ),
				]
			);

			$this->add_control(
				'confirm_title',
				[
					'label' => esc_html__( 'Confirm title', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Confirm email', 'voxel-elementor' ),
					'placeholder' => esc_html__( 'Type text', 'voxel-elementor' ),
				]
			);

			$this->add_control(
				'reset_pass_title',
				[
					'label' => esc_html__( 'Password recovery title', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Password recovery', 'voxel-elementor' ),
					'placeholder' => esc_html__( 'Type text', 'voxel-elementor' ),
				]
			);

			$this->add_control(
				'confirm_code',
				[
					'label' => esc_html__( 'Confirm code title', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Confirm code', 'voxel-elementor' ),
					'placeholder' => esc_html__( 'Type text', 'voxel-elementor' ),
				]
			);

			$this->add_control(
				'new_password',
				[
					'label' => esc_html__( 'New password title', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'New password', 'voxel-elementor' ),
					'placeholder' => esc_html__( 'Type text', 'voxel-elementor' ),
				]
			);

			$this->add_control(
				'update_password',
				[
					'label' => esc_html__( 'Update password title', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Update password', 'voxel-elementor' ),
					'placeholder' => esc_html__( 'Type text', 'voxel-elementor' ),
				]
			);

			$this->add_control(
				'update_email',
				[
					'label' => esc_html__( 'Update email title', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Update email', 'voxel-elementor' ),
					'placeholder' => esc_html__( 'Type text', 'voxel-elementor' ),
				]
			);

			$this->add_control(
				'auth_welc_title',
				[
					'label' => esc_html__( 'Welcome title', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Welcome!', 'voxel-elementor' ),
					'placeholder' => esc_html__( 'Type text', 'voxel-elementor' ),
				]
			);

			$this->add_control(
				'auth_welc_subtitle',
				[
					'label' => esc_html__( 'Welcome subtitle', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Complete your profile or skip for now', 'voxel-elementor' ),
					'placeholder' => esc_html__( 'Type text', 'voxel-elementor' ),
				]
			);




		$this->end_controls_section();

		$this->start_controls_section( 'auth_icons', [
			'label' => __( 'Icons', 'voxel-elementor' ),
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

			$this->add_control( 'auth_google_ico', [
				'label' => __( 'Google icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			] );

			$this->add_control( 'auth_user_ico', [
				'label' => __( 'Username icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			] );

			$this->add_control( 'auth_pass_ico', [
				'label' => __( 'Password icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			] );

			$this->add_control( 'auth_email_ico', [
				'label' => __( 'Email icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			] );

			$this->add_control( 'auth_welcome_ico', [
				'label' => __( 'Welcome icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			] );

			$this->add_control(
				'ts_chevron_left',
				[
					'label' => __( 'Left chevron', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'ts_privacy',
				[
					'label' => __( 'Privacy icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'ts_trash',
				[
					'label' => __( 'Trash icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);

			$this->add_control(
				'ts_logout',
				[
					'label' => __( 'Logout icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,
				]
			);




		$this->end_controls_section();

		$this->start_controls_section( 'auth_style', [
			'label' => __( 'General', 'voxel-elementor' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		] );

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'auth_heading_t',
					'label' => __( 'Title typography' ),
					'selector' => '{{WRAPPER}} .ts-login-head p',
				]
			);

			$this->add_responsive_control(
				'ts_sf_input_label_col',
				[
					'label' => __( 'Title color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-login-head p' => 'color: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'ts_section_spacing',
				[
					'label' => __( 'Section spacing', 'voxel-elementor' ),
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
						'{{WRAPPER}} .login-section' => 'margin-top: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'ts_fg_spacing',
				[
					'label' => __( 'Field spacing', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-login .ts-form-group' => 'padding-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'auth_primary_btn',
			[
				'label' => __( 'Primary button', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'one_btn_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'one_btn_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);



					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'one_btn_typo',
							'label' => __( 'Button typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-login .ts-btn-2',
						]
					);


					$this->add_responsive_control(
						'one_btn_radius',
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
								'{{WRAPPER}} .ts-login .ts-btn-2' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'one_btn_c',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-btn-2' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'one_btn_padding',
						[
							'label' => __( 'Padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-btn-2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'one_btn_height',
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
								'{{WRAPPER}} .ts-login .ts-btn-2' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);


					$this->add_responsive_control(
						'one_btn_bg',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-btn-2' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'one_btn_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-login .ts-btn-2',
						]
					);


					$this->add_responsive_control(
						'one_btn_icon_size',
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
								'{{WRAPPER}} .ts-login .ts-btn-2 i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-login .ts-btn-2 svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'one_btn_icon_pad',
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
								'{{WRAPPER}} .ts-login .ts-btn-2' => 'grid-gap: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'one_btn_icon_color',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-btn-2 i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-login .ts-btn-2 svg' => 'fill: {{VALUE}}',
							],

						]
					);



				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'one_btn_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_responsive_control(
						'one_btn_c_h',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-btn-2:hover' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'one_btn_bg_h',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-btn-2:hover' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'one_btn_border_h',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-btn-2:hover' => 'border-color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'one_btn_icon_color_h',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-btn-2:hover i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-login .ts-btn-2:hover svg' => 'fill: {{VALUE}}',
							],

						]
					);



				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'auth_scnd_btn',
			[
				'label' => __( 'Secondary button', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'scnd_btn_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'scnd_btn_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);



					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'scnd_btn_typo',
							'label' => __( 'Button typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-login .ts-btn-1',
						]
					);


					$this->add_responsive_control(
						'scnd_btn_radius',
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
								'{{WRAPPER}} .ts-login .ts-btn-1' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'scnd_btn_c',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-btn-1' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'scnd_btn_padding',
						[
							'label' => __( 'Padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-btn-1' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'scnd_btn_height',
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
								'{{WRAPPER}} .ts-login .ts-btn-1' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);


					$this->add_responsive_control(
						'scnd_btn_bg',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-btn-1' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'scnd_btn_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-login .ts-btn-1',
						]
					);


					$this->add_responsive_control(
						'scnd_btn_icon_size',
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
								'{{WRAPPER}} .ts-login .ts-btn-1 i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-login .ts-btn-1 svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'scnd_btn_icon_pad',
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
								'{{WRAPPER}} .ts-login .ts-btn-1' => 'grid-gap: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'scnd_btn_icon_color',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-btn-1 i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-login .ts-btn-1 svg' => 'fill: {{VALUE}}',
							],

						]
					);



				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'scnd_btn_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_responsive_control(
						'scnd_btn_c_h',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-btn-1:hover' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'scnd_btn_bg_h',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-btn-1:hover' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'scnd_btn_border_h',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-btn-1:hover' => 'border-color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'scnd_btn_icon_color_h',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-btn-1:hover i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-login .ts-btn-1:hover svg' => 'fill: {{VALUE}}',
							],

						]
					);



				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'auth_google_btn',
			[
				'label' => __( 'Google button', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'google_btn_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'google_btn_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);



					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'google_btn_typo',
							'label' => __( 'Button typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-login .ts-google-btn',
						]
					);


					$this->add_responsive_control(
						'google_btn_radius',
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
								'{{WRAPPER}} .ts-login .ts-google-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'google_btn_c',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-google-btn' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'google_btn_padding',
						[
							'label' => __( 'Padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-google-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'gl_btn_height',
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
								'{{WRAPPER}} .ts-google-btn' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);


					$this->add_responsive_control(
						'google_btn_bg',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-google-btn' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'google_btn_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-login .ts-google-btn',
						]
					);


					$this->add_responsive_control(
						'google_btn_icon_size',
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
								'{{WRAPPER}} .ts-login .ts-google-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-login .ts-google-btn svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'google_btn_icon_pad',
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
								'{{WRAPPER}} .ts-login .ts-google-btn' => 'grid-gap: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'google_btn_icon_color',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-google-btn i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-login .ts-google-btn svg' => 'fill: {{VALUE}}',
							],

						]
					);



				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'google_btn_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);

					$this->add_responsive_control(
						'google_btn_c_h',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-google-btn:hover' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'google_btn_bg_h',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-google-btn:hover' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'google_btn_border_h',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-google-btn:hover' => 'border-color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'google_btn_icon_color_h',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-login .ts-google-btn:hover i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-login .ts-google-btn:hover svg' => 'fill: {{VALUE}}',
							],

						]
					);



				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'auth_input',
			[
				'label' => __( 'Input', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs(
				'auth_input_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'auth_input_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);


					$this->add_control(
						'auth_input_height',
						[
							'label' => __( 'Input height', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-form input' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'auth_input_radius',
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
								'{{WRAPPER}} .ts-form input' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'auth_input_font',
							'label' => __( 'Typography' ),
							'selector' => '{{WRAPPER}} .ts-form input',
						]
					);

					$this->add_control(
						'auth_input_padding',
						[
							'label' => __( 'Input padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .ts-form input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'auth_input_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-form input',
						]
					);

					$this->add_control(
						'auth_input_bg',
						[
							'label' => __( 'Input background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form input' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'auth_input_background_filled',
						[
							'label' => __( 'Input background color (Focus)', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form input:focus' => 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'auth_input_value_col',
						[
							'label' => __( 'Input value color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form input' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'auth_input_placeholder_color',
						[
							'label' => __( 'Input placeholder color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form input::-webkit-input-placeholder' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-form input:-moz-placeholder' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-form input::-moz-placeholder' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-form input:-ms-input-placeholder' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'auth_input_icon_c',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-input-icon > i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-input-icon > svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'auth_input_icon_size',
						[
							'label' => __( 'Input icon size', 'voxel-elementor' ),
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
							'default' => [
								'unit' => 'px',
								'size' => 22,
							],
							'selectors' => [
								'{{WRAPPER}} .ts-input-icon > i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-input-icon > svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'auth_input_icon_margin',
						[
							'label' => __( 'Input icon left margin', 'voxel-elementor' ),
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
							'default' => [
								'unit' => 'px',
								'size' => 15,
							],
							'selectors' => [
								'{{WRAPPER}} .ts-input-icon > i' => 'left: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-input-icon > svg' => 'left: {{SIZE}}{{UNIT}};',
							],
						]
					);

				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'auth_input_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);


					$this->add_control(
						'auth_input_h',
						[
							'label' => __( 'Input', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_control(
						'auth_input_bg_h',
						[
							'label' => __( 'Input background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form input:hover' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'auth_input_h_border',
						[
							'label' => __( 'Input border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form input:hover' => 'border-color: {{VALUE}}',
							],

						]
					);


				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'auth_label_section',
			[
				'label' => __( 'Label and description', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'auth_label',
				[
					'label' => __( 'Label', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);


			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'auth_label_typo',
					'label' => __( 'Typography' ),
					'selector' => '{{WRAPPER}} .ts-form-group label, {{WRAPPER}} .container-checkbox p',
				]
			);


			$this->add_responsive_control(
				'auth_label_col',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-form-group label,{{WRAPPER}} .container-checkbox p' => 'color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'auth_desc',
				[
					'label' => __( 'Description', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);


			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'auth_desc_t',
					'label' => __( 'Typography' ),
					'selector' => '{{WRAPPER}} .ts-form-group small',
				]
			);


			$this->add_responsive_control(
				'auth_desc_col',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}}  .ts-form-group small' => 'color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'auth_link',
				[
					'label' => __( 'Link', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'auth_link_t',
					'label' => __( 'Typography' ),
					'selector' => '{{WRAPPER}} .ts-form-group label a',
				]
			);


			$this->add_responsive_control(
				'auth_link_col',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}}  .ts-form-group label a' => 'color: {{VALUE}}',
					],

				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'auth_welcome_section',
			[
				'label' => __( 'Welcome', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'welc_align',
				[
					'label' => __( 'Align content', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'center',
					'options' => [
						'flex-start'  => __( 'Left', 'voxel-elementor' ),
						'center' => __( 'Center', 'voxel-elementor' ),
						'flex-end' => __( 'Right', 'voxel-elementor' ),
					],
					'selectors' => [
						'{{WRAPPER}} .ts-welcome-message' => 'align-items: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'welc_align_text',
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
						'{{WRAPPER}} .ts-welcome-message' => 'text-align: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'welc_ico',
				[
					'label' => __( 'Welcome icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'welc_ico_size',
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
						'{{WRAPPER}} .ts-welcome-message i' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ts-welcome-message svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'welc_ico_color',
				[
					'label' => __( 'Icon color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-welcome-message i' => 'color: {{VALUE}}',
						'{{WRAPPER}} .ts-welcome-message svg' => 'fill: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'welc_heading',
				[
					'label' => __( 'Welcome heading', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'welc_heading_t',
					'label' => __( 'Typography' ),
					'selector' => '{{WRAPPER}} .ts-welcome-message h2',
				]
			);

			$this->add_responsive_control(
				'welc_heading_col',
				[
					'label' => __( 'Color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-welcome-message h2' => 'color: {{VALUE}}',
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
						'{{WRAPPER}} .ts-welcome-message h2' => 'margin-top: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'auth_checkbox_section',
			[
				'label' => __( 'Checkbox', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-form .ts-form-group.tos-group .container-checkbox .checkmark' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
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
						'{{WRAPPER}} .container-checkbox .checkmark' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'check_border',
					'label' => __( 'Border', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .container-checkbox .checkmark',
				]
			);

			$this->add_responsive_control(
				'unchecked_bg',
				[
					'label' => __( 'Background color (unchecked)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .container-checkbox .checkmark' => 'background-color: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'checked_bg',
				[
					'label' => __( 'Background color (checked)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .container-checkbox input:checked ~ .checkmark' => 'background-color: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'checked_border',
				[
					'label' => __( 'Border-color (checked)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .container-checkbox input:checked ~ .checkmark' => 'border-color: {{VALUE}}',
					],

				]
			);



		$this->end_controls_section();
	}

	protected function render( $instance = [] ) {
		$config = [
			'screen' => 'login',
			'nonce' => wp_create_nonce( 'vx_auth' ),
			'redirectUrl' => \Voxel\get_redirect_url(),
			'register_enabled' => \Voxel\get( 'settings.membership.enabled', true ),
			'recaptcha' => [
				'enabled' => \Voxel\get('settings.recaptcha.enabled'),
				'key' => \Voxel\get('settings.recaptcha.key'),
			],
		];

		if ( \Voxel\get('settings.recaptcha.enabled') ) {
			wp_enqueue_script( 'google-recaptcha' );
		}

		// set default screen
		if ( \Voxel\is_edit_mode() && ( $screen = $this->get_settings_for_display( 'ts_view_screen' ) ) ) {
			$config['screen'] = $this->get_settings_for_display( 'ts_view_screen' );
		} elseif ( is_user_logged_in() ) {
			if ( isset( $_GET['welcome'] ) ) {
				$user = \Voxel\current_user();
				$profile = $user->get_or_create_profile();
				$config['screen'] = 'welcome';
				$config['editProfileUrl'] = $profile ? $profile->get_edit_link() : null;
				$config['userDisplayName'] = $user->get_display_name();
			} else {
				$config['screen'] = 'security';
			}
		} elseif ( isset( $_GET['register'] ) && \Voxel\get( 'settings.membership.enabled', true ) ) {
			$config['screen'] = 'register';
		} else {
			$config['screen'] = 'login';
		}

		wp_print_styles( $this->get_style_depends() );
		require locate_template( 'templates/widgets/login.php' );

		if ( \Voxel\is_edit_mode() ) {
			printf( '<script type="text/javascript">%s</script>', 'window.render_auth();' );
		}
	}

	public function get_script_depends() {
		return [
			'vx:auth.js',
		];
	}

	public function get_style_depends() {
		return [ 'vx:forms.css', 'vx:login.css' ];
	}

	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}

	protected function register_runtime_controls() {
		$this->add_control( 'ts_view_screen', [
			'label' => __( 'View screen', 'voxel-elementor' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'login',
		] );

		$this->add_control(
			'auth_title',
			[
				'label' => esc_html__( 'Login title', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Hello visitor!', 'voxel-elementor' ),
				'placeholder' => esc_html__( 'Type text', 'voxel-elementor' ),
			]
		);

		$this->add_control(
			'auth_reg_title',
			[
				'label' => esc_html__( 'Register title', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Create an account', 'voxel-elementor' ),
				'placeholder' => esc_html__( 'Type text', 'voxel-elementor' ),
			]
		);

		$this->add_control(
			'confirm_title',
			[
				'label' => esc_html__( 'Confirm title', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Confirm email', 'voxel-elementor' ),
				'placeholder' => esc_html__( 'Type text', 'voxel-elementor' ),
			]
		);

		$this->add_control(
			'reset_pass_title',
			[
				'label' => esc_html__( 'Password recovery title', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Password recovery', 'voxel-elementor' ),
				'placeholder' => esc_html__( 'Type text', 'voxel-elementor' ),
			]
		);

		$this->add_control(
			'confirm_code',
			[
				'label' => esc_html__( 'Confirm code title', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Confirm code', 'voxel-elementor' ),
				'placeholder' => esc_html__( 'Type text', 'voxel-elementor' ),
			]
		);

		$this->add_control(
			'new_password',
			[
				'label' => esc_html__( 'New password title', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'New password', 'voxel-elementor' ),
				'placeholder' => esc_html__( 'Type text', 'voxel-elementor' ),
			]
		);

		$this->add_control(
			'update_password',
			[
				'label' => esc_html__( 'Update password title', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Update password', 'voxel-elementor' ),
				'placeholder' => esc_html__( 'Type text', 'voxel-elementor' ),
			]
		);

		$this->add_control(
			'update_email',
			[
				'label' => esc_html__( 'Update email title', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Update email', 'voxel-elementor' ),
				'placeholder' => esc_html__( 'Type text', 'voxel-elementor' ),
			]
		);

		$this->add_control(
			'auth_welc_title',
			[
				'label' => esc_html__( 'Welcome title', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Welcome!', 'voxel-elementor' ),
				'placeholder' => esc_html__( 'Type text', 'voxel-elementor' ),
			]
		);

		$this->add_control(
			'auth_welc_subtitle',
			[
				'label' => esc_html__( 'Welcome subtitle', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Complete your profile or skip for now', 'voxel-elementor' ),
				'placeholder' => esc_html__( 'Type text', 'voxel-elementor' ),
			]
		);


		$this->add_control( 'auth_google_ico', [
			'label' => __( 'Google icon', 'text-domain' ),
			'type' => \Elementor\Controls_Manager::ICONS,
		] );

		$this->add_control( 'auth_user_ico', [
			'label' => __( 'Username icon', 'text-domain' ),
			'type' => \Elementor\Controls_Manager::ICONS,
		] );

		$this->add_control( 'auth_pass_ico', [
			'label' => __( 'Password icon', 'text-domain' ),
			'type' => \Elementor\Controls_Manager::ICONS,
		] );

		$this->add_control( 'auth_email_ico', [
			'label' => __( 'Email icon', 'text-domain' ),
			'type' => \Elementor\Controls_Manager::ICONS,
		] );

		$this->add_control( 'auth_welcome_ico', [
			'label' => __( 'Welcome icon', 'text-domain' ),
			'type' => \Elementor\Controls_Manager::ICONS,
		] );

		$this->add_control(
			'ts_chevron_left',
			[
				'label' => __( 'Left chevron', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'ts_privacy',
			[
				'label' => __( 'Privacy icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'ts_trash',
			[
				'label' => __( 'Trash icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'ts_logout',
			[
				'label' => __( 'Logout icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

	}
}
