<?php
/**
 * Bootstraps the Simple Share Buttons Adder plugin.
 *
 * @package SimpleShareButtonsAdder
 */

namespace SimpleShareButtonsAdder;

/**
 * Main plugin bootstrap file.
 */
class Plugin extends Plugin_Base {

	/**
	 * Plugin constructor.
	 */
	public function __construct() {
		parent::__construct();

		// Define some prefixes to use througout the plugin.
		$this->assets_prefix = strtolower( preg_replace( '/\B([A-Z])/', '-$1', __NAMESPACE__ ) );
		$this->meta_prefix = strtolower( preg_replace( '/\B([A-Z])/', '_$1', __NAMESPACE__ ) );

		// Globals.
		$class_ssba = new Simple_Share_Buttons_Adder( $this );
		$database = new Database( $this, $class_ssba );
		$forms = new Forms( $this );
		$admin_panel = new Admin_Panel( $this, $class_ssba, $forms );

		// Initiate classes.
		$classes = array(
			$class_ssba,
			$database,
			$admin_panel,
			$forms,
			new Styles( $this, $class_ssba ),
			new Admin_Bits( $this, $class_ssba, $database, $admin_panel ),
			new Buttons( $this, $class_ssba ),
			new Widget(),

		);

		// Add classes doc hooks.
		foreach ( $classes as $instance ) {
			$this->add_doc_hooks( $instance );
		}
	}

	/**
	 * Register assets.
	 *
	 * @action wp_enqueue_scripts
	 */
	public function register_assets() {
		wp_register_script( "{$this->assets_prefix}-ssba", "{$this->dir_url}js/ssba.js", array( 'jquery' ), false, true );
		wp_register_style( "{$this->assets_prefix}-indie", '//fonts.googleapis.com/css?family=Indie+Flower' );
		wp_register_style( "{$this->assets_prefix}-reenie", '//fonts.googleapis.com/css?family=Reenie+Beanie' );
		wp_register_style( "{$this->assets_prefix}-ssba", "{$this->dir_url}css/ssba.css", false );
	}

	/**
	 * Register admin scripts/styles.
	 *
	 * @action admin_enqueue_scripts
	 */
	public function register_admin_assets() {
		wp_register_script( "{$this->assets_prefix}-admin", "{$this->dir_url}js/admin.js", array( 'jquery', 'wp-util' ) );
		wp_register_script( "{$this->assets_prefix}-bootstrap-js", "{$this->dir_url}js/vendor/bootstrap.js" );
		wp_register_script( "{$this->assets_prefix}-colorpicker", "{$this->dir_url}js/vendor/colorpicker.js" );
		wp_register_script( "{$this->assets_prefix}-switch", "{$this->dir_url}js/vendor/switch.js" );
		wp_register_style( "{$this->assets_prefix}-admin", "{$this->dir_url}css/admin.css", false );
		wp_register_style( "{$this->assets_prefix}-readable", "{$this->dir_url}css/readable.css" );
		wp_register_style( "{$this->assets_prefix}-colorpicker", "{$this->dir_url}css/colorpicker.css" );
		wp_register_style( "{$this->assets_prefix}-switch","{$this->dir_url}css/switch.css" );
		wp_register_style( "{$this->assets_prefix}-admin-theme", "{$this->dir_url}css/admin-theme.css", "{$this->assets_prefix}-font-awesome" );
		wp_register_style( "{$this->assets_prefix}-font-awesome", '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', false );
		wp_register_style( "{$this->assets_prefix}-styles", "{$this->dir_url}css/style.css" );
		wp_register_style( "{$this->assets_prefix}-indie", '//fonts.googleapis.com/css?family=Indie+Flower' );
		wp_register_style( "{$this->assets_prefix}-reenie", '//fonts.googleapis.com/css?family=Reenie+Beanie' );
	}
}
