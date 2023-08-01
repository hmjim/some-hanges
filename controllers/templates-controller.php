<?php

namespace Voxel\Controllers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Templates_Controller extends Base_Controller {

	protected function hooks() {
		$this->on( 'admin_menu', '@add_menu_page' );
		$this->filter( 'display_post_states', '@display_template_labels', 100, 2 );

		$this->on( 'voxel_ajax_backend.update_template_id', '@update_template_id' );
	}

	protected function add_menu_page() {
		add_menu_page(
			__( 'Templates', 'voxel-backend' ),
			__( 'Templates', 'voxel-backend' ),
			'manage_options',
			'voxel-templates',
			function() {
				$this->create_missing_templates();

				$config = [
					'tab' => $_GET['tab'] ?? 'general',
					'templates' => $this->get_templates_config(),
					'editLink' => admin_url( 'post.php?post={id}&action=elementor' ),
					'previewLink' => home_url( '/?p={id}' ),
				];

				wp_enqueue_script('vx:template-manager.js');
				require locate_template( 'templates/backend/templates.php' );
			},
			\Voxel\get_image('post-types/ic_tmpl.png'),
			'0.278'
		);
	}

	protected function create_missing_templates() {
		$templates = \Voxel\get( 'templates' );

		// header
		if ( ! \Voxel\template_exists( $templates['header'] ?? '' ) ) {
			$template_id = \Voxel\create_template( 'site template: header' );
			if ( ! is_wp_error( $template_id ) ) {
				$templates['header'] = $template_id;
			}
		}

		// footer
		if ( ! \Voxel\template_exists( $templates['footer'] ?? '' ) ) {
			$template_id = \Voxel\create_template( 'site template: footer' );
			if ( ! is_wp_error( $template_id ) ) {
				$templates['footer'] = $template_id;
			}
		}

		// orders
		if ( ! \Voxel\page_exists( $templates['orders'] ?? '' ) ) {
			$template_id = \Voxel\create_page( _x( 'Orders', 'orders page title', 'voxel-backend' ) );
			if ( ! is_wp_error( $template_id ) ) {
				$templates['orders'] = $template_id;
			}
		}

		// reservations
		if ( ! \Voxel\page_exists( $templates['reservations'] ?? '' ) ) {
			$template_id = \Voxel\create_page( _x( 'Reservations', 'reservations page title', 'voxel-backend' ) );
			if ( ! is_wp_error( $template_id ) ) {
				$templates['reservations'] = $template_id;
			}
		}

		// stripe connect account
		if ( ! \Voxel\page_exists( $templates['stripe_account'] ?? '' ) ) {
			$template_id = \Voxel\create_page( _x( 'Become a Seller', 'stripe account page title', 'voxel-backend' ) );
			if ( ! is_wp_error( $template_id ) ) {
				$templates['stripe_account'] = $template_id;
			}
		}

		// login and register
		if ( ! \Voxel\page_exists( $templates['auth'] ?? '' ) ) {
			$template_id = \Voxel\create_page( _x( 'Login / Register', 'login/register page title', 'voxel-backend' ), 'auth' );
			if ( ! is_wp_error( $template_id ) ) {
				$templates['auth'] = $template_id;
			}
		}

		// pricing plans
		if ( ! \Voxel\page_exists( $templates['pricing'] ?? '' ) ) {
			$template_id = \Voxel\create_page( _x( 'Pricing', 'pricing page title', 'voxel-backend' ), 'pricing' );
			if ( ! is_wp_error( $template_id ) ) {
				$templates['pricing'] = $template_id;
			}
		}

		// current plan
		if ( ! \Voxel\page_exists( $templates['current_plan'] ?? '' ) ) {
			$template_id = \Voxel\create_page( _x( 'Current plan', 'current plan page title', 'voxel-backend' ), 'current-plan' );
			if ( ! is_wp_error( $template_id ) ) {
				$templates['current_plan'] = $template_id;
			}
		}

		// timeline
		if ( ! \Voxel\page_exists( $templates['timeline'] ?? '' ) ) {
			$template_id = \Voxel\create_page( _x( 'Timeline', 'timeline page title', 'voxel-backend' ), 'timeline' );
			if ( ! is_wp_error( $template_id ) ) {
				$templates['timeline'] = $template_id;
			}
		}

		// inbox
		if ( ! \Voxel\page_exists( $templates['inbox'] ?? '' ) ) {
			$template_id = \Voxel\create_page( _x( 'Inbox', 'inbox page title', 'voxel-backend' ), 'inbox' );
			if ( ! is_wp_error( $template_id ) ) {
				$templates['inbox'] = $template_id;
			}
		}

		// privacy policy
		$templates['privacy_policy'] = $templates['privacy_policy'] ?? (int) get_option( 'wp_page_for_privacy_policy' );
		if ( ! \Voxel\page_exists( $templates['privacy_policy'] ) ) {
			$template_id = \Voxel\create_page( _x( 'Privacy Policy', 'privacy policy page title', 'voxel-backend' ), 'privacy-policy' );
			if ( ! is_wp_error( $template_id ) ) {
				$templates['privacy_policy'] = $template_id;
			}
		}

		// terms and conditions
		if ( ! \Voxel\page_exists( $templates['terms'] ?? '' ) ) {
			$template_id = \Voxel\create_page( _x( 'Terms & Conditions', 'terms and conditions page title', 'voxel-backend' ), 'terms' );
			if ( ! is_wp_error( $template_id ) ) {
				$templates['terms'] = $template_id;
			}
		}

		// 404
		if ( ! \Voxel\template_exists( $templates['404'] ?? '' ) ) {
			$template_id = \Voxel\create_template( 'site template: 404 Page Not Found' );
			if ( ! is_wp_error( $template_id ) ) {
				$templates['404'] = $template_id;
			}
		}

		// restricted
		if ( ! \Voxel\template_exists( $templates['restricted'] ?? '' ) ) {
			$template_id = \Voxel\create_template( 'site template: Restricted Content' );
			if ( ! is_wp_error( $template_id ) ) {
				$templates['restricted'] = $template_id;
			}
		}

		// popups style kit
		if ( ! \Voxel\template_exists( $templates['kit_popups'] ?? '' ) ) {
			$template_id = \Voxel\create_template( 'style kit: popups' );
			if ( ! is_wp_error( $template_id ) ) {
				$templates['kit_popups'] = $template_id;
			}
		}

		// qr-tags
		if ( ! \Voxel\page_exists( $templates['qr_tags'] ?? '' ) ) {
			$template_id = \Voxel\create_page( _x( 'QR tags', 'qr tags - page title', 'voxel-backend' ), 'qr-tags' );
			if ( ! is_wp_error( $template_id ) ) {
				$templates['qr_tags'] = $template_id;
			}
		}

		// cleanup
		$allowed_templates = [
			'header',
			'footer',
			'orders',
			'reservations',
			'auth',
			'pricing',
			'current_plan',
			'privacy_policy',
			'terms',
			'stripe_account',
			'timeline',
			'inbox',
			'404',
			'restricted',
			'kit_popups',
			'qr_tags',
		];

		foreach ( $templates as $template => $id ) {
			if ( ! in_array( $template, $allowed_templates ) ) {
				unset( $templates[ $template ] );
			}
		}

		// save
		\Voxel\set( 'templates', $templates );
	}

