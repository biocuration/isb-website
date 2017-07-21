<?php
defined('ABSPATH') or die('No direct access permitted');

// activate ssba function
function ssba_activate() {

    // likely a reactivation, return doing nothing
    if (get_option('ssba_version') !== false) {
        return;
    }

    // array ready with defaults
    $ssba_settings = array(
        'ssba_image_set' => 'somacro',
        'ssba_size' => '35',
        'ssba_pages' => '',
        'ssba_posts' => '',
        'ssba_cats_archs' => '',
        'ssba_homepage' => '',
        'ssba_excerpts' => '',
        'ssba_align' => 'left',
        'ssba_padding' => '6',
        'ssba_before_or_after' => 'after',
        'ssba_additional_css' => '',
        'ssba_custom_styles' => '',
        'ssba_custom_styles_enabled' => '',
        'ssba_email_message' => '',
        'ssba_twitter_text' => '',
        'ssba_buffer_text' => '',
        'ssba_flattr_user_id' => '',
        'ssba_flattr_url' => '',
        'ssba_share_new_window' => 'Y',
        'ssba_link_to_ssb' => 'N',
        'ssba_show_share_count' => '',
        'ssba_share_count_style' => 'default',
        'ssba_share_count_css' => '',
        'ssba_share_count_once' => 'Y',
        'ssba_widget_text' => '',
        'ssba_rel_nofollow' => '',
        'ssba_default_pinterest' => '',
        'ssba_pinterest_featured' => '',
        'ssba_content_priority' => '10',

        // share container
        'ssba_div_padding' => '',
        'ssba_div_rounded_corners' => '',
        'ssba_border_width' => '',
        'ssba_div_border' => '',
        'ssba_div_background' => '',

        // share text
        'ssba_share_text' => "It's only fair to share...",
        'ssba_text_placement' => 'left',
        'ssba_font_family' => 'Indie Flower',
        'ssba_font_color' => '',
        'ssba_font_size' => '20',
        'ssba_font_weight' => '',

        // include
        'ssba_selected_buttons' => 'facebook,google,twitter,linkedin',

        // custom images
        'ssba_custom_email' => '',
        'ssba_custom_google' => '',
        'ssba_custom_facebook' => '',
        'ssba_custom_twitter' => '',
        'ssba_custom_diggit' => '',
        'ssba_custom_linkedin' => '',
        'ssba_custom_reddit' => '',
        'ssba_custom_stumbleupon' => '',
        'ssba_custom_pinterest' => '',
        'ssba_custom_buffer' => '',
        'ssba_custom_flattr' => '',
        'ssba_custom_tumblr' => '',
        'ssba_custom_print' => '',
        'ssba_custom_vk' => '',
        'ssba_custom_yummly' => '',

        // sharedcount
        'sharedcount_enabled' => '',
        'sharedcount_api_key' => '',
        'sharedcount_plan' => 'free',

        // newsharecounts
        'twitter_newsharecounts' => '',

        // new with sharethis
        'facebook_insights' => '',
        'facebook_app_id' => '',
        'accepted_sharethis_terms' => 'Y',
    );

    // json encode
    $jsonSettings = json_encode($ssba_settings);

    // insert default options for ssba
    add_option('ssba_settings', $jsonSettings);

	// button helper array
	ssba_button_helper_array();

    // ssba version
    add_option('ssba_version', SSBA_VERSION);
}

// uninstall ssba
function ssba_uninstall() {

	//if uninstall not called from WordPress exit
	if (defined('WP_UNINSTALL_PLUGIN')) {
		exit();
	}

    // delete options
    delete_option('ssba_settings');
    delete_option('ssba_version');
}

// the upgrade function
function upgrade_ssba($arrSettings, $version) {

    // if version is less than 6.0.5
    if ($version < '6.0.5') {
        // ensure excerpts are set
        add_option('ssba_excerpts',		'');

        // add print button
        add_option('ssba_custom_print', '');

        // new for 3.8
        add_option('ssba_widget_text',	'');
        add_option('ssba_rel_nofollow',	'');

        // added pre 4.5, added in 4.6 to fix notice
        add_option('ssba_rel_nofollow',	'');

        // added in 5.0
        add_option('ssba_custom_vk', 	 '');
        add_option('ssba_custom_yummly', '');

        // added in 5.2
        add_option('ssba_default_pinterest', '');

        // added in 5.5
        add_option('ssba_pinterest_featured', '');

        // added in 5.7
        // additional CSS field
        add_option('ssba_additional_css', '');

        // empty custom CSS var and option
        $customCSS = '';
        add_option('ssba_custom_styles_enabled', '');

        // if some custom styles are in place
        if ($arrSettings['ssba_custom_styles'] != '') {
            $customCSS.= $arrSettings['ssba_custom_styles'];
            update_option('ssba_custom_styles_enabled', 'Y');
        }

        // if some custom share count styles are in place
        if ($arrSettings['ssba_share_count_css'] != '') {
            $customCSS.= $arrSettings['ssba_share_count_css'];
            update_option('ssba_custom_styles_enabled', 'Y');
        }

        // update custom CSS option
        update_option('ssba_custom_styles', $customCSS);

        // content priority
        add_option('ssba_content_priority', '10');
    }

    // if version is less than 6.0.6
    if ($version < '6.0.6') {
        // get old settings
        $oldSettings = get_old_ssba_settings();

        // json encode old settings
        $jsonSettings = json_encode($oldSettings);

        // insert all options for ssba as json
        add_option('ssba_settings', $jsonSettings);

        // delete old options
        ssba_delete_old_options();
    }

    // if version is less than 6.1.3
    if ($version < '6.1.3') {
        // new settings
        $new = array(
            'sharedcount_enabled' => '',
            'sharedcount_api_key' => '',
            'sharedcount_plan' => 'free',
        );

        // update settings
        ssba_update_options($new);
    }

    // if version is less than 6.1.5
    if ($version < '6.1.5') {
        // new settings
        $new = array(
            'twitter_newsharecounts' => '',
        );

        // update settings
        ssba_update_options($new);
    }

    // if version is less than 6.2.0
    if ($version < '6.2.0') {
        // new settings
        $new = array(
            'facebook_insights' => '',
            'facebook_app_id' => '',
            'accepted_sharethis_terms' => '',
        );

        // update settings
        ssba_update_options($new);
    }

	// button helper array
	ssba_button_helper_array();

    // Show the ST terms notice after upgrades if the user hasn't agreed.
    ssba_update_options( array( 'hide_sharethis_terms' => false ) );

	// update version number
	update_option('ssba_version', SSBA_VERSION);
}

