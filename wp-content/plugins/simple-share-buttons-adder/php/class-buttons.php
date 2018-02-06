<?php
/**
 * Buttons.
 *
 * @package SimpleShareButtonsAdder
 */

namespace SimpleShareButtonsAdder;

/**
 * Buttons Class
 *
 * @package SimpleShareButtonsAdder
 */
class Buttons {

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
	 * Format the returned number.
	 *
	 * @param integer $int_number The number to format.
	 *
	 * @return string
	 */
	public function ssba_format_number( $int_number ) {
		// If the number is greater than or equal to 1000.
		if ( $int_number >= 1000 ) {
			// Divide by 1000 and add k.
			$int_number = round( ( $int_number / 1000 ), 1 ) . 'k';
		}

		// Return the number.
		return $int_number;
	}

	/**
	 * Adds a filter around the content.
	 *
	 * @action wp_head, 99
	 */
	public function ssba_add_button_filter() {
		$arr_settings = $this->class_ssba->get_ssba_settings();

		add_filter( 'the_content', array( $this, 'show_share_buttons' ), (int) $arr_settings['ssba_content_priority'] );

		// If we wish to add to excerpts.
		if ( isset( $arr_settings['ssba_excerpts'] ) && 'Y' !== $arr_settings['ssba_excerpts'] ) {
			add_filter( 'the_excerpt', array( $this, 'show_share_buttons' ) );
		}
	}

	/**
	 * Call back for showing share buttons.
	 *
	 * @param string $content The current page or post content.
	 * @param bool   $boo_shortcode Whether to use shortcode or not.
	 * @param array  $atts Manual replacements for page url/title.
	 *
	 * @return string
	 */
	public function show_share_buttons( $content, $boo_shortcode = false, $atts = '' ) {
		global $post;

		// Variables.
		$html_content = $content;
		$str_share_text = '';
		$pattern = get_shortcode_regex();

		// Ssba_hide shortcode is in the post content and instance is not called by shortcode ssba.
		if ( isset( $post->post_content )
			 &&
			 preg_match_all( '/' . $pattern . '/s', $post->post_content, $matches )
			 &&
			 array_key_exists( 2, $matches )
			 &&
			 in_array( 'ssba_hide', $matches[2], true )
			 &&
			 ! $boo_shortcode
		) {
			// Exit the function returning the content without the buttons.
			return $content;
		}

		// Get sbba settings.
		$arr_settings = $this->class_ssba->get_ssba_settings();

		// Placement on pages/posts/categories/archives/homepage.
		if (
			( ! is_home() && ! is_front_page() && is_page() && 'Y' === $arr_settings['ssba_pages'] )
			 ||
			 ( is_single() && 'Y' === $arr_settings['ssba_posts'] )
			 ||
			 ( is_category() && 'Y' === $arr_settings['ssba_cats_archs'] )
			 ||
			 ( is_archive() && 'Y' === $arr_settings['ssba_cats_archs'] )
			 ||
			 ( ( is_home() || is_front_page() ) && 'Y' === $arr_settings['ssba_homepage'] )
			 ||
			 $boo_shortcode
		) {
			wp_enqueue_style( "{$this->plugin->assets_prefix}-ssba" );

			// If not shortcode.
			if ( isset( $atts['widget'] ) && 'Y' === $atts['widget'] && '' === $arr_settings['ssba_widget_text'] ) { // Use widget share text.
				$str_share_text = $arr_settings['ssba_widget_text'];
			} else { // Use normal share text.
				$str_share_text = $arr_settings['ssba_share_text'];
			}

			// Post id.
			$int_post_id = $post->ID;

			// Ssba div.
			$html_share_buttons = '<!-- Simple Share Buttons Adder (' . esc_html( SSBA_VERSION ) . ') simplesharebuttons.com --><div class="ssba ssba-wrap">';

			// Center if set so.
			$html_share_buttons .= '<div style="text-align:' . esc_attr( $arr_settings['ssba_align'] ) . '">';

			// Add custom text if set and set to placement above or left.
			if ( '' !== $str_share_text && ( 'above' === $arr_settings['ssba_text_placement'] || 'left' === $arr_settings['ssba_text_placement'] ) ) {
				// Check if user has left share link box checked.
				if ( 'Y' === $arr_settings['ssba_link_to_ssb'] ) {
					// Share text with link.
					$html_share_buttons .= '<a href="https://simplesharebuttons.com" target="_blank">' . esc_html( $str_share_text ) . '</a>';
				} else { // Just display the share text.
					// Share text.
					$html_share_buttons .= $str_share_text;
				}
				// Add a line break if set to above.
				$html_share_buttons .= 'above' === $arr_settings['ssba_text_placement'] ?  '<br/>' : '';
			}

			// If running standard.
			if ( ! $boo_shortcode ) {
				// Use wordpress functions for page/post details.
				$url_current_page = get_permalink( $post->ID );
				$str_page_title = get_the_title( $post->ID );

			} else { // Using shortcode.
				// Set page URL and title as set by user or get if needed.
				$url_current_page = isset( $atts['url'] ) ? $atts['url'] : $this->ssba_current_url( $atts );
				$str_page_title = ( isset( $atts['title'] ) ? $atts['title'] : get_the_title() );
			}

			// Strip any unwanted tags from the page title.
			$str_page_title = esc_attr( strip_tags( $str_page_title ) );

			// The buttons.
			$html_share_buttons .= $this->get_share_buttons( $arr_settings, $url_current_page, $str_page_title, $int_post_id );

			// Add custom text if set and set to placement right or below.
			if ( '' !== $str_share_text && ( 'right' === $arr_settings['ssba_text_placement'] || 'below' === $arr_settings['ssba_text_placement'] ) ) {
				// Add a line break if set to above.
				$html_share_buttons .= 'below' === $arr_settings['ssba_text_placement'] ? '<br/>' : '';

				// Check if user has checked share link option.
				if ( 'Y' === $arr_settings['ssba_link_to_ssb'] ) {
					// Share text with link.
					$html_share_buttons .= '<a href="https://simplesharebuttons.com" target="_blank">' . esc_html( $str_share_text ) . '</a>';
				} else { // Just display the share text.
					// Share text.
					$html_share_buttons .= $str_share_text;
				}
			}

			// Close center if set.
			$html_share_buttons .= '</div></div>';

			// If not using shortcode.
			if ( ! $boo_shortcode ) {
				// Switch for placement of ssba.
				switch ( $arr_settings['ssba_before_or_after'] ) {
					case 'before': // Before the content.
						$html_content = $html_share_buttons . $content;
						break;
					case 'after': // After the content.
						$html_content = $content . $html_share_buttons;
						break;
					case 'both': // Before and after the content.
						$html_content = $html_share_buttons . $content . $html_share_buttons;
						break;
				}
			} else { // If using shortcode.
				// Just return buttons.
				$html_content = $html_share_buttons;
			}
		} // End if().

		// Return content and share buttons.
		return $html_content;
	}

