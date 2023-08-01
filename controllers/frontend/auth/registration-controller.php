<?php

namespace Voxel\Controllers\Frontend\Auth;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Registration_Controller extends \Voxel\Controllers\Base_Controller {

	protected function hooks() {
		$this->on( 'voxel_ajax_nopriv_auth.register', '@register' );
		$this->on( 'voxel_ajax_nopriv_auth.register.resend_confirmation_code', '@resend_confirmation_code' );
	}

	protected function register() {
		try {
			\Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_auth' );
			if ( \Voxel\get('settings.recaptcha.enabled') ) {
				\Voxel\verify_recaptcha( $_REQUEST['_recaptcha'] ?? '', 'vx_register' );
			}

			$username = sanitize_user( wp_unslash( $_POST['username'] ?? '' ) );
			$email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
			$password = (string) ( $_POST['password'] ?? '' );

			$role = \Voxel\Role::get( sanitize_key( wp_unslash( $_POST['role'] ?? '' ) ) );

			if ( ! ( $role && $role->is_registration_enabled() ) ) {
				throw new \Exception( _x( 'Invalid request.', 'auth', 'voxel' ), 100 );
			}

			\Voxel\validate_username( $username );
			\Voxel\validate_user_email( $email );
			\Voxel\validate_password( $password );

			// validate registration form fields
			$registration_fields = \Voxel\Auth\validate_registration_fields( $role, json_decode( stripslashes( $_POST['postdata'] ), true ) );

			if ( ( $_POST['terms_agreed'] ?? false ) !== 'yes' ) {
				throw new \Exception( _x( 'You must agree to terms and conditions to proceed.', 'auth', 'voxel' ), 108 );
			}

			// email verification
			if ( $role->is_verification_required() ) {
				if ( isset( $_POST['_confirmation_code'] ) ) {
					\Voxel\Auth\verify_confirmation_code( $username, sanitize_text_field( $_POST['_confirmation_code'] ) );
				} else {
					\Voxel\Auth\send_confirmation_code( $username, $email );
					return wp_send_json( [
						'success' => true,
						'verification_required' => true,
					] );
				}
			}

			// create user
			$user_id = wp_insert_user( [
				'user_login' => wp_slash( $username ),
				'user_email' => wp_slash( $email ),
				'user_pass' => $password,
				'role' => $role->get_key(),
			] );

			if ( is_wp_error( $user_id ) ) {
				throw new \Exception( $user_id->get_error_message(), 109 );
			}

			$user = \Voxel\User::get( $user_id );

			$wp_user = wp_signon( [
				'user_login' => $user->get_username(),
				'user_password' => $password,
				'remember' => !! ( $_POST['remember'] ?? false ),
			], is_ssl() );

			if ( is_wp_error( $wp_user ) ) {
				throw new \Exception( wp_strip_all_tags( $wp_user->get_error_message() ), 110 );
			}

			// at this point user is confirmed, store registration fields validated previously
			\Voxel\Auth\save_registration_fields( $user, $registration_fields['fields'], $registration_fields['sanitized'] );

			do_action( 'voxel/user-registered', $user_id );
			( new \Voxel\Events\Membership\User_Registered_Event )->dispatch( $user_id );

			return wp_send_json( [
				'success' => true,
				'redirect_to' => \Voxel\Auth\get_registration_redirect( $role ),
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
				'code' => $e->getCode(),
			] );
		}
	}

	protected function resend_confirmation_code() {
		try {
			\Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_auth' );
			if ( \Voxel\get('settings.recaptcha.enabled') ) {
				\Voxel\verify_recaptcha( $_REQUEST['_recaptcha'] ?? '', 'vx_resend_confirmation_code' );
			}

			$username = sanitize_user( wp_unslash( $_POST['username'] ?? '' ) );
			$email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );

			\Voxel\validate_username( $username );
			\Voxel\validate_user_email( $email );

			\Voxel\Auth\send_confirmation_code( $username, $email );

			return wp_send_json( [
				'success' => true,
				'message' => \Voxel\replace_vars( _x( 'Confirmation code sent to @email', 'auth', 'voxel' ), [
					'@email' => $email,
				] ),
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}
}
