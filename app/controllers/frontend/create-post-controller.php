<?php

namespace Voxel\Controllers\Frontend;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Create_Post_Controller extends \Voxel\Controllers\Base_Controller {

	protected function hooks() {
		$this->on( 'voxel_ajax_create_post', '@handle' );
		$this->on( 'voxel_ajax_create_post__admin', '@handle_admin_mode' );
		$this->on( 'voxel_ajax_create_post.relations.get_posts', '@get_posts_for_relation_field' );
	}

	protected function handle() {
		try {
			$user = \Voxel\current_user();
			$post_type = \Voxel\Post_Type::get( $_GET['post_type'] ?? null );
			if ( ! $post_type ) {
				throw new \Exception( __( 'Invalid request', 'voxel' ), 100 );
			}

			if ( empty( $_POST['postdata'] ) ) {
				throw new \Exception( __( 'Invalid request', 'voxel' ), 101 );
			}

			$post = null;
			$is_editing = false;
			$save_as_draft = ( $_REQUEST['save_as_draft'] ?? null ) === 'yes';

			if ( $post_type->get_key() === 'profile' ) {
				$post = $user->get_or_create_profile();
				if ( ! $post ) {
					throw new \Exception( _x( 'Could not update profile.', 'create post', 'voxel' ), 102 );
				}

				// cannot save profiles as draft
				if ( $save_as_draft ) {
					throw new \Exception( __( 'Invalid request', 'voxel' ), 120 );
				}
			}

			if ( ! empty( $_GET['post_id'] ) ) {
				$post = \Voxel\Post::get( $_GET['post_id'] );

				if ( $post_type->get_setting( 'submissions.update_status' ) === 'disabled' ) {
					throw new \Exception( _x( 'Edits not allowed.', 'create post', 'voxel' ), 103 );
				}

				if ( ! ( $post && \Voxel\Post::current_user_can_edit( $_GET['post_id'] ) ) ) {
					throw new \Exception( __( 'Permission check failed.', 'voxel' ), 104 );
				}

				if ( ! ( $post && $post->post_type->get_key() === $post_type->get_key() ) ) {
					throw new \Exception( __( 'Invalid request', 'voxel' ), 105 );
				}

				// to save an existing post as draft, its previous status must be draft as well
				if ( $save_as_draft && $post->get_status() !== 'draft' ) {
					throw new \Exception( __( 'Invalid request', 'voxel' ), 120 );
				}

				$is_editing = true;
			}

			if ( ! $is_editing ) {
				if ( ! $user->can_create_post( $post_type->get_key() ) ) {
					throw new \Exception( _x( 'You do not have permission to create new posts.', 'create post', 'voxel' ), 106 );
				}
			}

			// submissions/edits allowed check
			if ( $is_editing ) {
				if ( $post_type->get_setting( 'submissions.update_status' ) === 'disabled' ) {
					throw new \Exception( _x( 'Edits not allowed.', 'create post', 'voxel' ), 107 );
				}
			} else {
				if ( ! $post_type->get_setting( 'submissions.enabled' ) ) {
					throw new \Exception( _x( 'Submissions not allowed.', 'create post', 'voxel' ), 108 );
				}

				do_action( 'voxel/create-post-validation', $post_type );
			}

			$postdata = json_decode( stripslashes( $_POST['postdata'] ), true );
			// dd($postdata);

			$fields = $post_type->get_fields();
			$sanitized = [];
			$errors = [];

			/** step 1 **/
			// loop through fields
			  // sanitize field values
			  // store sanitized values
			foreach ( $fields as $field ) {
				if ( $post ) {
					$field->set_post( $post );
				}

				if ( ! isset( $postdata[ $field->get_key() ] ) ) {
					$sanitized[ $field->get_key() ] = null;
				} else {
					$sanitized[ $field->get_key() ] = $field->sanitize( $postdata[ $field->get_key() ] );
				}
			}

			/** step 2 **/
			// loop through fields
			  // run visibility rules and remove fields that don't pass
			$hidden_steps = [];
			foreach ( $fields as $field_key => $field ) {
				if ( isset( $hidden_steps[ $field->get_step() ] ) || ! $field->passes_visibility_rules() ) {
					unset( $fields[ $field_key ] );
					if ( $field->get_type() === 'ui-step' ) {
						$hidden_steps[ $field->get_key() ] = true;
					}
				}
			}

			/** step 2.5 **/
			// loop through fields
			  // run conditional logic and remove fields that don't pass conditions
			foreach ( $fields as $field_key => $field ) {
				if ( $field->get_prop('enable-conditions') ) {
					$conditions = $field->get_conditions();
					$passes_conditions = false;

					foreach ( $conditions as $condition_group ) {
						if ( empty( $condition_group ) ) {
							continue;
						}

						$group_passes = true;
						foreach ( $condition_group as $condition ) {
							$subject_parts = explode( '.', $condition->get_source() );
							$subject_field_key = $subject_parts[0];
							$subject_subfield_key = $subject_parts[1] ?? null;

							$subject_field = $fields[ $subject_field_key ] ?? null;
							if ( ! $subject_field ) {
								continue;
							}

							$value = $sanitized[ $subject_field->get_key() ];
							if ( $subject_subfield_key !== null ) {
								$value = $value[ $subject_subfield_key ] ?? null;
							}

							if ( $condition->evaluate( $value ) === false ) {
								$group_passes = false;
							}
						}

						if ( $group_passes ) {
							$passes_conditions = true;
						}
					}

					if ( ! $passes_conditions ) {
						unset( $fields[ $field_key ] );
					}
				}
			}

			/** step 3 **/
			// loop through remaining fields
			  // run is_required check
			  // run validations on sanitized value
			  // log errors
			foreach ( $fields as $field ) {
				try {
					$value = $sanitized[ $field->get_key() ];
					$field->check_validity( $value );
				} catch ( \Exception $e ) {
					$errors[] = $e->getMessage();
				}
			}

			/** step 4 **/
			// if there are errors, send them back to the create post widget
			// otherwise, create new post from sanitized and validated values
			if ( ! empty( $errors ) ) {
				return wp_send_json( [
					'success' => false,
					'errors' => $errors,
				] );
			}

			// determine post status
			if ( $is_editing ) {
				$post_author_id = $post->get_author_id();

				if ( $post->get_status() === 'draft' ) {
					if ( $save_as_draft ) {
						$post_status = 'draft';
					} else {
						$post_status = $post_type->get_setting( 'submissions.status' ) === 'publish' ? 'publish' : 'pending';
					}
				} elseif ( $post->get_status() === 'publish' ) {
					$post_status = $post_type->get_setting( 'submissions.update_status' ) === 'pending' ? 'pending' : 'publish';
				} elseif ( $post->get_status() === 'rejected' ) {
					$post_status = 'pending';
				} else {
					$post_status = $post->get_status();
				}
			} else {
				$post_author_id = $user->get_id();

				if ( $save_as_draft ) {
					$post_status = 'draft';
				} elseif ( $post_type->get_setting( 'submissions.status' ) === 'publish' ) {
					$post_status = 'publish';
				} else {
					$post_status = 'pending';
				}
			}

			$data = [
				'post_type' => $post_type->get_key(),
				'post_title' => $sanitized['title'] ?? '',
				'post_status' => $post_status,
				'post_author' => $post_author_id,
			];

			if ( $post ) {
				$data['ID'] = $post->get_id();

				if ( $post_type->get_setting( 'submissions.update_slug' ) ) {
					$data['post_name'] = sanitize_title( $sanitized['title'] ?? '' );
				}

				$post_id = wp_update_post( $data );
			} else {
				$data['post_name'] = sanitize_title( $sanitized['title'] ?? '' );
				$post_id = wp_insert_post( $data );
			}

			if ( is_wp_error( $post_id ) ) {
				throw new \Exception( _x( 'Could not save post.', 'create post', 'voxel' ), 109 );
			}

			$post = \Voxel\Post::get( $post_id );

			foreach ( $fields as $field ) {
				$field->set_post( $post );
				$field->update( $sanitized[ $field->get_key() ] );
			}
			
			require_once 'create_title.php';
			
			$post = \Voxel\Post::get( $post_id );
			
			if(get_post($post_id)->post_status == 'pending'){
				wp_update_post(array(
					'ID'    =>  $post_id,
				));		
				
			}
			// clean post cache
			clean_post_cache( $post->get_id() );

			// refresh index
			$indexable_statuses = $post_type->get_indexable_statuses();
			( isset( $indexable_statuses[ $post_status ] ) ) ? $post->index() : $post->unindex();

			// success message
			if ( $is_editing ) {
				$update_status = $post_type->get_setting( 'submissions.update_status' );
				if ( $post_status === 'pending' ) {
					$message = _x( 'Your changes have been submitted for review.', 'create post', 'voxel' );
				} elseif ( $update_status === 'pending_merge' ) {
					$message = _x( 'Your changes have been submitted and will be applied once approved.', 'create post', 'voxel' );
				} else {
					$message = _x( 'Your changes have been applied.', 'create post', 'voxel' );
				}

				( new \Voxel\Events\Post_Updated_Event( $post->post_type ) )->dispatch( $post->get_id() );
			} else {
				if ( $post_status === 'draft' ) {
					$message = _x( 'Your post has been saved as draft.', 'create post', 'voxel' );
				} elseif ( $post_status === 'pending' ) {
					$message = _x( 'Your post has been submitted for review.', 'create post', 'voxel' );
				} else {
					$message = _x( 'Your post has been published.', 'create post', 'voxel' );
				}

				( new \Voxel\Events\Post_Submitted_Event( $post->post_type ) )->dispatch( $post->get_id() );
			}

			$view_link = $post->post_type->get_key() === 'profile' ? $user->get_link() : $post->get_link();

			return wp_send_json( [
				'success' => true,
				'edit_link' => $post->get_edit_link(),
				'view_link' => $view_link,
				'message' => $message,
				'status' => $post_status,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
				'code' => $e->getCode(),
			] );
		}
	}

