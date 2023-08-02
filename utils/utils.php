<?php

namespace Voxel;

if ( ! defined('ABSPATH') ) {
	exit;
}

spl_autoload_register( function( $classname ) {
	$parts = explode( '\\', $classname );
	if ( $parts[0] !== 'Voxel' ) {
		return;
	}

	$parts[0] = 'App';
	$path_parts = array_map( function( $part ) {
		return strtolower( str_replace( '_', '-', $part ) );
	}, $parts );

	$path = join( DIRECTORY_SEPARATOR, $path_parts ) . '.php';
	if ( locate_template( $path ) ) {
		require_once locate_template( $path );
	}
} );

require_once locate_template( 'app/utils/constants.php' );
require_once locate_template( 'app/utils/app-utils.php' );
require_once locate_template( 'app/utils/post-utils.php' );
require_once locate_template( 'app/utils/template-utils.php' );
require_once locate_template( 'app/utils/term-utils.php' );
require_once locate_template( 'app/utils/user-utils.php' );
require_once locate_template( 'app/utils/security-utils.php' );
require_once locate_template( 'app/utils/recurring-date-utils.php' );
require_once locate_template( 'app/utils/timeline-utils.php' );
require_once locate_template( 'app/utils/demo-import-utils.php' );
require_once locate_template( 'app/utils/dev-utils.php' );

function render( $string, $groups = null ) {
	return \Voxel\Dynamic_Tags\Dynamic_Tags::render( $string, $groups );
}

function classname_to_filename( $classname, $with_namespace = false ) {
	$parts = explode( '\\', $classname );
	return strtolower( str_replace( '_', '-', $with_namespace ? $classname : array_pop( $parts ) ) );
}

function filename_to_classname( $filename ) {
	return str_replace( ' ', '_', ucwords( str_replace( '-', ' ', $filename ) ) );
}

function get_assets_version() {
	static $version;
	if ( ! is_null( $version ) ) {
		return $version;
	}

	$version = \Voxel\is_dev_mode() ? microtime(true) : wp_get_theme( get_template() )->get('Version');
	return $version;
}

function is_elementor_active() {
	return class_exists( '\Elementor\Plugin' );
}

function is_elementor_pro_active() {
	return class_exists( '\ElementorPro\Plugin' );
}

function is_qm_active() {
	return class_exists( '\QueryMonitor' );
}

function is_edit_mode() {
	return \Voxel\is_elementor_active() && \Elementor\Plugin::$instance->editor->is_edit_mode();
}

function is_preview_mode() {
	return \Voxel\is_elementor_active() && \Elementor\Plugin::$instance->preview->is_preview_mode();
}

function is_elementor_ajax() {
	return ! empty( $_REQUEST['_nonce'] ) && wp_verify_nonce( $_REQUEST['_nonce'], 'elementor_ajax' );
}

function is_elementor_preview() {
	return isset( $_GET['elementor-preview'] );
}

function is_rendering_css() {
	return !! ( $GLOBALS['vx_rendering_css'] ?? null );
}

function is_importing_elementor_template() {
	static $is_importing;
	if ( is_null( $is_importing ) ) {
		$is_importing = ( $_REQUEST['action'] ?? null ) === 'elementor_library_direct_actions'
			&& ( $_REQUEST['library_action'] ?? null ) === 'direct_import_template'
			&& wp_verify_nonce( $_REQUEST['_nonce'] ?? null, 'elementor_ajax' );
	}

	return $is_importing;
}

function _is_using_mariadb() {
	global $wpdb;
	$db_version = $wpdb->get_results( "SHOW VARIABLES WHERE `Variable_name` IN ( 'version_comment', 'innodb_version' )", OBJECT_K );

	$str1 = strtolower( $db_version['version_comment']->Value ?? '' );
	$str2 = strtolower( $db_version['innodb_version']->Value ?? '' );
	$str3 = strtolower( $wpdb->get_var( 'SELECT VERSION()' ) );

	if ( str_contains( $str1, '8.0.' ) || str_contains( $str2, '8.0.' ) || str_contains( $str3, '8.0.' ) ) {
		return false;
	}

	return true;
}

