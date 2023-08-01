<?php

namespace Voxel\Controllers\Frontend\Auth;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Google_Controller extends \Voxel\Controllers\Base_Controller {

	protected function hooks() {
		$this->on( 'voxel_ajax_nopriv_auth.google.login', '@login_with_google' );
	}

	protected function login_with_google() {
		try {
			$state = explode( '..', $_REQUEST['state'] ?? '', 2 );
			\Voxel\verify_nonce( $state[0], 'vx_auth_google' );
			if ( empty( $_GET['code'] ) || ! \Voxel\get( 'settings.auth.google.enabled' ) ) {
				throw new \Exception( _x( 'Invalid request.', 'login with google', 'voxel' ) );
			}

			$code = $_GET['code'];
			$redirect_url = ! empty( $state[1] ) ? $state[1] : home_url('/');

			$client_id = \Voxel\get( 'settings.auth.google.client_id' );
			$client_secret = \Voxel\get( 'settings.auth.google.client_secret' );

			$response = wp_remote_post( 'https://www.googleapis.com/oauth2/v4/token', [
				'timeout' => 10,
				'headers' => 'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
				'body' => http_build_query( [
					'grant_type' => 'authorization_code',
					'client_id' => $client_id,
					'client_secret' => $client_secret,
					'redirect_uri' => home_url('/?vx=1&action=auth.google.login'),
					'code' => $code,
				] ),
			] );

			if ( is_wp_error( $response ) ) {
				\Voxel\log( 'Request to Google oAuth service failed.', $response->get_error_message() );
				throw new \Exception( _x( 'Could not retrieve data.', 'login with google', 'voxel' ) );
			}

			$data = json_decode( wp_remote_retrieve_body( $response ), ARRAY_A );
			if ( empty( $data['id_token'] ) ) {
				\Voxel\log( 'Request to Google oAuth service failed.', $data, $response );
				throw new \Exception( _x( 'Could not retrieve details.', 'login with google', 'voxel' ) );
			}

			$jwt = explode( '.', $data['id_token'] );
			$userinfo = json_decode( base64_decode( $jwt[1] ), true );

			if ( empty( $userinfo['aud'] ) || $userinfo['aud'] !== $client_id || empty( $userinfo['email'] ) ) {
				throw new \Exception( _x( 'Could not validate request.', 'login with google', 'voxel' ) );
			}

			$email = $userinfo['email'];

			// see if this account is connected to an existing user
			$users = get_users( [
			   'meta_key' => 'voxel:google_auth_id',
			   'meta_value' => $email,
			   'number' => 1,
			   'count_total' => false
			] );

			// if so, log them in
			if ( ! empty( $users ) ) {
				wp_clear_auth_cookie();
				wp_set_auth_cookie( $users[0]->ID );
				wp_safe_redirect( $redirect_url );
				exit;
			}

			// if a user with this email already exists, log them in
			if ( $user = get_user_by( 'email', $email ) ) {
				update_user_meta( $user->ID, 'voxel:google_auth_id', $email );
				wp_clear_auth_cookie();
				wp_set_auth_cookie( $user->ID );
				wp_safe_redirect( $redirect_url );
				exit;
			}

			// otherwise, insert a new user
			$email_parts = explode( '@', $email );
			$args = [
				'user_login' => $email_parts[0],
				'user_email' => $email,
				'user_pass'  => wp_generate_password(16),
				'role' => apply_filters( 'voxel/default-role', 'subscriber' ),
			];

			// if this user login is taken, append a random id for uniqueness
			if ( $user = get_user_by( 'login', $args['user_login'] ) ) {
				$args['user_login'] = sprintf( '%s_%s', $args['user_login'], strtolower( \Voxel\random_string(4) ) );
			}

			$user_id = wp_insert_user( $args );
			if ( is_wp_error( $user_id ) ) {
				throw new \Exception( $user_id->get_error_message() );
			}

			update_user_meta( $user_id, 'voxel:google_auth_id', $email );

			( new \Voxel\Events\Membership\User_Registered_Event )->dispatch( $user_id );

			wp_clear_auth_cookie();
			wp_set_auth_cookie( $user_id );

			// redirect to plans or welcome page
			if ( \Voxel\get( 'settings.membership.plans_enabled' ) && \Voxel\get( 'settings.membership.show_plans_on_signup' ) ) {
				$plans_page = get_permalink( \Voxel\get( 'templates.pricing' ) ) ?: home_url('/');
				$redirect_to = add_query_arg( 'redirect_to', rawurlencode( $redirect_url ), $plans_page );
			} else {
				$redirect_to = add_query_arg( [
					'welcome' => '',
					'redirect_to' => rawurlencode( $redirect_url ),
				], get_permalink( \Voxel\get( 'templates.auth' ) ) ?: home_url('/') );
			}

			wp_safe_redirect( $redirect_to );
			exit;
		} catch ( \Exception $e ) {
			$auth_link = get_permalink( \Voxel\get( 'templates.auth' ) ) ?: home_url('/');
			wp_safe_redirect( add_query_arg( 'redirect_to', $redirect_url, $auth_link ) );
			exit;
		}
	}
}
