<?php
/*
	This file handles the support licensing control for Paid Memberships Pro
	and PMPro addons.
	
	How it works:
	- All source code and resource files bundled with this plugin are licensed under the GPLv2 license unless otherwise noted (e.g. included third-party libraries).
	- An additional "support license" can be purchased at http://www.paidmembershipspro.com/support-license/
	  which will simultaneous support the development of this plugin and also give you access to support forums and documentation.
	- Once your license has been purchased, visit Settings --> PMPro License in your WP dashboard to enter your license.
	- Once the license is activated all "nags" will be disabled in the dashboard and member links will be added where appropriate.
    - This plugin will function 100% even if the support license is not installed.
    - If no support license is detected on this site, prompts will show in the admin to encourage you to purchase one.
	- You can override these prompts by setting the PMPRO_LICENSE_NAG constant to false.
*/

/*
	Developers, add this line to your wp-config.php to remove PMPro license nags even if no license has been purchased.
	
	define('PMPRO_LICENSE_NAG', false);	//consider purchasing a license at http://www.paidmembershipspro.com/support-license/
*/

/*
	Constants
*/
define('PMPRO_LICENSE_SERVER', 'https://license.paidmembershipspro.com/');

/*
	Add license settings page.
*/
function pmpro_license_settings_page() {				

	//only let admins get here
	if(!function_exists("current_user_can") || (!current_user_can("manage_options") && !current_user_can("pmpro_license")))
	{
		die(__("You do not have permissions to perform this action.", 'paid-memberships-pro' ));
	}
	
	//updating license?
	if(!empty($_REQUEST['pmpro-verify-submit']))
	{
		$key = preg_replace("/[^a-zA-Z0-9]/", "", $_REQUEST['pmpro-license-key']);
					
		//erase the old key
		delete_option('pmpro_license_key');
		
		//check key
		$valid = pmpro_license_isValid($key, NULL, true);
		
		if($valid)
		{
		?>
		<div id="message" class="updated fade">
			<p><?php _e('Your license key has been validated.', 'paid-memberships-pro' );?></p>
		</div>
		<?php
		}
		else
		{
			global $pmpro_license_error;
			if(!empty($pmpro_license_error))
			{
			?>
			<div id="message" class="error">
				<p><?php echo $pmpro_license_error;?></p>
			</div>
			<?php
			}
		}
		
		//update key
		update_option('pmpro_license_key', $key, 'no');
	}	
	
	//get saved license
	$key = get_option("pmpro_license_key", "");
	$pmpro_license_check = get_option("pmpro_license_check", array("license"=>false, "enddate"=>0));

	//html for license settings page
	if(defined('PMPRO_DIR'))
		require_once(PMPRO_DIR . "/adminpages/admin_header.php");
	?>
	<div class="wrap">
		<h2><?php _e('Paid Memberships Pro Support License', 'paid-memberships-pro' );?></h2>
		<p>Paid Memberships Pro and our add ons are distributed under the <a target="_blank" href='http://www.gnu.org/licenses/gpl-2.0.html'>GPLv2 license</a>. This means, among other things, that you may use the software on this site or any other site free of charge.</p>
		<p><strong>An annual support license is recommended for websites running Paid Memberships Pro.</strong> <a href="http://www.paidmembershipspro.com/pricing/?utm_source=plugin&utm_medium=banner&utm_campaign=license_notice" target="_blank">View Support License Options &raquo;</a></p>			
		<div class="metabox-holder">
			<div class="postbox">		
				<h3 class="hndle"><?php _e('License Key', 'paid-memberships-pro' );?></h3>
				<div class="inside">										
					<?php if(!pmpro_license_isValid() && empty($key)) { ?>
						<div class="notice notice-error inline"><p><strong><?php _e('Enter your support license key.</strong> Your license key can be found in your membership email receipt or in your <a href="http://www.paidmembershipspro.com/login/?redirect_to=/membership-account/?utm_source=plugin&utm_medium=banner&utm_campaign=license_notice" target="_blank">Membership Account</a>.', 'paid-memberships-pro' );?></p></div>
					<?php } elseif(!pmpro_license_isValid()) { ?>
						<div class="notice notice-error inline"><p><strong><?php _e('Your license is invalid or expired.', 'paid-memberships-pro' );?></strong> <?php _e('Visit the PMPro <a href="http://www.paidmembershipspro.com/login/?redirect_to=/membership-account/?utm_source=plugin&utm_medium=banner&utm_campaign=license_notice" target="_blank">Membership Account</a> page to confirm that your account is active and to find your license key.', 'paid-memberships-pro' );?></p></div>
					<?php } else { ?>													
						<div class="notice inline"><?php printf(__('<p><strong>Thank you!</strong> A valid <strong>%s</strong> license key has been used to activate your support license on this site.</p>', 'paid-memberships-pro' ), ucwords($pmpro_license_check['license']));?></div>
					<?php } ?>					
					<form action="" method="post">
					<table class="form-table">
						<tbody>
							<tr id="pmpro-settings-key-box">
								<td>
									<input type="password" name="pmpro-license-key" id="pmpro-license-key" value="<?php echo esc_attr($key);?>" placeholder="<?php _e('Enter license key here...', 'paid-memberships-pro' );?>" size="40"  />
									<?php wp_nonce_field( 'pmpro-key-nonce', 'pmpro-key-nonce' ); ?>
									<?php submit_button( __( 'Verify Key', 'paid-memberships-pro' ), 'primary', 'pmpro-verify-submit', false ); ?>										
								</td>
							</tr>
						</tbody>
					</table>
					</form>
				</div> <!-- end inside -->
			</div> <!-- end post-box -->
		</div> <!-- end metabox-holder -->		
	</div> <!-- end wrap -->
	<?php
}

