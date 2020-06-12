<?php

namespace FernleafSystems\Wordpress\Services;

use FernleafSystems\Wordpress\Services\Core;
use FernleafSystems\Wordpress\Services\Utilities;
use Pimple\Container;

class Services {

	/**
	 * @var Container
	 */
	protected static $oDic;

	/**
	 * @var Services The reference to *Singleton* instance of this class
	 */
	private static $oInstance;

	protected static $aItems;

	/**
	 * @return Services
	 */
	public static function GetInstance() {
		if ( null === static::$oInstance ) {
			static::$oInstance = new static();
		}
		return static::$oInstance;
	}

	/**
	 * Protected constructor to prevent creating a new instance of the
	 * *Singleton* via the `new` operator from outside of this class.
	 */
	protected function __construct() {
		$this->registerAll();
		self::CustomHooks(); // initiate these early
		self::Request(); // initiate these early
		self::WpCron();
	}

	public function registerAll() {
		self::$oDic = new Container();
		self::$oDic[ 'service_data' ] = function () {
			return new Utilities\Data();
		};
		self::$oDic[ 'service_corefilehashes' ] = function () {
			return new Core\CoreFileHashes();
		};
		self::$oDic[ 'service_email' ] = function () {
			return new Utilities\Email();
		};
		self::$oDic[ 'service_datamanipulation' ] = function () {
			return new Utilities\DataManipulation();
		};
		self::$oDic[ 'service_customhooks' ] = function () {
			return new Core\CustomHooks();
		};
		self::$oDic[ 'service_nonce' ] = function () {
			return new Core\Nonce();
		};
		self::$oDic[ 'service_request' ] = function () {
			return new Core\Request();
		};
		self::$oDic[ 'service_response' ] = function () {
			return new Core\Response();
		};
		self::$oDic[ 'service_rest' ] = function () {
			return new Core\Rest();
		};
		self::$oDic[ 'service_httprequest' ] = function () {
			return new Utilities\HttpRequest();
		};
		self::$oDic[ 'service_render' ] = function () {
			return new Utilities\Render();
		};
		self::$oDic[ 'service_respond' ] = function () {
			return new Core\Respond();
		};
		self::$oDic[ 'service_serviceproviders' ] = function () {
			return new Utilities\ServiceProviders();
		};
		self::$oDic[ 'service_includes' ] = function () {
			return new Core\Includes();
		};
		self::$oDic[ 'service_ip' ] = function () {
			return Utilities\IpUtils::GetInstance();
		};
		self::$oDic[ 'service_encrypt' ] = function () {
			return new Utilities\Encrypt\OpenSslEncrypt();
		};
		self::$oDic[ 'service_geoip' ] = function () {
			return Utilities\GeoIp::GetInstance();
		};
		self::$oDic[ 'service_wpadminnotices' ] = function () {
			return new Core\AdminNotices();
		};
		self::$oDic[ 'service_wpcomments' ] = function () {
			return new Core\Comments();
		};
		self::$oDic[ 'service_wpcron' ] = function () {
			return new Core\Cron();
		};
		self::$oDic[ 'service_wpdb' ] = function () {
			return new Core\Db();
		};
		self::$oDic[ 'service_wpfs' ] = function () {
			return new Core\Fs();
		};
		self::$oDic[ 'service_wpgeneral' ] = function () {
			return new Core\General();
		};
		self::$oDic[ 'service_wpplugins' ] = function () {
			return new Core\Plugins();
		};
		self::$oDic[ 'service_wpthemes' ] = function () {
			return new Core\Themes();
		};
		self::$oDic[ 'service_wppost' ] = function () {
			return new Core\Post();
		};
		self::$oDic[ 'service_wptrack' ] = function () {
			return new Core\Track();
		};
		self::$oDic[ 'service_wpusers' ] = function () {
			return new Core\Users();
		};
	}

	/**
	 * @return Core\CustomHooks
	 */
	public static function CustomHooks() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Utilities\Data
	 */
	public static function Data() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Utilities\Email
	 */
	public static function Email() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Utilities\DataManipulation
	 */
	public static function DataManipulation() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Core\CoreFileHashes
	 */
	public static function CoreFileHashes() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Core\Includes
	 */
	public static function Includes() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Utilities\Encrypt\OpenSslEncrypt
	 */
	public static function Encrypt() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Utilities\GeoIp
	 */
	public static function GeoIp() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Utilities\HttpRequest
	 */
	public static function HttpRequest() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Utilities\IpUtils
	 */
	public static function IP() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Core\Nonce
	 */
	public static function Nonce() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @param string $sTemplatePath
	 * @return Utilities\Render
	 */
	public static function Render( $sTemplatePath = '' ) {
		/** @var Utilities\Render $oRender */
		$oRender = self::getObj( __FUNCTION__ );
		if ( !empty( $sTemplatePath ) ) {
			$oRender->setTemplateRoot( $sTemplatePath );
		}
		return ( clone $oRender );
	}

	/**
	 * @return Core\Request
	 */
	public static function Request() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Core\Response
	 */
	public static function Response() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Core\Rest
	 */
	public static function Rest() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Core\Respond
	 */
	public static function Respond() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Utilities\ServiceProviders
	 */
	public static function ServiceProviders() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Core\AdminNotices
	 */
	public static function WpAdminNotices() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Core\Comments
	 */
	public static function WpComments() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Core\Cron
	 */
	public static function WpCron() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Core\Db
	 */
	public static function WpDb() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Core\Fs
	 */
	public static function WpFs() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Core\General
	 */
	public static function WpGeneral() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Core\Plugins
	 */
	public static function WpPlugins() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Core\Themes
	 */
	public static function WpThemes() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Core\Post
	 */
	public static function WpPost() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Core\Track
	 */
	public static function WpTrack() {
		return self::getObj( __FUNCTION__ );
	}

	/**
	 * @return Core\Users
	 */
	public static function WpUsers() {
		return self::getObj( __FUNCTION__ );
	}

	protected static function getObj( $sKeyFunction ) {
		$sFullKey = 'service_'.strtolower( $sKeyFunction );
		if ( !is_array( self::$aItems ) ) {
			self::$aItems = [];
		}
		if ( !isset( self::$aItems[ $sFullKey ] ) ) {
			self::$aItems[ $sFullKey ] = self::$oDic[ $sFullKey ];
		}
		return self::$aItems[ $sFullKey ];
	}
}