<?php

/**
 * Loop Example
 */

use WPOOP\Model\Post;

$posts = wpoop_get_posts();

if ( $posts->have_posts() ) :

	/**
	 * Render post rows.
	 */
	while ( $posts->have_posts() ) : $posts->the_post();
		$post = Post::instance( $posts->post->ID );
		get_template_part( 'content' );
	endwhile;
	wpoop_reset_postdata();

else :

	get_template_part( 'no-posts' );

endif;