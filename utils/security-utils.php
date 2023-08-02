<?php

namespace Voxel;

if ( ! defined('ABSPATH') ) {
	exit;
}

function verify_recaptcha( $token, $action ) {
	$secret = \Voxel\get( 'settings.recaptcha.secret' );

	if ( empty( $token ) || empty( $secret ) ) {
		throw new \Exception( _x( 'Missing security token.', 'recaptcha', 'voxel' ) );
	}

	$response = wp_remote_get( add_query_arg( [
		'secret'   => $secret,
		'response' => $token,
		'remoteip' => isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'],
	], 'https://www.google.com/recaptcha/api/siteverify' ) );

	if ( is_wp_error( $response ) || empty( $response['body'] ) ) {
		throw new \Exception( _x( 'Security check failed.', 'recaptcha', 'voxel' ) );
	}

	$json = json_decode( $response['body'] );
	if ( ! ( $json && $json->success && $json->action === $action && $json->score >= 0.5 ) ) {
		throw new \Exception( _x( 'Could not verify captcha.', 'recaptcha', 'voxel' ) );
	}
}

function verify_nonce( $nonce, $action ) {
	if ( ! wp_verify_nonce( $nonce, $action ) ) {
		throw new \Exception( __( 'Could not verify request.', 'voxel' ) );
	}
}

function validate_password( $password ) {
	if ( mb_strlen( $password ) < 8 ) {
		throw new \Exception( _x( 'Password must contain at least 8 characters.', 'validate password', 'voxel' ) );
	}

	if ( ! preg_match( '/[0-9]+/', $password ) ) {
		throw new \Exception( _x( 'Password must contain at least one number.', 'validate password', 'voxel' ) );
	}

	if ( ! preg_match( '/[A-Za-z]+/', $password ) ) {
		throw new \Exception( _x( 'Password must contain at least one letter.', 'validate password', 'voxel' ) );
	}
}
