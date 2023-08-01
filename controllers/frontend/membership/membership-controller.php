<?php

namespace Voxel\Controllers\Frontend\Membership;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Membership_Controller extends \Voxel\Controllers\Base_Controller {

	protected function hooks() {
		$this->on( 'voxel_ajax_plans.checkout.successful', '@checkout_successful' );
		$this->on( 'voxel/membership/pricing-plan-updated', '@unpublish_posts_over_the_limit', 10 );
		$this->on( 'voxel/membership/pricing-plan-updated', '@trigger_app_event', 100, 3 );
	}

	protected function checkout_successful() {
		$session_id = $_GET['session_id'] ?? null;
		if ( ! ( $session_id && is_user_logged_in() ) ) {
			die;
		}

		$user = \Voxel\current_user();
		$last_session_id = get_user_meta( $user->get_id(), 'voxel:tmp_last_session_id', true );

		// update plan information in case webhook hasn't been triggered yet
		if ( wp_verify_nonce( $_GET['_wpnonce'] ?? '', 'vx_pricing_checkout' ) && $last_session_id === $session_id ) {
			try {
				$stripe = \Voxel\Stripe::getClient();
				$membership = \Voxel\current_user()->get_membership();
				$session = \Voxel\Stripe::getClient()->checkout->sessions->retrieve( $session_id );

				if ( ( $session->mode ?? null ) === 'subscription' ) {
					$subscription = $stripe->subscriptions->retrieve( $session->subscription );
					if ( $subscription ) {
						do_action( 'voxel/membership/subscription-updated', $subscription );
					}
				}

				if ( ( $session->mode ?? null ) === 'payment' ) {
					$payment_intent = $stripe->paymentIntents->retrieve( $session->payment_intent );
					if ( $payment_intent ) {
						$payment_for = $payment_intent->metadata['voxel:payment_for'];
						if ( $payment_for === 'additional_submissions' ) {
							do_action( 'voxel/additional_submissions/payment_intent.succeeded', $payment_intent );
						} else {
							do_action( 'voxel/membership/payment_intent.succeeded', $payment_intent );
						}
					}
				}

				delete_user_meta( $user->get_id(), 'voxel:tmp_last_session_id' );
			} catch ( \Exception $e ) {
				//
			}
		}

		$redirect_to = base64_decode( $_REQUEST['redirect_to'] ?? '' );

		wp_safe_redirect( $redirect_to ?: home_url( '/' ) );
		die;
	}

	protected function trigger_app_event( $user, $old_plan, $new_plan ) {
		if ( $old_plan->get_type() === 'default' && $new_plan->get_type() !== 'default' ) {
			( new \Voxel\Events\Membership\Plan_Activated_Event )->dispatch( $user->get_id() );
		} elseif ( $old_plan->get_type() !== 'default' && $new_plan->get_type() !== 'default' ) {
			( new \Voxel\Events\Membership\Plan_Switched_Event )->dispatch( $user->get_id() );
		}
	}

	protected function unpublish_posts_over_the_limit( $user ) {
		global $wpdb;

		// exclude administrators and editors from having their posts unpublished
		if ( $user->has_role( 'administrator' ) || $user->has_role( 'editor' ) ) {
			return;
		}

		$stats = $user->get_post_stats();
		$to_unpublish = [];

		foreach ( $stats as $post_type_key => $post_type_stats ) {
			// no posts to unpublish
			if ( ( $post_type_stats['publish'] ?? 0 ) < 1 ) {
				continue;
			}

			// excluded post types from unpublishing
			if ( in_array( $post_type_key, [ 'profile' ], true ) ) {
				continue;
			}

			// validate post type
			$post_type = \Voxel\Post_Type::get( $post_type_key );
			if ( ! ( $post_type && $post_type->is_managed_by_voxel() ) ) {
				continue;
			}

			$limit = $user->get_submission_limit_for_post_type( $post_type->get_key() );

			if ( $limit ) {
				// if a limit exists and has been reached, unpublish all posts above the limit
				if ( $limit->get_count() < $post_type_stats['publish'] ) {
					$to_unpublish[ $post_type->get_key() ] = ( $post_type_stats['publish'] - $limit->get_count() );
				}
			} else {
				// if a limit for this post type has not been configured, unpublish all posts
				$to_unpublish[ $post_type->get_key() ] = $post_type_stats['publish'];
			}
		}

		foreach ( $to_unpublish as $post_type_key => $unpublish_count ) {
			$unpublish_count = absint( $unpublish_count );
			if ( $unpublish_count < 1 ) {
				continue;
			}

			$unpublish_ids = $wpdb->get_col( $wpdb->prepare( <<<SQL
				SELECT ID FROM {$wpdb->posts}
				WHERE post_author = %d
					AND post_type = %s
					AND post_status = 'publish'
				ORDER BY post_date DESC
				LIMIT %d OFFSET 0
			SQL, $user->get_id(), $post_type_key, $unpublish_count ) );

			if ( empty( $unpublish_ids ) ) {
				continue;
			}

			foreach ( $unpublish_ids as $post_id ) {
				$post_id = absint( $post_id );
				if ( $post_id < 1 ) {
					continue;
				}

				wp_update_post( [
					'ID' => $post_id,
					'post_status' => 'unpublished',
				] );

				// update index
				$post = \Voxel\Post::force_get( $post_id );
				$post->should_index() ? $post->index() : $post->unindex();
			}
		}
	}
}
