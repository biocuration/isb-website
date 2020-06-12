<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Licenses;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Class Lookup
 * @package FernleafSystems\Wordpress\Services\Utilities\Licenses
 * @deprecated 0.1.60
 */
class Lookup {

	/**
	 * @var array
	 */
	private $aAdditionalRequestParams;

	/**
	 * A simple outgoing POST request to see that we can communicate with the ODP servers
	 * @param string $sStoreUrl
	 * @return string
	 */
	public function ping( $sStoreUrl ) {
		$sStoreUrl = add_query_arg( [ 'license_ping' => 'Y' ], $sStoreUrl );
		$aParams = [
			'body' => [
				'ping'    => 'pong',
				'license' => 'abcdefghi',
				'item_id' => '123',
				'url'     => Services::WpGeneral()->getWpUrl()
			]
		];

		$oHttpReq = Services::HttpRequest();
		if ( $oHttpReq->post( $sStoreUrl, $aParams ) ) {
			$aResult = @json_decode( $oHttpReq->lastResponse->body, true );
			$sResult = ( isset( $aResult[ 'success' ] ) && $aResult[ 'success' ] ) ? 'success' : 'unknown failure';
		}
		else {
			$sResult = $oHttpReq->lastError->get_error_message();
		}
		return $sResult;
	}

	/**
	 * @param string $sStoreUrl
	 * @param string $sKey
	 * @param string $sItemId
	 * @return EddLicenseVO
	 */
	public function activateLicense( $sStoreUrl, $sKey, $sItemId ) {
		return $this->commonLicenseAction( 'activate_license', $sStoreUrl, $sKey, $sItemId );
	}

	/**
	 * @param string $sStoreUrl
	 * @param string $sItemId
	 * @return EddLicenseVO
	 */
	public function activateLicenseKeyless( $sStoreUrl, $sItemId ) {
		return $this->activateLicense( $sStoreUrl, '', $sItemId );
	}

	/**
	 * @param string $sStoreUrl
	 * @param string $sKey
	 * @param string $sItemId
	 * @return EddLicenseVO|null
	 */
	public function checkLicense( $sStoreUrl, $sKey, $sItemId ) {
		return $this->commonLicenseAction( 'check_license', $sStoreUrl, $sKey, $sItemId );
	}

	/**
	 * @param string $sStoreUrl
	 * @param string $sKey
	 * @param string $sItemId
	 * @return EddLicenseVO
	 */
	public function deactivateLicense( $sStoreUrl, $sKey, $sItemId ) {
		return $this->commonLicenseAction( 'deactivate_license', $sStoreUrl, $sKey, $sItemId );
	}

	/**
	 * @param string $sAction
	 * @param string $sStoreUrl
	 * @param string $sKey
	 * @param string $sItemId
	 * @return EddLicenseVO
	 */
	private function commonLicenseAction( $sAction, $sStoreUrl, $sKey, $sItemId ) {
		$oWp = Services::WpGeneral();
		$aLicenseLookupParams = [
			'timeout' => 60,
			'body'    => array_merge(
				[
					'edd_action' => $sAction,
					'license'    => $sKey,
					'item_id'    => $sItemId,
					'url'        => $oWp->getHomeUrl( '', true ),
				],
				$this->getRequestParams()
			)
		];

		$oLic = $this->getLicenseVoFromData( $this->sendReq( $sStoreUrl, $aLicenseLookupParams, false ) );
		$oLic->last_request_at = Services::Request()->ts();
		return $oLic;
	}

	/**
	 * first attempts GET, then POST if the GET is successful but the data is not right
	 * @param string $sUrl
	 * @param array  $aArgs
	 * @param bool   $bAsPost
	 * @return array
	 */
	private function sendReq( $sUrl, $aArgs, $bAsPost = false ) {
		$aResponse = [];
		$oHttpReq = Services::HttpRequest();

		if ( $bAsPost ) {
			if ( $oHttpReq->post( $sUrl, $aArgs ) ) {
				$aResponse = empty( $oHttpReq->lastResponse->body ) ? [] : @json_decode( $oHttpReq->lastResponse->body, true );
			}
			return $aResponse;
		}
		elseif ( $oHttpReq->get( $sUrl, $aArgs ) ) {
			$aResponse = empty( $oHttpReq->lastResponse->body ) ? [] : @json_decode( $oHttpReq->lastResponse->body, true );
			if ( empty( $aResponse ) ) {
				$aResponse = $this->sendReq( $sUrl, $aArgs, true );
			}
		}

		return $aResponse;
	}

	/**
	 * @param array $aData
	 * @return EddLicenseVO
	 */
	public function getLicenseVoFromData( $aData ) {
		return ( new EddLicenseVO() )->applyFromArray( $aData );
	}

	/**
	 * @return array
	 */
	public function getRequestParams() {
		return is_array( $this->aAdditionalRequestParams ) ? $this->aAdditionalRequestParams : [];
	}

	/**
	 * @param array $aParams
	 * @return $this
	 */
	public function setRequestParams( $aParams = [] ) {
		$this->aAdditionalRequestParams = is_array( $aParams ) ? $aParams : [];
		return $this;
	}
}