function is_using_mariadb() {
	$db_type = \Voxel\get('settings.db.type');

	if ( $db_type === 'mysql' ) {
		return false;
	} elseif ( $db_type === 'mariadb' ) {
		return true;
	} else {
		$db_type = \Voxel\_is_using_mariadb() ? 'mariadb' : 'mysql';
		\Voxel\set( 'settings.db.type', $db_type );

		return $db_type === 'mariadb';
	}
}

function set_rendering_css( bool $is_rendering ) {
	$GLOBALS['vx_rendering_css'] = $is_rendering;
}

function get_image( $image ) {
	return trailingslashit( get_template_directory_uri() ).'assets/images/'.$image;
}

/**
 * Helper; Return "uploads/" full directory path.
 *
 * @since 1.0
 */
function uploads_dir( $path = '' ) {
	return trailingslashit( wp_upload_dir()['basedir'] ).$path;
}

/**
 * Delete given directory.
 *
 * @since 2.2.3
 */
function delete_directory( $target ) {
	if ( is_dir( $target ) ) {
		$files = glob( $target . '*', GLOB_MARK );
		foreach( $files as $file ) {
			delete_directory( $file );
		}

		@rmdir( $target );
	} elseif ( is_file( $target ) ) {
		@unlink( $target );
	}
}

function parse_icon_string( $string ) {
	$string = (string) $string;
	$library = substr( $string, 0, strpos( $string, ':') );
	$icon = substr( $string, strpos( $string, ':') + 1 );

	if ( $library === 'svg' && is_numeric( $icon ) ) {
		$icon = [
			'id' => absint( $icon ),
			'url' => wp_get_attachment_url( $icon ),
		];
	}

	return [
		'value' => $icon,
		'library' => $library,
	];
}

function get_icon_markup( $icon ) {
	if ( ! \Voxel\is_elementor_active() ) {
		return '';
	}

	if ( ! is_array( $icon ) ) {
		$icon = \Voxel\parse_icon_string( $icon );
	}

	\Elementor\Plugin::$instance->frontend->enqueue_font( $icon['library'] );

	ob_start();
	\Elementor\Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
	return ob_get_clean();
}

function render_icon( $icon ) {
	echo \Voxel\get_icon_markup( $icon );
}

function get_weekdays() {
	return [
		'mon' => _x( 'Monday', 'weekdays', 'voxel' ),
		'tue' => _x( 'Tuesday', 'weekdays', 'voxel' ),
		'wed' => _x( 'Wednesday', 'weekdays', 'voxel' ),
		'thu' => _x( 'Thursday', 'weekdays', 'voxel' ),
		'fri' => _x( 'Friday', 'weekdays', 'voxel' ),
		'sat' => _x( 'Saturday', 'weekdays', 'voxel' ),
		'sun' => _x( 'Sunday', 'weekdays', 'voxel' ),
	];
}

function get_weekday_indexes() {
	return [
		'mon' => 0,
		'tue' => 1,
		'wed' => 2,
		'thu' => 3,
		'fri' => 4,
		'sat' => 5,
		'sun' => 6,
	];
}

/**
 * Return all registered image sizes.
 *
 * @since 1.0
 */
function get_image_sizes() {
	global $_wp_additional_image_sizes;
	$sizes = [];

	foreach ( [ 'thumbnail', 'medium', 'medium_large', 'large' ] as $size ) {
		$sizes[ $size ] = [
			'width'  => intval( get_option( "{$size}_size_w" ) ),
			'height' => intval( get_option( "{$size}_size_h" ) ),
			'crop'   => get_option( "{$size}_crop" ) ? get_option( "{$size}_crop" ) : false,
		];
	}

	if ( ! empty( $_wp_additional_image_sizes ) ) {
		$sizes = array_merge( $sizes, $_wp_additional_image_sizes );
	}

	return $sizes;
}

