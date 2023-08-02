<?php

namespace Voxel;

if ( ! defined('ABSPATH') ) {
	exit;
}

function get_current_post( $force_get = false ) {
	static $current_post;
	if ( ! is_null( $current_post ) && $force_get === false ) {
		return $current_post;
	}

	global $post;
	if ( $post instanceof \WP_Post ) {
		$current_post = \Voxel\Post::get( $post );
	} else {
		$queried_object = get_queried_object();
		if ( $queried_object instanceof \WP_Post ) {
			$current_post = \Voxel\Post::get( $queried_object );
		}
	}

	return $current_post;
}

function set_current_post( \Voxel\Post $the_post ) {
	global $post;
	$post = $the_post->get_wp_post_object();
	setup_postdata( $post );
	\Voxel\get_current_post( true );
}

function get_current_post_type() {
	$post = \Voxel\get_current_post();
	return $post ? $post->post_type : null;
}

function get_current_author() {
	$post = \Voxel\get_current_post();
	return $post ? $post->get_author() : null;
}

function get_current_term( $force_get = false ) {
	if ( ! is_null( $GLOBALS['vx_current_term'] ?? null ) && $force_get === false ) {
		return $GLOBALS['vx_current_term'];
	}

	$GLOBALS['vx_current_term'] = \Voxel\Term::get( get_queried_object() );
	return $GLOBALS['vx_current_term'];
}

function set_current_term( \Voxel\Term $term ) {
	$GLOBALS['vx_current_term'] = $term;
}

