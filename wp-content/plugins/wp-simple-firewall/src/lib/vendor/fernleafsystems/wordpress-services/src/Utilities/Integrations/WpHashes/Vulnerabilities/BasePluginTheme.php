<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Vulnerabilities;

/**
 * Class BasePluginTheme
 * @package FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Vulnerabilities
 */
abstract class BasePluginTheme extends Base {

	/**
	 * @param string $sSlug
	 * @param string $sVersion
	 * @return array[]|null
	 */
	public function getVulnerabilities( $sSlug, $sVersion ) {
		$oReq = $this->getRequestVO();
		$oReq->slug = strtolower( $sSlug );
		$oReq->version = $sVersion;
		return $this->query();
	}

	/**
	 * @return string
	 */
	protected function getApiUrl() {
		$oReq = $this->getRequestVO();
		return sprintf( '%s/%s/%s', parent::getApiUrl(), $oReq->slug, $oReq->version );
	}
}