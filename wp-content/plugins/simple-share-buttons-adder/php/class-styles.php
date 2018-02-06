<?php
/**
 * Styles.
 *
 * @package SimpleShareButtonsAdder
 */

namespace SimpleShareButtonsAdder;

/**
 * Styles Class
 *
 * @package SimpleShareButtonsAdder
 */
class Styles {

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
	}

	/**
	 * Add css scripts for page/post use.
	 *
	 * @action wp_enqueue_scripts
	 */
	public function ssba_page_scripts() {
		// Get settings.
		$arr_settings = $this->class_ssba->get_ssba_settings();

		if ( is_ssl() ) {
			$st_insights = 'https://ws.sharethis.com/button/st_insights.js';
		} else {
			$st_insights = 'http://w.sharethis.com/button/st_insights.js';
		}

		// Add call to st_insights.js with params.
		$url = add_query_arg( array(
			'publisher' => '4d48b7c5-0ae3-43d4-bfbe-3ff8c17a8ae6',
			'product'   => 'simpleshare',
		), $st_insights );

		if ( 'Y' === $arr_settings['accepted_sharethis_terms'] ) {
			wp_enqueue_script( 'ssba-sharethis', $url, null, null );
			add_filter( 'script_loader_tag', array( $this, 'ssba_script_tags' ), 10, 2 );
		}

		// Enqueue main script.
		wp_enqueue_script( "{$this->plugin->assets_prefix}-ssba" );
		wp_add_inline_script( "{$this->plugin->assets_prefix}-ssba", sprintf( 'Main.boot( %s );',
			wp_json_encode( array() )
		) );

		// If indie flower font is selected.
		if ( 'Indie Flower' === $arr_settings['ssba_font_family'] ) {
			// Font scripts.
			wp_enqueue_style( "{$this->plugin->assets_prefix}-indie" );
		} elseif ( 'Reenie Beanie' === $arr_settings['ssba_font_family'] ) {
			// Font scripts.
			wp_enqueue_style( "{$this->plugin->assets_prefix}-reenie" );
		}
	}

	/**
	 * Adds ID to sharethis script.
	 *
	 * @param string $tag HTML script tag.
	 * @param string $handle Script handle.
	 *
	 * @return string
	 */
	public function ssba_script_tags( $tag, $handle ) {
		if ( 'ssba-sharethis' === $handle ) {
			return str_replace( '<script ', '<script id=\'st_insights_js\' ', $tag );
		}

		return $tag;
	}

	/**
	 * Generate style.
	 *
	 * @action wp_enqueue_scripts
	 */
	public function get_ssba_style() {
		// Query the db for current ssba settings.
		$arr_settings = $this->class_ssba->get_ssba_settings();

		// If the sharethis terms have been accepted.
		if ( 'Y' === $arr_settings['accepted_sharethis_terms'] ) {
			// if a facebook app id has been set
			if ( '' !== $arr_settings['facebook_app_id'] ) {
				$src = '//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6&appID=' . $arr_settings['facebook_app_id'];
			} else {
				$src = '//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6';
			}

			// If an app id has been entered.
			if ( '' !== $arr_settings['facebook_app_id'] ) {
				// Init facebook.
				echo "<script>window.fbAsyncInit = function() {
				FB.init({
				  appId      : '" . $arr_settings['facebook_app_id'] . "',
				  xfbml      : true,
				  version    : 'v2.6'
				});
			  };</script>";
			}

			// Include facebook js sdk.
			echo '<script>(function(d, s, id){
				 var js, fjs = d.getElementsByTagName(s)[0];
				 if (d.getElementById(id)) {return;}
				 js = d.createElement(s); js.id = id;
				 js.src = "' . $src . '";
				 fjs.parentNode.insertBefore(js, fjs);
			   }(document, \'script\', \'facebook-jssdk\'));</script>';

			// If an app id has been entered.
			if ( '' !== $arr_settings['facebook_app_id'] ) {
				// If facebook insights have been enabled.
				if ( 'Y' === $arr_settings['facebook_insights'] ) {
					// Add facebook meta tag.
					echo '<meta property="fb:app_id" content="' . esc_attr( $arr_settings['facebook_app_id'] ) . '" />';
				}
			}
		} // End if().

		// Check if custom styles haven't been set.
		if ( 'Y' !== $arr_settings['ssba_custom_styles_enabled'] ) {
			$div_padding = '' !== $arr_settings['ssba_div_padding'] ? 'padding: ' . $arr_settings['ssba_div_padding'] . 'px;' : '';
			$border_width = '' !== $arr_settings['ssba_border_width'] ? 'border: ' . $arr_settings['ssba_border_width'] . 'px solid ' . $arr_settings['ssba_div_border'] . ';' : '';
			$div_background1 = '' !== $arr_settings['ssba_div_background'] ? 'background-color: ' . $arr_settings['ssba_div_background'] . ';' : '';
			$rounded = 'Y' === $arr_settings['ssba_div_rounded_corners'] ? '-moz-border-radius: 10px; -webkit-border-radius: 10px; -khtml-border-radius: 10px;  border-radius: 10px; -o-border-radius: 10px;' : '';
			$div_background2 = '' === $arr_settings['ssba_div_background'] ? 'background: none;' : '';
			$font = '' !== $arr_settings['ssba_font_family'] ? 'font-family: ' . $arr_settings['ssba_font_family'] . ';' : '';
			$font_size = '' !== $arr_settings['ssba_font_size'] ? 'font-size: ' . $arr_settings['ssba_font_size'] . 'px;' : '';
			$font_color = '' !== $arr_settings['ssba_font_color'] ? 'color: ' . $arr_settings['ssba_font_color'] . '!important;' : '';
			$font_weight = '' !== $arr_settings['ssba_font_weight'] ? 'font-weight: ' . $arr_settings['ssba_font_weight'] . ';' : '';

			// Use set options.
			$html_ssba_style = '	.ssba {
									' . esc_html( $div_padding ) . '
									' . esc_html( $border_width ) . '
									' . esc_html( $div_background1 ) . '
									' . esc_html( $rounded ) . '
								}
								.ssba img
								{
									width: ' . esc_html( $arr_settings['ssba_size'] ) . 'px !important;
									padding: ' . esc_html( $arr_settings['ssba_padding'] ) . 'px;
									border:  0;
									box-shadow: none !important;
									display: inline !important;
									vertical-align: middle;
									box-sizing: unset;
								}
								
								.ssba .fb-save
								{
								padding: ' . esc_html( $arr_settings['ssba_padding'] ) . 'px;
								line-height: ' . esc_html( (int) $arr_settings['ssba_size'] - 5 ) . 'px;
								}
								.ssba, .ssba a
								{
									text-decoration:none;
									' . esc_html( $div_background2 ) . '
									' . esc_html( $font ) . '
									' . esc_html( $font_size ) . '
									' . esc_html( $font_color ) . '
									' . esc_html( $font_weight ) . '
								}
								';

			// If counters option is set to Y.
			if ( 'Y' === $arr_settings['ssba_show_share_count'] ) {
				// Styles that apply to all counter css sets.
				$html_ssba_style .= '.ssba_sharecount:after, .ssba_sharecount:before {
									right: 100%;
									border: solid transparent;
									content: " ";
									height: 0;
									width: 0;
									position: absolute;
									pointer-events: none;
								}
								.ssba_sharecount:after {
									border-color: rgba(224, 221, 221, 0);
									border-right-color: #f5f5f5;
									border-width: 5px;
									top: 50%;
									margin-top: -5px;
								}
								.ssba_sharecount:before {
									border-color: rgba(85, 94, 88, 0);
									border-right-color: #e0dddd;
									border-width: 6px;
									top: 50%;
									margin-top: -6px;
								}
								.ssba_sharecount {
									font: 11px Arial, Helvetica, sans-serif;

									padding: 5px;
									-khtml-border-radius: 6px;
									-o-border-radius: 6px;
									-webkit-border-radius: 6px;
									-moz-border-radius: 6px;
									border-radius: 6px;
									position: relative;
									border: 1px solid #e0dddd;';

				// If default counter style has been chosen.
				if ( 'default' === $arr_settings['ssba_share_count_style'] ) {
					// Style share count.
					$html_ssba_style .= 'color: #555e58;
										background: #f5f5f5;
									}
									.ssba_sharecount:after {
										border-right-color: #f5f5f5;
									}';

				} elseif ( 'white' === $arr_settings['ssba_share_count_style'] ) {
					// Show white style share counts.
					$html_ssba_style .= 'color: #555e58;
										background: #ffffff;
									}
									.ssba_sharecount:after {
										border-right-color: #ffffff;
									}';

				} elseif ( 'blue' === $arr_settings['ssba_share_count_style'] ) {
					// Show blue style share counts.
					$html_ssba_style .= 'color: #ffffff;
										background: #42a7e2;
									}
									.ssba_sharecount:after {
										border-right-color: #42a7e2;
									}';
				}
			} // End if().

			// If there's any additional css.
			if ( '' !== $arr_settings['ssba_additional_css'] ) {
				// Add the additional CSS.
				$html_ssba_style .= $arr_settings['ssba_additional_css'];
			}

			wp_add_inline_style( "{$this->plugin->assets_prefix}-ssba", $html_ssba_style ); // WPCS: XSS ok.
		} else { // Else use set options.
			// Use custom styles.
			$html_ssba_style = $arr_settings['ssba_custom_styles'];

			wp_add_inline_style( "{$this->plugin->assets_prefix}-ssba", $html_ssba_style ); // WPCS: XSS ok.
		} // End if().
	}
}
