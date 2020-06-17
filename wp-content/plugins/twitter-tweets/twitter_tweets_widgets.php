<?php
class WeblizarTwitter extends WP_Widget {
	function __construct() {
		parent::__construct(
		'weblizar_twitter', // Base ID
		esc_html__( 'Customize Feeds for Twitter Widget', 'twitter-tweets' ), // Name
		array( 'description' => esc_html__( 'Display latest tweets from your Twitter account', 'twitter-tweets' )));
	}

	/*** Front-end display of widget. ***/
    public function widget( $args, $instance ) {
		// Outputs the content of the widget
		extract($args); // Make before_widget, etc available.
		$title = apply_filters('title', $instance['title']);
	    echo wp_kses_post($before_widget);
		if (!empty($title)) {	echo wp_kses_post($before_title . $title . $after_title);	}
		$TwitterUserName    = $instance['TwitterUserName'];
        $Theme              = $instance['Theme'];
        $Height             = $instance['Height'];
        $Width              = $instance['Width'];
        $LinkColor          = $instance['LinkColor'];
        $ExcludeReplies     = $instance['ExcludeReplies'];
        $AutoExpandPhotos   = $instance['AutoExpandPhotos'];
        $TwitterWidgetId    = $instance['TwitterWidgetId'];
        $tw_language   	 	= $instance['tw_language'];
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
		echo wp_kses_post($after_widget);
	}

