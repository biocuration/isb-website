<?php

namespace FernleafSystems\Wordpress\Services\Utilities;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Class GeoIp
 * @package FernleafSystems\Wordpress\Services\Utilities
 */
class GeoIp {

	const URL_REDIRECTLI = 'https://api.redirect.li/v1/ip/';

	/**
	 * @var GeoIp
	 */
	protected static $oInstance = null;

	/**
	 * @var
	 */
	private $aIpResults;

	/**
	 * @return GeoIp
	 */
	public static function GetInstance() {
		if ( is_null( self::$oInstance ) ) {
			self::$oInstance = new self();
		}
		return self::$oInstance;
	}

	private function __construct() {
		$this->aIpResults = [];
	}

	/**
	 * @param string $sIP
	 * @return string
	 */
	public function countryName( $sIP ) {
		return $this->lookupIp( $sIP )[ 'countryName' ];
	}

	/**
	 * @param string $sIP
	 * @return string - ISO2
	 */
	public function countryIso( $sIP ) {
		return $this->lookupIp( $sIP )[ 'countryCode' ];
	}

	/**
	 * @param string $sIP
	 * @return string[]
	 */
	public function lookupIp( $sIP ) {
		if ( empty( $this->aIpResults[ $sIP ] ) ) {
			$this->aIpResults[ $sIP ] = $this->redirectliIpLookup( $sIP );
		}
		return $this->aIpResults[ $sIP ];
	}

	/**
	 * @param string $sIp
	 * @return array
	 */
	private function redirectliIpLookup( $sIp ) {
		$oHttp = Services::HttpRequest();
		$aIpData = @json_decode( $oHttp->getContent( self::URL_REDIRECTLI.$sIp ), true );
		if ( empty( $aIpData ) || !is_array( $aIpData ) ) {
			$aIpData = [];
		}
		return $aIpData;
	}
}