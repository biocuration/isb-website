<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Hashes;

use FernleafSystems\Wordpress\Services;

/**
 * Class ClassicPress
 * @package FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Hashes
 */
class ClassicPress extends AssetHashesBase {

	const TYPE = 'classicpress';

	/**
	 * @param string $sVersion
	 * @param string $sHashAlgo
	 * @return string[]|null
	 */
	public function getHashes( $sVersion, $sHashAlgo = null ) {
		/** @var RequestVO $oReq */
		$oReq = $this->getRequestVO();
		$oReq->version = $sVersion;
		$oReq->hash = $sHashAlgo;
		return $this->query();
	}

	/**
	 * @return string[]|null
	 */
	public function getCurrent() {
		return $this->getHashes( Services\Services::WpGeneral()->getVersion() );
	}
}