<?php

namespace WPOOP\Abstracts\Ajax;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


abstract class Event {

	/**
	 * @var \WP_Error
	 */
	public static $errors;

	public static function check_referrer() {

	}

	public static function listen() {}

	public static function _listen() {
		static::$errors = new \WP_Error;
		static::listen();
	}

	/**
	 * @param $errors \WP_Error
	 */
	public static function send_errors( $errors = null ) {
		if ( ! $errors || ! is_wp_error( $errors ) ) {
			$errors = self::$errors;
		}

		wp_send_json_error( array(
			'errors' => $errors->get_error_messages(),
		) );
		die();
	}
}
