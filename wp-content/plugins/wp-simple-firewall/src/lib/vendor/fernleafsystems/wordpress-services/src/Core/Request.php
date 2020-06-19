<?php

namespace FernleafSystems\Wordpress\Services\Core;

use Carbon\Carbon;
use FernleafSystems\Wordpress\Services\Services;

/**
 * Class Request
 * @package FernleafSystems\Wordpress\Services\Core
 */
class Request {

	/**
	 * @var array
	 */
	private $aPost;

	/**
	 * @var array
	 */
	private $aQuery;

	/**
	 * @var array
	 */
	private $aCookie;

	/**
	 * @var array
	 */
	private $aServer;

	/**
	 * @var array
	 */
	private $aEnv;

	/**
	 * @var int
	 */
	private $nTs;

	/**
	 * @var float
	 */
	private $nMts;

	/**
	 * @var string
	 */
	private $sContent;

	/**
	 * Request constructor.
	 */
	public function __construct() {
		$this->aPost = $_POST;
		$this->aQuery = $_GET;
		$this->aCookie = &$_COOKIE;
		$this->aServer = $_SERVER;
		$this->aEnv = $_ENV;
		$this->ts();
	}

	/**
	 * @return string
	 */
	public function getContent() {
		if ( !isset( $this->sContent ) ) {
			$this->sContent = file_get_contents( 'php://input' );
		}
		return $this->sContent;
	}

	/**
	 * @return string
	 */
	public function getMethod() {
		$sRequestMethod = $this->server( 'REQUEST_METHOD' );
		return ( empty( $sRequestMethod ) ? '' : strtolower( $sRequestMethod ) );
	}

	/**
	 * @param bool $bMsOnly
	 * @return int
	 */
	public function mts( $bMsOnly = false ) {
		$nT = $this->ts();
		if ( empty( $this->nMts ) ) {
			$nT = $bMsOnly ? 0 : $nT;
		}
		else {
			$nT = $bMsOnly ? preg_replace( '#^[0-9]+\.#', '', $this->nMts ) : $this->nMts;
		}
		return $nT;
	}

	/**
	 * @return int
	 */
	public function ts() {
		if ( empty( $this->nTs ) ) {
			$this->nTs = time();
			$this->nMts = function_exists( 'microtime' ) ? @microtime( true ) : false;
		}
		return $this->nTs;
	}

	/**
	 * @param bool $bSetTimezone - useful only when you're reporting times or displaying
	 * @return Carbon
	 */
	public function carbon( $bSetTimezone = false ) {
		$oWP = Services::WpGeneral();
		$oC = new Carbon();
		$oC->setTimestamp( $this->ts() );
		$oC->setLocale( $oWP->getLocaleCountry() );
		if ( $bSetTimezone ) {
			$sTZ = $oWP->getOption( 'timezone_string' );
			if ( !empty( $sTZ ) ) {
				$oC->setTimezone( $sTZ );
			}
		}
		return $oC;
	}

	/**
	 * @param bool $bIncludeCookie
	 * @return array
	 */
	public function getRawRequestParams( $bIncludeCookie = true ) {
		$aParams = array_merge( $this->aQuery, $this->aPost );
		return $bIncludeCookie ? array_merge( $aParams, $this->aCookie ) : $aParams;
	}

	/**
	 * @return string
	 */
	public function getHost() {
		return $this->server( 'HTTP_HOST' );
	}

	/**
	 * @return string URI Path in lowercase
	 */
	public function getPath() {
		return $this->getUriParts()[ 'path' ];
	}

	/**
	 * @return string
	 */
	public function getServerAddress() {
		return $this->server( 'SERVER_ADDR' );
	}

	/**
	 * @return string
	 */
	public function getUri() {
		return $this->server( 'REQUEST_URI' );
	}

	/**
	 * @return array
	 */
	public function getUriParts() {
		$sPath = $this->getUri();
		if ( strpos( $sPath, '?' ) !== false ) {
			list( $sPath, $sQuery ) = explode( '?', $sPath, 2 );
		}
		else {
			$sQuery = '';
		}
		return [
			'path'  => $sPath,
			'query' => $sQuery,
		];
	}

