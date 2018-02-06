<?php
/**
 * Plugin Name: Simple Share Buttons Adder
 * Plugin URI: https://simplesharebuttons.com
 * Description: A simple plugin that enables you to add share buttons to all of your posts and/or pages.
 * Version: 7.3.10
 * Author: Simple Share Buttons
 * Author URI: https://simplesharebuttons.com
 * License: GPLv2

Copyright 2015 Simple Share Buttons admin@simplesharebuttons.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
 *
 * @package SimpleShareButtonsAdder
 */

if ( version_compare( phpversion(), '5.3', '>=' ) ) {
	require_once __DIR__ . '/instance.php';
} else {
	if ( defined( 'WP_CLI' ) ) {
		WP_CLI::warning( _simple_share_buttons_adder_php_version_text() );
	} else {
		add_action( 'admin_notices', '_simple_share_buttons_adder_php_version_error' );
	}
}

/**
 * Admin notice for incompatible versions of PHP.
 */
function _simple_share_buttons_adder_php_version_error() {
	printf( '<div class="error"><p>%s</p></div>', esc_html( _simple_share_buttons_adder_php_version_text() ) );
}

/**
 * String describing the minimum PHP version.
 *
 * @return string
 */
function _simple_share_buttons_adder_php_version_text() {
	return __( 'Simple Share Buttons Adder plugin error: Your version of PHP is too old to run this plugin. You must be running PHP 5.3 or higher.', 'simple-share-buttons-adder' );
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), '_simple_share_buttons_adder_add_action_links' );

function _simple_share_buttons_adder_add_action_links( $links ) {
	$mylinks = array(
		'<a href="' . admin_url( 'options-general.php?page=simple-share-buttons-adder' ) . '">Settings</a>',
	);
	return array_merge( $links, $mylinks );
}
