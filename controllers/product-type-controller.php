<?php

namespace Voxel\Controllers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Product_Type_Controller extends Base_Controller {

	protected function hooks() {
		$this->on( 'voxel/backend/product-types/screen:edit-type', '@render_edit_screen', 30 );
		$this->on( 'admin_post_voxel_save_product_type_settings', '@save_settings' );
	}

	protected function render_edit_screen() {
		$key = $_GET['product_type'] ?? null;
		$product_type = \Voxel\Product_Type::get( $key );
		if ( ! ( $key && $product_type ) ) {
			return;
		}

		// dump($product_type);

		// load required assets
		wp_enqueue_script('vue');
		wp_enqueue_script('sortable');
		wp_enqueue_script('vue-draggable');
		wp_enqueue_script('vx:product-type-editor.js');

		$editor_options = [
			'addition_types' => [],
			'field_types' => [],
			'tag_props' => ( new \Voxel\Product_Types\Order_Tag([]) )->get_props(),
		];

		// additions config
		ob_start();
		foreach ( \Voxel\config('product_types.addition_types') as $addition_type => $addition_class ) {
			$addition = new $addition_class;
			$addition->set_product_type( $product_type );

			printf( '<template v-if="addition.type === \'%s\'">', $addition->get_type() );
			foreach ( $addition->get_models() as $model_key => $model_args ) {
				if ( is_callable( $model_args ) ) {
					$model_args();
				} else {
					$model_type = $model_args['type'];
					$model_args['v-model'] = sprintf( 'addition[%s]', wp_json_encode( $model_key ) );
					unset( $model_args['type'] );
					$model_type::render( $model_args );
				}
			}
			printf( '</template>' );

			$editor_options['addition_types'][ $addition->get_type() ] = (object) $addition->get_props();
		}
		$addition_options_markup = ob_get_clean();

		// information fields config
		ob_start();
		foreach ( \Voxel\config('product_types.field_types') as $field_type => $field_class ) {
			$field = new $field_class;
			$field->set_product_type( $product_type );

			printf( '<template v-if="field.type === \'%s\'">', $field->get_type() );
			foreach ( $field->get_models() as $model_key => $model_args ) {
				$model_type = $model_args['type'];
				$model_args['v-model'] = sprintf( 'field[%s]', wp_json_encode( $model_key ) );
				unset( $model_args['type'] );
				$model_type::render( $model_args );
			}
			printf( '</template>' );

			$editor_options['field_types'][ $field->get_type() ] = (object) $field->get_props();
		}
		$field_options_markup = ob_get_clean();

		// general editor config
		printf(
			'<script type="text/javascript">window.Product_Type_Options = %s;</script>',
			wp_json_encode( (object) $editor_options )
		);

		// product type config
		printf(
			'<script type="text/javascript">window.Product_Type_Config = %s;</script>',
			wp_json_encode( (object) $product_type->get_editor_config() )
		);

		require locate_template( 'templates/backend/product-types/edit-product-type.php' );
	}

	protected function save_settings() {
		check_admin_referer( 'voxel_save_product_type_settings' );
		if ( ! current_user_can( 'manage_options' ) ) {
			die;
		}

		if ( empty( $_POST['product_type_config'] ) ) {
			die;
		}

		$config = json_decode( stripslashes( $_POST['product_type_config'] ), true );
		$settings = $config['settings'];
		$product_type = \Voxel\Product_Type::get( $settings['key'] );
        if ( ! ( $settings['key'] && $product_type && json_last_error() === JSON_ERROR_NONE ) ) {
        	die;
        }

        // delete product type
        if ( ! empty( $_POST['remove_product_type'] ) && $_POST['remove_product_type'] === 'yes' ) {
        	$product_type->remove();

			wp_safe_redirect( admin_url( 'admin.php?page=voxel-product-types' ) );
			die;
        }

        // edit product type
        $product_type->set_config( [
        	'settings' => $config['settings'],
        	'calendar' => $config['calendar'] ?? [],
        	'additions' => $config['additions'] ?? [],
        	'tags' => $config['tags'] ?? [],
        	'fields' => $config['fields'] ?? [],
        	'notes' => $config['notes'] ?? [],
        	'checkout' => $config['checkout'] ?? [],
        ] );

		wp_safe_redirect( admin_url( 'admin.php?page=voxel-product-types&action=edit-type&product_type='.$product_type->get_key() ) );
		die;
	}
}
