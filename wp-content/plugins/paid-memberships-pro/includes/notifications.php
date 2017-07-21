<?php
/*
	This code calls the server at www.paidmembershipspro.com to see if there are any notifications to display to the user. Notifications are shown on the PMPro settings pages in the dashboard.
*/
function pmpro_notifications()
{
	if(current_user_can("manage_options"))
	{		
		$pmpro_notification = get_transient("pmpro_notification_" . PMPRO_VERSION);
		if(empty($pmpro_notification))
		{
			//set to NULL in case the below times out or fails, this way we only check once a day
			set_transient("pmpro_notification_" . PMPRO_VERSION, 'NULL', 86400);
			
			//figure out which server to get from
			if(is_ssl())
			{
				$remote_notification = wp_remote_get("https://notifications.paidmembershipspro.com/?v=" . PMPRO_VERSION);
			}
			else
			{
				$remote_notification = wp_remote_get("http://notifications.paidmembershipspro.com/?v=" . PMPRO_VERSION);
			}						
			
			//get notification
			$pmpro_notification = wp_remote_retrieve_body($remote_notification);
			
			//update transient if we got something
			if(!empty($pmpro_notification))
				set_transient("pmpro_notification_" . PMPRO_VERSION, $pmpro_notification, 86400);
		}
		
		if($pmpro_notification && $pmpro_notification != "NULL")
		{
		?>
		<div id="pmpro_notifications">
			<?php echo $pmpro_notification; ?>
		</div>
		<?php
		}
	}
	
	//exit so we just show this content
	exit;
}
add_action('wp_ajax_pmpro_notifications', 'pmpro_notifications');	

/*
	Show Powered by Paid Memberships Pro comment (only visible in source) in the footer.
*/
function pmpro_link()
{
?>
Memberships powered by Paid Memberships Pro v<?php echo PMPRO_VERSION?>.
<?php
}
function pmpro_footer_link()
{
	if(!pmpro_getOption("hide_footer_link"))
	{
		?>
		<!-- <?php echo pmpro_link()?> -->
		<?php
	}
}
add_action("wp_footer", "pmpro_footer_link");
