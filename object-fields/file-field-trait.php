<?php

namespace Voxel\Object_Fields;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

trait File_Field_Trait {

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'key' => $this->get_model( 'key', [ 'width' => '1/1' ]),
			'description' => $this->get_description_model(),
			'allowed-types' => [
				'type' => Form_Models\Checkboxes_Model::class,
				'label' => 'Allowed file types',
				'width' => '1/1',
				'columns' => 'one',
				'choices' => array_combine( get_allowed_mime_types(), get_allowed_mime_types() ),
			],
			'max-count' => [
				'type' => Form_Models\Number_Model::class,
				'label' => 'Maximum file count',
				'width' => '1/1',
			],
			'max-size' => [
				'type' => Form_Models\Number_Model::class,
				'label' => 'Max file size (kB)',
				'width' => '1/2',
			],
			'required' => $this->get_required_model(),
		];
	}

	public function sanitize( $value ) {
		$files = [];
		$uploads = \Voxel\Utils\File_Uploader::prepare( $this->get_key(), $_FILES['files'] ?? [] );
		$upload_index = 0;

		foreach ( (array) $value as $file ) {
			if ( $file === 'uploaded_file' ) {
				$files[] = [
					'source' => 'new_upload',
					'data' => $uploads[ $upload_index ],
				];

				$upload_index++;
			} elseif ( is_numeric( $file ) ) {
				$files[] = [
					'source' => 'existing',
					'file_id' => absint( $file ),
				];
			}
		}

		return $files;
	}

	public function validate( $value ): void {
		if ( count( $value ) > absint( $this->props['max-count'] ) ) {
			throw new \Exception( sprintf(
				_x( '%s cannot have more than %d files.', 'field validation', 'voxel' ),
				$this->get_label(),
				absint( $this->props['max-count'] )
			) );
		}

		$allowed_types = $this->get_allowed_types();
		$max_size = absint( $this->props['max-size'] ) * 1000; // convert to bytes

		foreach ( $value as $file ) {
			if ( $file['source'] === 'new_upload' ) {
				if ( ! ( $file['data']['type'] && in_array( $file['data']['type'], $allowed_types, true ) ) ) {
					throw new \Exception( \Voxel\replace_vars(
						_x( '@field_name: file type not allowed: "@file_type"', 'field validation', 'voxel' ), [
							'@field_name' => $this->get_label(),
							'@file_type' => sanitize_text_field( $file['data']['type'] ),
						]
					) );
				}

				if ( $file['data']['size'] > $max_size ) {
					throw new \Exception( \Voxel\replace_vars(
						_x( '@field_name: uploaded file "@file" is larger than the @sizeMB limit', 'field validation', 'voxel' ), [
							'@field_name' => $this->get_label(),
							'@file' => sanitize_text_field( $file['data']['name'] ),
							'@size' => absint( $this->props['max-size'] ) / 1000,
						]
					) );
				}
			}

			if ( $file['source'] === 'existing' ) {
				$file_id = $file['file_id'] ?? null;
				if ( get_post_type( $file_id ) !== 'attachment' ) {
					throw new \Exception( \Voxel\replace_vars(
						_x( '@field_name: Invalid attachment provided', 'field validation', 'voxel' ), [
							'@field_name' => $this->get_label(),
						]
					) );
				}

				if ( ! current_user_can('administrator') ) {
					if ( get_post_status( $file_id ) === 'private' && (int) get_post_field( 'post_author', $file_id ) !== (int) get_current_user_id() ) {
						throw new \Exception( \Voxel\replace_vars(
							_x( '@field_name: Attachment not allowed', 'field validation', 'voxel' ), [
								'@field_name' => $this->get_label(),
							]
						) );
					}
				}

				$mime_type = get_post_mime_type( $file_id );
				if ( ! ( $mime_type && in_array( $mime_type, $allowed_types, true ) ) ) {
					throw new \Exception( \Voxel\replace_vars(
						_x( '@field_name: file type not allowed: "@file_type"', 'field validation', 'voxel' ), [
							'@field_name' => $this->get_label(),
							'@file_type' => sanitize_text_field( $mime_type ),
						]
					) );
				}
			}
		}
	}

	protected function frontend_props() {
		wp_enqueue_script( 'jquery-ui-sortable' );

		return [
			'maxCount' => $this->props['max-count'],
			'maxSize' => $this->props['max-size'],
			'allowedTypes' => $this->get_allowed_types(),
		];
	}

	protected function _prepare_ids_from_sanitized_input( $value, $insert_attachment_args = [] ): array {
		$file_ids = [];
		foreach ( $value as $file ) {
			if ( $file['source'] === 'new_upload' ) {
				try {
					$upload_dir = null;
					$skip_subdir = false;

					$custom_dir = $this->get_prop('upload_dir');
					if ( is_string( $custom_dir ) && ! empty( $custom_dir ) ) {
						$upload_dir = $custom_dir;
						$skip_subdir = !! $this->get_prop('skip_subdir');
					}

					if ( $this->get_prop('private_upload') ) {
						$insert_attachment_args['_display_filename'] = $file['data']['name'];
						$file['data']['name'] = sprintf( '%s.%s', strtolower( \Voxel\random_string(24) ), $file['data']['ext'] );
						$upload_dir = 'voxel_private';
						$insert_attachment_args['post_status'] = 'private';
						$insert_attachment_args['_skip_metadata'] = true;
					}

					$uploaded_file = \Voxel\Utils\File_Uploader::upload( $file['data'], apply_filters( 'voxel/file-field/upload-args', [
						'upload_dir' => $upload_dir,
						'skip_subdir' => $skip_subdir,
					], $this ) );
					$file_id = \Voxel\Utils\File_Uploader::create_attachment(
						$uploaded_file,
						apply_filters( 'voxel/file-field/attachment-args', $insert_attachment_args, $this )
					);
					$file_ids[] = $file_id;
				} catch ( \Exception $e ) {
					throw( $e );
				}
			} elseif ( $file['source'] === 'existing' ) {
				$file_ids[] = $file['file_id'];

				if ( ! $this->get_prop('private_upload') && ! wp_get_attachment_metadata( $file['file_id'] ) ) {
					require_once ABSPATH.'wp-admin/includes/file.php';
					require_once ABSPATH.'wp-admin/includes/media.php';
					require_once ABSPATH.'wp-admin/includes/image.php';

					wp_update_attachment_metadata(
						$file['file_id'],
						wp_generate_attachment_metadata( $file['file_id'], get_attached_file( $file['file_id'] ) )
					);
				}
			}
		}

		return $file_ids;
	}

	protected function get_allowed_types() {
		return (array) $this->props['allowed-types'];
	}
}