function get_image_sizes_with_labels() {
	$sizes = [];
	foreach ( \Voxel\get_image_sizes() as $key => $size ) {
		$label = ucwords( str_replace( '_', ' ', $key ) );
		$sizes[ $key ] = sprintf( '%s (%sx%s)', $label, $size['width'], $size['height'] ?: '(auto)' );
	}

	$sizes['full'] = 'Full size';

	return $sizes;
}

/**
 * Check whether the current request is nearing on using maximum
 * execution time and memory.
 *
 * @since 1.0
 */
function nearing_resource_limits(): bool {
	// check if less than 5 seconds of execution time are left
	$max_execution_time = absint( ini_get('max_execution_time') );
	$time_limit = $max_execution_time === 0 ? 60 : min( 60, ( $max_execution_time - 5 ) );
	$time_nearing_limit = ( $time_limit - ( microtime(true) - WP_START_TIMESTAMP ) ) < 0;

	// check if more than 85% of memory has been used (75% if QueryMonitor is active)
	$max_memory_usage = class_exists( '\QueryMonitor' ) ? 0.75 : 0.85;
	$memory_limit = absint( wp_convert_hr_to_bytes( ini_get( 'memory_limit' ) ) * $max_memory_usage );
	$memory_nearing_limit = ( $memory_limit - memory_get_usage() ) < 0;

	return $time_nearing_limit || $memory_nearing_limit;
}

function utc() {
	static $datetime;
	if ( is_null( $datetime ) ) {
		$datetime = new \DateTimeImmutable( 'now', new \DateTimeZone( 'UTC' ) );
	}

	return $datetime;
}

function now() {
	static $datetime;
	if ( is_null( $datetime ) ) {
		$datetime = new \DateTimeImmutable( 'now', wp_timezone() );
	}

	return $datetime;
}

function epoch() {
	static $datetime;
	if ( is_null( $datetime ) ) {
		$datetime = new \DateTimeImmutable( '1970-01-01', new \DateTimeZone( 'UTC' ) );
	}

	return $datetime;
}

function currency_format( $price, $currency, $amount_is_in_cents = true ) {
	static $formatter;

	// convert amount from cents to main currency, unless it's a zero decimal currency
	if ( $amount_is_in_cents && ! \Voxel\Stripe\Currencies::is_zero_decimal( $currency ) ) {
		$price /= 100;
	}

	if ( ! class_exists( '\NumberFormatter' ) ) {
		return $currency.' '.$price;
	}

	if ( is_null( $formatter ) ) {
		$formatter = new \NumberFormatter( get_locale(), \NumberFormatter::CURRENCY );
	}

	if ( intval( $price ) == $price ) {
	  $formatter->setAttribute( \NumberFormatter::MIN_FRACTION_DIGITS, 0 );
	}

	return $formatter->formatCurrency( $price, $currency );
}

function interval_format( $interval, $interval_count ) {
	$count = absint( $interval_count );

	if ( $interval === 'month' && $count === 12 ) {
		$interval = 'year';
		$count = 1;
	}

	if ( $interval === 'day' ) {
		return $count === 1
			? _x( 'daily', 'price interval', 'voxel' )
			: sprintf( _x( 'every %s days', 'price interval', 'voxel' ), number_format_i18n( $count ) );
	} elseif ( $interval === 'week' ) {
		return $count === 1
			? _x( 'weekly', 'price interval', 'voxel' )
			: sprintf( _x( 'every %s weeks', 'price interval', 'voxel' ), number_format_i18n( $count ) );
	} elseif ( $interval === 'month' ) {
		return $count === 1
			? _x( 'monthly', 'price interval', 'voxel' )
			: sprintf( _x( 'every %s months', 'price interval', 'voxel' ), number_format_i18n( $count ) );
	} elseif ( $interval === 'year' ) {
		return $count === 1
			? _x( 'yearly', 'price interval', 'voxel' )
			: sprintf( _x( 'every %s years', 'price interval', 'voxel' ), number_format_i18n( $count ) );
	}
}

