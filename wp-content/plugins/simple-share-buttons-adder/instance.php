<?php
/**
 * Instantiates the Simple Share Buttons Adder plugin
 *
 * @package SimpleShareButtonsAdder
 */

namespace SimpleShareButtonsAdder;

define( 'SSBA_FILE', __FILE__ );
define( 'SSBA_ROOT', dirname( __FILE__ ) );
define( 'SSBA_VERSION', '7.3.10' );

global $simple_share_buttons_adder_plugin;

require_once __DIR__ . '/php/class-plugin-base.php';
require_once __DIR__ . '/php/class-plugin.php';

$simple_share_buttons_adder_plugin = new Plugin();

/**
 * Simple Share Buttons Adder Plugin Instance
 *
 * @return Plugin
 */
function get_plugin_instance() {
	global $simple_share_buttons_adder_plugin;
	return $simple_share_buttons_adder_plugin;
}
