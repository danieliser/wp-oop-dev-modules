<?php


namespace WPOOP\Abstracts\Form;

use WPOOP\Form;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


abstract class Fields {

	public static $instances = array();

	public function __construct( Form $form ) {
		$this->form = $form;
	}

	public static function instance( Form $form ) {
		$key = md5( serialize( $form ) );

		if ( ! isset( static::$instances[ $key ] ) ) {
			static::$instances[ $key ] = new static( $form );
		}

		return static::$instances[ $key ];
	}

	/**
	 * Heading Callback
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	abstract public function heading_callback( $args );

	/**
	 * Button Callback
	 *
	 * Renders buttons.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	abstract public function button_callback( $args );

	/**
	 * Password Callback
	 *
	 * Renders password fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param string $value
	 */
	abstract public function password_callback( $args, $value = '' );

	/**
	 * Email Callback
	 *
	 * Renders email fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param string $value
	 */
	abstract public function email_callback( $args, $value = '' );

	/**
	 * Search Callback
	 *
	 * Renders search fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param string $value
	 */
	abstract public function search_callback( $args, $value = '' );

	/**
	 * URL Callback
	 *
	 * Renders url fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param string $value
	 */
	abstract public function url_callback( $args, $value = '' );

	/**
	 * Telephone Callback
	 *
	 * Renders telelphone number fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param string $value
	 */
	abstract public function tel_callback( $args, $value = '' );

	/**
	 * Number Callback
	 *
	 * Renders number fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param string $value
	 */
	abstract public function number_callback( $args, $value = '' );

	/**
	 * Range Callback
	 *
	 * Renders range fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param string $value
	 */
	abstract public function range_callback( $args, $value = '' );

	/**
	 * Text Callback
	 *
	 * Renders text fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param string $value
	 */
	abstract public function text_callback( $args, $value = '' );

	/**
	 *
	 * /**
	 * Textarea Callback
	 *
	 * Renders textarea fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param string $value
	 */
	abstract public function textarea_callback( $args, $value = '' );

	/**
	 * Hidden Callback
	 *
	 * Renders hidden fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param $value
	 */
	abstract public function hidden_callback( $args, $value = '' );

	/**
	 * Select Callback
	 *
	 * Renders select fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param $value
	 */
	abstract public function select_callback( $args, $value = '' );

	/**
	 * Select Callback
	 *
	 * Renders select fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param $value
	 */
	abstract public function postselect_callback( $args, $value = '' );

	/**
	 * Select Callback
	 *
	 * Renders select fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param $value
	 */
	abstract public function objectselect_callback( $args, $value = '' );

	/**
	 * Select Callback
	 *
	 * Renders select fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param $value
	 */
	abstract public function taxonomyselect_callback( $args, $value = '' );

	/**
	 * Checkbox Callback
	 *
	 * Renders checkboxes.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param $value
	 */
	abstract public function checkbox_callback( $args, $value = false );

	/**
	 * Multicheck Callback
	 *
	 * Renders multiple checkboxes.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param array $values
	 */
	abstract public function multicheck_callback( $args, $values = array() );

	/**
	 * Rangeslider Callback
	 *
	 * Renders the rangeslider.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param $value
	 */
	abstract public function rangeslider_callback( $args, $value = '' );

	/**
	 * Hook Callback
	 *
	 * Adds a do_action() hook in place of the field
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	abstract public function hook_callback( $args );

	/**
	 * Missing Callback
	 *
	 * If a function is missing for settings callbacks alert the user.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	abstract public function missing_callback( $args );

	/**
	 * Radio Callback
	 *
	 * Renders radio boxes.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param string $value
	 */
	abstract public function radio_callback( $args, $value = '' );

	/**
	 * Color select Callback
	 *
	 * Renders color select fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param string $value
	 */
	abstract public function color_select_callback( $args, $value = '' );

	/**
	 * Rich Editor Callback
	 *
	 * Renders rich editor fields.
	 *
	 * @param array $args Arguments passed by the setting
	 * @param string $value
	 *
	 * @return
	 */
	abstract public function rich_editor_callback( $args, $value = '' );

	/**
	 * Upload Callback
	 *
	 * Renders upload fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param string $value
	 */
	abstract public function upload_callback( $args, $value = '' );

	/**
	 * Color picker Callback
	 *
	 * Renders color picker fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @param string $value
	 */
	abstract public function color_callback( $args, $value = '' );

	/**
	 * Descriptive text callback.
	 *
	 * Renders descriptive text onto the settings field.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	abstract public function descriptive_text_callback( $args );

	/**
	 * Registers the license field callback for Software Licensing
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	abstract public function license_key_callback( $args );

}
