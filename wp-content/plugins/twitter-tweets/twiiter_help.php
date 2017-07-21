<style>
label {
	margin-right:10px;
}
#fb-msg {
	border: 1px #888888 solid; background-color: #FFFAF0; padding: 10px; font-size: inherit; font-weight: bold; font-family: inherit; font-style: inherit; text-decoration: inherit;
}
</style>
<script type="text/javascript">
function SaveSettings() {
	var FbAppId = jQuery("#twitter-page-id-fetch").val();
    var User_name_3 = jQuery("#twitter-page-user-name").val();	
	var show_theme = jQuery("#show-theme-background").val();
	var Height = jQuery("#twitter-page-url-Height").val();
	var link_color = jQuery("#twitter-page-lnk-Color").val();
	var replieses = jQuery("#exclude_replies_23").val();
	var photos_acces = jQuery("#photo_1234").val();
	var tw_language = jQuery("#tw_language").val();
	if(!FbAppId) {
		jQuery("#twitter-page-id-fetch").focus();
		return false;
	}
	jQuery("#fb-save-settings").hide();
	jQuery("#fb-img").show();
	jQuery.ajax({
		url: location.href,
		type: "POST",
		data: jQuery("form#fb-form").serialize(),
		dataType: "html",
		//Do not cache the page
		cache: false,
		//success
		success: function (html) {
			jQuery("#fb-img").hide();
			jQuery("#fb-msg").show();
			setTimeout(function() {location.reload(true);}, 2000);
		}
	});
}
</script>
<?php
wp_enqueue_style('op-bootstrap-css', WEBLIZAR_TWITTER_PLUGIN_URL. 'css/bootstrap.min.css');
if(isset($_REQUEST['twitter-page-user_name'])) {
 $TwitterUserName = sanitize_text_field( $_REQUEST['twitter-page-user_name'] );
 $Theme = sanitize_text_field( $_REQUEST['show-theme-background'] );
 $Height = sanitize_text_field( $_REQUEST['twitter-page-url-Height'] );
 $TwitterWidgetId = sanitize_text_field( $_REQUEST['twitter-page-id-fetch'] );
 $LinkColor = sanitize_text_field( $_REQUEST['twitter-page-lnk-Color'] );
 $ExcludeReplies = sanitize_option ( 'ExcludeReplies', $_REQUEST['exclude_replies_23'] );
 $AutoExpandPhotos = sanitize_option ( 'AutoExpandPhotos', $_REQUEST['photo_1234'] );
 $tw_language = sanitize_option ( 'Language', $_REQUEST['tw_language'] );


	$TwitterSettingsArray = serialize(
	array(
		'TwitterUserName' => $TwitterUserName,
		'Theme' => $Theme,
		'Height' => $Height,
		'TwitterWidgetId' => $TwitterWidgetId,
		'LinkColor' => $LinkColor,
		'ExcludeReplies' => $ExcludeReplies,
		'AutoExpandPhotos' => $AutoExpandPhotos,
		'tw_language' => $tw_language,
	));
	update_option("ali_twitter_shortcode", $TwitterSettingsArray);
} ?>
<div class="block ui-tabs-panel active" id="option-general">		
	<div class="row">
		<div class="col-md-6">
			<h2><?php _e( 'Twitter Shortcode Settings', "WEBLIZAR_TWITTER_TEXT_DOMAIN"); ?>: [TWTR]</h2>
			<hr>
			<form name='fb-form' id='fb-form'>
				<?php
					$twitterSettings =  unserialize(get_option("ali_twitter_shortcode"));
					$TwitterUserName = "weblizar";
					if( isset($twitterSettings[ 'TwitterUserName' ] ) )  {
						$TwitterUserName = $twitterSettings[ 'TwitterUserName' ];
					}
					$TwitterWidgetId = "123";
					if ( isset($twitterSettings[ 'TwitterWidgetId' ] ) ) {
						$TwitterWidgetId = $twitterSettings[ 'TwitterWidgetId' ];
					}
					$Theme = "light";
					if (isset( $twitterSettings[ 'Theme' ] ) ) {
						$Theme = $twitterSettings[ 'Theme' ];
					}
					$Height = "450";
					if ( isset($twitterSettings[ 'Height' ] ) ) {
						$Height = $twitterSettings[ 'Height' ];
					}
					$Width = "";
					if ( isset($twitterSettings[ 'Width' ] ) ) {
					$Width = $twitterSettings[ 'Width' ];
					}
					$LinkColor = "#CC0000";
					if ( isset( $twitterSettings[ 'LinkColor' ] ) ) {
						$LinkColor = $twitterSettings[ 'LinkColor' ];
					}
					$ExcludeReplies = "yes";
					if ( isset( $twitterSettings[ 'ExcludeReplies' ] ) )  {
						$ExcludeReplies = $twitterSettings['ExcludeReplies' ];
					}
					$AutoExpandPhotos = "yes";
					if ( isset( $twitterSettings[ 'AutoExpandPhotos' ] ) ) {
						$AutoExpandPhotos = $twitterSettings[ 'AutoExpandPhotos' ];
					}
					$tw_language = "";
					if ( isset( $twitterSettings[ 'tw_language' ] ) ) {
						$tw_language = $twitterSettings[ 'tw_language' ];
					}
				?>
				<p>
					<label><?php _e( 'Twitter Account Username',"WEBLIZAR_TWITTER_TEXT_DOMAIN"); ?></label>
					<input class="widefat" id="twitter-page-user-name" name="twitter-page-user_name" type="text" value="<?php echo esc_attr($TwitterUserName); ?>" placeholder="<?php _e( 'Enter Your Twitter Account Username',"WEBLIZAR_TWITTER_TEXT_DOMAIN"); ?>">
				</p>
				<br>
				<p>
					<input class="widefat" id="twitter-page-id-fetch" name="twitter-page-id-fetch" type="hidden" value="<?php echo esc_attr( $TwitterWidgetId); ?>" placeholder="<?php _e( 'Enter Your Twitter Widget ID',"WEBLIZAR_TWITTER_TEXT_DOMAIN"); ?>">
				</p>
				<p>
					<label><?php _e( 'Theme',"WEBLIZAR_TWITTER_TEXT_DOMAIN"); ?></label>
					<select id="show-theme-background" name="show-theme-background">
						<option value="light" <?php if($Theme == "light") echo "selected=selected" ?>>Light</option>
						<option value="dark" <?php if($Theme == "dark") echo "selected=selected" ?>>Dark</option>
					</select>
				</p>
				<br>
				
				<p>
					<label><?php _e( 'Height',"WEBLIZAR_TWITTER_TEXT_DOMAIN"); ?></label>
					<input class="widefat" id="twitter-page-url-Height" name="twitter-page-url-Height" type="text" value="<?php echo esc_attr($Height ); ?>">
				</p>
				<br>
				
				<p>
					<label><?php _e( 'URL Link Color:',"WEBLIZAR_TWITTER_TEXT_DOMAIN"); ?></label>
					<input class="widefat" id="twitter-page-lnk-Color" name="twitter-page-lnk-Color" type="text" value="<?php echo esc_attr( $LinkColor ); ?>" >
					Find More Color Codes <a href="http://html-color-codes.info/" target="_blank">HERE</a>
				</p>
				<br>
				
				<p>
					<label><?php _e( 'Exclude Replies on Tweets',"WEBLIZAR_TWITTER_TEXT_DOMAIN"); ?></label>
					<select id="exclude_replies_23" name="exclude_replies_23">
						<option value="yes" <?php if($ExcludeReplies == "yes") echo "selected=selected" ?>>Yes</option>
						<option value="no" <?php if($ExcludeReplies == "no") echo "selected=selected" ?>>No</option>
					</select>
				</p>
				<br>
				<p>
					<label><?php _e( 'Auto Expand Photos in Tweets',"WEBLIZAR_TWITTER_TEXT_DOMAIN"); ?></label>
					<select id="photo_1234" name="photo_1234">
						<option value="yes" <?php if($AutoExpandPhotos == "yes") echo "selected=selected" ?>>Yes</option>
						<option value="no" <?php if($AutoExpandPhotos == "no") echo "selected=selected" ?>>No</option>
					</select>
				</p>
				<br>
				
				<p>
					<label><?php _e( 'Select Language',"WEBLIZAR_TWITTER_TEXT_DOMAIN"); ?></label>
					<select id="tw_language" name="tw_language">
						<option value=""<?php if($tw_language == "") echo "selected=selected" ?>>Automatic</option>
						<option value="en"<?php if($tw_language == "en") echo "selected=selected" ?>>English (default)</option>
						<option value="ar"<?php if($tw_language == "ar") echo "selected=selected" ?>>Arabic</option>
						<option value="bn"<?php if($tw_language == "bn") echo "selected=selected" ?>>Bengali</option>
						<option value="cs"<?php if($tw_language == "cs") echo "selected=selected" ?>>Czech</option>
						<option value="da"<?php if($tw_language == "da") echo "selected=selected" ?>>Danish</option>
						<option value="de"<?php if($tw_language == "de") echo "selected=selected" ?>>German</option>
						<option value="el"<?php if($tw_language == "el") echo "selected=selected" ?>>Greek</option>
						<option value="es"<?php if($tw_language == "es") echo "selected=selected" ?>>Spanish</option>
						<option value="fa"<?php if($tw_language == "fa") echo "selected=selected" ?>>Persian</option>
						<option value="fi"<?php if($tw_language == "fi") echo "selected=selected" ?>>Finnish</option>
						<option value="fil"<?php if($tw_language == "fil") echo "selected=selected" ?>>Filipino</option>
						<option value="fr"<?php if($tw_language == "fr") echo "selected=selected" ?>>French</option>
						<option value="he"<?php if($tw_language == "he") echo "selected=selected" ?>>Hebrew</option>
						<option value="hi"<?php if($tw_language == "hi") echo "selected=selected" ?>>Hindi</option>
						<option value="hu"<?php if($tw_language == "hu") echo "selected=selected" ?>>Hungarian</option>
						<option value="id"<?php if($tw_language == "id") echo "selected=selected" ?>>Indonesian</option>
						<option value="it"<?php if($tw_language == "it") echo "selected=selected" ?>>Italian</option>
						<option value="ja"<?php if($tw_language == "ja") echo "selected=selected" ?>>Japanese</option>
						<option value="ko"<?php if($tw_language == "ko") echo "selected=selected" ?>>Korean</option>
						<option value="msa"<?php if($tw_language == "msa") echo "selected=selected" ?>>Malay</option>
						<option value="nl"<?php if($tw_language == "nl") echo "selected=selected" ?>>Dutch</option>
						<option value="no"<?php if($tw_language == "no") echo "selected=selected" ?>>Norwegian</option>
						<option value="pl"<?php if($tw_language == "pl") echo "selected=selected" ?>>Polish</option>
						<option value="pt"<?php if($tw_language == "pt") echo "selected=selected" ?>>Portuguese</option>
						<option value="ro"<?php if($tw_language == "ro") echo "selected=selected" ?>>Romanian</option>
						<option value="ru"<?php if($tw_language == "ru") echo "selected=selected" ?>>Russian</option>
						<option value="sv"<?php if($tw_language == "sv") echo "selected=selected" ?>>Swedish</option>
						<option value="th"<?php if($tw_language == "th") echo "selected=selected" ?>>Thai</option>
						<option value="tr"<?php if($tw_language == "tr") echo "selected=selected" ?>>Turkish</option>
						<option value="uk<?php if($tw_language == "uk") echo "selected=selected" ?>">Ukrainian</option>
						<option value="ur"<?php if($tw_language == "ur") echo "selected=selected" ?>>Urdu</option>
						<option value="vi"<?php if($tw_language == "vi") echo "selected=selected" ?>>Vietnamese</option>
						<option value="zh-cn"<?php if($tw_language == "zh-cn") echo "selected=selected" ?>>Chinese (Simplified)</option>
						<option value="zh-tw"<?php if($tw_language == "zh-tw") echo "selected=selected" ?>>Chinese (Traditional)</option>
					</select>
				</p>
				<br>
				
				<input onclick="return SaveSettings();" type="button" class="btn btn-primary btn-lg" id="fb-save-settings" name="fb-save-settings" value="SAVE">
			
				<div id="fb-img" style="display: none;">
					<img src="<?php echo WEBLIZAR_TWITTER_PLUGIN_URL.'images/loading.gif'; ?>" />
				</div>
				<div id="fb-msg" style="display: none;" class"alert">
					<?php _e( 'Settings successfully saved. Reloading page for generating preview right side of setting.', "WEBLIZAR_TWITTER_TEXT_DOMAIN" ); ?> 
				</div>		
			</form>
			
		</div>
		<!-- Preview Part-->
		<div class="col-md-6">
			<?php if($TwitterWidgetId) { ?>
			<h2>Twitter Shortcode <?php _e( 'Preview', "WEBLIZAR_TWITTER_TEXT_DOMAIN"); ?></h2>
			<hr>
			<p>
		<a class="twitter-timeline" data-dnt="true" href="https://twitter.com/<?php echo esc_attr($TwitterUserName); ?>" 
		min-width="<?php echo esc_attr($Width); ?>" 
		height="<?php echo esc_attr($Height); ?>" 
		data-theme="<?php echo esc_attr($Theme); ?>" 
		data-lang="<?php echo esc_attr($tw_language); ?>"
		data-link-color="<?php echo esc_attr($LinkColor); ?>"></a>
				<script>
				!function(d,s,id) {
					var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}
				} (document,"script","twitter-wjs");
				</script>
			</p>
			<?php }?>
		</div>
    </div>
