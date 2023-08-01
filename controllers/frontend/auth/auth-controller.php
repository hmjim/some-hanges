<?php

namespace Voxel\Controllers\Frontend\Auth;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Auth_Controller extends \Voxel\Controllers\Base_Controller {

	protected function hooks() {
		$this->on( 'voxel_ajax_nopriv_auth.login', '@login' );
		$this->on( 'voxel_ajax_nopriv_auth.recover', '@recover' );
		$this->on( 'voxel_ajax_nopriv_auth.recover_confirm', '@recover_confirm' );
		$this->on( 'voxel_ajax_nopriv_auth.recover_set_password', '@recover_set_password' );
		$this->on( 'voxel_ajax_nopriv_auth.register', '@register' );
		$this->on( 'voxel_ajax_nopriv_auth.confirm_account', '@confirm_account' );
		$this->on( 'voxel_ajax_nopriv_auth.resend_confirmation_code', '@resend_confirmation_code' );
		$this->on( 'voxel_ajax_auth.logout', '@logout' );

		// logged-in only
		$this->on( 'voxel_ajax_auth.update_password', '@update_password' );
		$this->on( 'voxel_ajax_auth.update_email', '@update_email' );
		$this->on( 'voxel_ajax_auth.request_personal_data', '@request_personal_data' );
		$this->on( 'voxel_ajax_auth.delete_account_permanently', '@delete_account_permanently' );
	}

