<?php
/**
 * Database.
 *
 * @package SimpleShareButtonsAdder
 */

namespace SimpleShareButtonsAdder;

/**
 * Database Class
 *
 * @package SimpleShareButtonsAdder
 */
class Database {

	/**
	 * Plugin instance.
	 *
	 * @var object
	 */
	public $plugin;

	/**
	 * Simple Share Buttons Adder instance.
	 *
	 * @var object
	 */
	public $class_ssba;

	/**
	 * Class constructor.
	 *
	 * @param object $plugin Plugin class.
	 * @param object $class_ssba Simple Share Buttons Adder class.
	 */
	public function __construct( $plugin, $class_ssba ) {
		$this->plugin = $plugin;
		$this->class_ssba = $class_ssba;

		// Run the activation function upon activation of the plugin.
		register_activation_hook( $this->plugin->dir_path . '/simple-share-buttons-adder.php', array( $this, 'activate' ) );

		// Register deactivation hook.
		register_deactivation_hook( $this->plugin->dir_path . '/simple-share-buttons-adder.php', array( $this, 'deactivate' ) );
	}

	/**
	 * Activate ssba function.
	 */
	public function activate() {
		// Likely a reactivation, return doing nothing.
		if ( false !== get_option( 'ssba_version' ) ) {
			return;
		}

		// Array ready with defaults.
		$ssba_settings = array(
			'ssba_image_set'             => 'somacro',
			'ssba_size'                  => '35',
			'ssba_pages'                 => '',
			'ssba_posts'                 => '',
			'ssba_cats_archs'            => '',
			'ssba_homepage'              => '',
			'ssba_excerpts'              => '',
			'ssba_plus_pages'            => '',
			'ssba_plus_posts'            => '',
			'ssba_plus_cats_archs'       => '',
			'ssba_plus_homepage'         => '',
			'ssba_plus_excerpts'         => '',
			'ssba_share_pages'           => '',
			'ssba_share_posts'           => '',
			'ssba_share_cats_archs'      => '',
			'ssba_share_homepage'        => '',
			'ssba_share_excerpts'        => '',
			'ssba_align'                 => 'left',
			'ssba_plus_align'            => 'left',
			'ssba_share_align'           => 'left',
			'ssba_padding'               => '6',
			'ssba_before_or_after'       => 'after',
			'ssba_before_or_after_plus'  => 'after',
			'ssba_additional_css'        => '',
			'ssba_custom_styles'         => '',
			'ssba_custom_styles_enabled' => '',
			'ssba_plus_additional_css'   => '',
			'ssba_plus_custom_styles'    => '',
			'ssba_plus_custom_styles_enabled' => '',
			'ssba_share_additional_css'   => '',
			'ssba_share_custom_styles'    => '',
			'ssba_share_custom_styles_enabled' => '',
			'ssba_email_message'         => '',
			'ssba_twitter_text'          => '',
			'ssba_buffer_text'           => '',
			'ssba_flattr_user_id'        => '',
			'ssba_flattr_url'            => '',
			'ssba_share_new_window'      => 'Y',
			'ssba_link_to_ssb'           => 'N',
			'ssba_show_share_count'      => '',
			'ssba_plus_show_share_count' => '',
			'ssba_share_show_share_count' => '',
			'ssba_share_count_style'     => 'default',
			'ssba_share_count_css'       => '',
			'ssba_share_count_once'      => 'Y',
			'ssba_widget_text'           => '',
			'ssba_rel_nofollow'          => '',
			'ssba_default_pinterest'     => '',
			'ssba_pinterest_featured'    => '',
			'ssba_plus_widget_text'      => '',
			'ssba_plus_rel_nofollow'     => '',
			'ssba_plus_default_pinterest'   => '',
			'ssba_plus_pinterest_featured'  => '',
			'ssba_share_widget_text'        => '',
			'ssba_share_rel_nofollow'       => '',
			'ssba_share_default_pinterest'  => '',
			'ssba_share_pinterest_featured' => '',
			'ssba_content_priority'      => '10',

			// Share container.
			'ssba_div_padding'           => '',
			'ssba_div_rounded_corners'   => '',
			'ssba_border_width'          => '',
			'ssba_div_border'            => '',
			'ssba_div_background'        => '',

			// Share text.
			'ssba_share_text'            => esc_html__( 'Share this...', 'simple-share-buttons-adder' ),
			'ssba_text_placement'        => 'above',
			'ssba_font_family'           => '',
			'ssba_font_color'            => '',
			'ssba_font_size'             => '12',
			'ssba_font_weight'           => '',
			'ssba_plus_share_text'       => esc_html__( 'Share this...', 'simple-share-buttons-adder' ),
			'ssba_plus_text_placement'   => 'above',
			'ssba_plus_font_family'      => '',
			'ssba_plus_font_color'       => '',
			'ssba_plus_font_size'        => '12',
			'ssba_plus_font_weight'      => '',

			// Include.
			'ssba_selected_buttons'         => 'facebook,google,twitter,linkedin',
			'ssba_selected_share_buttons'   => 'facebook,google,twitter,linkedin',
			'ssba_selected_plus_buttons'    => 'facebook,google,twitter,linkedin',
			'ssba_share_button_style'       => 1,
			'ssba_share_bar_style'          => 1,
			'ssba_new_buttons'              => '',
			'ssba_share_bar'                => '',
			'ssba_share_bar_position'       => 'left',
			'ssba_plus_height'              => '48',
			'ssba_plus_width'               => '48',
			'ssba_plus_margin'              => '12',
			'ssba_plus_button_color'        => '',
			'ssba_plus_button_hover_color'  => '',
			'ssba_plus_icon_size'           => '',
			'ssba_plus_icon_color'          => '',
			'ssba_plus_icon_hover_color'    => '',
			'ssba_share_height'             => '48',
			'ssba_share_width'              => '48',
			'ssba_share_margin'              => '0',
			'ssba_share_icon_size'          => '',
			'ssba_share_button_color'       => '',
			'ssba_share_button_hover_color' => '',
			'ssba_share_icon_color'         => '',
			'ssba_share_icon_hover_color'   => '',
			'ssba_share_desktop'            => '',
			'ssba_share_mobile'             => '',
			'ssba_mobile_breakpoint'        => '',

			// Custom images.
			'ssba_custom_email'          => '',
			'ssba_custom_google'         => '',
			'ssba_custom_facebook'       => '',
			'ssba_custom_twitter'        => '',
			'ssba_custom_diggit'         => '',
			'ssba_custom_linkedin'       => '',
			'ssba_custom_reddit'         => '',
			'ssba_custom_stumbleupon'    => '',
			'ssba_custom_pinterest'      => '',
			'ssba_custom_buffer'         => '',
			'ssba_custom_flattr'         => '',
			'ssba_custom_tumblr'         => '',
			'ssba_custom_print'          => '',
			'ssba_custom_vk'             => '',
			'ssba_custom_yummly'         => '',
			'ssba_custom_facebook_save'  => '',

			// Sharedcount.
			'sharedcount_enabled'        => '',
			'sharedcount_api_key'        => '',
			'sharedcount_plan'           => 'free',
			'sharedcount_plus_enabled'   => '',
			'sharedcount_plus_api_key'   => '',
			'sharedcount_plus_plan'      => 'free',
			'sharedcount_share_enabled'   => '',
			'sharedcount_share_api_key'   => '',
			'sharedcount_share_plan'      => 'free',

			// Newsharecounts.
			'twitter_newsharecounts'       => '',
			'plus_twitter_newsharecounts'  => '',
			'share_twitter_newsharecounts' => '',

			// New with sharethis.
			'facebook_insights'          => '',
			'facebook_app_id'            => '',
			'plus_facebook_insights'     => '',
			'plus_facebook_app_id'       => '',
			'share_facebook_insights'     => '',
			'share_facebook_app_id'       => '',
			'accepted_sharethis_terms'   => 'Y',
		);

		// Json encode.
		$json_settings = wp_json_encode( $ssba_settings );

		// Insert default options for ssba.
		add_option( 'ssba_settings', $json_settings );

		// Button helper array.
		$this->ssba_button_helper_array();

		// Ssba version.
		add_option( 'ssba_version', SSBA_VERSION );
	}

