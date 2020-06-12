<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\Plugin;

use FernleafSystems\Wordpress\Plugin\Shield\Modules\Base;
use FernleafSystems\Wordpress\Services\Services;

class Strings extends Base\Strings {

	/**
	 * @return string[]
	 */
	protected function getAdditionalDisplayStrings() {
		return [
			'actions_title'   => __( 'Plugin Actions', 'wp-simple-firewall' ),
			'actions_summary' => __( 'E.g. Import/Export', 'wp-simple-firewall' ),
		];
	}

	/**
	 * @return string[][]
	 */
	protected function getAuditMessages() {
		return [
			'import_notify_sent'     => [
				__( 'Sent notifications to whitelisted sites for required options import.', 'wp-simple-firewall' )
			],
			'import_notify_received' => [
				__( 'Received notification that options import required.', 'wp-simple-firewall' ),
				__( 'Current master site: %s', 'wp-simple-firewall' )
			],
			'options_exported'       => [
				__( 'Options exported to site: %s', 'wp-simple-firewall' ),
			],
			'options_imported'       => [
				__( 'Options imported from site: %s', 'wp-simple-firewall' ),
			],
			'whitelist_site_added'   => [
				__( 'Site added to export white list: %s', 'wp-simple-firewall' ),
			],
			'whitelist_site_removed' => [
				__( 'Site removed from export white list: %s', 'wp-simple-firewall' ),
			],
			'master_url_set'         => [
				__( 'Master Site URL set: %s', 'wp-simple-firewall' ),
			],
			'recaptcha_fail'         => [
				__( 'CAPTCHA Test Fail', 'wp-simple-firewall' )
			],
		];
	}

	/**
	 * @param string $sSectionSlug
	 * @return array
	 * @throws \Exception
	 */
	public function getSectionStrings( $sSectionSlug ) {
		$sPlugName = $this->getCon()->getHumanName();

		switch ( $sSectionSlug ) {

			case 'section_global_security_options' :
				$sTitle = __( 'Global Security Plugin Disable', 'wp-simple-firewall' );
				$sTitleShort = sprintf( __( 'Disable %s', 'wp-simple-firewall' ), $sPlugName );
				$aSummary = [
					sprintf( '%s - %s', __( 'Purpose', 'wp-simple-firewall' ), __( 'Use this option to completely disable all active Shield Protection.', 'wp-simple-firewall' ) ),
				];
				break;

			case 'section_defaults' :
				$sTitle = __( 'Plugin Defaults', 'wp-simple-firewall' );
				$sTitleShort = __( 'Plugin Defaults', 'wp-simple-firewall' );
				$aSummary = [
					sprintf( '%s - %s', __( 'Purpose', 'wp-simple-firewall' ), __( 'Important default settings used throughout the plugin.', 'wp-simple-firewall' ) ),
				];
				break;

			case 'section_importexport' :
				$sTitle = sprintf( '%s / %s', __( 'Import', 'wp-simple-firewall' ), __( 'Export', 'wp-simple-firewall' ) );
				$aSummary = [
					sprintf( '%s - %s', __( 'Purpose', 'wp-simple-firewall' ), __( 'Automatically import options, and deploy configurations across your entire network.', 'wp-simple-firewall' ) ),
					sprintf( __( 'This is a Pro-only feature.', 'wp-simple-firewall' ) ),
				];
				$sTitleShort = sprintf( '%s / %s', __( 'Import', 'wp-simple-firewall' ), __( 'Export', 'wp-simple-firewall' ) );
				break;

			case 'section_general_plugin_options' :
				$sTitle = __( 'General Plugin Options', 'wp-simple-firewall' );
				$sTitleShort = __( 'General Options', 'wp-simple-firewall' );
				break;

			case 'section_third_party_captcha' :
				$sTitle = __( 'CAPTCHA', 'wp-simple-firewall' );
				$sTitleShort = __( 'CAPTCHA', 'wp-simple-firewall' );
				$aSummary = [
					sprintf( '%s - %s', __( 'Purpose', 'wp-simple-firewall' ), sprintf( __( 'Setup CAPTCHA for use across %s.', 'wp-simple-firewall' ), $sPlugName ) ),
					sprintf( '%s - %s',
						__( 'Recommendation', 'wp-simple-firewall' ),
						sprintf( __( 'Use of this feature is highly recommend.', 'wp-simple-firewall' ).' '
								 .sprintf( '%s: %s', __( 'Note', 'wp-simple-firewall' ), __( 'You must create your own CAPTCHA API Keys.', 'wp-simple-firewall' ) )
						)
						.'<ul class="mt-1"><li>- '.sprintf( ' <a href="%s" target="_blank">%s</a>', 'https://www.google.com/recaptcha/admin', __( 'Google reCAPTCHA Keys', 'wp-simple-firewall' ) )
						.'</li><li>- '.sprintf( ' <a href="%s" target="_blank">%s</a>', 'https://dashboard.hcaptcha.com/', __( 'hCaptcha Keys', 'wp-simple-firewall' ) ).'</li></ul>'
					),
					sprintf( '%s - %s', __( 'Note', 'wp-simple-firewall' ), sprintf( __( 'Invisible CAPTCHA is available with %s Pro.', 'wp-simple-firewall' ), $sPlugName ) )
				];
				break;

			case 'section_third_party_duo' :
				$sTitle = __( 'Duo Security', 'wp-simple-firewall' );
				$sTitleShort = __( 'Duo Security', 'wp-simple-firewall' );
				break;

			default:
				return parent::getSectionStrings( $sSectionSlug );
		}

		return [
			'title'       => $sTitle,
			'title_short' => $sTitleShort,
			'summary'     => ( isset( $aSummary ) && is_array( $aSummary ) ) ? $aSummary : [],
		];
	}