	/**
	 * Function that shows the share bar if enabled.
	 *
	 * @action wp_head, 99
	 */
	public function show_share_bar() {
		global $post, $wp;

		// Get sbba settings.
		$arr_settings = $this->class_ssba->get_ssba_settings();

		// Get current url.
		$url_current_page = home_url( add_query_arg( array(), $wp->request ) );

		// Placement on pages/posts/categories/archives/homepage.
		if (
			( ! is_home() && ! is_front_page() && is_page() && isset( $arr_settings['ssba_bar_pages'] ) && 'Y' === $arr_settings['ssba_bar_pages'] )
			||
			( is_single() && isset( $arr_settings['ssba_bar_posts'] ) && 'Y' === $arr_settings['ssba_bar_posts'] )
			||
			( is_category() && isset( $arr_settings['ssba_bar_cats_archs'] ) && 'Y' === $arr_settings['ssba_bar_cats_archs'] )
			||
			( is_archive() && isset( $arr_settings['ssba_bar_cats_archs'] ) && 'Y' === $arr_settings['ssba_bar_cats_archs'] )
			||
			( ( is_home() || is_front_page() ) && isset( $arr_settings['ssba_bar_homepage'] ) && 'Y' === $arr_settings['ssba_bar_homepage'] )
		) {
			$html_share_buttons .= '<div style="position:fixed;left:0;z-index:1000000000;top:30%;">';
			// The buttons.
			$html_share_buttons .= $this->get_share_bar( $arr_settings, $url_current_page, $post->post_title, $post->ID );
			$html_share_buttons .= '</div>';
			echo wp_kses_post( $html_share_buttons );
		}
	}

	/**
	 * Shortcode for adding buttons.
	 *
	 * @param array $atts The current shortcodes attributes.
	 *
	 * @shortcode ssba-buttons
	 *
	 * @return string
	 */
	public function ssba_buttons( $atts ) {
		// Get buttons - NULL for $content, TRUE for shortcode flag.
		$html_share_buttons = $this->show_share_buttons( null, true, $atts );

		// Return buttons.
		return $html_share_buttons;
	}

	/**
	 * Shortcode for adding buttons.
	 *
	 * @param array $atts The current shortcodes attributes.
	 *
	 * @shortcode ssba
	 *
	 * @return string
	 */
	public function ssba_orig_buttons( $atts ) {
		// Get buttons - NULL for $content, TRUE for shortcode flag.
		$html_share_buttons = $this->show_share_buttons( null, true, $atts );

		// Return buttons.
		return $html_share_buttons;
	}

	/**
	 * Shortcode for hiding buttons
	 *
	 * @param string $content The current page or posts content.
	 *
	 * @shortcode ssba_hide
	 */
	public function ssba_hide( $content ) {
		// No need to do anything here!
	}

	/**
	 * Get URL function.
	 *
	 * @param array $atts The supplied attributes.
	 *
	 * @return string
	 */
	public function ssba_current_url( $atts ) {

		if ( ! isset( $_SERVER['SERVER_NAME'] ) || ! isset( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}

		// If multisite has been set to true.
		if ( isset( $atts['multisite'] ) && isset( $_SERVER['QUERY_STRING'] ) ) {
			global $wp;

			$url = add_query_arg( sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ), '', home_url( $wp->request ) ); // WPCS: CSRF ok.

			return esc_url( $url );
		}

		// Add http.
		$url_current_page = 'http';

		// Add s to http if required.
		if ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ) {
			$url_current_page .= 's';
		}