	protected function display_template_labels( $states, $post ) {
		if ( $post->post_type !== 'page' ) {
			return $states;
		}

		$labels = [
			'auth' => _x( 'Auth Page', 'templates', 'voxel-backend' ),
			'pricing' => _x( 'Pricing Plans Page', 'templates', 'voxel-backend' ),
			'current_plan' => _x( 'Current Plan Page', 'templates', 'voxel-backend' ),
			'orders' => _x( 'Orders Page', 'templates', 'voxel-backend' ),
			'reservations' => _x( 'Reservations Page', 'templates', 'voxel-backend' ),
			'qr_tags' => _x( 'Order tags: QR code handler', 'templates', 'voxel-backend' ),
			'terms' => _x( 'Terms & Conditions', 'templates', 'voxel-backend' ),
			'stripe_account' => _x( 'Seller Dashboard', 'templates', 'voxel-backend' ),
		];

		$templates = \Voxel\get( 'templates', [] );
		$template = array_search( absint( $post->ID ), $templates, true );
		if ( $template && isset( $labels[ $template ] ) ) {
			$states[ 'vx:'.$template ] = $labels[ $template ];
		}

		foreach ( \Voxel\Post_Type::get_voxel_types() as $post_type ) {
			if ( $post_type->get_templates()['form'] === $post->ID ) {
				$states[ 'vx:create_post' ] = sprintf( '%s: Submit page', $post_type->get_label() );
			}
		}

		return $states;
	}

