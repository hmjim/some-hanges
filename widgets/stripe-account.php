<?php

namespace Voxel\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Stripe_Account extends Base_Widget {

	public function get_name() {
		return 'ts-stripe-account';
	}

	public function get_title() {
		return __( 'Stripe Account (VX)', 'voxel-elementor' );
	}

	public function get_icon() {
		return 'vxi vxi-bag';
	}

	public function get_categories() {
		return [ 'voxel', 'basic' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'ts_ui_icons',
			[
				'label' => __( 'Icons', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'ts_connect_ico',
				[
					'label' => __( 'Panel icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_setup_ico',
				[
					'label' => __( 'Setup icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_submit_ico',
				[
					'label' => __( 'Submit information icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_update_ico',
				[
					'label' => __( 'Update information icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

			$this->add_control(
				'ts_stripe_ico',
				[
					'label' => __( 'Portal icon', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::ICONS,

				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'panel_options',
			[
				'label' => __( 'Panel', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'panel_border',
					'label' => __( 'Border', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-panel',
				]
			);


			$this->add_responsive_control(
				'panel_radius',
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
						'{{WRAPPER}} .ts-panel' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'panel_bg',
				[
					'label' => __( 'Background', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-panel' => 'background: {{VALUE}}',
					],

				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'panel_shadow',
					'label' => __( 'Box Shadow', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-panel',
				]
			);

			$this->add_control(
				'panel_body',
				[
					'label' => __( 'Panel body', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'panel_spacing',
				[
					'label' => __( 'Body spacing', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ac-body' => 'padding: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'panel_gap',
				[
					'label' => __( 'Body content gap', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ac-body' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'body_typo',
					'label' => __( 'Typography', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-panel .ac-body p',
				]
			);

			$this->add_responsive_control(
				'body_typo_col',
				[
					'label' => __( 'Text color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-panel .ac-body p' => 'color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'panel_head',
				[
					'label' => __( 'Panel head', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'head_padding',
				[
					'label' => __( 'Padding', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .ac-head' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'head_ico_size',
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
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ts-panel .ac-head i' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ts-panel .ac-head svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'head_ico_margin',
				[
					'label' => __( 'Icon right margin', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-panel .ac-head i' => 'margin-right: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ts-panel .ac-head svg' => 'margin-right: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'head_ico_col',
				[
					'label' => __( 'Icon color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-panel .ac-head i' => 'color: {{VALUE}}',
						'{{WRAPPER}} .ts-panel .ac-head svg' => 'fill: {{VALUE}}',
					],

				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'head_typo',
					'label' => __( 'Typography', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-panel .ac-head p',
				]
			);

			$this->add_responsive_control(
				'head_typo_col',
				[
					'label' => __( 'Text color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-panel .ac-head p' => 'color: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'head_border_col',
				[
					'label' => __( 'Separator color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-panel .ac-head' => 'border-color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'panel_buttons',
				[
					'label' => __( 'Panel buttons', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);


			$this->add_responsive_control(
				'panel_buttons_grid',
				[
					'label' => __( 'Number of columns', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 1,
					'max' => 6,
					'step' => 1,
					'default' => 1,
					'selectors' => [
						'{{WRAPPER}} .current-plan-btn' => 'display: grid;grid-template-columns: repeat({{VALUE}}, 1fr);',
					],
				]
			);

			$this->add_responsive_control(
				'panel_buttons_gap',
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
						'{{WRAPPER}} .current-plan-btn' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],

				]
			);



		$this->end_controls_section();


		$this->start_controls_section(
			'scnd_btn',
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
							'selector' => '{{WRAPPER}} .ts-btn-1',
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
								'{{WRAPPER}} .ts-btn-1' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'scnd_btn_c',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-1' => 'color: {{VALUE}}',
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
								'{{WRAPPER}} .ts-btn-1' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
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
								'{{WRAPPER}}  .ts-btn-1' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);


					$this->add_responsive_control(
						'scnd_btn_bg',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-1' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'scnd_btn_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-btn-1',
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
								'{{WRAPPER}} .ts-btn-1 i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-btn-1 svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'scnd_btn_icon_pad',
						[
							'label' => __( 'Icon right padding', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-btn-1 i' => 'padding-right: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-btn-1 svg' => 'margin-right: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'scnd_btn_icon_color',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-1 i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-btn-1 svg' => 'fill: {{VALUE}}',
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
								'{{WRAPPER}} .ts-btn-1:hover' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'scnd_btn_bg_h',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-1:hover' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'scnd_btn_border_h',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-1:hover' => 'border-color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'scnd_btn_icon_color_h',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-btn-1:hover i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-btn-1:hover svg' => 'fill: {{VALUE}}',
							],

						]
					);



				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

	}

	protected function render( $instance = [] ) {
		if ( ! is_user_logged_in() ) {
			printf( '<p class="ts-restricted">%s</p>', _x( 'You must be logged in to view this content.', 'orders widget', 'voxel-elementor' ) );
			return;
		}

		$user = \Voxel\current_user();

		// update account details after completing onboarding
		if ( isset( $_GET['onboarding_key'] ) ) {
			if ( $_GET['onboarding_key'] === get_user_meta( $user->get_id(), 'voxel:onboarding_key', true ) ) {
				delete_user_meta( $user->get_id(), 'voxel:onboarding_key' );
				try {
					do_action( 'voxel/connect/account-updated', $user->get_stripe_account() );
					wp_add_inline_script( 'vx:commons.js', 'window.addEventListener("DOMContentLoaded", () => Voxel.deleteSearchParam("onboarding_key"))' );
				} catch ( \Exception $e ) {
					//
				}
			}
		}

		$account = $user->get_stripe_account_details();

		$onboard_link = add_query_arg( [
			'vx' => 1,
			'action' => 'stripe.account.onboard',
		], home_url('/') );

		$dashboard_link = add_query_arg( [
			'vx' => 1,
			'action' => 'stripe.account.login',
		], home_url('/') );

		// dump($user->get_stripe_account());

		require locate_template( 'templates/widgets/stripe-account.php' );
	}

	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}

	protected function register_runtime_controls() {
		$this->add_control(
			'ts_connect_ico',
			[
				'label' => __( 'Panel icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_setup_ico',
			[
				'label' => __( 'Setup icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_submit_ico',
			[
				'label' => __( 'Submit information icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_update_ico',
			[
				'label' => __( 'Update information icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);

		$this->add_control(
			'ts_stripe_ico',
			[
				'label' => __( 'Portal icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::ICONS,

			]
		);
	}
}
