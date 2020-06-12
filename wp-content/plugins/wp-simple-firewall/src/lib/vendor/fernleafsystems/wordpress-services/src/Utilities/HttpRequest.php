<?php

namespace FernleafSystems\Wordpress\Services\Utilities;

use FernleafSystems\Utilities\Data\Adapter\StdClassAdapter;
use FernleafSystems\Wordpress\Services\Core\VOs\WpHttpResponseVo;

/**
 * Class HttpRequest
 * @package FernleafSystems\Wordpress\Services\Utilities
 * @property string           $url
 * @property array            $requestArgs
 * @property WpHttpResponseVo $lastResponse
 * @property \WP_Error        $lastError
 */
class HttpRequest {

	use StdClassAdapter;

	/**
	 * @param string $sUrl
	 * @param array  $aArg
	 * @return bool
	 */
	public function get( $sUrl, $aArg = [] ) {
		return $this->request( $sUrl, $aArg, 'GET' )->isSuccess();
	}

	/**
	 * @param string $sUrl
	 * @param array  $aArg
	 * @return string
	 */
	public function getContent( $sUrl, $aArg = [] ) {
		return $this->get( $sUrl, $aArg ) ? trim( $this->lastResponse->body ) : '';
	}

	/**
	 * @param string $sUrl
	 * @param array  $aArg
	 * @return bool
	 */
	public function post( $sUrl, $aArg = [] ) {
		return $this->request( $sUrl, $aArg, 'POST' )->isSuccess();
	}

	/**
	 * @return bool
	 */
	public function isSuccess() {
		return ( $this->lastResponse instanceof WpHttpResponseVo );
	}

	/**
	 * This is provided for backward compatibility with the old requestUrl
	 * @param string $sUrl
	 * @param array  $aRequestArgs
	 * @param string $sMethod
	 * @return array|false
	 */
	public function requestUrl( $sUrl, $aRequestArgs = [], $sMethod = 'GET' ) {
		return $this->request( $sUrl, $aRequestArgs, $sMethod )->isSuccess() ?
			$this->lastResponse->getRawDataAsArray() : false;
	}

	/**
	 * A helper method for making quick requests. At least a valid URL will need to be supplied.
	 * All requests default to empty data and GET
	 * @param string $sUrl
	 * @param array  $aRequestArgs
	 * @param string $sMethod
	 * @return $this
	 */
	public function request( $sUrl = null, $aRequestArgs = null, $sMethod = null ) {
		$this->resetResponses();
		try {
			if ( !empty( $sUrl ) ) {
				$this->setUrl( $sUrl );
			}
			if ( is_array( $aRequestArgs ) ) {
				$this->setRequestArgs( $aRequestArgs );
			}
			if ( !empty( $sMethod ) ) {
				$this->setMethod( $sMethod );
			}
			$this->lastResponse = $this->send();
		}
		catch ( \Exception $oE ) {
			$this->lastError = new \WP_Error( 'odp-http-error', $oE->getMessage() );
		}
		return $this;
	}

	/**
	 * @return array
	 */
	private function getRequestArgs() {
		if ( !is_array( $this->requestArgs ) ) {
			$this->requestArgs;
		}
		$this->requestArgs = array_merge(
			[ 'method' => 'GET' ],
			$this->requestArgs
		);
		return $this->requestArgs;
	}

	/**
	 * @return WpHttpResponseVo
	 * @throws \Exception
	 */
	private function send() {
		if ( wp_http_validate_url( $this->url ) === false ) {
			throw new \Exception( 'URL is invalid' );
		}
		$mResult = wp_remote_request( $this->url, $this->getRequestArgs() );
		if ( is_wp_error( $mResult ) ) {
			throw new \Exception( $mResult->get_error_message() );
		}
		return ( new WpHttpResponseVo() )->applyFromArray( $mResult );
	}

	/**
	 * @param string $sMethod
	 * @return $this
	 */
	public function setMethod( $sMethod ) {
		return $this->setRequestArg( 'method', strtoupper( $sMethod ) );
	}

	/**
	 * @param string $sKey
	 * @param mixed  $mValue
	 * @return $this
	 */
	public function setRequestArg( $sKey, $mValue ) {
		$aArgs = $this->getRequestArgs();
		$aArgs[ $sKey ] = $mValue;
		return $this->setRequestArgs( $aArgs );
	}

	/**
	 * @param array $aData
	 * @return $this
	 */
	public function setRequestArgs( $aData ) {
		$this->requestArgs = is_array( $aData ) ? $aData : [];
		return $this;
	}

	/**
	 * @param string $sUrl
	 * @return $this
	 */
	public function setUrl( $sUrl ) {
		$this->url = $sUrl;
		return $this;
	}

	/**
	 * @return $this
	 */
	private function resetResponses() {
		$this->lastResponse = null;
		$this->lastError = null;
		return $this;
	}
}