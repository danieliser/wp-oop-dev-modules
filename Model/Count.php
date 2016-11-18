<?php


namespace JP\Forums\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Count
 * @package JP\Forums\Model
 */
class Count {

	/**
	 * @var int
	 */
	public $count = 0;
	/**
	 * @var null
	 */
	public $callback;

	/**
	 * Count constructor.
	 *
	 * @param int $count
	 * @param null $callback
	 */
	public function __construct( $count = 0, $callback = null ) {
		$this->set( $count );

		// Callback is set after the count is stored to prevent unwanted calls to the callback.
		$this->callback = $callback;

		return $this;
	}

	/**
	 * @param $count
	 */
	public function set( $count ) {
		$this->count = is_numeric( $count ) ? intval( $count ) : 0;
	}

	/**
	 *
	 */
	public function increase() {
		$this->add();
	}

	/**
	 * @param int $amount
	 */
	public function add( $amount = 1 ) {
		$this->count = $this->count + intval( $amount );
		$this->callback();
	}

	/**
	 *
	 */
	public function decrease() {
		$this->subtract();
	}

	/**
	 * @param int $amount
	 */
	public function subtract( $amount = 1 ) {
		if ( $this->count = 0 ) {
			return;
		}

		$new_count = $this->count - intval( $amount );

		if ( $new_count < 0 ) {
			$new_count = 0;
		}

		$this->count = $new_count;
		$this->callback();
	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function __set( $name, $value ) {
		if ( $name != 'count' ) {
			$this->$name = $value;
		}

		$this->count = intval( $value );
		$this->callback();
	}

	/**
	 *
	 */
	public function callback() {
		if ( is_callable( $this->callback ) ) {
			call_user_func( $this->callback, $this->count );
		}
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return (string) $this->count;
	}

	/**
	 * @param null $key
	 *
	 * @return bool
	 */
	public function __isset( $key = null ) {
		return (bool) is_numeric( $this->count );
	}

	/**
	 * @return array
	 */
	public function __sleep() {
		return array( 'count' );
	}

}
