<?php

namespace WPOOP\Model;

use WP_Error;
use WP_Post;
use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Post {

	/**
	 * The post ID
	 *
	 * @since 1.0.0
	 */
	public $ID = 0;
	/**
	 * Declare the default properties in WP_Post as we can't extend it
	 *
	 * @since 1.0.0
	 */
	public $post_author = 0;
	public $post_date = '0000-00-00 00:00:00';
	public $post_date_gmt = '0000-00-00 00:00:00';
	public $post_content = '';
	public $post_title = '';
	public $post_excerpt = '';
	public $post_status = 'publish';
	public $comment_status = 'open';
	public $ping_status = 'open';
	public $post_password = '';
	public $post_name = '';
	public $to_ping = '';
	public $pinged = '';
	public $post_modified = '0000-00-00 00:00:00';
	public $post_modified_gmt = '0000-00-00 00:00:00';
	public $post_content_filtered = '';
	public $post_parent = 0;
	public $guid = '';
	public $menu_order = 0;
	public $post_mime_type = '';
	public $comment_count = 0;
	public $filter;

	/**
	 * The original WP_Post object
	 *
	 * @since 1.0.0
	 */
	public $post;

	/**
	 * The post meta array.
	 *
	 * @since 1.0.0
	 */
	public $meta;

	/**
	 * The required post type of the object.
	 *
	 * @since 1.0.0
	 */
	protected $required_post_type = false;

	/**
	 * Whether the object is valid.
	 *
	 * @since 1.0.0
	 */
	protected $valid = true;

	/**
	 * Get things going
	 *
	 * @since 1.0.0
	 *
	 * @param int $post
	 * @param bool $autoload_meta
	 * @param array $_args
	 *
	 * @internal param bool $_id
	 */
	public function __construct( $post = 0, $autoload_meta = true, $_args = array() ) {
		if ( ! is_a( $post, 'WP_Post' ) ) {
			$post = WP_Post::get_instance( $post );
		}

		$this->_setup( $post, $autoload_meta );

		return $this;
	}

	/**
	 * Given the post data, let's set the variables
	 *
	 * @since  1.0.0
	 *
	 * @param  object $post The Post Object
	 * @param bool $autoload_meta
	 */
	private function _setup( $post, $autoload_meta = true ) {
		if ( ! is_object( $post ) || ! is_a( $post, 'WP_Post' ) || ( $this->required_post_type && $this->required_post_type !== $post->post_type ) ) {
			$this->valid = false;

			return;
		}

		$this->post = $post;

		foreach ( get_object_vars( $post ) as $key => $value ) {
			$this->$key = $value;
		}

		if ( $autoload_meta ) {
			$this->get_post_meta( true );
		}

		$this->setup();
	}

	/**
	 * Set/get the post meta for this object
	 *
	 * The $force parameter is in place to prevent hitting the database each time the method is called
	 * when we already have what we need in $this->meta
	 *
	 * @link    https://developer.wordpress.org/reference/functions/get_post_meta
	 *
	 * @param bool $force Whether to force load the post meta (helpful if $this->meta is already an array).
	 *
	 * @return array
	 * @since    1.0.0
	 */
	public function get_post_meta( $force = false ) {
		# make sure we have an ID
		if ( ! $this->ID ) {
			return array();
		}
		# if $this->meta is already an array
		if ( is_array( $this->meta ) ) {

			# return the array if we're not forcing the post meta to load
			if ( ! $force ) {
				return $this->meta;
			}
		} # if $this->meta isn't an array yet, initialize it as one
		else {
			$this->meta = array();
		}
		# get all post meta for the post
		$post_meta = get_post_meta( $this->ID );
		# if we found nothing
		if ( ! $post_meta ) {
			return $this->meta;
		}
		# loop through and clean up singleton arrays
		foreach ( $post_meta as $k => $v ) {
			# need to grab the first item if it's a single value
			if ( count( $v ) == 1 ) {
				$this->meta[ $k ] = maybe_unserialize( $v[0] );
			} # or store them all if there are multiple
			else {
				$this->meta[ $k ] = $v;
			}
		}

		return $this->meta;
	}

	public function setup() {
	}

	/**
	 * Retrieve WP_Post instance.
	 *
	 * @param int $post_id Post ID.
	 * @param bool $autoload_meta
	 *
	 * @return $this
	 */
	public static function instance( $post_id, $autoload_meta = true ) {

		$post = \WP_Post::get_instance( $post_id );

		return new static( $post, $autoload_meta );
	}

	/**
	 * Get an array of new instances of this class (or an extension class) by meta_key value or values
	 *
	 * This method allows us to get posts via WP_Query, while also passing in key/value pairs and a 'meta_relation'
	 * argument to the same array
	 *
	 * The net effect is that we can easily get extended posts, complete with postmeta, by meta_key in a way
	 * that allows any arguments necessary from WP_Query
	 *
	 * If more control is needed over the meta_query item, you can
	 *
	 *        - use self::get() (a more basic wrapper for WP_Query) and pass in the meta_query manually
	 *        - use the '_get_posts_by' hook to access the query arguments
	 *        - use a normal WP_Query or get_posts; and then for each $post, create a new Helping_Friendly_Post( $post )
	 *
	 * @param    array $args {
	 *
	 *        Arguments for getting posts.  Besides the keys given, any arguments for WP_Query can also be included.
	 *
	 *        'meta_relation'
	 *        'meta' => array(
	 *            'meta_key_1' => 'value1',
	 *            'meta_key_2' => array( 'value2', 'value3' ),
	 *            ...
	 *        )
	 *    }
	 *
	 * @param    bool $autoload_post_meta Used when constructing the class instance
	 *
	 * @return    array
	 * @since    1.0.0
	 */
	public static function get_by( $args, $autoload_post_meta = true ) {
		$defaults = array(
			'posts_per_page' => - 1,
			'meta_relation'  => 'OR',
		);

		$args = wp_parse_args( $args, $defaults );

		$meta_query = array();
		if ( ! empty( $args['meta'] ) ) {
			foreach ( $args['meta'] as $k => $v ) {
				# if the key is not in our default array, we'll consider it a post meta key
				if ( ! in_array( $k, array_keys( $defaults ) ) ) {

					# the new item we'll add to meta_query
					$new_meta_query_item = array( 'key' => $k, 'value' => $v );

					# if we have an array of values
					if ( is_array( $v ) ) {
						$new_meta_query_item['compare'] = 'IN';
					} else {
						$new_meta_query_item['compare'] = '=';
					}
					$meta_query[] = $new_meta_query_item;
				}
			}
		}

		if ( ! empty( $meta_query ) ) {
			$meta_query['relation'] = $args['meta_relation'];
			$args['meta_query']     = $meta_query;
		}

		unset( $args['meta'], $args['meta_relation'] );

		return static::get( $args, $autoload_post_meta );
	}

	/**
	 * Get an array of new instances of this class (or an extension class), as a wrapper for a new WP_Query
	 *
	 * @param    array $wp_query_args Arguments to use for the WP_Query
	 * @param    bool $autoload_post_meta Used when constructing the class instance
	 *
	 * @return    array
	 * @since    1.0.0
	 */
	public static function get( $wp_query_args, $autoload_post_meta = true ) {
		$defaults = array(
			'posts_per_page' => - 1,
		);

		$wp_query_args = wp_parse_args( $wp_query_args, $defaults );

		$query = new WP_Query( $wp_query_args );

		$out = array();

		foreach ( $query->posts as $post ) {
			$out[] = new static( $post, $autoload_post_meta );
		}

		return $out;
	}

	/**
	 * Get an array of new instances of this class (or an extension class), as a wrapper for a new WP_Query
	 *
	 * @param    array $wp_query_args Arguments to use for the WP_Query
	 * @param    bool $autoload_post_meta Used when constructing the class instance
	 *
	 * @return    WP_Query
	 * @since    1.0.0
	 */
	public static function query( $wp_query_args, $autoload_post_meta = true ) {
		$defaults = array(
			'posts_per_page' => - 1,
		);

		$wp_query_args = wp_parse_args( $wp_query_args, $defaults );

		$query = new WP_Query( $wp_query_args );

		foreach ( $query->posts as $key => $post ) {
			$query->posts[ $key ] = new static( $post, $autoload_post_meta );
		}

		return $query;
	}


	/**
	 * Magic __get function to dispatch a call to retrieve a private property
	 *
	 * @since 1.0.0
	 *
	 * @param $key
	 *
	 * @return mixed|WP_Error
	 */
	public function __get( $key ) {

		if ( method_exists( $this, 'get_' . $key ) ) {

			return call_user_func( array( $this, 'get_' . $key ) );

		} else {

			$meta = get_post_meta( $this->ID, $key, true );

			if ( $meta ) {
				return $meta;
			}

			return new WP_Error( 'post-invalid-property', sprintf( __( 'Can\'t get property %s' ), $key ) );

		}

	}

	/**
	 * Convert object to array.
	 *
	 * @since 1.0.0
	 *
	 * @return array Object as array.
	 */
	public function to_array() {
		$post = get_object_vars( $this );

		return $post;
	}


	/**
	 * Is object valid.
	 *
	 * @since 1.0.0
	 *
	 * @return bool.
	 */
	public function is_valid() {
		return $this->valid;
	}

	public function get_meta( $key, $single = true ) {
		if ( isset ( $this->meta[ $key ] ) ) {
			return $this->meta[ $key ];
		}

		return get_post_meta( $this->ID, $key, $single );
	}

}
