<?php
if ( !class_exists( 'ICWP_WPSF_WpUpgrades', false ) ):

	class ICWP_WPSF_WpUpgrades extends ICWP_WPSF_Foundation {

		/**
		 * @var ICWP_WPSF_WpUpgrades
		 */
		protected static $oInstance = NULL;

		/**
		 * @return ICWP_WPSF_WpUpgrades
		 */
		public static function GetInstance() {
			if ( is_null( self::$oInstance ) ) {
				self::$oInstance = new self();
			}
			return self::$oInstance;
		}
	}
endif;

require_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );
require_once ( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

if ( !class_exists( 'WP_Upgrader_Skin', false ) ) {
	$sWordPressWpUpgraderClass = ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
	if ( !is_file( $sWordPressWpUpgraderClass ) ) {
		die( '-9999:Failed to find required WP_Upgrader_Skin at '.$sWordPressWpUpgraderClass );
	}
	include_once( $sWordPressWpUpgraderClass );
}

if ( !class_exists( 'ICWP_Upgrader_Skin', false ) && class_exists( 'WP_Upgrader_Skin', false ) ) {

	/**
	 * Class ICWP_Upgrader_Skin
	 */
	class ICWP_Upgrader_Skin extends WP_Upgrader_Skin {

		public $m_aErrors;
		public $aFeedback;

		public function __construct() {
			parent::__construct();
			$this->done_header = true;
		}

		/**
		 * @return array
		 */
		public function getFeedback() {
			return $this->aFeedback;
		}

		function error( $errors ) { }
		function feedback( $string ) { }
	}
}

if ( !class_exists( 'ICWP_Plugin_Upgrader', false ) && class_exists( 'Plugin_Upgrader' ) ) {
	class ICWP_Plugin_Upgrader extends Plugin_Upgrader {
		protected $fModeOverwrite = true;

		public function install( $package, $args = array() ) {

			$defaults = array(
				'clear_update_cache' => true,
			);
			$parsed_args = wp_parse_args( $args, $defaults );

			$this->init();
			$this->install_strings();

			add_filter('upgrader_source_selection', array($this, 'check_package') );

			$this->run( array(
				'package' => $package,
				'destination' => WP_PLUGIN_DIR,
				'clear_destination' => $this->getOverwriteMode(), // this is the key to overwrite and why we're extending the native wordpress class
				'clear_working' => true,
				'hook_extra' => array(
					'type' => 'plugin',
					'action' => 'install',
				)
			) );

			remove_filter('upgrader_source_selection', array($this, 'check_package') );

			if ( ! $this->result || is_wp_error($this->result) )
				return $this->result;

			// Force refresh of plugin update information
			wp_clean_plugins_cache( $parsed_args['clear_update_cache'] );

			return true;
		}

		public function getOverwriteMode() {
			return $this->fModeOverwrite;
		}

		public function setOverwriteMode( $fOn = true ) {
			$this->fModeOverwrite = $fOn;
		}
	}
}

if ( !class_exists( 'ICWP_Bulk_Plugin_Upgrader_Skin', false ) && class_exists( 'Bulk_Plugin_Upgrader_Skin', false ) ) {
	/**
	 * Class ICWP_Bulk_Plugin_Upgrader_Skin
	 */
	class ICWP_Bulk_Plugin_Upgrader_Skin extends Bulk_Plugin_Upgrader_Skin {

		/**
		 * @var array
		 */
		public $m_aErrors;

		/**
		 * @var array
		 */
		public $aFeedback;

		/**
		 *
		 */
		public function __construct() {
			parent::__construct( compact( 'nonce', 'url' ) );
			$this->m_aErrors = array();
			$this->aFeedback = array();
		}

		/**
		 * @param string|array $errors
		 */
		function error( $errors ) {
			$this->m_aErrors[] = $errors;

			if ( is_string( $errors ) ) {
				$this->feedback( $errors );
			}
			else if ( is_wp_error( $errors ) && $errors->get_error_code() ) {
				foreach ( $errors->get_error_messages() as $message ) {
					if ( $errors->get_error_data() ) {
						$this->feedback( $message . ' ' . $errors->get_error_data() );
					}
					else {
						$this->feedback( $message );
					}
				}
			}
		}

		/**
		 * @return array
		 */
		public function getFeedback() {
			return $this->aFeedback;
		}

		/**
		 * @param string $string
		 */
		function feedback( $string ) {
			if ( isset( $this->upgrader->strings[$string] ) )
				$string = $this->upgrader->strings[$string];

			if ( strpos( $string, '%' ) !== false ) {
				$args = func_get_args();
				$args = array_splice( $args, 1 );
				if ( !empty( $args ) ) {
					$string = vsprintf( $string, $args );
				}
			}
			if ( empty( $string ) ) {
				return;
			}
			$this->aFeedback[] = $string;
		}

		function before( $title = '' ) {}
		function after( $title = '' ) {}
		function flush_output() {}

		/*
		function footer() {
			var_dump(debug_backtrace());
			die( 'testing' );
		}
		*/
	}
}

if ( !class_exists( 'ICWP_Theme_Upgrader', false ) && class_exists( 'Theme_Upgrader' ) ) {

	require_once ABSPATH . 'wp-admin/includes/theme.php'; // to get themes_api
	class ICWP_Theme_Upgrader extends Theme_Upgrader {
		protected $fModeOverwrite = true;

		public function install( $package, $args = array() ) {

			$defaults = array(
				'clear_update_cache' => true,
			);
			$parsed_args = wp_parse_args( $args, $defaults );

			$this->init();
			$this->install_strings();

			add_filter('upgrader_source_selection', array($this, 'check_package') );
			add_filter('upgrader_post_install', array($this, 'check_parent_theme_filter'), 10, 3);

			$this->run( array(
				'package' => $package,
				'destination' => get_theme_root(),
				'clear_destination' => $this->getOverwriteMode(),
				'clear_working' => true,
				'hook_extra' => array(
					'type' => 'theme',
					'action' => 'install',
				),
			) );

			remove_filter('upgrader_source_selection', array($this, 'check_package') );
			remove_filter('upgrader_post_install', array($this, 'check_parent_theme_filter'));

			if ( ! $this->result || is_wp_error($this->result) )
				return $this->result;

			// Refresh the Theme Update information
			wp_clean_themes_cache( $parsed_args['clear_update_cache'] );

			return true;
		}

		public function getOverwriteMode() {
			return $this->fModeOverwrite;
		}

		public function setOverwriteMode( $fOn = true ) {
			$this->fModeOverwrite = $fOn;
		}
	}
}

if ( !class_exists( 'ICWP_Bulk_Theme_Upgrader_Skin', false ) && class_exists( 'Bulk_Theme_Upgrader_Skin', false ) ) {

	/**
	 * Class Worpit_Bulk_Theme_Upgrader_Skin
	 */
	class ICWP_Bulk_Theme_Upgrader_Skin extends Bulk_Theme_Upgrader_Skin {

		/**
		 * @var array
		 */
		public $m_aErrors;

		/**
		 * @var array
		 */
		public $aFeedback;

		/**
		 */
		public function __construct() {
			parent::__construct( compact('title', 'nonce', 'url', 'theme') );
			$this->m_aErrors = array();
			$this->aFeedback = array();
		}

		/**
		 * @param string|array $errors
		 */
		function error( $errors ) {
			$this->m_aErrors[] = $errors;

			if ( is_string( $errors ) ) {
				$this->feedback( $errors );
			}
			else if ( is_wp_error( $errors ) && $errors->get_error_code() ) {
				foreach ( $errors->get_error_messages() as $message ) {
					if ( $errors->get_error_data() ) {
						$this->feedback( $message . ' ' . $errors->get_error_data() );
					}
					else {
						$this->feedback( $message );
					}
				}
			}
		}

		/**
		 * @return array
		 */
		public function getFeedback() {
			return $this->aFeedback;
		}

		/**
		 * @param string $string
		 */
		function feedback( $string ) {
			if ( isset( $this->upgrader->strings[$string] ) )
				$string = $this->upgrader->strings[$string];

			if ( strpos( $string, '%' ) !== false ) {
				$args = func_get_args();
				$args = array_splice( $args, 1 );
				if ( !empty( $args ) ) {
					$string = vsprintf( $string, $args );
				}
			}
			if ( empty( $string ) ) {
				return;
			}
			$this->aFeedback[] = $string;
		}

		function before( $title = '' ) {}
		function after( $title = '' ) {}
		function flush_output() {}
	}
}