function random_string( int $length ) {
	$pool = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$max = strlen( $pool ) - 1;

	$token = '';
	for ( $i = 0; $i < $length; $i++ ) {
		$random_key = random_int( 0, $max );
		$token .= $pool[ $random_key ];
	}

	return $token;
}

function get_google_auth_link() {
	return add_query_arg( [
		'response_type' => 'code',
		'client_id' => \Voxel\get( 'settings.auth.google.client_id' ),
		'redirect_uri' => rawurlencode( home_url('/?vx=1&action=auth.google.login') ),
		'scope' => 'openid email',
		'state' => sprintf( '%s..%s', wp_create_nonce( 'vx_auth_google' ), rawurlencode( \Voxel\get_redirect_url() ) ),
	], 'https://accounts.google.com/o/oauth2/v2/auth' );
}

function get_redirect_url() {
	if ( ! empty( $_REQUEST['redirect_to'] ) ) {
		return wp_validate_redirect( $_REQUEST['redirect_to'], home_url('/') );
	} elseif ( $referrer = wp_get_referer() ) {
		return $referrer;
	} else {
		return home_url('/');
	}
}

function get_auth_url() {
	return get_permalink( \Voxel\get( 'templates.auth' ) ) ?: home_url('/');
}

function get_logout_url() {
	return add_query_arg( [
		'vx' => 1,
		'action' => 'auth.logout',
		'_wpnonce' => wp_create_nonce( 'vx_auth_logout' ),
	], home_url( '/' ) );
}

function date_format( $timestamp ) {
	if ( $timestamp instanceof \DateTime || $timestamp instanceof \DateTimeImmutable ) {
		$timestamp = $timestamp->getTimestamp() + $timestamp->getOffset();
	}

	return date_i18n( get_option( 'date_format' ), $timestamp );
}

function time_format( $timestamp ) {
	if ( $timestamp instanceof \DateTime || $timestamp instanceof \DateTimeImmutable ) {
		$timestamp = $timestamp->getTimestamp() + $timestamp->getOffset();
	}

	return date_i18n( get_option( 'time_format' ), $timestamp );
}

function datetime_format( $timestamp ) {
	if ( $timestamp instanceof \DateTime || $timestamp instanceof \DateTimeImmutable ) {
		$timestamp = $timestamp->getTimestamp() + $timestamp->getOffset();
	}

	return date_i18n( get_option( 'date_format' ).' '.get_option( 'time_format' ), $timestamp );
}

function get_minute_of_week( $datetime ) {
	$day_index = absint( $datetime->format('N') ) - 1;
	$day_start = ( $day_index * 1440 ) + 1440;
	return $day_start + ( absint( $datetime->format('H') ) * 60 ) + absint( $datetime->format('i') );
}

/**
 * Merge overlapping integer ranges.
 *
 * @param Array<Array{0: int, 1: int}> $ranges
 */
function merge_ranges( array $ranges ): array {
	usort( $ranges, function( $a, $b ) {
		return $a[0] - $b[0];
	} );

	$n = 0;
	$len = count( $ranges );
	for ( $i = 1; $i < $len; ++$i ) {
		if ( $ranges[$i][0] > $ranges[$n][1] + 1 ) {
			$n = $i;
		} else {
			if ( $ranges[$n][1] < $ranges[$i][1] ) {
				$ranges[$n][1] = $ranges[$i][1];
			}

			unset( $ranges[$i] );
		}
	}

	return array_values($ranges);
}

function clamp( $number, $min, $max ) {
	return max( $min, min( $max, $number ) );
}

function from_list( $value, array $list, $default = null ) {
	return in_array( $value, $list, true ) ? $value : $default;
}