function get_search_results( $request, $options = [] ) {
	$options = array_merge( [
		'limit' => 10,
		'render' => true,
		'ids' => null,
		'template_id' => null,
		'get_total_count' => false,
		'exclude' => [],
	], $options );

	$max_limit = apply_filters( 'voxel/get_search_results/max_limit', 500 );
	$limit = min( $options['limit'], $max_limit );

	$results = [
		'ids' => [],
		'render' => null,
		'has_next' => false,
		'has_prev' => false,
		'templates' => null,
		'scripts' => '',
	];

	$post_type = \Voxel\Post_Type::get( sanitize_text_field( $request['type'] ?? '' ) );
	if ( ! $post_type ) {
		return $results;
	}

	$template_id = $post_type->get_templates()['card'];
	if ( is_numeric( $options['template_id'] ) ) {
		$custom_card_templates = array_column( $post_type->repository->get_custom_templates()['card'], 'id' );
		if ( in_array( $options['template_id'], $custom_card_templates ) ) {
			$template_id = $options['template_id'];
		}
	}

	if ( ! \Voxel\template_exists( $template_id ) ) {
		return $results;
	}

	$results['template_id'] = $template_id;

	if ( $options['render'] && ( $GLOBALS['vx_preview_card_level'] ?? 0 ) > 1 ) {
		$results['ids'] = [];
	} elseif ( is_array( $options['ids'] ) ) {
		$results['ids'] = $options['ids'];
	} else {
		$args = [];
		foreach ( $post_type->get_filters() as $filter ) {
			if ( isset( $request[ $filter->get_key() ] ) ) {
				$args[ $filter->get_key() ] = $request[ $filter->get_key() ];
			}
		}

		$args['limit'] = absint( $limit );
		$page = absint( $request['pg'] ?? 1 );
		if ( $page > 1 ) {
			$args['offset'] = ( $args['limit'] * ( $page - 1 ) );
		}

		$args['limit'] += 1;

		$cb = function( $query ) use ( $options ) {
			if ( ! empty( $options['exclude'] ) && is_array( $options['exclude'] ) ) {
				$exclude_ids = array_values( array_filter( array_map( 'absint', $options['exclude'] ) ) );
				if ( ! empty( $exclude_ids ) ) {
					if ( count( $exclude_ids ) === 1 ) {
						$query->where( sprintf( 'post_id <> %d', $exclude_ids[0] ) );
					} else {
						$query->where( sprintf( 'post_id NOT IN (%s)', join( ',', $exclude_ids ) ) );
					}
				}
			}
		};

		$_start = microtime( true );
		$post_ids = $post_type->query( $args, $cb );

		if ( $options['get_total_count'] ) {
			$results['total_count'] = $post_type->get_index_query()->get_post_count( $args, $cb );
		}

		$_query_time = microtime( true ) - $_start;

		$results['has_prev'] = $page > 1;
		if ( count( $post_ids ) === $args['limit'] ) {
			$results['has_next'] = true;
			array_pop( $post_ids );
		}

		$results['ids'] = $post_ids;

		do_action( 'qm/info', sprintf( 'Query time: %sms', round( $_query_time * 1000, 1 ) ) );
		do_action( 'qm/info', trim( $post_type->get_index_query()->get_sql( $args ) ) );
	}

	if ( $options['render'] ) {
		if ( ! isset( $GLOBALS['vx_preview_card_current_ids'] ) ) {
			$GLOBALS['vx_preview_card_current_ids'] = $results['ids'];
		}

		if ( ! isset( $GLOBALS['vx_preview_card_level'] ) ) {
			$GLOBALS['vx_preview_card_level'] = 0;
		}

		if ( $GLOBALS['vx_preview_card_level'] > 1 ) {
			$results['render'] = '';
		} else {
			$previous_ids = $GLOBALS['vx_preview_card_current_ids'];
			$GLOBALS['vx_preview_card_current_ids'] = $results['ids'];
			$GLOBALS['vx_preview_card_level']++;

			do_action( 'qm/start', 'render_search_results' );
			do_action( 'voxel/before_render_search_results' );

			_prime_post_caches( array_map( 'absint', $results['ids'] ) );

			ob_start();
			$current_request_post = \Voxel\get_current_post();

			$has_results = false;

			add_filter( 'elementor/frontend/builder_content/before_print_css', '__return_false' );

			foreach ( $results['ids'] as $i => $post_id ) {
				$post = \Voxel\Post::get( $post_id );
				if ( ! $post ) {
					continue;
				}


				if ( is_admin() ) {
					\Voxel\print_template_css( $template_id );
				}

				$has_results = true;
				\Voxel\set_current_post( $post );

				echo '<div class="ts-preview" data-post-id="'.$post_id.'" '._post_get_position_attr( $post ).'>';
				\Voxel\print_template( $template_id );

				if ( $GLOBALS['vx_preview_card_level'] === 1 ) {
					echo '<div class="ts-marker-wrapper hidden">';
					echo _post_get_marker( $post );
					echo '</div>';
				}

				echo '</div>';

				do_action( 'qm/lap', 'render_search_results' );
			}

			// reset current post
			if ( $current_request_post ) {
				\Voxel\set_current_post( $current_request_post );
			}

			if ( \Voxel\is_dev_mode() ) { ?>
				<script type="text/javascript">
					<?php if ( ! is_array( $options['ids'] ) ): ?>
						console.log('Query time: %c' + <?= round( ( $_query_time ?? 0 ) * 1000, 1 ) ?> + 'ms', 'color: #81c784;');
					<?php endif ?>
				</script>
			<?php }

			$results['render'] = ob_get_clean();

			wp_enqueue_style( 'vx:post-feed.css' ); // phastpress compat
			ob_start();
			foreach ( wp_styles()->queue as $handle ) {
				wp_styles()->do_item( $handle );
			}
			$results['styles'] = ob_get_clean();

			ob_start();
			foreach ( wp_scripts()->queue as $handle ) {
				wp_scripts()->do_item( $handle );
			}
			$results['scripts'] = ob_get_clean();

			do_action( 'qm/stop', 'render_search_results' );
			$GLOBALS['vx_preview_card_level']--;
			$GLOBALS['vx_preview_card_current_ids'] = $previous_ids;
		}
	}

	return $results;
}

function _post_get_position_attr( $post ) {
	$location = $post->get_field('location');
	$loc = $location ? $location->get_value() : [];
	$position = ( $loc['latitude'] ?? null && $loc['longitude'] ?? null ) ? $loc['latitude'].','.$loc['longitude'] : null;
	return $position ? sprintf( 'data-position="%s"', esc_attr( $position ) ) : '';
}

