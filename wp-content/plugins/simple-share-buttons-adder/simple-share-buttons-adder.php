<?php
/*
Plugin Name: Simple Share Buttons Adder
Plugin URI: https://simplesharebuttons.com
Description: A simple plugin that enables you to add share buttons to all of your posts and/or pages.
Version: 6.3.4
Author: Simple Share Buttons
Author URI: https://simplesharebuttons.com
License: GPLv2

Copyright 2015 Simple Share Buttons admin@simplesharebuttons.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

      _                    _           _   _
  ___| |__   __ _ _ __ ___| |__  _   _| |_| |_ ___  _ __  ___
 / __| '_ \ / _` | '__/ _ \ '_ \| | | | __| __/ _ \| '_ \/ __|
 \__ \ | | | (_| | | |  __/ |_) | |_| | |_| || (_) | | | \__ \
 |___/_| |_|\__,_|_|  \___|_.__/ \__,_|\__|\__\___/|_| |_|___/

 */

//======================================================================
// 		CONSTANTS
//======================================================================

	define('SSBA_FILE', __FILE__);
    define('SSBA_ROOT', dirname(__FILE__));
	define('SSBA_VERSION', '6.3.4');

//======================================================================
// 		 SSBA SETTINGS
//======================================================================

	// make sure we have settings ready
	// this has been introduced to exclude from excerpts
	$arrSettings = get_ssba_settings();

//======================================================================
// 		INCLUDES
//======================================================================

    include_once plugin_dir_path(__FILE__).'/inc/ssba_admin_bits.php';
    include_once plugin_dir_path(__FILE__).'/inc/ssba_buttons.php';
    include_once plugin_dir_path(__FILE__).'/inc/ssba_styles.php';
    include_once plugin_dir_path(__FILE__).'/inc/ssba_widget.php';
    include_once plugin_dir_path(__FILE__).'/inc/ssba_database.php';

//======================================================================
// 		ACTIVATE/DEACTIVATE HOOKS
//======================================================================

    // run the activation function upon activation of the plugin
    register_activation_hook( __FILE__,'ssba_activate');

    // register deactivation hook
    register_uninstall_hook(__FILE__,'ssba_uninstall');

//======================================================================
// 		GET SSBA SETTINGS
//======================================================================

    // return ssba settings
    function get_ssba_settings()
    {
        // get json array settings from DB
        $jsonSettings = get_option('ssba_settings');

        // decode and return settings
        return json_decode($jsonSettings, true);
    }

//======================================================================
// 		UPDATE SSBA SETTINGS
//======================================================================

    // update an array of options
    function ssba_update_options($arrOptions)
    {
        // if not given an array
        if (! is_array($arrOptions)) {
            die('Value parsed not an array');
        }

        // get ssba settings
        $jsonSettings = get_option('ssba_settings');

        // decode the settings
        $ssba_settings = json_decode($jsonSettings, true);

        // loop through array given
        foreach ($arrOptions as $name => $value) {
            // update/add the option in the array
            $ssba_settings[$name] = $value;
        }

        // encode the options ready to save back
        $jsonSettings = json_encode($ssba_settings);

        // update the option in the db
        update_option('ssba_settings', $jsonSettings);
    }
