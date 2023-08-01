<?php

namespace Voxel\Post_Types\Fields;

use \Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Texteditor_Field extends Base_Post_Field {

	protected $supported_conditions = ['text'];

	protected $props = [
		'type' => 'texteditor',
		'label' => 'Text Editor',
		'placeholder' => '',
		'editor-type' => 'plain-text',
		'allow-shortcodes' => false,
		'minlength' => null,
		'maxlength' => null,
	];

	public function get_models(): array {
		return [
			'label' => $this->get_label_model(),
			'key' => $this->get_key_model(),
			'placeholder' => $this->get_placeholder_model(),
			'description' => $this->get_description_model(),
			'editor-type' => $this->get_model( 'editor_type', [ 'width' => '1/2' ] ),
			'allow-shortcodes' => [
				'type' => Form_Models\Switcher_Model::class,
				'label' => 'Allow shortcodes',
				'description' => 'Set whether to render shortcodes added by the user',
				'width' => '1/2',
			],
			'minlength' => $this->get_minlength_model(),
			'maxlength' => $this->get_maxlength_model(),
			'required' => $this->get_required_model(),
		];
	}

	public function sanitize( $value ) {
		return $this->props['editor-type'] === 'plain-text'
			? sanitize_textarea_field( trim( $value ) )
			: wp_kses_post( trim( $value ) );
	}

	public function validate( $value ): void {
		$strip_tags = $this->props['editor-type'] !== 'plain-text';
		$this->validate_minlength( $value, $strip_tags );
		$this->validate_maxlength( $value, $strip_tags );
	}

	public function update( $value ): void {
		if ( $this->is_empty( $value ) ) {
			delete_post_meta( $this->post->get_id(), $this->get_key() );
		} else {
			update_post_meta( $this->post->get_id(), $this->get_key(), $value );
		}
	}

	public function get_value_from_post() {
		return get_post_meta( $this->post->get_id(), $this->get_key(), true );
	}

	private function _get_editor_config() {
		if ( $this->get_prop('editor-type') === 'plain-text' ) {
			return [];
		}

		$config = [
			'textarea_name' => $this->_get_editor_id(),
			'textarea_rows' => 8,
			'tinymce' => [
				'inline' => true,
				'fixed_toolbar_container' => sprintf( '#_toolbar-%s', $this->_get_editor_id() ),
				'paste_as_text' => true,
				'paste_auto_cleanup_on_paste' => true,
				'paste_remove_spans' => true,
				'paste_remove_styles' => true,
				'paste_remove_styles_if_webkit' => true,
				'paste_strip_class_attributes' => true,
				'content_css' => '',
			],
		];

		// basic controls
		if ( $this->get_prop('editor-type') === 'wp-editor-basic' ) {
			$config['media_buttons'] = false;
			$config['quicktags'] = false;
			$config['tinymce']['plugins'] = 'lists,paste,tabfocus,wplink,wordpress';
			$config['tinymce']['toolbar1'] = 'bold,italic,bullist,numlist,link,unlink';
		}

		// advanced controls
		if ( $this->get_prop('editor-type') === 'wp-editor-advanced' ) {
			$config['media_buttons'] = false;
			$config['quicktags'] = false;
			$tb = 'formatselect,bold,italic,bullist,numlist,link,unlink,strikethrough,hr';
			$config['tinymce']['toolbar1'] = $tb;
			$config['tinymce']['plugins'] = 'lists,paste,tabfocus,wplink,wordpress,colorpicker,hr';
		}

		return $config;
	}

	protected function frontend_props() {
		if ( $this->props['editor-type'] !== 'plain-text' ) {
			if ( ! class_exists( '_WP_Editors', false ) ) {
				require( ABSPATH . WPINC . '/class-wp-editor.php' );
			}

			wp_deregister_style( 'editor-buttons' );
			\_WP_Editors::enqueue_default_editor();
		}

		return [
			'editorId' => $this->_get_editor_id(),
			'toolbarId' => sprintf( '_toolbar-%s', $this->_get_editor_id() ),
			'placeholder' => $this->props['placeholder'] ?: $this->props['label'],
			'minlength' => $this->props['minlength'],
			'maxlength' => $this->props['maxlength'],
			'editorType' => $this->props['editor-type'],
			'editorConfig' => $this->_get_editor_config(),
		];
	}

	protected function editing_value() {
		if ( ! $this->post ) {
			return null;
		} elseif ( $this->props['editor-type'] === 'plain-text' ) {
			return $this->get_value();
		} else {
			return wpautop( $this->get_value() );
		}
	}

	protected function _get_editor_id() {
		if ( $this->repeater === null && $this->get_key() === 'description' ) {
			return 'content';
		}

		return str_replace( '.', '-', $this->get_id() );
	}

	public function exports() {
		return [
			'label' => $this->get_label(),
			'type' => \Voxel\T_STRING,
			'callback' => function() {
				if ( $this->props['editor-type'] === 'plain-text' ) {
					return $this->get_value();
				} else {
					return wpautop( $this->get_value() );
				}
			},
		];
	}

	public function export_to_personal_data() {
		if ( $this->props['editor-type'] === 'plain-text' ) {
			return $this->get_value();
		} else {
			return wpautop( $this->get_value() );
		}
	}
}
