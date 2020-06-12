<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap" id="weblizar_wrap">
	<div id="content_wrap">			
		<div class="weblizar-header">
			<h2><span class="dashicons dashicons-twitter" style="width: auto;"><?php esc_html_e('Customize Feeds for Twitter', 'twitter-tweets');?></span></h2>
			<br>
			<div class="weblizar-submenu-links" id="weblizar-submenu-links">
				<ul>
					<li class=""><div class="dashicons dashicons-format-chat"></div>
					<a href="https://wordpress.org/plugins/twitter-tweets/" target="_blank" title="<?php esc_attr_e( 'Support Forum', 'twitter-tweets' ); ?>">
						<?php esc_html_e('Support Forum', 'twitter-tweets');?></a>
					</li>
					<li class=""><div class="dashicons dashicons-welcome-write-blog"></div> 
						<a href="<?php echo WEBLIZAR_TWITTER_PLUGIN_URL.'readme.txt'; ?>" target="_blank" title="<?php esc_attr_e( 'Plugin Changelog', 'twitter-tweets' ); ?>">
						<?php esc_html_e('Plugin Change Log','twitter-tweets');?></a>
					</li>
				</ul>
			</div>			
		</div>			
	</div>
	<div id="content">
		<div id="options_tabs" class="ui-tabs">
			<ul class="options_tabs ui-tabs-nav" role="tablist" id="nav">					
				<li class="active">
					<a id="general">
						<div class="dashicons dashicons-admin-generic"></div><?php esc_html_e('Feeds Widget','twitter-tweets');?>
					</a>
				</li>
				<li>
					<a id="apikey">
						<div class="dashicons dashicons-admin-generic"></div><?php esc_html_e('Twitter Feeds','twitter-tweets');?>
					</a>
				</li>				
				<li>
					<a id="needhelp">
						<div class="dashicons dashicons-editor-help"></div><?php esc_html_e('Need Help', 'twitter-tweets');?>
					</a>
				</li>
				<li>
					<a id="ourproduct">
						<div class="dashicons dashicons-plus"></div><?php esc_html_e('Upgrade To Pro', 'twitter-tweets');?>
					</a>
				</li>							
			</ul>
			<?php include('banner.php'); ?>				
			<?php require_once('twiiter_help.php'); ?>
		</div>		
	</div>
</div>