	protected function get_templates_config() {
		$templates = [
			/* General */
			[
				'category' => 'general',
				'label' => __( 'Header', 'voxel-backend' ),
				'key' => 'templates.header',
				'id' => \Voxel\get( 'templates.header' ),
				'image' => \Voxel\get_image('post-types/header.png'),
				'type' => 'template',
			],
			[
				'category' => 'general',
				'label' => __( 'Footer', 'voxel-backend' ),
				'key' => 'templates.footer',
				'id' => \Voxel\get( 'templates.footer' ),
				'image' => \Voxel\get_image('post-types/footer.png'),
				'type' => 'template',
			],
			[
				'category' => 'general',
				'label' => __( 'Timeline', 'voxel-backend' ),
				'key' => 'templates.timeline',
				'id' => \Voxel\get( 'templates.timeline' ),
				'image' => \Voxel\get_image('post-types/timeline.png'),
				'type' => 'page',
			],
			[
				'category' => 'general',
				'label' => __( 'Inbox', 'voxel-backend' ),
				'key' => 'templates.inbox',
				'id' => \Voxel\get( 'templates.inbox' ),
				'image' => \Voxel\get_image('post-types/timeline.png'),
				'type' => 'page',
			],
			[
				'category' => 'general',
				'label' => __( 'Privacy Policy', 'voxel-backend' ),
				'key' => 'templates.privacy_policy',
				'id' => \Voxel\get( 'templates.privacy_policy' ),
				'image' => \Voxel\get_image('post-types/prvc.png'),
				'type' => 'page',
			],
			[
				'category' => 'general',
				'label' => __( 'Terms & Conditions', 'voxel-backend' ),
				'key' => 'templates.terms',
				'id' => \Voxel\get( 'templates.terms' ),
				'image' => \Voxel\get_image('post-types/prvc.png'),
				'type' => 'page',
			],
			[
				'category' => 'general',
				'label' => __( '404 Not Found', 'voxel-backend' ),
				'key' => 'templates.404',
				'id' => \Voxel\get( 'templates.404' ),
				'image' => \Voxel\get_image('post-types/404.png'),
				'type' => 'template',
			],
			[
				'category' => 'general',
				'label' => __( 'Restricted content', 'voxel-backend' ),
				'key' => 'templates.restricted',
				'id' => \Voxel\get( 'templates.restricted' ),
				'image' => \Voxel\get_image('post-types/restricted.png'),
				'type' => 'template',
			],

			/* Membership */
			[
				'category' => 'membership',
				'label' => __( 'Login & registration', 'voxel-backend' ),
				'key' => 'templates.auth',
				'id' => \Voxel\get( 'templates.auth' ),
				'image' => \Voxel\get_image('post-types/login.png'),
				'type' => 'page',
			],
			[
				'category' => 'membership',
				'label' => __( 'Pricing plans', 'voxel-backend' ),
				'key' => 'templates.pricing',
				'id' => \Voxel\get( 'templates.pricing' ),
				'image' => \Voxel\get_image('post-types/plans.png'),
				'type' => 'page',
			],
			[
				'category' => 'membership',
				'label' => __( 'Current plan', 'voxel-backend' ),
				'key' => 'templates.current_plan',
				'id' => \Voxel\get( 'templates.current_plan' ),
				'image' => \Voxel\get_image('post-types/plans.png'),
				'type' => 'page',
			],

			/* Orders */
			[
				'category' => 'orders',
				'label' => __( 'Orders page', 'voxel-backend' ),
				'key' => 'templates.orders',
				'id' => \Voxel\get( 'templates.orders' ),
				'image' => \Voxel\get_image('post-types/orders.png'),
				'type' => 'page',
			],
			[
				'category' => 'orders',
				'label' => __( 'Reservations page', 'voxel-backend' ),
				'key' => 'templates.reservations',
				'id' => \Voxel\get( 'templates.reservations' ),
				'image' => \Voxel\get_image('post-types/orders.png'),
				'type' => 'page',
			],
			[
				'category' => 'orders',
				'label' => __( 'Stripe Connect account', 'voxel-backend' ),
				'key' => 'templates.stripe_account',
				'id' => \Voxel\get( 'templates.stripe_account' ),
				'image' => \Voxel\get_image('post-types/orders.png'),
				'type' => 'page',
			],
			[
				'category' => 'orders',
				'label' => __( 'Order tags: QR code handler', 'voxel-backend' ),
				'key' => 'templates.qr_tags',
				'id' => \Voxel\get( 'templates.qr_tags' ),
				'image' => \Voxel\get_image('post-types/orders.png'),
				'type' => 'page',
			],

			/* Style kits */
			[
				'category' => 'style_kits',
				'label' => __( 'Popup styles', 'voxel-backend' ),
				'key' => 'templates.kit_popups',
				'id' => \Voxel\get( 'templates.kit_popups' ),
				'image' => \Voxel\get_image('post-types/orders.png'),
				'type' => 'template',
			],
		];


		foreach ( \Voxel\Post_Type::get_voxel_types() as $post_type ) {
			$tpl = $post_type->get_templates();

			$templates[] = [
				'category' => 'post_type:'.$post_type->get_key(),
				'label' => __( 'Single page', 'voxel-backend' ),
				'key' => 'post_types:'.$post_type->get_key().'.single',
				'post_type' => $post_type->get_key(),
				'id' => $tpl['single'],
				'image' => \Voxel\get_image('post-types/single.png'),
				'type' => 'template',
			];

			$templates[] = [
				'category' => 'post_type:'.$post_type->get_key(),
				'label' => __( 'Preview card', 'voxel-backend' ),
				'key' => 'post_types:'.$post_type->get_key().'.card',
				'post_type' => $post_type->get_key(),
				'id' => $tpl['card'],
				'image' => \Voxel\get_image('post-types/pcard.png'),
				'type' => 'template',
			];

			$templates[] = [
				'category' => 'post_type:'.$post_type->get_key(),
				'label' => __( 'Archive page', 'voxel-backend' ),
				'key' => 'post_types:'.$post_type->get_key().'.archive',
				'post_type' => $post_type->get_key(),
				'id' => $tpl['archive'],
				'image' => \Voxel\get_image('post-types/archive.png'),
				'type' => 'template',
			];

			$templates[] = [
				'category' => 'post_type:'.$post_type->get_key(),
				'label' => __( 'Submit page', 'voxel-backend' ),
				'key' => 'post_types:'.$post_type->get_key().'.form',
				'post_type' => $post_type->get_key(),
				'id' => $tpl['form'],
				'image' => \Voxel\get_image('post-types/submit.png'),
				'type' => 'page',
			];
		}

		return $templates;
	}

