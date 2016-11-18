<?php


namespace WPOOP;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Config {

	/**
	 * Config
	 *
	 * @param $file_name
	 *
	 * @return mixed
	 */
	public static function load( $file_name ) {

		$file_name = str_replace( '\\', DIRECTORY_SEPARATOR, $file_name );

		$file = plugin_dir_path( __DIR__ ) . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . $file_name . '.php';

		if ( ! file_exists( $file ) ) {
			return array();
		}

		return include $file;
	}
}
