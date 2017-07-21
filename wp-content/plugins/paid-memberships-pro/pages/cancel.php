<?php 
	global $pmpro_msg, $pmpro_msgt, $pmpro_confirm, $current_user, $wpdb;
	
	if(isset($_REQUEST['levelstocancel']) && $_REQUEST['levelstocancel'] !== 'all') {
		//convert spaces back to +
		$_REQUEST['levelstocancel'] = str_replace(array(' ', '%20'), '+', $_REQUEST['levelstocancel']);
		
		//get the ids
		$old_level_ids = array_map('intval', explode("+", preg_replace("/[^0-9al\+]/", "", $_REQUEST['levelstocancel'])));

	} elseif(isset($_REQUEST['levelstocancel']) && $_REQUEST['levelstocancel'] == 'all') {
		$old_level_ids = 'all';
	} else {
		$old_level_ids = false;
	}
?>
<div id="pmpro_cancel">		
	<?php
		if($pmpro_msg) 
		{
			?>
			<div class="pmpro_message <?php echo $pmpro_msgt?>"><?php echo $pmpro_msg?></div>
			<?php
		}
	?>
	<?php 
		if(!$pmpro_confirm) 
		{ 
			if($old_level_ids)
			{
				if(!is_array($old_level_ids) && $old_level_ids == "all")
				{
					?>
					<p><?php _e('Are you sure you want to cancel your membership?', 'pmpro'); ?></p>
					<?php
				}
				else
				{
					$level_names = $wpdb->get_col("SELECT name FROM $wpdb->pmpro_membership_levels WHERE id IN('" . implode("','", $old_level_ids) . "')");
					?>
					<p><?php printf(_n('Are you sure you want to cancel your %s membership?', 'Are you sure you want to cancel your %s memberships?', count($level_names), 'pmpro'), pmpro_implodeToEnglish($level_names)); ?></p>
					<?php
				}
			?>			
			<div class="pmpro_actionlinks">
				<a class="pmpro_btn pmpro_yeslink yeslink" href="<?php echo pmpro_url("cancel", "?levelstocancel=" . esc_attr($_REQUEST['levelstocancel']) . "&confirm=true")?>"><?php _e('Yes, cancel this membership', 'pmpro');?></a>
				<a class="pmpro_btn pmpro_cancel pmpro_nolink nolink" href="<?php echo pmpro_url("account")?>"><?php _e('No, keep this membership', 'pmpro');?></a>
			</div>
			<?php
			}
			else
			{
				if($current_user->membership_level->ID) 
				{ 
					?>
					<hr />
					<h3><?php _e("My Memberships", "pmpro");?></h3>
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<thead>
							<tr>
								<th><?php _e("Level", "pmpro");?></th>
								<th><?php _e("Expiration", "pmpro"); ?></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php
								$current_user->membership_levels = pmpro_getMembershipLevelsForUser($current_user->ID);
								foreach($current_user->membership_levels as $level) {
								?>
								<tr>
									<td class="pmpro_cancel-membership-levelname">
										<?php echo $level->name?>
									</td>
									<td class="pmpro_cancel-membership-expiration">
									<?php 
										if($level->enddate) 
											echo date_i18n(get_option('date_format'), $level->enddate);
										else
											echo "---";
									?>
									</td>
									<td class="pmpro_cancel-membership-cancel">
										<a href="<?php echo pmpro_url("cancel", "?levelstocancel=" . $level->id)?>"><?php _e("Cancel", "pmpro");?></a>
									</td>
								</tr>
								<?php
								}
							?>
						</tbody>
					</table>				
					<div class="pmpro_actionlinks">
						<a href="<?php echo pmpro_url("cancel", "?levelstocancel=all"); ?>"><?php _e("Cancel All Memberships", "pmpro");?></a>
					</div>
					<?php
				}
			}
		}
		else 
		{ 
			?>
			<p><a href="<?php echo get_home_url()?>"><?php _e('Click here to go to the home page.', 'pmpro');?></a></p>
			<?php 
		} 
	?>		
</div> <!-- end pmpro_cancel -->
