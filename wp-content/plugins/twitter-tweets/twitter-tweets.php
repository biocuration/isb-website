<?php
/*
 * Plugin Name: Twitter Tweets
 * Version: 1.7.1
 * Description: Display your latest tweets on WordPress blog from your Twitter account.
 * Author: WebLizar
 * Author URI: http://www.weblizar.com/
 * Plugin URI: http://www.weblizar.com/plugins/
 */
 
/**
 * Constant Values & Variables
 */
define("WEBLIZAR_TWITTER_PLUGIN_URL", plugin_dir_url(__FILE__));
define("twitter_tweets", "weblizar_twitter");

/**
 * Widget Code
 */
class WeblizarTwitter extends WP_Widget {
	function __construct() {
		parent::__construct(
		'weblizar_twitter', // Base ID
		'Twitter Tweets', // Name
		array( 'description' => __( 'Display latest tweets from your Twitter account', twitter_tweets )));
	}
    
	/*
     * Front-end display of widget.
     */
    public function widget( $args, $instance ) {
		// Outputs the content of the widget
		extract($args); // Make before_widget, etc available.
		$title = apply_filters('title', $instance['title']);		
	    echo $before_widget;
		if (!empty($title)) {	echo $before_title . $title . $after_title;	}
		$TwitterUserName    =   apply_filters( 'weblizar_twitter_user_name', $instance['TwitterUserName'] );
        $Theme              =   apply_filters( 'weblizar_twitter_theme', $instance['Theme'] );
        $Height             =   apply_filters( 'weblizar_twitter_height', $instance['Height'] );
        $Width              =   apply_filters( 'weblizar_twitter_width', $instance['Width'] );
        $LinkColor          =   apply_filters( 'weblizar_twitter_link_color', $instance['LinkColor'] );
        $ExcludeReplies     =   apply_filters( 'weblizar_twitter_exclude_replies', $instance['ExcludeReplies'] );
        $AutoExpandPhotos   =   apply_filters( 'weblizar_twitter_auto_expand_photo', $instance['AutoExpandPhotos'] );
        $TwitterWidgetId    =   apply_filters( 'weblizar_twitter_widget_id', $instance['TwitterWidgetId'] );
        $tw_language   	 =   apply_filters( 'weblizar_twitter_language', $instance['tw_language'] );
        ?>
        <div style="display:block;width:100%;float:left;overflow:hidden">
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
		</div>
        <?php
		echo $after_widget;
	}
    