function evaluate_visibility_rules( $rules ): bool {
	$rule_list = \Voxel\config('dynamic_tags.visibility_rules');
	foreach ( $rules as $rule_group ) {
		$has_valid_rules = false;

		// all rules in a group must be true for the rule group to pass
		foreach ( $rule_group as $rule_config ) {
			if ( ! isset( $rule_list[ $rule_config['type'] ?? null ] ) ) {
				continue;
			}

			$has_valid_rules = true;
			$rule = new $rule_list[ $rule_config['type'] ]( $rule_config );
			if ( $rule->evaluate() === false ) {
				continue(2);
			}
		}

		// make sure group contains at least one valid rule
		if ( ! $has_valid_rules ) {
			continue;
		}

		// if a single rule group has passed conditions, no more evaluation is necessary
		return true;
	}

	return false;
}

function email_queue() {
	return \Voxel\Queues\Email_Queue::instance();
}

function email_template( $message ) {
	ob_start();
	require locate_template( 'templates/emails/default-template.php' );
	$template = ob_get_clean();

	// inline styles
	try {
		$emogrifier = new \Voxel\Utils\Vendor\Emogrifier( $template );
		$rendered_template = $emogrifier->emogrify();
	} catch ( \Exception $e ) {
		// if inline styles can't be applied, use the original markup as the message body
		$rendered_template = $template;
	}

	return $rendered_template;
}

function get_accent_color() {
	$default = '#A239FF';
	if ( ! \Voxel\is_elementor_active() ) {
		return $default;
	}

	$kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit_for_frontend();
	if ( ! $kit ) {
		return $default;
	}

	$colors = $kit->get_settings('system_colors');
	if ( ! is_array( $colors ) ) {
		return $default;
	}

	return $colors[3]['color'] ?? $default;
}

function get_default_from_email() {
	$sitename = wp_parse_url( network_home_url(), PHP_URL_HOST );
	$from_email = 'wordpress@';

	if ( null !== $sitename ) {
		if ( 'www.' === substr( $sitename, 0, 4 ) ) {
			$sitename = substr( $sitename, 4 );
		}

		$from_email .= $sitename;
	}

	return $from_email;
}

function get_default_email_footer_text() {
	return <<<HTML
	<strong>@site(title)</strong><br>
	@site(tagline)
	HTML;
}

function get_email_footer_text() {
	$custom_text = \Voxel\get( 'settings.emails.footer_text' );
	if ( ! empty( $custom_text ) ) {
		return \Voxel\render( $custom_text );
	}

	return \Voxel\render( \Voxel\get_default_email_footer_text() );
}

function qrcode( $text ) {
	$qr = \Voxel\Utils\Vendor\QRCode::getMinimumQRCode( $text, QR_ERROR_CORRECT_LEVEL_L );
	$image = $qr->createImage(8, 16);

	ob_start();
	imagepng( $image );
	$contents = ob_get_clean();
	imagedestroy( $image );

	return 'data:image/png;base64,'.base64_encode( $contents );
}

function svg( $filename, $output = true ) {
	$filename = str_ends_with( $filename, '.svg' ) ? $filename : $filename.'.svg';
	$svg = file_get_contents( locate_template( 'assets/images/svgs/'.$filename ) );
	if ( $output ) {
		echo $svg;
	} else {
		return $svg;
	}
}

function get_svg( $filename ) {
	return \Voxel\svg( $filename, false );
}

// haversine great-circle distance
// @link https://stackoverflow.com/a/10054282/3522553
function st_distance_sphere( $latFrom, $lngFrom, $latTo, $lngTo, $earthRadius = 6371000 ) {
	$latFrom = deg2rad( $latFrom );
	$lngFrom = deg2rad( $lngFrom );
	$latTo = deg2rad( $latTo );
	$lngTo = deg2rad( $lngTo );

	$latDelta = $latTo - $latFrom;
	$lngDelta = $lngTo - $lngFrom;

	$angle = 2 * asin( sqrt( pow( sin( $latDelta / 2 ), 2 ) +
		cos( $latFrom ) * cos( $latTo ) * pow( sin( $lngDelta / 2 ), 2 ) ) );

	return $angle * $earthRadius;
}