	/**
	 * Deactivate ssba.
	 */
	public function deactivate() {
		// Delete options.
		delete_option( 'ssba_settings' );
		delete_option( 'ssba_version' );
	}

	/**
	 * The upgrade function.
	 *
	 * @param array  $arr_settings The current ssba settings.
	 * @param string $version The current plugin version.
	 */
	public function upgrade_ssba( $arr_settings, $version ) {
		// If version is less than 6.0.5.
		if ( $version < '6.0.5' ) {
			// Ensure excerpts are set.
			add_option( 'ssba_excerpts', '' );

			// Add print button.
			add_option( 'ssba_custom_print', '' );

			// New for 3.8.
			add_option( 'ssba_widget_text', '' );
			add_option( 'ssba_rel_nofollow', '' );

			// Added pre 4.5, added in 4.6 to fix notice.
			add_option( 'ssba_rel_nofollow', '' );

			// Added in 5.0.
			add_option( 'ssba_custom_vk', '' );
			add_option( 'ssba_custom_yummly', '' );

			// Added in 5.2.
			add_option( 'ssba_default_pinterest', '' );

			// Added in 5.5.
			add_option( 'ssba_pinterest_featured', '' );

			// Added in 5.7. Additional CSS field.
			add_option( 'ssba_additional_css', '' );

			// Empty custom CSS var and option.
			$custom_css = '';

			add_option( 'ssba_custom_styles_enabled', '' );

			// If some custom styles are in place.
			if ( '' !== $arr_settings['ssba_custom_styles'] ) {
				$custom_css .= $arr_settings['ssba_custom_styles'];

				update_option( 'ssba_custom_styles_enabled', 'Y' );
			}

			// If some custom share count styles are in place.
			if ( '' !== $arr_settings['ssba_share_count_css'] ) {
				$custom_css .= $arr_settings['ssba_share_count_css'];

				update_option( 'ssba_custom_styles_enabled', 'Y' );
			}

			// Update custom CSS option.
			update_option( 'ssba_custom_styles', $custom_css );

			// Content priority.
			add_option( 'ssba_content_priority', '10' );
		} // End if().

		// If version is less than 6.0.6.
		if ( $version < '6.0.6' ) {
			// Get old settings.
			$old_settings = $this->get_old_ssba_settings();

			// Json encode old settings.
			$json_settings = wp_json_encode( $old_settings );

			// Insert all options for ssba as json.
			add_option( 'ssba_settings', $json_settings );

			// Delete old options.
			$this->ssba_delete_old_options();
		}

		// If version is less than 6.1.3.
		if ( $version < '6.1.3' ) {
			// New settings.
			$new = array(
				'sharedcount_enabled' => '',
				'sharedcount_api_key' => '',
				'sharedcount_plan'    => 'free',
			);

			// Update settings.
			$this->class_ssba->ssba_update_options( $new );
		}

		// If version is less than 6.1.5.
		if ( $version < '6.1.5' ) {
			// New settings.
			$new = array(
				'twitter_newsharecounts' => '',
			);

			// Update settings.
			$this->class_ssba->ssba_update_options( $new );
		}

		// If version is less than 6.2.0.
		if ( $version < '6.2.0' ) {
			// New settings.
			$new = array(
				'facebook_insights'        => '',
				'facebook_app_id'          => '',
				'accepted_sharethis_terms' => '',
			);

			// Update settings.
			$this->class_ssba->ssba_update_options( $new );
		}

		// Button helper array.
		$this->ssba_button_helper_array();

		// Show the ST terms notice after upgrades if the user hasn't agreed.
		$this->class_ssba->ssba_update_options( array(
			'hide_sharethis_terms' => false,
		) );

		// Update version number.
		update_option( 'ssba_version', SSBA_VERSION );
	}

