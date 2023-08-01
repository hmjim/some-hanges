<?php

namespace Voxel\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Search_Form extends Base_Widget {

	public function get_name() {
		return 'ts-search-form';
	}

	public function get_title() {
		return __( 'Search form (VX)', 'voxel-elementor' );
	}

	public function get_icon() {
		return 'vxi-search vxi';
	}

	public function get_categories() {
		return [ 'voxel', 'basic' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ts_sf_post_types',
			[
				'label' => __( 'Post types', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

			$post_types = [];
			foreach ( \Voxel\Post_Type::get_voxel_types() as $post_type ) {
				$post_types[ $post_type->get_key() ] = $post_type->get_label();
			}

			$this->add_control(
				'ts_choose_post_types',
				[
					'label' => __( 'Choose post types', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT2,
					'multiple' => true,
					'options' => $post_types,
				]
			);

			$this->add_control(
				'cpt_filter_show',
				[
					'label' => __( 'Show custom post type filter', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'Show', 'voxel-elementor' ),
					'label_off' => __( 'Hide', 'voxel-elementor' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);

			$this->add_control( 'ts_on_submit', [
				'label' => __( 'On form submit', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'post-to-feed',
				'label_block' => true,
				'options' => [
					'post-to-feed' => __( 'Post results to widget', 'voxel-elementor' ),
					'submit-to-archive' => __( 'Submit to post type archive', 'voxel-elementor' ),
					'submit-to-page' => __( 'Submit to page', 'voxel-elementor' ),
				],
			] );

			$this->add_control( 'ts_post_to_feed', [
				'label' => __( 'Choose Post Feed widget', 'voxel-elementor' ),
				'type' => 'voxel-relation',
				'vx_group' => 'feedToSearch',
				'vx_target' => 'elementor-widget-ts-post-feed',
				'vx_side' => 'left',
				'condition' => [ 'ts_on_submit' => 'post-to-feed' ],
			] );

			$this->add_control( 'ts_post_to_map', [
				'label' => __( 'Choose Map widget', 'voxel-elementor' ),
				'type' => 'voxel-relation',
				'vx_group' => 'mapToSearch',
				'vx_target' => 'elementor-widget-ts-map',
				'vx_side' => 'left',
				'condition' => [ 'ts_on_submit' => 'post-to-feed' ],
				'reload' => 'editor',
			] );

			$this->add_control( 'ts_update_url', [
				'label' => __( 'Update URL with search values?', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'voxel-elementor' ),
				'label_off' => __( 'No', 'voxel-elementor' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [ 'ts_on_submit' => 'post-to-feed' ],
			] );

			$this->add_control( 'ts_search_on', [
				'label' => __( 'Perform search:', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'submit',
				'options' => [
					'submit' => 'When the search button is clicked',
					'filter_update' => 'When any filter value is updated',
				],
				'condition' => [ 'ts_on_submit' => 'post-to-feed' ],
			] );

			$this->add_control( 'ts_submit_to_page', [
				'label' => __( 'Enter page ID', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'condition' => [ 'ts_on_submit' => 'submit-to-page' ],
			] );

			$this->add_control(
				'ts_cpt_visual',
				[
					'label' => __( 'Visual', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'ts_post_type_width',
				[
					'label' => __( 'Width', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'range' => [
						'min' => 0,
						'max' => 100,
						'step' => 5,
					],
					'selectors' => [
						'{{WRAPPER}} .choose-cpt-filter' => 'width: {{SIZE}}%;',
					],
				]
			);



		$this->end_controls_section();

		foreach ( \Voxel\Post_Type::get_voxel_types() as $post_type ) {
			$this->start_controls_section( sprintf( 'ts_sf_filters__%s', $post_type->get_key() ), [
				'label' => sprintf( '%s Filters', $post_type->get_singular_name() ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [ 'ts_choose_post_types' => $post_type->get_key() ],
			] );

			$repeater = new \Elementor\Repeater;

			$filters = [];
			// $defaults = [];
			foreach ( $post_type->get_filters() as $filter ) {
				$filters[ $filter->get_key() ] = $filter->get_label();
				// $defaults[] = [
				// 	'ts_choose_filter' => $filter->get_key(),
				// 	'ts_filter_width' => 50,
				// ];
			}

			// $repeater->add_control(
			// 	'ts_filt_data',
			// 	[
			// 		'label' => __( 'Data', 'voxel-elementor' ),
			// 		'type' => \Elementor\Controls_Manager::HEADING,
			// 		'separator' => 'before',
			// 	]
			// );

			$repeater->add_control( 'ts_choose_filter', [
				'label' => __( 'Choose filter', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => $filters,
			] );

			$repeater->add_control( 'ts_default_value', [
				'label' => __( 'Add default value', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			] );

			foreach ( $post_type->get_filters() as $filter ) {
				foreach ( $filter->get_elementor_controls() as $control_key => $control_args ) {
					if ( isset( $control_args['full_key'] ) ) {
						$control_key = $control_args['full_key'];
					} else {
						$control_key = $filter->get_key().':'.$control_key;
					}

					if ( ! isset( $control_args['condition'] ) ) {
						$control_args['condition'] = [];
					}

					$control_args['condition']['ts_choose_filter'] = $filter->get_key();

					if ( ( $control_args['conditional'] ?? null ) !== false ) {
						$control_args['condition']['ts_default_value'] = 'yes';
					}

					if ( $control_args['responsive'] ?? false ) {
						$repeater->add_responsive_control( $control_key, $control_args );
					} else {
						$repeater->add_control( $control_key, $control_args );
					}
				}
			}

			$repeater->add_control( 'ts_reset_value', [
				'label' => __( 'On search form reset, set the value of this filter to:', 'voxel-elementor' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT,
				'condition' => [ 'ts_default_value' => 'yes' ],
				'options' => [
					'empty' => 'Empty value',
					'default_value' => 'Default value',
				],
			] );


			$repeater->add_control(
				'ts_filt_visual',
				[
					'label' => __( 'Visual', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$repeater->add_responsive_control( 'ts_filter_width', [
				'label' => __( 'Width (%)', 'voxel-elementor' ),
				'description' => __( 'Leave empty for auto width', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'min' => 0,
					'max' => 100,
					'step' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .ts-filter-wrapper > {{CURRENT_ITEM}}' => 'width: {{SIZE}}%;',
				],
			] );

			$repeater->add_responsive_control(
				'align_popup_btn',
				[
					'label' => esc_html__( 'Align button content', 'textdomain' ),
					'type' => \Elementor\Controls_Manager::CHOOSE,
					'options' => [
						'flex-start' => [
							'title' => esc_html__( 'Left', 'textdomain' ),
							'icon' => 'eicon-text-align-left',
						],
						'center' => [
							'title' => esc_html__( 'Center', 'textdomain' ),
							'icon' => 'eicon-text-align-center',
						],
						'flex-end' => [
							'title' => esc_html__( 'Right', 'textdomain' ),
							'icon' => 'eicon-text-align-right',
						],
					],
					'toggle' => true,
					'selectors' => [
						'{{CURRENT_ITEM}} .ts-filter' => 'justify-content: {{VALUE}};',
					],
				]
			);

			$repeater->add_control(
				'ts_repeater_label',
				[
					'label' => __( 'Hide label', 'voxel-elementor' ),
					'description' => __( 'Hide label for this filter only', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'equal',
					'selectors' => [
						'{{CURRENT_ITEM}} >label' => 'display: none !important;',
					],
				]
			);

			$repeater->add_control(
				'ts_repeater_hide_filter',
				[
					'label' => __( 'Hide filter', 'voxel-elementor' ),
					'description' => __( 'Visually hide this filter but keep it functional', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'equal',
					'selectors' => [
						'{{CURRENT_ITEM}}' => 'display: none !important;',
					],
				]
			);

			$repeater->add_control(
				'filt_custom_popup',
				[
					'label' => __( 'Popup', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$repeater->add_control(
				'filt_custom_popup_enable',
				[
					'label' => __( 'Custom popup style', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'Show', 'voxel-elementor' ),
					'label_off' => __( 'Hide', 'voxel-elementor' ),
					'return_value' => 'yes',
				]
			);

			$repeater->add_responsive_control(
				'filt_pg_width',
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
					'condition' => [ 'filt_custom_popup_enable' => 'yes' ],
					'selectors' => [
						'{{CURRENT_ITEM}} .ts-field-popup' => 'min-width: {{SIZE}}{{UNIT}};',
					],
				]
			);



			$repeater->add_responsive_control(
				'filt_max_width',
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
					'condition' => [ 'filt_custom_popup_enable' => 'yes' ],
					'selectors' => [
						'{{CURRENT_ITEM}} .ts-field-popup' => 'max-width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$repeater->add_responsive_control(
				'filt_max_height',
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
					'condition' => [ 'filt_custom_popup_enable' => 'yes' ],
					'selectors' => [
						'{{CURRENT_ITEM}} .ts-popup-content-wrapper' => 'max-height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$repeater->add_responsive_control(
				'filt_center_position',
				[
					'label' => __( 'Switch position to center of screen', 'voxel' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'static',
					'condition' => [ 'filt_custom_popup_enable' => 'yes' ],
					'selectors' => [
						'(desktop){{CURRENT_ITEM}} .ts-popup-root' => 'position: fixed !important;',
						'(desktop){{CURRENT_ITEM}} .ts-form' => 'position: static !important;
		    						max-width: initial; width: auto !important;',
					],
				]
			);



			$this->add_control( sprintf( 'ts_filter_list__%s', $post_type->get_key() ), [
				'label' => __( 'Add filters', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				// 'default' => $defaults,
				'title_field' => '{{ts_choose_filter}}',
				'_disable_loop' => true,
			] );

			$card_templates = $post_type->repository->get_custom_templates()['card'];
			$card_templates = array_map( function( $tpl ) {
				$tpl['label'] = 'Preview card: '.$tpl['label'];
				return $tpl;
			}, $card_templates );

			$this->add_control( sprintf( 'ts_card_template__%s', $post_type->get_key() ), [
				'label' => __( 'Preview card template', 'voxel-elementor' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'main',
				'options' => [
					'main' => 'Main template',
				] + array_column( $card_templates, 'label', 'id' ),
			] );

			$this->add_control( sprintf( 'ts_card_template_map__%s', $post_type->get_key() ), [
				'label' => __( 'Custom map popup template', 'voxel-elementor' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'main',
				'options' => [
					'main' => 'None',
				] + array_column( $card_templates, 'label', 'id' ),
			] );

			$this->end_controls_section();
		}


		$this->start_controls_section(
			'ts_sf_buttons',
			[
				'label' => __( 'Buttons', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'ts_show_search_btn',
				[
					'label' => __( 'Show search button', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'true',
					'options' => [
						'true'  => __( 'Yes', 'voxel-elementor' ),
						'false' => __( 'No', 'voxel-elementor' ),
					],
				]
			);

			$this->add_control(
				'ts_search_text',
				[
					'label' => __( 'Button label', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Search', 'voxel-elementor' ),
					'condition' => [ 'ts_show_search_btn' => 'true' ],
					'placeholder' => __( 'Type your text', 'voxel-elementor' ),
				]
			);

			$this->add_control(
				'ts_show_reset_btn',
				[
					'label' => __( 'Show reset button', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'false',
					'options' => [
						'true'  => __( 'Yes', 'voxel-elementor' ),
						'false' => __( 'No', 'voxel-elementor' ),
					],
				]
			);

			$this->add_control(
				'ts_reset_text',
				[
					'label' => __( 'Button label', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Reset', 'voxel-elementor' ),
					'placeholder' => __( 'Type your text', 'voxel-elementor' ),
					'condition' => [ 'ts_show_reset_btn' => 'true' ],
				]
			);

			$this->add_responsive_control(
				'ts_search_btn_width',
				[
					'label' => __( 'Width', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'range' => [
						'min' => 0,
						'max' => 100,
						'step' => 5,
					],
					'selectors' => [
						'{{WRAPPER}} .ts-form-group.ts-form-submit' => 'width: {{SIZE}}%;',
					],
				]
			);



			$this->add_responsive_control(
				'ts_sf_submit_wrap',
				[
					'label' => __( 'Wrap buttons?', 'voxel-elementor' ),

					'type' => \Elementor\Controls_Manager::SWITCHER,
					'selectors' => [
						'{{WRAPPER}} .ts-form-submit' => 'flex-direction: column !important;',
						'{{WRAPPER}} .ts-form-submit .ts-btn' => 'width: 100%;',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_search_responsive',
			[
				'label' => __( 'Responsive behaviour', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);



			$this->add_control(
				'form_toggle',
				[
					'label' => __( 'Toggle mode', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);


			$this->add_control(
				'form_toggle_desktop',
				[
					'label' => __( 'Enable on desktop', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
				]
			);



			$this->add_control(
				'form_toggle_tablet',
				[
					'label' => __( 'Enable on tablet', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
				]
			);


			$this->add_control(
				'form_toggle_mobile',
				[
					'label' => __( 'Enable on mobile', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
				]
			);

			$this->add_control( 'form_toggle_apply', [
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => '<a href="#" onclick="voxel_reload_editor(); return false;" class="elementor-button">Apply changes</a>',
			] );

			$this->add_control(
				'ts_sf_filter_wrap',
				[
					'label' => __( 'Filter wrapping', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'ts_sf_wrap_desktop',
				[
					'label' => __( 'Enable horizontal scroll)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,

				]
			);

			$this->add_responsive_control(
				'ks_nowrap_max_width',
				[
					'label' => __( 'Max filter width', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'description' => __( 'Useful when filters have auto width', 'voxel-elementor' ),
					'size_units' => [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => 100,
							'max' => 500,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ts-filter' => 'max-width: {{SIZE}}{{UNIT}};',
					],
				]
			);



		$this->end_controls_section();

		$this->start_controls_section(
			'ts_map_feed_switch',
			[
				'label' => __( 'Map/Feed Switcher', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);


			$this->add_control(
				'mf_switcher_desktop',
				[
					'label' => __( 'Enable on desktop', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
				]
			);

			$this->add_control(
				'switcher_desktop_default',
				[
					'label' => __( 'Visible by default', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'feed',
					'condition' => [ 'mf_switcher_desktop' => 'yes' ],
					'options' => [
						'feed'  => __( 'Feed', 'voxel-elementor' ),
						'map' => __( 'Map', 'voxel-elementor' ),
					],
				]
			);


			$this->add_control(
				'mf_switcher_tablet',
				[
					'label' => __( 'Enable on tablet', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
				]
			);

			$this->add_control(
				'switcher_tablet_default',
				[
					'label' => __( 'Visible by default', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'feed',
					'condition' => [ 'mf_switcher_tablet' => 'yes' ],
					'options' => [
						'feed'  => __( 'Feed', 'voxel-elementor' ),
						'map' => __( 'Map', 'voxel-elementor' ),
					],
				]
			);

			$this->add_control(
				'mf_switcher_mobile',
				[
					'label' => __( 'Enable on mobile', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
				]
			);

			$this->add_control(
				'switcher_mobile_default',
				[
					'label' => __( 'Visible by default', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'feed',
					'condition' => [ 'mf_switcher_mobile' => 'yes' ],
					'options' => [
						'feed'  => __( 'Feed', 'voxel-elementor' ),
						'map' => __( 'Map', 'voxel-elementor' ),
					],
				]
			);

			$this->add_control( 'mf_switcher_apply', [
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => '<a href="#" onclick="voxel_reload_editor(); return false;" class="elementor-button">Apply changes</a>',
			] );

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_ui_icons',
			[
				'label' => __( 'Icons', 'voxel-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'ts_sf_form_btn_icon',
				[
					'label' => __( 'Search icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_sf_form_btn_icon_in',
				[
					'label' => __( 'Search icon (Input)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_sf_form_btn_reset_icon',
				[
					'label' => __( 'Reset icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_toggle_icon',
				[
					'label' => __( 'Filter toggle icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_location_icon',
				[
					'label' => __( 'Location icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_mylocation_icon',
				[
					'label' => __( 'My location icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_map_icon',
				[
					'label' => __( 'Map view icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_list_icon',
				[
					'label' => __( 'List view icon', 'voxel-elementor' ),
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
				'ts_minus_icon',
				[
					'label' => __( 'Minus icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_plus_icon',
				[
					'label' => __( 'Plus icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);



			$this->add_control(
				'ts_nav_dropdown_icon',
				[
					'label' => __( 'Down arrow', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_arrow_right',
				[
					'label' => __( 'Right arrow', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_arrow_left',
				[
					'label' => __( 'Left arrow', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_close_ico',
				[
					'label' => __( 'Close icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

			$this->add_control(
				'ts_trash_ico',
				[
					'label' => __( 'Trash icon', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
				]
			);

		$this->end_controls_section();


		$this->start_controls_section(
			'ts_sf_styling_general',
			[
				'label' => __( 'Search form', 'voxel-elementor' ),
				'tab' => 'tab_general',
			]
		);


				$this->add_responsive_control(
					'ts_sf_general_padding',
					[
						'label' => __( 'Form padding', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'selectors' => [
							'{{WRAPPER}} .ts-filter-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'ts_sf_outer_padding',
					[
						'label' => __( 'From margin', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'selectors' => [
							'{{WRAPPER}} .ts-search-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'ts_sf_general_bg',
					[
						'label' => __( 'Background color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-filter-wrapper' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' => 'ts_sf_general_border',
						'label' => __( 'Border', 'voxel-elementor' ),
						'selector' => '{{WRAPPER}} .ts-filter-wrapper',
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
							'{{WRAPPER}} .ts-filter-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'ts_sf_general_shadow',
						'label' => __( 'Box Shadow', 'voxel-elementor' ),
						'selector' => '{{WRAPPER}} .ts-filter-wrapper',
					]
				);

				$this->add_control(
					'horizontal_scroll_color',
					[
						'label' => __( 'Scrollbar color', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-filter-wrapper' => '--ts-scroll-color: {{VALUE}}',
						],
					]
				);




		$this->end_controls_section();

		$this->start_controls_section(
			'nsf_form_general',
			[
				'label' => __( 'Filters: General', 'voxel-elementor' ),
				'tab' => 'tab_general',
			]
		);

			$this->add_responsive_control(
				'ts_sf_form_group_padding',
				[
					'label' => __( 'Filter Margin', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .ts-filter-wrapper > .ts-form-group' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'ts_sf_input_lbl',
				[
					'label' => __( 'Label', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'ts_sf_input_label',
				[
					'label' => __( 'Show label', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
				]
			);

			$this->add_control(
				'ts_sf_input_label_t',
				[
					'label' => __( 'Disable on tablet', 'voxel-elementor' ),
					'description' => __( 'Disable label on tablet', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'condition' => [ 'ts_sf_input_label' => 'yes' ],
					'return_value' => 'equal',
					'selectors' => [
						'(tablet){{WRAPPER}} .ts-filter-wrapper > .ts-form-group>label' => 'display: none !important;',
					],
				]
			);

			$this->add_control(
				'ts_sf_input_label_m',
				[
					'label' => __( 'Disable on mobile', 'voxel-elementor' ),
					'description' => __( 'Disable label width on mobile', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'condition' => [ 'ts_sf_input_label' => 'yes' ],
					'return_value' => 'equal',
					'selectors' => [
						'(mobile){{WRAPPER}} .ts-filter-wrapper > .ts-form-group>label' => 'display: none !important;',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'ts_sf_input_label_text',
					'label' => __( 'Label typography' ),
					'selector' => '{{WRAPPER}} .ts-form .ts-form-group > label',
				]
			);


			$this->add_responsive_control(
				'ts_sf_input_label_col',
				[
					'label' => __( 'Label color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-form .ts-form-group > label' => 'color: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'ts_sf_input_label_padding',
				[
					'label' => __( 'Label Margin', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .ts-form .ts-form-group > label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);



			$this->add_control(
				'ts_db_filters',
				[
					'label' => __( 'Double filters', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'ts_sf_input_doube',
				[
					'label' => __( 'Double filter spacing', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-form .ts-double-input' => 'grid-gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'ts_sf_input_doube_force',
				[
					'label' => __( 'Equal width', 'voxel-elementor' ),
					'description' => __( 'Force equal width for double filters', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'equal',
					'selectors' => [
						'{{WRAPPER}} .ts-double-input .ts-filter' => 'width: 100% !important;',
					],
				]
			);

			$this->add_control(
				'ts_sf_input_doube_force_t',
				[
					'label' => __( 'Disable on tablet', 'voxel-elementor' ),
					'description' => __( 'Disable equal width on tablet', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'condition' => [ 'ts_sf_input_doube_force' => 'equal' ],
					'return_value' => 'equal',
					'selectors' => [
						'(tablet){{WRAPPER}} .ts-double-input .ts-filter' => 'width: auto !important;',
					],
				]
			);

			$this->add_control(
				'ts_sf_input_doube_force_m',
				[
					'label' => __( 'Disable on mobile', 'voxel-elementor' ),
					'description' => __( 'Disable equal width on mobile', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'condition' => [ 'ts_sf_input_doube_force' => 'equal' ],
					'return_value' => 'equal',
					'selectors' => [
						'(mobile){{WRAPPER}} .ts-double-input .ts-filter' => 'width: auto !important;',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_sf_styling_filters',
			[
				'label' => __( 'Popup button', 'voxel-elementor' ),
				'tab' => 'tab_general',
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
							'selector' => '{{WRAPPER}} .ts-form .ts-filter',
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
							'selectors' => [
								'{{WRAPPER}} .ts-filter' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);


					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_sf_input_shadow',
							'label' => __( 'Box Shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-filter',
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
								'{{WRAPPER}} .ts-form .ts-filter-text' => 'color: {{VALUE}}',
							],

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
							'selectors' => [
								'{{WRAPPER}} .ts-form .ts-filter' => 'border-radius: {{SIZE}}{{UNIT}};',
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
								'{{WRAPPER}} .ts-filter' => 'grid-gap: {{SIZE}}{{UNIT}};',
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
								'{{WRAPPER}} .ts-filter .ts-down-icon' => 'display: none !important;',
							],
						]
					);

					$this->add_control(
						'ts_chevron_btn_color',
						[
							'label' => __( 'Chevron color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-filter .ts-down-icon' => 'border-top-color: {{VALUE}}',
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
								'{{WRAPPER}} .ts-form .ts-filter:hover' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_sf_input_value_col_h',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form .ts-filter:hover .ts-filter-text' => 'color: {{VALUE}}',
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
								'{{WRAPPER}} .ts-filter:hover i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-filter:hover svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_sf_input_shadow_hover',
							'label' => __( 'Box Shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-filter:hover',
						]
					);

					$this->add_control(
						'ts_chevron_btn_h',
						[
							'label' => __( 'Chevron color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-filter:hover .ts-down-icon' => 'border-top-color: {{VALUE}}',
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
							'selector' => '{{WRAPPER}} .ts-filter.ts-filled',
						]
					);

					$this->add_control(
						'ts_sf_input_background_filled',
						[
							'label' => __( 'Background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form .ts-filter.ts-filled' => 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_sf_input_value_col_filled',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-filter.ts-filled .ts-filter-text' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_sf_input_icon_col_filled',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-filter.ts-filled i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-filter.ts-filled svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_sf_input_border_filled',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form .ts-filter.ts-filled' => 'border-color: {{VALUE}}',
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
								'{{WRAPPER}} .ts-form .ts-filter.ts-filled' => 'border-width: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_sf_input_shadow_active',
							'label' => __( 'Box Shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-filter.ts-filled',
						]
					);

					$this->add_control(
						'ts_chevron_btn_f',
						[
							'label' => __( 'Chevron color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-filter.ts-filled .ts-down-icon' => 'border-top-color: {{VALUE}}',
							],
						]
					);


				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'inline_input',
			[
				'label' => __( 'Input', 'voxel-elementor' ),
				'tab' => 'tab_inline',
			]
		);

			$this->start_controls_tabs(
				'inline_input_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'inline_sfi_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'inline_heading_input',
						[
							'label' => __( 'Input', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_responsive_control(
						'inline_input_height',
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
								'{{WRAPPER}} .inline-input' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'inline_input_min',
						[
							'label' => __( 'Min width', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'description' => __( 'Useful when this filter has auto width', 'voxel-elementor' ),
							'size_units' => [ 'px' ],
							'range' => [
								'px' => [
									'min' => 100,
									'max' => 500,
									'step' => 1,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ts-inline-filter' => 'min-width: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'inline_input_radius',
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
								'{{WRAPPER}} .inline-input' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'inline_input_shadow',
							'label' => __( 'Box Shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .inline-input',
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'inline_input_font',
							'label' => __( 'Typography' ),
							'selector' => '{{WRAPPER}} .inline-input',
						]
					);

					$this->add_responsive_control(
						'inline_padding_noico',
						[
							'label' => __( 'Padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .inline-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);


					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'inline_input_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .inline-input',
						]
					);

					$this->add_control(
						'inline_input_bg',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .inline-input' => 'background: {{VALUE}}',
							],

						]
					);


					$this->add_control(
						'inline_input_value_col',
						[
							'label' => __( 'Value color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .inline-input' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'inline_input_placeholder_color',
						[
							'label' => __( 'Input placeholder color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .inline-input::-webkit-input-placeholder' => 'color: {{VALUE}}',
								'{{WRAPPER}} .inline-input:-moz-placeholder' => 'color: {{VALUE}}',
								'{{WRAPPER}} .inline-input::-moz-placeholder' => 'color: {{VALUE}}',
								'{{WRAPPER}} .inline-input:-ms-input-placeholder' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'inline_input_popup_icon',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-inline-filter .ts-input-icon > i, {{WRAPPER}} .ts-inline-filter .ts-input-icon > span > i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-inline-filter .ts-input-icon > svg, {{WRAPPER}} .ts-inline-filter .ts-input-icon > span > svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'inline_input_icon_size',
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
								'{{WRAPPER}} .ts-inline-filter .ts-input-icon > i, {{WRAPPER}} .ts-inline-filter .ts-input-icon > span > i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-inline-filter .ts-input-icon > svg, {{WRAPPER}} .ts-inline-filter .ts-input-icon > span > svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'inline_input_icon_size_m',
						[
							'label' => __( 'Icon side margin', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'size_units' => [ 'px' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 40,
									'step' => 1,
								],
							],
							'default' => [
								'unit' => 'px',
								'size' => 15,
							],
							'selectors' => [
								'{{WRAPPER}} .ts-inline-filter .ts-input-icon > i, {{WRAPPER}} .ts-inline-filter .ts-input-icon > span, {{WRAPPER}} .ts-inline-filter .ts-input-icon > svg' => !is_rtl() ? 'left: {{SIZE}}{{UNIT}};' : 'right: {{SIZE}}{{UNIT}};',

							],
						]
					);

				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'inline_sfi_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);


					$this->add_control(
						'inline_input_popup_icon_hover',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-inline-filter:hover .ts-input-icon > i, {{WRAPPER}} .ts-inline-filter:hover .ts-input-icon > span > i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-inline-filter:hover .ts-input-icon > svg, {{WRAPPER}} .ts-inline-filter:hover .ts-input-icon > span > svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'inline_input_bg_h',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .inline-input:hover' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'inline_input_h_border',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .inline-input:hover' => 'border-color: {{VALUE}}',
							],

						]
					);


				$this->end_controls_tab();

				$this->start_controls_tab(
					'inline_sfi_active',
					[
						'label' => __( 'Focus', 'voxel-elementor' ),
					]
				);

					$this->add_control(
						'inline_input_bg_a',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .inline-input:focus' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'inline_input_a_border',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .inline-input:focus' => 'border-color: {{VALUE}}',
							],

						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_sf_styling_buttons',
			[
				'label' => __( 'Submit & Reset button', 'voxel-elementor' ),
				'tab' => 'tab_general',
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

					$this->add_control(
						'ts_sf_form_button_general',
						[
							'label' => __( 'General', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_responsive_control(
						'ts_sf_submit_tp[_space',
						[
							'label' => __( 'Top spacing', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-form-submit' => 'margin-top: {{SIZE}}{{UNIT}};',
							],
						]
					);





					$this->add_responsive_control(
						'ts_sf_submit_double_space',
						[
							'label' => __( 'Button spacing', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-form-submit' => 'grid-gap: {{SIZE}}{{UNIT}};',
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
								'{{WRAPPER}} .ts-form-submit .ts-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-form-submit .ts-btn svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_sf_search_button',
						[
							'label' => __( 'Search button', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);


					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_submit_btn_typo',
							'label' => __( 'Typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-form-submit .ts-btn-2',
						]
					);

					$this->add_responsive_control(
						'ts_sf_form_btn_c',
						[
							'label' => __( 'Color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'default' => '#fff',
							'selectors' => [
								'{{WRAPPER}} .ts-form-submit .ts-btn-2' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-form-submit .ts-btn-2 svg' => 'fill: {{VALUE}}',
							],

						]
					);




					$this->add_responsive_control(
						'ts_sf_form_btn_bg',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form-submit .ts-btn-2' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_sf_form_btn_height',
						[
							'label' => __( 'Button Height', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-form-submit .ts-btn-2' => 'height: {{SIZE}}{{UNIT}};',
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
								'{{WRAPPER}} .ts-form-submit .ts-btn-2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_sf_search_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-form-submit .ts-btn-2',
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
								'{{WRAPPER}} .ts-form-submit .ts-btn-2' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_submit_shadow',
							'label' => __( 'Box Shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-form-submit .ts-btn-2',
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
								'{{WRAPPER}} .ts-form-submit .ts-btn-2' => 'grid-gap: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_sf_reset_btn',
						[
							'label' => __( 'Reset button', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);


					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_reset_btn_typo',
							'label' => __( 'Typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-form-submit .ts-btn-1',
						]
					);

					$this->add_responsive_control(
						'ts_sf_reset_btn_c',
						[
							'label' => __( 'Color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form-submit .ts-btn-1' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-form-submit .ts-btn-1 svg' => 'fill: {{VALUE}}',
							],

						]
					);




					$this->add_responsive_control(
						'ts_sf_reset_btn_bg',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form-submit .ts-btn-1' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_sf_reset_btn_height',
						[
							'label' => __( 'Button Height', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-form-submit .ts-btn-1' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_sf_reset_padding',
						[
							'label' => __( 'Button padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .ts-form-submit .ts-btn-1' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_sf_reset_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-form-submit .ts-btn-1',
						]
					);

					$this->add_responsive_control(
						'ts_sf_reset_btn_radius',
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
								'{{WRAPPER}} .ts-form-submit .ts-btn-1' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_reset_shadow',
							'label' => __( 'Box Shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-btn-1',
						]
					);

					$this->add_responsive_control(
						'ts_reset_ico_pad',
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
								'{{WRAPPER}} .ts-form-submit .ts-btn-1' => 'grid-gap: {{SIZE}}{{UNIT}};',
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


					$this->add_control(
						'ts_sf_form_btn_c_h',
						[
							'label' => __( 'Search text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form-submit .ts-btn-2:hover' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-form-submit .ts-btn-2:hover svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_sf_form_btn_bg_h',
						[
							'label' => __( 'Search background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form-submit .ts-btn-2:hover' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_sf_form_btn_border_h',
						[
							'label' => __( 'Search border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form-submit .ts-btn-2:hover' => 'border-color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_submit_shadow_hover',
							'label' => __( 'Search box shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-form-submit .ts-btn-2:hover',
						]
					);


					$this->add_control(
						'ts_sf_form_btn_c_reset_h',
						[
							'label' => __( 'Reset text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form-submit .ts-btn-1:hover' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-form-submit .ts-btn-1:hover svg' => 'fill: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_sf_form_btn_bg_reset_h',
						[
							'label' => __( 'Reset background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form-submit .ts-btn-1:hover' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_sf_reset_btn_border_h',
						[
							'label' => __( 'Reset border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-form-submit .ts-btn-1:hover' => 'border-color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_reset_shadow_hover',
							'label' => __( 'Reset box shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-form-submit .ts-btn-1:hover',
						]
					);



				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();



		$this->start_controls_section(
			'inline_popup_list',
			[
				'label' => __( 'Terms', 'voxel-elementor' ),
				'tab' => 'tab_inline',
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
			'ts_ml_inline',
			[
				'label' => __( 'User location', 'voxel-elementor' ),
				'tab' => 'tab_inline',
			]
		);

			$this->add_control(
				'ml_inline_color',
				[
					'label' => __( 'Icon color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .inline-user-location i'
						=> 'color: {{VALUE}}',
						'{{WRAPPER}} .inline-user-location svg'
						=> 'fill: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'ml_inline_color_h',
				[
					'label' => __( 'Icon color (Hover)', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .inline-user-location:hover i'
						=> 'color: {{VALUE}}',
						'{{WRAPPER}} .inline-user-location:hover svg'
						=> 'fill: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'ml_inline_size',
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
						'{{WRAPPER}} .inline-user-location i' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .inline-user-location svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'ml_inline_MARGIN',
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
						'{{WRAPPER}} .inline-user-location' => !is_rtl() ? 'right: {{SIZE}}{{UNIT}};' : 'left: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_sf_popup_number',
			[
				'label' => __( 'Stepper', 'voxel-elementor' ),
				'tab' => 'tab_inline',
			]
		);


				$this->add_control(
					'ts_popup_number',
					[
						'label' => __( 'Number popup', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
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
							'{{WRAPPER}} .ts-inline-filter .ts-stepper-input input' => 'font-size: {{SIZE}}{{UNIT}};',
						],
					]
				);


		$this->end_controls_section();

		$this->start_controls_section(
			'pg_icon_button',
			[
				'label' => __( 'Stepper buttons', 'voxel-elementor' ),
				'tab' => 'tab_inline',
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
								'{{WRAPPER}} .ts-icon-btn.inline-btn-ts' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_number_btn_color',
						[
							'label' => __( 'Button icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-icon-btn.inline-btn-ts i'
								=> 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-icon-btn.inline-btn-ts svg'
								=> 'fill: {{VALUE}}',
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
								'{{WRAPPER}} .ts-icon-btn.inline-btn-ts i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-icon-btn.inline-btn-ts svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_number_btn_bg',
						[
							'label' => __( 'Button background', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-icon-btn.inline-btn-ts' => 'background-color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_number_btn_border',
							'label' => __( 'Button border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-icon-btn.inline-btn-ts',
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
								'{{WRAPPER}} .ts-icon-btn.inline-btn-ts' => 'border-radius: {{SIZE}}{{UNIT}};',
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
								'{{WRAPPER}} .ts-icon-btn.inline-btn-ts:hover i'
								=> 'color: {{VALUE}};',
								'{{WRAPPER}} .ts-icon-btn.inline-btn-ts:hover svg'
								=> 'fill: {{VALUE}};',
							],

						]
					);

					$this->add_control(
						'ts_number_btn_bg_h',
						[
							'label' => __( 'Button background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-icon-btn.inline-btn-ts:hover'
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
								'{{WRAPPER}} .ts-icon-btn.inline-btn-ts:hover'
								=> 'border-color: {{VALUE}};',
							],

						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_sf_popup_range',
			[
				'label' => __( 'Range slider', 'voxel-elementor' ),
				'tab' => 'tab_inline',
			]
		);

			$this->add_control(
				'ts_popup_range',
				[
					'label' => __( 'Range slider', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'ts_popup_range_size',
				[
					'label' => __( 'Range value size', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-inline-filter .range-slider-wrapper .range-value' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'ts_popup_range_val',
				[
					'label' => __( 'Range value color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-inline-filter .range-slider-wrapper .range-value'
						=> 'color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'ts_popup_range_bg',
				[
					'label' => __( 'Range background', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-inline-filter .noUi-target'
						=> 'background-color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'ts_popup_range_bg_selected',
				[
					'label' => __( 'Selected range background', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-inline-filter .noUi-connect'
						=> 'background-color: {{VALUE}}',
					],

				]
			);

			$this->add_control(
				'ts_popup_range_handle',
				[
					'label' => __( 'Handle background color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-inline-filter .noUi-handle' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'ts_popup_range_handle_border',
					'label' => __( 'Handle border', 'voxel-elementor' ),
					'selector' => '{{WRAPPER}} .ts-inline-filter .noUi-handle',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_sf_popup_switch',
			[
				'label' => __( 'Switcher', 'voxel-elementor' ),
				'tab' => 'tab_inline',
			]
		);

				$this->add_control(
					'ts_popup_switch',
					[
						'label' => __( 'Switch slider', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					'ts_popup_switch_bg',
					[
						'label' => __( 'Switch slider background (Inactive)', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-inline-filter .onoffswitch .onoffswitch-label'
							=> 'background-color: {{VALUE}}',
						],

					]
				);

				$this->add_control(
					'ts_popup_switch_bg_active',
					[
						'label' => __( 'Switch slider background (Active)', 'voxel-elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ts-inline-filter .onoffswitch .onoffswitch-checkbox:checked + .onoffswitch-label'
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
							'{{WRAPPER}} .ts-inline-filter .onoffswitch .onoffswitch-label:before'
							=> 'background-color: {{VALUE}}',
						],

					]
				);



		$this->end_controls_section();

		$this->start_controls_section(
			'auth_checkbox_section',
			[
				'label' => __( 'Checkbox', 'voxel-elementor' ),
				'tab' => 'tab_inline',
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
				'label' => __( 'Radio', 'voxel-elementor' ),
				'tab' => 'tab_inline',
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
			'auth_primary_btn',
			[
				'label' => __( 'Toggle button', 'voxel-elementor' ),
				'tab' => 'tab_general',
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
							'label' => __( 'Typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-toggle-text',
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
								'{{WRAPPER}} .ts-filter-toggle' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'one_btn_c',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-toggle-text' => 'color: {{VALUE}}',
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
								'{{WRAPPER}} .ts-filter-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);


					$this->add_responsive_control(
						'one_btn_bg',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-filter-toggle' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'one_btn_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-filter-toggle',
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
								'{{WRAPPER}} .ts-filter-toggle i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-filter-toggle svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
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
								'{{WRAPPER}} .ts-filter-toggle' => 'grid-gap: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'one_btn_icon_color',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-filter-toggle i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-filter-toggle svg' => 'fill: {{VALUE}}',
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
								'{{WRAPPER}} .ts-filter-toggle:hover .ts-toggle-text' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'one_btn_bg_h',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-filter-toggle:hover' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'one_btn_border_h',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-filter-toggle:hover' => 'border-color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'one_btn_icon_color_h',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-filter-toggle:hover i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-filter-toggle:hover svg' => 'fill: {{VALUE}}',
							],

						]
					);



				$this->end_controls_tab();

				$this->start_controls_tab(
					'one_btn_filled',
					[
						'label' => __( 'Filled', 'voxel-elementor' ),
					]
				);

					$this->add_responsive_control(
						'one_btn_c_filled',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-filter-toggle.ts-filled .ts-toggle-text' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'one_btn_bg_filled',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-filter-toggle.ts-filled' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'one_btn_border_filled',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-filter-toggle.ts-filled' => 'border-color: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'one_btn_icon_color_filled',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-filter-toggle.ts-filled i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ts-filter-toggle.ts-filled svg' => 'fill: {{VALUE}}',
							],

						]
					);



				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ts_toggle_popup',
			[
				'label' => __( 'Toggle popup', 'voxel-elementor' ),
				'tab' => 'tab_general',
			]
		);


			$this->add_control(
				'toggle_pg_width',
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
						'.ts-search-portal{{WRAPPER}} .ts-field-popup' => 'min-width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'toggle_max_width',
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
					'selectors' => [
						'.ts-search-portal{{WRAPPER}} .ts-field-popup' => 'max-width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'toggle_max_height',
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
					'selectors' => [
						'.ts-search-portal{{WRAPPER}} .ts-popup-content-wrapper' => 'max-height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'toggle_spacing_value',
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
						'.ts-search-portal{{WRAPPER}} .toggle-sf > .ts-filter-wrapper > .ts-form-group' => 'padding: {{SIZE}}{{UNIT}} !important;',

						'.ts-search-portal{{WRAPPER}}  .toggle-sf' => 'margin: -{{SIZE}}{{UNIT}} !important;',

					],

				]
			);


		$this->end_controls_section();

		$this->start_controls_section(
			'ts_input_suffix',
			[
				'label' => __( 'Toggle: Active count', 'voxel-elementor' ),
				'tab' => 'tab_general',
			]
		);

			$this->add_responsive_control(
				'ts_suffix_text',
				[
					'label' => __( 'Text color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-filter-count' => 'color: {{VALUE}}',
					],

				]
			);


			$this->add_responsive_control(
				'ts_suffix_bg',
				[
					'label' => __( 'Background color', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ts-filter-count' => 'background: {{VALUE}}',
					],

				]
			);

			$this->add_responsive_control(
				'ts_suffix_mg',
				[
					'label' => __( 'Right margin', 'voxel-elementor' ),
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
						'{{WRAPPER}} .ts-filter-count' => !is_rtl() ? 'right: {{SIZE}}{{UNIT}};' : 'left: {{SIZE}}{{UNIT}};',
					],
				]
			);




		$this->end_controls_section();


		$this->start_controls_section(
			'ts_feed_switcher',
			[
				'label' => __( 'Map/feed switcher', 'voxel-elementor' ),
				'tab' => 'tab_general',
			]
		);

			$this->start_controls_tabs(
				'ts_fswitch_tabs'
			);

				/* Normal tab */

				$this->start_controls_tab(
					'ts_fswitch_normal',
					[
						'label' => __( 'Normal', 'voxel-elementor' ),
					]
				);



					$this->add_control(
						'ts_freset_container',
						[
							'label' => __( 'Container', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);

					$this->add_responsive_control(
						'm_freset_spacing',
						[
							'label' => __( 'Bottom', 'voxel-elementor' ),
							'description' => __( 'Distance from bottom of the screen', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-switcher-btn' => 'bottom: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'm_freset_margin',
						[
							'label' => __( 'Side margin', 'voxel-elementor' ),
							'description' => __( 'Distance from left/right edges of the screen', 'voxel-elementor' ),
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
								'{{WRAPPER}} .ts-switcher-btn' => 'margin-left: {{SIZE}}{{UNIT}};margin-right: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'ts_freset_justify',
						[
							'label' => __( 'Justify', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::SELECT,
							'options' => [
								'flex-start'  => __( 'Left', 'voxel-elementor' ),
								'center' => __( 'Center', 'voxel-elementor' ),
								'flex-end' => __( 'Right', 'voxel-elementor' ),
							],

							'selectors' => [
								'{{WRAPPER}} .ts-switcher-btn' => 'justify-content: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'ts_freset_button',
						[
							'label' => __( 'Button', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::HEADING,
							'separator' => 'before',
						]
					);



					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'ts_fswitch_btn_typo',
							'label' => __( 'Typography', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-switcher-btn .ts-btn',
						]
					);

					$this->add_responsive_control(
						'ts_fswitch_btn_c',
						[
							'label' => __( 'Color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-switcher-btn .ts-btn' => 'color: {{VALUE}}',
							],

						]
					);




					$this->add_responsive_control(
						'ts_fswitch_btn_bg',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-switcher-btn .ts-btn' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_responsive_control(
						'ts_fswitch_btn_height',
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
								'{{WRAPPER}} .ts-switcher-btn .ts-btn' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_fswitch_btn_padding',
						[
							'label' => __( 'Button padding', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em' ],
							'selectors' => [
								'{{WRAPPER}} .ts-switcher-btn .ts-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'ts_fswitch_btn_border',
							'label' => __( 'Border', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}} .ts-switcher-btn .ts-btn',
						]
					);

					$this->add_responsive_control(
						'ts_fswitch_btn_btn_radius',
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
								'{{WRAPPER}} .ts-switcher-btn .ts-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_fswitch_btn_shadow',
							'label' => __( 'Box Shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}}  .ts-switcher-btn .ts-btn',
						]
					);

					$this->add_responsive_control(
						'ts_fswitch_btn_i_pad',
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
								'{{WRAPPER}}  .ts-switcher-btn .ts-btn' => 'grid-gap: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'fswitch_icon_size',
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
								'{{WRAPPER}} .ts-switcher-btn .ts-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ts-switcher-btn .ts-btn svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'ts_fswitch_btn_i',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-switcher-btn .ts-btn i' => 'color: {{VALUE}}',
									'{{WRAPPER}} .ts-switcher-btn .ts-btn svg' =>  'fill: {{VALUE}}',
							],

						]
					);






				$this->end_controls_tab();


				/* Hover tab */

				$this->start_controls_tab(
					'ts_fswitch_hover',
					[
						'label' => __( 'Hover', 'voxel-elementor' ),
					]
				);


					$this->add_control(
						'ts_fswitch_btn_c_h',
						[
							'label' => __( 'Text color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-switcher-btn .ts-btn:hover' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_fswitch_btn_bg_h',
						[
							'label' => __( 'Background color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ts-switcher-btn .ts-btn:hover' => 'background: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_fswitch_btn_border_h',
						[
							'label' => __( 'Border color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}}  .ts-switcher-btn .ts-btn:hover' => 'border-color: {{VALUE}}',
							],

						]
					);

					$this->add_control(
						'ts_fswitch_btn_icon_h',
						[
							'label' => __( 'Icon color', 'voxel-elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}}  .ts-switcher-btn .ts-btn:hover i' => 'color: {{VALUE}}',
							],

						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'ts_fswitch_btn_shadow_h',
							'label' => __( 'Box Shadow', 'voxel-elementor' ),
							'selector' => '{{WRAPPER}}  .ts-switcher-btn .ts-btn:hover',
						]
					);



				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'custom_popup',
			[
				'label' => __( 'Popups: Custom style', 'voxel-elementor' ),
				'tab' => 'tab_general',
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

			$this->add_responsive_control(
				'google_top_margin',
				[
					'label' => __( 'Autosuggest top margin', 'voxel-elementor' ),
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
						'.pac-container, .ts-autocomplete-dropdown .suggestions-list' => 'margin-top: {{SIZE}}{{UNIT}} !important;',
					],
				]
			);

		$this->end_controls_section();
	}

	protected function render( $instance = [] ) {
		$post_types = [];
		$default_post_type = $this->_get_default_post_type();
		$post_type_config = [];

		foreach ( (array) $this->get_settings_for_display('ts_choose_post_types') as $post_type_key ) {
			$post_type = \Voxel\Post_Type::get( $post_type_key );
			if ( ! $post_type ) {
				continue;
			}

			$post_types[] = $post_type;
			$post_type_config[ $post_type->get_key() ] = $this->_get_post_type_config( $post_type );
		}

		$switchable_desktop = $this->get_settings( 'mf_switcher_desktop' ) === 'yes';
		$desktop_default = $this->get_settings( 'switcher_desktop_default' );
		$switchable_tablet = $this->get_settings( 'mf_switcher_tablet' ) === 'yes';
		$tablet_default = $this->get_settings( 'switcher_tablet_default' );
		$switchable_mobile = $this->get_settings( 'mf_switcher_mobile' ) === 'yes';
		$mobile_default = $this->get_settings( 'switcher_mobile_default' );

		$general_config = [
			'is_edit_mode' => \Voxel\is_edit_mode(),
			'showLabels' => $this->get_settings_for_display('ts_sf_input_label') === 'yes',
			'defaultType' => $default_post_type ? $default_post_type->get_key() : null,
			'onSubmit' => [],
			'searchOn' => \Voxel\from_list( $this->get_settings_for_display('ts_search_on'), [ 'submit', 'filter_update' ], 'submit' ),
			'portal' => [
				'desktop' => $this->get_settings_for_display('form_toggle_desktop') === 'yes',
				'tablet' => $this->get_settings_for_display('form_toggle_tablet') === 'yes',
				'mobile' => $this->get_settings_for_display('form_toggle_mobile') === 'yes',
			],
			'popupAnchor' => [
				'desktop' => false,
				'tablet' => false,
				'mobile' => false,
			],
			'autocomplete' => $this->_get_autocomplete_config(),
		];

		if ( $this->get_settings_for_display( 'ts_on_submit' ) === 'submit-to-archive' ) {
			$general_config['onSubmit'] = [
				'action' => 'submit-to-archive',
			];
		} elseif ( $this->get_settings_for_display( 'ts_on_submit' ) === 'submit-to-page' ) {
			$page_id = $this->get_settings_for_display('ts_submit_to_page');
			$general_config['onSubmit'] = [
				'action' => 'submit-to-page',
				'pageId' => $page_id,
				'pageLink' => get_permalink( $page_id ),
			];
		} else {
			$post_feed = \Voxel\get_related_widget( $this, $this->_get_template_id(), 'feedToSearch', 'left' );
			$map = \Voxel\get_related_widget( $this, $this->_get_template_id(), 'mapToSearch', 'left' );
			// dump($post_feed);

			$general_config['onSubmit'] = [
				'action' => 'post-to-feed',
				'postFeedId' => $post_feed['id'] ?? null,
				'mapId' => $map['id'] ?? null,
				'updateUrl' => $this->_update_url(),
			];

			$general_config['popupAnchor'] = [
				'desktop' => ( $map['settings']['anchor_toggle_desktop'] ?? false ) ? 'fixed' : 'marker',
				'tablet' => ( $map['settings']['anchor_toggle_tablet'] ?? false ) ? 'fixed' : 'marker',
				'mobile' => ( $map['settings']['anchor_toggle_mobile'] ?? false ) ? 'fixed' : 'marker',
			];

			$general_config['show_total_count'] = $post_feed['settings']['ts_show_total_count'] ?? false;
		}

		wp_print_styles( $this->get_style_depends() );
		require locate_template( 'templates/widgets/search-form.php' );

		if ( \Voxel\is_edit_mode() ) {
			printf( '<script type="text/javascript">%s</script>', 'setTimeout( () => window.render_search_form(), 30 );' );
		}
	}

	public function get_script_depends() {
		return [
			'vx:search-form.js',
		];
	}

	public function get_style_depends() {
		return [ 'vx:forms.css'];
	}

	public function _get_post_type_config( $post_type ) {
		$update_url = $this->_update_url();
		$has_url_query = ( $_GET['type'] ?? null ) === $post_type->get_key();

		$card_template = $this->get_settings_for_display( sprintf( 'ts_card_template__%s', $post_type->get_key() ) );
		$card_template_map = $this->get_settings_for_display( sprintf( 'ts_card_template_map__%s', $post_type->get_key() ) );
		$config = [
			'key' => $post_type->get_key(),
			'label' => $post_type->get_label(),
			'icon' => \Voxel\get_icon_markup( $post_type->get_icon() ),
			'archive' => $post_type->get_archive_link(),
			'template_id' => is_numeric( $card_template ) ? (int) $card_template : null,
			'map_template_id' => is_numeric( $card_template_map ) ? (int) $card_template_map : null,
			'filters' => [],
		];

		$filter_list = (array) $this->get_settings_for_display( sprintf( 'ts_filter_list__%s', $post_type->get_key() ) );
		foreach ( $filter_list as $filter_config ) {
			if ( $filter = $post_type->get_filter( $filter_config['ts_choose_filter'] ?? '' ) ) {
				// needed to avoid cache issues when more than one widget is used on the same page
				$filter->reset_frontend_config();

				$controls = [];
				foreach ( $filter->get_elementor_controls() as $control_key => $control ) {
					$controls[ $control_key ] = $filter_config[ $control['full_key'] ?? sprintf( '%s:%s', $filter->get_key(), $control_key ) ] ?? null;
				}
				$filter->set_elementor_config( $controls );
				$filter->set_search_widget( $this );

				$filter_value = null;
				if ( $update_url ) {
					$filter_value = wp_unslash( $_GET[ $filter->get_key() ] ?? null );
				}

				$fallback_value = null;
				if ( ( ! $update_url || ! $has_url_query ) && ( $filter_config['ts_default_value'] ?? null ) === 'yes' ) {
					$fallback_value = $filter->get_default_value_from_elementor( $controls );
				}

				$filter->set_value( $filter_value ?? $fallback_value );

				if ( ( $filter_config['ts_reset_value'] ?? null ) === 'default_value' ) {
					$filter->resets_to( $filter->get_default_value_from_elementor( $controls ) );
				}

				$config['filters'][ $filter->get_key() ] = $filter->get_frontend_config();
			}
		}

		return $config;
	}

	public function _ssr_filters() {
		$post_type = $this->_get_default_post_type();
		if ( ! $post_type ) {
			return;
		}

		$show_labels = $this->get_settings_for_display('ts_sf_input_label') === 'yes';

		if ( $this->get_settings_for_display('cpt_filter_show') === 'yes' ) { ?>
			<div v-if="false" class="ts-form-group elementor-column choose-cpt-filter">
				<?php if ( $show_labels ): ?>
					<label><?= _x( 'Post type', 'search form widget', 'voxel-elementor' ) ?></label>
				<?php endif ?>
				<div class="ts-filter ts-popup-target ts-filled">
					<span><?= \Voxel\get_icon_markup( $post_type->get_icon() ) ?></span>
					<div class="ts-filter-text"><?= $post_type->get_label() ?></div>
					<div class="ts-down-icon"></div>
				</div>
			</div>
		<?php }

		$filter_list = (array) $this->get_settings_for_display(
			sprintf( 'ts_filter_list__%s', $post_type->get_key() )
		);

		foreach ( $filter_list as $filter_config ) {
			$filter = $post_type->get_filter( $filter_config['ts_choose_filter'] ?? '' );
			if ( $filter ) { ?>
				<?php $filter->ssr( [
					'show_labels' => $show_labels,
					'wrapper_class' => 'ts-form-group elementor-repeater-item-'.$filter_config['_id'],
				] ) ?>
			<?php }
		}
	}

	public function _get_default_post_type() {
		$chosen_types = (array) $this->get_settings_for_display('ts_choose_post_types');

		if ( ! $this->_update_url() ) {
			$post_type_key = ! empty( $chosen_types ) ? $chosen_types[0] : 'post';
			return \Voxel\Post_Type::get( $post_type_key );
		}

		$post_type_key = isset( $_GET['type'] ) && in_array( $_GET['type'], $chosen_types )
			? sanitize_text_field( $_GET['type'] )
			: ( ! empty( $chosen_types ) ? $chosen_types[0] : 'post' );

		return \Voxel\Post_Type::get( $post_type_key );
	}

	public function _update_url() {
		return $this->get_settings_for_display('ts_update_url') === 'yes';
	}

	public function _get_autocomplete_config() {
		if ( \Voxel\get( 'settings.maps.provider' ) === 'mapbox' ) {
			return [
				'countries' => array_filter( (array) \Voxel\get( 'settings.maps.mapbox.autocomplete.countries' ) ),
				'feature_types' => array_filter( (array) \Voxel\get( 'settings.maps.mapbox.autocomplete.feature_types' ) ),
			];
		} else {
			return [
				'countries' => array_filter( (array) \Voxel\get( 'settings.maps.google_maps.autocomplete.countries' ) ),
				'feature_types' => array_filter( (array) \Voxel\get( 'settings.maps.google_maps.autocomplete.feature_types' ) ),
			];
		}
	}

	protected function register_runtime_controls() {
		$this->add_control(
			'ts_choose_post_types',
			[
				'label' => __( 'Choose post types', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
			]
		);

		$this->add_control(
			'ts_sf_input_label',
			[
				'label' => __( 'Show label', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			]
		);

		$this->add_control( 'ts_search_on', [
			'label' => __( 'Perform search:', 'voxel-elementor' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'submit',
			'options' => [
				'submit' => 'When the search button is clicked',
				'filter_update' => 'When any filter value is updated',
			],
		] );

		$this->add_control( 'ts_submit_to_page', [
			'label' => __( 'Enter page ID', 'voxel-elementor' ),
			'type' => \Elementor\Controls_Manager::NUMBER,
		] );

		$this->add_control(
			'form_toggle_desktop',
			[
				'label' => __( 'Enable on desktop', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'form_toggle_tablet',
			[
				'label' => __( 'Enable on tablet', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'form_toggle_mobile',
			[
				'label' => __( 'Enable on mobile', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			]
		);

		$this->add_control( 'ts_on_submit', [
			'label' => __( 'On form submit', 'voxel-elementor' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'post-to-feed',
		] );

		$this->add_control(
			'cpt_filter_show',
			[
				'label' => __( 'Show custom post type filter', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'voxel-elementor' ),
				'label_off' => __( 'Hide', 'voxel-elementor' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control( 'ts_update_url', [
			'label' => __( 'Update URL with search values?', 'voxel-elementor' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label_on' => __( 'Yes', 'voxel-elementor' ),
			'label_off' => __( 'No', 'voxel-elementor' ),
			'return_value' => 'yes',
			'default' => 'yes',
		] );

		$this->add_control(
			'mf_switcher_desktop',
			[
				'label' => __( 'Enable on desktop', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'switcher_desktop_default',
			[
				'label' => __( 'Visible by default', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'feed',
				'condition' => [ 'mf_switcher_desktop' => 'yes' ],
				'options' => [
					'feed'  => __( 'Feed', 'voxel-elementor' ),
					'map' => __( 'Map', 'voxel-elementor' ),
				],
			]
		);

		$this->add_control(
			'mf_switcher_tablet',
			[
				'label' => __( 'Enable on tablet', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'switcher_tablet_default',
			[
				'label' => __( 'Visible by default', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'feed',
				'condition' => [ 'mf_switcher_tablet' => 'yes' ],
				'options' => [
					'feed'  => __( 'Feed', 'voxel-elementor' ),
					'map' => __( 'Map', 'voxel-elementor' ),
				],
			]
		);

		$this->add_control(
			'mf_switcher_mobile',
			[
				'label' => __( 'Enable on mobile', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'switcher_mobile_default',
			[
				'label' => __( 'Visible by default', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'feed',
				'condition' => [ 'mf_switcher_mobile' => 'yes' ],
				'options' => [
					'feed'  => __( 'Feed', 'voxel-elementor' ),
					'map' => __( 'Map', 'voxel-elementor' ),
				],
			]
		);

		$this->add_control(
			'ts_sf_form_btn_icon',
			[
				'label' => __( 'Search icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_sf_form_btn_icon_in',
			[
				'label' => __( 'Search icon (Input)', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_sf_form_btn_reset_icon',
			[
				'label' => __( 'Reset icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_toggle_icon',
			[
				'label' => __( 'Filter toggle icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_location_icon',
			[
				'label' => __( 'Location icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_mylocation_icon',
			[
				'label' => __( 'My location icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_map_icon',
			[
				'label' => __( 'Map view icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_list_icon',
			[
				'label' => __( 'List view icon', 'voxel-elementor' ),
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
			'ts_minus_icon',
			[
				'label' => __( 'Minus icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_plus_icon',
			[
				'label' => __( 'Plus icon', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_arrow_right',
			[
				'label' => __( 'Right arrow', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_arrow_left',
			[
				'label' => __( 'Left arrow', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'ts_show_search_btn',
			[
				'label' => __( 'Show search button', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'true',
				'options' => [
					'true'  => __( 'Yes', 'voxel-elementor' ),
					'false' => __( 'No', 'voxel-elementor' ),
				],
			]
		);

		$this->add_control(
			'ts_search_text',
			[
				'label' => __( 'Button label', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Search', 'voxel-elementor' ),
				'placeholder' => __( 'Type your text', 'voxel-elementor' ),
			]
		);

		$this->add_control(
			'ts_show_reset_btn',
			[
				'label' => __( 'Show reset button', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'false',
				'options' => [
					'true'  => __( 'Yes', 'voxel-elementor' ),
					'false' => __( 'No', 'voxel-elementor' ),
				],
			]
		);

		$this->add_control(
			'ts_reset_text',
			[
				'label' => __( 'Button label', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Reset', 'voxel-elementor' ),
				'placeholder' => __( 'Type your text', 'voxel-elementor' ),
			]
		);

		$this->add_control(
			'ts_sf_wrap_desktop',
			[
				'label' => __( 'Enable horizontal scroll)', 'voxel-elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
			]
		);
	}

	public function register_instance_controls( $settings ) {
		$controls = [];

		if ( isset( $settings['ts_choose_post_types'] ) && is_array( $settings['ts_choose_post_types'] ) ) {
			foreach ( $settings['ts_choose_post_types'] as $post_type_key ) {
				$post_type = \Voxel\Post_Type::get( $post_type_key );
				if ( ! $post_type ) {
					continue;
				}

				$repeater = new \Elementor\Repeater;

				$repeater->add_control( 'ts_choose_filter', [
					'label' => __( 'Choose filter', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => '',
				] );

				$repeater->add_control( 'ts_default_value', [
					'label' => __( 'Add default value', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
				] );

				foreach ( $post_type->get_filters() as $filter ) {
					foreach ( $filter->get_elementor_controls() as $control_key => $control_args ) {
						if ( isset( $control_args['full_key'] ) ) {
							$control_key = $control_args['full_key'];
						} else {
							$control_key = $filter->get_key().':'.$control_key;
						}

						if ( ! isset( $control_args['condition'] ) ) {
							$control_args['condition'] = [];
						}

						$control_args['condition']['ts_choose_filter'] = $filter->get_key();

						if ( ( $control_args['conditional'] ?? null ) !== false ) {
							$control_args['condition']['ts_default_value'] = 'yes';
						}

						if ( $control_args['responsive'] ?? false ) {
							$repeater->add_responsive_control( $control_key, $control_args );
						} else {
							$repeater->add_control( $control_key, $control_args );
						}
					}
				}

				$repeater->add_control( 'ts_reset_value', [
					'label' => __( 'On search form reset, set the value of this filter to:', 'voxel-elementor' ),
					'label_block' => true,
					'type' => \Elementor\Controls_Manager::SELECT,
					'condition' => [ 'ts_default_value' => 'yes' ],
					'options' => [
						'empty' => 'Empty value',
						'default_value' => 'Default value',
					],
				] );

				$controls[ sprintf( 'ts_filter_list__%s', $post_type->get_key() ) ] = [
					'label' => __( 'Add filters', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::REPEATER,
					'fields' => $repeater->get_controls(),
					'title_field' => '{{ts_choose_filter}}',
					'_disable_loop' => true,
				];

				$controls[ sprintf( 'ts_card_template__%s', $post_type->get_key() ) ] = [
					'label' => __( 'Preview card template', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'main',
				];

				$controls[ sprintf( 'ts_card_template_map__%s', $post_type->get_key() ) ] = [
					'label' => __( 'Custom map popup template', 'voxel-elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'main',
				];
			}
		}

		return $controls;
	}
}