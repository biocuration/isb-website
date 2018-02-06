<?php
/**
 * Forms.
 *
 * @package SimpleShareButtonsAdder
 */

namespace SimpleShareButtonsAdder;

/**
 * Forms Class
 *
 * @package SimpleShareButtonsAdder
 */
class Forms {

	/**
	 * Plugin instance.
	 *
	 * @var object
	 */
	public $plugin;

	/**
	 * Checkboxes.
	 *
	 * @var string
	 */
	public $ssba_checkboxes;

	/**
	 * Class constructor.
	 *
	 * @param object $plugin Plugin class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Opening form tag.
	 *
	 * @param string $wrap The wrap class.
	 * @param string $action The action attribute.
	 * @param string $class The class attribute.
	 *
	 * @return string
	 */
	public function open( $wrap, $action = '', $class = '' ) {
		$wrap = $wrap ? 'ssba-form-wrap' : '';
		$return = '<div class="' . esc_attr( $wrap ) . '">';
		$return .= '<form class="form-horizontal ' . esc_attr( $class ) . '" id="ssba-admin-form" method="post" action="' . esc_attr( $action ) . '">';

		// Required hidden fields.
		$return .= wp_nonce_field( 'ssba_save_settings','ssba_save_nonce' );
		$return .= '<input type="hidden" name="ssba_options" />';

		// Open fieldset.
		$return .= '<fieldset>';

		return $return;
	}

	/**
	 * Close form tag.
	 *
	 * @return string
	 */
	public function close() {
		// Save button.
		$return = '<button id="submit" class="ssba-btn-save btn btn-lg btn-primary"><i class="fa fa-floppy-o"></i></button>';

		// Success button.
		$return .= '<button type="button" class="ssba-btn-save-success btn btn-lg btn-success"><i class="fa fa-check"></i></button>';

		// Close fieldset.
		$return .= '</fieldset>';

		// Close form.
		$return .= '</form>';
		$return .= '</div>';

		return $return;
	}

	/**
	 * Inline checkboxes.
	 *
	 * @param array $opts The option array.
	 *
	 * @return string
	 */
	public function ssbp_checkboxes( $opts ) {
		// Check if opts passed is an array.
		if ( ! is_array( $opts ) ) {
			return 'Variable passed not an array';
		}

		// Define variable.
		$input = '';

		// If we're including the form group div.
		if ( $opts['form_group'] ) {
			$input .= '<div class="form-group">';
		}

		// If a tooltip has been set.
		if ( isset( $opts['tooltip'] ) && '' !== $opts['tooltip'] ) {
			$tooltip = 'data-toggle="tooltip" data-placement="right" data-original-title="' . esc_attr( $opts['tooltip'] ) . '"';
		} else {
			$tooltip = '';
		}

		// Label with tooltip.
		$input .= '<label class="control-label" ' . esc_attr( $tooltip ) . '>' . esc_html( $opts['label'] ) . '</label>';

		// Input div.
		$input .= '<div class="">';

		// Add all checkboxes.
		foreach ( $opts['checkboxes'] as $checkbox => $value ) {
			$input .= $this->_ssbp_add_checkboxes( $value, $checkbox );
		}

		// Close input div.
		$input .= '</div>';

		// If we're including the form group div.
		if ( $opts['form_group'] ) {
			$input .= '</div>';
		}

		// Return the input.
		return $input;
	}

	/**
	 * Checkboxes.
	 *
	 * @param array  $value The checked prop.
	 * @param string $key The label value.
	 */
	public function _ssbp_add_checkboxes( $value, $key ) {
		$checked = isset( $value['checked'] ) && $value['checked'] ? 'checked="checked"' : '';

		$ssba_checkboxes = '<label class="checkbox-inline no_indent">
									' . esc_html( $key ) . '<br />
									<input type="checkbox" id="' . esc_attr( $value['value'] ) . '" name="' . esc_attr( $value['value'] ) . '" value="Y" ' . esc_attr( $checked ) . '>
									</label>';
		return $ssba_checkboxes;
	}

