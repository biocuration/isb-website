<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Hashes;

/**
 * Class PluginThemeBase
 * @package FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Hashes
 */
abstract class PluginThemeBase extends AssetHashesBase {

	/**
	 * @param string $sSlug
	 * @param string $sVersion
	 * @param string $sHashAlgo
	 * @return array|null
	 */
	public function getHashes( $sSlug, $sVersion, $sHashAlgo = null ) {
		/** @var RequestVO $oReq */
		$oReq = $this->getRequestVO();
		$oReq->slug = $sSlug;
		$oReq->version = $sVersion;
		$oReq->hash = $sHashAlgo;
		return $this->query();
	}
}