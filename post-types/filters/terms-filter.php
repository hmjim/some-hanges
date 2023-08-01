<?php

namespace Voxel\Post_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Terms_Filter extends Base_Filter {

	protected $props = [
		'type' => 'terms',
		'label' => 'Terms',
		'placeholder' => '',
		'source' => '',
		'orderby' => 'default',
		'multiple' => true,
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'placeholder' => $this->get_placeholder_model(),
			'key' => $this->get_key_model(),
			'icon' => $this->get_icon_model(),
			'source' => $this->get_source_model( 'taxonomy' ),
			'orderby' => [
				'type' => \Voxel\Form_Models\Select_Model::class,
				'label' => 'Term order',
				'width' => '1/1',
				'choices' => [
					'default' => 'Default',
					'name' => 'Alphabetical',
				],
			],
			'multiple' => [
				'type' => \Voxel\Form_Models\Switcher_Model::class,
				'label' => 'Allow selection of multiple terms?',
				'width' => '1/1',
			],
		];
	}

	public function query( \Voxel\Post_Types\Index_Query $query, array $args ): void {
		global $wpdb;

		$term_slugs = $this->parse_value( $args[ $this->get_key() ] ?? null );
		if ( $term_slugs === null ) {
			return;
		}

		$taxonomy = \Voxel\Taxonomy::get( $this->_get_taxonomy() );
		if ( ! $taxonomy ) {
			return;
		}

		$_term_slugs = array_map( function( $term_slug ) {
			return '\''.esc_sql( sanitize_title( $term_slug ) ).'\'';
		}, $term_slugs );

		$_joined_terms = join( ',', $_term_slugs );
		$term_taxonomy_ids = $wpdb->get_col( $wpdb->prepare( <<<SQL
			SELECT tt.term_taxonomy_id FROM {$wpdb->terms} AS t
				LEFT JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id
				WHERE tt.taxonomy = %s AND t.slug IN ({$_joined_terms})
		SQL, $taxonomy->get_key() ) );

		$term_taxonomy_ids = array_filter( array_map( 'absint', $term_taxonomy_ids ) );
		$ids = array_unique( $term_taxonomy_ids );

		if ( empty( $ids ) ) {
			return;
		}

		$join_key = esc_sql( $this->db_key() );
		$ids_string = join( ',', $ids );

		$query->join( <<<SQL
			LEFT JOIN {$wpdb->term_relationships} AS `{$join_key}_tr`
				ON ( `{$query->table->get_escaped_name()}`.post_id = `{$join_key}_tr`.object_id )
		SQL );

		$query->where( "`{$join_key}_tr`.term_taxonomy_id IN ({$ids_string})" );
	}

	protected function _get_taxonomy() {
		$field = $this->post_type->get_field( $this->props['source'] );
		return $field ? $field->get_prop('taxonomy') : '';
	}

	public function frontend_props() {
		$taxonomy = \Voxel\Taxonomy::get( $this->_get_taxonomy() );
		if ( ! $taxonomy ) {
			return [
				'terms' => [],
			];
		}

		$args = [
			'orderby' => $this->get_prop( 'orderby' ) === 'name' ? 'name' : 'default',
		];

		$transient_key = sprintf( 'filter:%s.%s', $this->post_type->get_key(), $this->get_key() );
		$t = get_transient( $transient_key );

		$terms = ( is_array( $t ) && isset( $t['terms'] ) ) ? $t['terms'] : [];
		$time = ( is_array( $t ) && isset( $t['time'] ) ) ? $t['time'] : 0;
		$hash = ( is_array( $t ) && isset( $t['hash'] ) ) ? $t['hash'] : false;
		$new_hash = md5( wp_json_encode( $args ) );

		if ( ! $t || ( $time < $taxonomy->get_version() ) || $hash !== $new_hash ) {
			$terms = \Voxel\get_terms( $this->_get_taxonomy(), $args );
			set_transient( $transient_key, [
				'terms' => $terms,
				'time' => time(),
				'hash' => $new_hash,
			], 14 * DAY_IN_SECONDS );
			// dump('from query');
		} else {
			// dump('from cache');
		}

		return [
			'terms' => $terms,
			// 'default_icon' => \Voxel\get_icon_markup( 'la-regular:lar la-bookmark' ),
			'selected' => $this->_get_selected_terms() ?: ((object) []),
			'taxonomy' => [
				'label' => $taxonomy->get_label(),
			],
			'placeholder' => $this->props['placeholder'] ?: $this->props['label'],
			'multiple' => (bool) $this->props['multiple'],
			'display_as' => $this->elementor_config['display_as'] ?? 'popup',
		];
	}

	protected function _get_selected_terms() {
		if ( array_key_exists( 'selected_terms', $this->cache ) ) {
			return $this->cache['selected_terms'];
		}

		$value = $this->parse_value( $this->get_value() ) ?: [];
		if ( empty( $value ) ) {
			$this->cache['selected_terms'] = null;
			return $this->cache['selected_terms'];
		}

		$terms = \Voxel\get_terms( $this->_get_taxonomy(), [
			'orderby' => 'name',
			'slug__in' => $value,
		] );

		$selected = [];
		foreach ( $terms as $term ) {
			$selected[ $term['slug'] ] = $term;
		}

		$this->cache['selected_terms'] = ! empty( $selected ) ? $selected : null;
		return $this->cache['selected_terms'];
	}

	public function parse_value( $value ) {
		if ( ! is_string( $value ) || empty( $value ) ) {
			return null;
		}

		$terms = explode( ',', trim( $value ) );
		$terms = array_filter( array_map( 'sanitize_title', $terms ) );

		if ( ! $this->props['multiple'] ) {
			$terms = isset( $terms[0] ) ? [ $terms[0] ] : [];
		}

		return ! empty( $terms ) ? $terms : null;
	}

	public function get_elementor_controls(): array {
		return [
			'value' => [
				'label' => _x( 'Default value', 'terms filter', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => 'Enter a comma-delimited list of terms to be selected by default',
			],
			'display_as' => [
				'label' => _x( 'Display as', 'terms_filter', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'popup' => _x( 'Popup', 'terms_filter', 'voxel-backend' ),
					'inline' => _x( 'Inline', 'terms_filter', 'voxel-backend' ),
				],
				'conditional' => false,
			],
			'inl_term_columns' => [
				'label' => __( 'Additional settings', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'conditional' => false,
			],
			'inl_custom_menu_cols' => [
				'full_key' => $this->get_key().'__inl_custom_menu_cols',
				'label' => __( 'Multi column list', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'your-plugin' ),
				'label_off' => __( 'Hide', 'your-plugin' ),
				'return_value' => 'yes',
				'conditional' => false,
			],
			'inl_set_menu_cols' => [
				'label' => __( 'Menu columns', 'voxel-backend' ),
				'description' => __( 'We recommend increasing popup min width before if you plan to display the menu in multiple columns', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 6,
				'step' => 1,
				'default' => 1,
				'selectors' => [
					'{{CURRENT_ITEM}} .ts-term-dropdown-list' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr)); display: grid;',
				],
				'condition' => [ $this->get_key().'__inl_custom_menu_cols' => 'yes' ],
				'conditional' => false,
				'responsive' => true,
			],
			'inl_cols_rgap' => [
				'label' => __( 'Row gap', 'voxel-backend' ),
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
					'{{CURRENT_ITEM}} .ts-term-dropdown-list' => 'row-gap: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [ $this->get_key().'__inl_custom_menu_cols' => 'yes' ],
				'conditional' => false,
				'responsive' => true,
			],
			'inl_cols_cgap' => [
				'label' => __( 'Column gap', 'voxel-backend' ),
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
					'{{CURRENT_ITEM}} .ts-term-dropdown-list' => 'column-gap: {{SIZE}}{{UNIT}}!important;',
				],
				'condition' => [ $this->get_key().'__inl_custom_menu_cols' => 'yes' ],
				'conditional' => false,
				'responsive' => true,
			],


		];
	}
}
