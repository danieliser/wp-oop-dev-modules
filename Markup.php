<?php


namespace WPOOP;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Markup {

	/**
	 * Output markup conditionally.
	 *
	 * Supported keys for `$args` are:
	 *
	 *  - `tag` (`sprintf()` pattern markup),
	 *  - `context` (name of context),
	 *  - `echo` (default is true).
	 *
	 * Applies a `wpoop_markup_{context}` filter early to allow shortcutting the function.
	 *
	 * Applies a `wpoop_markup_{context}_output` filter at the end.
	 *
	 * @since 1.0.0
	 *
	 * @uses wpoop_attr()  Contextual attributes.
	 *
	 * @param array $args Array of arguments.
	 *
	 * @param array $attributes
	 *
	 * @return string|void Markup.
	 */
	public static function markup( $args = array(), $attributes = array() ) {

		$defaults = array(
			'tag'     => '',
			'context' => '',
			'echo'    => true,
		);

		$args = wp_parse_args( $args, $defaults );

		//* Short circuit filter
		$pre = apply_filters( "wpoop_markup_{$args['context']}", false, $args, $attributes );
		if ( false !== $pre ) {
			return $pre;
		}

		if ( ! $args['tag'] ) {
			return '';
		}

		$tag = $args['context'] ? sprintf( $args['tag'], static::attr( $args['context'], $attributes, false ) ) : $args['tag'];

		//* Contextual filter
		$tag = $args['context'] ? apply_filters( "wpoop_markup_{$args['context']}_output", $tag, $args ) : $tag;

		if ( $args['echo'] ) {
			echo $tag;
		} else {
			return $tag;
		}
	}

	/**
	 * Build list of attributes into a string and apply contextual filter on string.
	 *
	 * The contextual filter is of the form `wpoop_attr_{context}_output`.
	 *
	 * @since 1.0.0
	 *
	 * @uses wpoop_parse_attr() Merge array of attributes with defaults, and apply contextual filter on array.
	 *
	 * @param  string $context The context, to build filter name.
	 * @param  array $attributes Optional. Extra attributes to merge with defaults.
	 * @param  bool $echo
	 *
	 * @return string String of HTML attributes and values.
	 */
	public static function attr( $context, $attributes = array(), $echo = true ) {

		$attributes = static::parse_attr( $context, $attributes );

		if ( isset( $attributes['class'] ) ) {
			if ( ! is_array( $attributes['class'] ) ) {
				$attributes['class'] = explode( ' ', $attributes['class'] );
			}
		}


		if ( ! in_array( $context, $attributes['class'] ) ) {
			$attributes['class'][] = $context;
		}

		$output = '';

		//* Cycle through attributes, build tag attribute string
		foreach ( $attributes as $key => $value ) {
			if ( ! $value ) {
				continue;
			}

			switch ( $key ) {
				case 'class':
					$value = implode( ' ', (array) $value );
					$output .= sprintf( '%s="%s" ', esc_html( $key ), esc_attr( $value ) );
					break;
				case 'data':
					if ( ! is_array( $value ) ) {
						continue;
					}
					foreach ( $value as $name => $data ) {
						$output .= sprintf( 'data-%s="%s" ', esc_html( $name ), esc_attr( $data ) );
					}
					break;

				default:
					$output .= sprintf( '%s="%s" ', esc_html( $key ), esc_attr( $value ) );
					break;
			}

		}

		$output = trim( apply_filters( "wpoop_attr_{$context}_output", $output, $attributes, $context ) );

		if ( $echo ) {
			echo $output;
		} else {
			return $output;
		}
	}

	/**
	 * Merge array of attributes with defaults, and apply contextual filter on array.
	 *
	 * The contextual filter is of the form `wpoop_attr_{context}`.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $context The context, to build filter name.
	 * @param  array $attributes Optional. Extra attributes to merge with defaults.
	 *
	 * @return array Merged and filtered attributes.
	 */
	public static function parse_attr( $context, $attributes = array() ) {

		$defaults   = array(
			'class' => sanitize_html_class( $context ),
		);
		$attributes = wp_parse_args( $attributes, $defaults );

		//* Contextual filter

		return apply_filters( "wpoop_attr_{$context}", $attributes, $context );

	}
}
