<?php

namespace Voxel\Controllers\Elementor;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Elementor_Controller extends \Voxel\Controllers\Base_Controller {

	protected function authorize() {
		return class_exists( '\Elementor\Plugin' );
	}

	protected function hooks() {
		$this->on( 'elementor/widgets/register', '@register_widgets' );
		$this->on( 'elementor/controls/controls_registered', '@register_custom_controls', 1000 );
		$this->on( 'admin_footer', '@load_backend_icon_picker', 100 );
		$this->on( 'elementor/document/after_save', '@save_voxel_config', 100, 2 );
		$this->on( 'voxel_ajax_elementor.save_temporary_config', '@save_temporary_voxel_config', 100, 2 );
		$this->on( 'elementor/elements/categories_registered', '@register_widget_categories' );
		$this->on( 'admin_footer', '@enqueue_line_awesome_in_backend' );
		$this->on( 'elementor/editor/after_enqueue_scripts', '@enqueue_line_awesome_in_backend' );
		$this->on( 'elementor/init', '@add_custom_tabs' );
		$this->on( 'admin_print_footer_scripts', '@cache_values_of_vx_post_select' );

		$this->on( 'elementor/editor/init', '@set_current_post_in_editor' );
		$this->on( 'elementor/ajax/register_actions', '@set_current_post_in_editor' );

		$this->on( 'elementor/editor/init', '@set_current_term_in_editor' );
		$this->on( 'elementor/ajax/register_actions', '@set_current_term_in_editor' );

		$this->on( 'wp_head', '@print_dynamic_styles' );

		$this->filter( 'elementor/widget/print_template', '@handle_tags_in_editor' );

		if ( \Voxel\get('settings.icons.line_awesome.enabled') ) {
			$this->filter( 'elementor/icons_manager/additional_tabs', '@register_line_awesome_pack' );
		}

		$this->filter( 'elementor/editor/localize_settings', '@editor_config' );
		$this->filter( 'parse_query', '@hide_voxel_templates_from_library' );
		$this->filter( 'wp_get_attachment_image_src', '@fix_editor_image_preview', 100, 4 );

		// css should be rendered for all possible visibility states
		$this->on( 'elementor/element/before_parse_css', function() { \Voxel\set_rendering_css(true); } );
		$this->on( 'elementor/element/parse_css', function() { \Voxel\set_rendering_css(false); } );

		$this->on( 'elementor/element/text-editor/section_editor/before_section_end', '@custom_texteditor_controls' );

		$this->on( 'elementor/element/image/section_style_image/before_section_end', '@custom_image_controls' );
		$this->on( 'elementor/element/heading/section_title/before_section_end', '@custom_heading_controls' );

		$this->on( 'elementor/theme/register_locations', '@register_locations' );

		$this->on( 'elementor/page_templates/header-footer/before_content', '\Voxel\print_header' );
		$this->on( 'elementor/page_templates/header-footer/after_content', '\Voxel\print_footer' );

		// print form-group and popup templates only in "Elementor Canvas"
		$this->on( 'elementor/page_templates/canvas/before_content', '@print_required_templates_in_canvas' );

		if ( apply_filters( 'voxel/custom-elementor-settings-parser', true ) !== false ) {
			$this->on( 'elementor/frontend/before_render', '@custom_settings_parser' );
		}

		// workaround for https://github.com/elementor/elementor/issues/13038
		if ( apply_filters( 'voxel/elementor-missing-globals-bugfix', true ) !== false ) {
			$this->on( 'rest_api_init', '@missing_globals_bugfix' );
		}

		add_action( 'elementor/core/files/clear_cache', function() {
			wp_cache_flush();
		} );

		// if ( apply_filters( 'voxel/delete-elementor-css-on-save', true ) !== false ) {
		// 	add_action( 'elementor/document/after_save', function( $document ) {
		// 		delete_post_meta_by_key( '_elementor_css' );
		// 	} );
		// }
	}

	protected function register_widgets() {
		$manager = \Elementor\Plugin::instance()->widgets_manager;
		foreach ( \Voxel\config('widgets') as $widget ) {
			$manager->register( new $widget );
		}
	}

	/**
	 * Allows for rendering dynamic tags in Elementor editor while the user is editing.
	 *
	 * @since 1.0
	 */
	protected function handle_tags_in_editor( $template ) {
		if ( empty( $template ) ) {
			return $template;
		}

		return '<# var settings = voxel_handle_tags(settings) #>'.$template;
	}