function pmpro_license_admin_menu() {
	//add license settings page
	add_options_page('PMPro License', 'PMPro License', 'manage_options', 'pmpro_license_settings', 'pmpro_license_settings_page');
}
add_action('admin_menu', 'pmpro_license_admin_menu');

/*
	Check license.
*/
function pmpro_license_isValid($key = NULL, $type = NULL, $force = false) {		
	//check cache first
	$pmpro_license_check = get_option('pmpro_license_check', false);
	if(empty($force) && $pmpro_license_check !== false && $pmpro_license_check['enddate'] > current_time('timestamp'))
	{
		if(empty($type))
			return true;
		elseif($type == $pmpro_license_check['license'])
			return true;
		else
			return false;
	}
	
	//get key and site url
	if(empty($key))
		$key = get_option("pmpro_license_key", "");
	
	//no key
	if(!empty($key)) 
	{
		return pmpro_license_check_key($key);
	}
	else
	{
		//no key
		delete_option('pmpro_license_check');
		add_option('pmpro_license_check', array('license'=>false, 'enddate'=>0), NULL, 'no');
	
		return false;
	}
}

/*
	Activation/Deactivation. Check keys once a month.
*/
//activation
function pmpro_license_activation() {
	pmpro_maybe_schedule_event(current_time('timestamp'), 'monthly', 'pmpro_license_check_key');
}
register_activation_hook(__FILE__, 'pmpro_activation');

//deactivation
function pmpro_license_deactivation() {
	wp_clear_scheduled_hook('pmpro_license_check_key');
}
register_deactivation_hook(__FILE__, 'pmpro_deactivation');

