<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/*** Shortcode For twitter tweet ***/
add_shortcode("TWTR", "twitter_tweet_shortcode");
function twitter_tweet_shortcode() {
	ob_start();
	$twitterSettings = unserialize(get_option("ali_twitter_shortcode"));

	if( isset($twitterSettings[ 'TwitterUserName' ] ) )  {
		$TwitterUserName = $twitterSettings[ 'TwitterUserName' ];
	} else $TwitterUserName = "weblizar";

	$Theme = "light";
	if (isset($twitterSettings[ 'Theme' ] ) ) {
		$Theme = $twitterSettings[ 'Theme' ];
	}

	$Height = "450";
	if ( isset($twitterSettings[ 'Height' ] ) ) {
		$Height = $twitterSettings[ 'Height' ];
	}

	$Width = "";
	if (isset($twitterSettings[ 'Width' ] ) ) {
		$Width = $twitterSettings[ 'Width' ];
	}

	$LinkColor = "#CC0000";
	if (isset( $twitterSettings[ 'LinkColor' ] ) )  {
		$LinkColor = $twitterSettings[ 'LinkColor' ];
	}

	$ExcludeReplies = "yes";
	if (isset( $twitterSettings[ 'ExcludeReplies' ] ) )  {
		$ExcludeReplies = $twitterSettings['ExcludeReplies' ];
	}

	$AutoExpandPhotos = "yes";
	if (isset( $twitterSettings[ 'AutoExpandPhotos' ] ) ) {
		$AutoExpandPhotos = $twitterSettings[ 'AutoExpandPhotos' ];
	}
	$tw_language = "";
	if (isset( $twitterSettings[ 'tw_language' ] ) ) {
		$tw_language = $twitterSettings[ 'tw_language' ];
	}

	if (isset($twitterSettings[ 'TwitterWidgetId' ] ) ) {
		$TwitterWidgetId = $twitterSettings[ 'TwitterWidgetId' ];
	} else {
		$TwitterWidgetId = "";
	}

	$title = esc_html__( 'Widget Title Here', 'twitter-tweets' );
	if(isset($twitterSettings[ 'title' ] ) ) {
		$title = $twitterSettings[ 'title' ];
	}

	?>
	<div style="display:block;width:100%;float:left;overflow:hidden">
	<a class="twitter-timeline" data-dnt="true" href="https://twitter.com/<?php echo esc_attr($TwitterUserName); ?>"
		min-width="<?php echo esc_attr($Width); ?>"
		height="<?php echo esc_attr($Height); ?>"
		data-theme="<?php echo esc_attr($Theme); ?>"
		data-lang="<?php echo esc_attr($tw_language); ?>"
		data-link-color="<?php echo esc_attr($LinkColor); ?>"
		data-cards="hidden"></a>
		<script>
			!function(d,s,id) {
				var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}
			} (document,"script","twitter-wjs");
		</script>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode("WL_TWITTER", "wl_twitter_tweets_api");
function wl_twitter_tweets_api() {
	require_once( wl_twitter_dir_path . "load-tweets.php" );
	ob_start();
?>
<div class="wl_twt_free">
	<div class="container-fluid">
	    <div class="row">
	    	<!-- <div > -->
	    		<?php if ( isset( $statuses ) && is_array( $statuses )  ) {
				foreach ( $statuses as $status ) {
					// var_dump($status);
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
	    		<div class="wl_tweet_box col-md-<?php if( isset( $wl_twitter_layout ) ) {echo esc_attr($wl_twitter_layout);} ?>">
	                <p class="wl_tweet">
	                    <img class="align-self-start mr-3" src="<?php if( isset( $user->profile_image_url_https ) ) { echo esc_url($profile_image_url_https); } ?>"                      alt="">
	                    <a href="https://twitter.com/<?php if( isset( $user->screen_name ) ) {echo esc_attr($screen_name);} ?>">
							<?php if( isset( $user->screen_name ) ) {echo "@" . $screen_name;} ?>
	                    </a>
	                </p>
					<?php
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
								} else { ?>
	                                <a href="#"><img src="<?php echo esc_url($media_url); ?>" class="img-fluid"/></a>
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
								$media_sizes        = $media_value->sizes; ?>
	                            <a href="<?php echo esc_url($media_expanded_url); ?>">
	                                <img src="<?php echo esc_url($media_url_https); ?>" class="img-fluid"/>
	                            </a>
								<?php
							}
						} ?>
	                <p class="wl_tweet_desc">
						<?php
						if( isset( $status->text ) ) {
							echo makeLinks( $status->text );
						}
						?>
	                </p>
	                <p class="wl_tweet_action_buttons">
	                    <a href="https://twitter.com/intent/retweet?tweet_id=<?php echo esc_attr($id_str); ?>&related=<?php echo esc_attr($screen_name); ?> retweet"
	                       target="_blank"
	                       onclick="window.open('https://twitter.com/intent/retweet?tweet_id=<?php echo esc_attr($id_str); ?>&related=<?php echo esc_attr($screen_name); ?> retweet', 'newwindow', 'width=600,height=450'); return false;">
							<?php
		                       if ( isset( $status->retweet_count ) ) {
		                       		esc_html_e( 'Retweet', 'twitter-tweets' );
									echo esc_html("($status->retweet_count)");
								}
							?>
	                    </a>

	                    <a href="https://twitter.com/intent/like?tweet_id=<?php echo esc_attr($id_str); ?>&related=<?php echo esc_attr($screen_name); ?>" target="_blank" onclick="window.open('https://twitter.com/intent/like?tweet_id=<?php echo esc_attr($id_str); ?>&related=<?php echo esc_attr($screen_name); ?> retweet', 'newwindow', 'width=600,height=450'); return false;">
						<?php
						   if ( isset( $status->favorite_count ) ) {
								esc_html_e( 'Like', 'twitter-tweets' );
								echo esc_html("($status->favorite_count)");
						   }
						?>
	                    </a>
	                    <a href="https://twitter.com/intent/tweet?in_reply_to=<?php echo esc_attr($id_str); ?>&related=<?php echo esc_attr($screen_name); ?>" target="_blank" onclick="window.open('https://twitter.com/intent/tweet?in_reply_to=<?php echo esc_attr($id_str); ?>&related=<?php echo esc_attr($screen_name); ?> retweet', 'newwindow', 'width=600,height=450'); return false;"><?php esc_html_e( 'Reply', 'twitter-tweets' ); ?>
	                    </a>
	                </p>
	                <span class="wl-wtp-date-font-size">
						<?php if( isset( $status->created_at ) ) {echo tweet_time_calculate( $created_at );} ?>&nbsp;
						<?php if( isset( $status->created_at ) ) { esc_html_e( 'ago', 'twitter-tweets' );} ?>
					</span>
	            </div> <!-- Tweet box -->
	       <?php }
			} ?>
	    	<!-- </div> -->
	    </div>
	</div>
</div>
<?php return ob_get_clean(); } ?>
