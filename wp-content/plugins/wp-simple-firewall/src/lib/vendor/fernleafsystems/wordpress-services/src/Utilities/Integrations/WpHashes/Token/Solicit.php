<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Token;

/**
 * Class Solicit
 * @package FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Token
 */
class Solicit extends Base {

	/**
	 * @param string $sUrl
	 * @param string $sInstallId
	 * @return array|null
	 */
	public function retrieve( $sUrl, $sInstallId ) {
		/** @var RequestVO $oReq */
		$oReq = $this->getRequestVO();
		$oReq->action = 'solicit';
		$oReq->install_id = $sInstallId;
		$oReq->url = strpos( $sUrl, '?' ) ? explode( '?', $sUrl, 2 )[ 0 ] : $sUrl;
		return $this->query();
	}

	/**
	 * @inheritDoc
	 */
	protected function getApiUrl() {
		/** @var RequestVO $oReq */
		$oReq = $this->getRequestVO();
		return sprintf( '%s/%s/%s', parent::getApiUrl(), $oReq->action, $oReq->install_id );
	}

	/**
	 * @inheritDoc
	 */
	protected function getQueryData() {
		/** @var RequestVO $oReq */
		$oReq = $this->getRequestVO();
		$aData = parent::getQueryData();
		$aData[ 'url' ] = $oReq->url;
		return $aData;
	}
}