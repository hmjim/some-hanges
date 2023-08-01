<?php

namespace Voxel\Controllers\Frontend;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Search_Controller extends \Voxel\Controllers\Base_Controller {

	protected function hooks() {
		$this->on( 'voxel_ajax_search_posts', '@search_posts' );
		$this->on( 'voxel_ajax_nopriv_search_posts', '@search_posts' );

		$this->on( 'voxel_ajax_quick_search', '@quick_search' );
		$this->on( 'voxel_ajax_nopriv_quick_search', '@quick_search' );

		$this->on( 'voxel_ajax_get_preview_card', '@get_preview_card' );
		$this->on( 'voxel_ajax_nopriv_get_preview_card', '@get_preview_card' );
	}

	protected function search_posts() {
		$limit = absint( $_GET['limit'] ?? 10 );
		$page = absint( $_GET['pg'] ?? 1 );
		$results = \Voxel\get_search_results( wp_unslash( $_GET ), [
			'limit' => $limit,
			'template_id' => is_numeric( $_GET['__template_id'] ?? null ) ? (int) $_GET['__template_id'] : null,
			'get_total_count' => ! empty( $_GET['__get_total_count'] ),
		] );

		echo $results['styles'];
		echo $results['render'];
		echo $results['scripts'];

		$total_count = $results['total_count'] ?? 0;

		printf(
			'<script class="info" data-has-prev="%s" data-has-next="%s" data-has-results="%s" data-total-count="%d" data-display-count="%s" data-display-count-alt="%s"></script>',
			$results['has_prev'] ? 'true' : 'false',
			$results['has_next'] ? 'true' : 'false',
			! empty( $results['ids'] ) ? 'true' : 'false',
			$total_count,
			\Voxel\count_format( count( $results['ids'] ), $total_count ),
			\Voxel\count_format( ( ( $page - 1 ) * $limit ) + count( $results['ids'] ), $total_count )
		);
	}

	protected function quick_search() {
		try {
			$post_type = \Voxel\Post_Type::get( sanitize_text_field( $_GET['post_type'] ?? '' ) );
			$search_query = \Voxel\prepare_keyword_search( sanitize_text_field( wp_unslash( $_GET['search'] ?? '' ) ) );
			$filter_key = sanitize_text_field( $_GET['filter_key'] ?? '' );
			$taxonomies = array_filter( array_map( 'sanitize_text_field', explode( ',', $_GET['taxonomies'] ?? '' ) ) );
			if ( ! ( $post_type && $post_type->is_managed_by_voxel() ) ) {
				throw new \Exception( _x( 'Post type not found.', 'quick search', 'voxel' ) );
			}

			if ( empty( $search_query ) ) {
				return wp_send_json( [
					'data' => [],
					'success' => true,
				] );
			}

			global $wpdb;
			$results = [];
			$dev = [];

			$filter = $post_type->get_filter( $filter_key );
			if ( $filter && $filter->get_type() === 'keywords' ) {
				$post_ids = $post_type->query( [
					$filter->get_key() => $search_query,
					'limit' => 5,
				], function( $query, $args ) use ( $filter ) {
					$filter->orderby_relevance( $query, $args );
				} );

				$dev['postQuery'] = $wpdb->last_query;

				_prime_post_caches( $post_ids );
				foreach ( $post_ids as $post_id ) {
					if ( $post = \Voxel\Post::get( $post_id ) ) {
						if ( $post->post_type->get_key() === 'profile' && ( $author = $post->get_author() ) ) {
							$results[] = [
								'type' => 'post',
								'link' => $author->get_link(),
								'title' => $author->get_display_name(),
								'logo' => $author->get_avatar_markup(),
								'key' => sprintf( 'post:%d', $post->get_id() ),
							];
						} else {
							$results[] = [
								'type' => 'post',
								'link' => $post->get_link(),
								'title' => $post->get_title(),
								'logo' => $post->get_logo_id() ? $post->get_logo_markup() : '',
								'key' => sprintf( 'post:%d', $post->get_id() ),
							];
						}
					}
				}
			}

			// search terms
			$query_taxonomies = [];
			$allowed_taxonomies = $post_type->get_taxonomies();
			foreach ( $taxonomies as $taxonomy_key ) {
				if ( isset( $allowed_taxonomies[ $taxonomy_key ] ) && $allowed_taxonomies[ $taxonomy_key ]->is_publicly_queryable() ) {
					$query_taxonomies[] = $taxonomy_key;
				}
			}

			if ( ! empty( $query_taxonomies ) ) {
				$_taxonomy_in = '\''.join('\',\'', array_map( 'esc_sql', $query_taxonomies )).'\'';
				$term_ids = $wpdb->get_col( $wpdb->prepare( "
					SELECT t.term_id, MATCH (t.name) AGAINST (%s IN BOOLEAN MODE) AS relevance
					FROM {$wpdb->terms} AS t
					INNER JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id
					WHERE tt.taxonomy IN ({$_taxonomy_in})
						AND MATCH (t.name) AGAINST (%s IN BOOLEAN MODE)
					ORDER BY relevance DESC
					LIMIT 3
				", $search_query, $search_query ) );

				$dev['termQuery'] = $wpdb->last_query;

				_prime_term_caches( $term_ids );
				foreach ( $term_ids as $term_id ) {
					if ( $term = \Voxel\Term::get( $term_id ) ) {
						$results[] = [
							'type' => 'term',
							'link' => $term->get_link(),
							'title' => $term->get_label(),
							'logo' => \Voxel\get_icon_markup( $term->get_icon() ),
							'key' => sprintf( 'term:%d', $term->get_id() ),
						];
					}
				}
			}

			return wp_send_json( [
				'data' => $results,
				'success' => true,
				'dev' => \Voxel\is_dev_mode() ? $dev : null,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function get_preview_card() {
		try {
			$post = \Voxel\Post::get( $_GET['post_id'] ?? null );
			if ( ! ( $post && $post->post_type && $post->post_type->is_managed_by_voxel() ) ) {
				throw new \Exception( 'Invalid request.', 101 );
			}

			$template_id = absint( $_GET['template_id'] ?? null );
			$templates = $post->post_type->get_templates();
			$custom_card_templates = array_column( $post->post_type->repository->get_custom_templates()['card'], 'id' );
			if ( ! ( $template_id === $templates['card'] || in_array( $template_id, $custom_card_templates ) ) ) {
				throw new \Exception( 'Invalid request.', 102 );
			}

			\Voxel\set_current_post( $post );

			add_filter( 'elementor/frontend/builder_content/before_print_css', '__return_false' );

			ob_start();
			\Voxel\print_template( $template_id );
			foreach ( wp_scripts()->queue as $handle ) {
				wp_scripts()->do_item( $handle );
			}
			$markup = ob_get_clean();

			// print styles first
			foreach ( wp_styles()->queue as $handle ) {
				wp_styles()->do_item( $handle );
			}

			echo $markup;
			exit;
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
				'code' => $e->getCode(),
			] );
		}
	}
}
