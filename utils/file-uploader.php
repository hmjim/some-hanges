<?php

namespace Voxel\Utils;

if ( ! defined('ABSPATH') ) {
	exit;
}

class File_Uploader {

	private static $upload_dir;

	private static $skip_subdir;

	public static function upload( $file, $args = [] ) {
		require_once ABSPATH.'wp-admin/includes/file.php';
		require_once ABSPATH.'wp-admin/includes/media.php';
		require_once ABSPATH.'wp-admin/includes/image.php';

		$args = wp_parse_args( $args, [
			'upload_dir' => null,
			'skip_subdir' => false,
		] );

		if ( is_string( $args['upload_dir'] ) && ! empty( trim( $args['upload_dir'] ) ) ) {
			static::$upload_dir = $args['upload_dir'];
			static::$skip_subdir = $args['skip_subdir'] ?? false;
			add_filter( 'upload_dir', '\Voxel\Utils\File_Uploader::set_upload_dir', 35 );
		}

		$upload = wp_handle_upload( $file, [ 'test_form' => false ] );
		if ( ! empty( $upload['error'] ) ) {
			throw new \Exception( $upload['error'] );
		}

		remove_filter( 'upload_dir', '\Voxel\Utils\File_Uploader::set_upload_dir', 35 );

		return [
			'url' => $upload['url'],
			'path' => $upload['file'],
			'type' => $upload['type'],
			'name' => wp_basename( $upload['file'] ),
			'size' => $file['size'],
			'extension' => $file['ext'],
		];
	}

	public static function set_upload_dir( $pathdata ) {
		$dir = untrailingslashit( static::$upload_dir );

		if ( static::$skip_subdir ) {
			$pathdata['path'] = trailingslashit( $pathdata['basedir'] ) . $dir;
			$pathdata['url'] = trailingslashit( $pathdata['baseurl'] ) . $dir;
			$pathdata['subdir'] = '/'.$dir;
		} elseif ( empty( $pathdata['subdir'] ) ) {
			$pathdata['path'] = trailingslashit( $pathdata['path'] ) . $dir;
			$pathdata['url'] = trailingslashit( $pathdata['url'] ) . $dir;
			$pathdata['subdir'] = '/' . $dir;
		} else {
			$new_subdir = '/' . $dir . $pathdata['subdir'];
			$pathdata['path'] = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['path'] );
			$pathdata['url'] = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['url'] );
			$pathdata['subdir'] = $new_subdir;
		}

		return $pathdata;
	}

	public static function prepare( $key, $files ) {
		$prepared = [];
		if ( empty( $files['name'] ) || empty( $files['name'][ $key ] ) ) {
			return $prepared;
		}

		foreach ( (array) $files['name'][ $key ] as $index => $filename ) {
			$filetype = wp_check_filetype( $filename );
			$prepared[] = [
				'name' => $filename,
				'type' => $filetype['type'],
				'ext' => $filetype['ext'],
				'tmp_name' => ( (array) $files['tmp_name'][ $key ] )[ $index ],
				'error' => ( (array) $files['error'][ $key ] )[ $index ],
				'size' => ( (array) $files['size'][ $key ] )[ $index ],
			];
		}

		return $prepared;
	}

	public static function create_attachment( $uploaded_file, $args = [] ) {
		$attachment_id = wp_insert_attachment( array_merge( [
			'post_title' => $uploaded_file['name'],
			'post_content' => '',
			'post_mime_type' => $uploaded_file['type'],
			'post_status' => 'inherit',
		], $args ), $uploaded_file['path'] );

		if ( is_wp_error( $attachment_id ) ) {
			throw new \Exception( $attachment_id->get_error_message() );
		}

		if ( empty( $args['_skip_metadata'] ) ) {
			wp_update_attachment_metadata(
				$attachment_id,
				wp_generate_attachment_metadata( $attachment_id, $uploaded_file['path'] )
			);
		}

		if ( ! empty( $args['_display_filename'] ) ) {
			update_post_meta( $attachment_id, '_display_filename', sanitize_file_name( $args['_display_filename'] ) );
		}

		return $attachment_id;
	}
}