// @link https://stackoverflow.com/questions/55936249/get-circle-polygon-circumference-points-latitude-and-longitude
function st_buffer( $center_lat, $center_lng, $radius, $segments = 32 ) {
	$coordinates = [];
    $center_lat_rad = deg2rad( $center_lat );
    $center_lng_rad = deg2rad( $center_lng );

	for ( $i = 0; $i < $segments; $i++ ) {
		$bearing = 2 * M_PI * $i / $segments;
		$angular_distance = $radius / 6371000;

		$lat = asin(
			sin( $center_lat_rad ) * cos( $angular_distance ) +
			cos( $center_lat_rad ) * sin( $angular_distance ) * cos( $bearing )
		);

		$lng = $center_lng_rad + atan2(
			sin($bearing) * sin($angular_distance) * cos($center_lat_rad),
			cos($angular_distance) - sin($center_lat_rad) * sin($lat)
		);

		$lng = fmod( $lng + 3 * M_PI, 2 * M_PI ) - M_PI;
		$coordinates[] = [ rad2deg( $lng ), rad2deg( $lat ) ];
	}

    $coordinates[] = $coordinates[0];

	return [
		'coordinates' => $coordinates,
		'polygon' => sprintf( 'POLYGON((%s))', join( ',', array_map( function( $point ) {
			return $point[1].' '.$point[0];
		}, $coordinates ) ) ),
		'polygon_mariadb' => sprintf( 'POLYGON((%s))', join( ',', array_map( function( $point ) {
			return $point[0].' '.$point[1];
		}, $coordinates ) ) ),
	];
}

function prime_relations_cache( $post_ids, $field ) {
	global $wpdb;

	$relation_key = esc_sql( $field->get_relation_key() );
	$column_key = in_array( $field->get_prop('relation_type'), [ 'has_one', 'has_many' ], true ) ? 'parent_id' : 'child_id';
	$select_key = $column_key === 'child_id' ? 'parent_id' : 'child_id';

	foreach ( $post_ids as $index => $post_id ) {
		$cache_key = sprintf( 'relations:%s:%d:%s', $field->get_relation_key(), $post_id, $select_key );

		if ( wp_cache_get( $cache_key, 'voxel' ) ) {
			unset( $post_ids[ $index ] );
		}
	}

	if ( empty( $post_ids ) ) {
		return [];
	}

	$query_ids = join( ',', array_map( 'absint', $post_ids ) );

	$results = $wpdb->get_results( <<<SQL
		SELECT {$column_key} AS `post_id`, GROUP_CONCAT( {$select_key} SEPARATOR ',') AS `list`
		FROM {$wpdb->prefix}voxel_relations
		WHERE relation_key = '{$relation_key}'
		GROUP BY {$column_key}
	SQL, OBJECT_K );

	$total_ids = [];

	foreach ( $post_ids as $post_id ) {
		$cache_key = sprintf( 'relations:%s:%d:%s', $field->get_relation_key(), $post_id, $select_key );
		$ids = [];

		if ( ! empty( $results[ $post_id ]->list ?? '' ) ) {
			$ids = array_map( 'absint', explode( ',', $results[ $post_id ]->list ?? '' ) );
			$total_ids = array_merge( $total_ids, $ids );
		}

		wp_cache_set( $cache_key, $ids, 'voxel' );
	}

	$total_ids = array_unique( $total_ids );

	return $total_ids;
}