	protected function update_template_id() {
		try {
			if ( ! current_user_can( 'manage_options' ) ) {
				throw new \Exception( __( 'Invalid request.', 'voxel-backend' ) );
			}

			$templates = $this->get_templates_config();
			$template_key = $_GET['template_key'] ?? null;
			$new_template_id = $_GET['new_template_id'] ?? null;

			if ( empty( $template_key ) || ! is_numeric( $new_template_id ) || $new_template_id < 1 ) {
				throw new \Exception( __( 'Enter the ID of the new template.', 'voxel-backend' ) );
			}

			$new_template_id = absint( $new_template_id );
			$filtered = array_filter( $templates, function( $tpl ) use ( $template_key ) {
				return $tpl['key'] === $template_key;
			} );
			$template = array_shift( $filtered );

			if ( ! $template ) {
				throw new \Exception( __( 'Could not find requested template.', 'voxel-backend' ) );
			}

			if ( str_starts_with( $template['key'], 'templates.' ) ) {
				if ( $template['type'] === 'page' && ! \Voxel\page_exists( $new_template_id ) ) {
					throw new \Exception( __( 'Provided page template does not exist.', 'voxel-backend' ) );
				} elseif ( $template['type'] === 'template' && ! \Voxel\template_exists( $new_template_id ) ) {
					throw new \Exception( __( 'Provided template does not exist.', 'voxel-backend' ) );
				}

				\Voxel\set( $template['key'], $new_template_id );
			}

			if ( str_starts_with( $template['key'], 'post_types:' ) ) {
				$post_type = \Voxel\Post_Type::get( $template['post_type'] );
				if ( ! $post_type ) {
					throw new \Exception( __( 'Post type not found.', 'voxel-backend' ) );
				}

				if ( $template['type'] === 'page' && ! \Voxel\page_exists( $new_template_id ) ) {
					throw new \Exception( __( 'Provided page template does not exist.', 'voxel-backend' ) );
				} elseif ( $template['type'] === 'template' && ! \Voxel\template_exists( $new_template_id ) ) {
					throw new \Exception( __( 'Provided template does not exist.', 'voxel-backend' ) );
				}

				$post_type_templates = $post_type->get_templates();
				if ( str_ends_with( $template['key'], '.single' ) ) {
					$post_type_templates['single'] = $new_template_id;
				} elseif ( str_ends_with( $template['key'], '.card' ) ) {
					$post_type_templates['card'] = $new_template_id;
				} elseif ( str_ends_with( $template['key'], '.archive' ) ) {
					$post_type_templates['archive'] = $new_template_id;
				} elseif ( str_ends_with( $template['key'], '.form' ) ) {
					$post_type_templates['form'] = $new_template_id;
				}

				$post_type->repository->set_config( [
					'templates' => $post_type_templates,
				] );
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
}