		// Add colon and forward slashes.
		$url_current_page .= '://' . sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] ) );

		// Return url.
		return esc_url( $url_current_page );
	}

	/**
	 * Get set share buttons.
	 *
	 * @param array   $arr_settings The current ssba settings.
	 * @param string  $url_current_page The current pages url.
	 * @param string  $str_page_title The page title.
	 * @param integer $int_post_id The post id.
	 *
	 * @return string
	 */
	public function get_share_buttons( $arr_settings, $url_current_page, $str_page_title, $int_post_id ) {
		// Variables.
		$html_share_buttons = '';

		// Explode saved include list and add to a new array.
		$arr_selected_ssba = explode( ',', $arr_settings['ssba_selected_buttons'] );

		// Check if array is not empty.
		if ( '' !== $arr_settings['ssba_selected_buttons'] ) {

			// Add post ID to settings array.
			$arr_settings['post_id'] = $int_post_id;

			// If show counters option is selected.
			if ( 'Y' === $arr_settings['ssba_show_share_count'] ) {
				// Set show flag to true.
				$boo_show_share_count = true;

				// If show counters once option is selected.
				if ( 'Y' === $arr_settings['ssba_share_count_once'] ) {
					// If not a page or post.
					if ( ! is_page() && ! is_single() ) {
						// Let show flag to false.
						$boo_show_share_count = false;
					}
				}
			} else {
				// Set show flag to false.
				$boo_show_share_count = false;
			}

			// For each included button.
			foreach ( $arr_selected_ssba as $str_selected ) {
				$str_get_button = 'ssba_' . $str_selected;

				// Add a list item for each selected option.
				$html_share_buttons .= $this->$str_get_button( $arr_settings, $url_current_page, $str_page_title, $boo_show_share_count );
			}
		}

		// Return share buttons.
		return $html_share_buttons;
	}

	/**
	 * Get facebook button.
	 *
	 * @param array  $arr_settings The current ssba settings.
	 * @param string $url_current_page The current page url.
	 * @param string $str_page_title The page title.
	 * @param bool   $boo_show_share_count Show share count or not.
	 *
	 * @return string
	 */
	public function ssba_facebook( $arr_settings, $url_current_page, $str_page_title, $boo_show_share_count ) {
		$nofollow = 'Y' === $arr_settings['ssba_rel_nofollow'] ? ' rel="nofollow"' : '';
		$target = 'Y' === $arr_settings['ssba_share_new_window'] ? ' target="_blank" ' : '';

		// If the sharethis terms have been accepted.
		if ( 'Y' === $arr_settings['accepted_sharethis_terms'] && '' !== $arr_settings['facebook_app_id'] ) {
			// Facebook share link.
			$html_share_buttons = '<a data-site="" data-facebook="mobile" class="ssba_facebook_share" data-href="' . esc_attr( $url_current_page ) . '" href="https://www.facebook.com/dialog/share?app_id=' . esc_attr( $arr_settings['facebook_app_id'] ) . '&display=popup&href=' . esc_attr( $url_current_page ) . '&redirect_uri=' . esc_url( $url_current_page ) . '" ' . esc_attr( $target . $nofollow ) . '>';
		} else {
			// Facebook share link.
			$html_share_buttons = '<a data-site="" class="ssba_facebook_share" href="http://www.facebook.com/sharer.php?u=' . esc_attr( $url_current_page ) . '" ' . $target . $nofollow . '>';
		}

		// If not using custom.
		if ( 'custom' !== $arr_settings['ssba_image_set'] ) {
			// Show selected ssba image.
			$html_share_buttons .= '<img src="' . esc_url( plugins_url() ) . '/simple-share-buttons-adder/buttons/' . esc_attr( $arr_settings['ssba_image_set'] ) . '/facebook.png" title="Facebook" class="ssba ssba-img" alt="Share on Facebook" />';
		} else { // If using custom images.
			// Show custom image.
			$html_share_buttons .= '<img src="' . esc_url( $arr_settings['ssba_custom_facebook'] ) . '" title="Facebook" class="ssba ssba-img" alt="Share on Facebook" />';
		}

		// Close href.
		$html_share_buttons .= '</a>';

		// If show share count is set to Y.
		if ( 'Y' === $arr_settings['ssba_show_share_count'] && $boo_show_share_count ) {
			// Get and add facebook share count.
			$html_share_buttons .= '<span class="ssba_sharecount">' . esc_html( $this->get_facebook_share_count( $url_current_page, $arr_settings ) ) . '</span>';
		}

		// Return share buttons.
		return $html_share_buttons;
	}

	/**
	 * Get set share buttons.
	 *
	 * @param array   $arr_settings The current ssba settings.
	 * @param string  $url_current_page The current pages url.
	 * @param string  $str_page_title The page title.
	 * @param integer $int_post_id The post id.
	 *
	 * @return string
	 */
	public function get_share_bar( $arr_settings, $url_current_page, $str_page_title, $int_post_id ) {
		// Variables.
		$html_share_buttons = '';

		// Explode saved include list and add to a new array.
		$arr_selected_ssba = explode( ',', $arr_settings['ssba_selected_share_buttons'] );

		// Check if array is not empty.
		if ( '' !== $arr_settings['ssba_selected_share_buttons'] ) {

			// Add post ID to settings array.
			$arr_settings['post_id'] = $int_post_id;

			// If show counters option is selected.
			if ( 'Y' === $arr_settings['ssba_show_share_count'] ) {
				// Set show flag to true.
				$boo_show_share_count = true;

				// If show counters once option is selected.
				if ( 'Y' === $arr_settings['ssba_share_count_once'] ) {
					// If not a page or post.
					if ( ! is_page() && ! is_single() ) {
						// Let show flag to false.
						$boo_show_share_count = false;
					}
				}
			} else {
				// Set show flag to false.
				$boo_show_share_count = false;
			}

			// For each included button.
			foreach ( $arr_selected_ssba as $str_selected ) {
				if ( '' !== $str_selected ) {
					$str_get_button = 'ssba_' . $str_selected;

					// Add a list item for each selected option.
					$html_share_buttons .= $this->$str_get_button( $arr_settings, $url_current_page, $str_page_title, $boo_show_share_count );
				}
			}
		}

		// Return share buttons.
		return $html_share_buttons;
	}

	/**
	 * Get facebook button.
	 *
	 * @param array  $arr_settings The current ssba settings.
	 * @param string $url_current_page The current page url.
	 * @param string $str_page_title The page title.
	 * @param bool   $boo_show_share_count Show share count or not.
	 *
	 * @return string
	 */
	public function ssba_facebook_save( $arr_settings, $url_current_page, $str_page_title, $boo_show_share_count ) {
		// If the sharethis terms have been accepted.
		if ( 'Y' === $arr_settings['accepted_sharethis_terms'] ) {
			// Add facebook save button.
			return '<span class="fb-save" style="display:inline-block" data-uri="' . esc_attr( $url_current_page ) . '"></span>';
		}
	}

	/**
	 * Get facebook share count.
	 *
	 * @param string $url_current_page Current url.
	 * @param array  $arr_settings Current ssba settings.
	 *
	 * @return string
	 */
	public function get_facebook_share_count( $url_current_page, $arr_settings ) {
		$cache_key = sprintf(
			'facebook_sharecount_%s',
			wp_hash( $url_current_page )
		);

		// Get the longer cached value from the Transient API.
		$long_cached_count = get_transient( "ssba_{$cache_key}" );
		if ( false === $long_cached_count ) {
			$long_cached_count = 0;
		}

		// If sharedcount.com is enabled.
		if ( $arr_settings['sharedcount_enabled'] ) {
			// Request from sharedcount.com.
			$sharedcount = wp_safe_remote_get( 'https://' . $arr_settings['sharedcount_plan'] . '.sharedcount.com/url?url=' . $url_current_page . '&apikey=' . $arr_settings['sharedcount_api_key'], array(
				'timeout' => 6,
			) );

			// If no error.
			if ( is_wp_error( $sharedcount ) ) {
				return $this->ssba_format_number( $long_cached_count );
			}

			// Decode and return count.
			$shared_resp = json_decode( $sharedcount['body'], true );
			$sharedcount = $long_cached_count;

			if ( isset( $shared_resp['Facebook']['share_count'] ) ) {
				$sharedcount = (int) $shared_resp['Facebook']['share_count'];
				wp_cache_set( $cache_key, $sharedcount, 'ssba', MINUTE_IN_SECONDS * 2 );
				set_transient( "ssba_{$cache_key}", $sharedcount, DAY_IN_SECONDS );
			}

			return $this->ssba_format_number( $sharedcount );
		} else {
			// Get results from facebook.
			$html_facebook_share_details = wp_safe_remote_get( 'http://graph.facebook.com/' . $url_current_page, array(
				'timeout' => 6,
			) );

			// If no error.
			if ( is_wp_error( $html_facebook_share_details ) ) {
				return $this->ssba_format_number( $long_cached_count );
			}

			// Decode and return count.
			$arr_facebook_share_details = json_decode( $html_facebook_share_details['body'], true );
			$int_facebook_share_count = $long_cached_count;

			if ( isset( $arr_facebook_share_details['share']['share_count'] ) ) {
				$int_facebook_share_count = (int) $arr_facebook_share_details['share']['share_count'];

				wp_cache_set( $cache_key, $int_facebook_share_count, 'ssba', MINUTE_IN_SECONDS * 2 );
				set_transient( "ssba_{$cache_key}", $int_facebook_share_count, DAY_IN_SECONDS );
			}

			return $this->ssba_format_number( $int_facebook_share_count );
		} // End if().
	}

	/**
	 * Get twitter button.
	 *
	 * @param array  $arr_settings The current ssba settings.
	 * @param string $url_current_page The current page url.
	 * @param string $str_page_title The page title.
	 * @param bool   $boo_show_share_count Show share count or not.
	 *
	 * @return string
	 */
	public function ssba_twitter( $arr_settings, $url_current_page, $str_page_title, $boo_show_share_count ) {
		$nofollow = 'Y' === $arr_settings['ssba_rel_nofollow'] ? ' rel="nofollow"' : '';
		$target = 'Y' === $arr_settings['ssba_share_new_window'] ? ' target="_blank" ' : '';

		// Format the URL into friendly code.
		$twitter_share_text = rawurlencode( html_entity_decode( $str_page_title . ' ' . $arr_settings['ssba_twitter_text'], ENT_COMPAT, 'UTF-8' ) );

		// Twitter share link.
		$html_share_buttons = '<a data-site="" class="ssba_twitter_share" href="http://twitter.com/share?url=' . esc_attr( $url_current_page ) . '&amp;text=' . esc_attr( $twitter_share_text ) . '" ' . esc_attr( $target . $nofollow ) . '>';

		// If image set is not custom.
		if ( 'custom' !== $arr_settings['ssba_image_set'] ) {
			// Show ssba image.
			$html_share_buttons .= '<img src="' . plugins_url() . '/simple-share-buttons-adder/buttons/' . esc_attr( $arr_settings['ssba_image_set'] ) . '/twitter.png" title="Twitter" class="ssba ssba-img" alt="Tweet about this on Twitter" />';
		} else { // If using custom images.
			// Show custom image.
			$html_share_buttons .= '<img src="' . esc_url( $arr_settings['ssba_custom_twitter'] ) . '" title="Twitter" class="ssba ssba-img" alt="Tweet about this on Twitter" />';
		}

		// Close href.
		$html_share_buttons .= '</a>';

		// If show share count is set to Y.
		if ( 'Y' === $arr_settings['ssba_show_share_count'] && $boo_show_share_count ) {
			// Newsharedcount needs to be enabled.
			if ( 'Y' === $arr_settings['twitter_newsharecounts'] ) {
				$html_share_buttons .= '<span class="ssba_sharecount">' . esc_html( $this->ssba_twitter_count( $url_current_page ) ) . '</span>';
			}
		}

		// Return share buttons.
		return $html_share_buttons;
	}

	/**
	 * Get twitter share count.
	 *
	 * @param string $url_current_page The current page url.
	 *
	 * @return int|string
	 */
	public function ssba_twitter_count( $url_current_page ) {
		// Get results from newsharecounts and return the number of shares.
		$result = wp_safe_remote_get( 'http://public.newsharecounts.com/count.json?url=' . $url_current_page, array(
			'timeout' => 6,
		) );

		// Check there was an error.
		if ( is_wp_error( $result ) ) {
			return 0;
		}

		// Decode data.
		$result = json_decode( $result['body'], true );
		$count  = isset( $result['count'] ) ? $result['count'] : 0;

		// Return.
		return $this->ssba_format_number( $count );
	}

	/**
	 * Get google+ button.
	 *
	 * @param array  $arr_settings The current ssba settings.
	 * @param string $url_current_page The current page url.
	 * @param string $str_page_title The page title.
	 * @param bool   $boo_show_share_count Show share count or not.
	 *
	 * @return string
	 */
	public function ssba_google( $arr_settings, $url_current_page, $str_page_title, $boo_show_share_count ) {
		$nofollow = 'Y' === $arr_settings['ssba_rel_nofollow'] ? ' rel="nofollow"' : '';
		$target = 'Y' === $arr_settings['ssba_share_new_window'] ? ' target="_blank" ' : '';

		// Google share link.
		$html_share_buttons = '<a data-site="" class="ssba_google_share" href="https://plus.google.com/share?url=' . esc_attr( $url_current_page ) . '" ' . esc_attr( $target . $nofollow ) . '>';

		// If image set is not custom.
		if ( 'custom' !== $arr_settings['ssba_image_set'] ) {
			// Show ssba image.
			$html_share_buttons .= '<img src="' . esc_attr( plugins_url() ) . '/simple-share-buttons-adder/buttons/' . esc_attr( $arr_settings['ssba_image_set'] ) . '/google.png" title="Google+" class="ssba ssba-img" alt="Share on Google+" />';
		} else { // If using custom images.
			// Show custom image.
			$html_share_buttons .= '<img src="' . esc_url( $arr_settings['ssba_custom_google'] ) . '" title="Share on Google+" class="ssba ssba-img" alt="Google+" />';
		}

		// Close href.
		$html_share_buttons .= '</a>';

		// If show share count is set to Y.
		if ( 'Y' === $arr_settings['ssba_show_share_count'] && $boo_show_share_count ) {
			$html_share_buttons .= '<span class="ssba_sharecount">' . esc_html( $this->get_google_share_count( $url_current_page ) ) . '</span>';
		}

		// Return share buttons.
		return $html_share_buttons;
	}

	/**
	 * Get google share count.
	 *
	 * @param string $url_current_page The current page url.
	 *
	 * @return string
	 */
	public function get_google_share_count( $url_current_page ) {
		$args = array(
			'method'    => 'POST',
			'headers'   => array(
				// Setup content type to JSON.
				'Content-Type' => 'application/json',
			),
			// Setup POST options to Google API.
			'body'      => wp_json_encode( array(
				'method'     => 'pos.plusones.get',
				'id'         => 'p',
				'method'     => 'pos.plusones.get',
				'jsonrpc'    => '2.0',
				'key'        => 'p',
				'apiVersion' => 'v1',
				'params'     => array(
					'nolog'   => true,
					'id'      => $url_current_page,
					'source'  => 'widget',
					'userId'  => '@viewer',
					'groupId' => '@self',
				),
			) ),
			// Disable checking SSL sertificates.
			'sslverify' => false,
		);

		// Retrieves JSON with HTTP POST method for current URL.
		$json_string = wp_remote_post( 'https://clients6.google.com/rpc', $args );

		if ( is_wp_error( $json_string ) ) {
			// Return zero if response is error.
			return '0';
		} else {
			$json = json_decode( $json_string['body'], true );

			// Return count of Google +1 for requsted URL.
			return $this->ssba_format_number( intval( $json['result']['metadata']['globalCounts']['count'] ) );
		}
	}

	/**
	 * Get diggit button.
	 *
	 * @param array  $arr_settings The current ssba settings.
	 * @param string $url_current_page The current page url.
	 * @param string $str_page_title The page title.
	 * @param bool   $boo_show_share_count Show share count or not.
	 *
	 * @return string
	 */
	public function ssba_diggit( $arr_settings, $url_current_page, $str_page_title, $boo_show_share_count ) {
		$nofollow = 'Y' === $arr_settings['ssba_rel_nofollow'] ? ' rel="nofollow"' : '';
		$target = 'Y' === $arr_settings['ssba_share_new_window'] ? ' target="_blank" ' : '';

		// Diggit share link.
		$html_share_buttons = '<a data-site="digg" class="ssba_diggit_share ssba_share_link" href="http://www.digg.com/submit?url=' . esc_attr( $url_current_page ) . '" ' . esc_attr( $target . $nofollow ) . '>';

		// If image set is not custom.
		if ( 'custom' !== $arr_settings['ssba_image_set'] ) {
			// Show ssba image.
			$html_share_buttons .= '<img src="' . plugins_url() . '/simple-share-buttons-adder/buttons/' . esc_attr( $arr_settings['ssba_image_set'] ) . '/diggit.png" title="Digg" class="ssba ssba-img" alt="Digg this" />';
		} else { // If using custom images.
			// Show custom image.
			$html_share_buttons .= '<img src="' . esc_url( $arr_settings['ssba_custom_diggit'] ) . '" title="Digg" class="ssba ssba-img" alt="Digg this" />';
		}

		// Close href.
		$html_share_buttons .= '</a>';

		// Return share buttons.
		return $html_share_buttons;
	}

	/**
	 * Get reddit.
	 *
	 * @param array  $arr_settings The current ssba settings.
	 * @param string $url_current_page The current page url.
	 * @param string $str_page_title The page title.
	 * @param bool   $boo_show_share_count Show share count or not.
	 *
	 * @return string
	 */
	public function ssba_reddit( $arr_settings, $url_current_page, $str_page_title, $boo_show_share_count ) {
		$nofollow = 'Y' === $arr_settings['ssba_rel_nofollow'] ? ' rel="nofollow"' : '';
		$target = 'Y' === $arr_settings['ssba_share_new_window'] ? ' target="_blank" ' : '';

		// Reddit share link.
		$html_share_buttons = '<a data-site="reddit" class="ssba_reddit_share" href="http://reddit.com/submit?url=' . esc_attr( $url_current_page ) . '&amp;title=' . esc_attr( $str_page_title ) . '" ' . esc_attr( $target . $nofollow ) . '>';

		// If image set is not custom.
		if ( 'custom' !== $arr_settings['ssba_image_set'] ) {

			// Show ssba image.
			$html_share_buttons .= '<img src="' . plugins_url() . '/simple-share-buttons-adder/buttons/' . esc_attr( $arr_settings['ssba_image_set'] ) . '/reddit.png" title="Reddit" class="ssba ssba-img" alt="Share on Reddit" />';
		} else { // If using custom images.
			// Show custom image.
			$html_share_buttons .= '<img src="' . esc_attr( $arr_settings['ssba_custom_reddit'] ) . '" title="Reddit" class="ssba ssba-img" alt="Share on Reddit" />';
		}

		// Close href.
		$html_share_buttons .= '</a>';

		// If show share count is set to Y.
		if ( 'Y' === $arr_settings['ssba_show_share_count'] && $boo_show_share_count ) {
			// Get and display share count.
			$html_share_buttons .= '<span class="ssba_sharecount">' . esc_html( $this->get_reddit_share_count( $url_current_page ) ) . '</span>';
		}

		// Return share buttons.
		return $html_share_buttons;
	}

	/**
	 * Get reddit share count.
	 *
	 * @param string $url_current_page The current url.
	 *
	 * @return int|string
	 */
	public function get_reddit_share_count( $url_current_page ) {
		// Get results from reddit and return the number of shares.
		$html_reddit_share_details = wp_safe_remote_get( 'http://www.reddit.com/api/info.json?url=' . $url_current_page, array(
			'timeout' => 6,
		) );

		// Check there was an error.
		if ( is_wp_error( $html_reddit_share_details ) ) {
			return 0;
		}

		// Decode and get share count.
		$arr_reddit_result = json_decode( $html_reddit_share_details['body'], true );
		$int_reddit_share_count = isset( $arr_reddit_result['data']['children']['0']['data']['score'] ) ? $arr_reddit_result['data']['children']['0']['data']['score'] : 0;

		return  $int_reddit_share_count ? $this->ssba_format_number( $int_reddit_share_count ) : '0';
	}

	/**
	 * Get linkedin button.
	 *
	 * @param array  $arr_settings The current ssba settings.
	 * @param string $url_current_page The current page url.
	 * @param string $str_page_title The page title.
	 * @param bool   $boo_show_share_count Show share count or not.
	 *
	 * @return string
	 */
	public function ssba_linkedin( $arr_settings, $url_current_page, $str_page_title, $boo_show_share_count ) {
		$nofollow = 'Y' === $arr_settings['ssba_rel_nofollow'] ? ' rel="nofollow"' : '';
		$target = 'Y' === $arr_settings['ssba_share_new_window'] ? ' target="_blank" ' : '';

		// Linkedin share link.
		$html_share_buttons = '<a data-site="linkedin" class="ssba_linkedin_share ssba_share_link" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=' . esc_attr( $url_current_page ) . '" ' . esc_attr( $target . $nofollow ) . '>';

		// If image set is not custom.
		if ( 'custom' !== $arr_settings['ssba_image_set'] ) {
			// Show ssba image.
			$html_share_buttons .= '<img src="' . plugins_url() . '/simple-share-buttons-adder/buttons/' . esc_attr( $arr_settings['ssba_image_set'] ) . '/linkedin.png" title="LinkedIn" class="ssba ssba-img" alt="Share on LinkedIn" />';
		} else { // If using custom images.
			// Show custom image.
			$html_share_buttons .= '<img src="' . esc_url( $arr_settings['ssba_custom_linkedin'] ) . '" alt="Share on LinkedIn" title="LinkedIn" class="ssba ssba-img" />';
		}

		// Close href.
		$html_share_buttons .= '</a>';

		// If show share count is set to Y.
		if ( 'Y' === $arr_settings['ssba_show_share_count'] && $boo_show_share_count ) {
			// Get and display share count.
			$html_share_buttons .= '<span class="ssba_sharecount">' . esc_html( $this->get_linkedin_share_count( $url_current_page ) ) . '</span>';
		}

		// Return share buttons.
		return $html_share_buttons;
	}

	/**
	 * Get linkedin share count.
	 *
	 * @param string $url_current_page The current page url.
	 *
	 * @return int|string
	 */
	public function get_linkedin_share_count( $url_current_page ) {
		// Get results from linkedin and return the number of shares.
		$html_linkedin_share_details = wp_safe_remote_get( 'http://www.linkedin.com/countserv/count/share?url=' . $url_current_page, array(
			'timeout' => 6,
		) );

		// If there was an error.
		if ( is_wp_error( $html_linkedin_share_details ) ) {
			return 0;
		}

		// Extract/decode share count.
		$html_linkedin_share_details = str_replace( 'IN.Tags.Share.handleCount(', '', $html_linkedin_share_details );
		$html_linkedin_share_details = str_replace( ');', '', $html_linkedin_share_details );
		$arr_linkedin_share_details = json_decode( $html_linkedin_share_details['body'], true );
		$int_linkedin_share_count = $arr_linkedin_share_details['count'];

		return $int_linkedin_share_count ? $this->ssba_format_number( $int_linkedin_share_count ) : '0';
	}

	/**
	 * Get pinterest button.
	 *
	 * @param array  $arr_settings The current ssba settings.
	 * @param string $url_current_page The current page url.
	 * @param string $str_page_title The page title.
	 * @param bool   $boo_show_share_count Show share count or not.
	 *
	 * @return string
	 */
	public function ssba_pinterest( $arr_settings, $url_current_page, $str_page_title, $boo_show_share_count ) {
		$nofollow = 'Y' === $arr_settings['ssba_rel_nofollow'] ? ' rel="nofollow"' : '';
		$target = 'Y' === $arr_settings['ssba_share_new_window'] ? ' target="_blank" ' : '';

		// If using featured images for Pinteres.
		if ( 'Y' === $arr_settings['ssba_pinterest_featured'] ) {
			// If this post has a featured image.
			if ( has_post_thumbnail( $arr_settings['post_id'] ) ) {
				// Get the featured image.
				$url_post_thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $arr_settings['post_id'] ), 'full' );
				$url_post_thumb = $url_post_thumb[0];
			} else { // No featured image set.
				// Use the pinterest default.
				$url_post_thumb = $arr_settings['ssba_default_pinterest'];
			}

			// Pinterest share link.
			$html_share_buttons = '<a data-site="pinterest-featured" href="http://pinterest.com/pin/create/bookmarklet/?is_video=false&url=' . esc_attr( $url_current_page ) . '&media=' . esc_attr( $url_post_thumb ) . '&description=' . esc_attr( $str_page_title ) . '" class="ssba_pinterest_share ssba_share_link" ' . esc_attr( $target . $nofollow ) . '>';
		} else { // Not using featured images for pinterest.
			// Use the choice of pinnable images approach.
			$html_share_buttons = "<a data-site='pinterest' class='ssba_pinterest_share' href='javascript:void((function()%7Bvar%20e=document.createElement(&apos;script&apos;);e.setAttribute(&apos;type&apos;,&apos;text/javascript&apos;);e.setAttribute(&apos;charset&apos;,&apos;UTF-8&apos;);e.setAttribute(&apos;src&apos;,&apos;//assets.pinterest.com/js/pinmarklet.js?r=&apos;+Math.random()*99999999);document.body.appendChild(e)%7D)());'>";
		}

		// If image set is not custom.
		if ( 'custom' !== $arr_settings['ssba_image_set'] ) {
			// Show ssba image.
			$html_share_buttons .= '<img src="' . plugins_url() . '/simple-share-buttons-adder/buttons/' . esc_attr( $arr_settings['ssba_image_set'] ) . '/pinterest.png" title="Pinterest" class="ssba ssba-img" alt="Pin on Pinterest" />';
		} else { // If using custom images.
			// Show custom image.
			$html_share_buttons .= '<img title="Pinterest" class="ssba ssba-img" src="' . esc_url( $arr_settings['ssba_custom_pinterest'] ) . '" alt="Pin on Pinterest" />';
		}

		// Close href.
		$html_share_buttons .= '</a>';

		// If show share count is set to Y.
		if ( 'Y' === $arr_settings['ssba_show_share_count'] && $boo_show_share_count ) {
			$html_share_buttons .= '<span class="ssba_sharecount">' . esc_html( $this->get_pinterest_share_count( $url_current_page ) ) . '</span>';
		}

		// Return share buttons.
		return $html_share_buttons;
	}

	/**
	 * Get pinterest share count.
	 *
	 * @param string $url_current_page The current page url.
	 *
	 * @return int|string
	 */
	public function get_pinterest_share_count( $url_current_page ) {
		// Get results from pinterest.
		$html_pinterest_share_details = wp_safe_remote_get( 'http://api.pinterest.com/v1/urls/count.json?url=' . $url_current_page, array(
			'timeout' => 6,
		) );

		// Check there was an error.
		if ( is_wp_error( $html_pinterest_share_details ) ) {
			return 0;
		}

		// Decode data.
		$html_pinterest_share_details = str_replace( 'receiveCount(', '', $html_pinterest_share_details );
		$html_pinterest_share_details = str_replace( ')', '', $html_pinterest_share_details );
		$arr_pinterest_share_details = json_decode( $html_pinterest_share_details['body'], true );
		$int_pinterest_share_count = $arr_pinterest_share_details['count'];

		return $int_pinterest_share_count ? $this->ssba_format_number( $int_pinterest_share_count ) : '0';
	}

	/**
	 * Get stumbleupon button.
	 *
	 * @param array  $arr_settings The current ssba settings.
	 * @param string $url_current_page The current page url.
	 * @param string $str_page_title The page title.
	 * @param bool   $boo_show_share_count Show share count or not.
	 *
	 * @return string
	 */
	public function ssba_stumbleupon( $arr_settings, $url_current_page, $str_page_title, $boo_show_share_count ) {
		$nofollow = 'Y' === $arr_settings['ssba_rel_nofollow'] ? ' rel="nofollow"' : '';
		$target = 'Y' === $arr_settings['ssba_share_new_window'] ? ' target="_blank" ' : '';
		$url = 'http://www.stumbleupon.com/submit?url=' . esc_attr( $url_current_page ) . '&amp;title=' . esc_attr( $str_page_title );

		// Stumbleupon share link.
		$html_share_buttons = '<a data-site="stumbleupon" class="ssba_stumbleupon_share ssba_share_link" href="' . esc_url( $url ) . '" ' . esc_attr( $target . $nofollow ) . '>';

		// If image set is not custom.
		if ( 'custom' !== $arr_settings['ssba_image_set'] ) {
			// Show ssba image.
			$html_share_buttons .= '<img src="' . plugins_url() . '/simple-share-buttons-adder/buttons/' . esc_attr( $arr_settings['ssba_image_set'] ) . '/stumbleupon.png" title="StumbleUpon" class="ssba ssba-img" alt="Share on StumbleUpon" />';
		} else { // If using custom images.
			// Show custom image.
			$html_share_buttons .= '<img src="' . esc_url( $arr_settings['ssba_custom_stumbleupon'] ) . '" alt="Share on StumbleUpon" title="StumbleUpon" class="ssba ssba-img" />';
		}

		// Close href.
		$html_share_buttons .= '</a>';

		// If show share count is set to Y.
		if ( 'Y' === $arr_settings['ssba_show_share_count'] && $boo_show_share_count ) {
			$html_share_buttons .= '<span class="ssba_sharecount">' . esc_html( $this->get_stumble_upon_share_count( $url_current_page ) ) . '</span>';
		}

		// Return share buttons.
		return $html_share_buttons;
	}

	/**
	 * Get stumbleupon share count.
	 *
	 * @param string $url_current_page The current url.
	 *
	 * @return int|string
	 */
	public function get_stumble_upon_share_count( $url_current_page ) {
		// Get results from stumbleupon and return the number of shares.
		$html_stumble_upon_share_details = wp_safe_remote_get( 'http://www.stumbleupon.com/services/1.01/badge.getinfo?url=' . $url_current_page, array(
			'timeout' => 6,
		) );

		// Check there was an error.
		if ( is_wp_error( $html_stumble_upon_share_details ) ) {
			return 0;
		}

		// Decode data.
		$arr_stumble_upon_result = json_decode( $html_stumble_upon_share_details['body'], true );
		$int_stumble_upon_share_count = isset( $arr_stumble_upon_result['result']['views'] ) ? $arr_stumble_upon_result['result']['views'] : 0;

		return $int_stumble_upon_share_count ? $this->ssba_format_number( $int_stumble_upon_share_count ) : '0';
	}

	/**
	 * Get email button.
	 *
	 * @param array  $arr_settings The current ssba settings.
	 * @param string $url_current_page The current page url.
	 * @param string $str_page_title The page title.
	 * @param bool   $boo_show_share_count Show share count or not.
	 *
	 * @return string
	 */
	public function ssba_email( $arr_settings, $url_current_page, $str_page_title, $boo_show_share_count ) {
		// Replace ampersands as needed for email link.
		$email_title = str_replace( '&', '%26', $str_page_title );
		$url = 'mailto:?subject=' . $email_title . '&amp;body=' . $arr_settings['ssba_email_message'] . ' ' . $url_current_page;

		// Email share link.
		$html_share_buttons = '<a data-site="email" class="ssba_email_share" href="' . esc_url( $url ) . '">';

		// If image set is not custom.
		if ( 'custom' !== $arr_settings['ssba_image_set'] ) {
			// Show ssba image.
			$html_share_buttons .= '<img src="' . plugins_url() . '/simple-share-buttons-adder/buttons/' . esc_attr( $arr_settings['ssba_image_set'] ) . '/email.png" title="Email" class="ssba ssba-img" alt="Email this to someone" />';
		} else { // If using custom images.
			// Show custom image.
			$html_share_buttons .= '<img src="' . esc_url( $arr_settings['ssba_custom_email'] ) . '" title="Email" class="ssba ssba-img" alt="Email to someone" />';
		}

		// Close href.
		$html_share_buttons .= '</a>';

		// Return share buttons.
		return $html_share_buttons;
	}

	/**
	 * Get flattr button.
	 *
	 * @param array  $arr_settings The current ssba settings.
	 * @param string $url_current_page The current page url.
	 * @param string $str_page_title The page title.
	 * @param bool   $boo_show_share_count Show share count or not.
	 *
	 * @return string
	 */
	public function ssba_flattr( $arr_settings, $url_current_page, $str_page_title, $boo_show_share_count ) {
		$nofollow = 'Y' === $arr_settings['ssba_rel_nofollow'] ? ' rel="nofollow"' : '';
		$target = 'Y' === $arr_settings['ssba_share_new_window'] ? ' target="_blank" ' : '';

		// Check for dedicated flattr URL.
		if ( '' !== $arr_settings['ssba_flattr_url'] ) {
			// Update url that will be set to specified URL.
			$url_current_page = $arr_settings['ssba_flattr_url'];
		}

		// Flattr share link.
		$html_share_buttons = '<a data-site="flattr" class="ssba_flattr_share" href="https://flattr.com/submit/auto?user_id=' . esc_attr( $arr_settings['ssba_flattr_user_id'] ) . '&amp;title=' . esc_attr( $str_page_title ) . '&amp;url=' . esc_attr( $url_current_page ) . '" ' . esc_attr( $target . $nofollow ) . '>';

		// If image set is not custom.
		if ( 'custom' !== $arr_settings['ssba_image_set'] ) {
			// Show ssba image.
			$html_share_buttons .= '<img src="' . plugins_url() . '/simple-share-buttons-adder/buttons/' . esc_attr( $arr_settings['ssba_image_set'] ) . '/flattr.png" title="Flattr" class="ssba ssba-img" alt="Flattr the author" />';
		} else { // If using custom images.
			// Show custom image.
			$html_share_buttons .= '<img src="' . esc_url( $arr_settings['ssba_custom_flattr'] ) . '" title="Flattr" class="ssba ssba-img" alt="Flattr the author" />';
		}

		// Close href.
		$html_share_buttons .= '</a>';

		// Return share buttons.
		return $html_share_buttons;
	}

	/**
	 * Get buffer button.
	 *
	 * @param array  $arr_settings The current ssba settings.
	 * @param string $url_current_page The current page url.
	 * @param string $str_page_title The page title.
	 * @param bool   $boo_show_share_count Show share count or not.
	 *
	 * @return string
	 */
	public function ssba_buffer( $arr_settings, $url_current_page, $str_page_title, $boo_show_share_count ) {
		$buffer = '' !== $arr_settings['ssba_buffer_text'] ? $arr_settings['ssba_buffer_text'] : '';
		$nofollow = 'Y' === $arr_settings['ssba_rel_nofollow'] ? ' rel="nofollow"' : '';
		$target = 'Y' === $arr_settings['ssba_share_new_window'] ? ' target="_blank" ' : '';

		// Buffer share link.
		$html_share_buttons = '<a  data-site="buffer" class="ssba_buffer_share" href="https://bufferapp.com/add?url=' . esc_attr( $url_current_page ) . '&amp;text=' . esc_attr( $buffer ) . ' ' . esc_attr( $str_page_title ) . '" ' . esc_attr( $target . $nofollow ) . '>';

		// If image set is not custom.
		if ( 'custom' !== $arr_settings['ssba_image_set'] ) {
			// Show ssba image.
			$html_share_buttons .= '<img src="' . plugins_url() . '/simple-share-buttons-adder/buttons/' . esc_attr( $arr_settings['ssba_image_set'] ) . '/buffer.png" title="Buffer" class="ssba ssba-img" alt="Buffer this page" />';
		} else { // If using custom images.
			// Show custom image.
			$html_share_buttons .= '<img src="' . esc_url( $arr_settings['ssba_custom_buffer'] ) . '" title="Buffer" class="ssba ssba-img" alt="Buffer this page" />';
		}

		// Close href.
		$html_share_buttons .= '</a>';

		// Return share buttons.
		return $html_share_buttons;
	}

	/**
	 * Get tumblr button.
	 *
	 * @param array  $arr_settings The current ssba settings.
	 * @param string $url_current_page The current page url.
	 * @param string $str_page_title The page title.
	 * @param bool   $boo_show_share_count Show share count or not.
	 *
	 * @return string
	 */
	public function ssba_tumblr( $arr_settings, $url_current_page, $str_page_title, $boo_show_share_count ) {
		$nofollow = 'Y' === $arr_settings['ssba_rel_nofollow'] ? ' rel="nofollow"' : '';
		$target = 'Y' === $arr_settings['ssba_share_new_window'] ? ' target="_blank" ' : '';

		// Tumblr share link.
		$html_share_buttons = '<a data-site="tumblr" class="ssba_tumblr_share" href="http://www.tumblr.com/share/link?url=' . esc_attr( $url_current_page ) . '" ' . esc_attr( $target . $nofollow ) . '>';

		// If image set is not custom.
		if ( 'custom' !== $arr_settings['ssba_image_set'] ) {
			// Show ssba image.
			$html_share_buttons .= '<img src="' . plugins_url() . '/simple-share-buttons-adder/buttons/' . esc_attr( $arr_settings['ssba_image_set'] ) . '/tumblr.png" title="tumblr" class="ssba ssba-img" alt="Share on Tumblr" />';
		} else { // If using custom images.
			// Show custom image.
			$html_share_buttons .= '<img src="' . esc_url( $arr_settings['ssba_custom_tumblr'] ) . '" title="tumblr" class="ssba ssba-img" alt="share on Tumblr" />';
		}

		// Close href.
		$html_share_buttons .= '</a>';

		// If show share count is set to Y.
		if ( 'Y' === $arr_settings['ssba_show_share_count'] && $boo_show_share_count ) {
			$html_share_buttons .= '<span class="ssba_sharecount">' . esc_html( $this->get_tumblr_share_count( $url_current_page ) ) . '</span>';
		}

		// Return share buttons.
		return $html_share_buttons;
	}

	/**
	 * Get tumblr share count.
	 *
	 * @param string $url_current_page The current url.
	 *
	 * @return int|string
	 */
	public function get_tumblr_share_count( $url_current_page ) {
		// Get results from tumblr and return the number of shares.
		$result = wp_safe_remote_get( 'http://api.tumblr.com/v2/share/stats?url=' . $url_current_page, array(
			'timeout' => 6,
		) );

		// Check there was an error.
		if ( is_wp_error( $result ) ) {
			return 0;
		}

		// Decode data.
		$array = json_decode( $result['body'], true );
		$count = isset( $array['response']['note_count'] ) ? $array['response']['note_count'] : 0;

		return ( $count ) ? $count : '0';
	}

	/**
	 * Get print button.
	 *
	 * @param array  $arr_settings The current ssba settings.
	 * @param string $url_current_page The current page url.
	 * @param string $str_page_title The page title.
	 * @param bool   $boo_show_share_count Show share count or not.
	 *
	 * @return string
	 */
	public function ssba_print( $arr_settings, $url_current_page, $str_page_title, $boo_show_share_count ) {
		// Linkedin share link.
		$html_share_buttons = '<a data-site="print" class="ssba_print ssba_share_link" href="#" onclick="window.print()">';

		// If image set is not custom.
		if ( 'custom' !== $arr_settings['ssba_image_set'] ) {
			// Show ssba image.
			$html_share_buttons .= '<img src="' . plugins_url() . '/simple-share-buttons-adder/buttons/' . esc_attr( $arr_settings['ssba_image_set'] ) . '/print.png" title="Print" class="ssba ssba-img" alt="Print this page" />';
		} else { // If using custom images.
			// Show custom image.
			$html_share_buttons .= '<img src="' . esc_url( $arr_settings['ssba_custom_print'] ) . '" title="Print" class="ssba ssba-img" alt="Print this page" />';
		}

		// Close href.
		$html_share_buttons .= '</a>';

		// Return share buttons.
		return $html_share_buttons;
	}

	/**
	 * Get vk button.
	 *
	 * @param array  $arr_settings The current ssba settings.
	 * @param string $url_current_page The current page url.
	 * @param string $str_page_title The page title.
	 * @param bool   $boo_show_share_count Show share count or not.
	 *
	 * @return string
	 */
	public function ssba_vk( $arr_settings, $url_current_page, $str_page_title, $boo_show_share_count ) {
		$nofollow = 'Y' === $arr_settings['ssba_rel_nofollow'] ? ' rel="nofollow"' : '';
		$target = 'Y' === $arr_settings['ssba_share_new_window'] ? ' target="_blank" ' : '';

		// Vk share link.
		$html_share_buttons = '<a data-site="vk" class="ssba_vk_share ssba_share_link" href="http://vkontakte.ru/share.php?url=' . esc_attr( $url_current_page ) . '" ' . esc_attr( $target . $nofollow ) . '>';

		// If image set is not custom.
		if ( 'custom' !== $arr_settings['ssba_image_set'] ) {
			// Show ssba image.
			$html_share_buttons .= '<img src="' . plugins_url() . '/simple-share-buttons-adder/buttons/' . esc_attr( $arr_settings['ssba_image_set'] ) . '/vk.png" title="VK" class="ssba ssba-img" alt="Share on VK" />';
		} else { // If using custom images.
			// Show custom image.
			$html_share_buttons .= '<img src="' . esc_url( $arr_settings['ssba_custom_vk'] ) . '" title="VK" class="ssba ssba-img" alt="Share on VK" />';
		}

		// Close href.
		$html_share_buttons .= '</a>';

		// Return share buttons.
		return $html_share_buttons;
	}

	/**
	 * Get yummly button.
	 *
	 * @param array  $arr_settings The current ssba settings.
	 * @param string $url_current_page The current page url.
	 * @param string $str_page_title The page title.
	 * @param bool   $boo_show_share_count Show share count or not.
	 *
	 * @return string
	 */
	public function ssba_yummly( $arr_settings, $url_current_page, $str_page_title, $boo_show_share_count ) {
		$nofollow = 'Y' === $arr_settings['ssba_rel_nofollow'] ? ' rel="nofollow"' : '';
		$target = 'Y' === $arr_settings['ssba_share_new_window'] ? ' target="_blank" ' : '';

		// Yummly share link.
		$html_share_buttons = '<a data-site="yummly" class="ssba_yummly_share ssba_share_link" href="http://www.yummly.com/urb/verify?url=' . esc_attr( $url_current_page ) . '&title=' . esc_attr( rawurlencode( html_entity_decode( $str_page_title ) ) ) . '" ' . esc_attr( $target . $nofollow ) . '>';

		// If image set is not custom.
		if ( 'custom' !== $arr_settings['ssba_image_set'] ) {
			// Show ssba image.
			$html_share_buttons .= '<img src="' . plugins_url() . '/simple-share-buttons-adder/buttons/' . esc_attr( $arr_settings['ssba_image_set'] ) . '/yummly.png" title="Yummly" class="ssba ssba-img" alt="Share on Yummly" />';
		} else { // If using custom images.
			// Show custom image.
			$html_share_buttons .= '<img src="' . esc_url( $arr_settings['ssba_custom_yummly'] ) . '" title="Yummly" class="ssba ssba-img" alt="Share on Yummly" />';
		}

		// Close href.
		$html_share_buttons .= '</a>';

		// If show share count is set to Y.
		if ( 'Y' === $arr_settings['ssba_show_share_count'] && $boo_show_share_count ) {
			$html_share_buttons .= '<span class="ssba_sharecount">' . esc_html( $this->get_yummly_share_count( $url_current_page ) ) . '</span>';
		}

		// Return share buttons.
		return $html_share_buttons;
	}

	/**
	 * Get yummly share count.
	 *
	 * @param string $url_current_page the current page url.
	 *
	 * @return int|string
	 */
	public function get_yummly_share_count( $url_current_page ) {
		// Get results from yummly and return the number of shares.
		$result = wp_safe_remote_get( 'http://www.yummly.com/services/yum-count?url=' . $url_current_page, array(
			'timeout' => 6,
		) );

		// Check there was an error.
		if ( is_wp_error( $result ) ) {
			return 0;
		}

		// Decode data.
		$array = json_decode( $result['body'], true );
		$count = isset( $array['count'] ) ? $array['count'] : '0';

		// Return.
		return $count;
	}

	/**
	 * Get whatsapp button.
	 *
	 * @param array  $arr_settings The current ssba settings.
	 * @param string $url_current_page The current page url.
	 * @param string $str_page_title The page title.
	 * @param bool   $boo_show_share_count Show share count or not.
	 *
	 * @return string
	 */
	public function ssba_whatsapp( $arr_settings, $url_current_page, $str_page_title, $boo_show_share_count ) {
		if ( ! wp_is_mobile() ) {
			return;
		}

		$nofollow = 'Y' === $arr_settings['ssba_rel_nofollow'] ? ' rel="nofollow"' : '';
		$target = 'Y' === $arr_settings['ssba_share_new_window'] ? ' target="_blank" ' : '';

		// Whatsapp share link.
		$html_share_buttons = '<a data-site="whatsapp" class="ssba_whatsapp_share ssba_share_link" href="whatsapp://send?text=' . rawurlencode( $url_current_page . ' ' . $str_page_title ) . '" ' . esc_attr( $target . $nofollow ) . '>';

		// If image set is not custom.
		if ( 'custom' !== $arr_settings['ssba_image_set'] ) {
			// Show ssba image.
			$html_share_buttons .= '<img src="' . plugins_url() . '/simple-share-buttons-adder/buttons/' . esc_attr( $arr_settings['ssba_image_set'] ) . '/whatsapp.png" title="Whatsapp" class="ssba ssba-img" alt="Share on Whatsapp" />';
		} else { // If using custom images.
			// Show custom image.
			$html_share_buttons .= '<img src="' . esc_url( $arr_settings['ssba_custom_whatsapp'] ) . '" title="Whatsapp" class="ssba ssba-img" alt="Share on Whatsapp" />';
		}

		// Close href.
		$html_share_buttons .= '</a>';

		// Return share buttons.
		return $html_share_buttons;
	}

	/**
	 * Get xing button.
	 *
	 * @param array  $arr_settings The current ssba settings.
	 * @param string $url_current_page The current page url.
	 * @param string $str_page_title The page title.
	 * @param bool   $boo_show_share_count Show share count or not.
	 *
	 * @return string
	 */
	public function ssba_xing( $arr_settings, $url_current_page, $str_page_title, $boo_show_share_count ) {
		$nofollow = 'Y' === $arr_settings['ssba_rel_nofollow'] ? ' rel="nofollow"' : '';
		$target = 'Y' === $arr_settings['ssba_share_new_window'] ? ' target="_blank" ' : '';

		// Xing share link.
		$html_share_buttons = '<a data-site="xing" class="ssba_xing_share ssba_share_link" href="https://www.xing.com/spi/shares/new?url=' . $url_current_page . '" ' . esc_attr( $target . $nofollow ) . '>';

		// If image set is not custom.
		if ( 'custom' !== $arr_settings['ssba_image_set'] ) {
			// Show ssba image.
			$html_share_buttons .= '<img src="' . plugins_url() . '/simple-share-buttons-adder/buttons/' . esc_attr( $arr_settings['ssba_image_set'] ) . '/xing.png" title="Xing" class="ssba ssba-img" alt="Share on Xing" />';
		} else { // If using custom images.
			// Show custom image.
			$html_share_buttons .= '<img src="' . esc_url( $arr_settings['ssba_custom_xing'] ) . '" title="Xing" class="ssba ssba-img" alt="Share on Xing" />';
		}

		// Close href.
		$html_share_buttons .= '</a>';

		// Return share buttons.
		return $html_share_buttons;
	}
}
