<?php

namespace Voxel\Controllers\Elementor;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Container_Controller extends \Voxel\Controllers\Base_Controller {

	protected function hooks() {
		$this->on( 'elementor/element/container/section_layout/after_section_end', '@register_container_settings' );
		$this->on( 'elementor/frontend/container/before_render', '@before_render' );
	}

	protected function register_container_settings( $container ) {
		$container->start_controls_section( '_voxel_container_settings', [
			'label' => __( 'Container options', 'voxel-backend' ),
			'tab' => 'tab_voxel',
		] );

		/* Container sticky options */
		$container->add_control( 'sticky_option', [
			'label' => __( 'Sticky position', 'voxel-backend' ),
			'type' => \Elementor\Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$container->add_control( 'sticky_container', [
			'label' => __( 'Enable?', 'voxel-backend' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'sticky',
		] );

		$container->add_control(
			'sticky_container_desktop',
			[
				'label' => __( 'Enable on desktop', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'sticky'  => __( 'Enable', 'voxel-backend' ),
					'initial' => __( 'Disable', 'voxel-backend' ),
				],

				'selectors' => [
					'(desktop){{WRAPPER}}' => 'position: {{VALUE}}',
				],
				'condition' => [ 'sticky_container' => 'sticky' ],
			]
		);

		$container->add_control(
			'sticky_container_tablet',
			[
				'label' => __( 'Enable on tablet', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'sticky'  => __( 'Enable', 'voxel-backend' ),
					'initial' => __( 'Disable', 'voxel-backend' ),
				],

				'selectors' => [
					'(tablet){{WRAPPER}}' => 'position: {{VALUE}}',
				],
				'condition' => [ 'sticky_container' => 'sticky' ],
			]
		);

		$container->add_control(
			'sticky_container_mobile',
			[
				'label' => __( 'Enable on mobile', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'sticky'  => __( 'Enable', 'voxel-backend' ),
					'initial' => __( 'Disable', 'voxel-backend' ),
				],

				'selectors' => [
					'(mobile){{WRAPPER}}' => 'position: {{VALUE}}',
				],
				'condition' => [ 'sticky_container' => 'sticky' ],
			]
		);



		$container->add_responsive_control( 'sticky_top_value', [
			'label' => __( 'Top', 'voxel-backend' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%', 'vh'],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 500,
					'step' => 1,
				],
			],
			'selectors' => [
				'{{WRAPPER}}' => 'top: {{SIZE}}{{UNIT}};',
			],
			'condition' => [ 'sticky_container' => 'sticky' ],
		] );

		$container->add_responsive_control( 'sticky_left_value', [
			'label' => __( 'Left', 'voxel-backend' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%', 'vh'],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 500,
					'step' => 1,
				],
			],
			'selectors' => [
				'{{WRAPPER}}' => 'left: {{SIZE}}{{UNIT}};',
			],
			'condition' => [ 'sticky_container' => 'sticky' ],
		] );

		$container->add_responsive_control( 'sticky_right_value', [
			'label' => __( 'Right', 'voxel-backend' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%', 'vh'],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 500,
					'step' => 1,
				],
			],
			'selectors' => [
				'{{WRAPPER}}' => 'right: {{SIZE}}{{UNIT}};',
			],
			'condition' => [ 'sticky_container' => 'sticky' ],
		] );

		$container->add_responsive_control( 'sticky_bottom_value', [
			'label' => __( 'Bottom', 'voxel-backend' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%', 'vh'],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 500,
					'step' => 1,
				],
			],
			'selectors' => [
				'{{WRAPPER}}' => 'bottom: {{SIZE}}{{UNIT}};',
			],
			'condition' => [ 'sticky_container' => 'sticky' ],
		] );

