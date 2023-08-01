<?php

namespace Voxel\Controllers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Event_Controller extends Base_Controller {

	protected function hooks() {
		$this->on( 'admin_menu', '@add_menu_page' );
		$this->on( 'voxel_ajax_app_events.save_config', '@save_config' );

		$this->filter( 'wp_mail_from', '@wp_mail_from' );
		$this->filter( 'wp_mail_from_name', '@wp_mail_from_name' );
	}

	protected function add_menu_page() {
		add_menu_page(
			__( 'App Events', 'voxel-backend' ),
			__( 'App Events', 'voxel-backend' ),
			'manage_options',
			'voxel-events',
			function() {
				$config = [
					'events' => array_map( function( $event ) {
						return $event->get_editor_config();
					}, \Voxel\Events\Base_Event::get_all() ),
					'categories' => \Voxel\Events\Base_Event::get_categories(),
				];

				$tag_groups = [];
				foreach ( \Voxel\Events\Base_Event::get_all() as $event ) {
					$groups = $event->get_dynamic_tags();
					foreach ( $groups as $group_config ) {
						$group = new $group_config['type'];

						if ( isset( $group_config['props'] ) ) {
							foreach ( $group_config['props'] as $prop_key => $prop_value ) {
								$group->{$prop_key} = $prop_value;
							}
						}

						if ( ! isset( $tag_groups[ $group->get_id() ] ) ) {
							$tag_groups[ $group->get_id() ] = [
								'key' => $group->get_key(),
								'title' => $group->get_label(),
								'properties' => $group->get_properties(),
								'methods' => array_map( function( $method ) {
									return $method->get_editor_config();
								}, $group->get_methods() ),
							];
						}

						if ( ! isset( $config['events'][ $event->get_key() ]['dynamic_tags'] ) ) {
							$config['events'][ $event->get_key() ]['dynamic_tags'] = [];
						}

						$config['events'][ $event->get_key() ]['dynamic_tags'][] = [
							'id' => $group->get_id(),
							'label' => $group->get_label(),
						];
					}
				}

				wp_enqueue_script( 'vx:app-events.js' );
				require locate_template( 'templates/backend/app-events.php' );
			},
			\Voxel\get_image('post-types/ic_not.png'),
			'0.298'
		);
	}

	protected function save_config() {
		try {
			// @todo: nonce validation
			if ( ! current_user_can( 'manage_options' ) ) {
				throw new \Exception( __( 'Permission check failed.', 'voxel-backend' ) );
			}

			$config = json_decode( stripslashes( $_POST['config'] ), true );
			$sanitized = [];

			foreach ( \Voxel\Events\Base_Event::get_all() as $event ) {
				$defaults = $event::notifications();
				$items = [];

				foreach ( ( $config[ $event->get_key() ]['notifications'] ?? [] ) as $destination => $notification ) {
					if ( ! isset( $defaults[ $destination ] ) ) {
						continue;
					}

					$items[ $destination ] = [
						'inapp' => [
							'enabled' => !! $notification['inapp']['enabled'],
							'subject' => sanitize_text_field( $notification['inapp']['subject'] ) ?: null,
						],
						'email' => [
							'enabled' => !! $notification['email']['enabled'],
							'subject' => sanitize_text_field( $notification['email']['subject'] ) ?: null,
							'message' => $notification['email']['message'] ?: null,
						],
					];
				}

				if ( ! empty( $items ) ) {
					$sanitized[ $event->get_key() ] = [
						'notifications' => $items,
					];
				}
			}

			\Voxel\set( 'events', $sanitized, false );

			return wp_send_json( [
				'success' => true,
				'message' => __( 'Changes saved.', 'voxel-backend' ),
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function wp_mail_from( $email ) {
		$custom_email = \Voxel\get( 'settings.emails.from_email' );
		if ( ! empty( $custom_email ) ) {
			return $custom_email;
		}

		return $email;
	}

	protected function wp_mail_from_name( $name ) {
		$custom_name = \Voxel\get( 'settings.emails.from_name' );
		if ( ! empty( $custom_name ) ) {
			return $custom_name;
		}

		return $name;
	}
}
