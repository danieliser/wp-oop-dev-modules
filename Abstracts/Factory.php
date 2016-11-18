<?php


namespace WPOOP\Abstracts;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


abstract class Factory {

	public static $instances = array();

	public static function instance( $args = array(), $force = false ) {
		$key = md5( serialize( $args ) );
		if ( ! isset( static::$instances[ $key ] ) || $force ) {
			static::$instances[ $key ] = new static( $args );
		}

		return static::$instances[ $key ];
	}

	abstract public function __construct( $args = array() );

}
