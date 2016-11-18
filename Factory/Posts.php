<?php


namespace WPOOP\Factory;

use WPOOP\Abstracts\Factory\Query;
use WPOOP\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Posts extends Query {

	/**
	 * @var string class name of model.
	 */
	protected $model = '\WPOOP\Model\Post';

	public function query_args( $args = array() ) {

		$args['post_type'] = 'post';

		// Ordering
		$orderby = array();

		// Meta Query
		if ( ! isset( $args['meta_query'] ) ) {
			$args['meta_query'] = array(
				'relation' => 'AND',
			);
		}

		/**
		 * Looking for specific posts. No need for filtering.
		 */
		if ( isset( $args['ids'] ) ) {
			$args['post__in'] = is_array( $args['ids'] ) ? $args['ids'] : explode( ',', $args['ids'] );
			$args['post__in'] = array_map( 'trim', $args['post__in'] );
		}

		/**
		 * If not looking for specific topics begin filtering.
		 */
		if ( empty( $args['post__in'] ) ) {


			// Topics to get per page
			if ( ! isset( $args['posts_per_page'] ) ) {
				$args['posts_per_page'] = Options::get( 'topics_per_page', 20 );
			}
		}


		/**
		 * Apply easy ordering options or allow setting it manually.
		 */
		if ( ! isset( $args['orderby'] ) ) {
			$orderby['post_modified'] = isset( $args['order'] ) ? $args['order'] : 'DESC';
		} elseif ( $args['post__in'] && in_array( $args['orderby'], array( 'post__in', 'user_order' ) ) ) {
			// This one can't be part of an $orderby array so needs to override.
			$orderby = 'post__in';
		} else {
			switch ( $args['orderby'] ) {
				case 'name':
					$orderby['post_title'] = isset( $args['order'] ) ? $args['order'] : 'ASC';
					break;
				case 'date':
					$orderby['post_date'] = isset( $args['order'] ) ? $args['order'] : 'DESC';
					break;
				default:
					$orderby[ $args['orderby'] ] = isset( $args['order'] ) ? $args['order'] : 'DESC';
					break;
			}
		}

		// Replace the orderby property with the new $orderby array.
		$args['orderby'] = $orderby;

		// Clear unneeded values.
		unset( $args['order'], $args['type'], $args['ids'] );

		return $args;
	}

}