function _post_get_marker( $post ) {
	$marker_type = $post->post_type->get_setting( 'map.marker_type' );

	$icon_markup = \Voxel\get_icon_markup( $post->post_type->get_setting( 'map.marker_icon' ) );
	$default_marker = '<div data-post-id="'.$post->get_id().'" class="map-marker marker-type-icon">'.$icon_markup.'</div>';

	if ( $marker_type === 'text' ) {
		$text = esc_html( \Voxel\render( $post->post_type->get_setting( 'map.marker_text' ) ) );
		return '<div data-post-id="'.$post->get_id().'" class="map-marker marker-type-text">'.$text.'</div>';
	} elseif ( $marker_type === 'image' ) {
		$field = $post->get_field( $post->post_type->get_setting( 'map.marker_image' ) );
		if ( ! ( $field && $field->get_type() === 'image' ) ) {
			return $default_marker;
		}

		$image_ids = $field->get_value();
		if ( empty( $image_ids ) ) {
			$image_ids = [ $field->get_default() ];
		}

		$image_id = array_shift( $image_ids );
		$url = esc_attr( wp_get_attachment_image_url( $image_id, 'thumbnail' ) );
		$alt = esc_attr( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) );
		if ( empty( $url ) ) {
			return $default_marker;
		}

		return '<div data-post-id="'.$post->get_id().'" class="map-marker marker-type-image"><img src="'.$url.'" alt="'.$alt.'"></div>';
	} else {
		return $default_marker;
	}
}

function cache_post_review_stats( $post_id ) {
	global $wpdb;

	$stats = [
		'total' => 0,
		'average' => null,
		'by_score' => [],
		'latest' => null,
	];

	$results = $wpdb->get_row( $wpdb->prepare( <<<SQL
		SELECT AVG(review_score) AS average, COUNT(review_score) AS total
		FROM {$wpdb->prefix}voxel_timeline
		WHERE post_id = %d AND review_score IS NOT NULL
	SQL, $post_id ) );

	if ( is_numeric( $results->average ) && is_numeric( $results->total ) && $results->total > 0 ) {
		$stats['total'] = absint( $results->total );
		$stats['average'] = \Voxel\clamp( $results->average, -2, 2 );

		$by_score = $wpdb->get_results( $wpdb->prepare( <<<SQL
			SELECT ROUND(review_score) AS score, COUNT(review_score) AS total
			FROM {$wpdb->prefix}voxel_timeline
			WHERE post_id = %d AND review_score BETWEEN -2 AND 2
			GROUP BY ROUND(review_score)
		SQL, $post_id ) );

		foreach ( $by_score as $score ) {
			if ( is_numeric( $score->score ) && is_numeric( $score->total ) && $score->total > 0 ) {
				$stats['by_score'][ (int) $score->score ] = absint( $score->total );
			}
		}

		// get latest item
		$latest = $wpdb->get_row( $wpdb->prepare( <<<SQL
			SELECT id, created_at, user_id, published_as
			FROM {$wpdb->prefix}voxel_timeline
			WHERE post_id = %d AND review_score IS NOT NULL
			ORDER BY created_at DESC LIMIT 1
		SQL, $post_id ) );

		if ( is_numeric( $latest->id ?? null ) && strtotime( $latest->created_at ) ) {
			$stats['latest'] = [
				'id' => absint( $latest->id ),
				'user_id' => is_numeric( $latest->user_id ) ? absint( $latest->user_id ) : null,
				'published_as' => is_numeric( $latest->published_as ) ? absint( $latest->published_as ) : null,
				'created_at' => date( 'Y-m-d H:i:s', strtotime( $latest->created_at ) ),
			];
		}
	}

	update_post_meta( $post_id, 'voxel:review_stats', wp_slash( wp_json_encode( $stats ) ) );
	do_action( 'voxel/post/review-stats-updated', $post_id, $stats );
	return $stats;
}

