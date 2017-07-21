<?php
defined('ABSPATH') or die('No direct access permitted');

// call scripts add function
add_action( 'wp_enqueue_scripts', 'ssba_page_scripts' );

// add css scripts for page/post use
function ssba_page_scripts() {
    // get settings
   $arrSettings = get_ssba_settings();

   if (is_ssl()) {
      $st_insights = 'https://ws.sharethis.com/button/st_insights.js';
    } else {
      $st_insights = 'http://w.sharethis.com/button/st_insights.js';
    }

    // add call to st_insights.js with params
    $url = add_query_arg( array(
        'publisher' => '4d48b7c5-0ae3-43d4-bfbe-3ff8c17a8ae6',
        'product'   => 'simpleshare',
    ), $st_insights );
    if ( 'Y' === $arrSettings['accepted_sharethis_terms'] ) {
        wp_enqueue_script( 'ssba-sharethis', $url, null, null );
        add_filter( 'script_loader_tag', 'ssba_script_tags', 10, 2 );
    }

    // ssba.min.js
    wp_enqueue_script('ssba', plugins_url('js/ssba.min.js', SSBA_FILE), array('jquery'), false, true);

	// if indie flower font is selected
	if ($arrSettings['ssba_font_family'] == 'Indie Flower') {
		// font scripts
		wp_register_style('ssbaFont', '//fonts.googleapis.com/css?family=Indie+Flower');
		wp_enqueue_style( 'ssbaFont');
	} else if ($arrSettings['ssba_font_family'] == 'Reenie Beanie') {
		// font scripts
		wp_register_style('ssbaFont', '//fonts.googleapis.com/css?family=Reenie+Beanie');
		wp_enqueue_style( 'ssbaFont');
	}
}

/**
 * Adds ID to sharethis script.
 * @param string $tag    HTML script tag.
 * @param string $handle Script handle.
 * @return string
 */
function ssba_script_tags( $tag, $handle ) {
	if ( 'ssba-sharethis' === $handle ) {
		return str_replace( '<script ', '<script id=\'st_insights_js\' ', $tag );
	}
	return $tag;
}

// add CSS to the head
add_action( 'wp_head', 'get_ssba_style' );

// generate style
function get_ssba_style() {

	// query the db for current ssba settings
	$arrSettings = get_ssba_settings();

    // if the sharethis terms have been accepted
    if ($arrSettings['accepted_sharethis_terms'] == 'Y') {
        // if a facebook app id has been set
        if ($arrSettings['facebook_app_id'] != '') {
            $src = '//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6&appID='.$arrSettings['facebook_app_id'];
        } else {
            $src = '//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6';
        }

        // if an app id has been entered
        if ($arrSettings['facebook_app_id'] != '') {
            // init facebook
            echo "<script>window.fbAsyncInit = function() {
                FB.init({
                  appId      : '" . $arrSettings['facebook_app_id'] . "',
                  xfbml      : true,
                  version    : 'v2.6'
                });
              };</script>";
        }

        // include facebook js sdk
        echo "<script>(function(d, s, id){
                 var js, fjs = d.getElementsByTagName(s)[0];
                 if (d.getElementById(id)) {return;}
                 js = d.createElement(s); js.id = id;
                 js.src = \"" . $src . "\";
                 fjs.parentNode.insertBefore(js, fjs);
               }(document, 'script', 'facebook-jssdk'));</script>";

		// if an app id has been entered
		if ($arrSettings['facebook_app_id'] != '') {
			// if facebook insights have been enabled
			if ($arrSettings['facebook_insights'] == 'Y') {
				// add facebook meta tag
				echo '<meta property="fb:app_id" content="'.$arrSettings['facebook_app_id'].'" />';
			}
		}
    }

    // css style
	$htmlSSBAStyle = '<style type="text/css">';

	// check if custom styles haven't been set
	if ($arrSettings['ssba_custom_styles_enabled'] != 'Y') {

		// use set options
		$htmlSSBAStyle .= '	.ssba {
									' . ($arrSettings['ssba_div_padding'] 			!= ''	? 'padding: ' 	. $arrSettings['ssba_div_padding'] . 'px;' : NULL) . '
									' . ($arrSettings['ssba_border_width'] 			!= ''	? 'border: ' . $arrSettings['ssba_border_width'] . 'px solid ' 	. $arrSettings['ssba_div_border'] . ';' : NULL) . '
									' . ($arrSettings['ssba_div_background'] 		!= ''	? 'background-color: ' 	. $arrSettings['ssba_div_background'] . ';' : NULL) . '
									' . ($arrSettings['ssba_div_rounded_corners'] 	== 'Y'	? '-moz-border-radius: 10px; -webkit-border-radius: 10px; -khtml-border-radius: 10px;  border-radius: 10px; -o-border-radius: 10px;' : NULL) . '
								}
								.ssba img
								{
									width: ' . $arrSettings['ssba_size'] . 'px !important;
									padding: ' . $arrSettings['ssba_padding'] . 'px;
									border:  0;
									box-shadow: none !important;
									display: inline !important;
									vertical-align: middle;
								}
								.ssba, .ssba a
								{
									text-decoration:none;
									border:0;
									' . ($arrSettings['ssba_div_background'] == ''	? 'background: none;' : NULL) . '
									' . ($arrSettings['ssba_font_family'] 	!= ''	? 'font-family: ' . $arrSettings['ssba_font_family'] . ';' : NULL) . '
									' . ($arrSettings['ssba_font_size']		!= ''	? 'font-size: 	' . $arrSettings['ssba_font_size'] . 'px;' : NULL) . '
									' . ($arrSettings['ssba_font_color'] 	!= ''	? 'color: 		' . $arrSettings['ssba_font_color'] . '!important;' : NULL) . '
									' . ($arrSettings['ssba_font_weight'] 	!= ''	? 'font-weight: ' . $arrSettings['ssba_font_weight'] . ';' : NULL) . '
								}
								';

        // if counters option is set to Y
		if ($arrSettings['ssba_show_share_count'] == 'Y') {
			// styles that apply to all counter css sets
			$htmlSSBAStyle .= '.ssba_sharecount:after, .ssba_sharecount:before {
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

			// if default counter style has been chosen
			if ($arrSettings['ssba_share_count_style'] == 'default') {

				// style share count
				$htmlSSBAStyle .= 	'color: #555e58;
										background: #f5f5f5;
									}
									.ssba_sharecount:after {
										border-right-color: #f5f5f5;
									}';

			} elseif ($arrSettings['ssba_share_count_style'] == 'white') {

				// show white style share counts
				$htmlSSBAStyle .= 	'color: #555e58;
										background: #ffffff;
									}
									.ssba_sharecount:after {
										border-right-color: #ffffff;
									}';

			} elseif ($arrSettings['ssba_share_count_style'] == 'blue') {

				// show blue style share counts
				$htmlSSBAStyle .= 	'color: #ffffff;
										background: #42a7e2;
									}
									.ssba_sharecount:after {
										border-right-color: #42a7e2;
									}';
			}
		}

		// if there's any additional css
		if ($arrSettings['ssba_additional_css'] != '') {
    		// add the additional CSS
    		$htmlSSBAStyle .= $arrSettings['ssba_additional_css'];
		}
	}

	// else use set options
	else {

		// use custom styles
		$htmlSSBAStyle .= $arrSettings['ssba_custom_styles'];
	}

	// close style tag
	$htmlSSBAStyle .= '</style>';

	// return
	echo $htmlSSBAStyle;

}
