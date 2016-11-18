<?php

namespace WPOOP\Abstracts\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


abstract class Table_Columns {

	public static $post_type = '';

	/**
	 * Init.
	 */
	public static function init() {
		add_filter( 'manage_' . static::$post_type . '_posts_columns', array( get_called_class(), 'columns' ) );
		add_action( 'manage_' . static::$post_type . '_posts_custom_column', array( get_called_class(), 'render_columns' ), 2 );
		add_filter( 'manage_edit-' . static::$post_type . '_sortable_columns', array( get_called_class(), 'sortable_columns' ) );
		add_action( 'load-edit.php', array( get_called_class(), 'load' ), 9999 );
	}

	/**
	 * Define custom columns for forums.
	 *
	 * @param $columns
	 *
	 * @return array
	 */
	public static function columns( $columns ) {
		return $columns;
	}

	/**
	 * Ouput custom columns for forums.
	 *
	 * @param string $column
	 */
	public static function render_columns( $column ) {
		global $post;

		switch ( $column ) {
			default :
				break;
		}
	}

	/**
	 * Registers the sortable columns in the list table
	 *
	 * @param array $columns Array of the columns
	 *
	 * @return array $columns Array of sortable columns
	 */
	public static function sortable_columns( $columns ) {
		return $columns;
	}

	/**
	 * Sorts the table.
	 */
	public static function load() {
		add_filter( 'request', array( get_called_class(), 'sort' ) );
	}

	/**
	 * Sorts Columns in the Table
	 *
	 * @param array $vars Array of all the sort variables
	 *
	 * @return array $vars Array of all the sort variables
	 */
	public static function sort( $vars ) {
		// Check if we're viewing the correct post type
		if ( isset( $vars['post_type'] ) && static::$post_type == $vars['post_type'] ) {
			// Check if 'orderby' is set to "name"
			if ( isset( $vars['orderby'] ) ) {
				switch ( $vars['orderby'] ) {
					/*
					case 'popup_title':
						$vars = array_merge( $vars, array(
							'meta_key' => 'popup_title',
							'orderby'  => 'meta_value',
						) );
						break;
					case 'opens':
						$vars = array_merge( $vars, array(
							'meta_key' => 'popup_open_count',
							'orderby'  => 'post_date',
						) );
						break;
					*/
				}
			}
		}

		return $vars;
	}

}
