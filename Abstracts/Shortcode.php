<?php


namespace WPOOP\Abstracts;

use WPOOP\Form;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


abstract class Shortcode {

	public $tag = '';

	/**
	 * Shortcode supports inner content.
	 *
	 * @var Form
	 */
	public $form;

	/**
	 * Shortcode supports inner content.
	 *
	 * @var bool
	 */
	public $has_content = false;

	/**
	 * @var string
	 */
	public $inner_content_section = 'general';

	/**
	 * @var int
	 */
	public $inner_content_priority = 5;

	/**
	 * Class constructor will set the needed filter and action hooks
	 */
	public function __construct( $args = array() ) {

		$form_args = array(
			'sections'          => $this->sections(),
			'field_name_format' => '{$prefix}[{$field}]',
			'field_prefix'      => 'attrs',
		);

		$this->form = new Form( $form_args );

		if ( ! did_action( 'init' ) ) {
			add_action( 'init', array( $this, 'register' ) );
		} elseif ( ! did_action( 'admin_head' ) && current_action() != 'init' ) {
			add_action( 'admin_head', array( $this, 'register' ) );
		} else {
			$this->register();
		}

		return $this;
	}

	/**
	 * @return array
	 */
	public function sections() {
		return array(
			'general' => __( 'General', 'jp-forums' ),
			'options' => __( 'Options', 'jp-forums' ),
		);
	}

	/**
	 *
	 */
	public function register() {
		add_shortcode( $this->tag, array( $this, '_handler' ) );
		add_action( 'print_media_templates', array( $this, '_template' ) );
		add_action( 'register_shortcode_ui', array( $this, 'register_shortcode_ui' ) );

		if ( is_admin() ) {
			$fields = array();

			if ( $this->has_content ) {
				$inner_content_labels                                     = $this->inner_content_labels();
				$fields[ $this->inner_content_section ]['_inner_content'] = array(
					'label'    => $inner_content_labels['label'],
					'desc'     => $inner_content_labels['description'],
					'section'  => $this->inner_content_section,
					'type'     => 'textarea',
					'priority' => $this->inner_content_priority,
				);
			}

			$fields = array_merge_recursive( $fields, $this->fields() );

			$this->form->add_fields( $fields );
		}
	}

	/**
	 * @return array
	 */
	public function inner_content_labels() {
		return array(
			'label'       => $this->label(),
			'description' => $this->description(),
		);
	}

	/**
	 * @return array
	 */
	abstract public function fields();

	/**
	 * @return string
	 */
	abstract public function label();

	/**
	 * @return string
	 */
	abstract public function description();

	/**
	 * Shortcode handler
	 *
	 * @param  array $atts shortcode attributes
	 * @param  string $content shortcode content
	 *
	 * @return string
	 */
	public function _handler( $atts, $content = null ) {
		$atts = $this->shortcode_atts( $atts );

		return $this->handler( $atts, $content );
	}

	/**
	 * @param $atts
	 *
	 * @return array
	 */
	public function shortcode_atts( $atts ) {
		return shortcode_atts( $this->defaults(), $atts, $this->tag );
	}

	/**
	 * Shortcode handler
	 *
	 * @param  array $atts shortcode attributes
	 * @param  string $content shortcode content
	 *
	 * @return string
	 */
	abstract public function handler( $atts, $content = null );

	/**
	 * @return array
	 */
	abstract public function defaults();

	/**
	 *
	 */
	abstract public function _template();

	/**
	 *
	 */
	public function register_shortcode_ui() {

		if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
			return;
		}


		$shortcode_ui_args = array(
			'label'         => $this->label(),
			'listItemImage' => $this->icon(),
			'post_type'     => $this->post_types(),
			/*
			 * Register UI for the "inner content" of the shortcode. Optional.
			 * If no UI is registered for the inner content, then any inner content
			 * data present will be backed up during editing.
			 */
			'attrs'         => array(),
		);


		if ( $this->has_content ) {
			$shortcode_ui_args['inner_content'] = $this->inner_content_labels();
		}

		if ( count( $this->fields() ) ) {
			foreach ( $this->form->get_all_fields() as $section => $fields ) {
				foreach ( $fields as $id => $field ) {

					if ( '_inner_content' == $id ) {
						continue;
					}


					//text, checkbox, textarea, radio, select, email, url, number, date, attachment, color, post_select
					switch ( $field['type'] ) {
						case 'selectox':
							$shortcode_ui_args['attrs'][] = array(
								'label'   => esc_html( $field['label'] ),
								'attr'    => $id,
								'type'    => 'select',
								'options' => $field['options'],
							);
							break;

						case 'postselect':
						case 'objectselect':
							if ( empty( $field['post_type'] ) ) {
								break;
							}
							$shortcode_ui_args['attrs'][] = array(
								'label'   => esc_html( $field['label'] ),
								'attr'    => $id,
								'type'    => 'post_select',
								'options' => isset( $field['options'] ) ? $field['options'] : array(),
								'query'   => array( 'post_type' => $field['post_type'] ),
							);
							break;

						case 'taxonomyselect':
							break;

						case 'text';
						default:
							$shortcode_ui_args['attrs'][] = array(
								'label' => $field['label'],
								'attr'  => $id,
								'type'  => 'text',
								'value' => ! empty( $field['std'] ) ? $field['std'] : '',
								//'encode' => true,
								'meta'  => array(
									'placeholder' => $field['placeholder'],
								),
							);
							break;
					}

				}
			}
		}


		/**
		 * Register UI for your shortcode
		 *
		 * @param string $shortcode_tag
		 * @param array $ui_args
		 */
		shortcode_ui_register_for_shortcode( $this->tag, $shortcode_ui_args );
	}

	/**
	 * Dashican class like: dashicons-editor-quote
	 *
	 * @return string
	 */
	abstract public function icon();

	/**
	 * @return array
	 */
	public function post_types() {
		return array( 'post', 'page' );
	}


}