</div>

<!---------------- need help tab------------------------>
<div class="block ui-tabs-panel deactive" id="option-needhelp">		
	<div class="row">
		<div class="col-md-10">
			<div id="heading">
				<h2>Twitter Tweet Widget & Shortcode Help Section</h2>
			</div>
			<p>Twitter Tweet By Weblizar plugin comes with 2 major feature.</p>
			<br>
			<p><strong>1 - Twitter Tweets Widget</strong></p>
			<p><strong>2 - Twitter Tweets Shoertcode [TWTR]</strong></p>
			<br><br>
			<p><strong>Twitter Tweets Widget</strong></p>
			<hr>
			<p>You can use the widget to display your Twitter Tweets in any theme Widget Sections.</p>
			<p>Simple go to your <a href="<?php echo get_site_url(); ?>/wp-admin/widgets.php"><strong>Widgets</strong></a> section and activate available <strong>"Twitter By Weblizar"</strong> widget in any sidebar section, like in left sidebar, right sidebar or footer sidebar.</p>
			<br><br>
			<p><strong>Twitter Tweets Shoertcode [TWTR]</strong></p>
			<hr>
			<p><strong>[TWTR]</strong> shortcode give ability to display Twitter Tweets Box in any Page / Post with content.</p>
			<p>To use shortcode, just copy <strong>[TWTR]</strong> shortcode and paste into content editor of any Page / Post.</p>
		
			<br><br>
			<p><strong>Q. What is Twitter Widget ID?</strong></p>
			<p><strong>Ans. Twitter Widget ID</strong> used to authenticate your TWITTER
			Page data & settings. To get your own TWITTER ID please read our very simple and easy <a href="https://weblizar.com/get-twitter-widget-id/" target="_blank"><strong>Tutorial.</p>
		</div>
	</div>