	protected function register_custom_controls( $controls_manager ) {
		$controls_manager->register( new \Voxel\Custom_Controls\Repeater_Control );
		$controls_manager->register( new \Voxel\Custom_Controls\Media_Control );
		$controls_manager->register( new \Voxel\Custom_Controls\Gallery_Control );
		$controls_manager->register( new \Voxel\Custom_Controls\Icons_Control );
		$controls_manager->register( new \Voxel\Custom_Controls\Select2_Control );
		$controls_manager->register( new \Voxel\Custom_Controls\Url_Control );
		$controls_manager->register( new \Voxel\Custom_Controls\Relation_Control );
		$controls_manager->register( new \Voxel\Custom_Controls\Text_Control );
		$controls_manager->register( new \Voxel\Custom_Controls\Textarea_Control );
		$controls_manager->register( new \Voxel\Custom_Controls\Number_Control );
		$controls_manager->register( new \Voxel\Custom_Controls\Wysiwyg_Control );
		$controls_manager->register( new \Voxel\Custom_Controls\Color_Control );
		$controls_manager->register( new \Voxel\Custom_Controls\Visibility_Control );
		$controls_manager->register( new \Voxel\Custom_Controls\Date_Time_Control );
		$controls_manager->register( new \Voxel\Custom_Controls\Code_Control );
		$controls_manager->register( new \Voxel\Custom_Controls\Post_Select_Control );
	}

	protected function load_backend_icon_picker() {
		if ( class_exists( '\Elementor\Plugin' ) ) {
			$config = \Elementor\Icons_Manager::get_icon_manager_tabs();
			require locate_template( 'templates/backend/icon-picker.php' );
		}
	}

	protected function register_line_awesome_pack( $packs ) {
		$base_url = trailingslashit( get_template_directory_uri() ).'assets/icons/line-awesome/';

		// @todo: minify line-awesome.css and line-awesome.js on production build
		$packs['la-regular'] = [
			'name' => 'la-regular',
			'label' => __( 'Line Awesome - Regular', 'voxel-backend' ),
			'url' => $base_url.'line-awesome.css',
			'enqueue' => [],
			'prefix' => 'la-',
			'displayPrefix' => 'lar',
			'labelIcon' => 'fab fa-font-awesome-alt',
			'ver' => '1.3.0',
			'fetchJson' => $base_url.'line-awesome-regular.js',
			'native' => false,
		];

		$packs['la-solid'] = [
			'name' => 'la-solid',
			'label' => __( 'Line Awesome - Solid', 'voxel-backend' ),
			'url' => $base_url.'line-awesome.css',
			'enqueue' => [],
			'prefix' => 'la-',
			'displayPrefix' => 'las',
			'labelIcon' => 'fab fa-font-awesome-alt',
			'ver' => '1.3.0',
			'fetchJson' => $base_url.'line-awesome-solid.js',
			'native' => false,
		];

		$packs['la-brands'] = [
			'name' => 'la-brands',
			'label' => __( 'Line Awesome - Brands', 'voxel-backend' ),
			'url' => $base_url.'line-awesome.css',
			'enqueue' => [],
			'prefix' => 'la-',
			'displayPrefix' => 'lab',
			'labelIcon' => 'fab fa-font-awesome-alt',
			'ver' => '1.3.0',
			'fetchJson' => $base_url.'line-awesome-brands.js',
			'native' => false,
		];

		return $packs;
	}

	protected function editor_config( $config ) {
		$post_id = \Elementor\Plugin::$instance->editor->get_post_id();
		$settings = \Voxel\get_custom_page_settings( $post_id );

		$config['voxel'] = [
			'relations' => (object) ( $settings['relations'] ?? [] ),
		];

		return $config;
	}

	protected function save_voxel_config( $document, $data ) {
		$config = json_decode( stripslashes( $_REQUEST['voxel'] ?? '' ), ARRAY_A );
		$settings_to_save = [];

		if ( ! empty( $config['relations'] ) && is_array( $config['relations'] ) ) {
			$settings_to_save['relations'] = $config['relations'];
		}

		if ( ! empty( $config['template_tabs'] ) && is_array( $config['template_tabs'] ) ) {
			$settings_to_save['template_tabs'] = $config['template_tabs'];
		}

		if ( ! empty( $settings_to_save ) ) {
			update_post_meta( $document->get_id(), '_voxel_page_settings', wp_slash( wp_json_encode( $settings_to_save ) ) );
		} else {
			delete_post_meta( $document->get_id(), '_voxel_page_settings' );
		}

		// always delete temporary config (used while in the editor)
		delete_post_meta( $document->get_id(), '_voxel_page_settings_tmp' );
	}

