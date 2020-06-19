<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Licenses\Keyless;

use FernleafSystems\Utilities\Data\Adapter\StdClassAdapter;
use FernleafSystems\Wordpress\Services\Services;
use FernleafSystems\Wordpress\Services\Utilities\HttpRequest;

/**
 * Class Lookup
 * @package FernleafSystems\Wordpress\Services\Utilities\Licenses\Keyless
 * @property string      $lookup_url_stub
 * @property string      $request_method
 * @property int         $timeout
 * @property HttpRequest $last_http_req
 */
abstract class Base {

	use StdClassAdapter {
		__get as __adapterGet;
	}
	const DEFAULT_URL_STUB = 'https://www.shieldsecurity.io/wp-json/odp-eddkeyless/v1';
	const API_ACTION = '';

	/**
	 * @return array|null
	 */
	protected function sendReq() {
		$oHttpReq = Services::HttpRequest();

		$aBodyParams = array_intersect_key(
			$this->getRawDataAsArray(),
			array_flip( $this->getRequestBodyParamKeys() )
		);

		$aReqParams = [
			'timeout' => $this->timeout,
		];

		switch ( $this->request_method ) {
			case 'post':
				$aReqParams[ 'body' ] = $aBodyParams;
				$bReqSuccess = $oHttpReq->post( $this->getApiRequestUrl(), $aReqParams );
				break;
			case 'get':
			default:
				// Doing it in the ['body'] on some sites fails with the params not passed through to query string.
				// if they're not using the newer WP Request() class. WP 4.6+
				$bReqSuccess = $oHttpReq->get(
					add_query_arg( $aBodyParams, $this->getApiRequestUrl() ),
					$aReqParams
				);
				break;
		}

		if ( $bReqSuccess ) {
			$aResponse = empty( $oHttpReq->lastResponse->body ) ? [] : @json_decode( $oHttpReq->lastResponse->body, true );
		}
		else {
			$aResponse = null;
		}

		$this->last_http_req = $oHttpReq;
		return $aResponse;
	}

	/**
	 * @return string
	 */
	protected function getApiRequestUrl() {
		return sprintf( '%s/%s', $this->lookup_url_stub, static::API_ACTION );
	}

	/**
	 * @return string[]
	 */
	protected function getRequestBodyParamKeys() {
		return [];
	}

	/**
	 * @param string $sProperty
	 * @return mixed
	 */
	public function __get( $sProperty ) {

		$mValue = $this->__adapterGet( $sProperty );

		switch ( $sProperty ) {

			case 'request_method':
				if ( empty( $mValue ) ) {
					$mValue = 'get';
				}
				$mValue = strtolower( $mValue );
				break;

			case 'lookup_url_stub':
				if ( empty( $mValue ) ) {
					$mValue = static::DEFAULT_URL_STUB;
				}
				$mValue = rtrim( $mValue, '/' );
				break;

			case 'timeout':
				if ( empty( $mValue ) || !is_numeric( $mValue ) ) {
					$mValue = 60;
				}
				break;

			default:
				break;
		}

		return $mValue;
	}
}