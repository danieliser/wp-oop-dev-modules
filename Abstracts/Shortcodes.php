<?php

namespace WPOOP\Abstracts;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Shortcodes class
 */
abstract class Shortcodes {

	public static $shortcodes = array();

	public static function add_shortcode( Shortcode $shortcode ) {
		static::$shortcodes[ $shortcode->tag ] = $shortcode;
	}

	/**
	 * @return array PUM_Shortcode
	 */
	public static function get_shortcodes() {
		return static::$shortcodes;
	}

}