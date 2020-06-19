<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Hashes;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Class WordPress
 * @package FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Hashes
 */
class WordPress extends AssetHashesBase {

	const TYPE = 'wordpress';

	/**
	 * @param string $sVersion
	 * @param string $sLocale
	 * @param string $sHashAlgo
	 * @return string[]|null
	 */
	public function getHashes( $sVersion, $sLocale = null, $sHashAlgo = null ) {
		/** @var RequestVO $oReq */
		$oReq = $this->getRequestVO();
		$oReq->version = $sVersion;
		$oReq->hash = $sHashAlgo;
		$oReq->locale = strtolower( empty( $sLocale ) ? Services::WpGeneral()->getLocaleForChecksums() : $sLocale );
		return $this->query();
	}

	/**
	 * @return string[]|null
	 */
	public function getCurrent() {
		$oWp = Services::WpGeneral();
		return $this->getHashes( $oWp->getVersion(), $oWp->getLocaleForChecksums() );
	}
}