// button helper option
function ssba_button_helper_array()
{
	// helper array for ssbp
	update_option('ssba_buttons', json_encode(array(
		'buffer' => array(
			'full_name' 	=> 'Buffer'
		),
		'diggit' => array(
			'full_name' 	=> 'Diggit'
		),
		'email' => array(
			'full_name' 	=> 'Email'
		),
		'facebook' => array(
			'full_name' 	=> 'Facebook'
		),
        'facebook_save' => array(
            'full_name' 	=> 'Facebook Save'
        ),
		'flattr' => array(
			'full_name' 	=> 'Flattr'
		),
		'google' => array(
			'full_name' 	=> 'Google+'
		),
		'linkedin' => array(
			'full_name' 	=> 'LinkedIn'
		),
		'pinterest' => array(
			'full_name' 	=> 'Pinterest'
		),
		'print' => array(
			'full_name' 	=> 'Print'
		),
		'reddit' => array(
			'full_name' 	=> 'Reddit'
		),
		'stumbleupon' => array(
			'full_name' 	=> 'StumbleUpon'
		),
		'tumblr' => array(
			'full_name' 	=> 'Tumblr'
		),
		'twitter' => array(
			'full_name' 	=> 'Twitter'
		),
		'vk' => array(
			'full_name' 	=> 'VK'
		),
		'yummly' => array(
			'full_name' 	=> 'Yummly'
		)
	)));
}

// delete old options to move to json array
function ssba_delete_old_options()
{
    // delete all options
    delete_option('ssba_version');
    delete_option('ssba_image_set');
    delete_option('ssba_size');
    delete_option('ssba_pages');
    delete_option('ssba_posts');
    delete_option('ssba_cats_archs');
    delete_option('ssba_homepage');
    delete_option('ssba_excerpts');
    delete_option('ssba_align');
    delete_option('ssba_padding');
    delete_option('ssba_before_or_after');
    delete_option('ssba_additional_css');
    delete_option('ssba_custom_styles');
    delete_option('ssba_custom_styles_enabled');
    delete_option('ssba_email_message');
    delete_option('ssba_buffer_text');
    delete_option('ssba_twitter_text');
    delete_option('ssba_flattr_user_id');
    delete_option('ssba_flattr_url');
    delete_option('ssba_share_new_window');
    delete_option('ssba_link_to_ssb');
    delete_option('ssba_show_share_count');
    delete_option('ssba_share_count_style');
    delete_option('ssba_share_count_css');
    delete_option('ssba_share_count_once');
    delete_option('ssba_widget_text');
    delete_option('ssba_rel_nofollow');
    delete_option('ssba_default_pinterest');
    delete_option('ssba_pinterest_featured');
    delete_option('ssba_content_priority');

    // share container
    delete_option('ssba_div_padding');
    delete_option('ssba_div_rounded_corners');
    delete_option('ssba_border_width');
    delete_option('ssba_div_border');
    delete_option('ssba_div_background');

    // share text
    delete_option('ssba_share_text');
    delete_option('ssba_text_placement');
    delete_option('ssba_font_family');
    delete_option('ssba_font_color');
    delete_option('ssba_font_size');
    delete_option('ssba_font_weight');

    // include
    delete_option('ssba_selected_buttons');

    // custom images
    delete_option('ssba_custom_email');
    delete_option('ssba_custom_google');
    delete_option('ssba_custom_facebook');
    delete_option('ssba_custom_twitter');
    delete_option('ssba_custom_diggit');
    delete_option('ssba_custom_linkedin');
    delete_option('ssba_custom_reddit');
    delete_option('ssba_custom_stumbleupon');
    delete_option('ssba_custom_pinterest');
    delete_option('ssba_custom_buffer');
    delete_option('ssba_custom_flattr');
    delete_option('ssba_custom_tumblr');
    delete_option('ssba_custom_print');
    delete_option('ssba_custom_vk');
    delete_option('ssba_custom_yummly');
}

// return old ssba settings (pre 6.0.6)
function get_old_ssba_settings() {

    // globals
    global $wpdb;

    // query the db for current ssba settings
    $arrSettings = $wpdb->get_results("SELECT option_name, option_value
											 FROM $wpdb->options
											WHERE option_name LIKE 'ssba_%'");

    // loop through each setting in the array
    foreach ($arrSettings as $setting) {

        // add each setting to the array by name
        $arrSettings[$setting->option_name] =  $setting->option_value;
    }

    // return
    return $arrSettings;
}