	/**
	 * @return string
	 */
	public function getUserAgent() {
		return $this->server( 'HTTP_USER_AGENT' );
	}

	/**
	 * @return bool
	 */
	public function isPost() {
		return ( $this->getMethod() == 'post' );
	}

	/**
	 * @return int
	 */
	public function countQuery() {
		return $this->count( str_replace( 'count', '', __FUNCTION__ ) );
	}

	/**
	 * @return int
	 */
	public function countPost() {
		return $this->count( str_replace( 'count', '', __FUNCTION__ ) );
	}

	/**
	 * @param string $sContainer
	 * @return int
	 */
	private function count( $sContainer ) {
		$sArray = 'a'.$sContainer;
		$aArray = $this->{$sArray};
		return is_array( $aArray ) ? count( $aArray ) : 0;
	}

	/**
	 * @param string $sKey
	 * @param null   $mDefault
	 * @return mixed|null
	 */
	public function cookie( $sKey, $mDefault = null ) {
		return $this->arrayFetch( __FUNCTION__, $sKey, $mDefault );
	}

	/**
	 * @param string $sKey
	 * @param null   $mDefault
	 * @return mixed|null
	 */
	public function env( $sKey, $mDefault = null ) {
		return $this->arrayFetch( __FUNCTION__, $sKey, $mDefault );
	}

	/**
	 * @param string $sKey
	 * @param null   $mDefault
	 * @return mixed|null
	 */
	public function post( $sKey, $mDefault = null ) {
		return $this->arrayFetch( __FUNCTION__, $sKey, $mDefault );
	}

	/**
	 * @param string $sKey
	 * @param null   $mDefault
	 * @return mixed|null
	 */
	public function query( $sKey, $mDefault = null ) {
		return $this->arrayFetch( __FUNCTION__, $sKey, $mDefault );
	}

	/**
	 * POST > GET > COOKIE
	 * @param string $sKey
	 * @param bool   $bIncludeCookie
	 * @param null   $mDefault
	 * @return mixed|null
	 */
	public function request( $sKey, $bIncludeCookie = false, $mDefault = null ) {
		$mFetchVal = $this->post( $sKey );
		if ( is_null( $mFetchVal ) ) {
			$mFetchVal = $this->query( $sKey );
			if ( $bIncludeCookie && is_null( $mFetchVal ) ) {
				$mFetchVal = $this->cookie( $sKey );
			}
		}
		return is_null( $mFetchVal ) ? $mDefault : $mFetchVal;
	}

	/**
	 * @param string $sKey
	 * @param null   $mDefault
	 * @return mixed|null
	 */
	public function server( $sKey, $mDefault = null ) {
		return $this->arrayFetch( __FUNCTION__, $sKey, $mDefault );
	}

	/**
	 * @param string $sContainer
	 * @param string $sKey
	 * @param mixed  $mDefault
	 * @return mixed|null
	 */
	private function arrayFetch( $sContainer, $sKey, $mDefault = null ) {
		$sArray = 'a'.ucfirst( $sContainer );
		$aArray = $this->{$sArray};
		if ( is_null( $sKey ) || !isset( $aArray[ $sKey ] ) || !is_array( $aArray ) ) {
			return $mDefault;
		}
		return $aArray[ $sKey ];
	}

	/**
	 * @return int
	 * @deprecated
	 */
	public function time() {
		return $this->ts();
	}

	/**
	 * @param bool $bMicro
	 * @return int
	 * @deprecated
	 */
	public function getRequestTime( $bMicro = false ) {
		return $this->mts( $bMicro );
	}

	/**
	 * @return string
	 * @deprecated
	 */
	public function getRequestPath() {
		return $this->getPath();
	}

	/**
	 * @return string
	 * @deprecated
	 */
	public function getRequestUri() {
		return $this->server( 'REQUEST_URI', '' );
	}

	/**
	 * @return array|false
	 * @deprecated
	 */
	public function getRequestUriParts() {
		return $this->getUriParts();
	}
}