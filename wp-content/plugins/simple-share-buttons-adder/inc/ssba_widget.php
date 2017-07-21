<?php
defined('ABSPATH') or die('No direct access permitted');

// widget class
class ssba_widget extends WP_Widget {

	// construct the widget
	public function __construct() {
		parent::__construct(
 		'ssba_widget', // Base ID
		'Share Buttons', // Name
		array( 'description' => __( 'Simple Share Buttons Adder', 'text_domain' ), ) // Args
	);
	}

	// extract required arguments and run the shortcode
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$url = $instance['url'];
		$pagetitle = $instance['pagetitle'];

		echo $before_widget;
		if (!empty($title))
		echo $before_title . $title . $after_title;

		$shortcode = '[ssba';
			($url != '' ? $shortcode .= ' url="' . $url . '"' : NULL);
			($pagetitle != '' ? $shortcode .= ' title="' . $pagetitle . '"' : NULL);
		$shortcode .= ' widget="Y"]';
		echo do_shortcode($shortcode, 'text_domain' );
		echo $after_widget;
	}

	public function form( $instance )
	{
		if ( isset( $instance[ 'title' ] ) )
		{
			$title = $instance[ 'title' ];
		}
		else
		{
			$title = __( 'Share Buttons', 'text_domain' );
		}

		if ( isset( $instance[ 'title' ] ) )
		{
			$url = esc_url( $instance['url'] );
		}
		else
		{
			$url = '';
		}

		if ( isset( $instance[ 'title' ] ) )
		{
			$pagetitle = esc_attr( $instance['pagetitle'] );
		}
		else
		{
			$pagetitle = '';
		}

		# Title
		echo '<p><label for="' . $this->get_field_id('title') . '">' . 'Title:' . '</label><input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></p>';
		# URL
		echo '<p><label for="' . $this->get_field_id('url') . '">' . 'URL:' . '</label><input class="widefat" id="' . $this->get_field_id('url') . '" name="' . $this->get_field_name('url') . '" type="text" value="' . $url . '" /></p>';
		echo '<p class="description">Leave this blank to share the current page, or enter a URL to force one URL for all pages.</p>';
		# Page title
		echo '<p><label for="' . $this->get_field_id('pagetitle') . '">' . 'Page title:' . '</label><input class="widefat" id="' . $this->get_field_id('pagetitle') . '" name="' . $this->get_field_name('pagetitle') . '" type="text" value="' . $pagetitle . '" /></p>';
		echo '<p class="description">Set a page title for the page being shared, leave this blank if you have not set a URL.</p>';
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['url'] = strip_tags( $new_instance['url'] );
		$instance['pagetitle'] = strip_tags( $new_instance['pagetitle'] );

		return $instance;
	}

}

// add ssba to available widgets
add_action( 'widgets_init', create_function( '', 'register_widget( "ssba_widget" );' ) );

function mywidget_init() {

	register_sidebar_widget('Share Buttons Widget', 'ssba_widget');
	register_widget_control('Share Buttons Widget', 'ssba_widget_control');
}