	/**
	 * Button helper option.
	 */
	public function ssba_button_helper_array() {
		// Helper array for ssbp.
		update_option( 'ssba_buttons', wp_json_encode( array(
			'buffer'        => array(
				'full_name' => esc_html__( 'Buffer', 'simple-share-buttons-adder' ),
			),
			'diggit'        => array(
				'full_name' => esc_html__( 'Diggit', 'simple-share-buttons-adder' ),
			),
			'email'         => array(
				'full_name' => esc_html__( 'Email', 'simple-share-buttons-adder' ),
			),
			'facebook'      => array(
				'full_name' => esc_html__( 'Facebook', 'simple-share-buttons-adder' ),
			),
			'facebook_save' => array(
				'full_name' => esc_html__( 'Facebook Save', 'simple-share-buttons-adder' ),
			),
			'flattr'        => array(
				'full_name' => esc_html__( 'Flattr', 'simple-share-buttons-adder' ),
				),
			'google'        => array(
				'full_name' => esc_html__( 'Google+', 'simple-share-buttons-adder' ),
				),
			'linkedin'      => array(
				'full_name' => esc_html__( 'LinkedIn', 'simple-share-buttons-adder' ),
				),
			'pinterest'     => array(
				'full_name' => esc_html__( 'Pinterest', 'simple-share-buttons-adder' ),
				),
			'print'         => array(
				'full_name' => esc_html__( 'Print', 'simple-share-buttons-adder' ),
				),
			'reddit'        => array(
				'full_name' => esc_html__( 'Reddit', 'simple-share-buttons-adder' ),
				),
			'stumbleupon'   => array(
				'full_name' => esc_html__( 'StumbleUpon', 'simple-share-buttons-adder' ),
				),
			'tumblr'        => array(
				'full_name' => esc_html__( 'Tumblr', 'simple-share-buttons-adder' ),
				),
			'twitter'       => array(
				'full_name' => esc_html__( 'Twitter', 'simple-share-buttons-adder' ),
				),
			'vk'            => array(
				'full_name' => esc_html__( 'VK', 'simple-share-buttons-adder' ),
				),
			'whatsapp'      => array(
				'full_name' => esc_html__( 'WhatsApp', 'simple-share-buttons-adder' ),
			),
			'xing'          => array(
				'full_name' => esc_html__( 'Xing', 'simple-share-buttons-adder' ),
			),
			'yummly'        => array(
				'full_name' => esc_html__( 'Yummly', 'simple-share-buttons-adder' ),
			),
		) ) );
	}

