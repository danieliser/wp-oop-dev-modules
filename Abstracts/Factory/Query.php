<?php


namespace WPOOP\Abstracts\Factory;

use WPOOP\Abstracts\Factory;
use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Query
 * @package WPOOP\Abstracts\Factory
 */
abstract class Query extends Factory {

	/**
	 * @var WP_Query
	 */
	public $results = array();
	public $args;
	protected $model = '';

	public function __construct( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'with_meta'           => true,
			'p'                   => null,
			'fields'              => null,
			'post_type'           => null,
			'post_status'         => 'publish',
			'post__in'            => null,
			'post__not_in'        => null,
			'post_parent'         => null,
			'post_parent__in'     => null,
			'post_parent__not_in' => null,
			'post_name__in'       => null,
			'order'               => null,
			'orderby'             => null,
			'limit'               => null,
			'offset'              => null,
			'meta_key'            => null,
			'meta_value'          => null,
			'meta_query'          => null,
			'meta_compare'        => null,
			'meta_value_num'      => null,
			'posts_per_page'      => null,
			'tax_query' => null,
		) );

		$this->query = call_user_func( array( $this->model, 'query' ), $this->query_args( $args ), $args['with_meta'] );

		$this->results = $this->query->posts;

		return $this;
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	abstract public function query_args( $args = array() );

	public function return_as( $return_type = 'raw' ) {
		switch ( $return_type ) {
			case 'array':
				return $this->as_array();
				break;

			case 'object':
				return $this->as_object();
				break;

			case 'query':
				return $this->query;
				break;

			default:
			case 'raw':
				return $this;
				break;
		}
	}

	/**
	 * Returns an array of post arrays.
	 *
	 * @return array
	 */
	public function as_array() {
		$items = array();

		foreach ( $this->results as $post ) {
			$items[] = $post->to_array();
		}

		return $items;
	}

	/**
	 * Returns an array of post objects.
	 *
	 * @return array
	 */
	public function as_object() {
		return $this->results;
	}

}
