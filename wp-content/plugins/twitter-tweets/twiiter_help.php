<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<script type="text/javascript">
function SaveSettings() {
	var FbAppId 	 = jQuery("#twitter-page-id-fetch").val();
    var User_name_3  = jQuery("#twitter-page-user-name").val();
	var show_theme   = jQuery("#show-theme-background").val();
	var Height 		 = jQuery("#twitter-page-url-Height").val();
	var link_color   = jQuery("#twitter-page-lnk-Color").val();
	var replieses 	 = jQuery("#exclude_replies_23").val();
	var photos_acces = jQuery("#photo_1234").val();
	var tw_language  = jQuery("#tw_language").val();
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

function SaveApiSettings() {
 var wl_twitter_consumer_key 	= jQuery("#wl_twitter_consumer_key").val();
 var wl_twitter_consumer_secret = jQuery("#wl_twitter_consumer_secret").val();;
 var wl_twitter_access_token 	= jQuery("#wl_twitter_access_token").val();;
 var wl_twitter_token_secret 	= jQuery("#wl_twitter_token_secret").val();;
 if( ! wl_twitter_consumer_key ) {
	jQuery("#wl_twitter_consumer_key").focus();
	return false;
 }

 if( ! wl_twitter_consumer_secret ) {
	jQuery("#wl_twitter_consumer_secret").focus();
	return false;
 }

 if( ! wl_twitter_access_token ) {
	jQuery("#wl_twitter_access_token").focus();
	return false;
 }

 if( ! wl_twitter_token_secret ) {
	jQuery("#wl_twitter_token_secret").focus();
	return false;
 }
jQuery("#fb-api-save-settings").hide();
jQuery("#twitter-img").show();
 jQuery.ajax({
		url: location.href,
		type: "POST",
		data: jQuery("form#api-form").serialize(),
		dataType: "html",
		//Do not cache the page
		cache: false,
		//success
		success: function (html) {
			jQuery("#twitter-img").hide();
			jQuery("#wl_twitter_preview").show();
			setTimeout(function() {location.reload(true);}, 2000);
		}
	});
}
</script>
<?php
wp_enqueue_style('font-awesome', WEBLIZAR_TWITTER_PLUGIN_URL. 'css/all.min.css');
wp_enqueue_style('wl_bootstrap', WEBLIZAR_TWITTER_PLUGIN_URL. 'css/bootstrap.min.css');

if(isset($_REQUEST['twitter-page-user_name'])) {

	if ( ! current_user_can( 'manage_options' ) || ! wp_verify_nonce( $_POST['feeds-widget'], 'save-feeds-widget' ) ) {
		die;
	}

	$TwitterUserName  = sanitize_text_field( $_REQUEST['twitter-page-user_name'] );
	$Theme 		   = sanitize_text_field( $_REQUEST['show-theme-background'] );
	$Height 		   = sanitize_text_field( $_REQUEST['twitter-page-url-Height'] );
	$TwitterWidgetId  = sanitize_text_field( $_REQUEST['twitter-page-id-fetch'] );
	$LinkColor 	   = sanitize_hex_color( $_REQUEST['twitter-page-lnk-Color'] );
	$ExcludeReplies   = sanitize_text_field ( $_REQUEST['exclude_replies_23'] );
	$AutoExpandPhotos = sanitize_text_field ( $_REQUEST['photo_1234'] );
	$tw_language 	   = sanitize_text_field ( $_REQUEST['tw_language'] );

	$TwitterSettingsArray = serialize(
	array(
		'TwitterUserName'  => $TwitterUserName,
		'Theme' 	       => $Theme,
		'Height' 		   => $Height,
		'TwitterWidgetId'  => $TwitterWidgetId,
		'LinkColor' 	   => $LinkColor,
		'ExcludeReplies'   => $ExcludeReplies,
		'AutoExpandPhotos' => $AutoExpandPhotos,
		'tw_language' 	   => $tw_language,
	));
	update_option("ali_twitter_shortcode", $TwitterSettingsArray);
}

/* Twitter api key save */

if ( isset( $_REQUEST['wl_twitter_consumer_key'] ) && isset( $_REQUEST['twitter_api_nonce'] ) ) {

	if ( ! current_user_can( 'manage_options' ) || ! wp_verify_nonce( $_POST['twitter_api_nonce'], 'twitter_api_nonce' ) ) {
		die;
	}

	$wl_twitter_consumer_key 	= sanitize_text_field( $_REQUEST['wl_twitter_consumer_key'] );
	$wl_twitter_consumer_secret = sanitize_text_field( $_REQUEST['wl_twitter_consumer_secret'] );
	$wl_twitter_access_token 	= sanitize_text_field( $_REQUEST['wl_twitter_access_token'] );
	$wl_twitter_token_secret 	= sanitize_text_field( $_REQUEST['wl_twitter_token_secret'] );

	$wl_twitter_tweets = ( isset( $_REQUEST['wl_twitter_tweets'] ) ) ? sanitize_text_field( $_REQUEST['wl_twitter_tweets'] ) : '4';

	$wl_twitter_layout = ( isset( $_REQUEST['wl_twitter_layout'] ) ) ? sanitize_text_field( $_REQUEST['wl_twitter_layout'] ) : '3';

	$twitter_api_settings = array(
		'wl_twitter_consumer_key' 	 => $wl_twitter_consumer_key,
		'wl_twitter_consumer_secret' => $wl_twitter_consumer_secret,
		'wl_twitter_access_token' 	 => $wl_twitter_access_token,
		'wl_twitter_token_secret' 	 => $wl_twitter_token_secret,
		'wl_twitter_tweets'			 => $wl_twitter_tweets,
		'wl_twitter_layout'			 => $wl_twitter_layout
	);

	update_option( 'wl_twitter_api_settings', $twitter_api_settings );
}
?>
<div class="block ui-tabs-panel active" id="option-general">
	<div class="row">
		<div class="col-md-6">
			<h2 class="well"><?php esc_html_e( 'Customize feeds shortcode Settings', 'twitter-tweets'); ?></h2>
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

					wp_nonce_field( 'save-feeds-widget', 'feeds-widget' );
				?>
				<p>
					<div class="container">
					  <div class="row">
					    <div class="col-sm">
					      <label><?php esc_html_e( 'Twitter Account Username', 'twitter-tweets' ); ?></label>
					    </div>
					    <div class="col-sm">
					     <input class="widefat" id="twitter-page-user-name" name="twitter-page-user_name" type="text" value="<?php echo esc_attr($TwitterUserName); ?>" placeholder="<?php esc_attr_e( 'Enter Your Twitter Account Username', 'twitter-tweets' ); ?>">
					    </div>
					  </div>
					</div>
				</p>
				<br>
				<p>
					<input class="widefat" id="twitter-page-id-fetch" name="twitter-page-id-fetch" type="hidden" value="<?php echo esc_attr( $TwitterWidgetId); ?>" placeholder="<?php esc_html_e( 'Enter Your Twitter Widget ID', 'twitter-tweets'); ?>">
				</p>
				<p>
				<div class="container">
				  <div class="row">
				    <div class="col-sm">
				     <label><?php esc_html_e( 'Theme', 'twitter-tweets' ); ?></label>
				    </div>
				    <div class="col-sm">
				      <select id="show-theme-background" name="show-theme-background">
					<option value="light" <?php if($Theme == "light") echo "selected=selected" ?>><?php esc_html_e( 'Light', 'twitter-tweets' ); ?></option>
					<option value="dark" <?php if($Theme == "dark") echo "selected=selected" ?>><?php esc_html_e( 'Dark', 'twitter-tweets' ); ?></option>
					</select>
				    </div>
				  </div>
				</div>
				</p>
				<br>

				<p><div class="container">
				  <div class="row">
				    <div class="col-sm">
				     <label><?php esc_html_e( 'Height', 'twitter-tweets' ); ?></label>
				    </div>
				    <div class="col-sm">
	 					<input class="widefat wltt-slider" id="twitter-page-url-Height" name="twitter-page-url-Height" type="range"  value="<?php echo esc_attr($Height ); ?>" min="0" max="1500" data-rangeSlider>
				<p><b><?php esc_html_e( 'Set your desire height px (Use Arrow Keys For Exact Numbers)', 'twitter-tweets' ); ?></b> <span id="twitter-range-val"></span></p>

				    </div>
				  </div>
				</div>


				</p>
				<!-- <br>		 -->

				<p>
					<div class="container">
					  <div class="row">
					    <div class="col-sm">
					      <label><?php esc_html_e( 'URL Link Color:', 'twitter-tweets' ); ?></label>
					    </div>
					    <div class="col-sm">

					     <input class=" color-field" id="twitter-page-lnk-Color" name="twitter-page-lnk-Color" type="text" value="<?php echo esc_attr( $LinkColor ); ?>" data-default-color="#effeff" >
					    </div>
					  </div>
					</div>


				</p>
				<br>

				<p><div class="container">
				  <div class="row">
				    <div class="col-sm">
				      <label><?php esc_html_e( 'Exclude Replies on Tweets', 'twitter-tweets' ); ?></label>
				    </div>
				    <div class="col-sm">
				     <select id="exclude_replies_23" name="exclude_replies_23">
						<option value="yes" <?php if($ExcludeReplies == "yes") echo "selected=selected" ?>><?php esc_html_e( 'Yes', 'twitter-tweets' ); ?></option>
						<option value="no" <?php if($ExcludeReplies == "no") echo "selected=selected" ?>><?php esc_html_e( 'No', 'twitter-tweets' ); ?></option>
					</select>
				    </div>
				  </div>
				</div>


				</p>
				<br>
				<p><div class="container">
				  <div class="row">
				    <div class="col-sm">
				      <label><?php esc_html_e( 'Auto Expand Photos in Tweets', 'twitter-tweets' ); ?></label>
				    </div>
				    <div class="col-sm">
				      <select id="photo_1234" name="photo_1234">
						<option value="yes" <?php if($AutoExpandPhotos == "yes") echo "selected=selected" ?>><?php esc_html_e( 'Yes', 'twitter-tweets' ); ?></option>
						<option value="no" <?php if($AutoExpandPhotos == "no") echo "selected=selected" ?>><?php esc_html_e( 'No', 'twitter-tweets' ); ?></option>
					</select>
				    </div>
				  </div>
				</div>


				</p>
				<br>
				<p><div class="container">
					  <div class="row">
					    <div class="col-sm">
					    <label><?php esc_html_e( 'Select Language', 'twitter-tweets' ); ?></label>
					    </div>
					    <div class="col-sm">
					      <select id="tw_language" name="tw_language">
						<option value=""<?php if($tw_language == "") echo "selected=selected" ?>><?php esc_html_e('Automatic', 'twitter-tweets'); ?></option>
						<option value="en"<?php if($tw_language == "en") echo "selected=selected" ?>><?php esc_html_e('English (default)', 'twitter-tweets'); ?></option>
						<option value="ar"<?php if($tw_language == "ar") echo "selected=selected" ?>><?php esc_html_e('Arabic', 'twitter-tweets'); ?></option>
						<option value="bn"<?php if($tw_language == "bn") echo "selected=selected" ?>><?php esc_html_e('Bengali', 'twitter-tweets'); ?></option>
						<option value="cs"<?php if($tw_language == "cs") echo "selected=selected" ?>><?php esc_html_e('Czech', 'twitter-tweets'); ?></option>
						<option value="da"<?php if($tw_language == "da") echo "selected=selected" ?>><?php esc_html_e('Danish', 'twitter-tweets'); ?></option>
						<option value="de"<?php if($tw_language == "de") echo "selected=selected" ?>><?php esc_html_e('German', 'twitter-tweets'); ?></option>
						<option value="el"<?php if($tw_language == "el") echo "selected=selected" ?>><?php esc_html_e('Greek', 'twitter-tweets'); ?></option>
						<option value="es"<?php if($tw_language == "es") echo "selected=selected" ?>><?php esc_html_e('Spanish', 'twitter-tweets'); ?></option>
						<option value="fa"<?php if($tw_language == "fa") echo "selected=selected" ?>><?php esc_html_e('Persian', 'twitter-tweets'); ?></option>
						<option value="fi"<?php if($tw_language == "fi") echo "selected=selected" ?>><?php esc_html_e('Finnish', 'twitter-tweets'); ?></option>
						<option value="fil"<?php if($tw_language == "fil") echo "selected=selected" ?>><?php esc_html_e('Filipino', 'twitter-tweets'); ?></option>
						<option value="fr"<?php if($tw_language == "fr") echo "selected=selected" ?>><?php esc_html_e('French', 'twitter-tweets'); ?></option>
						<option value="he"<?php if($tw_language == "he") echo "selected=selected" ?>><?php esc_html_e('Hebrew', 'twitter-tweets'); ?></option>
						<option value="hi"<?php if($tw_language == "hi") echo "selected=selected" ?>><?php esc_html_e('Hindi', 'twitter-tweets'); ?></option>
						<option value="hu"<?php if($tw_language == "hu") echo "selected=selected" ?>><?php esc_html_e('EHungarian', 'twitter-tweets'); ?></option>
						<option value="id"<?php if($tw_language == "id") echo "selected=selected" ?>><?php esc_html_e('Indonesian', 'twitter-tweets'); ?></option>
						<option value="it"<?php if($tw_language == "it") echo "selected=selected" ?>><?php esc_html_e('Italian', 'twitter-tweets'); ?></option>
						<option value="ja"<?php if($tw_language == "ja") echo "selected=selected" ?>><?php esc_html_e('Japanese', 'twitter-tweets'); ?></option>
						<option value="ko"<?php if($tw_language == "ko") echo "selected=selected" ?>><?php esc_html_e('Korean', 'twitter-tweets'); ?></option>
						<option value="msa"<?php if($tw_language == "msa") echo "selected=selected" ?>><?php esc_html_e('Malay', 'twitter-tweets'); ?></option>
						<option value="nl"<?php if($tw_language == "nl") echo "selected=selected" ?>><?php esc_html_e('Dutch)', 'twitter-tweets'); ?></option>
						<option value="no"<?php if($tw_language == "no") echo "selected=selected" ?>><?php esc_html_e('Norwegian', 'twitter-tweets'); ?></option>
						<option value="pl"<?php if($tw_language == "pl") echo "selected=selected" ?>><?php esc_html_e('Polish', 'twitter-tweets'); ?></option>
						<option value="pt"<?php if($tw_language == "pt") echo "selected=selected" ?>><?php esc_html_e('Portuguese', 'twitter-tweets'); ?></option>
						<option value="ro"<?php if($tw_language == "ro") echo "selected=selected" ?>><?php esc_html_e('Romanian', 'twitter-tweets'); ?></option>
						<option value="ru"<?php if($tw_language == "ru") echo "selected=selected" ?>><?php esc_html_e('Russian', 'twitter-tweets'); ?></option>
						<option value="sv"<?php if($tw_language == "sv") echo "selected=selected" ?>><?php esc_html_e('Swedish', 'twitter-tweets'); ?></option>
						<option value="th"<?php if($tw_language == "th") echo "selected=selected" ?>><?php esc_html_e('Thai', 'twitter-tweets'); ?></option>
						<option value="tr"<?php if($tw_language == "tr") echo "selected=selected" ?>><?php esc_html_e('Turkish', 'twitter-tweets'); ?></option>
						<option value="uk"<?php if($tw_language == "uk") echo "selected=selected" ?>><?php esc_html_e('Ukrainian', 'twitter-tweets'); ?></option>
						<option value="ur"<?php if($tw_language == "ur") echo "selected=selected" ?>><?php esc_html_e('Urdu', 'twitter-tweets'); ?></option>
						<option value="vi"<?php if($tw_language == "vi") echo "selected=selected" ?>><?php esc_html_e('Vietnamese', 'twitter-tweets'); ?></option>
						<option value="zh-cn"<?php if($tw_language == "zh-cn") echo "selected=selected" ?>><?php esc_html_e('Chinese (Simplified)', 'twitter-tweets'); ?></option>
						<option value="zh-tw"<?php if($tw_language == "zh-tw") echo "selected=selected" ?>><?php esc_html_e('Chinese', 'twitter-tweets'); ?> (Traditional)</option>
					</select>
					    </div>
					  </div>
					</div>


				</p>
				<br>

				<input onclick="return SaveSettings();" type="button" class="twt_save_btn" id="fb-save-settings" name="fb-save-settings" value="SAVE">

				<div id="fb-img" style="display: none;">
					<img src="<?php echo WEBLIZAR_TWITTER_PLUGIN_URL.'images/loading.gif'; ?>" />
				</div>
				<div id="fb-msg" style="display: none;" class="alert">
					<?php esc_html_e( 'Settings successfully saved. Reloading page for generating preview right side of setting.', 'twitter-tweets' ); ?>
				</div>
			</form>

		</div>
		<!-- Preview Part-->
		<div class="col-md-6">
			<?php if($TwitterWidgetId) { ?>
			<h2 class="well"><?php esc_html_e( 'Feeds Shortcode Preview', 'twitter-tweets'); ?></h2>
			<hr>
			<p>
		<a class="twitter-timeline" data-dnt="true" href="https://twitter.com/<?php echo esc_attr($TwitterUserName); ?>"
		min-width="<?php echo esc_attr($Width); ?>"
		height="<?php echo esc_attr($Height); ?>"
		data-theme="<?php echo esc_attr($Theme); ?>"
		data-lang="<?php echo esc_attr($tw_language); ?>"
		data-link-color="<?php echo esc_attr($LinkColor); ?>"></a>
		<div class="twt_help">
			<?php esc_html_e('Please copy the twitter shortcode', 'twitter-tweets' );?>  <span style="color:#000;"><b>[TWTR]</b> </span> <?php esc_html_e('and paste it to on the Page/Post', 'twitter-tweets' );?></span>
		</div>

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

<!-- API Key -->
<?php
	include_once('load-tweets.php');
?>
<div class="block ui-tabs-panel deactive" id="option-apikey">
	<div class="row">
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-12">
					<h2 class="well"><?php esc_html_e( 'Twitter API Setting', 'twitter-tweets'); ?></h2>
				</div>
				<div class="col-md-12">
					<form name='api-form' id='api-form'>
						<br>
						<p><div class="container">
							  <div class="row">
							    <div class="col-sm">
							      <label><?php esc_html_e( 'Consumer Key', 'twitter-tweets' ); ?>&nbsp;*</label>
							    </div>
							    <div class="col-sm">
							     <input class="widefat" id="wl_twitter_consumer_key" name="wl_twitter_consumer_key" type="text" value="<?php if( isset( $wl_twitter_consumer_key ) ) {echo esc_attr($wl_twitter_consumer_key);} ?>" placeholder="<?php esc_attr_e( 'Enter Your Twitter Consumer Key', 'twitter-tweets' ); ?>">
							     <span class="helplink"><?php esc_html_e("Visit this link to ",  'twitter-tweets' ); ?><a href="https://weblizar.com/blog/generate-twitter-api-key/" target="_bank"><?php esc_html_e("Generate Twitter API key", 'twitter-tweets' ); ?></a></span>
						<br>
							    </div>
							  </div>
							</div>


						</p>


						<br>
						<p><div class="container">
							  <div class="row">
							    <div class="col-sm">
							      <label><?php esc_html_e( 'Consumer Secret', 'twitter-tweets' ); ?>&nbsp;*</label>
							    </div>
							    <div class="col-sm">
							     <input class="widefat" id="wl_twitter_consumer_secret" name="wl_twitter_consumer_secret" type="text" value="<?php if(isset($wl_twitter_consumer_secret)) {echo esc_attr($wl_twitter_consumer_secret);} ?>" placeholder="<?php esc_attr_e( 'Enter Your Twitter Consumer Secret', 'twitter-tweets' ); ?>">
							    </div>
							  </div>
							</div>


						</p>
						<br>

						<br>
						<p>
							<div class="container">
							  <div class="row">
							    <div class="col-sm">
							      <label><?php esc_html_e( 'Access Token', 'twitter-tweets' ); ?>&nbsp;*</label>
							    </div>
							    <div class="col-sm">
							      <input class="widefat" id="wl_twitter_access_token" name="wl_twitter_access_token" type="text" value="<?php if( isset( $wl_twitter_access_token ) ) {echo esc_attr($wl_twitter_access_token);} ?>" placeholder="<?php esc_attr_e( 'Enter Your Twitter Access Token', 'twitter-tweets' ); ?>">
							    </div>
							  </div>
							</div>


						</p>
						<br>

						<br>
						<p>
							<div class="container">
							  <div class="row">
							    <div class="col-sm">
							      <label><?php esc_html_e( 'Access Token Secret', 'twitter-tweets' ); ?>&nbsp;*</label>
							    </div>
							    <div class="col-sm">
							      <input class="widefat" id="wl_twitter_token_secret" name="wl_twitter_token_secret" type="text" value="<?php if( isset( $wl_twitter_token_secret ) ) {echo esc_attr($wl_twitter_token_secret);} ?>" placeholder="<?php esc_attr_e( 'Enter Your Twitter Token Secret', 'twitter-tweets' ); ?>">
							    </div>
							  </div>
							</div>


						</p>

						<br>
						<p>
							<div class="container">
							  <div class="row">
							    <div class="col-sm">
							     <label><?php esc_html_e( 'No. Of tweets Show', 'twitter-tweets' ); ?></label>
							    </div>
							    <div class="col-sm">
      							<input class="widefat wltt-slider" id="wl_twitter_tweets" name="wl_twitter_tweets" type="range" value="<?php  if( isset( $wl_twitter_tweets ) ) {echo esc_attr($wl_twitter_tweets);} ?>" min="1" max="14" data-rangeSlider>
      							<p>
								<b><?php esc_html_e( 'Set no of tweets you want to show (Use Arrow Keys)', 'twitter-tweets' ); ?></b> <span id="wl_twitter_range_show"></span>
								</p>
							    </div>
							  </div>
							</div>


						</p>

							<br>
						<p>
							<div class="container">
							  <div class="row">
							    <div class="col-sm">
							     <label><?php esc_html_e( 'Layout', 'twitter-tweets' ); ?></label>
							    </div>
							    <div class="col-sm">
							      <select class="widefat" name="wl_twitter_layout" id="wl_twitter_layout">
								<option value=""><?php esc_html_e( 'Select', 'twitter-tweets' ); ?></option>
								<option value="12"><?php esc_html_e( '1', 'twitter-tweets' ); ?></option>
								<option value="6"><?php esc_html_e( '2', 'twitter-tweets' ); ?></option>
								<option value="4"><?php esc_html_e( '3', 'twitter-tweets' ); ?></option>
								<option value="3"><?php esc_html_e( '4', 'twitter-tweets' ); ?></option>
								</select>
							    </div>
							  </div>
							</div>


						</p>
						<script type="text/javascript">
                            var abc = '<?php echo esc_attr($wl_twitter_layout); ?>';
                            jQuery('#wl_twitter_layout').find('option[value="' + abc + '"]').attr('selected', 'selected')
                        </script>
						<br>
						<?php
							wp_nonce_field( 'twitter_api_nonce', 'twitter_api_nonce' );
						?>
						<input onclick="return SaveApiSettings();" type="button" class="twt_save_btn" id="fb-api-save-settings" name="fb-api-save-settings" value="SAVE">
						<br><br><br>
						<div class="twt_help">
							<?php esc_html_e('Please copy the twitter shortcode', 'twitter-tweets' );?>  <span style="color:#000;"> <b><?php esc_html_e('[WL_TWITTER]', 'twitter-tweets' );?></b> </span>
							<?php esc_html_e('and paste it to on the Page/Post', 'twitter-tweets' );?></span>
						</div>
						<div id="twitter-img" style="display: none;">
							<img src="<?php echo WEBLIZAR_TWITTER_PLUGIN_URL.'images/loading.gif'; ?>" />
						</div>
						<div id="wl_twitter_preview" style="display: none;" class="alert">
							<?php esc_html_e( 'Settings successfully saved. Reloading page for generating preview right side of setting.', 'twitter-tweets' ); ?>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="wl_twt_free">
				<div class="container-fluid">
				    <div class="row">
				    	<div class="col-md-<?php if( isset( $wl_twitter_layout ) ){echo esc_attr($wl_twitter_layout);} ?>">
				    		<?php
				    		if ( isset( $statuses ) && is_array( $statuses ) ) {
							foreach ( $statuses as $status ) {
							/* user info */
							if( isset( $status->user ) ) {
							 $user = $status->user;
							}
							if( isset( $user->name ) ) {
							 $name = $user->name;
							}
							if( isset( $user->screen_name ) ) {
							$screen_name = $user->screen_name;
							}
							if( isset( $user->location ) ) {
							$location = $user->location;
							}
							if( isset( $user->description ) ) {
							$description = $user->description;
							}
							if( isset( $user->url ) ) {
							$url = $user->url;
							}
							if( isset( $status->id_str ) ) {
							$id_str = $status->id_str; /* use it to make link of post */
							}
							if( isset( $status->created_at ) ) {
							$created_at = $status->created_at; /* time when tweet was created */
							}
							/* profile_image_url */
							if( isset( $user->profile_image_url ) ) {
							$profile_image_url = $user->profile_image_url;
							}
							if( isset( $user->profile_image_url_https ) ) {
							$profile_image_url_https = $user->profile_image_url_https;
							}
					    ?>
				    		<div class="wl_tweet_box">
				                <p class="wl_tweet">
				                    <img class="align-self-start mr-3" src="<?php if( isset( $user->profile_image_url_https ) ){echo esc_url($profile_image_url_https);} ?>"
				                         alt="">
				                    <a href="https://twitter.com/<?php if( isset( $user->screen_name ) ){echo esc_attr($screen_name);} ?>">
										<?php if( isset( $user->screen_name ) ) {echo "@" . $screen_name;} ?>
				                    </a>
				                </p>
								<?php
								// $entities = $status->entities;
									if ( isset( $status->extended_entities ) ) {
										$extended_entities_array = $status->extended_entities->media;
										$extended_entities       = $extended_entities_array[0];
										$display_url             = $extended_entities->display_url;
										$media_expanded_url      = $extended_entities->expanded_url;
										$media_type              = $extended_entities->type;
										$media_url               = $extended_entities->media_url;
										if ( $media_type == "photo" ) {
											?>
				                            <img src="<?php echo esc_url($media_url); ?>" class="img-fluid"/>
											<?php
										} elseif ( $media_type == "video" ) {
											$video_info   = $extended_entities->video_info->variants[2];
											$content_type = $video_info->content_type;
											$url          = $video_info->url;
											$new_url      = str_replace( "?tag=8", "", $url );

											if ( isset( $enable_extended_entitie ) && $enable_extended_entitie == "enable" ) {
												?>
				                                <a href="#" data-toggle="modal" data-target="#myModal">
				                                    <img src="<?php echo esc_url($media_url); ?>" class="img-fluid"/>
				                                </a>
												<?php
											} else {
												?>
				                                <a href="#">
				                                    <img src="<?php echo esc_url($media_url); ?>" class="img-fluid"/>
				                                </a>
												<?php
											}
										}
									} /* extended enntities */
				                    elseif ( ! empty( $entities->media ) && is_array( $entities->media ) ) {
										$media = $entities->media;
										foreach ( $media as $media_key => $media_value ) {
											$media_url          = $media_value->media_url;
											$media_url_https    = $media_value->media_url_https;
											$media_detail_url   = $media_value->url;
											$media_display_url  = $media_value->display_url;
											$media_expanded_url = $media_value->expanded_url;
											$media_type         = $media_value->type;
											$media_sizes        = $media_value->sizes;
											?>
				                            <a href="<?php echo esc_url($media_expanded_url); ?>">
				                                <img src="<?php echo esc_url($media_url_https); ?>" class="img-fluid"/>
				                            </a>
											<?php
										}
									}
								?>
				                <p class="wl_tweet_desc">
									<?php
									if( isset( $status->text ) ) {
										echo makeLinks( $status->text );
									}
									?>
				                </p>
				                <p class="wl_tweet_action_buttons">
				                    <a href="https://twitter.com/intent/retweet?tweet_id=<?php echo esc_attr($id_str); ?>&related=<?php echo esc_attr($screen_name); ?> retweet" target="_blank" onclick="window.open('https://twitter.com/intent/retweet?tweet_id=<?php echo esc_attr($id_str); ?>&related=<?php echo esc_attr($screen_name); ?> retweet', 'newwindow', 'width=600,height=450'); return false;">
				                       <?php
				                       if ( isset( $status->retweet_count ) ) {
				                       		esc_html_e( 'Retweet', 'twitter-tweets' );
											echo esc_html("($status->retweet_count)");
				                       }
				                       ?>
				                    </a>

				                    <a href="https://twitter.com/intent/like?tweet_id=<?php echo esc_html($id_str); ?>&related=<?php echo esc_attr($screen_name); ?>"
				                       target="_blank" onclick="window.open('https://twitter.com/intent/like?tweet_id=<?php echo esc_attr($id_str); ?>&related=<?php echo esc_attr($screen_name); ?> retweet', 'newwindow', 'width=600,height=450'); return false;">
				                       <?php
				                       if ( isset( $status->favorite_count ) ) {
			                       		    esc_html_e( 'Like', 'twitter-tweets' );
											echo esc_html("($status->favorite_count)");
				                       }
				                      ?>
				                    </a>

				                    <a href="https://twitter.com/intent/tweet?in_reply_to=<?php echo esc_attr($id_str); ?>&related=<?php echo esc_html($screen_name); ?>"
				                       target="_blank" onclick="window.open('https://twitter.com/intent/tweet?in_reply_to=<?php echo esc_attr($id_str); ?>&related=<?php echo esc_attr($screen_name); ?> retweet', 'newwindow', 'width=600,height=450'); return false;"><?php esc_html_e( 'Reply', 'twitter-tweets' ); ?>
				                    </a>
				                </p>
				                <span class="wl-wtp-date-font-size"><?php if( isset( $status->created_at ) ) {echo tweet_time_calculate( $created_at );} ?>
				                    &nbsp;<?php if( isset( $status->created_at ) ) {esc_html_e( 'ago', 'twitter-tweets' );} ?></span>
				            </div> <!-- Tweet box -->
				        <?php }
				        } ?>
				    	</div>
				    </div>
				</div>
			</div>
		</div>
	</div>
</div>

<!---------------- need help tab------------------------>
<div class="block ui-tabs-panel deactive" id="option-needhelp">
	<div class="row">
		<div class="col-md-10">
			<div id="heading">
				<h2 class="well"><?php esc_html_e('Shortcode Help Section', 'twitter-tweets'); ?></h2>
			</div>
			<p class="well"><b><?php esc_html_e('Customize Feeds for Twitter plugin comes with 2 major feature.', 'twitter-tweets'); ?></b></p>
			<ol>
				<li><?php esc_html_e('Customize feeds Widget', 'twitter-tweets'); ?></li>
				<li><?php esc_html_e('Customize feeds Shortcode', 'twitter-tweets'); ?><?php esc_html_e('[TWTR]', 'twitter-tweets' );?></li>
				<li><?php esc_html_e('Note: Protected tweets will not view', 'twitter-tweets'); ?> <a href="https://help.twitter.com/en/safety-and-security/public-and-protected-tweets" target="_blank"><?php esc_html_e('Help', 'twitter-tweets'); ?></a></li>
			</ol>
			<br>
			<p class="well"><strong><?php esc_html_e('Customize Feeds for Twitter Widget', 'twitter-tweets'); ?></strong></p>

			<ol>
				<li><?php esc_html_e('You can use the widget to display your Twitter Tweets in any theme Widget Sections.', 'twitter-tweets'); ?></li>
				<li><?php esc_html_e('Simple go to your', 'twitter-tweets' ); ?><a href="<?php echo get_site_url(); ?>/wp-admin/widgets.php"><strong><?php esc_html_e('Widgets', 'twitter-tweets'); ?></strong></a><?php esc_html_e(' section and activate available', 'twitter-tweets'); ?> <strong><?php esc_html_e('Twitter By Weblizar', 'twitter-tweets'); ?></strong> <?php esc_html_e('widget in any sidebar section, like in left sidebar, right sidebar or footer sidebar.', 'twitter-tweets'); ?></li>
		    </ol>
			<br>
			<p class="well"><strong><?php esc_html_e('Feeds Short-code ', 'twitter-tweets'); ?><?php esc_html_e('[TWTR]', 'twitter-tweets' );?></strong></p>
			<ol>
				<li><strong>[TWTR]</strong> <?php esc_html_e('shortcode give ability to display Twitter Tweets Box in any Page / Post with content', 'twitter-tweets'); ?>.</li>
				<li><?php esc_html_e('To use shortcode, just copy', 'twitter-tweets'); ?> <strong><?php esc_html_e('[TWTR]', 'twitter-tweets' );?></strong><?php esc_html_e( 'shortcode and paste into content editor of any Page / Post.', 'twitter-tweets'); ?></li>
			</ol>

			<br>
			<p class="well"><strong><?php esc_html_e('Twitter Short-code', 'twitter-tweets'); ?><?php esc_html_e('[WL_TWITTER]', 'twitter-tweets' );?></strong></p>
			<ol>
				<li><strong><?php esc_html_e('[WL_TWITTER]', 'twitter-tweets' );?></strong><?php esc_html_e( 'shortcode, another shortcode, using API Key to login, give ability to display Twitter Tweets Box in any Page / Post with content.', 'twitter-tweets'); ?></li>
				<li><?php esc_html_e('To use shortcode, just copy', 'twitter-tweets'); ?> <strong><?php esc_html_e('[WL_TWITTER]', 'twitter-tweets' );?></strong><?php esc_html_e(' shortcode and paste into content editor of any Page / Post.', 'twitter-tweets'); ?></li>
			</ol>

			<br>
			<p class="well"><strong><?php esc_html_e('How to generate Twitter API Key', 'twitter-tweets'); ?></strong></p>
			<p><?php esc_html_e('We have created a blog post on this topic. It is very easy to understand.', 'twitter-tweets'); ?> <span class="helptopic"><a href="https://weblizar.com/blog/generate-twitter-api-key/" target="_blank"><?php esc_html_e('Click here', 'twitter-tweets'); ?></a></span><?php esc_html_e(' to visit the blog.', 'twitter-tweets'); ?>
			</p>

			<br>
			<p class="well"><strong><?php esc_html_e('Q. What is Twitter Widget ID?', 'twitter-tweets'); ?></strong></p>
			<p><strong><?php esc_html_e('Ans. Twitter Widget ID', 'twitter-tweets'); ?></strong><?php esc_html_e( 'used to authenticate your TWITTER Page data & settings. To get your own TWITTER ID please read our very simple and easy', 'twitter-tweets'); ?> <a href="https://weblizar.com/get-twitter-widget-id/" target="_blank"><strong><?php esc_html_e('Tutorial', 'twitter-tweets'); ?></strong>.</a>
			</p>
		</div>
	</div>
	<div class="row">
		<div class="col-md-10">
			<div id="heading"><h2><?php esc_html_e('Rate Us', 'twitter-tweets' ); ?></h2></div>
			<p><?php esc_html_e('If you are enjoying using our', 'twitter-tweets' ); ?> <b><?php esc_html_e('Customize Feeds for Twitter Widget', 'twitter-tweets'); ?></b><?php esc_html_e( 'plugin and find it useful, then please consider writing a positive feedback. Your feedback will help us to encourage and support the plugin continued development and better user support.', 'twitter-tweets'); ?>
			</p>
			<div class="twt_star">
				<a class="acl-rate-us" style="text-align:center; text-decoration: none;font:normal 30px/l;" href="https://wordpress.org/plugins/twitter-tweets/#reviews" target="_blank" >
					<span class="dashicons dashicons-star-filled"></span>
					<span class="dashicons dashicons-star-filled"></span>
					<span class="dashicons dashicons-star-filled"></span>
					<span class="dashicons dashicons-star-filled"></span>
					<span class="dashicons dashicons-star-filled"></span>
				</a>
			</div>
		</div>
	</div>
</div>
<!---------------- our product tab------------------------>
<div class="block ui-tabs-panel deactive" id="option-ourproduct">
	<?php require_once('our_product.php'); ?>
</div>