</div>

<!-- Recommendation ---->
<!---------------- our product tab------------------------>
<div class="block ui-tabs-panel deactive" id="option-recommendation">
	<!-- Dashboard Settings panel content --- >
<!----------------------------------------> 

<div class="row">
	<div class="panel panel-primary panel-default content-panel">
		<div class="panel-body">
			<table class="form-table2">
				<tr class="radio-span" style="border-bottom:none;">
					<td>
	<?php
	include( ABSPATH . "wp-admin/includes/plugin-install.php" );
	global $tabs, $tab, $paged, $type, $term;
	$tabs = array();
	$tab = "search";
	$per_page = 20;
	$args = array
	(
		"author"=> "weblizar",
		"page" => $paged,
		"per_page" => $per_page,
		"fields" => array( "last_updated" => true, "downloaded" => true, "icons" => true ),
		"locale" => get_locale(),
	);
	$arges = apply_filters( "install_plugins_table_api_args_$tab", $args );
	$api = plugins_api( "query_plugins", $arges );
	$item = $api->plugins;
	if(!function_exists("wp_star_rating"))
	{
		function wp_star_rating( $args = array() )
		{
			$defaults = array(
					'rating' => 0,
					'type' => 'rating',
					'number' => 0,
			);
			$r = wp_parse_args( $args, $defaults );
	
			// Non-english decimal places when the $rating is coming from a string
			$rating = str_replace( ',', '.', $r['rating'] );
	
			// Convert Percentage to star rating, 0..5 in .5 increments
			if ( 'percent' == $r['type'] ) {
				$rating = round( $rating / 10, 0 ) / 2;
			}
	
			// Calculate the number of each type of star needed
			$full_stars = floor( $rating );
			$half_stars = ceil( $rating - $full_stars );
			$empty_stars = 5 - $full_stars - $half_stars;
	
			if ( $r['number'] ) {
				/* translators: 1: The rating, 2: The number of ratings */
				$format = _n( '%1$s rating based on %2$s rating', '%1$s rating based on %2$s ratings', $r['number'] );
				$title = sprintf( $format, number_format_i18n( $rating, 1 ), number_format_i18n( $r['number'] ) );
			} else {
				/* translators: 1: The rating */
				$title = sprintf( __( '%s rating' ), number_format_i18n( $rating, 1 ) );
			}
	
			echo '<div class="star-rating" title="' . esc_attr( $title ) . '">';
			echo '<span class="screen-reader-text">' . $title . '</span>';
			echo str_repeat( '<div class="star star-full"></div>', $full_stars );
			echo str_repeat( '<div class="star star-half"></div>', $half_stars );
			echo str_repeat( '<div class="star star-empty"></div>', $empty_stars);
			echo '</div>';
		}
	}
	?>
	<form id="frmrecommendation" class="layout-form">
		<div id="poststuff" style="width: 99% !important;">
			<div id="post-body" class="metabox-holder">
				<div id="postbox-container-2" class="postbox-container">
					<div id="advanced" class="meta-box-sortables">
						<div id="gallery_bank_get_started" class="postbox" >
							<div class="handlediv" data-target="ux_recommendation" title="Click to toggle" data-toggle="collapse"><br></div>
							<h2 class="hndle"><span>Get More Free WordPress Plugins From Weblizar</span></h3>
							<div class="inside">
								<div id="ux_recommendation" class="gallery_bank_layout">
									
									<div class="separator-doubled"></div>
									<div class="fluid-layout">
										<div class="layout-span12">
											<div class="wp-list-table plugin-install">
												<div id="the-list">
													<?php 
													foreach ((array) $item as $plugin) 
													{
														if (is_object( $plugin))
														{
															$plugin = (array) $plugin;
															
														}
														if (!empty($plugin["icons"]["svg"]))
														{
															$plugin_icon_url = $plugin["icons"]["svg"];
														} 
														elseif (!empty( $plugin["icons"]["2x"])) 
														{
															$plugin_icon_url = $plugin["icons"]["2x"];
														} 
														elseif (!empty( $plugin["icons"]["1x"]))
														{
															$plugin_icon_url = $plugin["icons"]["1x"];
														} 
														else 
														{
															$plugin_icon_url = $plugin["icons"]["default"];
														}
														$plugins_allowedtags = array
														(
															"a" => array( "href" => array(),"title" => array(), "target" => array() ),
															"abbr" => array( "title" => array() ),"acronym" => array( "title" => array() ),
															"code" => array(), "pre" => array(), "em" => array(),"strong" => array(),
															"ul" => array(), "ol" => array(), "li" => array(), "p" => array(), "br" => array()
														);
														$title = wp_kses($plugin["name"], $plugins_allowedtags);
														$description = strip_tags($plugin["short_description"]);
														$author = wp_kses($plugin["author"], $plugins_allowedtags);
														$version = wp_kses($plugin["version"], $plugins_allowedtags);
														$name = strip_tags( $title . " " . $version );
														$details_link   = self_admin_url( "plugin-install.php?tab=plugin-information&amp;plugin=" . $plugin["slug"] .
														"&amp;TB_iframe=true&amp;width=600&amp;height=550" );
														
														/* translators: 1: Plugin name and version. */
														$action_links[] = '<a href="' . esc_url( $details_link ) . '" class="thickbox" aria-label="' . esc_attr( sprintf("More information about %s", $name ) ) . '" data-title="' . esc_attr( $name ) . '">' . __( 'More Details' ) . '</a>';
														$action_links = array();
														if (current_user_can( "install_plugins") || current_user_can("update_plugins"))
														{
															$status = install_plugin_install_status( $plugin );
															switch ($status["status"])
															{
																case "install":
																	if ( $status["url"] )
																	{
																		/* translators: 1: Plugin name and version. */
																		$action_links[] = '<a class="install-now button" href="' . $status['url'] . '" aria-label="' . esc_attr( sprintf("Install %s now", $name ) ) . '">' . __( 'Install Now' ) . '</a>';
																	}
																break;
																case "update_available":
																	if ($status["url"])
																	{
																		/* translators: 1: Plugin name and version */
																		$action_links[] = '<a class="button" href="' . $status['url'] . '" aria-label="' . esc_attr( sprintf( "Update %s now", $name ) ) . '">' . __( 'Update Now' ) . '</a>';
																	}
																break;
																case "latest_installed":
																case "newer_installed":
																	$action_links[] = '<span class="button button-disabled" title="' . esc_attr__( "This plugin is already installed and is up to date" ) . ' ">' . _x( 'Installed', 'plugin' ) . '</span>';
																break;
															}
														}
														?>
														<div class="plugin-div plugin-div-settings">
															<div class="plugin-div-top plugin-div-settings-top">
																<div class="plugin-div-inner-content">
																	<a href="<?php echo esc_url( $details_link ); ?>" class="thickbox plugin-icon plugin-icon-custom">
																		<img class="custom_icon" src="<?php echo esc_attr( $plugin_icon_url ) ?>" />
																	</a>
																	<div class="name column-name">
																		<h4>
																			<a href="<?php echo esc_url( $details_link ); ?>" class="thickbox"><?php echo $title; ?></a>
																		</h4>
																	</div>
																	<div class="desc column-description">
																		<p>
																			<?php echo $description; ?>
																		</p>
																		<p class="authors">
																			<cite>
																				By <?php echo $author;?>
																			</cite>
																		</p>
																	</div>
																</div>
																<div class="action-links">
																	<ul class="plugin-action-buttons-custom">
																		<li>
																			<?php
																				if ($action_links) {
																					echo implode("</li><li>", $action_links);
																				}
																					
																				switch($plugin["slug"]) {
																					case "gallery-bank" :
																						?>
																							<a class="plugin-div-button install-now button" href="http://tech-banker.com/products/wp-gallery-bank/pricing/" target="_blank" >
																								<?php _e("Premium Editions", WEBLIZAR_ACL); ?>
																							</a>
																							<a class="plugin-div-button install-now button" href="http://tech-banker.com/products/wp-gallery-bank/" target="_blank" >
																								<?php _e("Visit Website", WEBLIZAR_ACL); ?>
																							</a>
																						<?php
																					break;
																					case "contact-bank" :
																						?>
																							<a class="plugin-div-button install-now button" href="http://tech-banker.com/products/wp-contact-bank/pricing/" target="_blank" >
																								<?php _e("Premium Editions", WEBLIZAR_ACL); ?>
																							</a>
																							<a class="plugin-div-button install-now button" href="http://tech-banker.com/products/wp-contact-bank/" target="_blank" >
																								<?php _e("Visit Website", WEBLIZAR_ACL); ?>
																							</a>
																						<?php
																					break;
																					case "captcha-bank" :
																						?>
																							<a class="plugin-div-button install-now button" href="http://tech-banker.com/products/wp-captcha-bank/pricing/" target="_blank" >
																								<?php _e("Premium Editions", WEBLIZAR_ACL); ?>
																							</a>
																							<a class="plugin-div-button install-now button" href="http://tech-banker.com/products/wp-captcha-bank/" target="_blank" >
																								<?php _e("Visit Website", WEBLIZAR_ACL); ?>
																							</a>
																						<?php 
																					break;
																					case "wp-clean-up-optimizer" :
																						?>
																							<a class="plugin-div-button install-now button" href="http://tech-banker.com/products/wp-clean-up-optimizer/pricing/" target="_blank" >
																								<?php _e("Premium Editions", WEBLIZAR_ACL); ?>
																							</a>
																							<a class="plugin-div-button install-now button" href="http://tech-banker.com/products/wp-clean-up-optimizer/" target="_blank" >
																								<?php _e("Visit Website", WEBLIZAR_ACL); ?>
																							</a>
																						<?php 
																					break;
																					case "google-maps-bank":
																						?>
																							<a class="plugin-div-button install-now button" href="http://tech-banker.com/products/wp-google-maps-bank/pricing/" target="_blank" >
																								<?php _e("Premium Editions", WEBLIZAR_ACL); ?>
																							</a>
																							<a class="plugin-div-button install-now button" href="http://tech-banker.com/products/wp-google-maps-bank/" target="_blank" >
																								<?php _e("Visit Website", WEBLIZAR_ACL); ?>
																							</a>
																						<?php
																					break;
																					case "wp-backup-bank":
																						?>
																							<a class="plugin-div-button install-now button" href="http://tech-banker.com/products/wp-backup-bank/pricing/" target="_blank" >
																								<?php _e("Premium Editions", WEBLIZAR_ACL); ?>
																							</a>
																							<a class="plugin-div-button install-now button" href="http://tech-banker.com/products/wp-backup-bank/" target="_blank" >
																								<?php _e("Visit Website", WEBLIZAR_ACL); ?>
																							</a>
																						<?php
																					break;
																				}
																			?>
																		</li>
																	</ul>
																</div>
															</div>
															<div class="plugin-card-bottom plugin-card-bottom_settings">
																<div class="vers column-rating">
																	<?php wp_star_rating( array( "rating" => $plugin["rating"], "type" => "percent", "number" => $plugin["num_ratings"] ) ); ?>
																	<span class="num-ratings">
																		(<?php echo number_format_i18n( $plugin["num_ratings"] ); ?>)
																	</span>
																</div>
																<div class="column-updated">
																	<strong><?php _e("Last Updated:"); ?></strong> <span title="<?php echo esc_attr($plugin["last_updated"]); ?>">
																		<?php printf("%s ago", human_time_diff(strtotime($plugin["last_updated"]))); ?>
																	</span>
																</div>
																<div class="column-downloaded">
																	<?php echo sprintf( _n("%s download", "%s downloads", $plugin["downloaded"]), number_format_i18n($plugin["downloaded"])); ?>
																</div>
																<div class="column-compatibility">
																	<?php
																	if ( !empty($plugin["tested"]) && version_compare(substr($GLOBALS["wp_version"], 0, strlen($plugin["tested"])), $plugin["tested"], ">"))
																	{
																		echo '<span class="compatibility-untested">' . __( "<strong>Untested</strong> with your version of WordPress" ) . '</span>';
																	} 
																	elseif (!empty($plugin["requires"]) && version_compare(substr($GLOBALS["wp_version"], 0, strlen($plugin["requires"])), $plugin["requires"], "<")) 
																	{
																		echo '<span class="compatibility-incompatible">' . __("Incompatible with your version of WordPress") . '</span>';
																	} 
																	else
																	{
																		echo '<span class="compatibility-compatible">' . __("Compatible with your version of WordPress") . '</span>';
																	}
																	?>
																</div>
															</div>
														</div>
													<?php
													}
													?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
				</td>
			</tr>
		</table>
	</div>
