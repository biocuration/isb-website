<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes;

use FernleafSystems\Wordpress\Services\Utilities\HttpRequest;
use FernleafSystems\Wordpress\Services\Utilities\Integrations\RequestVO;

/**
 * Class ApiBase
 * @package FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes
 */
abstract class ApiBase {

	const API_URL = 'https://wphashes.com/api/apto-wphashes/v1/';
	const API_ENDPOINT = '';
	const REQUEST_TYPE = 'GET';
	const RESPONSE_DATA_KEY = '';

	protected static $API_TOKEN;

	/**
	 * @var RequestVO
	 */
	private $oReq;

	/**
	 * @var bool
	 */
	private $bUseQueryCache = false;

	/**
	 * @var array
	 */
	private static $aQueryCache = [];

	/**
	 * ApiBase constructor.
	 * @param string $sApiToken
	 */
	public function __construct( $sApiToken = null ) {
		$this->setApiToken( $sApiToken );
	}

	/**
	 * @return string
	 */
	protected function getApiUrl() {
		return static::API_URL.static::API_ENDPOINT;
	}

	/**
	 * @return array
	 */
	protected function getQueryData() {
		return empty( static::$API_TOKEN ) ? [] : [ 'token' => static::$API_TOKEN ];
	}

	/**
	 * @return RequestVO|mixed
	 */
	protected function getRequestVO() {
		if ( !isset( $this->oReq ) ) {
			$this->oReq = $this->newReqVO();
		}
		return $this->oReq;
	}

	/**
	 * @return RequestVO
	 */
	protected function newReqVO() {
		return new RequestVO();
	}

	/**
	 * @return array|mixed|null
	 */
	public function query() {
		$aData = $this->fireRequestDecodeResponse();
		if ( is_array( $aData ) ) {
			if ( strlen( static::RESPONSE_DATA_KEY ) > 0 ) {
				$aData = isset( $aData[ static::RESPONSE_DATA_KEY ] ) ? $aData[ static::RESPONSE_DATA_KEY ] : null;
			}
		}
		else {
			$aData = null;
		}
		return $aData;
	}

	/**
	 * @return array|null - null on failure
	 */
	protected function fireRequestDecodeResponse() {
		$sResponse = $this->fireRequest();
		return empty( $sResponse ) ? null : json_decode( $sResponse, true );
	}

	/**
	 * @return string
	 */
	protected function fireRequest() {
		$this->preRequest();
		switch ( static::REQUEST_TYPE ) {
			case 'POST':
				$sResponse = $this->fireRequest_POST();
				break;
			case 'GET':
			default:
				$sResponse = $this->fireRequest_GET();
				break;
		}
		return $sResponse;
	}

	protected function preRequest() {
	}

	/**
	 * @return string
	 */
	protected function fireRequest_GET() {
		$sResponse = null;

		$sUrl = add_query_arg( $this->getQueryData(), $this->getApiUrl() );
		$sSig = md5( $sUrl );

		if ( $this->isUseQueryCache() && isset( self::$aQueryCache[ $sSig ] ) ) {
			$sResponse = self::$aQueryCache[ $sSig ];
		}

		if ( is_null( $sResponse ) ) {
			$sResponse = ( new HttpRequest() )->getContent( $sUrl );
			if ( $this->isUseQueryCache() ) {
				self::$aQueryCache[ $sSig ] = $sResponse;
			}
		}

		return $sResponse;
	}

	/**
	 * @return string|null
	 */
	protected function fireRequest_POST() {
		$oHttp = new HttpRequest();
		$oHttp
			->post(
				add_query_arg( $this->getQueryData(), $this->getApiUrl() ),
				[ 'body' => $this->getRequestVO()->getRawDataAsArray() ]
			);
		return $oHttp->isSuccess() ? $oHttp->lastResponse->body : null;
	}

	/**
	 * @return bool
	 */
	public function isUseQueryCache() {
		return (bool)$this->bUseQueryCache;
	}

	/**
	 * @param string $sToken
	 * @return $this
	 */
	public function setApiToken( $sToken ) {
		if ( is_string( $sToken ) && preg_match( '#^[a-z0-9]{32,}$#', $sToken ) ) {
			static::$API_TOKEN = $sToken;
		}
		return $this;
	}

	/**
	 * @param bool $bUseQueryCache
	 * @return $this
	 */
	public function setUseQueryCache( $bUseQueryCache ) {
		$this->bUseQueryCache = $bUseQueryCache;
		return $this;
	}
}