<?php
/*
 * Plugin Name: Customize Feeds for Twitter
 * Version: 1.9.4
 * Description: Customize Feeds for Twitter plugin for WordPress. You can use this to display real time Twitter feeds on any where on your webiste by using shortcode or widgets
 * Author: Weblizar
 * Author URI: https://www.weblizar.com/
 * Plugin URI: https://wordpress.org/plugins/twitter-tweets/
   Domain Path: /lang/
 */

/*** Constant Values & Variables ***/

if ( ! defined( 'ABSPATH' ) ) exit;

define("WEBLIZAR_TWITTER_PLUGIN_URL", plugin_dir_url(__FILE__));
define("wl_twitter_dir_path", plugin_dir_path( __FILE__ ) );

add_action( 'plugins_loaded', 'weblizar_twitter_tweeets_load_translation' );
function weblizar_twitter_tweeets_load_translation() {
	load_plugin_textdomain( 'twitter-tweets', false, basename( wl_twitter_dir_path ) . '/lang' );
}

/*** Widget Code ***/
require_once( wl_twitter_dir_path."vendor/autoload.php" );

/**** Twitter Shortcode ***/
require_once("twitter-tweets_shortcode.php");

/**** Twitter widgets ***/
require_once("twitter_tweets_widgets.php");

/** Shortcode Settings Menu **/
function  Weblizar_Twitter_Menu()  {
	$AdminMenu = add_menu_page( esc_html__('Customize Feeds for Twitter', 'twitter-tweets'), esc_html__('Customize Feeds for Twitter', 'twitter-tweets'), 'manage_options', 'Twitter', 'Twitter_by_weblizar_page_function', "dashicons-wordpress-alt");
}
add_action('admin_menu','Weblizar_Twitter_Menu');
function Twitter_by_weblizar_page_function() {

	 wp_enqueue_style( 'wp-color-picker');
        wp_enqueue_script( 'wp-color-picker');
	/**CSS**/
	wp_enqueue_style('weblizar-option-twiiter-style-css', WEBLIZAR_TWITTER_PLUGIN_URL .'css/weblizar-option-twiiter-style.css');
    wp_enqueue_style('heroic', WEBLIZAR_TWITTER_PLUGIN_URL .'css/heroic-features.css');

	/**JS**/
	wp_enqueue_script('jquery');
	wp_enqueue_script('popper', WEBLIZAR_TWITTER_PLUGIN_URL . 'js/popper.min.js', array( 'jquery' ), true, true );
	wp_enqueue_script('wl_bootstrap', WEBLIZAR_TWITTER_PLUGIN_URL . 'js/bootstrap.min.js', array( 'jquery' ), true, true );
    wp_enqueue_script('weblizar-tab-js',WEBLIZAR_TWITTER_PLUGIN_URL .'js/option-js.js',array('jquery', 'media-upload', 'jquery-ui-sortable'));
	require_once("twiiter_help_body.php");
}

if( ! is_admin() ){  add_action( 'wp_enqueue_scripts', 'wl_enqueue_css_frontend' ); }
function wl_enqueue_css_frontend(){
	global $post;
	if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'WL_TWITTER' ) ) {
		wp_enqueue_style('front-end-css', WEBLIZAR_TWITTER_PLUGIN_URL .'css/front-end-css.css');
		wp_enqueue_style('wl-bootstrap', WEBLIZAR_TWITTER_PLUGIN_URL. 'css/bootstrap.min.css');
	}
}

/*Plugin Setting Link*/
function wl_twitter_settinglinks( $links ) {
	$twt_go_pro_link = '<a href="https://weblizar.com/plugins/twitter-tweets-pro/" target="_blank">'. esc_html__( 'Go Pro', 'twitter-tweets' ) .'</a>';
	$twitter_settings_link = '<a href="admin.php?page=Twitter">' . esc_html__( 'Settings', 'twitter-tweets' ) . '</a>';
    array_unshift( $links,$twt_go_pro_link,$twitter_settings_link);
  	return $links;
}
$plugin_wl_twitter = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin_wl_twitter", 'wl_twitter_settinglinks' );
?>