function cache_post_timeline_stats( $post_id ) {
	global $wpdb;

	$stats = [
		'total' => 0,
		'latest' => null,
	];

	// calculate total count
	$total = $wpdb->get_var( $wpdb->prepare( <<<SQL
		SELECT COUNT(id) AS total
		FROM {$wpdb->prefix}voxel_timeline
		WHERE post_id = %d AND published_as = %d AND review_score IS NULL
	SQL, $post_id, $post_id ) );

	$stats['total'] = is_numeric( $total ) ? absint( $total ) : 0;

	// get latest item
	$latest = $wpdb->get_row( $wpdb->prepare( <<<SQL
		SELECT id, created_at
		FROM {$wpdb->prefix}voxel_timeline
		WHERE post_id = %d AND published_as = %d AND review_score IS NULL
		ORDER BY created_at DESC LIMIT 1
	SQL, $post_id, $post_id ) );

	if ( is_numeric( $latest->id ?? null ) && strtotime( $latest->created_at ) ) {
		$stats['latest'] = [
			'id' => absint( $latest->id ),
			'created_at' => date( 'Y-m-d H:i:s', strtotime( $latest->created_at ) ),
		];
	}

	update_post_meta( $post_id, 'voxel:timeline_stats', wp_slash( wp_json_encode( $stats ) ) );
	do_action( 'voxel/post/timeline-stats-updated', $post_id, $stats );
	return $stats;
}

function cache_post_wall_stats( $post_id ) {
	global $wpdb;

	$stats = [
		'total' => 0,
		'latest' => null,
	];

	// calculate total count
	$total = $wpdb->get_var( $wpdb->prepare( <<<SQL
		SELECT COUNT(id) AS total
		FROM {$wpdb->prefix}voxel_timeline
		WHERE post_id = %d AND NOT( published_as <=> %d ) AND review_score IS NULL
	SQL, $post_id, $post_id ) );

	$stats['total'] = is_numeric( $total ) ? absint( $total ) : 0;

	// get latest item
	$latest = $wpdb->get_row( $wpdb->prepare( <<<SQL
		SELECT id, created_at, user_id, published_as
		FROM {$wpdb->prefix}voxel_timeline
		WHERE post_id = %d AND NOT( published_as <=> %d ) AND review_score IS NULL
		ORDER BY created_at DESC LIMIT 1
	SQL, $post_id, $post_id ) );

	if ( is_numeric( $latest->id ?? null ) && strtotime( $latest->created_at ) ) {
		$stats['latest'] = [
			'id' => absint( $latest->id ),
			'user_id' => is_numeric( $latest->user_id ) ? absint( $latest->user_id ) : null,
			'published_as' => is_numeric( $latest->published_as ) ? absint( $latest->published_as ) : null,
			'created_at' => date( 'Y-m-d H:i:s', strtotime( $latest->created_at ) ),
		];
	}

	update_post_meta( $post_id, 'voxel:wall_stats', wp_slash( wp_json_encode( $stats ) ) );
	do_action( 'voxel/post/wall-stats-updated', $post_id, $stats );
	return $stats;
}

function cache_post_review_reply_stats( $post_id ) {
	global $wpdb;

	$stats = [
		'total' => 0,
		'latest' => null,
	];

	$results = $wpdb->get_row( $wpdb->prepare( <<<SQL
		SELECT COUNT(r.id) AS total
		FROM {$wpdb->prefix}voxel_timeline_replies r
		LEFT JOIN {$wpdb->prefix}voxel_timeline t ON r.status_id = t.id
		WHERE t.post_id = %d AND t.review_score IS NOT NULL
	SQL, $post_id ) );

	if ( is_numeric( $results->total ) && $results->total > 0 ) {
		$stats['total'] = absint( $results->total );

		// get latest item
		$latest = $wpdb->get_row( $wpdb->prepare( <<<SQL
			SELECT r.id AS id, r.created_at AS created_at, r.user_id AS user_id, r.published_as AS published_as
			FROM {$wpdb->prefix}voxel_timeline_replies r
			LEFT JOIN {$wpdb->prefix}voxel_timeline t ON r.status_id = t.id
			WHERE t.post_id = %d AND t.review_score IS NOT NULL
			ORDER BY r.created_at DESC LIMIT 1
		SQL, $post_id ) );

		if ( is_numeric( $latest->id ?? null ) && strtotime( $latest->created_at ) ) {
			$stats['latest'] = [
				'id' => absint( $latest->id ),
				'user_id' => is_numeric( $latest->user_id ) ? absint( $latest->user_id ) : null,
				'published_as' => is_numeric( $latest->published_as ) ? absint( $latest->published_as ) : null,
				'created_at' => date( 'Y-m-d H:i:s', strtotime( $latest->created_at ) ),
			];
		}
	}

	update_post_meta( $post_id, 'voxel:review_reply_stats', wp_slash( wp_json_encode( $stats ) ) );
	do_action( 'voxel/post/review-reply-stats-updated', $post_id, $stats );
	return $stats;
}