	protected function handle_admin_mode() {
		try {
			if ( ! wp_verify_nonce( $_GET['admin_mode'], 'vx_create_post_admin_mode' ) ) {
				throw new \Exception( __( 'Invalid request', 'voxel' ) );
			}

			$user = \Voxel\current_user();
			$post_type = \Voxel\Post_Type::get( $_GET['post_type'] ?? null );
			if ( ! $post_type ) {
				throw new \Exception( __( 'Invalid request', 'voxel' ) );
			}

			if ( empty( $_POST['postdata'] ) ) {
				throw new \Exception( __( 'Invalid request', 'voxel' ) );
			}

			$post = \Voxel\Post::get( $_GET['post_id'] );
			if ( ! ( $post && current_user_can( 'edit_others_posts', $post->get_id() ) ) ) {
				throw new \Exception( __( 'Permission check failed.', 'voxel' ) );
			}

			if ( ! ( $post && $post->post_type->get_key() === $post_type->get_key() ) ) {
				throw new \Exception( __( 'Invalid request', 'voxel' ) );
			}

			$postdata = json_decode( stripslashes( $_POST['postdata'] ), true );
			$fields = $post->get_fields();
			$sanitized = [];
			$errors = [];

			/** step 1 **/
			// loop through fields
			  // sanitize field values
			  // store sanitized values
			foreach ( $fields as $field ) {
				if ( ! isset( $postdata[ $field->get_key() ] ) ) {
					$sanitized[ $field->get_key() ] = null;
				} else {
					$sanitized[ $field->get_key() ] = $field->sanitize( $postdata[ $field->get_key() ] );
				}
			}

			/** step 2 **/
			// loop through fields
			  // run visibility rules and remove fields that don't pass
			$hidden_steps = [];
			foreach ( $fields as $field_key => $field ) {
				if ( isset( $hidden_steps[ $field->get_step() ] ) || ! $field->passes_visibility_rules() ) {
					unset( $fields[ $field_key ] );
					if ( $field->get_type() === 'ui-step' ) {
						$hidden_steps[ $field->get_key() ] = true;
					}
				}
			}

			/** step 2.5 **/
			// loop through fields
			  // run conditional logic and remove fields that don't pass conditions
			foreach ( $fields as $field_key => $field ) {
				if ( $field->get_prop('enable-conditions') ) {
					$conditions = $field->get_conditions();
					$passes_conditions = false;

					foreach ( $conditions as $condition_group ) {
						if ( empty( $condition_group ) ) {
							continue;
						}

						$group_passes = true;
						foreach ( $condition_group as $condition ) {
							$subject_parts = explode( '.', $condition->get_source() );
							$subject_field_key = $subject_parts[0];
							$subject_subfield_key = $subject_parts[1] ?? null;

							$subject_field = $fields[ $subject_field_key ] ?? null;
							if ( ! $subject_field ) {
								continue;
							}

							$value = $sanitized[ $subject_field->get_key() ];
							if ( $subject_subfield_key !== null ) {
								$value = $value[ $subject_subfield_key ] ?? null;
							}

							if ( $condition->evaluate( $value ) === false ) {
								$group_passes = false;
							}
						}

						if ( $group_passes ) {
							$passes_conditions = true;
						}
					}

					if ( ! $passes_conditions ) {
						unset( $fields[ $field_key ] );
					}
				}
			}

			/** step 3 **/
			// loop through remaining fields
			// skip validation on wp-admin

			/** step 4 **/
			// if there are errors, send them back to the create post widget
			// otherwise, create new post from sanitized and validated values
			if ( ! empty( $errors ) ) {
				return wp_send_json( [
					'success' => false,
					'errors' => $errors,
				] );
			}

			foreach ( $fields as $field ) {
				$field->update( $sanitized[ $field->get_key() ] );
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

	protected function get_posts_for_relation_field() {
		try {
			$post_type_keys = explode( ',', sanitize_text_field( $_GET['post_types'] ?? '' ) );
			$post_types = [];
			foreach ( $post_type_keys as $post_type_key ) {
				$post_type = \Voxel\Post_Type::get( $post_type_key );
				if ( $post_type && $post_type->is_managed_by_voxel() ) {
					$post_types[] = $post_type->get_key();
				}
			}

			if ( empty( $post_types ) ) {
				throw new \Exception( __( 'Invalid request', 'voxel' ) );
			}

			global $wpdb;

			$author_id = absint( get_current_user_id() );
			if ( ! empty( $_GET['post_id'] ) ) {
				$post = \Voxel\Post::get( (int) $_GET['post_id'] );
				if ( $post && $post->is_editable_by_current_user() ) {
					$author_id = $post->get_author_id();
				}
			}

			$offset = isset( $_GET['offset'] ) ? absint( $_GET['offset'] ) : 0;
			$per_page = 10;
			$limit = $per_page + 1;

			if ( ! empty( $_GET['exclude'] ) ) {
				$exclude_ids = explode( ',', sanitize_text_field( $_GET['exclude'] ) );
				$exclude_ids = array_filter( array_map( 'absint', $exclude_ids ) );
				if ( ! empty( $exclude_ids ) ) {
					$post__not_in = sprintf( 'AND ID NOT IN (%s)', join( ',', $exclude_ids ) );
				}
			} else {
				$post__not_in = '';
			}

			$query_post_types = '\''.join( '\',\'', array_map( 'esc_sql', $post_types ) ).'\'';

			$query_order_by = 'post_title ASC';
			$query_search = '';

			if ( ! empty( $_GET['search'] ) ) {
				$search_string = sanitize_text_field( $_GET['search'] );
				$search_string = \Voxel\prepare_keyword_search( $search_string );
				if ( ! empty( $search_string ) ) {
					$search_string = esc_sql( $search_string );
					$query_search = "AND MATCH(post_title) AGAINST('{$search_string}' IN BOOLEAN MODE)";
					$query_order_by = "MATCH(post_title) AGAINST('{$search_string}' IN BOOLEAN MODE) DESC";
				}
			}

			$sql = <<<SQL
				SELECT ID FROM {$wpdb->posts}
					WHERE post_status = 'publish'
						AND post_type IN ({$query_post_types})
						{$post__not_in}
						{$query_search}
					ORDER BY {$query_order_by}
					LIMIT {$limit} OFFSET {$offset}
			SQL;
			$post_ids = $wpdb->get_col( $sql );
			// dd_sql($sql);
			$has_more = count( $post_ids ) > $per_page;
			if ( $has_more ) {
				array_pop( $post_ids );
			}

			_prime_post_caches( $post_ids );

			$posts = [];
			foreach ( $post_ids as $post_id ) {
				if ( $post = \Voxel\Post::get( $post_id ) ) {
					if ( $post->post_type->get_key() === 'profile' && ( $author = $post->get_author() ) ) {
						$posts[] = [
							'id' => $post->get_id(),
							'title' => $author->get_display_name(),
							'logo' => $author->get_avatar_markup(),
							'type' => $post->post_type->get_singular_name(),
							'icon' => \Voxel\get_icon_markup( $post->post_type->get_icon() ),
						];
					} else {
						$posts[] = [
							'id' => $post->get_id(),
							'title' => $post->get_title(),
							'logo' => $post->get_logo_markup(),
							'type' => $post->post_type->get_singular_name(),
							'icon' => \Voxel\get_icon_markup( $post->post_type->get_icon() ),
						];
					}
				}
			}

			return wp_send_json( [
				'success' => true,
				'has_more' => $has_more,
				'data' => $posts,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

}