	/**
	 * Form input with group.
	 *
	 * @param array $opts The option array.
	 *
	 * @return string
	 */
	public function ssbp_input( $opts ) {
		// Check if opts passed is an array.
		if ( ! is_array( $opts ) ) {
			return 'Variable passed not an array';
		}

		// Define variable.
		$input = '';

		// If we're including the form group div.
		if ( $opts['form_group'] ) {
			$input .= '<div class="form-group">';
		}

		// If a tooltip has been set.
		if ( isset( $opts['tooltip'] ) && '' !== $opts['tooltip'] ) {
			$tooltip = 'data-toggle="tooltip" data-placement="right" data-original-title="' . esc_attr( $opts['tooltip'] ) . '"';
		} else {
			$tooltip = '';
		}

		// Label with tooltip.
		$input .= '<label for="' . esc_attr( $opts['name'] ) . '" class="control-label" ' . $tooltip . '>' . esc_html( $opts['label'] ) . '</label>';

		// Input div.
		$input .= '<div class="input-div">';
		$disabled = isset( $opts['disabled'] ) ? $opts['disabled'] : null;

		// Switch based on the input type.
		switch ( $opts['type'] ) {
			case 'text':
			default:
				$input .= '<input class="form-control" name="' . esc_attr( $opts['name'] ) . '" id="' . esc_attr( $opts['name'] ) . '" type="text" value="' . esc_attr( $opts['value'] ) . '" placeholder="' . esc_attr( $opts['placeholder'] ) . '" ' . $disabled . ' />';
			break;
			case 'text_prefix':
				$input .= '<div class="input-group">
							<span class="input-group-addon">' . esc_html( $opts['prefix'] ) . '</span>
							<input name="' . esc_attr( $opts['name'] ) . '" id="' . esc_attr( $opts['name'] ) . '" type="text" value="' . esc_attr( $opts['value'] ) . '" class="form-control" placeholder="' . esc_attr( $opts['placeholder'] ) . '">
						  </div>';
			break;
			case 'error':
				$input .= '<p class="text-danger">' . esc_html( $opts['error'] ) . '</p>';
			break;
			case 'number':
				$input .= '<input class="form-control" name="' . esc_attr( $opts['name'] ) . '" id="' . esc_attr( $opts['name'] ) . '" type="number" value="' . esc_attr( $opts['value'] ) . '" placeholder="' . esc_attr( $opts['placeholder'] ) . '" />';
			break;
			case 'image_upload':
				$input .= '<div class="input-group">
							<input id="' . esc_attr( $opts['name'] ) . '" name="' . esc_attr( $opts['name'] ) . '" type="text" class="form-control" value="' . esc_attr( $opts['value'] ) . '">
							<span class="input-group-btn">
							  <button id="upload_' . esc_attr( $opts['name'] ) . '_button" class="ssbpUpload ssbp_upload_btn btn btn-default" data-ssbp-input="' . esc_attr( $opts['name'] ) . '" type="button">Upload</button>
							</span>
						  </div>';
			break;
			case 'number_addon':
				$input .= '<div class="input-group">
							<input id="' . esc_attr( $opts['name'] ) . '" name="' . esc_attr( $opts['name'] ) . '" type="number" class="form-control" value="' . esc_attr( $opts['value'] ) . '" placeholder="' . esc_attr( $opts['placeholder'] ) . '" />
							<span class="input-group-addon">' . esc_html( $opts['addon'] ) . '</span>
						  </div>';
			break;
			case 'colorpicker':
				$value = '' !== $opts['value'] ? $opts['value'] : '#eaeaea';
				$input .= '<input id="' . esc_attr( $opts['name'] ) . '" name="' . esc_attr( $opts['name'] ) . '" type="text" class="ssba-colorpicker form-control" value="' . esc_attr( $opts['value'] ) . '" placeholder="#4582ec" style="border-color: ' . esc_attr( $value ) . '" />';
			break;
			case 'textarea':
				$class = isset( $opts['class'] ) ? $opts['class'] : '';
				$input .= '<textarea class="form-control ' . esc_attr( $class ) . '" name="' . esc_attr( $opts['name'] ) . '" id="' . esc_attr( $opts['name'] ) . '" rows="' . esc_attr( $opts['rows'] ) . '">' . esc_html( $opts['value'] ) . '</textarea>';
			break;
			case 'checkbox':
				$class = isset( $opts['class'] ) ? $opts['class'] : '';
				$disabled = isset( $opts['disabled'] ) ? $opts['disabled'] : '';
				$input .= '<input class="' . esc_attr( $class ) . '" name="' . esc_attr( $opts['name'] ) . '" id="' . esc_attr( $opts['name'] ) . '" type="checkbox" ' . esc_attr( $opts['checked'] ) . ' value="' . esc_attr( $opts['value'] ) . '" ' . esc_attr( $disabled ) . ' />';
			break;
			case 'select':
				$input .= '<select class="form-control" name="' . esc_attr( $opts['name'] ) . '" id="' . esc_attr( $opts['name'] ) . '">';

				// Add all options.
				foreach ( $opts['options'] as $key => $value ) {
					$selected = (string) $value === $opts['selected'] ? 'selected="selected"' : '';
					$input .= '<option value="' . esc_attr( $value ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $key ) . '</option>';
				}

				$input .= '</select>';
			break;
		} // End switch().

		// Close input div.
		$input .= '</div>';

		// If we're including the form group div.
		if ( $opts['form_group'] ) {
			$input .= '</div>';
		}

		// Return the input.
		return $input;
	}
}
