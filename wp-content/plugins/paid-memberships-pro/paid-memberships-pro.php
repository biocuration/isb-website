<?php
/*
Plugin Name: Paid Memberships Pro
Plugin URI: http://www.paidmembershipspro.com
Description: Plugin to Handle Memberships
Version: 1.8.13.5
Author: Stranger Studios
Author URI: http://www.strangerstudios.com
Text Domain: pmpro
Domain Path: /languages
*/
/*
	Copyright 2011	Stranger Studios	(email : jason@strangerstudios.com)
	GPLv2 Full license details in license.txt
*/

//version constant
define("PMPRO_VERSION", "1.8.13.5");
define("PMPRO_USER_AGENT", "Paid Memberships Pro v" . PMPRO_VERSION . "; " . site_url());

//if the session has been started yet, start it (ignore if running from command line)
if(!defined('PMPRO_USE_SESSIONS') || PMPRO_USE_SESSIONS == true) {
	if(defined('STDIN')) {
		//command line
	} else {
		if (version_compare(phpversion(), '5.4.0', '>=')) {
			if (session_status() == PHP_SESSION_NONE)
				session_start();
		} else {
			if(!session_id())
				session_start();
		}
	}
}
	
/*
	Includes
*/
define("PMPRO_DIR", dirname(__FILE__));
require_once(PMPRO_DIR . "/includes/localization.php");			//localization functions
require_once(PMPRO_DIR . "/includes/lib/name-parser.php");		//parses "Jason Coleman" into firstname=>Jason, lastname=>Coleman
require_once(PMPRO_DIR . "/includes/functions.php");			//misc functions used by the plugin
require_once(PMPRO_DIR . "/includes/updates.php");			//database and other updates
require_once(PMPRO_DIR . "/includes/upgradecheck.php");			//database and other updates

if(!defined('PMPRO_LICENSE_SERVER'))
	require_once(PMPRO_DIR . "/includes/license.php");			//defines location of addons data and licenses

require_once(PMPRO_DIR . "/scheduled/crons.php");				//crons for expiring members, sending expiration emails, etc

require_once(PMPRO_DIR . "/classes/class.memberorder.php");		//class to process and save orders
require_once(PMPRO_DIR . "/classes/class.pmproemail.php");		//setup and filter emails sent by PMPro

require_once(PMPRO_DIR . "/includes/filters.php");				//filters, hacks, etc, moved into the plugin
require_once(PMPRO_DIR . "/includes/reports.php");				//load reports for admin (reports may also include tracking code, etc)
require_once(PMPRO_DIR . "/includes/adminpages.php");			//dashboard pages
require_once(PMPRO_DIR . "/includes/services.php");				//services loaded by AJAX and via webhook, etc
require_once(PMPRO_DIR . "/includes/metaboxes.php");			//metaboxes for dashboard
require_once(PMPRO_DIR . "/includes/profile.php");				//edit user/profile fields
require_once(PMPRO_DIR . "/includes/https.php");				//code related to HTTPS/SSL
require_once(PMPRO_DIR . "/includes/notifications.php");		//check for notifications at PMPro, shown in PMPro settings
require_once(PMPRO_DIR . "/includes/init.php");					//code run during init, set_current_user, and wp hooks
require_once(PMPRO_DIR . "/includes/content.php");				//code to check for memebrship and protect content
require_once(PMPRO_DIR . "/includes/email.php");				//code related to email
require_once(PMPRO_DIR . "/includes/recaptcha.php");			//load recaptcha files if needed
require_once(PMPRO_DIR . "/includes/cleanup.php");				//clean things up when deletes happen, etc.
require_once(PMPRO_DIR . "/includes/login.php");				//code to redirect away from login/register page
require_once(PMPRO_DIR . "/includes/capabilities.php");			//manage PMPro capabilities for roles

require_once(PMPRO_DIR . "/includes/xmlrpc.php");				//xmlrpc methods

require_once(PMPRO_DIR . "/shortcodes/checkout_button.php");	//[pmpro_checkout_button] shortcode to show link to checkout for a level
require_once(PMPRO_DIR . "/shortcodes/membership.php");			//[membership] shortcode to hide/show member content
require_once(PMPRO_DIR . "/shortcodes/pmpro_account.php");			//[pmpro_account] shortcode to show account information

//load gateway
require_once(PMPRO_DIR . "/classes/gateways/class.pmprogateway.php");	//loaded by memberorder class when needed