function cache_post_timeline_reply_stats( $post_id ) {
	global $wpdb;

	$stats = [
		'total' => 0,
		'latest' => null,
	];

	$results = $wpdb->get_row( $wpdb->prepare( <<<SQL
		SELECT COUNT(r.id) AS total
		FROM {$wpdb->prefix}voxel_timeline_replies r
		LEFT JOIN {$wpdb->prefix}voxel_timeline t ON r.status_id = t.id
		WHERE t.post_id = %d AND t.published_as = %d AND t.review_score IS NULL
	SQL, $post_id, $post_id ) );

	if ( is_numeric( $results->total ) && $results->total > 0 ) {
		$stats['total'] = absint( $results->total );

		// get latest item
		$latest = $wpdb->get_row( $wpdb->prepare( <<<SQL
			SELECT r.id AS id, r.created_at AS created_at, r.user_id AS user_id, r.published_as AS published_as
			FROM {$wpdb->prefix}voxel_timeline_replies r
			LEFT JOIN {$wpdb->prefix}voxel_timeline t ON r.status_id = t.id
			WHERE t.post_id = %d AND t.published_as = %d AND t.review_score IS NULL
			ORDER BY r.created_at DESC LIMIT 1
		SQL, $post_id, $post_id ) );

		if ( is_numeric( $latest->id ?? null ) && strtotime( $latest->created_at ) ) {
			$stats['latest'] = [
				'id' => absint( $latest->id ),
				'user_id' => is_numeric( $latest->user_id ) ? absint( $latest->user_id ) : null,
				'published_as' => is_numeric( $latest->published_as ) ? absint( $latest->published_as ) : null,
				'created_at' => date( 'Y-m-d H:i:s', strtotime( $latest->created_at ) ),
			];
		}
	}

	update_post_meta( $post_id, 'voxel:timeline_reply_stats', wp_slash( wp_json_encode( $stats ) ) );
	do_action( 'voxel/post/timeline-reply-stats-updated', $post_id, $stats );
	return $stats;
}


function cache_post_wall_reply_stats( $post_id ) {
	global $wpdb;

	$stats = [
		'total' => 0,
		'latest' => null,
	];

	$results = $wpdb->get_row( $wpdb->prepare( <<<SQL
		SELECT COUNT(r.id) AS total
		FROM {$wpdb->prefix}voxel_timeline_replies r
		LEFT JOIN {$wpdb->prefix}voxel_timeline t ON r.status_id = t.id
		WHERE t.post_id = %d AND NOT( t.published_as <=> %d ) AND t.review_score IS NULL
	SQL, $post_id, $post_id ) );

	if ( is_numeric( $results->total ) && $results->total > 0 ) {
		$stats['total'] = absint( $results->total );

		// get latest item
		$latest = $wpdb->get_row( $wpdb->prepare( <<<SQL
			SELECT r.id AS id, r.created_at AS created_at, r.user_id AS user_id, r.published_as AS published_as
			FROM {$wpdb->prefix}voxel_timeline_replies r
			LEFT JOIN {$wpdb->prefix}voxel_timeline t ON r.status_id = t.id
			WHERE t.post_id = %d AND NOT( t.published_as <=> %d ) AND t.review_score IS NULL
			ORDER BY r.created_at DESC LIMIT 1
		SQL, $post_id, $post_id ) );

		if ( is_numeric( $latest->id ?? null ) && strtotime( $latest->created_at ) ) {
			$stats['latest'] = [
				'id' => absint( $latest->id ),
				'user_id' => is_numeric( $latest->user_id ) ? absint( $latest->user_id ) : null,
				'published_as' => is_numeric( $latest->published_as ) ? absint( $latest->published_as ) : null,
				'created_at' => date( 'Y-m-d H:i:s', strtotime( $latest->created_at ) ),
			];
		}
	}

	update_post_meta( $post_id, 'voxel:wall_reply_stats', wp_slash( wp_json_encode( $stats ) ) );
	do_action( 'voxel/post/wall-reply-stats-updated', $post_id, $stats );
	return $stats;
}
