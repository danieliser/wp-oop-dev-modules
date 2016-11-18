<?php


namespace WPOOP\Abstracts;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


abstract class Notices {

	/**
	 * @var string
	 */
	protected static $option_name;

	/**
	 * @var array
	 */
	protected static $notices = array();

	/**
	 * @var array
	 */
	protected static $notices_by_type = array();

	/**
	 *
	 */
	public static function init() {
		static::load();
		add_action( 'shutdown', array( __CLASS__, 'save' ) );
	}

	public static function load() {
		static::$notices = get_option( static::$option_name, array() );

		foreach ( static::$notices as $notice ) {
			static::$notices_by_type[ $notice['type'] ][] = $notice;
		}
	}

	public static function get( $type = false ) {
		if ( ! $type ) {
			return self::$notices;
		}

		return isset( self::$notices_by_type[ $type ] ) ? self::$notices_by_type[ $type ] : array();
	}

	/**
	 * Get the count of notices added, either for all notices (default) or for one.
	 *
	 * @param bool $type
	 *
	 * @return int
	 */
	public static function count( $type = false ) {
		if ( $type ) {
			return isset( static::$notices_by_type[ $type ] ) ? count( static::$notices_by_type[ $type ] ) : 0;
		}

		return count( static::$notices );
	}

	/**
	 * Check if a notice has already been added.
	 *
	 * @param $message
	 * @param bool $type
	 *
	 * @return bool
	 */
	public static function has_notice( $message, $type = false ) {
		if ( $type ) {
			$notices = isset( static::$notices_by_type[ $type ] ) ? static::$notices_by_type[ $type ] : array();
		} else {
			$notices = static::$notices;
		}

		foreach ( $notices as $notice ) {
			if ( $message == $notice['message'] ) {
				return true;
			}
		}

		return false;
	}

	public static function render( $type = false, $group_by_type = true ) {
		if ( $type && is_string( $type ) && array_key_exists( $type, static::$notices_by_type ) ) {
			$notices = isset( static::$notices_by_type[ $type ] ) ? static::$notices_by_type[ $type ] : array();
		} else {
			$notices = static::$notices;
		}

		if ( ! $type && $group_by_type ) {
			foreach ( static::$notices_by_type as $type => $notices ) {
				foreach ( $notices as $notice ) {
					static::render_notice( $notice );
				}
			}
		} else {
			foreach ( $notices as $notice ) {
				static::render_notice( $notice );
			}
		}

		static::clear( $type );
	}

	public static function render_notice( $notice ) {}

	/**
	 * @param bool $type
	 * @param bool $force_save
	 */
	public static function clear( $type = false, $force_save = false ) {
		if ( $type && is_string( $type ) && array_key_exists( $type, static::$notices_by_type ) ) {
			unset( self::$notices_by_type[ $type ] );
			foreach ( self::$notices as $key => $notice ) {
				if ( $notice['type'] == $type ) {
					unset( self::$notices[ $key ] );
				}
			}
		} else {
			self::$notices = array();
		}

		if ( $force_save ) {
			static::save();
		}
	}

	/**
	 *
	 */
	public static function save() {
		// Notices have been cleared already, lets delete the option.
		if ( empty( self::$notices ) && get_option( static::$option_name, false ) ) {
			delete_option( static::$option_name );
		} elseif ( ! empty ( self::$notices ) ) {
			update_option( static::$option_name, self::$notices );
		}
	}

	/**
	 * Add notices from instance of WP_Errors.
	 *
	 * @param WP_Error $errors
	 */
	public static function add_wp_errors( WP_Error $errors ) {
		if ( is_wp_error( $errors ) && $errors->get_error_messages() ) {
			foreach ( $errors->errors as $code => $error ) {
				static::add( 'error', $error, $code );
			}
		}
	}

	/**
	 * @param $type string Types available are success, error, info, warning
	 * @param string $message
	 * @param string $code
	 * @param bool $dismissible
	 */
	public static function add( $type = 'success', $message = '', $code = '', $dismissible = true ) {
		$notice = array(
			'code'        => $code,
			'type'        => $type,
			'message'     => $message,
			'dismissible' => $dismissible,
		);

		$notice = apply_filters( static::$option_name . '_add_' . $type, $notice );

		static::$notices[]                  = $notice;
		static::$notices_by_type[ $type ][] = $notice;
	}

	public static function add_success( $message = '', $code = '', $dismissible = true ) {
		static::add( 'success', $message, $code, $dismissible );
	}

	public static function add_info( $message = '', $code = '', $dismissible = true ) {
		static::add( 'info', $message, $code, $dismissible );
	}

	public static function add_error( $message = '', $code = '', $dismissible = true ) {
		static::add( 'error', $message, $code, $dismissible );
	}

	public static function add_warning( $message = '', $code = '', $dismissible = true ) {
		static::add( 'warning', $message, $code, $dismissible );
	}

	public static function get_wp_errors( $type = false, $clear = false ) {
		$errors = new WP_Error;

		foreach ( static::$notices as $notice ) {
			if ( $type && $notice['type'] != $type ) {
				continue;
			}

			$errors->add( $notice['code'], $notice['message'], $notice );
		}

		if ( $clear ) {
			static::clear( $type );
		}

		return $errors;
	}

}
