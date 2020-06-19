<?php
	defined( 'ABSPATH' ) or die();

	use Abraham\TwitterOAuth\TwitterOAuth;

	$wl_twitter_api_settings = get_option('wl_twitter_api_settings');	

if( ! empty( $wl_twitter_api_settings ) ) {
	$wl_twitter_consumer_key 	= $wl_twitter_api_settings['wl_twitter_consumer_key'];
	$wl_twitter_consumer_secret = $wl_twitter_api_settings['wl_twitter_consumer_secret'];
	$wl_twitter_access_token 	= $wl_twitter_api_settings['wl_twitter_access_token'];
	$wl_twitter_token_secret 	= $wl_twitter_api_settings['wl_twitter_token_secret'];
}

	/* temp value assigned in case of empty value coming from DB */

	$temp_wl_twitter_consumer_key 	 = "72eoxhZGGhhACh4oQ0MKM5Z1G";
	$temp_wl_twitter_consumer_secret = "LLVWGiSB6mUfjdNk1UugGHxpkHSo3ppcmRVZWqe7VfiVlZaFjs";
	$temp_wl_twitter_access_token 	 = "2409346562-OR0avORZhdUblkLh7mQmzbR4lqlrccfcb3okLd7";
	$temp_wl_twitter_token_secret 	 = "iRohUaA8INkcWZG7S8TnrUCd24lFDHPviEs4XW2ALfKhy";

	$wl_twitter_tweets = ( isset( $wl_twitter_api_settings['wl_twitter_tweets'] ) ) ? $wl_twitter_api_settings['wl_twitter_tweets'] : '5';
	$wl_twitter_layout = ( isset( $wl_twitter_api_settings['wl_twitter_layout'] ) ) ? $wl_twitter_api_settings['wl_twitter_layout'] : '6';

	function get_twitter_connection( $wl_twitter_consumer_key, $wl_twitter_consumer_secret, $wl_twitter_access_token, $wl_twitter_token_secret ) {
		try {
			$twitter_client = new TwitterOAuth( $wl_twitter_consumer_key, $wl_twitter_consumer_secret, $wl_twitter_access_token, $wl_twitter_token_secret );

			$content = $twitter_client->get( "account/verify_credentials" );

			if ( ! $content ) {
				throw new Exception( esc_html__( 'Connection Error', 'twitter-tweets' ) );
			}
		} catch ( Exception $e ) {
			echo esc_html($e->getMessage());
			return null;
		}

		return $twitter_client;
	}

	function tweet_time_calculate( $created_at ) {
		$then  = new DateTime( $created_at );
		$now   = new DateTime();
		$delta = $now->diff( $then );

		$quantities = array(
			'year'   => $delta->y,
			'month'  => $delta->m,
			'day'    => $delta->d,
			'hour'   => $delta->h,
			'minute' => $delta->i,
		);

		$str = '';
		foreach ( $quantities as $unit => $value ) {
			if ( $value == 0 ) {
				continue;
			}
			$str .= $value . ' ' . $unit;
			if ( $value != 1 ) {
				$str .= 's';
			}
			$str .= ', ';
		}
		$str = $str == '' ? 'a moment ' : substr( $str, 0, - 2 );

		return $str;
	}

	function makeLinks( $str ) {
		$reg_exUrl     = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
		$urls          = array();
		$urlsToReplace = array();
		if ( preg_match_all( $reg_exUrl, $str, $urls ) ) {
			$numOfMatches       = count( $urls[0] );
			$numOfUrlsToReplace = 0;
			for ( $i = 0; $i < $numOfMatches; $i ++ ) {
				$alreadyAdded       = false;
				$numOfUrlsToReplace = count( $urlsToReplace );
				for ( $j = 0; $j < $numOfUrlsToReplace; $j ++ ) {
					if ( $urlsToReplace[ $j ] == $urls[0][ $i ] ) {
						$alreadyAdded = true;
					}
				}
				if ( ! $alreadyAdded ) {
					array_push( $urlsToReplace, $urls[0][ $i ] );
				}
			}
			$numOfUrlsToReplace = count( $urlsToReplace );
			for ( $i = 0; $i < $numOfUrlsToReplace; $i ++ ) {
				$str = str_replace( $urlsToReplace[ $i ], "<a href=\"" . $urlsToReplace[ $i ] . "\" target='_blank'>" . $urlsToReplace[ $i ] . "</a> ", $str );
			}

			return $str;
		} else {
			return $str;
		}
	}
	if ( isset( $wl_twitter_consumer_key ) ) {
		$connection = get_twitter_connection( $wl_twitter_consumer_key, $wl_twitter_consumer_secret, $wl_twitter_access_token, $wl_twitter_token_secret );
	}
	else {
		$connection = get_twitter_connection( $temp_wl_twitter_consumer_key, $temp_wl_twitter_consumer_secret, $temp_wl_twitter_access_token, $temp_wl_twitter_token_secret );
	}	

	if ( ! $connection ) {
		$error_messaage = esc_html__("Can't connect to Twitter API. Check your internet connection.", 'twitter-tweets');
		die( $error_messaage );
	}	
	$statuses = $connection->get( "statuses/home_timeline",
	["count"           => $wl_twitter_tweets,
	"exclude_replies"  => 'false',
	"include_entities" => 0	]);
?>