function get_range_presets( $key = null ) {
	static $ranges;

	if ( $ranges === null ) {
		$ranges = [
			'all' => [
				'key' => 'all',
				'label' => _x( 'All', 'range presets', 'voxel' ),
				'callback' => function( $now ) {
					return [
						'start' => '1000-01-01',
						'end' => '9999-12-31',
					];
				},
			],
			'upcoming' => [
				'key' => 'upcoming',
				'label' => _x( 'Upcoming', 'range presets', 'voxel' ),
				'callback' => function( $now ) {
					return [
						'start' => $now->format( 'Y-m-d H:i:s' ),
						'end' => '9999-12-31 23:59:59',
					];
				},
			],
			'today' => [
				'key' => 'today',
				'label' => _x( 'Today', 'range presets', 'voxel' ),
				'callback' => function( $now ) {
					return [
						'start' => $now->format( 'Y-m-d H:i:s' ),
						'end' => $now->format( 'Y-m-d 23:59:59' ),
					];
				},
			],
			'tomorrow' => [
				'key' => 'tomorrow',
				'label' => _x( 'Tomorrow', 'range presets', 'voxel' ),
				'callback' => function( $now ) {
					return [
						'start' => $now->modify( '+1 day' )->format( 'Y-m-d 00:00:00' ),
						'end' => $now->modify( '+1 day' )->format( 'Y-m-d 23:59:59' ),
					];
				},
			],
			'this-weekend' => [
				'key' => 'this-weekend',
				'label' => _x( 'This weekend', 'range presets', 'voxel' ),
				'callback' => function( $now ) {
					$is_weekend = $now->getTimestamp() > $now->modify('this saturday')->getTimestamp();
					return [
						'start' => $is_weekend ? $now->format( 'Y-m-d H:i:s' ) : $now->modify('this saturday')->format( 'Y-m-d 00:00:00' ),
						'end' => $now->modify('this sunday')->format( 'Y-m-d 23:59:59' ),
					];
				},
			],
			'this-week' => [
				'key' => 'this-week',
				'label' => _x( 'This week', 'range presets', 'voxel' ),
				'callback' => function( $now ) {
					$start_of_week = (int) get_option( 'start_of_week' );
					$weekdays = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ];

					return [
						'start' => $now->format( 'Y-m-d H:i:s' ),
						'end' => $now->modify( 'next '.$weekdays[ $start_of_week ] )->modify( '-1 day' )->format( 'Y-m-d 23:59:59' ),
					];
				},
			],
			'next-week' => [
				'key' => 'next-week',
				'label' => _x( 'Next week', 'range presets', 'voxel' ),
				'callback' => function( $now ) {
					$start_of_week = (int) get_option( 'start_of_week' );
					$weekdays = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ];
					return [
						'start' => $now->modify( 'next '.$weekdays[ $start_of_week ] )->format( 'Y-m-d 00:00:00' ),
						'end' => $now->modify( 'next '.$weekdays[ $start_of_week ] )->modify('+6 days')->format( 'Y-m-d 23:59:59' ),
					];
				},
			],
			'next-7-days' => [
				'key' => 'next-7-days',
				'label' => _x( 'Next 7 days', 'range presets', 'voxel' ),
				'callback' => function( $now ) {
					return [
						'start' => $now->format( 'Y-m-d H:i:s' ),
						'end' => $now->modify( '+6 days' )->format( 'Y-m-d 23:59:59' ),
					];
				},
			],
			'this-month' => [
				'key' => 'this-month',
				'label' => _x( 'This month', 'range presets', 'voxel' ),
				'callback' => function( $now ) {
					return [
						'start' => $now->format( 'Y-m-d H:i:s' ),
						'end' => $now->format( 'Y-m-t 23:59:59' ),
					];
				},
			],
			'next-month' => [
				'key' => 'next-month',
				'label' => _x( 'Next month', 'range presets', 'voxel' ),
				'callback' => function( $now ) {
					return [
						'start' => $now->modify('first day of next month')->format( 'Y-m-d 00:00:00' ),
						'end' => $now->modify('first day of next month')->format( 'Y-m-t 23:59:59' ),
					];
				},
			],
			'next-30-days' => [
				'key' => 'next-30-days',
				'label' => _x( 'Next 30 days', 'range presets', 'voxel' ),
				'callback' => function( $now ) {
					return [
						'start' => $now->format( 'Y-m-d H:i:s' ),
						'end' => $now->modify( '+29 days' )->format( 'Y-m-d 23:59:59' ),
					];
				},
			],
			'this-year' => [
				'key' => 'this-year',
				'label' => _x( 'This year', 'range presets', 'voxel' ),
				'callback' => function( $now ) {
					return [
						'start' => $now->format( 'Y-m-d H:i:s' ),
						'end' => $now->format( 'Y-12-31 23:59:59' ),
					];
				},
			],
			'next-year' => [
				'key' => 'next-year',
				'label' => _x( 'Next year', 'range presets', 'voxel' ),
				'callback' => function( $now ) {
					return [
						'start' => $now->modify('+1 year')->format( 'Y-01-01 00:00:00' ),
						'end' => $now->modify('+1 year')->format( 'Y-12-31 23:59:59' ),
					];
				},
			],
			'next-365-days' => [
				'key' => 'next-365-days',
				'label' => _x( 'Next 365 days', 'range presets', 'voxel' ),
				'callback' => function( $now ) {
					return [
						'start' => $now->format( 'Y-m-d H:i:s' ),
						'end' => $now->modify( '+364 days' )->format( 'Y-m-d 23:59:59' ),
					];
				},
			],
		];
	}

	if ( $key === null ) {
		return $ranges;
	}

	return $ranges[ $key ] ?? null;
}