</div>
	
   
	
</div>
<!-- /row -->

</div>


<!---------------- our product tab------------------------>
<div class="block ui-tabs-panel deactive" id="option-ourproduct">
	<div class="row-fluid pricing-table pricing-three-column">
		<div class="plan-name centre"> 
			<a href="http://weblizar.com" target="_new" style="margin-bottom:10px;textt-align:center"><img src="http://weblizar.com/wp-content/themes/home-theme/images/weblizar2.png"></a>
		</div>	
		<div class="plan-name">
			<h2>Weblizar Responsive WordPress Theme</h2>
			<h6>Get The Premium Products & Create Your Website Beautifully.  </h6>
		</div>
		<div class="section container">
			<div class="col-lg-6">
				<h2>Premium Themes </h2>
				<br>
				<ol id="weblizar_product">
					<li><a href="http://weblizar.com/themes/enigma-premium/">Enigma </a> </li>
					<li><a href="http://weblizar.com/themes/weblizar-premium-theme/">Weblizar </a></li>					
					<li><a href="http://weblizar.com/themes/guardian-premium-theme/">Guardian </a></li>
					<li><a href="http://weblizar.com/plugins/green-lantern-premium-theme/">Green Lantern</a> </li>
					<li><a href="https://weblizar.com/themes/creative-premium-theme/">Creative </a> </li>
					<li><a href="https://weblizar.com/themes/incredible-premium-theme/">Incredible </a></li>
				</ol>
			</div>
			<div class="col-lg-6">
				<h2>Pro Plugins</h2>
				<br>
				<ol id="weblizar_product">
					<li><a href="http://weblizar.com/plugins/responsive-photo-gallery-pro/">Responsive Photo Gallery</a></li>
					<li><a href="http://weblizar.com/plugins/ultimate-responsive-image-slider-pro/">Ultimate Responsive Image Slider</a></li>
					<li><a href="http://weblizar.com/plugins/responsive-portfolio-pro/">Responsive Portfolio</a></li>
					<li><a href="http://weblizar.com/plugins/photo-video-link-gallery-pro//">Photo Video Link Gallery</a></li>
					<li><a href="http://weblizar.com/plugins/lightbox-slider-pro/">Lightbox Slider</a></li>
					<li><a href="http://weblizar.com/plugins/flickr-album-gallery-pro/">Flickr Album Gallery</a></li>
					<li><a href="https://weblizar.com/plugins/instagram-shortcode-and-widget-pro/">Instagram Shortcode &amp; Widget</a></li>
					<li><a href="https://weblizar.com/plugins/instagram-gallery-pro/">Instagram Gallery</a></li>
					<li><a href="https://weblizar.com/plugins/gallery-pro/">Gallery Pro</a></li>
				</ol>
			</div>
		</div>	
	</div>
</div>