	protected function login() {
		try {
			\Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_auth' );
			if ( \Voxel\get('settings.recaptcha.enabled') ) {
				\Voxel\verify_recaptcha( $_REQUEST['_recaptcha'] ?? '', 'vx_login' );
			}

			$credentials = [
				'user_login' => sanitize_text_field( $_POST['username'] ?? '' ),
				'user_password' => $_POST['password'] ?? '',
				'remember' => !! ( $_POST['remember'] ?? false ),
			];

			$wp_user = wp_authenticate( $credentials['user_login'], $credentials['user_password'] );
			if ( is_wp_error( $wp_user ) ) {
				throw new \Exception( wp_strip_all_tags( $wp_user->get_error_message() ) );
			}

			// show confirm_account step if not confirmed yet
			$user = \Voxel\User::get( $wp_user );
			if ( ! $user->is_confirmed() ) {
				$user->send_confirmation_code();

				return wp_send_json( [
					'success' => true,
					'confirmed' => false,
				] );
			}

			$wp_user = wp_signon( $credentials, is_ssl() );
			if ( is_wp_error( $wp_user ) ) {
				throw new \Exception( wp_strip_all_tags( $wp_user->get_error_message() ) );
			}

			// cleanup recovery session if it exists
			delete_user_meta( $wp_user->ID, 'voxel:recovery' );
			delete_user_meta( $wp_user->ID, 'voxel:email_update' );

			return wp_send_json( [
				'success' => true,
				'confirmed' => true,
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function recover() {
		try {
			\Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_auth' );
			if ( \Voxel\get('settings.recaptcha.enabled') ) {
				\Voxel\verify_recaptcha( $_REQUEST['_recaptcha'] ?? '', 'vx_recover' );
			}

			$email = sanitize_text_field( $_POST['email'] ?? '' );

			$user = \Voxel\User::get( get_user_by( 'email', $email ) );
			if ( ! $user ) {
				throw new \Exception( _x( 'Account not found.', 'auth', 'voxel' ) );
			}

			$user->send_recovery_code();

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

	protected function recover_confirm() {
		try {
			\Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_auth' );
			if ( \Voxel\get('settings.recaptcha.enabled') ) {
				\Voxel\verify_recaptcha( $_REQUEST['_recaptcha'] ?? '', 'vx_recover_confirm' );
			}

			$email = sanitize_text_field( $_POST['email'] ?? '' );
			$code = sanitize_text_field( $_POST['code'] ?? '' );

			$user = \Voxel\User::get( get_user_by( 'email', $email ) );
			if ( ! $user ) {
				throw new \Exception( _x( 'Account not found.', 'auth', 'voxel' ) );
			}

			$user->verify_recovery_code( $code );

			// give user 5 minutes to use code to set new password
			$recovery = json_decode( get_user_meta( $user->get_id(), 'voxel:recovery', true ), ARRAY_A );
			$recovery['expires'] = time() + ( 5 * MINUTE_IN_SECONDS );
			update_user_meta( $user->get_id(), 'voxel:recovery', wp_slash( wp_json_encode( $recovery ) ) );

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

	protected function recover_set_password() {
		try {
			\Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_auth' );
			if ( \Voxel\get('settings.recaptcha.enabled') ) {
				\Voxel\verify_recaptcha( $_REQUEST['_recaptcha'] ?? '', 'vx_recover_set_password' );
			}

			$email = sanitize_text_field( $_POST['email'] ?? '' );
			$code = sanitize_text_field( $_POST['code'] ?? '' );
			$password = (string) ( $_POST['password'] ?? '' );
			$confirm_password = $_POST['confirm_password'] ?? '';

			$user = \Voxel\User::get( get_user_by( 'email', $email ) );
			if ( ! $user ) {
				throw new \Exception( _x( 'Account not found.', 'auth', 'voxel' ) );
			}

			$user->verify_recovery_code( $code );

			\Voxel\validate_password( $password );
			if ( ! is_string( $password ) || $password !== $confirm_password ) {
				throw new \Exception( _x( 'Passwords do not match.', 'auth', 'voxel' ) );
			}

			// validation passed, update user pasword
			wp_set_password( $password, $user->get_id() );
			delete_user_meta( $user->get_id(), 'voxel:recovery' );

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

	protected function register() {
		try {
			\Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_auth' );
			if ( \Voxel\get('settings.recaptcha.enabled') ) {
				\Voxel\verify_recaptcha( $_REQUEST['_recaptcha'] ?? '', 'vx_register' );
			}

			$username = sanitize_user( wp_unslash( $_POST['username'] ?? '' ) );
			$email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
			$password = (string) ( $_POST['password'] ?? '' );

			if ( ! \Voxel\get( 'settings.membership.enabled', true ) ) {
				throw new \Exception( _x( 'Invalid request.', 'auth', 'voxel' ) );
			}

			// validate username
			if ( empty( $username ) ) {
				throw new \Exception( _x( 'Please enter a username.', 'auth', 'voxel' ) );
			}

			if ( ! validate_username( $username ) ) {
				throw new \Exception( _x( 'Please enter a valid username.', 'auth', 'voxel' ) );
			}

			if ( username_exists( $username ) ) {
				throw new \Exception( _x( 'This username is already registered. Please choose another one.', 'auth', 'voxel' ) );
			}

			$illegal_user_logins = (array) apply_filters( 'illegal_user_logins', [] );
			if ( in_array( strtolower( $username ), array_map( 'strtolower', $illegal_user_logins ), true ) ) {
				throw new \Exception( _x( 'This username is not allowed.', 'auth', 'voxel' ) );
			}

			// validate email
			if ( empty( $email ) ) {
				throw new \Exception( _x( 'Please enter your email address.', 'auth', 'voxel' ) );
			}

			if ( ! is_email( $email ) ) {
				throw new \Exception( _x( 'Please enter a valid email address.', 'auth', 'voxel' ) );
			}

			if ( email_exists( $email ) ) {
				throw new \Exception( _x( 'This email is already registered.', 'auth', 'voxel' ) );
			}

			// validate password
			\Voxel\validate_password( $password );

			if ( ( $_POST['terms_agreed'] ?? false ) !== 'yes' ) {
				throw new \Exception( _x( 'You must agree to terms and conditions to proceed.', 'auth', 'voxel' ) );
			}

			// create user
			$user_id = wp_insert_user( [
				'user_login' => wp_slash( $username ),
				'user_email' => wp_slash( $email ),
				'user_pass' => $password,
				'role' => apply_filters( 'voxel/default-role', 'subscriber' ),
			] );

			if ( is_wp_error( $user_id ) ) {
				throw new \Exception( $user_id->get_error_message() );
			}

			$user = \Voxel\User::get( $user_id );

			do_action( 'voxel/user-registered', $user_id );
			( new \Voxel\Events\Membership\User_Registered_Event )->dispatch( $user_id );

			$verification_required = !! \Voxel\get( 'settings.membership.require_verification', true );
			if ( $verification_required ) {
				$user->send_confirmation_code();
				return wp_send_json( [
					'success' => true,
					'verification_required' => true,
				] );
			} else {
				$wp_user = wp_signon( [
					'user_login' => $user->get_username(),
					'user_password' => $password,
					'remember' => !! ( $_POST['remember'] ?? false ),
				], is_ssl() );

				if ( is_wp_error( $wp_user ) ) {
					throw new \Exception( wp_strip_all_tags( $wp_user->get_error_message() ) );
				}

				return wp_send_json( [
					'success' => true,
					'redirect_to' => $this->_get_registration_redirect(),
				] );
			}
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function confirm_account() {
		try {
			\Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_auth' );
			if ( \Voxel\get('settings.recaptcha.enabled') ) {
				\Voxel\verify_recaptcha( $_REQUEST['_recaptcha'] ?? '', 'vx_confirm_account' );
			}

			$code = sanitize_text_field( $_POST['code'] ?? '' );

			$credentials = [
				'user_login' => sanitize_text_field( $_POST['username'] ?? '' ),
				'user_password' => $_POST['password'] ?? '',
				'remember' => !! ( $_POST['remember'] ?? false ),
			];

			$wp_user = wp_authenticate( $credentials['user_login'], $credentials['user_password'] );
			if ( is_wp_error( $wp_user ) ) {
				throw new \Exception( wp_strip_all_tags( $wp_user->get_error_message() ) );
			}

			$user = \Voxel\User::get( $wp_user );
			$user->verify_confirmation_code( $code );
			delete_user_meta( $wp_user->ID, 'voxel:confirmation' );

			( new \Voxel\Events\Membership\User_Confirmed_Event )->dispatch( $user->get_id() );

			$wp_user = wp_signon( $credentials, is_ssl() );
			if ( is_wp_error( $wp_user ) ) {
				throw new \Exception( wp_strip_all_tags( $wp_user->get_error_message() ) );
			}

			// cleanup recovery session if it exists
			delete_user_meta( $wp_user->ID, 'voxel:recovery' );

			return wp_send_json( [
				'success' => true,
				'redirect_to' => $this->_get_registration_redirect(),
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function _get_registration_redirect() {
		// redirect to plans or welcome page
		if ( \Voxel\get( 'settings.membership.plans_enabled' ) && \Voxel\get( 'settings.membership.show_plans_on_signup' ) ) {
			$plans_page = get_permalink( \Voxel\get( 'templates.pricing' ) ) ?: home_url('/');
			return add_query_arg( 'redirect_to', '{REDIRECT_URL}', $plans_page );
		}

		if ( \Voxel\get( 'settings.membership.after_registration' ) === 'welcome_step' ) {
			return add_query_arg( [
				'welcome' => '',
				'redirect_to' => '{REDIRECT_URL}',
			], get_permalink( \Voxel\get( 'templates.auth' ) ) ?: home_url('/') );
		}

		return '{REDIRECT_URL}';
	}

	protected function resend_confirmation_code() {
		try {
			\Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_auth' );
			if ( \Voxel\get('settings.recaptcha.enabled') ) {
				\Voxel\verify_recaptcha( $_REQUEST['_recaptcha'] ?? '', 'vx_resend_confirmation_code' );
			}

			$username = sanitize_text_field( $_POST['username'] ?? '' );
			$user = \Voxel\User::get( get_user_by( 'login', $username ) );
			if ( ! $user ) {
				throw new \Exception( _x( 'Account not found.', 'auth', 'voxel' ) );
			}

			if ( $user->is_confirmed() ) {
				throw new \Exception( _x( 'Account has been confirmed already.', 'auth', 'voxel' ) );
			}

			$user->send_confirmation_code();

			return wp_send_json( [
				'success' => true,
				'message' => \Voxel\replace_vars( _x( 'Confirmation code sent to @email', 'auth', 'voxel' ), [
					'@email' => $user->get_email(),
				] ),
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function logout() {
		try {
			\Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_auth_logout' );
			wp_logout();
		} catch ( \Exception $e ) {}

		wp_safe_redirect( get_permalink( \Voxel\get( 'templates.auth' ) ) ?: home_url('/') );
		exit;
	}

	protected function update_password() {
		try {
			\Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_auth' );
			if ( \Voxel\get('settings.recaptcha.enabled') ) {
				\Voxel\verify_recaptcha( $_REQUEST['_recaptcha'] ?? '', 'vx_update_password' );
			}

			$user = \Voxel\current_user();
			if ( ! $user ) {
				throw new \Exception( _x( 'Something went wrong.', 'auth', 'voxel' ) );
			}

			$current_pw = (string) ( $_POST['current'] ?? '' );

			$wp_user = wp_authenticate( $user->get_username(), $current_pw );
			if ( is_wp_error( $wp_user ) ) {
				throw new \Exception( _x( 'Your current password is not correct.', 'auth', 'voxel' ) );
			}

			$new_pw = (string) ( $_POST['new'] ?? '' );
			$confirm_new_pw = $_POST['confirm_new'] ?? '';

			\Voxel\validate_password( $new_pw );
			if ( ! is_string( $new_pw ) || $new_pw !== $confirm_new_pw ) {
				throw new \Exception( _x( 'Passwords do not match.', 'auth', 'voxel' ) );
			}

			// validation passed, update user pasword
			wp_set_password( $new_pw, $user->get_id() );
			wp_signon( [
				'user_login' => $user->get_username(),
				'user_password' => $new_pw,
			], is_ssl() );

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

	protected function update_email() {
		try {
			\Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_auth' );
			if ( \Voxel\get('settings.recaptcha.enabled') ) {
				\Voxel\verify_recaptcha( $_REQUEST['_recaptcha'] ?? '', 'vx_update_email' );
			}

			$user = \Voxel\current_user();
			if ( ! $user ) {
				throw new \Exception( _x( 'Something went wrong.', 'auth', 'voxel' ) );
			}

			$state = sanitize_text_field( $_POST['state'] ?? null );

			if ( $state === 'send_code' ) {
				$email = sanitize_email( wp_unslash( $_POST['new'] ?? '' ) );
				if ( empty( $email ) || ! is_email( $email ) ) {
					throw new \Exception( _x( 'Provided email address is not valid.', 'auth', 'voxel' ) );
				}

				$user->send_email_update_code( $email );

				return wp_send_json( [
					'success' => true,
					'state' => 'verify_code',
				] );
			} else {
				$code = sanitize_text_field( $_POST['code'] ?? null );
				$verified_email = $user->verify_email_update_code( $code );
				if ( empty( $verified_email ) || ! is_email( $verified_email ) ) {
					throw new \Exception( _x( 'Provided email address is not valid.', 'auth', 'voxel' ) );
				}

				delete_user_meta( $user->get_id(), 'voxel:email_update' );
				wp_update_user( [
					'ID' => $user->get_id(),
					'user_email' => $verified_email,
				] );

				return wp_send_json( [
					'success' => true,
					'state' => 'confirmed',
				] );
			}
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function request_personal_data() {
		try {
			\Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_auth' );
			if ( \Voxel\get('settings.recaptcha.enabled') ) {
				\Voxel\verify_recaptcha( $_REQUEST['_recaptcha'] ?? '', 'vx_request_personal_data' );
			}

			$user = \Voxel\current_user();
			if ( ! $user ) {
				throw new \Exception( _x( 'Something went wrong.', 'auth', 'voxel' ) );
			}

			wp_create_user_request( $user->get_email(), 'export_personal_data', [], 'confirmed' );

			return wp_send_json( [
				'success' => true,
				'message' => __( 'You will receive a link to download your personal data via email once the request is fulfilled.', 'voxel' ),
			] );
		} catch ( \Exception $e ) {
			return wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	protected function delete_account_permanently() {
		try {
			\Voxel\verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'vx_auth' );
			if ( \Voxel\get('settings.recaptcha.enabled') ) {
				\Voxel\verify_recaptcha(
					$_REQUEST['_recaptcha'] ?? '',
					( ( $_REQUEST['confirmed'] ?? '' ) !== 'yes' ) ? 'vx_delete_account' : 'vx_delete_account_permanently'
				);
			}

			$user = \Voxel\current_user();
			if ( ! $user ) {
				throw new \Exception( _x( 'Something went wrong.', 'auth', 'voxel' ) );
			}

			$current_pw = (string) ( $_REQUEST['password'] ?? '' );
			$wp_user = wp_authenticate( $user->get_username(), $current_pw );
			if ( is_wp_error( $wp_user ) ) {
				throw new \Exception( _x( 'Your password is not correct.', 'auth', 'voxel' ) );
			}

			if ( current_user_can('administrator') ) {
				throw new \Exception( _x( 'You cannot delete your account as an administrator.', 'auth', 'voxel' ) );
			}

			if ( ( $_REQUEST['confirmed'] ?? '' ) !== 'yes' ) {
				return wp_send_json( [
					'success' => true,
					'confirmation_code' => wp_create_nonce( 'vx_confirm_account_delete' ),
				] );
			}

			\Voxel\verify_nonce( $_REQUEST['confirmation_code'] ?? '', 'vx_confirm_account_delete' );

			require_once ABSPATH.'wp-admin/includes/user.php';

			global $wpdb;

			$user_id = $user->get_id();

			wp_delete_user( $user_id );

			// ensure compatibility with multisite
			$meta = $wpdb->get_col( $wpdb->prepare( "SELECT umeta_id FROM $wpdb->usermeta WHERE user_id = %d", $user_id ) );
			foreach ( $meta as $mid ) {
				delete_metadata_by_mid( 'user', $mid );
			}

			$wpdb->delete( $wpdb->users, [ 'ID' => $user_id ] );

			// clear any authentication cookies
			wp_logout();

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