function get_visitor_os() {
	$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
	if ( empty( $user_agent ) ) {
		return null;
	}

	$list = [
		'/windows/i' => 'Windows',
		'/macintosh|mac os x/i' => 'macOS',
		'/linux/i' => 'Linux',
		'/ubuntu/i' => 'Ubuntu',
		'/iphone|ipad|ipod/i' => 'iOS',
		'/android/i' => 'Android',
		'/webos/i' => 'webOS'
	];

	foreach ( $list as $regex => $os ) {
		if ( preg_match( $regex, $user_agent ) ) {
			return $os;
		}
	}
}

// innodb default stopwords
function get_stopwords() {
	static $stopwords;
	if ( $stopwords === null ) {
		$list = apply_filters(
			'voxel/search-stopwords',
			'a about an are as at be by com de en for from how i in is it la of on or that the this to was what when where who will with und the www'
		);

		$words = preg_split('/\s+/', $list );
		$stopwords = [];
		foreach ( $words as $word ) {
			$stopwords[ $word ] = true;
		}
	}

	return $stopwords;
}

function prepare_keyword_search( $str ) {
	$stopwords = \Voxel\get_stopwords();

	$keywords = preg_split('/\s+/', str_replace( '@', '', $str ) );
	$keywords = array_map( function( $word ) use ( $stopwords ) {
		$word = trim( $word, '*+-~<>"()' );
		if ( mb_strlen( $word ) <= 2 ) {
			return null;
		}

		if ( isset( $stopwords[ strtolower( $word ) ] ) ) {
			return null;
		}

		return '+'.$word.'*';
	}, $keywords );

	return join( ' ', array_filter( $keywords ) );
}

function count_format( $visible_count, $total_count ) {
	if ( $total_count === 0 ) {
		return _x( 'No results', 'post feed', 'voxel' );
	} elseif ( $total_count === 1 ) {
		return _x( 'One result', 'post feed', 'voxel' );
	} elseif ( $total_count <= $visible_count ) {
		return \Voxel\replace_vars( _x( '@count results', 'post feed', 'voxel' ), [
			'@count' => number_format_i18n( $total_count ),
		] );
	} else {
		return \Voxel\replace_vars( _x( 'Showing @count out of @total results', 'post feed', 'voxel' ), [
			'@count' => number_format_i18n( $visible_count ),
			'@total' => number_format_i18n( $total_count ),
		] );
	}
}

function enqueue_maps() {
	if ( \Voxel\get('settings.maps.provider') === 'mapbox' && ! \Voxel\is_preview_mode() ) {
		wp_enqueue_script( 'vx:mapbox.js' );
		wp_enqueue_style( 'vx:mapbox.css' );
		wp_enqueue_script( 'mapbox-gl' );
		wp_enqueue_style( 'mapbox-gl' );
	} else {
		wp_enqueue_script( 'vx:google-maps.js' );
		wp_enqueue_script( 'google-maps' );
	}
}

function replace_vars( $str, $vars ) {
	return str_replace( array_keys( $vars ), array_values( $vars ), $str );
}