	/** Back-end widget form. **/
    public function form( $instance ) {
		if ( isset( $instance[ 'TwitterUserName' ] ) ) {
			$TwitterUserName = $instance[ 'TwitterUserName' ];
		}  else  {
			$TwitterUserName =  "weblizar";
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

		if ( isset( $instance[ 'Width' ] ) )  {
			$Width = $instance[ 'Width' ];
		} else  {
			$Width = "450";
		}

		if ( isset( $instance[ 'LinkColor' ] ) )  {
			$LinkColor = $instance[ 'LinkColor' ];
		} else  {
			$LinkColor = "#0000ff";
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
			 $title = esc_html__( 'Tweets', 'Widget Title Here', 'twitter-tweets' );
		} ?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'twitter-tweets' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" placeholder="<?php esc_attr_e( 'Enter Widget Title','twitter-tweets'); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'TwitterUserName' )); ?>"><?php esc_html_e( 'Twitter Username', 'twitter-tweets' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'TwitterUserName' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'TwitterUserName' )); ?>" type="text" value="<?php echo esc_attr( $TwitterUserName ); ?>" placeholder="<?php esc_attr_e( 'Enter Your Twitter Account Username', 'twitter-tweets'); ?>">
		</p>
		<p>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'TwitterWidgetId' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'TwitterWidgetId' )); ?>" type="hidden" value="<?php echo esc_attr( $TwitterWidgetId ); ?>" placeholder="<?php esc_attr_e( 'Enter Your Twitter Widget ID','twitter-tweets'); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'Theme' )); ?>"><?php esc_html_e( 'Theme', 'twitter-tweets' ); ?></label>
			<select id="<?php echo esc_attr($this->get_field_id( 'Theme' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'Theme' )); ?>">
				<option value="light" <?php if($Theme == "light") echo "selected=selected" ?>><?php esc_html_e( 'Light', 'twitter-tweets' ); ?></option>
				<option value="dark" <?php if($Theme == "dark") echo "selected=selected" ?>><?php esc_html_e( 'Dark', 'twitter-tweets' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'Height' )); ?>"><?php esc_html_e( 'Height', 'twitter-tweets'  ); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'Height' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'Height' )); ?>" type="text" value="<?php echo esc_attr( $Height ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'LinkColor' )); ?>"><?php esc_html_e( 'URL Link Color:','twitter-tweets' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'LinkColor' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'LinkColor' )); ?>" type="text" value="<?php echo esc_attr( $LinkColor ); ?>">
			<?php esc_html_e( 'Find More Color Codes', 'twitter-tweets'  ); ?> <a href="http://html-color-codes.info/" target="_blank"><?php esc_html_e( 'HERE', 'twitter-tweets'  ); ?></a>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'ExcludeReplies' )); ?>"><?php esc_html_e( 'Exclude Replies on Tweets', 'twitter-tweets' ); ?></label>
			<select id="<?php echo esc_attr($this->get_field_id( 'ExcludeReplies' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'ExcludeReplies' )); ?>">
				<option value="yes" <?php if($ExcludeReplies == "yes") echo "selected=selected" ?>><?php esc_html_e( 'Yes', 'twitter-tweets'  ); ?></option>
				<option value="no" <?php if($ExcludeReplies == "no") echo "selected=selected" ?>><?php esc_html_e( 'No', 'twitter-tweets'  ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'AutoExpandPhotos' )); ?>"><?php esc_html_e( 'Auto Expand Photos in Tweets', 'twitter-tweets' ); ?></label>
			<select id="<?php echo esc_attr($this->get_field_id( 'AutoExpandPhotos' )); ?>" name="<?php echo esc_attr($this->get_field_name('AutoExpandPhotos' )); ?>">
				<option value="yes" <?php if($AutoExpandPhotos == "yes") echo "selected=selected" ?>><?php esc_html_e( 'Yes', 'twitter-tweets'  ); ?></option>
				<option value="no" <?php if($AutoExpandPhotos == "no") echo "selected=selected" ?>><?php esc_html_e( 'No', 'twitter-tweets'  ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'tw_language' )); ?>"><?php esc_html_e( 'Select Language' ); ?></label>
			<select id="<?php echo esc_attr($this->get_field_id( 'tw_language' )); ?>" name="<?php echo esc_attr($this->get_field_name('tw_language' )); ?>">
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
				<option value="hu"<?php if($tw_language == "hu") echo "selected=selected" ?>><?php esc_html_e('Hungarian', 'twitter-tweets'); ?></option>
				<option value="id"<?php if($tw_language == "id") echo "selected=selected" ?>><?php esc_html_e('Indonesian', 'twitter-tweets'); ?></option>
				<option value="it"<?php if($tw_language == "it") echo "selected=selected" ?>><?php esc_html_e('Italian', 'twitter-tweets'); ?></option>
				<option value="ja"<?php if($tw_language == "ja") echo "selected=selected" ?>><?php esc_html_e('Japanese', 'twitter-tweets'); ?></option>
				<option value="ko"<?php if($tw_language == "ko") echo "selected=selected" ?>><?php esc_html_e('Korean', 'twitter-tweets'); ?></option>
				<option value="msa"<?php if($tw_language == "msa") echo "selected=selected" ?>><?php esc_html_e('Malay', 'twitter-tweets'); ?></option>
				<option value="nl"<?php if($tw_language == "nl") echo "selected=selected" ?>><?php esc_html_e('Dutch', 'twitter-tweets'); ?></option>
				<option value="no"<?php if($tw_language == "no") echo "selected=selected" ?>><?php esc_html_e('Norwegian', 'twitter-tweets'); ?></option>
				<option value="pl"<?php if($tw_language == "pl") echo "selected=selected" ?>><?php esc_html_e('Polish', 'twitter-tweets'); ?></option>
				<option value="pt"<?php if($tw_language == "pt") echo "selected=selected" ?>><?php esc_html_e('Portuguese', 'twitter-tweets'); ?></option>
				<option value="ro"<?php if($tw_language == "ro") echo "selected=selected" ?>><?php esc_html_e('Romanian', 'twitter-tweets'); ?></option>
				<option value="ru"<?php if($tw_language == "ru") echo "selected=selected" ?>><?php esc_html_e('Russian', 'twitter-tweets'); ?></option>
				<option value="sv"<?php if($tw_language == "sv") echo "selected=selected" ?>><?php esc_html_e('Swedish', 'twitter-tweets'); ?></option>
				<option value="th"<?php if($tw_language == "th") echo "selected=selected" ?>><?php esc_html_e('Thai', 'twitter-tweets'); ?></option>
				<option value="tr"<?php if($tw_language == "tr") echo "selected=selected" ?>><?php esc_html_e('Turkish', 'twitter-tweets'); ?></option>
				<option value="uk<?php if($tw_language == "uk") echo "selected=selected" ?>"><?php esc_html_e('Ukrainian', 'twitter-tweets'); ?></option>
				<option value="ur"<?php if($tw_language == "ur") echo "selected=selected" ?>><?php esc_html_e('Urdu', 'twitter-tweets'); ?></option>
				<option value="vi"<?php if($tw_language == "vi") echo "selected=selected" ?>><?php esc_html_e('Vietnamese', 'twitter-tweets'); ?></option>
				<option value="zh-cn"<?php if($tw_language == "zh-cn") echo "selected=selected" ?>><?php esc_html_e('Chinese (Simplified)', 'twitter-tweets'); ?></option>
				<option value="zh-tw"<?php if($tw_language == "zh-tw") echo "selected=selected" ?>><?php esc_html_e('Chinese (Traditional)', 'twitter-tweets'); ?></option>
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
		$Theme = sanitize_text_field( 'theme', ( ! empty( $new_instance['Theme'] ) ) ? strip_tags( $new_instance['Theme'] ) : 'light' );		
		$Height = sanitize_text_field( ( ! empty( $new_instance['Height'] ) ) ? strip_tags( $new_instance['Height'] ) : '450' );
		$Width = sanitize_text_field( ( ! empty( $new_instance['Width'] ) ) ? strip_tags( $new_instance['Width'] ) : '' );
		$Linkcolor = sanitize_hex_color( ( ! empty( $new_instance['LinkColor'] ) ) ? strip_tags( $new_instance['LinkColor'] ) : '#CC0000' );
		$ExcludeReplies = sanitize_text_field( ( ! empty( $new_instance['ExcludeReplies'] ) ) ? strip_tags( $new_instance['ExcludeReplies'] ) : 'yes' );
		$AutoExpandPhotos = sanitize_text_field( ( ! empty( $new_instance['AutoExpandPhotos'] ) ) ? strip_tags( $new_instance['AutoExpandPhotos'] ) : 'yes' );
		$TwitterWidgetId = sanitize_text_field( ( ! empty( $new_instance['TwitterWidgetId'] ) ) ? strip_tags( $new_instance['TwitterWidgetId'] ) : '' );
		$tw_language = sanitize_text_field( ( ! empty( $new_instance['tw_language'] ) ) ? strip_tags( $new_instance['tw_language'] ) : '' );

        $instance['title'] 				= $title;
        $instance['TwitterUserName'] 	= $TwitterUserName;
        $instance['Theme'] 				= $Theme;
        $instance['Height'] 			= $Height;
        $instance['LinkColor'] 			= $Linkcolor;
        $instance['ExcludeReplies'] 	= $ExcludeReplies;
        $instance['AutoExpandPhotos'] 	= $AutoExpandPhotos;
        $instance['TwitterWidgetId'] 	= $TwitterWidgetId;
        $instance['tw_language'] 		= $tw_language;
        $instance['Width'] 	 			= $Width;
        return $instance;
	}
}
// end of class WeblizarTwitter
// register WeblizarTwitter widget
function WeblizarTwitterWidget() {
	register_widget( 'WeblizarTwitter' );
}
add_action( 'widgets_init', 'WeblizarTwitterWidget' );
