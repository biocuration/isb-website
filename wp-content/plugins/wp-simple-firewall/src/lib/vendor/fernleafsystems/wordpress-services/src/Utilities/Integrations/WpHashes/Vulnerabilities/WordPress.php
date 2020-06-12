<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Vulnerabilities;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Class WordPress
 * @package FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Hashes
 */
class WordPress extends Base {

	const ASSET_TYPE = 'wordpress';

	/**
	 * @param string $sVersion
	 * @return array[]|null
	 */
	public function getVulnerabilities( $sVersion ) {
		if ( empty( $sVersion ) ) {
			$sVersion = Services::WpGeneral()->getVersion( true );
		}
		$oReq = $this->getRequestVO();
		$oReq->version = $sVersion;
		return $this->query();
	}

	/**
	 * @return string
	 */
	protected function getApiUrl() {
		return sprintf( '%s/%s', parent::getApiUrl(), $this->getRequestVO()->version );
	}

	/**
	 * @return array[]|null
	 */
	public function getCurrent() {
		$oWp = Services::WpGeneral();
		return $this->getVulnerabilities( $oWp->getVersion( true ) );
	}
}