	/**
	 * @param string $sOptKey
	 * @return array
	 * @throws \Exception
	 */
	public function getOptionStrings( $sOptKey ) {
		/** @var \ICWP_WPSF_FeatureHandler_Plugin $oMod */
		$oMod = $this->getMod();
		/** @var Options $oOpts */
		$oOpts = $this->getOptions();
		$sPlugName = $this->getCon()->getHumanName();

		switch ( $sOptKey ) {

			case 'global_enable_plugin_features' :
				$sName = sprintf( __( 'Enable %s Protection', 'wp-simple-firewall' ), $sPlugName );
				$sSummary = __( 'Switch Off To Disable All Security Protection', 'wp-simple-firewall' );
				$sDescription = [
					sprintf( __( "You can keep the security plugin activated, but temporarily disable all protection it provides.", 'wp-simple-firewall' ), $sPlugName ),
					sprintf( '<a href="%s" target="_blank">%s</a>',
						$this->getCon()->getModule_Insights()->getUrl_SubInsightsPage( 'debug' ),
						'Launch Debug Info Page'
					)
				];
				break;

			case 'enable_tracking' :
				$sName = __( 'Anonymous Usage Statistics', 'wp-simple-firewall' );
				$sSummary = __( 'Permit Anonymous Usage Information Gathering', 'wp-simple-firewall' );
				$sDescription = [
					__( 'Allows us to gather information on statistics and features in-use across our client installations.', 'wp-simple-firewall' )
					.' '.__( 'This information is strictly anonymous and contains no personally, or otherwise, identifiable data.', 'wp-simple-firewall' ),
					sprintf( '<a href="%s" target="_blank" class="new-window-link">%s</a>', $oMod->getLinkToTrackingDataDump(), __( 'Click to see the exact data that would be sent.', 'wp-simple-firewall' ) )
				];
				break;

			case 'visitor_address_source' :
				$sName = __( 'IP Source', 'wp-simple-firewall' );
				$sSummary = __( 'Which IP Address Is Yours', 'wp-simple-firewall' );
				$sDescription = __( 'There are many possible ways to detect visitor IP addresses. If Auto-Detect is not working, please select yours from the list.', 'wp-simple-firewall' )
								.'<br />'.__( 'If the option you select becomes unavailable, we will revert to auto detection.', 'wp-simple-firewall' )
								.'<br />'.sprintf(
									__( 'Current source is: %s (%s)', 'wp-simple-firewall' ),
									'<strong>'.$oOpts->getIpSource().'</strong>',
									Services::IP()->getRequestIp()
								)
								.sprintf(
									'<p class="mt-2"><a href="%s" target="_blank">%s</a></p>',
									'https://shsec.io/shieldwhatismyip',
									__( 'What Is My IP Address?', 'wp-simple-firewall' )
								);
				break;

			case 'block_send_email_address' :
				$sName = __( 'Report Email', 'wp-simple-firewall' );
				$sSummary = __( 'Where to send email reports', 'wp-simple-firewall' );
				$sDescription = sprintf( __( 'If this is empty, it will default to the blog admin email address: %s', 'wp-simple-firewall' ), '<br /><strong>'.get_bloginfo( 'admin_email' ).'</strong>' );
				break;

			case 'enable_upgrade_admin_notice' :
				$sName = __( 'In-Plugin Notices', 'wp-simple-firewall' );
				$sSummary = __( 'Display Plugin Specific Notices', 'wp-simple-firewall' );
				$sDescription = __( 'Disable this option to hide certain plugin admin notices about available updates and post-update notices.', 'wp-simple-firewall' );
				break;

			case 'display_plugin_badge' :
				$sName = __( 'Show Plugin Badge', 'wp-simple-firewall' );
				$sSummary = __( 'Display Plugin Badge On Your Site', 'wp-simple-firewall' );
				$sDescription = __( 'Enabling this option helps support the plugin by spreading the word about it on your website.', 'wp-simple-firewall' )
								.' '.__( 'The plugin badge also lets visitors know your are taking your website security seriously.', 'wp-simple-firewall' )
								.sprintf( '<br /><strong><a href="%s" target="_blank">%s</a></strong>', 'https://shsec.io/wpsf20', __( 'Read this carefully before enabling this option.', 'wp-simple-firewall' ) );
				break;

			case 'delete_on_deactivate' :
				$sName = __( 'Delete Plugin Settings', 'wp-simple-firewall' );
				$sSummary = __( 'Delete All Plugin Settings Upon Plugin Deactivation', 'wp-simple-firewall' );
				$sDescription = __( 'Careful: Removes all plugin options when you deactivate the plugin', 'wp-simple-firewall' );
				break;

			case 'enable_xmlrpc_compatibility' :
				$sName = __( 'XML-RPC Compatibility', 'wp-simple-firewall' );
				$sSummary = __( 'Allow Login Through XML-RPC To By-Pass Accounts Management Rules', 'wp-simple-firewall' );
				$sDescription = __( 'Enable this if you need XML-RPC functionality e.g. if you use the WordPress iPhone/Android App.', 'wp-simple-firewall' );
				break;

			case 'importexport_enable' :
				$sName = __( 'Allow Import/Export', 'wp-simple-firewall' );
				$sSummary = __( 'Allow Import And Export Of Options On This Site', 'wp-simple-firewall' );
				$sDescription = __( 'Uncheck this box to completely disable import and export of options.', 'wp-simple-firewall' )
								.'<br />'.sprintf( '%s: %s', __( 'Note', 'wp-simple-firewall' ), __( 'Import/Export is a premium-only feature.', 'wp-simple-firewall' ) );
				break;

			case 'importexport_whitelist' :
				$sName = __( 'Export Whitelist', 'wp-simple-firewall' );
				$sSummary = __( 'Whitelisted Sites To Export Options From This Site', 'wp-simple-firewall' );
				$sDescription = __( 'Whitelisted sites may export options from this site without the key.', 'wp-simple-firewall' )
								.'<br />'.__( 'List each site URL on a new line.', 'wp-simple-firewall' )
								.'<br />'.__( 'This is to be used in conjunction with the Master Import Site feature.', 'wp-simple-firewall' );
				break;

			case 'importexport_masterurl' :
				$sName = __( 'Master Import Site', 'wp-simple-firewall' );
				$sSummary = __( 'Automatically Import Options From This Site URL', 'wp-simple-firewall' );
				$sDescription = __( "Supplying a site URL here will make this site an 'Options Slave'.", 'wp-simple-firewall' )
								.'<br />'.__( 'Options will be automatically exported from the Master site each day.', 'wp-simple-firewall' )
								.'<br />'.sprintf( '%s: %s', __( 'Warning', 'wp-simple-firewall' ), __( 'Use of this feature will overwrite existing options and replace them with those from the Master Import Site.', 'wp-simple-firewall' ) );
				break;

			case 'importexport_whitelist_notify' :
				$sName = __( 'Notify Whitelist', 'wp-simple-firewall' );
				$sSummary = __( 'Notify Sites On The Whitelist To Update Options From Master', 'wp-simple-firewall' );
				$sDescription = __( "When enabled, manual options saving will notify sites on the whitelist to export options from the Master site.", 'wp-simple-firewall' );
				break;

			case 'importexport_secretkey' :
				$sName = __( 'Secret Key', 'wp-simple-firewall' );
				$sSummary = __( 'Import/Export Secret Key', 'wp-simple-firewall' );
				$sDescription = __( 'Keep this Secret Key private as it will allow the import and export of options.', 'wp-simple-firewall' );
				break;

			case 'unique_installation_id' :
				$sName = __( 'Installation ID', 'wp-simple-firewall' );
				$sSummary = __( 'Unique Plugin Installation ID', 'wp-simple-firewall' );
				$sDescription = __( 'Keep this ID private.', 'wp-simple-firewall' );
				break;

			case 'captcha_provider' :
				$sName = __( 'CAPTCHA Provider', 'wp-simple-firewall' );
				$sSummary = __( 'Which CAPTCHA Provider To Use Throughout', 'wp-simple-firewall' );
				$sDescription = [
					__( 'You can choose the CAPTCHA provider depending on your preferences.', 'wp-simple-firewall' ),
					__( 'Ensure your Site Keys and Secret Keys are supplied from the appropriate provider.', 'wp-simple-firewall' ),
					sprintf( '<strong>%s</strong>',
						sprintf( '%s: %s', __( 'Important', 'wp-simple-firewall' ),
							__( 'Keys for different providers are not interchangeable.', 'wp-simple-firewall' ) )
					),
				];
				break;

			case 'google_recaptcha_secret_key' :
				$sName = __( 'CAPTCHA Secret', 'wp-simple-firewall' );
				$sSummary = __( 'CAPTCHA Secret Key', 'wp-simple-firewall' );
				$sDescription = __( 'Enter your CAPTCHA secret key for use throughout the plugin.', 'wp-simple-firewall' )
								.'<br />'.sprintf( '<strong>%s</strong>: %s', __( 'Important', 'wp-simple-firewall' ), __( 'Google reCAPTCHA v3 not supported.', 'wp-simple-firewall' ) );
				break;

			case 'google_recaptcha_site_key' :
				$sName = __( 'CAPTCHA Site Key', 'wp-simple-firewall' );
				$sSummary = __( 'CAPTCHA Site Key', 'wp-simple-firewall' );
				$sDescription = __( 'Enter your CAPTCHA site key for use throughout the plugin.', 'wp-simple-firewall' )
								.'<br />'.sprintf( '<strong>%s</strong>: %s', __( 'Important', 'wp-simple-firewall' ), __( 'Google reCAPTCHA v3 not supported.', 'wp-simple-firewall' ) );
				break;

			case 'google_recaptcha_style' :
				$sName = __( 'CAPTCHA Style', 'wp-simple-firewall' );
				$sSummary = __( 'How CAPTCHA Will Be Displayed By Default', 'wp-simple-firewall' );
				$sDescription = __( 'You can choose the CAPTCHA display format that best suits your site, including the new Invisible CAPTCHA.', 'wp-simple-firewall' );
				break;

			default:
				return parent::getOptionStrings( $sOptKey );
		}

		return [
			'name'        => $sName,
			'summary'     => $sSummary,
			'description' => $sDescription,
		];
	}