	protected function save_temporary_voxel_config() {
		try {
			if ( ! current_user_can('manage_options') ) {
				throw new \Exception( __( 'Permission denied.', 'voxel-backend' ) );
			}

			$document_id = absint( $_REQUEST['document_id'] ?? null );
			if ( ! $document_id ) {
				throw new \Exception( __( 'Invalid request', 'voxel-backend' ) );
			}

			$config = json_decode( stripslashes( $_REQUEST['voxel'] ?? '' ), ARRAY_A );
			$settings_to_save = [];

			if ( ! empty( $config['relations'] ) && is_array( $config['relations'] ) ) {
				$settings_to_save['relations'] = $config['relations'];
			}

			if ( ! empty( $config['template_tabs'] ) && is_array( $config['template_tabs'] ) ) {
				$settings_to_save['template_tabs'] = $config['template_tabs'];
			}

			if ( ! empty( $settings_to_save ) ) {
				update_post_meta( $document_id, '_voxel_page_settings_tmp', wp_slash( wp_json_encode( $settings_to_save ) ) );
			}

			return wp_send_json( [
				'success' => true,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function register_widget_categories( $elements_manager ) {
		$elements_manager->add_category( 'voxel', [
			'title' => __( 'Voxel 🎉', 'voxel-backend' ),
			'icon' => 'eicon-plus',
		] );
	}

	protected function hide_voxel_templates_from_library( $query ) {
		global $typenow;
		if ( ! is_admin() || $typenow !== 'elementor_library' || ( $_GET['tabs_group'] ?? '' ) !== 'library' ) {
			return $query;
		}

		if ( isset( $_GET['elementor_library_type'] ) && $_GET['elementor_library_type'] !== 'page' ) {
			return $query;
		}

		if ( ! isset( $query->query_vars['tax_query'] ) ) {
			$query->query_vars['tax_query'] = [];
		}

		$query->query_vars['tax_query'][] = [
			'taxonomy' => 'elementor_library_category',
			'field' => 'slug',
			'terms' => 'voxel-template',
			'operator' => 'NOT IN',
		];

		return $query;
	}

	protected function enqueue_line_awesome_in_backend() {
		$base_url = trailingslashit( get_template_directory_uri() ).'assets/icons/line-awesome/';
		wp_enqueue_style( 'line-awesome', $base_url.'line-awesome.css', [], '1.3.0' );
	}

	protected function add_custom_tabs() {
		\Elementor\Controls_Manager::add_tab( 'tab_voxel', 'Voxel' );
		\Elementor\Controls_Manager::add_tab( 'tab_inline', 'Inline' );
		\Elementor\Controls_Manager::add_tab( 'tab_general', 'General' );
		\Elementor\Controls_Manager::add_tab( 'tab_fields', 'Field style' );
	}

	protected function set_current_post_in_editor() {
		if ( \Voxel\is_elementor_ajax() ) {
			$template_id = absint( $_REQUEST['editor_post_id'] ?? '' );
			$current_post = \Voxel\get_post_for_preview( $template_id );
			\Voxel\set_current_post( $current_post );
		} elseif ( \Voxel\is_edit_mode() ) {
			$template_id = absint( $_REQUEST['post'] ?? '' );
			$current_post = \Voxel\get_post_for_preview( $template_id );
			\Voxel\set_current_post( $current_post );
		}
	}

	protected function set_current_term_in_editor() {
		if ( \Voxel\is_elementor_ajax() ) {
			$template_id = absint( $_REQUEST['editor_post_id'] ?? '' );
			$current_term = $this->_get_current_term_in_editor( $template_id );
			\Voxel\set_current_term( $current_term );
		} elseif ( \Voxel\is_edit_mode() ) {
			$template_id = absint( $_REQUEST['post'] ?? '' );
			$current_term = $this->_get_current_term_in_editor( $template_id );
			\Voxel\set_current_term( $current_term );
		}
	}

	private function _get_current_term_in_editor( $template_id ) {
		$taxonomy = current( array_filter( \Voxel\Taxonomy::get_all(), function( $taxonomy ) use ( $template_id ) {
			$templates = $taxonomy->get_templates();
			return in_array( $template_id, [ $templates['single'], $templates['card'] ] );
		} ) );

		if ( $taxonomy ) {
			$term = get_terms( [
				'taxonomy' => $taxonomy->get_key(),
				'number' => 1,
				'hide_empty' => false,
			] );

			if ( is_array( $term ) && ( $term[0] ?? null ) instanceof \WP_Term ) {
				return \Voxel\Term::get( $term[0] );
			}
		}

		return \Voxel\Term::dummy();
	}

	/**
	 * Fixes error: Elementor\Images_Manager retrieves previews in editor for
	 * all media controls. Those media controls that are using dynamic tags haven't
	 * been parsed yet and an invalid value is passed to `wp_get_attachment_image_src`.
	 *
	 * @since 1.0
	 */
	protected function fix_editor_image_preview( $image, $attachment_id, $size, $icon ) {
		if ( is_string( $attachment_id ) && strncmp( $attachment_id, '@tags()', 7 ) === 0 && \Voxel\is_elementor_ajax() ) {
			$attachment_id = \Voxel\render( $attachment_id );
			$src = wp_get_attachment_image_src( absint( $attachment_id ), $size, $icon );
			return $src ? $src : [''];
		}

		return $image;
	}

	protected function print_dynamic_styles() {
		$mobile_end = \Elementor\Plugin::$instance->breakpoints->get_breakpoints('mobile')->get_value();
		$tablet_start = $mobile_end + 1;
		$tablet_end = \Elementor\Plugin::$instance->breakpoints->get_breakpoints('tablet')->get_value();
		$desktop_start = $tablet_end + 1;
		if ( class_exists( '\Elementor\Plugin' ) ) {
			echo <<<HTML
			<style type="text/css">
				@media screen and (max-width: {$mobile_end}px) { .vx-hidden-mobile { display: none !important; } }
				@media screen and (min-width: {$tablet_start}px) and (max-width: {$tablet_end}px) { .vx-hidden-tablet { display: none !important; } }
				@media screen and (min-width: {$desktop_start}px) { .vx-hidden-desktop { display: none !important; } }
			</style>
			HTML;
		}
	}

	protected function custom_texteditor_controls( $widget ) {
		$widget->add_responsive_control( 'vx_paragraph_gap', [
			'label' => __( 'Paragraph Gap', 'voxel-backend' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => ['px'],
			'selectors' => [
				'{{WRAPPER}} p:not(:empty)' => 'margin-bottom: {{SIZE}}px;',
			],
		] );
	}

	protected function custom_image_controls( $widget ) {
		$widget->add_responsive_control( 'vx_paragraph_gap', [
			'label' => __( 'Aspect ratio', 'voxel-backend' ),
			'description' => __( 'Set image aspect ratio e.g 16/9', 'voxel-backend' ),
			'type' => \Elementor\Controls_Manager::TEXT,

			'selectors' => [
				'{{WRAPPER}} img' => 'aspect-ratio: {{VALUE}}; object-fit: cover;',
				'{{WRAPPER}}' => 'width: 100%;',
			],
		] );
	}

	protected function custom_heading_controls( $widget ) {
		$widget->add_responsive_control( 'vx_heading_nowrap', [
			'label' => __( 'Disable wrapping', 'voxel-backend' ),
			'description' => __( 'Disable text wrap and enable ellipsis for overflowing text', 'voxel-backend' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'nowrap',
			'selectors' => [
				'{{WRAPPER}} .elementor-heading-title' => 'white-space: nowrap;text-overflow: ellipsis; overflow: hidden;',
			],
		] );
	}

	protected function register_locations( $manager ) {
		$manager->register_location('header');
		$manager->register_location('footer');
	}

	protected function custom_settings_parser( $element ) {
		$get_settings = static::class.'::_get_active_settings';
		( \Closure::bind( function( $element ) use ( $get_settings ) {
			if ( ! $element->parsed_active_settings ) {
				$settings = \Voxel\is_elementor_pro_active()
					? $element->get_parsed_dynamic_settings()
					: $element->get_settings();

				$element->parsed_active_settings = $get_settings( $element, $settings, $element->get_controls() );
			}
		}, null, \Elementor\Controls_Stack::class ) )( $element );
	}

	public static function _get_active_settings( $element, $settings, $controls ) {
		// \Voxel\measure_start( 'elementor/get_active_settings' );
		$is_first_request = ! $settings && ! $element->active_settings;

		if ( ! $settings ) {
			if ( $element->active_settings ) {
				return $element->active_settings;
			}

			$settings = $element->get_controls_settings();

			$controls = $element->get_controls();
		}

		$active_settings = [];

		foreach ( $settings as $setting_key => $setting ) {
			if ( ! isset( $controls[ $setting_key ] ) ) {
				$active_settings[ $setting_key ] = $setting;

				continue;
			}

			$control = $controls[ $setting_key ];

			if ( static::_is_control_visible( $element, $control, $settings ) ) {
				$control_obj = \Elementor\Plugin::$instance->controls_manager->get_control( $control['type'] );

				if ( $control_obj instanceof \Elementor\Control_Repeater ) {
					foreach ( $setting as & $item ) {
						$item = static::_get_active_settings( $element, $item, $control['fields'] );
					}
				}

				$active_settings[ $setting_key ] = $setting;
			} else {
				$active_settings[ $setting_key ] = null;
			}
		}

		if ( $is_first_request ) {
			$element->active_settings = $active_settings;
		}

		// \Voxel\measure_end( 'elementor/get_active_settings' );
		return $active_settings;
	}

	public static function _is_control_visible( $element, $control, $values = null ) {
		if ( null === $values ) {
			$values = $element->get_settings();
		}

		if ( ! empty( $control['conditions'] ) && ! \Elementor\Conditions::check( $control['conditions'], $values ) ) {
			return false;
		}

		if ( empty( $control['condition'] ) ) {
			return true;
		}

		foreach ( $control['condition'] as $condition_key => $condition_value ) {
			$parts = explode( '!', $condition_key );
			$keys = explode( '[', $parts[0] );

			$pure_condition_key = $keys[0];
			$condition_sub_key = isset( $keys[1] ) ? substr( $keys[1], 0, -1 ) : null;
			$is_negative_condition = isset( $parts[1] );

			if ( ! isset( $values[ $pure_condition_key ] ) || null === $values[ $pure_condition_key ] ) {
				return false;
			}

			$instance_value = $values[ $pure_condition_key ];

			if ( $condition_sub_key && is_array( $instance_value ) ) {
				if ( ! isset( $instance_value[ $condition_sub_key ] ) ) {
					return false;
				}

				$instance_value = $instance_value[ $condition_sub_key ];
			}

			if ( is_array( $condition_value ) && ! empty( $condition_value ) ) {
				$is_contains = in_array( $instance_value, $condition_value, true );
			} elseif ( is_array( $instance_value ) && ! empty( $instance_value ) ) {
				$is_contains = in_array( $condition_value, $instance_value, true );
			} else {
				$is_contains = $instance_value === $condition_value;
			}

			if ( $is_negative_condition && $is_contains || ! $is_negative_condition && ! $is_contains ) {
				return false;
			}
		}

		return true;
	}

	protected function print_required_templates_in_canvas() {
		require locate_template( 'templates/components/popup.php' );
		require locate_template( 'templates/components/form-group.php' );
	}

	protected function missing_globals_bugfix() {
		$controller = \Elementor\Plugin::$instance->data_manager_v2->controllers['globals'] ?? null;
		if ( $controller ) {
			$controller->endpoints['globals/colors'] = new class( $controller ) extends \Elementor\Core\Editor\Data\Globals\Endpoints\Colors {
				public function get_item( $id, $request ) {
					$items = $this->get_kit_items();
					return $items[ $id ] ?? $items[ array_key_first( $items ) ];
				}
			};

			$controller->endpoints['globals/typography'] = new class( $controller ) extends \Elementor\Core\Editor\Data\Globals\Endpoints\Typography {
				public function get_item( $id, $request ) {
					$items = $this->get_kit_items();
					return $items[ $id ] ?? $items[ array_key_first( $items ) ];
				}
			};
		}
	}

	protected function cache_values_of_vx_post_select() {
		global $_vx_post_select_values;
		if ( is_array( $_vx_post_select_values ) ) {
			$ids = array_filter( array_map( 'absint', $_vx_post_select_values ) );
			$cache = [];
			if ( ! empty( $ids ) ) {
				global $wpdb;
				$id_in = join( ',', $ids );
				$results = $wpdb->get_results( "SELECT ID, post_title FROM {$wpdb->posts} WHERE ID IN ($id_in)" );
				foreach ( $results as $result ) {
					$cache[ $result->ID ] = $result->post_title;
				}
			}

			printf( '<script type="text/javascript">window.VX_Post_Select_Cache = %s</script>', wp_json_encode( (object) $cache ) );
		}
	}
}
