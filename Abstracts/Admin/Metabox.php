<?php


namespace WPOOP\Abstracts\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


abstract class Metabox {

	public static $post_types = array();

	protected static $cap = 'edit_post';

	public static function init() {
		add_action( 'add_meta_boxes', array( get_called_class(), 'register' ) );
		add_action( 'save_post', array( get_called_class(), 'maybe_save' ), 1, 2 );
	}

	public static function register() {
		foreach ( static::$post_types as $post_type ) {
			static::add( $post_type );
		}
	}


	/**
	 * Add Meta boxes.
	 *
	 * @param $post_type
	 */
	public static function add( $post_type ) {}

	/**
	 * Save Meta boxes.
	 *
	 * @param $post_id
	 * @param $post
	 */
	public static function save( $post_id, $post ) {}

	/**
	 * Display Meta boxes.
	 */
	public static function output( $post ) {}

	/**
	 * Check if we're saving, the trigger an action based on the post type.
	 *
	 * @param  int $post_id
	 * @param  object $post
	 */
	public static function maybe_save( $post_id, $post ) {
		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		if ( ! in_array( $post->post_type, static::$post_types ) ) {
			return;
		}

		// Check the nonce
		if ( empty( $_POST['wpoop_meta_nonce'] ) || ! wp_verify_nonce( $_POST['wpoop_meta_nonce'], 'wpoop_save_data' ) ) {
			return;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( static::$cap, $post_id ) ) {
			return;
		}

		remove_action( 'save_post', array( get_called_class(), 'maybe_save' ), 1 );

		static::save( $post_id, $post );
	}


}