//check keys with PMPro once a month
function pmpro_license_check_key($key = NULL) {
	//get key
	if(empty($key))
		$key = get_option('pmpro_license_key');
	
	//key? check with server
	if(!empty($key))
	{
		//check license server
		$url = add_query_arg(array('license'=>$key, 'domain'=>site_url()), PMPRO_LICENSE_SERVER);

        /**
         * Filter to change the timeout for this wp_remote_get() request.
         *
         * @since 1.8.5.1
         *
         * @param int $timeout The number of seconds before the request times out
         */
        $timeout = apply_filters("pmpro_license_check_key_timeout", 5);

        $r = wp_remote_get($url, array("timeout" => $timeout));

        //test response
        if(is_wp_error($r)) {
            //error
            pmpro_setMessage("Could not connect to the PMPro License Server to check key Try again later.", "error");
        }
        elseif(!empty($r) && $r['response']['code'] == 200)
		{
			$r = json_decode($r['body']);
						
			if($r->active == 1)
			{
				//valid key save enddate
				if(!empty($r->enddate))
					$enddate = strtotime($r->enddate, current_time('timestamp'));
				else
					$enddate = strtotime("+1 Year", current_time("timestamp"));
					
				delete_option('pmpro_license_check');
				add_option('pmpro_license_check', array('license'=>$r->license, 'enddate'=>$enddate), NULL, 'no');		
				return true;
			}
			elseif(!empty($r->error))
			{
				//invalid key
				global $pmpro_license_error;
				$pmpro_license_error = $r->error;
				
				delete_option('pmpro_license_check');
				add_option('pmpro_license_check', array('license'=>false, 'enddate'=>0), NULL, 'no');
                
			}
		}	
	}

    //no key or there was an error
    return false;
}
add_action('pmpro_license_check_key', 'pmpro_license_check_key');

/*
	Check for pause
*/
function pmpro_license_pause() {
	if(!empty($_REQUEST['pmpro_nag_paused']) && current_user_can('manage_options')) {
		$pmpro_nag_paused = current_time('timestamp')+(3600*24*7);
		update_option('pmpro_nag_paused', $pmpro_nag_paused, 'no');
		
		return;
	}
}
add_action('admin_init', 'pmpro_license_pause');

/*
	Add nags.
*/
//nag function embedded into headers of plugins
function pmpro_license_nag() {
	global $pmpro_nagged;
	
	//nagged already?
	if(!empty($pmpro_nagged))
		return;
		
	//remember that we've nagged already
	$pmpro_nagged = true;
	
	//blocked by constant?
	if(defined('PMPRO_LICENSE_NAG') && !PMPRO_LICENSE_NAG)
		return;
	
	//don't load on the license page
	if(!empty($_REQUEST['page']) && $_REQUEST['page'] == 'pmpro_license_settings')
		return;
	
	//valid license?
	if(pmpro_license_isValid())
		return;
	
	//always show on updates page
	/*
	$screen = get_current_screen();	
	if($screen->id == 'update-core')
		$pmpro_nag_paused = false;	
	else
	*/
		$pmpro_nag_paused = get_option('pmpro_nag_paused', 0);		
		
	if(current_time('timestamp') < $pmpro_nag_paused && $pmpro_nag_paused < current_time('timestamp')*3600*24*8)
		return;

	//get key for later
	$key = get_option('pmpro_license_key');

	//okay, show nag
	?>
	<div class="<?php if(!empty($key)) { ?>error<?php } else { ?>notice notice-warning<?php } ?> fade">
		<p>
			<?php
				//only show the invalid part if they've entered a key
				
				if(!empty($key)) {
					?><strong><?php _e('Invalid PMPro License Key.', 'paid-memberships-pro' );?></strong><?php
				} 
			?>
			<?php _e("If you're running Paid Memberships Pro on a production website, we recommend an annual support license.", 'paid-memberships-pro' );?>
			<a href="<?php echo admin_url('options-general.php?page=pmpro_license_settings');?>"><?php _e('More Info', 'paid-memberships-pro' );?></a>&nbsp;|&nbsp;<a href="<?php echo add_query_arg('pmpro_nag_paused', '1', $_SERVER['REQUEST_URI']);?>"><?php _e('Dismiss', 'paid-memberships-pro' );?></a>
		</p>
	</div>
	<?php
}
add_action('admin_notices', 'pmpro_license_nag');
