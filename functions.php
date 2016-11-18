<?php

use WPOOP\Model\Post;
use WPOOP\Factory\Posts;

#region Queries
/**
 * Querys posts and returns them in a specific format.
 *
 * @param array $args
 * @param string $return_type
 * @param bool $force Bypasses instance caching and forces a fresh query.
 *
 * @return WP_Query|array
 */
function wpoop_get_posts( $args = array(), $return_type = 'query', $force = false ) {
	return Posts::instance( $args, $force )->return_as( $return_type );
}

/**
 * Alias for wp_reset_postdata() function in case we need to do extra cleanup.
 */
function wpoop_reset_postdata() {
	wp_reset_postdata();
}
#endregion

#region Getters
/**
 * Return the post id.
 *
 * @param int $post_id
 *
 * @return int
 */
function wpoop_get_post_id( $post_id = 0 ) {


	global $wp_query;

	if ( is_singular( 'post' ) && ! in_the_loop() && isset( $wp_query->queried_object->ID ) ) {
		$_post_id = $wp_query->queried_object->ID;

	} elseif ( is_singular( 'post' ) && in_the_loop() ) {
		$_post_id = get_the_ID();
	} elseif ( ! empty( $post_id ) && is_numeric( $post_id ) ) {
		$_post_id = $post_id;
	} else {
		$_post_id = 0;
	}

	return (int) apply_filters( 'wpoop_get_post_id', (int) $_post_id, $post_id );
}

/**
 * @param int $post_id
 *
 * @return Post
 */
function wpoop_get_post( $post_id = 0 ) {
	return Post::instance( wpoop_get_post_id( $post_id ) );
}
#endregion