		$container->add_control( 'con_inline_flex', [
			'label' => __( 'Inline Flex', 'voxel-backend' ),
			'type' => \Elementor\Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$container->add_responsive_control( 'enable_inline_flex', [
			'label' => __( 'Enable?', 'voxel-backend' ),
			'description' => __( 'Changes container display to inline flex and applies auto width', 'voxel-backend' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'selectors' => [
				'{{WRAPPER}}' => 'display: inline-flex; width: auto;',
			],
		] );

		$container->add_control( 'con_fixed_Width_heading', [
			'label' => __( 'Fixed width', 'voxel-backend' ),
			'description' => __( 'Apply fixed width to this container', 'voxel-backend' ),
			'type' => \Elementor\Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$container->add_responsive_control( 'enable_fixed_Width', [
			'label' => __( 'Enable?', 'voxel-backend' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'fixed_width',
		] );

		$container->add_responsive_control( 'fixed_width_value', [
			'label' => __( 'Width', 'voxel-backend' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'condition' => [ 'enable_fixed_Width' => 'fixed_width' ],
			'size_units' => [ 'px', '%', 'vh'],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 1000,
					'step' => 1,
				],
			],
			'selectors' => [
				'{{WRAPPER}}' => 'width: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
			],
		] );

		$container->add_control( 'con_fixed_height_heading', [
			'label' => __( 'Fixed height', 'voxel-backend' ),
			'description' => __( 'Apply fixed height to this container', 'voxel-backend' ),
			'type' => \Elementor\Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$container->add_responsive_control( 'enable_fixed_height', [
			'label' => __( 'Enable?', 'voxel-backend' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'fixed_height',
		] );

		$container->add_responsive_control( 'fixed_height_value', [
			'label' => __( 'Height', 'voxel-backend' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'condition' => [ 'enable_fixed_height' => 'fixed_height' ],
			'size_units' => [ 'px', '%', 'vh'],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 1000,
					'step' => 1,
				],
			],
			'selectors' => [
				'{{WRAPPER}}' => 'height: {{SIZE}}{{UNIT}};',
			],
		] );

		$container->add_control( 'con_calc_height_heading', [
			'label' => __( 'Calculated dimensions', 'voxel-backend' ),
			'type' => \Elementor\Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$container->add_responsive_control(
			'enable_con_calc_h',
			[
				'label' => __( 'Calculate min height?', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'voxel-backend' ),
				'label_off' => __( 'Hide', 'voxel-backend' ),
				'return_value' => 'yes',
				'default' => 'no'
			]
		);



		$container->add_responsive_control(
			'mcon_calc_height',
			[
				'label' => esc_html__( 'Calculation', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'calc()', 'voxel-backend' ),
				'description' => __( 'Use CSS calc() to calculate min-height e.g calc(100vh - 215px).', 'voxel-backend' ),
				'selectors' => [
					'{{WRAPPER}}' => 'min-height: {{VALUE}};',
				],
				'condition' => [ 'enable_con_calc_h' => 'yes' ],
			]
		);

		$container->add_responsive_control(
			'enable_con_calc_mh',
			[
				'label' => __( 'Calculate max height?', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'voxel-backend' ),
				'label_off' => __( 'Hide', 'voxel-backend' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);



		$container->add_responsive_control(
			'mcon_calc_mheight',
			[
				'label' => esc_html__( 'Calculation', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'calc()', 'voxel-backend' ),
				'description' => __( 'Use CSS calc() to calculate max-height e.g calc(100vh - 215px).', 'voxel-backend' ),
				'selectors' => [
					'{{WRAPPER}}' => 'max-height: {{VALUE}}; overflow-y: overlay; overflow-x: hidden;',
				],
				'condition' => [ 'enable_con_calc_mh' => 'yes' ],
			]
		);

		$container->add_control(
			'horizontal_scroll_color',
			[
				'label' => __( 'Scrollbar color', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--ts-scroll-color: {{VALUE}}',
				],
				'condition' => [ 'enable_con_calc_mh' => 'yes' ],
			]
		);

		$container->add_responsive_control(
			'enable_con_calc_w',
			[
				'label' => __( 'Calculate width?', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'voxel-backend' ),
				'label_off' => __( 'Hide', 'voxel-backend' ),
				'return_value' => 'yes',
				'default' => 'no'
			]
		);

		$container->add_responsive_control(
			'mcon_calc_width',
			[
				'label' => esc_html__( 'Calculation', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'calc()', 'voxel-backend' ),
				'description' => __( 'Use CSS calc() to calculate width e.g calc(100vh - 215px).', 'voxel-backend' ),
				'selectors' => [
					'{{WRAPPER}}' => 'width: {{VALUE}};',
				],
				'condition' => [ 'enable_con_calc_w' => 'yes' ],
			]
		);

		$container->add_responsive_control(
			'enable_blur',
			[
				'label' => __( 'Backdrop blur?', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'voxel-backend' ),
				'label_off' => __( 'Hide', 'voxel-backend' ),
				'return_value' => 'yes',
				'default' => 'no'
			]
		);

		$container->add_responsive_control(
			'ts_blur_backdrop',
			[
				'label' => __( 'Width', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'min' => 0,
					'max' => 100,
					'step' => 1,
				],
				'selectors' => [
					'{{WRAPPER}}' => 'backdrop-filter: blur({{SIZE}}{{UNIT}}); -webkit-backdrop-filter: blur({{SIZE}}{{UNIT}});',
				],
				'condition' => [ 'enable_blur' => 'yes' ],
			]
		);



		$container->end_controls_section();

		$container->start_controls_section( 'css_grid_vx', [
			'label' => __( 'CSS grid', 'voxel-backend' ),
			'tab' => 'layout',
		] );

			$container->add_control(
				'ts_csgrid_parent',
				[
					'label' => __( 'For parent containers', 'voxel-backend' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$container->add_control( 'csgrid_on', [
				'label' => __( 'Enable CSS grid', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'selectors' => [
					'{{WRAPPER}}, {{WRAPPER}} > .e-con-inner' => 'display: grid;  grid-auto-rows: max-content;',

				],
			] );

			$container->add_responsive_control(
				'ts_cgrid_columns',
				[
					'label' => __( 'Number of columns', 'voxel-backend' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 1,
					'max' => 24,
					'step' => 1,
					'selectors' => [
						'{{WRAPPER}}' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
						'{{WRAPPER}} > .e-con' => 'width: 100%; grid-column-end: span 1;',
					],
				    'condition' => [ 'csgrid_on' => 'yes', 'content_width' => 'full' ],
				]
			);





			$container->add_responsive_control(
				'ts_cgrid_gap',
				[
					'label' => __( 'Grid gap', 'voxel-backend' ),
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
						'{{WRAPPER}}' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],
				    'condition' => [ 'csgrid_on' => 'yes', 'content_width' => 'full' ],

				]
			);

			$container->add_responsive_control(
				'ts_cgrid_columns_b',
				[
					'label' => __( 'Number of columns', 'voxel-backend' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 1,
					'max' => 24,
					'step' => 1,
					'selectors' => [
						'{{WRAPPER}} > .e-con-inner' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
						'{{WRAPPER}} > .e-con-inner > .e-con ' => 'width: 100%; grid-column-end: span 1;',
					],
				    'condition' => [ 'csgrid_on' => 'yes', 'content_width' => 'boxed' ],
				]
			);





			$container->add_responsive_control(
				'ts_cgrid_gap_b',
				[
					'label' => __( 'Grid gap', 'voxel-backend' ),
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
						'{{WRAPPER}} > .e-con-inner' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],
				    'condition' => [ 'csgrid_on' => 'yes', 'content_width' => 'boxed' ],

				]
			);

			$container->add_control(
				'ts_csgrid_child',
				[
					'label' => __( 'For child containers (if parent has grid position)', 'voxel-backend' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);






			$container->add_responsive_control(
				'ts_mosaic_one_col',
				[
					'label' => __( 'Column span', 'voxel-backend' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 1,
					'max' => 24,
					'step' => 1,
					'selectors' => [
						'{{WRAPPER}}.elementor-element' => 'grid-column-end:  span {{VALUE}} !important;',
					],
				]
			);

			$container->add_responsive_control(
				'ts_mosaic_one_col_span',
				[
					'label' => __( 'Column start', 'voxel-backend' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 1,
					'max' => 24,
					'step' => 1,
					'selectors' => [
						'{{WRAPPER}}.elementor-element' => 'grid-column-start: {{VALUE}} !important;',
					],
				]
			);



			$container->add_responsive_control(
				'ts_mosaic_one_row',
				[
					'label' => __( 'Row span', 'voxel-backend' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 1,
					'max' => 24,
					'step' => 1,
					'selectors' => [
						'{{WRAPPER}}.elementor-element' => 'grid-row-end: span {{VALUE}}!important;',
					],
				]
			);

			$container->add_responsive_control(
				'ts_mosaic_one_row_span',
				[
					'label' => __( 'Row start', 'voxel-backend' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 1,
					'max' => 24,
					'step' => 1,
					'selectors' => [
						'{{WRAPPER}}.elementor-element' => 'grid-row-start:  {{VALUE}}!important;',
					],
				]
			);







		$container->end_controls_section();

		$container->start_controls_section( 'canvas_width_vx', [
			'label' => __( 'Canvas', 'voxel-backend' ),
			'tab' => 'layout',
		] );

			$container->add_responsive_control( 'edit_canvas_value', [
				'label' => __( 'Width', 'voxel-backend' ),
				'description' => __( 'Change the width of the canvas, useful when designing preview cards', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1200,
						'step' => 1,
					],
				],
				'selectors' => [
					'.vx-viewport-card' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			] );



		$container->end_controls_section();
	}

	protected function before_render( $container ) {
		if ( $container->get_settings('enable_con_calc_mh') === 'yes' ) {
			$container->add_render_attribute( '_wrapper', 'class', 'min-scroll' );
		}
	}
}