	/**
	 * Kept just in-case and represent dynamically translated strings
	 */
	private function manual_translations() {
		{ // selects
			__( 'Install', 'wp-simple-firewall' );
			__( 'Update', 'wp-simple-firewall' );
			__( 'Activate', 'wp-simple-firewall' );
			__( 'Delete', 'wp-simple-firewall' );
			__( 'Edit Theme Options', 'wp-simple-firewall' );
			__( 'Create/Edit', 'wp-simple-firewall' );
			__( 'Publish', 'wp-simple-firewall' );
			__( 'Author Name', 'wp-simple-firewall' );
			__( 'Author Email', 'wp-simple-firewall' );
			__( 'Comment Content', 'wp-simple-firewall' );
			__( 'Browser User Agent', 'wp-simple-firewall' );
			__( 'Login', 'wp-simple-firewall' );
			__( 'Register', 'wp-simple-firewall' );
			__( 'Lost Password', 'wp-simple-firewall' );
			__( 'Checkout (WooCommerce)', 'wp-simple-firewall' );
			__( 'Simple Requests', 'wp-simple-firewall' );
			__( 'Logged-In Users', 'wp-simple-firewall' );
			__( 'Search Engines', 'wp-simple-firewall' );
			__( 'Uptime Monitoring Services', 'wp-simple-firewall' );
			__( 'Enabled With Email Reports', 'wp-simple-firewall' );
			__( 'Never', 'wp-simple-firewall' );
			__( 'Minor Versions Only', 'wp-simple-firewall' );
			__( 'Major and Minor Versions', 'wp-simple-firewall' );
			__( 'Let The Plugin Decide', 'wp-simple-firewall' );
			__( 'As Soon As Possible', 'wp-simple-firewall' );
			__( 'Move To Pending Moderation', 'wp-simple-firewall' );
			__( 'Move To SPAM', 'wp-simple-firewall' );
			__( 'Move To Trash', 'wp-simple-firewall' );
			__( 'Block And Redirect', 'wp-simple-firewall' );
			__( 'Invisible', 'wp-simple-firewall' );
			__( 'Default Style', 'wp-simple-firewall' );
			__( 'Redirect To Home Page', 'wp-simple-firewall' );
			__( 'Return 404', 'wp-simple-firewall' );
			__( 'Die', 'wp-simple-firewall' );
			__( 'Scan Disabled', 'wp-simple-firewall' );
			__( 'Scan Enabled', 'wp-simple-firewall' );
			__( 'Automatic Scan Disabled', 'wp-simple-firewall' );
			__( 'Automatic Scan Enabled', 'wp-simple-firewall' );
			__( 'Scan Enabled - Send Email Notification', 'wp-simple-firewall' );
			__( 'Scan Enabled - No Email Notification', 'wp-simple-firewall' );
			__( 'Scan Enabled - Automatically Delete Files', 'wp-simple-firewall' );
			__( 'Scan Enabled - Delete Files and Send Email Notification', 'wp-simple-firewall' );
			__( 'Off: iFrames Not Blocked', 'wp-simple-firewall' );
			__( 'On: Allow iFrames On The Same Domain', 'wp-simple-firewall' );
			__( 'On: Block All iFrames', 'wp-simple-firewall' );
			__( "Default: Full Referrer URL (aka 'Unsafe URL')", 'wp-simple-firewall' );
			__( 'No Referrer', 'wp-simple-firewall' );
			__( 'No Referrer When Downgrade', 'wp-simple-firewall' );
			__( 'Same Origin', 'wp-simple-firewall' );
			__( 'Origin', 'wp-simple-firewall' );
			__( 'Strict Origin', 'wp-simple-firewall' );
			__( 'Origin When Cross-Origin', 'wp-simple-firewall' );
			__( 'Strict Origin When Cross-Origin', 'wp-simple-firewall' );
			__( 'Empty Header', 'wp-simple-firewall' );
			__( "Disabled - Don't Send This Header", 'wp-simple-firewall' );
			__( 'Minute', 'wp-simple-firewall' );
			__( 'Hour', 'wp-simple-firewall' );
			__( 'Day', 'wp-simple-firewall' );
			__( 'Week', 'wp-simple-firewall' );
			__( 'Month', 'wp-simple-firewall' );
			__( 'With Shield Bot Protection', 'wp-simple-firewall' );
			__( 'Audit Log Only', 'wp-simple-firewall' );
			__( 'Increment Offense Counter', 'wp-simple-firewall' );
			__( 'Double-Increment Offense Counter', 'wp-simple-firewall' );
			__( 'Immediate Block', 'wp-simple-firewall' );
			__( 'Very Weak', 'wp-simple-firewall' );
			__( 'Weak', 'wp-simple-firewall' );
			__( 'Medium', 'wp-simple-firewall' );
			__( 'Strong', 'wp-simple-firewall' );
			__( 'Very Strong', 'wp-simple-firewall' );
		}

		__( 'General Settings', 'wp-simple-firewall' );
		__( 'Security Dashboard', 'wp-simple-firewall' );
		__( 'Automatically Detect Visitor IP', 'wp-simple-firewall' );
		__( 'IP Whitelist', 'wp-simple-firewall' );
		__( 'IP Address White List', 'wp-simple-firewall' );
		__( 'Any IP addresses on this list will by-pass all Plugin Security Checking.', 'wp-simple-firewall' );
		__( 'Your IP address is: %s', 'wp-simple-firewall' );
		__( 'Choose IP Addresses To Blacklist', 'wp-simple-firewall' );
		__( 'Recommendation - %s', 'wp-simple-firewall' );
		__( 'Blacklist', 'wp-simple-firewall' );
		__( 'Logging', 'wp-simple-firewall' );
		__( 'User "%s" was forcefully logged out as they were not verified by either cookie or IP address (or both).', 'wp-simple-firewall' );
		__( 'User "%s" was found to be un-verified at the given IP Address: "%s".', 'wp-simple-firewall' );
		__( 'Cookie', 'wp-simple-firewall' );
		__( 'IP Address', 'wp-simple-firewall' );
		__( 'IP', 'wp-simple-firewall' );
		__( 'This will restrict all user login sessions to a single browser. Use this if your users have dynamic IP addresses.', 'wp-simple-firewall' );
		__( 'All users will be required to authenticate their login by email-based two-factor authentication, when logging in from a new IP address', 'wp-simple-firewall' );
		__( '2-Factor Auth', 'wp-simple-firewall' );
		__( 'Include Logged-In Users', 'wp-simple-firewall' );
		__( 'You may also enable GASP for logged in users', 'wp-simple-firewall' );
		__( 'Since logged-in users would be expected to be vetted already, this is off by default.', 'wp-simple-firewall' );
		__( 'Security Admin', 'wp-simple-firewall' );
		__( 'Protect your security plugin not just your WordPress site', 'wp-simple-firewall' );
		__( 'Security Admin', 'wp-simple-firewall' );
		__( 'Audit Trail', 'wp-simple-firewall' );
		__( 'Get a view on what happens on your site, when it happens', 'wp-simple-firewall' );
		__( 'Audit Trail Viewer', 'wp-simple-firewall' );
		__( 'Automatic Updates', 'wp-simple-firewall' );
		__( 'Take back full control of WordPress automatic updates', 'wp-simple-firewall' );
		__( 'Comments SPAM', 'wp-simple-firewall' );
		__( 'Block Bad IPs/Visitors', 'wp-simple-firewall' );
		__( 'Block comment SPAM and retain your privacy', 'wp-simple-firewall' );
		__( 'Email', 'wp-simple-firewall' );
		__( 'Firewall', 'wp-simple-firewall' );
		__( 'Automatically block malicious URLs and data sent to your site', 'wp-simple-firewall' );
		__( 'Hack Guard', 'wp-simple-firewall' );
		__( 'HTTP Headers', 'wp-simple-firewall' );
		__( 'Control HTTP Security Headers', 'wp-simple-firewall' );
		__( 'IP Manager', 'wp-simple-firewall' );
		__( 'Manage Visitor IP Address', 'wp-simple-firewall' );
		__( 'WP Lockdown', 'wp-simple-firewall' );
		__( 'Harden the more loosely controlled settings of your site', 'wp-simple-firewall' );
		__( 'Login Guard', 'wp-simple-firewall' );
		__( 'Block brute force attacks and secure user identities with Two-Factor Authentication', 'wp-simple-firewall' );
		__( 'Dashboard', 'wp-simple-firewall' );
		__( 'General Plugin Settings', 'wp-simple-firewall' );
		__( 'Statistics', 'wp-simple-firewall' );
		__( 'Summary of the main security actions taken by this plugin', 'wp-simple-firewall' );
		__( 'Stats Viewer', 'wp-simple-firewall' );
		__( 'Premium Support', 'wp-simple-firewall' );
		__( 'Premium Plugin Support Centre', 'wp-simple-firewall' );
		__( 'User Management', 'wp-simple-firewall' );
		__( 'Get true user sessions and control account sharing, session duration and timeouts', 'wp-simple-firewall' );
		__( 'Two-Factor Authentication', 'wp-simple-firewall' );
		__( 'Support Forums', 'wp-simple-firewall' );
		__( 'Light Theme', 'wp-simple-firewall' );
		__( 'Dark Theme', 'wp-simple-firewall' );
		__( 'Once', 'wp-simple-firewall' );
		__( 'Twice', 'wp-simple-firewall' );
		__( 'Go To Security Dashboard', 'wp-simple-firewall' );

		__( 'None - Turn Off Malware Intelligence Network', 'wp-simple-firewall' );
		__( 'Low', 'wp-simple-firewall' );
		__( 'Medium', 'wp-simple-firewall' );
		__( 'High', 'wp-simple-firewall' );
		__( 'Full', 'wp-simple-firewall' );

		__( 'Last Offense', 'wp-simple-firewall' );
		__( 'Automatic license verification failed.', 'wp-simple-firewall' );
	}
}