	/*
     * Back-end widget form.
     */
    public function form( $instance ) {
		if ( isset( $instance[ 'TwitterUserName' ] ) ) {
			$TwitterUserName = $instance[ 'TwitterUserName' ];
		}  else  {
			$TwitterUserName = "";
		}
		if ( isset( $instance[ 'Theme' ] ) ) {
			$Theme = $instance[ 'Theme' ];
		}  else  {
			$Theme = "light";
		}
		if ( isset( $instance[ 'Height' ] ) )  {
			$Height = $instance[ 'Height' ];
		} else  {
			$Height = "450";
		}
		 
		if ( isset( $instance[ 'LinkColor' ] ) ) {
			$LinkColor = $instance[ 'LinkColor' ];
		} else  {
			$LinkColor = "#CC0000";
		}
		
		if ( isset( $instance[ 'ExcludeReplies' ] ) ) {
			$ExcludeReplies = $instance[ 'ExcludeReplies' ];
		} else {
			$ExcludeReplies = "yes";
		}
		
		if ( isset( $instance[ 'AutoExpandPhotos' ] ) ) {
			$AutoExpandPhotos = $instance[ 'AutoExpandPhotos' ];
		} else {
			$AutoExpandPhotos = "yes";
		}
		
		if ( isset( $instance[ 'tw_language' ] ) ) {
			$tw_language = $instance[ 'tw_language' ];
		} else {
			$tw_language = "";
		}
		if ( isset( $instance[ 'TwitterWidgetId' ] ) ) {
			$TwitterWidgetId = $instance[ 'TwitterWidgetId' ];
		} else {
			$TwitterWidgetId = "";
		}
		
		if ( isset( $instance[ 'title' ] ) ) {
			 $title = $instance[ 'title' ];
		} else {
			 $title = __( 'Tweets', 'Widget Title Here' );
		} ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" placeholder="<?php _e( 'Enter Widget Title',twitter_tweets); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'TwitterUserName' ); ?>"><?php _e( 'Twitter Username' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'TwitterUserName' ); ?>" name="<?php echo $this->get_field_name( 'TwitterUserName' ); ?>" type="text" value="<?php echo esc_attr( $TwitterUserName ); ?>" placeholder="<?php _e( 'Enter Your Twitter Account Username',twitter_tweets); ?>">
		</p>
		<p>
			<input class="widefat" id="<?php echo $this->get_field_id( 'TwitterWidgetId' ); ?>" name="<?php echo $this->get_field_name( 'TwitterWidgetId' ); ?>" type="hidden" value="<?php echo esc_attr( $TwitterWidgetId ); ?>" placeholder="<?php _e( 'Enter Your Twitter Widget ID',twitter_tweets); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'Theme' ); ?>"><?php _e( 'Theme' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'Theme' ); ?>" name="<?php echo $this->get_field_name( 'Theme' ); ?>">
				<option value="light" <?php if($Theme == "light") echo "selected=selected" ?>>Light</option>
				<option value="dark" <?php if($Theme == "dark") echo "selected=selected" ?>>Dark</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'Height' ); ?>"><?php _e( 'Height' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'Height' ); ?>" name="<?php echo $this->get_field_name( 'Height' ); ?>" type="text" value="<?php echo esc_attr( $Height ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'LinkColor' ); ?>"><?php _e( 'URL Link Color:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'LinkColor' ); ?>" name="<?php echo $this->get_field_name( 'LinkColor' ); ?>" type="text" value="<?php echo esc_attr( $LinkColor ); ?>">
			Find More Color Codes <a href="http://html-color-codes.info/" target="_blank">HERE</a>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'ExcludeReplies' ); ?>"><?php _e( 'Exclude Replies on Tweets' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'ExcludeReplies' ); ?>" name="<?php echo $this->get_field_name( 'ExcludeReplies' ); ?>">
				<option value="yes" <?php if($ExcludeReplies == "yes") echo "selected=selected" ?>>Yes</option>
				<option value="no" <?php if($ExcludeReplies == "no") echo "selected=selected" ?>>No</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'AutoExpandPhotos' ); ?>"><?php _e( 'Auto Expand Photos in Tweets' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'AutoExpandPhotos' ); ?>" name="<?php echo $this->get_field_name('AutoExpandPhotos' ); ?>">
				<option value="yes" <?php if($AutoExpandPhotos == "yes") echo "selected=selected" ?>>Yes</option>
				<option value="no" <?php if($AutoExpandPhotos == "no") echo "selected=selected" ?>>No</option>
			</select>
		</p>
		
		
		<p>
			<label for="<?php echo $this->get_field_id( 'tw_language' ); ?>"><?php _e( 'Select Language' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'tw_language' ); ?>" name="<?php echo $this->get_field_name('tw_language' ); ?>">
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
		
		<?php    
	}
   /*
      Sanitize widget form values as they are saved.
      @see WP_Widget::update()
      @param array $new_instance Values just sent to be saved.
      @param array $old_instance Previously saved values from database.
      @return array Updated safe values to be saved.
    */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
		$title= sanitize_text_field( ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : 'Widget Title Here' );
		$TwitterUserName = sanitize_text_field( ( ! empty( $new_instance['TwitterUserName'] ) ) ? strip_tags( $new_instance['TwitterUserName'] ) : '' );
		$Theme = sanitize_option( 'theme', ( ! empty( $new_instance['Theme'] ) ) ? strip_tags( $new_instance['Theme'] ) : 'light' );
		$Height = sanitize_text_field( ( ! empty( $new_instance['Height'] ) ) ? strip_tags( $new_instance['Height'] ) : '450' );
		$Width = sanitize_text_field( ( ! empty( $new_instance['Width'] ) ) ? strip_tags( $new_instance['Width'] ) : '' );
		$Linkcolor = sanitize_option( ( ! empty( $new_instance['LinkColor'] ) ) ? strip_tags( $new_instance['LinkColor'] ) : '#CC0000' );
		$ExcludeReplies = sanitize_option( ( ! empty( $new_instance['ExcludeReplies'] ) ) ? strip_tags( $new_instance['ExcludeReplies'] ) : 'yes' );
		$AutoExpandPhotos = sanitize_option( ( ! empty( $new_instance['AutoExpandPhotos'] ) ) ? strip_tags( $new_instance['AutoExpandPhotos'] ) : 'yes' );
		$TwitterWidgetId = sanitize_text_field( ( ! empty( $new_instance['TwitterWidgetId'] ) ) ? strip_tags( $new_instance['TwitterWidgetId'] ) : '' ); 
		$tw_language = sanitize_text_field( ( ! empty( $new_instance['tw_language'] ) ) ? strip_tags( $new_instance['tw_language'] ) : '' ); 
	
        $instance['title'] 				= $title;
        $instance['TwitterUserName'] 	= $TwitterUserName;
        $instance['Theme'] 				= $Theme;
        $instance['Height'] 			= $Height;
        $instance['Width'] 				= $Width;
        $instance['LinkColor'] 			= $Linkcolor;
        $instance['ExcludeReplies'] 	= $ExcludeReplies;
        $instance['AutoExpandPhotos'] 	= $AutoExpandPhotos;
        $instance['TwitterWidgetId'] 	= $TwitterWidgetId;
        $instance['tw_language'] 	= $tw_language;
        return $instance;
	}
} 
// end of class WeblizarTwitter

// register WeblizarTwitter widget
function WeblizarTwitterWidget() {
	register_widget( 'WeblizarTwitter' );
}
add_action( 'widgets_init', 'WeblizarTwitterWidget' );

/***
 * Shortcode Settings Menu
 */
function  Twitter_Menu()  {
	$AdminMenu = add_menu_page( 'Twitter Tweets', 'Twitter Tweets', 'administrator', 'Twitter', 'Twitter_by_weblizar_page_function', "dashicons-wordpress-alt");
}
add_action('admin_menu','Twitter_Menu');
function Twitter_by_weblizar_page_function() {
	wp_enqueue_script('jquery');
    wp_enqueue_style('weblizar-option-twiiter-style-css', WEBLIZAR_TWITTER_PLUGIN_URL .'css/weblizar-option-twiiter-style.css');
    wp_enqueue_style('recom', WEBLIZAR_TWITTER_PLUGIN_URL .'css/recom.css');
    wp_enqueue_script('weblizar-tab-js',WEBLIZAR_TWITTER_PLUGIN_URL .'js/option-js.js',array('jquery', 'media-upload', 'jquery-ui-sortable'));
	require_once("twiiter_help_body.php");
}

/***
 * Twitter Shortcode
 */
require_once("twitter-tweets_shortcode.php");
?>