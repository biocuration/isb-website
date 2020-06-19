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
class Styles
{
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
    public function __construct($plugin, $class_ssba)
    {
        $this->plugin     = $plugin;
        $this->class_ssba = $class_ssba;
    }

    /**
     * Add css scripts for page/post use.
     *
     * @action wp_enqueue_scripts
     */
    public function ssba_page_scripts()
    {
        // Get settings.
        $arr_settings = $this->class_ssba->get_ssba_settings();

        if (is_ssl()) {
            $st_insights = 'https://ws.sharethis.com/button/st_insights.js';
        } else {
            $st_insights = 'http://w.sharethis.com/button/st_insights.js';
        }

        // Add call to st_insights.js with params.
        $url = add_query_arg(array(
            'publisher' => '4d48b7c5-0ae3-43d4-bfbe-3ff8c17a8ae6',
            'product'   => 'simpleshare',
        ), $st_insights);

        if ('Y' === $arr_settings['accepted_sharethis_terms']) {
            wp_enqueue_script('ssba-sharethis', $url, null, null);
            add_filter('script_loader_tag', array($this, 'ssba_script_tags'), 10, 2);
        }

        // Enqueue main script.
        wp_enqueue_script("{$this->plugin->assets_prefix}-ssba");
        wp_add_inline_script("{$this->plugin->assets_prefix}-ssba", sprintf('Main.boot( %s );',
            wp_json_encode(array())
        ));

        // If indie flower font is selected.
        if ('Indie Flower' === $arr_settings['ssba_font_family'] || 'Indie Flower' === $arr_settings['ssba_plus_font_family']) {
            // Font scripts.
            wp_enqueue_style("{$this->plugin->assets_prefix}-indie");
        }

        if ('Reenie Beanie' === $arr_settings['ssba_font_family'] || 'Reenie Beanie' === $arr_settings['ssba_plus_font_family']) {
            // Font scripts.
            wp_enqueue_style("{$this->plugin->assets_prefix}-reenie");
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
    public function ssba_script_tags($tag, $handle)
    {
        if ('ssba-sharethis' === $handle) {
            return str_replace('<script ', '<script id=\'st_insights_js\' ', $tag);
        }

        return $tag;
    }

    /**
     * Generate style.
     *
     * @action wp_enqueue_scripts
     */
    public function get_ssba_style()
    {
        // Query the db for current ssba settings.
        $arr_settings = $this->class_ssba->get_ssba_settings();

        // If the sharethis terms have been accepted.
        if ('Y' === $arr_settings['accepted_sharethis_terms'] && (('Y' !== $arr_settings['ssba_new_buttons'] && 'Y' !== $arr_settings['ignore_facebook_sdk']) || ('Y' === $arr_settings['ssba_new_buttons'] && 'Y' !== $arr_settings['plus_ignore_facebook_sdk']))) {
            // if a facebook app id has been set
            if ('' !== $arr_settings['facebook_app_id']) {
                $src = '//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6&appID=' . $arr_settings['facebook_app_id'];
            } else {
                $src = '//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6';
            }

            // If an app id has been entered.
            if ('' !== $arr_settings['facebook_app_id']) {
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
            if ('' !== $arr_settings['facebook_app_id']) {
                // If facebook insights have been enabled.
                if ('Y' === $arr_settings['facebook_insights']) {
                    // Add facebook meta tag.
                    echo '<meta property="fb:app_id" content="' . esc_attr($arr_settings['facebook_app_id']) . '" />';
                }
            }
        } // End if().

        // Check if custom styles haven't been set.
        if ('Y' !== $arr_settings['ssba_custom_styles_enabled'] && 'Y' !== $arr_settings['ssba_new_buttons']) {
            $div_padding     = '' !== $arr_settings['ssba_div_padding'] ? 'padding: ' . $arr_settings['ssba_div_padding'] . 'px;' : '';
            $border_width    = '' !== $arr_settings['ssba_border_width'] ? 'border: ' . $arr_settings['ssba_border_width'] . 'px solid ' . $arr_settings['ssba_div_border'] . ';' : '';
            $div_background1 = '' !== $arr_settings['ssba_div_background'] ? 'background-color: ' . $arr_settings['ssba_div_background'] . ';' : '';
            $rounded         = 'Y' === $arr_settings['ssba_div_rounded_corners'] ? '-moz-border-radius: 10px; -webkit-border-radius: 10px; -khtml-border-radius: 10px;  border-radius: 10px; -o-border-radius: 10px;' : '';
            $div_background2 = '' === $arr_settings['ssba_div_background'] ? 'background: none;' : '';
            $font            = '' !== $arr_settings['ssba_font_family'] ? 'font-family: ' . $arr_settings['ssba_font_family'] . ';' : '';
            $font_size       = '' !== $arr_settings['ssba_font_size'] ? 'font-size: ' . $arr_settings['ssba_font_size'] . 'px;' : '';
            $font_color      = '' !== $arr_settings['ssba_font_color'] ? 'color: ' . $arr_settings['ssba_font_color'] . '!important;' : '';
            $font_weight     = '' !== $arr_settings['ssba_font_weight'] ? 'font-weight: ' . $arr_settings['ssba_font_weight'] . ';' : '';

            // Use set options.
            $html_ssba_style = '	.ssba {
									' . esc_html($div_padding) . '
									' . esc_html($border_width) . '
									' . esc_html($div_background1) . '
									' . esc_html($rounded) . '
								}
								.ssba img
								{
									width: ' . esc_html($arr_settings['ssba_size']) . 'px !important;
									padding: ' . esc_html($arr_settings['ssba_padding']) . 'px;
									border:  0;
									box-shadow: none !important;
									display: inline !important;
									vertical-align: middle;
									box-sizing: unset;
								}

								.ssba-classic-2 .ssbp-text {
									display: none!important;
								}

								.ssba .fb-save
								{
								padding: ' . esc_html($arr_settings['ssba_padding']) . 'px;
								';

            $html_ssba_style .= 'line-height: ' . esc_html((int)$arr_settings['ssba_size'] - 5) . 'px; }
								.ssba, .ssba a
								{
									text-decoration:none;
									' . esc_html($div_background2) . '
									' . esc_html($font) . '
									' . esc_html($font_size) . '
									' . esc_html($font_color) . '
									' . esc_html($font_weight) . '
								}
								';

            // If counters option is set to Y.
            if ('Y' === $arr_settings['ssba_show_share_count']) {
                // Styles that apply to all counter css sets.
                $html_ssba_style .= ' .ssba_sharecount:after, .ssba_sharecount:before {
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
                if ('default' === $arr_settings['ssba_share_count_style']) {
                    // Style share count.
                    $html_ssba_style .= 'color: #555e58;
										background: #f5f5f5;
									}
									.ssba_sharecount:after {
										border-right-color: #f5f5f5;
									}';

                } elseif ('white' === $arr_settings['ssba_share_count_style']) {
                    // Show white style share counts.
                    $html_ssba_style .= 'color: #555e58;
										background: #ffffff;
									}
									.ssba_sharecount:after {
										border-right-color: #ffffff;
									}';

                } elseif ('blue' === $arr_settings['ssba_share_count_style']) {
                    // Show blue style share counts.
                    $html_ssba_style .= 'color: #ffffff;
										background: #42a7e2;
									}
									.ssba_sharecount:after {
										border-right-color: #42a7e2;
									}';
                }
            }

            // If there's any additional css.
            if ('' !== $arr_settings['ssba_additional_css']) {
                // Add the additional CSS.
                $html_ssba_style .= $arr_settings['ssba_additional_css'];
            }

            wp_add_inline_style("{$this->plugin->assets_prefix}-ssba", $html_ssba_style); // WPCS: XSS ok.
        } elseif ('Y' !== $arr_settings['ssba_new_buttons']) { // Else use set options.
            // Use custom styles.
            $html_ssba_style = $arr_settings['ssba_custom_styles'];

            wp_add_inline_style("{$this->plugin->assets_prefix}-ssba", $html_ssba_style); // WPCS: XSS ok.
        }

        if ('Y' === $arr_settings['ssba_new_buttons']) {
            // Plus styles.
            $plus_height       = '' !== $arr_settings['ssba_plus_height'] ? 'height: ' . $arr_settings['ssba_plus_height'] . 'px!important;' : 'height: 48px!important;';
            $plus_width        = '' !== $arr_settings['ssba_plus_width'] ? 'width: ' . $arr_settings['ssba_plus_width'] . 'px!important;' : 'width: 48px!important;';
            $plus_icon         = '' !== $arr_settings['ssba_plus_icon_size'] ? 'line-' . $plus_height . '; font-size: ' . $arr_settings['ssba_plus_icon_size'] . 'px;' : 'line-' . $plus_height . '; font-size: 18px;';
            $plus_margin       = '' !== $arr_settings['ssba_plus_margin'] ? 'margin-left: ' . $arr_settings['ssba_plus_margin'] . 'px!important;' : 'margin-left: 7px!important;';
            $plus_font_style   = '' !== $arr_settings['ssba_plus_font_family'] ? 'font-family: ' . $arr_settings['ssba_plus_font_family'] . ';' : 'font-family: inherit;';
            $plus_font_size    = '' !== $arr_settings['ssba_plus_font_size'] ? 'font-size: ' . $arr_settings['ssba_plus_font_size'] . 'px;' : 'font-size: 12px;';
            $plus_font_weight  = '' !== $arr_settings['ssba_plus_font_weight'] ? 'font-weight: ' . $arr_settings['ssba_plus_font_weight'] . ';' : 'font-weight: normal;';
            $plus_font_color   = '' !== $arr_settings['ssba_plus_font_color'] ? 'color: ' . $arr_settings['ssba_plus_font_color'] . '!important;' : '';
            $plus_icon_color   = '' !== $arr_settings['ssba_plus_icon_color'] ? 'color: ' . $arr_settings['ssba_plus_icon_color'] . '!important;' : '';
            $plus_icon_hover   = '' !== $arr_settings['ssba_plus_icon_hover_color'] ? 'color: ' . $arr_settings['ssba_plus_icon_hover_color'] . '!important;' : '';
            $plus_button_color = '' !== $arr_settings['ssba_plus_button_color'] ? 'background-color: ' . $arr_settings['ssba_plus_button_color'] . '!important;' : '';
            $plus_button_hover = '' !== $arr_settings['ssba_plus_button_hover_color'] ? 'background-color: ' . $arr_settings['ssba_plus_button_hover_color'] . '!important;' : '';

            $html_ssba_style =
                '.ssba img
								{border:  0;
									box-shadow: none !important;
									display: inline !important;
									vertical-align: middle;
									box-sizing: unset;
								}

								.ssba-classic-2 .ssbp-text {
									display: none!important;
								}
					.ssbp-list li a {' .
                esc_html($plus_height) . ' ' .
                esc_html($plus_width) . ' ' .
                esc_html($plus_button_color) . '
					}
					.ssbp-list li a:hover {' .
                esc_html($plus_button_hover) . '
					}

					.ssbp-list li a::before {' .
                esc_html($plus_icon) .
                esc_html($plus_icon_color) .
                '}
					.ssbp-list li a:hover::before {' .
                esc_html($plus_icon_hover) .
                '}
					.ssbp-list li {
					' . esc_html($plus_margin) . '
					}

					.ssba-share-text {
					' . esc_html($plus_font_size) . ' '
                . esc_html($plus_font_color) . ' '
                . esc_html($plus_font_weight) . ' '
                . esc_html($plus_font_style) . '
						}';

            if ('' !== $arr_settings['ssba_plus_additional_css'] && 'Y' === $arr_settings['ssba_new_buttons']) {
                $html_ssba_style .= $arr_settings['ssba_plus_additional_css'];
            }

            wp_add_inline_style("{$this->plugin->assets_prefix}-ssba", $html_ssba_style); // WPCS: XSS ok.
        }

        // If sharebar custom css is enabled use it.
        // Check if custom styles haven't been set.
        if ('Y' !== $arr_settings['ssba_bar_custom_styles_enabled']) {
            // Share bar styles.
            $bar_height       = '' !== $arr_settings['ssba_bar_height'] ? 'height: ' . $arr_settings['ssba_bar_height'] . 'px!important;' : 'height: 48px!important;';
            $bar_width        = '' !== $arr_settings['ssba_bar_width'] ? 'width: ' . $arr_settings['ssba_bar_width'] . 'px!important;' : 'width: 48px!important;';
            $bar_icon         = '' !== $arr_settings['ssba_bar_icon_size'] ? 'line-' . $bar_height . '; font-size: ' . $arr_settings['ssba_bar_icon_size'] . 'px;' : 'line-' . $bar_height . '; font-size: 18px;';
            $bar_margin       = '' !== $arr_settings['ssba_bar_margin'] ? 'margin: ' . $arr_settings['ssba_bar_margin'] . 'px 0!important;' : '';
            $bar_button_color = '' !== $arr_settings['ssba_bar_button_color'] ? 'background-color: ' . $arr_settings['ssba_bar_button_color'] . '!important;' : '';
            $bar_button_hover = '' !== $arr_settings['ssba_bar_button_hover_color'] ? 'background-color: ' . $arr_settings['ssba_bar_button_hover_color'] . '!important;' : '';
            $bar_icon_color   = '' !== $arr_settings['ssba_bar_icon_color'] ? 'color: ' . $arr_settings['ssba_bar_icon_color'] . '!important;' : '';
            $bar_icon_hover   = '' !== $arr_settings['ssba_bar_icon_hover_color'] ? 'color: ' . $arr_settings['ssba_bar_icon_hover_color'] . '!important;' : '';
            $bar_break_point  = 'Y' !== $arr_settings['ssba_bar_mobile'] ? 'display: none;' : 'display: block;';
            $the_breakpoint   = '' === $arr_settings['ssba_mobile_breakpoint'] || null === $arr_settings['ssba_mobile_breakpoint'] ? '750' : $arr_settings['ssba_mobile_breakpoint'];

            $html_bar_ssba_style = '
			   #ssba-bar-2 .ssbp-bar-list {
					max-' . esc_html($bar_width) . ';
			   }
			   #ssba-bar-2 .ssbp-bar-list li a {' .
                                   esc_html($bar_height) . ' ' .
                                   esc_html($bar_width) . ' ' .
                                   esc_html($bar_button_color) . '
				}
				#ssba-bar-2 .ssbp-bar-list li a:hover {' .
                                   esc_html($bar_button_hover) . '
				}

				#ssba-bar-2 .ssbp-bar-list li a::before {' .
                                   esc_html($bar_icon) .
                                   esc_html($bar_icon_color) .
                                   '}
				#ssba-bar-2 .ssbp-bar-list li a:hover::before {' .
                                   esc_html($bar_icon_hover) .
                                   '}
				#ssba-bar-2 .ssbp-bar-list li {
				' . esc_html($bar_margin) . '
				}';

            $html_bar_ssba_style .= '@media only screen and ( max-width: ' . $the_breakpoint . 'px ) {
				#ssba-bar-2 {
				' . $bar_break_point . '
				}
			}';

            // If there's any additional css.
            if ('' !== $arr_settings['ssba_bar_additional_css']) {
                // Add the additional CSS.
                $html_bar_ssba_style .= $arr_settings['ssba_bar_additional_css'];
            }

            wp_add_inline_style("{$this->plugin->assets_prefix}-ssba", $html_bar_ssba_style); // WPCS: XSS ok.
        }
    }
}
