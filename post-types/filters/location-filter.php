<?php

namespace Voxel\Post_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Location_Filter extends Base_Filter {

	protected $props = [
		'type' => 'location',
		'label' => 'Location',
		'placeholder' => '',
		'source' => 'location',
		'radius_default' => 10,
		'radius_min' => 0,
		'radius_max' => 100,
		'radius_step' => 1,
		'radius_units' => 'km',
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'placeholder' => $this->get_placeholder_model(),
			'key' => $this->get_key_model(),
			'icon' => $this->get_icon_model(),
			'source' => $this->get_source_model( 'location' ),
			'radius_settings' => [
				'type' => \Voxel\Form_Models\Info_Model::class,
				'label' => 'Radius search',
			],
			'radius_default' => [
				'type' => \Voxel\Form_Models\Number_Model::class,
				'label' => 'Default value',
				'width' => '1/2',
				'step' => 'any',
			],
			'radius_units' => [
				'type' => \Voxel\Form_Models\Select_Model::class,
				'label' => 'Units',
				'width' => '1/2',
				'choices' => [
					'km' => 'Kilometers',
					'mi' => 'Miles',
				],
			],
			'radius_min' => [
				'type' => \Voxel\Form_Models\Number_Model::class,
				'label' => 'Min',
				'width' => '1/3',
				'step' => 'any',
			],
			'radius_max' => [
				'type' => \Voxel\Form_Models\Number_Model::class,
				'label' => 'Max',
				'width' => '1/3',
				'step' => 'any',
			],
			'radius_step' => [
				'type' => \Voxel\Form_Models\Number_Model::class,
				'label' => 'Step size',
				'width' => '1/3',
				'step' => 'any',
			],
		];
	}

	public function setup( \Voxel\Post_Types\Index_Table $table ): void {
		// mariadb doesn't support srid attribute, while mysql requires it to use index
		$srid = ! \Voxel\is_using_mariadb() ? 'SRID 4326' : '';
		$table->add_column( sprintf( '`%s` POINT NOT NULL %s', esc_sql( $this->db_key() ), $srid ) );
		$table->add_key( sprintf( 'SPATIAL KEY(`%s`)', esc_sql( $this->db_key() ) ) );
	}

	public function index( \Voxel\Post $post ): array {
		$field = $post->get_field( $this->props['source'] );
		if ( ! ( $field && $field->get_type() === 'location' ) ) {
			$lat = 0;
			$lng = 0;
		} else {
			$value = $field->get_value();
			$lat = $value['latitude'] ?? 0;
			$lng = $value['longitude'] ?? 0;
			if ( ! ( is_numeric( $lat ) && is_numeric( $lng ) ) ) {
				$lat = 0;
				$lng = 0;
			}
		}

		if ( \Voxel\is_using_mariadb() ) {
			return [
				$this->db_key() => sprintf( 'ST_PointFromText( \'POINT(%s %s)\', 4326 )', $lng, $lat ),
			];
		} else {
			return [
				$this->db_key() => sprintf( 'ST_PointFromText( \'POINT(%s %s)\', 4326 )', $lat, $lng ),
			];
		}
	}

	public function query( \Voxel\Post_Types\Index_Query $query, array $args ): void {
		$value = $this->parse_value( $args[ $this->get_key() ] ?? null );
		if ( $value === null ) {
			return;
		}

		if ( $value['method'] === 'radius' ) {
			$this->query_radius( $query, $args, $value );
		} else {
			$this->query_area( $query, $args, $value );
		}
	}

	public function query_area( $query, $args, $value ) {
		if ( \Voxel\is_using_mariadb() ) {
			// handle idl meridian
			if ( $value['nelng'] < $value['swlng'] ) {
				$polygon = sprintf(
					'MULTIPOLYGON(((%s %s,%s %s,%s %s,%s %s,%s %s)),((%s %s,%s %s,%s %s,%s %s,%s %s)))',
					// first polygon
					$value['swlng'], $value['swlat'],
					180, $value['swlat'],
					180, $value['nelat'],
					$value['swlng'], $value['nelat'],
					$value['swlng'], $value['swlat'],

					// second polygon
					-180, $value['swlat'],
					$value['nelng'], $value['swlat'],
					$value['nelng'], $value['nelat'],
					-180, $value['nelat'],
					-180, $value['swlat'],
				);
			} else {
				$polygon = sprintf(
					'POLYGON((%s %s,%s %s,%s %s,%s %s,%s %s))',
					$value['swlng'], $value['swlat'],
					$value['nelng'], $value['swlat'],
					$value['nelng'], $value['nelat'],
					$value['swlng'], $value['nelat'],
					$value['swlng'], $value['swlat'],
				);
			}
		} else {
			// handle idl meridian
			if ( $value['nelng'] < $value['swlng'] ) {
				$polygon = sprintf(
					'MULTIPOLYGON(((%s %s,%s %s,%s %s,%s %s,%s %s)),((%s %s,%s %s,%s %s,%s %s,%s %s)))',
					// first polygon
					$value['swlat'], $value['swlng'],
					$value['swlat'], 180,
					$value['nelat'], 180,
					$value['nelat'], $value['swlng'],
					$value['swlat'], $value['swlng'],

					// second polygon
					$value['swlat'], -180,
					$value['swlat'], $value['nelng'],
					$value['nelat'], $value['nelng'],
					$value['nelat'], -180,
					$value['swlat'], -180,
				);
			} else {
				$polygon = sprintf(
					'POLYGON((%s %s,%s %s,%s %s,%s %s,%s %s))',
					$value['swlat'], $value['swlng'],
					$value['swlat'], $value['nelng'],
					$value['nelat'], $value['nelng'],
					$value['nelat'], $value['swlng'],
					$value['swlat'], $value['swlng'],
				);
			}
		}

		$query->where( sprintf(
			'ST_Contains( ST_GeomFromText( \'%s\', 4326 ), `%s` )',
			esc_sql( $polygon ),
			esc_sql( $this->db_key() )
		) );
	}

	public function query_radius( $query, $args, $value ) {
		$point = sprintf( 'POINT(%s %s)', $value['lat'], $value['lng'] );

		$radius = floatval( $value['radius'] );

		// convert miles to km
		if ( $this->props['radius_units'] === 'mi' ) {
			$radius *= 1.609344;
		}

		// convert km to meters
		$radius = absint( $radius * 1000 );

		if ( \Voxel\is_using_mariadb() ) {
			$buffer = \Voxel\st_buffer( $value['lat'], $value['lng'], $radius, 32 );
			$query->where( sprintf(
				'ST_Contains( ST_GeomFromText( \'%s\', 4326 ), `%s` )',
				esc_sql( $buffer['polygon_mariadb'] ),
				esc_sql( $this->db_key() )
			) );
		} else {
			$query->where( sprintf(
				'ST_Contains( ST_Buffer( ST_GeomFromText( \'%s\', 4326 ), %d ), `%s` )',
				esc_sql( $point ),
				$radius,
				esc_sql( $this->db_key() )
			) );
		}
	}

	public function orderby_distance( \Voxel\Post_Types\Index_Query $query, array $coordinates ): void {
		$lat = $coordinates[0];
		$lng = $coordinates[1];
		if ( $lat === null || $lng === null ) {
			return;
		}

		$lat = floatval( $lat );
		$lng = floatval( $lng );
		if ( $lat > 90 || $lat < -90 || $lng > 180 || $lng < -180 ) {
			return;
		}

		$orderby_key = $this->db_key().'_distance';

		if ( \Voxel\is_using_mariadb() ) {
			$query->select( sprintf(
				'ST_Distance_Sphere( ST_GeomFromText( \'%s\' ), `%s` ) AS `%s`',
				sprintf( 'POINT(%s %s)', $lng, $lat ),
				esc_sql( $this->db_key() ),
				esc_sql( $orderby_key )
			) );
		} else {
			$query->select( sprintf(
				'ST_Distance_Sphere( ST_GeomFromText( \'%s\' ), ST_SRID( `%s`, 0 ) ) AS `%s`',
				sprintf( 'POINT(%s %s)', $lng, $lat ),
				esc_sql( $this->db_key() ),
				esc_sql( $orderby_key )
			) );
		}

		$query->orderby( sprintf( '`%s` ASC', esc_sql( $orderby_key ) ) );

		$GLOBALS['_voxel_nearby_ref'] = compact( 'lat', 'lng' );
	}

	public function parse_value( $value ) {
		preg_match( '/(?P<address>.*);(?P<swlat>.*),(?P<swlng>.*)\.\.(?P<nelat>.*),(?P<nelng>.*)/i', $value ?? '', $matches );

		if ( ! isset( $matches['address'], $matches['swlat'], $matches['swlng'], $matches['nelat'], $matches['nelng'] ) ) {
			return $this->parse_value_alternate( $value );
		}

		$address = $matches['address'] ?? '';
		$swlat = floatval( $matches['swlat'] );
		$swlng = floatval( $matches['swlng'] );
		$nelat = floatval( $matches['nelat'] );
		$nelng = floatval( $matches['nelng'] );

		if ( ( $swlat > 90 || $swlat < -90 ) || ( $nelat > 90 || $nelat < -90 ) ) {
			return null;
		}

		if ( ( $swlng > 180 || $swlng < -180 ) || ( $nelng > 180 || $nelng < -180 ) ) {
			return null;
		}

		return [
			'method' => 'area',
			'address' => $address,
			'swlat' => $swlat,
			'swlng' => $swlng,
			'nelat' => $nelat,
			'nelng' => $nelng,
		];
	}

	public function parse_value_alternate( $value ) {
		preg_match( '/(?P<address>.*);(?P<lat>.*),(?P<lng>.*),(?P<radius>.*)/i', $value ?? '', $matches );

		if ( ! isset( $matches['address'], $matches['lat'], $matches['lng'], $matches['radius'] ) ) {
			return null;
		}

		$address = $matches['address'] ?? '';
		$lat = floatval( $matches['lat'] );
		$lng = floatval( $matches['lng'] );
		$radius = abs( floatval( $matches['radius'] ) );
		$radius = \Voxel\clamp( $radius, 0, $this->props['radius_units'] === 'mi' ? 2000 : 3200 );

		if ( ( $lat > 90 || $lat < -90 ) || ( $lng > 180 || $lng < -180 ) || $radius === 0 ) {
			return null;
		}

		return [
			'method' => 'radius',
			'address' => $address,
			'lat' => $lat,
			'lng' => $lng,
			'radius' => $radius,
		];
	}

	public function frontend_props() {
		if ( ! is_admin() ) {
			\Voxel\enqueue_maps();
			wp_print_styles( 'nouislider' );
			wp_enqueue_script( 'nouislider' );
		}

		$value = $this->parse_value( $this->get_value() );
		return [
			'value' => [
				'method' => $value['method'] ?? 'area',
				'address' => $value['address'] ?? '',
				'swlat' => $value['swlat'] ?? null,
				'swlng' => $value['swlng'] ?? null,
				'nelat' => $value['nelat'] ?? null,
				'nelng' => $value['nelng'] ?? null,

				'lat' => $value['lat'] ?? null,
				'lng' => $value['lng'] ?? null,
				'radius' => $value['radius'] ?? null,
			],
			'radius' => [
				'default' => $this->props['radius_default'],
				'min' => $this->props['radius_min'],
				'max' => $this->props['radius_max'],
				'units' => $this->props['radius_units'],
				'step' => $this->props['radius_step'],
			],
			'placeholder' => $this->props['placeholder'] ?: $this->props['label'],
			'display_as' => $this->elementor_config['display_as'] ?? 'popup',
			'display_proximity_as' => $this->elementor_config['display_proximity_as'] ?? 'popup',
			'l10n' => [
				'visibleArea' => _x( 'Visible map area', 'location filter', 'voxel' ),
			],
		];
	}

	public function get_elementor_controls(): array {
		return [
			'address' => [
				'label' => _x( 'Default address', 'date filter', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			],
			'method' => [
				'full_key' => $this->get_key().'__method',
				'label' => _x( 'Search method', 'date filter', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'area',
				'options' => [
					'radius' => 'Search by radius',
					'area' => 'Search by area',
				],
			],
			'box' => [
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'condition' => [ $this->get_key().'__method' => 'area' ],
				'raw' => <<<HTML
					<h3 class="elementor-control-title"><strong>Default search area</strong></h3>
					<p class="elementor-control-field-description">
						Enter coordinates for the southwest and northeast points of the default area to be searched.
					</p>
				HTML,
			],
			'southwest' => [
				'label' => _x( 'Southwest ', 'date filter', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'condition' => [ $this->get_key().'__method' => 'area' ],
			],
			'swlat' => [
				'label' => _x( 'Latitude', 'date filter', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => -90,
				'max' => 90,
				'condition' => [ $this->get_key().'__method' => 'area' ],
			],
			'swlng' => [
				'label' => _x( 'Longitude', 'date filter', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => -180,
				'max' => 180,
				'condition' => [ $this->get_key().'__method' => 'area' ],
			],
			'northeast' => [
				'label' => _x( 'Northeast', 'date filter', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'condition' => [ $this->get_key().'__method' => 'area' ],
			],
			'nelat' => [
				'label' => _x( 'Latitude', 'date filter', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => -90,
				'max' => 90,
				'condition' => [ $this->get_key().'__method' => 'area' ],
			],
			'nelng' => [
				'label' => _x( 'Longitude', 'date filter', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => -180,
				'max' => 180,
				'condition' => [ $this->get_key().'__method' => 'area' ],
			],
			'box_alt' => [
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'condition' => [ $this->get_key().'__method' => 'radius' ],
				'raw' => <<<HTML
					<h3 class="elementor-control-title"><strong>Default location</strong></h3>
					<p class="elementor-control-field-description">
						Enter location coordinates and radius.
					</p>
				HTML,
			],
			'lat' => [
				'label' => _x( 'Latitude', 'date filter', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => -90,
				'max' => 90,
				'condition' => [ $this->get_key().'__method' => 'radius' ],
			],
			'lng' => [
				'label' => _x( 'Longitude', 'date filter', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => -180,
				'max' => 180,
				'condition' => [ $this->get_key().'__method' => 'radius' ],
			],
			'radius' => [
				'label' => _x( 'Radius', 'date filter', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 8000,
				'condition' => [ $this->get_key().'__method' => 'radius' ],
			],

			'display_as' => [
				'label' => _x( 'Display as', 'keywords_filter', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'popup' => _x( 'Popup', 'keywords_filter', 'voxel-backend' ),
					'inline' => _x( 'Inline', 'keywords_filter', 'voxel-backend' ),
				],
				'conditional' => false,
			],

			'display_proximity_as' => [
				'label' => _x( 'Display proximity as', 'keywords_filter', 'voxel-backend' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'popup' => _x( 'Popup', 'keywords_filter', 'voxel-backend' ),
					'inline' => _x( 'Inline', 'keywords_filter', 'voxel-backend' ),
					'none' => _x( 'None', 'keywords_filter', 'voxel-backend' ),
				],
				'conditional' => false,
				// 'condition' => [ $this->get_key().'__display_as' => 'inline' ],
			],
		];
	}

	public function get_default_value_from_elementor( $controls ) {
		$method = $controls['method'] ?? null;

		if ( $method === 'radius' ) {
			$address = $controls['address'] ?? null;
			$lat = $controls['lat'] ?? null;
			$lng = $controls['lng'] ?? null;
			$radius = $controls['radius'] ?? null;

			if ( ! ( $address && $lat !== null && $lng !== null && $radius !== null ) ) {
				return null;
			}

			return sprintf( '%s;%s,%s,%s', $address, $lat, $lng, $radius );
		} else {
			$address = $controls['address'] ?? null;
			$swlat = $controls['swlat'] ?? null;
			$swlng = $controls['swlng'] ?? null;
			$nelat = $controls['nelat'] ?? null;
			$nelng = $controls['nelng'] ?? null;
			if ( ! ( $address && $swlat && $swlng && $nelat && $nelng ) ) {
				return null;
			}

			return sprintf( '%s;%s,%s..%s,%s', $address, $swlat, $swlng, $nelat, $nelng );
		}
	}
}
