<?php

/**
 * Loop Example
 */

use WPOOP\Model\Post;

/**
 * Custom instance of WP_Query.
 */
$posts = wpoop_get_posts();

if ( $posts->have_posts() ) :

	/**
	 * Render post.
	 */
	while ( $posts->have_posts() ) : $posts->the_post();

		/**
		 * @var $post \WPOOP\Model\Post
		 *
		 * The query already uses the custom model, so the $posts->post object will be set correctly.
		 *
		 * Our custom factory will eventually override $posts->the_post() to pass
		 * the full custom model into the global $post variable but currently the_post()
		 * sets it to an instance of WP_Post.
		 */
		$post = $posts->post;

		// Or if you want a cleaner look.
		$post = wpoop_get_post();

		// Fetch a meta key using custom methods;
		$field = $post->get_meta( '_custom' );

		// We use a custom template loading function generally which allows
		// passing in variable such as the $Post_Model, but for example sake
		// we are just using get_template_part.
		get_template_part( 'content' );
	endwhile;
	wpoop_reset_postdata();

else :

	get_template_part( 'no-posts' );

endif;