//load payment gateway class
require_once(PMPRO_DIR . "/classes/gateways/class.pmprogateway_authorizenet.php");
require_once(PMPRO_DIR . "/classes/gateways/class.pmprogateway_braintree.php");
require_once(PMPRO_DIR . "/classes/gateways/class.pmprogateway_check.php");
require_once(PMPRO_DIR . "/classes/gateways/class.pmprogateway_cybersource.php");
require_once(PMPRO_DIR . "/classes/gateways/class.pmprogateway_payflowpro.php");
require_once(PMPRO_DIR . "/classes/gateways/class.pmprogateway_paypal.php");
require_once(PMPRO_DIR . "/classes/gateways/class.pmprogateway_paypalexpress.php");
require_once(PMPRO_DIR . "/classes/gateways/class.pmprogateway_paypalstandard.php");
require_once(PMPRO_DIR . "/classes/gateways/class.pmprogateway_stripe.php");
require_once(PMPRO_DIR . "/classes/gateways/class.pmprogateway_twocheckout.php");

/*
	Setup the DB and check for upgrades
*/
global $wpdb;

//check if the DB needs to be upgraded
if(is_admin())
	pmpro_checkForUpgrades();

//load plugin updater
if(is_admin())
	require_once(PMPRO_DIR . "/includes/addons.php");
	
/*
	Definitions
*/
define("SITENAME", str_replace("&#039;", "'", get_bloginfo("name")));
$urlparts = explode("//", home_url());
define("SITEURL", $urlparts[1]);
define("SECUREURL", str_replace("http://", "https://", get_bloginfo("wpurl")));
define("PMPRO_URL", WP_PLUGIN_URL . "/paid-memberships-pro");
define("PMPRO_DOMAIN", pmpro_getDomainFromURL(site_url()));
define("PAYPAL_BN_CODE", "PaidMembershipsPro_SP");

/*
	Globals
*/
global $gateway_environment;
$gateway_environment = pmpro_getOption("gateway_environment");


// Returns a list of all available gateway
function pmpro_gateways(){
	$pmpro_gateways = array(
		'' 					=> __('Testing Only', 'pmpro'),
		'check' 			=> __('Pay by Check', 'pmpro'),
		'stripe' 			=> __('Stripe', 'pmpro'),
		'paypalexpress' 	=> __('PayPal Express', 'pmpro'),
		'paypal' 			=> __('PayPal Website Payments Pro', 'pmpro'),
		'payflowpro' 		=> __('PayPal Payflow Pro/PayPal Pro', 'pmpro'),
		'paypalstandard' 	=> __('PayPal Standard', 'pmpro'),
		'authorizenet' 		=> __('Authorize.net', 'pmpro'),
		'braintree' 		=> __('Braintree Payments', 'pmpro'),
		'twocheckout' 		=> __('2Checkout', 'pmpro'),
		'cybersource' 		=> __('Cybersource', 'pmpro')
	);

	return apply_filters( 'pmpro_gateways', $pmpro_gateways );
}


//when checking levels for users, we save the info here for caching. each key is a user id for level object for that user.
global $all_membership_levels;

//we sometimes refer to this array of levels
global $membership_levels;
$membership_levels = $wpdb->get_results( "SELECT * FROM {$wpdb->pmpro_membership_levels}", OBJECT );

/*
	Activation/Deactivation
*/
//we need monthly crons
function pmpro_cron_schedules_monthly($schedules) {	
	$schedules['monthly'] = array(
		'interval' => 2635200,
		'display' => __('Once a month')
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'pmpro_cron_schedules_monthly'); 

//activation
function pmpro_activation() {
	//schedule crons	
	pmpro_maybe_schedule_event(current_time('timestamp'), 'daily', 'pmpro_cron_expire_memberships');
	pmpro_maybe_schedule_event(current_time('timestamp')+1, 'daily', 'pmpro_cron_expiration_warnings');
	pmpro_maybe_schedule_event(current_time('timestamp'), 'monthly', 'pmpro_cron_credit_card_expiring_warnings');

	pmpro_set_capabilities_for_role( 'administrator', 'enable' );

	do_action('pmpro_activation');
}

//deactivation
function pmpro_deactivation() {
	//remove crons
	wp_clear_scheduled_hook('pmpro_cron_expiration_warnings');
	wp_clear_scheduled_hook('pmpro_cron_trial_ending_warnings');
	wp_clear_scheduled_hook('pmpro_cron_expire_memberships');
	wp_clear_scheduled_hook('pmpro_cron_credit_card_expiring_warnings');

	//remove caps from admin role
	pmpro_set_capabilities_for_role('administrator', 'disable');

	do_action('pmpro_deactivation');
}
register_activation_hook(__FILE__, 'pmpro_activation');
register_deactivation_hook(__FILE__, 'pmpro_deactivation');