	/**
	 * Delete old options to move to json array.
	 */
	public function ssba_delete_old_options() {
		// Delete all options.
		delete_option( 'ssba_version' );
		delete_option( 'ssba_image_set' );
		delete_option( 'ssba_size' );
		delete_option( 'ssba_pages' );
		delete_option( 'ssba_posts' );
		delete_option( 'ssba_cats_archs' );
		delete_option( 'ssba_homepage' );
		delete_option( 'ssba_excerpts' );
		delete_option( 'ssba_plus_pages' );
		delete_option( 'ssba_plus_posts' );
		delete_option( 'ssba_plus_cats_archs' );
		delete_option( 'ssba_plus_homepage' );
		delete_option( 'ssba_plus_excerpts' );
		delete_option( 'ssba_share_bar' );
		delete_option( 'ssba_share_pages' );
		delete_option( 'ssba_share_posts' );
		delete_option( 'ssba_share_cats_archs' );
		delete_option( 'ssba_share_homepage' );
		delete_option( 'ssba_share_excerpts' );
		delete_option( 'ssba_align' );
		delete_option( 'ssba_plus_align' );
		delete_option( 'ssba_padding' );
		delete_option( 'ssba_before_or_after' );
		delete_option( 'ssba_before_or_after_plus' );
		delete_option( 'ssba_additional_css' );
		delete_option( 'ssba_custom_styles' );
		delete_option( 'ssba_custom_styles_enabled' );
		delete_option( 'ssba_email_message' );
		delete_option( 'ssba_buffer_text' );
		delete_option( 'ssba_twitter_text' );
		delete_option( 'ssba_flattr_user_id' );
		delete_option( 'ssba_flattr_url' );
		delete_option( 'ssba_share_new_window' );
		delete_option( 'ssba_link_to_ssb' );
		delete_option( 'ssba_show_share_count' );
		delete_option( 'ssba_share_count_style' );
		delete_option( 'ssba_share_count_css' );
		delete_option( 'ssba_share_count_once' );
		delete_option( 'ssba_widget_text' );
		delete_option( 'ssba_rel_nofollow' );
		delete_option( 'ssba_default_pinterest' );
		delete_option( 'ssba_pinterest_featured' );
		delete_option( 'ssba_content_priority' );
		delete_option( 'ssba_plus_additional_css' );
		delete_option( 'ssba_plus_custom_styles' );
		delete_option( 'ssba_plus_custom_styles_enabled' );
		delete_option( 'ssba_plus_email_message' );
		delete_option( 'ssba_plus_buffer_text' );
		delete_option( 'ssba_plus_twitter_text' );
		delete_option( 'ssba_plus_flattr_user_id' );
		delete_option( 'ssba_plus_flattr_url' );
		delete_option( 'ssba_plus_share_new_window' );
		delete_option( 'ssba_plus_link_to_ssb' );
		delete_option( 'ssba_plus_show_share_count' );
		delete_option( 'ssba_plus_share_count_style' );
		delete_option( 'ssba_plus_share_count_css' );
		delete_option( 'ssba_plus_share_count_once' );
		delete_option( 'ssba_plus_widget_text' );
		delete_option( 'ssba_plus_rel_nofollow' );
		delete_option( 'ssba_plus_default_pinterest' );
		delete_option( 'ssba_plus_pinterest_featured' );
		delete_option( 'ssba_share_additional_css' );
		delete_option( 'ssba_share_custom_styles' );
		delete_option( 'ssba_share_custom_styles_enabled' );
		delete_option( 'ssba_share_email_message' );
		delete_option( 'ssba_share_buffer_text' );
		delete_option( 'ssba_share_twitter_text' );
		delete_option( 'ssba_share_flattr_user_id' );
		delete_option( 'ssba_share_flattr_url' );
		delete_option( 'ssba_share_share_new_window' );
		delete_option( 'ssba_share_link_to_ssb' );
		delete_option( 'ssba_share_show_share_count' );
		delete_option( 'ssba_share_share_count_style' );
		delete_option( 'ssba_share_share_count_css' );
		delete_option( 'ssba_share_share_count_once' );
		delete_option( 'ssba_share_widget_text' );
		delete_option( 'ssba_share_rel_nofollow' );
		delete_option( 'ssba_share_default_pinterest' );
		delete_option( 'ssba_share_pinterest_featured' );

		// Share container.
		delete_option( 'ssba_div_padding' );
		delete_option( 'ssba_div_rounded_corners' );
		delete_option( 'ssba_border_width' );
		delete_option( 'ssba_div_border' );
		delete_option( 'ssba_div_background' );

		// Share text.
		delete_option( 'ssba_share_text' );
		delete_option( 'ssba_text_placement' );
		delete_option( 'ssba_font_family' );
		delete_option( 'ssba_font_color' );
		delete_option( 'ssba_font_size' );
		delete_option( 'ssba_font_weight' );
		delete_option( 'ssba_plus_share_text' );
		delete_option( 'ssba_plus_text_placement' );
		delete_option( 'ssba_plus_font_family' );
		delete_option( 'ssba_plus_font_color' );
		delete_option( 'ssba_plus_font_size' );
		delete_option( 'ssba_plus_font_weight' );

		// Include.
		delete_option( 'ssba_selected_buttons' );
		delete_option( 'ssba_selected_share_buttons' );
		delete_option( 'ssba_selected_plus_buttons' );
		delete_option( 'ssba_share_button_style' );
		delete_option( 'ssba_share_bar_style' );
		delete_option( 'ssba_new_buttons' );
		delete_option( 'ssba_share_bar_position' );
		delete_option( 'ssba_plus_height' );
		delete_option( 'ssba_plus_width' );
		delete_option( 'ssba_plus_margin' );
		delete_option( 'ssba_plus_button_color' );
		delete_option( 'ssba_plus_button_hover_color' );
		delete_option( 'ssba_plus_icon_size' );
		delete_option( 'ssba_plus_icon_color' );
		delete_option( 'ssba_plus_icon_hover_color' );
		delete_option( 'ssba_share_height' );
		delete_option( 'ssba_share_width' );
		delete_option( 'ssba_share_margin' );
		delete_option( 'ssba_share_button_color' );
		delete_option( 'ssba_share_button_hover_color' );
		delete_option( 'ssba_share_icon_size' );
		delete_option( 'ssba_share_icon_color' );
		delete_option( 'ssba_share_icon_hover_color' );
		delete_option( 'ssba_share_desktop' );
		delete_option( 'ssba_share_mobile' );
		delete_option( 'ssba_mobile_breakpoint' );

		// Custom images.
		delete_option( 'ssba_custom_email' );
		delete_option( 'ssba_custom_google' );
		delete_option( 'ssba_custom_facebook' );
		delete_option( 'ssba_custom_twitter' );
		delete_option( 'ssba_custom_diggit' );
		delete_option( 'ssba_custom_linkedin' );
		delete_option( 'ssba_custom_reddit' );
		delete_option( 'ssba_custom_stumbleupon' );
		delete_option( 'ssba_custom_pinterest' );
		delete_option( 'ssba_custom_buffer' );
		delete_option( 'ssba_custom_flattr' );
		delete_option( 'ssba_custom_tumblr' );
		delete_option( 'ssba_custom_print' );
		delete_option( 'ssba_custom_vk' );
		delete_option( 'ssba_custom_yummly' );

		// Notice.
		delete_option( 'ssba_dismiss_notice' );
	}

	/**
	 * Return old ssba settings (pre 6.0.6).
	 *
	 * @return array|null|object
	 */
	public function get_old_ssba_settings() {
		global $wp_registered_settings;

		// Set variable.
		$arr_settings = array();

		foreach ( $wp_registered_settings as $name => $value ) {
			if ( in_array( 'ssba', explode( '_', $name ), true ) ) {
				$arr_settings[ $name ] = $value;
			}
		}

		return $arr_settings;
	}
}
