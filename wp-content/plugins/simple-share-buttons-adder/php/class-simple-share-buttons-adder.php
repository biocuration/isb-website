<?php
/**
 * Simple_Share_Buttons_Adder
 *
 * @package SimpleShareButtonsAdder
 */

namespace SimpleShareButtonsAdder;

/**
 * Simple Share Buttons Adder Class
 *
 * @package SimpleShareButtonsAdder
 */
class Simple_Share_Buttons_Adder {

	/**
	 * Plugin instance.
	 *
	 * @var object
	 */
	public $plugin;

	/**
	 * Class constructor.
	 *
	 * @param object $plugin Plugin class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Get the SSBA option settings.
	 *
	 * @action init
	 * @return array
	 */
	public function get_ssba_settings() {
		$json_settings = get_option( 'ssba_settings' );

		// Decode and return settings.
		return json_decode( $json_settings, true );
	}

	/**
	 * Update an array of options.
	 *
	 * @param array $arr_options The options array.
	 */
	public function ssba_update_options( $arr_options ) {
		// If not given an array.
		if ( ! is_array( $arr_options ) ) {
			die( esc_html__( 'Value parsed not an array', 'simple-share-buttons-adder' ) );
		}

		// Get ssba settings.
		$json_settings = get_option( 'ssba_settings' );

		// Decode the settings.
		$ssba_settings = json_decode( $json_settings, true );

		// Loop through array given.
		foreach ( $arr_options as $name => $value ) {
			// Update/add the option in the array.
			$ssba_settings[ $name ] = $value;
		}

		// Encode the options ready to save back.
		$json_settings = wp_json_encode( $ssba_settings );

		// Update the option in the db.
		update_option( 'ssba_settings', $json_settings );
	}

	/**
	 * Add setting link to plugin page.
	 *
	 * @param $links
	 *
	 * @return mixed
	 */
	public function add_action_links( $links ) {
		$mylinks = array(
			'<a href="' . admin_url( 'options-general.php?page=simple-share-buttons-adder' ) . '">Settings</a>',
		);
		return array_merge( $links, $mylinks );
	}
}
